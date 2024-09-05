<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Tambah User &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="<?=site_url('User/index')?>" class="btn primary"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Tambah Data User</h1>
        </div>

        <div class="section-body">
            <div class="card">
                  <div class="card-header">
                    <h4>Tambah Data User</h4>
                  </div>
                  <div class="card-body col-md-6">
                  <?php echo form_open('User/insertdata') ?>
                        <div class="form-group">
                            <label for="Nama">Nama *</label>
                            <input name="nama" class="form-control" placeholder="Nama" required>
                        </div>
                        <div class="form-group">
                            <label for="Username">Username *</label>
                            <input name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <label for="Password">Password *</label>
                            <input name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label for="Level">Level *</label>
                            <input name="level" class="form-control" placeholder="Level" required>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" onclick="window.history.back()">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                  </div>
                  <?php echo form_close() ?>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>
