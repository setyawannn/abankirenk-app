<?php
// app/actions/order_action.php

require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../data/order_data.php';
require_once __DIR__ . '/../data/mou_data.php';
require_once __DIR__ . '/../data/user_data.php';
require_once __DIR__ . '/../data/prospek_data.php';
require_once __DIR__ . '/../data/timeline_data.php';
require_once __DIR__ . '/../data/pengajuan_order_data.php';


function index_action()
{
  $data = [
    'page_title' => 'Manajemen Order Produksi',
    'active_menu' => 'order',
    'status_options' => ['baru', 'proses', 'selesai', 'batal']
  ];
  view('order.index', $data);
}

function detail_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0); // Ini adalah id_order_produksi (int)
  $user = auth(); // Pengguna yang sedang login

  if (!$db || $id <= 0 || !$user) {
    flash_message('error', 'Error', 'Gagal memuat data.');
    return redirect('/dashboard');
  }

  $order = order_get_by_id_for_detail($db, $id); // (Fungsi ini sudah kita buat)

  if (!$order) {
    flash_message('error', 'Data Tidak Ditemukan', 'Order produksi tidak ditemukan.');
    return redirect('/dashboard');
  }

  $user_role = $user['role'];
  $id_user = $user['id'];

  $is_owner = ($user_role == 'klien' && $id_user == $order['id_klien']);

  $is_core_staff = in_array($user_role, [
    'project_officer',
    'manajer_marketing',
    'manajer_produksi'
  ]);

  $is_assigned_staff = false;
  if (in_array($user_role, ['desainer', 'tim_percetakan'])) {
    $is_assigned_staff = timeline_is_user_assigned_to_order_by_nomor($db, $id_user, $order['id_order_produksi']);
  }

  if ($is_owner || (!$is_core_staff && !$is_assigned_staff)) {
    flash_message('error', 'Akses Ditolak', 'Anda tidak memiliki izin untuk melihat order ini.');

    if ($is_owner) {
      return redirect('/klien/pengajuan-order');
    }
    return redirect('/dashboard');
  }

  $allowed_tabs = [];
  $baseUrl = '/ajax/order/' . $id;

  if ($user_role != 'klien') {
    $allowed_tabs[] = ['id' => 'timeline', 'label' => 'Timeline Produksi', 'url' => url($baseUrl . '/timeline')];
  }

  if (in_array($user_role, ['project_officer', 'manajer_marketing', 'manajer_produksi', 'klien'])) {
    $allowed_tabs[] = ['id' => 'mou', 'label' => 'MoU', 'url' => url($baseUrl . '/mou')];
  }

  if (in_array($user_role, ['project_officer', 'manajer_produksi', 'manajer_marketing', 'desainer', 'klien'])) {
    $allowed_tabs[] = ['id' => 'desain', 'label' => 'Desain', 'url' => url($baseUrl . '/desain')];
  }

  if (in_array($user_role, ['project_officer', 'manajer_produksi', 'manajer_marketing', 'tim_percetakan'])) {
    $allowed_tabs[] = ['id' => 'qc', 'label' => 'Quality Control', 'url' => url($baseUrl . '/qc')];
  }

  // (Tambahkan tab QC, Pengiriman, Tiket, dll. di sini nanti)

  $data = [
    'page_title' => 'Detail Order: ' . $order['nomor_order'],
    'active_menu' => 'order',
    'order' => $order,
    'tabs' => $allowed_tabs,
  ];

  if ($user_role == 'project_officer') {
    $data['status_options'] = ['baru', 'proses', 'batal'];
  } elseif ($user_role == 'manajer_produksi') {
    $data['status_options'] = ['proses', 'selesai'];
  }

  view('order.detail', $data);
}

function create_action()
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    view('order.create', ['sumber_order' => [], 'klien_list' => []]);
    return;
  }

  $data = [
    'page_title' => 'Buat Order Produksi Baru',
    'active_menu' => 'order',
    'sumber_order' => order_get_successful_sources($db),
    'klien_list' => user_get_all_by_role($db, 'klien')
  ];
  view('order.create', $data);
}

function store_action()
{
  $db = db_connect();
  $po_user = auth();

  if (!$db || !$po_user) {
    flash_message('error', 'Error', 'Otentikasi gagal atau database tidak terhubung.');
    return redirect('/order/create');
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
      'id_klien' => $id_klien,
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

  return redirect($success ? '/dashboard' : '/order/create');
}

function ajax_update_status_action()
{
  header('Content-Type: application/json');
  $db = db_connect();
  $po_user = auth();

  if (!$db || !$po_user) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Otentikasi gagal.']);
    exit();
  }

  try {
    $id = (int) ($_POST['id_order_produksi'] ?? 0);
    $status = (string) ($_POST['status'] ?? '');

    if ($id <= 0 || empty($status)) {
      http_response_code(400);
      throw new Exception('ID Order atau Status tidak valid.');
    }

    $allowed_statuses_po = ['baru', 'proses', 'batal'];
    if (!in_array($status, $allowed_statuses_po)) {
      http_response_code(403);
      throw new Exception("Anda (PO) tidak diizinkan mengubah status menjadi '{$status}'.");
    }

    db_begin_transaction($db);

    $affectedRows = order_update_status($db, $id, $status);

    if ($affectedRows > 0) {
      db_commit($db);
      echo json_encode([
        'success' => true,
        'message' => 'Status order berhasil diperbarui.'
      ]);
    } else {
      db_rollback($db);
      throw new Exception('Gagal memperbarui status (data mungkin sama atau ID tidak ditemukan).');
    }
  } catch (Exception $e) {
    if ($db) db_rollback($db);
    error_log('AJAX Order Status Error: ' . $e->getMessage());
    if (http_response_code() == 200) {
      http_response_code(500);
    }
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage()
    ]);
  }

  exit();
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
    'narahubung' => $data['narahubung'] ?? $data['narahubung'],
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

function ajax_get_timeline_tab($params)
{
  $db = db_connect();
  $nomor_order = $params['nomor_order'] ?? null;

  $items = timeline_get_by_order_id($db, $nomor_order);

  $data = [
    'items' => $items,
    'nomor_order' => $nomor_order,
  ];

  view('order.partials._timeline_tab', $data);
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
