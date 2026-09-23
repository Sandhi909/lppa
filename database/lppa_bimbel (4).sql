-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Sep 2026 pada 05.17
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lppa_bimbel`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `pertemuan_ke` int(11) NOT NULL,
  `status` enum('hadir','izin','sakit','alpa') NOT NULL DEFAULT 'hadir',
  `keterangan` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL,
  `nama_tahun_ajaran` varchar(30) NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `academic_years`
--

INSERT INTO `academic_years` (`id`, `nama_tahun_ajaran`, `tanggal_mulai`, `tanggal_selesai`, `status`) VALUES
(1, '2024/2025', '2024-07-01', '2025-06-30', 'nonaktif'),
(2, '2025/2026', '2025-07-01', '2026-06-30', 'aktif'),
(3, '2026/2027', '2026-07-01', '2027-06-30', 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `tabel` varchar(60) DEFAULT NULL,
  `data_id` int(11) DEFAULT NULL,
  `data_lama` text DEFAULT NULL,
  `data_baru` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `aktivitas`, `tabel`, `data_id`, `data_lama`, `data_baru`, `created_at`) VALUES
(1, NULL, 'Logout dari sistem', 'users', 2, NULL, NULL, '2026-08-06 06:06:05'),
(2, NULL, 'Login ke sistem', 'users', 1, NULL, NULL, '2026-08-06 06:06:10'),
(3, NULL, 'Logout dari sistem', 'users', 1, NULL, NULL, '2026-08-06 07:01:30'),
(4, NULL, 'Login ke sistem', 'users', 1, NULL, NULL, '2026-08-06 07:01:36'),
(5, NULL, 'Logout dari sistem', 'users', 1, NULL, NULL, '2026-08-06 07:03:04'),
(6, NULL, 'Login ke sistem', 'users', 1, NULL, NULL, '2026-08-06 07:03:13'),
(7, NULL, 'Logout dari sistem', 'users', 1, NULL, NULL, '2026-08-06 07:03:21'),
(8, NULL, 'Login ke sistem', 'users', 2, NULL, NULL, '2026-08-06 07:03:29'),
(9, NULL, 'Logout dari sistem', 'users', 2, NULL, NULL, '2026-08-06 07:19:05'),
(10, NULL, 'Login ke sistem', 'users', 1, NULL, NULL, '2026-08-06 07:19:16'),
(11, NULL, 'Mengubah rekomendator: Ustadz Ahmad Fauzi', 'recommenders', 1, NULL, '{\"nama\":\"Ustadz Ahmad Fauzi\",\"nomor_induk\":\"NIP-1001\",\"unit_id\":\"3\",\"jenis\":\"wali_kelas\",\"telepon\":\"081311112222\",\"status\":\"aktif\"}', '2026-08-06 07:32:14'),
(12, NULL, 'Mengubah rekomendator: Ustadzah Nur Laila', 'recommenders', 4, NULL, '{\"nama\":\"Ustadzah Nur Laila\",\"nomor_induk\":\"NIP-2002\",\"unit_id\":\"5\",\"jenis\":\"wali_santri\",\"telepon\":\"081344445555\",\"status\":\"aktif\"}', '2026-08-06 07:32:19'),
(13, NULL, 'Mengubah rekomendator: Ustadzah Siti Aminah', 'recommenders', 2, NULL, '{\"nama\":\"Ustadzah Siti Aminah\",\"nomor_induk\":\"NIP-2001\",\"unit_id\":null,\"jenis\":\"wali_santri\",\"telepon\":\"081322223333\",\"status\":\"aktif\"}', '2026-08-06 07:32:35'),
(14, NULL, 'Mengubah rekomendator: Ustadzah Nur Laila', 'recommenders', 4, NULL, '{\"nama\":\"Ustadzah Nur Laila\",\"nomor_induk\":\"NIP-2002\",\"unit_id\":\"5\",\"jenis\":\"wali_santri\",\"telepon\":\"081344445555\",\"status\":\"aktif\"}', '2026-08-06 07:32:58'),
(15, NULL, 'Menambah data siswa: Siswa', 'students', 9, NULL, '{\"partner_id\":1,\"academic_year_id\":2,\"unit_id\":3,\"tanggal_pendaftaran\":\"2026-08-06\",\"nama_siswa\":\"Siswa\",\"kelas\":\"XI1\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":500000,\"biaya_program\":2000000,\"total_biaya\":2500000,\"triwulan\":\"I\",\"is_psam\":\"Tidak\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":1,\"wali_santri_id\":null,\"keterangan\":\"\"}', '2026-08-06 07:34:31'),
(16, NULL, 'Menghapus data siswa: Khadijah Salsabila', 'students', 8, '{\"id\":8,\"partner_id\":1,\"academic_year_id\":2,\"unit_id\":2,\"tanggal_pendaftaran\":\"2025-09-20\",\"nama_siswa\":\"Khadijah Salsabila\",\"kelas\":\"12 IPA\",\"program_bimbel\":\"Persiapan UTBK\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":null,\"is_psam\":\"Tidak\",\"total_biaya\":\"2000000.00\",\"biaya_program\":\"2000000.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":3,\"wali_santri_id\":4,\"keterangan\":\"\",\"created_at\":\"2026-08-06 11:30:46\",\"updated_at\":\"2026-08-06 13:44:52\"}', NULL, '2026-08-06 07:34:45'),
(17, NULL, 'Menghapus data siswa: Rizky Ramadhan', 'students', 3, '{\"id\":3,\"partner_id\":1,\"academic_year_id\":2,\"unit_id\":2,\"tanggal_pendaftaran\":\"2025-07-15\",\"nama_siswa\":\"Rizky Ramadhan\",\"kelas\":\"10 IPA\",\"program_bimbel\":\"Persiapan UTBK\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":null,\"is_psam\":\"Tidak\",\"total_biaya\":\"2500000.00\",\"biaya_program\":\"2500000.00\",\"status_pembayaran\":\"angsuran\",\"wali_kelas_id\":3,\"wali_santri_id\":4,\"keterangan\":\"\",\"created_at\":\"2026-08-06 11:30:46\",\"updated_at\":\"2026-08-06 13:44:52\"}', NULL, '2026-08-06 07:34:49'),
(18, NULL, 'Menghapus data siswa: Ahmad Zaki', 'students', 1, '{\"id\":1,\"partner_id\":1,\"academic_year_id\":2,\"unit_id\":1,\"tanggal_pendaftaran\":\"2025-07-10\",\"nama_siswa\":\"Ahmad Zaki\",\"kelas\":\"7A\",\"program_bimbel\":\"Neutron\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":null,\"is_psam\":\"Tidak\",\"total_biaya\":\"1500000.00\",\"biaya_program\":\"1500000.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":1,\"wali_santri_id\":2,\"keterangan\":\"\",\"created_at\":\"2026-08-06 11:30:46\",\"updated_at\":\"2026-08-06 13:44:52\"}', NULL, '2026-08-06 07:34:51'),
(19, NULL, 'Mengubah data siswa: Aisyah Putri', 'students', 4, '{\"id\":4,\"partner_id\":1,\"academic_year_id\":2,\"unit_id\":3,\"tanggal_pendaftaran\":\"2025-08-01\",\"nama_siswa\":\"Aisyah Putri\",\"kelas\":\"5A\",\"program_bimbel\":\"Bimbel Bahasa Inggris\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":null,\"is_psam\":\"Tidak\",\"total_biaya\":\"1000000.00\",\"biaya_program\":\"1000000.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":null,\"wali_santri_id\":null,\"keterangan\":\"\",\"created_at\":\"2026-08-06 11:30:46\",\"updated_at\":\"2026-08-06 13:44:52\"}', '{\"partner_id\":1,\"academic_year_id\":2,\"unit_id\":3,\"tanggal_pendaftaran\":\"2025-08-01\",\"nama_siswa\":\"Aisyah Putri\",\"kelas\":\"5A\",\"program_bimbel\":\"Bimbel Bahasa Inggris\",\"biaya_pendaftaran\":0,\"biaya_program\":1000000,\"total_biaya\":1000000,\"triwulan\":null,\"is_psam\":\"Tidak\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":1,\"wali_santri_id\":null,\"keterangan\":\"\"}', '2026-08-06 07:34:59'),
(20, NULL, 'Mengubah data siswa: Umar Faruq', 'students', 7, '{\"id\":7,\"partner_id\":2,\"academic_year_id\":2,\"unit_id\":3,\"tanggal_pendaftaran\":\"2025-09-02\",\"nama_siswa\":\"Umar Faruq\",\"kelas\":\"6B\",\"program_bimbel\":\"Bimbel Reguler\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":null,\"is_psam\":\"Tidak\",\"total_biaya\":\"1200000.00\",\"biaya_program\":\"1200000.00\",\"status_pembayaran\":\"angsuran\",\"wali_kelas_id\":null,\"wali_santri_id\":null,\"keterangan\":\"\",\"created_at\":\"2026-08-06 11:30:46\",\"updated_at\":\"2026-08-06 13:44:52\"}', '{\"partner_id\":2,\"academic_year_id\":2,\"unit_id\":3,\"tanggal_pendaftaran\":\"2025-09-02\",\"nama_siswa\":\"Umar Faruq\",\"kelas\":\"6B\",\"program_bimbel\":\"Bimbel Reguler\",\"biaya_pendaftaran\":0,\"biaya_program\":1200000,\"total_biaya\":1200000,\"triwulan\":null,\"is_psam\":\"Tidak\",\"status_pembayaran\":\"angsuran\",\"wali_kelas_id\":1,\"wali_santri_id\":null,\"keterangan\":\"\"}', '2026-08-06 07:35:16'),
(21, NULL, 'Mengubah aturan fee rekomendator', 'fee_rules', 1, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 1\",\"poin_minimum\":1,\"poin_maksimum\":3,\"nominal_fee\":0,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-06 07:35:52'),
(22, NULL, 'Logout dari sistem', 'users', 1, NULL, NULL, '2026-08-06 07:41:41'),
(23, NULL, 'Login ke sistem', 'users', 2, NULL, NULL, '2026-08-06 07:41:47'),
(24, NULL, 'Logout dari sistem', 'users', 2, NULL, NULL, '2026-08-06 07:52:45'),
(25, NULL, 'Login ke sistem', 'users', 1, NULL, NULL, '2026-08-06 07:52:50'),
(26, NULL, 'Logout dari sistem', 'users', 1, NULL, NULL, '2026-08-06 07:53:06'),
(27, NULL, 'Login ke sistem', 'users', 2, NULL, NULL, '2026-08-06 07:53:18'),
(28, NULL, 'Logout dari sistem', 'users', 2, NULL, NULL, '2026-08-06 08:12:58'),
(29, NULL, 'Login ke sistem', 'users', 1, NULL, NULL, '2026-08-06 08:13:05'),
(30, NULL, 'Logout dari sistem', 'users', 1, NULL, NULL, '2026-08-06 08:13:16'),
(31, NULL, 'Login ke sistem', 'users', 3, NULL, NULL, '2026-08-06 08:13:41'),
(32, NULL, 'Logout dari sistem', 'users', 3, NULL, NULL, '2026-08-06 08:14:42'),
(33, NULL, 'Login ke sistem', 'users', 1, NULL, NULL, '2026-08-07 02:24:16'),
(34, NULL, 'Menghapus data siswa: Siswa', 'students', 9, '{\"id\":9,\"partner_id\":1,\"academic_year_id\":2,\"unit_id\":3,\"tanggal_pendaftaran\":\"2026-08-06\",\"nama_siswa\":\"Siswa\",\"kelas\":\"XI1\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":\"500000.00\",\"triwulan\":\"I\",\"is_psam\":\"Tidak\",\"total_biaya\":\"2500000.00\",\"biaya_program\":\"2000000.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":1,\"wali_santri_id\":null,\"keterangan\":\"\",\"created_at\":\"2026-08-06 14:34:31\",\"updated_at\":\"2026-08-06 14:34:31\"}', NULL, '2026-08-07 02:43:06'),
(35, NULL, 'Menghapus data siswa: Umar Faruq', 'students', 7, '{\"id\":7,\"partner_id\":2,\"academic_year_id\":2,\"unit_id\":3,\"tanggal_pendaftaran\":\"2025-09-02\",\"nama_siswa\":\"Umar Faruq\",\"kelas\":\"6B\",\"program_bimbel\":\"Bimbel Reguler\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":null,\"is_psam\":\"Tidak\",\"total_biaya\":\"1200000.00\",\"biaya_program\":\"1200000.00\",\"status_pembayaran\":\"angsuran\",\"wali_kelas_id\":1,\"wali_santri_id\":null,\"keterangan\":\"\",\"created_at\":\"2026-08-06 11:30:46\",\"updated_at\":\"2026-08-06 14:35:16\"}', NULL, '2026-08-07 02:43:08'),
(36, NULL, 'Menghapus data siswa: Nabila Syifa', 'students', 6, '{\"id\":6,\"partner_id\":2,\"academic_year_id\":2,\"unit_id\":5,\"tanggal_pendaftaran\":\"2025-08-11\",\"nama_siswa\":\"Nabila Syifa\",\"kelas\":\"11 IPS\",\"program_bimbel\":\"Kelas Intensif\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":null,\"is_psam\":\"Tidak\",\"total_biaya\":\"2500000.00\",\"biaya_program\":\"2500000.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":3,\"wali_santri_id\":null,\"keterangan\":\"\",\"created_at\":\"2026-08-06 11:30:46\",\"updated_at\":\"2026-08-06 13:44:52\"}', NULL, '2026-08-07 02:43:10'),
(37, NULL, 'Menghapus data siswa: Muhammad Iqbal', 'students', 5, '{\"id\":5,\"partner_id\":2,\"academic_year_id\":2,\"unit_id\":4,\"tanggal_pendaftaran\":\"2025-08-05\",\"nama_siswa\":\"Muhammad Iqbal\",\"kelas\":\"9C\",\"program_bimbel\":\"Ganesha Operation\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":null,\"is_psam\":\"Tidak\",\"total_biaya\":\"1800000.00\",\"biaya_program\":\"1800000.00\",\"status_pembayaran\":\"angsuran\",\"wali_kelas_id\":1,\"wali_santri_id\":null,\"keterangan\":\"\",\"created_at\":\"2026-08-06 11:30:46\",\"updated_at\":\"2026-08-06 13:44:52\"}', NULL, '2026-08-07 02:43:11'),
(38, NULL, 'Menghapus data siswa: Aisyah Putri', 'students', 4, '{\"id\":4,\"partner_id\":1,\"academic_year_id\":2,\"unit_id\":3,\"tanggal_pendaftaran\":\"2025-08-01\",\"nama_siswa\":\"Aisyah Putri\",\"kelas\":\"5A\",\"program_bimbel\":\"Bimbel Bahasa Inggris\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":null,\"is_psam\":\"Tidak\",\"total_biaya\":\"1000000.00\",\"biaya_program\":\"1000000.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":1,\"wali_santri_id\":null,\"keterangan\":\"\",\"created_at\":\"2026-08-06 11:30:46\",\"updated_at\":\"2026-08-06 14:34:59\"}', NULL, '2026-08-07 02:43:13'),
(39, NULL, 'Menghapus data siswa: Fatimah Azzahra', 'students', 2, '{\"id\":2,\"partner_id\":1,\"academic_year_id\":2,\"unit_id\":4,\"tanggal_pendaftaran\":\"2025-07-12\",\"nama_siswa\":\"Fatimah Azzahra\",\"kelas\":\"7B\",\"program_bimbel\":\"Primagama\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":null,\"is_psam\":\"Tidak\",\"total_biaya\":\"1500000.00\",\"biaya_program\":\"1500000.00\",\"status_pembayaran\":\"angsuran\",\"wali_kelas_id\":1,\"wali_santri_id\":null,\"keterangan\":\"\",\"created_at\":\"2026-08-06 11:30:46\",\"updated_at\":\"2026-08-06 13:44:52\"}', NULL, '2026-08-07 02:43:15'),
(40, NULL, 'Menghapus Wali Kelas', 'recommenders', 1, NULL, NULL, '2026-08-07 02:43:18'),
(41, NULL, 'Menghapus Wali Kelas', 'recommenders', 3, NULL, NULL, '2026-08-07 02:43:20'),
(42, NULL, 'Menghapus Wali Santri', 'recommenders', 4, NULL, NULL, '2026-08-07 02:43:23'),
(43, NULL, 'Menghapus Wali Santri', 'recommenders', 2, NULL, NULL, '2026-08-07 02:43:24'),
(44, NULL, 'Menghapus Wali Kelas', 'recommenders', 164, NULL, NULL, '2026-08-07 04:19:47'),
(46, NULL, 'Logout dari sistem', 'users', 1, NULL, NULL, '2026-08-07 04:28:56'),
(47, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-07 06:12:45'),
(48, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-07 06:14:24'),
(49, NULL, 'Login ke sistem', 'users', 431, NULL, NULL, '2026-08-07 06:15:31'),
(50, NULL, 'Logout dari sistem', 'users', 431, NULL, NULL, '2026-08-07 06:16:25'),
(51, NULL, 'Login ke sistem', 'users', 432, NULL, NULL, '2026-08-07 06:16:40'),
(52, NULL, 'Logout dari sistem', 'users', 432, NULL, NULL, '2026-08-07 06:16:41'),
(53, 543, 'Login ke sistem', 'users', 543, NULL, NULL, '2026-08-07 06:19:31'),
(54, 543, 'Logout dari sistem', 'users', 543, NULL, NULL, '2026-08-07 06:19:42'),
(55, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-07 06:19:48'),
(56, 430, 'Menghapus mitra', 'partners', 1, NULL, NULL, '2026-08-07 06:19:54'),
(57, 430, 'Menghapus mitra', 'partners', 2, NULL, NULL, '2026-08-07 06:19:57'),
(58, 430, 'Menambah mitra: Neutron (Bagi Hasil Yayasan 12,5%)', 'partners', 3, NULL, '{\"nama_mitra\":\"Neutron\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-01-01\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":12.5}', '2026-08-07 06:20:25'),
(59, 430, 'Mengubah mitra: Neutron', 'partners', 3, NULL, '{\"nama_mitra\":\"Neutron\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-01-01\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":12.5}', '2026-08-07 06:20:36'),
(60, 430, 'Membuat akun mitra: neutron', 'users', 639, NULL, NULL, '2026-08-07 06:24:02'),
(61, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-07 06:24:24'),
(62, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-07 06:26:02'),
(63, 430, 'Menambah mitra: Neutron Bandung (Bagi Hasil Yayasan 12,5%)', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":12.5}', '2026-08-07 06:26:17'),
(64, 430, 'Membuat akun mitra: neutron', 'users', 640, NULL, NULL, '2026-08-07 06:26:36'),
(65, 430, 'Menghapus mitra', 'partners', 3, NULL, NULL, '2026-08-07 06:26:47'),
(66, 430, 'Membuat akun mitra: neutron', 'users', 641, NULL, NULL, '2026-08-07 06:27:17'),
(67, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-07 06:27:21'),
(68, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-07 06:27:31'),
(69, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-07 07:47:13'),
(70, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-07 07:47:18'),
(71, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-07 07:48:35'),
(72, 571, 'Login ke sistem', 'users', 571, NULL, NULL, '2026-08-07 07:48:46'),
(73, 571, 'Logout dari sistem', 'users', 571, NULL, NULL, '2026-08-07 07:56:32'),
(74, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-07 07:56:42'),
(75, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-07 08:11:51'),
(76, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-07 08:11:55'),
(77, 430, 'Menambah Wali Kelas: Ato Taopik, S.Ag', 'recommenders', 648, NULL, '{\"nama\":\"Ato Taopik, S.Ag\",\"nomor_induk\":\"\",\"unit_id\":\"5\",\"jenis\":\"wali_kelas\",\"telepon\":\"\",\"email\":\"atotaopik@gmail.com\",\"status\":\"aktif\"}', '2026-08-07 08:13:04'),
(78, 430, 'Membuat akun Wali Kelas: ato.taopik', 'users', 642, NULL, NULL, '2026-08-07 08:16:09'),
(79, 430, 'Menambah Wali Kelas: Riad Taufik Lazwardi, M.Si', 'recommenders', 649, NULL, '{\"nama\":\"Riad Taufik Lazwardi, M.Si\",\"nomor_induk\":\"\",\"unit_id\":\"5\",\"jenis\":\"wali_kelas\",\"telepon\":\"\",\"email\":\"Riadtaufik@gmail.com\",\"status\":\"aktif\"}', '2026-08-07 08:17:02'),
(80, 430, 'Membuat akun Wali Kelas: riad.taufik', 'users', 643, NULL, NULL, '2026-08-07 08:17:23'),
(81, 430, 'Menambah Wali Kelas: H. Udin Nashruddin, S. Si, M.M', 'recommenders', 650, NULL, '{\"nama\":\"H. Udin Nashruddin, S. Si, M.M\",\"nomor_induk\":\"\",\"unit_id\":\"5\",\"jenis\":\"wali_kelas\",\"telepon\":\"\",\"email\":\"udinnashruddin@gmail.com\",\"status\":\"aktif\"}', '2026-08-07 08:17:47'),
(82, 430, 'Membuat akun Wali Kelas: udin.nashruddin', 'users', 644, NULL, NULL, '2026-08-07 08:18:04'),
(83, 430, 'Menambah Wali Kelas: Dinar Nurul Fauziah, S.E. M.E.', 'recommenders', 651, NULL, '{\"nama\":\"Dinar Nurul Fauziah, S.E. M.E.\",\"nomor_induk\":\"\",\"unit_id\":\"5\",\"jenis\":\"wali_kelas\",\"telepon\":\"\",\"email\":\"dinarnurul@gmail.com\",\"status\":\"aktif\"}', '2026-08-07 08:18:33'),
(84, 430, 'Membuat akun Wali Kelas: dinar.nurul', 'users', 645, NULL, NULL, '2026-08-07 08:18:46'),
(85, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-07 08:19:02'),
(86, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-07 08:19:08'),
(87, 641, 'Import: menambah data siswa SEILA SANAH ASSABILIYAH', 'students', 10, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:44'),
(88, 641, 'Import: menambah data siswa MUHAMMAD ALFARISY HASBULLAH', 'students', 11, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:44'),
(89, 641, 'Import: menambah data siswa AHMAD AZIZUL ZAHID', 'students', 12, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:44'),
(90, 641, 'Import: menambah data siswa MUHAMMAD ZAHRAN AGASTYA', 'students', 13, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:44'),
(91, 641, 'Import: menambah data siswa MUHAMMAD ALDI SUKMA B', 'students', 14, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4400000,\"total_biaya\":4400000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:44'),
(92, 641, 'Import: menambah data siswa DIVYA NUR FADHILA AHLADITHA', 'students', 15, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":6450000,\"total_biaya\":6450000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:44'),
(93, 641, 'Import: menambah data siswa DZAFIRA IMRA\'AH GUNAWAN', 'students', 16, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:44'),
(94, 641, 'Import: menambah data siswa ADHIE JATI KESUMA', 'students', 17, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:44'),
(95, 641, 'Import: menambah data siswa ZASKIA WIDYA MAHARANI YUNUS', 'students', 18, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:44'),
(96, 641, 'Import: menambah data siswa IJLAL NAJIHUL AMAL', 'students', 19, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4900000,\"total_biaya\":5200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:44'),
(97, 641, 'Import: menambah data siswa SHAFIRA IZZATI', 'students', 20, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:44'),
(98, 641, 'Import: menambah data siswa SALSABILA BILQIS ADELIN', 'students', 21, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(99, 641, 'Import: menambah data siswa NURUL NAZWA MAYRITZA', 'students', 22, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4450000,\"total_biaya\":4450000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(100, 641, 'Import: menambah data siswa KHARIDAH KHAIRIYAH KARAMATULLAH', 'students', 23, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":5900000,\"total_biaya\":6200000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(101, 641, 'Import: menambah data siswa ANDINI APRILIA KARTINI', 'students', 24, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6400000,\"total_biaya\":6700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(102, 641, 'Import: menambah data siswa HANNA NOER PRATIWI', 'students', 25, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4700000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(103, 641, 'Import: menambah data siswa SITI NURLAILA LATIFAH', 'students', 26, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(104, 641, 'Import: menambah data siswa KEYLA TANZA KHAIRUNNISA', 'students', 27, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6400000,\"total_biaya\":6700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(105, 641, 'Import: menambah data siswa HAYFA ADINDA SHOFA', 'students', 28, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(106, 641, 'Import: menambah data siswa GHANY AHMAD AR-RIFQI', 'students', 29, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(107, 641, 'Import: menambah data siswa ZAHRA APRILIA', 'students', 30, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4450000,\"total_biaya\":4450000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(108, 641, 'Import: menambah data siswa MUHAMMAD SHIDQI', 'students', 31, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(109, 641, 'Import: menambah data siswa NAJMIE BANI A', 'students', 32, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(110, 641, 'Import: menambah data siswa FARHAH AFIFAH', 'students', 33, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(111, 641, 'Import: menambah data siswa FARELL MAHENDRA', 'students', 34, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(112, 641, 'Import: menambah data siswa NAYLA SOFIA', 'students', 35, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(113, 641, 'Import: menambah data siswa FITRI AFIFAH', 'students', 36, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(114, 641, 'Import: menambah data siswa SUCI FEBRIAN W', 'students', 37, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(115, 641, 'Import: menambah data siswa SAFIRA KHOIRUNNISA', 'students', 38, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(116, 641, 'Import: menambah data siswa MUHAMMAD NUR HAYKAL', 'students', 39, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(117, 641, 'Import: menambah data siswa DIAH ZAHRATUSSITA', 'students', 40, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(118, 641, 'Import: menambah data siswa NAMIERA ABDIRIBIHA AZIZ', 'students', 41, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(119, 641, 'Import: menambah data siswa GARINI RANTI', 'students', 42, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(120, 641, 'Import: menambah data siswa M. ZILLAN AL-AKBARI', 'students', 43, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(121, 641, 'Import: menambah data siswa FICO FATHONI', 'students', 44, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(122, 641, 'Import: menambah data siswa CEYZAR PUTRA D', 'students', 45, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(123, 641, 'Import: menambah data siswa EGHLIYA ELHABAYIEB', 'students', 46, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(124, 641, 'Import: menambah data siswa FAIZ ZIYAD FAUZAN', 'students', 47, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(125, 641, 'Import: menambah data siswa JIHAN AQILA G', 'students', 48, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(126, 641, 'Import: menambah data siswa ASSYIFA PUTRI A', 'students', 49, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4450000,\"total_biaya\":4450000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(127, 641, 'Import: menambah data siswa BISMA MAHARDIKA', 'students', 50, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(128, 641, 'Import: menambah data siswa FAIRUZA RAIHANA T', 'students', 51, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6200000,\"total_biaya\":6500000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(129, 641, 'Import: menambah data siswa KARIMA TANINA S', 'students', 52, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6400000,\"total_biaya\":6700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(130, 641, 'Import: menambah data siswa KANAYA TANINA S', 'students', 53, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(131, 641, 'Import: menambah data siswa M. IQBAL DARMAWAN', 'students', 54, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4950000,\"total_biaya\":4950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(132, 641, 'Import: menambah data siswa SYABANI FAJARINA', 'students', 55, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(133, 641, 'Import: menambah data siswa NAZWA CHARERINA', 'students', 56, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(134, 641, 'Import: menambah data siswa ABDULLAH GEPARDI', 'students', 57, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(135, 641, 'Import: menambah data siswa SABRINA AZZAHRAA', 'students', 58, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4450000,\"total_biaya\":4450000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(136, 641, 'Import: menambah data siswa CALISTA KHAIRINNISWAH', 'students', 59, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(137, 641, 'Import: menambah data siswa AISYAH AMIRA D', 'students', 60, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(138, 641, 'Import: menambah data siswa AHSAN MUHAMMAD FADHLAN', 'students', 61, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4950000,\"total_biaya\":4950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(139, 641, 'Import: menambah data siswa KAKA RAMDAN N', 'students', 62, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(140, 641, 'Import: menambah data siswa SYAHLA NADZIFA Z.', 'students', 63, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3900000,\"total_biaya\":3900000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(141, 641, 'Import: menambah data siswa DAVINA PEBRIANAN', 'students', 64, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(142, 641, 'Import: menambah data siswa KIRANA HARSYA D', 'students', 65, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(143, 641, 'Import: menambah data siswa IQMA FAUZIAH', 'students', 66, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(144, 641, 'Import: menambah data siswa REYSHA KIRANI V', 'students', 67, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(145, 641, 'Import: menambah data siswa SYIFA NURUL GINA', 'students', 68, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4950000,\"total_biaya\":4950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(146, 641, 'Import: menambah data siswa PRIYATAMA RAUFAN HIBRIZI', 'students', 69, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(147, 641, 'Import: menambah data siswa M. DZACKY AZ ZIDANE R', 'students', 70, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(148, 641, 'Import: menambah data siswa SASKIA RAMADHANI', 'students', 71, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(149, 641, 'Import: menambah data siswa FAWAIDZ RIFAI E', 'students', 72, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(150, 641, 'Import: menambah data siswa DEYNISA TISA S', 'students', 73, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(151, 641, 'Import: menambah data siswa DINARA SAFINA', 'students', 74, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(152, 641, 'Import: menambah data siswa BILQIS ALTAMIRA DEVI', 'students', 75, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(153, 641, 'Import: menambah data siswa M. YUSUF AL FAREL', 'students', 76, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6900000,\"total_biaya\":7200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(154, 641, 'Import: menambah data siswa M. ARDHI GHONIYUL', 'students', 77, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4450000,\"total_biaya\":4450000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(155, 641, 'Import: menambah data siswa SEKAR WANGI NUR A', 'students', 78, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(156, 641, 'Import: menambah data siswa SABRINA AZZAHRA', 'students', 79, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(157, 641, 'Import: menambah data siswa KEYZA PUTRI Y', 'students', 80, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6400000,\"total_biaya\":6700000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(158, 641, 'Import: menambah data siswa DAYYANA MUJTAHIDA', 'students', 81, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6400000,\"total_biaya\":6700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(159, 641, 'Import: menambah data siswa NAYLA PARISHA RAYYA', 'students', 82, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6400000,\"total_biaya\":6700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(160, 641, 'Import: menambah data siswa KAYLA RIZKI Z.', 'students', 83, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(161, 641, 'Import: menambah data siswa AURELIA ZAHRA I. S.', 'students', 84, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(162, 641, 'Import: menambah data siswa EDWARD MAULANA I.', 'students', 85, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(163, 641, 'Import: menambah data siswa RAIHAN KAMILA R', 'students', 86, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(164, 641, 'Import: menambah data siswa NATHANIA RAHMA', 'students', 87, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(165, 641, 'Import: menambah data siswa KEYSYA ANDREA P.', 'students', 88, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(166, 641, 'Import: menambah data siswa KAYLA MUTIARA F', 'students', 89, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(167, 641, 'Import: menambah data siswa FARRAS ATHA R', 'students', 90, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4450000,\"total_biaya\":4450000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(168, 641, 'Import: menambah data siswa SOPIYATUL K. H.', 'students', 91, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(169, 641, 'Import: menambah data siswa RASYA SABIAN N', 'students', 92, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(170, 641, 'Import: menambah data siswa OLINE PARAHITA', 'students', 93, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(171, 641, 'Import: menambah data siswa REVA ZAHROTU S', 'students', 94, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(172, 641, 'Import: menambah data siswa ASFIA NAZLA LATIFA', 'students', 95, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(173, 641, 'Import: menambah data siswa LULU MUTHIA A', 'students', 96, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(174, 641, 'Import: menambah data siswa KALILA', 'students', 97, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3950000,\"total_biaya\":3950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(175, 641, 'Import: menambah data siswa DAIFA FADILA M.', 'students', 98, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(176, 641, 'Import: menambah data siswa JANNATA R.Z.', 'students', 99, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(177, 641, 'Import: menambah data siswa M. ARKAN ZAKARIA', 'students', 100, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4950000,\"total_biaya\":4950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(178, 641, 'Import: menambah data siswa RAHMADINA SYIFA A', 'students', 101, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6400000,\"total_biaya\":6700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(179, 641, 'Import: menambah data siswa LIKAYLA MUTIARA', 'students', 102, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4900000,\"total_biaya\":5200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(180, 641, 'Import: menambah data siswa KAYESHA DZALFA', 'students', 103, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(181, 641, 'Import: menambah data siswa M. AKMAL BAIHAKI', 'students', 104, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":550000,\"total_biaya\":550000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(182, 641, 'Import: menambah data siswa DAFID KHOIRUL F', 'students', 105, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(183, 641, 'Import: menambah data siswa HASBIAN WAHYU N', 'students', 106, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(184, 641, 'Import: menambah data siswa YANUAR ARIEF NUGRAHA', 'students', 107, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4400000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(185, 641, 'Import: menambah data siswa RAFAY ZIYA QURATUAIN', 'students', 108, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6400000,\"total_biaya\":6700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(186, 641, 'Import: menambah data siswa MAHIRA HASNA MAFAID', 'students', 109, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(187, 641, 'Import: menambah data siswa ARYA MUHAMMAD FARREL', 'students', 110, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(188, 641, 'Import: menambah data siswa SATRIA WIRAYUDHA', 'students', 111, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(189, 641, 'Import: menambah data siswa RAIHAN PRIA A', 'students', 112, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4700000,\"total_biaya\":4700000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(190, 641, 'Import: menambah data siswa FIRLIAN ATHAYA R', 'students', 113, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(191, 641, 'Import: menambah data siswa SEKAR AULIA PUTRI', 'students', 114, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-07 08:32:45'),
(192, 641, 'Import: menambah data siswa IZARD WISANGGENI', 'students', 115, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(193, 641, 'Import: menambah data siswa ERIKA NUR F', 'students', 116, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3900000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(194, 641, 'Import: menambah data siswa M. FAKHRI ABINAYA HARSONO', 'students', 117, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":4650000,\"total_biaya\":4950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(195, 641, 'Import: menambah data siswa AYESHA SALSABILA', 'students', 118, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":6695000,\"total_biaya\":6995000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(196, 641, 'Import: menambah data siswa JIHAN FATIN RAMADHANI', 'students', 119, NULL, '{\"biaya_pendaftaran\":300000,\"biaya_program\":3650000,\"total_biaya\":3950000,\"is_psam\":\"YA\"}', '2026-08-07 08:32:45'),
(197, 641, 'Import data siswa: 110 baru, 0 diperbarui, 0 dilewati, 0 gagal', 'students', NULL, NULL, NULL, '2026-08-07 08:32:45'),
(198, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-07 08:33:48'),
(199, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-07 08:33:52'),
(200, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 6, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 1\",\"poin_minimum\":1,\"poin_maksimum\":3,\"nominal_fee\":0,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-07 08:35:13'),
(201, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 7, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 2\",\"poin_minimum\":3.5,\"poin_maksimum\":5,\"nominal_fee\":50000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-07 08:35:28'),
(202, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 8, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 3\",\"poin_minimum\":5.5,\"poin_maksimum\":9,\"nominal_fee\":75000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-07 08:35:42'),
(203, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 9, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 4\",\"poin_minimum\":9.5,\"poin_maksimum\":14,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-07 08:35:56'),
(204, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 10, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 5\",\"poin_minimum\":15,\"poin_maksimum\":null,\"nominal_fee\":125000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-07 08:36:11'),
(205, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 2, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 2\",\"poin_minimum\":3.5,\"poin_maksimum\":5,\"nominal_fee\":75000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-07 08:36:22'),
(206, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 3, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 3\",\"poin_minimum\":5.5,\"poin_maksimum\":9,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-07 08:36:36'),
(207, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 4, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 4\",\"poin_minimum\":9.5,\"poin_maksimum\":14,\"nominal_fee\":125000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-07 08:37:05'),
(208, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 5, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 5\",\"poin_minimum\":15,\"poin_maksimum\":null,\"nominal_fee\":150000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-07 08:37:14'),
(209, 430, 'Menambah mitra: PRIMAGAMA (Bagi Hasil Yayasan 15%)', 'partners', 5, NULL, '{\"nama_mitra\":\"PRIMAGAMA\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15}', '2026-08-07 09:04:24'),
(210, 430, 'Membuat akun mitra: primagama', 'users', 646, NULL, NULL, '2026-08-07 09:04:36'),
(211, 430, 'Menambah mitra: IEC (Bagi Hasil Yayasan 15%)', 'partners', 6, NULL, '{\"nama_mitra\":\"IEC\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15}', '2026-08-07 09:04:55'),
(212, 430, 'Membuat akun mitra: iec', 'users', 647, NULL, NULL, '2026-08-07 09:05:03'),
(213, 430, 'Menambah mitra: CENTRAL BRAINPRO (Bagi Hasil Yayasan 15%)', 'partners', 7, NULL, '{\"nama_mitra\":\"CENTRAL BRAINPRO\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15}', '2026-08-07 09:05:21'),
(214, 430, 'Membuat akun mitra: central', 'users', 648, NULL, NULL, '2026-08-07 09:05:31'),
(215, 430, 'Menambah mitra: Padjadjaran Language School (Bagi Hasil Yayasan 15%)', 'partners', 8, NULL, '{\"nama_mitra\":\"Padjadjaran Language School\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15}', '2026-08-07 09:05:52'),
(216, 430, 'Membuat akun mitra: pls', 'users', 649, NULL, NULL, '2026-08-07 09:06:01'),
(217, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":13}', '2026-08-07 09:07:34'),
(218, 430, 'Mengubah Persentase Bagi Hasil Yayasan Mitra Neutron Bandung dari 12,5% menjadi 13%', 'partners', 4, '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":12.5}', '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":13}', '2026-08-07 09:07:34'),
(219, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15}', '2026-08-07 09:07:58'),
(220, 430, 'Mengubah Persentase Bagi Hasil Yayasan Mitra Neutron Bandung dari 13% menjadi 15%', 'partners', 4, '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":13}', '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":15}', '2026-08-07 09:07:58'),
(221, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":14.5}', '2026-08-07 09:08:31'),
(222, 430, 'Mengubah Persentase Bagi Hasil Yayasan Mitra Neutron Bandung dari 15% menjadi 14,5%', 'partners', 4, '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":15}', '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":14.5}', '2026-08-07 09:08:31'),
(223, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":14.6}', '2026-08-07 09:08:52'),
(224, 430, 'Mengubah Persentase Bagi Hasil Yayasan Mitra Neutron Bandung dari 14,5% menjadi 14,6%', 'partners', 4, '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":14.5}', '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":14.6}', '2026-08-07 09:08:52'),
(225, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":14.7}', '2026-08-07 09:09:20'),
(226, 430, 'Mengubah Persentase Bagi Hasil Yayasan Mitra Neutron Bandung dari 14,6% menjadi 14,7%', 'partners', 4, '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":14.6}', '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":14.7}', '2026-08-07 09:09:20'),
(227, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":14.65}', '2026-08-07 09:09:36'),
(228, 430, 'Mengubah Persentase Bagi Hasil Yayasan Mitra Neutron Bandung dari 14,7% menjadi 14,65%', 'partners', 4, '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":14.7}', '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":14.65}', '2026-08-07 09:09:36'),
(229, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":14.6}', '2026-08-07 09:09:51'),
(230, 430, 'Mengubah Persentase Bagi Hasil Yayasan Mitra Neutron Bandung dari 14,65% menjadi 14,6%', 'partners', 4, '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":14.65}', '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":14.6}', '2026-08-07 09:09:51'),
(231, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.5,\"tax_method\":\"deduct_share\"}', '2026-08-07 09:31:42'),
(232, 430, 'Mengubah Persentase Bagi Hasil Yayasan Mitra Neutron Bandung dari 14,6% menjadi 15%', 'partners', 4, '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":14.6}', '{\"nama_mitra\":\"Neutron Bandung\",\"persentase_bagi_hasil\":15}', '2026-08-07 09:31:42'),
(233, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-10 01:32:48'),
(234, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-10 01:46:17'),
(235, 646, 'Login ke sistem', 'users', 646, NULL, NULL, '2026-08-10 01:46:23'),
(236, 646, 'Import: menambah data siswa NAYLA ACHMAD', 'students', 120, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(237, 646, 'Import: menambah data siswa BEVAN SAKTI E', 'students', 121, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(238, 646, 'Import: menambah data siswa KEISHA SAVIRA', 'students', 122, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(239, 646, 'Import: menambah data siswa M HADIANSYAH', 'students', 123, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(240, 646, 'Import: menambah data siswa STEVE WILLIAM', 'students', 124, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2750000,\"total_biaya\":2750000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(241, 646, 'Import: menambah data siswa KENARY ADELINE P', 'students', 125, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21');
INSERT INTO `audit_logs` (`id`, `user_id`, `aktivitas`, `tabel`, `data_id`, `data_lama`, `data_baru`, `created_at`) VALUES
(242, 646, 'Import: menambah data siswa NAFISHA NAIRA', 'students', 126, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(243, 646, 'Import: menambah data siswa GUNTUR', 'students', 127, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(244, 646, 'Import: menambah data siswa KAYLA AURELIA', 'students', 128, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2750000,\"total_biaya\":2750000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(245, 646, 'Import: menambah data siswa QAIREEN FARRAS', 'students', 129, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2750000,\"total_biaya\":2750000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(246, 646, 'Import: menambah data siswa ALIYA ALANA', 'students', 130, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(247, 646, 'Import: menambah data siswa SHADAM ALBARRA', 'students', 131, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2750000,\"total_biaya\":2750000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(248, 646, 'Import: menambah data siswa RIZKA WARDAH', 'students', 132, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(249, 646, 'Import: menambah data siswa M RASYA IVAN A', 'students', 133, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(250, 646, 'Import: menambah data siswa M IMAM MA', 'students', 134, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(251, 646, 'Import: menambah data siswa HIGEA M', 'students', 135, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3150000,\"total_biaya\":3150000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(252, 646, 'Import: menambah data siswa M AKBAR F', 'students', 136, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(253, 646, 'Import: menambah data siswa KHANSA AQILLAH', 'students', 137, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(254, 646, 'Import: menambah data siswa REYNALDI TIRTA K', 'students', 138, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(255, 646, 'Import: menambah data siswa ANIQ ZAYN N', 'students', 139, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3750000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(256, 646, 'Import: menambah data siswa M ZAHRAN A', 'students', 140, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3250000,\"total_biaya\":3250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(257, 646, 'Import: menambah data siswa M RUSLI NAJID', 'students', 141, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3450000,\"total_biaya\":3450000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(258, 646, 'Import: menambah data siswa BILQIS SHIDQIA', 'students', 142, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(259, 646, 'Import: menambah data siswa KEISYA PUTRI AZZAHRA', 'students', 143, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(260, 646, 'Import: menambah data siswa KAYISA SALSABILA', 'students', 144, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3650000,\"total_biaya\":3650000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(261, 646, 'Import: menambah data siswa KINETA AIDA R', 'students', 145, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(262, 646, 'Import: menambah data siswa NAURA HASNA', 'students', 146, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(263, 646, 'Import: menambah data siswa KANDZIRA PUTRI', 'students', 147, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3200000,\"total_biaya\":3450000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(264, 646, 'Import: menambah data siswa MAYESHA RAISHA', 'students', 148, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(265, 646, 'Import: menambah data siswa SADIRA JANEETA', 'students', 149, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3750000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(266, 646, 'Import: menambah data siswa FABIANO ABBASY', 'students', 150, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3550000,\"total_biaya\":3550000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(267, 646, 'Import: menambah data siswa M FANDY YULIANTO', 'students', 151, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(268, 646, 'Import: menambah data siswa M FARIZ B', 'students', 152, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(269, 646, 'Import: menambah data siswa RATU DALFA A', 'students', 153, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3750000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(270, 646, 'Import: menambah data siswa GUNTUR ISKANDAR', 'students', 154, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3750000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(271, 646, 'Import: menambah data siswa NAKULA ARARYA', 'students', 155, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(272, 646, 'Import: menambah data siswa ADILLA A', 'students', 156, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3550000,\"total_biaya\":3550000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(273, 646, 'Import: menambah data siswa ZYLVANA A', 'students', 157, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(274, 646, 'Import: menambah data siswa M SYARIF KAMIL', 'students', 158, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3750000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(275, 646, 'Import: menambah data siswa ALMEIRA PUTRI', 'students', 159, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3750000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(276, 646, 'Import: menambah data siswa QANITAH QURRATU', 'students', 160, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2950000,\"total_biaya\":2950000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(277, 646, 'Import: menambah data siswa PELANGI PUTRI N', 'students', 161, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(278, 646, 'Import: menambah data siswa ALUNA SAGITA', 'students', 162, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(279, 646, 'Import: menambah data siswa AGHNIA AHSIL', 'students', 163, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3650000,\"total_biaya\":3650000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(280, 646, 'Import: menambah data siswa DINAR ALKAUTSAR', 'students', 164, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(281, 646, 'Import: menambah data siswa ROFIQI BAIHAQI', 'students', 165, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3550000,\"total_biaya\":3800000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(282, 646, 'Import: menambah data siswa REZA NURRUHPRATAMA', 'students', 166, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3650000,\"total_biaya\":3650000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(283, 646, 'Import: menambah data siswa KHANSA RAHMA', 'students', 167, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3750000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(284, 646, 'Import: menambah data siswa ANISA ARISTAWATI', 'students', 168, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3950000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(285, 646, 'Import: menambah data siswa ALUNA KEINISYA', 'students', 169, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(286, 646, 'Import: menambah data siswa RAZAN DZAKI', 'students', 170, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3450000,\"total_biaya\":3450000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(287, 646, 'Import: menambah data siswa RAHMA SRI R', 'students', 171, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3450000,\"total_biaya\":3450000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(288, 646, 'Import: menambah data siswa RADITYO IBNU HAFIDZ', 'students', 172, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3950000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(289, 646, 'Import: menambah data siswa KEN AISYAH', 'students', 173, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(290, 646, 'Import: menambah data siswa AIKO RAMEYZA', 'students', 174, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3950000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(291, 646, 'Import: menambah data siswa QUEENSHA ZAHIYA', 'students', 175, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(292, 646, 'Import: menambah data siswa RAIDA PUTRI M', 'students', 176, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3450000,\"total_biaya\":3450000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(293, 646, 'Import: menambah data siswa ASKANA SAKHI A', 'students', 177, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3450000,\"total_biaya\":3450000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(294, 646, 'Import: menambah data siswa NADHIFA ZIYA', 'students', 178, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3450000,\"total_biaya\":3450000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(295, 646, 'Import: menambah data siswa MARWAH HANI', 'students', 179, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(296, 646, 'Import: menambah data siswa DIKZA AFRIZA', 'students', 180, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2650000,\"total_biaya\":2650000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(297, 646, 'Import: menambah data siswa JASMINE N', 'students', 181, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3450000,\"total_biaya\":3450000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(298, 646, 'Import: menambah data siswa QAMEERA', 'students', 182, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4900000,\"total_biaya\":4900000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(299, 646, 'Import: menambah data siswa ZAHRA HAMIDAH', 'students', 183, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3450000,\"total_biaya\":3700000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(300, 646, 'Import: menambah data siswa M AZALIA ATHA', 'students', 184, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":7300000,\"total_biaya\":7300000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(301, 646, 'Import: menambah data siswa MUSTHOFA AHMAD', 'students', 185, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4450000,\"total_biaya\":4450000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(302, 646, 'Import: menambah data siswa REINDRA', 'students', 186, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":5450000,\"total_biaya\":5450000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(303, 646, 'Import: menambah data siswa PANJI TRISTAN', 'students', 187, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":2950000,\"total_biaya\":3200000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(304, 646, 'Import: menambah data siswa RAFFASYA AZKA', 'students', 188, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3750000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(305, 646, 'Import: menambah data siswa KARINA ASWARI', 'students', 189, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3950000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(306, 646, 'Import: menambah data siswa MARITZA AULIA', 'students', 190, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":4250000,\"total_biaya\":4500000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(307, 646, 'Import: menambah data siswa GHAZI D', 'students', 191, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3900000,\"total_biaya\":4150000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(308, 646, 'Import: menambah data siswa ANDHIRNI', 'students', 192, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":3950000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(309, 646, 'Import: menambah data siswa HANIFA N', 'students', 193, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(310, 646, 'Import: menambah data siswa RAFI R', 'students', 194, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(311, 646, 'Import: menambah data siswa XENA S', 'students', 195, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(312, 646, 'Import: menambah data siswa ARIANE QINAN', 'students', 196, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(313, 646, 'Import: menambah data siswa RAISYA', 'students', 197, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(314, 646, 'Import: menambah data siswa HAZEL ARCELIA', 'students', 198, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(315, 646, 'Import: menambah data siswa WIDYA IRAWAN', 'students', 199, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(316, 646, 'Import: menambah data siswa FATISHA ALMIRA', 'students', 200, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(317, 646, 'Import: menambah data siswa QORI HABIBURRAHMAN', 'students', 201, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(318, 646, 'Import: menambah data siswa REYDEN RAJA', 'students', 202, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(319, 646, 'Import: menambah data siswa SEAN JULI ANDI', 'students', 203, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(320, 646, 'Import: menambah data siswa M ZAMZAM', 'students', 204, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(321, 646, 'Import: menambah data siswa MAHARDIKA', 'students', 205, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(322, 646, 'Import: menambah data siswa KEAN SEVENSIX', 'students', 206, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(323, 646, 'Import: menambah data siswa ALISHA HUSNA', 'students', 207, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"YA\"}', '2026-08-10 01:48:21'),
(324, 646, 'Import: menambah data siswa ALDEN K', 'students', 208, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(325, 646, 'Import: menambah data siswa DANISH BAHTIAR', 'students', 209, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(326, 646, 'Import: menambah data siswa ARKAN ALFATIH', 'students', 210, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(327, 646, 'Import: menambah data siswa KAI KINAN', 'students', 211, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1100000,\"total_biaya\":1100000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(328, 646, 'Import: menambah data siswa M KAIZAN', 'students', 212, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(329, 646, 'Import: menambah data siswa WULAN CANIA', 'students', 213, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(330, 646, 'Import: menambah data siswa RAFI SULTAN', 'students', 214, NULL, '{\"biaya_pendaftaran\":250000,\"biaya_program\":850000,\"total_biaya\":1100000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(331, 646, 'Import: menambah data siswa SYALUNNA', 'students', 215, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1250000,\"total_biaya\":1250000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:48:21'),
(332, 646, 'Import data siswa: 96 baru, 0 diperbarui, 0 dilewati, 0 gagal', 'students', NULL, NULL, NULL, '2026-08-10 01:48:21'),
(333, 646, 'Logout dari sistem', 'users', 646, NULL, NULL, '2026-08-10 01:49:36'),
(334, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-10 01:49:41'),
(335, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-10 01:58:38'),
(336, 647, 'Login ke sistem', 'users', 647, NULL, NULL, '2026-08-10 01:58:42'),
(337, 647, 'Import: menambah data siswa Priyanka Inara', 'students', 216, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:31'),
(338, 647, 'Import: menambah data siswa Deandra Razak', 'students', 217, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:31'),
(339, 647, 'Import: menambah data siswa Zalfa Aqilah Mahbub', 'students', 218, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:31'),
(340, 647, 'Import: menambah data siswa Zein Aqil Mahbub', 'students', 219, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:31'),
(341, 647, 'Import: menambah data siswa Kirana Haristya Dayu', 'students', 220, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:31'),
(342, 647, 'Import: menambah data siswa Nabila Zahira Budiman', 'students', 221, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:31'),
(343, 647, 'Import: menambah data siswa Reza Nurruh Pratama', 'students', 222, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:31'),
(344, 647, 'Import: menambah data siswa Den Ripky Aldirano', 'students', 223, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:31'),
(345, 647, 'Import: menambah data siswa Muhammad Alfarisy Hasbullah', 'students', 224, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:31'),
(346, 647, 'Import: menambah data siswa Herlangga Putra Syafa\'atullah', 'students', 225, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:31'),
(347, 647, 'Import: menambah data siswa Muhammad Akbar Firmansyah', 'students', 226, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:31'),
(348, 647, 'Import: menambah data siswa M. Noval Anwari Faiz', 'students', 227, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:31'),
(349, 647, 'Import: menambah data siswa Adzkia Putri Setyawana', 'students', 228, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:31'),
(350, 647, 'Import: menambah data siswa Jakalana Sampurna Budiman', 'students', 229, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:31'),
(351, 647, 'Import: menambah data siswa Fawaz Nur Fajar', 'students', 230, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(352, 647, 'Import: menambah data siswa Kanaya Sevilla Putriani', 'students', 231, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(353, 647, 'Import: menambah data siswa Maheswara Rabbani Wicaksono', 'students', 232, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(354, 647, 'Import: menambah data siswa Muhammad Daffa Dhiya', 'students', 233, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(355, 647, 'Import: menambah data siswa Adly Rezky A', 'students', 234, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(356, 647, 'Import: menambah data siswa Chaska Alvaro', 'students', 235, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(357, 647, 'Import: menambah data siswa Akhsan Alby Fauzan', 'students', 236, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(358, 647, 'Import: menambah data siswa Widya Irawan Sanjaya', 'students', 237, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(359, 647, 'Import: menambah data siswa Kenzo Richie Ilham', 'students', 238, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(360, 647, 'Import: menambah data siswa Alif Septian Nugraha', 'students', 239, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(361, 647, 'Import: menambah data siswa Naufal Dzikri Amarulah', 'students', 240, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(362, 647, 'Import: menambah data siswa Aisyah Nurul Anshor', 'students', 241, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(363, 647, 'Import: menambah data siswa Zhalika Khaira Lubna', 'students', 242, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(364, 647, 'Import: menambah data siswa Kean Sevensix Saputra', 'students', 243, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(365, 647, 'Import: menambah data siswa Ichsan Fajri', 'students', 244, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(366, 647, 'Import: menambah data siswa Nobeldy El Shidqan', 'students', 245, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(367, 647, 'Import: menambah data siswa Aufaa Sakha Suryadi', 'students', 246, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(368, 647, 'Import: menambah data siswa Fauzan Rhido Al Fharoby', 'students', 247, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(369, 647, 'Import: menambah data siswa M. Raihan Aria Putra', 'students', 248, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(370, 647, 'Import: menambah data siswa Adena Zahirah Mahsa', 'students', 249, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(371, 647, 'Import: menambah data siswa Abidzar Dimas Firdaus', 'students', 250, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(372, 647, 'Import: menambah data siswa Bilqis Shidqia Putri', 'students', 251, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(373, 647, 'Import: menambah data siswa Keisya Putri Azzahra', 'students', 252, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(374, 647, 'Import: menambah data siswa Adhwa Fajri Annada', 'students', 253, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(375, 647, 'Import: menambah data siswa Naura Hasna Humaira', 'students', 254, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(376, 647, 'Import: menambah data siswa Davia Fatimah Adeline', 'students', 255, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(377, 647, 'Import: menambah data siswa M. Irsyad Rifki', 'students', 256, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(378, 647, 'Import: menambah data siswa Liby Hadilla Selsa A.', 'students', 257, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(379, 647, 'Import: menambah data siswa Jarvis Gyasi Abdilah', 'students', 258, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(380, 647, 'Import: menambah data siswa Dinar Al Kautsar R', 'students', 259, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(381, 647, 'Import: menambah data siswa Rifki M. Fauzi', 'students', 260, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(382, 647, 'Import: menambah data siswa M. Radyansyah Anahda', 'students', 261, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(383, 647, 'Import: menambah data siswa Zahra Hamidah', 'students', 262, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(384, 647, 'Import: menambah data siswa King Ghazali Al. K.', 'students', 263, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(385, 647, 'Import: menambah data siswa M. Rafi Ali Al Faqih', 'students', 264, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(386, 647, 'Import: menambah data siswa Hasbi Alfitra', 'students', 265, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(387, 647, 'Import: menambah data siswa Safarazky Putra Berlian', 'students', 266, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(388, 647, 'Import: menambah data siswa Muhammad Alwi R.', 'students', 267, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(389, 647, 'Import: menambah data siswa Azka Ramadhan', 'students', 268, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(390, 647, 'Import: menambah data siswa Nalendra Razka Imam', 'students', 269, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(391, 647, 'Import: menambah data siswa Rafa Athaya Pahlevi', 'students', 270, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(392, 647, 'Import: menambah data siswa Aurel Kiranimas', 'students', 271, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(393, 647, 'Import: menambah data siswa Edelweis Sahila Basyar', 'students', 272, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(394, 647, 'Import: menambah data siswa Nazwa Syahira Putri D', 'students', 273, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(395, 647, 'Import: menambah data siswa Aluna Keinisya Azzahra', 'students', 274, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(396, 647, 'Import: menambah data siswa Humam Zaida Putra M.', 'students', 275, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(397, 647, 'Import: menambah data siswa Naufal Imana Triana', 'students', 276, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(398, 647, 'Import: menambah data siswa Mandala Bagaskara', 'students', 277, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(399, 647, 'Import: menambah data siswa Nimas Fathonah', 'students', 278, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(400, 647, 'Import: menambah data siswa M. Ardhi Ghoniyul I.', 'students', 279, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(401, 647, 'Import: menambah data siswa Chintya Amanda R', 'students', 280, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(402, 647, 'Import: menambah data siswa Fathimah Az Zahra', 'students', 281, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(403, 647, 'Import: menambah data siswa Muhammad Al Ghazali', 'students', 282, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(404, 647, 'Import: menambah data siswa Azka Nambagus B', 'students', 283, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(405, 647, 'Import: menambah data siswa Virajati Ramadhan', 'students', 284, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(406, 647, 'Import: menambah data siswa Nayla Sofia', 'students', 285, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(407, 647, 'Import: menambah data siswa Khayla Shaqueena R.', 'students', 286, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(408, 647, 'Import: menambah data siswa Khairahma Putri Lindya', 'students', 287, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(409, 647, 'Import: menambah data siswa Elmira Zivania', 'students', 288, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(410, 647, 'Import: menambah data siswa Muhammad Taruna A.', 'students', 289, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(411, 647, 'Import: menambah data siswa Rizky Hamdani', 'students', 290, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(412, 647, 'Import: menambah data siswa Ariq Aziz Syamsi', 'students', 291, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(413, 647, 'Import: menambah data siswa Athalla', 'students', 292, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(414, 647, 'Import: menambah data siswa Qeenantye Putri Kastorie', 'students', 293, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(415, 647, 'Import: menambah data siswa Geza Alfarizky', 'students', 294, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(416, 647, 'Import: menambah data siswa Farzan Rafindra Wismono', 'students', 295, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(417, 647, 'Import: menambah data siswa Ayyash Thoriq Habibi', 'students', 296, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(418, 647, 'Import: menambah data siswa Fariza Muhammad A.H', 'students', 297, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(419, 647, 'Import: menambah data siswa Rio Anggara', 'students', 298, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(420, 647, 'Import: menambah data siswa Senia Ramadani', 'students', 299, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(421, 647, 'Import: menambah data siswa Andhirni Putri Alifah', 'students', 300, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(422, 647, 'Import: menambah data siswa Nadhif Al Islami', 'students', 301, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(423, 647, 'Import: menambah data siswa M. Elfadli Athallah', 'students', 302, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(424, 647, 'Import: menambah data siswa Revio Septa Resmana', 'students', 303, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(425, 647, 'Import: menambah data siswa Darrel Fathan Ibrahim', 'students', 304, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(426, 647, 'Import: menambah data siswa Astara Luca Arkatama', 'students', 305, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(427, 647, 'Import: menambah data siswa Gavar Muhammad', 'students', 306, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"YA\"}', '2026-08-10 01:59:32'),
(428, 647, 'Import: menambah data siswa Putri safura Alisha Novadela', 'students', 307, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 01:59:32'),
(429, 647, 'Import data siswa: 92 baru, 0 diperbarui, 0 dilewati, 0 gagal', 'students', NULL, NULL, NULL, '2026-08-10 01:59:32'),
(430, 647, 'Logout dari sistem', 'users', 647, NULL, NULL, '2026-08-10 02:00:37'),
(431, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-10 02:00:42'),
(432, 430, 'Menambah data master Tahun Ajaran: 2026/2027', 'academic_years', 3, NULL, NULL, '2026-08-10 02:05:35'),
(433, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-10 02:12:33'),
(434, 649, 'Login ke sistem', 'users', 649, NULL, NULL, '2026-08-10 02:12:37'),
(435, 649, 'Import: menambah data siswa Bazla Rot', 'students', 308, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4080000,\"total_biaya\":4080000,\"is_psam\":\"YA\"}', '2026-08-10 02:12:50'),
(436, 649, 'Import: menambah data siswa Muhammad Danish', 'students', 309, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4080000,\"total_biaya\":4080000,\"is_psam\":\"YA\"}', '2026-08-10 02:12:50'),
(437, 649, 'Import: menambah data siswa Ayesha Belva A', 'students', 310, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4080000,\"total_biaya\":4080000,\"is_psam\":\"TIDAK\"}', '2026-08-10 02:12:50'),
(438, 649, 'Import: menambah data siswa Sadira Janeeta', 'students', 311, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4080000,\"total_biaya\":4080000,\"is_psam\":\"TIDAK\"}', '2026-08-10 02:12:50'),
(439, 649, 'Import: menambah data siswa Geofarrel Ibrahim', 'students', 312, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1000000,\"total_biaya\":1000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 02:12:50'),
(440, 649, 'Import: menambah data siswa Ceyzar Putra', 'students', 313, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2620000,\"total_biaya\":2620000,\"is_psam\":\"YA\"}', '2026-08-10 02:12:50'),
(441, 649, 'Import: menambah data siswa M. Hirzam Abiayasa', 'students', 314, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1000000,\"total_biaya\":1000000,\"is_psam\":\"YA\"}', '2026-08-10 02:12:50'),
(442, 649, 'Import: menambah data siswa Raisa Azka Arifin', 'students', 315, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2730000,\"total_biaya\":2730000,\"is_psam\":\"TIDAK\"}', '2026-08-10 02:12:50'),
(443, 649, 'Import: menambah data siswa M Danish', 'students', 316, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4080000,\"total_biaya\":4080000,\"is_psam\":\"YA\"}', '2026-08-10 02:12:50'),
(444, 649, 'Import: menambah data siswa Ahsan Muhammad fadilah', 'students', 317, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":5080000,\"total_biaya\":5080000,\"is_psam\":\"YA\"}', '2026-08-10 02:12:50'),
(445, 649, 'Import: menambah data siswa Faira Bilqist S', 'students', 318, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":5080000,\"total_biaya\":5080000,\"is_psam\":\"TIDAK\"}', '2026-08-10 02:12:50'),
(446, 649, 'Import: menambah data siswa M Rafiqurahman arsad', 'students', 319, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":5080000,\"total_biaya\":5080000,\"is_psam\":\"TIDAK\"}', '2026-08-10 02:12:50'),
(447, 649, 'Import: menambah data siswa Calista Odeli Rozadin', 'students', 320, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":8650000,\"total_biaya\":8650000,\"is_psam\":\"TIDAK\"}', '2026-08-10 02:12:50'),
(448, 649, 'Import data siswa: 13 baru, 0 diperbarui, 0 dilewati, 0 gagal', 'students', NULL, NULL, NULL, '2026-08-10 02:12:50'),
(449, 649, 'Logout dari sistem', 'users', 649, NULL, NULL, '2026-08-10 02:13:04'),
(450, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-10 02:13:09'),
(451, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-10 02:30:30'),
(452, 648, 'Login ke sistem', 'users', 648, NULL, NULL, '2026-08-10 02:30:35'),
(453, 648, 'Import: menambah data siswa RISA SYALWA OKTAVIA', 'students', 321, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":6000000,\"total_biaya\":6000000,\"is_psam\":\"YA\"}', '2026-08-10 02:30:46'),
(454, 648, 'Import: menambah data siswa M DANISH SABRAN', 'students', 322, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":6000000,\"total_biaya\":6000000,\"is_psam\":\"YA\"}', '2026-08-10 02:30:46'),
(455, 648, 'Import: menambah data siswa AYESHA SALSABILA', 'students', 323, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":6000000,\"total_biaya\":6000000,\"is_psam\":\"YA\"}', '2026-08-10 02:30:46'),
(456, 648, 'Import: menambah data siswa M ZULFADLI', 'students', 324, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":6000000,\"total_biaya\":6000000,\"is_psam\":\"YA\"}', '2026-08-10 02:30:46'),
(457, 648, 'Import: menambah data siswa ARFI ABDA\'A ISMAIL', 'students', 325, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":6000000,\"total_biaya\":6000000,\"is_psam\":\"YA\"}', '2026-08-10 02:30:46'),
(458, 648, 'Import: menambah data siswa MARSYAFIRA BUNGA CHANIAGO', 'students', 326, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":6000000,\"total_biaya\":6000000,\"is_psam\":\"YA\"}', '2026-08-10 02:30:46'),
(459, 648, 'Import: menambah data siswa JAMILATUS SOLEHA', 'students', 327, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":6000000,\"total_biaya\":6000000,\"is_psam\":\"YA\"}', '2026-08-10 02:30:46'),
(460, 648, 'Import: menambah data siswa FAUZAN RHIDO ALFHAROBY', 'students', 328, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":6000000,\"total_biaya\":6000000,\"is_psam\":\"YA\"}', '2026-08-10 02:30:46'),
(461, 648, 'Import: menambah data siswa M RAIHAN A.P', 'students', 329, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":6000000,\"total_biaya\":6000000,\"is_psam\":\"YA\"}', '2026-08-10 02:30:46'),
(462, 648, 'Import data siswa: 9 baru, 0 diperbarui, 0 dilewati, 0 gagal', 'students', NULL, NULL, NULL, '2026-08-10 02:30:46'),
(463, 648, 'Logout dari sistem', 'users', 648, NULL, NULL, '2026-08-10 02:30:51'),
(464, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-10 02:30:55'),
(465, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-10 02:33:27'),
(466, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-10 02:33:32'),
(467, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-10 02:33:37'),
(468, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-10 02:33:42'),
(469, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-10 02:38:20'),
(470, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-10 02:40:05'),
(471, 430, 'Admin menambah setoran bagi hasil: Rp76.776.750 (Disetujui)', 'setoran_bagi_hasil', 1, NULL, '{\"partner_id\":4,\"tanggal_setoran\":\"2026-08-10\",\"nominal\":76776750,\"bukti_file\":null,\"keterangan\":\"\",\"status\":\"disetujui\",\"sumber\":\"admin\",\"created_by\":430,\"reviewed_by\":430,\"reviewed_at\":\"2026-08-10 09:43:36\"}', '2026-08-10 02:43:36'),
(472, 430, 'Admin menambah setoran bagi hasil: Rp8.100.000 (Disetujui)', 'setoran_bagi_hasil', 2, NULL, '{\"partner_id\":7,\"tanggal_setoran\":\"2026-08-10\",\"nominal\":8100000,\"bukti_file\":null,\"keterangan\":\"\",\"status\":\"disetujui\",\"sumber\":\"admin\",\"created_by\":430,\"reviewed_by\":430,\"reviewed_at\":\"2026-08-10 09:43:56\"}', '2026-08-10 02:43:56'),
(473, 430, 'Admin menambah setoran bagi hasil: Rp55.290.000 (Disetujui)', 'setoran_bagi_hasil', 3, NULL, '{\"partner_id\":6,\"tanggal_setoran\":\"2026-08-10\",\"nominal\":55290000,\"bukti_file\":null,\"keterangan\":\"\",\"status\":\"disetujui\",\"sumber\":\"admin\",\"created_by\":430,\"reviewed_by\":430,\"reviewed_at\":\"2026-08-10 09:44:20\"}', '2026-08-10 02:44:20'),
(474, 430, 'Admin menambah setoran bagi hasil: Rp43.920.000 (Disetujui)', 'setoran_bagi_hasil', 4, NULL, '{\"partner_id\":5,\"tanggal_setoran\":\"2026-08-10\",\"nominal\":43920000,\"bukti_file\":null,\"keterangan\":\"\",\"status\":\"disetujui\",\"sumber\":\"admin\",\"created_by\":430,\"reviewed_by\":430,\"reviewed_at\":\"2026-08-10 09:44:43\"}', '2026-08-10 02:44:43'),
(475, 430, 'Admin menambah setoran bagi hasil: Rp7.746.000 (Disetujui)', 'setoran_bagi_hasil', 5, NULL, '{\"partner_id\":8,\"tanggal_setoran\":\"2026-08-10\",\"nominal\":7746000,\"bukti_file\":null,\"keterangan\":\"\",\"status\":\"disetujui\",\"sumber\":\"admin\",\"created_by\":430,\"reviewed_by\":430,\"reviewed_at\":\"2026-08-10 09:45:16\"}', '2026-08-10 02:45:16'),
(476, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-10 03:12:23'),
(477, 542, 'Login ke sistem', 'users', 542, NULL, NULL, '2026-08-10 03:12:30'),
(478, 542, 'Logout dari sistem', 'users', 542, NULL, NULL, '2026-08-10 03:13:11'),
(479, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-10 03:13:17'),
(480, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.5,\"tax_method\":\"deduct_share\",\"is_public\":1,\"display_order\":0,\"public_description\":\"\",\"public_programs\":\"\",\"public_levels\":\"\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"\",\"public_address\":\"\",\"public_logo\":\"1786334146_logo_images.png\",\"public_cover\":\"1786334146_cover_iEvLQJV9AA8BZtQpth2aBpXYU4.avif\"}', '2026-08-10 03:55:46'),
(481, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.5,\"tax_method\":\"deduct_share\",\"is_public\":1,\"display_order\":0,\"public_description\":\"\",\"public_programs\":\"\",\"public_levels\":\"\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"\",\"public_address\":\"\",\"public_cover\":\"1786334402_cover_unnamed (10).png\"}', '2026-08-10 04:00:02'),
(482, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.5,\"tax_method\":\"deduct_share\",\"is_public\":1,\"display_order\":0,\"public_description\":\"\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"\",\"public_address\":\"\"}', '2026-08-10 04:00:53'),
(483, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.5,\"tax_method\":\"deduct_share\",\"is_public\":1,\"display_order\":0,\"public_description\":\"\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-7649-5990\",\"public_address\":\"\"}', '2026-08-10 04:02:50'),
(484, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.5,\"tax_method\":\"deduct_share\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-7649-5990\",\"public_address\":\"\"}', '2026-08-10 04:04:53');
INSERT INTO `audit_logs` (`id`, `user_id`, `aktivitas`, `tabel`, `data_id`, `data_lama`, `data_baru`, `created_at`) VALUES
(485, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.5,\"tax_method\":\"deduct_share\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-7649-5990\",\"public_address\":\"\"}', '2026-08-10 04:13:36'),
(486, 430, 'Mengubah mitra: PRIMAGAMA', 'partners', 5, NULL, '{\"nama_mitra\":\"PRIMAGAMA\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":0,\"tax_percentage\":0,\"tax_method\":\"none\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Mata pelajaran SD    : Semua mata pelajaran \\r\\nMata pelajaran SMP : Matematika, IPA(Fisika,Biologi, Kimia, Bhs Inggris, Bhs Indonesia)\",\"public_programs\":\"\",\"public_levels\":\"SD,SMP\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-5649-0920\",\"public_address\":\"\",\"public_logo\":\"1786335371_logo_images (1).png\"}', '2026-08-10 04:16:11'),
(487, 430, 'Mengubah mitra: IEC', 'partners', 6, NULL, '{\"nama_mitra\":\"IEC\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":0,\"tax_percentage\":0,\"tax_method\":\"none\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Lembaga Kursus Bahasa Inggris yang menekankan pada pengembangan kemampuan komunikatif dengan metodologi inovatif yang memberikan solusi terhadap setiap kesulitan siswa\",\"public_programs\":\"\",\"public_levels\":\"\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0821-3031-2324\",\"public_address\":\"\",\"public_logo\":\"1786335540_logo_Al Ma’soem (11).png\"}', '2026-08-10 04:19:00'),
(488, 430, 'Mengubah mitra: IEC', 'partners', 6, NULL, '{\"nama_mitra\":\"IEC\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":0,\"tax_percentage\":0,\"tax_method\":\"none\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Lembaga Kursus Bahasa Inggris yang menekankan pada pengembangan kemampuan komunikatif dengan metodologi inovatif yang memberikan solusi terhadap setiap kesulitan siswa\",\"public_programs\":\"\",\"public_levels\":\"SD,SMP,SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0821-3031-2324\",\"public_address\":\"\"}', '2026-08-10 04:19:27'),
(489, 430, 'Mengubah mitra: PRIMAGAMA', 'partners', 5, NULL, '{\"nama_mitra\":\"PRIMAGAMA\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":0,\"tax_percentage\":0,\"tax_method\":\"none\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Mata pelajaran SD    : Semua mata pelajaran \\r\\nMata pelajaran SMP : Matematika, IPA(Fisika,Biologi, Kimia, Bhs Inggris, Bhs Indonesia)\",\"public_programs\":\"\",\"public_levels\":\"SD,SMP\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-5649-0920\",\"public_address\":\"\",\"public_logo\":\"1786335717_logo_Al Ma’soem (13).png\"}', '2026-08-10 04:21:57'),
(490, 430, 'Mengubah mitra: CENTRAL BRAINPRO', 'partners', 7, NULL, '{\"nama_mitra\":\"CENTRAL BRAINPRO\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":0,\"tax_percentage\":0,\"tax_method\":\"none\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Lembaga yang bisa menguatkan daya analisis sehingga motivasi dan kemampuan belajar anak meningkat berlipat. Berbagai mata  pelajaran menjadi mudah dikuasai.\",\"public_programs\":\"\",\"public_levels\":\"SD,SMP,SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0812-8758-6322\",\"public_address\":\"\",\"public_logo\":\"1786335931_logo_Al Ma’soem (14).png\"}', '2026-08-10 04:25:31'),
(491, 430, 'Mengubah mitra: Padjadjaran Language School', 'partners', 8, NULL, '{\"nama_mitra\":\"Padjadjaran Language School\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":0,\"tax_percentage\":0,\"tax_method\":\"none\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Menyelenggarakan kursus berbagai bahasa asing Terpercaya Untuk SD-SMP-SMA-S1\",\"public_programs\":\"\",\"public_levels\":\"\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0899-7220-7957\",\"public_address\":\"\",\"public_logo\":\"1786336014_logo_Al Ma’soem (15).png\"}', '2026-08-10 04:26:54'),
(492, 430, 'Mengubah mitra: Padjadjaran Language School', 'partners', 8, NULL, '{\"nama_mitra\":\"Padjadjaran Language School\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":0,\"tax_percentage\":0,\"tax_method\":\"none\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Menyelenggarakan kursus berbagai bahasa asing Terpercaya Untuk SD-SMP-SMA-S1\",\"public_programs\":\"\",\"public_levels\":\"SD,SMP,SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0899-7220-7957\",\"public_address\":\"\"}', '2026-08-10 04:27:12'),
(493, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.5,\"tax_method\":\"deduct_share\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-7649-5990\",\"public_address\":\"\",\"public_logo\":\"1786336103_logo_horse riding courses 0895-3719-79297 For information and reservations (Sandhi) Train with Skill, Grow with Adab (2).png\"}', '2026-08-10 04:28:23'),
(494, 430, 'Menambah jadwal pemakaian ruang', 'jadwal_pemakaian_ruang', 1, NULL, NULL, '2026-08-10 05:11:59'),
(495, 430, 'Menghapus jadwal pemakaian ruang', 'jadwal_pemakaian_ruang', 1, NULL, NULL, '2026-08-10 05:12:28'),
(496, 430, 'Menambah jadwal pemakaian ruang', 'jadwal_pemakaian_ruang', 2, NULL, NULL, '2026-08-10 05:41:22'),
(497, 430, 'Menambah jadwal pemakaian ruang', 'jadwal_pemakaian_ruang', 3, NULL, NULL, '2026-08-10 05:41:46'),
(498, 430, 'Menghapus jadwal pemakaian ruang', 'jadwal_pemakaian_ruang', 3, NULL, NULL, '2026-08-10 05:42:16'),
(499, 430, 'Menghapus jadwal pemakaian ruang', 'jadwal_pemakaian_ruang', 2, NULL, NULL, '2026-08-10 05:42:18'),
(500, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.6,\"tax_method\":\"deduct_share\",\"is_public\":1,\"display_order\":0,\"public_description\":\"Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-7649-5990\",\"public_address\":\"\"}', '2026-08-10 08:55:45'),
(501, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-10 09:23:24'),
(502, 647, 'Login ke sistem', 'users', 647, NULL, NULL, '2026-08-10 09:23:28'),
(503, 647, 'Import: menambah data siswa Priyanka Inara', 'students', 330, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2280000,\"total_biaya\":2280000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(504, 647, 'Import: menambah data siswa Deandra Razak', 'students', 331, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2280000,\"total_biaya\":2280000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(505, 647, 'Import: menambah data siswa Zalfa Aqilah Mahbub', 'students', 332, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1900000,\"total_biaya\":1900000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(506, 647, 'Import: menambah data siswa Zein Aqil Mahbub', 'students', 333, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":1900000,\"total_biaya\":1900000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(507, 647, 'Import: menambah data siswa Kirana Haristya Dayu', 'students', 334, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2940000,\"total_biaya\":2940000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(508, 647, 'Import: menambah data siswa Nabila Zahira Budiman', 'students', 335, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2280000,\"total_biaya\":2280000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(509, 647, 'Import: menambah data siswa Reza Nurruh Pratama', 'students', 336, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2400000,\"total_biaya\":2400000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(510, 647, 'Import: menambah data siswa Den Ripky Aldirano', 'students', 337, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2300000,\"total_biaya\":2300000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(511, 647, 'Import: menambah data siswa Muhammad Alfarisy Hasbullah', 'students', 338, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2940000,\"total_biaya\":2940000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(512, 647, 'Import: menambah data siswa Herlangga Putra Syafa\'atullah', 'students', 339, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2100000,\"total_biaya\":2100000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(513, 647, 'Import: menambah data siswa Muhammad Akbar Firmansyah', 'students', 340, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3200000,\"total_biaya\":3200000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(514, 647, 'Import: menambah data siswa M. Noval Anwari Faiz', 'students', 341, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2940000,\"total_biaya\":2940000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(515, 647, 'Import: menambah data siswa Adzkia Putri Setyawana', 'students', 342, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3040000,\"total_biaya\":3040000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(516, 647, 'Import: menambah data siswa Jakalana Sampurna Budiman', 'students', 343, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2520000,\"total_biaya\":2520000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(517, 647, 'Import: menambah data siswa Fawaz Nur Fajar', 'students', 344, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3200000,\"total_biaya\":3200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(518, 647, 'Import: menambah data siswa Kanaya Sevilla Putriani', 'students', 345, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3360000,\"total_biaya\":3360000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(519, 647, 'Import: menambah data siswa Maheswara Rabbani Wicaksono', 'students', 346, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3360000,\"total_biaya\":3360000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(520, 647, 'Import: menambah data siswa Muhammad Daffa Dhiya', 'students', 347, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3360000,\"total_biaya\":3360000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(521, 647, 'Import: menambah data siswa Adly Rezky A', 'students', 348, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3200000,\"total_biaya\":3200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(522, 647, 'Import: menambah data siswa Chaska Alvaro', 'students', 349, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3040000,\"total_biaya\":3040000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(523, 647, 'Import: menambah data siswa Akhsan Alby Fauzan', 'students', 350, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2660000,\"total_biaya\":2660000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(524, 647, 'Import: menambah data siswa Widya Irawan Sanjaya', 'students', 351, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3200000,\"total_biaya\":3200000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(525, 647, 'Import: menambah data siswa Kenzo Richie Ilham', 'students', 352, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3200000,\"total_biaya\":3200000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(526, 647, 'Import: menambah data siswa Alif Septian Nugraha', 'students', 353, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(527, 647, 'Import: menambah data siswa Naufal Dzikri Amarulah', 'students', 354, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(528, 647, 'Import: menambah data siswa Aisyah Nurul Anshor', 'students', 355, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3570000,\"total_biaya\":3570000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(529, 647, 'Import: menambah data siswa Zhalika Khaira Lubna', 'students', 356, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3040000,\"total_biaya\":3040000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(530, 647, 'Import: menambah data siswa Kean Sevensix Saputra', 'students', 357, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(531, 647, 'Import: menambah data siswa Ichsan Fajri', 'students', 358, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(532, 647, 'Import: menambah data siswa Nobeldy El Shidqan', 'students', 359, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(533, 647, 'Import: menambah data siswa Aufaa Sakha Suryadi', 'students', 360, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(534, 647, 'Import: menambah data siswa Fauzan Rhido Al Fharoby', 'students', 361, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(535, 647, 'Import: menambah data siswa M. Raihan Aria Putra', 'students', 362, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(536, 647, 'Import: menambah data siswa Adena Zahirah Mahsa', 'students', 363, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(537, 647, 'Import: menambah data siswa Abidzar Dimas Firdaus', 'students', 364, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(538, 647, 'Import: menambah data siswa Bilqis Shidqia Putri', 'students', 365, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(539, 647, 'Import: menambah data siswa Keisya Putri Azzahra', 'students', 366, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(540, 647, 'Import: menambah data siswa Adhwa Fajri Annada', 'students', 367, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(541, 647, 'Import: menambah data siswa Naura Hasna Humaira', 'students', 368, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2900000,\"total_biaya\":2900000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(542, 647, 'Import: menambah data siswa Davia Fatimah Adeline', 'students', 369, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(543, 647, 'Import: menambah data siswa M. Irsyad Rifki', 'students', 370, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3572500,\"total_biaya\":3572500,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(544, 647, 'Import: menambah data siswa Liby Hadilla Selsa A.', 'students', 371, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(545, 647, 'Import: menambah data siswa Jarvis Gyasi Abdilah', 'students', 372, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(546, 647, 'Import: menambah data siswa Dinar Al Kautsar R', 'students', 373, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(547, 647, 'Import: menambah data siswa Rifki M. Fauzi', 'students', 374, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(548, 647, 'Import: menambah data siswa M. Radyansyah Anahda', 'students', 375, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2900000,\"total_biaya\":2900000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(549, 647, 'Import: menambah data siswa Zahra Hamidah', 'students', 376, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(550, 647, 'Import: menambah data siswa King Ghazali Al. K.', 'students', 377, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(551, 647, 'Import: menambah data siswa M. Rafi Ali Al Faqih', 'students', 378, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(552, 647, 'Import: menambah data siswa Hasbi Alfitra', 'students', 379, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3230000,\"total_biaya\":3230000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(553, 647, 'Import: menambah data siswa Safarazky Putra Berlian', 'students', 380, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(554, 647, 'Import: menambah data siswa Muhammad Alwi R.', 'students', 381, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3570000,\"total_biaya\":3570000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(555, 647, 'Import: menambah data siswa Azka Ramadhan', 'students', 382, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3570000,\"total_biaya\":3570000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(556, 647, 'Import: menambah data siswa Nalendra Razka Imam', 'students', 383, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3070000,\"total_biaya\":3070000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(557, 647, 'Import: menambah data siswa Rafa Athaya Pahlevi', 'students', 384, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3570000,\"total_biaya\":3570000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(558, 647, 'Import: menambah data siswa Aurel Kiranimas', 'students', 385, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3230000,\"total_biaya\":3230000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(559, 647, 'Import: menambah data siswa Edelweis Sahila Basyar', 'students', 386, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3780000,\"total_biaya\":3780000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(560, 647, 'Import: menambah data siswa Nazwa Syahira Putri D', 'students', 387, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3600000,\"total_biaya\":3600000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(561, 647, 'Import: menambah data siswa Aluna Keinisya Azzahra', 'students', 388, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3420000,\"total_biaya\":3420000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(562, 647, 'Import: menambah data siswa Humam Zaida Putra M.', 'students', 389, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3780000,\"total_biaya\":3780000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(563, 647, 'Import: menambah data siswa Naufal Imana Triana', 'students', 390, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3600000,\"total_biaya\":3600000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(564, 647, 'Import: menambah data siswa Mandala Bagaskara', 'students', 391, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3780000,\"total_biaya\":3780000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(565, 647, 'Import: menambah data siswa Nimas Fathonah', 'students', 392, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3780000,\"total_biaya\":3780000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(566, 647, 'Import: menambah data siswa M. Ardhi Ghoniyul I.', 'students', 393, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3100000,\"total_biaya\":3100000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(567, 647, 'Import: menambah data siswa Chintya Amanda R', 'students', 394, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2800000,\"total_biaya\":2800000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(568, 647, 'Import: menambah data siswa Fathimah Az Zahra', 'students', 395, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2660000,\"total_biaya\":2660000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(569, 647, 'Import: menambah data siswa Muhammad Al Ghazali', 'students', 396, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3040000,\"total_biaya\":3040000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(570, 647, 'Import: menambah data siswa Azka Nambagus B', 'students', 397, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3040000,\"total_biaya\":3040000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(571, 647, 'Import: menambah data siswa Virajati Ramadhan', 'students', 398, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3780000,\"total_biaya\":3780000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(572, 647, 'Import: menambah data siswa Nayla Sofia', 'students', 399, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3040000,\"total_biaya\":3040000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(573, 647, 'Import: menambah data siswa Khayla Shaqueena R.', 'students', 400, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3780000,\"total_biaya\":3780000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(574, 647, 'Import: menambah data siswa Khairahma Putri Lindya', 'students', 401, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3040000,\"total_biaya\":3040000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(575, 647, 'Import: menambah data siswa Elmira Zivania', 'students', 402, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3200000,\"total_biaya\":3200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(576, 647, 'Import: menambah data siswa Muhammad Taruna A.', 'students', 403, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3780000,\"total_biaya\":3780000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(577, 647, 'Import: menambah data siswa Rizky Hamdani', 'students', 404, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3360000,\"total_biaya\":3360000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(578, 647, 'Import: menambah data siswa Ariq Aziz Syamsi', 'students', 405, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2660000,\"total_biaya\":2660000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(579, 647, 'Import: menambah data siswa Athalla', 'students', 406, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3360000,\"total_biaya\":3360000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(580, 647, 'Import: menambah data siswa Qeenantye Putri Kastorie', 'students', 407, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2520000,\"total_biaya\":2520000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(581, 647, 'Import: menambah data siswa Geza Alfarizky', 'students', 408, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2280000,\"total_biaya\":2280000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(582, 647, 'Import: menambah data siswa Farzan Rafindra Wismono', 'students', 409, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2280000,\"total_biaya\":2280000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(583, 647, 'Import: menambah data siswa Ayyash Thoriq Habibi', 'students', 410, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2400000,\"total_biaya\":2400000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(584, 647, 'Import: menambah data siswa Fariza Muhammad A.H', 'students', 411, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(585, 647, 'Import: menambah data siswa Rio Anggara', 'students', 412, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(586, 647, 'Import: menambah data siswa Senia Ramadani', 'students', 413, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(587, 647, 'Import: menambah data siswa Andhirni Putri Alifah', 'students', 414, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3500000,\"total_biaya\":3500000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(588, 647, 'Import: menambah data siswa Nadhif Al Islami', 'students', 415, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(589, 647, 'Import: menambah data siswa M. Elfadli Athallah', 'students', 416, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(590, 647, 'Import: menambah data siswa Revio Septa Resmana', 'students', 417, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(591, 647, 'Import: menambah data siswa Darrel Fathan Ibrahim', 'students', 418, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(592, 647, 'Import: menambah data siswa Astara Luca Arkatama', 'students', 419, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4200000,\"total_biaya\":4200000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(593, 647, 'Import: menambah data siswa Gavar Muhammad', 'students', 420, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3800000,\"total_biaya\":3800000,\"is_psam\":\"YA\"}', '2026-08-10 09:23:49'),
(594, 647, 'Import: menambah data siswa Putri safura Alisha Novadela', 'students', 421, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"TIDAK\"}', '2026-08-10 09:23:49'),
(595, 647, 'Import data siswa: 92 baru, 0 diperbarui, 0 dilewati, 0 gagal', 'students', NULL, NULL, NULL, '2026-08-10 09:23:49'),
(596, 647, 'Logout dari sistem', 'users', 647, NULL, NULL, '2026-08-10 09:23:59'),
(597, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-10 09:24:04'),
(598, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-10 09:42:59'),
(599, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-11 00:55:10'),
(600, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":0,\"tax_percentage\":2.6,\"tax_method\":\"percentage\",\"tax_manual_nominal\":0,\"is_public\":1,\"display_order\":0,\"public_description\":\"Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-7649-5990\",\"public_address\":\"\"}', '2026-08-11 00:55:57'),
(601, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.6,\"tax_method\":\"manual\",\"tax_manual_nominal\":1919418,\"is_public\":1,\"display_order\":0,\"public_description\":\"Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-7649-5990\",\"public_address\":\"\"}', '2026-08-11 01:03:46'),
(602, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.6,\"tax_method\":\"manual\",\"tax_manual_nominal\":1919418,\"is_public\":1,\"display_order\":0,\"public_description\":\"Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-7649-5990\",\"public_address\":\"\"}', '2026-08-11 01:04:14'),
(603, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":0,\"tax_percentage\":2.6,\"tax_method\":\"manual\",\"tax_manual_nominal\":1919418,\"is_public\":1,\"display_order\":0,\"public_description\":\"Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-7649-5990\",\"public_address\":\"\"}', '2026-08-11 01:05:31'),
(604, 430, 'Mengubah mitra: Neutron Bandung', 'partners', 4, NULL, '{\"nama_mitra\":\"Neutron Bandung\",\"penanggung_jawab\":\"\",\"telepon\":\"\",\"email\":\"\",\"alamat\":\"\",\"tanggal_mulai\":\"2026-08-07\",\"status\":\"aktif\",\"keterangan\":\"\",\"persentase_bagi_hasil\":15,\"tax_enabled\":1,\"tax_percentage\":2.6,\"tax_method\":\"manual\",\"tax_manual_nominal\":1919418,\"is_public\":1,\"display_order\":0,\"public_description\":\"Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.\",\"public_programs\":\"\",\"public_levels\":\"SMA\",\"public_program_unggulan\":\"\",\"public_website\":\"\",\"public_whatsapp\":\"0896-7649-5990\",\"public_address\":\"\"}', '2026-08-11 01:05:43'),
(605, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-11 01:22:16'),
(606, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-11 01:22:30'),
(607, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-11 01:22:43'),
(608, 571, 'Login ke sistem', 'users', 571, NULL, NULL, '2026-08-11 01:22:55'),
(609, 571, 'Logout dari sistem', 'users', 571, NULL, NULL, '2026-08-11 01:23:41'),
(610, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-11 01:24:27'),
(611, 430, 'Export laporan rekap (EXCEL)', 'students', NULL, NULL, NULL, '2026-08-11 01:24:32'),
(612, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-11 01:41:53'),
(613, 535, 'Login ke sistem', 'users', 535, NULL, NULL, '2026-08-11 01:41:57'),
(614, 535, 'Logout dari sistem', 'users', 535, NULL, NULL, '2026-08-11 02:02:41'),
(615, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-11 02:02:45'),
(616, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 1, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 1\",\"poin_minimum\":1,\"poin_maksimum\":3,\"nominal_fee\":0,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 02:02:55'),
(617, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 6, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 1\",\"poin_minimum\":1,\"poin_maksimum\":3,\"nominal_fee\":0,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 02:03:01'),
(618, 430, 'Export laporan rekap (EXCEL)', 'students', NULL, NULL, NULL, '2026-08-11 02:03:25'),
(619, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-11 02:15:50'),
(620, 649, 'Login ke sistem', 'users', 649, NULL, NULL, '2026-08-11 02:16:00'),
(621, 649, 'Menambah data siswa: Muhammad Rizky Abidzar', 'students', 437, NULL, '{\"partner_id\":8,\"academic_year_id\":2,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-08-11\",\"nama_siswa\":\"Muhammad Rizky Abidzar\",\"kelas\":\"11-8\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":0,\"biaya_program\":2500000,\"total_biaya\":2500000,\"triwulan\":\"I\",\"is_psam\":\"Ya\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":646,\"wali_santri_id\":549,\"keterangan\":\"\"}', '2026-08-11 02:18:30'),
(622, 649, 'Menambah data siswa: Muhammda Jiddan Khawalni', 'students', 438, NULL, '{\"partner_id\":8,\"academic_year_id\":2,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-08-11\",\"nama_siswa\":\"Muhammda Jiddan Khawalni\",\"kelas\":\"11-5\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":0,\"biaya_program\":3900000,\"total_biaya\":3900000,\"triwulan\":\"I\",\"is_psam\":\"Ya\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":643,\"wali_santri_id\":549,\"keterangan\":\"\"}', '2026-08-11 02:19:30'),
(623, 649, 'Menambah data siswa: Fahmi Rahman Sidiq', 'students', 439, NULL, '{\"partner_id\":8,\"academic_year_id\":2,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-08-11\",\"nama_siswa\":\"Fahmi Rahman Sidiq\",\"kelas\":\"11-6\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":0,\"biaya_program\":3900000,\"total_biaya\":3900000,\"triwulan\":\"I\",\"is_psam\":\"Ya\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":638,\"wali_santri_id\":549,\"keterangan\":\"\"}', '2026-08-11 02:20:38'),
(624, 649, 'Mengubah data siswa: Fahmi Rahman Sidiq', 'students', 439, '{\"id\":439,\"partner_id\":8,\"academic_year_id\":2,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-08-11\",\"nama_siswa\":\"Fahmi Rahman Sidiq\",\"kelas\":\"11-6\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":\"I\",\"is_psam\":\"Ya\",\"total_biaya\":\"3900000.00\",\"biaya_program\":\"3900000.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":638,\"wali_santri_id\":549,\"keterangan\":\"\",\"created_at\":\"2026-08-11 09:20:38\",\"updated_at\":\"2026-08-11 09:20:38\"}', '{\"partner_id\":8,\"academic_year_id\":2,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-08-11\",\"nama_siswa\":\"Fahmi Rahman Sidiq\",\"kelas\":\"11-6\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":0,\"biaya_program\":3900000,\"total_biaya\":3900000,\"triwulan\":\"I\",\"is_psam\":\"Ya\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":644,\"wali_santri_id\":549,\"keterangan\":\"\"}', '2026-08-11 02:22:44'),
(625, 649, 'Menghapus data siswa: Fahmi Rahman Sidiq', 'students', 439, '{\"id\":439,\"partner_id\":8,\"academic_year_id\":2,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-08-11\",\"nama_siswa\":\"Fahmi Rahman Sidiq\",\"kelas\":\"11-6\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":\"I\",\"is_psam\":\"Ya\",\"total_biaya\":\"3900000.00\",\"biaya_program\":\"3900000.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":644,\"wali_santri_id\":549,\"keterangan\":\"\",\"created_at\":\"2026-08-11 09:20:38\",\"updated_at\":\"2026-08-11 09:22:44\"}', NULL, '2026-08-11 02:22:51'),
(626, 649, 'Menghapus data siswa: Muhammda Jiddan Khawalni', 'students', 438, '{\"id\":438,\"partner_id\":8,\"academic_year_id\":2,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-08-11\",\"nama_siswa\":\"Muhammda Jiddan Khawalni\",\"kelas\":\"11-5\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":\"I\",\"is_psam\":\"Ya\",\"total_biaya\":\"3900000.00\",\"biaya_program\":\"3900000.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":643,\"wali_santri_id\":549,\"keterangan\":\"\",\"created_at\":\"2026-08-11 09:19:30\",\"updated_at\":\"2026-08-11 09:19:30\"}', NULL, '2026-08-11 02:22:53'),
(627, 649, 'Menghapus data siswa: Muhammad Rizky Abidzar', 'students', 437, '{\"id\":437,\"partner_id\":8,\"academic_year_id\":2,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-08-11\",\"nama_siswa\":\"Muhammad Rizky Abidzar\",\"kelas\":\"11-8\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":\"0.00\",\"triwulan\":\"I\",\"is_psam\":\"Ya\",\"total_biaya\":\"2500000.00\",\"biaya_program\":\"2500000.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":646,\"wali_santri_id\":549,\"keterangan\":\"\",\"created_at\":\"2026-08-11 09:18:30\",\"updated_at\":\"2026-08-11 09:18:30\"}', NULL, '2026-08-11 02:22:57'),
(628, 649, 'Import: menambah data siswa Muhammad Rizky Abidzar', 'students', 440, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":2500000,\"total_biaya\":2500000,\"is_psam\":\"YA\"}', '2026-08-11 02:25:45'),
(629, 649, 'Import: menambah data siswa Muhammda Jiddan Khawalni', 'students', 441, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3900000,\"total_biaya\":3900000,\"is_psam\":\"YA\"}', '2026-08-11 02:25:45'),
(630, 649, 'Import: menambah data siswa Fahmi Rahman Sidiq', 'students', 442, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3900000,\"total_biaya\":3900000,\"is_psam\":\"YA\"}', '2026-08-11 02:25:45'),
(631, 649, 'Import: menambah data siswa Rifat Ahmad', 'students', 443, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3300000,\"total_biaya\":3300000,\"is_psam\":\"YA\"}', '2026-08-11 02:25:45'),
(632, 649, 'Import: menambah data siswa Barik Ghiyas Somatri', 'students', 444, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3000000,\"total_biaya\":3000000,\"is_psam\":\"YA\"}', '2026-08-11 02:25:45'),
(633, 649, 'Import: menambah data siswa Danish Adjie Fadhillah', 'students', 445, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3750000,\"total_biaya\":3750000,\"is_psam\":\"YA\"}', '2026-08-11 02:25:45'),
(634, 649, 'Import: menambah data siswa Muhammad Khadafy', 'students', 446, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3650000,\"total_biaya\":3650000,\"is_psam\":\"TIDAK\"}', '2026-08-11 02:25:45'),
(635, 649, 'Import: menambah data siswa Nawra rizka ramdhani', 'students', 447, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3650000,\"total_biaya\":3650000,\"is_psam\":\"YA\"}', '2026-08-11 02:25:45'),
(636, 649, 'Import: menambah data siswa Rafa Bintang Bagaskara', 'students', 448, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3900000,\"total_biaya\":3900000,\"is_psam\":\"YA\"}', '2026-08-11 02:25:45'),
(637, 649, 'Import: menambah data siswa Aisyah Nurul Anshor', 'students', 449, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3900000,\"total_biaya\":3900000,\"is_psam\":\"YA\"}', '2026-08-11 02:25:45'),
(638, 649, 'Import: menambah data siswa Karina Kamal', 'students', 450, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":4000000,\"total_biaya\":4000000,\"is_psam\":\"TIDAK\"}', '2026-08-11 02:25:45'),
(639, 649, 'Import: menambah data siswa Muhammad Ikhsan Nurfathan', 'students', 451, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":500000,\"total_biaya\":500000,\"is_psam\":\"TIDAK\"}', '2026-08-11 02:25:45'),
(640, 649, 'Import: menambah data siswa Calista Khairinnisah', 'students', 452, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3400000,\"total_biaya\":3400000,\"is_psam\":\"YA\"}', '2026-08-11 02:25:45'),
(641, 649, 'Import: menambah data siswa Muhammad Satriadi', 'students', 453, NULL, '{\"biaya_pendaftaran\":0,\"biaya_program\":3750000,\"total_biaya\":3750000,\"is_psam\":\"TIDAK\"}', '2026-08-11 02:25:45'),
(642, 649, 'Import data siswa: 14 baru, 0 diperbarui, 0 dilewati, 0 gagal', 'students', NULL, NULL, NULL, '2026-08-11 02:25:45'),
(643, 649, 'Logout dari sistem', 'users', 649, NULL, NULL, '2026-08-11 02:26:00'),
(644, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-11 02:26:04'),
(645, 430, 'Export laporan rekap (EXCEL)', 'students', NULL, NULL, NULL, '2026-08-11 02:26:13'),
(646, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-11 02:28:39'),
(647, 571, 'Login ke sistem', 'users', 571, NULL, NULL, '2026-08-11 02:28:45'),
(648, 571, 'Logout dari sistem', 'users', 571, NULL, NULL, '2026-08-11 02:28:59'),
(649, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-11 02:29:04'),
(650, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-11 02:29:17'),
(651, 550, 'Login ke sistem', 'users', 550, NULL, NULL, '2026-08-11 02:29:24'),
(652, 550, 'Logout dari sistem', 'users', 550, NULL, NULL, '2026-08-11 02:40:25'),
(653, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-11 02:40:28'),
(654, 430, 'Export laporan rekap (EXCEL)', 'students', NULL, NULL, NULL, '2026-08-11 02:53:50'),
(655, 430, 'Export laporan rekap (CSV)', 'students', NULL, NULL, NULL, '2026-08-11 02:57:38'),
(656, 430, 'Export laporan rekap (EXCEL)', 'students', NULL, NULL, NULL, '2026-08-11 03:00:05'),
(657, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 4, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 4\",\"poin_minimum\":9,\"poin_maksimum\":14,\"nominal_fee\":125000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:12:57'),
(658, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 3, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 3\",\"poin_minimum\":5.5,\"poin_maksimum\":9.5,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:13:04'),
(659, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 4, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 4\",\"poin_minimum\":9,\"poin_maksimum\":14.5,\"nominal_fee\":125000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:13:19'),
(660, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 2, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 2\",\"poin_minimum\":3.5,\"poin_maksimum\":5.5,\"nominal_fee\":75000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:13:29'),
(661, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 3, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 3\",\"poin_minimum\":5,\"poin_maksimum\":9.5,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:13:35'),
(662, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 7, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 2\",\"poin_minimum\":3.5,\"poin_maksimum\":5.5,\"nominal_fee\":50000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:13:44'),
(663, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 8, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 3\",\"poin_minimum\":5,\"poin_maksimum\":9.5,\"nominal_fee\":75000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:13:51'),
(664, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 9, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 4\",\"poin_minimum\":9,\"poin_maksimum\":14.5,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:13:58'),
(665, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 3, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 3\",\"poin_minimum\":5,\"poin_maksimum\":9.5,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:16:16'),
(666, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 2, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 2\",\"poin_minimum\":3.5,\"poin_maksimum\":5,\"nominal_fee\":75000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:16:23'),
(667, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 3, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 3\",\"poin_minimum\":5.5,\"poin_maksimum\":9.5,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:16:55'),
(668, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 3, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 3\",\"poin_minimum\":5.5,\"poin_maksimum\":9,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:20:09'),
(669, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 4, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 4\",\"poin_minimum\":9.5,\"poin_maksimum\":14.5,\"nominal_fee\":125000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:20:20'),
(670, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 7, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 2\",\"poin_minimum\":3.5,\"poin_maksimum\":5,\"nominal_fee\":50000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:20:36'),
(671, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 8, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 3\",\"poin_minimum\":5.5,\"poin_maksimum\":9,\"nominal_fee\":75000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:20:49'),
(672, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 9, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 4\",\"poin_minimum\":9.5,\"poin_maksimum\":14.5,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:20:57'),
(673, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 2, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 2\",\"poin_minimum\":3.5,\"poin_maksimum\":5.5,\"nominal_fee\":75000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:23:41'),
(674, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 3, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 3\",\"poin_minimum\":6,\"poin_maksimum\":9.5,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:23:48'),
(675, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 4, NULL, '{\"jenis_rekomendator\":\"wali_kelas\",\"kategori\":\"Kategori 4\",\"poin_minimum\":10,\"poin_maksimum\":14.5,\"nominal_fee\":125000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:23:56'),
(676, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 7, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 2\",\"poin_minimum\":3.5,\"poin_maksimum\":5.5,\"nominal_fee\":50000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:24:06'),
(677, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 8, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 3\",\"poin_minimum\":6,\"poin_maksimum\":9.5,\"nominal_fee\":75000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:24:12'),
(678, 430, 'Mengubah aturan fee rekomendator', 'fee_rules', 9, NULL, '{\"jenis_rekomendator\":\"wali_santri\",\"kategori\":\"Kategori 4\",\"poin_minimum\":10,\"poin_maksimum\":14.5,\"nominal_fee\":100000,\"tanggal_mulai\":\"2025-01-01\",\"tanggal_selesai\":null,\"status\":\"aktif\"}', '2026-08-11 03:24:18'),
(679, 430, 'Export laporan rekap (EXCEL)', 'students', NULL, NULL, NULL, '2026-08-11 03:27:45'),
(680, 430, 'Admin menambah setoran bagi hasil: Rp7.065.000 (Disetujui)', 'setoran_bagi_hasil', 6, NULL, '{\"partner_id\":8,\"tanggal_setoran\":\"2026-08-11\",\"nominal\":7065000,\"bukti_file\":null,\"keterangan\":\"\",\"status\":\"disetujui\",\"sumber\":\"admin\",\"created_by\":430,\"tax_method\":\"none\",\"tax_percentage\":0,\"tax_manual_nominal\":0,\"tax_amount\":0,\"amount_before_tax\":7065000,\"amount_after_tax\":7065000,\"reviewed_by\":430,\"reviewed_at\":\"2026-08-11 10:32:09\"}', '2026-08-11 03:32:09'),
(681, 430, 'Export laporan rekap (EXCEL)', 'students', NULL, NULL, NULL, '2026-08-11 03:51:16'),
(682, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-18 03:47:53'),
(683, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-19 00:43:56');
INSERT INTO `audit_logs` (`id`, `user_id`, `aktivitas`, `tabel`, `data_id`, `data_lama`, `data_baru`, `created_at`) VALUES
(684, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-19 00:44:30'),
(685, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-19 00:44:47'),
(686, 641, 'Mengubah konten public mitra', 'partners', 4, NULL, NULL, '2026-08-19 01:29:46'),
(687, 641, 'Mengubah konten public mitra', 'partners', 4, NULL, NULL, '2026-08-19 01:30:15'),
(688, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 04:13:17'),
(689, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 04:14:07'),
(690, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-20 04:14:12'),
(691, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-20 04:28:32'),
(692, NULL, 'Login ke sistem', 'users', 651, NULL, NULL, '2026-08-20 04:28:42'),
(693, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 06:32:18'),
(694, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 06:32:30'),
(695, 537, 'Login ke sistem', 'users', 537, NULL, NULL, '2026-08-20 06:32:36'),
(696, 537, 'Logout dari sistem', 'users', 537, NULL, NULL, '2026-08-20 06:33:34'),
(697, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 06:33:38'),
(698, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 06:34:02'),
(699, 540, 'Login ke sistem', 'users', 540, NULL, NULL, '2026-08-20 06:34:11'),
(700, 540, 'Logout dari sistem', 'users', 540, NULL, NULL, '2026-08-20 06:35:08'),
(701, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 06:35:14'),
(702, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 06:35:29'),
(703, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 06:35:39'),
(704, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 06:35:51'),
(705, 541, 'Login ke sistem', 'users', 541, NULL, NULL, '2026-08-20 06:35:54'),
(706, 541, 'Logout dari sistem', 'users', 541, NULL, NULL, '2026-08-20 06:37:30'),
(707, 542, 'Login ke sistem', 'users', 542, NULL, NULL, '2026-08-20 06:37:37'),
(708, 542, 'Logout dari sistem', 'users', 542, NULL, NULL, '2026-08-20 06:41:04'),
(709, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 06:41:08'),
(710, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 06:41:16'),
(711, 544, 'Login ke sistem', 'users', 544, NULL, NULL, '2026-08-20 06:41:21'),
(712, 544, 'Logout dari sistem', 'users', 544, NULL, NULL, '2026-08-20 06:43:36'),
(713, 548, 'Login ke sistem', 'users', 548, NULL, NULL, '2026-08-20 06:43:43'),
(714, 548, 'Logout dari sistem', 'users', 548, NULL, NULL, '2026-08-20 06:48:33'),
(715, 549, 'Login ke sistem', 'users', 549, NULL, NULL, '2026-08-20 06:48:42'),
(716, 549, 'Logout dari sistem', 'users', 549, NULL, NULL, '2026-08-20 06:50:52'),
(717, 550, 'Login ke sistem', 'users', 550, NULL, NULL, '2026-08-20 06:50:59'),
(718, 550, 'Logout dari sistem', 'users', 550, NULL, NULL, '2026-08-20 06:53:47'),
(719, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 06:54:10'),
(720, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 06:54:21'),
(721, 551, 'Login ke sistem', 'users', 551, NULL, NULL, '2026-08-20 06:54:26'),
(722, 551, 'Logout dari sistem', 'users', 551, NULL, NULL, '2026-08-20 06:55:34'),
(723, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 06:55:39'),
(724, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 06:55:50'),
(725, 554, 'Login ke sistem', 'users', 554, NULL, NULL, '2026-08-20 06:55:55'),
(726, 554, 'Logout dari sistem', 'users', 554, NULL, NULL, '2026-08-20 06:59:45'),
(727, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:00:08'),
(728, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:00:16'),
(729, 585, 'Login ke sistem', 'users', 585, NULL, NULL, '2026-08-20 07:00:21'),
(730, 585, 'Logout dari sistem', 'users', 585, NULL, NULL, '2026-08-20 07:00:57'),
(731, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:01:00'),
(732, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:01:09'),
(733, 589, 'Login ke sistem', 'users', 589, NULL, NULL, '2026-08-20 07:01:14'),
(734, 589, 'Logout dari sistem', 'users', 589, NULL, NULL, '2026-08-20 07:03:28'),
(735, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:03:32'),
(736, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:03:49'),
(737, 597, 'Login ke sistem', 'users', 597, NULL, NULL, '2026-08-20 07:03:52'),
(738, 597, 'Logout dari sistem', 'users', 597, NULL, NULL, '2026-08-20 07:16:12'),
(739, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:16:16'),
(740, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:16:25'),
(741, 592, 'Login ke sistem', 'users', 592, NULL, NULL, '2026-08-20 07:16:29'),
(742, 592, 'Logout dari sistem', 'users', 592, NULL, NULL, '2026-08-20 07:17:33'),
(743, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:17:36'),
(744, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:17:49'),
(745, 593, 'Login ke sistem', 'users', 593, NULL, NULL, '2026-08-20 07:17:53'),
(746, 593, 'Logout dari sistem', 'users', 593, NULL, NULL, '2026-08-20 07:18:24'),
(747, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:18:32'),
(748, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:18:41'),
(749, 598, 'Login ke sistem', 'users', 598, NULL, NULL, '2026-08-20 07:18:45'),
(750, 598, 'Logout dari sistem', 'users', 598, NULL, NULL, '2026-08-20 07:19:33'),
(751, 603, 'Login ke sistem', 'users', 603, NULL, NULL, '2026-08-20 07:19:38'),
(752, 603, 'Logout dari sistem', 'users', 603, NULL, NULL, '2026-08-20 07:20:39'),
(753, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:20:43'),
(754, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:20:55'),
(755, 604, 'Login ke sistem', 'users', 604, NULL, NULL, '2026-08-20 07:20:58'),
(756, 604, 'Logout dari sistem', 'users', 604, NULL, NULL, '2026-08-20 07:21:24'),
(757, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:21:38'),
(758, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:21:48'),
(759, 605, 'Login ke sistem', 'users', 605, NULL, NULL, '2026-08-20 07:21:52'),
(760, 605, 'Logout dari sistem', 'users', 605, NULL, NULL, '2026-08-20 07:22:40'),
(761, 607, 'Login ke sistem', 'users', 607, NULL, NULL, '2026-08-20 07:22:45'),
(762, 607, 'Logout dari sistem', 'users', 607, NULL, NULL, '2026-08-20 07:23:16'),
(763, 608, 'Login ke sistem', 'users', 608, NULL, NULL, '2026-08-20 07:23:25'),
(764, 608, 'Logout dari sistem', 'users', 608, NULL, NULL, '2026-08-20 07:24:05'),
(765, 609, 'Login ke sistem', 'users', 609, NULL, NULL, '2026-08-20 07:24:13'),
(766, 609, 'Logout dari sistem', 'users', 609, NULL, NULL, '2026-08-20 07:25:22'),
(767, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:25:26'),
(768, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:25:34'),
(769, 613, 'Login ke sistem', 'users', 613, NULL, NULL, '2026-08-20 07:25:38'),
(770, 613, 'Logout dari sistem', 'users', 613, NULL, NULL, '2026-08-20 07:26:37'),
(771, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:26:41'),
(772, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:26:48'),
(773, 613, 'Login ke sistem', 'users', 613, NULL, NULL, '2026-08-20 07:26:52'),
(774, 613, 'Logout dari sistem', 'users', 613, NULL, NULL, '2026-08-20 07:27:17'),
(775, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:27:21'),
(776, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:27:29'),
(777, 615, 'Login ke sistem', 'users', 615, NULL, NULL, '2026-08-20 07:27:33'),
(778, 615, 'Logout dari sistem', 'users', 615, NULL, NULL, '2026-08-20 07:28:11'),
(779, 616, 'Login ke sistem', 'users', 616, NULL, NULL, '2026-08-20 07:28:19'),
(780, 616, 'Logout dari sistem', 'users', 616, NULL, NULL, '2026-08-20 07:28:57'),
(781, 618, 'Login ke sistem', 'users', 618, NULL, NULL, '2026-08-20 07:29:05'),
(782, 618, 'Logout dari sistem', 'users', 618, NULL, NULL, '2026-08-20 07:30:50'),
(783, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:30:54'),
(784, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:31:04'),
(785, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:31:18'),
(786, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:31:30'),
(787, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-20 07:32:27'),
(788, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-20 07:33:18'),
(789, 643, 'Login ke sistem', 'users', 643, NULL, NULL, '2026-08-20 07:33:23'),
(790, 643, 'Logout dari sistem', 'users', 643, NULL, NULL, '2026-08-20 07:34:03'),
(791, 629, 'Login ke sistem', 'users', 629, NULL, NULL, '2026-08-20 07:34:48'),
(792, 629, 'Logout dari sistem', 'users', 629, NULL, NULL, '2026-08-20 07:36:41'),
(793, 635, 'Login ke sistem', 'users', 635, NULL, NULL, '2026-08-20 07:36:43'),
(794, 635, 'Logout dari sistem', 'users', 635, NULL, NULL, '2026-08-20 07:37:21'),
(795, 636, 'Login ke sistem', 'users', 636, NULL, NULL, '2026-08-20 07:37:24'),
(796, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-21 01:18:32'),
(797, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-21 01:20:51'),
(798, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-21 01:20:59'),
(799, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-21 01:21:16'),
(800, NULL, 'Login ke sistem', 'users', 651, NULL, NULL, '2026-08-21 01:21:21'),
(801, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-21 03:56:44'),
(802, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-21 03:56:57'),
(803, NULL, 'Login ke sistem', 'users', 651, NULL, NULL, '2026-08-21 03:57:03'),
(804, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-26 04:47:57'),
(805, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-26 04:48:15'),
(806, NULL, 'Login ke sistem', 'users', 651, NULL, NULL, '2026-08-26 04:48:19'),
(807, NULL, 'Logout dari sistem', 'users', 651, NULL, NULL, '2026-08-26 04:49:26'),
(808, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-26 04:49:32'),
(809, 641, 'Menambah data absensi siswa', 'absensi', 1, NULL, NULL, '2026-08-26 04:50:15'),
(810, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-26 04:50:29'),
(811, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-26 07:56:19'),
(812, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-26 07:56:34'),
(813, NULL, 'Login ke sistem', 'users', 651, NULL, NULL, '2026-08-26 07:56:38'),
(814, NULL, 'Logout dari sistem', 'users', 651, NULL, NULL, '2026-08-26 07:58:07'),
(815, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-26 07:58:22'),
(816, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-27 00:59:42'),
(817, 641, 'Menambah foto galeri mitra', 'galeri_mitra', 1, NULL, NULL, '2026-08-27 01:00:19'),
(818, 641, 'Menghapus foto galeri mitra', 'galeri_mitra', 1, '{\"id\":1,\"partner_id\":4,\"judul\":\"\",\"deskripsi\":\"\",\"file_gambar\":\"galeri_1787792419_3046.jpg\",\"urutan\":1,\"is_aktif\":1,\"created_by\":641,\"created_at\":\"2026-08-27 08:00:19\",\"updated_at\":\"2026-08-27 08:00:19\"}', NULL, '2026-08-27 01:00:51'),
(819, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-27 01:00:52'),
(820, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-27 01:00:56'),
(821, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-31 01:19:36'),
(822, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-08-31 01:29:05'),
(823, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-08-31 01:29:13'),
(824, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-08-31 01:32:17'),
(825, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-08-31 01:32:21'),
(826, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-02 02:00:56'),
(827, 430, 'Menambah akun pengguna: cecep', 'users', 652, NULL, NULL, '2026-09-02 06:20:47'),
(828, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-02 06:20:49'),
(829, 652, 'Login ke sistem', 'users', 652, NULL, NULL, '2026-09-02 06:20:53'),
(830, 652, 'Logout dari sistem', 'users', 652, NULL, NULL, '2026-09-02 06:25:59'),
(831, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-09-14 01:09:10'),
(832, 641, 'Menghapus data absensi siswa', 'absensi', 1, '{\"id\":1,\"student_id\":75,\"partner_id\":4,\"academic_year_id\":2,\"tanggal\":\"2026-08-26\",\"pertemuan_ke\":1,\"status\":\"hadir\",\"keterangan\":\"\",\"created_by\":641,\"created_at\":\"2026-08-26 11:50:15\",\"updated_at\":\"2026-08-26 11:50:15\"}', NULL, '2026-09-14 01:09:24'),
(833, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-09-14 01:31:05'),
(834, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 01:31:09'),
(835, 430, 'Menghapus akun pengguna', 'users', 651, NULL, NULL, '2026-09-14 01:31:18'),
(836, 430, 'Menghapus akun pengguna', 'users', 650, NULL, NULL, '2026-09-14 01:31:23'),
(837, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 01:31:43'),
(838, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-09-14 01:31:49'),
(839, 641, 'Menambah data siswa: Sandhi Ariandhito', 'students', 454, NULL, '{\"partner_id\":4,\"academic_year_id\":null,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-09-14\",\"nama_siswa\":\"Sandhi Ariandhito\",\"kelas\":\"12\",\"program_bimbel\":\"\",\"kategori_peserta\":\"reguler\",\"biaya_pendaftaran\":4000000,\"biaya_program\":0,\"total_biaya\":4000000,\"triwulan\":\"I\",\"is_psam\":\"Tidak\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":648,\"wali_santri_id\":null,\"keterangan\":\"\"}', '2026-09-14 01:33:52'),
(840, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-09-14 01:34:14'),
(841, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 01:34:17'),
(842, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 01:40:00'),
(843, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 01:40:03'),
(844, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 01:40:07'),
(845, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-09-14 01:40:11'),
(846, 641, 'Mengubah data siswa: Sandhi Ariandhito', 'students', 454, '{\"id\":454,\"nis_nisn\":null,\"partner_id\":4,\"academic_year_id\":null,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-09-14\",\"nama_siswa\":\"Sandhi Ariandhito\",\"kelas\":\"12\",\"kategori_peserta\":\"reguler\",\"program_bimbel\":\"\",\"biaya_pendaftaran\":\"4000000.00\",\"triwulan\":\"I\",\"is_psam\":\"Tidak\",\"total_biaya\":\"4000000.00\",\"biaya_program\":\"0.00\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":648,\"wali_santri_id\":null,\"keterangan\":\"\",\"created_at\":\"2026-09-14 08:33:52\",\"updated_at\":\"2026-09-14 08:33:52\"}', '{\"partner_id\":4,\"academic_year_id\":3,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-09-14\",\"nama_siswa\":\"Sandhi Ariandhito\",\"kelas\":\"12\",\"program_bimbel\":\"\",\"kategori_peserta\":\"reguler\",\"biaya_pendaftaran\":4000000,\"biaya_program\":0,\"total_biaya\":4000000,\"triwulan\":\"I\",\"is_psam\":\"Tidak\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":648,\"wali_santri_id\":null,\"keterangan\":\"\"}', '2026-09-14 01:45:26'),
(847, 641, 'Menambah data siswa: Sandhi Ariandhito', 'students', 455, NULL, '{\"partner_id\":4,\"academic_year_id\":3,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-09-14\",\"nama_siswa\":\"Sandhi Ariandhito\",\"kelas\":\"12\",\"program_bimbel\":\"\",\"kategori_peserta\":\"reguler\",\"biaya_pendaftaran\":8000000,\"biaya_program\":0,\"total_biaya\":8000000,\"triwulan\":\"I\",\"is_psam\":\"Tidak\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":640,\"wali_santri_id\":null,\"keterangan\":\"\"}', '2026-09-14 01:52:38'),
(848, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-09-14 01:53:03'),
(849, NULL, 'Login ke sistem', 'users', 653, NULL, NULL, '2026-09-14 01:53:12'),
(850, NULL, 'Logout dari sistem', 'users', 653, NULL, NULL, '2026-09-14 01:53:42'),
(851, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 01:53:55'),
(852, 430, 'Menambah data siswa: Sandhi Ariandhito', 'students', 456, NULL, '{\"partner_id\":8,\"academic_year_id\":3,\"unit_id\":5,\"tanggal_pendaftaran\":\"2026-09-14\",\"nama_siswa\":\"Sandhi Ariandhito\",\"kelas\":\"12\",\"program_bimbel\":\"\",\"kategori_peserta\":\"reguler\",\"biaya_pendaftaran\":4000000,\"biaya_program\":0,\"total_biaya\":4000000,\"triwulan\":\"I\",\"is_psam\":\"Tidak\",\"status_pembayaran\":\"lunas\",\"wali_kelas_id\":640,\"wali_santri_id\":null,\"keterangan\":\"\"}', '2026-09-14 01:54:50'),
(853, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 01:55:12'),
(854, NULL, 'Login ke sistem', 'users', 653, NULL, NULL, '2026-09-14 01:55:19'),
(855, NULL, 'Logout dari sistem', 'users', 653, NULL, NULL, '2026-09-14 01:55:27'),
(856, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 01:55:30'),
(857, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 01:59:19'),
(858, 644, 'Login ke sistem', 'users', 644, NULL, NULL, '2026-09-14 01:59:24'),
(859, 644, 'Logout dari sistem', 'users', 644, NULL, NULL, '2026-09-14 01:59:32'),
(860, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 01:59:35'),
(861, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 01:59:47'),
(862, NULL, 'Login ke sistem', 'users', 631, NULL, NULL, '2026-09-14 01:59:55'),
(863, NULL, 'Logout dari sistem', 'users', 631, NULL, NULL, '2026-09-14 02:00:21'),
(864, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 02:00:25'),
(865, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 02:00:41'),
(866, 644, 'Login ke sistem', 'users', 644, NULL, NULL, '2026-09-14 02:00:51'),
(867, 644, 'Logout dari sistem', 'users', 644, NULL, NULL, '2026-09-14 02:01:12'),
(868, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 02:01:16'),
(869, 430, 'Menghapus akun pengguna', 'users', 631, NULL, NULL, '2026-09-14 02:01:35'),
(870, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 02:03:24'),
(871, NULL, 'Login ke sistem', 'users', 653, NULL, NULL, '2026-09-14 02:03:32'),
(872, NULL, 'Logout dari sistem', 'users', 653, NULL, NULL, '2026-09-14 02:05:03'),
(873, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-09-14 02:05:15'),
(874, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-09-14 02:05:43'),
(875, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 02:06:09'),
(876, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 02:14:23'),
(877, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-09-14 02:14:35'),
(878, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-09-14 02:56:12'),
(879, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 02:56:24'),
(880, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 03:07:01'),
(881, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 03:09:28'),
(882, 430, 'Logout dari sistem', 'users', 430, NULL, NULL, '2026-09-14 03:09:40'),
(883, 641, 'Login ke sistem', 'users', 641, NULL, NULL, '2026-09-14 03:09:46'),
(884, 641, 'Mengubah konten public mitra', 'partners', 4, NULL, NULL, '2026-09-14 03:11:57'),
(885, 641, 'Logout dari sistem', 'users', 641, NULL, NULL, '2026-09-14 03:17:02'),
(886, 430, 'Login ke sistem', 'users', 430, NULL, NULL, '2026-09-14 03:17:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `config_fee_settings`
--

CREATE TABLE `config_fee_settings` (
  `id` int(11) NOT NULL,
  `jenis_fee` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tarif_default` decimal(10,0) NOT NULL DEFAULT 5000,
  `satuan` varchar(20) NOT NULL DEFAULT 'per_pemakaian',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `config_fee_settings`
