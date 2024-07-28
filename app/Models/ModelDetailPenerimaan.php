<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelDetailPenerimaan extends Model
{
    protected $table = 'detail_penerimaan';
    protected $primarykey = 'id_penerimaan';
    protected $allowedfields = ['kode_penerimaan', 'kode_barang', 'jumlah_yang_diterima', 'kondisi_barang', 'status'];
    
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
    }

    public function updateDetail($kode_penerimaan, $kode_barang, $dataDetail)
    {
        $this->db->table($this->table)
                 ->where('kode_penerimaan', $kode_penerimaan)
                 ->where('kode_barang', $kode_barang)
                 ->update($dataDetail);
    }

    public function ubahdata($dataDetail)
    {
        $this->db->table('detail_penerimaan')->where('id_penerimaan', $dataDetail['id_penerimaan'])->update($dataDetail);
    }

    public function hapusdata($dataDetail)
    {
        $this->db->table('detail_penerimaan')->where('id_penerimaan', $dataDetail['id_penerimaan'])->delete($dataDetail);
    }
}
