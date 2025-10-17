<?php

// Muat file-file yang dibutuhkan di awal
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../data/user_data.php';

function register_form_action()
{
  view('auth.register', ['title' => 'Register']);
}

function register_process_action()
{
  $db = db_connect();
  // Validasi sederhana
  if (empty($_POST['full_name']) || empty($_POST['email']) || empty($_POST['password'])) {
    flash_message('error', 'Semua kolom wajib diisi.');
    redirect('/register');
    exit();
  }
  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    flash_message('error', 'Format email tidak valid.');
    redirect('/register');
    exit();
  }
  if (user_find_by_email($db, $_POST['email'])) {
    flash_message('error', 'Email sudah terdaftar.');
    redirect('/register');
    exit();
  }

  // Hash password sebelum disimpan
  $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $user_data = [
    'full_name' => $_POST['full_name'],
    'email' => $_POST['email'],
    'phone_number' => $_POST['phone_number'] ?? null,
    'password' => $hashed_password,
    'role' => 'user' // Role default saat registrasi
  ];

  if (user_create($db, $user_data)) {
    flash_message('success', 'Registrasi berhasil! Silakan login.');
    redirect('/login');
  } else {
    flash_message('error', 'Terjadi kesalahan. Gagal mendaftar.');
    redirect('/register');
  }
  exit();
}

function login_form_action()
{
  view('auth.login', ['title' => 'Login']);
}

function login_process_action()
{
  $db = db_connect();
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  $user = user_find_by_email($db, $email);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = [
      'id' => $user['id'],
      'name' => $user['full_name'],
      'role' => $user['role']
    ];
    log_message('info', "Pengguna '{$user['email']}' berhasil login.");
    redirect('/dashboard');
  } else {
    log_message('error', "Percobaan login gagal untuk email '{$email}'.");
    flash_message('error', 'Email atau password salah.');
    redirect('/login');
  }
  exit();
}

function logout_action()
{
  session_destroy();
  redirect('/');
  exit();
}
