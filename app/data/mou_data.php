<?php
// app/data/mou_data.php

function mou_create(mysqli $mysqli, string $file_url, int $id_user): int
{
  $sql = "INSERT INTO mou (mou, id_user) VALUES (?, ?)";
  $params = [$file_url, $id_user];

  $newId = db_query($mysqli, $sql, $params);
  return (int) $newId;
}
