<?php
// app/actions/timeline_action.php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../data/timeline_data.php';
require_once __DIR__ . '/../data/user_data.php';
require_once __DIR__ . '/../data/order_data.php'; // (Penting)

function get_order_from_id_int(mysqli $db, int $id_order_produksi): ?array
{
  return order_get_by_id_for_detail($db, $id_order_produksi);
}

function ajax_get_timeline_tab($params)
{
  $db = db_connect();
  $id_order_produksi = (int) ($params['id'] ?? 0); // Ambil ID integer (auto-increment)

  $order = order_get_by_id_for_detail($db, $id_order_produksi);
  if (!$order) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Data order tidak ditemukan.']);
    exit();
  }

  // Ambil task timeline menggunakan ID (INT)
  $items = timeline_get_by_order_id($db, $id_order_produksi);

  $staff_desain = user_get_all_by_role($db, 'desainer');
  $staff_cetak = user_get_all_by_role($db, 'tim_percetakan');

  $data = [
    'items' => $items,
    'order' => $order, // Kirim data order lengkap
  ];

  view('order.partials._timeline_tab', $data);
  exit();
}

// ==========================================================
//  AKSI UNTUK CRUD HALAMAN PENUH
// ==========================================================

/**
 * Menampilkan form untuk membuat task timeline baru.
 * Dipanggil oleh: GET /order/{id}/timeline/create
 */
function create_action($params)
{
  $db = db_connect();
  $id_order_produksi = (int) ($params['id'] ?? 0);
  $order = order_get_by_id_for_detail($db, $id_order_produksi);

  if (!$order) {
    flash_message('error', 'Error', 'Order tidak ditemukan.');
    return redirect('/dashboard');
  }

  $staff_desain = user_get_all_by_role($db, 'desainer');
  $staff_cetak = user_get_all_by_role($db, 'tim_percetakan');
  $staff_po = user_get_all_by_role($db, 'project_officer');

  $data = [
    'page_title' => 'Tambah Task Timeline',
    'active_menu' => 'order',
    'order' => $order,
    'staff_list' => array_merge($staff_desain, $staff_cetak, $staff_po),
    'status_options' => ['Ditugaskan', 'Dalam Proses', 'Selesai']
  ];

  view('order.timeline.create', $data);
}

/**
 * Menyimpan task timeline baru.
 * Dipanggil oleh: POST /order/{id}/timeline/store
 */
function store_action($params)
{
  $db = db_connect();
  $id_order_produksi = (int) ($params['id'] ?? 0);

  try {
    $data = [
      'id_order_produksi' => $id_order_produksi, // Simpan ID (INT)
      'id_user' => (int)$_POST['id_user'],
      'judul' => $_POST['judul'],
      'deskripsi' => $_POST['deskripsi'],
      'status_timeline' => $_POST['status_timeline'],
      'deadline' => $_POST['deadline']
    ];

    if (empty($data['judul']) || empty($data['id_user']) || empty($data['deadline'])) {
      throw new Exception("Judul, User, dan Deadline wajib diisi.");
    }

    if (!timeline_create($db, $data)) throw new Exception("Gagal menyimpan ke database.");

    flash_message('success', 'Berhasil', 'Task timeline baru berhasil ditambahkan.');
  } catch (Exception $e) {
    flash_message('error', 'Gagal', $e->getMessage());
    return redirect('/order/' . $id_order_produksi . '/timeline/create');
  }

  return redirect('/order/' . $id_order_produksi . '/detail');
}

/**
 * Menampilkan form untuk mengedit task.
 * Dipanggil oleh: GET /timeline/{id_task}/edit
 */
function edit_action($params)
{
  $db = db_connect();
  $id_task = (int) ($params['id_task'] ?? 0);

  $task = timeline_get_by_id($db, $id_task);
  if (!$task) {
    flash_message('error', 'Error', 'Task tidak ditemukan.');
    return redirect('/dashboard');
  }

  // Ambil data order induknya menggunakan ID (INT)
  $order = order_get_by_id_for_detail($db, $task['id_order_produksi']);
  if (!$order) {
    flash_message('error', 'Error', 'Order induk untuk task ini tidak ditemukan.');
    return redirect('/dashboard');
  }

  $staff_desain = user_get_all_by_role($db, 'desainer');
  $staff_cetak = user_get_all_by_role($db, 'tim_percetakan');
  $staff_po = user_get_all_by_role($db, 'project_officer');

  $data = [
    'page_title' => 'Edit Task Timeline',
    'active_menu' => 'order',
    'task' => $task,
    'order' => $order,
    'staff_list' => array_merge($staff_desain, $staff_cetak, $staff_po),
    'status_options' => ['Ditugaskan', 'Dalam Proses', 'Selesai']
  ];

  view('order.timeline.edit', $data);
}

function update_action($params)
{
  $db = db_connect();
  $id_task = (int) ($params['id_task'] ?? 0);

  $task = timeline_get_by_id($db, $id_task);
  if (!$task) {
    flash_message('error', 'Gagal', 'Task tidak ditemukan');
  }

  try {
    $data = [
      'id_user' => (int)$_POST['id_user'],
      'judul' => $_POST['judul'],
      'deskripsi' => $_POST['deskripsi'],
      'status_timeline' => $_POST['status_timeline'],
      'deadline' => $_POST['deadline']
    ];

    if (!timeline_update($db, $id_task, $data)) {
      throw new Exception("Gagal memperbarui database.");
    }

    flash_message('success', 'Berhasil', 'Task timeline berhasil diperbarui.');
  } catch (Exception $e) {
    flash_message('error', 'Gagal', $e->getMessage());
    return redirect('/timeline/' . $id_task . '/edit');
  }

  return redirect('/order/' . $task['id_order_produksi'] . '/detail');
}

function delete_action($params)
{
  $db = db_connect();
  $id_task = (int) ($params['id_task'] ?? 0);

  $task = timeline_get_by_id($db, $id_task);
  if (!$task) {
    flash_message('error', 'Gagal', 'Task tidak ditemukan');
  }

  try {
    if (!timeline_delete($db, $id_task)) {
      throw new Exception("Gagal menghapus task.");
    }
    flash_message('success', 'Berhasil', 'Task timeline berhasil dihapus.');
  } catch (Exception $e) {
    flash_message('error', 'Gagal', $e->getMessage());
  }

  return redirect('/order/' . $task['id_order_produksi'] . '/detail');
}

function ajax_update_status_action()
{
  header('Content-Type: application/json');
  $db = db_connect();
  $user = auth();

  if (!$db || !$user) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Otentikasi gagal.']);
    exit();
  }

  try {
    $id_timeline = (int) ($_POST['id_timeline'] ?? 0);
    $status = (string) ($_POST['status_timeline'] ?? '');

    if ($id_timeline <= 0 || empty($status)) {
      http_response_code(400);
      throw new Exception('ID Timeline atau Status tidak valid.');
    }

    if (timeline_update_status($db, $id_timeline, $status) > 0) {
      echo json_encode([
        'success' => true,
        'message' => 'Status timeline berhasil diperbarui.'
      ]);
    } else {
      throw new Exception('Gagal memperbarui database (data mungkin sama).');
    }
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
  }

  exit();
}
