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

    public function hapusdata($data)
    {
        $this->db->table('pengeluaran')->where('kode_pengeluaran', $data['kode_pengeluaran'])->delete($data);
    }

    public function cekPenggunaanData($kode_permintaan)
    {
        $builder = $this->db->table('pengeluaran');
        $builder->where('kode_permintaan', $kode_permintaan);
        $builder->countAllResults(); // Menghitung jumlah penggunaan data

        return ($builder->countAllResults() > 0); // Mengembalikan true jika data masih digunakan
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
                        ->join('barang', 'barang.kode_barang=detail_pengeluaran.kode_barang')
                        ->where('kode_pengeluaran', $kode_pengeluaran)
                        ->get()
                        ->getResultArray();
    }

    public function generateKodePRO()
    {
        $prefix = 'PRO-' . date('Ymd') . '-';

        $kodeTerbesar = $this->db->table('pengeluaran')
            ->select('kode_pengeluaran')
            ->like('kode_pengeluaran', $prefix, 'after')
            ->orderBy('kode_pengeluaran', 'desc')
            ->get()
            ->getRowArray();

        if ($kodeTerbesar) {
            $urutan = (int) substr($kodeTerbesar['kode_pengeluaran'], -3);
        } else {
            $urutan = 0;
        }

        $urutan++;
        return $prefix . sprintf("%03s", $urutan);
    }
}
