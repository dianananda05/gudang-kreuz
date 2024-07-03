<?php

use CodeIgniter\Router\RouteCollection;


/*
 *Routes Setup
 *
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(true);

/**
 * @var RouteCollection $routes
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
// $routes->get('/', 'Home::index');

$routes->addRedirect('/', 'home');
$routes->get('barang/barangMasuk', 'Barang::barangMasuk');
$routes->get('barang/barangKeluar', 'Barang::barangKeluar');

$routes->get('barang', 'Barang::index');
$routes->get('barang/tambah', 'Barang::tambahdata');
$routes->POST('barang/get', 'Barang::insertdata');
$routes->get('barang/edit/(:any)', 'Barang::edit/$1');
$routes->POST('barang/get/(:any)', 'Barang::ubahdata/$1');
$routes->get('barang/hapus', 'Barang::hapusdata');
$routes->get('Barang/cetakLaporan/(:segment)', 'Barang::cetakLaporan/$1');
$routes->get('barang/qr_code/(:segment)', 'Barang::qr_code/$1');
$routes->get('barang/qrcode_print/(:any)', 'Barang::qrCodePrint/$1');
$routes->post('barang/scan', 'Barang::scan');



$routes->get('permintaan', 'Permintaan::index');
$routes->get('permintaan/tambah', 'Permintaan::tambahdata');
$routes->POST('permintaan/get', 'Permintaan::insertdata');
$routes->get('permintaan/hapus', 'Permintaan::hapusdata');
$routes->get('Permintaan/laporan', 'Permintaan::laporan');
$routes->get('Permintaan/cetakLaporan', 'Permintaan::cetakLaporan');


$routes->get('pengadaan', 'Pengadaan::index');
$routes->get('pengadaan/tambah', 'Pengadaan::tambahdata');
$routes->POST('pengadaan/get', 'Pengadaan::insertdata');
$routes->get('pengadaan/edit/(:any)', 'Pengadaan::edit/$1');
$routes->get('pengadaan/get/(:any)', 'Pengadaan::ubahdata/$1');
$routes->get('Pengadaan/cetakLaporan/(:segment)', 'Pengadaan::cetakLaporan/$1');

$routes->get('penerimaan', 'Penerimaan::index');
$routes->get('penerimaan/tambah', 'Penerimaan::tambahdata');
$routes->POST('penerimaan/get', 'Penerimaan::insertdata');
$routes->get('Penerimaan/cetakLaporan/(:segment)', 'Penerimaan::cetakLaporan/$1');

$routes->get('pengeluaran', 'Pengeluaran::index');
$routes->get('pengeluaran/tambah', 'Pengeluaran::tambahdata');
$routes->POST('pengeluaran/get', 'Pengeluaran::insertdata');
$routes->get('Pengeluaran/cetakLaporan/(:segment)', 'Pengeluaran::cetakLaporan/$1');

$routes->get('penukaran', 'Penukaran::index');
$routes->get('penukaran/tambah', 'Penukaran::tambahdata');
$routes->POST('penukaran/get', 'Penukaran::insertdata');
$routes->get('Pengeluaran/cetakLaporan/(:segment)', 'Pengeluaran::cetakLaporan/$1');

$routes->get('login', 'Login::index');
$routes->get('/auth', 'Login::index');
$routes->post('/auth/login', 'Login::login');
$routes->get('/auth/logout', 'Login::logout');
$routes->get('/dashboard/admin', 'Dashboard::admin', ['filter' => 'auth']);
$routes->get('/dashboard/kepalagudang', 'Dashboard::kepalagudang', ['filter' => 'auth']);
$routes->get('/dashboard/kepalapembelian', 'Dashboard::kepalapembelian', ['filter' => 'auth']);
$routes->get('/dashboard/kepalaproduksi', 'Dashboard::kepalaproduksi', ['filter' => 'auth']);
