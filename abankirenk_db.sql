-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 10:15 PM
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
-- Database: `abankirenk_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `desain`
--

CREATE TABLE `desain` (
  `design_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `template_desain_id` int(11) DEFAULT NULL,
  `desainer_id` int(11) NOT NULL,
  `preview_path` varchar(255) DEFAULT NULL,
  `status_approval` varchar(20) NOT NULL,
  `feedback_klien` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `pengiriman_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `nps_score` int(11) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `sentimen` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mou`
--

CREATE TABLE `mou` (
  `mou_id` int(11) NOT NULL,
  `nomor_mou` varchar(40) DEFAULT NULL,
  `prospek_id` int(11) NOT NULL,
  `versi` int(11) DEFAULT 1,
  `nilai_kontrak` decimal(15,2) DEFAULT NULL,
  `ruang_lingkup` text DEFAULT NULL,
  `timeline` date DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL,
  `nomor_order` varchar(40) DEFAULT NULL,
  `mou_id` int(11) NOT NULL,
  `konsep` text DEFAULT NULL,
  `jumlah_halaman` int(11) DEFAULT NULL,
  `kuantitas_cetak` int(11) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `pengiriman_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `nomor_resi` varchar(60) DEFAULT NULL,
  `kurir` varchar(40) DEFAULT NULL,
  `status_pengiriman` varchar(30) NOT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `tanggal_terima` date DEFAULT NULL,
  `tracking_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prospek_sekolah`
--

CREATE TABLE `prospek_sekolah` (
  `prospek_id` int(11) NOT NULL,
  `status` varchar(30) NOT NULL,
  `staf_id` int(11) DEFAULT NULL,
  `tipe_prospek` varchar(20) NOT NULL,
  `tanggal_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `catatan` text DEFAULT NULL,
  `sekolah_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qc_checklist`
--

CREATE TABLE `qc_checklist` (
  `qc_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `batch_number` varchar(30) DEFAULT NULL,
  `qc_by` int(11) NOT NULL,
  `qc_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `jumlah_sampel_diperiksa` int(11) DEFAULT NULL,
  `check_cover_material` tinyint(1) DEFAULT NULL,
  `check_cover_fisik` tinyint(1) DEFAULT NULL,
  `check_jilid_kekuatan` tinyint(1) DEFAULT NULL,
  `check_laminasi_kerapian` tinyint(1) DEFAULT NULL,
  `check_cover_posisi` tinyint(1) DEFAULT NULL,
  `check_cetak_ketajaman` tinyint(1) DEFAULT NULL,
  `check_cetak_warna` tinyint(1) DEFAULT NULL,
  `check_cetak_kecerahan` tinyint(1) DEFAULT NULL,
  `check_cetak_kebersihan` tinyint(1) DEFAULT NULL,
  `check_halaman_urutan` tinyint(1) DEFAULT NULL,
  `check_halaman_kelengkapan` tinyint(1) DEFAULT NULL,
  `check_pemotongan_presisi` tinyint(1) DEFAULT NULL,
  `check_halaman_nomor` tinyint(1) DEFAULT NULL,
  `status_kelolosan` varchar(20) NOT NULL,
  `jenis_cacat` varchar(120) DEFAULT NULL,
  `jumlah_cacat` int(11) DEFAULT NULL,
  `bukti_foto` varchar(255) DEFAULT NULL,
  `catatan_qc` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sekolah`
--

CREATE TABLE `sekolah` (
  `sekolah_id` int(11) NOT NULL,
  `nama_sekolah` varchar(120) NOT NULL,
  `wilayah` varchar(80) DEFAULT NULL,
  `kontak_sekolah` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `template_desain`
--

CREATE TABLE `template_desain` (
  `template_desain_id` int(11) NOT NULL,
  `nama_template` varchar(80) NOT NULL,
  `versi` varchar(120) DEFAULT NULL,
  `font` varchar(120) DEFAULT NULL,
  `grid` varchar(60) DEFAULT NULL,
  `warna` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status_approval` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `template_mou`
--

CREATE TABLE `template_mou` (
  `template_id` int(11) NOT NULL,
  `nama_template` varchar(80) NOT NULL,
  `jenis_paket` varchar(20) NOT NULL,
  `konten` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tiket`
--

CREATE TABLE `tiket` (
  `tiket_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `jenis_cacat` varchar(120) DEFAULT NULL,
  `jumlah_buku` int(11) DEFAULT NULL,
  `bukti_cacat` varchar(255) DEFAULT NULL,
  `batch_number` varchar(30) DEFAULT NULL,
  `status_tiket` varchar(30) NOT NULL,
  `tanggal_konfirmasi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `handled_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timeline`
--

CREATE TABLE `timeline` (
  `timeline_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `nama_tahapan` varchar(40) NOT NULL,
  `status` varchar(20) NOT NULL,
  `deadline` date DEFAULT NULL,
  `pic_id` int(11) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(256) NOT NULL,
  `nomor_telepon` varchar(20) NOT NULL,
  `password` varchar(256) NOT NULL,
  `username` varchar(60) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `desain`
--
ALTER TABLE `desain`
  ADD PRIMARY KEY (`design_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `template_desain_id` (`template_desain_id`),
  ADD KEY `desainer_id` (`desainer_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `pengiriman_id` (`pengiriman_id`);

--
-- Indexes for table `mou`
--
ALTER TABLE `mou`
  ADD PRIMARY KEY (`mou_id`),
  ADD UNIQUE KEY `nomor_mou` (`nomor_mou`),
  ADD KEY `prospek_id` (`prospek_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `nomor_order` (`nomor_order`),
  ADD KEY `mou_id` (`mou_id`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`pengiriman_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `prospek_sekolah`
--
ALTER TABLE `prospek_sekolah`
  ADD PRIMARY KEY (`prospek_id`),
  ADD KEY `staf_id` (`staf_id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indexes for table `qc_checklist`
--
ALTER TABLE `qc_checklist`
  ADD PRIMARY KEY (`qc_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `qc_by` (`qc_by`);

--
-- Indexes for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`sekolah_id`),
  ADD UNIQUE KEY `nama_sekolah` (`nama_sekolah`);

--
-- Indexes for table `template_desain`
--
ALTER TABLE `template_desain`
  ADD PRIMARY KEY (`template_desain_id`);

--
-- Indexes for table `template_mou`
--
ALTER TABLE `template_mou`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `tiket`
--
ALTER TABLE `tiket`
  ADD PRIMARY KEY (`tiket_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `handled_by` (`handled_by`);

--
-- Indexes for table `timeline`
--
ALTER TABLE `timeline`
  ADD PRIMARY KEY (`timeline_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `pic_id` (`pic_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `desain`
--
ALTER TABLE `desain`
  MODIFY `design_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mou`
--
ALTER TABLE `mou`
  MODIFY `mou_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `pengiriman_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prospek_sekolah`
--
ALTER TABLE `prospek_sekolah`
  MODIFY `prospek_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qc_checklist`
--
ALTER TABLE `qc_checklist`
  MODIFY `qc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `sekolah_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `template_desain`
--
ALTER TABLE `template_desain`
  MODIFY `template_desain_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `template_mou`
--
ALTER TABLE `template_mou`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tiket`
--
ALTER TABLE `tiket`
  MODIFY `tiket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timeline`
--
ALTER TABLE `timeline`
  MODIFY `timeline_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `desain`
--
ALTER TABLE `desain`
  ADD CONSTRAINT `desain_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `desain_ibfk_2` FOREIGN KEY (`template_desain_id`) REFERENCES `template_desain` (`template_desain_id`),
  ADD CONSTRAINT `desain_ibfk_3` FOREIGN KEY (`desainer_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`pengiriman_id`) REFERENCES `pengiriman` (`pengiriman_id`);

--
-- Constraints for table `mou`
--
ALTER TABLE `mou`
  ADD CONSTRAINT `mou_ibfk_1` FOREIGN KEY (`prospek_id`) REFERENCES `prospek_sekolah` (`prospek_id`),
  ADD CONSTRAINT `mou_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `template_mou` (`template_id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`mou_id`) REFERENCES `mou` (`mou_id`);

--
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`);

--
-- Constraints for table `prospek_sekolah`
--
ALTER TABLE `prospek_sekolah`
  ADD CONSTRAINT `prospek_sekolah_ibfk_1` FOREIGN KEY (`staf_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `prospek_sekolah_ibfk_2` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`sekolah_id`);

--
-- Constraints for table `qc_checklist`
--
ALTER TABLE `qc_checklist`
  ADD CONSTRAINT `qc_checklist_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `qc_checklist_ibfk_2` FOREIGN KEY (`qc_by`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `tiket`
--
ALTER TABLE `tiket`
  ADD CONSTRAINT `tiket_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `tiket_ibfk_2` FOREIGN KEY (`handled_by`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `timeline`
--
ALTER TABLE `timeline`
  ADD CONSTRAINT `timeline_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `timeline_ibfk_2` FOREIGN KEY (`pic_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
