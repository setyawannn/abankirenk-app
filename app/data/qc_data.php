<?php
// app/data/qc_data.php

require_once __DIR__ . '/../../core/database.php';

/**
 * Menghasilkan Batch Number QC (QCymdXXX).
 */
function qc_generate_batch_number(mysqli $mysqli): array
{
  $todayPrefix = 'QC' . date('ymd');

  $sqlSeq = "SELECT MAX(sequence) as max_seq FROM qc_checklist WHERE batch_number LIKE ?";
  $result = db_query($mysqli, $sqlSeq, ["{$todayPrefix}%"]);
  $row = $result->fetch_assoc();

  $nextSequence = ($row && $row['max_seq']) ? (int)$row['max_seq'] + 1 : 1;
  $batchNumber = $todayPrefix . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);

  return [
    'batch_number' => $batchNumber,
    'sequence' => $nextSequence
  ];
}

/**
 * Menyimpan data QC Checklist baru ke database.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param array $data Data lengkap dari action.
 * @return int ID dari QC Checklist baru.
 */
function qc_create(mysqli $mysqli, array $data): int
{
  // Daftar kolom sesuai tabel 'qc_checklist' baru
  $columns = [
    'id_order_produksi',
    'id_user',
    'batch_number',
    'sequence',
    'tanggal_qc',
    'jumlah_sampel_diperiksa',
    'check_cover_material',
    'check_cover_fisik',
    'check_jilid_kekuatan',
    'check_laminasi_kerapian',
    'check_cover_posisi',
    'check_cetak_ketajaman',
    'check_cetak_warna',
    'check_cetak_kecerahan',
    'check_cetak_kebersihan',
    'check_halaman_urutan',
    'check_halaman_kelengkapan',
    'check_pemotongan_presisi',
    'check_halaman_nomor',
    'persentase_lolos',
    'status_kelolosan',
    'jenis_cacat',
    'jumlah_cacat',
    'bukti_foto',
    'catatan_qc'
  ];

  $placeholders = implode(', ', array_fill(0, count($columns), '?'));
  $sql = "INSERT INTO qc_checklist (" . implode(', ', $columns) . ") VALUES ($placeholders)";

  $params = [];
  foreach ($columns as $col) {
    $params[] = $data[$col] ?? null;
  }

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}

/**
 * Mengambil semua riwayat QC untuk satu order.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_order_produksi ID (INT) dari order.
 * @return array Daftar riwayat QC.
 */
function qc_get_by_order_id(mysqli $mysqli, int $id_order_produksi): array
{
  $sql = "SELECT q.*, u.nama AS nama_pemeriksa
            FROM qc_checklist q
            LEFT JOIN users u ON q.id_user = u.id_user
            WHERE q.id_order_produksi = ?
            ORDER BY q.tanggal_qc DESC";

  $result = db_query($mysqli, $sql, [$id_order_produksi]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil data QC tunggal berdasarkan ID-nya (untuk halaman Detail).
 */
function qc_get_by_id(mysqli $mysqli, int $id_qc)
{
  $sql = "SELECT q.*, u.nama AS nama_pemeriksa
            FROM qc_checklist q
            LEFT JOIN users u ON q.id_user = u.id_user
            WHERE q.id_qc = ?";

  $result = db_query($mysqli, $sql, [$id_qc]);
  return $result ? $result->fetch_assoc() : null;
}

/**
 * Mengambil status_kelolosan TERBARU untuk sebuah order.
 */
function qc_get_latest_status(mysqli $mysqli, int $id_order_produksi): ?string
{
  $sql = "SELECT status_kelolosan FROM qc_checklist
            WHERE id_order_produksi = ?
            ORDER BY tanggal_qc DESC
            LIMIT 1";

  $result = db_query($mysqli, $sql, [$id_order_produksi]);
  $row = $result ? $result->fetch_assoc() : null;
  return $row ? $row['status_kelolosan'] : null;
}