--

INSERT INTO `config_fee_settings` (`id`, `jenis_fee`, `deskripsi`, `tarif_default`, `satuan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'fee_wali_kelas_sd', 'Fee Wali Kelas SD per pemakaian ruang bimbel', 5000, 'per_pemakaian', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `fee_rules`
--

CREATE TABLE `fee_rules` (
  `id` int(11) NOT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `jenis_rekomendator` enum('wali_kelas','wali_santri') NOT NULL,
  `kategori` varchar(60) DEFAULT NULL,
  `poin_minimum` decimal(6,1) NOT NULL,
  `poin_maksimum` decimal(6,1) DEFAULT NULL,
  `nominal_fee` decimal(14,2) NOT NULL DEFAULT 0.00,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `fee_rules`
--

INSERT INTO `fee_rules` (`id`, `academic_year_id`, `jenis_rekomendator`, `kategori`, `poin_minimum`, `poin_maksimum`, `nominal_fee`, `tanggal_mulai`, `tanggal_selesai`, `status`, `created_at`) VALUES
(1, NULL, 'wali_kelas', 'Kategori 1', 1.0, 3.0, 0.00, '2025-01-01', NULL, 'aktif', '2026-08-06 04:30:46'),
(2, NULL, 'wali_kelas', 'Kategori 2', 3.5, 5.5, 75000.00, '2025-01-01', NULL, 'aktif', '2026-08-06 04:30:46'),
(3, NULL, 'wali_kelas', 'Kategori 3', 6.0, 9.5, 100000.00, '2025-01-01', NULL, 'aktif', '2026-08-06 04:30:46'),
(4, NULL, 'wali_kelas', 'Kategori 4', 10.0, 14.5, 125000.00, '2025-01-01', NULL, 'aktif', '2026-08-06 04:30:46'),
(5, NULL, 'wali_kelas', 'Kategori 5', 15.0, NULL, 150000.00, '2025-01-01', NULL, 'aktif', '2026-08-06 04:30:46'),
(6, NULL, 'wali_santri', 'Kategori 1', 1.0, 3.0, 0.00, '2025-01-01', NULL, 'aktif', '2026-08-06 04:30:46'),
(7, NULL, 'wali_santri', 'Kategori 2', 3.5, 5.5, 50000.00, '2025-01-01', NULL, 'aktif', '2026-08-06 04:30:46'),
(8, NULL, 'wali_santri', 'Kategori 3', 6.0, 9.5, 75000.00, '2025-01-01', NULL, 'aktif', '2026-08-06 04:30:46'),
(9, NULL, 'wali_santri', 'Kategori 4', 10.0, 14.5, 100000.00, '2025-01-01', NULL, 'aktif', '2026-08-06 04:30:46'),
(10, NULL, 'wali_santri', 'Kategori 5', 15.0, NULL, 125000.00, '2025-01-01', NULL, 'aktif', '2026-08-06 04:30:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `galeri_mitra`
--

CREATE TABLE `galeri_mitra` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `judul` varchar(200) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_gambar` varchar(255) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_pemakaian_ruang`
--

CREATE TABLE `jadwal_pemakaian_ruang` (
  `id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `kelas_detail_id` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `minggu_ke` int(11) NOT NULL DEFAULT 1,
  `tanggal_mulai_minggu` date NOT NULL,
  `tanggal_selesai_minggu` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `tarif_fee` decimal(10,0) NOT NULL DEFAULT 5000,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_pencapaian`
--

CREATE TABLE `jenis_pencapaian` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `is_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jenis_pencapaian`
--

INSERT INTO `jenis_pencapaian` (`id`, `nama`, `deskripsi`, `is_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Tryout', 'Ujian masuk perguruan tinggi', 1, '2026-08-18 06:17:53', '2026-08-18 06:17:53'),
(2, 'Ujian', 'Ujian sekolah internal', 1, '2026-08-18 06:17:53', '2026-08-18 06:17:53'),
(3, 'Ranking', 'Peringkat kelas atau sekolah', 1, '2026-08-18 06:17:53', '2026-08-18 06:17:53'),
(4, 'Nilai', 'Nilai rapor atau ujian', 1, '2026-08-18 06:17:53', '2026-08-18 06:17:53'),
(5, 'Prestasi', 'Pencapaian luar akademik', 1, '2026-08-18 06:17:53', '2026-08-18 06:17:53'),
(6, 'Sertifikat', 'Sertifikat penghargaan', 1, '2026-08-18 06:17:53', '2026-08-18 06:17:53'),
(7, 'Lomba', 'Keikut serta lomba', 1, '2026-08-18 06:17:53', '2026-08-18 06:17:53'),
(8, 'Project', 'Project atau tugas', 1, '2026-08-18 06:17:53', '2026-08-18 06:17:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas_detail`
--

CREATE TABLE `kelas_detail` (
  `id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `nama_kelas` varchar(10) NOT NULL,
  `jenjang` varchar(5) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelas_detail`
--

INSERT INTO `kelas_detail` (`id`, `unit_id`, `nama_kelas`, `jenjang`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, '1A', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(2, 3, '1B', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(3, 3, '1C', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(4, 3, '2A', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(5, 3, '2B', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(6, 3, '2C', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(7, 3, '2D', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(8, 3, '3A', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(9, 3, '3B', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(10, 3, '3C', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(11, 3, '3D', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(12, 3, '4A', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(13, 3, '4B', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(14, 3, '4C', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(15, 3, '5A', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(16, 3, '5B', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(17, 3, '5C', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(18, 3, '6A', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(19, 3, '6B', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45'),
(20, 3, '6C', 'SD', 'aktif', '2026-08-10 04:59:45', '2026-08-10 04:59:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `konten_lppa`
--

CREATE TABLE `konten_lppa` (
  `id` int(11) NOT NULL,
  `bagian` enum('tentang','visi','misi','tujuan','kontak') NOT NULL,
  `judul` varchar(200) NOT NULL,
  `konten` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `konten_lppa`
--

INSERT INTO `konten_lppa` (`id`, `bagian`, `judul`, `konten`, `gambar`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'tentang', 'Tentang LPPA', 'Lembaga Pengembangan Potensi Akademik (LPPA) adalah lembaga bimbingan belajar yang berdedikasi untuk meningkatkan potensi akademik siswa di lingkungan Yayasan Al Ma\'soem Bandung.', NULL, NULL, '2026-08-18 06:18:15', '2026-08-18 06:18:15'),
(2, 'visi', 'Visi LPPA', 'Menjadi lembaga bimbingan belajar terpercaya yang menghasilkan generasi berkualitas tinggi dan berakhlak mulia.', NULL, NULL, '2026-08-18 06:18:15', '2026-08-18 06:18:15'),
(3, 'misi', 'Misi LPPA', 'Memberikan pelayanan pendidikan berkualitas dengan metode pembelajaran yang efektif dan menyenangkan.', NULL, NULL, '2026-08-18 06:18:15', '2026-08-18 06:18:15'),
(4, 'tujuan', 'Tujuan LPPA', 'Membantu siswa mencapai potensi maksimal dalam bidang akademik dan membentuk karakter yang positif.', NULL, NULL, '2026-08-18 06:18:15', '2026-08-18 06:18:15'),
(5, 'kontak', 'Kontak LPPA', 'Yayasan Al Ma\'soem Bandung. Email: info@lppa-yab.com | Telepon: (022) 1234567', NULL, NULL, '2026-08-18 06:18:15', '2026-08-18 06:18:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_mitra` varchar(150) NOT NULL,
  `penanggung_jawab` varchar(120) DEFAULT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `persentase_bagi_hasil` decimal(5,2) NOT NULL DEFAULT 15.00,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tax_enabled` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Apakah pajak diaktifkan untuk mitra ini',
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Persentase pajak (0-100)',
  `tax_manual_nominal` decimal(14,2) DEFAULT 0.00,
  `tax_method` enum('none','percentage','manual') NOT NULL DEFAULT 'none',
  `public_programs` text DEFAULT NULL,
  `public_levels` text DEFAULT NULL,
  `public_program_unggulan` text DEFAULT NULL,
  `public_website` varchar(255) DEFAULT NULL,
  `public_whatsapp` varchar(30) DEFAULT NULL,
  `public_address` text DEFAULT NULL,
  `public_logo` varchar(255) DEFAULT NULL,
  `public_cover` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `public_description` text DEFAULT NULL,
  `deskripsi_public` text DEFAULT NULL,
  `keunggulan_public` text DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `video_platform` enum('youtube','drive','other') DEFAULT 'youtube'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `partners`
--

INSERT INTO `partners` (`id`, `user_id`, `nama_mitra`, `penanggung_jawab`, `telepon`, `email`, `alamat`, `tanggal_mulai`, `status`, `persentase_bagi_hasil`, `keterangan`, `created_at`, `tax_enabled`, `tax_percentage`, `tax_manual_nominal`, `tax_method`, `public_programs`, `public_levels`, `public_program_unggulan`, `public_website`, `public_whatsapp`, `public_address`, `public_logo`, `public_cover`, `is_public`, `display_order`, `public_description`, `deskripsi_public`, `keunggulan_public`, `video_url`, `video_platform`) VALUES
(4, 641, 'Neutron Bandung', '', '', '', '', '2026-08-07', 'aktif', 15.00, '', '2026-08-07 06:26:17', 1, 2.60, 1919418.00, 'manual', '', 'SMA', '', '', '0896-7649-5990', '', '1786336103_logo_horse riding courses 0895-3719-79297 For information and reservations (Sandhi) Train with Skill, Grow with Adab (2).png', NULL, 1, 0, 'Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.', 'Bimbingan belajar berpengalaman yang berfokus pada peningkatan prestasi akademik melalui pembelajaran terstruktur, latihan intensif, dan pendampingan siswa.', '-', '', 'youtube'),
(5, 646, 'PRIMAGAMA', '', '', '', '', '2026-08-07', 'aktif', 15.00, '', '2026-08-07 09:04:24', 0, 0.00, 0.00, 'none', '', 'SD,SMP', '', '', '0896-5649-0920', '', '1786335717_logo_Al Ma’soem (13).png', NULL, 1, 0, 'Mata pelajaran SD    : Semua mata pelajaran \r\nMata pelajaran SMP : Matematika, IPA(Fisika,Biologi, Kimia, Bhs Inggris, Bhs Indonesia)', NULL, NULL, NULL, 'youtube'),
(6, 647, 'IEC', '', '', '', '', '2026-08-07', 'aktif', 15.00, '', '2026-08-07 09:04:55', 0, 0.00, 0.00, 'none', '', 'SD,SMP,SMA', '', '', '0821-3031-2324', '', '1786335540_logo_Al Ma’soem (11).png', NULL, 1, 0, 'Lembaga Kursus Bahasa Inggris yang menekankan pada pengembangan kemampuan komunikatif dengan metodologi inovatif yang memberikan solusi terhadap setiap kesulitan siswa', NULL, NULL, NULL, 'youtube'),
(7, 648, 'CENTRAL BRAINPRO', '', '', '', '', '2026-08-07', 'aktif', 15.00, '', '2026-08-07 09:05:21', 0, 0.00, 0.00, 'none', '', 'SD,SMP,SMA', '', '', '0812-8758-6322', '', '1786335931_logo_Al Ma’soem (14).png', NULL, 1, 0, 'Lembaga yang bisa menguatkan daya analisis sehingga motivasi dan kemampuan belajar anak meningkat berlipat. Berbagai mata  pelajaran menjadi mudah dikuasai.', NULL, NULL, NULL, 'youtube'),
(8, 649, 'Padjadjaran Language School', '', '', '', '', '2026-08-07', 'aktif', 15.00, '', '2026-08-07 09:05:52', 0, 0.00, 0.00, 'none', '', 'SD,SMP,SMA', '', '', '0899-7220-7957', '', '1786336014_logo_Al Ma’soem (15).png', NULL, 1, 0, 'Menyelenggarakan kursus berbagai bahasa asing Terpercaya Untuk SD-SMP-SMA-S1', NULL, NULL, NULL, 'youtube');

-- --------------------------------------------------------

--
-- Struktur dari tabel `partner_programs_pdfs`
--

CREATE TABLE `partner_programs_pdfs` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `pdf_title` varchar(255) NOT NULL,
  `pdf_description` text DEFAULT NULL,
  `pdf_file` varchar(255) NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pencapaian_siswa`
--

CREATE TABLE `pencapaian_siswa` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `jenis_pencapaian_id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `nilai` decimal(10,2) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `file_pdf` varchar(255) DEFAULT NULL,
  `file_gambar` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `profit_sharing_rules`
--

CREATE TABLE `profit_sharing_rules` (
  `id` int(11) NOT NULL,
  `persentase` decimal(5,2) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profit_sharing_rules`
--

INSERT INTO `profit_sharing_rules` (`id`, `persentase`, `tanggal_mulai`, `tanggal_selesai`, `status`, `keterangan`, `created_at`) VALUES
(1, 15.00, '2025-01-01', NULL, 'nonaktif', 'Arsip: aturan bagi hasil global lama (tidak dipakai lagi)', '2026-08-06 04:30:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `nama_program` varchar(120) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `programs`
--

INSERT INTO `programs` (`id`, `nama_program`, `status`) VALUES
(1, 'Bimbel Reguler', 'aktif'),
(2, 'Intensif UTBK', 'aktif'),
(3, 'Persiapan Ujian Sekolah', 'aktif'),
(4, 'Tahfidz Plus', 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `recommenders`
--

CREATE TABLE `recommenders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(120) NOT NULL,
  `nomor_induk` varchar(50) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `jenis` enum('wali_kelas','wali_santri') NOT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `recommenders`
--

INSERT INTO `recommenders` (`id`, `user_id`, `nama`, `nomor_induk`, `unit_id`, `jenis`, `telepon`, `email`, `status`, `created_at`) VALUES
(544, 535, 'AAM SYAMSIAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(545, 536, 'ASEP RAHMAN', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(546, 537, 'AGUS YUDI ALI FIRDAUS', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(547, 538, 'ALIFA TURROSYIDAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(548, 539, 'AYEP MULYA BARKAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(549, 540, 'DADANG DIMYATI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(550, 541, 'DINI PURNAMA NURUL AJI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(551, 542, 'ELA N H', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(552, 543, 'ELIS SUMYATI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(553, 544, 'F. NIZAR', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(554, 545, 'FERRY FERDIANSYAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(555, 546, 'FITRIADI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(556, 547, 'HAMDANI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(557, 548, 'HILMAN', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(558, 549, 'HUDA AHMAD SYARIF', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(559, 550, 'IDA HAMIDAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(560, 551, 'IMA SRI YUNIANI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(561, 552, 'ITANG RUSTANDI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(562, 553, 'LUKMANUL HAKIM', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(563, 554, 'M. IQBAL SYEKHABUDIN', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(564, 555, 'MIA NUR ASIAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(565, 556, 'ROSLIANAWATI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(566, 557, 'SITI INDIRA MARYAM', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(567, 558, 'SRI RODIAH', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(568, 559, 'SANTI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(569, 560, 'RYSNA', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(570, 561, 'NUNUNG KARTINI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(571, 562, 'YAYAH HERYATI', NULL, NULL, 'wali_santri', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(572, 563, 'TATAN HERAWATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(573, 564, 'TINTIN AGUSTIANA', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(574, 565, 'SUSAN MARIAM', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(575, 566, 'RAISA INDAH FARDINI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(576, 567, 'RYAN FEBRIAN', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(577, 568, 'YULLI MELANIAWATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(578, 569, 'SUSILOWATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(579, 570, 'HENI HARYANI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(580, 571, 'AI MULYANI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(581, 572, 'TENI WARTINI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(582, 573, 'RD.ELIH JAMILAH', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(583, 574, 'ABILLA PRIMA HASTUTI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(584, 575, 'YATI KURHAYATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(585, 576, 'AYI KANA', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(586, 577, 'LILIS LISNAWATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(587, 578, 'SRI WULANDARI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(588, 579, 'WIWI WIHARYATI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(589, 580, 'INDAYANI', NULL, 3, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(590, 581, 'RIKI AHMAD ZAELANI, S.Si.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(591, 582, 'VANNY GUSTIKASARI, S.Pd., M.M.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(592, 583, 'Hj. KUSMAYATI, S.Pd.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(593, 584, 'Dra. IPAH SARIPAH', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(594, 585, 'ELIS MASITOH, S.P.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(595, 586, 'WINA NURNINGSARI, M.Pd.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(596, 587, 'Drs. RONY HERMANSYAH', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:14'),
(597, 588, 'HANNY HADIYANTI, S.Pd.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(598, 589, 'NELA NAYLA, S. Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(599, 590, 'SUCI LESTARI, M. Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(600, 591, 'CHAERUL WILDAN, S.Pd.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(601, 592, 'KURNIAWATI, S.Ag.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(602, 593, 'RITA PUSPITASARI, S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(603, 594, 'TINTIN SURTINI, Dra', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(604, 595, 'EUIS NURYANTI DEWI, S.TP', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(605, 596, 'MOMON , S.S', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(606, 597, 'Drs. MUHAMMAD RIDWAN, M.M.', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(607, 598, 'INDRIANI APSARI, S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(608, 599, 'SATIVA , SE', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(609, 600, 'HASYIDAH BR MANURUNG ,S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(610, 601, 'SUMARTONO, S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(611, 602, 'IIS NURHAYATI,S.Si', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(612, 603, 'SOLEH, S.Ag', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(613, 604, 'IMAS SUNARTI S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(614, 605, 'SITI SUWARNI, S.Pd', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(615, 606, 'YUKARSA', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(616, 607, 'ANI SOPYANI', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(617, 608, 'DADANG SUPRIYANTO', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(618, 609, 'AHMAD SODIKIN', NULL, 4, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(619, 610, 'Lilis Nurlaela Hasanah, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(620, 611, 'Kosim Saputra, Drs.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(621, 612, 'M. Ady Saefurohman S. Y., S.H.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(622, 613, 'Raihani Ariefah, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(623, 614, 'Risni Tristania, S.Si.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(624, 615, 'Rina Fauziyyah Hasna, S.S.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(625, 616, 'Anggia Perbawa, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(626, 617, 'Hj. Eros Rosmawati, Dra., M.M.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(627, 618, 'Dadang Supriatna, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(628, 619, 'Eva Nur Indah Hayati, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(629, 620, 'Aida Mariyah, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(630, 621, 'Hj. Euis Nuraeni, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(631, 622, 'Didik Permana, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(632, 623, 'Fitria Dwi Farina, M.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(633, 624, 'Yunita Puji Astutik, S.Kom.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(634, 625, 'Rita Permata, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(635, 626, 'Mochtar Arief Kamis, Drs.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(636, 627, 'Yani Taryani, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(637, 628, 'Budi Jayadi, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(638, 629, 'Tati Rohayati, S.S.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(639, 630, 'Hanny Fitriani Yusuf, S.Si.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(640, NULL, 'H. Udin Nashruddin, S.Si., M.M.', NULL, 5, 'wali_kelas', NULL, NULL, 'nonaktif', '2026-08-07 06:19:15'),
(641, 632, 'Abas Basari, S.Si. M.M.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(642, 633, 'Dadan Rohaendi, S.Pd.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(643, 634, 'Wiwit Widorini, S.Si', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(644, 635, 'Endang Jamaludin H., S.Ag.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(645, 636, 'Noval Anwari Faiz, S.S.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(646, 637, 'Endang Rahmat, Drs.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(647, 638, 'Wawan Gunawan, S.Ag.', NULL, 5, 'wali_kelas', NULL, NULL, 'aktif', '2026-08-07 06:19:15'),
(648, 642, 'Ato Taopik, S.Ag', '', 5, 'wali_kelas', '', 'atotaopik@gmail.com', 'aktif', '2026-08-07 08:13:04'),
(649, 643, 'Riad Taufik Lazwardi, M.Si', '', 5, 'wali_kelas', '', 'Riadtaufik@gmail.com', 'aktif', '2026-08-07 08:17:02'),
(650, 644, 'H. Udin Nashruddin, S. Si, M.M', '', 5, 'wali_kelas', '', 'udinnashruddin@gmail.com', 'aktif', '2026-08-07 08:17:47'),
(651, 645, 'Dinar Nurul Fauziah, S.E. M.E.', '', 5, 'wali_kelas', '', 'dinarnurul@gmail.com', 'aktif', '2026-08-07 08:18:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `setoran_bagi_hasil`
--

CREATE TABLE `setoran_bagi_hasil` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `tanggal_setoran` date NOT NULL,
  `nominal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `amount_after_tax` decimal(14,2) DEFAULT 0.00,
  `amount_before_tax` decimal(14,2) DEFAULT 0.00,
  `tax_amount` decimal(14,2) DEFAULT 0.00,
  `tax_manual_nominal` decimal(14,2) DEFAULT 0.00,
  `tax_percentage` decimal(5,2) DEFAULT 0.00,
  `tax_method` enum('none','percentage','manual') DEFAULT 'none',
  `bukti_file` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `sumber` enum('mitra','admin') NOT NULL DEFAULT 'mitra',
  `created_by` int(11) DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `catatan_review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `setoran_bagi_hasil`
--

INSERT INTO `setoran_bagi_hasil` (`id`, `partner_id`, `tanggal_setoran`, `nominal`, `amount_after_tax`, `amount_before_tax`, `tax_amount`, `tax_manual_nominal`, `tax_percentage`, `tax_method`, `bukti_file`, `keterangan`, `status`, `sumber`, `created_by`, `reviewed_by`, `reviewed_at`, `catatan_review`, `created_at`, `updated_at`) VALUES
(1, 4, '2026-08-10', 76776750.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'none', NULL, '', 'disetujui', 'admin', 430, 430, '2026-08-10 02:43:36', NULL, '2026-08-10 02:43:36', '2026-08-10 02:43:36'),
(2, 7, '2026-08-10', 8100000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'none', NULL, '', 'disetujui', 'admin', 430, 430, '2026-08-10 02:43:56', NULL, '2026-08-10 02:43:56', '2026-08-10 02:43:56'),
(3, 6, '2026-08-10', 55290000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'none', NULL, '', 'disetujui', 'admin', 430, 430, '2026-08-10 02:44:20', NULL, '2026-08-10 02:44:20', '2026-08-10 02:44:20'),
(4, 5, '2026-08-10', 43920000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'none', NULL, '', 'disetujui', 'admin', 430, 430, '2026-08-10 02:44:43', NULL, '2026-08-10 02:44:43', '2026-08-10 02:44:43'),
(5, 8, '2026-08-10', 7746000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'none', NULL, '', 'disetujui', 'admin', 430, 430, '2026-08-10 02:45:16', NULL, '2026-08-10 02:45:16', '2026-08-10 02:45:16'),
(6, 8, '2026-08-11', 7065000.00, 7065000.00, 7065000.00, 0.00, 0.00, 0.00, 'none', NULL, '', 'disetujui', 'admin', 430, 430, '2026-08-11 03:32:09', NULL, '2026-08-11 03:32:09', '2026-08-11 03:32:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa_orang_tua`
--

CREATE TABLE `siswa_orang_tua` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hubungan` varchar(50) NOT NULL DEFAULT 'Orang Tua',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `nis_nisn` varchar(50) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `tanggal_pendaftaran` date NOT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `kategori_peserta` enum('reguler','khusus') DEFAULT 'reguler',
  `program_bimbel` varchar(150) DEFAULT NULL,
  `biaya_pendaftaran` decimal(14,2) NOT NULL DEFAULT 0.00,
  `triwulan` enum('I','II','III','IV') DEFAULT NULL,
  `is_psam` enum('Ya','Tidak') NOT NULL DEFAULT 'Tidak',
  `total_biaya` decimal(14,2) NOT NULL DEFAULT 0.00,
  `biaya_program` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status_pembayaran` enum('angsuran','lunas') NOT NULL DEFAULT 'angsuran',
  `wali_kelas_id` int(11) DEFAULT NULL,
  `wali_santri_id` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `students`
--

INSERT INTO `students` (`id`, `nis_nisn`, `partner_id`, `academic_year_id`, `unit_id`, `tanggal_pendaftaran`, `nama_siswa`, `kelas`, `kategori_peserta`, `program_bimbel`, `biaya_pendaftaran`, `triwulan`, `is_psam`, `total_biaya`, `biaya_program`, `status_pembayaran`, `wali_kelas_id`, `wali_santri_id`, `keterangan`, `created_at`, `updated_at`) VALUES
(10, NULL, 4, 2, 5, '2026-08-07', 'SEILA SANAH ASSABILIYAH', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(11, NULL, 4, 2, 5, '2026-08-07', 'MUHAMMAD ALFARISY HASBULLAH', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 648, 549, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(12, NULL, 4, 2, 5, '2026-08-07', 'AHMAD AZIZUL ZAHID', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 634, 563, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(13, NULL, 4, 2, 5, '2026-08-07', 'MUHAMMAD ZAHRAN AGASTYA', '2', 'reguler', '', 0.00, 'I', 'Ya', 3950000.00, 3950000.00, 'lunas', 649, 554, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(14, NULL, 4, 2, 5, '2026-08-07', 'MUHAMMAD ALDI SUKMA B', '3', 'reguler', '', 0.00, 'I', 'Tidak', 4400000.00, 4400000.00, 'lunas', 634, NULL, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(15, NULL, 4, 2, 5, '2026-08-07', 'DIVYA NUR FADHILA AHLADITHA', '3', 'reguler', '', 0.00, 'I', 'Ya', 6450000.00, 6450000.00, 'lunas', 629, 559, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(16, NULL, 4, 2, 5, '2026-08-07', 'DZAFIRA IMRA\'AH GUNAWAN', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(17, NULL, 4, 2, 5, '2026-08-07', 'ADHIE JATI KESUMA', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 632, 563, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(18, NULL, 4, 2, 5, '2026-08-07', 'ZASKIA WIDYA MAHARANI YUNUS', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 629, 559, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(19, NULL, 4, 2, 5, '2026-08-07', 'IJLAL NAJIHUL AMAL', '3', 'reguler', '', 300000.00, 'I', 'Ya', 5200000.00, 4900000.00, 'lunas', 636, 563, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(20, NULL, 4, 2, 5, '2026-08-07', 'SHAFIRA IZZATI', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 631, 559, NULL, '2026-08-07 08:32:44', '2026-08-07 08:32:44'),
(21, NULL, 4, 2, 5, '2026-08-07', 'SALSABILA BILQIS ADELIN', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 639, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(22, NULL, 4, 2, 5, '2026-08-07', 'NURUL NAZWA MAYRITZA', '3', 'reguler', '', 0.00, 'I', 'Ya', 4450000.00, 4450000.00, 'lunas', 632, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(23, NULL, 4, 2, 5, '2026-08-07', 'KHARIDAH KHAIRIYAH KARAMATULLAH', '2', 'reguler', '', 300000.00, 'I', 'Tidak', 6200000.00, 5900000.00, 'lunas', 643, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(24, NULL, 4, 2, 5, '2026-08-07', 'ANDINI APRILIA KARTINI', '2', 'reguler', '', 300000.00, 'I', 'Ya', 6700000.00, 6400000.00, 'lunas', 650, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(25, NULL, 4, 2, 5, '2026-08-07', 'HANNA NOER PRATIWI', '3', 'reguler', '', 0.00, 'I', 'Ya', 4700000.00, 4700000.00, 'lunas', 631, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(26, NULL, 4, 2, 5, '2026-08-07', 'SITI NURLAILA LATIFAH', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 631, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(27, NULL, 4, 2, 5, '2026-08-07', 'KEYLA TANZA KHAIRUNNISA', '3', 'reguler', '', 300000.00, 'I', 'Ya', 6700000.00, 6400000.00, 'lunas', 631, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(28, NULL, 4, 2, 5, '2026-08-07', 'HAYFA ADINDA SHOFA', '2', 'reguler', '', 0.00, 'I', 'Ya', 3950000.00, 3950000.00, 'lunas', 650, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(29, NULL, 4, 2, 5, '2026-08-07', 'GHANY AHMAD AR-RIFQI', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 636, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(30, NULL, 4, 2, 5, '2026-08-07', 'ZAHRA APRILIA', '3', 'reguler', '', 0.00, 'I', 'Ya', 4450000.00, 4450000.00, 'lunas', 634, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(31, NULL, 4, 2, 5, '2026-08-07', 'MUHAMMAD SHIDQI', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 631, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(32, NULL, 4, 2, 5, '2026-08-07', 'NAJMIE BANI A', '2', 'reguler', '', 0.00, 'I', 'Tidak', 3950000.00, 3950000.00, 'lunas', 643, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(33, NULL, 4, 2, 5, '2026-08-07', 'FARHAH AFIFAH', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 631, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(34, NULL, 4, 2, 5, '2026-08-07', 'FARELL MAHENDRA', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 636, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(35, NULL, 4, 2, 5, '2026-08-07', 'NAYLA SOFIA', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(36, NULL, 4, 2, 5, '2026-08-07', 'FITRI AFIFAH', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(37, NULL, 4, 2, 5, '2026-08-07', 'SUCI FEBRIAN W', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(38, NULL, 4, 2, 5, '2026-08-07', 'SAFIRA KHOIRUNNISA', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(39, NULL, 4, 2, 5, '2026-08-07', 'MUHAMMAD NUR HAYKAL', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 629, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(40, NULL, 4, 2, 5, '2026-08-07', 'DIAH ZAHRATUSSITA', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(41, NULL, 4, 2, 5, '2026-08-07', 'NAMIERA ABDIRIBIHA AZIZ', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 650, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(42, NULL, 4, 2, 5, '2026-08-07', 'GARINI RANTI', '2', 'reguler', '', 0.00, 'I', 'Ya', 3950000.00, 3950000.00, 'lunas', 623, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(43, NULL, 4, 2, 5, '2026-08-07', 'M. ZILLAN AL-AKBARI', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 629, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(44, NULL, 4, 2, 5, '2026-08-07', 'FICO FATHONI', '2', 'reguler', '', 0.00, 'I', 'Tidak', 3950000.00, 3950000.00, 'lunas', 649, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(45, NULL, 4, 2, 5, '2026-08-07', 'CEYZAR PUTRA D', '2', 'reguler', '', 0.00, 'I', 'Ya', 3950000.00, 3950000.00, 'lunas', 649, 554, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(46, NULL, 4, 2, 5, '2026-08-07', 'EGHLIYA ELHABAYIEB', '2', 'reguler', '', 0.00, 'I', 'Ya', 3950000.00, 3950000.00, 'lunas', 623, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(47, NULL, 4, 2, 5, '2026-08-07', 'FAIZ ZIYAD FAUZAN', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 649, 549, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(48, NULL, 4, 2, 5, '2026-08-07', 'JIHAN AQILA G', '2', 'reguler', '', 300000.00, 'I', 'Tidak', 4200000.00, 3900000.00, 'lunas', 643, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(49, NULL, 4, 2, 5, '2026-08-07', 'ASSYIFA PUTRI A', '3', 'reguler', '', 0.00, 'I', 'Ya', 4450000.00, 4450000.00, 'lunas', 633, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(50, NULL, 4, 2, 5, '2026-08-07', 'BISMA MAHARDIKA', '2', 'reguler', '', 0.00, 'I', 'Ya', 3950000.00, 3950000.00, 'lunas', 644, 549, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(51, NULL, 4, 2, 5, '2026-08-07', 'FAIRUZA RAIHANA T', '3', 'reguler', '', 300000.00, 'I', 'Ya', 6500000.00, 6200000.00, 'lunas', 637, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(52, NULL, 4, 2, 5, '2026-08-07', 'KARIMA TANINA S', '3', 'reguler', '', 300000.00, 'I', 'Ya', 6700000.00, 6400000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(53, NULL, 4, 2, 5, '2026-08-07', 'KANAYA TANINA S', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 636, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(54, NULL, 4, 2, 5, '2026-08-07', 'M. IQBAL DARMAWAN', '3', 'reguler', '', 0.00, 'I', 'Ya', 4950000.00, 4950000.00, 'lunas', 632, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(55, NULL, 4, 2, 5, '2026-08-07', 'SYABANI FAJARINA', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 650, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(56, NULL, 4, 2, 5, '2026-08-07', 'NAZWA CHARERINA', '2', 'reguler', '', 300000.00, 'I', 'Tidak', 4700000.00, 4400000.00, 'lunas', 623, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(57, NULL, 4, 2, 5, '2026-08-07', 'ABDULLAH GEPARDI', '3', 'reguler', '', 300000.00, 'I', 'Tidak', 4700000.00, 4400000.00, 'lunas', 631, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(58, NULL, 4, 2, 5, '2026-08-07', 'SABRINA AZZAHRAA', '3', 'reguler', '', 0.00, 'I', 'Ya', 4450000.00, 4450000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(59, NULL, 4, 2, 5, '2026-08-07', 'CALISTA KHAIRINNISWAH', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 636, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(60, NULL, 4, 2, 5, '2026-08-07', 'AISYAH AMIRA D', '2', 'reguler', '', 300000.00, 'I', 'Tidak', 4200000.00, 3900000.00, 'lunas', 623, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(61, NULL, 4, 2, 5, '2026-08-07', 'AHSAN MUHAMMAD FADHLAN', '3', 'reguler', '', 0.00, 'I', 'Ya', 4950000.00, 4950000.00, 'lunas', 651, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(62, NULL, 4, 2, 5, '2026-08-07', 'KAKA RAMDAN N', '2', 'reguler', '', 300000.00, 'I', 'Tidak', 4200000.00, 3900000.00, 'lunas', 643, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(63, NULL, 4, 2, 5, '2026-08-07', 'SYAHLA NADZIFA Z.', '2', 'reguler', '', 0.00, 'I', 'Ya', 3900000.00, 3900000.00, 'lunas', 643, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(64, NULL, 4, 2, 5, '2026-08-07', 'DAVINA PEBRIANAN', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 629, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(65, NULL, 4, 2, 5, '2026-08-07', 'KIRANA HARSYA D', '2', 'reguler', '', 0.00, 'I', 'Tidak', 3950000.00, 3950000.00, 'lunas', 650, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(66, NULL, 4, 2, 5, '2026-08-07', 'IQMA FAUZIAH', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 637, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(67, NULL, 4, 2, 5, '2026-08-07', 'REYSHA KIRANI V', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 651, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(68, NULL, 4, 2, 5, '2026-08-07', 'SYIFA NURUL GINA', '3', 'reguler', '', 0.00, 'I', 'Ya', 4950000.00, 4950000.00, 'lunas', 629, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(69, NULL, 4, 2, 5, '2026-08-07', 'PRIYATAMA RAUFAN HIBRIZI', '2', 'reguler', '', 0.00, 'I', 'Tidak', 3950000.00, 3950000.00, 'lunas', 649, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(70, NULL, 4, 2, 5, '2026-08-07', 'M. DZACKY AZ ZIDANE R', '2', 'reguler', '', 300000.00, 'I', 'Tidak', 4200000.00, 3900000.00, 'lunas', 643, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(71, NULL, 4, 2, 5, '2026-08-07', 'SASKIA RAMADHANI', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 636, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(72, NULL, 4, 2, 5, '2026-08-07', 'FAWAIDZ RIFAI E', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 636, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(73, NULL, 4, 2, 5, '2026-08-07', 'DEYNISA TISA S', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 639, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(74, NULL, 4, 2, 5, '2026-08-07', 'DINARA SAFINA', '1', 'reguler', '', 300000.00, 'I', 'Tidak', 4200000.00, 3900000.00, 'lunas', 628, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(75, NULL, 4, 2, 5, '2026-08-07', 'BILQIS ALTAMIRA DEVI', '1', 'reguler', '', 300000.00, 'I', 'Tidak', 4200000.00, 3900000.00, 'lunas', 619, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(76, NULL, 4, 2, 5, '2026-08-07', 'M. YUSUF AL FAREL', '3', 'reguler', '', 300000.00, 'I', 'Ya', 7200000.00, 6900000.00, 'lunas', 634, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(77, NULL, 4, 2, 5, '2026-08-07', 'M. ARDHI GHONIYUL', '3', 'reguler', '', 0.00, 'I', 'Ya', 4450000.00, 4450000.00, 'lunas', 632, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(78, NULL, 4, 2, 5, '2026-08-07', 'SEKAR WANGI NUR A', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 648, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(79, NULL, 4, 2, 5, '2026-08-07', 'SABRINA AZZAHRA', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 623, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(80, NULL, 4, 2, 5, '2026-08-07', 'KEYZA PUTRI Y', '2', 'reguler', '', 300000.00, 'I', 'Tidak', 6700000.00, 6400000.00, 'lunas', 623, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(81, NULL, 4, 2, 5, '2026-08-07', 'DAYYANA MUJTAHIDA', '2', 'reguler', '', 300000.00, 'I', 'Ya', 6700000.00, 6400000.00, 'lunas', 639, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(82, NULL, 4, 2, 5, '2026-08-07', 'NAYLA PARISHA RAYYA', '2', 'reguler', '', 300000.00, 'I', 'Ya', 6700000.00, 6400000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(83, NULL, 4, 2, 5, '2026-08-07', 'KAYLA RIZKI Z.', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 639, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(84, NULL, 4, 2, 5, '2026-08-07', 'AURELIA ZAHRA I. S.', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 648, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(85, NULL, 4, 2, 5, '2026-08-07', 'EDWARD MAULANA I.', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 651, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(86, NULL, 4, 2, 5, '2026-08-07', 'RAIHAN KAMILA R', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 651, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(87, NULL, 4, 2, 5, '2026-08-07', 'NATHANIA RAHMA', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 646, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(88, NULL, 4, 2, 5, '2026-08-07', 'KEYSYA ANDREA P.', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 628, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(89, NULL, 4, 2, 5, '2026-08-07', 'KAYLA MUTIARA F', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 646, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(90, NULL, 4, 2, 5, '2026-08-07', 'FARRAS ATHA R', '3', 'reguler', '', 0.00, 'I', 'Ya', 4450000.00, 4450000.00, 'lunas', 637, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(91, NULL, 4, 2, 5, '2026-08-07', 'SOPIYATUL K. H.', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 648, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(92, NULL, 4, 2, 5, '2026-08-07', 'RASYA SABIAN N', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 643, 554, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(93, NULL, 4, 2, 5, '2026-08-07', 'OLINE PARAHITA', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 645, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(94, NULL, 4, 2, 5, '2026-08-07', 'REVA ZAHROTU S', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 623, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(95, NULL, 4, 2, 5, '2026-08-07', 'ASFIA NAZLA LATIFA', '1', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 627, 560, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(96, NULL, 4, 2, 5, '2026-08-07', 'LULU MUTHIA A', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 646, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(97, NULL, 4, 2, 5, '2026-08-07', 'KALILA', '2', 'reguler', '', 0.00, 'I', 'Ya', 3950000.00, 3950000.00, 'lunas', 623, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(98, NULL, 4, 2, 5, '2026-08-07', 'DAIFA FADILA M.', '1', 'reguler', '', 300000.00, 'I', 'Tidak', 4200000.00, 3900000.00, 'lunas', 619, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(99, NULL, 4, 2, 5, '2026-08-07', 'JANNATA R.Z.', '1', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 619, 553, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(100, NULL, 4, 2, 5, '2026-08-07', 'M. ARKAN ZAKARIA', '3', 'reguler', '', 0.00, 'I', 'Ya', 4950000.00, 4950000.00, 'lunas', 632, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(101, NULL, 4, 2, 5, '2026-08-07', 'RAHMADINA SYIFA A', '2', 'reguler', '', 300000.00, 'I', 'Ya', 6700000.00, 6400000.00, 'lunas', 639, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(102, NULL, 4, 2, 5, '2026-08-07', 'LIKAYLA MUTIARA', '3', 'reguler', '', 300000.00, 'I', 'Ya', 5200000.00, 4900000.00, 'lunas', 630, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(103, NULL, 4, 2, 5, '2026-08-07', 'KAYESHA DZALFA', '1', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 624, 560, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(104, NULL, 4, 2, 5, '2026-08-07', 'M. AKMAL BAIHAKI', '2', 'reguler', '', 0.00, 'I', 'Tidak', 550000.00, 550000.00, 'lunas', 643, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(105, NULL, 4, 2, 5, '2026-08-07', 'DAFID KHOIRUL F', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 636, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(106, NULL, 4, 2, 5, '2026-08-07', 'HASBIAN WAHYU N', '1', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 628, 553, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(107, NULL, 4, 2, 5, '2026-08-07', 'YANUAR ARIEF NUGRAHA', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4700000.00, 4400000.00, 'lunas', 637, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(108, NULL, 4, 2, 5, '2026-08-07', 'RAFAY ZIYA QURATUAIN', '3', 'reguler', '', 300000.00, 'I', 'Ya', 6700000.00, 6400000.00, 'lunas', 636, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(109, NULL, 4, 2, 5, '2026-08-07', 'MAHIRA HASNA MAFAID', '1', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 619, 550, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(110, NULL, 4, 2, 5, '2026-08-07', 'ARYA MUHAMMAD FARREL', '2', 'reguler', '', 300000.00, 'I', 'Tidak', 4200000.00, 3900000.00, 'lunas', 643, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(111, NULL, 4, 2, 5, '2026-08-07', 'SATRIA WIRAYUDHA', '1', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 620, 553, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(112, NULL, 4, 2, 5, '2026-08-07', 'RAIHAN PRIA A', '3', 'reguler', '', 0.00, 'I', 'Ya', 4700000.00, 4700000.00, 'lunas', 630, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(113, NULL, 4, 2, 5, '2026-08-07', 'FIRLIAN ATHAYA R', '2', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 645, 565, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(114, NULL, 4, 2, 5, '2026-08-07', 'SEKAR AULIA PUTRI', '1', 'reguler', '', 300000.00, 'I', 'Tidak', 4200000.00, 3900000.00, 'lunas', 628, NULL, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(115, NULL, 4, 2, 5, '2026-08-07', 'IZARD WISANGGENI', '1', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 621, 553, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(116, NULL, 4, 2, 5, '2026-08-07', 'ERIKA NUR F', '1', 'reguler', '', 300000.00, 'I', 'Ya', 4200000.00, 3900000.00, 'lunas', 627, 560, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(117, NULL, 4, 2, 5, '2026-08-07', 'M. FAKHRI ABINAYA HARSONO', '3', 'reguler', '', 300000.00, 'I', 'Ya', 4950000.00, 4650000.00, 'lunas', 632, 563, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(118, NULL, 4, 2, 5, '2026-08-07', 'AYESHA SALSABILA', '3', 'reguler', '', 300000.00, 'I', 'Ya', 6995000.00, 6695000.00, 'lunas', 632, 559, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(119, NULL, 4, 2, 5, '2026-08-07', 'JIHAN FATIN RAMADHANI', '1', 'reguler', '', 300000.00, 'I', 'Ya', 3950000.00, 3650000.00, 'lunas', 619, 560, NULL, '2026-08-07 08:32:45', '2026-08-07 08:32:45'),
(120, NULL, 5, 2, 4, '2026-08-10', 'NAYLA ACHMAD', '8', 'reguler', '', 0.00, 'I', 'Ya', 2950000.00, 2950000.00, 'lunas', 610, 544, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(121, NULL, 5, 2, 4, '2026-08-10', 'BEVAN SAKTI E', '8', 'reguler', '', 0.00, 'I', 'Tidak', 2950000.00, 2950000.00, 'lunas', 610, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(122, NULL, 5, 2, 4, '2026-08-10', 'KEISHA SAVIRA', '8', 'reguler', '', 0.00, 'I', 'Ya', 2950000.00, 2950000.00, 'lunas', 614, 566, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(123, NULL, 5, 2, 4, '2026-08-10', 'M HADIANSYAH', '8', 'reguler', '', 0.00, 'I', 'Ya', 2950000.00, 2950000.00, 'lunas', 611, 555, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(124, NULL, 5, 2, 4, '2026-08-10', 'STEVE WILLIAM', '8', 'reguler', '', 0.00, 'I', 'Tidak', 2750000.00, 2750000.00, 'lunas', 614, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(125, NULL, 5, 2, 4, '2026-08-10', 'KENARY ADELINE P', '8', 'reguler', '', 0.00, 'I', 'Tidak', 2950000.00, 2950000.00, 'lunas', 608, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(126, NULL, 5, 2, 4, '2026-08-10', 'NAFISHA NAIRA', '9', 'reguler', '', 0.00, 'I', 'Ya', 2950000.00, 2950000.00, 'lunas', 618, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(127, NULL, 5, 2, 4, '2026-08-10', 'GUNTUR', '8', 'reguler', '', 0.00, 'I', 'Tidak', 2950000.00, 2950000.00, 'lunas', 608, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(128, NULL, 5, 2, 4, '2026-08-10', 'KAYLA AURELIA', '9', 'reguler', '', 0.00, 'I', 'Tidak', 2750000.00, 2750000.00, 'lunas', 595, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(129, NULL, 5, 2, 4, '2026-08-10', 'QAIREEN FARRAS', '8', 'reguler', '', 0.00, 'I', 'Tidak', 2750000.00, 2750000.00, 'lunas', 608, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(130, NULL, 5, 2, 4, '2026-08-10', 'ALIYA ALANA', '8', 'reguler', '', 0.00, 'I', 'Tidak', 2950000.00, 2950000.00, 'lunas', 612, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(131, NULL, 5, 2, 4, '2026-08-10', 'SHADAM ALBARRA', '9', 'reguler', '', 0.00, 'I', 'Ya', 2750000.00, 2750000.00, 'lunas', 595, 558, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(132, NULL, 5, 2, 4, '2026-08-10', 'RIZKA WARDAH', '9', 'reguler', '', 0.00, 'I', 'Ya', 2950000.00, 2950000.00, 'lunas', 595, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(133, NULL, 5, 2, 4, '2026-08-10', 'M RASYA IVAN A', '9', 'reguler', '', 0.00, 'I', 'Tidak', 2950000.00, 2950000.00, 'lunas', 595, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(134, NULL, 5, 2, 4, '2026-08-10', 'M IMAM MA', '8', 'reguler', '', 0.00, 'I', 'Ya', 2950000.00, 2950000.00, 'lunas', 613, 555, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(135, NULL, 5, 2, 4, '2026-08-10', 'HIGEA M', '8', 'reguler', '', 0.00, 'I', 'Ya', 3150000.00, 3150000.00, 'lunas', 608, 566, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(136, NULL, 5, 2, 4, '2026-08-10', 'M AKBAR F', '8', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 615, 555, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(137, NULL, 5, 2, 4, '2026-08-10', 'KHANSA AQILLAH', '8', 'reguler', '', 0.00, 'I', 'Ya', 2950000.00, 2950000.00, 'lunas', 613, 544, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(138, NULL, 5, 2, 4, '2026-08-10', 'REYNALDI TIRTA K', '8', 'reguler', '', 250000.00, 'I', 'Tidak', 3700000.00, 3450000.00, 'lunas', 612, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(139, NULL, 5, 2, 4, '2026-08-10', 'ANIQ ZAYN N', '9', 'reguler', '', 250000.00, 'I', 'Tidak', 4000000.00, 3750000.00, 'lunas', 618, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(140, NULL, 5, 2, 4, '2026-08-10', 'M ZAHRAN A', '8', 'reguler', '', 0.00, 'I', 'Tidak', 3250000.00, 3250000.00, 'lunas', 614, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(141, NULL, 5, 2, 4, '2026-08-10', 'M RUSLI NAJID', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3450000.00, 3450000.00, 'lunas', 604, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(142, NULL, 5, 2, 4, '2026-08-10', 'BILQIS SHIDQIA', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 605, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(143, NULL, 5, 2, 4, '2026-08-10', 'KEISYA PUTRI AZZAHRA', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 600, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(144, NULL, 5, 2, 4, '2026-08-10', 'KAYISA SALSABILA', '9', 'reguler', '', 0.00, 'I', 'Tidak', 3650000.00, 3650000.00, 'lunas', 591, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(145, NULL, 5, 2, 4, '2026-08-10', 'KINETA AIDA R', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 605, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(146, NULL, 5, 2, 4, '2026-08-10', 'NAURA HASNA', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 606, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(147, NULL, 5, 2, 4, '2026-08-10', 'KANDZIRA PUTRI', '7', 'reguler', '', 250000.00, 'I', 'Tidak', 3450000.00, 3200000.00, 'lunas', 605, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(148, NULL, 5, 2, 4, '2026-08-10', 'MAYESHA RAISHA', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 601, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(149, NULL, 5, 2, 4, '2026-08-10', 'SADIRA JANEETA', '8', 'reguler', '', 250000.00, 'I', 'Tidak', 4000000.00, 3750000.00, 'lunas', 608, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(150, NULL, 5, 2, 4, '2026-08-10', 'FABIANO ABBASY', '9', 'reguler', '', 0.00, 'I', 'Ya', 3550000.00, 3550000.00, 'lunas', 618, 558, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(151, NULL, 5, 2, 4, '2026-08-10', 'M FANDY YULIANTO', '8', 'reguler', '', 0.00, 'I', 'Ya', 2950000.00, 2950000.00, 'lunas', 615, 555, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(152, NULL, 5, 2, 4, '2026-08-10', 'M FARIZ B', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 600, 562, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(153, NULL, 5, 2, 4, '2026-08-10', 'RATU DALFA A', '9', 'reguler', '', 250000.00, 'I', 'Ya', 4000000.00, 3750000.00, 'lunas', 618, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(154, NULL, 5, 2, 4, '2026-08-10', 'GUNTUR ISKANDAR', '8', 'reguler', '', 250000.00, 'I', 'Tidak', 4000000.00, 3750000.00, 'lunas', 612, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(155, NULL, 5, 2, 4, '2026-08-10', 'NAKULA ARARYA', '7', 'reguler', '', 250000.00, 'I', 'Tidak', 3700000.00, 3450000.00, 'lunas', 599, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(156, NULL, 5, 2, 4, '2026-08-10', 'ADILLA A', '9', 'reguler', '', 0.00, 'I', 'Ya', 3550000.00, 3550000.00, 'lunas', 592, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(157, NULL, 5, 2, 4, '2026-08-10', 'ZYLVANA A', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 598, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(158, NULL, 5, 2, 4, '2026-08-10', 'M SYARIF KAMIL', '8', 'reguler', '', 250000.00, 'I', 'Ya', 4000000.00, 3750000.00, 'lunas', 611, 548, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(159, NULL, 5, 2, 4, '2026-08-10', 'ALMEIRA PUTRI', '9', 'reguler', '', 250000.00, 'I', 'Ya', 4000000.00, 3750000.00, 'lunas', 591, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(160, NULL, 5, 2, 4, '2026-08-10', 'QANITAH QURRATU', '9', 'reguler', '', 0.00, 'I', 'Ya', 2950000.00, 2950000.00, 'lunas', 592, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(161, NULL, 5, 2, 4, '2026-08-10', 'PELANGI PUTRI N', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 599, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(162, NULL, 5, 2, 4, '2026-08-10', 'ALUNA SAGITA', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 601, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(163, NULL, 5, 2, 4, '2026-08-10', 'AGHNIA AHSIL', '9', 'reguler', '', 0.00, 'I', 'Ya', 3650000.00, 3650000.00, 'lunas', 603, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(164, NULL, 5, 2, 4, '2026-08-10', 'DINAR ALKAUTSAR', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 616, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(165, NULL, 5, 2, 4, '2026-08-10', 'ROFIQI BAIHAQI', '9', 'reguler', '', 250000.00, 'I', 'Ya', 3800000.00, 3550000.00, 'lunas', 617, 558, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(166, NULL, 5, 2, 4, '2026-08-10', 'REZA NURRUHPRATAMA', '9', 'reguler', '', 0.00, 'I', 'Tidak', 3650000.00, 3650000.00, 'lunas', 603, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(167, NULL, 5, 2, 4, '2026-08-10', 'KHANSA RAHMA', '9', 'reguler', '', 250000.00, 'I', 'Tidak', 4000000.00, 3750000.00, 'lunas', 592, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(168, NULL, 5, 2, 4, '2026-08-10', 'ANISA ARISTAWATI', '8', 'reguler', '', 250000.00, 'I', 'Tidak', 4200000.00, 3950000.00, 'lunas', 608, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(169, NULL, 5, 2, 4, '2026-08-10', 'ALUNA KEINISYA', '8', 'reguler', '', 250000.00, 'I', 'Tidak', 3700000.00, 3450000.00, 'lunas', 599, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(170, NULL, 5, 2, 4, '2026-08-10', 'RAZAN DZAKI', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3450000.00, 3450000.00, 'lunas', 600, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(171, NULL, 5, 2, 4, '2026-08-10', 'RAHMA SRI R', '8', 'reguler', '', 0.00, 'I', 'Ya', 3450000.00, 3450000.00, 'lunas', 613, 566, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(172, NULL, 5, 2, 4, '2026-08-10', 'RADITYO IBNU HAFIDZ', '9', 'reguler', '', 250000.00, 'I', 'Tidak', 4200000.00, 3950000.00, 'lunas', 618, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(173, NULL, 5, 2, 4, '2026-08-10', 'KEN AISYAH', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 602, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(174, NULL, 5, 2, 4, '2026-08-10', 'AIKO RAMEYZA', '9', 'reguler', '', 250000.00, 'I', 'Ya', 4200000.00, 3950000.00, 'lunas', 603, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(175, NULL, 5, 2, 4, '2026-08-10', 'QUEENSHA ZAHIYA', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 600, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(176, NULL, 5, 2, 4, '2026-08-10', 'RAIDA PUTRI M', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3450000.00, 3450000.00, 'lunas', 601, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(177, NULL, 5, 2, 4, '2026-08-10', 'ASKANA SAKHI A', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3450000.00, 3450000.00, 'lunas', 602, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(178, NULL, 5, 2, 4, '2026-08-10', 'NADHIFA ZIYA', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3450000.00, 3450000.00, 'lunas', 606, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(179, NULL, 5, 2, 4, '2026-08-10', 'MARWAH HANI', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 602, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(180, NULL, 5, 2, 4, '2026-08-10', 'DIKZA AFRIZA', '7', 'reguler', '', 0.00, 'I', 'Ya', 2650000.00, 2650000.00, 'lunas', 600, 562, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(181, NULL, 5, 2, 4, '2026-08-10', 'JASMINE N', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3450000.00, 3450000.00, 'lunas', 606, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(182, NULL, 5, 2, 3, '2026-08-10', 'QAMEERA', '4', 'reguler', '', 0.00, 'I', 'Tidak', 4900000.00, 4900000.00, 'lunas', 575, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(183, NULL, 5, 2, 4, '2026-08-10', 'ZAHRA HAMIDAH', '7', 'reguler', '', 250000.00, 'I', 'Ya', 3700000.00, 3450000.00, 'lunas', 606, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(184, NULL, 5, 2, 4, '2026-08-10', 'M AZALIA ATHA', '9', 'reguler', '', 0.00, 'I', 'Ya', 7300000.00, 7300000.00, 'lunas', 591, 558, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(185, NULL, 5, 2, 4, '2026-08-10', 'MUSTHOFA AHMAD', '8', 'reguler', '', 0.00, 'I', 'Ya', 4450000.00, 4450000.00, 'lunas', 611, 548, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(186, NULL, 5, 2, 4, '2026-08-10', 'REINDRA', '8', 'reguler', '', 0.00, 'I', 'Ya', 5450000.00, 5450000.00, 'lunas', 610, 555, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(187, NULL, 5, 2, 4, '2026-08-10', 'PANJI TRISTAN', '9', 'reguler', '', 250000.00, 'I', 'Ya', 3200000.00, 2950000.00, 'lunas', 618, 558, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(188, NULL, 5, 2, 4, '2026-08-10', 'RAFFASYA AZKA', '8', 'reguler', '', 250000.00, 'I', 'Tidak', 4000000.00, 3750000.00, 'lunas', 611, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(189, NULL, 5, 2, 4, '2026-08-10', 'KARINA ASWARI', '7', 'reguler', '', 250000.00, 'I', 'Ya', 4200000.00, 3950000.00, 'lunas', 602, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(190, NULL, 5, 2, 4, '2026-08-10', 'MARITZA AULIA', '8', 'reguler', '', 250000.00, 'I', 'Tidak', 4500000.00, 4250000.00, 'lunas', 613, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(191, NULL, 5, 2, 4, '2026-08-10', 'GHAZI D', '8', 'reguler', '', 250000.00, 'I', 'Tidak', 4150000.00, 3900000.00, 'lunas', 608, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(192, NULL, 5, 2, 4, '2026-08-10', 'ANDHIRNI', '7', 'reguler', '', 250000.00, 'I', 'Tidak', 4200000.00, 3950000.00, 'lunas', 605, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(193, NULL, 5, 2, 4, '2026-08-10', 'HANIFA N', '9', 'reguler', '', 0.00, 'I', 'Ya', 1250000.00, 1250000.00, 'lunas', 617, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(194, NULL, 5, 2, 4, '2026-08-10', 'RAFI R', '9', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 617, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(195, NULL, 5, 2, 4, '2026-08-10', 'XENA S', '7', 'reguler', '', 0.00, 'I', 'Ya', 1250000.00, 1250000.00, 'lunas', 616, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(196, NULL, 5, 2, 4, '2026-08-10', 'ARIANE QINAN', '7', 'reguler', '', 0.00, 'I', 'Ya', 1250000.00, 1250000.00, 'lunas', 606, 552, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(197, NULL, 5, 2, 4, '2026-08-10', 'RAISYA', '9', 'reguler', '', 0.00, 'I', 'Ya', 1250000.00, 1250000.00, 'lunas', 617, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(198, NULL, 5, 2, 4, '2026-08-10', 'HAZEL ARCELIA', '9', 'reguler', '', 0.00, 'I', 'Ya', 1250000.00, 1250000.00, 'lunas', 618, 551, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(199, NULL, 5, 2, 4, '2026-08-10', 'WIDYA IRAWAN', '7', 'reguler', '', 0.00, 'I', 'Ya', 1250000.00, 1250000.00, 'lunas', 606, 557, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(200, NULL, 5, 2, 4, '2026-08-10', 'FATISHA ALMIRA', '7', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 604, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(201, NULL, 5, 2, 4, '2026-08-10', 'QORI HABIBURRAHMAN', '7', 'reguler', '', 0.00, 'I', 'Ya', 1250000.00, 1250000.00, 'lunas', 601, 562, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(202, NULL, 5, 2, 4, '2026-08-10', 'REYDEN RAJA', '7', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 606, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(203, NULL, 5, 2, 4, '2026-08-10', 'SEAN JULI ANDI', '7', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 606, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(204, NULL, 5, 2, 4, '2026-08-10', 'M ZAMZAM', '9', 'reguler', '', 0.00, 'I', 'Ya', 1250000.00, 1250000.00, 'lunas', 618, 558, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(205, NULL, 5, 2, 4, '2026-08-10', 'MAHARDIKA', '7', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 600, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(206, NULL, 5, 2, 4, '2026-08-10', 'KEAN SEVENSIX', '7', 'reguler', '', 0.00, 'I', 'Ya', 1250000.00, 1250000.00, 'lunas', 606, 557, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(207, NULL, 5, 2, 4, '2026-08-10', 'ALISHA HUSNA', '8', 'reguler', '', 0.00, 'I', 'Ya', 1250000.00, 1250000.00, 'lunas', 612, 566, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(208, NULL, 5, 2, 4, '2026-08-10', 'ALDEN K', '9', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 617, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(209, NULL, 5, 2, 4, '2026-08-10', 'DANISH BAHTIAR', '7', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 599, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(210, NULL, 5, 2, 4, '2026-08-10', 'ARKAN ALFATIH', '7', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 599, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(211, NULL, 5, 2, 4, '2026-08-10', 'KAI KINAN', '8', 'reguler', '', 0.00, 'I', 'Tidak', 1100000.00, 1100000.00, 'lunas', 599, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(212, NULL, 5, 2, 4, '2026-08-10', 'M KAIZAN', '8', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 607, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(213, NULL, 5, 2, 4, '2026-08-10', 'WULAN CANIA', '9', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 618, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(214, NULL, 5, 2, 4, '2026-08-10', 'RAFI SULTAN', '7', 'reguler', '', 250000.00, 'I', 'Tidak', 1100000.00, 850000.00, 'lunas', 606, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(215, NULL, 5, 2, 4, '2026-08-10', 'SYALUNNA', '7', 'reguler', '', 0.00, 'I', 'Tidak', 1250000.00, 1250000.00, 'lunas', 606, NULL, NULL, '2026-08-10 01:48:21', '2026-08-10 01:48:21'),
(308, NULL, 8, 2, 5, '2026-08-10', 'Bazla Rot', '10', 'reguler', '', 0.00, 'I', 'Ya', 4080000.00, 4080000.00, 'lunas', 649, 565, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(309, NULL, 8, 2, 5, '2026-08-10', 'Muhammad Danish', '10', 'reguler', '', 0.00, 'I', 'Ya', 4080000.00, 4080000.00, 'lunas', 627, 553, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(310, NULL, 8, 2, 5, '2026-08-10', 'Ayesha Belva A', '12', 'reguler', '', 0.00, 'I', 'Tidak', 4080000.00, 4080000.00, 'lunas', 632, NULL, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(311, NULL, 8, 2, 4, '2026-08-10', 'Sadira Janeeta', '7-D', 'reguler', '', 0.00, 'I', 'Tidak', 4080000.00, 4080000.00, 'lunas', 608, NULL, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(312, NULL, 8, 2, 5, '2026-08-10', 'Geofarrel Ibrahim', '10', 'reguler', '', 0.00, 'I', 'Tidak', 1000000.00, 1000000.00, 'lunas', 649, NULL, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(313, NULL, 8, 2, 5, '2026-08-10', 'Ceyzar Putra', '10', 'reguler', '', 0.00, 'I', 'Ya', 2620000.00, 2620000.00, 'lunas', 649, 554, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(314, NULL, 8, 2, 4, '2026-08-10', 'M. Hirzam Abiayasa', '9-E', 'reguler', '', 0.00, 'I', 'Ya', 1000000.00, 1000000.00, 'lunas', 594, 556, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(315, NULL, 8, 2, 4, '2026-08-10', 'Raisa Azka Arifin', '8-B', 'reguler', '', 0.00, 'I', 'Tidak', 2730000.00, 2730000.00, 'lunas', 591, NULL, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(316, NULL, 8, 2, 5, '2026-08-10', 'M Danish', '10', 'reguler', '', 0.00, 'I', 'Ya', 4080000.00, 4080000.00, 'lunas', 627, 553, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(317, NULL, 8, 2, 5, '2026-08-10', 'Ahsan Muhammad fadilah', '10', 'reguler', '', 0.00, 'I', 'Ya', 5080000.00, 5080000.00, 'lunas', 651, 563, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(318, NULL, 8, 2, 5, '2026-08-10', 'Faira Bilqist S', '10', 'reguler', '', 0.00, 'I', 'Tidak', 5080000.00, 5080000.00, 'lunas', 645, NULL, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(319, NULL, 8, 2, 5, '2026-08-10', 'M Rafiqurahman arsad', '10', 'reguler', '', 0.00, 'I', 'Tidak', 5080000.00, 5080000.00, 'lunas', 622, NULL, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(320, NULL, 8, 2, 4, '2026-08-10', 'Calista Odeli Rozadin', '7-C2', 'reguler', '', 0.00, 'I', 'Tidak', 8650000.00, 8650000.00, 'lunas', 616, NULL, NULL, '2026-08-10 02:12:50', '2026-08-10 02:12:50'),
(321, NULL, 7, 2, 5, '2026-08-10', 'RISA SYALWA OKTAVIA', '12', 'reguler', '', 0.00, 'I', 'Ya', 6000000.00, 6000000.00, 'lunas', 634, 566, NULL, '2026-08-10 02:30:46', '2026-08-10 02:30:46'),
(322, NULL, 7, 2, 5, '2026-08-10', 'M DANISH SABRAN', '10', 'reguler', '', 0.00, 'I', 'Ya', 6000000.00, 6000000.00, 'lunas', 627, 553, NULL, '2026-08-10 02:30:46', '2026-08-10 02:30:46'),
(323, NULL, 7, 2, 5, '2026-08-10', 'AYESHA SALSABILA', '12', 'reguler', '', 0.00, 'I', 'Ya', 6000000.00, 6000000.00, 'lunas', 632, 559, NULL, '2026-08-10 02:30:46', '2026-08-10 02:30:46'),
(324, NULL, 7, 2, 4, '2026-08-10', 'M ZULFADLI', '9', 'reguler', '', 0.00, 'I', 'Ya', 6000000.00, 6000000.00, 'lunas', 594, 558, NULL, '2026-08-10 02:30:46', '2026-08-10 02:30:46'),
(325, NULL, 7, 2, 4, '2026-08-10', 'ARFI ABDA\'A ISMAIL', '9', 'reguler', '', 0.00, 'I', 'Ya', 6000000.00, 6000000.00, 'lunas', 617, 558, NULL, '2026-08-10 02:30:46', '2026-08-10 02:30:46'),
(326, NULL, 7, 2, 5, '2026-08-10', 'MARSYAFIRA BUNGA CHANIAGO', '10', 'reguler', '', 0.00, 'I', 'Ya', 6000000.00, 6000000.00, 'lunas', 624, 560, NULL, '2026-08-10 02:30:46', '2026-08-10 02:30:46'),
(327, NULL, 7, 2, 5, '2026-08-10', 'JAMILATUS SOLEHA', '12', 'reguler', '', 0.00, 'I', 'Ya', 6000000.00, 6000000.00, 'lunas', 631, 559, NULL, '2026-08-10 02:30:46', '2026-08-10 02:30:46'),
(328, NULL, 7, 2, 4, '2026-08-10', 'FAUZAN RHIDO ALFHAROBY', '7', 'reguler', '', 0.00, 'I', 'Ya', 6000000.00, 6000000.00, 'lunas', 598, 557, NULL, '2026-08-10 02:30:46', '2026-08-10 02:30:46'),
(329, NULL, 7, 2, 4, '2026-08-10', 'M RAIHAN A.P', '7', 'reguler', '', 0.00, 'I', 'Ya', 6000000.00, 6000000.00, 'lunas', 606, 557, NULL, '2026-08-10 02:30:46', '2026-08-10 02:30:46'),
(330, NULL, 6, 2, 3, '2026-08-10', 'Priyanka Inara', '4', 'reguler', '', 0.00, 'I', 'Tidak', 2280000.00, 2280000.00, 'lunas', 576, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(331, NULL, 6, 2, 3, '2026-08-10', 'Deandra Razak', '4', 'reguler', '', 0.00, 'I', 'Tidak', 2280000.00, 2280000.00, 'lunas', 576, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(332, NULL, 6, 2, 3, '2026-08-10', 'Zalfa Aqilah Mahbub', '4', 'reguler', '', 0.00, 'I', 'Tidak', 1900000.00, 1900000.00, 'lunas', 577, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(333, NULL, 6, 2, 3, '2026-08-10', 'Zein Aqil Mahbub', '2', 'reguler', '', 0.00, 'I', 'Tidak', 1900000.00, 1900000.00, 'lunas', 589, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(334, NULL, 6, 2, 5, '2026-08-10', 'Kirana Haristya Dayu', '11', 'reguler', '', 0.00, 'I', 'Tidak', 2940000.00, 2940000.00, 'lunas', 640, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(335, NULL, 6, 2, 3, '2026-08-10', 'Nabila Zahira Budiman', '2', 'reguler', '', 0.00, 'I', 'Tidak', 2280000.00, 2280000.00, 'lunas', 574, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(336, NULL, 6, 2, 4, '2026-08-10', 'Reza Nurruh Pratama', '8', 'reguler', '', 0.00, 'I', 'Tidak', 2400000.00, 2400000.00, 'lunas', 597, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(337, NULL, 6, 2, 4, '2026-08-10', 'Den Ripky Aldirano', '8', 'reguler', '', 0.00, 'I', 'Ya', 2300000.00, 2300000.00, 'lunas', 613, 549, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(338, NULL, 6, 2, 5, '2026-08-10', 'Muhammad Alfarisy Hasbullah', '11', 'reguler', '', 0.00, 'I', 'Ya', 2940000.00, 2940000.00, 'lunas', 638, 549, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(339, NULL, 6, 2, 5, '2026-08-10', 'Herlangga Putra Syafa\'atullah', '11', 'reguler', '', 0.00, 'I', 'Tidak', 2100000.00, 2100000.00, 'lunas', 646, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(340, NULL, 6, 2, 4, '2026-08-10', 'Muhammad Akbar Firmansyah', '8', 'reguler', '', 0.00, 'I', 'Ya', 3200000.00, 3200000.00, 'lunas', 607, 555, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(341, NULL, 6, 2, 5, '2026-08-10', 'M. Noval Anwari Faiz', '11', 'reguler', '', 0.00, 'I', 'Ya', 2940000.00, 2940000.00, 'lunas', 638, 549, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(342, NULL, 6, 2, 3, '2026-08-10', 'Adzkia Putri Setyawana', '6', 'reguler', '', 0.00, 'I', 'Tidak', 3040000.00, 3040000.00, 'lunas', 583, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(343, NULL, 6, 2, 5, '2026-08-10', 'Jakalana Sampurna Budiman', '10', 'reguler', '', 0.00, 'I', 'Tidak', 2520000.00, 2520000.00, 'lunas', 586, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(344, NULL, 6, 2, 4, '2026-08-10', 'Fawaz Nur Fajar', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3200000.00, 3200000.00, 'lunas', 606, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(345, NULL, 6, 2, 4, '2026-08-10', 'Kanaya Sevilla Putriani', '8', 'reguler', '', 0.00, 'I', 'Ya', 3360000.00, 3360000.00, 'lunas', 614, 544, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(346, NULL, 6, 2, 5, '2026-08-10', 'Maheswara Rabbani Wicaksono', '10', 'reguler', '', 0.00, 'I', 'Tidak', 3360000.00, 3360000.00, 'lunas', 621, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(347, NULL, 6, 2, 5, '2026-08-10', 'Muhammad Daffa Dhiya', '11', 'reguler', '', 0.00, 'I', 'Ya', 3360000.00, 3360000.00, 'lunas', 638, 549, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(348, NULL, 6, 2, 4, '2026-08-10', 'Adly Rezky A', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3200000.00, 3200000.00, 'lunas', 602, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(349, NULL, 6, 2, 3, '2026-08-10', 'Chaska Alvaro', '4', 'reguler', '', 0.00, 'I', 'Tidak', 3040000.00, 3040000.00, 'lunas', 577, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(350, NULL, 6, 2, 3, '2026-08-10', 'Akhsan Alby Fauzan', '6', 'reguler', '', 0.00, 'I', 'Tidak', 2660000.00, 2660000.00, 'lunas', 582, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(351, NULL, 6, 2, 4, '2026-08-10', 'Widya Irawan Sanjaya', '7', 'reguler', '', 0.00, 'I', 'Ya', 3200000.00, 3200000.00, 'lunas', 606, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(352, NULL, 6, 2, 4, '2026-08-10', 'Kenzo Richie Ilham', '7', 'reguler', '', 0.00, 'I', 'Ya', 3200000.00, 3200000.00, 'lunas', 598, 562, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(353, NULL, 6, 2, 4, '2026-08-10', 'Alif Septian Nugraha', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 598, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(354, NULL, 6, 2, 4, '2026-08-10', 'Naufal Dzikri Amarulah', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 604, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(355, NULL, 6, 2, 5, '2026-08-10', 'Aisyah Nurul Anshor', '10', 'reguler', '', 0.00, 'I', 'Ya', 3570000.00, 3570000.00, 'lunas', 638, 550, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(356, NULL, 6, 2, 4, '2026-08-10', 'Zhalika Khaira Lubna', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3040000.00, 3040000.00, 'lunas', 598, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(357, NULL, 6, 2, 4, '2026-08-10', 'Kean Sevensix Saputra', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 606, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(358, NULL, 6, 2, 4, '2026-08-10', 'Ichsan Fajri', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 600, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(359, NULL, 6, 2, 4, '2026-08-10', 'Nobeldy El Shidqan', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 606, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(360, NULL, 6, 2, 4, '2026-08-10', 'Aufaa Sakha Suryadi', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 604, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(361, NULL, 6, 2, 4, '2026-08-10', 'Fauzan Rhido Al Fharoby', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 598, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(362, NULL, 6, 2, 4, '2026-08-10', 'M. Raihan Aria Putra', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 602, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(363, NULL, 6, 2, 4, '2026-08-10', 'Adena Zahirah Mahsa', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 606, 552, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49');
INSERT INTO `students` (`id`, `nis_nisn`, `partner_id`, `academic_year_id`, `unit_id`, `tanggal_pendaftaran`, `nama_siswa`, `kelas`, `kategori_peserta`, `program_bimbel`, `biaya_pendaftaran`, `triwulan`, `is_psam`, `total_biaya`, `biaya_program`, `status_pembayaran`, `wali_kelas_id`, `wali_santri_id`, `keterangan`, `created_at`, `updated_at`) VALUES
(364, NULL, 6, 2, 4, '2026-08-10', 'Abidzar Dimas Firdaus', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 600, 562, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(365, NULL, 6, 2, 4, '2026-08-10', 'Bilqis Shidqia Putri', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 605, 552, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(366, NULL, 6, 2, 4, '2026-08-10', 'Keisya Putri Azzahra', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 600, 552, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(367, NULL, 6, 2, 4, '2026-08-10', 'Adhwa Fajri Annada', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3400000.00, 3400000.00, 'lunas', 601, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(368, NULL, 6, 2, 4, '2026-08-10', 'Naura Hasna Humaira', '7', 'reguler', '', 0.00, 'I', 'Ya', 2900000.00, 2900000.00, 'lunas', 606, 552, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(369, NULL, 6, 2, 4, '2026-08-10', 'Davia Fatimah Adeline', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 600, 552, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(370, NULL, 6, 2, 4, '2026-08-10', 'M. Irsyad Rifki', '7', 'reguler', '', 0.00, 'I', 'Ya', 3572500.00, 3572500.00, 'lunas', 605, 562, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(371, NULL, 6, 2, 5, '2026-08-10', 'Liby Hadilla Selsa A.', '10', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 638, 550, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(372, NULL, 6, 2, 4, '2026-08-10', 'Jarvis Gyasi Abdilah', '8', 'reguler', '', 0.00, 'I', 'Tidak', 3400000.00, 3400000.00, 'lunas', 610, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(373, NULL, 6, 2, 4, '2026-08-10', 'Dinar Al Kautsar R', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 616, 552, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(374, NULL, 6, 2, 4, '2026-08-10', 'Rifki M. Fauzi', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 599, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(375, NULL, 6, 2, 4, '2026-08-10', 'M. Radyansyah Anahda', '7', 'reguler', '', 0.00, 'I', 'Ya', 2900000.00, 2900000.00, 'lunas', 599, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(376, NULL, 6, 2, 4, '2026-08-10', 'Zahra Hamidah', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 606, 552, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(377, NULL, 6, 2, 4, '2026-08-10', 'King Ghazali Al. K.', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 606, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(378, NULL, 6, 2, 4, '2026-08-10', 'M. Rafi Ali Al Faqih', '7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 600, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(379, NULL, 6, 2, 4, '2026-08-10', 'Hasbi Alfitra', '7', 'reguler', '', 0.00, 'I', 'Ya', 3230000.00, 3230000.00, 'lunas', 605, 562, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(380, NULL, 6, 2, 3, '2026-08-10', 'Safarazky Putra Berlian', '3', 'reguler', '', 0.00, 'I', 'Tidak', 3400000.00, 3400000.00, 'lunas', 572, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(381, NULL, 6, 2, 4, '2026-08-10', 'Muhammad Alwi R.', '7', 'reguler', '', 0.00, 'I', 'Ya', 3570000.00, 3570000.00, 'lunas', 599, 557, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(382, NULL, 6, 2, 5, '2026-08-10', 'Azka Ramadhan', '10', 'reguler', '', 0.00, 'I', 'Ya', 3570000.00, 3570000.00, 'lunas', 625, 546, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(383, NULL, 6, 2, 5, '2026-08-10', 'Nalendra Razka Imam', '10', 'reguler', '', 0.00, 'I', 'Ya', 3070000.00, 3070000.00, 'lunas', 620, 553, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(384, NULL, 6, 2, 5, '2026-08-10', 'Rafa Athaya Pahlevi', '10', 'reguler', '', 0.00, 'I', 'Ya', 3570000.00, 3570000.00, 'lunas', 625, 546, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(385, NULL, 6, 2, 5, '2026-08-10', 'Aurel Kiranimas', '10', 'reguler', '', 0.00, 'I', 'Ya', 3230000.00, 3230000.00, 'lunas', 622, 560, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(386, NULL, 6, 2, 3, '2026-08-10', 'Edelweis Sahila Basyar', '3', 'reguler', '', 0.00, 'I', 'Tidak', 3780000.00, 3780000.00, 'lunas', 572, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(387, NULL, 6, 2, 5, '2026-08-10', 'Nazwa Syahira Putri D', '10', 'reguler', '', 0.00, 'I', 'Ya', 3600000.00, 3600000.00, 'lunas', 638, 550, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(388, NULL, 6, 2, 4, '2026-08-10', 'Aluna Keinisya Azzahra', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3420000.00, 3420000.00, 'lunas', 599, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(389, NULL, 6, 2, 3, '2026-08-10', 'Humam Zaida Putra M.', '4', 'reguler', '', 0.00, 'I', 'Tidak', 3780000.00, 3780000.00, 'lunas', 576, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(390, NULL, 6, 2, 5, '2026-08-10', 'Naufal Imana Triana', '12', 'reguler', '', 0.00, 'I', 'Tidak', 3600000.00, 3600000.00, 'lunas', 637, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(391, NULL, 6, 2, 4, '2026-08-10', 'Mandala Bagaskara', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3780000.00, 3780000.00, 'lunas', 600, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(392, NULL, 6, 2, 5, '2026-08-10', 'Nimas Fathonah', '10', 'reguler', '', 0.00, 'I', 'Tidak', 3780000.00, 3780000.00, 'lunas', 638, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(393, NULL, 6, 2, 5, '2026-08-10', 'M. Ardhi Ghoniyul I.', '12', 'reguler', '', 0.00, 'I', 'Ya', 3100000.00, 3100000.00, 'lunas', 632, 563, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(394, NULL, 6, 2, 4, '2026-08-10', 'Chintya Amanda R', '7', 'reguler', '', 0.00, 'I', 'Ya', 2800000.00, 2800000.00, 'lunas', 599, 552, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(395, NULL, 6, 2, 4, '2026-08-10', 'Fathimah Az Zahra', '9', 'reguler', '', 0.00, 'I', 'Tidak', 2660000.00, 2660000.00, 'lunas', 591, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(396, NULL, 6, 2, 3, '2026-08-10', 'Muhammad Al Ghazali', '2', 'reguler', '', 0.00, 'I', 'Tidak', 3040000.00, 3040000.00, 'lunas', 574, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(397, NULL, 6, 2, 3, '2026-08-10', 'Azka Nambagus B', '4', 'reguler', '', 0.00, 'I', 'Tidak', 3040000.00, 3040000.00, 'lunas', 577, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(398, NULL, 6, 2, 3, '2026-08-10', 'Virajati Ramadhan', '4', 'reguler', '', 0.00, 'I', 'Tidak', 3780000.00, 3780000.00, 'lunas', 577, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(399, NULL, 6, 2, 5, '2026-08-10', 'Nayla Sofia', '12', 'reguler', '', 0.00, 'I', 'Ya', 3040000.00, 3040000.00, 'lunas', 630, 559, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(400, NULL, 6, 2, 3, '2026-08-10', 'Khayla Shaqueena R.', '3', 'reguler', '', 0.00, 'I', 'Tidak', 3780000.00, 3780000.00, 'lunas', 573, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(401, NULL, 6, 2, 5, '2026-08-10', 'Khairahma Putri Lindya', '11', 'reguler', '', 0.00, 'I', 'Tidak', 3040000.00, 3040000.00, 'lunas', 639, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(402, NULL, 6, 2, 3, '2026-08-10', 'Elmira Zivania', '5', 'reguler', '', 0.00, 'I', 'Tidak', 3200000.00, 3200000.00, 'lunas', 633, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(403, NULL, 6, 2, 4, '2026-08-10', 'Muhammad Taruna A.', '8', 'reguler', '', 0.00, 'I', 'Tidak', 3780000.00, 3780000.00, 'lunas', 611, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(404, NULL, 6, 2, 5, '2026-08-10', 'Rizky Hamdani', '10', 'reguler', '', 0.00, 'I', 'Ya', 3360000.00, 3360000.00, 'lunas', 628, 546, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(405, NULL, 6, 2, 5, '2026-08-10', 'Ariq Aziz Syamsi', '12', 'reguler', '', 0.00, 'I', 'Ya', 2660000.00, 2660000.00, 'lunas', 630, 563, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(406, NULL, 6, 2, 3, '2026-08-10', 'Athalla', '3', 'reguler', '', 0.00, 'I', 'Tidak', 3360000.00, 3360000.00, 'lunas', 572, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(407, NULL, 6, 2, 5, '2026-08-10', 'Qeenantye Putri Kastorie', '10', 'reguler', '', 0.00, 'I', 'Ya', 2520000.00, 2520000.00, 'lunas', 621, 560, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(408, NULL, 6, 2, 5, '2026-08-10', 'Geza Alfarizky', '10', 'reguler', '', 0.00, 'I', 'Ya', 2280000.00, 2280000.00, 'lunas', 620, 553, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(409, NULL, 6, 2, 3, '2026-08-10', 'Farzan Rafindra Wismono', '1', 'reguler', '', 0.00, 'I', 'Tidak', 2280000.00, 2280000.00, 'lunas', 589, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(410, NULL, 6, 2, 3, '2026-08-10', 'Ayyash Thoriq Habibi', '4', 'reguler', '', 0.00, 'I', 'Tidak', 2400000.00, 2400000.00, 'lunas', 575, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(411, NULL, 6, 2, 4, '2026-08-10', 'Fariza Muhammad A.H', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3800000.00, 3800000.00, 'lunas', 598, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(412, NULL, 6, 2, 3, '2026-08-10', 'Rio Anggara', '4', 'reguler', '', 0.00, 'I', 'Tidak', 4200000.00, 4200000.00, 'lunas', 576, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(413, NULL, 6, 2, 5, '2026-08-10', 'Senia Ramadani', '10', 'reguler', '', 0.00, 'I', 'Tidak', 4000000.00, 4000000.00, 'lunas', 638, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(414, NULL, 6, 2, 4, '2026-08-10', 'Andhirni Putri Alifah', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3500000.00, 3500000.00, 'lunas', 605, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(415, NULL, 6, 2, 4, '2026-08-10', 'Nadhif Al Islami', '7', 'reguler', '', 0.00, 'I', 'Tidak', 4200000.00, 4200000.00, 'lunas', 599, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(416, NULL, 6, 2, 5, '2026-08-10', 'M. Elfadli Athallah', '10', 'reguler', '', 0.00, 'I', 'Tidak', 4000000.00, 4000000.00, 'lunas', 624, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(417, NULL, 6, 2, 4, '2026-08-10', 'Revio Septa Resmana', '7', 'reguler', '', 0.00, 'I', 'Tidak', 3800000.00, 3800000.00, 'lunas', 601, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(418, NULL, 6, 2, 3, '2026-08-10', 'Darrel Fathan Ibrahim', '5', 'reguler', '', 0.00, 'I', 'Tidak', 3800000.00, 3800000.00, 'lunas', 580, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(419, NULL, 6, 2, 3, '2026-08-10', 'Astara Luca Arkatama', '3', 'reguler', '', 0.00, 'I', 'Tidak', 4200000.00, 4200000.00, 'lunas', 574, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(420, NULL, 6, 2, 5, '2026-08-10', 'Gavar Muhammad', '12', 'reguler', '', 0.00, 'I', 'Ya', 3800000.00, 3800000.00, 'lunas', 632, 563, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(421, NULL, 6, 2, 3, '2026-08-10', 'Putri safura Alisha Novadela', '4', 'reguler', '', 0.00, 'I', 'Tidak', 3400000.00, 3400000.00, 'lunas', 576, NULL, NULL, '2026-08-10 09:23:49', '2026-08-10 09:23:49'),
(422, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Aam Syamsiah (1)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', NULL, 544, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(423, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Ayep Mulya Barkah (1)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', NULL, 548, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(424, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Ayep Mulya Barkah (2)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', NULL, 548, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(425, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Fitriadi (1)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', NULL, 555, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(426, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Fitriadi (2)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', NULL, 555, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(427, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Huda Ahmad Syarif (1)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', NULL, 558, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(428, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - M. Iqbal Syekhabudin (1)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', NULL, 563, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(429, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Roslianawati (1)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', NULL, 565, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(430, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Tatan Herawati (1)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', 572, NULL, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(431, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Tatan Herawati (2)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', 572, NULL, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(432, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Tatan Herawati (3)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', 572, NULL, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(433, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Teni Wartini (1)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', 581, NULL, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(434, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Teni Wartini (2)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', 581, NULL, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(435, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Teni Wartini (3)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', 581, NULL, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(436, NULL, 6, 2, NULL, '2026-08-11', 'PENYESUAIAN TA 2024/2025 - Teni Wartini (4)', NULL, 'reguler', NULL, 0.00, NULL, 'Ya', 0.00, 0.00, 'lunas', 581, NULL, 'Penyesuaian poin dari TA 2024/2025 (dummy)', '2026-08-11 01:54:11', '2026-08-11 02:01:13'),
(440, NULL, 8, 2, 5, '2026-08-11', 'Muhammad Rizky Abidzar', '11-8', 'reguler', '', 0.00, 'I', 'Ya', 2500000.00, 2500000.00, 'lunas', 646, 549, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(441, NULL, 8, 2, 5, '2026-08-11', 'Muhammda Jiddan Khawalni', '11-5', 'reguler', '', 0.00, 'I', 'Ya', 3900000.00, 3900000.00, 'lunas', 643, 549, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(442, NULL, 8, 2, 5, '2026-08-11', 'Fahmi Rahman Sidiq', '11-6', 'reguler', '', 0.00, 'I', 'Ya', 3900000.00, 3900000.00, 'lunas', 644, 549, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(443, NULL, 8, 2, 5, '2026-08-11', 'Rifat Ahmad', '10-C1', 'reguler', '', 0.00, 'I', 'Ya', 3300000.00, 3300000.00, 'lunas', 638, 553, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(444, NULL, 8, 2, 5, '2026-08-11', 'Barik Ghiyas Somatri', '10-3', 'reguler', '', 0.00, 'I', 'Ya', 3000000.00, 3000000.00, 'lunas', 625, 546, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(445, NULL, 8, 2, 5, '2026-08-11', 'Danish Adjie Fadhillah', '10', 'reguler', '', 0.00, 'I', 'Ya', 3750000.00, 3750000.00, 'lunas', 638, 546, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(446, NULL, 8, 2, 5, '2026-08-11', 'Muhammad Khadafy', '10-C2', 'reguler', '', 0.00, 'I', 'Tidak', 3650000.00, 3650000.00, 'lunas', 622, NULL, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(447, NULL, 8, 2, 5, '2026-08-11', 'Nawra rizka ramdhani', '10-C2', 'reguler', '', 0.00, 'I', 'Ya', 3650000.00, 3650000.00, 'lunas', 622, 560, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(448, NULL, 8, 2, 5, '2026-08-11', 'Rafa Bintang Bagaskara', '11', 'reguler', '', 0.00, 'I', 'Ya', 3900000.00, 3900000.00, 'lunas', 648, 549, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(449, NULL, 8, 2, 5, '2026-08-11', 'Aisyah Nurul Anshor', '10-C1', 'reguler', '', 0.00, 'I', 'Ya', 3900000.00, 3900000.00, 'lunas', 638, 550, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(450, NULL, 8, 2, 5, '2026-08-11', 'Karina Kamal', '10-3', 'reguler', '', 0.00, 'I', 'Tidak', 4000000.00, 4000000.00, 'lunas', 625, NULL, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(451, NULL, 8, 2, 5, '2026-08-11', 'Muhammad Ikhsan Nurfathan', '11-7', 'reguler', '', 0.00, 'I', 'Tidak', 500000.00, 500000.00, 'lunas', 645, NULL, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(452, NULL, 8, 2, 5, '2026-08-11', 'Calista Khairinnisah', '11-7', 'reguler', '', 0.00, 'I', 'Ya', 3400000.00, 3400000.00, 'lunas', 636, 559, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45'),
(453, NULL, 8, 2, 5, '2026-08-11', 'Muhammad Satriadi', '10-C2', 'reguler', '', 0.00, 'I', 'Tidak', 3750000.00, 3750000.00, 'lunas', 622, NULL, NULL, '2026-08-11 02:25:45', '2026-08-11 02:25:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `students_backup_pra_revisi`
--

CREATE TABLE `students_backup_pra_revisi` (
  `id` int(11) NOT NULL DEFAULT 0,
  `partner_id` int(11) NOT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `tanggal_pendaftaran` date NOT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `program_bimbel` varchar(150) DEFAULT NULL,
  `biaya_kursus` decimal(14,2) NOT NULL DEFAULT 0.00,
  `pendapatan` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status_pembayaran` enum('angsuran','lunas') NOT NULL DEFAULT 'angsuran',
  `wali_kelas_id` int(11) DEFAULT NULL,
  `wali_santri_id` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `students_backup_pra_revisi`
--

INSERT INTO `students_backup_pra_revisi` (`id`, `partner_id`, `academic_year_id`, `unit_id`, `tanggal_pendaftaran`, `nama_siswa`, `kelas`, `program_bimbel`, `biaya_kursus`, `pendapatan`, `status_pembayaran`, `wali_kelas_id`, `wali_santri_id`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, '2025-07-10', 'Ahmad Zaki', '7A', 'Neutron', 1500000.00, 1500000.00, 'lunas', 1, 2, '', '2026-08-06 04:30:46', '2026-08-06 04:30:46'),
(2, 1, 2, 4, '2025-07-12', 'Fatimah Azzahra', '7B', 'Primagama', 1500000.00, 700000.00, 'angsuran', 1, NULL, '', '2026-08-06 04:30:46', '2026-08-06 04:30:46'),
(3, 1, 2, 2, '2025-07-15', 'Rizky Ramadhan', '10 IPA', 'Persiapan UTBK', 2500000.00, 1000000.00, 'angsuran', 3, 4, '', '2026-08-06 04:30:46', '2026-08-06 04:30:46'),
(4, 1, 2, 3, '2025-08-01', 'Aisyah Putri', '5A', 'Bimbel Bahasa Inggris', 1000000.00, 1000000.00, 'lunas', NULL, NULL, '', '2026-08-06 04:30:46', '2026-08-06 04:30:46'),
(5, 2, 2, 4, '2025-08-05', 'Muhammad Iqbal', '9C', 'Ganesha Operation', 1800000.00, 800000.00, 'angsuran', 1, NULL, '', '2026-08-06 04:30:46', '2026-08-06 04:30:46'),
(6, 2, 2, 5, '2025-08-11', 'Nabila Syifa', '11 IPS', 'Kelas Intensif', 2500000.00, 2500000.00, 'lunas', 3, NULL, '', '2026-08-06 04:30:46', '2026-08-06 04:30:46'),
(7, 2, 2, 3, '2025-09-02', 'Umar Faruq', '6B', 'Bimbel Reguler', 1200000.00, 600000.00, 'angsuran', NULL, NULL, '', '2026-08-06 04:30:46', '2026-08-06 04:30:46'),
(8, 1, 2, 2, '2025-09-20', 'Khadijah Salsabila', '12 IPA', 'Persiapan UTBK', 2000000.00, 2000000.00, 'lunas', 3, 4, '', '2026-08-06 04:30:46', '2026-08-06 04:30:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `student_enrollments`
--

CREATE TABLE `student_enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `program_bimbel` varchar(150) DEFAULT NULL,
  `biaya_pendaftaran` decimal(14,2) NOT NULL DEFAULT 0.00,
  `biaya_program` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_biaya` decimal(14,2) NOT NULL DEFAULT 0.00,
  `triwulan` varchar(10) DEFAULT NULL,
  `is_psam` enum('Ya','Tidak') DEFAULT 'Tidak',
  `kategori_peserta` enum('reguler','khusus') DEFAULT 'reguler',
  `status_pembayaran` enum('angsuran','lunas') DEFAULT 'angsuran',
  `wali_kelas_id` int(11) DEFAULT NULL,
  `wali_santri_id` int(11) DEFAULT NULL,
  `tanggal_enroll` date NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `student_enrollments`
--

INSERT INTO `student_enrollments` (`id`, `student_id`, `partner_id`, `academic_year_id`, `unit_id`, `program_bimbel`, `biaya_pendaftaran`, `biaya_program`, `total_biaya`, `triwulan`, `is_psam`, `kategori_peserta`, `status_pembayaran`, `wali_kelas_id`, `wali_santri_id`, `tanggal_enroll`, `status`, `created_at`, `updated_at`) VALUES
(1, 10, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(2, 11, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 648, 549, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(3, 12, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 634, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(4, 13, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Ya', 'reguler', 'lunas', 649, 554, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(5, 14, 4, 2, 5, '', 0.00, 4400000.00, 4400000.00, 'I', 'Tidak', 'reguler', 'lunas', 634, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(6, 15, 4, 2, 5, '', 0.00, 6450000.00, 6450000.00, 'I', 'Ya', 'reguler', 'lunas', 629, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(7, 16, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(8, 17, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 632, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(9, 18, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 629, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(10, 19, 4, 2, 5, '', 300000.00, 4900000.00, 5200000.00, 'I', 'Ya', 'reguler', 'lunas', 636, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(11, 20, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 631, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(12, 21, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 639, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(13, 22, 4, 2, 5, '', 0.00, 4450000.00, 4450000.00, 'I', 'Ya', 'reguler', 'lunas', 632, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(14, 23, 4, 2, 5, '', 300000.00, 5900000.00, 6200000.00, 'I', 'Tidak', 'reguler', 'lunas', 643, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(15, 24, 4, 2, 5, '', 300000.00, 6400000.00, 6700000.00, 'I', 'Ya', 'reguler', 'lunas', 650, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(16, 25, 4, 2, 5, '', 0.00, 4700000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 631, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(17, 26, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 631, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(18, 27, 4, 2, 5, '', 300000.00, 6400000.00, 6700000.00, 'I', 'Ya', 'reguler', 'lunas', 631, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(19, 28, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Ya', 'reguler', 'lunas', 650, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(20, 29, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 636, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(21, 30, 4, 2, 5, '', 0.00, 4450000.00, 4450000.00, 'I', 'Ya', 'reguler', 'lunas', 634, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(22, 31, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 631, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(23, 32, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Tidak', 'reguler', 'lunas', 643, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(24, 33, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 631, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(25, 34, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 636, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(26, 35, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(27, 36, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(28, 37, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(29, 38, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(30, 39, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 629, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(31, 40, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(32, 41, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 650, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(33, 42, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Ya', 'reguler', 'lunas', 623, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(34, 43, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 629, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(35, 44, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Tidak', 'reguler', 'lunas', 649, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(36, 45, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Ya', 'reguler', 'lunas', 649, 554, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(37, 46, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Ya', 'reguler', 'lunas', 623, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(38, 47, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 649, 549, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(39, 48, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 643, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(40, 49, 4, 2, 5, '', 0.00, 4450000.00, 4450000.00, 'I', 'Ya', 'reguler', 'lunas', 633, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(41, 50, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Ya', 'reguler', 'lunas', 644, 549, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(42, 51, 4, 2, 5, '', 300000.00, 6200000.00, 6500000.00, 'I', 'Ya', 'reguler', 'lunas', 637, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(43, 52, 4, 2, 5, '', 300000.00, 6400000.00, 6700000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(44, 53, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 636, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(45, 54, 4, 2, 5, '', 0.00, 4950000.00, 4950000.00, 'I', 'Ya', 'reguler', 'lunas', 632, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(46, 55, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 650, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(47, 56, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Tidak', 'reguler', 'lunas', 623, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(48, 57, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Tidak', 'reguler', 'lunas', 631, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(49, 58, 4, 2, 5, '', 0.00, 4450000.00, 4450000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(50, 59, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 636, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(51, 60, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 623, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(52, 61, 4, 2, 5, '', 0.00, 4950000.00, 4950000.00, 'I', 'Ya', 'reguler', 'lunas', 651, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(53, 62, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 643, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(54, 63, 4, 2, 5, '', 0.00, 3900000.00, 3900000.00, 'I', 'Ya', 'reguler', 'lunas', 643, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(55, 64, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 629, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(56, 65, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Tidak', 'reguler', 'lunas', 650, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(57, 66, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 637, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(58, 67, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 651, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(59, 68, 4, 2, 5, '', 0.00, 4950000.00, 4950000.00, 'I', 'Ya', 'reguler', 'lunas', 629, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(60, 69, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Tidak', 'reguler', 'lunas', 649, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(61, 70, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 643, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(62, 71, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 636, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(63, 72, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 636, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(64, 73, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 639, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(65, 74, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 628, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(66, 75, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 619, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(67, 76, 4, 2, 5, '', 300000.00, 6900000.00, 7200000.00, 'I', 'Ya', 'reguler', 'lunas', 634, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(68, 77, 4, 2, 5, '', 0.00, 4450000.00, 4450000.00, 'I', 'Ya', 'reguler', 'lunas', 632, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(69, 78, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 648, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(70, 79, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 623, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(71, 80, 4, 2, 5, '', 300000.00, 6400000.00, 6700000.00, 'I', 'Tidak', 'reguler', 'lunas', 623, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(72, 81, 4, 2, 5, '', 300000.00, 6400000.00, 6700000.00, 'I', 'Ya', 'reguler', 'lunas', 639, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(73, 82, 4, 2, 5, '', 300000.00, 6400000.00, 6700000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(74, 83, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 639, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(75, 84, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 648, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(76, 85, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 651, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(77, 86, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 651, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(78, 87, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 646, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(79, 88, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 628, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(80, 89, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 646, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(81, 90, 4, 2, 5, '', 0.00, 4450000.00, 4450000.00, 'I', 'Ya', 'reguler', 'lunas', 637, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(82, 91, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 648, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(83, 92, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 643, 554, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(84, 93, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 645, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(85, 94, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 623, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(86, 95, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 627, 560, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(87, 96, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 646, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(88, 97, 4, 2, 5, '', 0.00, 3950000.00, 3950000.00, 'I', 'Ya', 'reguler', 'lunas', 623, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(89, 98, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 619, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(90, 99, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 619, 553, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(91, 100, 4, 2, 5, '', 0.00, 4950000.00, 4950000.00, 'I', 'Ya', 'reguler', 'lunas', 632, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(92, 101, 4, 2, 5, '', 300000.00, 6400000.00, 6700000.00, 'I', 'Ya', 'reguler', 'lunas', 639, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(93, 102, 4, 2, 5, '', 300000.00, 4900000.00, 5200000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(94, 103, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 624, 560, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(95, 104, 4, 2, 5, '', 0.00, 550000.00, 550000.00, 'I', 'Tidak', 'reguler', 'lunas', 643, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(96, 105, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 636, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(97, 106, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 628, 553, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(98, 107, 4, 2, 5, '', 300000.00, 4400000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 637, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(99, 108, 4, 2, 5, '', 300000.00, 6400000.00, 6700000.00, 'I', 'Ya', 'reguler', 'lunas', 636, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(100, 109, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 619, 550, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(101, 110, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 643, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(102, 111, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 620, 553, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(103, 112, 4, 2, 5, '', 0.00, 4700000.00, 4700000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(104, 113, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 645, 565, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(105, 114, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 628, NULL, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(106, 115, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 621, 553, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(107, 116, 4, 2, 5, '', 300000.00, 3900000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 627, 560, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(108, 117, 4, 2, 5, '', 300000.00, 4650000.00, 4950000.00, 'I', 'Ya', 'reguler', 'lunas', 632, 563, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(109, 118, 4, 2, 5, '', 300000.00, 6695000.00, 6995000.00, 'I', 'Ya', 'reguler', 'lunas', 632, 559, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(110, 119, 4, 2, 5, '', 300000.00, 3650000.00, 3950000.00, 'I', 'Ya', 'reguler', 'lunas', 619, 560, '2026-08-07', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(111, 120, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Ya', 'reguler', 'lunas', 610, 544, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(112, 121, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Tidak', 'reguler', 'lunas', 610, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(113, 122, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Ya', 'reguler', 'lunas', 614, 566, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(114, 123, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Ya', 'reguler', 'lunas', 611, 555, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(115, 124, 5, 2, 4, '', 0.00, 2750000.00, 2750000.00, 'I', 'Tidak', 'reguler', 'lunas', 614, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(116, 125, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Tidak', 'reguler', 'lunas', 608, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(117, 126, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Ya', 'reguler', 'lunas', 618, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(118, 127, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Tidak', 'reguler', 'lunas', 608, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(119, 128, 5, 2, 4, '', 0.00, 2750000.00, 2750000.00, 'I', 'Tidak', 'reguler', 'lunas', 595, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(120, 129, 5, 2, 4, '', 0.00, 2750000.00, 2750000.00, 'I', 'Tidak', 'reguler', 'lunas', 608, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(121, 130, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Tidak', 'reguler', 'lunas', 612, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(122, 131, 5, 2, 4, '', 0.00, 2750000.00, 2750000.00, 'I', 'Ya', 'reguler', 'lunas', 595, 558, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(123, 132, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Ya', 'reguler', 'lunas', 595, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(124, 133, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Tidak', 'reguler', 'lunas', 595, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(125, 134, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Ya', 'reguler', 'lunas', 613, 555, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(126, 135, 5, 2, 4, '', 0.00, 3150000.00, 3150000.00, 'I', 'Ya', 'reguler', 'lunas', 608, 566, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(127, 136, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 615, 555, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(128, 137, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Ya', 'reguler', 'lunas', 613, 544, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(129, 138, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Tidak', 'reguler', 'lunas', 612, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(130, 139, 5, 2, 4, '', 250000.00, 3750000.00, 4000000.00, 'I', 'Tidak', 'reguler', 'lunas', 618, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(131, 140, 5, 2, 4, '', 0.00, 3250000.00, 3250000.00, 'I', 'Tidak', 'reguler', 'lunas', 614, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(132, 141, 5, 2, 4, '', 0.00, 3450000.00, 3450000.00, 'I', 'Tidak', 'reguler', 'lunas', 604, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(133, 142, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 605, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(134, 143, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 600, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(135, 144, 5, 2, 4, '', 0.00, 3650000.00, 3650000.00, 'I', 'Tidak', 'reguler', 'lunas', 591, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(136, 145, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 605, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(137, 146, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(138, 147, 5, 2, 4, '', 250000.00, 3200000.00, 3450000.00, 'I', 'Tidak', 'reguler', 'lunas', 605, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(139, 148, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 601, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(140, 149, 5, 2, 4, '', 250000.00, 3750000.00, 4000000.00, 'I', 'Tidak', 'reguler', 'lunas', 608, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(141, 150, 5, 2, 4, '', 0.00, 3550000.00, 3550000.00, 'I', 'Ya', 'reguler', 'lunas', 618, 558, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(142, 151, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Ya', 'reguler', 'lunas', 615, 555, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(143, 152, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 600, 562, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(144, 153, 5, 2, 4, '', 250000.00, 3750000.00, 4000000.00, 'I', 'Ya', 'reguler', 'lunas', 618, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(145, 154, 5, 2, 4, '', 250000.00, 3750000.00, 4000000.00, 'I', 'Tidak', 'reguler', 'lunas', 612, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(146, 155, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Tidak', 'reguler', 'lunas', 599, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(147, 156, 5, 2, 4, '', 0.00, 3550000.00, 3550000.00, 'I', 'Ya', 'reguler', 'lunas', 592, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(148, 157, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 598, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(149, 158, 5, 2, 4, '', 250000.00, 3750000.00, 4000000.00, 'I', 'Ya', 'reguler', 'lunas', 611, 548, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(150, 159, 5, 2, 4, '', 250000.00, 3750000.00, 4000000.00, 'I', 'Ya', 'reguler', 'lunas', 591, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(151, 160, 5, 2, 4, '', 0.00, 2950000.00, 2950000.00, 'I', 'Ya', 'reguler', 'lunas', 592, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(152, 161, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 599, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(153, 162, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 601, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(154, 163, 5, 2, 4, '', 0.00, 3650000.00, 3650000.00, 'I', 'Ya', 'reguler', 'lunas', 603, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(155, 164, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 616, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(156, 165, 5, 2, 4, '', 250000.00, 3550000.00, 3800000.00, 'I', 'Ya', 'reguler', 'lunas', 617, 558, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(157, 166, 5, 2, 4, '', 0.00, 3650000.00, 3650000.00, 'I', 'Tidak', 'reguler', 'lunas', 603, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(158, 167, 5, 2, 4, '', 250000.00, 3750000.00, 4000000.00, 'I', 'Tidak', 'reguler', 'lunas', 592, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(159, 168, 5, 2, 4, '', 250000.00, 3950000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 608, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(160, 169, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Tidak', 'reguler', 'lunas', 599, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(161, 170, 5, 2, 4, '', 0.00, 3450000.00, 3450000.00, 'I', 'Tidak', 'reguler', 'lunas', 600, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(162, 171, 5, 2, 4, '', 0.00, 3450000.00, 3450000.00, 'I', 'Ya', 'reguler', 'lunas', 613, 566, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(163, 172, 5, 2, 4, '', 250000.00, 3950000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 618, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(164, 173, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 602, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(165, 174, 5, 2, 4, '', 250000.00, 3950000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 603, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(166, 175, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 600, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(167, 176, 5, 2, 4, '', 0.00, 3450000.00, 3450000.00, 'I', 'Tidak', 'reguler', 'lunas', 601, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(168, 177, 5, 2, 4, '', 0.00, 3450000.00, 3450000.00, 'I', 'Tidak', 'reguler', 'lunas', 602, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(169, 178, 5, 2, 4, '', 0.00, 3450000.00, 3450000.00, 'I', 'Tidak', 'reguler', 'lunas', 606, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(170, 179, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 602, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(171, 180, 5, 2, 4, '', 0.00, 2650000.00, 2650000.00, 'I', 'Ya', 'reguler', 'lunas', 600, 562, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(172, 181, 5, 2, 4, '', 0.00, 3450000.00, 3450000.00, 'I', 'Tidak', 'reguler', 'lunas', 606, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(173, 182, 5, 2, 3, '', 0.00, 4900000.00, 4900000.00, 'I', 'Tidak', 'reguler', 'lunas', 575, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(174, 183, 5, 2, 4, '', 250000.00, 3450000.00, 3700000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(175, 184, 5, 2, 4, '', 0.00, 7300000.00, 7300000.00, 'I', 'Ya', 'reguler', 'lunas', 591, 558, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(176, 185, 5, 2, 4, '', 0.00, 4450000.00, 4450000.00, 'I', 'Ya', 'reguler', 'lunas', 611, 548, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(177, 186, 5, 2, 4, '', 0.00, 5450000.00, 5450000.00, 'I', 'Ya', 'reguler', 'lunas', 610, 555, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(178, 187, 5, 2, 4, '', 250000.00, 2950000.00, 3200000.00, 'I', 'Ya', 'reguler', 'lunas', 618, 558, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(179, 188, 5, 2, 4, '', 250000.00, 3750000.00, 4000000.00, 'I', 'Tidak', 'reguler', 'lunas', 611, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(180, 189, 5, 2, 4, '', 250000.00, 3950000.00, 4200000.00, 'I', 'Ya', 'reguler', 'lunas', 602, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(181, 190, 5, 2, 4, '', 250000.00, 4250000.00, 4500000.00, 'I', 'Tidak', 'reguler', 'lunas', 613, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(182, 191, 5, 2, 4, '', 250000.00, 3900000.00, 4150000.00, 'I', 'Tidak', 'reguler', 'lunas', 608, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(183, 192, 5, 2, 4, '', 250000.00, 3950000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 605, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(184, 193, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Ya', 'reguler', 'lunas', 617, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(185, 194, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 617, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(186, 195, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Ya', 'reguler', 'lunas', 616, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(187, 196, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(188, 197, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Ya', 'reguler', 'lunas', 617, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(189, 198, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Ya', 'reguler', 'lunas', 618, 551, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(190, 199, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(191, 200, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 604, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(192, 201, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Ya', 'reguler', 'lunas', 601, 562, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(193, 202, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 606, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(194, 203, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 606, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(195, 204, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Ya', 'reguler', 'lunas', 618, 558, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(196, 205, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 600, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(197, 206, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(198, 207, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Ya', 'reguler', 'lunas', 612, 566, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(199, 208, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 617, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(200, 209, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 599, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(201, 210, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 599, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(202, 211, 5, 2, 4, '', 0.00, 1100000.00, 1100000.00, 'I', 'Tidak', 'reguler', 'lunas', 599, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(203, 212, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 607, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(204, 213, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 618, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(205, 214, 5, 2, 4, '', 250000.00, 850000.00, 1100000.00, 'I', 'Tidak', 'reguler', 'lunas', 606, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(206, 215, 5, 2, 4, '', 0.00, 1250000.00, 1250000.00, 'I', 'Tidak', 'reguler', 'lunas', 606, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(207, 308, 8, 2, 5, '', 0.00, 4080000.00, 4080000.00, 'I', 'Ya', 'reguler', 'lunas', 649, 565, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(208, 309, 8, 2, 5, '', 0.00, 4080000.00, 4080000.00, 'I', 'Ya', 'reguler', 'lunas', 627, 553, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(209, 310, 8, 2, 5, '', 0.00, 4080000.00, 4080000.00, 'I', 'Tidak', 'reguler', 'lunas', 632, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(210, 311, 8, 2, 4, '', 0.00, 4080000.00, 4080000.00, 'I', 'Tidak', 'reguler', 'lunas', 608, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(211, 312, 8, 2, 5, '', 0.00, 1000000.00, 1000000.00, 'I', 'Tidak', 'reguler', 'lunas', 649, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(212, 313, 8, 2, 5, '', 0.00, 2620000.00, 2620000.00, 'I', 'Ya', 'reguler', 'lunas', 649, 554, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(213, 314, 8, 2, 4, '', 0.00, 1000000.00, 1000000.00, 'I', 'Ya', 'reguler', 'lunas', 594, 556, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(214, 315, 8, 2, 4, '', 0.00, 2730000.00, 2730000.00, 'I', 'Tidak', 'reguler', 'lunas', 591, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(215, 316, 8, 2, 5, '', 0.00, 4080000.00, 4080000.00, 'I', 'Ya', 'reguler', 'lunas', 627, 553, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(216, 317, 8, 2, 5, '', 0.00, 5080000.00, 5080000.00, 'I', 'Ya', 'reguler', 'lunas', 651, 563, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(217, 318, 8, 2, 5, '', 0.00, 5080000.00, 5080000.00, 'I', 'Tidak', 'reguler', 'lunas', 645, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(218, 319, 8, 2, 5, '', 0.00, 5080000.00, 5080000.00, 'I', 'Tidak', 'reguler', 'lunas', 622, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(219, 320, 8, 2, 4, '', 0.00, 8650000.00, 8650000.00, 'I', 'Tidak', 'reguler', 'lunas', 616, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(220, 321, 7, 2, 5, '', 0.00, 6000000.00, 6000000.00, 'I', 'Ya', 'reguler', 'lunas', 634, 566, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(221, 322, 7, 2, 5, '', 0.00, 6000000.00, 6000000.00, 'I', 'Ya', 'reguler', 'lunas', 627, 553, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(222, 323, 7, 2, 5, '', 0.00, 6000000.00, 6000000.00, 'I', 'Ya', 'reguler', 'lunas', 632, 559, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(223, 324, 7, 2, 4, '', 0.00, 6000000.00, 6000000.00, 'I', 'Ya', 'reguler', 'lunas', 594, 558, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(224, 325, 7, 2, 4, '', 0.00, 6000000.00, 6000000.00, 'I', 'Ya', 'reguler', 'lunas', 617, 558, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(225, 326, 7, 2, 5, '', 0.00, 6000000.00, 6000000.00, 'I', 'Ya', 'reguler', 'lunas', 624, 560, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(226, 327, 7, 2, 5, '', 0.00, 6000000.00, 6000000.00, 'I', 'Ya', 'reguler', 'lunas', 631, 559, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(227, 328, 7, 2, 4, '', 0.00, 6000000.00, 6000000.00, 'I', 'Ya', 'reguler', 'lunas', 598, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(228, 329, 7, 2, 4, '', 0.00, 6000000.00, 6000000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(229, 330, 6, 2, 3, '', 0.00, 2280000.00, 2280000.00, 'I', 'Tidak', 'reguler', 'lunas', 576, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(230, 331, 6, 2, 3, '', 0.00, 2280000.00, 2280000.00, 'I', 'Tidak', 'reguler', 'lunas', 576, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(231, 332, 6, 2, 3, '', 0.00, 1900000.00, 1900000.00, 'I', 'Tidak', 'reguler', 'lunas', 577, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(232, 333, 6, 2, 3, '', 0.00, 1900000.00, 1900000.00, 'I', 'Tidak', 'reguler', 'lunas', 589, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(233, 334, 6, 2, 5, '', 0.00, 2940000.00, 2940000.00, 'I', 'Tidak', 'reguler', 'lunas', 640, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(234, 335, 6, 2, 3, '', 0.00, 2280000.00, 2280000.00, 'I', 'Tidak', 'reguler', 'lunas', 574, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(235, 336, 6, 2, 4, '', 0.00, 2400000.00, 2400000.00, 'I', 'Tidak', 'reguler', 'lunas', 597, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(236, 337, 6, 2, 4, '', 0.00, 2300000.00, 2300000.00, 'I', 'Ya', 'reguler', 'lunas', 613, 549, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(237, 338, 6, 2, 5, '', 0.00, 2940000.00, 2940000.00, 'I', 'Ya', 'reguler', 'lunas', 638, 549, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(238, 339, 6, 2, 5, '', 0.00, 2100000.00, 2100000.00, 'I', 'Tidak', 'reguler', 'lunas', 646, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(239, 340, 6, 2, 4, '', 0.00, 3200000.00, 3200000.00, 'I', 'Ya', 'reguler', 'lunas', 607, 555, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(240, 341, 6, 2, 5, '', 0.00, 2940000.00, 2940000.00, 'I', 'Ya', 'reguler', 'lunas', 638, 549, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(241, 342, 6, 2, 3, '', 0.00, 3040000.00, 3040000.00, 'I', 'Tidak', 'reguler', 'lunas', 583, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(242, 343, 6, 2, 5, '', 0.00, 2520000.00, 2520000.00, 'I', 'Tidak', 'reguler', 'lunas', 586, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(243, 344, 6, 2, 4, '', 0.00, 3200000.00, 3200000.00, 'I', 'Tidak', 'reguler', 'lunas', 606, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(244, 345, 6, 2, 4, '', 0.00, 3360000.00, 3360000.00, 'I', 'Ya', 'reguler', 'lunas', 614, 544, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(245, 346, 6, 2, 5, '', 0.00, 3360000.00, 3360000.00, 'I', 'Tidak', 'reguler', 'lunas', 621, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(246, 347, 6, 2, 5, '', 0.00, 3360000.00, 3360000.00, 'I', 'Ya', 'reguler', 'lunas', 638, 549, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(247, 348, 6, 2, 4, '', 0.00, 3200000.00, 3200000.00, 'I', 'Tidak', 'reguler', 'lunas', 602, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(248, 349, 6, 2, 3, '', 0.00, 3040000.00, 3040000.00, 'I', 'Tidak', 'reguler', 'lunas', 577, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(249, 350, 6, 2, 3, '', 0.00, 2660000.00, 2660000.00, 'I', 'Tidak', 'reguler', 'lunas', 582, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(250, 351, 6, 2, 4, '', 0.00, 3200000.00, 3200000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(251, 352, 6, 2, 4, '', 0.00, 3200000.00, 3200000.00, 'I', 'Ya', 'reguler', 'lunas', 598, 562, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(252, 353, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 598, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(253, 354, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 604, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(254, 355, 6, 2, 5, '', 0.00, 3570000.00, 3570000.00, 'I', 'Ya', 'reguler', 'lunas', 638, 550, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(255, 356, 6, 2, 4, '', 0.00, 3040000.00, 3040000.00, 'I', 'Tidak', 'reguler', 'lunas', 598, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(256, 357, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(257, 358, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 600, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(258, 359, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(259, 360, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 604, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(260, 361, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 598, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(261, 362, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 602, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(262, 363, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(263, 364, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 600, 562, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(264, 365, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 605, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(265, 366, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 600, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(266, 367, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Tidak', 'reguler', 'lunas', 601, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(267, 368, 6, 2, 4, '', 0.00, 2900000.00, 2900000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(268, 369, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 600, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(269, 370, 6, 2, 4, '', 0.00, 3572500.00, 3572500.00, 'I', 'Ya', 'reguler', 'lunas', 605, 562, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(270, 371, 6, 2, 5, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 638, 550, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(271, 372, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Tidak', 'reguler', 'lunas', 610, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(272, 373, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 616, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(273, 374, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 599, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(274, 375, 6, 2, 4, '', 0.00, 2900000.00, 2900000.00, 'I', 'Ya', 'reguler', 'lunas', 599, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(275, 376, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(276, 377, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 606, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(277, 378, 6, 2, 4, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 600, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(278, 379, 6, 2, 4, '', 0.00, 3230000.00, 3230000.00, 'I', 'Ya', 'reguler', 'lunas', 605, 562, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(279, 380, 6, 2, 3, '', 0.00, 3400000.00, 3400000.00, 'I', 'Tidak', 'reguler', 'lunas', 572, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(280, 381, 6, 2, 4, '', 0.00, 3570000.00, 3570000.00, 'I', 'Ya', 'reguler', 'lunas', 599, 557, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(281, 382, 6, 2, 5, '', 0.00, 3570000.00, 3570000.00, 'I', 'Ya', 'reguler', 'lunas', 625, 546, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(282, 383, 6, 2, 5, '', 0.00, 3070000.00, 3070000.00, 'I', 'Ya', 'reguler', 'lunas', 620, 553, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(283, 384, 6, 2, 5, '', 0.00, 3570000.00, 3570000.00, 'I', 'Ya', 'reguler', 'lunas', 625, 546, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(284, 385, 6, 2, 5, '', 0.00, 3230000.00, 3230000.00, 'I', 'Ya', 'reguler', 'lunas', 622, 560, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(285, 386, 6, 2, 3, '', 0.00, 3780000.00, 3780000.00, 'I', 'Tidak', 'reguler', 'lunas', 572, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(286, 387, 6, 2, 5, '', 0.00, 3600000.00, 3600000.00, 'I', 'Ya', 'reguler', 'lunas', 638, 550, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(287, 388, 6, 2, 4, '', 0.00, 3420000.00, 3420000.00, 'I', 'Tidak', 'reguler', 'lunas', 599, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(288, 389, 6, 2, 3, '', 0.00, 3780000.00, 3780000.00, 'I', 'Tidak', 'reguler', 'lunas', 576, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(289, 390, 6, 2, 5, '', 0.00, 3600000.00, 3600000.00, 'I', 'Tidak', 'reguler', 'lunas', 637, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(290, 391, 6, 2, 4, '', 0.00, 3780000.00, 3780000.00, 'I', 'Tidak', 'reguler', 'lunas', 600, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(291, 392, 6, 2, 5, '', 0.00, 3780000.00, 3780000.00, 'I', 'Tidak', 'reguler', 'lunas', 638, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(292, 393, 6, 2, 5, '', 0.00, 3100000.00, 3100000.00, 'I', 'Ya', 'reguler', 'lunas', 632, 563, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(293, 394, 6, 2, 4, '', 0.00, 2800000.00, 2800000.00, 'I', 'Ya', 'reguler', 'lunas', 599, 552, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(294, 395, 6, 2, 4, '', 0.00, 2660000.00, 2660000.00, 'I', 'Tidak', 'reguler', 'lunas', 591, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(295, 396, 6, 2, 3, '', 0.00, 3040000.00, 3040000.00, 'I', 'Tidak', 'reguler', 'lunas', 574, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(296, 397, 6, 2, 3, '', 0.00, 3040000.00, 3040000.00, 'I', 'Tidak', 'reguler', 'lunas', 577, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(297, 398, 6, 2, 3, '', 0.00, 3780000.00, 3780000.00, 'I', 'Tidak', 'reguler', 'lunas', 577, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(298, 399, 6, 2, 5, '', 0.00, 3040000.00, 3040000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 559, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(299, 400, 6, 2, 3, '', 0.00, 3780000.00, 3780000.00, 'I', 'Tidak', 'reguler', 'lunas', 573, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14');
INSERT INTO `student_enrollments` (`id`, `student_id`, `partner_id`, `academic_year_id`, `unit_id`, `program_bimbel`, `biaya_pendaftaran`, `biaya_program`, `total_biaya`, `triwulan`, `is_psam`, `kategori_peserta`, `status_pembayaran`, `wali_kelas_id`, `wali_santri_id`, `tanggal_enroll`, `status`, `created_at`, `updated_at`) VALUES
(300, 401, 6, 2, 5, '', 0.00, 3040000.00, 3040000.00, 'I', 'Tidak', 'reguler', 'lunas', 639, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(301, 402, 6, 2, 3, '', 0.00, 3200000.00, 3200000.00, 'I', 'Tidak', 'reguler', 'lunas', 633, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(302, 403, 6, 2, 4, '', 0.00, 3780000.00, 3780000.00, 'I', 'Tidak', 'reguler', 'lunas', 611, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(303, 404, 6, 2, 5, '', 0.00, 3360000.00, 3360000.00, 'I', 'Ya', 'reguler', 'lunas', 628, 546, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(304, 405, 6, 2, 5, '', 0.00, 2660000.00, 2660000.00, 'I', 'Ya', 'reguler', 'lunas', 630, 563, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(305, 406, 6, 2, 3, '', 0.00, 3360000.00, 3360000.00, 'I', 'Tidak', 'reguler', 'lunas', 572, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(306, 407, 6, 2, 5, '', 0.00, 2520000.00, 2520000.00, 'I', 'Ya', 'reguler', 'lunas', 621, 560, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(307, 408, 6, 2, 5, '', 0.00, 2280000.00, 2280000.00, 'I', 'Ya', 'reguler', 'lunas', 620, 553, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(308, 409, 6, 2, 3, '', 0.00, 2280000.00, 2280000.00, 'I', 'Tidak', 'reguler', 'lunas', 589, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(309, 410, 6, 2, 3, '', 0.00, 2400000.00, 2400000.00, 'I', 'Tidak', 'reguler', 'lunas', 575, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(310, 411, 6, 2, 4, '', 0.00, 3800000.00, 3800000.00, 'I', 'Tidak', 'reguler', 'lunas', 598, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(311, 412, 6, 2, 3, '', 0.00, 4200000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 576, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(312, 413, 6, 2, 5, '', 0.00, 4000000.00, 4000000.00, 'I', 'Tidak', 'reguler', 'lunas', 638, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(313, 414, 6, 2, 4, '', 0.00, 3500000.00, 3500000.00, 'I', 'Tidak', 'reguler', 'lunas', 605, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(314, 415, 6, 2, 4, '', 0.00, 4200000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 599, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(315, 416, 6, 2, 5, '', 0.00, 4000000.00, 4000000.00, 'I', 'Tidak', 'reguler', 'lunas', 624, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(316, 417, 6, 2, 4, '', 0.00, 3800000.00, 3800000.00, 'I', 'Tidak', 'reguler', 'lunas', 601, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(317, 418, 6, 2, 3, '', 0.00, 3800000.00, 3800000.00, 'I', 'Tidak', 'reguler', 'lunas', 580, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(318, 419, 6, 2, 3, '', 0.00, 4200000.00, 4200000.00, 'I', 'Tidak', 'reguler', 'lunas', 574, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(319, 420, 6, 2, 5, '', 0.00, 3800000.00, 3800000.00, 'I', 'Ya', 'reguler', 'lunas', 632, 563, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(320, 421, 6, 2, 3, '', 0.00, 3400000.00, 3400000.00, 'I', 'Tidak', 'reguler', 'lunas', 576, NULL, '2026-08-10', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(321, 422, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', NULL, 544, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(322, 423, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', NULL, 548, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(323, 424, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', NULL, 548, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(324, 425, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', NULL, 555, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(325, 426, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', NULL, 555, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(326, 427, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', NULL, 558, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(327, 428, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', NULL, 563, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(328, 429, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', NULL, 565, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(329, 430, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', 572, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(330, 431, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', 572, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(331, 432, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', 572, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(332, 433, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', 581, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(333, 434, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', 581, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(334, 435, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', 581, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(335, 436, 6, 2, NULL, NULL, 0.00, 0.00, 0.00, NULL, 'Ya', 'reguler', 'lunas', 581, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(336, 440, 8, 2, 5, '', 0.00, 2500000.00, 2500000.00, 'I', 'Ya', 'reguler', 'lunas', 646, 549, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(337, 441, 8, 2, 5, '', 0.00, 3900000.00, 3900000.00, 'I', 'Ya', 'reguler', 'lunas', 643, 549, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(338, 442, 8, 2, 5, '', 0.00, 3900000.00, 3900000.00, 'I', 'Ya', 'reguler', 'lunas', 644, 549, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(339, 443, 8, 2, 5, '', 0.00, 3300000.00, 3300000.00, 'I', 'Ya', 'reguler', 'lunas', 638, 553, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(340, 444, 8, 2, 5, '', 0.00, 3000000.00, 3000000.00, 'I', 'Ya', 'reguler', 'lunas', 625, 546, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(341, 445, 8, 2, 5, '', 0.00, 3750000.00, 3750000.00, 'I', 'Ya', 'reguler', 'lunas', 638, 546, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(342, 446, 8, 2, 5, '', 0.00, 3650000.00, 3650000.00, 'I', 'Tidak', 'reguler', 'lunas', 622, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(343, 447, 8, 2, 5, '', 0.00, 3650000.00, 3650000.00, 'I', 'Ya', 'reguler', 'lunas', 622, 560, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(344, 448, 8, 2, 5, '', 0.00, 3900000.00, 3900000.00, 'I', 'Ya', 'reguler', 'lunas', 648, 549, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(345, 449, 8, 2, 5, '', 0.00, 3900000.00, 3900000.00, 'I', 'Ya', 'reguler', 'lunas', 638, 550, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(346, 450, 8, 2, 5, '', 0.00, 4000000.00, 4000000.00, 'I', 'Tidak', 'reguler', 'lunas', 625, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(347, 451, 8, 2, 5, '', 0.00, 500000.00, 500000.00, 'I', 'Tidak', 'reguler', 'lunas', 645, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(348, 452, 8, 2, 5, '', 0.00, 3400000.00, 3400000.00, 'I', 'Ya', 'reguler', 'lunas', 636, 559, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14'),
(349, 453, 8, 2, 5, '', 0.00, 3750000.00, 3750000.00, 'I', 'Tidak', 'reguler', 'lunas', 622, NULL, '2026-08-11', 'aktif', '2026-09-14 01:19:14', '2026-09-14 01:19:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `nama_unit` varchar(100) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `units`
--

INSERT INTO `units` (`id`, `nama_unit`, `status`) VALUES
(3, 'SD', 'aktif'),
(4, 'SMP', 'aktif'),
(5, 'SMA', 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(120) NOT NULL,
  `username` varchar(60) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','mitra','wali_kelas','wali_santri','orang_tua','direksi') NOT NULL DEFAULT 'admin',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(430, 'admin', 'admin', 'admin@lppa.com', '$2y$10$DhzcyHXQIQYnxf9d5K6Qq.KIY.MGLLamnjACFocgf7YHlgKW1jCZm', 'admin', 'aktif', '2026-08-07 06:10:46', '2026-08-07 06:12:39'),
(535, 'AAM SYAMSIAH', 'aam.syamsiah', 'aam.syamsiah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(536, 'ASEP RAHMAN', 'asep.rahman', 'asep.rahman@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(537, 'AGUS YUDI ALI FIRDAUS', 'agus.yudi.ali.firdaus', 'agus.yudi.ali.firdaus@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(538, 'ALIFA TURROSYIDAH', 'alifa.turrosyidah', 'alifa.turrosyidah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(539, 'AYEP MULYA BARKAH', 'ayep.mulya.barkah', 'ayep.mulya.barkah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(540, 'DADANG DIMYATI', 'dadang.dimyati', 'dadang.dimyati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(541, 'DINI PURNAMA NURUL AJI', 'dini.purnama.nurul.aji', 'dini.purnama.nurul.aji@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(542, 'ELA N H', 'ela.n.h', 'ela.n.h@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(543, 'ELIS SUMYATI', 'elis.sumyati', 'elis.sumyati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(544, 'F. NIZAR', 'f.nizar', 'f.nizar@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(545, 'FERRY FERDIANSYAH', 'ferry.ferdiansyah', 'ferry.ferdiansyah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(546, 'FITRIADI', 'fitriadi', 'fitriadi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(547, 'HAMDANI', 'hamdani', 'hamdani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(548, 'HILMAN', 'hilman', 'hilman@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(549, 'HUDA AHMAD SYARIF', 'huda.ahmad.syarif', 'huda.ahmad.syarif@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(550, 'IDA HAMIDAH', 'ida.hamidah', 'ida.hamidah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(551, 'IMA SRI YUNIANI', 'ima.sri.yuniani', 'ima.sri.yuniani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(552, 'ITANG RUSTANDI', 'itang.rustandi', 'itang.rustandi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(553, 'LUKMANUL HAKIM', 'lukmanul.hakim', 'lukmanul.hakim@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(554, 'M. IQBAL SYEKHABUDIN', 'm.iqbal.syekhabudin', 'm.iqbal.syekhabudin@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(555, 'MIA NUR ASIAH', 'mia.nur.asiah', 'mia.nur.asiah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(556, 'ROSLIANAWATI', 'roslianawati', 'roslianawati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(557, 'SITI INDIRA MARYAM', 'siti.indira.maryam', 'siti.indira.maryam@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(558, 'SRI RODIAH', 'sri.rodiah', 'sri.rodiah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(559, 'SANTI', 'santi', 'santi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(560, 'RYSNA', 'rysna', 'rysna@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(561, 'NUNUNG KARTINI', 'nunung.kartini', 'nunung.kartini@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(562, 'YAYAH HERYATI', 'yayah.heryati', 'yayah.heryati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_santri', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(563, 'TATAN HERAWATI', 'tatan.herawati', 'tatan.herawati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(564, 'TINTIN AGUSTIANA', 'tintin.agustiana', 'tintin.agustiana@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(565, 'SUSAN MARIAM', 'susan.mariam', 'susan.mariam@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(566, 'RAISA INDAH FARDINI', 'raisa.indah.fardini', 'raisa.indah.fardini@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(567, 'RYAN FEBRIAN', 'ryan.febrian', 'ryan.febrian@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(568, 'YULLI MELANIAWATI', 'yulli.melaniawati', 'yulli.melaniawati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(569, 'SUSILOWATI', 'susilowati', 'susilowati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(570, 'HENI HARYANI', 'heni.haryani', 'heni.haryani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(571, 'AI MULYANI', 'ai.mulyani', 'ai.mulyani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(572, 'TENI WARTINI', 'teni.wartini', 'teni.wartini@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(573, 'RD.ELIH JAMILAH', 'rdelih.jamilah', 'rdelih.jamilah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(574, 'ABILLA PRIMA HASTUTI', 'abilla.prima.hastuti', 'abilla.prima.hastuti@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(575, 'YATI KURHAYATI', 'yati.kurhayati', 'yati.kurhayati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(576, 'AYI KANA', 'ayi.kana', 'ayi.kana@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(577, 'LILIS LISNAWATI', 'lilis.lisnawati', 'lilis.lisnawati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(578, 'SRI WULANDARI', 'sri.wulandari', 'sri.wulandari@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(579, 'WIWI WIHARYATI', 'wiwi.wiharyati', 'wiwi.wiharyati@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(580, 'INDAYANI', 'indayani', 'indayani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(581, 'RIKI AHMAD ZAELANI, S.Si.', 'riki.ahmad.zaelani.ssi', 'riki.ahmad.zaelani.ssi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(582, 'VANNY GUSTIKASARI, S.Pd., M.M.', 'vanny.gustikasari.spd.mm', 'vanny.gustikasari.spd.mm@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(583, 'Hj. KUSMAYATI, S.Pd.', 'hj.kusmayati.spd', 'hj.kusmayati.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(584, 'Dra. IPAH SARIPAH', 'dra.ipah.saripah', 'dra.ipah.saripah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(585, 'ELIS MASITOH, S.P.', 'elis.masitoh.sp', 'elis.masitoh.sp@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(586, 'WINA NURNINGSARI, M.Pd.', 'wina.nurningsari.mpd', 'wina.nurningsari.mpd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(587, 'Drs. RONY HERMANSYAH', 'drs.rony.hermansyah', 'drs.rony.hermansyah@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(588, 'HANNY HADIYANTI, S.Pd.', 'hanny.hadiyanti.spd', 'hanny.hadiyanti.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(589, 'NELA NAYLA, S. Pd', 'nela.nayla.s.pd', 'nela.nayla.s.pd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(590, 'SUCI LESTARI, M. Pd', 'suci.lestari.m.pd', 'suci.lestari.m.pd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(591, 'CHAERUL WILDAN, S.Pd.', 'chaerul.wildan.spd', 'chaerul.wildan.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(592, 'KURNIAWATI, S.Ag.', 'kurniawati.sag', 'kurniawati.sag@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(593, 'RITA PUSPITASARI, S.Pd', 'rita.puspitasari.spd', 'rita.puspitasari.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(594, 'TINTIN SURTINI, Dra', 'tintin.surtini.dra', 'tintin.surtini.dra@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(595, 'EUIS NURYANTI DEWI, S.TP', 'euis.nuryanti.dewi.stp', 'euis.nuryanti.dewi.stp@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(596, 'MOMON , S.S', 'momon.ss', 'momon.ss@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(597, 'Drs. MUHAMMAD RIDWAN, M.M.', 'drs.muhammad.ridwan.mm', 'drs.muhammad.ridwan.mm@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(598, 'INDRIANI APSARI, S.Pd', 'indriani.apsari.spd', 'indriani.apsari.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(599, 'SATIVA , SE', 'sativa.se', 'sativa.se@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(600, 'HASYIDAH BR MANURUNG ,S.Pd', 'hasyidah.br.manurung.spd', 'hasyidah.br.manurung.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(601, 'SUMARTONO, S.Pd', 'sumartono.spd', 'sumartono.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(602, 'IIS NURHAYATI,S.Si', 'iis.nurhayatissi', 'iis.nurhayatissi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(603, 'SOLEH, S.Ag', 'soleh.sag', 'soleh.sag@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(604, 'IMAS SUNARTI S.Pd', 'imas.sunarti.spd', 'imas.sunarti.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(605, 'SITI SUWARNI, S.Pd', 'siti.suwarni.spd', 'siti.suwarni.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(606, 'YUKARSA', 'yukarsa', 'yukarsa@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(607, 'ANI SOPYANI', 'ani.sopyani', 'ani.sopyani@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(608, 'DADANG SUPRIYANTO', 'dadang.supriyanto', 'dadang.supriyanto@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(609, 'AHMAD SODIKIN', 'ahmad.sodikin', 'ahmad.sodikin@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(610, 'Lilis Nurlaela Hasanah, S.Pd.', 'lilis.nurlaela.hasanah.spd', 'lilis.nurlaela.hasanah.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(611, 'Kosim Saputra, Drs.', 'kosim.saputra.drs', 'kosim.saputra.drs@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(612, 'M. Ady Saefurohman S. Y., S.H.', 'm.ady.saefurohman.s.y.sh', 'm.ady.saefurohman.s.y.sh@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(613, 'Raihani Ariefah, S.Pd.', 'raihani.ariefah.spd', 'raihani.ariefah.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(614, 'Risni Tristania, S.Si.', 'risni.tristania.ssi', 'risni.tristania.ssi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(615, 'Rina Fauziyyah Hasna, S.S.', 'rina.fauziyyah.hasna.ss', 'rina.fauziyyah.hasna.ss@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(616, 'Anggia Perbawa, S.Pd.', 'anggia.perbawa.spd', 'anggia.perbawa.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(617, 'Hj. Eros Rosmawati, Dra., M.M.', 'hj.eros.rosmawati.dra.mm', 'hj.eros.rosmawati.dra.mm@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(618, 'Dadang Supriatna, S.Pd.', 'dadang.supriatna.spd', 'dadang.supriatna.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(619, 'Eva Nur Indah Hayati, S.Pd.', 'eva.nur.indah.hayati.spd', 'eva.nur.indah.hayati.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(620, 'Aida Mariyah, S.Pd.', 'aida.mariyah.spd', 'aida.mariyah.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(621, 'Hj. Euis Nuraeni, S.Pd.', 'hj.euis.nuraeni.spd', 'hj.euis.nuraeni.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(622, 'Didik Permana, S.Pd.', 'didik.permana.spd', 'didik.permana.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(623, 'Fitria Dwi Farina, M.Pd.', 'fitria.dwi.farina.mpd', 'fitria.dwi.farina.mpd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(624, 'Yunita Puji Astutik, S.Kom.', 'yunita.puji.astutik.skom', 'yunita.puji.astutik.skom@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(625, 'Rita Permata, S.Pd.', 'rita.permata.spd', 'rita.permata.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(626, 'Mochtar Arief Kamis, Drs.', 'mochtar.arief.kamis.drs', 'mochtar.arief.kamis.drs@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(627, 'Yani Taryani, S.Pd.', 'yani.taryani.spd', 'yani.taryani.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(628, 'Budi Jayadi, S.Pd.', 'budi.jayadi.spd', 'budi.jayadi.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(629, 'Tati Rohayati, S.S.', 'tati.rohayati.ss', 'tati.rohayati.ss@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(630, 'Hanny Fitriani Yusuf, S.Si.', 'hanny.fitriani.yusuf.ssi', 'hanny.fitriani.yusuf.ssi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(632, 'Abas Basari, S.Si. M.M.', 'abas.basari.ssi.mm', 'abas.basari.ssi.mm@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:07', '2026-08-07 06:19:07'),
(633, 'Dadan Rohaendi, S.Pd.', 'dadan.rohaendi.spd', 'dadan.rohaendi.spd@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:08', '2026-08-07 06:19:08'),
(634, 'Wiwit Widorini, S.Si', 'wiwit.widorini.ssi', 'wiwit.widorini.ssi@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:08', '2026-08-07 06:19:08'),
(635, 'Endang Jamaludin H., S.Ag.', 'endang.jamaludin.h.sag', 'endang.jamaludin.h.sag@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:08', '2026-08-07 06:19:08'),
(636, 'Noval Anwari Faiz, S.S.', 'noval.anwari.faiz.ss', 'noval.anwari.faiz.ss@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:08', '2026-08-07 06:19:08'),
(637, 'Endang Rahmat, Drs.', 'endang.rahmat.drs', 'endang.rahmat.drs@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:08', '2026-08-07 06:19:08'),
(638, 'Wawan Gunawan, S.Ag.', 'wawan.gunawan.sag', 'wawan.gunawan.sag@lppa.local', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 06:19:08', '2026-08-07 06:19:08'),
(641, 'Neutron Bandung', 'neutron', NULL, '$2y$10$L0o38vki/9.qEjMk/dUxbu5RV9yLg3Rrda1j33XJIGUUZRyvt6BN.', 'mitra', 'aktif', '2026-08-07 06:27:17', '2026-08-07 06:27:17'),
(642, 'Ato Taopik, S.Ag', 'ato.taopik', 'atotaopik@gmail.com', '$2y$10$uMhDNhistBh6PvCb.F.rj.gF1NIwedE21Nk3vfr/3LgCssveNBIQq', 'wali_kelas', 'aktif', '2026-08-07 08:16:09', '2026-08-07 08:16:09'),
(643, 'Riad Taufik Lazwardi, M.Si', 'riad.taufik', 'Riadtaufik@gmail.com', '$2y$10$AwZrkRdWIvJ1MCwBde3PGOOPsu5Tg1sblCouoxNQ6Yd0y4P6w0mLi', 'wali_kelas', 'aktif', '2026-08-07 08:17:23', '2026-08-20 07:33:16'),
(644, 'H. Udin Nashruddin, S. Si, M.M', 'udin.nashruddin', 'udinnashruddin@gmail.com', '$2y$10$9EIS5OS0U4R61G8xpLi1we0I6i0uzbNv5Y3L2E1vxwqRLxExk3Iz2', 'wali_kelas', 'aktif', '2026-08-07 08:18:04', '2026-08-07 08:18:04'),
(645, 'Dinar Nurul Fauziah, S.E. M.E.', 'dinar.nurul', 'dinarnurul@gmail.com', '$2y$10$uFUOV5MBj2drE66FhTtGEehVHaseTUO2SuAHB9OIstsvsDtlkQNY2', 'wali_kelas', 'aktif', '2026-08-07 08:18:46', '2026-08-07 08:18:46'),
(646, 'PRIMAGAMA', 'primagama', NULL, '$2y$10$dcuA5nL0/iWiuWFYkenl4uqySvBMAFl1Dl50Dzvyd4ZlufNW3yT7a', 'mitra', 'aktif', '2026-08-07 09:04:36', '2026-08-07 09:04:36'),
(647, 'IEC', 'iec', NULL, '$2y$10$wRsz3TqvsKYKMFUTFfFpKuuzu3pdDjpktYR1EqjUmKSUseFQFzzMu', 'mitra', 'aktif', '2026-08-07 09:05:03', '2026-08-07 09:05:03'),
(648, 'CENTRAL BRAINPRO', 'central', NULL, '$2y$10$XDTTSJOg.iV6R19vE0jpVu38YzLb0GcSXmJnxsRsmRqjb5oqk5f4q', 'mitra', 'aktif', '2026-08-07 09:05:31', '2026-08-07 09:05:31'),
(649, 'Padjadjaran Language School', 'pls', NULL, '$2y$10$tGp8ULuqo8qOgKmDo5kSweZBOBo0CI9VkQSj2rpym051vfsipUnxK', 'mitra', 'aktif', '2026-08-07 09:06:01', '2026-08-07 09:06:01'),
(652, 'cecep', 'cecep', 'cecep@gmail.com', '$2y$10$MxZiZiln9OQjSlXU1ROd9.tdU36F9wqKfL4AGI1PT0jNhkhAptdj.', 'direksi', 'aktif', '2026-09-02 06:20:47', '2026-09-02 06:20:47');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_tanggal` (`student_id`,`tanggal`),
  ADD KEY `idx_partner_tanggal` (`partner_id`,`tanggal`),
  ADD KEY `idx_academic_year` (`academic_year_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ta_status` (`status`);

--
-- Indeks untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`),
  ADD KEY `idx_audit_created` (`created_at`);

--
-- Indeks untuk tabel `config_fee_settings`
--
ALTER TABLE `config_fee_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_jenis` (`jenis_fee`);

--
-- Indeks untuk tabel `fee_rules`
--
ALTER TABLE `fee_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fee_jenis` (`jenis_rekomendator`),
  ADD KEY `idx_fee_status` (`status`),
  ADD KEY `idx_academic_year` (`academic_year_id`);

--
-- Indeks untuk tabel `galeri_mitra`
--
ALTER TABLE `galeri_mitra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_partner` (`partner_id`),
  ADD KEY `idx_urutan` (`urutan`),
  ADD KEY `idx_aktif` (`is_aktif`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `jadwal_pemakaian_ruang`
--
ALTER TABLE `jadwal_pemakaian_ruang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `idx_academic_year` (`academic_year_id`),
  ADD KEY `idx_partner` (`partner_id`),
  ADD KEY `idx_kelas` (`kelas_detail_id`),
  ADD KEY `idx_hari` (`hari`),
  ADD KEY `idx_jadwal_unik` (`academic_year_id`,`kelas_detail_id`,`hari`,`jam_mulai`,`jam_selesai`);

--
-- Indeks untuk tabel `jenis_pencapaian`
--
ALTER TABLE `jenis_pencapaian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_aktif` (`is_aktif`);

--
-- Indeks untuk tabel `kelas_detail`
--
ALTER TABLE `kelas_detail`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_kelas` (`unit_id`,`nama_kelas`),
  ADD KEY `idx_unit_jenjang` (`unit_id`,`jenjang`);

--
-- Indeks untuk tabel `konten_lppa`
--
ALTER TABLE `konten_lppa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bagian` (`bagian`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_partner_user` (`user_id`),
  ADD KEY `idx_partner_nama` (`nama_mitra`),
  ADD KEY `idx_partner_status` (`status`),
  ADD KEY `idx_public_display` (`is_public`,`display_order`);

--
-- Indeks untuk tabel `partner_programs_pdfs`
--
ALTER TABLE `partner_programs_pdfs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_partner_public` (`partner_id`,`is_public`);

--
-- Indeks untuk tabel `pencapaian_siswa`
--
ALTER TABLE `pencapaian_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_partner` (`partner_id`),
  ADD KEY `idx_academic_year` (`academic_year_id`),
  ADD KEY `idx_jenis` (`jenis_pencapaian_id`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `profit_sharing_rules`
--
ALTER TABLE `profit_sharing_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ps_status` (`status`),
  ADD KEY `idx_ps_mulai` (`tanggal_mulai`);

--
-- Indeks untuk tabel `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_program_status` (`status`);

--
-- Indeks untuk tabel `recommenders`
--
ALTER TABLE `recommenders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rec_user` (`user_id`),
  ADD KEY `fk_rec_unit` (`unit_id`),
  ADD KEY `idx_rec_jenis` (`jenis`),
  ADD KEY `idx_rec_nama` (`nama`),
  ADD KEY `idx_rec_status` (`status`);

--
-- Indeks untuk tabel `setoran_bagi_hasil`
--
ALTER TABLE `setoran_bagi_hasil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_setoran_created` (`created_by`),
  ADD KEY `fk_setoran_reviewed` (`reviewed_by`),
  ADD KEY `idx_setoran_partner` (`partner_id`),
  ADD KEY `idx_setoran_status` (`status`),
  ADD KEY `idx_setoran_tanggal` (`tanggal_setoran`);

--
-- Indeks untuk tabel `siswa_orang_tua`
--
ALTER TABLE `siswa_orang_tua`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_user` (`student_id`,`user_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indeks untuk tabel `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_student_identitas` (`partner_id`,`academic_year_id`,`unit_id`,`nama_siswa`,`kelas`),
  ADD UNIQUE KEY `uq_student_nis` (`nis_nisn`),
  ADD KEY `fk_std_ta` (`academic_year_id`),
  ADD KEY `fk_std_unit` (`unit_id`),
  ADD KEY `idx_std_nama` (`nama_siswa`),
  ADD KEY `idx_std_status` (`status_pembayaran`),
  ADD KEY `idx_std_partner` (`partner_id`),
  ADD KEY `idx_std_tgl` (`tanggal_pendaftaran`),
  ADD KEY `idx_std_program` (`program_bimbel`),
  ADD KEY `idx_std_wk` (`wali_kelas_id`),
  ADD KEY `idx_std_ws` (`wali_santri_id`),
  ADD KEY `idx_std_triwulan` (`triwulan`),
  ADD KEY `idx_std_psam` (`is_psam`);

--
-- Indeks untuk tabel `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_enroll_identitas` (`student_id`,`partner_id`,`academic_year_id`,`program_bimbel`),
  ADD KEY `fk_enroll_unit` (`unit_id`),
  ADD KEY `fk_enroll_wk` (`wali_kelas_id`),
  ADD KEY `fk_enroll_ws` (`wali_santri_id`),
  ADD KEY `idx_enroll_student` (`student_id`),
  ADD KEY `idx_enroll_partner` (`partner_id`),
  ADD KEY `idx_enroll_ta` (`academic_year_id`),
  ADD KEY `idx_enroll_status` (`status`);

--
-- Indeks untuk tabel `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_unit_status` (`status`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_status` (`status`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=887;

--
-- AUTO_INCREMENT untuk tabel `config_fee_settings`
--
ALTER TABLE `config_fee_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `fee_rules`
--
ALTER TABLE `fee_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `galeri_mitra`
--
ALTER TABLE `galeri_mitra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `jadwal_pemakaian_ruang`
--
ALTER TABLE `jadwal_pemakaian_ruang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `jenis_pencapaian`
--
ALTER TABLE `jenis_pencapaian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `kelas_detail`
--
ALTER TABLE `kelas_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `konten_lppa`
--
ALTER TABLE `konten_lppa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `partner_programs_pdfs`
--
ALTER TABLE `partner_programs_pdfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pencapaian_siswa`
--
ALTER TABLE `pencapaian_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `profit_sharing_rules`
--
ALTER TABLE `profit_sharing_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `recommenders`
--
ALTER TABLE `recommenders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=652;

--
-- AUTO_INCREMENT untuk tabel `setoran_bagi_hasil`
--
ALTER TABLE `setoran_bagi_hasil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `siswa_orang_tua`
--
ALTER TABLE `siswa_orang_tua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=462;

--
-- AUTO_INCREMENT untuk tabel `student_enrollments`
--
ALTER TABLE `student_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=512;

--
-- AUTO_INCREMENT untuk tabel `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=656;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absensi_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absensi_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `fee_rules`
--
ALTER TABLE `fee_rules`
  ADD CONSTRAINT `fee_rules_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `galeri_mitra`
--
ALTER TABLE `galeri_mitra`
  ADD CONSTRAINT `galeri_mitra_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `galeri_mitra_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `jadwal_pemakaian_ruang`
--
ALTER TABLE `jadwal_pemakaian_ruang`
  ADD CONSTRAINT `jadwal_pemakaian_ruang_ibfk_1` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_pemakaian_ruang_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_pemakaian_ruang_ibfk_3` FOREIGN KEY (`kelas_detail_id`) REFERENCES `kelas_detail` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_pemakaian_ruang_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jadwal_pemakaian_ruang_ibfk_5` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `kelas_detail`
--
ALTER TABLE `kelas_detail`
  ADD CONSTRAINT `kelas_detail_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `konten_lppa`
--
ALTER TABLE `konten_lppa`
  ADD CONSTRAINT `konten_lppa_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `partners`
--
ALTER TABLE `partners`
  ADD CONSTRAINT `fk_partner_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `partner_programs_pdfs`
--
ALTER TABLE `partner_programs_pdfs`
  ADD CONSTRAINT `partner_programs_pdfs_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pencapaian_siswa`
--
ALTER TABLE `pencapaian_siswa`
  ADD CONSTRAINT `pencapaian_siswa_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pencapaian_siswa_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pencapaian_siswa_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pencapaian_siswa_ibfk_4` FOREIGN KEY (`jenis_pencapaian_id`) REFERENCES `jenis_pencapaian` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pencapaian_siswa_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `recommenders`
--
ALTER TABLE `recommenders`
  ADD CONSTRAINT `fk_rec_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_rec_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `setoran_bagi_hasil`
--
ALTER TABLE `setoran_bagi_hasil`
  ADD CONSTRAINT `fk_setoran_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_setoran_partner` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_setoran_reviewed` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `siswa_orang_tua`
--
ALTER TABLE `siswa_orang_tua`
  ADD CONSTRAINT `siswa_orang_tua_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `siswa_orang_tua_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_std_partner` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_std_ta` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_std_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_std_wk` FOREIGN KEY (`wali_kelas_id`) REFERENCES `recommenders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_std_ws` FOREIGN KEY (`wali_santri_id`) REFERENCES `recommenders` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD CONSTRAINT `fk_enroll_partner` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enroll_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enroll_ta` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enroll_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_enroll_wk` FOREIGN KEY (`wali_kelas_id`) REFERENCES `recommenders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_enroll_ws` FOREIGN KEY (`wali_santri_id`) REFERENCES `recommenders` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
