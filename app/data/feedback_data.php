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

function feedback_get_summary_stats(mysqli $mysqli): array
{
  $sql = "SELECT 
                COUNT(id_feedback) as total_feedback,
                AVG(rating) as avg_rating_all,
                
                (SELECT COUNT(id_feedback) FROM feedback 
                 WHERE created_at >= (NOW() - INTERVAL 30 DAY)) as total_new_30,
                 
                (SELECT AVG(rating) FROM feedback 
                 WHERE created_at >= (NOW() - INTERVAL 30 DAY)) as avg_rating_30
            FROM feedback";

  $result = db_query($mysqli, $sql);
  $row = $result->fetch_assoc();

  // Handle null values (jika belum ada data)
  return [
    'total_feedback' => (int) ($row['total_feedback'] ?? 0),
    'avg_rating_all' => (float) ($row['avg_rating_all'] ?? 0),
    'total_new_30'   => (int) ($row['total_new_30'] ?? 0),
    'avg_rating_30'  => (float) ($row['avg_rating_30'] ?? 0),
  ];
}

/**
 * Mengambil distribusi bintang (5, 4, 3, 2, 1).
 * Mengembalikan array [5 => count, 4 => count, ...].
 */
function feedback_get_star_distribution(mysqli $mysqli): array
{
  $sql = "SELECT rating, COUNT(id_feedback) as count 
            FROM feedback 
            GROUP BY rating 
            ORDER BY rating DESC";

  $result = db_query($mysqli, $sql);
  $data = $result->fetch_all(MYSQLI_ASSOC);

  // Inisialisasi array default (agar chart tidak error jika bintang tertentu kosong)
  $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

  foreach ($data as $row) {
    $distribution[$row['rating']] = (int) $row['count'];
  }

  return $distribution;
}

/**
 * Mengambil tren rating rata-rata per bulan (Tahun ini).
 */
function feedback_get_monthly_trend(mysqli $mysqli): array
{
  $sql = "SELECT 
                MONTH(created_at) as bulan, 
                AVG(rating) as avg_rating
            FROM feedback
            WHERE YEAR(created_at) = YEAR(NOW())
            GROUP BY bulan
            ORDER BY bulan ASC";

  $result = db_query($mysqli, $sql);
  $data = $result->fetch_all(MYSQLI_ASSOC);

  // Format data untuk Chart.js (Label Bulan & Data)
  $formatted = array_fill(1, 12, 0); // Default 0 untuk Jan-Des

  foreach ($data as $row) {
    $formatted[$row['bulan']] = round((float)$row['avg_rating'], 1);
  }

  return array_values($formatted); // Reset keys ke 0-11
}

/**
 * Mengambil daftar semua review dengan detail user dan order (Pagination).
 */
function feedback_get_all_reviews(mysqli $mysqli, array $options = []): array
{
  $limit = $options['limit'] ?? 10;
  $page = $options['page'] ?? 1;
  $offset = ($page - 1) * $limit;

  // Hitung total
  $sqlTotal = "SELECT COUNT(id_feedback) as total FROM feedback";
  $resTotal = db_query($mysqli, $sqlTotal);
  $total = $resTotal->fetch_assoc()['total'];

  // Ambil data
  $sql = "SELECT f.*, 
                   u.nama as nama_klien, 
                   o.nomor_order,
                   s.nama as nama_sekolah,
                   DATE_FORMAT(f.created_at, '%d %M %Y') as tgl_review
            FROM feedback f
            JOIN users u ON f.id_user = u.id_user
            JOIN order_produksi o ON f.id_order_produksi = o.id_order_produksi
            JOIN sekolah s ON o.id_sekolah = s.id_sekolah
            ORDER BY f.created_at DESC
            LIMIT ? OFFSET ?";

  $result = db_query($mysqli, $sql, [$limit, $offset]);
  $data = $result->fetch_all(MYSQLI_ASSOC);

  return [
    'data' => $data,
    'total' => (int)$total
  ];
}
