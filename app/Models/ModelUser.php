<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelUser extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';
    protected $allowedFields    = ['nama', 'username', 'password', 'level'];
    public function AllData()
    {
        return $this->findAll();
    }
}