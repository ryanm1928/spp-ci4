-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 05:17 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spp`
--

-- --------------------------------------------------------

--
-- Table structure for table `approval_laporan_bulanan`
--

CREATE TABLE `approval_laporan_bulanan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `laporan_bulanan_id` bigint(20) UNSIGNED NOT NULL,
  `approver_user_id` int(10) UNSIGNED DEFAULT NULL,
  `role_approval` enum('kepala_sekolah','bendahara') NOT NULL COMMENT 'bendahara memakai user dengan role guru',
  `status_approval` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `tanggal_approval` datetime DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_activation_attempts`
--

CREATE TABLE `auth_activation_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_groups`
--

CREATE TABLE `auth_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_groups`
--

INSERT INTO `auth_groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator sistem'),
(2, 'kepala_sekolah', 'Kepala sekolah'),
(3, 'guru', 'Guru');

-- --------------------------------------------------------

--
-- Table structure for table `auth_groups_permissions`
--

CREATE TABLE `auth_groups_permissions` (
  `group_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `permission_id` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_groups_users`
--

CREATE TABLE `auth_groups_users` (
  `group_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_groups_users`
--

INSERT INTO `auth_groups_users` (`group_id`, `user_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6);

-- --------------------------------------------------------

--
-- Table structure for table `auth_logins`
--

CREATE TABLE `auth_logins` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `date` datetime NOT NULL,
  `success` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_logins`
--

INSERT INTO `auth_logins` (`id`, `ip_address`, `email`, `user_id`, `date`, `success`) VALUES
(1, '::1', 'admin@sekolah.com', 1, '2026-04-02 14:47:44', 1),
(2, '::1', 'guru@sekolah.com', 3, '2026-04-02 14:49:24', 1),
(3, '::1', 'admin@sekolah.com', 1, '2026-04-02 14:50:03', 1),
(4, '::1', 'guru1@sekolah.com', 4, '2026-04-02 14:50:22', 1),
(5, '::1', 'guru1@sekolah.com', 4, '2026-04-02 15:11:02', 1),
(6, '::1', 'kepsek@sekolah.com', 2, '2026-04-02 15:30:40', 1),
(7, '::1', 'admin@sekolah.com', 1, '2026-04-02 15:32:50', 1),
(8, '::1', 'guru@sekolah.com', 3, '2026-04-03 15:16:33', 1),
(9, '::1', 'guru1@sekolah.com', 4, '2026-04-03 15:17:16', 1),
(10, '::1', 'admin@sekolah.com', 1, '2026-04-03 15:18:33', 1),
(11, '::1', 'admin@sekolah.com', 1, '2026-04-04 04:16:21', 1),
(12, '::1', 'guru1@sekolah.com', 4, '2026-04-04 04:26:22', 1),
(13, '::1', 'kepsek@sekolah.com', 2, '2026-04-04 04:28:46', 1),
(14, '::1', 'kepsek@sekolah.com', 2, '2026-04-04 04:39:16', 1),
(15, '::1', 'admin@sekolah.com', 1, '2026-04-05 13:45:32', 1),
(16, '::1', 'guru@sekolah.com', 3, '2026-04-06 03:16:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `auth_permissions`
--

CREATE TABLE `auth_permissions` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_reset_attempts`
--

CREATE TABLE `auth_reset_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `selector` varchar(255) NOT NULL,
  `hashedValidator` varchar(255) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_users_permissions`
--

CREATE TABLE `auth_users_permissions` (
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `permission_id` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kas_sekolah`
--

CREATE TABLE `kas_sekolah` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_kas` varchar(100) NOT NULL,
  `nomor_rekening` varchar(100) DEFAULT NULL,
  `saldo_awal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `saldo_berjalan` decimal(14,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kas_sekolah`
--

INSERT INTO `kas_sekolah` (`id`, `nama_kas`, `nomor_rekening`, `saldo_awal`, `saldo_berjalan`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Saldo TK Kartini', NULL, '0.00', '0.00', 1, '2026-04-02 14:35:49', '2026-04-02 14:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `deskripsi`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'TK A', 'Kelas TK A', '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL),
(2, 'TK B', 'Kelas TK B', '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kelas_tahun_ajaran`
--

CREATE TABLE `kelas_tahun_ajaran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kelas_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `wali_kelas_user_id` int(10) UNSIGNED DEFAULT NULL,
  `kuota_siswa` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas_tahun_ajaran`
--

INSERT INTO `kelas_tahun_ajaran` (`id`, `kelas_id`, `tahun_ajaran_id`, `wali_kelas_user_id`, `kuota_siswa`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 4, 25, '2026-04-02 14:47:21', '2026-04-02 15:13:25', NULL),
(2, 2, 1, 5, 25, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `laporan_bulanan_keuangan`
--

CREATE TABLE `laporan_bulanan_keuangan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bulan` tinyint(3) UNSIGNED NOT NULL,
  `tahun` smallint(5) UNSIGNED NOT NULL,
  `saldo_awal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_pemasukan` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_pengeluaran` decimal(14,2) NOT NULL DEFAULT 0.00,
  `saldo_akhir` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status_laporan` enum('draft','menunggu_approval','disetujui','ditolak') NOT NULL DEFAULT 'draft',
  `dibuat_oleh_user_id` int(10) UNSIGNED DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2017-11-20-223112', 'App\\Database\\Migrations\\CreateAuthTables', 'default', 'App', 1775140548, 1),
(2, '2017-11-20-223112', 'Myth\\Auth\\Database\\Migrations\\CreateAuthTables', 'default', 'Myth\\Auth', 1775140548, 1),
(3, '2026-03-25-112400', 'App\\Database\\Migrations\\CreateSppModuleTables', 'default', 'App', 1775140549, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran_spp`
--

CREATE TABLE `pembayaran_spp` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tagihan_spp_id` bigint(20) UNSIGNED NOT NULL,
  `kode_pembayaran` varchar(50) NOT NULL,
  `tanggal_bayar` datetime NOT NULL,
  `jumlah_bayar` decimal(12,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` enum('tunai','transfer','qris','lainnya') NOT NULL DEFAULT 'tunai',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `dicatat_oleh_user_id` int(10) UNSIGNED DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status_pembayaran_record` enum('active','void') NOT NULL DEFAULT 'active',
  `void_reason` varchar(255) DEFAULT NULL,
  `voided_at` datetime DEFAULT NULL,
  `voided_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `edited_at` datetime DEFAULT NULL,
  `edited_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `wa_notif_status` enum('belum_dikirim','dibuka','terkirim') NOT NULL DEFAULT 'belum_dikirim',
  `wa_notif_opened_at` datetime DEFAULT NULL,
  `wa_notif_sent_at` datetime DEFAULT NULL,
  `wa_notif_sent_by_user_id` int(10) UNSIGNED DEFAULT NULL,
  `wa_notif_phone` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran_spp`
--

INSERT INTO `pembayaran_spp` (`id`, `tagihan_spp_id`, `kode_pembayaran`, `tanggal_bayar`, `jumlah_bayar`, `metode_pembayaran`, `bukti_pembayaran`, `dicatat_oleh_user_id`, `keterangan`, `status_pembayaran_record`, `void_reason`, `voided_at`, `voided_by_user_id`, `edited_at`, `edited_by_user_id`, `created_at`, `updated_at`, `wa_notif_status`, `wa_notif_opened_at`, `wa_notif_sent_at`, `wa_notif_sent_by_user_id`, `wa_notif_phone`) VALUES
(1, 1, 'SPP-20260402145101-70B2CA', '2026-04-02 14:50:00', '80000.00', 'tunai', NULL, 4, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-04-02 14:51:01', '2026-04-02 14:51:01', 'belum_dikirim', NULL, NULL, NULL, NULL),
(2, 2, 'SPP-20260402145148-3F414F', '2026-04-02 14:51:00', '80000.00', 'tunai', NULL, 4, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-04-02 14:51:48', '2026-04-02 14:51:48', 'belum_dikirim', NULL, NULL, NULL, NULL),
(3, 3, 'SPP-20260402145156-02D064', '2026-04-02 14:51:00', '80000.00', 'tunai', NULL, 4, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-04-02 14:51:56', '2026-04-02 14:51:56', 'belum_dikirim', NULL, NULL, NULL, NULL),
(4, 4, 'SPP-20260402151202-25C86F', '2026-04-02 15:11:00', '80000.00', 'tunai', NULL, 4, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-04-02 15:12:02', '2026-04-02 15:24:44', 'terkirim', '2026-04-02 15:24:44', '2026-04-02 15:24:44', 4, '6287816305848');

-- --------------------------------------------------------

--
-- Table structure for table `saldo_spp_bulanan`
--

CREATE TABLE `saldo_spp_bulanan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `bulan` tinyint(3) UNSIGNED NOT NULL,
  `tahun` smallint(5) UNSIGNED NOT NULL,
  `nama_periode` varchar(50) DEFAULT NULL,
  `saldo_awal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_masuk` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_keluar` decimal(14,2) NOT NULL DEFAULT 0.00,
  `saldo_akhir` decimal(14,2) NOT NULL DEFAULT 0.00,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saldo_spp_bulanan`
--

INSERT INTO `saldo_spp_bulanan` (`id`, `tahun_ajaran_id`, `bulan`, `tahun`, `nama_periode`, `saldo_awal`, `total_masuk`, `total_keluar`, `saldo_akhir`, `is_locked`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 2025, 'Juli 2025', '0.00', '320000.00', '0.00', '320000.00', 0, '2026-04-02 14:51:01', '2026-04-02 15:12:02');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nis` varchar(30) NOT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL COMMENT 'L = Laki-laki, P = Perempuan',
  `kelas_tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `nama_orang_tua` varchar(150) NOT NULL,
  `nomor_hp_orang_tua` varchar(25) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `gambar_siswa` varchar(255) DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nis`, `nama_siswa`, `jenis_kelamin`, `kelas_tahun_ajaran_id`, `nama_orang_tua`, `nomor_hp_orang_tua`, `alamat`, `gambar_siswa`, `status_aktif`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SIS001', 'Ahmad Fauzan', 'L', 1, 'Bapak Rahmat', '081234567801', 'Jl. Melati No. 1', NULL, 1, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL),
(2, 'SIS002', 'Siti Aisyah', 'P', 1, 'Ibu Nurhayati', '081234567802', 'Jl. Kenanga No. 2', NULL, 1, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL),
(3, 'SIS003', 'Dewi Kartika', 'P', 1, 'Bapak Hendra', '081234567803', 'Jl. Flamboyan No. 3', NULL, 1, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL),
(4, 'SIS004', 'Budi Santoso', 'L', 2, 'Bapak Santoso', '081234567804', 'Jl. Mawar No. 4', NULL, 1, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL),
(5, 'SIS005', 'Citra Lestari', 'P', 2, 'Ibu Lestari', '081234567805', 'Jl. Anggrek No. 5', NULL, 1, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL),
(6, 'SIS006', 'Eko Prasetyo', 'L', 2, 'Ibu Rina', '081234567806', 'Jl. Dahlia No. 6', NULL, 1, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL),
(7, '12345', 'Mohamad Riyan', 'L', 1, 'Mugiarto', '087816305848', 'Test', NULL, 1, '2026-04-02 15:10:49', '2026-04-02 15:13:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tagihan_spp`
--

CREATE TABLE `tagihan_spp` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `kelas_tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `bulan` tinyint(3) UNSIGNED NOT NULL,
  `tahun` smallint(5) UNSIGNED NOT NULL,
  `nominal_tagihan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `nominal_terbayar` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `status_pembayaran` enum('belum_bayar','sebagian','lunas') NOT NULL DEFAULT 'belum_bayar',
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tagihan_spp`
--

INSERT INTO `tagihan_spp` (`id`, `siswa_id`, `kelas_tahun_ajaran_id`, `tahun_ajaran_id`, `bulan`, `tahun`, `nominal_tagihan`, `nominal_terbayar`, `tanggal_jatuh_tempo`, `status_pembayaran`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 7, 2025, '80000.00', '80000.00', '2025-07-31', 'lunas', NULL, '2026-04-02 14:51:01', '2026-04-02 14:51:01'),
(2, 3, 1, 1, 7, 2025, '80000.00', '80000.00', '2025-07-31', 'lunas', NULL, '2026-04-02 14:51:48', '2026-04-02 14:51:48'),
(3, 2, 1, 1, 7, 2025, '80000.00', '80000.00', '2025-07-31', 'lunas', NULL, '2026-04-02 14:51:56', '2026-04-02 14:51:56'),
(4, 7, 1, 1, 7, 2025, '80000.00', '80000.00', '2025-07-31', 'lunas', NULL, '2026-04-02 15:12:02', '2026-04-02 15:12:02');

-- --------------------------------------------------------

--
-- Table structure for table `tahun_ajaran`
--

CREATE TABLE `tahun_ajaran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_tahun_ajaran` varchar(20) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `nominal_spp` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tahun_ajaran`
--

INSERT INTO `tahun_ajaran` (`id`, `nama_tahun_ajaran`, `tanggal_mulai`, `tanggal_selesai`, `nominal_spp`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2025/2026', '2025-07-01', '2026-06-30', '80000.00', 1, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_kas`
--

CREATE TABLE `transaksi_kas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kas_sekolah_id` bigint(20) UNSIGNED NOT NULL,
  `pembayaran_spp_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `jenis_transaksi` enum('debit','kredit') NOT NULL COMMENT 'debit = pemasukan, kredit = pengeluaran',
  `sumber_transaksi` enum('spp','operasional','pemasukan_lain','pengeluaran_lain') NOT NULL DEFAULT 'operasional',
  `kategori` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `nominal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `saldo_sebelum` decimal(14,2) NOT NULL DEFAULT 0.00,
  `saldo_sesudah` decimal(14,2) NOT NULL DEFAULT 0.00,
  `dibuat_oleh_user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_spp_bulanan`
--

CREATE TABLE `transaksi_spp_bulanan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `saldo_spp_bulanan_id` bigint(20) UNSIGNED NOT NULL,
  `pembayaran_spp_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `jenis_transaksi` enum('debit','kredit') NOT NULL DEFAULT 'debit',
  `sumber_transaksi` enum('pembayaran_spp','penyesuaian','pengembalian') NOT NULL DEFAULT 'pembayaran_spp',
  `kategori` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `nominal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `saldo_sebelum` decimal(14,2) NOT NULL DEFAULT 0.00,
  `saldo_sesudah` decimal(14,2) NOT NULL DEFAULT 0.00,
  `dibuat_oleh_user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_spp_bulanan`
--

INSERT INTO `transaksi_spp_bulanan` (`id`, `saldo_spp_bulanan_id`, `pembayaran_spp_id`, `tanggal_transaksi`, `jenis_transaksi`, `sumber_transaksi`, `kategori`, `deskripsi`, `nominal`, `saldo_sebelum`, `saldo_sesudah`, `dibuat_oleh_user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-04-02 14:50:00', 'debit', 'pembayaran_spp', 'Pembayaran SPP', 'Pembayaran SPP siswa Ahmad Fauzan (SIS001)', '80000.00', '0.00', '80000.00', 4, '2026-04-02 14:51:01', '2026-04-02 14:51:01'),
(2, 1, 2, '2026-04-02 14:51:00', 'debit', 'pembayaran_spp', 'Pembayaran SPP', 'Pembayaran SPP siswa Dewi Kartika (SIS003)', '80000.00', '80000.00', '160000.00', 4, '2026-04-02 14:51:48', '2026-04-02 14:51:48'),
(3, 1, 3, '2026-04-02 14:51:00', 'debit', 'pembayaran_spp', 'Pembayaran SPP', 'Pembayaran SPP siswa Siti Aisyah (SIS002)', '80000.00', '160000.00', '240000.00', 4, '2026-04-02 14:51:56', '2026-04-02 14:51:56'),
(4, 1, 4, '2026-04-02 15:11:00', 'debit', 'pembayaran_spp', 'Pembayaran SPP', 'Pembayaran SPP siswa Mohamad Riyan (12345)', '80000.00', '240000.00', '320000.00', 4, '2026-04-02 15:12:02', '2026-04-02 15:12:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `reset_hash` varchar(255) DEFAULT NULL,
  `reset_at` datetime DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `activate_hash` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_message` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `force_pass_reset` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `profile_photo`, `password_hash`, `reset_hash`, `reset_at`, `reset_expires`, `activate_hash`, `status`, `status_message`, `active`, `force_pass_reset`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin@sekolah.com', 'admin', NULL, '$2y$10$JjF3DqLixk9IcvLiZj4pfOFrdm9AvdMmTRjvwk9Rudjq5vpB17UkK', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-04-02 14:47:09', '2026-04-02 14:47:09', NULL),
(2, 'kepsek@sekolah.com', 'kepsek', NULL, '$2y$10$oED.fOeK3ythzXIbEC7ZMOrIC8PkezmLkDFSStkWfWhI4pzfzyIyq', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-04-02 14:47:09', '2026-04-02 14:47:09', NULL),
(3, 'guru@sekolah.com', 'guru', NULL, '$2y$10$ssx65ERw.2cCdy3QSJtL0u3NPnE8uLJOptRUkIqvUOu8gJUMTKso.', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-04-02 14:47:09', '2026-04-02 14:47:09', NULL),
(4, 'guru1@sekolah.com', 'guru1', NULL, '$2y$10$K2JPolC4yIXAzAzAl4vBf.eDyS6S5WQFaa/rkPA2T33WV3DZLNnta', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL),
(5, 'guru2@sekolah.com', 'guru2', NULL, '$2y$10$5pFWuKMLSPp4IuPC.hZxeubYwChQYQbUUr1GP4WifiK2eG.z273Ea', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL),
(6, 'guru3@sekolah.com', 'guru3', NULL, '$2y$10$31daBVAGJyOrgxdTmvNN0uRhb1FoJ/ULPYUd0zttZTLdJF9mtO756', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-04-02 14:47:21', '2026-04-02 14:47:21', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval_laporan_bulanan`
--
ALTER TABLE `approval_laporan_bulanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `approval_laporan_role_unique` (`laporan_bulanan_id`,`role_approval`),
  ADD KEY `approval_laporan_bulanan_approver_user_id_foreign` (`approver_user_id`);

--
-- Indexes for table `auth_activation_attempts`
--
ALTER TABLE `auth_activation_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_groups`
--
ALTER TABLE `auth_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_groups_permissions`
--
ALTER TABLE `auth_groups_permissions`
  ADD KEY `auth_groups_permissions_permission_id_foreign` (`permission_id`),
  ADD KEY `group_id_permission_id` (`group_id`,`permission_id`);

--
-- Indexes for table `auth_groups_users`
--
ALTER TABLE `auth_groups_users`
  ADD KEY `auth_groups_users_user_id_foreign` (`user_id`),
  ADD KEY `group_id_user_id` (`group_id`,`user_id`);

--
-- Indexes for table `auth_logins`
--
ALTER TABLE `auth_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `auth_permissions`
--
ALTER TABLE `auth_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_reset_attempts`
--
ALTER TABLE `auth_reset_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auth_tokens_user_id_foreign` (`user_id`),
  ADD KEY `selector` (`selector`);

--
-- Indexes for table `auth_users_permissions`
--
ALTER TABLE `auth_users_permissions`
  ADD KEY `auth_users_permissions_permission_id_foreign` (`permission_id`),
  ADD KEY `user_id_permission_id` (`user_id`,`permission_id`);

--
-- Indexes for table `kas_sekolah`
--
ALTER TABLE `kas_sekolah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kas` (`nama_kas`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kelas` (`nama_kelas`);

--
-- Indexes for table `kelas_tahun_ajaran`
--
ALTER TABLE `kelas_tahun_ajaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kelas_tahun_ajaran_unique` (`kelas_id`,`tahun_ajaran_id`),
  ADD KEY `kelas_tahun_ajaran_tahun_ajaran_id_foreign` (`tahun_ajaran_id`),
  ADD KEY `kelas_tahun_ajaran_wali_kelas_user_id_foreign` (`wali_kelas_user_id`),
  ADD KEY `kelas_id_tahun_ajaran_id` (`kelas_id`,`tahun_ajaran_id`);

--
-- Indexes for table `laporan_bulanan_keuangan`
--
ALTER TABLE `laporan_bulanan_keuangan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `laporan_bulanan_unique` (`bulan`,`tahun`),
  ADD KEY `laporan_bulanan_keuangan_dibuat_oleh_user_id_foreign` (`dibuat_oleh_user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran_spp`
--
ALTER TABLE `pembayaran_spp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pembayaran` (`kode_pembayaran`),
  ADD KEY `pembayaran_spp_dicatat_oleh_user_id_foreign` (`dicatat_oleh_user_id`),
  ADD KEY `tagihan_spp_id_tanggal_bayar` (`tagihan_spp_id`,`tanggal_bayar`),
  ADD KEY `idx_pembayaran_spp_status` (`status_pembayaran_record`),
  ADD KEY `idx_pembayaran_spp_voided_by` (`voided_by_user_id`),
  ADD KEY `idx_pembayaran_spp_edited_by` (`edited_by_user_id`),
  ADD KEY `idx_pembayaran_spp_wa_notif_status` (`wa_notif_status`),
  ADD KEY `idx_pembayaran_spp_wa_notif_sent_by` (`wa_notif_sent_by_user_id`);

--
-- Indexes for table `saldo_spp_bulanan`
--
ALTER TABLE `saldo_spp_bulanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `saldo_spp_bulanan_unique` (`tahun_ajaran_id`,`bulan`,`tahun`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD KEY `kelas_tahun_ajaran_id` (`kelas_tahun_ajaran_id`);

--
-- Indexes for table `tagihan_spp`
--
ALTER TABLE `tagihan_spp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tagihan_spp_unique` (`siswa_id`,`tahun_ajaran_id`,`bulan`,`tahun`),
  ADD KEY `tagihan_spp_kelas_tahun_ajaran_id_foreign` (`kelas_tahun_ajaran_id`),
  ADD KEY `tagihan_spp_tahun_ajaran_id_foreign` (`tahun_ajaran_id`),
  ADD KEY `siswa_id_tahun_ajaran_id` (`siswa_id`,`tahun_ajaran_id`);

--
-- Indexes for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_tahun_ajaran` (`nama_tahun_ajaran`);

--
-- Indexes for table `transaksi_kas`
--
ALTER TABLE `transaksi_kas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_kas_dibuat_oleh_user_id_foreign` (`dibuat_oleh_user_id`),
  ADD KEY `kas_sekolah_id_tanggal_transaksi` (`kas_sekolah_id`,`tanggal_transaksi`),
  ADD KEY `pembayaran_spp_id` (`pembayaran_spp_id`);

--
-- Indexes for table `transaksi_spp_bulanan`
--
ALTER TABLE `transaksi_spp_bulanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saldo_spp_bulanan_id` (`saldo_spp_bulanan_id`),
  ADD KEY `pembayaran_spp_id` (`pembayaran_spp_id`),
  ADD KEY `dibuat_oleh_user_id` (`dibuat_oleh_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approval_laporan_bulanan`
--
ALTER TABLE `approval_laporan_bulanan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_activation_attempts`
--
ALTER TABLE `auth_activation_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_groups`
--
ALTER TABLE `auth_groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `auth_logins`
--
ALTER TABLE `auth_logins`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `auth_permissions`
--
ALTER TABLE `auth_permissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_reset_attempts`
--
ALTER TABLE `auth_reset_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kas_sekolah`
--
ALTER TABLE `kas_sekolah`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kelas_tahun_ajaran`
--
ALTER TABLE `kelas_tahun_ajaran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `laporan_bulanan_keuangan`
--
ALTER TABLE `laporan_bulanan_keuangan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembayaran_spp`
--
ALTER TABLE `pembayaran_spp`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `saldo_spp_bulanan`
--
ALTER TABLE `saldo_spp_bulanan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tagihan_spp`
--
ALTER TABLE `tagihan_spp`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaksi_kas`
--
ALTER TABLE `transaksi_kas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi_spp_bulanan`
--
ALTER TABLE `transaksi_spp_bulanan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_laporan_bulanan`
--
ALTER TABLE `approval_laporan_bulanan`
  ADD CONSTRAINT `approval_laporan_bulanan_approver_user_id_foreign` FOREIGN KEY (`approver_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `approval_laporan_bulanan_laporan_bulanan_id_foreign` FOREIGN KEY (`laporan_bulanan_id`) REFERENCES `laporan_bulanan_keuangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `auth_groups_permissions`
--
ALTER TABLE `auth_groups_permissions`
  ADD CONSTRAINT `auth_groups_permissions_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_groups_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_groups_users`
--
ALTER TABLE `auth_groups_users`
  ADD CONSTRAINT `auth_groups_users_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_groups_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_users_permissions`
--
ALTER TABLE `auth_users_permissions`
  ADD CONSTRAINT `auth_users_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_users_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kelas_tahun_ajaran`
--
ALTER TABLE `kelas_tahun_ajaran`
  ADD CONSTRAINT `kelas_tahun_ajaran_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_tahun_ajaran_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_tahun_ajaran_wali_kelas_user_id_foreign` FOREIGN KEY (`wali_kelas_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `laporan_bulanan_keuangan`
--
ALTER TABLE `laporan_bulanan_keuangan`
  ADD CONSTRAINT `laporan_bulanan_keuangan_dibuat_oleh_user_id_foreign` FOREIGN KEY (`dibuat_oleh_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran_spp`
--
ALTER TABLE `pembayaran_spp`
  ADD CONSTRAINT `pembayaran_spp_dicatat_oleh_user_id_foreign` FOREIGN KEY (`dicatat_oleh_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_spp_edited_by_user_id_foreign` FOREIGN KEY (`edited_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `pembayaran_spp_tagihan_spp_id_foreign` FOREIGN KEY (`tagihan_spp_id`) REFERENCES `tagihan_spp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_spp_voided_by_user_id_foreign` FOREIGN KEY (`voided_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `pembayaran_spp_wa_notif_sent_by_user_id_foreign` FOREIGN KEY (`wa_notif_sent_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `saldo_spp_bulanan`
--
ALTER TABLE `saldo_spp_bulanan`
  ADD CONSTRAINT `saldo_spp_bulanan_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_kelas_tahun_ajaran_id_foreign` FOREIGN KEY (`kelas_tahun_ajaran_id`) REFERENCES `kelas_tahun_ajaran` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tagihan_spp`
--
ALTER TABLE `tagihan_spp`
  ADD CONSTRAINT `tagihan_spp_kelas_tahun_ajaran_id_foreign` FOREIGN KEY (`kelas_tahun_ajaran_id`) REFERENCES `kelas_tahun_ajaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tagihan_spp_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tagihan_spp_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_kas`
--
ALTER TABLE `transaksi_kas`
  ADD CONSTRAINT `transaksi_kas_dibuat_oleh_user_id_foreign` FOREIGN KEY (`dibuat_oleh_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_kas_kas_sekolah_id_foreign` FOREIGN KEY (`kas_sekolah_id`) REFERENCES `kas_sekolah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_kas_pembayaran_spp_id_foreign` FOREIGN KEY (`pembayaran_spp_id`) REFERENCES `pembayaran_spp` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_spp_bulanan`
--
ALTER TABLE `transaksi_spp_bulanan`
  ADD CONSTRAINT `transaksi_spp_bulanan_dibuat_oleh_user_id_foreign` FOREIGN KEY (`dibuat_oleh_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `transaksi_spp_bulanan_pembayaran_spp_id_foreign` FOREIGN KEY (`pembayaran_spp_id`) REFERENCES `pembayaran_spp` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_spp_bulanan_saldo_spp_bulanan_id_foreign` FOREIGN KEY (`saldo_spp_bulanan_id`) REFERENCES `saldo_spp_bulanan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
