<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Edit Penerimaan &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="<?= site_url('Penerimaan/index') ?>" class="btn btn-primary"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Edit Penerimaan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Edit Penerimaan Barang</h4>
            </div>
            <div class="card-body">
                <?= form_open('Penerimaan/ubahdata/' . $penerimaan['kode_penerimaan']) ?>
                <div class="form-group">
                    <label for="kode_po">Kode PO *</label>
                    <select name="kode_po" id="kode_po" class="form-control" required disabled>
                        <option value="<?= $penerimaan['kode_po'] ?>"><?= $penerimaan['kode_po'] ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kode_penerimaan">Kode Penerimaan *</label>
                    <input type="text" name="kode_penerimaan" class="form-control" value="<?= $penerimaan['kode_penerimaan'] ?>" readonly required style="pointer-events: none;">
                </div>
                <div class="form-group">
                    <label for="tanggal_penerimaan">Tanggal Penerimaan *</label>
                    <input type="date" name="tanggal_penerimaan" id="tanggal_penerimaan" class="form-control" value="<?= $penerimaan['tanggal_penerimaan'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="nomor_po">Nomor PO *</label>
                    <input name="nomor_po" class="form-control" placeholder="Nomor PO" value="<?= $penerimaan['nomor_po'] ?>" readonly required>
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
                                <th>Jumlah Yang Dipesan</th>
                                <th>Jumlah Yang Diterima</th>
                                <th>Kondisi Barang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail_penerimaan as $detail) { ?>
                                <tr>
                                    <td><input type="text" name="kode_barang[]" class="form-control kode_barang" value="<?= $detail['kode_barang'] ?>" readonly></td>
                                    <td><input type="text" name="nama_barang[]" class="form-control nama_barang" value="<?= $detail['nama_barang'] ?>" readonly></td>
                                    <td><input type="text" name="satuan[]" class="form-control satuan" value="<?= $detail['satuan'] ?>" readonly></td>
                                    <td><input type="number" class="form-control" value="<?= $detail['jumlah_dipesan'] ?>" readonly></td>
                                    <td><input type="number" name="jumlah_yang_diterima[]" class="form-control" value="<?= $detail['jumlah_yang_diterima'] ?>" required></td>
                                    <td><input type="text" name="kondisi_barang[]" class="form-control kondisi_barang" value="<?= $detail['kondisi_barang'] ?>" required></td>
                                    <td><button type="button" class="btn btn-danger btn-remove-row">Hapus</button></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" onclick="window.history.back()">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
                <?= form_close() ?>
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
        
        const attachEventListeners = (row) => {
            row.querySelector('.btn-remove-row').addEventListener('click', function () {
                row.remove();
            });
        };

        // Attach event listener to the initial rows
        barangTable.querySelectorAll('tbody tr').forEach(row => {
            attachEventListeners(row);
        });

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
</script>
<?= $this->endSection() ?>
