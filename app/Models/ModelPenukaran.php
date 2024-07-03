<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPenukaran extends Model
{
    protected $table = 'penukaran';
    protected $primarykey = 'kode_penukaran';
    protected $allowedfields = ['kode_pengeluaran', 'tanggal_penukaran', 'timestamp', 'detail'];

    public function AllData()
    {
        return $this->db->table('penukaran')
            ->join('pengeluaran', 'pengeluaran.kode_pengeluaran=pengeluaran.kode_pengeluaran')
            ->orderBy('kode_penukaran', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function Tambah($data)
    {
        $this->db->table('penukaran')->insert($data);
    }

    public function ubahdata($data)
    {
        $this->db->table('penukaran')->where('kode_penukaran', $data['kode_penukaran'])->update($data);
    }

    public function hapusdata($data)
    {
        $this->db->table('penukaran')->where('kode_penukaran', $data['kode_penukaran'])->delete($data);
    }

    public function getLaporan($start_date, $end_date)
    {
        return $this->where('tanggal_penukaran >=', $start_date)
                    ->where('tanggal_penukaran <=', $end_date)
                    ->findAll();
    }

    public function filterByDate($start_date, $end_date)
    {
        return $this->where('tanggal_penukaran >=', $start_date)
                    ->where('tanggal_penukaran <=', $end_date)
                    ->findAll();
    }

    public function getDetailPenukaran($kode_penukaran)
    {
        return $this->db->table('detail_penukaran')
                        ->where('kode_penukaran', $kode_penukaran)
                        ->get()
                        ->getResultArray();
    }
}
