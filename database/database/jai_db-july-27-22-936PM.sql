-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2022 at 03:36 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jai_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `b_id` int(11) NOT NULL,
  `picture` varchar(100) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `contactno` varchar(50) NOT NULL,
  `birthday` date NOT NULL,
  `businessname` varchar(50) NOT NULL,
  `occupation` varchar(50) NOT NULL,
  `comaker` varchar(50) NOT NULL,
  `comakerno` varchar(50) NOT NULL,
  `remarks` varchar(50) NOT NULL,
  `datecreated` date NOT NULL,
  `activeloan` tinyint(1) NOT NULL,
  `isdeleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`b_id`, `picture`, `firstname`, `middlename`, `lastname`, `address`, `contactno`, `birthday`, `businessname`, `occupation`, `comaker`, `comakerno`, `remarks`, `datecreated`, `activeloan`, `isdeleted`) VALUES
(1, 'pictures/LJ Garcia Bernardo/testimage1.png', 'Lj', 'Garcia', 'Bernardo', '1273 Pulong Camias, San Jose, San Simon, Pampanga', '+63 956-691-1727', '1994-06-25', 'Jai', 'It', 'Willie Bernardo', '+63 995-048-5682', '', '2022-07-25', 1, 0),
(2, 'pictures/Heart Fernando Alvarado/testimage2.png', 'Heart', 'Fernando', 'Alvarado', 'Asdf', '16047249826', '1994-07-01', 'Aaa', 'Bbb', 'Lj Bernardo', '+63 956-691-1727', '', '2022-07-25', 0, 0),
(3, '', 'Willie', 'Solabo', 'Bernardo', '1273 PCamias', '+63 995-048-5682', '1962-05-06', 'JAI', 'Owner', 'Anabel Garcia Bernardo', '+63 995-048-5685', '', '2022-07-26', 1, 0),
(4, 'pictures/TEST TEST TEST/testimageblack.png', 'TEST', 'TEST', 'TEST', 'TEST', '+63 999-999-9999', '0000-00-00', 'test', 'test', 'test', '+63 234-234-2342', '', '2022-07-26', 0, 0),
(5, 'pictures/Anabel Garcia Bernardo/11070190_915757645111780_2021431170813868029_n.jpg', 'Anabel', 'Garcia', 'Bernardo', 'San Simon', '+63 123-456-7898', '1963-12-22', 'JAI', 'Mother', 'Ivan Bernardo', '+63 216-549-8798', '', '2022-07-27', 1, 0),
(6, 'C:\\xampp\\htdocs\\JAI/public/assets/borrower-picture-placeholder.jpg', 'zzz', 'zzz', 'zzz', 'zzz', '+63 ', '0000-00-00', 'zzz', 'zzz', 'zzz', '+63 ', '', '2022-07-27', 0, 0),
(7, '/public/assets/borrower-picture-placeholder.jpg', 'aaa', 'aaa', 'aaa', 'aaa', '+63 ', '0000-00-00', 'aaa', 'aaa', 'aaa', '+63 ', '', '2022-07-27', 0, 0),
(8, 'assets/borrower-picture-placeholder.jpg', 'ddd', 'ddd', 'ddd', 'ddd', '+63 ', '0000-00-00', 'dd', 'dd', 'dd', '+63 ', '', '2022-07-27', 0, 0),
(9, 'pictures/ffff fff fff/testimage1.png', 'ffff', 'fff', 'fff', 'ffff', '+63 ', '0000-00-00', 'fff', 'fff', 'fff', '+63 ', '', '2022-07-27', 0, 0),
(10, 'assets/borrower-picture-placeholder.jpg', 'ooo', 'ooo', 'ooo', 'ooo', '+63 ', '0000-00-00', 'ooo', 'ooo', 'ooo', '+63 ', '', '2022-07-27', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `collectors`
--

CREATE TABLE `collectors` (
  `c_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `collectors`
--

