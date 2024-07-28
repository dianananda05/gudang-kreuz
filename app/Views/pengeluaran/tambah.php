<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Tambah Pengeluaran &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="<?= site_url('Pengeluaran/index') ?>" class="btn primary"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Tambah Pengeluaran</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Pengeluaran Barang</h4>
            </div>
            <div class="card-body col-md-12">
                <?= form_open('Pengeluaran/insertdata') ?>
                <div class="form-group">
                    <label for="Kode Permintaan">Kode Permintaan *</label>
                    <select id="kode_permintaan" name="kode_permintaan" class="form-control" id="kode_permintaan">
                        <option value="">Kode Permintaan</option>
                        <?php foreach ($kode_perm_available as $kodePerm) : ?>
                            <?php $selected = (isset($_GET['kode_permintaan']) && $_GET['kode_permintaan'] === $kodePerm) ? 'selected' : ''; ?>
                            <option value="<?= $kodePerm ?>" <?= $selected ?>><?= $kodePerm ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="Kode Pengeluaran">Kode Pengeluaran *</label>
                    <input type="text" name="kode_pengeluaran" class="form-control" value="<?= isset($kode_pengeluaran) ? $kode_pengeluaran : '' ?>" readonly required style="pointer-events: none;">
                </div>
                <div class="form-group">
                    <label for="Tanggal Pengeluaran">Tanggal Pengeluaran *</label>
                    <input type="date" name="tanggal_pengeluaran" class="form-control" placeholder="Tanggal Pengeluaran" required value="<?= date('Y-m-d') ?>">
                </div>

                <!-- Tabel untuk memasukkan data barang -->
                <div class="form-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari Barang...">
                    <table class="table table-bordered" id="barangTable">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Jumlah Yang Diminta</th>
                                <th>Jumlah Yang Diserahkan</th>
                                <th>Scan QR</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list_barang as $barang) : ?>
                                <tr>
                                    <td><input name="kode_barang[]" class="form-control" value="<?= $barang['kode_barang'] ?>" readonly></td>
                                    <td><?= $barang['nama_barang'] ?></td>
                                    <td><?= $barang['satuan'] ?></td>
                                    <td><?= $barang['jumlah_yang_diminta'] ?></td>
                                    <td>
                                        <input name="jumlah_yang_diserahkan[]" type="number" class="form-control barcode-input" placeholder="Jumlah Yang Diserahkan" required data-barcode-input>
                                    </td>
                                    <td id="cameraView">
                                        <video id="videoElement" autoplay></video>
                                    </td>
                                    <td><input name="keterangan[]" class="form-control" placeholder="Keterangan" required></td>
                                    <td>
                                        <button type="button" class="btn btn-primary barcode-scan-button">Scan QR</button>
                                        <button type="button" class="btn btn-danger btn-remove-row">Hapus</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" onclick="window.history.back()">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@2.1.0/umd/jsQR.min.js"></script>
<!-- Script untuk menambah baris baru secara otomatis dan fitur search -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const barangTable = document.getElementById('barangTable');
        const searchInput = document.getElementById('searchInput');
        const kodePRMSelect = document.getElementById('kode_permintaan');

        function fetchDetailBarang(selectedKodePRM) {
            if (selectedKodePRM) {
                fetch(`<?=base_url('Pengadaan/getBarangByKodePRM/') ?>${selectedKodePRM}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const barangTableBody = document.querySelector('#barangTable tbody');
                    barangTableBody.innerHTML = '';

                    data.forEach(barang => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td><input name="kode_barang[]" class="form-control" value="${barang.kode_barang}" readonly></td>
                            <td>${barang.nama_barang}</td>
                            <td>${barang.satuan}</td>
                            <td>${barang.jumlah_yang_diminta}</td>
                            <td><input type="number" name="jumlah_yang_diserahkan[]" class="form-control barcode-input" placeholder="Jumlah Yang Diserahkan" required data-barcode-input></td>
                            <td><input type="text" name="keterangan[]" class="form-control" placeholder="Keterangan" required></td>
                            <td><button type="button" class="btn btn-danger btn-remove-row">Hapus</button></td>
                        `;
                        barangTableBody.appendChild(row);
                    });

                    kodePRMSelect.value = selectedKodePRM;
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
            }
        }

        kodePRMSelect.addEventListener('change', function (evt) {
            const selectedKodePRM = evt.target.value;
            const currentUrl = window.location.href;
            const url = new URL(currentUrl);
            const params = new URLSearchParams(url.search);
            params.set('kode_permintaan', selectedKodePRM);
            url.search = params.toString();
            window.location.href = url.toString();
        });

        const initialSelectedKodePRM = kodePRMSelect.value;
        if (initialSelectedKodePRM) {
            fetchDetailBarang(initialSelectedKodePRM);
        }

        // Search functionality
        searchInput.addEventListener('input', function () {
            const filter = searchInput.value.toLowerCase();
            const rows = barangTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const kodeBarang = row.querySelector('input[name="kode_barang[]"]').value.toLowerCase();
                const namaBarang = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

                if (kodeBarang.includes(filter) || namaBarang.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('btn-remove-row')) {
                event.target.closest('tr').remove();
            }
        });

        // Date validation
        document.addEventListener('DOMContentLoaded', function() {
            const tanggalPengeluaranInput = document.querySelector('input[name="tanggal_pengeluaran"]');
            tanggalPengeluaranInput.valueAsDate = new Date();

            function validateTanggalPengeluaran(input) {
                const selectedDate = new Date(input.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0); // Reset time to midnight for accurate comparison

                if (selectedDate < today) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanggal tidak valid',
                        text: 'Harap pilih tanggal setelah hari ini.'
                    });
                    input.value = ''; // Clear the invalid date
                }
            }

            tanggalPengeluaranInput.addEventListener('change', function() {
                validateTanggalPengeluaran(tanggalPengeluaranInput);
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const videoElement = document.getElementById('videoElement');

        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    videoElement.srcObject = stream;
                    videoElement.play();
                })
                .catch(function (error) {
                    console.error('Error accessing camera: ', error);
                });
        }

        function handleQRScan(result) {
            if (result) {
                const scannedValue = result.text.trim();
                const barcodeInput = document.querySelector('[data-barcode-input]');
                if (barcodeInput) {
                    barcodeInput.value = scannedValue;
                } else {
                    alert('No input field found for barcode data.');
                }
            } else {
                alert('QR code not detected or could not be read.');
            }
        }

        function scanQRCode() {
            const constraints = { video: true };
            const video = document.createElement('video');

            function handleSuccess(stream) {
                video.srcObject = stream;
                video.addEventListener('loadedmetadata', () => {
                    video.play();
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    handleQRScan(code);
                    video.srcObject.getTracks().forEach(track => track.stop());
                    video.remove();
                    canvas.remove();
                });
            }

            function handleError(error) {
                console.error('Error accessing camera: ', error);
            }

            navigator.mediaDevices.getUserMedia(constraints)
                .then(handleSuccess)
                .catch(handleError);
        }

        // Start camera when the DOM content is loaded
        startCamera();

        // Trigger QR code scanning on click of the "Scan QR" button
        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('barcode-scan-button')) {
                scanQRCode();
            }
        });
    });
</script>
<?= $this->endSection() ?>
