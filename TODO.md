# EmailService Implementation TODO

## Tasks to Complete

- [x] Update app/Config/Email.php with Gmail SMTP configuration (protocol, host, port, crypto)
- [x] Create app/Libraries/EmailService.php with sendOrderConfirmation and sendOrderStatusUpdate methods
- [x] Create app/Views/emails/order_confirmation.php HTML template
- [x] Create app/Views/emails/order_status_update.php HTML template
- [x] Integrate EmailService usage in Staff controller's updateStatusPost method
- [ ] Test email functionality (requires Gmail credentials)

## Barcode Generation TODO

## Tasks Completed

- [x] Add Picqer\Barcode\BarcodeGeneratorPNG to composer.json
- [x] Run composer install to install barcode library
- [x] Create app/Libraries/BarcodeService.php with generateBarcode and getBarcodeHtml methods
- [x] Add barcode button to app/Views/admin/orders.php
- [x] Add barcode modal and JavaScript for displaying barcodes
- [x] Add generateBarcode method to Admin controller
- [x] Add generate-barcode route to app/Config/Routes.php
- [ ] Test barcode generation functionality
