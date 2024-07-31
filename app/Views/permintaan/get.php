<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Permintaan Barang &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <section class="section">
        <div class="section-header">
            <h1>Permintaan</h1>
            <div class="section-header-button">
            <?php if ($isAdmin | $isKepalaProduksi) : ?>
                <a href="<?= site_url('Permintaan/tambahdata') ?>" class="btn btn-primary">Buat Permintaan</a>
            <?php endif; ?>
            </div>
        </div>

        <div class="section-body">
            <div class="section">
                <div class="section-header">
                    <div class="row mb-4">
                    <?php if ($isAdmin) : ?>
                        <div class="col-md-12">
                            <h6>Cetak Laporan Permintaan Barang</h6>
                            <form action="<?= site_url('Permintaan/filter') ?>" method="get" class="form-inline">
                                <input type="date" name="start_date" class="form-control mb-2 mr-sm-2" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>" required>
                                <input type="date" name="end_date" class="form-control mb-2 mr-sm-2" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>" required>
                                <button type="submit" class="btn btn-primary mb-2">Filter</button>
                                <div class="d-flex justify-content-end">
                                <a href="<?= base_url('Permintaan/cetakLaporan?start_date=' . (isset($_GET['start_date']) ? $_GET['start_date'] : '') . '&end_date=' . (isset($_GET['end_date']) ? $_GET['end_date'] : '')) ?>" class="btn btn-primary mb-2 ml-2" target="_blank">Cetak PDF</a>
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
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari Permintaan...">
                    </div>

                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th scope="col" width=50px>No</th>
                                    <th scope="col">Kode Permintaan</th>
                                    <th scope="col">Nama Pengaju</th>
                                    <th scope="col">Tanggal Permintaan</th>
                                    <th scope="col">Type Permintaan</th>
                                    <th scope="col">Timestamp</th>
                                    <th scope="col">Detail</th>
                                    <th scope="col">Status</th>
                                    <?php if ($isAdmin || $isKepalaProduksi) : ?>
                                        <th scope="col">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($permintaan as $value) :
                                ?>
                                    <tr scope="row" class="text-center">
                                        <td><?= $no++ ?></td>
                                        <td><?= $value['kode_permintaan'] ?></td>
                                        <td><?= $value['nama_pengaju'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($value['tanggal_permintaan'])) ?></td>
                                        <td><?= $value['type_permintaan'] ?></td>
                                        <td><?= $value['timestamp'] ?></td>
                                        <td><button class="btn btn-info btn-sm btn-flat" data-toggle="modal" data-target="#detailModal<?= $value['kode_permintaan']; ?>"><i class="fas fa-info-circle"></i> Detail</button></td>
                                        <td>
                                            <?php if ($value['type_permintaan'] == 'PENGADAAN' && $isKepalaProduksi) : ?>
                                                <?php if (is_null($value['status'])) : ?>
                                                    <a href="<?= site_url('Permintaan/approve/' . $value['kode_permintaan']) ?>" class="btn btn-success btn-sm"><i class="fas fa-check"></i></a>
                                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal<?= $value['kode_permintaan'] ?>"><i class="fas fa-times"></i></button>
                                                <?php else : ?>
                                                    <?php if ($value['status'] == 1) : ?>
                                                        <span class="badge badge-success">Disetujui</span>
                                                    <?php elseif ($value['status'] == 0) : ?>
                                                        <span class="badge badge-danger" style="cursor: pointer;" data-toggle="modal" data-target="#alasanPenolakanModal<?= $value['kode_permintaan'] ?>">
                                                            Ditolak
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php elseif ($value['type_permintaan'] == 'PRODUKSI' && $isAdmin) : ?>
                                                <?php if (is_null($value['status'])) : ?>
                                                    <a href="<?= site_url('Permintaan/approve/' . $value['kode_permintaan']) ?>" class="btn btn-success btn-sm"><i class="fas fa-check"></i></a>
                                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal<?= $value['kode_permintaan'] ?>"><i class="fas fa-times"></i></button>
                                                <?php else : ?>
                                                    <?php if ($value['status'] == 1) : ?>
                                                        <span class="badge badge-success">Disetujui</span>
                                                    <?php elseif ($value['status'] == 0) : ?>
                                                        <span class="badge badge-danger" style="cursor: pointer;" data-toggle="modal" data-target="#alasanPenolakanModal<?= $value['kode_permintaan'] ?>">
                                                            Ditolak
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <?php if (is_null($value['status'])) : ?>
                                                    <span class="badge badge-warning">Menunggu Konfirmasi</span>
                                                <?php elseif ($value['status'] == 1) : ?>
                                                    <span class="badge badge-success">Disetujui</span>
                                                <?php elseif ($value['status'] == 0) : ?>
                                                    <span class="badge badge-danger" style="cursor: pointer;" data-toggle="modal" data-target="#alasanPenolakanModal<?= $value['kode_permintaan'] ?>">
                                                        Ditolak
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($isAdmin || $isKepalaProduksi) : ?>
                                        <td class="text-center" style="width:15%">
                                            <?php if ($value['status'] == 0) : ?>
                                                <?php if ($value['type_permintaan'] == 'PENGADAAN' && $isAdmin) : ?>
                                                    <a href="<?= site_url('permintaan/edit/' . $value['kode_permintaan']) ?>" class="btn btn-warning btn-sm mr-1">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                <?php elseif ($value['type_permintaan'] == 'PRODUKSI' && $isKepalaProduksi) : ?>
                                                    <a href="<?= site_url('permintaan/edit/' . $value['kode_permintaan']) ?>" class="btn btn-warning btn-sm mr-1">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusModal-<?= $value['kode_permintaan'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
        </div>
    </section>

    <?php foreach ($permintaan as $value) : ?>
    <!-- Modal Konfirmasi Penolakan -->
        <div class="modal fade" id="rejectModal<?= $value['kode_permintaan'] ?>" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel<?= $value['kode_permintaan'] ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel<?= $value['kode_permintaan'] ?>">Konfirmasi Penolakan Permintaan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menolak permintaan ini?</p>
                        <form id="rejectForm<?= $value['kode_permintaan'] ?>" action="<?= site_url('Permintaan/reject/' . $value['kode_permintaan']) ?>" method="post">
                            <div class="form-group">
                                <label for="catatan<?= $value['kode_permintaan'] ?>">Alasan Penolakan</label>
                                <textarea class="form-control" id="catatan<?= $value['kode_permintaan'] ?>" name="catatan" rows="3" required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" form="rejectForm<?= $value['kode_permintaan'] ?>" class="btn btn-danger">Tolak Permintaan</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach ($permintaan as $value) : ?>
    <!-- Modal Alasan Penolakan -->
        <div class="modal fade" id="alasanPenolakanModal<?= $value['kode_permintaan'] ?>" tabindex="-1" role="dialog" aria-labelledby="alasanPenolakanModalLabel<?= $value['kode_permintaan'] ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="alasanPenolakanModalLabel<?= $value['kode_permintaan'] ?>">Alasan Penolakan Permintaan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Kode Permintaan:</strong> <?= $value['kode_permintaan']; ?></p>
                        <p><strong>Alasan Penolakan:</strong></p>
                        <p><?= $value['catatan']; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach ($permintaan as $value) : ?>
    <!-- Modal -->
    <div class="modal fade" id="detailModal<?= $value['kode_permintaan']; ?>" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Permintaan Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Kode Permintaan: <?= $value['kode_permintaan']; ?></strong> <span id="detailKodePermintaan"></span></p>
                    <p><strong>Nama Pengaju: <?= $value['nama_pengaju']; ?></strong> <span id="detailNamaPengaju"></span></p>
                    <p><strong>Tanggal Permintaan: <?= date('d/m/Y', strtotime($value['tanggal_permintaan'])) ?></strong> <span id="detailTanggalPermintaan"></span></p>
                    <p><strong>Type Permintaan: <?= $value['type_permintaan']; ?></strong><span id="detailTypePermintaan"></span></p>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">Kode Barang</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Satuan</th>
                                    <th scope="col">Jumlah Yang Diminta</th>
                                    <th scope="col">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="detailBarangList">
                                <?php if (!empty($detail_permintaan[$value['kode_permintaan']])): ?>
                                    <?php foreach ($detail_permintaan[$value['kode_permintaan']] as $detail) : ?>
                                            <tr class="text-center">
                                                <td><?= $detail['kode_barang']; ?></td>
                                                <td><?= $detail['nama_barang']; ?></td>
                                                <td><?= $detail['satuan']; ?></td>
                                                <td><?= $detail['jumlah_yang_diminta']; ?></td>
                                                <td><?= $detail['keterangan']; ?></td>
                                            </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">Tidak ada data detail permintaan.</td>
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
    <?php foreach ($permintaan as $value) : ?>
        <div class="modal fade" id="hapusModal-<?= $value['kode_permintaan'] ?>" tabindex="-1" role="dialog" aria-labelledby="hapusModalLabel-<?= $value['kode_permintaan'] ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="hapusModalLabel-<?= $value['kode_permintaan'] ?>">Hapus Data <?= $value['kode_permintaan']; ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus Permintaan dengan kode permintaan <strong><?= $value['kode_permintaan']; ?></strong>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <a href="<?= base_url('Permintaan/hapusdata/' . $value['kode_permintaan']) ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

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

    document.addEventListener('DOMContentLoaded', function() {
        function fetchNewPermintaan() {
            fetch('<?= site_url('Permintaan/getNewPermintaan') ?>')
                .then(response => response.json())
                .then(data => {
                    let inboxList = document.getElementById('inboxList');
                    inboxList.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(permintaan => {
                            let newItem = document.createElement('a');
                            newItem.href = '#';
                            newItem.className = 'dropdown-item dropdown-item-unread';
                            newItem.innerHTML = `
                                <div class="dropdown-item-avatar">
                                    <img alt="image" src="<?=base_url()?>/template/assets/img/avatar/avatar-1.png" class="rounded-circle">
                                    <div class="is-online"></div>
                                </div>
                                <div class="dropdown-item-desc">
                                    <b>${permintaan.nama_pengaju}</b>
                                    <p>${permintaan.kode_permintaan}</p>
                                    <div class="time">${new Date(permintaan.tanggal_permintaan).toLocaleString()}</div>
                                </div>`;
                            inboxList.appendChild(newItem);
                        });
                    } else {
                        let noItem = document.createElement('p');
                        noItem.className = 'text-center';
                        noItem.innerText = 'Tidak ada permintaan baru.';
                        inboxList.appendChild(noItem);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        setInterval(fetchNewPermintaan, 60000); // Memeriksa permintaan baru setiap 60 detik
        fetchNewPermintaan(); // Memeriksa permintaan baru saat halaman dimuat
    });
</script>
<?= $this->endSection() ?>
