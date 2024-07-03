<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelDetailPermintaan extends Model
{
    protected $table = 'detail_permintaan';
    protected $primaryKey = 'id_permintaan';
    protected $allowedFields = ['kode_permintaan', 'kode_barang', 'jumlah_yang_diminta', 'keterangan'];
    
    public function AllData($kode_permintaan = null)
    {
        $builder = $this->db->table('detail_permintaan')
            ->join('barang', 'barang.kode_barang=detail_permintaan.kode_barang')
            ->orderBy('detail_permintaan.kode_barang', 'DESC');

        if (!empty($kode_permintaan)) {
            $builder->where('detail_permintaan.kode_permintaan', $kode_permintaan);
        }

        return $builder
            ->get()
            ->getResultArray();
    }

    public function Tambah($dataDetail)
    {
        $this->db->table('detail_permintaan')->insert($dataDetail);
    }

    public function ubahdata($dataDetail)
    {
        $this->db->table('detail_permintaan')->where('kode_permintaan', $dataDetail['kode_permintaan'])->update($dataDetail);
    }

    public function hapusdata($kode_permintaan)
    {
        return $this->where('kode_permintaan', $kode_permintaan)->delete();
    }
    

}
