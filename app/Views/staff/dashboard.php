<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Staff Dashboard</h2>
<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white mb-3">
            <div class="card-body">
                <h5 class="card-title">Assigned Orders</h5>
                <p class="card-text">Check laundry orders assigned to you.</p>
                <a href="<?= site_url('staff/orders') ?>" class="btn btn-light btn-sm">View</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-secondary text-white mb-3">
            <div class="card-body">
                <h5 class="card-title">Update Status</h5>
                <p class="card-text">Update washing, drying, or delivery status.</p>
                <a href="<?= site_url('staff/orders') ?>" class="btn btn-light btn-sm">Update</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-dark text-white mb-3">
            <div class="card-body">
                <h5 class="card-title">Report Issues</h5>
                <p class="card-text">Submit complaints or machine problems.</p>
                <a href="<?= site_url('staff/issues/report') ?>" class="btn btn-light btn-sm">Report</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-success text-white mb-3">
            <div class="card-body">
                <h5 class="card-title">Team Chat</h5>
                <p class="card-text">Chat with admin and staff in real time.</p>
                <a href="<?= site_url('staff/chat') ?>" class="btn btn-light btn-sm">Open Chat</a>
            </div>
        </div>
    </div>
    
</div>

<!-- Barcode scanner area (embedded in dashboard for quick scanning with USB laser scanners) -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-barcode"></i> Barcode Scanner</h5>
                <p class="card-text">Use a plug-and-play USB laser barcode scanner (acts as a keyboard). Focus the field and scan — the scanner usually sends the barcode plus an Enter keystroke.</p>

                <form id="scannerForm">
                    <?= csrf_field() ?>
                    <div class="input-group mb-3">
                        <input type="text" name="barcode" id="barcodeInput" class="form-control" placeholder="Scan or type barcode" autofocus />
                        <button class="btn btn-primary" id="scanBtn" type="submit">Lookup</button>
                    </div>
                </form>

                <div id="scannerResult" style="display:none">
                    <h6>Order Details</h6>
                    <div id="orderInfo"></div>
                    <div id="userInfo" class="mt-2"></div>
                </div>

                <div id="scannerError" class="alert alert-danger mt-2" style="display:none"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('scannerForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    document.getElementById('scannerError').style.display = 'none';
    document.getElementById('scannerResult').style.display = 'none';

    const form = e.currentTarget;
    const formData = new FormData(form);

    try {
        const res = await fetch('<?= site_url('staff/scanner/scan') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const json = await res.json();
        if (!json.success) {
            document.getElementById('scannerError').textContent = json.message || 'Not found';
            document.getElementById('scannerError').style.display = 'block';
            return;
        }

        const order = json.order;
        const user = json.user;

        let orderHtml = '<ul class="list-group">';
        orderHtml += `<li class="list-group-item"><strong>Order ID:</strong> ${order.id}</li>`;
        orderHtml += `<li class="list-group-item"><strong>Barcode:</strong> ${order.barcode}</li>`;
        orderHtml += `<li class="list-group-item"><strong>Service:</strong> ${order.service_type || ''}</li>`;
        orderHtml += `<li class="list-group-item"><strong>Total:</strong> ${order.total || ''}</li>`;
        orderHtml += `<li class="list-group-item"><strong>Status:</strong> ${order.status || ''}</li>`;
        orderHtml += '</ul>';

        let userHtml = '<ul class="list-group">';
        if (user) {
            userHtml += `<li class="list-group-item"><strong>Name:</strong> ${user.fullname || user.username || ''}</li>`;
            userHtml += `<li class="list-group-item"><strong>Phone:</strong> ${user.phone || ''}</li>`;
            userHtml += `<li class="list-group-item"><strong>Email:</strong> ${user.email || ''}</li>`;
        }
        userHtml += '</ul>';

        document.getElementById('orderInfo').innerHTML = orderHtml;
        document.getElementById('userInfo').innerHTML = userHtml;
        document.getElementById('scannerResult').style.display = 'block';
        // clear input and refocus for next scan
        document.getElementById('barcodeInput').value = '';
        document.getElementById('barcodeInput').focus();

    } catch (err) {
        document.getElementById('scannerError').textContent = 'Lookup failed';
        document.getElementById('scannerError').style.display = 'block';
        console.error(err);
    }
});

// Ensure Enter from scanner triggers submit (most barcode scanners send an Enter keystroke)
document.getElementById('barcodeInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('scanBtn').click();
    }
});
</script>
</div>

<?= $this->endSection() ?>
