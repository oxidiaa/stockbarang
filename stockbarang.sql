-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2025 at 12:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stockbarang`
--

-- --------------------------------------------------------

--
-- Table structure for table `keluar`
--

CREATE TABLE `keluar` (
  `idkeluar` int(11) NOT NULL,
  `idbarang` int(11) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `notabung` varchar(25) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keluar`
--

INSERT INTO `keluar` (`idkeluar`, `idbarang`, `tanggal`, `notabung`, `qty`) VALUES
(1, 2, '2025-02-11 19:32:14', 'ryan', 2),
(2, 2, '2025-02-11 19:33:08', 'rayeun', 2),
(3, 3, '2025-02-11 19:36:14', 'rayeun', 1),
(4, 3, '2025-02-11 19:48:20', 'ryan', 2),
(5, 3, '2025-02-11 19:50:02', 'rayeun', 4),
(6, 3, '2025-02-11 20:46:17', '32520', 2),
(7, 4, '2025-02-11 20:47:44', 'rayeun', 2),
(8, 4, '2025-02-12 00:54:47', 'ryan', 2),
(9, 3, '2025-02-12 00:55:01', '32520', 6),
(10, 6, '2025-02-13 16:48:21', '123', 2),
(11, 7, '2025-02-14 17:31:08', '32520', 6),
(12, 7, '2025-02-14 20:48:22', '32520', 2),
(13, 6, '2025-02-16 06:24:32', 'rayeun', 2),
(14, 7, '2025-02-16 06:24:51', 'rayeun', 1),
(15, 8, '2025-02-16 06:27:34', 'rayeun', 2);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `iduser` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`iduser`, `email`, `password`, `role`) VALUES
(1, 'ryan@gmail.com', 'ryan', 'admin'),
(5, 'qc@gmail.com', 'qc', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `masuk`
--

CREATE TABLE `masuk` (
  `idmasuk` int(11) NOT NULL,
  `idbarang` int(11) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `keterangan` varchar(25) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `masuk`
--

INSERT INTO `masuk` (`idmasuk`, `idbarang`, `tanggal`, `keterangan`, `qty`) VALUES
(1, 2, '2025-02-11 19:17:10', '32520', 0),
(2, 2, '2025-02-11 19:32:38', 'rayeun', 0),
(3, 3, '2025-02-11 19:39:45', 'ryan', 0),
(4, 3, '2025-02-11 19:41:26', 'ryan', 0),
(5, 3, '2025-02-11 19:45:45', 'ryan', 6),
(6, 3, '2025-02-11 19:46:12', 'rayeun', 22),
(7, 3, '2025-02-11 19:48:02', 'ryan', 5),
(8, 3, '2025-02-11 19:49:50', 'rayeun', 7),
(9, 3, '2025-02-11 20:46:03', 'rayeun', 5),
(10, 4, '2025-02-11 20:47:36', 'ryan', 11),
(11, 3, '2025-02-11 20:48:01', 'ryan', 12),
(12, 3, '2025-02-11 20:48:10', 'rayeun', 3),
(17, 6, '2025-02-14 20:50:04', 'rayeun', 11),
(18, 7, '2025-02-14 20:50:12', '1', 11),
(19, 8, '2025-02-16 06:27:19', 'rayeun', 5),
(20, 8, '2025-02-18 02:42:55', '123', 123);

-- --------------------------------------------------------

--
-- Table structure for table `solar_keluar`
--

CREATE TABLE `solar_keluar` (
  `id` int(11) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `forklift` varchar(100) DEFAULT NULL,
  `sebelum` float DEFAULT NULL,
  `sesudah` float DEFAULT NULL,
  `total` float DEFAULT NULL,
  `user` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `solar_keluar`
--

INSERT INTO `solar_keluar` (`id`, `tanggal`, `forklift`, `sebelum`, `sesudah`, `total`, `user`) VALUES
(9, '2025-04-10 04:48:43', 'R3', 0, 10.1, 10.1, 'Ryan'),
(10, '2025-04-10 04:49:01', 'R6', 10.1, 110.1, 100, 'Ryan'),
(11, '2025-04-10 04:49:22', 'R4', 110.1, 118, 7.9, 'Raa');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `idbarang` int(11) NOT NULL,
  `namabarang` varchar(50) NOT NULL,
  `deskripsi` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`idbarang`, `namabarang`, `deskripsi`, `stock`) VALUES
(8, 'REFFILL GAS', 'Gas area', 226),
(10, 'Solar', 'Solar', 5000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `keluar`
--
ALTER TABLE `keluar`
  ADD PRIMARY KEY (`idkeluar`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`iduser`);

--
-- Indexes for table `masuk`
--
ALTER TABLE `masuk`
  ADD PRIMARY KEY (`idmasuk`);

--
-- Indexes for table `solar_keluar`
--
ALTER TABLE `solar_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`idbarang`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `keluar`
--
ALTER TABLE `keluar`
  MODIFY `idkeluar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `masuk`
--
ALTER TABLE `masuk`
  MODIFY `idmasuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `solar_keluar`
--
ALTER TABLE `solar_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `idbarang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
