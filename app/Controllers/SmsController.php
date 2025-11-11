<?php

namespace App\Controllers;

use App\Libraries\SmsService;

/**
 * SMS Controller
 * 
 * Handles SMS-related operations
 */
class Sms extends BaseController
{
    /**
     * SMS Service instance
     */
    private SmsService $smsService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->smsService = new SmsService();
    }

    /**
     * Send SMS message
     * 
     * Expected POST parameters:
     * - phone: recipient phone number
     * - message: message content
     */
    public function send()
    {
        // Verify request method
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method Not Allowed'
            ]);
        }

        try {
            // Get input data
            $phone = $this->request->getPost('phone');
            $message = $this->request->getPost('message');

            // Validate input
            if (!$phone || !$message) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Phone number and message are required'
                ]);
            }

            // Send SMS
            $result = $this->smsService->send($phone, $message);

            // Return response
            $statusCode = $result['success'] ? 200 : 400;
            return $this->response->setStatusCode($statusCode)->setJSON($result);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Send SMS to multiple recipients
     * 
     * Expected POST parameters:
     * - phones: JSON array of phone numbers
     * - message: message content
     */
    public function sendBulk()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method Not Allowed'
            ]);
        }

        try {
            $phonesJson = $this->request->getPost('phones');
            $message = $this->request->getPost('message');

            if (!$phonesJson || !$message) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Phone numbers and message are required'
                ]);
            }

            // Decode JSON array
            $phones = json_decode($phonesJson, true);

            if (!is_array($phones) || empty($phones)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Invalid phone numbers format'
                ]);
            }

            // Send bulk SMS
            $results = $this->smsService->sendBulk($phones, $message);

            return $this->response->setStatusCode(200)->setJSON([
                'success' => true,
                'message' => 'Bulk SMS processing completed',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get carrier information
     * 
     * Expected POST parameters:
     * - phone: phone number
     */
    public function getCarrier()
    {
        try {
            $phone = $this->request->getPost('phone');

            if (!$phone) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Phone number is required'
                ]);
            }

            $carrier = $this->smsService->getCarrierName($phone);

            return $this->response->setStatusCode(200)->setJSON([
                'success' => true,
                'phone' => $phone,
                'carrier' => $carrier
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }
}
?>
