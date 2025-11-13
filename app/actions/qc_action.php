<?php
// app/actions/qc_action.php
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../data/qc_data.php';
require_once __DIR__ . '/../data/order_data.php';

const BOBOT_QC = [
  'check_cover_material' => 10,
  'check_cover_fisik' => 10,
  'check_jilid_kekuatan' => 10,
  'check_laminasi_kerapian' => 5,
  'check_cover_posisi' => 5,
  'check_cetak_ketajaman' => 10,
  'check_cetak_warna' => 10,
  'check_cetak_kecerahan' => 5,
  'check_cetak_kebersihan' => 5,
  'check_halaman_urutan' => 10,
  'check_halaman_kelengkapan' => 10,
  'check_pemotongan_presisi' => 5,
  'check_halaman_nomor' => 5
];

function ajax_get_qc_tab($params)
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

  $allowed_roles = ['project_officer', 'manajer_produksi', 'tim_percetakan'];
  if (!in_array($user['role'], $allowed_roles)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit();
  }

  $items = qc_get_by_order_id($db, $id_order_produksi);
  $latest_status = qc_get_latest_status($db, $id_order_produksi);

  $data = [
    'items' => $items, // Riwayat QC
    'order' => $order,
    'is_lolos' => ($latest_status === 'Lolos')
  ];

  view('order.partials._qc_tab', $data);
  exit();
}


function create_action($params)
{
  $db = db_connect();
  $id_order_produksi = (int) ($params['id_order'] ?? 0);
  $order = order_get_by_id_for_detail($db, $id_order_produksi);

  if (!$order) {
    flash_message('error', 'Error', 'Order tidak ditemukan.');
    return redirect('/dashboard');
  }

  $latest_status = qc_get_latest_status($db, $id_order_produksi);
  if ($latest_status === 'Lolos') {
    flash_message('info', 'Sudah Lolos', 'Order ini sudah lolos QC dan tidak bisa di-QC ulang.');

    return redirect('/order/' . $id_order_produksi . '/detail');
  }

  $data = [
    'page_title' => 'Formulir QC: ' . $order['nomor_order'],
    'active_menu' => 'order',
    'order' => $order,
    'bobot_qc' => BOBOT_QC // Kirim data bobot ke view
  ];

  view('order.qc.create', $data);
}

/**
 * Menyimpan hasil QC Checklist baru.
 */
function store_action($params)
{
  $db = db_connect();
  $user = auth();
  $id_order_produksi = (int) ($params['id_order'] ?? 0);
  $order = order_get_by_id_for_detail($db, $id_order_produksi);

  if (!$order) {
    flash_message('error', 'Error', 'Order tidak ditemukan.');
    return redirect('/dashboard');
  }

  try {
    // --- 1. Kalkulasi Bobot & Persentase ---
    $skor_didapat = 0;
    $total_skor = array_sum(BOBOT_QC);
    $data_checklist = [];

    foreach (BOBOT_QC as $key => $bobot) {
      $data_checklist[$key] = 0; // Set default 0 (Gagal)
      if (isset($_POST[$key]) && $_POST[$key] == '1') {
        $skor_didapat += $bobot;
        $data_checklist[$key] = 1; // Set 1 (Lolos)
      }
    }
    $persentase = ($total_skor > 0) ? ($skor_didapat / $total_skor) * 100 : 0;

    // --- 2. Tentukan Status Kelolosan (Sesuai Aturan Anda) ---
    $jumlah_cacat = (int)($_POST['jumlah_cacat'] ?? 0);
    $status_kelolosan = '';

    if ($jumlah_cacat > 0) {
      $status_kelolosan = 'Gagal Total'; // Cacat > 0 = Gagal Total
    } elseif ($persentase > 90) {
      $status_kelolosan = 'Lolos'; // Lolos Sempurna
    } elseif ($persentase > 75) {
      $status_kelolosan = 'Gagal Sebagian';
    } else {
      $status_kelolosan = 'Gagal Total';
    }

    // --- 3. Handle Upload Bukti Foto (jika ada) ---
    $buktiFotoUrl = null;
    if (isset($_FILES['bukti_foto']) && $_FILES['bukti_foto']['error'] === UPLOAD_ERR_OK) {
      $uploadResult = handle_file_upload($_FILES['bukti_foto'], 'qc', 'images');
      if ($uploadResult['success']) {
        $buktiFotoUrl = $uploadResult['url'];
      }
    }

    // --- 4. Generate Batch Number ---
    $batchInfo = qc_generate_batch_number($db);

    // --- 5. Siapkan Data untuk Database ---
    $data = $data_checklist; // Mulai dengan data checklist
    $data['id_order_produksi'] = $id_order_produksi;
    $data['id_user'] = $user['id'];
    $data['batch_number'] = $batchInfo['batch_number'];
    $data['sequence'] = $batchInfo['sequence'];
    $data['tanggal'] = date('Y-m-d H:i:s');
    $data['jumlah_sampel_diperiksa'] = (int)($_POST['jumlah_sampel_diperiksa'] ?? 0);
    $data['persentase_lolos'] = $persentase;
    $data['status_kelolosan'] = $status_kelolosan;
    $data['jenis_cacat'] = $_POST['jenis_cacat'] ?? null;
    $data['jumlah_cacat'] = $jumlah_cacat;
    $data['bukti_foto'] = $buktiFotoUrl;
    $data['catatan_qc'] = $_POST['catatan_qc'] ?? null;

    // --- 6. Simpan ke DB ---
    if (!qc_create($db, $data)) {
      throw new Exception("Gagal menyimpan data QC ke database.");
    }

    flash_message('success', 'Berhasil', "Hasil QC (Batch: {$batchInfo['batch_number']}) telah disimpan. Status: {$status_kelolosan}");
  } catch (Exception $e) {
    flash_message('error', 'Gagal', $e->getMessage());
    return redirect('/order/' . $id_order_produksi . '/qc/create');
  }

  // PERBAIKAN: Tanda kutip ekstra dihapus
  return redirect('/order/' . $id_order_produksi . '/detail');
}

/**
 * Menampilkan halaman detail untuk satu hasil QC (read-only).
 */
function detail_action($params)
{
  $db = db_connect();
  $id_qc = (int) ($params['id_qc'] ?? 0);

  $qc_data = qc_get_by_id($db, $id_qc);
  if (!$qc_data) {
    flash_message('error', 'Error', 'Data QC tidak ditemukan.');
    return redirect('/dashboard');
  }

  $order = order_get_by_id_for_detail($db, $qc_data['id_order_produksi']);

  $data = [
    'page_title' => 'Detail QC: ' . $qc_data['batch_number'],
    'active_menu' => 'order',
    'qc' => $qc_data,
    'order' => $order,
    'bobot_qc' => BOBOT_QC // Kirim bobot untuk ditampilkan
  ];

  view('order.qc.detail', $data);
}

// PERBAIKAN: Kurung kurawal '}' ekstra dihapus dari sini