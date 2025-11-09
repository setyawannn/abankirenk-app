<?php
// app/actions/project_officer/order_action.php

require_once __DIR__ . '/../../../core/database.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../data/order_data.php';
require_once __DIR__ . '/../../data/mou_data.php';
require_once __DIR__ . '/../../data/user_data.php';
require_once __DIR__ . '/../../data/prospek_data.php';
require_once __DIR__ . '/../../data/pengajuan_order_data.php';


function index_action()
{
  $data = [
    'page_title' => 'Manajemen Order Produksi',
    'active_menu' => 'order_po',
    'status_options' => ['baru', 'proses', 'selesai', 'batal']
  ];
  view('project_officer.order.index', $data);
}

function create_action()
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    view('project_officer.order.create', ['sumber_order' => [], 'klien_list' => []]);
    return;
  }

  $data = [
    'page_title' => 'Buat Order Produksi Baru',
    'active_menu' => 'order_po',
    'sumber_order' => order_get_successful_sources($db),
    'klien_list' => user_get_all_by_role($db, 'klien')
  ];
  view('project_officer.order.create', $data);
}

function store_action()
{
  $db = db_connect();
  $po_user = auth();

  if (!$db || !$po_user) {
    flash_message('error', 'Error', 'Otentikasi gagal atau database tidak terhubung.');
    return redirect('/project-officer/order/create');
  }

  $success = false;
  db_begin_transaction($db);

  try {
    $id_klien = $_POST['id_klien'] ?? null;

    if (isset($_POST['create_klien_checkbox'])) {
      $password_plain = '12345678';
      $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

      $klien_data = [
        'nama' => $_POST['nama_klien'],
        'email' => $_POST['email_klien'],
        'username' => $_POST['username_klien'],
        'password' => $password_hash,
        'role' => 'klien'
      ];
      $id_klien = user_create($db, $klien_data);
      if (!$id_klien) throw new Exception("Gagal membuat akun klien baru.");
    } else {
      if (empty($id_klien)) throw new Exception("Klien harus dipilih atau dibuat.");
    }

    if (!isset($_FILES['file_mou']) || $_FILES['file_mou']['error'] !== UPLOAD_ERR_OK) {
      throw new Exception("File MoU wajib di-upload.");
    }
    $uploadResult = handle_file_upload($_FILES['file_mou'], 'mou', 'documents');
    if (!$uploadResult['success']) {
      throw new Exception("Upload MoU Gagal: " . $uploadResult['message']);
    }

    $id_mou = mou_create($db, $uploadResult['url'], $po_user['id']);
    if (!$id_mou) throw new Exception("Gagal menyimpan data MoU.");

    $nomor_order = generate_nomor_order($db);
    $sequence = (int) substr($nomor_order, -3);

    $data = [
      'nomor_order' => $nomor_order,
      'sequence' => $sequence,
      'id_sekolah' => (int) $_POST['id_sekolah'],
      'id_mou' => $id_mou,
      'id_user_klien' => $id_klien,
      'narahubung' => $_POST['narahubung'],
      'no_narahubung' => $_POST['no_narahubung'],
      'kuantitas' => (int) $_POST['kuantitas'],
      'halaman' => (int) $_POST['halaman'],
      'konsep' => $_POST['konsep'],
      'deadline' => $_POST['deadline']
    ];

    if (!order_create($db, $data)) {
      throw new Exception("Gagal menyimpan data order produksi utama.");
    }

    db_commit($db);
    flash_message('success', 'Berhasil', 'Order produksi baru telah dibuat dengan nomor: ' . $nomor_order);
    $success = true;
  } catch (Exception $e) {
    db_rollback($db);
    error_log('Error in store_action (order_action): ' . $e->getMessage());
    flash_message('error', 'Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  return redirect($success ? '/project-officer/dashboard' : '/project-officer/order/create');
}

function ajax_get_source_details_action()
{
  header('Content-Type: application/json');
  $db = db_connect();
  if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit();
  }

  $source_key = $_GET['source_key'] ?? '';
  list($type, $id) = array_pad(explode('_', $source_key), 2, null);

  $data = null;
  if ($type === 'prospek' && $id) {
    $data = prospek_get_by_id($db, (int)$id);
  } elseif ($type === 'pengajuan' && $id) {
    $data = pengajuan_order_get_by_id($db, (int)$id);
  }

  if (!$data) {
    http_response_code(404);
    echo json_encode(['error' => 'Data tidak ditemukan']);
    exit();
  }

  $response = [
    'id_sekolah' => $data['id_sekolah'],
    'narahubung' => $data['narahubung'] ?? $data['narahbung'],
    'no_narahubung' => $data['no_narahubung'],
    'id_klien_existing' => $data['id_user'] ?? null
  ];

  echo json_encode($response);
  exit();
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
  $status = $_GET['status'] ?? '';

  $options = [
    'page' => $page,
    'limit' => $limit,
    'search' => $search,
    'status' => $status,
  ];

  $result = order_get_all_for_po($db, $options);

  $dataWithBadges = array_map(function ($row) {
    $row['status_badge'] = generate_order_status_badge($row['status_order']);
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