INSERT INTO `collectors` (`c_id`, `firstname`, `middlename`, `lastname`) VALUES
(1, 'King', 'Docena', 'Cruz'),
(2, 'Carl', '', 'Corpuz');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `l_id` int(11) NOT NULL,
  `b_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `payable` float NOT NULL,
  `balance` float NOT NULL,
  `mode` varchar(50) NOT NULL,
  `term` varchar(50) NOT NULL,
  `interestrate` float NOT NULL,
  `amortization` float NOT NULL,
  `releasedate` date NOT NULL,
  `duedate` date NOT NULL,
  `paymentsmade` int(11) NOT NULL,
  `passes` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `activeloan` tinyint(1) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`l_id`, `b_id`, `amount`, `payable`, `balance`, `mode`, `term`, `interestrate`, `amortization`, `releasedate`, `duedate`, `paymentsmade`, `passes`, `c_id`, `activeloan`, `status`) VALUES
(1, 1, 35000, 42616, 0, 'daily', '4 months', 21.76, 464, '0000-00-00', '0000-00-00', 3, 1, 1, 0, 'Finished'),
(2, 2, 25000, 31438, -16, 'daily', '5 months', 25.753, 274, '0000-00-00', '0000-00-00', 8, 1, 1, 0, 'Finished'),
(4, 2, 10000, 11777, 0, 'daily', '3 months', 17.77, 171, '0000-00-00', '0000-00-00', 3, 0, 2, 0, 'Finished'),
(5, 2, 5000, 5507, 0, 'daily', '1 month', 10.14, 240, '0000-00-00', '0000-00-00', 2, 0, 2, 0, 'Finished'),
(6, 4, 10000, 12974, 0, 'daily', '6 months', 29.74, 95, '0000-00-00', '0000-00-00', 2, 0, 1, 0, 'Finished'),
(7, 2, 20000, 25150, -750, 'daily', '5 months', 25.753, 219, '0000-00-00', '0000-00-00', 3, 0, 2, 0, 'Finished'),
(8, 4, 15000, 18264, 0, 'daily', '4 months', 21.76, 200, '0000-00-00', '0000-00-00', 2, 0, 1, 0, 'Finished'),
(9, 3, 10000, 11777, 0, 'daily', '3 months', 17.77, 171, '0000-00-00', '0000-00-00', 1, 0, 2, 0, 'Finished'),
(10, 1, 80000, 103792, 103039, 'daily', '6 months', 29.74, 753, '0000-00-00', '0000-00-00', 1, 1, 2, 1, 'Active'),
(11, 3, 50000, 58885, 58885, 'daily', '3 months', 17.77, 854, '0000-00-00', '0000-00-00', 0, 0, 1, 1, 'Active'),
(12, 2, 50000, 58885, 0, 'daily', '3 months', 17.77, 854, '0000-00-00', '0000-00-00', 2, 1, 1, 0, 'Finished'),
(13, 5, 100000, 108140, 108140, 'daily', '1 month', 8.14, 4702, '0000-00-00', '0000-00-00', 0, 1, 2, 1, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `p_id` int(11) NOT NULL,
  `b_id` int(11) NOT NULL,
  `l_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `passamount` float NOT NULL,
  `type` varchar(50) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`p_id`, `b_id`, `l_id`, `c_id`, `amount`, `passamount`, `type`, `date`) VALUES
