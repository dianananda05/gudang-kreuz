<?php 
namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Log\Logger;

class ModelPermintaan extends Model
{
    protected $table = 'permintaan';
    protected $primaryKey = 'kode_permintaan';
    protected $allowedFields = ['nama_pengaju', 'tanggal_permintaan', 'type_permintaan', 'timestamp', 'catatan'];
    
    public function AllData()
    {
        return $this->orderBy('kode_permintaan', 'ASC')
                    ->findAll();
    }

    public function generateKodePermintaan($type_permintaan)
    {
        // Tentukan awalan berdasarkan jenis permintaan
        $prefix = ($type_permintaan == 'PRODUKSI') ? 'PRO' : 'PO';
        log_message('debug', 'Prefix: ' . $prefix);

        // Ambil kode permintaan terakhir berdasarkan awalan
        $lastCode = $this->select('kode_permintaan')
                        ->like('kode_permintaan', $prefix . '%', 'after')
                        ->orderBy('kode_permintaan', 'DESC')
                        ->first();
        log_message('debug', 'Last Code: ' . print_r($lastCode, true));

        // Generate nomor urut baru
        if ($lastCode) {
            $lastNumber = substr($lastCode['kode_permintaan'], strlen($prefix));
            $newNumber = intval($lastNumber) + 1;
            log_message('debug', 'Last Number: ' . $lastNumber . ', New Number: ' . $newNumber);
        } else {
            $newNumber = 1;
            log_message('debug', 'No last code found, starting with new number: ' . $newNumber);
        }

        // Format kode permintaan dengan awalan dan nomor urut
        $newCode = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        log_message('debug', 'New Code: ' . $newCode);

        return $newCode;
    }

    public function Tambah($data)
    {
        log_message('debug', 'Data before adding kode_permintaan: ' . print_r($data, true));

        // Generate kode permintaan baru berdasarkan type_permintaan
        $data['kode_permintaan'] = $this->generateKodePermintaan($data['type_permintaan']);

        log_message('debug', 'Data after adding kode_permintaan: ' . print_r($data, true));

        // Insert data ke database
        $this->db->table('permintaan')->insert($data);

        log_message('debug', 'Data inserted to the database');
    }
    
    public function getNewPermintaan() {
        $this->where('status', NULL);  // Mengambil permintaan yang belum dikonfirmasi
        return $this->findAll();
    }
    
    public function updateStatus($kode_permintaan, $data)
    {
        return $this->db->table('permintaan')->where('kode_permintaan', $kode_permintaan)->update($data);
    }

    public function getPermintaan($kode_permintaan)
    {
        return $this->where('kode_permintaan', $kode_permintaan)->first();
    }

    public function ubahdata($data)
    {
        $this->db->table('permintaan')->where('kode_permintaan', $data['kode_permintaan'])->update($data);
    }

    public function hapusdata($kode_permintaan)
    {
        return $this->where('kode_permintaan', $kode_permintaan)->delete();
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
                        ->join('barang', 'barang.kode_barang=detail_permintaan.kode_barang')
                        ->where('kode_permintaan', $kode_permintaan)
                        ->get()
                        ->getResultArray();
    }

}
