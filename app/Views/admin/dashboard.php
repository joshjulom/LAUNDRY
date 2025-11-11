<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2 class="mb-4 fw-bold text-primary">
    <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
</h2>

<!-- Statistics Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white shadow-lg">
            <div class="card-body text-center">
                <i class="bi bi-people-fill display-4 mb-2"></i>
                <h3><?= $total_users ?></h3>
                <p class="mb-0">Total Users</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white shadow-lg">
            <div class="card-body text-center">
                <i class="bi bi-cart-check-fill display-4 mb-2"></i>
                <h3><?= $total_orders ?></h3>
                <p class="mb-0">Total Orders</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white shadow-lg">
            <div class="card-body text-center">
                <i class="bi bi-hourglass-split display-4 mb-2"></i>
                <h3><?= $pending_orders ?></h3>
                <p class="mb-0">Pending Orders</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white shadow-lg">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle-fill display-4 mb-2"></i>
                <h3><?= $open_complaints ?></h3>
                <p class="mb-0">Open Complaints</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- Manage Users -->
    <div class="col-md-3">
        <div class="card text-white bg-success shadow-lg h-100 border-0 card-hover">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-people-fill me-2"></i> Manage Users
                </h5>
                <p class="card-text">Add, edit or delete system users.</p>
                <a href="<?= site_url('admin/users') ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Go
                </a>
            </div>
        </div>
    </div>

    <!-- Orders -->
    <div class="col-md-3">
        <div class="card text-white bg-info shadow-lg h-100 border-0 card-hover">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-cart-check-fill me-2"></i> Orders
                </h5>
                <p class="card-text">View and manage all customer orders.</p>
                <a href="<?= site_url('admin/orders') ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Go
                </a>
            </div>
        </div>
    </div>

    <!-- Inventory -->
    <div class="col-md-3">
        <div class="card text-white bg-warning shadow-lg h-100 border-0 card-hover">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-box-seam-fill me-2"></i> Inventory
                </h5>
                <p class="card-text">Track detergents, machines, and stock.</p>
                <a href="<?= site_url('admin/inventory') ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Go
                </a>
            </div>
        </div>
    </div>

    <!-- Complaints -->
    <div class="col-md-3">
        <div class="card text-white bg-danger shadow-lg h-100 border-0 card-hover">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Complaints
                </h5>
                <p class="card-text">Check and resolve customer complaints.</p>
                <a href="<?= site_url('admin/complaints') ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Go
                </a>
            </div>
        </div>
    </div>

</div>

<!-- Quick Actions -->
<div class="row g-4 mt-4">

    <div class="col-md-6">
        <div class="card shadow-lg border-0">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-chat-dots me-2"></i> Support Chat
                </h5>
                <p class="card-text">Communicate with staff and customers</p>
                <a href="<?= site_url('chat') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-chat-square-text me-1"></i> Open Chat
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Extra styles -->
<style>
    .card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-hover:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }
    h2 {
        letter-spacing: 0.5px;
    }
</style>

<?= $this->endSection() ?>