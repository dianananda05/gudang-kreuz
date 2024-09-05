<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelUser extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';
    protected $allowedFields    = ['id_user', 'nama', 'username', 'password', 'level'];
    
    public function AllData()
    {
        return $this->findAll();
    }

    public function getUserById($id_user)
    {
        return $this->where('id_user', $id_user)->first(); // Sesuaikan dengan query yang sesuai
    }

    public function Tambah($data)
    {
        $this->db->table('user')->insert($data);
    }
    
    public function ubahdata($id_user, $data)
    {
        $this->db->table('user')->where('id_user', $id_user)->set($data)->update();
    }

    public function hapusdata($data)
    {
        $this->db->table('user')->where('id_user', $data['id_user'])->delete($data);
    }

}