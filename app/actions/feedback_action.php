<?php
// app/actions/feedback_action.php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../data/feedback_data.php';
require_once __DIR__ . '/../data/order_data.php';
// (Kita tidak perlu tiket_data atau pengiriman_data lagi)

// ==========================================================
//  AKSI UNTUK TAB (AJAX)
// ==========================================================
function ajax_get_feedback_tab($params)
{
  $db = db_connect();
  $user = auth();
  $id_order_produksi = (int) ($params['id'] ?? 0);

  $order = order_get_by_id_for_detail($db, $id_order_produksi);
  if (!$order) {
    http_response_code(404);
    echo json_encode(['error' => 'Data order tidak ditemukan']);
    exit();
  }

  $user_role = $user['role'];
  $id_user = $user['id'];

  $is_core_staff = in_array($user_role, ['project_officer', 'manajer_produksi', 'manajer_marketing']);
  $is_owner = ($user_role == 'klien' && $id_user == $order['id_klien']);

  if (!$is_core_staff && !$is_owner) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit();
  }

  $feedback = feedback_get_by_order_id($db, $id_order_produksi);

  $is_locked = false;
  if ($feedback) {
    $created_timestamp = strtotime($feedback['created_at']);
    if ((time() - $created_timestamp) > 86400) { // 24 jam
      $is_locked = true;
    }
  }

  $is_order_selesai = ($order['status_order'] == 'selesai');

  $data = [
    'feedback' => $feedback,
    'order' => $order,
    'is_locked' => $is_locked,
    'is_owner' => $is_owner,
    'is_order_selesai' => $is_order_selesai
  ];

  view('order.partials._feedback_tab', $data);
  exit();
}

function store_action($params)
{
  $db = db_connect();
  $user = auth();

  $id_order_produksi = (int) ($_POST['id_order_produksi'] ?? 0);
  $id_feedback = (int) ($_POST['id_feedback'] ?? 0);

  $order = order_get_by_id_for_detail($db, $id_order_produksi);

  if (!$order || $user['role'] !== 'klien' || $order['id_klien'] !== $user['id']) {
    flash_message('error', 'Error', 'Otorisasi gagal.');
    return redirect('/dashboard');
  }

  if ($order['status_order'] !== 'selesai') {
    flash_message('error', 'Error', 'Anda hanya bisa memberi review jika status order sudah "Selesai".');
    return redirect('/order/' . $id_order_produksi . '/detail#feedback');
  }

  try {
    $data = [
      'id_order_produksi' => $id_order_produksi,
      'id_user' => $user['id'],
      'rating' => (int)$_POST['rating'],
      'komentar' => $_POST['komentar'],
    ];

    if (empty($data['rating']) || empty($data['komentar'])) {
      throw new Exception("Rating (Bintang) dan Komentar wajib diisi.");
    }

    if ($id_feedback > 0) {
      $existing_feedback = feedback_get_by_id($db, $id_feedback);
      if (!$existing_feedback || $existing_feedback['id_user'] != $user['id']) {
        throw new Exception("Otorisasi update gagal.");
      }

      $created_timestamp = strtotime($existing_feedback['created_at']);
      if ((time() - $created_timestamp) > 86400) {
        throw new Exception("Waktu edit (24 jam) sudah habis.");
      }

      if (!feedback_update($db, $id_feedback, $data)) {
        throw new Exception("Gagal memperbarui feedback.");
      }
      flash_message('success', 'Berhasil', 'Feedback Anda berhasil diperbarui.');
    } else {
      if (!feedback_create($db, $data)) {
        throw new Exception("Gagal menyimpan feedback baru.");
      }
      flash_message('success', 'Berhasil', 'Terima kasih atas feedback Anda!');
    }
  } catch (Exception $e) {
    flash_message('error', 'Gagal', $e->getMessage());
  }

  return redirect('/order/' . $id_order_produksi . '/detail#feedback');
}
