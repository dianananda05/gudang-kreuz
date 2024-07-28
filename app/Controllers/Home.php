<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // phpinfo();
        $data = [
            'page' => 'home'
        ];
        return view('home', $data);
    }
}
