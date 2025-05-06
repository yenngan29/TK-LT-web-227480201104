-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2025 at 02:11 PM
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
-- Database: `quanlybanhang`
--

-- --------------------------------------------------------

--
-- Table structure for table `hanghoa`
--

CREATE TABLE `hanghoa` (
  `mahang` varchar(4) NOT NULL COMMENT 'Mã hàng',
  `tenhang` varchar(40) NOT NULL COMMENT 'Tên hàng',
  `mansx` varchar(2) NOT NULL COMMENT 'Mã nhà sx',
  `namsx` int(11) NOT NULL COMMENT 'Năm sản xuất',
  `gia` int(11) NOT NULL COMMENT 'Giá'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hanghoa`
--

INSERT INTO `hanghoa` (`mahang`, `tenhang`, `mansx`, `namsx`, `gia`) VALUES
('AS01', 'Asus TUF', 'AS', 2017, 520),
('AS02', 'Asus Vivobook', 'AS', 2017, 580),
('DE01', 'Dell Vostro', 'DE', 2015, 650),
('DE02', 'Dell Inspiron', 'DE', 2015, 550),
('LE01', 'Lenovo Thinkpad', 'LE', 2019, 750),
('LE02', 'Lenovo Legion', 'LE', 2020, 540),
('LE03', 'Lenovo Yoga', 'LE', 2020, 600),
('TO01', 'Toshiba Satellite', 'TO', 2014, 630);

-- --------------------------------------------------------

--
-- Table structure for table `hoadon`
--

CREATE TABLE `hoadon` (
  `mahd` varchar(3) NOT NULL COMMENT 'Mã hoá đơn',
  `makh` varchar(3) NOT NULL COMMENT 'Mã khách hàng',
  `mahang` varchar(4) NOT NULL COMMENT 'Mã hàng',
  `soluong` int(11) NOT NULL COMMENT 'Số lượng',
  `thanhtien` int(11) NOT NULL COMMENT 'Thành tiền'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hoadon`
--

INSERT INTO `hoadon` (`mahd`, `makh`, `mahang`, `soluong`, `thanhtien`) VALUES
('002', 'K00', 'LE01', 5, 0),
('002', 'K00', 'LE02', 3, 0),
('003', 'K00', 'TO01', 1, 0),
('004', 'K00', 'DE01', 2, 0),
('005', 'K00', 'AS01', 4, 0),
('005', 'K00', 'LE01', 6, 0),
('005', 'K00', 'LE02', 8, 0),
('006', 'K00', 'AS01', 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `makh` varchar(4) NOT NULL COMMENT 'Mã khách hàng',
  `tenkh` varchar(30) NOT NULL COMMENT 'Tên khách hàng',
  `namsinh` int(2) NOT NULL COMMENT 'Năm sinh',
  `dienthoai` varchar(10) NOT NULL COMMENT 'Điện thoại',
  `diachi` varchar(50) NOT NULL COMMENT 'Địa chỉ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`makh`, `tenkh`, `namsinh`, `dienthoai`, `diachi`) VALUES
('K001', 'Nguyễn Thị Lan', 1980, '0913123456', 'Bạc Liêu'),
('K002', 'Ngô Thanh Minh', 1985, '0913024357', 'Vĩnh Lợi'),
('K003', 'Lâm Văn Thanh', 1979, '0913123457', 'Giá Rai'),
('K004', 'Dương Thu Thuỷ', 1995, '0913024358', 'Hồng Dân'),
('K005', 'Nguyễn Thị Xuân', 1987, '0903223344', 'Phước Long'),
('K006', 'Huỳnh Mẫn Đạt', 1975, '0989122112', 'Bạc Liêu'),
('K007', 'Lâm Hoài Bảo', 1990, '0912131415', 'Bạc Liêu'),
('K008', 'Hồ Trung Tín', 2000, '0944119999', 'Phước Long'),
('K009', 'Trương Xuân Thi', 2001, '0909000111', 'Vĩnh Lợi'),
('K010', 'Ngô Văn Nam', 2001, '0909000112', 'Giá Rai');

-- --------------------------------------------------------

--
-- Table structure for table `nhasanxuat`
--

CREATE TABLE `nhasanxuat` (
  `mansx` varchar(2) NOT NULL COMMENT 'Mã nhà sản xuấ',
  `tennsx` varchar(40) NOT NULL COMMENT 'Tên nhà sản xuất',
  `quocgia` varchar(20) NOT NULL COMMENT 'Quốc gia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nhasanxuat`
--

INSERT INTO `nhasanxuat` (`mansx`, `tennsx`, `quocgia`) VALUES
('AS', 'ASUS', 'Dai Loan'),
('DE', 'DELL', 'Hoa Ky'),
('LE', 'LENOVO', 'Trung Quoc'),
('TO', 'TOSHIBA', 'Nhat Ban');

-- --------------------------------------------------------

--
-- Table structure for table `ton kho`
--

CREATE TABLE `ton kho` (
  `mahang` varchar(4) NOT NULL COMMENT 'Mã hàng',
  `tenhang` varchar(40) NOT NULL COMMENT 'Tên hàng',
  `mansx` varchar(2) NOT NULL COMMENT 'Mã nhà sx',
  `namsx` int(11) NOT NULL COMMENT 'Năm sản xuất',
  `gia` int(11) NOT NULL COMMENT 'Giá bán',
  `soluong` int(11) NOT NULL COMMENT 'Số lượng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ton kho`
--

INSERT INTO `ton kho` (`mahang`, `tenhang`, `mansx`, `namsx`, `gia`, `soluong`) VALUES
('DE01', 'Dell Inspiron', 'DE', 2015, 650, 20),
('DE02', 'Dell Latitude', 'DE', 2015, 550, 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hanghoa`
--
ALTER TABLE `hanghoa`
  ADD PRIMARY KEY (`mahang`);

--
-- Indexes for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`mahd`,`makh`,`mahang`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`makh`);

--
-- Indexes for table `nhasanxuat`
--
ALTER TABLE `nhasanxuat`
  ADD PRIMARY KEY (`mansx`);

--
-- Indexes for table `ton kho`
--
ALTER TABLE `ton kho`
  ADD PRIMARY KEY (`mahang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
