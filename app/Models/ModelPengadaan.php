<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPengadaan extends Model
{
    protected $table = 'pengadaan';
    protected $primarykey = 'kode_po';
    protected $allowedfields = ['kode_permintaan', 'nama_supplier', 'tanggal_pengadaan', 'timestamp', 'detail'];

    public function AllData()
    {
        return $this->db->table('pengadaan')
            ->join('permintaan', 'permintaan.kode_permintaan=permintaan.kode_permintaan')
            // ->join('supplier', 'supplier.kode_supplier=supplier.kode_supplier')
            ->where('permintaan.type_permintaan','PENGADAAN')
            ->orderBy('kode_po', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    public function Tambah($data)
    {
        $this->db->table('pengadaan')->insert($data);
    }

    public function ubahdata($data)
    {
        $this->db->table('pengadaan')->where('kode_po', $data['kode_po'])->update($data);
    }

    public function updateStatus($kode_po, $data)
    {
        return $this->db->table('pengadaan')->where('kode_po', $kode_po)->update($data);
    }

    public function hapusdata($data)
    {
        $this->db->table('pengadaan')->where('kode_po', $data['kode_po'])->delete($data);
    }

    public function getLaporan($start_date, $end_date)
    {
        return $this->where('tanggal_pengadaan >=', $start_date)
                    ->where('tanggal_pengadaan <=', $end_date)
                    ->findAll();
    }

    public function filterByDate($start_date, $end_date)
    {
        return $this->where('tanggal_pengadaan >=', $start_date)
                    ->where('tanggal_pengadaan <=', $end_date)
                    ->findAll();
    }

    public function getDetailPengadaan($kode_po)
    {
        return $this->db->table('detail_pengadaan')
                        ->join('barang', 'barang.kode_barang=detail_pengadaan.kode_barang')
                        ->where('kode_po', $kode_po)
                        ->get()
                        ->getResultArray();
    }

    public function findByKodePO($kode_po)
    {
        return $this->where('kode_po', $kode_po)
                    ->first();
    }
}
