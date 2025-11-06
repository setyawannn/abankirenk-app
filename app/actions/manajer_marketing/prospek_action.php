<?php
// actions/manajer_marketing/prospek_action.php

require_once __DIR__ . '/../../../core/database.php';
require_once __DIR__ . '/../../data/prospek_data.php';
require_once __DIR__ . '/../../data/sekolah_data.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../data/user_data.php';

function index_action()
{
  $data = [
    'page_title' => 'Manajemen Prospek',
    'active_menu' => 'prospek_mm',
    'status_options' => ['berhasil', 'gagal', 'batal', 'dalam proses']
  ];
  view('manajer_marketing.manajemen_prospek.index', $data);
}

function create_action()
{
  $db = db_connect();
  $sekolah = sekolah_get_all($db);
  $staff = user_get_all_by_role($db, 'tim_marketing');

  $data = [
    'page_title' => 'Manajemen Prospek',
    'active_menu' => 'prospek_mm',
    'sekolah' => $sekolah,
    'staff' => $staff,
  ];

  view('manajer_marketing.manajemen_prospek.create', $data);
}

function store_action()
{
  $db = db_connect();
  $narahubung = $_POST['narahubung'];
  $no_narahubung = $_POST['no_narahubung'];
  $id_sekolah = $_POST['id_sekolah'];
  $id_user = $_POST['id_user'];
  $catatan = $_POST['catatan'];


  $nama_sekolah = $_POST['nama_sekolah'] ?? '';
  $lokasi_sekolah = $_POST['lokasi_sekolah'] ?? '';
  $kontak_sekolah = $_POST['kontak_sekolah'] ?? '';

  if (!$id_sekolah) {
    $new_id_sekolah = sekolah_insert($db, $nama_sekolah, $lokasi_sekolah, $kontak_sekolah);
  }

  prospek_create($db, [
    'narahubung' => $narahubung,
    'no_narahubung' => $no_narahubung,
    'id_sekolah' => $id_sekolah ?? $new_id_sekolah,
    'id_user' => $id_user,
    'catatan' => $catatan,
    'status' => 'baru'
  ]);

  flash_message('success', 'Prospek baru berhasil ditambahkan.');
  return redirect('/manajer-marketing/manajemen-prospek');
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

  $dataWithBadges = array_map(function ($row) {
    $row['status_badge'] = generate_status_badge($row['status_prospek']);
    return $row;
  }, $result['data']);


  $totalPages = ceil($result['total'] / $limit);

  $response = [
    'data' => $dataWithBadges,
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
    case 'baru':
      return "<span class='{$baseClass} bg-yellow-100 text-yellow-800'>Baru</span>";
    case 'berhasil':
      return "<span class='{$baseClass} bg-green-100 text-green-800'>Berhasil</span>";
    case 'gagal':
      return "<span class='{$baseClass} bg-red-100 text-red-800'>Gagal</span>";
    case 'batal':
      return "<span class='{$baseClass} bg-gray-100 text-gray-800'>Batal</span>";
    case 'dalam proses':
      return "<span class='{$baseClass} bg-blue-100 text-blue-800'>Dalam Proses</span>";
    default:
      return "<span class='{$baseClass} bg-yellow-100 text-yellow-800'>{$status}</span>";
  }
}
