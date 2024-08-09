<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Edit Pengadaan &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="<?= site_url('Pengadaan/index') ?>" class="btn primary"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Edit Pengadaan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Edit Pengadaan Barang</h4>
            </div>
            <div class="card-body col-md-12">
                <?php echo form_open('Pengadaan/ubahdata/' . $pengadaan['kode_po']) ?>
                <div class="form-group">
                    <label for="Kode Permintaan">Kode Permintaan *</label>
                    <input name="kode_permintaan" class="form-control" placeholder="Kode Permintaan" value="<?= $pengadaan['kode_permintaan'] ?>" required readonly>
                </div>
                <div class="form-group">
                    <label for="Kode PO">Kode PO *</label>
                    <input name="kode_po" class="form-control" placeholder="Kode PO" value="<?= $pengadaan['kode_po'] ?>" required readonly>
                </div>
                <div class="form-group">
                    <label for="Tanggal Pengadaan">Tanggal Pengadaan *</label>
                    <input type="date" name="tanggal_pengadaan" class="form-control" value="<?= $pengadaan['tanggal_pengadaan'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="Nama Supplier">Nama Supplier *</label>
                    <input name="nama_supplier" class="form-control" placeholder="Nama Supplier" value="<?= $pengadaan['nama_supplier'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="Tanggal Pengadaan">Tanggal Pengadaan *</label>
                    <input type="date" name="tanggal_pengadaan" class="form-control" value="<?= $pengadaan['tanggal_pengadaan'] ?>" required>
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
                                <th>Harga Satuan</th>
                                <th>Jumlah Barang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail_pengadaan as $detail) { ?>
                                <tr>
                                    <td><input type="text" name="kode_barang[]" class="form-control kode_barang" value="<?= $detail['kode_barang'] ?>" readonly></td>
                                    <td><input type="text" name="nama_barang[]" class="form-control nama_barang" value="<?= $detail['nama_barang'] ?>" readonly></td>
                                    <td><input type="text" name="satuan[]" class="form-control satuan" value="<?= $detail['satuan'] ?>" readonly></td>
                                    <td><input type="number" name="harga_satuan[]" class="form-control harga_satuan" value="<?= $detail['harga_satuan'] ?>" readonly></td>
                                    <td><input type="number" name="jumlah_barang[]" class="form-control" value="<?= $detail['jumlah_barang'] ?>" required readonly></td>
                                    <td><button type="button" class="btn btn-danger btn-remove-row">Hapus</button></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
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
        const searchInput = document.getElementById('searchInput');

        const addRow = () => {
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td>
                    <select name="kode_barang[]" class="form-control kode_barang">
                        <option value="">Pilih Barang</option>
                        <?php foreach ($barang as $value) { ?>
                            <option value="<?= $value['kode_barang'] ?>" data-nama="<?= $value['nama_barang'] ?>" data-satuan="<?= $value['satuan'] ?>" data-harga="<?= $value['harga_satuan'] ?>">
                                <?= $value['kode_barang'] ?> - <?= $value['nama_barang'] ?> - <?= $value['harga_satuan'] ?>
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
                    <input name="harga_satuan[]" type="number" class="form-control harga_satuan" placeholder="Harga Satuan" required>
                </td>
                <td>
                    <input name="jumlah_barang[]" type="number" class="form-control" placeholder="Jumlah Barang" required>
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
                row.querySelector('.nama_barang').value = selectedOption.getAttribute('data-nama');
                row.querySelector('.satuan').value = selectedOption.getAttribute('data-satuan');
                row.querySelector('.harga_satuan').value = selectedOption.getAttribute('data-harga');
            });

            row.querySelector('.btn-remove-row').addEventListener('click', function () {
                row.remove();
            });
        };

        // Attach event listener to the initial rows
        const rows = barangTable.querySelectorAll('tbody tr');
        rows.forEach(row => {
            attachEventListeners(row);
        });

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
        const tanggalPengadaanInput = document.querySelector('input[name="tanggal_pengadaan"]');

        function validateTanggalPengadaan(input) {
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

        tanggalPengadaanInput.addEventListener('change', function() {
            validateTanggalPengadaan(tanggalPengadaanInput);
        });
    });
</script>
<?= $this->endSection() ?>
