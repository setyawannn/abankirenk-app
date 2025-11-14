<?php
// app/data/dashboard_data.php

require_once __DIR__ . '/../../core/database.php';

/**
 * Mengambil semua statistik utama untuk dashboard PO/Manajer Produksi.
 * Menggunakan satu query efisien.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @return array Statistik dalam bentuk array asosiatif.
 */
function dashboard_get_po_stats(mysqli $mysqli): array
{
  // Satu query untuk semua stats
  $sql = "SELECT 
        (SELECT COUNT(id_pengajuan) FROM pengajuan_order WHERE status_pengajuan = 'dalam proses') as pengajuan_baru,
        
        (SELECT COUNT(id_order_produksi) FROM order_produksi WHERE status_order = 'proses') as order_aktif,
        
        (SELECT COUNT(id_timeline) FROM timeline 
         WHERE status_timeline != 'Selesai' 
         AND deadline BETWEEN NOW() AND NOW() + INTERVAL 7 DAY) as timeline_mendesak,
        
        (SELECT COUNT(id_order_produksi) FROM order_produksi 
         WHERE status_order = 'selesai' 
         AND YEAR(updated_at) = YEAR(NOW()) 
         AND MONTH(updated_at) = MONTH(NOW())) as order_selesai_bulan_ini
    ";

  $result = db_query($mysqli, $sql);
  return $result ? $result->fetch_assoc() : [
    'pengajuan_baru' => 0,
    'order_aktif' => 0,
    'timeline_mendesak' => 0,
    'order_selesai_bulan_ini' => 0
  ];
}

/**
 * Mengambil data untuk chart status order (PO/Manajer).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @return array Data untuk chart.
 */
