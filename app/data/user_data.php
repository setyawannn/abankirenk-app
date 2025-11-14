<?php

// data/user_data.php

function user_find_by_email(mysqli $mysqli, string $email): ?array
{
  $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
  $params = [$email];

  $result = db_query($mysqli, $sql, $params);

  return $result ? $result->fetch_assoc() : null;
}

function user_find_by_id(mysqli $mysqli, int $id): ?array
{
  $sql = "SELECT * FROM users WHERE id_user = ? LIMIT 1";
  $params = [$id];

  $result = db_query($mysqli, $sql, $params);

  return $result ? $result->fetch_assoc() : null;
}

function user_create(mysqli $mysqli, array $data): int
{
  $sql = "INSERT INTO users (nama, email, username, password, role) 
            VALUES (?, ?, ?, ?, ?)";

  $params = [
    $data['nama'],
    $data['email'],
    $data['username'],
    $data['password'],
    $data['role']
  ];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}


function user_get_all_by_role(mysqli $mysqli, string $role): array
{
  $sql = "SELECT id_user, nama, username, email, role 
            FROM users 
            WHERE role = ?";

  $params = [$role];
  $result = db_query($mysqli, $sql, $params);

  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
