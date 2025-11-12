<!DOCTYPE html>
<html>
<head>
    <title>Verify Phone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-lg p-4" style="width: 420px;">
        <h3 class="text-center">Verify Your Phone</h3>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php 
            $pending = session()->get('pending_registration');
            $phone = $pending['phone'] ?? 'unknown';
            // Mask phone number for privacy (show last 4 digits)
            $maskedPhone = '***-****-' . substr($phone, -4);
        ?>

        <p class="text-center text-muted mb-4">
            A verification code has been sent to:<br>
            <strong><?= $maskedPhone ?></strong>
        </p>

        <form method="post" action="<?= site_url('register/verify') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Verification Code</label>
                <input type="text" name="code" class="form-control" placeholder="Enter 6-digit code" required autofocus maxlength="6">
            </div>

            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>

        <div class="text-center mt-3">
            <form method="post" action="<?= site_url('register/resend') ?>" style="display:inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-link btn-sm">Didn't receive the code? Resend</button>
            </form>
        </div>
    </div>
</body>
</html>