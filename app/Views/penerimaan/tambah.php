<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Tambah Penerimaan &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="<?= site_url('Penerimaan/index') ?>" class="btn btn-primary"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Tambah Penerimaan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Penerimaan Barang</h4>
            </div>
            <div class="card-body">
                <?= form_open('Penerimaan/insertdata') ?>
                <div class="form-group">
                    <label for="kode_po">Kode PO *</label>
                    <select name="kode_po" id="kode_po" class="form-control" required>
                        <option value="">Pilih Kode PO</option>
                        <?php foreach ($kode_po_available as $kodePo) : ?>
                            <?php $selected = (isset($_GET['kode_po']) && $_GET['kode_po'] === $kodePo) ? 'selected' : ''; ?>
                            <option value="<?= $kodePo ?>" <?= $selected ?>><?= $kodePo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kode_penerimaan">Kode Penerimaan *</label>
                    <input type="text" name="kode_penerimaan" class="form-control" value="<?= isset($kode_penerimaan) ? $kode_penerimaan : '' ?>" readonly required style="pointer-events: none;">
                </div>
                <div class="form-group">
                    <label for="tanggal_penerimaan">Tanggal Penerimaan *</label>
                    <input type="date" name="tanggal_penerimaan" id="tanggal_penerimaan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="nomor_po">Nomor PO *</label>
                    <input name="nomor_po" id="nomor_po" class="form-control" placeholder="Nomor PO" required>
                </div>

                <!-- Tabel untuk memasukkan data barang -->
                <div class="form-group">
                    <div class="form-inline mb-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari Barang..." style="flex: 1; margin-right: 10px;">
                        <button type="button" class="btn btn-primary" onclick="openScanModal()">Scan QR</button>
                    </div>
                    
                    <table class="table table-bordered" id="barangTable">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Jumlah Yang Dipesan</th>
                                <th>Jumlah Yang Diterima</th>
                                <th>Kondisi Barang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list_barang as $barang) : ?>
                                <tr>
                                    <td><input name="kode_barang[]" class="form-control" value="<?= $barang['kode_barang'] ?>"></td>
                                    <td><?= $barang['nama_barang'] ?></td>
                                    <td><?= $barang['satuan'] ?></td>
                                    <td><?= $barang['jumlah_barang'] ?></td>
                                    <td>
                                        <input id="<?= $barang['kode_barang'] ?>" name="jumlah_yang_diterima[]" type="number" class="form-control barcode-input" placeholder="Jumlah Yang Diterima" required data-barcode-input>
                                    </td>
                                    <td>
                                        <input name="kondisi_barang[]" class="form-control" placeholder="Kondisi Barang" required>
                                    </td>
                                    <td>
                                        <a href="#" onclick="openModal('<?= $barang['kode_barang']; ?>', '<?= $barang['nama_barang']; ?>');">
                                            <i class="fas fa-qrcode" style="font-size: 24px;"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<!-- Modal untuk preview QR Code -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                <button type="button" class="btn-close" onclick="closeModal();" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="qrCodeImg" src="" class="img-fluid mb-3" alt="QR Code">
                <p id="qrCodeText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal();">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printQRCode();">Cetak</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qrScanModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR Scan</h5>
                <button type="button" class="btn-close" onclick="closeScanModal();" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loadingMessage">🎥 Unable to access video stream (please make sure you have a webcam enabled)</div>
                <canvas id="qrView" hidden></canvas>
                <div id="output" hidden>
                    <div id="outputMessage">No QR code detected.</div>
                    <div hidden><b>Data:</b> <span id="outputData"></span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeScanModal();">Tutup</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@2.1.0/umd/jsQR.min.js"></script>
