<?php

/**
 * Mencari pengguna berdasarkan alamat email.
 *
 * @param mysqli $mysqli
 * @param string $email
 * @return array|null
 */
function user_find_by_email(mysqli $mysqli, string $email): ?array
{
  $stmt = mysqli_prepare($mysqli, "SELECT * FROM users WHERE email = ?");
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  return mysqli_fetch_assoc($result);
}

/**
 * Membuat pengguna baru di database.
 *
 * @param mysqli $mysqli
 * @param array $data
 * @return bool
 */
function user_create(mysqli $mysqli, array $data): bool
{
  $stmt = mysqli_prepare($mysqli, "INSERT INTO users (full_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?)");
  mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $data['full_name'],
    $data['email'],
    $data['phone_number'],
    $data['password'],
    $data['role']
  );
  return mysqli_stmt_execute($stmt);
}
