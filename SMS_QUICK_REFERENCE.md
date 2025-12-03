# SMS API Quick Reference Card

Print this page or save it for quick access!

---

## 📋 Setup Cheat Sheet

### Twilio Setup (5 minutes)
```
1. Go to https://www.twilio.com
2. Sign up → Verify email & phone
3. Get Account SID from: https://www.twilio.com/console
4. Get Auth Token from: https://www.twilio.com/console
5. Get or verify phone number
6. Add to .env:
   SMS_PROVIDER=twilio
   SMS_TWILIO_ACCOUNT_SID=ACxxx...
   SMS_TWILIO_AUTH_TOKEN=your_token
   SMS_TWILIO_PHONE=+1234567890
7. Done! Test with /sms-example/test
```

### Nexmo Setup (5 minutes)
```
1. Go to https://dashboard.nexmo.com
2. Sign up → Verify email
3. Get API Key & Secret from dashboard
4. Create Sender ID (max 11 characters)
5. Add to .env:
   SMS_PROVIDER=nexmo
   SMS_NEXMO_API_KEY=your_key
   SMS_NEXMO_API_SECRET=your_secret
   SMS_NEXMO_FROM=YourBrand
6. Done! Test it out
```

### AWS SNS Setup (15 minutes)
```
1. Create AWS Account
2. Create IAM user with SNS permissions
3. Generate Access Key & Secret Key
4. Choose region (us-east-1, eu-west-1, etc)
5. Add to .env:
   SMS_PROVIDER=aws_sns
   SMS_AWS_ACCESS_KEY=your_key
   SMS_AWS_SECRET_KEY=your_secret
   SMS_AWS_REGION=us-east-1
6. Done! Ready to go
```

---

## 🚀 Testing Endpoints

### Check System Status
```
GET /sms-example/test
Response: {"message": "SMS API is configured..."}
```

### Send Test SMS
```
POST /sms-example/send
Parameters: phone=+639051234567&message=Hello

Response:
{
  "success": true,
  "message": "SMS sent successfully",
  "phone": "+639051234567",
  "provider": "Twilio"
}
```

### Send Bulk SMS
```
POST /sms-example/send-bulk
Parameters: phones=["639051234567", "639171234567"]&message=Bulk

Response: {
  "success": true,
  "total": 2,
  "results": {...}
}
```

### Get Carrier Info
```
POST /sms-example/carrier
Parameters: phone=09051234567

Response: {
  "success": true,
  "carrier": "PLDT"
}
```

---

## 💻 Code Examples

### Send SMS in Code
```php
use App\Libraries\SmsService;

$sms = new SmsService();
$result = $sms->send('+639051234567', 'Hello World');

if ($result['success']) {
    echo "Sent!";
} else {
    echo "Error: " . $result['message'];
}
```

### Send Bulk SMS
```php
$sms = new SmsService();
$phones = ['+639051234567', '+639171234567'];
$results = $sms->sendBulk($phones, 'Bulk message');

foreach ($results as $phone => $result) {
    echo $phone . ": " . ($result['success'] ? 'OK' : 'FAILED');
}
```

### Change Provider at Runtime
```php
$sms = new SmsService();
$sms->setProvider('nexmo');
$result = $sms->send('+639051234567', 'Message');
```

### Set Credentials Programmatically
```php
$sms = new SmsService();
$sms->setTwilioCredentials('ACxxx...', 'token', '+1234567890');
$result = $sms->send('+639051234567', 'Message');
```

### Get Carrier Name
```php
$sms = new SmsService();
$carrier = $sms->getCarrierName('+639051234567');
echo $carrier; // PLDT, TNT/Smart/Sun, DITO, or Unknown
```

---

## 📱 Phone Number Format

### Correct Format ✅
- +639051234567 (Philippines)
- +1234567890 (USA/Canada)
- +447911123456 (UK)
- +33123456789 (France)

**Rule**: Country code + number
- Always use +
- Include country code
- No spaces or dashes

### Wrong Format ❌
- 09051234567 (missing country code)
- 639051234567 (missing +)
- +63-905-1234567 (has dashes)
- +639051234567 ext. 123 (has extension)

---

## ⚙️ Environment Variables

### Twilio Variables
```env
SMS_PROVIDER=twilio
SMS_TWILIO_ACCOUNT_SID=ACa1b2c3d4e5f6g7h8i9j0
SMS_TWILIO_AUTH_TOKEN=1a2b3c4d5e6f7g8h9i0j1k2l
SMS_TWILIO_PHONE=+1415123456
```

### Nexmo Variables
```env
SMS_PROVIDER=nexmo
SMS_NEXMO_API_KEY=a1b2c3d4e5f6g7h8i9j0
SMS_NEXMO_API_SECRET=x1y2z3a4b5c6d7e8f9g0
SMS_NEXMO_FROM=Laundry
```

### AWS Variables
```env
SMS_PROVIDER=aws_sns
SMS_AWS_ACCESS_KEY=AKIAIOSFODNN7EXAMPLE
SMS_AWS_SECRET_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
SMS_AWS_REGION=us-east-1
```

