<?php

namespace App\Libraries;

/**
 * SMS Service Library
 * 
 * Handles SMS sending functionality with support for multiple carriers
 * (PLDT, TNT Smart Sun, DITO)
 */
class SmsService
{
    /**
     * SMS Gateway URL
     */
    private string $gatewayUrl = 'http://192.168.1.251/default/en_US/send.html?';

    /**
     * SMS Gateway Username
     */
    private string $username = 'admin';

    /**
     * SMS Gateway Password
     */
    private string $password = '285952';

    /**
     * Default carrier line
     */
    private string $defaultLine = '1'; // PLDT

    /**
     * PLDT Phone Prefixes
     */
    private array $pldtPrefixes = [
        '0817', '0905', '0906', '0915', '0916', '0917', '0926', '0927',
        '0935', '0936', '0937', '0945', '0955', '0956', '0965', '0966',
        '0967', '0973', '0975', '0976', '0977', '0978', '0979', '0994', '0995', '0996', '0997'
    ];

    /**
     * TNT/Smart/Sun Phone Prefixes
     */
    private array $tntSmartSunPrefixes = [
        '0813', '0907', '0908', '0909', '0910', '0911', '0912', '0913',
        '0914', '0918', '0919', '0921', '0928', '0929', '0930', '0938',
        '0940', '0946', '0947', '0948', '0949', '0950', '0951', '0970',
        '0981', '0989', '0992', '0998', '0999', '0922', '0923', '0924',
        '0925', '0931', '0932', '0933', '0934', '0941', '0942', '0943', '0944'
    ];

    /**
     * DITO Phone Prefixes
     */
    private array $ditoPrefixes = [
        '0991', '0892', '0893', '0894', '0895', '0896', '0897', '0898'
    ];

    /**
     * Send SMS to a phone number
     *
     * @param string $phoneNumber The recipient phone number
     * @param string $message The message to send
     * @return array The response array with status and message
     */
    public function send(string $phoneNumber, string $message): array
    {
        try {
            // Validate inputs
            if (empty($phoneNumber) || empty($message)) {
                return [
                    'success' => false,
                    'message' => 'Phone number and message are required'
                ];
            }

            // Determine carrier line based on phone prefix
            $line = $this->getCarrierLine($phoneNumber);

            // Prepare POST data
            $postData = [
                'u' => $this->username,
                'p' => $this->password,
                'l' => $line,
                'n' => $phoneNumber,
                'm' => $message
            ];

            // Convert array to URL-encoded string
            $postString = http_build_query($postData);

            // Log SMS attempt
            log_message('info', "SMS Request - To: {$phoneNumber}, Carrier: {$line}, Message: {$message}");

            // Initialize cURL
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->gatewayUrl);
            curl_setopt($curl, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postString);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            // Execute request
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            // Check for cURL errors
            if ($response === false) {
                log_message('error', "SMS cURL Error - Phone: {$phoneNumber}, Error: {$curlError}");
                return [
                    'success' => false,
                    'message' => 'Failed to send SMS: ' . $curlError
                ];
            }

            // Check HTTP response code
            if ($httpCode !== 200) {
                log_message('warning', "SMS HTTP Error - Phone: {$phoneNumber}, HTTP Code: {$httpCode}");
            }

            // Log successful send
            log_message('info', "SMS Sent Successfully - To: {$phoneNumber}, Response: {$response}");

            return [
                'success' => true,
                'message' => 'SMS sent successfully',
                'phone' => $phoneNumber,
                'carrier' => $this->getCarrierName($phoneNumber),
                'response' => $response
            ];

        } catch (\Exception $e) {
            log_message('error', "SMS Exception - Message: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Error sending SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Determine carrier line based on phone prefix
     *
     * @param string $phoneNumber The phone number
     * @return string The carrier line ID
     */
    private function getCarrierLine(string $phoneNumber): string
    {
        // Extract first 4 digits
        $prefix = substr($phoneNumber, 0, 4);

        // Check PLDT
        if (in_array($prefix, $this->pldtPrefixes)) {
            return '1';
        }

        // Check TNT/Smart/Sun
        if (in_array($prefix, $this->tntSmartSunPrefixes)) {
            return '2';
        }

        // Check DITO
        if (in_array($prefix, $this->ditoPrefixes)) {
            return '3';
        }

        // Default to PLDT
        return $this->defaultLine;
    }

    /**
     * Send SMS to multiple recipients
     *
     * @param array $phoneNumbers Array of phone numbers
     * @param string $message The message to send
     * @return array Array of results for each phone number
     */
    public function sendBulk(array $phoneNumbers, string $message): array
    {
        $results = [];

        foreach ($phoneNumbers as $phoneNumber) {
            $results[$phoneNumber] = $this->send($phoneNumber, $message);
        }

        return $results;
    }

    /**
     * Get carrier information from phone number
     *
     * @param string $phoneNumber The phone number
     * @return string The carrier name
     */
    public function getCarrierName(string $phoneNumber): string
    {
        $prefix = substr($phoneNumber, 0, 4);

        if (in_array($prefix, $this->pldtPrefixes)) {
            return 'PLDT';
        }

        if (in_array($prefix, $this->tntSmartSunPrefixes)) {
            return 'TNT/Smart/Sun';
        }

        if (in_array($prefix, $this->ditoPrefixes)) {
            return 'DITO';
        }

        return 'Unknown';
    }
}
?>
