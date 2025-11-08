<?php
// app/actions/klien/pengajuan_order_action.php

require_once __DIR__ . '/../../../core/database.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../data/pengajuan_order_data.php';
require_once __DIR__ . '/../../data/sekolah_data.php';

function index_action()
{
  $data = [
    'page_title' => 'Riwayat Pengajuan Order',
    'active_menu' => 'pengajuan_order_klien',
    'status_options' => ['berhasil', 'gagal', 'batal', 'dalam proses']
  ];
  view('klien.pengajuan_order.index', $data);
}

function create_action()
{
  $data = [
    'page_title' => 'Buat Pengajuan Order Baru',
    'active_menu' => 'pengajuan_order_klien',
  ];
  view('klien.pengajuan_order.create', $data);
}


function store_action()
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    return redirect('/klien/pengajuan-order/create');
  }

  $user = auth();
  if (!$user) {
    flash_message('error', 'Otentikasi Gagal', 'Anda harus login untuk membuat pengajuan.');
    return redirect('/login');
  }

  $success = false;
  db_begin_transaction($db);

  try {
    $id_sekolah = $_POST['id_sekolah'] ?? null;
    $narahubung = $_POST['narahubung'] ?? null;
    $no_narahubung = $_POST['no_narahubung'] ?? null;
    $pesan = $_POST['pesan'] ?? null;

    if (empty($id_sekolah) || empty($narahubung) || empty($no_narahubung) || empty($pesan)) {
      throw new Exception("Semua field wajib diisi.");
    }

    $final_id_sekolah = $id_sekolah;
    if (empty($final_id_sekolah)) {
      $nama_sekolah = $_POST['nama_sekolah'] ?? null;
      $lokasi_sekolah = $_POST['lokasi_sekolah'] ?? null;
      $kontak_sekolah = $_POST['kontak_sekolah'] ?? null;

      if (empty($nama_sekolah) || empty($lokasi_sekolah)) {
        throw new Exception("Jika menambah sekolah baru, nama dan alamat sekolah wajib diisi.");
      }

      $final_id_sekolah = sekolah_insert($db, $nama_sekolah, $lokasi_sekolah, $kontak_sekolah);
      if (!$final_id_sekolah) {
        throw new Exception("Gagal menyimpan data sekolah baru.");
      }
    }

    $data = [
      'id_sekolah' => $final_id_sekolah,
      'id_user' => $user['id'],
      'status_pengajuan' => 'dalam proses',
      'pesan' => $pesan,
      'narahubung' => $narahubung,
      'no_narahubung' => $no_narahubung,
    ];

    if (!pengajuan_order_create($db, $data)) {
      throw new Exception("Gagal menyimpan pengajuan order ke database.");
    }

    db_commit($db);
    flash_message('success', 'Berhasil', 'Pengajuan order Anda telah terkirim dan akan segera diproses.');
    $success = true;
  } catch (Exception $e) {
    db_rollback($db);
    error_log('Error in store_action (pengajuan_order): ' . $e->getMessage());
    flash_message('error', 'Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  if ($success) {
    return redirect('/klien/pengajuan-order');
  } else {
    return redirect('/klien/pengajuan-order/create');
  }
}

function ajax_list_action()
{
  header('Content-Type: application/json');
  $db = db_connect();
  $user = auth();

  if (!$db || !$user) {
    http_response_code(403);
    echo json_encode(['error' => 'Otentikasi gagal.', 'data' => []]);
    exit();
  }

  $id_user = $user['id'];
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
  $search = $_GET['search'] ?? '';

  $options = [
    'page' => $page,
    'limit' => $limit,
    'search' => $search,
  ];

  $result = pengajuan_order_get_by_user($db, $id_user, $options);

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
