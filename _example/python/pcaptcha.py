import requests
from requests import Request
import json

def land():
    s = requests.Session()
    return s

if __name__ == "__main__":
    i = 1
    c = 1
    s = land()
    pcaptcha 	= "ee507ff105c95a41b21716a092edc5f6e497d83189b65d5ca1789e34da804206cf0c8dbff4fbac72acb7870e79c1cfc07cc39cca0cc03b5b07b70a0c906bac1b" # مقداری است که از طریق فرم ارسال شده است
    uid 		= "0x4e2e" # کد یکتای کپچای شما در سایت من لاکین
    secretKey 	= "e8414ae66a37806bae1166a6297ce302a393e70b8e444b37abe20fe4e277779c" # کلید خصوصی
    url         = "http://manlogin.com/captcha/cheack/v1/"+uid+"/"+secretKey+"/"+str(pcaptcha)
    r = s.get(url)
    if r.status_code == 200:
        json = json.loads(r.text)
        if "success" in json and json["success"]:
            print("کپچا مورد تایید است")
        else:
            print("کپچا مورد تایید نمی باشد!!!!!")
    else:
        print(r.status_code)
