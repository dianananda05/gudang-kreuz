<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelUser;
use CodeIgniter\API\ResponseTrait;

class User extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->ModelUser = new ModelUser();
    }

    protected function isAdmin()
    {
        return session()->get('level') === 'admin';
    }

    public function index()
    {
        $isAdmin = $this->isAdmin();
        $user = $this->ModelUser->findAll();
        $data = [
            'judul' => 'Master Data',
            'subjudul' => 'User',
            'menu' => 'user',
            'submenu' => '',
            'page' => 'user',
            'user' => $user,
            'isAdmin' => $isAdmin,
        ];
        return view('user/get', $data);
    }

    public function tambahdata()
    {
        // $user = $this->ModelUser->findAll();
        $data = [
            'judul' => 'User ',
            'subjudul' => 'User',
            'menu' => 'masterdata',
            'submenu' => 'user',
            'page' => 'user',
            'user' => $this->ModelUser->AllData(),
        ];

        return view('user/tambah', $data);
    }
    
    public function insertdata() 
    {
        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'level' => $this->request->getPost('level')
        ];
        $this->ModelUser->Tambah($data);
        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan');
        return redirect()->to('User');
    }

    public function edit($id_user)
    {
        $user = $this->ModelUser->getUserById($id_user);
        $data = [
            'judul' => 'Edit Data User',
            'subjudul' => 'User',
            'menu' => 'masterdata',
            'submenu' => 'user',
            'page' => 'user',
            'user' => $user,
        ];
        return view('user/edit', $data);
    }

    public function ubahdata($id_user)
    {
        $data = [
            // 'id_user' => $this->request->getPost('id_user'),
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'level' => $this->request->getPost('level'),
        ];
        $this->ModelUser->ubahdata($id_user, $data);
        session()->setFlashdata('pesan', 'Data Berhasil Diubah');
        return redirect()->to(base_url('User'));
    }

    public function hapusdata($id_user)
    {
        $data = [
            'id_user' => $id_user,
        ];
        $this->ModelUser->hapusdata($data);
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus');
        return redirect()->to('User');
    }   

}
