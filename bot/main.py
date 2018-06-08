import time
import json
import secrets

import requests
import MySQLdb
from bs4 import BeautifulSoup as bs

smwc_endpoint = "https://www.smwcentral.net/"
pm_template = """Your SMW Maker token is {}. Go to [url]...[/url] to log in now.
If you need a new token, just PM me again. That will also invalidate the previous token.
"""

def login(auth_data):
    payload = {
        "login": "Login",
        "username": auth_data["uname"],
        "password": auth_data["pass"]
    }
    with requests.post(smwc_endpoint+"?p=login", data=payload) as r:
        soup = bs(r.text)
        # TODO: detect that we are indeed logged in
        sess_token = r.cookies["smwc_session"]
    return sess_token


def logout():
    with requests.get(smwc_endpoint+"?p=login&do=logout", cookies={"smwc_session":sess_token}) as r:
        # locate "security token" from that page
        soup = bs(r.text)
        elem = soup.find("input", attrs={"name":"token"})
        if elem:
            sec_token = elem.value
        else:
            return # no token found -> not logged in
    payload = {
        "logout": "Confirm+Logout",
        "token": sec_token
    }
    requests.post(smwc_endpoint+"?p=login&do=logout", data=payload, cookies={"smwc_session":sess_token})
    # don't even care about the result, we did our best to log out

def send_pm(target, title, text):
    # TODO
    pass

def handle_user(uname, uid):
    # generate token for user, insert it into the DB and PM them the new token
    cur = conn.cursor()
    cur.execute("SELECT login_token FROM users")
    used_tokens = [x[0] for x in cur.fetchall()]
    token = secrets.token_hex(16)
    while token in used_tokens:
        # because repeating code is totally Pythonic™
        token = secrets.token_hex(16)
    cur.execute("INSERT INTO users (smwc_id, name, token) VALUES (?, ?, ?)", uid, uname, token)
    send_pm(uname, "Re: smwmaker verify", pm_template.format(token))

def handle_pm(table_row):
    # handle incoming PM (table_row is the <tr> element of the PM in the PM list)
    url = smwc_endpoint + table_row.select("td")[1].a.attrs['href'].replace("/","")
    with requests.get(url, cookies={"smwc_session":sess_token}) as r:
        # we don't actually need to do anything with this, just do this to mark the PM read
        pass
    uname_elem = table_row.select("td")[2].a
    if 'title' in uname_elem.attrs:
        uname = uname_elem.attrs['title']
    else:
        uname = uname_elem.string
    u_id = int(uname_elem.attrs['href'].split('id=')[1])
    handle_user(uname, u_id)

def check_smwc():
    global sess_token
    # using a while loop here is hacky but it's the best alternative to a goto (which doesn't even exist in python :( )
    while True:
        with requests.get(smwc_endpoint+"?p=pm", cookies={"smwc_session":sess_token}) as r:
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
    with open("randombot_credentials.json") as f:
        auth_data = json.load(f)
    conn = MySQLdb.connect(host="localhost", user="root", db="smwmaker") # , passwd=None
    sess_token = login(auth_data)
    try:
        while True:
            check_smwc()
            time.sleep(30)
    finally:
        # logout
        logout()

if __name__ == '__main__':
    main()