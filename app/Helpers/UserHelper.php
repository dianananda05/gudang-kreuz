<?php

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        $session = session();
        return $session->get('level') === 'admin'; // Sesuaikan dengan struktur peran Anda
    }
}

if (!function_exists('isKepalaGudang')) {
    function isKepalaGudang()
    {
        $session = session();
        return $session->get('level') === 'kepalagudang'; // Sesuaikan dengan struktur peran Anda
    }
}
