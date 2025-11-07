<?php
// app/data/template_mou_data.php

function template_mou_get_all_paginated(mysqli $mysqli, array $options = []): array
{
  $limit = $options['limit'] ?? 10;
  $page = $options['page'] ?? 1;
  $search = $options['search'] ?? '';
  $offset = ($page - 1) * $limit;

  $baseQuery = "FROM template_mou";
  $whereClauses = [];
  $params = [];

  if (!empty($search)) {
    $whereClauses[] = "(judul LIKE ? OR deskripsi LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
  }

  $whereSql = "";
  if (!empty($whereClauses)) {
    $whereSql = " WHERE " . implode(" AND ", $whereClauses);
  }

  // Query untuk Total
  $totalQuery = "SELECT COUNT(id_template_mou) as total " . $baseQuery . $whereSql;
  $totalResult = db_query($mysqli, $totalQuery, $params);
  $totalRows = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

  // Query untuk Data
  $dataQuery = "SELECT *, 
                         DATE_FORMAT(updated_at, '%d/%m/%y %H:%i') AS formatted_updated_at
                  " . $baseQuery . $whereSql . " 
                  ORDER BY updated_at DESC 
                  LIMIT ? OFFSET ?";

  $dataParams = $params;
  $dataParams[] = $limit;
  $dataParams[] = $offset;

  $dataResult = db_query($mysqli, $dataQuery, $dataParams);
  $data = $dataResult ? $dataResult->fetch_all(MYSQLI_ASSOC) : [];

  return [
    'data' => $data,
    'total' => (int) $totalRows
  ];
}

function template_mou_get_all(mysqli $mysqli): array
{
  $sql = "SELECT *, 
                   DATE_FORMAT(updated_at, '%d/%m/%y %H:%i') AS formatted_updated_at
            FROM template_mou 
            ORDER BY judul";

  $result = db_query($mysqli, $sql);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function template_mou_get_by_id(mysqli $mysqli, int $id): ?array
{
  $sql = "SELECT * FROM template_mou WHERE id_template_mou = ?";
  $params = [$id];

  $result = db_query($mysqli, $sql, $params);
  return $result ? $result->fetch_assoc() : null;
}

function template_mou_create(mysqli $mysqli, array $data): int
{
  $sql = "INSERT INTO template_mou (judul, deskripsi, mou) VALUES (?, ?, ?)";
  $params = [
    $data['judul'],
    $data['deskripsi'],
    $data['file_url']
  ];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}

function template_mou_update(mysqli $mysqli, int $id, array $data): int
{
  $sql = "UPDATE template_mou SET 
                judul = ?, 
                deskripsi = ?, 
                mou = ? 
            WHERE id_template_mou = ?";

  $params = [
    $data['judul'],
    $data['deskripsi'],
    $data['file_url'],
    $id
  ];

  $affectedRows = db_query($mysqli, $sql, $params);
  return (int) $affectedRows;
}

function template_mou_delete(mysqli $mysqli, int $id): int
{
  $sql = "DELETE FROM template_mou WHERE id_template_mou = ?";
  $params = [$id];

  $affectedRows = db_query($mysqli, $sql, $params);
  return (int) $affectedRows;
}
