-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2024 at 09:24 AM
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
-- Database: `ukk2024_raihan_203`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `ID_KATEGORI` varchar(255) NOT NULL,
  `NAMA_KATEGORI` char(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`ID_KATEGORI`, `NAMA_KATEGORI`) VALUES
('2', 'Puyer'),
('3', 'Tablet'),
('4', 'Kapsul'),
('43de80e3-5a99-4476-a46c-3cdb4bdf184a', 'Sirup');

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `KODE_OBAT` varchar(255) NOT NULL,
  `GAMBAR` varchar(255) NOT NULL,
  `ID_KATEGORI` varchar(255) DEFAULT NULL,
  `NAMA_OBAT` varchar(35) DEFAULT NULL,
  `HARGA` int(11) DEFAULT NULL,
  `KETERANGAN` text DEFAULT NULL,
  `STOK` int(11) DEFAULT NULL,
  `EXP` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obat`
--

INSERT INTO `obat` (`KODE_OBAT`, `GAMBAR`, `ID_KATEGORI`, `NAMA_OBAT`, `HARGA`, `KETERANGAN`, `STOK`, `EXP`) VALUES
('348fc6f5-8441-45c4-8035-f276628b28a5', 'dexamethasone.jpg', '3', 'dexamethasone', 2300, 'obat pilek', 12, '2024-02-14'),
('48e036f9-70e4-4f28-ba3d-271a760906d3', 'CTM.png', '3', 'CTM', 250000, 'YNTKTS', 27, '2026-10-12'),
('998bdafc-1292-4102-b201-60221b7b73cd', 'puyer.png', '2', 'puyer', 5000, 'obat pusing paling manjur untuk programmer', 13, '2024-02-13');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `ID_PELANGGAN` varchar(255) NOT NULL,
  `NAMA_PELANGGAN` varchar(40) DEFAULT NULL,
  `USERNAME` varchar(30) DEFAULT NULL,
  `PASSWORD` varchar(30) DEFAULT NULL,
  `ALAMAT` text DEFAULT NULL,
  `ROLE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`ID_PELANGGAN`, `NAMA_PELANGGAN`, `USERNAME`, `PASSWORD`, `ALAMAT`, `ROLE`) VALUES
('316612d6-edf9-45b6-bb23-bf977c595ee5', 'Asep Karburator Mber', 'Asep', '123', 'Dimana Mana', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `ID_PEMBAYARAN` varchar(255) NOT NULL,
  `NAMA_PEMBAYARAN` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`ID_PEMBAYARAN`, `NAMA_PEMBAYARAN`) VALUES
('1', 'Cash'),
('2', 'Transfer'),
('3', 'Kredit');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `ID_PENJUALAN` varchar(255) NOT NULL,
  `ID_PELANGGAN` varchar(255) DEFAULT NULL,
  `KODE_OBAT` varchar(255) DEFAULT NULL,
  `ID_PEMBAYARAN` varchar(255) DEFAULT NULL,
  `ID_USER` varchar(255) DEFAULT NULL,
  `TANGGAL` date DEFAULT NULL,
  `JUMLAH` int(11) DEFAULT NULL,
  `TOTAL` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`ID_PENJUALAN`, `ID_PELANGGAN`, `KODE_OBAT`, `ID_PEMBAYARAN`, `ID_USER`, `TANGGAL`, `JUMLAH`, `TOTAL`) VALUES
