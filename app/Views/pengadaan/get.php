<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Pengadaan Barang &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <section class="section">
        <div class="section-header">
            <h1>Pengadaan</h1>
            <div class="section-header-button">
            <?php if ($isAdmin) : ?>
                <a href="<?=site_url('Pengadaan/tambahdata')?>" class="btn btn-primary"></i>Buat Purchase Order</a>
            <?php endif; ?>
            </div>
        </div>

        <div class="section-body">
            <div class="section">
                <div class="section-header">
                    <div class="row mb-4">
                    <?php if ($isAdmin) : ?>
                        <div class="col-md-12">
                            <h6>Cetak Laporan Pengadaan Barang</h6>
                            <form action="<?= site_url('Pengadaan/filter') ?>" method="get" class="form-inline">
                                <input type="date" name="start_date" class="form-control mb-2 mr-sm-2" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>" required>
                                <input type="date" name="end_date" class="form-control mb-2 mr-sm-2" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>" required>
                                <button type="submit" class="btn btn-primary mb-2">Filter</button>
                                <div class="d-flex justify-content-end">
                                <a href="<?= base_url('Pengadaan/cetakLaporan?start_date=' . (isset($_GET['start_date']) ? $_GET['start_date'] : '') . '&end_date=' . (isset($_GET['end_date']) ? $_GET['end_date'] : '')) ?>" class="btn btn-primary mb-2 ml-2" target="_blank">Cetak PDF</a>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                <div class="card-body">
                    <?php
                    if (session()->getFlashdata('pesan')) {
                        echo '<div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i>';
                        echo session()->getFlashdata('pesan');
                        echo '</h5>
                        </div>';
                    }
                    ?>
                    <?php
                    if (session()->getFlashdata('error')) {
                        echo '<div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i>';
                        echo session()->getFlashdata('error');
                        echo '</h5>
                        </div>';
                    }
                    ?>

                    <!-- Input pencarian -->
                    <div class="form-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari Pengadaan...">
                    </div>

                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th scope="col" width=50px>No</th>
                                    <th scope="col">Kode Permintaan</th>
                                    <th scope="col">Kode PO</th>
                                    <th scope="col">Tanggal Pengadaan</th>
                                    <th scope="col">Timestamp</th>
                                    <th scope="col">Detail</th>
                                    <th scope="col">Status</th>
                                    <?php if ($isAdmin || $isKepalaPembelian) : ?>
                                        <th scope="col">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($pengadaan as $value) :
                                ?>
                                    <tr scope="row" class="text-center">
                                        <td><?= $no++ ?></td>
                                        <td><?= $value['kode_permintaan'] ?></td>
                                        <td><?= $value['kode_po'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($value['tanggal_pengadaan'])) ?></td>
                                        <td><?= $value['timestamp'] ?></td>
                                        <td><button class="btn btn-info btn-sm btn-flat" data-toggle="modal" data-target="#detailModal" data-kode="<?= $value['kode_po']; ?>"><i class="fas fa-info-circle"></i> Detail</button></td>
                                        <?php if ($isKepalaPembelian) : ?>
                                            <td class="text-center" style="width: 15%">
                                                <?php if (is_null($value['status'])) : ?>
                                                    <a href="<?= site_url('Pengadaan/approve/' . $value['kode_po']) ?>" class="btn btn-success btn-sm"><i class="fas fa-check"></i></a>
                                                    <a href="<?= site_url('Pengadaan/reject/' . $value['kode_po']) ?>" class="btn btn-danger btn-sm"><i class="fas fa-times"></i></a>
                                                <?php else : ?>
                                                    <?php if ($value['status'] == 1) : ?>
                                                        <span class="badge badge-success">Disetujui</span>
                                                    <?php elseif ($value['status'] == 0) : ?>
                                                        <span class="badge badge-danger">Ditolak</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                        <?php if ($isAdmin) : ?>
                                            <td>
                                                <?php if (is_null($value['status'])) : ?>
                                                    <span class="badge badge-warning">Menunggu Konfirmasi</span>
                                                <?php elseif ($value['status'] == 1) : ?>
                                                    <span class="badge badge-success">Disetujui</span>
                                                <?php elseif ($value['status'] == 0) : ?>
                                                    <span class="badge badge-danger">Ditolak</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                        <?php if ($isKepalaPembelian) : ?>
                                            <td class="text-center" style="width:15%">
                                                <?php if ($isKepalaPembelian && $value['status'] == 1) : ?>
                                                    <a href="<?= site_url('pengadaan/edit/' . $value['kode_po']) ?>" class="btn btn-warning btn-sm mr-1">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    <a href="<?= base_url('Pengadaan/downloadInvoice/' . $value['kode_po']) ?>" class="btn btn-success btn-sm"><i class="fas fa-download"></i></a>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusModal-<?= $value['kode_po'] ?>"><i class="fas fa-trash"></i></button>
                                            </td>
                                        <?php endif; ?>
                                        <?php if ($isAdmin) : ?>
                                            <td class="text-center" style="width:15%">
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusModal-<?= $value['kode_po'] ?>"><i class="fas fa-trash"></i></button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php
                                    $no++;
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php foreach ($pengadaan as $value) : ?>
    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Pengadaan Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Kode Permintaan: <?= $value['kode_permintaan']; ?></strong> <span id="detailKodePermintaan"></span></p>
                    <p><strong>Kode PO: <?= $value['kode_po']; ?></strong> <span id="detailKodePO"></span></p>
                    <p><strong>Tanggal Pengadaan: <?= date('d/m/Y', strtotime($value['tanggal_pengadaan'])) ?></strong> <span id="detailTanggalPengadaan"></span></p>
                    <p><strong>Nama Supplier: <?= $value['nama_supplier']; ?></strong> <span id="detailNamaSupplier"></span></p>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">Kode Barang</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Satuan</th>
                                    <th scope="col">Harga Satuan</th>
                                    <th scope="col">Jumlah Barang</th>
                                    <th scope="col">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody id="detailBarangList">
                                <?php if (!empty($detail_pengadaan[$value['kode_po']])): ?>
                                    <?php foreach ($detail_pengadaan[$value['kode_po']] as $detailpo) : ?>
                                            <tr class="text-center">
                                                <td><?= $detailpo['kode_barang']; ?></td>
                                                <td><?= $detailpo['nama_barang']; ?></td>
                                                <td><?= $detailpo['satuan']; ?></td>
                                                <td><?= number_format($detailpo['harga_satuan'], 0, ',', '.') ?></td>
                                                <td><?= $detailpo['jumlah_barang']; ?></td>
                                                <td><?= number_format($detailpo['harga_satuan'] * $detailpo['jumlah_barang'], 0, ',', '.') ?></td>
                                            </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">Tidak ada data detail pengadaan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Modal Hapus Data -->
    <?php foreach ($pengadaan as $value) : ?>
        <div class="modal fade" id="hapusModal-<?= $value['kode_po'] ?>" tabindex="-1" role="dialog" aria-labelledby="hapusModalLabel-<?= $value['kode_po'] ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="hapusModalLabel-<?= $value['kode_po'] ?>">Hapus Data <?= $value['kode_po']; ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus Purchase Order dengan kode PO <strong><?= $value['kode_po']; ?></strong>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <a href="<?= base_url('Pengadaan/hapusdata/' . $value['kode_po']) ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        $('#detailModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var kodePo = button.data('kode');

            $.ajax({
                url: '<?= site_url("Pengadaan/getDetail") ?>/' + kodePo,
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#detailKodePermintaan').text(data.kode_permintaan);
                    $('#detailKodePO').text(data.kode_po);
                    $('#detailTanggalPengadaan').text(data.tanggal_pengadaan);
                    $('#detailNamaSupplier').text(data.nama_supplier);
                    $('#detailTimestamp').text(data.timestamp);

                    var barangList = '';
                    if (data.barang.length > 0) {
                        data.barang.forEach(function (barang) {
                            barangList += '<tr>' +
                                '<td>' + barang.kode_barang + '</td>' +
                                '<td>' + barang.nama_barang + '</td>' +
                                '<td>' + barang.satuan + '</td>' +
                                '<td>' + barang.harga_satuan + '</td>' +
                                '<td>' + barang.jumlah_yang_diminta + '</td>' +
                                '<td>' + barang.total_harga + '</td>' +
                                '</tr>';
                        });
                    } else {
                        barangList += '<tr><td colspan="5">Tidak ada data detail barang.</td></tr>';
                    }

                    $('#detailBarangList-' + kodePengadaan).html(barangList);
                },
                error: function () {
                    alert('Gagal mengambil data detail.');
                }
            });
        });
    </script>

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

            $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            $('#dataTable tbody tr').filter(function() {
                var date = new Date($(this).find('td:eq(3)').text().split('/').reverse().join('-'));
                var start = new Date(startDate);
                var end = new Date(endDate);
                return (date >= start && date <= end);
            }).toggle(true);

            $('#dataTable tbody tr').filter(function() {
                var date = new Date($(this).find('td:eq(3)').text().split('/').reverse().join('-'));
                var start = new Date(startDate);
                var end = new Date(endDate);
                return !(date >= start && date <= end);
            }).toggle(false);
        });
    });
</script>
<?= $this->endSection() ?>
