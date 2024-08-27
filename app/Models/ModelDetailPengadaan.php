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

    public function updateDetail($kode_po, $kode_barang, $dataDetail)
    {
        $this->db->table($this->table)
                 ->where('kode_po', $kode_po)
                 ->where('kode_barang', $kode_barang)
                 ->update($dataDetail);
    }

    public function ubahdata($dataDetail)
    {
        $this->db->table('detail_pengadaan')->where('id_pengadaan', $dataDetail['id_pengadaan'])->update($dataDetail);
    }

    public function hapusdata($dataDetail)
    {
        $this->db->table('detail_pengadaan')->where('id_pengadaan', $dataDetail['id_pengadaan'])->delete($dataDetail);
    }

    public function getByKodePO($kode_po)
    {
        return $this->db->table($this->table)
                        ->join('barang', 'barang.kode_barang=detail_pengadaan.kode_barang')
                        ->where('detail_pengadaan.kode_po', $kode_po)
                        ->get()
                        ->getResultArray();
    }
    
    public function getJumlahBarangDipesanByKodePO($kode_po)
    {
        $query = $this->db->table('detail_pengadaan')
                          ->select('kode_barang, jumlah_barang')
                          ->where('kode_po', $kode_po)
                          ->get();

        // $result = $query->getRow();
        // return $result ? $result->jumlah_barang : 0;

        $result = $query->getResultArray();
    
        // Mengembalikan array dengan kode_barang sebagai kunci dan jumlah_barang sebagai nilai
        return array_column($result, 'jumlah_barang', 'kode_barang');
    }

}
