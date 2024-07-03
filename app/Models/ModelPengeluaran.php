<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPengeluaran extends Model
{
    protected $table = 'pengeluaran';
    protected $primarykey = 'kode_pengeluaran';
    protected $allowedfields = ['kode_permintaan', 'tanggal_pengeluaran', 'timestamp', 'detail'];

    public function AllData()
    {
        return $this->db->table('pengeluaran')
            ->join('permintaan', 'permintaan.kode_permintaan=permintaan.kode_permintaan')
            ->orderBy('kode_pengeluaran', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function Tambah($data)
    {
        $this->db->table('pengeluaran')->insert($data);
    }

    public function ubahdata($data)
    {
        $this->db->table('pengeluaran')->where('kode_pengeluaran', $data['kode_pengeluaran'])->update($data);
    }

    public function hapusdata($data)
    {
        $this->db->table('pengeluaran')->where('kode_pengeluaran', $data['kode_pengeluaran'])->delete($data);
    }

    public function getLaporan($start_date, $end_date)
    {
        return $this->where('tanggal_pengeluaran >=', $start_date)
                    ->where('tanggal_pengeluaran <=', $end_date)
                    ->findAll();
    }

    public function filterByDate($start_date, $end_date)
    {
        return $this->where('tanggal_pengeluaran >=', $start_date)
                    ->where('tanggal_pengeluaran <=', $end_date)
                    ->findAll();
    }

    public function getDetailPengeluaran($kode_pengeluaran)
    {
        return $this->db->table('detail_pengeluaran')
                        ->where('kode_pengeluaran', $kode_pengeluaran)
                        ->get()
                        ->getResultArray();
    }
}
