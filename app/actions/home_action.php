<?php

require_once __DIR__ . '/../data/template_desain_data.php';
require_once __DIR__ . '/../data/feedback_data.php';

/**
 * Fungsi ini menangani logika untuk halaman utama.
 */
function index_action()
{

    $db = db_connect();

    $templates = template_desain_get_all_paginated($db, ['limit' => 3]);

    $feedbacks = feedback_get_all_reviews($db, ['limit' => 3]);

    $data = [
        'title' => 'AbankIrenk - Jasa Pembuatan Yearbook Sekolah',
        'templates' => $templates['data'],
        'feedbacks' => $feedbacks['data']
    ];

    view('home.index', $data);
}

/**
 * Fungsi ini menangani logika untuk halaman 'about'.
 */
function about_action()
{
    $data = ['title' => 'Tentang Kami'];
    view('about', $data);
}
