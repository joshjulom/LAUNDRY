# SMS API Integration Guide

This guide will help you set up SMS sending using a third-party API instead of a local device connection.

## Overview

Your laundry system now supports three major SMS API providers:

1. **Twilio** (Recommended for most use cases)
2. **Nexmo/Vonage** (Good for international coverage)
3. **AWS SNS** (If you're already using AWS)

---

## Quick Setup

### Option 1: Twilio (RECOMMENDED)

#### Step 1: Create Twilio Account
- Go to https://www.twilio.com
- Sign up for a free account
- Complete phone number verification

#### Step 2: Get Your Credentials
- Go to https://www.twilio.com/console
- Find your **Account SID**
- Find your **Auth Token**
- Get a Twilio phone number or verify your own number

#### Step 3: Configure in Your App

**Method A: Using .env file**
1. Open your `.env` file in the project root
2. Add these lines:
```
SMS_PROVIDER=twilio
SMS_TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxx
SMS_TWILIO_AUTH_TOKEN=your_auth_token_here
SMS_TWILIO_PHONE=+1234567890
```

**Method B: Using Config file**
1. Open `app/Config/Sms.php`
2. Update the properties:
```php
public string $provider = 'twilio';
public string $twilioAccountSid = 'your_account_sid';
public string $twilioAuthToken = 'your_auth_token';
public string $twilioPhoneNumber = '+1234567890';
```

#### Step 4: Test SMS Sending
```php
$sms = new \App\Libraries\SmsService();
$result = $sms->send('+639051234567', 'Test message');
if ($result['success']) {
    echo "SMS sent successfully!";
} else {
    echo "Error: " . $result['message'];
}
```

---

### Option 2: Nexmo/Vonage

#### Step 1: Create Account
- Go to https://dashboard.nexmo.com
- Sign up for a free account
- Complete email verification

#### Step 2: Get Your Credentials
- Go to Your API Settings
- Find your **API Key**
- Find your **API Secret**
- Set up a Sender ID (Brand name - max 11 characters)

#### Step 3: Configure

**Using .env file:**
```
SMS_PROVIDER=nexmo
SMS_NEXMO_API_KEY=your_api_key_here
SMS_NEXMO_API_SECRET=your_api_secret_here
SMS_NEXMO_FROM=Laundry
```

**Using Config file:**
```php
public string $provider = 'nexmo';
public string $nexmoApiKey = 'your_key';
public string $nexmoApiSecret = 'your_secret';
public string $nexmoFromName = 'Laundry';
```

#### Step 4: Test
```php
$sms = new \App\Libraries\SmsService();
$result = $sms->send('639051234567', 'Test message');
```

---

### Option 3: AWS SNS

#### Step 1: AWS Setup
- Go to AWS Console
- Create an IAM user with SNS permissions
- Generate Access Key and Secret Key

#### Step 2: Get Your Credentials
- AWS Access Key ID
- AWS Secret Access Key
- Choose region (e.g., us-east-1, eu-west-1)

#### Step 3: Configure

**Using .env file:**
```
SMS_PROVIDER=aws_sns
SMS_AWS_ACCESS_KEY=your_access_key
SMS_AWS_SECRET_KEY=your_secret_key
SMS_AWS_REGION=us-east-1
```

---

## Usage Examples

### Send Single SMS
```php
use App\Libraries\SmsService;

$sms = new SmsService();
$result = $sms->send('+639051234567', 'Hello, this is a test message');

if ($result['success']) {
    echo "SMS sent successfully!";
    echo "Provider: " . $result['provider'];
} else {
    echo "Error: " . $result['message'];
}
```

### Send Bulk SMS
```php
$phones = ['+639051234567', '+639171234567', '+639251234567'];
$result = $sms->sendBulk($phones, 'This is a bulk message');

foreach ($result as $phone => $response) {
    echo "Phone: $phone - " . ($response['success'] ? 'Sent' : 'Failed');
}
```

### Change Provider at Runtime
```php
$sms = new SmsService();
$sms->setProvider('nexmo');
$result = $sms->send('+639051234567', 'Message');
```

### Set Credentials at Runtime
```php
$sms = new SmsService();
$sms->setTwilioCredentials('ACxxx...', 'auth_token', '+1234567890');
$result = $sms->send('+639051234567', 'Message');
```

---

## Troubleshooting

### "Credentials not configured" Error
- Make sure you've added your credentials to `.env` or `app/Config/Sms.php`
- Check that environment variables are loaded

### SMS Not Sending
1. Check your API credentials are correct
2. Verify phone number format (include country code: +63 for Philippines)
3. Check logs in `writable/logs/` for detailed errors
4. Verify API account has balance/credits

### "HTTP Error 401" from API
- Your credentials are incorrect or expired
- Regenerate tokens from API provider's dashboard

### "Phone number invalid"
- Ensure phone number format includes country code
- Example: +639051234567 (not just 09051234567)

---

## Existing Code Integration

Your existing code in `app/Controllers/Auth.php` already uses SMS sending:

```php
try {
    $sms = new SmsService();
    $message = "Your verification code is: {$code}";
    $sms->send($this->request->getPost('phone'), $message);
} catch (\Exception $e) {
    log_message('error', 'SMS send error: ' . $e->getMessage());
}
```

This will automatically work with your configured API provider!

---

## Pricing Comparison

| Provider | Cost | Best For | Coverage |
|----------|------|----------|----------|
| Twilio | ~$0.0075/SMS | General use | 200+ countries |
| Nexmo | ~$0.006/SMS | International | 200+ countries |
| AWS SNS | ~$0.00645/SMS | AWS users | 200+ countries |

---

## Security Best Practices

1. **Never commit credentials to git**
   - Use `.env` file (add to `.gitignore`)
   - Never hardcode API keys in code

2. **Rotate credentials regularly**
   - Update tokens periodically
   - Remove old keys from API provider

3. **Use environment variables in production**
   - Set via hosting provider's control panel
   - Don't store in repository

4. **Monitor API usage**
   - Check your API provider's dashboard
   - Set up alerts for unusual activity

5. **Validate phone numbers**
   - Sanitize user input before sending
   - Use proper phone number format

---

## Next Steps

1. Choose your SMS provider
2. Create an account and get credentials
3. Add credentials to `.env` file
4. Test with a simple SMS send
5. Deploy to production

---

## Support & Resources

- **Twilio**: https://www.twilio.com/docs
- **Nexmo/Vonage**: https://developer.vonage.com/docs
- **AWS SNS**: https://docs.aws.amazon.com/sns/

---

Generated: 2025-12-03
Updated SMS System: API-based (no local device connection)
