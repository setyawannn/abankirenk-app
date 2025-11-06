<?php

// data/sekolah_data.php

function sekolah_get_all(mysqli $mysqli): array
{
    $sql = "SELECT id_sekolah, nama, lokasi, kontak FROM sekolah ORDER BY nama";

    $result = db_query($mysqli, $sql);

    if (!$result) {
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

function sekolah_insert(mysqli $mysqli, string $nama, string $lokasi, string $kontak): int
{
    $sql = "INSERT INTO sekolah (nama, lokasi, kontak) VALUES (?, ?, ?)";
    $params = [$nama, $lokasi, $kontak];

    $newId = db_query($mysqli, $sql, $params);

    return (int) $newId;
}

function sekolah_get_by_id(mysqli $mysqli, int $id): ?array
{
    $sql = "SELECT id_sekolah, nama, lokasi, kontak FROM sekolah WHERE id_sekolah = ?";
    $params = [$id];

    $result = db_query($mysqli, $sql, $params);

    if (!$result) {
        return null;
    }

    $sekolah = $result->fetch_assoc();

    return $sekolah ?: null;
}

function sekolah_ajax_search(mysqli $mysqli, string $query): array
{
    $sql = "SELECT id_sekolah, nama, lokasi 
            FROM sekolah 
            WHERE nama LIKE ? 
            ORDER BY nama 
            LIMIT 20";

    $searchTerm = '%' . $query . '%';
    $params = [$searchTerm];

    $result = db_query($mysqli, $sql, $params);

    if (!$result) {
        return [];
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}
