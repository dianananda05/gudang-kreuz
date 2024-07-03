<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelBarang extends Model
{
    protected $table = 'barang';
    protected $primarykey = 'kode_barang';
    protected $allowedfields = ['kode_barang', 'nama_barang', 'satuan', 'stok', 'kategori_barang', 'harga_satuan'];

    public function AllData()
    {
        return $this->findAll();
    }

    public function Tambah($data)
    {
        $this->db->table('barang')->insert($data);
    }

    public function getDataByKodeBarang($kode_barang)
    {
        return $this->where('kode_barang', $kode_barang)->first();
    }
    
    public function ubahdata($data)
    {
        $this->db->table('barang')->where('kode_barang', $data['kode_barang'])->update($data);
    }

    public function hapusdata($data)
    {
        $this->db->table('barang')->where('kode_barang', $data['kode_barang'])->delete($data);
    }

    // public function getStokBarang($kode_barang)
    // {
    //     $barang = $this->find($kode_barang);

    //     $modelDetailPenerimaan = new ModelDetailPenerimaan();
    //     $modelDetailPengeluaran = new ModelDetailPengeluaran();

    //     // Hitung total barang masuk
    //     $barang_masuk = $modelDetailPenerimaan->where('kode_barang', $kode_barang)->findAll();
    //     $total_masuk = array_sum(array_column($barang_masuk, 'jumlah_yang_diterima'));

    //     // Hitung total barang keluar
    //     $barang_keluar = $modelDetailPengeluaran->where('kode_barang', $kode_barang)->findAll();
    //     $total_keluar = array_sum(array_column($barang_keluar, 'jumlah_yang_diserahkan'));

    //     // Hitung stok awal dan stok akhir
    //     $stok_awal = $barang['stok'];
    //     $stok_akhir = $stok_awal + $total_masuk - $total_keluar;

    //     // Siapkan data untuk laporan
    //     $data = [
    //         'kode_barang' => $barang['kode_barang'],
    //         'nama_barang' => $barang['nama_barang'],
    //         'satuan' => $barang['satuan'],
    //         'stok_awal' => $stok_awal,
    //         'stok_masuk' => $total_masuk,
    //         'stok_keluar' => $total_keluar,
    //         'stok_akhir' => $stok_akhir,
    //         'kategori_barang' => $barang['kategori_barang'],
    //     ];

    //     return $data;
    // }
}
