<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Data Barang &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <h1>Barang</h1>
        <div class="section-header-button">
        <?php if ($isAdmin) : ?>
            <a href="<?= site_url('Barang/tambahdata') ?>" class="btn btn-primary">Tambah Data</a>
            <a href="<?= site_url('Barang/cetakLaporan') ?>" class="btn btn-info ml-2"><i class="fas fa-print"></i> Cetak Laporan</a>
        <?php endif; ?>
        </div>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                    <?php if (session()->getFlashdata('pesan')) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong><i class="fas fa-check-circle"></i> Sukses!</strong> <?= session()->getFlashdata('pesan') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong><i class="fas fa-exclamation-circle"></i> Error!</strong> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                <!-- Input pencarian -->
                <div class="form-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari Barang...">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th scope="col" width="50px">No</th>
                                <th scope="col">Kode Barang</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Satuan</th>
                                <th scope="col">Stok</th>
                                <th scope="col">Stok In</th>
                                <th scope="col">Stok Out</th>
                                <th scope="col">Repair</th>
                                <th scope="col">Reject</th>
                                <?php if ($isKepalaPembelian) : ?>
                                <th scope="col">Harga Satuan</th>
                                <?php endif; ?>
                                <th scope="col">QR Code</th>
                                <th scope="col">Status</th>
                                <?php if ($isAdmin || $isKepalaPembelian) : ?>
                                    <th scope="col">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($barang as $value) : ?>
                            <tr class="text-center">
                                <td><?= $no++ ?></td>
                                <td><?= $value['kode_barang'] ?></td>
                                <td><?= $value['nama_barang'] ?></td>
                                <td><?= $value['satuan'] ?></td>
                                <td><?= $value['stok_tersisa'] ?></td>
                                <td><?= $value['total_masuk'] ?></td>
                                <td><?= $value['total_keluar'] ?></td>
                                <td><?= $value['repair'] ?></td>
                                <td><?= $value['reject'] ?></td>
                                <?php if ($isKepalaPembelian) : ?>
                                <td><?= number_format($value['harga_satuan'], 0, ',', '.') ?></td>
                                <?php endif; ?>
                                <td>
                                    <a href="#" onclick="openModal('<?= $value['kode_barang']; ?>', '<?= $value['nama_barang']; ?>');">
                                        <i class="fas fa-qrcode" style="font-size: 24px;"></i>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($value['stok_status'] === 'stok_minimum') : ?>
                                        <i class="fas fa-exclamation-triangle text-danger" title="Stok minimum"></i>
                                    <?php elseif ($value['stok_status'] === 'stok_mendekati_minimum') : ?>
                                        <i class="fas fa-exclamation-triangle text-warning" title="Stok mendekati minimum"></i>
                                    <?php elseif ($value['stok_status'] === 'stok_aman') : ?>
                                        <i class="fas fa-check-circle text-success" title="Stok aman"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center">
                                        <?php if ($isKepalaPembelian || $isAdmin) : ?>
                                        <a href="<?= site_url('barang/edit/' . $value['kode_barang']) ?>" class="btn btn-warning btn-sm mr-1">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($isAdmin) : ?>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusModal-<?= $value['kode_barang'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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

<!-- Modal Hapus Data -->
<?php foreach ($barang as $value) : ?>
<div class="modal fade" id="hapusModal-<?= $value['kode_barang'] ?>" tabindex="-1" role="dialog" aria-labelledby="hapusModalLabel-<?= $value['kode_barang'] ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="hapusModalLabel-<?= $value['kode_barang'] ?>">Hapus Data <?= $value['nama_barang']; ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data barang <strong><?= $value['nama_barang']; ?></strong> dengan kode barang <strong><?= $value['kode_barang']; ?></strong>?
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a href="<?= base_url('Barang/hapusdata/' . $value['kode_barang']) ?>" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Script untuk pencarian -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#dataTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
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

    // Fungsi untuk menutup modal
    function closeModal() {
        $('#qrCodeModal').modal('hide'); // Sembunyikan modal menggunakan Bootstrap
    }

        // Fungsi untuk menutup modal
        function closeModal() {
        $('#qrCodeModal').modal('hide'); // Sembunyikan modal menggunakan Bootstrap
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

<?= $this->endSection() ?>

