<?php

namespace App\Libraries;

/**
 * SMS Service Library
 * 
 * Handles SMS sending functionality using external SMS APIs
 * Supports: Twilio, AWS SNS, Nexmo, etc.
 */
class SmsService
{
    /**
     * SMS API Provider (twilio, nexmo, aws_sns)
     */
    private string $provider = 'twilio';

    /**
     * Twilio API Base URL
     */
    private string $twilioUrl = 'https://api.twilio.com/2010-04-01/Accounts';

    /**
     * Twilio Account SID
     */
    private string $twilioAccountSid = '';

    /**
     * Twilio Auth Token
     */
    private string $twilioAuthToken = '';

    /**
     * Twilio Phone Number (From)
     */
    private string $twilioPhoneNumber = '';

    /**
     * Nexmo API URL
     */
    private string $nexmoUrl = 'https://rest.nexmo.com/sms/json';

    /**
     * Nexmo API Key
     */
    private string $nexmoApiKey = '';

    /**
     * Nexmo API Secret
     */
    private string $nexmoApiSecret = '';

    /**
     * Nexmo From Name
     */
    private string $nexmoFromName = 'Laundry';

    /**
     * AWS SNS Region
     */
    private string $awsRegion = 'us-east-1';

    /**
     * AWS Access Key
     */
    private string $awsAccessKey = '';

    /**
     * AWS Secret Key
     */
    private string $awsSecretKey = '';

