<?php
// actions/manajer_marketing/prospek_action.php

require_once __DIR__ . '/../../../core/database.php';
require_once __DIR__ . '/../../data/prospek_data.php';
require_once __DIR__ . '/../../data/sekolah_data.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../data/user_data.php';

function index_action()
{
  $data = [
    'page_title' => 'Manajemen Prospek',
    'active_menu' => 'prospek_mm',
    'status_options' => ['baru', 'berhasil', 'gagal', 'batal', 'dalam proses']
  ];
  view('manajer_marketing.manajemen_prospek.index', $data);
}

function create_action()
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    view('manajer_marketing.manajemen_prospek.create', [
      'page_title' => 'Tambah Prospek',
      'active_menu' => 'prospek_mm',
      'staff' => [],
    ]);
    return;
  }

  $staff = user_get_all_by_role($db, 'tim_marketing');

  $data = [
    'page_title' => 'Tambah Prospek Baru',
    'active_menu' => 'prospek_mm',
    'staff' => $staff,
  ];

  view('manajer_marketing.manajemen_prospek.create', $data);
}

/**
 * Menampilkan detail prospek (Read-only).
 *
 * @param array $params Parameter dari router (berisi ID).
 */
function show_action($params)
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  $id = (int) ($params['id'] ?? 0);
  if ($id <= 0) {
    flash_message('error', 'Error', 'ID Prospek tidak valid.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  $prospek = prospek_get_by_id($db, $id);

  if (!$prospek) {
    flash_message('error', 'Data Prospek', 'Prospek tidak ditemukan.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  $sekolah = sekolah_get_by_id($db, $prospek['id_sekolah']);
  $staff = user_find_by_id($db, $prospek['id_user']);

  $current_user = auth();
  $is_my_job = ($current_user['id'] == $prospek['id_user']);

  $data = [
    'page_title' => 'Detail Prospek',
    'active_menu' => 'prospek_mm',
    'prospek' => $prospek,
    'sekolah' => $sekolah,
    'staff' => $staff,
    'is_my_job' => $is_my_job
  ];

  // Pastikan nama view sesuai dengan lokasi file Anda
  view('manajer_marketing.manajemen_prospek.show', $data);
}

function store_action()
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    return redirect('/manajer-marketing/manajemen-prospek/create');
  }

  $success = false;
  db_begin_transaction($db);

  try {
    $narahubung = $_POST['narahubung'] ?? null;
    $no_narahubung = $_POST['no_narahubung'] ?? null;
    $id_sekolah = $_POST['id_sekolah'] ?? null;
    $id_user = $_POST['id_user'] ?? null;
    $deskripsi = $_POST['deskripsi'] ?? null;

    if (empty($narahubung) || empty($no_narahubung) || empty($id_user)) {
      throw new Exception("Narahubung, No. Narahubung, dan PIC wajib diisi.");
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

    $prospek_data = [
      'id_sekolah' => $final_id_sekolah,
      'id_user' => $id_user,
      'narahubung' => $narahubung,
      'no_narahubung' => $no_narahubung,
      'status' => 'baru',
      'deskripsi' => $deskripsi,
      'catatan' => ''
    ];

    if (!prospek_create($db, $prospek_data)) {
      throw new Exception("Gagal menyimpan data prospek.");
    }

    db_commit($db);
    flash_message('success', 'Tambah Prospek', 'Prospek baru berhasil ditambahkan.');
    $success = true;
  } catch (Exception $e) {
    db_rollback($db);
    error_log('Error in store_action: ' . $e->getMessage());
    flash_message('error', 'Tambah Prospek Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  if ($success) {
    return redirect('/manajer-marketing/manajemen-prospek');
  } else {
    return redirect('/manajer-marketing/manajemen-prospek/create');
  }
}

function edit_action($params)
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  $id = (int) ($params['id'] ?? 0);
  if ($id <= 0) {
    flash_message('error', 'Error', 'ID Prospek tidak valid.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  $prospek = prospek_get_by_id($db, $id);

  if (!$prospek) {
    flash_message('error', 'Data Prospek', 'Prospek tidak ditemukan.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  $sekolah = sekolah_get_by_id($db, $prospek['id_sekolah']);
  $staff = user_get_all_by_role($db, 'tim_marketing');

  $data = [
    'page_title' => 'Edit Prospek',
    'active_menu' => 'prospek_mm',
    'prospek' => $prospek,
    'sekolah' => $sekolah,
    'staff' => $staff,
    'status_options' => ['baru', 'berhasil', 'gagal', 'batal', 'dalam proses']
  ];

  view('manajer_marketing.manajemen_prospek.edit', $data);
}

function update_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0);

  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    return redirect('/manajer-marketing/manajemen-prospek/edit/' . $id);
  }

  if ($id <= 0) {
    flash_message('error', 'Error', 'ID Prospek tidak valid.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  $success = false;
  db_begin_transaction($db);

  try {
    $narahubung = $_POST['narahubung'] ?? null;
    $no_narahubung = $_POST['no_narahubung'] ?? null;
    $id_sekolah = $_POST['id_sekolah'] ?? null;
    $id_user = $_POST['id_user'] ?? null;
    $status_prospek = $_POST['status_prospek'] ?? null;
    $deskripsi = $_POST['deskripsi'] ?? null;

    if (empty($narahubung) || empty($no_narahubung) || empty($id_user) || empty($status_prospek)) {
      throw new Exception("Narahubung, No. Narahubung, PIC, dan Status wajib diisi.");
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

    $prospek_data = [
      'id_sekolah' => $final_id_sekolah,
      'id_user' => $id_user,
      'narahubung' => $narahubung,
      'no_narahubung' => $no_narahubung,
      'status_prospek' => $status_prospek,
      'deskripsi' => $deskripsi,
      'catatan' => ''
    ];

    if (prospek_update($db, $id, $prospek_data) === false) {
      throw new Exception("Gagal memperbarui data prospek.");
    }

    db_commit($db);
    flash_message('success', 'Update Berhasil', 'Prospek berhasil diperbarui.');
    $success = true;
  } catch (Exception $e) {
    db_rollback($db);
    error_log('Error in update_action: ' . $e->getMessage());
    flash_message('error', 'Update Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  if ($success) {
    return redirect('/manajer-marketing/manajemen-prospek');
  } else {
    return redirect('/manajer-marketing/manajemen-prospek/edit/' . $id);
  }
}

function delete_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0);

  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  if ($id <= 0) {
    flash_message('error', 'Error', 'ID Prospek tidak valid.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  db_begin_transaction($db);
  try {
    if (!prospek_delete($db, $id)) {
      throw new Exception("Gagal menghapus data prospek.");
    }

    db_commit($db);
    flash_message('success', 'Hapus Berhasil', 'Prospek berhasil dihapus.');
  } catch (Exception $e) {
    db_rollback($db);
    error_log('Error in delete_action: ' . $e->getMessage());
    flash_message('error', 'Hapus Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  return redirect('/manajer-marketing/manajemen-prospek');
}


function ajax_list_action()
{
  $db = db_connect();
  if (!$db) {
    header('Content-Type: application/json');
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

  $result = prospek_get_all($db, $options);

  $dataWithBadges = array_map(function ($row) {
    $status = $row['status_prospek'] ?? 'undefined';
    $row['status_badge'] = generate_status_badge($status);
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

  header('Content-Type: application/json');
  echo json_encode($response);
  exit();
}

function ajax_update_status_action()
{
  header('Content-Type: application/json');

  $db = db_connect();
  if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
  }

  try {
    $id = (int) ($_POST['id_prospek'] ?? 0);
    $status = (string) ($_POST['status'] ?? '');

    if ($id <= 0 || empty($status)) {
      http_response_code(400);
      throw new Exception('ID Prospek atau Status tidak valid.');
    }

    $allowed_statuses = ['baru', 'berhasil', 'gagal', 'batal', 'dalam proses'];
    if (!in_array($status, $allowed_statuses)) {
      http_response_code(400);
      throw new Exception("Nilai status '{$status}' tidak diizinkan.");
    }

    db_begin_transaction($db);

    $affectedRows = prospek_update_status($db, $id, $status);

    if ($affectedRows > 0) {
      db_commit($db);
      echo json_encode([
        'success' => true,
        'message' => 'Status prospek berhasil diperbarui.'
      ]);
    } else {
      db_rollback($db);
      throw new Exception('Gagal memperbarui status (mungkin data sama atau ID tidak ditemukan).');
    }
  } catch (Exception $e) {
    if (isset($db)) db_rollback($db);

    error_log('AJAX Update Status Error: ' . $e->getMessage());

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


function generate_status_badge($status)
{
  $baseClass = "px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full";
  switch ($status) {
    case 'baru':
      return "<span class='{$baseClass} bg-yellow-100 text-yellow-800'>Baru</span>";
    case 'dalam proses':
      return "<span class='{$baseClass} bg-blue-100 text-blue-800'>Dalam Proses</span>";
    case 'berhasil':
      return "<span class='{$baseClass} bg-green-100 text-green-800'>Berhasil</span>";
    case 'gagal':
      return "<span class='{$baseClass} bg-red-100 text-red-800'>Gagal</span>";
    case 'batal':
      return "<span class='{$baseClass} bg-gray-100 text-gray-800'>Batal</span>";
    default:
      return "<span class='{$baseClass} bg-gray-100 text-gray-800'>{$status}</span>";
  }
}
