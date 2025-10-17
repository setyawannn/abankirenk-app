<?php

/**
 * Mengambil semua data produk dari database.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @return array
 */
function products_get_all($mysqli) {
    $query = "SELECT id, name, price FROM products ORDER BY name ASC";
    $result = mysqli_query($mysqli, $query);

    if (!$result) {
        error_log("Query gagal: " . mysqli_error($mysqli));
        return [];
    }

    // Mengambil semua baris hasil menjadi sebuah array
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Mengambil satu produk berdasarkan ID-nya menggunakan prepared statement.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id ID produk yang dicari.
 * @return array|null
 */
function products_find_by_id($mysqli, $id) {
    $query = "SELECT id, name, price FROM products WHERE id = ?";
    
    // 1. Prepare
    $stmt = mysqli_prepare($mysqli, $query);

    // 2. Bind parameter
    mysqli_stmt_bind_param($stmt, "i", $id); // "i" untuk integer

    // 3. Execute
    mysqli_stmt_execute($stmt);

    // 4. Get result
    $result = mysqli_stmt_get_result($stmt);

    // 5. Fetch data
    return mysqli_fetch_assoc($result);
}