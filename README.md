# Moodle Auth One-Time Password Authentication plugin

OTP plugin validates the user using the phone number and one-time password that send in their validated phone number. No users need to remember the password or any other credentials. There is no trouble registering a user. The new users are automatically registered by the system. Multiple OTP service supported Like AWS sms service, Twilio sms service etc

Additional security can be set:

revoke threshold: login failures limit causing revoke of the generated password, works independently of the account lockout (lockout threshold and lockout window site security settings)

minimum request period: a time in seconds after which another password can be generated


Auth instruction setting (global auth_instructions) is recommended depending on the adopted user account policy and plugin configuration.

Login page looks like -
<p align="left">
<img src="https://i.imgur.com/4fVRxOa.png">
</p>

after chose login with otp -
<p align="left">
<img src="https://i.imgur.com/xHVzSs8.png">
</p>


## Features
- Easy to Authentic user
- No hasel to register
- Easy Integration
- Multiple OTP service Supported
- Secure OTP based access


## Configuration

You can install this plugin from [Moodle plugins directory](https://moodle.org/plugins) or can download from [Github](https://github.com/eLearning-BS23/moodle-auth_otp).

You can download zip file and install or you can put file under auth as otp


## Plugin Global Settings
### Go to
```
  Dashboard->Site administration->Plugins->Authentication->OTP settings
```
- Enable AWS sms Service
- provide aws accesskey
  provide aws security key
- if you want to use another service like Twilio then enable it and provide credentials
- Done!
- 
- <p align="left">
<img src="https://i.imgur.com/DKIboXf.png">
</p>
  