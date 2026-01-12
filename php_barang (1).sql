-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 07, 2026 at 05:19 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_barang`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `harga` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `kategori` varchar(50) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `id_kategori` int DEFAULT NULL,
  `stok_minimum` int DEFAULT '5'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `nama`, `harga`, `jumlah`, `kategori`, `gambar`, `id_kategori`, `stok_minimum`) VALUES
(13, 'Sprite', 6000, 30, 'minuman', '691e19b5a75ee.jpg', NULL, 5),
(14, 'Mejikom', 350000, 45, 'perlengkapan', '691e1a861dd7e.jpeg', NULL, 5),
(15, 'Gudang garam', 25000, 39, 'rokok', '691e1b07012f7.jpg', NULL, 5),
(16, 'Indomie goreng', 2500, 978, 'makanan', '691e7bc20166a.png', NULL, 5),
(17, 'Teko', 25000, 0, 'perlengkapan', '691f0198adc8b.webp', NULL, 5),
(18, 'Silverqueen', 30000, 999, 'makanan', '691f49d202875.png', NULL, 5),
(19, 'DairyMilk', 20000, 66, 'makanan', '691f56ca3bb5b.webp', NULL, 5),
(20, 'Teh Pucuk', 7000, 2, 'minuman', '691f573ada444.jpeg', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`) VALUES
(1, 'Minuman'),
(2, 'Makanan'),
(3, 'Perlengkapan Rumah'),
(4, 'Rokok'),
(6, 'Sepatu');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `nama_pelanggan` varchar(100) DEFAULT NULL,
  `subtotal` int NOT NULL,
  `total_harga` int DEFAULT NULL,
  `diskon` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `tanggal`, `nama_pelanggan`, `subtotal`, `total_harga`, `diskon`) VALUES
