<?php
// app/actions/timeline_action.php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
// Anda perlu membuat data layer untuk timeline:
// require_once __DIR__ . '/../data/timeline_data.php'; 

function ajax_get_timeline($params)
{
  $db = db_connect();
  $id_order_produksi = (int) $params['id'];

  // (TODO: Buat fungsi ini di data layer)
  // $timeline_items = timeline_get_by_order_id($db, $id_order_produksi); 

  // (Data placeholder untuk demo)
  $timeline_items = [
    ['judul' => 'Finalisasi Cover', 'deadline' => '20/11/2025', 'user' => 'Ahmad Handoko'],
    ['judul' => 'Review Foto Siswa', 'deadline' => '25/11/2025', 'user' => 'Abinoto Sutejo']
  ];

  $data = [
    'items' => $timeline_items,
    'id_order' => $id_order_produksi
  ];

  // Render HANYA file partial-nya
  view('order.partials._timeline_tab', $data);
  exit();
}
