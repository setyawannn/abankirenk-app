<?php

function prospek_get_all(mysqli $mysqli, array $options = []): array
{
  $limit = $options['limit'] ?? 10;
  $page = $options['page'] ?? 1;
  $search = $options['search'] ?? '';
  $status = $options['status'] ?? '';
  $offset = ($page - 1) * $limit;

  $baseQuery = "FROM prospek_sekolah p JOIN sekolah s ON p.sekolah_id = s.sekolah_id";
  $whereClauses = [];
  $params = [];
  $types = "";

  if (!empty($search)) {
    $whereClauses[] = "(s.nama_sekolah LIKE ? OR p.catatan LIKE ?)";
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

  $totalQuery = "SELECT COUNT(p.prospek_id) as total " . $baseQuery . $whereSql;
  $totalStmt = mysqli_prepare($mysqli, $totalQuery);
  if (!empty($params)) {
    mysqli_stmt_bind_param($totalStmt, $types, ...$params);
  }
  mysqli_stmt_execute($totalStmt);
  $totalResult = mysqli_stmt_get_result($totalStmt);
  $totalRows = mysqli_fetch_assoc($totalResult)['total'] ?? 0;
  mysqli_stmt_close($totalStmt);


  $dataQuery = "SELECT p.*, s.nama_sekolah AS nama_sekolah " . $baseQuery . $whereSql . " ORDER BY p.prospek_id DESC LIMIT ? OFFSET ?";
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
