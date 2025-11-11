<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .order-details {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Order Confirmation</h1>
        <p>Thank you for choosing our Laundry Management System!</p>
    </div>

    <div class="content">
        <p>Dear <?= esc($user['username']) ?>,</p>

        <p>Your order has been successfully placed and confirmed. Here are the details:</p>

        <div class="order-details">
            <h3>Order Information</h3>
            <p><strong>Order ID:</strong> #<?= esc($order['id']) ?></p>
            <p><strong>Status:</strong> <?= ucfirst(esc($order['status'])) ?></p>
            <p><strong>Total Price:</strong> $<?= number_format(esc($order['total_price']), 2) ?></p>
            <p><strong>Due Date:</strong> <?= date('F j, Y', strtotime(esc($order['due_date']))) ?></p>
            <p><strong>Order Date:</strong> <?= date('F j, Y g:i A', strtotime(esc($order['created_at']))) ?></p>
        </div>

        <p>We will process your laundry order according to the schedule. You will receive updates on your order status via email.</p>

        <p>If you have any questions or need to make changes to your order, please contact our support team.</p>

        <p>Thank you for your business!</p>

        <p>Best regards,<br>
        Laundry Management System Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; <?= date('Y') ?> Laundry Management System. All rights reserved.</p>
    </div>
</body>
</html>
