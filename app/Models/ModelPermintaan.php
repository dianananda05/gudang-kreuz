<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPermintaan extends Model
{
    protected $table = 'permintaan';
    protected $primaryKey = 'kode_permintaan';
    protected $allowedFields = ['nama_pengaju', 'tanggal_permintaan', 'type_permintaan', 'timestamp', 'detail'];
    
    public function AllData()
    {
        return $this->findAll();
    }

    public function Tambah($data)
    {
        $this->db->table('permintaan')->insert($data);
    }

    public function ubahdata($data)
    {
        $this->db->table('permintaan')->where('kode_permintaan', $data['kode_permintaan'])->update($data);
    }
    
    public function getNewPermintaan() {
        $this->where('status', NULL);  // Mengambil permintaan yang belum dikonfirmasi
        return $this->findAll();
    }
    
    public function updateStatus($kode_permintaan, $data)
    {
        return $this->db->table('permintaan')->where('kode_permintaan', $kode_permintaan)->update($data);
    }

    public function hapusdata($data)
    {
        $this->db->table('permintaan')->where('kode_permintaan', $data['kode_permintaan'])->delete($data);
    }

    public function getLaporan($start_date, $end_date)
    {
        return $this->where('tanggal_permintaan >=', $start_date)
                    ->where('tanggal_permintaan <=', $end_date)
                    ->findAll();
    }

    public function filterByDate($start_date, $end_date)
    {
        return $this->where('tanggal_permintaan >=', $start_date)
                    ->where('tanggal_permintaan <=', $end_date)
                    ->findAll();
    }

    public function getDetailPermintaan($kode_permintaan)
    {
        return $this->db->table('detail_permintaan')
                        ->where('kode_permintaan', $kode_permintaan)
                        ->get()
                        ->getResultArray();
    }

}
