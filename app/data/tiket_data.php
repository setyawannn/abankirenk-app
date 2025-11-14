<?php
// app/data/tiket_data.php

require_once __DIR__ . '/../../core/database.php';

/**
 * Menghasilkan Nomor Komplain (COMymdXXX).
 */
function tiket_generate_nomor_komplain(mysqli $mysqli): array
{
  $todayPrefix = 'COM' . date('ymd');

  $sqlSeq = "SELECT MAX(sequence) as max_seq FROM tiket WHERE nomor_komplain LIKE ?";
  $result = db_query($mysqli, $sqlSeq, ["{$todayPrefix}%"]);
  $row = $result->fetch_assoc();

  $nextSequence = ($row && $row['max_seq']) ? (int)$row['max_seq'] + 1 : 1;
  $nomorKomplain = $todayPrefix . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);

  return [
    'nomor_komplain' => $nomorKomplain,
    'sequence' => $nextSequence
  ];
}

/**
 * Membuat tiket komplain baru.
 */
function tiket_create(mysqli $mysqli, array $data): int
{
  $sql = "INSERT INTO tiket (
                id_order_produksi, id_user, nomor_komplain, sequence, 
                kategori, deskripsi, link_video, status_tiket
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

  $params = [
    $data['id_order_produksi'], // INT
    $data['id_user'], // ID Klien
    $data['nomor_komplain'],
    $data['sequence'],
    $data['kategori'],
    $data['deskripsi'],
    $data['link_video'],
    'belum dibalas' // Status default
  ];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}

/**
 * Mengambil semua riwayat tiket untuk satu order (untuk tab).
 */
function tiket_get_by_order_id(mysqli $mysqli, int $id_order_produksi): array
{
  $sql = "SELECT t.*, u.nama AS nama_klien
            FROM tiket t
            LEFT JOIN users u ON t.id_user = u.id_user
            WHERE t.id_order_produksi = ?
            ORDER BY t.created_at DESC";

  $result = db_query($mysqli, $sql, [$id_order_produksi]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil satu tiket lengkap untuk halaman detail/edit.
 */
function tiket_get_by_id(mysqli $mysqli, int $id_tiket)
{
  $sql = "SELECT 
                t.*, 
                klien.nama AS nama_klien,
                cs.nama AS nama_cs,
                cs.role AS role_cs,
                o.nomor_order,
                o.id_klien, -- (Penting untuk otorisasi)
                s.nama AS nama_sekolah,
                DATE_FORMAT(t.created_at, '%d %M %Y %H:%i') AS formatted_created_at,
                DATE_FORMAT(t.tanggal_respon, '%d %M %Y %H:%i') AS formatted_tanggal_respon
            FROM tiket t
            JOIN users klien ON t.id_user = klien.id_user
            JOIN order_produksi o ON t.id_order_produksi = o.id_order_produksi
            JOIN sekolah s ON o.id_sekolah = s.id_sekolah
            LEFT JOIN users cs ON t.id_user_cs = cs.id_user
            WHERE t.id_tiket = ?";

  $result = db_query($mysqli, $sql, [$id_tiket]);
  return $result ? $result->fetch_assoc() : null;
}

/**
 * Memperbarui tiket dengan balasan dari CS.
 */
function tiket_update_balasan(mysqli $mysqli, int $id_tiket, array $data): int
{
  $sql = "UPDATE tiket SET
                respon = ?,
                status_retur = ?,
                status_tiket = 'selesai',
                id_user_cs = ?,
                tanggal_respon = NOW()
            WHERE id_tiket = ?";

  $params = [
    $data['respon'],
    $data['status_retur'],
    $data['id_user_cs'],
    $id_tiket
  ];

  $affectedRows = db_query($mysqli, $sql, $params);
  return (int) $affectedRows;
}
