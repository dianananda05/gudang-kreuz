<!-- barang/edit.php -->
<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Edit Barang &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <section class="section">
        <div class="section-header">
            <div class="section-header-back">
                <a href="<?= site_url('Barang/index') ?>" class="btn primary"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>Edit Data</h1>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Data Barang</h4>
                </div>
                <div class="card-body col-md-6">
                    <!-- Menggunakan JSON data untuk mengisi input -->
                    <?php $barang = json_decode($barang, true); // Konversi JSON ke array ?>
                    <!-- Form edit barang -->
                    <form method="post" action="<?= site_url('Barang/ubahdata/' . $barang['kode_barang']) ?>">
                        <div class="form-group">
                            <label for="kode_barang">Kode Barang *</label>
                            <input id="kode_barang" name="kode_barang" value="<?= $barang['kode_barang']; ?>" class="form-control" placeholder="Kode Barang" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="nama_barang">Nama Barang *</label>
                            <input id="nama_barang" name="nama_barang" value="<?= $barang['nama_barang']; ?>" class="form-control" placeholder="Nama Barang" required>
                        </div>
                        <div class="form-group">
                            <label for="satuan">Satuan *</label>
                            <input id="satuan" name="satuan" value="<?= $barang['satuan']; ?>" class="form-control" placeholder="Satuan" required>
                        </div>
                        <div class="form-group">
                            <label for="stok">Stok *</label>
                            <input id="stok" type="number" value="<?= $barang['stok']; ?>" name="stok" class="form-control" placeholder="Stok" required>
                        </div>
                        <?php if ($isKepalaPembelian) : ?>
                        <div class="form-group">
                            <label for="harga_satuan">Harga Satuan *</label>
                            <input id="harga_satuan" name="harga_satuan" value="<?= $barang['harga_satuan']; ?>" class="form-control" placeholder="Harga Satuan" required>
                        </div>
                        <?php endif; ?>
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
