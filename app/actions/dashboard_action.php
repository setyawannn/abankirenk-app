<?php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../data/dashboard_data.php';

function generate_order_status_badge($status)
{
  $baseClass = "px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full";
  switch ($status) {
    case 'baru':
      return "<span class='{$baseClass} bg-blue-100 text-blue-800'>Baru</span>";
    case 'proses':
      return "<span class='{$baseClass} bg-yellow-100 text-yellow-800'>Proses</span>";
    case 'selesai':
      return "<span class='{$baseClass} bg-green-100 text-green-800'>Selesai</span>";
    case 'batal':
      return "<span class='{$baseClass} bg-gray-100 text-gray-800'>Batal</span>";
    default:
      return "<span class='{$baseClass} bg-gray-100 text-gray-800'>{$status}</span>";
  }
}


function index_action()
{
  $db = db_connect();
  $user = auth();
  $role = $user['role'];
  $id_user = $user['id'];


  $data = [
    'page_title' => 'Dashboard',
    'active_menu' => 'dashboard',
    'user' => $user
  ];

  if ($role == 'project_officer') {
    $data['stats'] = dashboard_get_po_stats($db);
    $data['pending_pengajuan'] = dashboard_get_po_pending_pengajuan($db, 5);
    $data['urgent_tasks'] = dashboard_get_po_urgent_tasks($db, 5);
    $data['order_chart_data'] = dashboard_get_po_order_chart($db);

    view('dashboard.project_officer', $data);
  } elseif ($role == 'manajer_produksi') {

    $data['stats'] = dashboard_get_mp_stats($db);
    $data['failed_qc_list'] = dashboard_get_mp_failed_qc($db, 5);

    $data['urgent_tasks'] = dashboard_get_po_urgent_tasks($db, 5);
    $data['order_chart_data'] = dashboard_get_po_order_chart($db);

    view('dashboard.manajer_produksi', $data);
  } elseif ($role == 'manajer_marketing') {

    $data['stats'] = dashboard_get_mm_stats($db);
    $data['prospek_chart_data'] = dashboard_get_mm_status_chart($db);
    $data['staff_chart_data'] = dashboard_get_mm_staff_performance($db);
    $data['recent_success'] = dashboard_get_mm_recent_success($db, 5);

    view('dashboard.manajer_marketing', $data);
  } elseif ($role == 'tim_marketing') {

    $data['stats'] = dashboard_get_tm_stats($db, $id_user);
    $data['prospek_chart_data'] = dashboard_get_tm_status_chart($db, $id_user);
    $data['actionable_prospects'] = dashboard_get_tm_actionable_prospects($db, $id_user, 5);

    view('dashboard.tim_marketing', $data);
  } elseif ($role == 'tim_percetakan') {

    $data['stats'] = dashboard_get_percetakan_stats($db, $id_user);
    $data['active_tasks'] = dashboard_get_percetakan_active_tasks($db, $id_user);
    $data['failed_qc_list'] = dashboard_get_mp_failed_qc($db, 5);
    $data['task_chart_data'] = dashboard_get_percetakan_chart($db, $id_user);

    view('dashboard.tim_percetakan', $data);
  } elseif ($role == 'klien') {

    $data['stats'] = dashboard_get_klien_stats($db, $id_user);
    $orders = dashboard_get_klien_orders($db, $id_user);
    $data['orders'] = array_map(function ($order) {
      $order['status_badge'] = generate_order_status_badge($order['status_order']);
      return $order;
    }, $orders);

    view('dashboard.klien', $data);
  } elseif ($role == 'desainer') {

    $data['stats'] = dashboard_get_desainer_stats($db, $id_user);
    $data['task_chart_data'] = dashboard_get_desainer_chart($db, $id_user);
    $data['active_tasks'] = dashboard_get_desainer_active_tasks($db, $id_user);

    view('dashboard.desainer', $data);
  }
}
