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
            <a class="navbar-brand" href="<?= site_url('admin') ?>">
                <i class="fas fa-tshirt"></i> Laundry Management System
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= site_url('admin/users') ?>">
                    <i class="fas fa-arrow-left"></i> Back to Users
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
                            <i class="fas fa-user-plus"></i> Add New User
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (session()->has('error')): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= session('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('admin/addUser') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user"></i> Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="<?= old('username') ?>" required>
                                <?php if (session()->has('errors') && isset(session('errors')['username'])): ?>
                                    <div class="text-danger small">
                                        <?= session('errors')['username'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email Address
                                </label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= old('email') ?>" required>
                                <?php if (session()->has('errors') && isset(session('errors')['email'])): ?>
                                    <div class="text-danger small">
                                        <?= session('errors')['email'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <?php if (session()->has('errors') && isset(session('errors')['password'])): ?>
                                    <div class="text-danger small">
                                        <?= session('errors')['password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Confirm Password
                                </label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    <i class="fas fa-user-tag"></i> Role
                                </label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="staff" <?= old('role') === 'staff' ? 'selected' : '' ?>>Staff</option>
                                    <option value="customer" <?= old('role') === 'customer' ? 'selected' : '' ?>>Customer</option>
                                </select>
                                <?php if (session()->has('errors') && isset(session('errors')['role'])): ?>
                                    <div class="text-danger small">
                                        <?= session('errors')['role'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Add User
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
