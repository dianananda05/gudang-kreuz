<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelDetailPenukaran extends Model
{
    protected $table = 'detail_penukaran';
    protected $primarykey = 'id_penukaran';
    protected $allowedfields = ['kode_penukaran', 'kode_barang', 'jumlah_penukaran', 'alasan_penukaran'];
    
    public function AllData($kode_penukaran = null)
    {
        $builder = $this->db->table('detail_penukaran')
            ->join('barang', 'barang.kode_barang=detail_penukaran.kode_barang')
            ->orderBy('detail_penukaran.kode_barang', 'DESC');

        if (!empty($kode_penukaran)) {
            $builder->where('detail_penukaran.kode_penukaran', $kode_penukaran);
        }

        return $builder
            ->get()
            ->getResultArray();
    }

    public function getRepairCount($kode_barang)
    {
        return $this->db->table('detail_penukaran')
            ->selectSum('jumlah_penukaran', 'total_repair')
            ->where('kode_barang', $kode_barang)
            ->like('alasan_penukaran', 'repair', 'both')
            ->get()
            ->getRowArray();
    }

    public function getRejectCount($kode_barang)
    {
        return $this->db->table('detail_penukaran')
            ->selectSum('jumlah_penukaran', 'total_reject')
            ->where('kode_barang', $kode_barang)
            ->like('alasan_penukaran', 'reject', 'both')
            ->get()
            ->getRowArray();
    }

    public function Tambah($dataDetail)
    {
        $this->db->table('detail_penukaran')->insert($dataDetail);
    }

    public function ubahdata($dataDetail)
    {
        $this->db->table('detail_penukaran')->where('kode_penukaran', $dataDetail['kode_penukaran'])->update($dataDetail);
    }

    public function hapusdata($dataDetail)
    {
        $this->db->table('detail_penukaran')->where('id_penukaran', $dataDetail['id_penukaran'])->delete($dataDetail);
    }

}
