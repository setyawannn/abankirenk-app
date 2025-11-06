<?php

// data/user_data.php

function user_find_by_email(mysqli $mysqli, string $email): ?array
{
  $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
  $params = [$email];

  $result = db_query($mysqli, $sql, $params);

  return $result ? $result->fetch_assoc() : null;
}

function user_create(mysqli $mysqli, array $data): bool
{
  $sql = "INSERT INTO users (nama, email, password, username, role) 
            VALUES (?, ?, ?, ?, ?)";

  $params = [
    $data['nama'],
    $data['email'],
    $data['password'],
    $data['username'],
    $data['role']
  ];

  $result = db_query($mysqli, $sql, $params);

  return (bool) $result;
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
