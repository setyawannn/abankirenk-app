<?php
// app/actions/project_officer/pengajuan_order_action.php

require_once __DIR__ . '/../../../core/database.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../data/pengajuan_order_data.php';

function index_action()
{
  $data = [
    'page_title' => 'Manajemen Pengajuan Order',
    'active_menu' => 'pengajuan_order_po',
    'status_options' => ['dalam proses', 'berhasil', 'gagal', 'batal']
  ];
  view('project_officer.pengajuan_order.index', $data);
}

function detail_action($params)
{
  $db = db_connect();
  $user = auth();
  $id = (int) ($params['id'] ?? 0);

  if (!$db || $id <= 0 || !$user) {
    flash_message('error', 'Error', 'Gagal memuat data atau ID tidak valid.');
    return redirect('/project-officer/pengajuan-order');
  }

  $pengajuan = pengajuan_order_get_by_id($db, $id);

  $pengajuan['status_badge'] = generate_status_badge_klien($pengajuan['status_pengajuan']);

  $data = [
    'page_title' => $pengajuan['nomor_pengajuan'],
    'active_menu' => 'pengajuan_order_po',
    'pengajuan' => $pengajuan,
  ];

  view('project_officer.pengajuan_order.detail', $data);
}

function edit_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0);

  if (!$db || $id <= 0) {
    flash_message('error', 'Error', 'Gagal terhubung atau ID tidak valid.');
    return redirect('/project-officer/pengajuan-order');
  }

  $pengajuan = pengajuan_order_get_by_id($db, $id);
  if (!$pengajuan) {
    flash_message('error', 'Data Tidak Ditemukan', 'Pengajuan order tidak ditemukan.');
    return redirect('/project-officer/pengajuan-order');
  }

  $data = [
    'page_title' => $pengajuan['nomor_pengajuan'],
    'active_menu' => 'pengajuan_order_po',
    'pengajuan' => $pengajuan,
    'status_options' => ['dalam proses', 'berhasil', 'gagal', 'batal']
  ];
  view('project_officer.pengajuan_order.edit', $data);
}

function update_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0);
  $po_user = auth();

  if (!$db || $id <= 0) {
    flash_message('error', 'Error', 'Gagal terhubung atau ID tidak valid.');
    return redirect('/project-officer/pengajuan-order');
  }

  $success = false;
  db_begin_transaction($db);

  try {
    $balasan = $_POST['balasan'] ?? null;
    $status = $_POST['status_pengajuan'] ?? null;

    if (empty($status)) {
      throw new Exception("Status wajib diisi.");
    }
    if (empty($balasan) && $status != 'dalam proses') {
      throw new Exception("Balasan wajib diisi jika status bukan 'Dalam Proses'.");
    }


    $id_user_po = $po_user['id'];
    if (pengajuan_order_update_by_po($db, $id, $status, $balasan, $id_user_po) === false) {
      throw new Exception("Gagal memperbarui data pengajuan di database.");
    }

    db_commit($db);
    flash_message('success', 'Berhasil', 'Pengajuan order berhasil diperbarui.');
    $success = true;
  } catch (Exception $e) {
    db_rollback($db);
    error_log('Error in update_action (PO pengajuan_order): ' . $e->getMessage());
    flash_message('error', 'Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  return redirect($success ? '/project-officer/pengajuan-order' : '/project-officer/pengajuan-order/' . $id . '/edit');
}

function ajax_list_action()
{
  header('Content-Type: application/json');
  $db = db_connect();

  if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed', 'data' => []]);
    exit();
  }

  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
  $search = $_GET['search'] ?? '';

  $options = [
    'page' => $page,
    'limit' => $limit,
    'search' => $search,
  ];

  $result = pengajuan_order_get_all_for_po($db, $options);

  $dataWithBadges = array_map(function ($row) {
    $row['status_badge'] = generate_status_badge_klien($row['status_pengajuan']);

    return $row;
  }, $result['data']);

  $totalPages = ($result['total'] > 0 && $limit > 0) ? ceil($result['total'] / $limit) : 1;

  $response = [
    'data' => $dataWithBadges,
    'pagination' => [
      'total' => $result['total'],
      'per_page' => $limit,
      'current_page' => $page,
      'last_page' => $totalPages,
    ]
  ];

  echo json_encode($response);
  exit();
}

function generate_status_badge_klien($status)
{
  $baseClass = "px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full";
  switch ($status) {
    case 'dalam proses':
      return "<span class='{$baseClass} bg-blue-100 text-blue-800'>Dalam Proses</span>";
    case 'berhasil':
      return "<span class='{$baseClass} bg-green-100 text-green-800'>Disetujui</span>";
    case 'gagal':
      return "<span class='{$baseClass} bg-red-100 text-red-800'>Ditolak</span>";
    case 'batal':
      return "<span class='{$baseClass} bg-gray-100 text-gray-800'>Dibatalkan</span>";
    default:
      return "<span class='{$baseClass} bg-yellow-100 text-yellow-800'>{$status}</span>";
  }
}
