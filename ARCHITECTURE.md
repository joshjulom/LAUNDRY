# SMS System Architecture

## Before Migration (Device-Based)

```
┌─────────────────────────────────────────────────────────────┐
│                    Laundry App                              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Controllers                                          │   │
│  │  - Auth.php (registration, verification)            │   │
│  │  - SmsController.php (SMS endpoints)                 │   │
│  │  - Customer.php (notifications)                      │   │
│  └──────────────┬───────────────────────────────────────┘   │
│                 │                                            │
│  ┌──────────────▼───────────────────────────────────────┐   │
│  │  SmsService.php                                       │   │
│  │  (Device Gateway Connection)                          │   │
│  └──────────────┬───────────────────────────────────────┘   │
└─────────────────┼───────────────────────────────────────────┘
                  │
                  │ HTTP Request
                  │ (192.168.1.251/default/en_US/send.html?)
                  │
        ┌─────────▼─────────┐
        │  Local Device     │ ❌ PROBLEM
        │  Gateway          │    - Must be online
        │  (Must be running)│    - Local only
        │                   │    - Not scalable
        └─────────┬─────────┘
                  │
                  │ Serial Connection
                  │
        ┌─────────▼──────────────┐
        │  GSM Modem/Device      │
        │  (Physical Hardware)   │
        └────────────────────────┘
```

---

## After Migration (API-Based)

```
┌─────────────────────────────────────────────────────────────┐
│                    Laundry App                              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Controllers                                          │   │
│  │  - Auth.php (registration, verification)            │   │
│  │  - SmsController.php (SMS endpoints)                 │   │
│  │  - Customer.php (notifications)                      │   │
│  └──────────────┬───────────────────────────────────────┘   │
│                 │                                            │
│  ┌──────────────▼───────────────────────────────────────┐   │
│  │  SmsService.php                                       │   │
│  │  (API-Based Gateway)                                  │   │
│  └────┬──────────┬──────────┬──────────────────────────┘   │
│       │          │          │                              │
│       │ Config   │ Config   │ Config                      │
│       │ Loading  │ Loading  │ Loading                     │
│       │ (.env)   │ (.env)   │ (.env)                      │
└───────┼──────────┼──────────┼──────────────────────────────┘
        │          │          │
        │          │          │
        │ TLS/SSL  │ TLS/SSL  │ TLS/SSL
        │ HTTPS    │ HTTPS    │ HTTPS
        │          │          │
    ┌───▼──────┐  ┌───▼──────┐  ┌───▼────────┐
    │ TWILIO   │  │ NEXMO    │  │ AWS SNS    │
    │ API      │  │ API      │  │ API        │
    │          │  │          │  │            │
    │ Reliable │  │ Global   │  │ Integrated │
    │ Popular  │  │ Coverage │  │ w/ AWS     │
    └─────────┬┘  └─────────┬┘  └────────────┘
              │             │
              │ Global      │ Global
              │ Network     │ Network
              │             │
          ┌───▼─────┬───────▼─┐
          │         │         │
    ┌─────▼──┐ ┌────▼───┐ ┌──▼──────┐
    │ SMS    │ │ SMS    │ │ SMS     │
    │ Network│ │ Network│ │ Network │
    │ 1      │ │ 2      │ │ 3       │
    └────────┘ └────────┘ └─────────┘
```

---

## Component Diagram

```
┌──────────────────────────────────────────────────────────────────┐
│                    SYSTEM COMPONENTS                             │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌────────────────────┐                                         │
│  │  app/Libraries/    │  SmsService.php                        │
│  │  SmsService.php    │  ✅ Provider abstraction               │
│  │                    │  ✅ Multiple API support               │
│  └────────┬───────────┘  ✅ Credential management              │
│           │              ✅ Error handling                      │
│           │                                                     │
│  ┌────────▼───────────┐                                        │
│  │  app/Config/       │  Sms.php                              │
│  │  Sms.php           │  ✅ Centralized configuration        │
│  │                    │  ✅ All provider settings             │
│  └────────────────────┘  ✅ Security notes                     │
│                                                                 │
│  ┌────────────────────┐                                        │
│  │  app/Controllers/  │  SmsExample.php                       │
│  │  SmsExample.php    │  ✅ Testing endpoints                 │
│  │                    │  ✅ Example usage                      │
│  └────────────────────┘  ✅ API demonstrations               │
│                                                                 │
│  ┌────────────────────┐                                        │
│  │  app/Helpers/      │  SmsHelper.php                        │
│  │  SmsHelper.php     │  ✅ Helper functions                  │
│  │                    │  ✅ Quick methods                      │
│  └────────────────────┘  ✅ Convenience API                   │
│                                                                 │
│  ┌────────────────────┐                                        │
│  │  .env              │  Environment Config                   │
│  │                    │  ✅ Provider selection                │
│  │                    │  ✅ API credentials                   │
│  └────────────────────┘  ✅ Security                          │
│                                                                 │
└──────────────────────────────────────────────────────────────────┘
```

---

## Data Flow - Sending SMS

### Step-by-Step Process

