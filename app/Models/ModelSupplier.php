<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelSupplier extends Model
{
    protected $table = 'supplier';
    protected $primarykey = 'kode_supplier';
    protected $allowedfields = ['nama_supplier', 'alamat_supplier'];

    public function AllData()
    {
        return $this->findAll();
    }

    public function Tambah($data)
    {
        $this->db->table('supplier')->insert($data);
    }
    
    public function ubahdata($data)
    {
        $this->db->table('supplier')->where('kode_supplier', $data['kode_supplier'])->update($data);
    }

    public function hapusdata($data)
    {
        $this->db->table('supplier')->where('kode_supplier', $data['kode_supplier'])->delete($data);
    }
}
