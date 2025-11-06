<?php
// app/actions/upload_action.php

require_once __DIR__ . '/../../core/functions.php';

function wysiwyg_upload_action()
{
  header('Content-Type: application/json');

  if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode([
      "error" => ["message" => "Otentikasi diperlukan."]
    ]);
    exit();
  }

  if (!isset($_FILES['upload'])) {
    http_response_code(400);
    echo json_encode([
      "error" => ["message" => "Tidak ada file yang di-upload."]
    ]);
    exit();
  }

  $result = handle_file_upload($_FILES['upload'], 'prospek');

  if ($result['success']) {
    echo json_encode([
      "url" => url($result['url'])
    ]);
  } else {
    http_response_code(500);
    echo json_encode([
      "error" => ["message" => $result['message']]
    ]);
  }

  exit();
}
