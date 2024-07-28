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
            ->orderBy('kode_penukaran', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function Tambah($data)
    {
        $this->db->table('penukaran')->insert($data);
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
                        ->join('barang', 'barang.kode_barang=detail_penukaran.kode_barang')
                        ->where('kode_penukaran', $kode_penukaran)
                        ->get()
                        ->getResultArray();
    }

    public function generateKodeTKR()
    {
        $prefix = 'TKR-' . date('Ymd') . '-';

        $kodeTerbesar = $this->db->table('penukaran')
            ->select('kode_penukaran')
            ->like('kode_penukaran', $prefix, 'after')
            ->orderBy('kode_penukaran', 'desc')
            ->get()
            ->getRowArray();

        if ($kodeTerbesar) {
            $urutan = (int) substr($kodeTerbesar['kode_penukaran'], -3);
        } else {
            $urutan = 0;
        }

        $urutan++;
        return $prefix . sprintf("%03s", $urutan);
    }
}
