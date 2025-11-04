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
  $stmt = mysqli_prepare($mysqli, "INSERT INTO users (nama, email, nomor_telepon, password, username, role) VALUES (?, ?, ?, ?, ?, ?)");
  mysqli_stmt_bind_param(
    $stmt,
    "ssssss",
    $data['nama'],
    $data['email'],
    $data['nomor_telepon'],
    $data['password'],
    $data['username'],
    $data['role']
  );
  return mysqli_stmt_execute($stmt);
}

function user_get_all_by_role(mysqli $mysqli, string $role): array
{
  $result = mysqli_query($mysqli, "SELECT * FROM users WHERE role = '$role'");
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