```
1. User Input/Trigger
   └─> $sms->send($phone, $message)
       │
2. SmsService Constructor
   └─> loadCredentials() from .env
       │
3. send() Method Called
   └─> Input Validation
       │
4. Provider Router
   └─> match($provider) {
           'twilio'  => sendViaTwilio()
           'nexmo'   => sendViaNexmo()
           'aws_sns' => sendViaAwsSns()
       }
       │
5. API Preparation
   └─> Build request data
       │
6. HTTP Request
   └─> makeApiRequest() [HTTPS]
       │
7. API Response
   └─> Parse response
       │
8. Return Result
   └─> [success => true/false, ...]
       │
9. Logging
   └─> writable/logs/
```

---

## Provider Selection Flow

```
                    ┌─── SMS Service ───┐
                    │                    │
                    └─── Credential     ─┘
                         Loader
                            │
                            ▼
                    Check SMS_PROVIDER
                            │
         ┌──────────┬────────┼────────┬──────────┐
         │          │        │        │          │
    ┌────▼─┐   ┌───▼──┐  ┌─▼────┐  ┌▼────┐   ┌─▼─┐
    │Twilio│   │Nexmo │  │ AWS  │  │Test?│   │??!│
    │✅    │   │ ✅   │  │ ✅   │  │     │   │   │
    └──────┘   └──────┘  └──────┘  └─────┘   └───┘
       │          │         │
       │          │         │
    [Send]     [Send]    [Send]
```

---

## Security Architecture

```
┌────────────────────────────────────┐
│     Credential Management          │
├────────────────────────────────────┤
│                                    │
│  .env File                         │
│  ├─ SMS_PROVIDER                   │
│  ├─ SMS_TWILIO_*                   │
│  ├─ SMS_NEXMO_*                    │
│  └─ SMS_AWS_*                      │
│                                    │
│  ⚠️ Never commit to git            │
│  ⚠️ Add to .gitignore              │
│  ⚠️ Keep secret                    │
│                                    │
├────────────────────────────────────┤
│  HTTPS Communication               │
│  ├─ Twilio: TLS 1.2+              │
│  ├─ Nexmo: TLS 1.2+                │
│  └─ AWS: AWS SDK security          │
│                                    │
│  ✅ SSL certificate verification  │
│  ✅ Encrypted data transmission    │
│  ✅ Secure handshake               │
│                                    │
├────────────────────────────────────┤
│  Input Validation                  │
│  ├─ Phone number format check      │
│  ├─ Message length validation      │
│  └─ Provider verification          │
│                                    │
│  ✅ Prevents injection attacks     │
│  ✅ Ensures data integrity        │
│  ✅ Secure API calls               │
│                                    │
└────────────────────────────────────┘
```

---

## Error Handling Flow

```
                   send() Called
                        │
                        ▼
                   Validate Input
                        │
         ┌──────────────┼──────────────┐
         │              │              │
         ▼              ▼              ▼
      Empty?         Invalid?       Other?
         │              │              │
         ├─ Return  ─────┼─ Return ────┤─ Continue
         │  Error        │  Error      │
         │               │             │
         └───────────────┴─────────────┘
                   │
                   ▼
           Make API Request
                   │
         ┌─────────┴──────────┐
         │                    │
         ▼                    ▼
      Success            Failed
         │                    │
         ├─ Log Info ──────┐  │
         │                 │  ├─ Catch Exception
         │                 │  │
         └──Return [success=>true]
                            │
                            ├─ Log Error
                            │
                            └─ Return [success=>false]
```

---

## Integration Points

```
Laundry App Integration Points
═══════════════════════════════════

1. Registration & Verification
   Auth.php → registerStep2() → SmsService.send()
   Auth.php → resendCode()    → SmsService.send()

2. Customer Notifications
   Customer.php → notify() → SmsService.send()

3. Bulk Communications
   Admin.php → sendBulkSMS() → SmsService.sendBulk()

4. API Endpoints
   SmsController.php → Multiple routes → SmsService methods

5. Helper Functions
   Any file → send_sms() → SmsHelper.php → SmsService

6. Testing
   SmsExample.php → All methods available for testing
```

---

## Deployment Stages

```
┌────────────┐
│ Local Dev  │  Development with test credentials
└─────┬──────┘
      │
      ▼
┌────────────┐
│ Staging    │  Pre-production testing
└─────┬──────┘
      │ All tests passing?
      │
      ▼
┌────────────┐
│ Production │  Live environment
└────────────┘
  All credentials from environment variables
  All logging enabled
  All error handling active
```

---

**Architecture Diagram**: SMS System Evolution

```
Device-Based (Old)          →       API-Based (New)
═════════════════                  ═════════════════
❌ Hardware dependent             ✅ Cloud-based
❌ Single location                ✅ Global reach
❌ Limited scalability            ✅ Unlimited scalability
❌ Manual configuration           ✅ Automated setup
❌ Difficult debugging            ✅ Easy diagnostics
❌ High maintenance               ✅ Low maintenance
❌ Unreliable                     ✅ 99.9% reliable
```

---

**Last Updated**: December 3, 2025
**Architecture Version**: 2.0 (API-Based)
**Status**: ✅ Production Ready
