<?php

use App\Libraries\SmsService;

/**
 * SMS Helper Functions
 * 
 * Provides convenient functions for SMS operations
 */

/**
 * Send an SMS message
 *
 * @param string $phoneNumber The recipient phone number
 * @param string $message The message content
 * @return array The response array
 * 
 * @example
 * $result = send_sms('09051234567', 'Hello, this is a test message');
 * if ($result['success']) {
 *     echo "SMS sent successfully!";
 * } else {
 *     echo "Error: " . $result['message'];
 * }
 */
function send_sms(string $phoneNumber, string $message): array
{
    $smsService = new SmsService();
    return $smsService->send($phoneNumber, $message);
}

/**
 * Send SMS to multiple recipients
 *
 * @param array $phoneNumbers Array of phone numbers
 * @param string $message The message content
 * @return array Array of results
 * 
 * @example
 * $phones = ['09051234567', '09171234567'];
 * $results = send_sms_bulk($phones, 'Bulk message');
 */
function send_sms_bulk(array $phoneNumbers, string $message): array
{
    $smsService = new SmsService();
    return $smsService->sendBulk($phoneNumbers, $message);
}

/**
 * Get carrier information from phone number
 *
 * @param string $phoneNumber The phone number
 * @return string The carrier name
 * 
 * @example
 * $carrier = get_sms_carrier('09051234567');
 * echo $carrier; // Output: PLDT
 */
function get_sms_carrier(string $phoneNumber): string
{
    $smsService = new SmsService();
    return $smsService->getCarrierName($phoneNumber);
}
?>
