<?php if (session()->get('level') == 'admin') { ?>
<li><a href="<?=site_url('Dashboard/admin')?>" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
<li><a href="<?=site_url('Barang')?>" class="nav-link"><i class="fa fa-cubes" aria-hidden="true"></i><span>Data Barang</span></a></li>
<li class="menu-header">Main Menu</li>
    <li class="nav-item dropdown">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa fa-columns"></i> <span>Transaksi Barang</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?=site_url('Penerimaan')?>">Penerimaan Barang</a></li>
                <li><a class="nav-link" href="<?=site_url('Pengadaan')?>">Pengadaan Barang</a></li>
                <li><a class="nav-link" href="<?=site_url('Permintaan')?>">Permintaan Barang</a></li>
                <li><a class="nav-link" href="<?=site_url('Pengeluaran')?>">Pengeluaran Barang</a></li>
                <li><a class="nav-link" href="<?=site_url('Penukaran')?>">Penukaran Barang</a></li>
            </ul>
    </li>
<?php } ?>

<?php if (session()->get('level') == 'kepalaproduksi') { ?>
<li><a href="<?=site_url('Dashboard/kepalaproduksi')?>" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
<li><a href="<?=site_url('Barang')?>" class="nav-link"><i class="fa fa-cubes" aria-hidden="true"></i><span>Data Barang</span></a></li>
<li class="menu-header">Main Menu</li>
    <li class="nav-item dropdown">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa fa-columns"></i> <span>Transaksi Barang</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?=site_url('Permintaan')?>">Permintaan Barang</a></li>
            </ul>
    </li>
<?php } ?>

<?php if (session()->get('level') == 'kepalapembelian') { ?>
<li><a href="<?=site_url('Dashboard/kepalapembelian')?>" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
<li><a href="<?=site_url('Barang')?>" class="nav-link"><i class="fa fa-cubes" aria-hidden="true"></i><span>Data Barang</span></a></li>
<li class="menu-header">Main Menu</li>
    <li class="nav-item dropdown">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa fa-columns"></i> <span>Transaksi Barang</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?=site_url('Penerimaan')?>">Penerimaan Barang</a></li>
                <li><a class="nav-link" href="<?=site_url('Pengadaan')?>">Pengadaan Barang</a></li>
            </ul>
    </li>
<?php } ?>

<?php if (session()->get('level') == 'kepalagudang') { ?>
<li><a href="<?=site_url('Dashboard/kepalagudang')?>" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
<li><a href="<?=site_url('Barang')?>" class="nav-link"><i class="fa fa-cubes" aria-hidden="true"></i><span>Data Barang</span></a></li>
<li class="menu-header">Main Menu</li>
    <li class="nav-item dropdown">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa fa-columns"></i> <span>Transaksi Barang</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="<?=site_url('Penerimaan')?>">Penerimaan Barang</a></li>
                <li><a class="nav-link" href="<?=site_url('Pengadaan')?>">Pengadaan Barang</a></li>
                <li><a class="nav-link" href="<?=site_url('Permintaan')?>">Permintaan Barang</a></li>
                <li><a class="nav-link" href="<?=site_url('Pengeluaran')?>">Pengeluaran Barang</a></li>
                <li><a class="nav-link" href="<?=site_url('Penukaran')?>">Penukaran Barang</a></li>
            </ul>
    </li>
<?php } ?>