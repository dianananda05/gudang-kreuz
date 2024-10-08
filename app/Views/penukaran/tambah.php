<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Tambah Penukaran &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="<?= site_url('Penukaran/index') ?>" class="btn primary"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Tambah Penukaran</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Penukaran Barang</h4>
            </div>
            <div class="card-body col-md-12">
                <?php echo form_open('Penukaran/insertdata') ?>
                <div class="form-group">
                    <label for="Kode Pengeluaran">Kode Pengeluaran *</label>
                    <select name="kode_pengeluaran" id="kode_pengeluaran" class="form-control" required>
                        <option value="">Pilih Kode Pengeluaran</option>
                        <?php
                        $uniqueKodePeng = array_unique(array_column($pengeluaran, 'kode_pengeluaran'));
                        foreach ($uniqueKodePeng as $kodePeng) :
                        ?>
                            <option value="<?= $kodePeng ?>"><?= $kodePeng ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="Kode Penukaran">Kode Penukaran *</label>
                    <input type="text" name="kode_penukaran" class="form-control" value="<?= isset($kode_penukaran) ? $kode_penukaran : '' ?>" readonly required style="pointer-events: none;">
                </div>
                <div class="form-group">
                    <label for="Tanggal Pengeluaran">Tanggal Penukaran *</label>
                    <input type="date" name="tanggal_penukaran" class="form-control" placeholder="Tanggal Penukaran" required value="<?= date('Y-m-d') ?>">
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
                                <th>Jumlah Penukaran</th>
                                <th>Alasan Penukaran</th>
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
                                    <input name="nama_barang[]" class="form-control nama_barang" placeholder="Nama Barang" required readonly>
                                </td>
                                <td>
                                    <input name="satuan[]" class="form-control satuan" placeholder="Satuan" required readonly>
                                </td>
                                <td>
                                    <input name="jumlah_penukaran[]" type="number" class="form-control" placeholder="Jumlah Penukaran" required>
                                </td>
                                <td>
                                    <input name="alasan_penukaran[]" class="form-control" placeholder="Alasan Penukaran" required>
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
                    <input name="nama_barang[]" class="form-control nama_barang" placeholder="Nama Barang" required readonly>
                </td>
                <td>
                    <input name="satuan[]" class="form-control satuan" placeholder="Satuan" required readonly>
                </td>
                <td>
                    <input name="jumlah_penukaran[]" type="number" class="form-control" placeholder="Jumlah Penukaran" required>
                </td>
                <td>
                    <input name="alasan_penukaran[]" class="form-control" placeholder="Alasan Penukaran" required>
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
        const tanggalPenukaranInput = document.querySelector('input[name="tanggal_penukaran"]');
        tanggalPenukaranInput.valueAsDate = new Date();

        function validateTanggalPenukaran(input) {
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

        tanggalPenukaranInput.addEventListener('change', function() {
            validateTanggalPenukaran(tanggalPenukaranInput);
        });
    });
</script>
<?= $this->endSection() ?>
