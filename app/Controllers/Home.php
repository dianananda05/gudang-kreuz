<?php

namespace App\Controllers;

// use App\Models\ModelUser;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'page' => 'home'
        ];
        return view('home', $data);
    }
    // public function __construct()
    // {
    //     $this->ModelUser = new ModelUser();
    // }

    // public function index()
    // {
    //     $data = [
    //         'judul' => 'login/index'
    //     ];
    //     return view('login/index', $data);
    // }

    // public function ceklogin()
    // {
    //     if ($this->validate([
    //         'username' => [
    //             'label' => 'Username',
    //             'rules' => 'required',
    //             'errors' => [
    //                 'required' => '{field} Masih Kosong!',
    //             ]
    //         ],
    //         'password' => [
    //             'label' => 'Password',
    //             'rules' => 'required',
    //             'errors' => [
    //                 'required' => '{field} Belum Dipilih!',
    //             ]
    //         ]
    //     ])) {
    //         $username = $this->request->getPost('username');
    //         $password = sha1($this->request->getPost('password'));
    //         $ceklogin = $this->ModelUser->loginuser($username, $password);
    //         if ($ceklogin) {
    //             session()->set('id', $ceklogin['id']);
    //             session()->set('nama', $ceklogin['nama']);
    //             session()->set('level', $ceklogin['level']);
    //             if ($ceklogin['level'] == 1) {
    //                 return redirect()->to(base_url('Admin'));
    //             } else {
    //                 return redirect()->to(base_url('Penjualan'));
    //             }
    //         } else {
    //             session()->setFlashdata('gagal', 'E-Mail atau Password Salah!!!');
    //             return redirect()->to(base_url('Home'));
    //         }
    //     } else {
    //         session()->setFlashdata('errors', \Config\Services::validation()->getErrors());
    //         return redirect()->to(base_url('Home'))->withInput('validation', \Config\Services::validation());
    //     }
    // }

    // public function Logout()
    // {
    //     session()->remove('id');
    //     session()->remove('nama');
    //     session()->remove('level');
    //     session()->setFlashdata('pesan', 'Anda Berhasil LogOut');
    //     return redirect()->to(base_url('Home'));
    // }
}
