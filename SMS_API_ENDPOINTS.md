# SMS API Endpoints Reference

This document lists all available SMS API endpoints after the migration.

---

## Overview

The SMS system now provides multiple ways to send messages:
1. Via the `SmsService` library (code-based)
2. Via the `SmsController` (API endpoints)
3. Via the `SmsExample` controller (testing & examples)

---

## SMS Service Library (Recommended)

### Basic Usage
```php
use App\Libraries\SmsService;

$sms = new SmsService();
$result = $sms->send('+639051234567', 'Hello World');
```

### Available Methods

#### `send(string $phoneNumber, string $message): array`
Send a single SMS message

**Parameters:**
- `$phoneNumber` (string): Phone number with country code (e.g., +639051234567)
- `$message` (string): Message content (max 160 characters for SMS)

**Returns:**
```php
[
    'success' => true|false,
    'message' => 'Status message',
    'phone' => '+639051234567',
    'provider' => 'Twilio|Nexmo|AWS SNS',
    'response' => 'API response'
]
```

**Example:**
```php
$sms = new SmsService();
$result = $sms->send('+639051234567', 'Your verification code is: 123456');

if ($result['success']) {
    echo "SMS sent successfully!";
}
```

---

#### `sendBulk(array $phoneNumbers, string $message): array`
Send SMS to multiple recipients

**Parameters:**
- `$phoneNumbers` (array): Array of phone numbers
- `$message` (string): Message content

**Returns:**
```php
[
    '+639051234567' => ['success' => true, ...],
    '+639171234567' => ['success' => true, ...],
    ...
]
```

**Example:**
```php
$phones = ['+639051234567', '+639171234567'];
$result = $sms->sendBulk($phones, 'Bulk message');

foreach ($result as $phone => $response) {
    echo "Phone: $phone - " . ($response['success'] ? 'Sent' : 'Failed');
}
```

---

#### `getCarrierName(string $phoneNumber): string`
Get carrier information from phone number

**Parameters:**
- `$phoneNumber` (string): Phone number

**Returns:** Carrier name (PLDT, TNT/Smart/Sun, DITO, Unknown)

**Example:**
```php
$carrier = $sms->getCarrierName('+639051234567');
echo $carrier; // Output: PLDT
```

---

#### `setProvider(string $provider): void`
Change SMS provider at runtime

**Parameters:**
- `$provider` (string): Provider name (twilio, nexmo, aws_sns)

**Example:**
```php
$sms = new SmsService();
$sms->setProvider('nexmo');
```

---

#### `setTwilioCredentials(string $accountSid, string $authToken, string $phoneNumber): void`
Set Twilio credentials programmatically

**Example:**
```php
$sms = new SmsService();
$sms->setTwilioCredentials('ACxxx...', 'token', '+1234567890');
$result = $sms->send('+639051234567', 'Message');
```

---

#### `setNexmoCredentials(string $apiKey, string $apiSecret, string $fromName = 'Laundry'): void`
Set Nexmo credentials programmatically

**Example:**
```php
$sms = new SmsService();
$sms->setNexmoCredentials('api_key', 'api_secret', 'MyBrand');
```

---

#### `setAwsCredentials(string $accessKey, string $secretKey, string $region = 'us-east-1'): void`
Set AWS credentials programmatically

**Example:**
```php
$sms = new SmsService();
$sms->setAwsCredentials('access_key', 'secret_key', 'us-east-1');
```

---

## SMS Controller API Endpoints

### Send Single SMS
**Endpoint:** `POST /sms/send`

**Parameters:**
- `phone` (string): Phone number with country code
- `message` (string): Message content

**Example Request:**
```bash
curl -X POST http://yourapp.com/sms/send \
  -d "phone=+639051234567&message=Hello"
```

**Response:**
```json
{
    "success": true,
    "message": "SMS sent successfully",
    "phone": "+639051234567",
    "provider": "Twilio"
}
```

---

### Send Bulk SMS
**Endpoint:** `POST /sms/sendBulk`

**Parameters:**
- `phones` (JSON): Array of phone numbers
- `message` (string): Message content

**Example Request:**
```bash
curl -X POST http://yourapp.com/sms/sendBulk \
  -d 'phones=["[\"09051234567\", \"09171234567\"]&message=Bulk message'
```

**Response:**
```json
{
    "success": true,
    "message": "Bulk SMS processing completed",
    "total": 2,
    "results": {
        "+639051234567": {"success": true, ...},
        "+639171234567": {"success": true, ...}
    }
}
```