---

## 🐛 Troubleshooting

| Problem | Fix |
|---------|-----|
| "Credentials not configured" | Add variables to .env |
| "HTTP Error 401" | Check credentials in dashboard |
| "Invalid phone format" | Use +639051234567 format |
| "SMS not arriving" | Check logs, verify phone |
| "Can't find config" | Located at app/Config/Sms.php |
| "Test endpoint 404" | Controller at app/Controllers/SmsExample.php |

---

## 📂 File Locations

```
Main Library:     app/Libraries/SmsService.php
Configuration:    app/Config/Sms.php
Example Code:     app/Controllers/SmsExample.php
Helper Functions: app/Helpers/SmsHelper.php
Environment:      .env (your copy)
Template:         .env.sms.example
Logs:             writable/logs/
```

---

## 🔒 Security Checklist

- [ ] Add .env to .gitignore
- [ ] Never commit credentials
- [ ] Use different creds per environment
- [ ] Rotate keys periodically
- [ ] Use HTTPS only
- [ ] Validate all inputs
- [ ] Monitor API usage
- [ ] Log all activity

---

## 🎯 Decision Tree

```
START: I want to send SMS
  │
  ├─→ Want quick setup?
  │   ├─→ YES: Use Twilio
  │   └─→ NO: Compare providers
  │
  ├─→ Using AWS already?
  │   ├─→ YES: Use AWS SNS
  │   └─→ NO: Use Twilio or Nexmo
  │
  └─→ Need global coverage?
      ├─→ YES: Use Nexmo
      └─→ NO: Any provider works
```

---

## 📊 Provider Comparison

| Factor | Twilio | Nexmo | AWS SNS |
|--------|--------|-------|---------|
| Setup Time | 5 min | 5 min | 15 min |
| Cost | $0.0075/SMS | $0.006/SMS | $0.00645/SMS |
| Free Tier | Yes ($15) | Yes | No |
| Documentation | Excellent | Good | Good |
| Best For | General | International | AWS users |
| Start Here? | ✅ YES | Maybe | If using AWS |

---

## 🚀 Deployment Checklist

### Pre-Deployment
- [ ] Credentials obtained
- [ ] .env configured
- [ ] Testing successful
- [ ] Logs reviewed
- [ ] Existing code tested

### Deployment
- [ ] Move to staging
- [ ] Test end-to-end
- [ ] Check all features
- [ ] Monitor logs
- [ ] Deploy to production

### Post-Deployment
- [ ] Verify working
- [ ] Monitor usage
- [ ] Check error logs
- [ ] Get user feedback
- [ ] Adjust as needed

---

## 💰 Cost Estimates

**Twilio**
- Free tier: $15 credits (~2000 SMS)
- After: ~$0.0075 per SMS
- 100 SMS = $0.75
- 1000 SMS = $7.50

**Nexmo**
- Free trial: Check dashboard
- Rate: ~$0.006 per SMS
- 100 SMS = $0.60
- 1000 SMS = $6.00

**AWS SNS**
- Rate: ~$0.00645 per SMS
- 100 SMS = $0.645
- 1000 SMS = $6.45

---

## 📞 Emergency Contacts

**API Providers**
- Twilio Support: https://www.twilio.com/support
- Nexmo Support: https://support.vonage.com
- AWS Support: https://aws.amazon.com/support

**Documentation**
- Twilio Docs: https://www.twilio.com/docs
- Nexmo Docs: https://developer.vonage.com/docs
- AWS Docs: https://docs.aws.amazon.com/sns/

**Your Files**
- Setup Guide: SMS_API_SETUP.md
- Quick Start: SMS_QUICK_START.md
- API Ref: SMS_API_ENDPOINTS.md

---

## ⏱️ Timing

| Task | Time |
|------|------|
| Choose provider | 2 min |
| Create account | 3 min |
| Get credentials | 2 min |
| Add to .env | 1 min |
| Test | 2 min |
| **Total** | **10 min** |

---

## ✅ Success Indicators

✅ `GET /sms-example/test` works
✅ SMS sends successfully
✅ Phone receives message
✅ Logs show activity
✅ Registration works
✅ Verification arrives

---

## 📄 Quick Links

| Link | Purpose |
|------|---------|
| SMS_QUICK_START.md | 5-minute setup |
| SMS_API_SETUP.md | Detailed guide |
| SMS_API_ENDPOINTS.md | API reference |
| ARCHITECTURE.md | System design |
| README_SMS_MIGRATION.md | Full documentation |

---

## 🎓 Learning Path

1. Read: SMS_QUICK_START.md (5 min)
2. Setup: Choose provider (2 min)
3. Create: Get account (3 min)
4. Configure: Add to .env (1 min)
5. Test: Check endpoints (2 min)
6. Deploy: Go live (5 min)

**Total Time**: ~18 minutes ⏱️

---

**Print this page for reference!**

Save as: `SMS_QUICK_REFERENCE.md`
Last Updated: December 3, 2025
Version: 1.0
