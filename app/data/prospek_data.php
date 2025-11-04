<?php

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
  $types = "";

  if (!empty($search)) {
    $whereClauses[] = "(s.nama LIKE ? OR p.catatan LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
  }

  if (!empty($status)) {
    $whereClauses[] = "p.status_prospek = ?";
    $params[] = $status;
    $types .= "s";
  }

  $whereSql = "";
  if (!empty($whereClauses)) {
    $whereSql = " WHERE " . implode(" AND ", $whereClauses);
  }

  $totalQuery = "SELECT COUNT(p.id_prospek) as total " . $baseQuery . $whereSql;
  $totalStmt = mysqli_prepare($mysqli, $totalQuery);
  if (!empty($params)) {
    mysqli_stmt_bind_param($totalStmt, $types, ...$params);
  }
  mysqli_stmt_execute($totalStmt);
  $totalResult = mysqli_stmt_get_result($totalStmt);
  $totalRows = mysqli_fetch_assoc($totalResult)['total'] ?? 0;
  mysqli_stmt_close($totalStmt);


  $dataQuery = "SELECT p.*, s.nama AS nama_sekolah " . $baseQuery . $whereSql . " ORDER BY p.id_prospek DESC LIMIT ? OFFSET ?";
  $dataStmt = mysqli_prepare($mysqli, $dataQuery);

  $allParams = $params;
  $allParams[] = $limit;
  $allParams[] = $offset;
  $allTypes = $types . "ii";

  if (!empty($allParams)) {
    mysqli_stmt_bind_param($dataStmt, $allTypes, ...$allParams);
  }

  mysqli_stmt_execute($dataStmt);
  $result = mysqli_stmt_get_result($dataStmt);
  $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
  mysqli_stmt_close($dataStmt);

  return [
    'data' => $data,
    'total' => $totalRows
  ];
}

function prospek_create(mysqli $mysqli, array $data): int
{
    $stmt = mysqli_prepare($mysqli, "INSERT INTO prospek (id_sekolah, id_user, narahubung, no_narahubung, status_prospek, catatan) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param(
        $stmt,
        "iissss",
        $data['id_sekolah'],
        $data['id_user'],
        $data['narahubung'],
        $data['no_narahubung'],
        $data['status_prospek'],
        $data['catatan']
    );
    mysqli_stmt_execute($stmt);
    $newId = mysqli_insert_id($mysqli);
    mysqli_stmt_close($stmt);
    return $newId;
}

function prospek_get_by_id(mysqli $mysqli, int $id): ?array
{
    $stmt = mysqli_prepare($mysqli, "SELECT p.*, s.nama AS nama_sekolah FROM prospek p JOIN sekolah s ON p.id_sekolah = s.id_sekolah WHERE p.id_prospek = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $data;
}

function prospek_update(mysqli $mysqli, int $id, array $data): int
{
    $stmt = mysqli_prepare($mysqli, "UPDATE prospek SET id_sekolah = ?, id_user = ?, narahubung = ?, no_narahubung = ?, status_prospek = ?, catatan = ? WHERE id_prospek = ?");
    mysqli_stmt_bind_param(
        $stmt,
        "iissssi",
        $data['id_sekolah'],
        $data['id_user'],
        $data['narahubung'],
        $data['no_narahubung'],
        $data['status_prospek'],
        $data['catatan'],
        $id
    );
    mysqli_stmt_execute($stmt);
    $affectedRows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affectedRows;
}
