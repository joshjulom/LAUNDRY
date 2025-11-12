<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<h2><?= esc($title ?? 'Barcode Scanner') ?></h2>

<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <label for="barcode" class="form-label">Barcode</label>
            <input id="barcode" class="form-control" placeholder="Enter or scan barcode" autofocus />
        </div>

        <button id="scanBtn" class="btn btn-primary">Lookup</button>

        <hr />

        <div id="result" style="display:none">
            <h5>Order Details</h5>
            <div id="orderInfo"></div>
            <div id="userInfo" class="mt-2"></div>
        </div>

        <div id="error" class="alert alert-danger mt-2" style="display:none"></div>
    </div>
</div>

<script>
document.getElementById('scanBtn').addEventListener('click', async function () {
    const barcode = document.getElementById('barcode').value.trim();
    document.getElementById('error').style.display = 'none';
    document.getElementById('result').style.display = 'none';

    if (!barcode) {
        document.getElementById('error').textContent = 'Please enter a barcode.';
        document.getElementById('error').style.display = 'block';
        return;
    }

    const formData = new FormData();
    formData.append('barcode', barcode);

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
            document.getElementById('error').textContent = json.message || 'Not found';
            document.getElementById('error').style.display = 'block';
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
        document.getElementById('result').style.display = 'block';

    } catch (err) {
        document.getElementById('error').textContent = 'Lookup failed';
        document.getElementById('error').style.display = 'block';
        console.error(err);
    }
});

// allow Enter to trigger lookup
document.getElementById('barcode').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        document.getElementById('scanBtn').click();
    }
});
</script>

<?= $this->endSection() ?>