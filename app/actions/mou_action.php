<?php
// app/actions/mou_action.php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../data/order_data.php'; // Kita pakai fungsi yang sudah ada

function ajax_get_mou($params)
{
  $db = db_connect();
  $id_order = (int) $params['id'];

  // Kita ambil data MoU dari order detail
  $order = order_get_by_id_for_detail($db, $id_order);

  $data = [
    'file_mou' => $order['file_mou'] ?? null
  ];

  view('order.partials._mou_tab', $data);
  exit();
}
