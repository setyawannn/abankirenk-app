<?php
// app/data/pengajuan_order_data.php

function pengajuan_order_create(mysqli $mysqli, array $data): int
{
  $todayPrefix = 'RO' . date('ymd');

  $sqlSeq = "SELECT MAX(sequence) as max_seq FROM pengajuan_order WHERE nomor_pengajuan LIKE ?";
  $result = db_query($mysqli, $sqlSeq, ["{$todayPrefix}%"]);
  $row = $result->fetch_assoc();
  $nextSequence = ($row && $row['max_seq']) ? (int)$row['max_seq'] + 1 : 1;
  $nomorPengajuan = $todayPrefix . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);

  $sql = "INSERT INTO pengajuan_order 
                (id_sekolah, id_user, status_pengajuan, pesan, narahubung, no_narahubung, nomor_pengajuan, sequence) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

  $params = [
    $data['id_sekolah'],
    $data['id_user'],
    $data['status_pengajuan'],
    $data['pesan'],
    $data['narahubung'],
    $data['no_narahubung'],
    $nomorPengajuan,
    $nextSequence
  ];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}

function pengajuan_order_get_by_user(mysqli $mysqli, int $id_user, array $options = []): array
{
  $limit = $options['limit'] ?? 10;
  $page = $options['page'] ?? 1;
  $search = $options['search'] ?? '';
  $offset = ($page - 1) * $limit;

  $baseQuery = "FROM pengajuan_order p 
                  JOIN sekolah s ON p.id_sekolah = s.id_sekolah
                  WHERE p.id_user = ?";

  $whereClauses = [];
  $params = [$id_user];

  if (!empty($search)) {
    $whereClauses[] = "(s.nama LIKE ? OR p.pesan LIKE ? OR p.nomor_pengajuan LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
  }

  $whereSql = "";
  if (!empty($whereClauses)) {
    $whereSql = " AND " . implode(" AND ", $whereClauses);
  }

  $totalQuery = "SELECT COUNT(p.id_pengajuan) as total " . $baseQuery . $whereSql;
  $totalResult = db_query($mysqli, $totalQuery, $params);
  $totalRows = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

  $dataQuery = "SELECT p.*, s.nama AS nama_sekolah,
                         DATE_FORMAT(p.created_at, '%d/%m/%y %H:%i') AS formatted_created_at
                  " . $baseQuery . $whereSql . " 
                  ORDER BY p.created_at DESC 
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

function pengajuan_order_get_by_id(mysqli $mysqli, int $id): ?array
{
  $sql = "SELECT p.*, 
                   s.nama AS nama_sekolah,
                   u_klien.nama AS nama_klien,
                   u_po.nama AS nama_po,
                   DATE_FORMAT(p.created_at, '%d/%m/%y %H:%i') AS formatted_created_at,
                   DATE_FORMAT(p.tanggal_balasan, '%d/%m/%y %H:%i') AS formatted_tanggal_balasan
            FROM pengajuan_order p
            JOIN sekolah s ON p.id_sekolah = s.id_sekolah
            JOIN users u_klien ON p.id_user = u_klien.id_user
            LEFT JOIN users u_po ON p.id_user_po = u_po.id_user
            WHERE p.id_pengajuan = ?";

  $result = db_query($mysqli, $sql, [$id]);
  return $result ? $result->fetch_assoc() : null;
}

function pengajuan_order_update_by_po(mysqli $mysqli, int $id, string $status, string $balasan, int $id_user_po): int
{
  $sql = "UPDATE pengajuan_order SET 
                status_pengajuan = ?, 
                balasan = ?,
                id_user_po = ?,
                tanggal_balasan = NOW()
            WHERE id_pengajuan = ?";

  $params = [$status, $balasan, $id_user_po, $id];

  $affectedRows = db_query($mysqli, $sql, $params);
  return (int) $affectedRows;
}

function pengajuan_order_get_all_for_po(mysqli $mysqli, array $options = []): array
{
  $limit = $options['limit'] ?? 10;
  $page = $options['page'] ?? 1;
  $search = $options['search'] ?? '';
  $offset = ($page - 1) * $limit;

  $baseQuery = "FROM pengajuan_order p 
                  JOIN sekolah s ON p.id_sekolah = s.id_sekolah
                  JOIN users u ON p.id_user = u.id_user";

  $whereClauses = [];
  $params = [];

  if (!empty($search)) {
    $whereClauses[] = "(s.nama LIKE ? OR p.nomor_pengajuan LIKE ? OR u.nama LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
  }

  $whereSql = "";
  if (!empty($whereClauses)) {
    $whereSql = " WHERE " . implode(" AND ", $whereClauses);
  }

  $totalQuery = "SELECT COUNT(p.id_pengajuan) as total " . $baseQuery . $whereSql;
  $totalResult = db_query($mysqli, $totalQuery, $params);
  $totalRows = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

  $dataQuery = "SELECT p.*, s.nama AS nama_sekolah, u.nama AS nama_klien,
                         DATE_FORMAT(p.created_at, '%d/%m/%y %H:%i') AS formatted_created_at
                  " . $baseQuery . $whereSql . " 
                  ORDER BY 
                    CASE 
                      WHEN p.status_pengajuan = 'dalam proses' AND p.created_at < CURDATE() THEN 1
                      WHEN p.status_pengajuan = 'dalam proses' AND p.created_at >= CURDATE() THEN 2
                      ELSE 3
                    END ASC,
                    CASE 
                      WHEN p.status_pengajuan = 'dalam proses' THEN p.created_at 
                    END ASC,
                    p.created_at DESC
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
