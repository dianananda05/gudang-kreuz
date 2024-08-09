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
                    <select id="kode_permintaan" name="kode_permintaan" class="form-control">
                        <option value="">Kode Permintaan</option>
                        <?php foreach ($kode_perm_available as $kodePerm) : ?>
                            <?php $selected = (isset($_GET['kode_permintaan']) && $_GET['kode_permintaan'] === $kodePerm) ? 'selected' : ''; ?>
                            <option value="<?= $kodePerm ?>" <?= $selected ?>><?= $kodePerm ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="Kode PO">Kode PO *</label>
                    <input type="text" name="kode_po" class="form-control" value="<?= isset($kode_po) ? $kode_po : '' ?>" readonly required style="pointer-events: none;">
                </div>
                <div class="form-group">
                    <label for="Tanggal Pengadaan">Tanggal Pengadaan *</label>
                    <input type="date" name="tanggal_pengadaan" class="form-control" placeholder="Tanggal Pengadaan" required value="<?= date('Y-m-d') ?>">
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
                                <?php if ($isKepalaPembelian) : ?>
                                <th>Harga Satuan</th>
                                <?php endif; ?>
                                <th>Jumlah Yang Dipesan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list_barang as $barang) : ?>
                                    <tr>
                                        <td><input name="kode_barang[]" class="form-control" value="<?= $barang['kode_barang'] ?>"></td>
                                        <td><?= $barang['nama_barang'] ?></td>
                                        <td><?= $barang['satuan'] ?></td>
                                        <td><?= $barang['jumlah_yang_diminta'] ?></td>
                                        <?php if ($isKepalaPembelian) : ?>
                                        <td><input type="number" name="harga_satuan[]" class="form-control harga_satuan" placeholder="Harga Satuan" readonly></td>
                                        <?php endif; ?>
                                        <td><input type="number" name="jumlah_barang[]" class="form-control jumlah_barang" placeholder="Jumlah Barang"></td>
                                        <td><button type="button" class="btn btn-danger btn-remove-row">Hapus</button></td>
                                    </tr>
                            <?php endforeach; ?>
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
                            <?php if ($isKepalaPembelian) : ?>
                            <td><input type="number" name="harga_satuan[]" class="form-control harga_satuan" placeholder="Harga Satuan" value="${barang.harga_satuan}" readonly></td>
                            <?php endif; ?>
                            <td><input type="number" name="jumlah_barang[]" class="form-control jumlah_barang" placeholder="Jumlah Barang" value="${barang.jumlah_barang}"></td>
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

        document.addEventListener('DOMContentLoaded', function() {
            const tanggalPengadaanInput = document.querySelector('input[name="tanggal_pengadaan"]');
            tanggalPengadaanInput.valueAsDate = new Date();

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
    });
</script>
<?= $this->endSection() ?>
