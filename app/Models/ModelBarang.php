<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelBarang extends Model
{
    protected $table = 'barang';
    protected $primarykey = 'kode_barang';
    protected $allowedfields = ['kode_barang', 'nama_barang', 'satuan', 'stok', 'harga_satuan'];

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

    public function getMaxKodeBarangByPrefix($prefix)
    {
        return $this->select('kode_barang')
                    ->like('kode_barang', $prefix, 'after')
                    ->orderBy('kode_barang', 'desc')
                    ->first()['kode_barang'] ?? null;
    }

}
