# EmailService Implementation TODO

## Tasks to Complete

- [x] Update app/Config/Email.php with Gmail SMTP configuration (protocol, host, port, crypto)
- [x] Create app/Libraries/EmailService.php with sendOrderConfirmation and sendOrderStatusUpdate methods
- [x] Create app/Views/emails/order_confirmation.php HTML template
- [x] Create app/Views/emails/order_status_update.php HTML template
- [x] Integrate EmailService usage in Staff controller's updateStatusPost method
- [ ] Test email functionality (requires Gmail credentials)
