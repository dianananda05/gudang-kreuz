<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Tambah Permintaan &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="<?= site_url('Permintaan/index') ?>" class="btn primary"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Tambah Permintaan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Permintaan Barang</h4>
            </div>
            <div id="canvas" class="card-body col-md-12">
                <?php echo form_open('Permintaan/insertdata') ?>
                <div class="form-group">
                    <label for="Kode Permintaan">Kode Permintaan *</label>
                    <input name="kode_permintaan" id="kode_permintaan" class="form-control" placeholder="Kode Permintaan" value="<?= $newKode ?>" required readonly style="pointer-events: none;">
                </div>
                <div class="form-group">
                    <label for="Nama Pengaju">Nama Pengaju *</label>
                    <input name="nama_pengaju" class="form-control" placeholder="Nama Pengaju" required>
                </div>
                <div class="form-group">
                    <label for="Tanggal Permintaan">Tanggal Permintaan *</label>
                    <input type="date" name="tanggal_permintaan" class="form-control" placeholder="Tanggal Permintaan" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label for="Type Permintaan">Type Permintaan *</label>
                    <div class="form-check">
                        <input type="radio" name="type_permintaan" value="PRODUKSI" class="form-check-input" required>
                        <label class="form-check-label">PRODUKSI</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="type_permintaan" value="PENGADAAN" class="form-check-input" required>
                        <label class="form-check-label">PENGADAAN</label>
                    </div>
                </div>

                <!-- Tabel untuk memasukkan data barang -->
                <div class="form-group" style="overflow: scroll">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari Barang...">
                    <table class="table table-bordered" id="barangTable">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Jumlah Yang Diminta</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="kode_barang[]" class="form-control kode_barang">
                                        <option value="">Pilih Barang</option>
                                        <?php foreach ($barang as $value) { ?>
                                            <option value="<?= $value['kode_barang'] ?>" data-nama="<?= $value['nama_barang'] ?>" data-satuan="<?= $value['satuan'] ?>">
                                                <?= $value['kode_barang'] ?> - <?= $value['nama_barang'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <input name="nama_barang[]" class="form-control nama_barang" placeholder="Nama Barang" required readonly style="pointer-events: none;">
                                </td>
                                <td>
                                    <input name="satuan[]" class="form-control satuan" placeholder="Satuan" required readonly style="pointer-events: none;">
                                </td>
                                <td>
                                    <input name="jumlah_yang_diminta[]" type="number" class="form-control" placeholder="Jumlah Yang Diminta" required>
                                </td>
                                <td>
                                    <input name="keterangan[]" class="form-control" placeholder="Keterangan" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-remove-row">Hapus</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary" id="addRowBtn">Tambah Barang</button>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" onclick="window.history.back()">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Script untuk menambah baris baru secara otomatis dan fitur search -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const barangTable = document.getElementById('barangTable');
        const addRowBtn = document.getElementById('addRowBtn');
        const searchInput = document.getElementById('searchInput');
        const kodePermintaanInput = document.getElementById('kode_permintaan');
        const typePermintaanRadios = document.querySelectorAll('input[name="type_permintaan"]');

        // Fungsi untuk mengubah kode permintaan berdasarkan type permintaan yang dipilih
        const updateKodePermintaan = () => {
            const selectedTypePermintaan = document.querySelector('input[name="type_permintaan"]:checked');
            if (selectedTypePermintaan) {
                const typePermintaan = selectedTypePermintaan.value;
                // Kirim request AJAX atau gunakan data langsung dari PHP
                fetch(`/Permintaan/generateKodePermintaan/${typePermintaan}`)
                    .then(response => response.json())
                    .then(data => {
                        kodePermintaanInput.value = data.newKode;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        };

        // Event listener untuk perubahan pada radio button type permintaan
        typePermintaanRadios.forEach(radio => {
            radio.addEventListener('change', updateKodePermintaan);
        });

        updateKodePermintaan();

        const addRow = () => {
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td>
                    <select name="kode_barang[]" class="form-control kode_barang">
                        <option value="">Pilih Barang</option>
                        <?php foreach ($barang as $value) { ?>
                            <option value="<?= $value['kode_barang'] ?>" data-nama="<?= $value['nama_barang'] ?>" data-satuan="<?= $value['satuan'] ?>">
                                <?= $value['kode_barang'] ?> - <?= $value['nama_barang'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <input name="nama_barang[]" class="form-control nama_barang" placeholder="Nama Barang" required readonly style="pointer-events: none;">
                </td>
                <td>
                    <input name="satuan[]" class="form-control satuan" placeholder="Satuan" required readonly style="pointer-events: none;">
                </td>
                <td>
                    <input name="jumlah_yang_diminta[]" type="number" class="form-control" placeholder="Jumlah Yang Diminta" required>
                </td>
                <td>
                    <input name="keterangan[]" class="form-control" placeholder="Keterangan" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-remove-row">Hapus</button>
                </td>
            `;

            barangTable.querySelector('tbody').appendChild(newRow);

            attachEventListeners(newRow);
        };

        const attachEventListeners = (row) => {
            row.querySelector('.kode_barang').addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const selectedKode = selectedOption.value;
                const selectedNama = selectedOption.getAttribute('data-nama');
                const selectedSatuan = selectedOption.getAttribute('data-satuan');
                // Check if the selected code is already in the table
                const existingRows = barangTable.querySelectorAll('tbody tr');
                let isDuplicate = false;
                existingRows.forEach(existingRow => {
                    if (existingRow !== row) { // Exclude current row from comparison
                        const existingKode = existingRow.querySelector('.kode_barang').value;
                        if (existingKode === selectedKode) {
                            isDuplicate = true;
                            return;
                        }
                    }
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplikat Barang',
                        text: 'Barang ini sudah ditambahkan sebelumnya.'
                    });
                    this.selectedIndex = 0; // Reset dropdown selection
                } else {
                    // Populate nama_barang and satuan fields
                    row.querySelector('.nama_barang').value = selectedNama;
                    row.querySelector('.satuan').value = selectedSatuan;
                }
            });

            row.querySelector('.btn-remove-row').addEventListener('click', function () {
                row.remove();
            });
        };

        // Attach event listener to the initial row
        attachEventListeners(barangTable.querySelector('tbody tr'));

        // Event listener for the "Tambah Barang" button
        addRowBtn.addEventListener('click', addRow);

        // Search functionality
        searchInput.addEventListener('input', function () {
            const filter = searchInput.value.toLowerCase();
            const rows = barangTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const kodeBarang = row.querySelector('.kode_barang');
                const namaBarang = row.querySelector('.nama_barang');

                if (kodeBarang && namaBarang) {
                    const textValue = (kodeBarang.value + namaBarang.value).toLowerCase();
                    row.style.display = textValue.includes(filter) ? '' : 'none';
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const tanggalPermintaanInput = document.querySelector('input[name="tanggal_permintaan"]');
        tanggalPermintaanInput.valueAsDate = new Date();

        function validateTanggalPermintaan(input) {
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

        tanggalPermintaanInput.addEventListener('change', function() {
            validateTanggalPermintaan(tanggalPermintaanInput);
        });
    });
</script>
<?= $this->endSection() ?>
