<?php
// app/data/feedback_data.php

require_once __DIR__ . '/../../core/database.php';

/**
 * Mengambil data feedback untuk satu order (untuk tab).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_order_produksi ID (INT) dari order.
 * @return array|null Data feedback (hanya 1 review per order).
 */
function feedback_get_by_order_id(mysqli $mysqli, int $id_order_produksi)
{
  $sql = "SELECT 
                f.*, 
                u.nama AS nama_klien,
                DATE_FORMAT(f.created_at, '%d %M %Y %H:%i') AS formatted_created_at,
                DATE_FORMAT(f.updated_at, '%d %M %Y %H:%i') AS formatted_updated_at
            FROM feedback f
            LEFT JOIN users u ON f.id_user = u.id_user
            WHERE f.id_order_produksi = ?
            LIMIT 1"; // Asumsi hanya 1 feedback per order

  $result = db_query($mysqli, $sql, [$id_order_produksi]);
  return $result ? $result->fetch_assoc() : null;
}

function feedback_get_by_id(mysqli $mysqli, int $id_feedback)
{
  $sql = "SELECT * FROM feedback WHERE id_feedback = ?";

  $result = db_query($mysqli, $sql, [$id_feedback]);
  return $result ? $result->fetch_assoc() : null;
}

/**
 * Membuat feedback/review baru.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param array $data Data (id_order_produksi, id_user, rating, komentar).
 * @return int ID feedback baru.
 */
function feedback_create(mysqli $mysqli, array $data): int
{
  $sql = "INSERT INTO feedback (id_order_produksi, id_user, rating, komentar, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())";

  $params = [
    $data['id_order_produksi'],
    $data['id_user'],
    $data['rating'],
    $data['komentar']
  ];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}

/**
 * Memperbarui feedback/review.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_feedback ID feedback yang akan di-update.
 * @param array $data Data (rating, komentar).
 * @return int Jumlah baris terpengaruh.
 */
function feedback_update(mysqli $mysqli, int $id_feedback, array $data): int
{
  // 'updated_at' akan otomatis di-handle oleh database
  $sql = "UPDATE feedback SET
                rating = ?,
                komentar = ?
            WHERE id_feedback = ?";

  $params = [
    $data['rating'],
    $data['komentar'],
    $id_feedback
  ];

  $affectedRows = db_query($mysqli, $sql, $params);
  return (int) $affectedRows;
}
