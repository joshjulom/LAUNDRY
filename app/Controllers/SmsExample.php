<?php

namespace App\Controllers;

use App\Libraries\SmsService;

/**
 * SMS API Examples Controller
 * 
 * This controller demonstrates how to use the new API-based SMS service
 * 
 * Route Examples:
 * - GET /sms-example/test
 * - POST /sms-example/send
 */
class SmsExample extends BaseController
{
    /**
     * Display SMS examples page
     */
    public function index()
    {
        return view('sms_examples');
    }

    /**
     * Test SMS sending with all providers
     * GET: /sms-example/test
     */
    public function test()
    {
        return $this->response->setJSON([
            'message' => 'SMS API is configured and ready to use',
            'supported_providers' => ['twilio', 'nexmo', 'aws_sns'],
            'documentation' => 'See SMS_API_SETUP.md for configuration instructions'
        ]);
    }

    /**
     * Send a test SMS
     * POST: /sms-example/send
     * 
     * Expected parameters:
     * - phone: recipient phone number (with country code, e.g., +639051234567)
     * - message: message content
     * - provider: (optional) provider to use
     */
    public function send()
    {
        if (!$this->request->is('post')) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Method Not Allowed']);
        }

        try {
            $phone = $this->request->getPost('phone');
            $message = $this->request->getPost('message');
            $provider = $this->request->getPost('provider');

            // Validate input
            if (!$phone || !$message) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Phone number and message are required'
                    ]);
            }

            // Initialize SMS service
            $sms = new SmsService();

            // Set provider if specified
            if ($provider) {
                $sms->setProvider($provider);
            }

            // Send SMS
            $result = $sms->send($phone, $message);

            // Return result
            $statusCode = $result['success'] ? 200 : 400;
            return $this->response
                ->setStatusCode($statusCode)
                ->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'SMS Test Error: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Send bulk SMS
     * POST: /sms-example/send-bulk
     * 
     * Expected parameters (JSON):
     * {
     *   "phones": ["+639051234567", "+639171234567"],
     *   "message": "Your message here"
     * }
     */
    public function sendBulk()
    {
        if (!$this->request->is('post')) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Method Not Allowed']);
        }

        try {
            $phonesJson = $this->request->getPost('phones');
            $message = $this->request->getPost('message');

            // Validate input
            if (!$phonesJson || !$message) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Phone numbers array and message are required'
                    ]);
            }

            // Decode JSON
            $phones = json_decode($phonesJson, true);

            if (!is_array($phones) || empty($phones)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Invalid phone numbers format'
                    ]);
            }

            // Send bulk SMS
            $sms = new SmsService();
            $results = $sms->sendBulk($phones, $message);

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'success' => true,
                    'message' => 'Bulk SMS processing completed',
                    'total' => count($results),
                    'results' => $results
                ]);

        } catch (\Exception $e) {
            log_message('error', 'Bulk SMS Error: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Get carrier information from phone number
     * POST: /sms-example/carrier
     * 
     * Expected parameters:
     * - phone: phone number
     */
    public function getCarrier()
    {
        try {
            $phone = $this->request->getPost('phone');

            if (!$phone) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Phone number is required'
                    ]);
            }

            $sms = new SmsService();
            $carrier = $sms->getCarrierName($phone);

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'success' => true,
                    'phone' => $phone,
                    'carrier' => $carrier
                ]);

        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Set API credentials at runtime
     * POST: /sms-example/set-credentials
     * 
     * Expected parameters (for Twilio):
     * {
     *   "provider": "twilio",
     *   "account_sid": "ACxxx...",
     *   "auth_token": "your_token",
     *   "phone": "+1234567890"
     * }
     */
    public function setCredentials()
    {
        if (!$this->request->is('post')) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Method Not Allowed']);
        }

        try {
            $provider = $this->request->getPost('provider');
            $sms = new SmsService();

            switch ($provider) {
                case 'twilio':
                    $accountSid = $this->request->getPost('account_sid');
                    $authToken = $this->request->getPost('auth_token');
                    $phone = $this->request->getPost('phone');

                    $sms->setTwilioCredentials($accountSid, $authToken, $phone);
                    break;

                case 'nexmo':
                    $apiKey = $this->request->getPost('api_key');
                    $apiSecret = $this->request->getPost('api_secret');
                    $fromName = $this->request->getPost('from_name') ?? 'Laundry';

                    $sms->setNexmoCredentials($apiKey, $apiSecret, $fromName);
                    break;

                case 'aws_sns':
                    $accessKey = $this->request->getPost('access_key');
                    $secretKey = $this->request->getPost('secret_key');
                    $region = $this->request->getPost('region') ?? 'us-east-1';

                    $sms->setAwsCredentials($accessKey, $secretKey, $region);
                    break;

                default:
                    return $this->response
                        ->setStatusCode(400)
                        ->setJSON([
                            'success' => false,
                            'message' => 'Invalid provider. Use: twilio, nexmo, aws_sns'
                        ]);
            }

            return $this->response
                ->setStatusCode(200)
                ->setJSON([
                    'success' => true,
                    'message' => "Credentials set for provider: {$provider}"
                ]);

        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
        }
    }
}
?>
