<?php
// app/data/timeline_data.php

require_once __DIR__ . '/../../core/database.php';

function timeline_get_by_order_id(mysqli $mysqli, int $id_order_produksi): array
{
  $sql = "SELECT t.*, u.nama AS nama_user
            FROM timeline t
            LEFT JOIN users u ON t.id_user = u.id_user
            WHERE t.id_order_produksi = ?
            ORDER BY 
                FIELD(t.status_timeline, 'Ditugaskan', 'Dalam Proses', 'Selesai'),
                t.deadline ASC";

  $result = db_query($mysqli, $sql, [$id_order_produksi]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function timeline_get_by_id(mysqli $mysqli, int $id_timeline)
{
  $sql = "SELECT * FROM timeline WHERE id_timeline = ?";
  $result = db_query($mysqli, $sql, [$id_timeline]);
  return $result ? $result->fetch_assoc() : null;
}

function timeline_create(mysqli $mysqli, array $data): int
{
  $sql = "INSERT INTO timeline (id_order_produksi, id_user, judul, deskripsi, status_timeline, deadline)
            VALUES (?, ?, ?, ?, ?, ?)";

  $params = [
    $data['id_order_produksi'],
    $data['id_user'],
    $data['judul'],
    $data['deskripsi'],
    $data['status_timeline'],
    $data['deadline']
  ];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}

function timeline_update(mysqli $mysqli, int $id_timeline, array $data): int
{
  $sql = "UPDATE timeline SET
                id_user = ?,
                judul = ?,
                deskripsi = ?,
                status_timeline = ?,
                deadline = ?
            WHERE id_timeline = ?";

  $params = [
    $data['id_user'],
    $data['judul'],
    $data['deskripsi'],
    $data['status_timeline'],
    $data['deadline'],
    $id_timeline
  ];

  $affectedRows = db_query($mysqli, $sql, $params);
  return (int) $affectedRows;
}

function timeline_delete(mysqli $mysqli, int $id_timeline): int
{
  $sql = "DELETE FROM timeline WHERE id_timeline = ?";
  $affectedRows = db_query($mysqli, $sql, [$id_timeline]);
  return (int) $affectedRows;
}

function timeline_update_status(mysqli $mysqli, int $id_timeline, string $status_timeline): int
{
  $sql = "UPDATE timeline SET status_timeline = ? WHERE id_timeline = ?";
  $params = [$status_timeline, $id_timeline];

  $affectedRows = db_query($mysqli, $sql, $params);
  return (int) $affectedRows;
}

function timeline_is_user_assigned_to_order_by_nomor(mysqli $mysqli, int $id_user, string $id_order_produksi): bool
{
  $sql = "SELECT 1 FROM timeline WHERE id_user = ? AND id_order_produksi = ? LIMIT 1";
  $result = db_query($mysqli, $sql, [$id_user, $id_order_produksi]);
  return $result && $result->num_rows > 0;
}

function timeline_is_user_assigned_to_order(mysqli $mysqli, int $id_user, int $id_order_produksi): bool
{
  $sql = "SELECT 1 FROM timeline WHERE id_user = ? AND id_order_produksi = ? LIMIT 1";
  $result = db_query($mysqli, $sql, [$id_user, $id_order_produksi]);
  return $result && $result->num_rows > 0;
}
