<?php

require_once __DIR__ . '/../../../core/database.php';
require_once __DIR__ . '/../../data/prospek_data.php';
require_once __DIR__ . '/../../data/sekolah_data.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../data/user_data.php';

function index_action()
{
  $data = [
    'page_title' => 'Prospek Saya',
    'active_menu' => 'prospek_tm',
    'status_options' => ['baru', 'berhasil', 'gagal', 'batal', 'dalam proses']
  ];
  view('tim_marketing.prospek_saya.index', $data);
}
