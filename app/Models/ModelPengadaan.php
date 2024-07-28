<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPengadaan extends Model
{
    protected $table = 'pengadaan';
    protected $primarykey = 'kode_po';
    protected $allowedfields = ['kode_permintaan', 'nama_supplier', 'tanggal_pengadaan', 'timestamp', 'catatan'];

    public function AllData()
    {
        return $this->db->table('pengadaan')
            ->join('permintaan', 'permintaan.kode_permintaan=permintaan.kode_permintaan')
            ->where('permintaan.type_permintaan','PENGADAAN')
            ->orderBy('kode_po', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    public function Tambah($data)
    {
        $this->db->table('pengadaan')->insert($data);
    }

    public function ubahdata($kode_po, $data)
    {
        $this->db->table('pengadaan')->where('kode_po', $kode_po)->update($data);
    }

    public function updateStatus($kode_po, $data)
    {
        return $this->db->table('pengadaan')->where('kode_po', $kode_po)->update($data);
    }

    public function getPengadaan($kode_po)
    {
        return $this->where('kode_po', $kode_po)->first();
    }

    public function hapusdata($kode_po)
    {
        $this->db->table('pengadaan')->where('kode_po', $kode_po)->delete();
    }

    public function cekPenggunaanData($kode_permintaan)
    {
        $builder = $this->db->table('pengadaan');
        $builder->where('kode_permintaan', $kode_permintaan);
        $builder->countAllResults(); // Menghitung jumlah penggunaan data

        return ($builder->countAllResults() > 0); // Mengembalikan true jika data masih digunakan
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

    public function findSupplier($kode_po)
    {
        return $this->db->table($this->table)
                        ->select('nama_supplier')
                        ->where('kode_po', $kode_po)
                        ->get()
                        ->getRowArray();
    }

    public function generateKodePO()
    {
        $prefix = 'PO-' . date('Ymd') . '-';

        // Dapatkan kode PO terbesar berdasarkan prefix hari ini
        $kodeTerbesar = $this->db->table('pengadaan')
            ->select('kode_po')
            ->like('kode_po', $prefix, 'after')
            ->orderBy('kode_po', 'desc')
            ->get()
            ->getRowArray();

        if ($kodeTerbesar) {
            $urutan = (int) substr($kodeTerbesar['kode_po'], -3);
        } else {
            $urutan = 0;
        }

        // Tambahkan 1 untuk kode PO baru
        $urutan++;
        return $prefix . sprintf("%03s", $urutan);
    }
}
