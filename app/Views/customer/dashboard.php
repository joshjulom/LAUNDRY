<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Customer Dashboard</h2>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-plus-circle"></i> New Order</h5>
                <p class="card-text">Place a new laundry order.</p>
                <a href="<?= site_url('customer/addOrder') ?>" class="btn btn-light btn-sm">Create Order</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-list"></i> My Orders</h5>
                <p class="card-text">View your laundry orders and status.</p>
                <a href="<?= site_url('customer/orders') ?>" class="btn btn-light btn-sm">View</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-exclamation-triangle"></i> Submit Complaint</h5>
                <p class="card-text">Report an issue with your order.</p>
                <a href="<?= site_url('customer/complaint') ?>" class="btn btn-dark btn-sm">Submit</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
