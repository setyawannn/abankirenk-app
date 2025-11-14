-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2025 at 04:08 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `abankirenk_db_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `desain`
--

CREATE TABLE `desain` (
  `id_desain` smallint(5) UNSIGNED NOT NULL,
  `id_user` smallint(5) UNSIGNED NOT NULL,
  `id_order_produksi` int(10) UNSIGNED NOT NULL,
  `desain` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `desain`
--

INSERT INTO `desain` (`id_desain`, `id_user`, `id_order_produksi`, `desain`, `created_at`, `updated_at`) VALUES
(3, 4, 2, '/storage/documents/25/11/13/desain/5e4d836c-dummy.pdf', '2025-11-13 04:41:47', '2025-11-13 04:41:47'),
(4, 4, 2, '/storage/documents/25/11/13/desain/9f637d7b-inihanyalahcontoh.pdf', '2025-11-13 04:42:17', '2025-11-13 04:42:17'),
(5, 4, 3, '/storage/documents/25/11/14/desain/4dd96ccb-dummy.pdf', '2025-11-14 02:46:46', '2025-11-14 02:46:46');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` smallint(5) UNSIGNED NOT NULL,
  `id_order_produksi` int(10) UNSIGNED NOT NULL,
  `id_user` smallint(5) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id_feedback`, `id_order_produksi`, `id_user`, `rating`, `komentar`, `created_at`, `updated_at`) VALUES
(2, 2, 8, 4, 'AWOOOOOOKKKKKKKKKKKKKKKKKK', '2025-11-14 00:33:28', '2025-11-14 00:34:17'),
(3, 3, 11, 5, 'Mantapppp buah aloyy updated', '2025-11-14 03:07:47', '2025-11-14 03:07:55');

-- --------------------------------------------------------

--
-- Table structure for table `mou`
--

CREATE TABLE `mou` (
  `id_mou` smallint(5) UNSIGNED NOT NULL,
  `id_user` smallint(5) UNSIGNED NOT NULL,
  `mou` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mou`
--

