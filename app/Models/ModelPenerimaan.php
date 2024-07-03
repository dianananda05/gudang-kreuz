<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPenerimaan extends Model
{
    protected $table = 'penerimaan';
    protected $primarykey = 'kode_penerimaan';
    protected $allowedfields = ['kode_po', 'tanggal_penerimaan', 'nomor_po', 'timestamp', 'detail'];
    
    public function AllData()
    {
        return $this->db->table('penerimaan')
            ->join('pengadaan', 'pengadaan.kode_po=pengadaan.kode_po')
            ->orderBy('kode_penerimaan', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function Tambah($data)
    {
        $this->db->table('penerimaan')->insert($data);
    }

    public function ubahdata($data)
    {
        $this->db->table('penerimaan')->where('kode_penerimaan', $data['kode_penerimaan'])->update($data);
    }

    public function hapusdata($data)
    {
        $this->db->table('penerimaan')->where('kode_penerimaan', $data['kode_penerimaan'])->delete($data);
    }

    public function getLaporan($start_date, $end_date)
    {
        return $this->where('tanggal_penerimaan >=', $start_date)
                    ->where('tanggal_penerimaan <=', $end_date)
                    ->findAll();
    }

    public function filterByDate($start_date, $end_date)
    {
        return $this->where('tanggal_penerimaan >=', $start_date)
                    ->where('tanggal_penerimaan <=', $end_date)
                    ->findAll();
    }

    public function getDetailPenerimaan($kode_penerimaan)
    {
        return $this->db->table('detail_penerimaan')
                        ->where('kode_penerimaan', $kode_penerimaan)
                        ->get()
                        ->getResultArray();
    }
}