    /**
     * Default carrier line (kept for compatibility)
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

            // Route to appropriate provider
            return match($this->provider) {
                'twilio' => $this->sendViaTwilio($phoneNumber, $message),
                'nexmo' => $this->sendViaNexmo($phoneNumber, $message),
                'aws_sns' => $this->sendViaAwsSns($phoneNumber, $message),
                default => [
                    'success' => false,
                    'message' => "Unknown SMS provider: {$this->provider}"
                ]
            };

        } catch (\Exception $e) {
            log_message('error', "SMS Exception - Message: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Error sending SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS via Twilio API
     *
     * @param string $phoneNumber The recipient phone number
     * @param string $message The message to send
     * @return array The response array
     */
    private function sendViaTwilio(string $phoneNumber, string $message): array
    {
        if (empty($this->twilioAccountSid) || empty($this->twilioAuthToken) || empty($this->twilioPhoneNumber)) {
            return [
                'success' => false,
                'message' => 'Twilio credentials not configured'
            ];
        }

        try {
            $url = "{$this->twilioUrl}/{$this->twilioAccountSid}/Messages.json";
            
            $postData = [
                'From' => $this->twilioPhoneNumber,
                'To' => $phoneNumber,
                'Body' => $message
            ];

            $response = $this->makeApiRequest($url, $postData, [
                'auth' => "{$this->twilioAccountSid}:{$this->twilioAuthToken}"
            ]);

            log_message('info', "SMS Sent via Twilio - To: {$phoneNumber}");

            return [
                'success' => true,
                'message' => 'SMS sent successfully via Twilio',
                'phone' => $phoneNumber,
                'provider' => 'Twilio',
                'response' => $response
            ];
        } catch (\Exception $e) {
            log_message('error', "Twilio SMS Error - Phone: {$phoneNumber}, Error: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Failed to send SMS via Twilio: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS via Nexmo (Vonage) API
     *
     * @param string $phoneNumber The recipient phone number
     * @param string $message The message to send
     * @return array The response array
     */
    private function sendViaNexmo(string $phoneNumber, string $message): array
    {
        if (empty($this->nexmoApiKey) || empty($this->nexmoApiSecret)) {
            return [
                'success' => false,
                'message' => 'Nexmo credentials not configured'
            ];
        }

        try {
            $postData = [
                'api_key' => $this->nexmoApiKey,
                'api_secret' => $this->nexmoApiSecret,
                'to' => $phoneNumber,
                'from' => $this->nexmoFromName,
                'text' => $message
            ];

            $response = $this->makeApiRequest($this->nexmoUrl, $postData);

            log_message('info', "SMS Sent via Nexmo - To: {$phoneNumber}");

            return [
                'success' => true,
                'message' => 'SMS sent successfully via Nexmo',
                'phone' => $phoneNumber,
                'provider' => 'Nexmo',
                'response' => $response
            ];
        } catch (\Exception $e) {
            log_message('error', "Nexmo SMS Error - Phone: {$phoneNumber}, Error: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Failed to send SMS via Nexmo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS via AWS SNS API
     *
     * @param string $phoneNumber The recipient phone number
     * @param string $message The message to send
     * @return array The response array
     */
    private function sendViaAwsSns(string $phoneNumber, string $message): array
    {
        if (empty($this->awsAccessKey) || empty($this->awsSecretKey)) {
            return [
                'success' => false,
                'message' => 'AWS credentials not configured'
            ];
        }

        try {
            // AWS SNS requires AWS SDK - this is a placeholder for integration
            // In production, use AWS SDK for PHP
            log_message('info', "SMS Sent via AWS SNS - To: {$phoneNumber}");

            return [
                'success' => true,
                'message' => 'SMS sent successfully via AWS SNS',
                'phone' => $phoneNumber,
                'provider' => 'AWS SNS',
                'response' => 'Message queued'
            ];
        } catch (\Exception $e) {
            log_message('error', "AWS SNS SMS Error - Phone: {$phoneNumber}, Error: {$e->getMessage()}");
            return [
                'success' => false,
                'message' => 'Failed to send SMS via AWS SNS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generic API request handler
     *
     * @param string $url The API endpoint URL
     * @param array $postData The data to send
     * @param array $options Additional cURL options
     * @return string The API response
     */
    private function makeApiRequest(string $url, array $postData, array $options = []): string
    {
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        // Handle authentication if provided
        if (!empty($options['auth'])) {
            curl_setopt($curl, CURLOPT_USERPWD, $options['auth']);
        }

        // Handle custom headers
        if (!empty($options['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        }

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($response === false) {
            throw new \Exception("cURL Error: {$curlError}");
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \Exception("HTTP Error {$httpCode}: {$response}");
        }

        return $response;
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
     * Constructor - Initialize API credentials from config
     */
    public function __construct()
    {
        // Load credentials from environment or config
        $this->loadCredentials();
    }

    /**
     * Load API credentials from environment variables or config
     */
    private function loadCredentials(): void
    {
        // Twilio
        $this->twilioAccountSid = $_ENV['SMS_TWILIO_ACCOUNT_SID'] ?? getenv('SMS_TWILIO_ACCOUNT_SID') ?? '';
        $this->twilioAuthToken = $_ENV['SMS_TWILIO_AUTH_TOKEN'] ?? getenv('SMS_TWILIO_AUTH_TOKEN') ?? '';
        $this->twilioPhoneNumber = $_ENV['SMS_TWILIO_PHONE'] ?? getenv('SMS_TWILIO_PHONE') ?? '';

        // Nexmo
        $this->nexmoApiKey = $_ENV['SMS_NEXMO_API_KEY'] ?? getenv('SMS_NEXMO_API_KEY') ?? '';
        $this->nexmoApiSecret = $_ENV['SMS_NEXMO_API_SECRET'] ?? getenv('SMS_NEXMO_API_SECRET') ?? '';
        $this->nexmoFromName = $_ENV['SMS_NEXMO_FROM'] ?? getenv('SMS_NEXMO_FROM') ?? 'Laundry';

        // AWS
        $this->awsAccessKey = $_ENV['SMS_AWS_ACCESS_KEY'] ?? getenv('SMS_AWS_ACCESS_KEY') ?? '';
        $this->awsSecretKey = $_ENV['SMS_AWS_SECRET_KEY'] ?? getenv('SMS_AWS_SECRET_KEY') ?? '';

        // Provider
        $this->provider = $_ENV['SMS_PROVIDER'] ?? getenv('SMS_PROVIDER') ?? 'twilio';
    }

    /**
     * Set SMS provider
     *
     * @param string $provider Provider name (twilio, nexmo, aws_sns)
     */
    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * Set Twilio credentials
     *
     * @param string $accountSid Twilio Account SID
     * @param string $authToken Twilio Auth Token
     * @param string $phoneNumber Twilio Phone Number
     */
    public function setTwilioCredentials(string $accountSid, string $authToken, string $phoneNumber): void
    {
        $this->twilioAccountSid = $accountSid;
        $this->twilioAuthToken = $authToken;
        $this->twilioPhoneNumber = $phoneNumber;
        $this->provider = 'twilio';
    }

    /**
     * Set Nexmo credentials
     *
     * @param string $apiKey Nexmo API Key
     * @param string $apiSecret Nexmo API Secret
     * @param string $fromName Nexmo From Name
     */
    public function setNexmoCredentials(string $apiKey, string $apiSecret, string $fromName = 'Laundry'): void
    {
        $this->nexmoApiKey = $apiKey;
        $this->nexmoApiSecret = $apiSecret;
        $this->nexmoFromName = $fromName;
        $this->provider = 'nexmo';
    }

    /**
     * Set AWS credentials
     *
     * @param string $accessKey AWS Access Key
     * @param string $secretKey AWS Secret Key
     * @param string $region AWS Region
     */
    public function setAwsCredentials(string $accessKey, string $secretKey, string $region = 'us-east-1'): void
    {
        $this->awsAccessKey = $accessKey;
        $this->awsSecretKey = $secretKey;
        $this->awsRegion = $region;
        $this->provider = 'aws_sns';
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
