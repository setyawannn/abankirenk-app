<?php
// app/actions/pengiriman_action.php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../data/pengiriman_data.php';
require_once __DIR__ . '/../data/order_data.php';

/**
 * Helper untuk membuat URL tracking manual.
 * Ini adalah pengganti API eksternal.
 *
 * @param string $ekspedisi Nama ekspedisi (e.g., 'JNE', 'Sicepat').
 * @param string $no_resi Nomor resi.
 * @return string|null URL pelacakan lengkap.
 */
function generate_tracking_url(string $ekspedisi, string $no_resi): ?string
{
  $ekspedisi = strtolower($ekspedisi);

  // Kumpulan website resmi (bisa ditambahkan)
  $tracking_map = [
    'jne' => 'https://www.jne.co.id/id/tracking/trace?awb=',
    'sicepat' => 'https://sicepat.com/checkAwb?awb=',
    'tiki' => 'https://tiki.id/id/tracking?q=',
    'pos' => 'https://www.posindonesia.co.id/id/tracking?q=',
    'j&t' => 'https://jet.co.id/track?txtexpress=',
    'wahana' => 'https://www.wahana.com/lacak?key=',
    'anteraja' => 'https://anteraja.id/tracking?tracking_code=',
    'ninja' => 'https://www.ninjaxpress.co/id-id/tracking?id=',
    'shopee' => 'https://spx.co.id/track?',
    'spx' => 'https://spx.co.id/track?'
  ];

  foreach ($tracking_map as $key => $url) {
    if (strpos($ekspedisi, $key) !== false) {
      return $url . urlencode($no_resi);
    }
  }

  return null; // Tidak ditemukan
}

// ==========================================================
//  AKSI UNTUK TAB (AJAX)
// ==========================================================
function ajax_get_pengiriman_tab($params)
{
  $db = db_connect();
  $user = auth();
  $id_order_produksi = (int) ($params['id'] ?? 0);

  if (!$db || !$user || $id_order_produksi <= 0) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Akses ditolak atau ID order tidak valid.']);
    exit();
  }

  $order = order_get_by_id_for_detail($db, $id_order_produksi);
  if (!$order) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Data order tidak ditemukan.']);
    exit();
  }

  // ==========================================================
  //  PERBAIKAN OTORISASI (Sesuai Permintaan Anda)
  // ==========================================================

  // 1. Tentukan role internal yang diizinkan
  $allowed_roles_internal = [
    'project_officer',
    'manajer_produksi',
    'tim_percetakan',
    'customer_service'
  ];
  $is_internal_staff = in_array($user['role'], $allowed_roles_internal);

  // 2. Cek apakah user adalah pemilik order (Klien)
  $is_owner = ($user['role'] == 'klien' && $user['id'] == $order['id_klien']);

  // 3. Tolak jika BUKAN staf internal DAN BUKAN pemilik
  if (!$is_internal_staff && !$is_owner) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit();
  }
  // ==========================================================

  // (Relasi di DB Anda sudah benar menggunakan INT, jadi ini akan bekerja)
  $items = pengiriman_get_by_order_id($db, $id_order_produksi);

  $data = [
    'items' => $items, // Riwayat Pengiriman
    'order' => $order
  ];

  view('order.partials._pengiriman_tab', $data);
  exit();
}

// ==========================================================
//  AKSI UNTUK CRUD HALAMAN PENUH
// ==========================================================

/**
 * Menampilkan form untuk membuat data pengiriman baru.
 */
function create_action($params)
{
  $db = db_connect();
  $id_order_produksi = (int) ($params['id_order'] ?? 0);
  $order = order_get_by_id_for_detail($db, $id_order_produksi);

  if (!$order) {
    flash_message('error', 'Error', 'Order tidak ditemukan.');
    return redirect('/dashboard');
  }

  $data = [
    'page_title' => 'Input Pengiriman: ' . $order['nomor_order'],
    'active_menu' => 'order',
    'order' => $order
  ];

  view('order.pengiriman.create', $data);
}

/**
 * Menyimpan data pengiriman baru.
 */