function dashboard_get_po_order_chart(mysqli $mysqli): array
{
  $sql = "SELECT status_order, COUNT(id_order_produksi) as count 
            FROM order_produksi 
            GROUP BY status_order";
  $result = db_query($mysqli, $sql);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil 5 pengajuan order terbaru yang menunggu respon.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $limit Jumlah data yang diambil.
 * @return array Daftar pengajuan.
 */
function dashboard_get_po_pending_pengajuan(mysqli $mysqli, int $limit = 5): array
{
  $sql = "SELECT p.*, s.nama as nama_sekolah,
                   DATE_FORMAT(p.created_at, '%d %M %Y') AS tgl_pengajuan
            FROM pengajuan_order p
            JOIN sekolah s ON p.id_sekolah = s.id_sekolah
            WHERE p.status_pengajuan = 'dalam proses'
            ORDER BY p.created_at ASC -- (Yang terlama di atas)
            LIMIT ?";
  $result = db_query($mysqli, $sql, [$limit]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil 5 task timeline yang paling mendesak (deadline < 7 hari).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $limit Jumlah data yang diambil.
 * @return array Daftar task.
 */
function dashboard_get_po_urgent_tasks(mysqli $mysqli, int $limit = 5): array
{
  $sql = "SELECT 
                t.id_timeline, t.judul, t.deadline, 
                o.nomor_order, o.id_order_produksi
            FROM timeline t
            JOIN order_produksi o ON t.id_order_produksi = o.id_order_produksi
            WHERE t.status_timeline != 'Selesai'
            AND t.deadline BETWEEN NOW() AND NOW() + INTERVAL 7 DAY
            ORDER BY t.deadline ASC -- (Paling mendesak di atas)
            LIMIT ?";
  $result = db_query($mysqli, $sql, [$limit]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil semua statistik utama untuk dashboard KLIEN.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_klien ID user klien yang login.
 * @return array Statistik dalam bentuk array asosiatif.
 */
function dashboard_get_klien_stats(mysqli $mysqli, int $id_klien): array
{
  $sql = "SELECT 
        (SELECT COUNT(id_pengajuan) FROM pengajuan_order 
         WHERE id_user = ? AND status_pengajuan = 'dalam proses') as pengajuan_aktif,
        
        (SELECT COUNT(id_order_produksi) FROM order_produksi 
         WHERE id_klien = ? AND status_order = 'proses') as order_berjalan,
         
        (SELECT COUNT(id_order_produksi) FROM order_produksi 
         WHERE id_klien = ? AND status_order = 'selesai') as order_selesai,
        
        (SELECT COUNT(t.id_tiket) FROM tiket t
         JOIN order_produksi o ON t.id_order_produksi = o.id_order_produksi
         WHERE o.id_klien = ? AND t.status_tiket IN ('baru', 'proses')) as tiket_aktif
    ";

  $params = [$id_klien, $id_klien, $id_klien, $id_klien];

  $result = db_query($mysqli, $sql, $params);
  return $result ? $result->fetch_assoc() : [
    'pengajuan_aktif' => 0,
    'order_berjalan' => 0,
    'order_selesai' => 0,
    'tiket_aktif' => 0
  ];
}

/**
 * Mengambil daftar order milik klien (untuk tabel dashboard).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_klien ID user klien yang login.
 * @return array Daftar order.
 */
function dashboard_get_klien_orders(mysqli $mysqli, int $id_klien): array
{
  $sql = "SELECT 
                o.id_order_produksi, o.nomor_order, o.status_order,
                s.nama AS nama_sekolah,
                DATE_FORMAT(o.updated_at, '%d %M %Y') AS tgl_update
            FROM order_produksi o
            JOIN sekolah s ON o.id_sekolah = s.id_sekolah
            WHERE o.id_klien = ?
            ORDER BY o.updated_at DESC
            LIMIT 5";

  $result = db_query($mysqli, $sql, [$id_klien]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function dashboard_get_mp_stats(mysqli $mysqli): array
{
  $sql = "SELECT 
        (SELECT COUNT(id_order_produksi) FROM order_produksi WHERE status_order = 'baru') as order_baru,
        
        (SELECT COUNT(id_order_produksi) FROM order_produksi WHERE status_order = 'proses') as order_aktif,
        
        (SELECT COUNT(DISTINCT id_order_produksi) FROM qc_checklist 
         WHERE status_kelolosan IN ('Gagal Sebagian', 'Gagal Total')
         AND id_qc = (SELECT MAX(id_qc) FROM qc_checklist q2 WHERE q2.id_order_produksi = qc_checklist.id_order_produksi)
        ) as qc_gagal,
        
        (SELECT COUNT(id_order_produksi) FROM order_produksi 
         WHERE status_order = 'selesai' 
         AND YEAR(updated_at) = YEAR(NOW()) 
         AND MONTH(updated_at) = MONTH(NOW())) as order_selesai_bulan_ini
    ";

  $result = db_query($mysqli, $sql);
  return $result ? $result->fetch_assoc() : [
    'order_baru' => 0,
    'order_aktif' => 0,
    'qc_gagal' => 0,
    'order_selesai_bulan_ini' => 0
  ];
}

/**
 * Mengambil daftar 5 order terbaru yang gagal QC dan perlu rework.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $limit Jumlah data yang diambil.
 * @return array Daftar order gagal QC.
 */
function dashboard_get_mp_failed_qc(mysqli $mysqli, int $limit = 5): array
{
  $sql = "SELECT 
                q.id_qc, q.id_order_produksi, q.batch_number, q.status_kelolosan,
                o.nomor_order, s.nama AS nama_sekolah
            FROM qc_checklist q
            JOIN order_produksi o ON q.id_order_produksi = o.id_order_produksi
            JOIN sekolah s ON o.id_sekolah = s.id_sekolah
            WHERE q.status_kelolosan IN ('Gagal Sebagian', 'Gagal Total')
            ORDER BY q.tanggal_qc DESC
            LIMIT ?";

  $result = db_query($mysqli, $sql, [$limit]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// app/data/dashboard_data.php
// ... (fungsi dashboard_get_po_stats, dashboard_get_klien_stats, dll. tetap di sini) ...

/**
 * Mengambil semua statistik utama untuk dashboard Manajer Marketing.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @return array Statistik dalam bentuk array asosiatif.
 */
function dashboard_get_mm_stats(mysqli $mysqli): array
{
  $sql = "SELECT 
        (SELECT COUNT(id_prospek) FROM prospek WHERE status_prospek = 'baru') as prospek_baru,
        
        (SELECT COUNT(id_prospek) FROM prospek WHERE status_prospek = 'dalam proses') as prospek_aktif,
        
        (SELECT COUNT(id_prospek) FROM prospek 
         WHERE status_prospek = 'berhasil' 
         AND updated_at >= (NOW() - INTERVAL 30 DAY)) as konversi_30_hari,
        
        (SELECT COUNT(id_prospek) FROM prospek 
         WHERE status_prospek IN ('gagal', 'batal')
         AND updated_at >= (NOW() - INTERVAL 30 DAY)) as gagal_30_hari
    ";

  $result = db_query($mysqli, $sql);
  return $result ? $result->fetch_assoc() : [
    'prospek_baru' => 0,
    'prospek_aktif' => 0,
    'konversi_30_hari' => 0,
    'gagal_30_hari' => 0
  ];
}

/**
 * Mengambil data untuk chart status prospek (MM).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @return array Data untuk chart.
 */
function dashboard_get_mm_status_chart(mysqli $mysqli): array
{
  $sql = "SELECT status_prospek, COUNT(id_prospek) as count 
            FROM prospek 
            GROUP BY status_prospek";
  $result = db_query($mysqli, $sql);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil data performa Tim Marketing (Bar Chart).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @return array Data untuk chart.
 */
function dashboard_get_mm_staff_performance(mysqli $mysqli): array
{
  // Menghitung jumlah prospek (aktif + berhasil) yang ditangani per staf
  $sql = "SELECT u.nama, COUNT(p.id_prospek) as total_ditangani 
            FROM prospek p
            JOIN users u ON p.id_user = u.id_user
            WHERE p.status_prospek IN ('dalam proses', 'berhasil')
            AND u.role = 'tim_marketing'
            GROUP BY p.id_user 
            ORDER BY total_ditangani DESC";

  $result = db_query($mysqli, $sql);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil 5 prospek yang baru saja berhasil (dikonversi).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $limit Jumlah data yang diambil.
 * @return array Daftar prospek.
 */
function dashboard_get_mm_recent_success(mysqli $mysqli, int $limit = 5): array
{
  $sql = "SELECT p.id_prospek, p.narahubung, s.nama as nama_sekolah, 
                   DATE_FORMAT(p.updated_at, '%d %M %Y') AS tgl_konversi
            FROM prospek p
            JOIN sekolah s ON p.id_sekolah = s.id_sekolah
            WHERE p.status_prospek = 'berhasil'
            ORDER BY p.updated_at DESC
            LIMIT ?";

  $result = db_query($mysqli, $sql, [$limit]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil semua statistik utama untuk dashboard Tim Marketing (Perorangan).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_user ID user (tim marketing) yang login.
 * @return array Statistik dalam bentuk array asosiatif.
 */
function dashboard_get_tm_stats(mysqli $mysqli, int $id_user): array
{
  $sql = "SELECT 
        (SELECT COUNT(id_prospek) FROM prospek 
         WHERE id_user = ? AND status_prospek = 'dalam proses') as prospek_aktif,
        
        (SELECT COUNT(id_prospek) FROM prospek 
         WHERE id_user = ? AND status_prospek = 'berhasil' 
         AND updated_at >= (NOW() - INTERVAL 30 DAY)) as konversi_30_hari,
        
        (SELECT COUNT(id_prospek) FROM prospek 
         WHERE id_user = ? AND status_prospek IN ('gagal', 'batal')
         AND updated_at >= (NOW() - INTERVAL 30 DAY)) as gagal_30_hari
    ";

  $params = [$id_user, $id_user, $id_user];

  $result = db_query($mysqli, $sql, $params);
  return $result ? $result->fetch_assoc() : [
    'prospek_aktif' => 0,
    'konversi_30_hari' => 0,
    'gagal_30_hari' => 0
  ];
}

/**
 * Mengambil data untuk chart status prospek (Tim Marketing perorangan).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_user ID user (tim marketing) yang login.
 * @return array Data untuk chart.
 */
function dashboard_get_tm_status_chart(mysqli $mysqli, int $id_user): array
{
  $sql = "SELECT status_prospek, COUNT(id_prospek) as count 
            FROM prospek 
            WHERE id_user = ?
            AND status_prospek IN ('dalam proses', 'berhasil', 'gagal', 'batal')
            GROUP BY status_prospek";
  $result = db_query($mysqli, $sql, [$id_user]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil 5 prospek 'dalam proses' milik staf yang paling lama tidak di-update.
 * (Tugas yang perlu ditindak lanjuti)
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_user ID user (tim marketing) yang login.
 * @param int $limit Jumlah data yang diambil.
 * @return array Daftar prospek.
 */
function dashboard_get_tm_actionable_prospects(mysqli $mysqli, int $id_user, int $limit = 5): array
{
  $sql = "SELECT p.id_prospek, p.narahubung, s.nama as nama_sekolah,
                   DATE_FORMAT(p.updated_at, '%d %M %Y') AS tgl_update
            FROM prospek p
            JOIN sekolah s ON p.id_sekolah = s.id_sekolah
            WHERE p.id_user = ? AND p.status_prospek = 'dalam proses'
            ORDER BY p.updated_at ASC -- (Yang terlama di atas)
            LIMIT ?";

  $result = db_query($mysqli, $sql, [$id_user, $limit]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// app/data/dashboard_data.php
// ... (fungsi-fungsi dashboard Anda yang lain) ...

/**
 * Mengambil statistik utama untuk dashboard Tim Percetakan (Perorangan).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_user ID user (tim percetakan) yang login.
 * @return array Statistik dalam bentuk array asosiatif.
 */
function dashboard_get_percetakan_stats(mysqli $mysqli, int $id_user): array
{
  $sql = "SELECT 
        (SELECT COUNT(id_timeline) FROM timeline 
         WHERE id_user = ? AND status_timeline = 'Ditugaskan') as tugas_baru,
        
        (SELECT COUNT(id_timeline) FROM timeline 
         WHERE id_user = ? AND status_timeline = 'Dalam Proses') as tugas_dikerjakan,
         
        (SELECT COUNT(id_timeline) FROM timeline 
         WHERE id_user = ? AND status_timeline != 'Selesai' 
         AND deadline BETWEEN NOW() AND NOW() + INTERVAL 7 DAY) as tugas_mendesak,

        (SELECT COUNT(DISTINCT id_order_produksi) FROM qc_checklist 
         WHERE status_kelolosan IN ('Gagal Sebagian', 'Gagal Total')
         AND id_qc = (SELECT MAX(id_qc) FROM qc_checklist q2 WHERE q2.id_order_produksi = qc_checklist.id_order_produksi)
        ) as qc_gagal_terbaru
    ";

  // Parameter diulang untuk setiap subquery
  $params = [$id_user, $id_user, $id_user];

  $result = db_query($mysqli, $sql, $params);
  return $result ? $result->fetch_assoc() : [
    'tugas_baru' => 0,
    'tugas_dikerjakan' => 0,
    'tugas_mendesak' => 0,
    'qc_gagal_terbaru' => 0
  ];
}

/**
 * Mengambil semua task timeline AKTIF (Ditugaskan/Dalam Proses) untuk satu user.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_user ID user (tim percetakan) yang login.
 * @return array Daftar task.
 */
function dashboard_get_percetakan_active_tasks(mysqli $mysqli, int $id_user): array
{
  $sql = "SELECT 
                t.id_timeline, t.judul, t.deadline, t.status_timeline,
                o.nomor_order, o.id_order_produksi,
                s.nama AS nama_sekolah
            FROM timeline t
            JOIN order_produksi o ON t.id_order_produksi = o.id_order_produksi
            JOIN sekolah s ON o.id_sekolah = s.id_sekolah
            WHERE t.id_user = ? 
            AND t.status_timeline IN ('Ditugaskan', 'Dalam Proses')
            ORDER BY t.deadline ASC"; // Mendesak di atas

  $result = db_query($mysqli, $sql, [$id_user]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function dashboard_get_percetakan_chart(mysqli $mysqli, int $id_user): array
{
  $sql = "SELECT status_timeline, COUNT(id_timeline) as count 
            FROM timeline 
            WHERE id_user = ?
            AND status_timeline IN ('Ditugaskan', 'Dalam Proses')
            GROUP BY status_timeline";

  $result = db_query($mysqli, $sql, [$id_user]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil statistik utama untuk dashboard Desainer (Perorangan).
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_user ID user (desainer) yang login.
 * @return array Statistik dalam bentuk array asosiatif.
 */
function dashboard_get_desainer_stats(mysqli $mysqli, int $id_user): array
{
  $sql = "SELECT 
        (SELECT COUNT(id_timeline) FROM timeline 
         WHERE id_user = ? AND status_timeline = 'Ditugaskan') as tugas_baru,
        
        (SELECT COUNT(id_timeline) FROM timeline 
         WHERE id_user = ? AND status_timeline = 'Dalam Proses') as tugas_dikerjakan,
         
        (SELECT COUNT(id_timeline) FROM timeline 
         WHERE id_user = ? AND status_timeline = 'Selesai'
         AND updated_at >= (NOW() - INTERVAL 30 DAY)) as tugas_selesai_30_hari,

        (SELECT COUNT(id_timeline) FROM timeline 
         WHERE id_user = ? AND status_timeline != 'Selesai' 
         AND deadline BETWEEN NOW() AND NOW() + INTERVAL 7 DAY) as tugas_mendesak
    ";

  $params = [$id_user, $id_user, $id_user, $id_user];

  $result = db_query($mysqli, $sql, $params);
  return $result ? $result->fetch_assoc() : [
    'tugas_baru' => 0,
    'tugas_dikerjakan' => 0,
    'tugas_selesai_30_hari' => 0,
    'tugas_mendesak' => 0
  ];
}

/**
 * Mengambil data untuk chart status tugas (Desainer perorangan).
 * (Fungsi ini identik dengan 'dashboard_get_percetakan_chart'
 * tetapi kita buat terpisah agar mudah dikelola)
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_user ID user (desainer) yang login.
 * @return array Data untuk chart.
 */
function dashboard_get_desainer_chart(mysqli $mysqli, int $id_user): array
{
  $sql = "SELECT status_timeline, COUNT(id_timeline) as count 
            FROM timeline 
            WHERE id_user = ?
            AND status_timeline IN ('Ditugaskan', 'Dalam Proses')
            GROUP BY status_timeline";

  $result = db_query($mysqli, $sql, [$id_user]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Mengambil semua task timeline AKTIF (Ditugaskan/Dalam Proses) untuk satu Desainer.
 *
 * @param mysqli $mysqli Objek koneksi database.
 * @param int $id_user ID user (desainer) yang login.
 * @return array Daftar task.
 */
function dashboard_get_desainer_active_tasks(mysqli $mysqli, int $id_user): array
{
  $sql = "SELECT 
                t.id_timeline, t.judul, t.deadline, t.status_timeline,
                o.nomor_order, o.id_order_produksi,
                s.nama AS nama_sekolah
            FROM timeline t
            JOIN order_produksi o ON t.id_order_produksi = o.id_order_produksi
            JOIN sekolah s ON o.id_sekolah = s.id_sekolah
            WHERE t.id_user = ? 
            AND t.status_timeline IN ('Ditugaskan', 'Dalam Proses')
            ORDER BY t.deadline ASC"; // Mendesak di atas

  $result = db_query($mysqli, $sql, [$id_user]);
  return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
