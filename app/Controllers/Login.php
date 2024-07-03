<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelUser;

class Login extends BaseController
{
    public function __construct()
    {
        $this->ModelUser = new ModelUser();
    }
    
    public function index()
    {
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $data = $this->ModelUser->where('username', $username)->first();

        if ($data) {
            log_message('debug', 'User found: ' . json_encode($data));
            $pass = $data['password'];
            $verify_pass = password_verify($password, $pass);
            if ($verify_pass) {
                $ses_data = [
                    'id_user'      => $data['id_user'],
                    'username'     => $data['username'],
                    'level'        => $data['level'],
                    'logged_in'    => TRUE
                ];
                $session->set($ses_data);

                // Redirect based on user level
                switch ($data['level']) {
                    case 'admin':
                        return redirect()->to('/dashboard/admin');
                    case 'kepalagudang':
                        return redirect()->to('/dashboard/kepalagudang');
                    case 'kepalapembelian':
                        return redirect()->to('/dashboard/kepalapembelian');
                    case 'kepalaproduksi':
                        return redirect()->to('/dashboard/kepalaproduksi');
                }
            } else {
                log_message('debug', 'Wrong Password');
                $session->setFlashdata('msg', 'Wrong Password');
                return redirect()->to('/auth');
            }
        } else {
            log_message('debug', 'Username not Found');
            $session->setFlashdata('msg', 'Username not Found');
            return redirect()->to('/auth');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/auth');
    }
}

    
