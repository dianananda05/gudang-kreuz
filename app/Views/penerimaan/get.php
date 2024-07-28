<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Penerimaan Barang &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <section class="section">
        <div class="section-header">
            <h1>Penerimaan</h1>
            <div class="section-header-button">
            <?php if ($isAdmin) : ?>
                <a href="<?=site_url('Penerimaan/tambahdata')?>" class="btn btn-primary"></i>Tambah Data Penerimaan</a>
            <?php endif; ?>
            </div>
        </div>

        <div class="section-body">
            <div class="section">
                    <div class="section-header">
                        <div class="row mb-4">
                        <?php if ($isAdmin) : ?>
                            <div class="col-md-12">
                                <h6>Cetak Laporan Penerimaan Barang</h6>
                                <form action="<?= site_url('Penerimaan/filter') ?>" method="get" class="form-inline">
                                    <input type="date" name="start_date" class="form-control mb-2 mr-sm-2" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>" required>
                                    <input type="date" name="end_date" class="form-control mb-2 mr-sm-2" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>" required>
                                    <button type="submit" class="btn btn-primary mb-2">Filter</button>
                                    <div class="d-flex justify-content-end">
                                    <a href="<?= base_url('Penerimaan/cetakLaporan?start_date=' . (isset($_GET['start_date']) ? $_GET['start_date'] : '') . '&end_date=' . (isset($_GET['end_date']) ? $_GET['end_date'] : '')) ?>" class="btn btn-primary mb-2 ml-2" target="_blank">Cetak PDF</a>
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
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari Penerimaan...">
                    </div>

                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th scope="col" width=50px>No</th>
                                    <th scope="col">Kode PO</th>
                                    <th scope="col">Kode Penerimaan</th>
                                    <th scope="col">Tanggal Penerimaan</th>
                                    <th scope="col">Nomor PO</th>
                                    <th scope="col">Timestamp</th>
                                    <th scope="col">Detail</th>
                                    <?php if ($isAdmin) : ?>
                                        <th scope="col">Aksi</th>
                                    <?php endif; ?>
                                    <?php if ($isKepalaPembelian || $isKepalaGudang) : ?>
                                        <th scope="col">Status</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($penerimaan as $value) :
                                ?>
                                    <tr scope="row" class="text-center">
                                        <td><?= $no++ ?></td>
                                        <td><?= $value['kode_po'] ?></td>
                                        <td><?= $value['kode_penerimaan'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($value['tanggal_penerimaan'])) ?></td>
                                        <td><?= $value['nomor_po'] ?></td>
                                        <td><?= $value['timestamp'] ?></td>
                                        <td><button class="btn btn-info btn-sm btn-flat" data-toggle="modal" data-target="#detailModal<?= $value['kode_penerimaan']; ?>"><i class="fas fa-info-circle"></i> Detail</button></td>
                                        <?php if ($isAdmin) : ?>   
                                        <td class="text-center" style="width:15%">
                                            <?php if ($value['status'] == 0) : ?>
                                            <a href="<?= site_url('penerimaan/edit/' . $value['kode_penerimaan']) ?>" class="btn btn-warning btn-sm mr-1">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <?php endif; ?> 
                                            <!-- <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusModal-<?= $value['kode_penerimaan'] ?>"><i class="fas fa-trash"></i></button>-->
                                        </td> 
                                        <?php endif; ?>
                                        <?php if ($isKepalaPembelian || $isKepalaGudang) : ?>
                                            <td>
                                                <?php if (is_null($value['status'])) : ?>
                                                    <span class="badge badge-warning">Dalam Proses</span>
                                                <?php elseif ($value['status'] == 1) : ?>
                                                    <span class="badge badge-success">Selesai</span>
                                                <?php endif; ?>
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

    <?php foreach ($penerimaan as $value) : ?>
    <!-- Modal -->
    <div class="modal fade" id="detailModal<?= $value['kode_penerimaan']; ?>" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Penerimaan Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Kode PO: <?= $value['kode_po']; ?></strong> <span id="detailKodePO"></span></p>
                    <p><strong>Kode Penerimaan: <?= $value['kode_penerimaan']; ?></strong> <span id="detailKodePenerimaan"></span></p>
                    <p><strong>Tanggal Penerimaan: <?= date('d/m/Y', strtotime($value['tanggal_penerimaan'])) ?></strong> <span id="detailTanggalPenerimaan"></span></p>
                    <p><strong>Nomor PO: <?= $value['nomor_po']; ?></strong><span id="detailTypePenerimaan"></span></p>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">Kode Barang</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Satuan</th>
                                    <th scope="col">Jumlah Yang Diterima</th>
                                    <th scope="col">Kondisi Barang</th>
                                </tr>
                            </thead>
                            <tbody id="detailBarangList">
                                <?php if (!empty($detail_penerimaan[$value['kode_penerimaan']])): ?>
                                    <?php foreach ($detail_penerimaan[$value['kode_penerimaan']] as $detailtrm) : ?>
                                            <tr class="text-center">
                                                <td><?= $detailtrm['kode_barang']; ?></td>
                                                <td><?= $detailtrm['nama_barang']; ?></td>
                                                <td><?= $detailtrm['satuan']; ?></td>
                                                <td><?= $detailtrm['jumlah_yang_diterima']; ?></td>
                                                <td><?= $detailtrm['kondisi_barang']; ?></td>
                                            </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">Tidak ada data detail penerimaan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if ($isAdmin) : ?>
                <div class="modal-footer">
                    <?php if ($value['status'] != 1) : ?>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <form action="<?= site_url('Penerimaan/selesaiPenerimaan/' . $value['kode_penerimaan']) ?>" method="post">
                        <button type="submit" class="btn btn-success">Selesai</button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Modal Hapus Data -->
    <?php foreach ($penerimaan as $value) : ?>
        <div class="modal fade" id="hapusModal-<?= $value['kode_penerimaan'] ?>" tabindex="-1" role="dialog" aria-labelledby="hapusModalLabel-<?= $value['kode_penerimaan'] ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="hapusModalLabel-<?= $value['kode_penerimaan'] ?>">Hapus Data <?= $value['kode_penerimaan']; ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus Penerimaan dengan kode penerimaan <strong><?= $value['kode_penerimaan']; ?></strong>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <a href="<?= base_url('Penerimaan/hapusdata/' . $value['kode_penerimaan']) ?>" class="btn btn-danger">Delete</a>
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
