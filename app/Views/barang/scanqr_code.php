<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Scan QR Code &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <h1>Scan QR Code</h1>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-body text-center">
                <div id="reader" style="width: 300px; height: 300px;"></div>
                <br>
                <div id="result"></div>
            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById('result').innerHTML = `Scanned result: ${decodedText}`;

        fetch('<?= site_url('barang/scan') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify({ kode_barang: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('result').innerHTML = `Item: ${data.item.nama_barang} - ${data.item.harga_satuan}`;
            } else {
                document.getElementById('result').innerHTML = 'Item not found';
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function onScanFailure(error) {
        console.warn(`QR error: ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
<?= $this->endSection() ?>
