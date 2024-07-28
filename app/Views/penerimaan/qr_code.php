<!DOCTYPE html>
<html>
<head>
    <title>QR Code</title>
    <style>
        .print-area {
            text-align: center;
        }
        @media print {
            button {
                display: none;
            }
        }
        /* Tambahkan CSS untuk modal */
        .modal {
            display: none; /* Sembunyikan modal secara default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .modal-content {
            margin: 10% auto;
            padding: 20px;
            background-color: #fefefe;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="print-area">
        <h1>QR Code for <?= $barang['kode_barang']; ?></h1>
        <!-- Tambahkan tautan atau gambar QR Code -->
        <a href="#" id="qrCodeLink">
            <img src="<?= $publicQrCodePath; ?>" alt="QR Code">
        </a>
        <p><?= $kode_barang ?> - <?= $nama_barang ?></p>
        <!-- Tambahkan tombol untuk membuka modal -->
        <button onclick="openModal();">Preview QR Code</button>
        <!-- Modal -->
        <div id="qrCodeModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal();">&times;</span>
                <h2>Preview QR Code</h2>
                <img src="<?= $publicQrCodePath; ?>" alt="QR Code">
                <p><?= $kode_barang ?> - <?= $nama_barang ?></p>
                <button onclick="printQRCode();">Print</button>
            </div>
        </div>
    </div>

    <!-- Tambahkan JavaScript untuk kontrol modal -->
    <script>
        // Fungsi untuk membuka modal
        function openModal() {
            var modal = document.getElementById('qrCodeModal');
            modal.style.display = 'block';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            var modal = document.getElementById('qrCodeModal');
            modal.style.display = 'none';
        }

        // Fungsi untuk mencetak QR Code
        function printQRCode() {
            var qrCodeImg = document.querySelector('#qrCodeModal img');
            var printWindow = window.open('', '_blank');
            printWindow.document.open();
            printWindow.document.write('<html><head><title>Print QR Code</title></head><body>');
            printWindow.document.write('<img src="' + qrCodeImg.src + '">');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        }
    </script>
</body>
</html>
