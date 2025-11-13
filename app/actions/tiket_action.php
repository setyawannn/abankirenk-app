<?php
// app/actions/tiket_action.php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../data/tiket_data.php';
require_once __DIR__ . '/../data/order_data.php';

/**
 * Helper Otorisasi Internal
 * (Klien pemilik, CS, atau PO)
 */
function can_user_access_tiket(array $user, array $tiket): bool
{
  $role = $user['role'];

  // 1. Klien pemilik tiket
  // (Perbaikan: $tiket['id_user'] adalah ID klien, $tiket['id_klien'] dari join order)
  if ($role == 'klien' && $user['id'] == $tiket['id_user']) {
    return true;
  }

  // 2. Staf yang diizinkan (CS atau PO)
  if (in_array($role, ['customer_service', 'project_officer'])) {
    return true;
  }

  return false;
}

// ==========================================================
//  AKSI UNTUK TAB (AJAX)
// ==========================================================
function ajax_get_tiket_tab($params)
{
  $db = db_connect();
  $user = auth();
  $id_order_produksi = (int) ($params['id'] ?? 0);

  $order = order_get_by_id_for_detail($db, $id_order_produksi);
  if (!$order) { /*... error 404 ...*/
  }

  // Otorisasi: Hanya Klien pemilik, CS, dan PO
  $is_owner = ($user['role'] == 'klien' && $user['id'] == $order['id_klien']);
  $is_staff = in_array($user['role'], ['customer_service', 'project_officer']);

  if (!$is_owner && !$is_staff) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit();
  }

  $items = tiket_get_by_order_id($db, $id_order_produksi);

  $data = [
    'items' => $items,
    'order' => $order
  ];

  view('order.partials._tiket_tab', $data);
  exit();
}

// ==========================================================
//  AKSI CRUD (HALAMAN PENUH)
// ==========================================================

/**
 * Menampilkan form untuk KLIEN membuat tiket baru.
 */
function create_action($params)
{
  $db = db_connect();
  $user = auth();
  $id_order_produksi = (int) ($params['id_order'] ?? 0);

  // PERBAIKAN: Menggunakan id_user
  $completed_orders = order_get_completed_for_klien($db, $user['id']);

  $selected_order = null;
  if ($id_order_produksi > 0) {
    $selected_order = order_get_by_id_for_detail($db, $id_order_produksi);

    $is_completed = false;
    foreach ($completed_orders as $order) {
      if ($order['id_order_produksi'] == $id_order_produksi) {
        $is_completed = true;
        break;
      }
    }
    // PERBAIKAN: Otorisasi (pastikan $selected_order ada DAN milik user)
    if (!$selected_order || !$is_completed || $selected_order['id_klien'] != $user['id']) {
      flash_message('error', 'Error', 'Order tidak ditemukan atau belum selesai.');
      return redirect('/dashboard'); // (Redirect ke dashboard klien)
    }
  }

  $data = [
    'page_title' => 'Buat Tiket Komplain',
    'active_menu' => 'order',
    'completed_orders' => $completed_orders,
    'order' => $selected_order, // PERBAIKAN: Kirim sebagai 'order' bukan 'selected_order'
    'kategori_options' => ['keluhan', 'pertanyaan', 'lainnya']
  ];

  view('order.tiket.create', $data);
}

/**
 * Menyimpan tiket komplain baru dari KLIEN.
 */
