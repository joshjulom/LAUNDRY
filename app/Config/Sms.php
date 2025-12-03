<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * SMS Configuration
 * 
 * Configure your SMS API credentials and provider settings
 */
class Sms extends BaseConfig
{
    /**
     * SMS Provider
     * 
     * Options: 'twilio', 'nexmo', 'aws_sns'
     * Default: 'twilio'
     */
    public string $provider = 'twilio';

    /**
     * ==========================================
     * TWILIO CONFIGURATION
     * ==========================================
     */

    /**
     * Twilio Account SID
     * Get from: https://www.twilio.com/console
     */
    public string $twilioAccountSid = '';

    /**
     * Twilio Auth Token
     * Get from: https://www.twilio.com/console
     */
    public string $twilioAuthToken = '';

    /**
     * Twilio Phone Number (From)
     * Example: '+1234567890'
     */
    public string $twilioPhoneNumber = '';

    /**
     * ==========================================
     * NEXMO (VONAGE) CONFIGURATION
     * ==========================================
     */

    /**
     * Nexmo API Key
     * Get from: https://dashboard.nexmo.com/
     */
    public string $nexmoApiKey = '';

    /**
     * Nexmo API Secret
     * Get from: https://dashboard.nexmo.com/
     */
    public string $nexmoApiSecret = '';

    /**
     * Nexmo From Name (Brand/Sender ID)
     * Max 11 alphanumeric characters
     */
    public string $nexmoFromName = 'Laundry';

    /**
     * ==========================================
     * AWS SNS CONFIGURATION
     * ==========================================
     */

    /**
     * AWS Access Key ID
     */
    public string $awsAccessKey = '';

    /**
     * AWS Secret Access Key
     */
    public string $awsSecretKey = '';

    /**
     * AWS Region
     * Example: 'us-east-1', 'eu-west-1'
     */
    public string $awsRegion = 'us-east-1';

    /**
     * ==========================================
     * GENERAL SETTINGS
     * ==========================================
     */

    /**
     * Enable SMS logging
     */
    public bool $enableLogging = true;

    /**
     * Log file location (relative to WRITEPATH)
     */
    public string $logFile = 'logs/sms.log';

    /**
     * Timeout for API requests (in seconds)
     */
    public int $timeout = 30;

    /**
     * ==========================================
     * SETUP INSTRUCTIONS
     * ==========================================
     * 
     * 1. TWILIO SETUP:
     *    - Sign up at https://www.twilio.com
     *    - Get your Account SID and Auth Token from the console
     *    - Get a Twilio phone number or verify your number
     *    - Add credentials above
     * 
     * 2. NEXMO/VONAGE SETUP:
     *    - Sign up at https://dashboard.nexmo.com
     *    - Get your API Key and Secret from the dashboard
     *    - Set up a sender ID (brand name)
     *    - Add credentials above
     * 
     * 3. AWS SNS SETUP:
     *    - Create an AWS account
     *    - Create IAM user with SNS permissions
     *    - Get Access Key and Secret Key
     *    - Use region that supports SMS (us-east-1, eu-west-1, etc.)
     *    - Add credentials above
     * 
     * 4. ENVIRONMENT VARIABLES:
     *    Alternatively, set credentials via .env file:
     *    - SMS_PROVIDER=twilio
     *    - SMS_TWILIO_ACCOUNT_SID=your_sid
     *    - SMS_TWILIO_AUTH_TOKEN=your_token
     *    - SMS_TWILIO_PHONE=+1234567890
     */
}
?>