('0d515fb0-531b-483f-94a3-f8157b6f5fcf', '316612d6-edf9-45b6-bb23-bf977c595ee5', '998bdafc-1292-4102-b201-60221b7b73cd', '1', '2', '2024-02-13', 3, 15000),
('0f5573e1-4ace-4e34-a4da-7f8190870420', '316612d6-edf9-45b6-bb23-bf977c595ee5', '348fc6f5-8441-45c4-8035-f276628b28a5', '3', '1', '2024-02-13', 4, 9200),
('55a174b1-3f00-4cbe-930d-6dc31b708575', '316612d6-edf9-45b6-bb23-bf977c595ee5', '348fc6f5-8441-45c4-8035-f276628b28a5', '1', '1', '2024-02-13', 1, 2300),
('69a6c21d-9506-4846-b912-bc58b51f183c', '316612d6-edf9-45b6-bb23-bf977c595ee5', '348fc6f5-8441-45c4-8035-f276628b28a5', '1', '1', '2024-02-13', 3, 6900),
('71ad0bd7-060a-4161-9b8b-3a955f5e9e22', '316612d6-edf9-45b6-bb23-bf977c595ee5', '348fc6f5-8441-45c4-8035-f276628b28a5', '1', '1', '2024-02-13', 3, 6900),
('7ca4b9af-36b2-49fb-a6e4-16bc1a6b7e88', '316612d6-edf9-45b6-bb23-bf977c595ee5', '48e036f9-70e4-4f28-ba3d-271a760906d3', '1', '1', '2024-02-13', 1, 250000),
('8162a3e5-a305-4418-9eaf-9beccc528fd2', '316612d6-edf9-45b6-bb23-bf977c595ee5', '348fc6f5-8441-45c4-8035-f276628b28a5', '3', '1', '2024-02-13', 2, 4600),
('90e75c11-38d2-45e7-a936-a55e56b24b4d', '316612d6-edf9-45b6-bb23-bf977c595ee5', '48e036f9-70e4-4f28-ba3d-271a760906d3', '1', '1', '2024-02-13', 1, 250000),
('ac168e98-a55a-4969-b35d-e1985618c9dc', '316612d6-edf9-45b6-bb23-bf977c595ee5', '348fc6f5-8441-45c4-8035-f276628b28a5', '3', '1', '2024-02-13', 3, 6900),
('ac9ccb97-0af1-43c1-9df6-b98bd6b5fa29', '316612d6-edf9-45b6-bb23-bf977c595ee5', '348fc6f5-8441-45c4-8035-f276628b28a5', '3', '1', '2024-02-13', 5, 11500),
('b467c1bd-9986-4111-a914-da3779e7b72a', '316612d6-edf9-45b6-bb23-bf977c595ee5', '348fc6f5-8441-45c4-8035-f276628b28a5', '1', '1', '2024-02-13', 1, 2300),
('cbc4681e-447c-446a-9323-ab5bc8d37f45', '316612d6-edf9-45b6-bb23-bf977c595ee5', '348fc6f5-8441-45c4-8035-f276628b28a5', '1', '1', '2024-02-13', 3, 6900);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID_USER` varchar(255) NOT NULL,
  `NAMA_USER` varchar(30) DEFAULT NULL,
  `USERNAME` varchar(30) DEFAULT NULL,
  `PASSWORD` varchar(30) DEFAULT NULL,
  `ROLE` varchar(30) DEFAULT NULL,
  `ALAMAT` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID_USER`, `NAMA_USER`, `USERNAME`, `PASSWORD`, `ROLE`, `ALAMAT`) VALUES
('1', 'ADMIN', 'admin', '123', 'admin', 'Kemirahan'),
('2', 'PETUGAS', 'petugas', '123', 'petugas', 'Mojolangu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`ID_KATEGORI`);

--
-- Indexes for table `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`KODE_OBAT`),
  ADD KEY `FK_RELATIONSHIP_5` (`ID_KATEGORI`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`ID_PELANGGAN`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`ID_PEMBAYARAN`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`ID_PENJUALAN`),
  ADD KEY `FK_RELATIONSHIP_1` (`ID_PELANGGAN`),
  ADD KEY `FK_RELATIONSHIP_2` (`ID_PEMBAYARAN`),
  ADD KEY `FK_RELATIONSHIP_3` (`ID_USER`),
  ADD KEY `FK_RELATIONSHIP_4` (`KODE_OBAT`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID_USER`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `obat`
--
ALTER TABLE `obat`
  ADD CONSTRAINT `FK_RELATIONSHIP_5` FOREIGN KEY (`ID_KATEGORI`) REFERENCES `kategori` (`ID_KATEGORI`);

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `FK_RELATIONSHIP_1` FOREIGN KEY (`ID_PELANGGAN`) REFERENCES `pelanggan` (`ID_PELANGGAN`),
  ADD CONSTRAINT `FK_RELATIONSHIP_2` FOREIGN KEY (`ID_PEMBAYARAN`) REFERENCES `pembayaran` (`ID_PEMBAYARAN`),
  ADD CONSTRAINT `FK_RELATIONSHIP_3` FOREIGN KEY (`ID_USER`) REFERENCES `user` (`ID_USER`),
  ADD CONSTRAINT `FK_RELATIONSHIP_4` FOREIGN KEY (`KODE_OBAT`) REFERENCES `obat` (`KODE_OBAT`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
