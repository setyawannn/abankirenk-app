<?php
// app/actions/project_officer/template_mou_action.php

require_once __DIR__ . '/../../../core/database.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../data/template_mou_data.php';

/**
 * Menampilkan halaman daftar (index) template MoU.
 */
function index_action()
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    view('project_officer.template_mou.index', ['templates' => []]);
    return;
  }

  $data = [
    'page_title' => 'Template MoU',
    'active_menu' => 'template_mou_po',
    'templates' => template_mou_get_all($db)
  ];
  view('project_officer.template_mou.index', $data);
}

/**
 * Menampilkan form untuk membuat template baru.
 */
function create_action()
{
  $data = [
    'page_title' => 'Tambah Template MoU Baru',
    'active_menu' => 'template_mou_po',
  ];
  view('project_officer.template_mou.create', $data);
}

/**
 * Menyimpan template baru (menangani upload file).
 */
function store_action()
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    return redirect('/project-officer/template-mou/create');
  }

  $success = false;
  db_begin_transaction($db);

  try {
    $judul = $_POST['judul'] ?? null;
    $deskripsi = $_POST['deskripsi'] ?? null;

    // 1. Validasi Input Dasar
    if (empty($judul)) {
      throw new Exception("Judul template wajib diisi.");
    }

    // 2. Validasi File Upload
    if (!isset($_FILES['file_mou']) || $_FILES['file_mou']['error'] !== UPLOAD_ERR_OK) {
      throw new Exception("File template (Word/PDF) wajib di-upload.");
    }

    // 3. Panggil Helper Upload (yang sudah divalidasi MIME type-nya)
    // Kita kelompokkan file dalam folder 'mou'
    $uploadResult = handle_file_upload($_FILES['file_mou'], 'mou');

    if (!$uploadResult['success']) {
      throw new Exception("Upload Gagal: " . $uploadResult['message']);
    }

    // 4. Siapkan data untuk database
    $data = [
      'judul' => $judul,
      'deskripsi' => $deskripsi,
      'file_url' => $uploadResult['url'] // URL publik dari helper
    ];

    // 5. Simpan ke database
    if (!template_mou_create($db, $data)) {
      throw new Exception("Gagal menyimpan data template ke database.");
    }

    db_commit($db);
    flash_message('success', 'Berhasil', 'Template MoU baru berhasil ditambahkan.');
    $success = true;
  } catch (Exception $e) {
    db_rollback($db);
    error_log('Error in store_action (template_mou): ' . $e->getMessage());
    flash_message('error', 'Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  if ($success) {
    return redirect('/project-officer/template-mou');
  } else {
    return redirect('/project-officer/template-mou/create');
  }
}

/**
 * Menampilkan form untuk mengedit template.
 */
function edit_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0);

  if (!$db || $id <= 0) {
    flash_message('error', 'Error', 'Gagal terhubung ke database atau ID tidak valid.');
    return redirect('/project-officer/template-mou');
  }

  $template = template_mou_get_by_id($db, $id);
  if (!$template) {
    flash_message('error', 'Data Tidak Ditemukan', 'Template MoU tidak ditemukan.');
    return redirect('/project-officer/template-mou');
  }

  $data = [
    'page_title' => 'Edit Template MoU',
    'active_menu' => 'template_mou_po',
    'template' => $template
  ];
  view('project_officer.template_mou.edit', $data);
}

/**
 * Memperbarui template yang ada.
 */
function update_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0);

  if (!$db || $id <= 0) {
    flash_message('error', 'Error', 'Gagal terhubung atau ID tidak valid.');
    return redirect('/project-officer/template-mou');
  }

  $success = false;
  db_begin_transaction($db);

  try {
    $judul = $_POST['judul'] ?? null;
    $deskripsi = $_POST['deskripsi'] ?? null;

    if (empty($judul)) {
      throw new Exception("Judul template wajib diisi.");
    }

    // Ambil data lama (penting untuk path file)
    $oldTemplate = template_mou_get_by_id($db, $id);
    if (!$oldTemplate) {
      throw new Exception("Template lama tidak ditemukan.");
    }

    $newFileUrl = $oldTemplate['mou']; // Default: gunakan file lama

    // Cek jika ada file BARU yang di-upload
    if (isset($_FILES['file_mou']) && $_FILES['file_mou']['error'] === UPLOAD_ERR_OK) {
      // Panggil Helper Upload
      $uploadResult = handle_file_upload($_FILES['file_mou'], 'mou');

      if (!$uploadResult['success']) {
        throw new Exception("Upload Gagal: " . $uploadResult['message']);
      }
      $newFileUrl = $uploadResult['url'];

      unlink(__DIR__ . '/../../../public' . $oldTemplate['mou']);
    }

    $data = [
      'judul' => $judul,
      'deskripsi' => $deskripsi,
      'file_url' => $newFileUrl
    ];

    if (template_mou_update($db, $id, $data) === false) {
      throw new Exception("Gagal memperbarui data template di database.");
    }

    db_commit($db);
    flash_message('success', 'Berhasil', 'Template MoU berhasil diperbarui.');
    $success = true;
  } catch (Exception $e) {
    db_rollback($db);
    error_log('Error in update_action (template_mou): ' . $e->getMessage());
    flash_message('error', 'Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  if ($success) {
    return redirect('/project-officer/template-mou');
  } else {
    return redirect('/project-officer/template-mou/' . $id . '/edit');
  }
}

/**
 * Menghapus template.
 */
function delete_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0);

  if (!$db || $id <= 0) {
    flash_message('error', 'Error', 'Gagal terhubung atau ID tidak valid.');
    return redirect('/project-officer/template-mou');
  }

  $template = template_mou_get_by_id($db, $id);
  unlink(__DIR__ . '/../../../public' . $template['mou']);

  if (template_mou_delete($db, $id)) {
    flash_message('success', 'Berhasil', 'Template MoU berhasil dihapus.');
  } else {
    flash_message('error', 'Gagal', 'Gagal menghapus template dari database.');
  }

  return redirect('/project-officer/template-mou');
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

  $result = template_mou_get_all_paginated($db, $options);
  $totalPages = ($result['total'] > 0 && $limit > 0) ? ceil($result['total'] / $limit) : 1;

  $response = [
    'data' => $result['data'],
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
