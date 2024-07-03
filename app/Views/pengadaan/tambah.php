<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Tambah Pengadaan &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="<?= site_url('Pengadaan/index') ?>" class="btn primary"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Tambah Pengadaan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Pengadaan Barang</h4>
            </div>
            <div class="card-body col-md-12">
                <?php echo form_open('Pengadaan/insertdata') ?>
                <div class="form-group">
                    <label for="Kode Permintaan">Kode Permintaan *</label>
                    <select name="kode_permintaan" class="form-control">
                            <option value="">Kode Permintaan</option>
                            <?php foreach ($permintaan as $key => $p) { ?>
                                <option value="<?= $p['kode_permintaan'] ?>"><?= $p['kode_permintaan'] ?></option>
                            <?php    } ?>
                        </select>
                </div>
                <div class="form-group">
                    <label for="Kode PO">Kode PO *</label>
                    <input name="kode_po" class="form-control" placeholder="Kode PO" required>
                </div>
                <div class="form-group">
                    <label for="Tanggal Pengadaan">Tanggal Pengadaan *</label>
                    <input type="date" name="tanggal_pengadaan" class="form-control" placeholder="Tanggal Pengadaan" required>
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
                                <th>Harga Satuan</th>
                                <th>Jumlah Barang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="kode_barang[]" class="form-control kode_barang">
                                        <option value="">Pilih Barang</option>
                                        <?php foreach ($barang as $b) { ?>
                                            <option value="<?= $b['kode_barang'] ?>" data-nama="<?= $b['nama_barang'] ?>" data-satuan="<?= $b['satuan'] ?>" data-harga="<?= $b['harga_satuan'] ?>">
                                                <?= $b['kode_barang'] ?> - <?= $b['nama_barang'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td><input type="text" name="nama_barang[]" class="form-control nama_barang" readonly></td>
                                <td><input type="text" name="satuan[]" class="form-control satuan" readonly></td>
                                <td><input type="number" name="harga_satuan[]" class="form-control harga_satuan" readonly></td>
                                <td><input type="number" name="jumlah_barang[]" class="form-control jumlah_barang"></td>
                                <td><button type="button" class="btn btn-danger btn-remove-row">Hapus</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary" id="addRowBtn">Tambah Barang</button>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default">Close</button>
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
                    <input name="harga_satuan[]" type="number" class="form-control harga_satuan" placeholder="Harga Satuan" required readonly>
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