INSERT INTO `mou` (`id_mou`, `id_user`, `mou`, `created_at`, `updated_at`) VALUES
(1, 5, 'Isi MoU yang sudah disesuaikan untuk SMAN 1 Contoh Kota...', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(4, 5, '/storage/documents/25/11/09/mou/f494a6fa-dummy.pdf', '2025-11-09 04:40:20', '2025-11-09 04:40:20'),
(5, 5, '/storage/documents/25/11/14/mou/295e2b68-inihanyalahcontoh.pdf', '2025-11-14 02:38:49', '2025-11-14 02:38:49');

-- --------------------------------------------------------

--
-- Table structure for table `order_produksi`
--

CREATE TABLE `order_produksi` (
  `id_order_produksi` int(10) UNSIGNED NOT NULL,
  `nomor_order` varchar(20) NOT NULL,
  `id_sekolah` smallint(5) UNSIGNED NOT NULL,
  `id_mou` smallint(5) UNSIGNED NOT NULL,
  `id_klien` smallint(5) UNSIGNED NOT NULL,
  `status_order` enum('baru','proses','selesai','batal') DEFAULT NULL,
  `narahubung` varchar(100) DEFAULT NULL,
  `no_narahubung` varchar(40) DEFAULT NULL,
  `kuantitas` smallint(5) UNSIGNED DEFAULT NULL,
  `halaman` smallint(6) DEFAULT NULL,
  `konsep` text DEFAULT NULL,
  `deadline` timestamp NULL DEFAULT NULL,
  `sequence` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_produksi`
--

INSERT INTO `order_produksi` (`id_order_produksi`, `nomor_order`, `id_sekolah`, `id_mou`, `id_klien`, `status_order`, `narahubung`, `no_narahubung`, `kuantitas`, `halaman`, `konsep`, `deadline`, `sequence`, `created_at`, `updated_at`) VALUES
(2, 'ORD251109001', 1, 4, 8, 'proses', 'Eko Rusdianto', '0814871211211', 150, 64, 'adwadawdawdawdwa', '2025-11-29 17:00:00', 1, '2025-11-09 04:40:20', '2025-11-14 02:12:47'),
(3, 'ORD251114001', 6, 5, 11, 'selesai', 'Aloy Kentang', '081999888220', 80, 102, 'Sak ono e mas', '2025-11-17 17:00:00', 1, '2025-11-14 02:38:49', '2025-11-14 03:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_order`
--

CREATE TABLE `pengajuan_order` (
  `id_pengajuan` smallint(5) UNSIGNED NOT NULL,
  `id_sekolah` smallint(5) UNSIGNED NOT NULL,
  `id_user` smallint(5) UNSIGNED NOT NULL,
  `nomor_pengajuan` varchar(20) DEFAULT NULL,
  `status_pengajuan` enum('berhasil','gagal','batal','dalam proses') DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `balasan` text DEFAULT NULL,
  `id_user_po` smallint(5) UNSIGNED DEFAULT NULL COMMENT 'ID User (PO) yang membalas',
  `tanggal_balasan` timestamp NULL DEFAULT NULL COMMENT 'Waktu PO terakhir membalas',
  `narahubung` varchar(100) DEFAULT NULL,
  `no_narahubung` varchar(40) DEFAULT NULL,
  `sequence` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengajuan_order`
--

INSERT INTO `pengajuan_order` (`id_pengajuan`, `id_sekolah`, `id_user`, `nomor_pengajuan`, `status_pengajuan`, `pesan`, `balasan`, `id_user_po`, `tanggal_balasan`, `narahubung`, `no_narahubung`, `sequence`, `created_at`, `updated_at`) VALUES
(1, 2, 8, 'RO251106001', 'dalam proses', 'Kami dari SMA Harapan Bangsa tertarik untuk membuat yearbook.', NULL, NULL, NULL, 'Hani', '089876543210', NULL, '2025-11-06 13:02:29', '2025-11-09 01:29:55'),
(2, 5, 8, 'RO251107001', 'gagal', '<p>Kami dari SMA Harapan Bangsa tertarik untuk membuat yearbook.</p><figure class=\"image\"><img style=\"aspect-ratio:1200/675;\" src=\"http://localhost/abankirenk-app/public/storage/images/25/11/08/prospek/0b1f9952-angry.webp\" width=\"1200\" height=\"675\"></figure>', '<p>Sorry to say kon weird asf balenono</p>', 5, '2025-11-09 02:08:21', 'Ini pasti berhasil', '081498210811102', NULL, '2025-11-08 12:37:30', '2025-11-09 02:08:21'),
(3, 1, 8, 'RO251108001', 'berhasil', '<p>dawawdadwawdawd</p><figure class=\"image image_resized\" style=\"width:36.73%;\"><img style=\"aspect-ratio:1200/675;\" src=\"http://localhost/abankirenk-app/public/storage/images/25/11/08/prospek/fea0cb60-angry.webp\" width=\"1200\" height=\"675\"></figure>', '<p>Selamat Sore Bapak Wahyu A5. &nbsp;&nbsp;<br><br>Terima kasih telah mempercayakan yearbook anda kepada AbankIrenk, kami baru saja memutuskan untuk menerima pengajuan yang telah diajukan untuk tidak lanjutnya dan lebih detailnya silakan hubungi wa/telp AbankIrenk yang sudah tertera untuk berdiskusi lebih lanjut mengenai apa yang dibutuhkan oleh Bapak Wahyu. &nbsp;✌️💘<br><br>Selamat Sore.</p>', 5, '2025-11-09 02:32:18', 'Eko Rusdianto', '0814871211211', 1, '2025-11-08 12:55:34', '2025-11-09 02:32:18'),
(4, 6, 11, 'RO251114001', 'berhasil', '<p>Lorem ipsum dolor sit amet.</p>', '<p>OKEEE ACC BOLO</p>', 5, '2025-11-14 02:38:02', 'Aloy Kentang', '081999888220', 1, '2025-11-14 02:36:23', '2025-11-14 02:38:02');

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id_pengiriman` smallint(5) UNSIGNED NOT NULL,
  `id_order_produksi` int(10) UNSIGNED NOT NULL,
  `id_user` smallint(5) UNSIGNED NOT NULL,
  `ekspedisi` varchar(100) DEFAULT NULL,
  `no_resi` varchar(50) DEFAULT NULL,
  `tracking_url` text DEFAULT NULL,
  `tanggal_buat` timestamp NULL DEFAULT NULL,
  `tanggal_sampai` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengiriman`
--

INSERT INTO `pengiriman` (`id_pengiriman`, `id_order_produksi`, `id_user`, `ekspedisi`, `no_resi`, `tracking_url`, `tanggal_buat`, `tanggal_sampai`, `created_at`, `updated_at`) VALUES
(4, 2, 3, 'Shopee Express', 'SPXID05740590895B', 'https://spx.co.id/track?SPXID05740590895B', '2025-11-12 17:00:00', '2025-11-13 14:36:49', '2025-11-13 06:48:37', '2025-11-13 14:36:49'),
(5, 3, 3, 'J&T ', 'JX6226019237', 'https://jet.co.id/track?txtexpress=JX6226019237', '2025-11-13 17:00:00', '2025-11-14 03:07:36', '2025-11-14 02:54:20', '2025-11-14 03:07:36');

-- --------------------------------------------------------

--
-- Table structure for table `prospek`
--

CREATE TABLE `prospek` (
  `id_prospek` smallint(5) UNSIGNED NOT NULL,
  `id_user` smallint(5) UNSIGNED NOT NULL,
  `id_sekolah` smallint(5) UNSIGNED NOT NULL,
  `status_prospek` enum('baru','berhasil','gagal','batal','dalam proses') DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `narahubung` varchar(100) DEFAULT NULL,
  `no_narahubung` varchar(40) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prospek`
--

INSERT INTO `prospek` (`id_prospek`, `id_user`, `id_sekolah`, `status_prospek`, `deskripsi`, `catatan`, `narahubung`, `no_narahubung`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'dalam proses', 'Prospek awal untuk SMAN 1 Contoh Kota, tahun ajaran 2025/2026.', 'Deal tercapai setelah presentasi.', 'Ibu Rina', '081234567890', '2025-11-06 13:02:29', '2025-11-14 01:26:15'),
(2, 2, 3, 'berhasil', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quod alias, delectus laudantium iure dignissimos placeat a nihil numquam! Rerum, praesentium. Placeat iste suscipit dolorem explicabo? Possimus, excepturi accusamus laborum deleniti, libero vitae repudiandae tenetur, a nemo recusandae odio minima iure incidunt. Doloremque, laborum autem sed hic excepturi tempore molestias distinctio quis mollitia quaerat impedit assumenda, non, cum reiciendis temporibus porro accusamus commodi. In veniam minima repudiandae similique inventore ipsum quas quia. Dignissimos deleniti explicabo laborum est doloribus voluptatibus illum, temporibus repellat architecto reiciendis cumque. Officia, eius nobis, libero quasi architecto rem impedit hic error velit excepturi eos enim quo porro!', '<p>&nbsp;</p><figure class=\"image image_resized\" style=\"width:22.66%;\"><img style=\"aspect-ratio:5472/3648;\" src=\"http://localhost/abankirenk-app/public/storage/images/25/11/06/prospek/5d3e2d00-yuki.webp\" width=\"5472\" height=\"3648\"></figure><p><mark class=\"pen-red\">Sulit</mark> <mark class=\"marker-yellow\">Dim<strong>enger</strong>tos Sem</mark>oga <span style=\"color:hsl(30,75%,60%);\">Ngisingmu Atos awd </span><span style=\"color:hsl(0,0%,0%);\">dawdawd</span><span style=\"color:hsl(0,0%,0%);font-family:\'Courier New\', Courier, monospace;\"> Awdawdawdwa </span><span style=\"color:hsl(0,0%,0%);\">dawdwad </span><span class=\"text-big\" style=\"color:hsl(0,0%,0%);\">BAPAK MU JOKOPI kk</span><br><br><br>&nbsp;</p>', 'Sri Mulyani', '081333717212', '2025-11-06 13:02:29', '2025-11-14 01:28:23'),
(4, 2, 3, 'gagal', 'adwadwadwawddaw', 'adwadwadwawddaw', 'Joko Wihoho', '081333717212', '2025-11-06 13:02:29', '2025-11-14 01:28:30'),
(5, 2, 2, 'baru', 'llokpokpokpokpokpkpo;\';', '<p>WALAWEEEEEEE</p>', 'TERBARU LAGI', '019210921121', '2025-11-06 13:02:29', '2025-11-14 01:27:18'),
(6, 2, 2, 'dalam proses', 'adwwadawdawdawddawdawd', '<p>Sing Nggenah Le</p>', 'Ini pasti berhasil', '019210921121', '2025-11-06 13:02:29', '2025-11-14 01:30:17'),
(8, 2, 5, 'baru', 'adwcacwacawcxczegtdr hdrgdrgdz', 'adwcacwacawcxczegtdr hdrgdrgdz', 'Ini Manual Sekolah', '08218129011', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(9, 2, 6, 'baru', 'DWAAWDawdawdad', 'DWAAWDawdawdad', 'JokoADWDW', '09102910210', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(10, 2, 4, 'batal', 'adwawdawdawdawdawdawd', 'adwawdawdawdawdawdawd', 'INI CEK FORM', '082102811212', '2025-11-06 13:02:29', '2025-11-14 01:30:47'),
(11, 9, 1, 'baru', 'ISian Tahu', 'ISian Tahu', 'Sing Bener Le', '082121111', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(12, 9, 5, 'baru', 'Client', '', 'Creat', '08129102901', '2025-11-06 14:12:32', '2025-11-06 14:12:32'),
(13, 9, 5, 'dalam proses', 'ru garap', '<p>dadawwaddawdaw<br>&nbsp;</p><ol><li><figure class=\"image image_resized\" style=\"width:14.35%;\"><img style=\"aspect-ratio:1315/1115;\" src=\"http://localhost/abankirenk-app/public/storage/images/25/11/07/prospek/1917220b-logo-scp.webp\" width=\"1315\" height=\"1115\"></figure></li><li><figure class=\"image\"><img></figure></li></ol>', 'Sri ', '08109029102', '2025-11-07 02:08:54', '2025-11-08 12:51:57'),
(14, 9, 5, 'dalam proses', 'cepat ', '<p>nhinhn</p><figure class=\"image\"><img style=\"aspect-ratio:300/168;\" src=\"http://localhost/abankirenk-app/public/storage/images/25/11/07/prospek/b847db68-leclerc.webp\" width=\"300\" height=\"168\"></figure>', 'sri', '0808098', '2025-11-07 02:24:17', '2025-11-07 02:25:39');

-- --------------------------------------------------------

--
-- Table structure for table `qc_checklist`
--

CREATE TABLE `qc_checklist` (
  `id_qc` int(10) UNSIGNED NOT NULL,
  `id_order_produksi` int(10) UNSIGNED NOT NULL,
  `id_user` smallint(5) UNSIGNED NOT NULL,
  `batch_number` varchar(20) NOT NULL COMMENT 'Format: QCymdXXX',
  `sequence` int(10) UNSIGNED NOT NULL,
  `tanggal_qc` timestamp NOT NULL DEFAULT current_timestamp(),
  `jumlah_sampel_diperiksa` int(11) DEFAULT NULL,
  `check_cover_material` tinyint(1) DEFAULT 0,
  `check_cover_fisik` tinyint(1) DEFAULT 0,
  `check_jilid_kekuatan` tinyint(1) DEFAULT 0,
  `check_laminasi_kerapian` tinyint(1) DEFAULT 0,
  `check_cover_posisi` tinyint(1) DEFAULT 0,
  `check_cetak_ketajaman` tinyint(1) DEFAULT 0,
  `check_cetak_warna` tinyint(1) DEFAULT 0,
  `check_cetak_kecerahan` tinyint(1) DEFAULT 0,
  `check_cetak_kebersihan` tinyint(1) DEFAULT 0,
  `check_halaman_urutan` tinyint(1) DEFAULT 0,
  `check_halaman_kelengkapan` tinyint(1) DEFAULT 0,
  `check_pemotongan_presisi` tinyint(1) DEFAULT 0,
  `check_halaman_nomor` tinyint(1) DEFAULT 0,
  `persentase_lolos` decimal(5,2) DEFAULT NULL COMMENT 'Skor kalkulasi bobot',
  `status_kelolosan` enum('Lolos','Gagal Sebagian','Gagal Total') NOT NULL,
  `jenis_cacat` varchar(120) DEFAULT NULL,
  `jumlah_cacat` int(11) DEFAULT 0,
  `bukti_foto` varchar(255) DEFAULT NULL,
  `catatan_qc` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `qc_checklist`
--

INSERT INTO `qc_checklist` (`id_qc`, `id_order_produksi`, `id_user`, `batch_number`, `sequence`, `tanggal_qc`, `jumlah_sampel_diperiksa`, `check_cover_material`, `check_cover_fisik`, `check_jilid_kekuatan`, `check_laminasi_kerapian`, `check_cover_posisi`, `check_cetak_ketajaman`, `check_cetak_warna`, `check_cetak_kecerahan`, `check_cetak_kebersihan`, `check_halaman_urutan`, `check_halaman_kelengkapan`, `check_pemotongan_presisi`, `check_halaman_nomor`, `persentase_lolos`, `status_kelolosan`, `jenis_cacat`, `jumlah_cacat`, `bukti_foto`, `catatan_qc`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 'QC251113001', 1, '2025-11-13 05:34:20', 25, 1, 0, 1, 1, 1, 0, 1, 0, 1, 1, 1, 0, 1, 70.00, 'Gagal Total', 'Jilid Miring Kaya Otaknya', 2, '/storage/images/25/11/13/qc/34b7cebe-risolmayo.webp', 'Anuuu mbalen iki', '2025-11-13 05:34:20', '2025-11-13 05:34:20'),
(2, 2, 6, 'QC251113002', 2, '2025-11-13 14:02:26', 60, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 100.00, 'Lolos', '', 0, '/storage/images/25/11/13/qc/2861dd35-yuki.webp', 'Amann boss lanjutt', '2025-11-13 14:02:26', '2025-11-13 14:02:26'),
(3, 3, 6, 'QC251114001', 1, '2025-11-14 02:49:45', 15, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 100.00, 'Lolos', '', 0, '/storage/images/25/11/14/qc/1f815a82-pupr.webp', '-', '2025-11-14 02:49:45', '2025-11-14 02:49:45');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah`
--

CREATE TABLE `sekolah` (
  `id_sekolah` smallint(5) UNSIGNED NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `kontak` varchar(40) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah`
--

INSERT INTO `sekolah` (`id_sekolah`, `nama`, `lokasi`, `kontak`, `created_at`, `updated_at`) VALUES
(1, 'SMA Negeri 1 Contoh Kota', 'Jl. Pendidikan No. 1, Contoh Kota', '021-123456', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(2, 'SMA Harapan Bangsa', 'Jl. Merdeka No. 10, Kota Maju', '022-789012', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(3, 'SMK Karya Muda', 'Jl. Industri No. 5, Distrik Kreatif', '031-345678', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(4, 'Tahu Kotak', 'Segita Bermuda', '083 212 108', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(5, 'Sekolah Manual', 'Jalan Danau Kono Adoh', '02133902121', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(6, 'SMK Badak Bercula 16', 'Jalan Adoh kono Anjay', '08201291', '2025-11-06 13:02:29', '2025-11-06 13:02:29');

-- --------------------------------------------------------

--
-- Table structure for table `template_desain`
--

CREATE TABLE `template_desain` (
  `id_template_desain` smallint(5) UNSIGNED NOT NULL,
  `judul` varchar(40) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `template_desain` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `template_desain`
--

INSERT INTO `template_desain` (`id_template_desain`, `judul`, `deskripsi`, `template_desain`, `created_at`, `updated_at`) VALUES
(1, 'Desain Modern Minimalis', 'Template dengan layout bersih dan modern.', NULL, '2025-11-06 13:02:29', '2025-11-07 02:40:46'),
(2, 'Desain Tema Vintage', 'Template dengan nuansa retro dan klasik.', NULL, '2025-11-06 13:02:29', '2025-11-07 02:40:49'),
(5, 'Desain Tema Vintage 02', 'dfgfghjkll', '/storage/images/25/11/07/desain/c86703cf-inihanyalahcontoh.pdf', '2025-11-07 02:45:53', '2025-11-07 02:46:22'),
(6, 'TEST PDF BROKKK', 'adwadwdawadwawdadwadwadwadw', '/storage/documents/25/11/07/desain/5930f6bc-inihanyalahcontoh.pdf', '2025-11-07 02:59:43', '2025-11-07 02:59:43');

-- --------------------------------------------------------

--
-- Table structure for table `template_mou`
--

CREATE TABLE `template_mou` (
  `id_template_mou` smallint(5) UNSIGNED NOT NULL,
  `judul` varchar(40) DEFAULT NULL,
  `mou` text DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `template_mou`
--

INSERT INTO `template_mou` (`id_template_mou`, `judul`, `mou`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Template MoU Standar', '', 'Gunakan untuk penawaran umum', '2025-11-06 13:02:29', '2025-11-07 01:18:47'),
(4, 'Quidem hic quaerat est.', '/storage/images/25/11/07/mou/d37b357a-dummy.pdf', 'ljjjijkklklklklk', '2025-11-07 02:11:50', '2025-11-07 02:11:50'),
(5, 'Kepo EDITED MANTAP', '/storage/images/25/11/07/mou/729793d4-inihanyalahcontoh.pdf', 'wertyuio', '2025-11-07 02:26:08', '2025-11-07 02:26:08');

-- --------------------------------------------------------

--
-- Table structure for table `tiket`
--

CREATE TABLE `tiket` (
  `id_tiket` smallint(5) UNSIGNED NOT NULL,
  `id_user` smallint(5) UNSIGNED NOT NULL,
  `id_order_produksi` int(10) UNSIGNED NOT NULL,
  `nomor_komplain` varchar(20) DEFAULT NULL COMMENT 'Format: COMymdXXX',
  `sequence` int(10) UNSIGNED DEFAULT NULL,
  `kategori` enum('keluhan','pertanyaan','lainnya') DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `link_video` text DEFAULT NULL COMMENT 'Link opsional dari klien',
  `status_tiket` enum('belum dibalas','dibalas') DEFAULT NULL,
  `respon` text DEFAULT NULL,
  `status_retur` enum('pending','disetujui','ditolak') DEFAULT NULL,
  `tanggal_respon` timestamp NULL DEFAULT NULL,
  `id_user_cs` smallint(5) UNSIGNED DEFAULT NULL COMMENT 'ID User (CS) yang membalas',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tiket`
--

INSERT INTO `tiket` (`id_tiket`, `id_user`, `id_order_produksi`, `nomor_komplain`, `sequence`, `kategori`, `deskripsi`, `link_video`, `status_tiket`, `respon`, `status_retur`, `tanggal_respon`, `id_user_cs`, `created_at`, `updated_at`) VALUES
(2, 8, 2, 'COM251113001', 1, 'keluhan', '<p>DADAWDAW</p><p>&nbsp;</p><figure class=\"image\"><img style=\"aspect-ratio:1200/800;\" src=\"http://localhost/abankirenk-app/public/storage/images/25/11/13/prospek/0eff5c5e-Gedung-DPR.webp\" width=\"1200\" height=\"800\"></figure>', 'https://vt.tiktok.com/Yntkts', 'dibalas', '<p>Gaje lu anjay</p>', 'ditolak', '2025-11-13 15:25:53', 7, '2025-11-13 15:12:45', '2025-11-13 23:58:27');

-- --------------------------------------------------------

--
-- Table structure for table `timeline`
--

CREATE TABLE `timeline` (
  `id_timeline` smallint(5) UNSIGNED NOT NULL,
  `id_order_produksi` int(10) UNSIGNED NOT NULL,
  `id_user` smallint(5) UNSIGNED NOT NULL,
  `judul` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `status_timeline` enum('Ditugaskan','Dalam Proses','Selesai') DEFAULT NULL,
  `deadline` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timeline`
--

INSERT INTO `timeline` (`id_timeline`, `id_order_produksi`, `id_user`, `judul`, `deskripsi`, `status_timeline`, `deadline`, `created_at`, `updated_at`) VALUES
(4, 2, 4, 'Menyesuaikan Foto Pada Desain BLABLABLABA BLIBLI BLI BLU BLU BLUUUU', 'Sesuaikan desain dengan foto foto yang sudah dipilih oleh user\r\n', 'Dalam Proses', '2025-11-12 17:00:00', '2025-11-11 06:02:42', '2025-11-14 02:16:28'),
(5, 2, 6, 'Cek contoh sample Buku', 'cek aja lah pokoknya', 'Dalam Proses', '2025-11-14 17:00:00', '2025-11-11 09:15:05', '2025-11-13 05:30:52'),
(7, 3, 4, 'Buat Desainnya', '-', 'Selesai', '2025-11-16 17:00:00', '2025-11-14 02:42:37', '2025-11-14 02:46:15'),
(8, 3, 4, 'Pasang Foto', '-', 'Selesai', '2025-11-17 17:00:00', '2025-11-14 02:43:01', '2025-11-14 02:46:35'),
(9, 3, 6, 'Siapkan bahan cetak', '-', 'Selesai', '2025-11-10 17:00:00', '2025-11-14 02:43:35', '2025-11-14 02:48:40'),
(10, 3, 6, 'Cetak Dimulai', '', 'Selesai', '2025-11-19 17:00:00', '2025-11-14 02:44:10', '2025-11-14 02:48:42'),
(11, 3, 6, 'Lakukan QC', '', 'Selesai', '2025-11-20 17:00:00', '2025-11-14 02:44:46', '2025-11-14 02:48:45'),
(12, 3, 6, 'Kirim ke Klien', '', 'Selesai', '2025-11-23 17:00:00', '2025-11-14 02:45:10', '2025-11-14 02:48:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` smallint(5) UNSIGNED NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('manajer_marketing','tim_marketing','manajer_produksi','desainer','project_officer','tim_percetakan','customer_service','klien') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Andi Manajer', 'manajer_marketing', '$2y$10$5D0wUNWYHTx1/TzF67IpA.zXErtKUJBAgzrrnELUoXn3wv7spg1uG', 'andi.manajer@example.com', 'manajer_marketing', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(2, 'Budi Marketing', 'tim_marketing_1', '$2y$10$5D0wUNWYHTx1/TzF67IpA.zXErtKUJBAgzrrnELUoXn3wv7spg1uG', 'budi.marketing@example.com', 'tim_marketing', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(3, 'Charlie Produksi', 'manajer_produksi', '$2y$10$5D0wUNWYHTx1/TzF67IpA.zXErtKUJBAgzrrnELUoXn3wv7spg1uG', 'charlie.produksi@example.com', 'manajer_produksi', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(4, 'Dina Desainer', 'desainer_1', '$2y$10$5D0wUNWYHTx1/TzF67IpA.zXErtKUJBAgzrrnELUoXn3wv7spg1uG', 'dina.desainer@example.com', 'desainer', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(5, 'Eko Project Officer', 'project_officer_1', '$2y$10$5D0wUNWYHTx1/TzF67IpA.zXErtKUJBAgzrrnELUoXn3wv7spg1uG', 'eko.po@example.com', 'project_officer', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(6, 'Fani Percetakan', 'tim_percetakan_1', '$2y$10$5D0wUNWYHTx1/TzF67IpA.zXErtKUJBAgzrrnELUoXn3wv7spg1uG', 'fani.cetak@example.com', 'tim_percetakan', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(7, 'Gilang CS', 'cs_1', '$2y$10$5D0wUNWYHTx1/TzF67IpA.zXErtKUJBAgzrrnELUoXn3wv7spg1uG', 'gilang.cs@example.com', 'customer_service', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(8, 'Hani Klien', 'klien_1', '$2y$10$5D0wUNWYHTx1/TzF67IpA.zXErtKUJBAgzrrnELUoXn3wv7spg1uG', 'hani.klien@example.com', 'klien', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(9, 'Heru Marketing', 'tim_marketing_2', '$2y$10$5D0wUNWYHTx1/TzF67IpA.zXErtKUJBAgzrrnELUoXn3wv7spg1uG', 'heru.marketing@example.com', 'tim_marketing', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(10, 'Sisi Desainer', 'desainer_2', '$2y$10$5D0wUNWYHTx1/TzF67IpA.zXErtKUJBAgzrrnELUoXn3wv7spg1uG', 'sisi.desainer@example.com', 'desainer', '2025-11-06 13:02:29', '2025-11-06 13:02:29'),
(11, 'Aloy Kentang', 'admin', '$2y$10$l9OSDYGvKt8xnUhNyNgjz.CrcEXgyWrzozXtKTxewXR1AwOPjcG.i', 'admin@gmail.com', 'klien', '2025-11-14 02:34:24', '2025-11-14 02:34:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `desain`
--
ALTER TABLE `desain`
  ADD PRIMARY KEY (`id_desain`),
  ADD KEY `idx_desain_user` (`id_user`),
  ADD KEY `idx_desain_order` (`id_order_produksi`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`),
  ADD KEY `idx_feedback_order` (`id_order_produksi`),
  ADD KEY `idx_feedback_user` (`id_user`);

--
-- Indexes for table `mou`
--
ALTER TABLE `mou`
  ADD PRIMARY KEY (`id_mou`),
  ADD KEY `idx_mou_user` (`id_user`);

--
-- Indexes for table `order_produksi`
--
ALTER TABLE `order_produksi`
  ADD PRIMARY KEY (`id_order_produksi`),
  ADD UNIQUE KEY `nomor_order` (`nomor_order`),
  ADD KEY `idx_order_user` (`id_klien`),
  ADD KEY `idx_order_mou` (`id_mou`),
  ADD KEY `idx_order_sekolah` (`id_sekolah`);

--
-- Indexes for table `pengajuan_order`
--
ALTER TABLE `pengajuan_order`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `idx_pengajuan_user` (`id_user`),
  ADD KEY `idx_pengajuan_sekolah` (`id_sekolah`),
  ADD KEY `idx_nomor_pengajuan` (`nomor_pengajuan`),
  ADD KEY `fk_pengajuan_po` (`id_user_po`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id_pengiriman`),
  ADD KEY `idx_pengiriman_order` (`id_order_produksi`);

--
-- Indexes for table `prospek`
--
ALTER TABLE `prospek`
  ADD PRIMARY KEY (`id_prospek`),
  ADD KEY `idx_prospek_user` (`id_user`),
  ADD KEY `idx_prospek_sekolah` (`id_sekolah`);

--
-- Indexes for table `qc_checklist`
--
ALTER TABLE `qc_checklist`
  ADD PRIMARY KEY (`id_qc`),
  ADD KEY `idx_qc_order` (`id_order_produksi`),
  ADD KEY `idx_qc_user` (`id_user`),
  ADD KEY `idx_batch_number` (`batch_number`);

--
-- Indexes for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`id_sekolah`);

--
-- Indexes for table `template_desain`
--
ALTER TABLE `template_desain`
  ADD PRIMARY KEY (`id_template_desain`);

--
-- Indexes for table `template_mou`
--
ALTER TABLE `template_mou`
  ADD PRIMARY KEY (`id_template_mou`);

--
-- Indexes for table `tiket`
--
ALTER TABLE `tiket`
  ADD PRIMARY KEY (`id_tiket`),
  ADD KEY `idx_tiket_user` (`id_user`),
  ADD KEY `idx_tiket_order` (`id_order_produksi`),
  ADD KEY `idx_nomor_komplain` (`nomor_komplain`),
  ADD KEY `fk_tiket_cs` (`id_user_cs`);

--
-- Indexes for table `timeline`
--
ALTER TABLE `timeline`
  ADD PRIMARY KEY (`id_timeline`),
  ADD KEY `idx_timeline_user` (`id_user`),
  ADD KEY `idx_timeline_order` (`id_order_produksi`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `desain`
--
ALTER TABLE `desain`
  MODIFY `id_desain` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mou`
--
ALTER TABLE `mou`
  MODIFY `id_mou` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_produksi`
--
ALTER TABLE `order_produksi`
  MODIFY `id_order_produksi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengajuan_order`
--
ALTER TABLE `pengajuan_order`
  MODIFY `id_pengajuan` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `id_pengiriman` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `prospek`
--
ALTER TABLE `prospek`
  MODIFY `id_prospek` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `qc_checklist`
--
ALTER TABLE `qc_checklist`
  MODIFY `id_qc` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `id_sekolah` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `template_desain`
--
ALTER TABLE `template_desain`
  MODIFY `id_template_desain` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `template_mou`
--
ALTER TABLE `template_mou`
  MODIFY `id_template_mou` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tiket`
--
ALTER TABLE `tiket`
  MODIFY `id_tiket` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `timeline`
--
ALTER TABLE `timeline`
  MODIFY `id_timeline` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `desain`
--
ALTER TABLE `desain`
  ADD CONSTRAINT `desain_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_desain_order_int` FOREIGN KEY (`id_order_produksi`) REFERENCES `order_produksi` (`id_order_produksi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_order_int` FOREIGN KEY (`id_order_produksi`) REFERENCES `order_produksi` (`id_order_produksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_feedback_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mou`
--
ALTER TABLE `mou`
  ADD CONSTRAINT `mou_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE;

--
-- Constraints for table `order_produksi`
--
ALTER TABLE `order_produksi`
  ADD CONSTRAINT `fk_order_mou` FOREIGN KEY (`id_mou`) REFERENCES `mou` (`id_mou`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_produksi_ibfk_1` FOREIGN KEY (`id_klien`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_produksi_ibfk_3` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON UPDATE CASCADE;

--
-- Constraints for table `pengajuan_order`
--
ALTER TABLE `pengajuan_order`
  ADD CONSTRAINT `fk_pengajuan_po` FOREIGN KEY (`id_user_po`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pengajuan_order_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pengajuan_order_ibfk_2` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON UPDATE CASCADE;

--
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `fk_pengiriman_order_int` FOREIGN KEY (`id_order_produksi`) REFERENCES `order_produksi` (`id_order_produksi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `prospek`
--
ALTER TABLE `prospek`
  ADD CONSTRAINT `prospek_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `prospek_ibfk_2` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON UPDATE CASCADE;

--
-- Constraints for table `qc_checklist`
--
ALTER TABLE `qc_checklist`
  ADD CONSTRAINT `fk_qc_order` FOREIGN KEY (`id_order_produksi`) REFERENCES `order_produksi` (`id_order_produksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_qc_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE;

--
-- Constraints for table `tiket`
--
ALTER TABLE `tiket`
  ADD CONSTRAINT `fk_tiket_cs` FOREIGN KEY (`id_user_cs`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tiket_order_int` FOREIGN KEY (`id_order_produksi`) REFERENCES `order_produksi` (`id_order_produksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tiket_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE;

--
-- Constraints for table `timeline`
--
ALTER TABLE `timeline`
  ADD CONSTRAINT `fk_timeline_order_int` FOREIGN KEY (`id_order_produksi`) REFERENCES `order_produksi` (`id_order_produksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `timeline_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
