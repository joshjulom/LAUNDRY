<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Orders</h2>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="mb-3">
    <small class="text-muted">Showing all orders. Staff can update status for any order.</small>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Status</th>
            <th>Total</th>
            <th>Due Date</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= esc($o['id']) ?></td>
                <td>
                    <span class="badge
                        <?php
                        switch($o['status']) {
                            case 'pending': echo 'bg-warning text-dark'; break;
                            case 'washing': echo 'bg-info'; break;
                            case 'ready': echo 'bg-success'; break;
                            case 'delivered': echo 'bg-secondary'; break;
                            default: echo 'bg-secondary';
                        }
                        ?>">
                        <?= esc(ucfirst($o['status'])) ?>
                    </span>
                </td>
                <td>$<?= esc(number_format($o['total_price'], 2)) ?></td>
                <td><?= esc(date('M d, Y', strtotime($o['due_date']))) ?></td>
                <td><?= esc(date('M d, Y', strtotime($o['created_at']))) ?></td>
                <td>
                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('staff/orders/' . $o['id'] . '/status') ?>">
                        <i class="fas fa-edit"></i> Update Status
                    </a>
                    <a class="btn btn-sm btn-outline-dark" href="<?= site_url('staff/issues/report') ?>?order_id=<?= $o['id'] ?>">
                        <i class="fas fa-exclamation-triangle"></i> Report Issue
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No orders found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
