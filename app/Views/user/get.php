<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Data User &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="section">
    <div class="section-header">
        <h1>Data User</h1>
        <div class="section-header-button">
        <?php if ($isAdmin) : ?>
            <a href="<?= site_url('User/tambahdata') ?>" class="btn btn-primary">Tambah Data</a>
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
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari User...">
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th scope="col" width="50px">No</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Username</th>
                                <th scope="col">Password</th>
                                <th scope="col">Level</th>
                                <?php if ($isAdmin) : ?>
                                    <th scope="col">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($user as $value) : ?>
                            <tr class="text-center">
                                <td><?= $no++ ?></td>
                                <td><?= $value['nama'] ?></td>
                                <td><?= $value['username'] ?></td>
                                <td><?= $value['password'] ?></td>
                                <td><?= $value['level'] ?></td>
                                <td>
                                    <div class="d-inline-flex align-items-center">
                                        <?php if ($isAdmin) : ?>
                                        <a href="<?= site_url('user/edit/' . $value['id_user']) ?>" class="btn btn-warning btn-sm mr-1">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($isAdmin) : ?>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapusModal-<?= $value['id_user'] ?>">
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

<!-- Modal Hapus Data -->
<?php foreach ($user as $value) : ?>
        <div class="modal fade" id="hapusModal-<?= $value['id_user'] ?>" tabindex="-1" role="dialog" aria-labelledby="hapusModalLabel-<?= $value['id_user'] ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="hapusModalLabel-<?= $value['username'] ?>">Hapus Data User <?= $value['username']; ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus data user <strong><?= $value['username']; ?></strong>?
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a href="<?= base_url('User/hapusdata/' . $value['id_user']) ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
<?php endforeach; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<!-- Script untuk pencarian -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Script untuk pencarian -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const dataTable = document.getElementById('dataTable');
    const tableRows = dataTable.querySelectorAll('tbody tr');

    searchInput.addEventListener('keyup', function () {
        const filter = searchInput.value.toLowerCase();
        tableRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(filter)) {
                    match = true;
                }
            });
            row.style.display = match ? '' : 'none';
        });
    });
});
</script>


<?= $this->endSection() ?>

