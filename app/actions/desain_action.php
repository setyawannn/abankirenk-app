<?php
// app/actions/desain_action.php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
// require_once __DIR__ . '/../data/desain_data.php';

function ajax_get_desain($params)
{
  // ... (Logika ambil data desain) ...
  $data = [
    'desain_items' => [] // (Data placeholder)
  ];
  view('order.partials._desain_tab', $data);
  exit();
}
