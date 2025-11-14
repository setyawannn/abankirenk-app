<?php
// app/data/order_data.php

function generate_nomor_order(mysqli $mysqli): string
{
  $todayPrefix = 'ORD' . date('ymd');

  $sqlSeq = "SELECT MAX(sequence) as max_seq FROM order_produksi WHERE nomor_order LIKE ?";
  $result = db_query($mysqli, $sqlSeq, ["{$todayPrefix}%"]);
  $row = $result->fetch_assoc();

  $nextSequence = ($row && $row['max_seq']) ? (int)$row['max_seq'] + 1 : 1;

  return $todayPrefix . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
}

function order_create(mysqli $mysqli, array $data): int
{
  $sql = "INSERT INTO order_produksi (
                nomor_order, sequence, id_sekolah, id_mou, id_klien, 
                status_order, narahubung, no_narahubung, kuantitas, 
                halaman, konsep, deadline
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $params = [
    $data['nomor_order'],
    $data['sequence'],
    $data['id_sekolah'],
    $data['id_mou'],
    $data['id_klien'],
    'baru',
    $data['narahubung'],
    $data['no_narahubung'],
    $data['kuantitas'],
    $data['halaman'],
    $data['konsep'],
    $data['deadline']
  ];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}

function order_get_successful_sources(mysqli $mysqli): array
{
  $sqlProspek = "SELECT 
                    'prospek' as source_type, 
                    p.id_prospek as source_id, 
                    s.nama as nama_sekolah, 
                    p.id_sekolah, 
                    p.narahubung, 
                    p.no_narahubung, 
                    NULL as id_klien_existing 
                   FROM prospek p
                   JOIN sekolah s ON p.id_sekolah = s.id_sekolah
                   WHERE p.status_prospek = 'berhasil'";

  $sqlPengajuan = "SELECT 
                       'pengajuan' as source_type, 
                       p.id_pengajuan as source_id, 
                       s.nama as nama_sekolah, 
                       p.id_sekolah, 
                       p.narahubung, 
                       p.no_narahubung, 
                       p.id_user as id_klien_existing
                     FROM pengajuan_order p
                     JOIN sekolah s ON p.id_sekolah = s.id_sekolah
                     WHERE p.status_pengajuan = 'berhasil'";

  $sql = "$sqlProspek UNION $sqlPengajuan ORDER BY nama_sekolah";

  $result = db_query($mysqli, $sql);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function order_get_all_for_po(mysqli $mysqli, array $options = []): array
{
  $limit = $options['limit'] ?? 10;
  $page = $options['page'] ?? 1;
  $search = $options['search'] ?? '';
  $status = $options['status'] ?? '';
  $offset = ($page - 1) * $limit;

  $baseQuery = "FROM order_produksi o
                  JOIN sekolah s ON o.id_sekolah = s.id_sekolah";

  $whereClauses = [];
  $params = [];

  if (!empty($search)) {
    $whereClauses[] = "(o.nomor_order LIKE ? OR s.nama LIKE ? OR o.narahubung LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
  }

  if (!empty($status)) {
    $whereClauses[] = "o.status_order = ?";
    $params[] = $status;
  }

  $whereSql = "";
  if (!empty($whereClauses)) {
    $whereSql = " WHERE " . implode(" AND ", $whereClauses);
  }

  $totalQuery = "SELECT COUNT(o.id_order_produksi) as total " . $baseQuery . $whereSql;
  $totalResult = db_query($mysqli, $totalQuery, $params);
  $totalRows = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

  $dataQuery = "SELECT o.*, s.nama AS nama_sekolah,
                         DATE_FORMAT(o.updated_at, '%d/%m/%y %H:%i') AS formatted_updated_at
                  " . $baseQuery . $whereSql . " 
                  ORDER BY o.updated_at DESC 
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

function order_get_by_id_for_detail(mysqli $mysqli, int $id): ?array
{
  $sql = "SELECT 
                o.*, 
                s.nama AS nama_sekolah, 
                s.lokasi AS lokasi_sekolah,
                k.nama AS nama_klien,
                k.email AS email_klien,
                m.mou AS file_mou,
                DATE_FORMAT(o.deadline, '%d %M %Y') AS formatted_deadline,
                DATE_FORMAT(o.created_at, '%d/%m/%y %H:%i') AS formatted_created_at
            FROM order_produksi o
            JOIN sekolah s ON o.id_sekolah = s.id_sekolah
            JOIN users k ON o.id_klien = k.id_user
            LEFT JOIN mou m ON o.id_mou = m.id_mou
            WHERE o.id_order_produksi = ?";

  $result = db_query($mysqli, $sql, [$id]);
  return $result ? $result->fetch_assoc() : null;
}

function order_update_status(mysqli $mysqli, int $id, string $status): int
{
  $sql = "UPDATE order_produksi SET status_order = ? WHERE id_order_produksi = ?";
  $params = [$status, $id];

  $affectedRows = db_query($mysqli, $sql, $params);
  return (int) $affectedRows;
}

function order_get_by_nomor_order(mysqli $mysqli, string $nomor_order)
{
  $sql = "SELECT o.*, s.nama AS nama_sekolah
            FROM order_produksi o
            JOIN sekolah s ON o.id_sekolah = s.id_sekolah
            WHERE o.nomor_order = ?";

  $result = db_query($mysqli, $sql, [$nomor_order]);
  return $result ? $result->fetch_assoc() : null;
}

function order_get_by_id(mysqli $mysqli, string $id_order_produksi)
{
  $sql = "SELECT o.*, s.nama AS nama_sekolah
            FROM order_produksi o
            JOIN sekolah s ON o.id_sekolah = s.id_sekolah
            WHERE o.id_order_produksi = ?";

  $result = db_query($mysqli, $sql, [$id_order_produksi]);
  return $result ? $result->fetch_assoc() : null;
}

function order_get_completed_for_klien(mysqli $mysqli, int $id_klien): array
{
  $sql = "SELECT DISTINCT o.id_order_produksi, o.nomor_order, s.nama AS nama_sekolah
            FROM order_produksi o
            JOIN pengiriman p ON o.id_order_produksi = p.id_order_produksi
            JOIN sekolah s ON o.id_sekolah = s.id_sekolah
            WHERE o.id_klien = ? 
            AND p.tanggal_sampai IS NOT NULL
            ORDER BY o.created_at DESC";

  $result = db_query($mysqli, $sql, [$id_klien]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
