<?php

/**
 * Fungsi ini menangani logika untuk halaman utama.
 */
function index_action()
{
    $data = [
        'title' => 'Selamat Datang!',
        'content' => 'Ini adalah halaman utama kerangka terstruktur kita.'
    ];

    // Memanggil fungsi helper untuk menampilkan view
    view('home', $data);
}

/**
 * Fungsi ini menangani logika untuk halaman 'about'.
 */
function about_action()
{
    $data = ['title' => 'Tentang Kami'];
    view('about', $data);
}
