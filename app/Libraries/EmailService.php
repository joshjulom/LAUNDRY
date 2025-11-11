<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use Config\Email as EmailConfig;

class EmailService
{
    protected Email $email;

    public function __construct()
    {
        $this->email = new Email(new EmailConfig());
    }

    /**
     * Send order confirmation email
     *
     * @param array $orderData Order data array
     * @param array $userData User data array
     * @return bool
     */
    public function sendOrderConfirmation(array $orderData, array $userData): bool
    {
        $subject = 'Order Confirmation - Laundry Management System';
        $templateData = [
            'order' => $orderData,
            'user' => $userData,
            'subject' => $subject
        ];

        return $this->sendEmail($userData['email'] ?? '', $subject, 'emails/order_confirmation', $templateData);
    }

    /**
     * Send order status update email
     *
     * @param array $orderData Order data array
     * @param array $userData User data array
     * @param string $oldStatus Previous status
     * @param string $newStatus New status
     * @return bool
     */
    public function sendOrderStatusUpdate(array $orderData, array $userData, string $oldStatus, string $newStatus): bool
    {
        $subject = 'Order Status Update - Laundry Management System';
        $templateData = [
            'order' => $orderData,
            'user' => $userData,
            'oldStatus' => $oldStatus,
            'newStatus' => $newStatus,
            'subject' => $subject
        ];

        return $this->sendEmail($userData['email'] ?? '', $subject, 'emails/order_status_update', $templateData);
    }

    /**
     * Send email using template
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $template Template view path
     * @param array $data Data for template
     * @return bool
     */
    private function sendEmail(string $to, string $subject, string $template, array $data = []): bool
    {
        if (empty($to)) {
            log_message('error', 'Email recipient is empty');
            return false;
        }

        try {
            $this->email->setTo($to);
            $this->email->setSubject($subject);

            $message = view($template, $data);
            $this->email->setMessage($message);

            if ($this->email->send()) {
                log_message('info', "Email sent successfully to: {$to} with subject: {$subject}");
                return true;
            } else {
                log_message('error', "Failed to send email to: {$to}. Error: " . $this->email->printDebugger());
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "Exception while sending email to {$to}: " . $e->getMessage());
            return false;
        }
    }
}
