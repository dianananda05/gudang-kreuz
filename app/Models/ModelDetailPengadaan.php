<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelDetailPengadaan extends Model
{
    protected $table = 'detail_pengadaan';
    protected $primarykey = 'id_pengadaan';
    protected $allowedfields = ['kode_po', 'kode_barang', 'jumlah_barang'];
    
    public function AllData($kode_po = null)
    {
        $builder = $this->db->table('detail_pengadaan')
            ->join('barang', 'barang.kode_barang=detail_pengadaan.kode_barang')
            ->orderBy('detail_pengadaan.kode_barang', 'DESC');

        if (!empty($kode_po)) {
            $builder->where('detail_pengadaan.kode_po', $kode_po);
        }

        return $builder
            ->get()
            ->getResultArray();
    }

    public function Tambah($dataDetail)
    {
        $this->db->table('detail_pengadaan')->insert($dataDetail);
    }

    public function ubahdata($dataDetail)
    {
        $this->db->table('detail_pengadaan')->where('kode_po', $dataDetail['kode_po'])->update($dataDetail);
    }

    public function hapusdata($dataDetail)
    {
        $this->db->table('detail_pengadaan')->where('id_pengadaan', $dataDetail['id_pengadaan'])->delete($dataDetail);
    }
}
