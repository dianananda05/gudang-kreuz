<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelDetailPenerimaan extends Model
{
    protected $table = 'detail_penerimaan';
    protected $primarykey = 'id_penerimaan';
    protected $allowedfields = ['kode_penerimaan', 'kode_barang', 'jumlah_yang_diterima', 'kondisi_barang'];
    
    public function AllData($kode_penerimaan = null)
    {
        $builder = $this->db->table('detail_penerimaan')
            ->join('barang', 'barang.kode_barang=detail_penerimaan.kode_barang')
            ->orderBy('detail_penerimaan.kode_barang', 'DESC');

        if (!empty($kode_penerimaan)) {
            $builder->where('detail_penerimaan.kode_penerimaan', $kode_penerimaan);
        }

        return $builder
            ->get()
            ->getResultArray();
    }

    public function Tambah($dataDetail)
    {
        $this->db->table('detail_penerimaan')->insert($dataDetail);
        $this->updateStokBarang($dataDetail['kode_barang'], $dataDetail['jumlah_yang_diterima']);
    }

    private function updateStokBarang($kode_barang, $jumlah_yang_diterima)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('barang');
        $builder->set('stok', 'stok + ' . $jumlah_yang_diterima, false);
        $builder->where('kode_barang', $kode_barang);
        $builder->update();
    }

    public function ubahdata($dataDetail)
    {
        $this->db->table('detail_penerimaan')->where('kode_penerimaan', $dataDetail['kode_penerimaan'])->update($dataDetail);
    }

    public function hapusdata($dataDetail)
    {
        $this->db->table('detail_penerimaan')->where('id_penerimaan', $dataDetail['id_penerimaan'])->delete($dataDetail);
    }
}
