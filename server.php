<?php
// server.php
// File ini bertugas meniru fungsi .htaccess atau Nginx try_files

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// 1. Jika file fisik (Gambar, CSS, JS) ada di folder public, tampilkan langsung.
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false; // Biarkan PHP server menyajikan file statis ini
}

// 2. Jika tidak ada, oper request ke index.php utama aplikasi
require_once __DIR__ . '/public/index.php';