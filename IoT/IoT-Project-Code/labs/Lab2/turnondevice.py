# -*- coding: utf-8 -*-
#!/usr/bin/env python
   
# This is where to insert your generated API keys (http://api.telldus.com/keys)
pubkey = "FEHUVEW84RAFR5SP22RABURUPHAFRUNU"  # Public Key
privkey = "ZUXEVEGA9USTAZEWRETHAQUBUR69U6EF" # Private Key
token = "8aba8385b6f65e0f7bf274e5e673f04b05d541a1e" # Token
secret = "ecd6a7203c64ec98469df1da577eeff3" # Token Secret 

import requests, json, hashlib, uuid, time
localtime = time.localtime(time.time())
timestamp = str(time.mktime(localtime))
nonce = uuid.uuid4().hex
oauthSignature = (privkey + "%26" + secret)
 
# GET-request
response = requests.get(
	url="https://pa-api.telldus.com/json/device/turnOn",
	params={
		"id": "11504861",
	},
	headers={
		"Authorization": 'OAuth oauth_consumer_key="{pubkey}", oauth_nonce="{nonce}", oauth_signature="{oauthSignature}", oauth_signature_method="PLAINTEXT", oauth_timestamp="{timestamp}", oauth_token="{token}", oauth_version="1.0"'.format(pubkey=pubkey, nonce=nonce, oauthSignature=oauthSignature, timestamp=timestamp, token=token),
		},
	)
# Output/response from GET-request	
responseData = response.json()

# Uncomment to print response :) 
#print(json.dumps(responseData, indent=4, sort_keys=True))