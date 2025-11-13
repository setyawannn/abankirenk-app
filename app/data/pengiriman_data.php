<?php
// app/data/pengiriman_data.php

require_once __DIR__ . '/../../core/database.php';

/**
 * Mengambil semua riwayat pengiriman untuk satu order.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_order_produksi ID (INT) dari order.
 * @return array Daftar riwayat pengiriman.
 */
function pengiriman_get_by_order_id(mysqli $mysqli, int $id_order_produksi): array
{
  $sql = "SELECT p.*, u.nama AS nama_user_input
            FROM pengiriman p
            LEFT JOIN users u ON p.id_user = u.id_user
            WHERE p.id_order_produksi = ?
            ORDER BY p.tanggal_buat DESC";

  $result = db_query($mysqli, $sql, [$id_order_produksi]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil satu data pengiriman berdasarkan ID-nya.
 */
function pengiriman_get_by_id(mysqli $mysqli, int $id_pengiriman)
{
  $sql = "SELECT p.*, u.nama AS nama_user_input
            FROM pengiriman p
            LEFT JOIN users u ON p.id_user = u.id_user
            WHERE p.id_pengiriman = ?";

  $result = db_query($mysqli, $sql, [$id_pengiriman]);
  return $result ? $result->fetch_assoc() : null;
}

/**
 * Membuat data pengiriman baru.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param array $data Data (id_order, id_user, ekspedisi, no_resi, tgl_buat, tracking_url).
 * @return int ID pengiriman baru.
 */
function pengiriman_create(mysqli $mysqli, array $data): int
{
  $sql = "INSERT INTO pengiriman (id_order_produksi, id_user, ekspedisi, no_resi, tanggal_buat, tracking_url)
            VALUES (?, ?, ?, ?, ?, ?)";

  $params = [
    $data['id_order_produksi'],
    $data['id_user'],
    $data['ekspedisi'],
    $data['no_resi'],
    $data['tanggal_buat'],
    $data['tracking_url']
  ];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}

/**
 * Menghapus data pengiriman.
 */
function pengiriman_delete(mysqli $mysqli, int $id_pengiriman): int
{
  $sql = "DELETE FROM pengiriman WHERE id_pengiriman = ?";
  $affectedRows = db_query($mysqli, $sql, [$id_pengiriman]);
  return (int) $affectedRows;
}
