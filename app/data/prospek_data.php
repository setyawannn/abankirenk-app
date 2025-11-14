<?php
// data/prospek_data.php

function prospek_get_all(mysqli $mysqli, array $options = []): array
{
  $limit = $options['limit'] ?? 10;
  $page = $options['page'] ?? 1;
  $search = $options['search'] ?? '';
  $status = $options['status'] ?? '';
  $offset = ($page - 1) * $limit;

  $baseQuery = "FROM prospek p JOIN sekolah s ON p.id_sekolah = s.id_sekolah";
  $whereClauses = [];
  $params = [];

  if (!empty($search)) {
    $whereClauses[] = "(s.nama LIKE ? OR p.narahubung LIKE ? OR p.deskripsi LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
  }

  if (!empty($status)) {
    $whereClauses[] = "p.status_prospek = ?";
    $params[] = $status;
  }

  $userRole = $_SESSION['user']['role'] ?? '';

  if ($userRole === 'tim_marketing') {
    $user = auth();
    $whereClauses[] = "p.id_user = ?";
    $params[] = $user['id'];
  }

  $whereSql = "";
  if (!empty($whereClauses)) {
    $whereSql = " WHERE " . implode(" AND ", $whereClauses);
  }

  $totalQuery = "SELECT COUNT(p.id_prospek) as total " . $baseQuery . $whereSql;

  $totalResult = db_query($mysqli, $totalQuery, $params);
  $totalRows = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

  $dataQuery = "SELECT p.*, s.nama AS nama_sekolah, 
                         DATE_FORMAT(p.created_at, '%d/%m/%y %H:%i') AS formatted_created_at,
                         DATE_FORMAT(p.updated_at, '%d/%m/%y %H:%i') AS formatted_updated_at
                  " . $baseQuery . $whereSql . " 
                  ORDER BY p.updated_at DESC 
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

function prospek_create(mysqli $mysqli, array $data): int
{
  $sql = "INSERT INTO prospek (id_sekolah, id_user, narahubung, no_narahubung, status_prospek, deskripsi, catatan) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

  $params = [
    $data['id_sekolah'],
    $data['id_user'],
    $data['narahubung'],
    $data['no_narahubung'],
    $data['status'],
    $data['deskripsi'],
    $data['catatan']
  ];

  $newId = db_query($mysqli, $sql, $params);

  return (int) $newId;
}

function prospek_get_by_id(mysqli $mysqli, int $id): ?array
{
  $sql = "SELECT p.*, s.nama AS nama_sekolah 
            FROM prospek p 
            JOIN sekolah s ON p.id_sekolah = s.id_sekolah 
            WHERE p.id_prospek = ?";

  $params = [$id];
  $result = db_query($mysqli, $sql, $params);

  return $result ? $result->fetch_assoc() : null;
}

function prospek_update(mysqli $mysqli, int $id, array $data): int
{
  $sql = "UPDATE prospek SET 
                id_sekolah = ?, 
                id_user = ?, 
                narahubung = ?, 
                no_narahubung = ?, 
                status_prospek = ?, 
                deskripsi = ?,
                catatan = ? 
            WHERE id_prospek = ?";

  $params = [
    $data['id_sekolah'],
    $data['id_user'],
    $data['narahubung'],
    $data['no_narahubung'],
    $data['status_prospek'],
    $data['deskripsi'],
    $data['catatan'],
    $id
  ];

  $affectedRows = db_query($mysqli, $sql, $params);

  return (int) $affectedRows;
}

function prospek_update_catatan(mysqli $mysqli, int $id, string $catatan): int
{
  $sql = "UPDATE prospek SET catatan = ? WHERE id_prospek = ?";
  $params = [$catatan, $id];

  $affectedRows = db_query($mysqli, $sql, $params);

  return (int) $affectedRows;
}

function prospek_delete(mysqli $mysqli, int $id): int
{
  $sql = "DELETE FROM prospek WHERE id_prospek = ?";
  $params = [$id];

  $affectedRows = db_query($mysqli, $sql, $params);

  return (int) $affectedRows;
}

function prospek_update_status(mysqli $mysqli, int $id, string $status): int
{
  $sql = "UPDATE prospek SET status_prospek = ? WHERE id_prospek = ?";
  $params = [$status, $id];

  $affectedRows = db_query($mysqli, $sql, $params);

  return (int) $affectedRows;
}