(1, '2025-11-19 16:51:11', NULL, 0, 12000, 0),
(2, '2025-11-19 16:51:40', NULL, 0, 12000, 0),
(3, '2025-11-19 16:51:54', NULL, 0, 12000, 0),
(4, '2025-11-19 16:52:45', NULL, 0, 12000, 0),
(5, '2025-11-19 16:52:52', NULL, 0, 0, 0),
(6, '2025-11-19 18:54:28', NULL, 0, 12000, 0),
(7, '2025-11-19 19:12:49', NULL, 0, 12000, 0),
(8, '2025-11-19 19:40:24', NULL, 0, 350000, 0),
(9, '2025-11-19 19:40:57', NULL, 0, 25000, 0),
(10, '2025-11-19 19:41:17', NULL, 0, 6000, 0),
(11, '2025-11-20 01:32:57', NULL, 0, 25000, 0),
(12, '2025-11-20 01:33:21', NULL, 0, 12000, 0),
(13, '2025-11-20 01:41:14', NULL, 0, 25000, 0),
(14, '2025-11-20 01:42:06', NULL, 0, 6000, 0),
(15, '2025-11-20 01:42:32', NULL, 0, 50000, 0),
(16, '2025-11-20 02:03:50', NULL, 0, 18000, 0),
(17, '2025-11-20 02:07:40', NULL, 0, 25000, 0),
(18, '2025-11-20 02:08:47', NULL, 0, 6000, 0),
(19, '2025-11-20 02:15:15', NULL, 0, 12000, 0),
(20, '2025-11-20 02:17:17', NULL, 0, 12000, 0),
(21, '2025-11-20 02:21:51', NULL, 0, 6000, 0),
(22, '2025-11-20 02:26:56', NULL, 0, 2500, 0),
(23, '2025-11-20 04:28:46', NULL, 0, 25000, 0),
(24, '2025-11-20 06:46:51', NULL, 0, 12000, 0),
(25, '2025-11-20 07:18:50', NULL, 0, 120000, 0),
(26, '2025-11-20 10:46:32', NULL, 0, 25000, 0),
(27, '2025-11-20 11:55:32', NULL, 0, 25000, 0),
(28, '2025-11-20 11:56:00', NULL, 0, 25000, 0),
(29, '2025-11-20 12:05:14', NULL, 0, 2500, 0),
(30, '2025-11-20 12:52:45', NULL, 0, 37000, 0),
(31, '2025-11-20 12:53:08', NULL, 0, 12000, 0),
(32, '2025-11-20 12:53:55', NULL, 0, 12000, 0),
(33, '2025-11-20 12:54:29', NULL, 0, 25000, 0),
(34, '2025-11-20 12:59:36', NULL, 0, 12000, 0),
(35, '2025-11-20 13:59:32', NULL, 0, 85000, 0),
(36, '2025-11-20 14:00:13', NULL, 0, 12000, 0),
(37, '2025-11-20 14:01:15', NULL, 0, 352500, 0),
(38, '2025-11-20 14:47:28', 'Muhamad Rizki Nurjakiah', 0, 5000, 0),
(39, '2025-11-20 14:55:11', 'zaki', 0, 2500, 0),
(40, '2025-11-20 14:57:55', 'zaki', 0, 12000, 0),
(41, '2025-11-20 15:00:42', '', 0, 2500, 0),
(42, '2025-11-20 15:00:54', '', 0, 25000, 0),
(43, '2025-11-20 15:03:49', '', 0, 2500, 0),
(44, '2025-11-20 15:11:06', 'zaki', 0, 12000, 0),
(45, '2025-11-20 15:16:36', 'agus', 0, 12000, 0),
(46, '2025-11-20 15:17:23', 'zaki', 0, 12000, 0),
(47, '2025-11-20 15:18:56', 'ale', 0, 12000, 0),
(48, '2025-11-20 15:25:56', '', 0, 12000, 0),
(49, '2025-11-20 15:26:33', '', 0, 12000, 0),
(50, '2025-11-20 15:38:16', '', 0, 12000, 0),
(51, '2025-11-20 15:40:15', '', 0, 2500, 0),
(52, '2025-11-20 15:48:00', '', 0, 25000, 0),
(53, '2025-11-20 15:51:12', '', 0, 24000, 0),
(54, '2025-11-20 15:52:36', 'zaki', 0, 25000, 0),
(55, '2025-11-20 15:58:57', '', 0, 2500, 0),
(56, '2025-11-20 16:15:04', 'zaki', 27500, 13750, 13750),
(57, '2025-11-20 16:19:18', '', 25000, 25000, 0),
(58, '2025-11-20 16:51:39', '', 27500, 13750, 13750),
(59, '2025-11-20 17:21:57', '', 392000, 196000, 196000),
(60, '2025-11-21 06:45:19', '', 25000, 25000, 0),
(61, '2025-11-21 14:55:19', 'agus', 33500, 33500, 0),
(62, '2025-11-21 14:55:50', '', 1750000, 1750000, 0),
(63, '2025-11-25 01:28:42', '', 20000, 20000, 0),
(64, '2025-11-25 02:39:37', '', 55000, 55000, 0),
(65, '2025-11-30 17:37:12', '', 75000, 75000, 0),
(66, '2025-11-30 17:39:10', '', 47500, 47500, 0),
(67, '2025-11-30 19:11:41', '', 120000, 120000, 0),
(68, '2025-12-01 02:54:12', 'restu', 75000, 52500, 22500),
(69, '2025-12-09 13:47:23', '', 328500, 328500, 0),
(70, '2025-12-09 13:48:25', '', 2500, 2500, 0),
(71, '2025-12-09 14:08:49', '', 105000, 105000, 0),
(72, '2025-12-09 14:09:24', '', 21000, 21000, 0),
(73, '2025-12-10 12:42:17', '', 25000, 25000, 0),
(74, '2025-12-10 12:42:36', '', 6000, 6000, 0),
(75, '2025-12-11 05:57:26', '', 25000, 25000, 0),
(76, '2025-12-11 05:58:19', '', 6000, 6000, 0),
(77, '2025-12-11 07:00:48', '', 6000, 6000, 0),
(78, '2025-12-11 14:12:45', '', 20000, 20000, 0),
(79, '2025-12-15 08:01:33', '', 120000, 120000, 0),
(80, '2025-12-15 08:02:18', '', 1750000, 1750000, 0),
(81, '2026-01-06 19:08:35', 'zaki', 20000, 20000, 0),
(82, '2026-01-06 19:48:53', '', 175000, 175000, 0),
(83, '2026-01-06 19:56:15', '', 20000, 20000, 0),
(84, '2026-01-06 20:17:16', '', 5000, 5000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id` int NOT NULL,
  `id_transaksi` int DEFAULT NULL,
  `id_barang` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `total` int DEFAULT NULL,
  `diskon_rp` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`id`, `id_transaksi`, `id_barang`, `jumlah`, `total`, `diskon_rp`) VALUES
(5, 8, 14, 1, 350000, 0),
(6, 9, 15, 1, 25000, 0),
(7, 10, 13, 1, 6000, 0),
(8, 11, 15, 1, 25000, 0),
(10, 13, 15, 1, 25000, 0),
(11, 14, 13, 1, 6000, 0),
(12, 15, 15, 2, 50000, 0),
(14, 16, 13, 1, 6000, 0),
(15, 17, 15, 1, 25000, 0),
(16, 18, 13, 1, 6000, 0),
(19, 21, 13, 1, 6000, 0),
(20, 22, 16, 1, 2500, 0),
(21, 23, 15, 1, 25000, 0),
(24, 26, 15, 1, 25000, 0),
(25, 27, 17, 1, 25000, 0),
(26, 28, 17, 1, 25000, 0),
(27, 29, 16, 1, 2500, 0),
(29, 30, 15, 1, 25000, 0),
(32, 33, 15, 1, 25000, 0),
(35, 35, 15, 1, 25000, 0),
(37, 37, 14, 1, 350000, 0),
(38, 37, 16, 1, 2500, 0),
(39, 38, 16, 2, 5000, 0),
(40, 39, 16, 1, 2500, 0),
(42, 41, 16, 1, 2500, 0),
(43, 42, 15, 1, 25000, 0),
(44, 43, 16, 1, 2500, 0),
(52, 51, 16, 1, 2500, 0),
(53, 52, 15, 1, 25000, 0),
(55, 54, 17, 1, 25000, 0),
(56, 55, 16, 1, 2500, 0),
(57, 56, 15, 1, 25000, 0),
(58, 56, 16, 1, 2500, 0),
(59, 57, 15, 1, 25000, 0),
(60, 58, 16, 1, 2500, 0),
(61, 58, 15, 1, 25000, 0),
(63, 59, 16, 2, 5000, 0),
(64, 59, 15, 1, 25000, 0),
(65, 59, 14, 1, 350000, 0),
(66, 60, 15, 1, 25000, 0),
(67, 61, 15, 1, 25000, 0),
(68, 61, 16, 1, 2500, 0),
(69, 61, 13, 1, 6000, 0),
(70, 62, 14, 5, 1750000, 0),
(71, 63, 19, 1, 20000, 0),
(72, 64, 18, 1, 30000, 0),
(73, 64, 17, 1, 25000, 0),
(74, 65, 15, 3, 75000, 0),
(75, 66, 19, 2, 40000, 0),
(76, 66, 16, 3, 7500, 0),
(77, 67, 19, 6, 120000, 0),
(78, 68, 15, 3, 75000, 0),
(79, 69, 16, 1, 2500, 0),
(80, 69, 13, 1, 6000, 0),
(81, 69, 19, 16, 320000, 0),
(82, 70, 16, 1, 2500, 0),
(83, 71, 20, 15, 105000, 0),
(84, 72, 20, 3, 21000, 0),
(85, 73, 15, 1, 25000, 0),
(86, 74, 13, 1, 6000, 0),
(87, 75, 15, 1, 25000, 0),
(88, 76, 13, 1, 6000, 0),
(89, 77, 13, 1, 6000, 0),
(90, 78, 19, 1, 20000, 0),
(91, 79, 19, 6, 120000, 0),
(92, 80, 14, 5, 1750000, 0),
(93, 81, 19, 1, 20000, 0),
(94, 82, 15, 1, 25000, 0),
(95, 82, 17, 6, 150000, 0),
(96, 83, 19, 1, 20000, 0),
(97, 84, 16, 2, 5000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Muhamad Rijki Nurjakiah', 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'admin'),
(3, 'agus', 'kasir1', '5f4dcc3b5aa765d61d8327deb882cf99', 'kasir');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_detail_ibfk_1` (`id_transaksi`),
  ADD KEY `transaksi_detail_ibfk_2` (`id_barang`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_detail_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
