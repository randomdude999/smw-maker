import time
import json

import requests
import MySQLdb
from bs4 import BeautifulSoup as bs

def login(auth_data):
    payload = {
        "login": "Login",
        "username": auth_data["uname"],
        "password": auth_data["pass"]
    }
    with requests.post("https://smwcentral.net/?p=login", data=payload) as r:
        soup = bs(r.text)
        sess_token = r.cookies["smwc_session"]
    return sess_token

def logout():
    cookies = {"smwc_session": sess_token}
    with requests.get("https://www.smwcentral.net/?p=login&do=logout", cookies=cookies) as r:
        # locate "security token" from that page
        soup = bs(r.text)

        sec_token = "blah"
    payload = {
        "logout": "Confirm+Logout",
        "token": sec_token
    }
    requests.post("https://www.smwcentral.net/?p=login&do=logout", data=payload, cookies=cookies)
    # don't even care about the result, we did our best to log out

def check_smwc():
    global sess_token

    pass

def main():
    global sess_token # needs to be global since we might need to generate a new one sometimes
    with open("randombot_credentials.json") as f:
        auth_data = json.load(f)
    # login
    sess_token = login(auth_data)
    try:
        while True:
            check_smwc()
            time.sleep(30)
    finally:
        # logout

if __name__ == '__main__':
    main()