(1, 1, 1, 1, 464, 0, 'GCash', '2022-07-25'),
(2, 1, 1, 1, 464, 0, 'GCash', '2022-07-26'),
(3, 1, 1, 1, 0, 0, 'Pass', '2022-07-27'),
(4, 2, 2, 1, 500, 0, 'Cash', '2022-07-25'),
(5, 2, 2, 1, 0, 0, 'Pass', '2022-07-27'),
(6, 2, 2, 1, 30000, 0, 'Cash', '2022-07-28'),
(7, 2, 2, 2, 800, 0, 'Cash', '2022-07-29'),
(8, 2, 2, 1, 150, 0, 'Cash', '2022-08-01'),
(9, 2, 2, 1, 1, 0, 'GCash', '2022-08-02'),
(10, 2, 2, 1, 1, 0, 'GCash', '2022-08-03'),
(11, 2, 2, 1, 1, 0, 'GCash', '2022-08-04'),
(12, 2, 4, 2, 171, 0, 'GCash', '2022-07-27'),
(13, 2, 4, 2, 11000, 0, 'GCash', '2022-07-30'),
(14, 2, 4, 2, 606, 0, 'Cash', '2022-07-31'),
(15, 2, 5, 2, 240, 0, 'Cash', '2022-07-26'),
(16, 2, 5, 2, 5267, 0, 'Cash', '2022-07-27'),
(17, 4, 6, 1, 10000, 0, 'Cash', '2022-07-26'),
(18, 4, 6, 1, 2974, 0, 'Cash', '2022-07-27'),
(19, 2, 7, 2, 250, 0, 'GCash', '2022-07-26'),
(20, 2, 7, 2, 500, 0, 'GCash', '2022-07-27'),
(21, 2, 7, 1, 25150, 0, 'GCash', '2022-07-28'),
(22, 4, 8, 1, 200, 0, 'GCash', '2022-07-27'),
(23, 1, 1, 1, 41688, 0, 'GCash', '2022-07-27'),
(24, 4, 8, 1, 18064, 0, 'GCash', '2022-07-27'),
(25, 3, 9, 2, 11777, 0, 'Cash', '2022-07-27'),
(26, 2, 12, 1, 222, 0, 'GCash', '2022-07-27'),
(27, 2, 12, 1, 0, 0, 'Pass', '2022-07-27'),
(28, 2, 12, 1, 58663, 0, 'GCash', '2022-07-26'),
(29, 5, 13, 2, 0, 0, 'Pass', '2022-07-27'),
(30, 1, 10, 2, 0, 753, 'Pass', '2022-07-27'),
(31, 1, 10, 2, 753, 0, 'Cash', '2022-07-27');

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `r_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `payable` float NOT NULL,
  `amortization` float NOT NULL,
  `interestrate` float NOT NULL,
  `mode` varchar(100) NOT NULL,
  `term` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rates`
--

