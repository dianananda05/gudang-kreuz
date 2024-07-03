<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Tambah Barang &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="<?=site_url('Barang/index')?>" class="btn primary"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Tambah Data</h1>
        </div>

        <div class="section-body">
            <div class="card">
                  <div class="card-header">
                    <h4>Tambah Data Barang</h4>
                  </div>
                  <div class="card-body col-md-6">
                  <?php echo form_open('Barang/insertdata') ?>
                        <div class="form-group">
                            <label for="Kode Barang">Kode Barang *</label>
                            <input name="kode_barang" class="form-control" placeholder="Kode Barang" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="Nama Barang">Nama Barang *</label>
                            <input name="nama_barang" class="form-control" placeholder="Nama Barang" required>
                        </div>
                        <div class="form-group">
                            <label for="Satuan">Satuan *</label>
                            <input name="satuan" class="form-control" placeholder="Satuan" required>
                        </div>
                        <div class="form-group">
                            <label for="Stok">Stok *</label>
                            <input type="number" name="stok" class="form-control" placeholder="Stok" required>
                        </div>
                        <div class="form-group">
                            <label for="Kategori Barang">Kategori Barang *</label>
                            <input name="kategori_barang" class="form-control" placeholder="Kategori Barang" required>
                        </div>
                        <div class="form-group">
                            <label for="Harga Satuan">Harga Satuan *</label>
                            <input name="harga_satuan" class="form-control" placeholder="Harga Satuan" required>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                  </div>
                  <?php echo form_close() ?>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>