<!-- Script untuk menambah baris baru secara otomatis dan fitur search -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const barangTable = document.getElementById('barangTable');
        const searchInput = document.getElementById('searchInput');
        const kodePOSelect = document.getElementById('kode_po');
        let selectedKodePO = kodePOSelect.value;
        
        kodePOSelect.addEventListener('change', function (evt) {
            const selectedKodePO = evt.target.value;
            const currentUrl = window.location.href;
            const url = new URL(currentUrl);
            const params = new URLSearchParams(url.search);
            params.set('kode_po', selectedKodePO);
            url.search = params.toString();
            window.location.href = url.toString();
        });

        function fetchDetailBarang(selectedKodePO) {
            if (selectedKodePO) {
                // Fetch barang data based on selected Kode PO using AJAX
                fetch(`<?= base_url('Penerimaan/getBarangByKodePO/') ?>${selectedKodePO}`)
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
                                <td>${barang.kode_barang}</td>
                                <td>${barang.nama_barang}</td>
                                <td>${barang.satuan}</td>
                                <td>${barang.jumlah_barang}</td>
                            `;
                            barangTableBody.appendChild(row);
                        });

                        kodePOSelect.value = selectedKodePO;
                    })
            }
        }

        const initialSelectedKodePO = kodePOSelect.value;
        if (initialSelectedKodePO) {
            fetchDetailBarang(initialSelectedKodePO);
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
        
        kodePoElm.addEventListener('change', function (evt) {
            const selectedKodePo = evt.target.value;
            fetchDetailBarang(selectedKodePo);
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const tanggalPenerimaanInput = document.querySelector('input[name="tanggal_penerimaan"]');
        tanggalPenerimaanInput.valueAsDate = new Date();

        function validateTanggalPenerimaan(input) {
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

        tanggalPenerimaanInput.addEventListener('change', function () {
            validateTanggalPenerimaan(tanggalPenerimaanInput);
        });
    });


    function openModal(kode_barang, nama_barang) {
        var qrCodeImg = document.getElementById('qrCodeImg');
        var qrCodeText = document.getElementById('qrCodeText');

        // Mengirimkan request Ajax untuk mendapatkan QR Code
        $.ajax({
            url: '<?= base_url('barang/qr_code/') ?>' + kode_barang,
            type: 'GET',
            success: function(response) {
                // Mengatur sumber gambar QR Code dan teks
                qrCodeImg.src = response.publicQrCodePath;
                qrCodeText.innerHTML = kode_barang + ' - ' + nama_barang;

                $('#qrCodeModal').modal('show'); // Tampilkan modal
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan: ' + error); // Tampilkan pesan kesalahan jika ada
            }
        });
    }

    function openScanModal(kode_barang) {
        $('#qrScanModal').modal('show'); // Tampilkan modal
    }
    
    function closeModal() {
        $('#qrCodeModal').modal('hide'); // Sembunyikan modal menggunakan Bootstrap
    }

    // Fungsi untuk menutup modal
    function closeScanModal() {
        $('#qrScanModal').modal('hide'); // Sembunyikan modal menggunakan Bootstrap
    }

    // Fungsi untuk mencetak QR Code
    function printQRCode() {
        var qrCodeImgSrc = document.getElementById('qrCodeImg').src;
        if (qrCodeImgSrc) {
            var printWindow = window.open('', '_blank');
            printWindow.document.open();
            printWindow.document.write('<html><head><title>Cetak QR Code</title></head><body>');
            printWindow.document.write('<img src="' + qrCodeImgSrc + '">');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.onload = function() {
                printWindow.print();
                printWindow.close();
            };
        } else {
            alert("QR Code tidak tersedia untuk dicetak.");
        }
    }
</script>

<script>
    var listBarang = {};
    <?php foreach ($list_barang as $barang) : ?>
    listBarang[ "<?= $barang['kode_barang'] ?>"] = <?= $barang['jumlah_barang'] ?>;
    <?php endforeach; ?>
    console.log('listBarang', listBarang)

    var video = document.createElement("video");
    var canvasElement = document.getElementById("qrView");
    var canvas = canvasElement.getContext("2d");
    var loadingMessage = document.getElementById("loadingMessage");
    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");
    var outputData = document.getElementById("outputData");

    function drawLine(begin, end, color) {
      canvas.beginPath();
      canvas.moveTo(begin.x, begin.y);
      canvas.lineTo(end.x, end.y);
      canvas.lineWidth = 4;
      canvas.strokeStyle = color;
      canvas.stroke();
    }

    // Use facingMode: environment to attemt to get the front camera on phones
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
      video.srcObject = stream;
      video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
      video.play();
      requestAnimationFrame(tick);
    });

    function incrementJumahBarang (elm, kode) {
        const sound = new Audio('<?=base_url()?>/template/assets/img/beep.mp3');
        sound.play();
        let value = parseInt(elm.value)
        if (Number.isNaN(value)) {
            value = 0;
        }
        const maxJumlahBarang = listBarang[kode]
        console.log('maxJumlahBarang', maxJumlahBarang)

        if (value < maxJumlahBarang) {
            sound.play();
            elm.value = value + 1;
        } else {
            alert('Jumlah barang ' + kode + ' sudah mencukupi!')
            // outputData.innerHtml = '<span style="color: red">Jumlah barang sudah mencukupi!</span>'
        }
    }

    const debouncedIncrementJumahBarang = _.debounce(incrementJumahBarang, 500)

    function tick() {
      loadingMessage.innerText = "⌛ Loading video..."
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        loadingMessage.hidden = true;
        canvasElement.hidden = false;
        outputContainer.hidden = false;

        canvasElement.height = video.videoHeight;
        canvasElement.width = video.videoWidth;
        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
        var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
        var code = jsQR(imageData.data, imageData.width, imageData.height, {
          inversionAttempts: "dontInvert",
        });
        if (code) {
          drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
          drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
          drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
          drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
          outputMessage.hidden = true;
          outputData.parentElement.hidden = false;
          outputData.innerText = code.data; // BRG101 - TRIANGLE INTERNAL
          
        //   debouncedIncrementJumahBarang(code)
            const scannedBarcode = code.data.split(' - ') // ['BRG101', 'TRIANGEL INTERNAL']
            const kodeBarang = scannedBarcode[0]
            const namaBarang = scannedBarcode[1]
            const elm = document.getElementById(kodeBarang)

            if (elm) {
                debouncedIncrementJumahBarang(elm, kodeBarang)
            }

            // const jumlahTBarang = listBarang[code.data];
        } else {
          outputMessage.hidden = false;
          outputData.parentElement.hidden = true;
        }
      }
      requestAnimationFrame(tick);
    }
  </script>
<?= $this->endSection() ?>