---

### Get Carrier Information
**Endpoint:** `POST /sms/getCarrier`

**Parameters:**
- `phone` (string): Phone number

**Example Request:**
```bash
curl -X POST http://yourapp.com/sms/getCarrier \
  -d "phone=09051234567"
```

**Response:**
```json
{
    "success": true,
    "phone": "09051234567",
    "carrier": "PLDT"
}
```

---

## SMS Example Controller (Testing)

### Test Endpoint Status
**Endpoint:** `GET /sms-example/test`

**Response:**
```json
{
    "message": "SMS API is configured and ready to use",
    "supported_providers": ["twilio", "nexmo", "aws_sns"],
    "documentation": "See SMS_API_SETUP.md for configuration instructions"
}
```

---

### Send Test SMS
**Endpoint:** `POST /sms-example/send`

**Parameters:**
- `phone` (string): Phone number
- `message` (string): Message
- `provider` (string, optional): Override default provider

**Example:**
```bash
curl -X POST http://yourapp.com/sms-example/send \
  -d "phone=+639051234567&message=Test&provider=twilio"
```

---

### Send Bulk SMS (Example)
**Endpoint:** `POST /sms-example/send-bulk`

**Parameters (JSON):**
```json
{
    "phones": ["+639051234567", "+639171234567"],
    "message": "Test bulk message"
}
```

---

### Set Credentials (Example)
**Endpoint:** `POST /sms-example/set-credentials`

**For Twilio:**
```json
{
    "provider": "twilio",
    "account_sid": "ACxxx...",
    "auth_token": "your_token",
    "phone": "+1234567890"
}
```

**For Nexmo:**
```json
{
    "provider": "nexmo",
    "api_key": "your_key",
    "api_secret": "your_secret",
    "from_name": "YourBrand"
}
```

**For AWS SNS:**
```json
{
    "provider": "aws_sns",
    "access_key": "your_key",
    "secret_key": "your_secret",
    "region": "us-east-1"
}
```

---

## Current Implementation in Auth

Your existing code in `Auth.php` already uses the SMS service:

```php
public function registerStep2()
{
    // ... validation code ...
    
    try {
        $sms = new SmsService();
        $message = "Your verification code is: {$code}";
        $sms->send($this->request->getPost('phone'), $message);
    } catch (\Exception $e) {
        log_message('error', 'SMS send error: ' . $e->getMessage());
    }
}

public function resendCode()
{
    // ... validation code ...
    
    try {
        $sms = new SmsService();
        $message = "Your verification code is: {$code}";
        $sms->send($pendingReg['phone'], $message);
    } catch (\Exception $e) {
        log_message('error', 'SMS resend error: ' . $e->getMessage());
    }
}
```

✅ **These automatically work with your configured API provider!**

---

## Helper Functions

The `app/Helpers/SmsHelper.php` file provides convenient functions:

```php
// Send single SMS
$result = send_sms('09051234567', 'Message');

// Send bulk SMS
$results = send_sms_bulk(['09051234567', '09171234567'], 'Message');

// Get carrier info
$carrier = get_sms_carrier('09051234567');
```

---

## Error Responses

### Missing Credentials
```json
{
    "success": false,
    "message": "Twilio credentials not configured"
}
```

### Invalid Phone Number
```json
{
    "success": false,
    "message": "Phone number and message are required"
}
```

### API Error
```json
{
    "success": false,
    "message": "Failed to send SMS via Twilio: HTTP Error 401"
}
```

### Server Error
```json
{
    "success": false,
    "message": "Server error: Exception message"
}
```

---

## Quick Troubleshooting

| Issue | Solution |
|-------|----------|
| "Credentials not configured" | Add SMS_PROVIDER and credentials to .env |
| "HTTP Error 401" | Check API credentials are correct |
| "Phone number invalid" | Use format: +639051234567 (with country code) |
| "SMS not sending" | Check logs at writable/logs/sms.log |
| "Provider not supported" | Use: twilio, nexmo, or aws_sns |

---

## Rate Limiting

Each SMS provider has rate limits:
- **Twilio**: 1,000 SMS/hour (free tier)
- **Nexmo**: 1,000 SMS/hour
- **AWS SNS**: Varies by region

Monitor your usage in provider dashboards.

---

**Last Updated**: December 3, 2025
**Migration Status**: ✅ Complete
**All Endpoints**: ✅ Functional
