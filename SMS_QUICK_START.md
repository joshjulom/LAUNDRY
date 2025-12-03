# SMS API - Quick Start Guide

Get your SMS system working in 5 minutes!

---

## 1️⃣ Choose Your Provider (1 minute)

### Best Options:

**Twilio** (Recommended)
- Free $15 credits
- Easy to use
- Excellent documentation
- https://www.twilio.com

**Nexmo/Vonage** (Alternative)
- Good for international
- https://dashboard.nexmo.com

**AWS SNS** (If using AWS)
- https://aws.amazon.com/sns

👉 **Start with Twilio** - easiest setup

---

## 2️⃣ Create Account & Get Credentials (2 minutes)

### For Twilio:
1. Go to https://www.twilio.com
2. Sign up (verify email & phone)
3. Go to https://www.twilio.com/console
4. Copy your **Account SID** (ACxxx...)
5. Copy your **Auth Token**
6. Get a Twilio phone number (or verify your own)

Example credentials:
```
Account SID: ACa1b2c3d4e5f6g7h8i9j0
Auth Token: 1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p
Phone: +1415123456
```

---

## 3️⃣ Add to Your App (1 minute)

### Option A: Via .env File (Recommended)

1. Open `.env` file in project root
2. Add these lines:

```env
SMS_PROVIDER=twilio
SMS_TWILIO_ACCOUNT_SID=ACa1b2c3d4e5f6g7h8i9j0
SMS_TWILIO_AUTH_TOKEN=1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p
SMS_TWILIO_PHONE=+1415123456
```

3. Save and done! ✅

### Option B: Via Config File

1. Open `app/Config/Sms.php`
2. Fill in the Twilio properties:

```php
public string $provider = 'twilio';
public string $twilioAccountSid = 'ACa1b2c3d4e5f6g7h8i9j0';
public string $twilioAuthToken = '1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p';
public string $twilioPhoneNumber = '+1415123456';
```

3. Save ✅

---

## 4️⃣ Test It Works (1 minute)

### Test via Code

Create a test script at `app/Controllers/Test.php`:

```php
<?php
namespace App\Controllers;
use App\Libraries\SmsService;

class Test extends BaseController {
    public function sms() {
        $sms = new SmsService();
        $result = $sms->send('+639051234567', 'Test message');
        
        if ($result['success']) {
            echo "✅ SMS sent successfully!<br>";
            echo "Response: " . json_encode($result);
        } else {
            echo "❌ Error: " . $result['message'];
        }
    }
}
?>
```

Then visit: `http://yourapp.com/test/sms`

### Test via API

```bash
curl -X POST http://yourapp.com/sms-example/send \
  -d "phone=+639051234567&message=Hello"
```

### Test via Example Controller

Visit: `http://yourapp.com/sms-example/test`

---

## ✅ You're Done!

Your SMS system now works with the cloud API!

### What Now Works:

✅ SMS verification codes in registration
✅ Bulk SMS notifications
✅ Any SMS sending in your app

### Next Steps:

1. **Replace device IP**: The system no longer needs `192.168.1.251`
2. **Test verification flow**: Try registering a new account
3. **Monitor logs**: Check `writable/logs/` for details
4. **Go live**: Deploy to production

---

## Common Issues & Fixes

### "Credentials not configured"
```
Fix: Make sure you added credentials to .env or Sms.php
```

### "HTTP Error 401"
```
Fix: Your credentials are wrong. Double-check in Twilio console.
```

### SMS Not Arriving
```
Fix 1: Check phone number format (+639051234567, not just 09051234567)
Fix 2: Check Twilio account has trial or paid status
Fix 3: Check logs: writable/logs/
```

### "Invalid URL"
```
Fix: Make sure SMS_PROVIDER=twilio is set
```

---

## Testing with Your Real App

Your existing code already works! Just test:

1. Go to registration page
2. Enter phone number
3. Should receive SMS with verification code
4. Verify it works ✅

---

## Free Testing Tips

### Use Twilio Free Trial
- $15 free credits
- ~2000 SMS messages
- Perfect for testing

### Test Phone Numbers
1. In Twilio console, add your phone as verified
2. Create sandbox if in trial mode
3. Test SMS to your own phone

### Monitor Usage
- Log into Twilio console
- Check SMS count
- View delivery reports

---

## Security Reminder

⚠️ **IMPORTANT:**
- Never commit `.env` to git
- Keep credentials secret
- Rotate tokens periodically
- Don't share credentials

---

## Phone Number Format

Make sure you use the correct format:

✅ **Correct:**
- +639051234567 (Philippines)
- +1415123456 (USA)
- +447911123456 (UK)

❌ **Wrong:**
- 09051234567 (missing country code)
- 639051234567 (missing +)
- 9051234567 (incomplete)

---

## Support

Need help?

1. **Setup Guide**: Read `SMS_API_SETUP.md`
2. **API Reference**: Read `SMS_API_ENDPOINTS.md`
3. **Migration Info**: Read `SMS_MIGRATION_SUMMARY.md`
4. **Twilio Docs**: https://www.twilio.com/docs

---

## Pricing

**Twilio**: ~$0.0075 per SMS (after free credits)

**Example costs:**
- 100 SMS = $0.75
- 1,000 SMS = $7.50
- 10,000 SMS = $75

---

**Ready to go?** 🚀

Next: Test your SMS by creating an account!

---

*Last Updated: December 3, 2025*
*Status: ✅ Ready to Use*
