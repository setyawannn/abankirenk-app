<?php

require_once __DIR__ . '/../../../core/database.php';
require_once __DIR__ . '/../../data/prospek_data.php';
require_once __DIR__ . '/../../data/sekolah_data.php';
require_once __DIR__ . '/../../../core/functions.php';
require_once __DIR__ . '/../../data/user_data.php';

function index_action()
{
  $data = [
    'page_title' => 'Prospek Saya',
    'active_menu' => 'prospek_tm',
    'status_options' => ['baru', 'berhasil', 'gagal', 'batal', 'dalam proses']
  ];
  view('tim_marketing.prospek_saya.index', $data);
}

function edit_action($params)
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  $id = (int) ($params['id'] ?? 0);
  if ($id <= 0) {
    flash_message('error', 'Error', 'ID Prospek tidak valid.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  $prospek = prospek_get_by_id($db, $id);
  $sekolah = sekolah_get_by_id($db, $prospek['id_sekolah']);
  $staff = user_find_by_id($db, $prospek['id_user']);
  $isMyJob = isset($_SESSION['user']) && $_SESSION['user']['id'] === $prospek['id_user'];

  $prospek['status_badge'] = generate_status_badge($prospek['status_prospek']);
  $staff['role'] = format_role_name($staff['role']);

  if (!$prospek) {
    flash_message('error', 'Data Prospek', 'Prospek tidak ditemukan.');
    return redirect('/manajer-marketing/manajemen-prospek');
  }

  $data = [
    'page_title' => 'Prospek Saya',
    'active_menu' => 'prospek_tm',
    'prospek' => $prospek,
    'sekolah' => $sekolah,
    'staff' => $staff,
    'is_my_job' => $isMyJob
  ];

  view('tim_marketing.prospek_saya.edit', $data);
}

function update_action($params)
{
  $db = db_connect();
  if (!$db) {
    flash_message('error', 'Database Error', 'Gagal terhubung ke database.');
    return redirect('/tim-marketing/prospek-saya');
  }

  $id = (int) ($params['id'] ?? 0);
  if ($id <= 0) {
    flash_message('error', 'Error', 'ID Prospek tidak valid.');
    return redirect('/tim-marketing/prospek-saya');
  }

  $catatan = $_POST['catatan'] ?? '';

  $updatedRows = prospek_update_catatan($db, $id, $catatan);

  if ($updatedRows > 0) {
    flash_message('success', 'Berhasil', 'Catatan prospek berhasil diperbarui.');
  } else {
    flash_message('error', 'Gagal', 'Gagal memperbarui catatan prospek.');
  }

  return redirect('/tim-marketing/prospek-saya/' . $id);
}


function generate_status_badge($status)
{
  $baseClass = "px-4 py-1.25 inline-flex text-sm leading-5 font-semibold rounded-full";
  switch ($status) {
    case 'baru':
      return "<span class='{$baseClass} bg-yellow-100 text-yellow-800'>Baru</span>";
    case 'dalam proses':
      return "<span class='{$baseClass} bg-blue-100 text-blue-800'>Dalam Proses</span>";
    case 'berhasil':
      return "<span class='{$baseClass} bg-green-100 text-green-800'>Berhasil</span>";
    case 'gagal':
      return "<span class='{$baseClass} bg-red-100 text-red-800'>Gagal</span>";
    case 'batal':
      return "<span class='{$baseClass} bg-gray-100 text-gray-800'>Batal</span>";
    default:
      return "<span class='{$baseClass} bg-gray-100 text-gray-800'>{$status}</span>";
  }
}
