<?php
// app/data/template_desain_data.php

function template_desain_get_all_paginated(mysqli $mysqli, array $options = []): array
{
  $limit = $options['limit'] ?? 10;
  $page = $options['page'] ?? 1;
  $search = $options['search'] ?? '';
  $offset = ($page - 1) * $limit;

  $baseQuery = "FROM template_desain";
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

  $totalQuery = "SELECT COUNT(id_template_desain) as total " . $baseQuery . $whereSql;
  $totalResult = db_query($mysqli, $totalQuery, $params);
  $totalRows = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

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

function template_desain_get_by_id(mysqli $mysqli, int $id): ?array
{
  $sql = "SELECT * FROM template_desain WHERE id_template_desain = ?";
  $params = [$id];

  $result = db_query($mysqli, $sql, $params);
  return $result ? $result->fetch_assoc() : null;
}

function template_desain_create(mysqli $mysqli, array $data): int
{
  $sql = "INSERT INTO template_desain (judul, deskripsi, template_desain) VALUES (?, ?, ?)";
  $params = [
    $data['judul'],
    $data['deskripsi'],
    $data['file_url']
  ];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}

function template_desain_update(mysqli $mysqli, int $id, array $data): int
{
  $sql = "UPDATE template_desain SET 
                judul = ?, 
                deskripsi = ?, 
                template_desain = ? 
            WHERE id_template_desain = ?";

  $params = [
    $data['judul'],
    $data['deskripsi'],
    $data['file_url'],
    $id
  ];

  $affectedRows = db_query($mysqli, $sql, $params);
  return (int) $affectedRows;
}

function template_desain_delete(mysqli $mysqli, int $id): int
{
  $sql = "DELETE FROM template_desain WHERE id_template_desain = ?";
  $params = [$id];

  $affectedRows = db_query($mysqli, $sql, $params);
  return (int) $affectedRows;
}
