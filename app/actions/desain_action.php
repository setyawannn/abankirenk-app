<?php
// app/actions/desain_action.php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../data/desain_data.php';
require_once __DIR__ . '/../data/order_data.php';
require_once __DIR__ . '/../data/timeline_data.php'; // (Penting untuk otorisasi)

/**
 * Helper Otorisasi Internal
 * Mengecek apakah user boleh meng-upload/melihat tab desain
 */
function can_user_access_desain(mysqli $db, array $user, array $order): bool
{
  $user_role = $user['role'];

  // 1. Core staff selalu bisa
  $is_core_staff = in_array($user_role, [
    'project_officer',
    'manajer_produksi',
    'manajer_marketing'
  ]);
  if ($is_core_staff) {
    return true;
  }

  // 2. Desainer HANYA jika ditugaskan
  if ($user_role == 'desainer') {
    return timeline_is_user_assigned_to_order_by_nomor($db, $user['id'], $order['id_order_produksi']);
  }

  // 3. Role lain (termasuk Klien) tidak bisa
  return false;
}

/**
 * Mengambil data desain (untuk Tab AJAX).
 * Dipanggil oleh: GET /ajax/order/{id}/desain
 */
function ajax_get_desain_tab($params)
{
  $db = db_connect();
  $user = auth();
  $id_order_produksi = (int) ($params['id'] ?? 0);

  $order = order_get_by_id_for_detail($db, $id_order_produksi);
  if (!$order) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Data order tidak ditemukan.']);
    exit();
  }

  // === OTORISASI (Aturan Anda) ===
  if (!can_user_access_desain($db, $user, $order)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit();
  }

  // Ambil data desain
  $items = desain_get_by_order_id($db, $order['id_order_produksi']);
  $latest_design = null;
  if (!empty($items)) {
    $latest_design = array_pop($items); // Ambil item terakhir (terbaru)
  }
  $design_history = $items; // Sisanya adalah riwayat

  $data = [
    'order' => $order,
    'latest_design' => $latest_design,
    'design_history' => $design_history,
    'can_upload' => true // (Jika sudah lolos otorisasi di atas, pasti bisa)
  ];

  view('order.partials._desain_tab', $data);
  exit();
}


function store_action($params)
{
  $db = db_connect();
  $user = auth();
  $id_order_produksi = (int) ($params['id'] ?? 0);

  $order = order_get_by_id_for_detail($db, $id_order_produksi);

  if (!$db || !$user || !$order) {
    flash_message('error', 'Error', 'Data order tidak valid.');
    return redirect('/dashboard');
  }

  try {
    if (!can_user_access_desain($db, $user, $order)) {
      throw new Exception("Anda tidak memiliki izin untuk meng-upload ke order ini.");
    }

    if (!isset($_FILES['file_desain']) || $_FILES['file_desain']['error'] !== UPLOAD_ERR_OK) {
      throw new Exception("File PDF desain wajib di-upload.");
    }

    $uploadResult = handle_file_upload($_FILES['file_desain'], 'desain', 'documents');

    if (!$uploadResult['success']) {
      throw new Exception("Upload Gagal: " . $uploadResult['message']);
    }

    $data = [
      'id_order_produksi' => $order['id_order_produksi'],
      'id_user' => $user['id'],
      'file_url' => $uploadResult['url']
    ];

    if (!desain_create($db, $data)) {
      throw new Exception("Gagal menyimpan data desain ke database.");
    }

    flash_message('success', 'Berhasil', 'File desain baru berhasil di-upload.');
  } catch (Exception $e) {
    flash_message('error', 'Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
  }

  return redirect('/order/' . $id_order_produksi . '/detail');
}
