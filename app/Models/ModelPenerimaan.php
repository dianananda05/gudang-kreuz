<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPenerimaan extends Model
{
    protected $table = 'penerimaan';
    protected $primarykey = 'kode_penerimaan';
    protected $allowedfields = ['kode_po', 'tanggal_penerimaan', 'nomor_po', 'timestamp', 'status'];
    
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

    // public function hapusdata($data)
    // {
    //     $this->db->table('penerimaan')->where('kode_penerimaan', $data['kode_penerimaan'])->delete($data);
    // }

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
                        ->join('barang', 'barang.kode_barang=detail_penerimaan.kode_barang')
                        ->where('kode_penerimaan', $kode_penerimaan)
                        ->get()
                        ->getResultArray();
    }

    public function findByKodeTRM($kode_penerimaan)
    {
        return $this->where('kode_penerimaan', $kode_penerimaan)
                    ->first();
    }

    public function updateStatusSelesai($kode_penerimaan)
    {
        return $this->db->table($this->table)
                        ->where('kode_penerimaan', $kode_penerimaan)
                        ->update(['status' => '1']);
    }

    public function generateKodeTRM()
    {
        $prefix = 'TRM-' . date('Ymd') . '-';

        // Dapatkan kode PO terbesar berdasarkan prefix hari ini
        $kodeTerbesar = $this->db->table('penerimaan')
            ->select('kode_penerimaan')
            ->like('kode_penerimaan', $prefix, 'after')
            ->orderBy('kode_penerimaan', 'desc')
            ->get()
            ->getRowArray();

        if ($kodeTerbesar) {
            $urutan = (int) substr($kodeTerbesar['kode_penerimaan'], -3);
        } else {
            $urutan = 0;
        }

        // Tambahkan 1 untuk kode PO baru
        $urutan++;
        return $prefix . sprintf("%03s", $urutan);
    }
}
