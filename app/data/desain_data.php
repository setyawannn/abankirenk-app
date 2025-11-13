<?php
// app/data/desain_data.php

require_once __DIR__ . '/../../core/database.php';

function desain_get_by_order_id(mysqli $mysqli, string $nomor_order): array
{
  $sql = "SELECT d.*, u.nama AS nama_uploader
            FROM desain d
            LEFT JOIN users u ON d.id_user = u.id_user
            WHERE d.id_order_produksi = ?
            ORDER BY d.created_at ASC";

  $result = db_query($mysqli, $sql, [$nomor_order]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function desain_create(mysqli $mysqli, array $data): int
{
  $sql = "INSERT INTO desain (id_order_produksi, id_user, desain)
            VALUES (?, ?, ?)";

  $params = [
    $data['id_order_produksi'],
    $data['id_user'],
    $data['file_url']
  ];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}