INSERT INTO `rates` (`r_id`, `amount`, `payable`, `amortization`, `interestrate`, `mode`, `term`) VALUES
(1, 5000, 5507, 240, 10.14, 'daily', '1 month'),
(2, 8000, 8692, 378, 8.65, 'Daily', '1 Month'),
(3, 10000, 10814, 471, 8.14, 'daily', '1 month'),
(4, 12000, 13037, 567, 8.64, 'daily', '1 month'),
(5, 14000, 15160, 660, 8.29, 'daily', '1 month'),
(6, 15000, 16221, 706, 8.14, 'daily', '1 month'),
(7, 20000, 21628, 941, 8.14, 'daily', '1 month'),
(8, 25000, 27035, 1176, 8.14, 'daily', '1 month'),
(9, 30000, 32442, 1411, 8.14, 'daily', '1 month'),
(10, 35000, 37849, 1646, 8.14, 'daily', '1 month'),
(11, 40000, 43256, 1881, 8.14, 'daily', '1 month'),
(12, 45000, 48663, 2116, 8.14, 'daily', '1 month'),
(13, 50000, 54070, 2351, 8.14, 'daily', '1 month'),
(14, 55000, 59477, 2586, 8.14, 'daily', '1 month'),
(15, 60000, 64884, 2822, 8.14, 'daily', '1 month'),
(16, 65000, 70291, 3057, 8.14, 'daily', '1 month'),
(17, 70000, 75698, 3292, 8.14, 'daily', '1 month'),
(18, 75000, 81105, 3527, 8.14, 'daily', '1 month'),
(19, 80000, 86512, 3762, 8.14, 'daily', '1 month'),
(20, 85000, 91919, 3997, 8.14, 'daily', '1 month'),
(21, 90000, 97326, 4232, 8.14, 'daily', '1 month'),
(22, 95000, 102733, 4467, 8.14, 'daily', '1 month'),
(23, 100000, 108140, 4702, 8.14, 'daily', '1 month'),
(24, 5000, 5749, 125, 14.98, 'daily', '2 months'),
(25, 8000, 9077, 198, 13.46, 'daily', '2 months'),
(26, 10000, 11296, 246, 12.96, 'daily', '2 months'),
(27, 12000, 13615, 296, 13.46, 'daily', '2 months'),
(28, 14000, 15835, 345, 13.11, 'daily', '2 months'),
(29, 15000, 16944, 369, 12.96, 'daily', '2 months'),
(30, 20000, 22591, 492, 12.96, 'daily', '2 months'),
(31, 25000, 28240, 614, 12.96, 'daily', '2 months'),
(32, 30000, 33887, 737, 12.96, 'daily', '2 months'),
(33, 35000, 39535, 860, 12.96, 'daily', '2 months'),
(34, 40000, 45182, 983, 12.96, 'daily', '2 months'),
(35, 45000, 50831, 1106, 12.96, 'daily', '2 months'),
(36, 50000, 56478, 1228, 12.96, 'daily', '2 months'),
(37, 55000, 62126, 1351, 12.96, 'daily', '2 months'),
(38, 60000, 67773, 1474, 12.96, 'daily', '2 months'),
(39, 65000, 73422, 1597, 12.96, 'daily', '2 months'),
(40, 70000, 79069, 1719, 12.96, 'daily', '2 months'),
(41, 75000, 84717, 1842, 12.96, 'daily', '2 months'),
(42, 80000, 90364, 1965, 12.96, 'daily', '2 months'),
(43, 85000, 96013, 2088, 12.96, 'daily', '2 months'),
(44, 90000, 101660, 2210, 12.96, 'daily', '2 months'),
(45, 95000, 107308, 2333, 12.96, 'daily', '2 months'),
(46, 100000, 112955, 2456, 12.96, 'daily', '2 months'),
(47, 5000, 5989, 87, 19.78, 'daily', '3 months'),
(48, 8000, 9462, 138, 18.275, 'daily', '3 months'),
(49, 10000, 11777, 171, 17.77, 'daily', '3 months'),
(50, 12000, 14193, 206, 18.275, 'daily', '3 months'),
(51, 14000, 16508, 240, 17.914, 'daily', '3 months'),
(52, 15000, 17666, 257, 17.773, 'daily', '3 months'),
(53, 20000, 23554, 342, 17.77, 'daily', '3 months'),
(54, 25000, 29443, 427, 17.772, 'daily', '3 months'),
(55, 30000, 35331, 513, 17.77, 'daily', '3 months'),
(56, 35000, 41220, 598, 17.77, 'daily', '3 months'),
(57, 40000, 47108, 683, 17.77, 'daily', '3 months'),
(58, 45000, 52997, 767, 17.77, 'daily', '3 months'),
(59, 50000, 58885, 854, 17.77, 'daily', '3 months'),
(60, 55000, 64774, 939, 17.77, 'daily', '3 months'),
(61, 60000, 70662, 1025, 17.77, 'daily', '3 months'),
(62, 65000, 76551, 1110, 17.77, 'daily', '3 months'),
(63, 70000, 82439, 1195, 17.77, 'daily', '3 months'),
(64, 75000, 88328, 1281, 17.77, 'daily', '3 months'),
(65, 80000, 94216, 1366, 17.77, 'daily', '3 months'),
(66, 85000, 100105, 1451, 17.77, 'daily', '3 months'),
(67, 90000, 105993, 1537, 17.77, 'daily', '3 months'),
(68, 95000, 111882, 1622, 17.77, 'daily', '3 months'),
(69, 100000, 117770, 1707, 17.77, 'daily', '3 months'),
(70, 5000, 6188, 68, 23.76, 'daily', '4 months'),
(71, 8000, 9781, 107, 22.263, 'daily', '4 months'),
(72, 10000, 12176, 133, 21.76, 'daily', '4 months'),
(73, 12000, 14672, 160, 22.267, 'daily', '4 months'),
(74, 14000, 17067, 186, 21.907, 'daily', '4 months'),
(75, 15000, 18264, 200, 21.76, 'daily', '4 months'),
(76, 20000, 24352, 270, 21.76, 'daily', '4 months'),
(77, 25000, 30440, 331, 21.76, 'daily', '4 months'),
(78, 30000, 36528, 398, 21.76, 'daily', '4 months'),
(79, 35000, 42616, 464, 21.76, 'daily', '4 months'),
(80, 40000, 48704, 530, 21.76, 'daily', '4 months'),
(81, 45000, 54792, 596, 21.76, 'daily', '4 months'),
(82, 50000, 60880, 662, 21.76, 'daily', '4 months'),
(83, 55000, 66968, 728, 21.76, 'daily', '4 months'),
(84, 60000, 73056, 795, 21.76, 'daily', '4 months'),
(85, 65000, 79144, 861, 21.76, 'daily', '4 months'),
(86, 70000, 85232, 927, 21.76, 'daily', '4 months'),
(87, 75000, 91320, 993, 21.76, 'daily', '4 months'),
(88, 80000, 97408, 1059, 21.76, 'daily', '4 months'),
(89, 85000, 103496, 1125, 21.76, 'daily', '4 months'),
(90, 90000, 109584, 1192, 21.76, 'daily', '4 months'),
(91, 95000, 115672, 1258, 21.76, 'daily', '4 months'),
(92, 100000, 121760, 1324, 21.76, 'daily', '4 months'),
(93, 5000, 6388, 56, 27.76, 'daily', '5 months'),
(94, 8000, 10100, 88, 26.25, 'daily', '5 months'),
(95, 10000, 12575, 110, 25.75, 'daily', '5 months'),
(96, 12000, 15150, 132, 26.25, 'daily', '5 months'),
(97, 14000, 17625, 154, 25.893, 'daily', '5 months'),
(98, 15000, 18863, 165, 25.753, 'daily', '5 months'),
(99, 20000, 25150, 219, 25.753, 'daily', '5 months'),
(100, 25000, 31438, 274, 25.753, 'daily', '5 months'),
(101, 30000, 37725, 329, 25.753, 'daily', '5 months'),
(102, 35000, 44013, 383, 25.753, 'daily', '5 months'),
(103, 40000, 50300, 438, 25.753, 'daily', '5 months'),
(104, 45000, 56588, 493, 25.753, 'daily', '5 months'),
(105, 50000, 62875, 547, 25.753, 'daily', '5 months'),
(106, 55000, 69163, 602, 25.753, 'daily', '5 months'),
(107, 60000, 75450, 657, 25.753, 'daily', '5 months'),
(108, 65000, 81738, 711, 25.753, 'daily', '5 months'),
(109, 70000, 88025, 766, 25.753, 'daily', '5 months'),
(110, 75000, 94313, 821, 25.753, 'daily', '5 months'),
(111, 80000, 100600, 875, 25.753, 'daily', '5 months'),
(112, 85000, 106888, 930, 25.753, 'daily', '5 months'),
(113, 90000, 113175, 985, 25.753, 'daily', '5 months'),
(114, 95000, 119463, 1039, 25.753, 'daily', '5 months'),
(115, 100000, 125750, 1094, 25.753, 'daily', '5 months'),
(116, 5000, 6587, 48, 31.74, 'daily', '6 months'),
(117, 8000, 10420, 76, 30.25, 'daily', '6 months'),
(118, 10000, 12974, 95, 29.74, 'daily', '6 months'),
(119, 12000, 15629, 114, 30.242, 'daily', '6 months'),
(120, 14000, 18184, 132, 29.886, 'daily', '6 months'),
(121, 15000, 19461, 142, 29.74, 'daily', '6 months'),
(122, 20000, 25948, 189, 29.74, 'daily', '6 months'),
(123, 25000, 32435, 236, 29.74, 'daily', '6 months'),
(124, 30000, 38922, 283, 29.74, 'daily', '6 months'),
(125, 35000, 45409, 330, 29.74, 'daily', '6 months'),
(126, 40000, 51896, 377, 29.74, 'daily', '6 months'),
(127, 45000, 58383, 424, 29.74, 'daily', '6 months'),
(128, 50000, 64870, 471, 29.74, 'daily', '6 months'),
(129, 55000, 71357, 518, 29.74, 'daily', '6 months'),
(130, 60000, 77844, 565, 29.74, 'daily', '6 months'),
(131, 65000, 84331, 612, 29.74, 'daily', '6 months'),
(132, 70000, 90818, 659, 29.74, 'daily', '6 months'),
(133, 75000, 97305, 706, 29.74, 'daily', '6 months'),
(134, 80000, 103792, 753, 29.74, 'daily', '6 months'),
(135, 85000, 110279, 800, 29.74, 'daily', '6 months'),
(136, 90000, 116766, 847, 29.74, 'daily', '6 months'),
(137, 95000, 123253, 894, 29.74, 'daily', '6 months'),
(138, 100000, 129740, 941, 29.74, 'daily', '6 months');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`b_id`);

--
-- Indexes for table `collectors`
--
ALTER TABLE `collectors`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`l_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`r_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `collectors`
--
ALTER TABLE `collectors`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `l_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
