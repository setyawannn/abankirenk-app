<?php
// app/actions/desainer/template_desain_action.php

require_once __DIR__ . '/../../../core/database.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../data/template_desain_data.php';

function index_action()
{
  $db = db_connect();
  $data = [
    'page_title' => 'Manajemen Template Desain',
    'active_menu' => 'template_desain_ds',
    'templates' => $db ? template_desain_get_all_paginated($db, [])['data'] : []
  ];
  view('desainer.template_desain.index', $data);
}

function create_action()
{
  $data = [
    'page_title' => 'Tambah Template Desain Baru',
    'active_menu' => 'template_desain_ds',
  ];
  view('desainer.template_desain.create', $data);
}

function store_action()
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    return redirect('/desainer/template-desain/create');
  }

  $success = false;
  db_begin_transaction($db);

  try {
    $judul = $_POST['judul'] ?? null;
    $deskripsi = $_POST['deskripsi'] ?? null;

    if (empty($judul)) throw new Exception("Judul template wajib diisi.");
    if (!isset($_FILES['file_desain']) || $_FILES['file_desain']['error'] !== UPLOAD_ERR_OK) {
      throw new Exception("File gambar template wajib di-upload.");
    }

    $uploadResult = handle_file_upload($_FILES['file_desain'], 'desain', 'documents');

    if (!$uploadResult['success']) {
      throw new Exception("Upload Gagal: " . $uploadResult['message']);
    }

    $data = [
      'judul' => $judul,
      'deskripsi' => $deskripsi,
      'file_url' => $uploadResult['url']
    ];

    if (!template_desain_create($db, $data)) {
      throw new Exception("Gagal menyimpan data template ke database.");
    }

    db_commit($db);
    flash_message('success', 'Berhasil', 'Template desain baru berhasil ditambahkan.');
    $success = true;
  } catch (Exception $e) {
    db_rollback($db);
    error_log('Error in store_action (template_desain): ' . $e->getMessage());
    flash_message('error', 'Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  return redirect($success ? '/desainer/template-desain' : '/desainer/template-desain/create');
}

function edit_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0);

  if (!$db || $id <= 0) {
    flash_message('error', 'Error', 'Gagal terhubung atau ID tidak valid.');
    return redirect('/desainer/template-desain');
  }

  $template = template_desain_get_by_id($db, $id);
  if (!$template) {
    flash_message('error', 'Data Tidak Ditemukan', 'Template desain tidak ditemukan.');
    return redirect('/desainer/template-desain');
  }

  $data = [
    'page_title' => 'Edit Template Desain',
    'active_menu' => 'template_desain_ds',
    'template' => $template
  ];
  view('desainer.template_desain.edit', $data);
}

function update_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0);

  if (!$db || $id <= 0) {
    flash_message('error', 'Error', 'Gagal terhubung atau ID tidak valid.');
    return redirect('/desainer/template-desain');
  }

  $success = false;
  db_begin_transaction($db);

  try {
    $judul = $_POST['judul'] ?? null;
    $deskripsi = $_POST['deskripsi'] ?? null;

    if (empty($judul)) throw new Exception("Judul template wajib diisi.");

    $oldTemplate = template_desain_get_by_id($db, $id);
    if (!$oldTemplate) throw new Exception("Template lama tidak ditemukan.");

    $newFileUrl = $oldTemplate['template_desain'];

    if (isset($_FILES['file_desain']) && $_FILES['file_desain']['error'] === UPLOAD_ERR_OK) {
      $uploadResult = handle_file_upload($_FILES['file_desain'], 'desain', 'documents', $oldTemplate['template_desain']);

      if (!$uploadResult['success']) {
        throw new Exception("Upload Gagal: " . $uploadResult['message']);
      }
      $newFileUrl = $uploadResult['url'];

      if (!empty($oldTemplate['template_desain'])) {
        delete_storage_file($oldTemplate['template_desain']);
      }
    }

    $data = [
      'judul' => $judul,
      'deskripsi' => $deskripsi,
      'file_url' => $newFileUrl
    ];

    if (template_desain_update($db, $id, $data) === false) {
      throw new Exception("Gagal memperbarui data template di database.");
    }

    db_commit($db);
    flash_message('success', 'Berhasil', 'Template desain berhasil diperbarui.');
    $success = true;
  } catch (Exception $e) {
    db_rollback($db);
    error_log('Error in update_action (template_desain): ' . $e->getMessage());
    flash_message('error', 'Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  return redirect($success ? '/desainer/template-desain' : '/desainer/template-desain/' . $id . '/edit');
}

function delete_action($params)
{
  $db = db_connect();
  $id = (int) ($params['id'] ?? 0);

  if (!$db || $id <= 0) {
    flash_message('error', 'Error', 'Gagal terhubung atau ID tidak valid.');
    return redirect('/desainer/template-desain');
  }

  $template = template_desain_get_by_id($db, $id);
  if (!$template) {
    flash_message('error', 'Gagal', 'Template desain tidak ditemukan.');
    return redirect('/desainer/template-desain');
  }

  if (!empty($template['template_desain'])) {
    delete_storage_file($template['template_desain']);
  }

  if (template_desain_delete($db, $id)) {
    flash_message('success', 'Berhasil', 'Template desain berhasil dihapus.');
  } else {
    flash_message('error', 'Gagal', 'Gagal menghapus template dari database.');
  }

  return redirect('/desainer/template-desain');
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

  $result = template_desain_get_all_paginated($db, $options);
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
