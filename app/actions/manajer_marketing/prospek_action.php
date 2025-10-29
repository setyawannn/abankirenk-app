<?php

require_once __DIR__ . '/../../../core/database.php';
require_once __DIR__ . '/../../data/prospek_data.php';
require_once __DIR__ . '/../../../core/functions.php';

function index_action()
{
  $data = [
    'page_title' => 'Manajemen Prospek',
    'active_menu' => 'prospek_mm',
    'status_options' => ['berhasil', 'gagal', 'batal', 'dalam_proses']
  ];
  view('manajer_marketing.manajemen_prospek.index', $data);
}


function ajax_list_action()
{

  $db = db_connect();
  if (!$db) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
  }

  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
  $search = $_GET['search'] ?? '';
  $status = $_GET['status'] ?? '';

  $options = [
    'page' => $page,
    'limit' => $limit,
    'search' => $search,
    'status' => $status,
  ];

  $result = prospek_get_all($db, $options);

  $totalPages = ceil($result['total'] / $limit);

  $response = [
    'data' => $result['data'],
    'pagination' => [
      'total' => $result['total'],
      'per_page' => $limit,
      'current_page' => $page,
      'last_page' => $totalPages,
    ]
  ];

  header('Content-Type: application/json');
  echo json_encode($response);
  exit();
}

function generate_status_badge($status)
{
  $baseClass = "px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full";
  switch ($status) {
    case 'berhasil':
      return "<span class='{$baseClass} bg-green-100 text-green-800'>Berhasil</span>";
    case 'gagal':
      return "<span class='{$baseClass} bg-red-100 text-red-800'>Gagal</span>";
    case 'batal':
      return "<span class='{$baseClass} bg-gray-100 text-gray-800'>Batal</span>";
    case 'dalam_proses':
      return "<span class='{$baseClass} bg-blue-100 text-blue-800'>Dalam Proses</span>";
    default:
      return "<span class='{$baseClass} bg-yellow-100 text-yellow-800'>{$status}</span>";
  }
}
