<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Penukaran Barang &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <section class="section">
        <div class="section-header">
            <h1>Penukaran</h1>
            <div class="section-header-button">
            <?php if ($isAdmin) : ?>
                <a href="<?=site_url('Penukaran/tambahdata')?>" class="btn btn-primary"></i>Tambah Penukaran</a>
            <?php endif; ?>
            </div>
        </div>

        <div class="section-body">
            <div class="section-header">
                        <div class="row mb-4" style="overflow: scroll">
                        <?php if ($isAdmin) : ?>
                                <div class="col-md-12">
                                    <h6>Cetak Laporan Penukaran Barang</h6>
                                    <form action="<?= site_url('Penukaran/filter') ?>" method="get" class="form-inline">
                                        <input type="date" name="start_date" class="form-control mb-2 mr-sm-2" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>" required>
                                        <input type="date" name="end_date" class="form-control mb-2 mr-sm-2" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>" required>
                                        <button type="submit" class="btn btn-primary mb-2">Filter</button>
                                        <div class="d-flex justify-content-end">
                                        <a href="<?= base_url('Penukaran/cetakLaporan?start_date=' . (isset($_GET['start_date']) ? $_GET['start_date'] : '') . '&end_date=' . (isset($_GET['end_date']) ? $_GET['end_date'] : '')) ?>" class="btn btn-primary mb-2 ml-2" target="_blank">Cetak PDF</a>
                                        </div>
                                    </form>
                                </div>
                        <?php endif; ?>
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
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari Penukaran...">
                    </div>

                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th scope="col" width=50px>No</th>
                                    <th scope="col">Kode Pengeluaran</th>
                                    <th scope="col">Kode Penukaran</th>
                                    <th scope="col">Tanggal Penukaran</th>
                                    <th scope="col">Timestamp</th>
                                    <th scope="col">Detail</th>
                                    <?php if ($isAdmin) : ?>
                                        <th scope="col">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($penukaran as $value) :
                                ?>
                                    <tr scope="row" class="text-center">
                                        <td><?= $no++ ?></td>
                                        <td><?= $value['kode_pengeluaran'] ?></td>
                                        <td><?= $value['kode_penukaran'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($value['tanggal_penukaran'])) ?></td>
                                        <td><?= $value['timestamp'] ?></td>
                                        <td><button class="btn btn-info btn-sm btn-flat" data-toggle="modal" data-target="#detailModal<?= $value['kode_penukaran']; ?>"><i class="fas fa-info-circle"></i> Detail</button></td>
                                        <?php if ($isAdmin) : ?>
                                        <td class="text-center" style="width:15%">
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusModal-<?= $value['kode_penukaran'] ?>"><i class="fas fa-trash"></i></button>
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

    <?php foreach ($penukaran as $value) : ?>
    <!-- Modal -->
    <div class="modal fade" id="detailModal<?= $value['kode_penukaran']; ?>" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Penukaran Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Kode Pengeluaran: <?= $value['kode_pengeluaran']; ?></strong> <span id="detailKodePengeluaran"></span></p>
                    <p><strong>Kode Penukaran: <?= $value['kode_penukaran']; ?></strong> <span id="detailKodePenukaran"></span></p>
                    <p><strong>Tanggal Penukaran: <?= date('d/m/Y', strtotime($value['tanggal_penukaran'])) ?></strong> <span id="detailTanggalPenukaran"></span></p>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">Kode Barang</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Satuan</th>
                                    <th scope="col">Jumlah Penukaran</th>
                                    <th scope="col">Alasan Penukaran</th>
                                </tr>
                            </thead>
                            <tbody id="detailBarangList">
                                <?php if (!empty($detail_penukaran[$value['kode_penukaran']])): ?>
                                    <?php foreach ($detail_penukaran[$value['kode_penukaran']] as $detailtkr) : ?>
                                            <tr class="text-center">
                                                <td><?= $detailtkr['kode_barang']; ?></td>
                                                <td><?= $detailtkr['nama_barang']; ?></td>
                                                <td><?= $detailtkr['satuan']; ?></td>
                                                <td><?= $detailtkr['jumlah_penukaran']; ?></td>
                                                <td><?= $detailtkr['alasan_penukaran']; ?></td>
                                            </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">Tidak ada data detail penukaran.</td>
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
    <?php foreach ($penukaran as $value) : ?>
        <div class="modal fade" id="hapusModal-<?= $value['kode_penukaran'] ?>" tabindex="-1" role="dialog" aria-labelledby="hapusModalLabel-<?= $value['kode_penukaran'] ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="hapusModalLabel-<?= $value['kode_penukaran'] ?>">Hapus Data <?= $value['kode_penukaran']; ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus Penukaran dengan kode penukaran <strong><?= $value['kode_penukaran']; ?></strong>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <a href="<?= base_url('Penukaran/hapusdata/' . $value['kode_penukaran']) ?>" class="btn btn-danger">Delete</a>
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
