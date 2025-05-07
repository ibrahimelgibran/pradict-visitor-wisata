-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 07, 2025 at 11:10 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pradict-visitor-wisata`
--

-- --------------------------------------------------------

--
-- Table structure for table `pengelola`
--

CREATE TABLE `pengelola` (
  `id` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nama_wisata` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengelola_wisata`
--

CREATE TABLE `pengelola_wisata` (
  `id` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nama_wisata` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengelola_wisata`
--

INSERT INTO `pengelola_wisata` (`id`, `nama`, `nama_wisata`, `username`, `password`) VALUES
(2, 'agaspol', 'iegcode', 'bagas', '$2y$10$32iiODVAbS7OzDQXCovSOOundkYdVF.IYhVi4W9/pCxJqO9zbFyOm'),
(3, 'gibran', 'iegcodewisata', 'gibran', '$2y$10$zwGTeTL1Gx74wW/5JDs3xujj5q1Ma82HRvaOviwVTcnaUB87WuBW.');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int NOT NULL,
  `permission` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `createuser` varchar(255) DEFAULT NULL,
  `deleteuser` varchar(255) DEFAULT NULL,
  `createbid` varchar(255) DEFAULT NULL,
  `updatebid` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission`, `createuser`, `deleteuser`, `createbid`, `updatebid`) VALUES
(1, 'Superuser', '1', '1', '1', '1'),
(2, 'Admin', '1', NULL, '1', '1'),
(3, 'User', NULL, NULL, '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `ID` int NOT NULL,
  `Staffid` varchar(255) DEFAULT NULL,
  `AdminName` varchar(120) DEFAULT NULL,
  `UserName` varchar(120) DEFAULT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `MobileNumber` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Status` int NOT NULL DEFAULT '1',
  `Photo` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'avatar15.jpg',
  `Password` varchar(120) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `Staffid`, `AdminName`, `UserName`, `FirstName`, `LastName`, `MobileNumber`, `Email`, `Status`, `Photo`, `Password`, `AdminRegdate`) VALUES
(2, 'C001', 'Admin', 'admin', 'Super', 'Admin ', 'wisdom', 'useradmin', 1, 'download.png', '21232f297a57a5a743894a0e4a801fc3', '2020-07-21 10:18:39'),
(30, '1', 'User', 'pengelola', 'User', 'Pengelola', 'wisdompark', 'userpengelola', 1, '129604094-transformed.jpeg', '202cb962ac59075b964b07152d234b70', '2025-05-06 03:53:45'),
(31, '123', 'User', 'bagas', 'agaspol', 'fauzi', 'asdfgh', 'bagaspol', 1, 'avatar15.jpg', '202cb962ac59075b964b07152d234b70', '2025-05-06 08:22:07'),
(34, '1', 'User', 'bagas23', NULL, NULL, 'wisdom ugm', 'user', 0, 'avatar15.jpg', '202cb962ac59075b964b07152d234b70', '2025-05-06 15:29:03'),
(35, '12', 'Admin', 'dean', NULL, NULL, 'ugm', 'dean', 0, 'avatar15.jpg', '827ccb0eea8a706c4c34a16891f84e7b', '2025-05-06 15:44:48'),
(36, '4', 'Admin', 'supri', 'deartr', 'eedads', 'ugm1', 'supra1', 0, 'avatar15.jpg', '202cb962ac59075b964b07152d234b70', '2025-05-07 05:44:57');

-- --------------------------------------------------------

--
-- Table structure for table `tblvisitor`
--

CREATE TABLE `tblvisitor` (
  `ID` int NOT NULL,
  `FullName` varchar(120) DEFAULT NULL,
  `Email` varchar(120) DEFAULT NULL,
  `MobileNumber` bigint DEFAULT NULL,
  `Address` varchar(250) DEFAULT NULL,
  `WhomtoMeet` varchar(120) DEFAULT NULL,
  `Deptartment` varchar(120) DEFAULT NULL,
  `ReasontoMeet` varchar(120) DEFAULT NULL,
  `EnterDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `remark` varchar(255) DEFAULT NULL,
  `outtime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblvisitor`
--

INSERT INTO `tblvisitor` (`ID`, `FullName`, `Email`, `MobileNumber`, `Address`, `WhomtoMeet`, `Deptartment`, `ReasontoMeet`, `EnterDate`, `remark`, `outtime`) VALUES
(1, 'asdawd', 'agaspol333@gmail.com', 124343142, 'Indonesia', 'awdddas', 'asdawdwd', 'awddawd', '2025-05-06 08:28:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tourism_data`
--

CREATE TABLE `tourism_data` (
  `ID` int NOT NULL,
  `NamaWisata` varchar(100) NOT NULL,
  `JumlahPengunjung` int NOT NULL,
  `Pendapatan` decimal(15,2) NOT NULL,
  `SewaGedung` decimal(15,2) NOT NULL,
  `RentangWaktu` varchar(50) NOT NULL,
  `Tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourism_data`
--

INSERT INTO `tourism_data` (`ID`, `NamaWisata`, `JumlahPengunjung`, `Pendapatan`, `SewaGedung`, `RentangWaktu`, `Tanggal`) VALUES
(1, 'iegcode', 12, '1233333.00', '213242.00', 'Mingguan', '2025-05-22'),
(2, 'iegcodewisata', 24, '43422432.00', '234444.00', 'Harian', '2025-05-01'),
(3, 'dawd', 24, '2342342.00', '2312222.00', 'Bulanan', '2025-05-07'),
(4, 'WISDOM', 32, '2345554254.00', '324232.00', 'Bulanan', '2025-05-02'),
(5, 'PAGAR ALAM', 455, '8000000.00', '5000000.00', 'Bulanan', '2025-06-01'),
(6, 'UGM', 800, '9000000.00', '400000.00', 'Bulanan', '2025-07-31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pengelola`
--
ALTER TABLE `pengelola`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengelola_wisata`
--
ALTER TABLE `pengelola_wisata`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblvisitor`
--
ALTER TABLE `tblvisitor`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tourism_data`
--
ALTER TABLE `tourism_data`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pengelola`
--
ALTER TABLE `pengelola`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengelola_wisata`
--
ALTER TABLE `pengelola_wisata`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tblvisitor`
--
ALTER TABLE `tblvisitor`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tourism_data`
--
ALTER TABLE `tourism_data`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
