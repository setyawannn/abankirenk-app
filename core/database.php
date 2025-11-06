<?php
// core/database.php

/**
 * Membuat dan mengembalikan instance koneksi MySQLi.
 * Koneksi dibuat hanya sekali.
 *
 * @return mysqli|null
 */
function db_connect()
{
    static $connection = null;

    if ($connection === null) {
        $host = config('database.host');
        $user = config('database.username');
        $pass = config('database.password');
        $db   = config('database.dbname');
        $port = config('database.port');

        $connection = mysqli_connect($host, $user, $pass, $db, $port);

        if (mysqli_connect_errno()) {
            error_log("Koneksi database gagal: " . mysqli_connect_error());
            return null;
        }

        mysqli_set_charset($connection, 'utf8mb4');
    }

    return $connection;
}


function db_begin_transaction($db)
{
    $db->begin_transaction();
}


function db_commit($db)
{
    $db->commit();
}


function db_rollback($db)
{
    $db->rollback();
}


function db_query($db, $sql, $params = [])
{
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        error_log("Gagal prepare statement: " . $db->error . " | SQL: " . $sql);
        return false;
    }

    if (!empty($params)) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        error_log("Gagal eksekusi statement: " . $stmt->error . " | SQL: " . $sql);
        return false;
    }

    $sql_command = strtoupper(strtok(trim($sql), " \n\t"));

    switch ($sql_command) {
        case 'SELECT':
            return $stmt->get_result();
        case 'INSERT':
            return $db->insert_id;
        case 'UPDATE':
        case 'DELETE':
            return $stmt->affected_rows;
        default:
            return true;
    }
}
