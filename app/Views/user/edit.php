<!-- barang/edit.php -->
<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Edit Data User &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="<?= site_url('User/index') ?>" class="btn primary"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Edit Data</h1>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Data User</h4>
                </div>
                <div class="card-body col-md-6">
                    <!-- Form edit user -->
                    <form method="post" action="<?= site_url('User/ubahdata/' . $user['id_user']) ?>">
                        <div class="form-group">
                            <label for="nama">Nama *</label>
                            <input id="nama" name="nama" value="<?= $user['nama']; ?>" class="form-control" placeholder="Nama" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input id="username" name="username" value="<?= $user['username']; ?>" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input id="password" name="password" value="<?= $user['password']; ?>" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label for="level">Level *</label>
                            <input id="Level" name="level" value="<?= $user['level']; ?>" class="form-control" placeholder="User" required>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" onclick="window.history.back()">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>
