<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - Laundry Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('customer/dashboard') ?>">
                <i class="fas fa-tshirt"></i> Laundry Management System
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= site_url('customer/dashboard') ?>">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a class="nav-link" href="<?= site_url('auth/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle"></i> Create New Order
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (session()->has('error')): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= session('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('customer/addOrder') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="total_price" class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Total Price ($)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="total_price" name="total_price"
                                       value="<?= old('total_price') ?>" required min="0">
                                <div class="form-text">Enter the total amount for your laundry order</div>
                                <?php if (session()->has('errors') && isset(session('errors')['total_price'])): ?>
                                    <div class="text-danger small">
                                        <?= session('errors')['total_price'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="due_date" class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Due Date
                                </label>
                                <input type="date" class="form-control" id="due_date" name="due_date"
                                       value="<?= old('due_date', date('Y-m-d', strtotime('+3 days'))) ?>" required
                                       min="<?= date('Y-m-d') ?>">
                                <div class="form-text">Select when you need your laundry back</div>
                                <?php if (session()->has('errors') && isset(session('errors')['due_date'])): ?>
                                    <div class="text-danger small">
                                        <?= session('errors')['due_date'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> Your order will be set to "Pending" status initially.
                                You will receive a confirmation email once the order is placed.
                                Staff will update the status as they process your laundry.
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Place Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