function store_action($params)
{
  $db = db_connect();
  $user = auth();
  $id_order_produksi = (int) ($params['id_order'] ?? 0);

  if (!$user || !$db || $id_order_produksi <= 0) {
    flash_message('error', 'Error', 'Data tidak valid.');
    return redirect('/dashboard');
  }

  try {
    $ekspedisi = $_POST['ekspedisi'];
    $no_resi = $_POST['no_resi'];
    $tanggal_buat = $_POST['tanggal_buat'];

    if (empty($ekspedisi) || empty($no_resi) || empty($tanggal_buat)) {
      throw new Exception("Ekspedisi, Nomor Resi, dan Tanggal Kirim wajib diisi.");
    }

    // Buat tracking URL
    $tracking_url = generate_tracking_url($ekspedisi, $no_resi);

    $data = [
      'id_order_produksi' => $id_order_produksi,
      'id_user' => $user['id'],
      'ekspedisi' => $ekspedisi,
      'no_resi' => $no_resi,
      'tanggal_buat' => $tanggal_buat,
      'tracking_url' => $tracking_url
    ];

    if (!pengiriman_create($db, $data)) {
      throw new Exception("Gagal menyimpan data pengiriman ke database.");
    }

    flash_message('success', 'Berhasil', 'Data pengiriman baru berhasil disimpan.');
  } catch (Exception $e) {
    flash_message('error', 'Gagal', $e->getMessage());
    return redirect('/order/' . $id_order_produksi . '/pengiriman/create');
  }

  return redirect('/order/' . $id_order_produksi . '/detail');
}

/**
 * Menampilkan halaman detail untuk satu pengiriman (read-only).
 */
function detail_action($params)
{
  $db = db_connect();
  $id_pengiriman = (int) ($params['id_pengiriman'] ?? 0);

  $pengiriman = pengiriman_get_by_id($db, $id_pengiriman);
  if (!$pengiriman) {
    flash_message('error', 'Error', 'Data pengiriman tidak ditemukan.');
    return redirect('/dashboard');
  }

  $order = order_get_by_id_for_detail($db, $pengiriman['id_order_produksi']);
  if (!$order) {
    flash_message('error', 'Error', 'Order induk tidak ditemukan.');
    return redirect('/dashboard');
  }

  $data = [
    'page_title' => 'Detail Pengiriman: ' . $pengiriman['no_resi'],
    'active_menu' => 'order',
    'pengiriman' => $pengiriman,
    'order' => $order
  ];

  view('order.pengiriman.detail', $data);
}

/**
 * Menghapus data pengiriman (hanya Manajer).
 */
function delete_action($params)
{
  $db = db_connect();
  $id_pengiriman = (int) ($params['id_pengiriman'] ?? 0);

  $pengiriman = pengiriman_get_by_id($db, $id_pengiriman);
  if (!$pengiriman) {
    flash_message('error', 'Error', 'Data pengiriman tidak ditemukan.');
    return redirect('/dashboard');
  }

  try {
    if (!pengiriman_delete($db, $id_pengiriman)) {
      throw new Exception("Gagal menghapus data pengiriman.");
    }
    flash_message('success', 'Berhasil', 'Data pengiriman berhasil dihapus.');
  } catch (Exception $e) {
    flash_message('error', 'Gagal', $e->getMessage());
  }

  return redirect('/order/' . $pengiriman['id_order_produksi'] . '/detail');
}

function konfirmasi_action($params)
{
  $db = db_connect();
  $user = auth(); // Klien yang sedang login
  $id_pengiriman = (int) ($params['id_pengiriman'] ?? 0);

  if (!$db || !$user || $id_pengiriman <= 0) {
    flash_message('error', 'Error', 'Permintaan tidak valid.');
    return redirect('/dashboard');
  }

  try {
    // 1. Dapatkan data pengiriman
    $pengiriman = pengiriman_get_by_id($db, $id_pengiriman);
    if (!$pengiriman) {
      throw new Exception("Data pengiriman tidak ditemukan.");
    }

    // 2. Dapatkan data order induk
    $order = order_get_by_id_for_detail($db, $pengiriman['id_order_produksi']);
    if (!$order) {
      throw new Exception("Data order induk tidak ditemukan.");
    }

    // 3. OTORISASI:
    // Cek apakah user adalah Klien DAN Klien tersebut adalah pemilik order ini
    if ($user['role'] !== 'klien' || $user['id'] !== $order['id_klien']) {
      throw new Exception("Anda tidak memiliki izin untuk melakukan konfirmasi pada order ini.");
    }

    // 4. Lakukan Update
    if (!pengiriman_konfirmasi_diterima($db, $id_pengiriman)) {
      throw new Exception("Gagal memperbarui status penerimaan.");
    }

    flash_message('success', 'Berhasil', 'Konfirmasi penerimaan paket telah disimpan. Terima kasih!');
  } catch (Exception $e) {
    flash_message('error', 'Gagal', $e->getMessage());
  }

  // 5. Redirect kembali ke halaman detail pengiriman
  return redirect('/pengiriman/' . $id_pengiriman . '/detail');
}
