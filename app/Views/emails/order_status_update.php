<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update</title>
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
            background-color: #2196F3;
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
        .status-update {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #2196F3;
        }
        .order-details {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background-color: #FF9800; color: white; }
        .status-washing { background-color: #2196F3; color: white; }
        .status-ready { background-color: #4CAF50; color: white; }
        .status-delivered { background-color: #9C27B0; color: white; }
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
        <h1>Order Status Update</h1>
        <p>Your order status has been updated</p>
    </div>

    <div class="content">
        <p>Dear <?= esc($user['username']) ?>,</p>

        <p>We wanted to inform you that the status of your order has been updated.</p>

        <div class="status-update">
            <h3>Status Change</h3>
            <p><strong>Order ID:</strong> #<?= esc($order['id']) ?></p>
            <p><strong>Previous Status:</strong> <span class="status-badge status-<?= strtolower(esc($oldStatus)) ?>"><?= ucfirst(esc($oldStatus)) ?></span></p>
            <p><strong>New Status:</strong> <span class="status-badge status-<?= strtolower(esc($newStatus)) ?>"><?= ucfirst(esc($newStatus)) ?></span></p>
            <p><strong>Updated on:</strong> <?= date('F j, Y g:i A') ?></p>
        </div>

        <div class="order-details">
            <h3>Order Information</h3>
            <p><strong>Total Price:</strong> $<?= number_format(esc($order['total_price']), 2) ?></p>
            <p><strong>Due Date:</strong> <?= date('F j, Y', strtotime(esc($order['due_date']))) ?></p>
            <p><strong>Order Date:</strong> <?= date('F j, Y g:i A', strtotime(esc($order['created_at']))) ?></p>
        </div>

        <?php if ($newStatus === 'ready'): ?>
            <div style="background-color: #E8F5E8; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3 style="color: #2E7D32; margin-top: 0;">🎉 Your laundry is ready for pickup!</h3>
                <p>Please visit our facility to collect your clean laundry. Don't forget to bring your order ID.</p>
            </div>
        <?php elseif ($newStatus === 'delivered'): ?>
            <div style="background-color: #E8F5E8; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3 style="color: #2E7D32; margin-top: 0;">✅ Order Delivered Successfully!</h3>
                <p>Thank you for using our laundry services. We hope to serve you again soon!</p>
            </div>
        <?php endif; ?>

        <p>If you have any questions about your order, please contact our support team.</p>

        <p>Thank you for choosing our Laundry Management System!</p>

        <p>Best regards,<br>
        Laundry Management System Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; <?= date('Y') ?> Laundry Management System. All rights reserved.</p>
    </div>
</body>
</html>
