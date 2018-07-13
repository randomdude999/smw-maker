import time
import json
import secrets

import requests
import MySQLdb
from bs4 import BeautifulSoup

bs = lambda text: BeautifulSoup(text, "html.parser")

smwc_endpoint = "https://www.smwcentral.net/"
pm_template = """Your SMW Maker token is {}. Go to [url]{}[/url] to log in now.
If you need a new token, just PM me again. That will also invalidate the previous token.
"""

def smwc_request(url, data=None):
    if data is None:
        return requests.get(smwc_endpoint+url, cookies={"smwc_session":sess_token})
    else:
        return requests.post(smwc_endpoint+url, data=data, cookies={"smwc_session":sess_token})

def login():
    payload = {
        "login": "Login",
        "username": auth_data["smwc_user"],
        "password": auth_data["smwc_password"]
    }
    with requests.post(smwc_endpoint+"?p=login", data=payload) as r:
        soup = bs(r.text)
        # TODO: detect that we are indeed logged in
        sess_token = r.cookies["smwc_session"]
    return sess_token


def logout():
    with smwc_request("?p=login&do=logout") as r:
        # locate "security token" from that page
        soup = bs(r.text)
        elem = soup.find("input", attrs={"name":"token"})
        if elem:
            sec_token = elem.attrs['value']
        else:
            return # no token found -> not logged in
    payload = {
        "logout": "Confirm+Logout",
        "token": sec_token
    }
    smwc_request("?p=login&do=logout", data=payload)
    # don't even care about the result, we did our best to log out

def send_pm(target, title, text):
    print(f"PM to {repr(target)} title {repr(title)} text {repr(text)}")
    with smwc_request("?p=pm&do=compose") as r:
        soup = bs(r.text)
        token = soup.find("input", attrs={"name":"token"}).attrs['value']
    payload = {
        "do": "compose",
        "recipient": target,
        "subject": title,
        "text": text,
        "submit": "Submit Message",
        "token": token
    }
    smwc_request("?p=pm", data=payload) # if this actually works then wtf

def handle_user(uname, uid):
    # generate token for user, insert it into the DB and PM them the new token
    cur = conn.cursor()
    cur.execute("SELECT token FROM users")
    used_tokens = [x[0] for x in cur.fetchall()]
    token = secrets.token_hex(16)
    while token in used_tokens:
        # because repeating code is totally Pythonic™
        token = secrets.token_hex(16)
    cur.execute("INSERT INTO users (smwc_id, name, token) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE name=%s, token=%s", (uid, uname, token, uname, token))
    conn.commit()
    send_pm(uname, "Re: smwmaker verify", pm_template.format(token, auth_data["login_page_url"]))

def handle_pm(table_row):
    # handle incoming PM (table_row is the <tr> element of the PM in the PM list)
    url = table_row.select("td")[1].a.attrs['href'].replace("/","")
    with smwc_request(url) as r:
        # we don't actually need to do anything with this, just do this to mark the PM read
        pass
    uname_elem = table_row.select("td")[2].a
    if 'title' in uname_elem.attrs:
        uname = str(uname_elem.attrs['title'])
    else:
        uname = str(uname_elem.string)
    u_id = int(uname_elem.attrs['href'].split('id=')[1])
    handle_user(uname, u_id)

def check_smwc():
    global sess_token
    # using a while loop here is hacky but it's the best alternative to a goto (which doesn't even exist in python :( )
    while True:
        with smwc_request("?p=pm") as r:
            soup = bs(r.text)
            if soup.find(id="deleteform"):
                # we are logged in
                for x in soup.select("#deleteform > table > tr"):
                    if x.select("td")[0].string == "NEW":
                        # a new PM!
                        if x.select("td")[1].string == "smwmaker verify":
                            handle_pm(x)
                        else:
                            # TODO: forward PM to bot owner
                            # url = smwc_endpoint + table_row.select("td")[1].a.attrs['href'].replace("/","")
                            # with requests.get(url, cookies={"smwc_session":sess_token}) as r:
                            pass
                break # exit
            else:
                sess_token = login()
                continue # go to the request again

def main():
    global sess_token # needs to be global since we might need to generate a new one sometimes
    global conn
    global auth_data
    with open("../config.json") as f:
        auth_data = json.load(f)
    if auth_data["mysql_password"] != None:
        conn = MySQLdb.connect(host="localhost", user=auth_data["mysql_user"],
                               db="smwmaker", passwd=auth_data["mysql_password"])
    else:
        conn = MySQLdb.connect(host="localhost", user=auth_data["mysql_user"], db="smwmaker")
    sess_token = login()
    try:
        while True:
            check_smwc()
            time.sleep(30)
    finally:
        # logout
        logout()

if __name__ == '__main__':
    main()