function store_action($params)
{
  $db = db_connect();
  $user = auth(); // Klien
  $id_order_produksi = (int) ($_POST['id_order_produksi'] ?? 0);

  $order = order_get_by_id_for_detail($db, $id_order_produksi);
  if (!$order || $order['id_klien'] !== $user['id']) {
    flash_message('error', 'Error', 'Otorisasi gagal.');
    return redirect('/dashboard');
  }

  try {
    $batchInfo = tiket_generate_nomor_komplain($db);

    $data = [
      'id_order_produksi' => $id_order_produksi, // INT
      'id_user' => $user['id'],
      'nomor_komplain' => $batchInfo['nomor_komplain'],
      'sequence' => $batchInfo['sequence'],
      'kategori' => $_POST['kategori'],
      'deskripsi' => $_POST['deskripsi'], // (Dari WYSIWYG)
      'link_video' => $_POST['link_video'] ?? null
    ];

    if (empty($data['kategori']) || empty($data['deskripsi'])) {
      throw new Exception("Kategori dan Deskripsi wajib diisi.");
    }

    $newId = tiket_create($db, $data);
    if (!$newId) throw new Exception("Gagal menyimpan tiket.");

    flash_message('success', 'Berhasil', 'Tiket komplain Anda telah terkirim.');

    // Redirect ke halaman detail tiket yang baru dibuat
    return redirect('/tiket/' . $newId . '/detail');
  } catch (Exception $e) {
    flash_message('error', 'Gagal', $e->getMessage());
    return redirect('/order/' . $id_order_produksi . '/tiket/create');
  }
}

/**
 * Menampilkan halaman detail/balas (untuk Klien, CS, dan PO).
 */
function detail_action($params)
{
  $db = db_connect();
  $user = auth();
  $id_tiket = (int) ($params['id_tiket'] ?? 0);

  $tiket = tiket_get_by_id($db, $id_tiket);
  if (!$tiket) {
    flash_message('error', 'Error', 'Tiket tidak ditemukan.');
    return redirect('/dashboard');
  }

  // Otorisasi (Sesuai permintaan Anda)
  if (!can_user_access_tiket($user, $tiket)) {
    flash_message('error', 'Akses Ditolak', 'Anda tidak diizinkan melihat tiket ini.');
    return redirect('/dashboard');
  }

  $tiket['retur_badge'] = get_retur_badge($tiket['status_retur']);

  $data = [
    'page_title' => 'Detail Tiket: ' . $tiket['nomor_komplain'],
    'active_menu' => 'order',
    'tiket' => $tiket,
    'status_retur_options' => ['pending', 'disetujui', 'ditolak'],
  ];

  // Tentukan view berdasarkan role
  if ($user['role'] == 'customer_service') {
    view('order.tiket.edit', $data); // Form Balas
  } else {
    view('order.tiket.detail', $data); // Read-only
  }
}

/**
 * Menyimpan balasan tiket dari CS.
 */
function update_action($params)
{
  $db = db_connect();
  $user = auth(); // CS
  $id_tiket = (int) ($params['id_tiket'] ?? 0);

  if ($user['role'] !== 'customer_service') {
    flash_message('error', 'Akses Ditolak', 'Hanya Customer Service yang bisa membalas.');
    return redirect('/tiket/' . $id_tiket . '/detail');
  }

  try {
    $data = [
      'respon' => $_POST['respon'],
      'status_retur' => $_POST['status_retur'],
      'id_user_cs' => $user['id']
    ];

    // PERBAIKAN: Validasi 'status_tiket' DIHAPUS
    if (empty($data['respon']) || empty($data['status_retur'])) {
      throw new Exception("Status Retur dan Balasan wajib diisi.");
    }

    // Memanggil fungsi data yang sudah diperbarui
    if (!tiket_update_balasan($db, $id_tiket, $data)) {
      throw new Exception("Gagal memperbarui database.");
    }

    flash_message('success', 'Berhasil', 'Balasan tiket berhasil dikirim.');
  } catch (Exception $e) {
    flash_message('error', 'Gagal', $e->getMessage());
  }

  return redirect('/tiket/' . $id_tiket . '/detail');
}

function get_retur_badge($status)
{
  $baseClass = "px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full";
  $colors = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'disetujui' => 'bg-green-100 text-green-800',
    'ditolak' => 'bg-red-100 text-red-800'
  ];
  $class = $colors[$status] ?? 'bg-gray-100 text-gray-800';
  return "<span class='{$baseClass} {$class}'>" . ucfirst($status) . "</span>";
}
