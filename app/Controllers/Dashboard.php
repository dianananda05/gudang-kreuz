<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\ModelPenerimaan;
use App\Models\ModelPengadaan;
use App\Models\ModelPengeluaran;
use App\Models\ModelPermintaan;

class Dashboard extends BaseController
{
    public function admin()
    {
        $modelPermintaan = new ModelPermintaan();
        $modelPengeluaran = new ModelPengeluaran();
        $modelPengadaan = new ModelPengadaan();
        $modelPenerimaan = new ModelPenerimaan();
        if (!$this->isAdmin()) {
            return redirect()->to('/auth'); // Redirect jika bukan admin
        }

        $data = [
            'permintaanCount' => $modelPermintaan->countAllResults(),
            'pengeluaranCount' => $modelPengeluaran->countAllResults(),
            'pengadaanCount' => $modelPengadaan->countAllResults(),
            'penerimaanCount' => $modelPenerimaan->countAllResults(),
            'isAdmin' => true // Atau gunakan nilai sesuai kebutuhan
        ];
        // Konten untuk halaman dashboard admin
        return view('dashboard/admin', $data);
    }

    public function kepalagudang()
    {
        $modelPermintaan = new ModelPermintaan();
        $modelPengeluaran = new ModelPengeluaran();
        $modelPengadaan = new ModelPengadaan();
        $modelPenerimaan = new ModelPenerimaan();
        if (!$this->isKepalaGudang()) {
            return redirect()->to('/auth'); // Redirect jika bukan kepala gudang
        }

        $data = [
            'permintaanCount' => $modelPermintaan->countAllResults(),
            'pengeluaranCount' => $modelPengeluaran->countAllResults(),
            'pengadaanCount' => $modelPengadaan->countAllResults(),
            'penerimaanCount' => $modelPenerimaan->countAllResults(),
            'isKepalaGudang' => true // Atau gunakan nilai sesuai kebutuhan
        ];
        // Konten untuk halaman dashboard user
        return view('dashboard/kepalagudang', $data);
    }

    public function kepalapembelian()
    {
        $modelPengadaan = new ModelPengadaan();
        $modelPenerimaan = new ModelPenerimaan();

        $data = [
            'pengadaanCount' => $modelPengadaan->countAllResults(),
            'penerimaanCount' => $modelPenerimaan->countAllResults(),
        ];
        // Konten untuk halaman dashboard user
        return view('dashboard/kepalapembelian', $data);
    }

    public function kepalaproduksi()
    {
        $modelPermintaan = new ModelPermintaan();

        $data = [
            'permintaanCount' => $modelPermintaan->countAllResults(),
        ];
        // Konten untuk halaman dashboard user
        return view('dashboard/kepalaproduksi', $data);
    }

    protected function isAdmin()
    {
        $session = session();
        return $session->get('level') === 'admin';
    }

    protected function isKepalaGudang()
    {
        $session = session();
        return $session->get('level') === 'kepalagudang';
    }

}
