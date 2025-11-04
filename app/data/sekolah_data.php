<?php

function sekolah_get_all(mysqli $mysqli): array
{
    $result = mysqli_query($mysqli, "SELECT * FROM sekolah ORDER BY nama");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function sekolah_create(mysqli $mysqli, array $data): int
{
    $stmt = mysqli_prepare($mysqli, "INSERT INTO sekolah (nama) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "s", $data['nama']);
    mysqli_stmt_execute($stmt);
    $newId = mysqli_insert_id($mysqli);
    mysqli_stmt_close($stmt);
    return $newId;
}