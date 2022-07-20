-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2022 at 06:23 PM
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
  `birthday` int(11) NOT NULL,
  `businessname` varchar(50) NOT NULL,
  `occupation` varchar(50) NOT NULL,
  `comaker` varchar(50) NOT NULL,
  `comakerno` varchar(50) NOT NULL,
  `remarks` varchar(50) NOT NULL,
  `datecreated` int(11) NOT NULL,
  `isdeleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`b_id`, `picture`, `firstname`, `middlename`, `lastname`, `address`, `contactno`, `birthday`, `businessname`, `occupation`, `comaker`, `comakerno`, `remarks`, `datecreated`, `isdeleted`) VALUES
(3, 'pictures/QWERTY QWERTY QWERTY/testimage1.png', 'QWERTY', 'QWERTY', 'QWERTY', 'QWERTY', 'QWERTY', 12345, 'QWERTY', 'QWERTY', 'QWERTY', '', 'hehe', 2022, 0),
(9, 'pictures/m m m/testimageblack.png', 'm', 'm', 'm', 'm', 'm', 0, 'm', 'm', 'm', '', '', 2022, 0),
(10, 'pictures/2 2 2/testimageblack.png', '2', '2', '2', '2', '2', 2, '2', '2', '2', 'etst', '', 2022, 0),
(14, 'pictures/TEST TEST ASDF/testimageblack.png', 'tEsT', 'TEST', 'ASDF', '9', '9', 9, '9', '9', '9', '', '9', 2022, 0),
(16, 'pictures/Lee Jordan Garcia Bernardo/testimage4.png', 'Lee Jordan', 'Garcia', 'Bernardo', '1273 Pulong Camias', '+639566911727', 0, 'JAI Fair Loan', 'IT', 'TEST', '', 'ASDF', 2022, 0),
(17, 'pictures/anabel garcia bernardo/testimage5.png', 'anabel', 'garcia', 'bernardo', '654', '654', 654, '654', '546', '654', '', '654', 2022, 0),
(19, 'pictures/aNgeLo HMMM mmmhh/testimageblack.png', 'aNgeLo', 'HMMM', 'mmmhh', '654', '654', 654, '654', '654', '654', 'test comaker no.', '654', 2022, 0),
(21, 'pictures/TEST ASDF ZCSX/testimageblack.png', 'TEST', 'ASDF', 'ZCSX', 'Address test', '123456test', 0, 'asdf', 'asdfasdf', 'testcomaker', 'testcomakerno', 'test', 2022, 0),
(22, 'pictures/Willie Solabo Bernardo/testimage1.png', 'Willie', 'Solabo', 'Bernardo', '1273 Pulong Camias, San Simon, Pampanga', '09123456789', 0, 'JAI Fair Loan', 'Business', 'Anabel Garcia Bernardo', '09987654321', 'Test Remarks', 2022, 0),
(24, 'pictures/a a a/testimage3.png', 'a', 'a', 'a', 'a', 'a', 0, 'a', 'a', 'a', 'a', '', 2022, 0),
(26, '', 'LJ', 'Garcia', 'Bernardo', '5', '5', 5, '5', '5', '5', '5', '', 2022, 0),
(27, '', 'Lee', 'lkj', 'lkj', 'lkj', 'lkj', 0, 'lkj', 'lkj', 'lkj', 'lkj', '', 2022, 0);

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
  `status` varchar(50) NOT NULL,
  `c_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`l_id`, `b_id`, `amount`, `payable`, `balance`, `mode`, `term`, `interestrate`, `amortization`, `releasedate`, `duedate`, `status`, `c_id`) VALUES
(2, 16, 100000, 115000, 115000, 'daily', '6 months', 15, 1500, '2022-07-11', '2023-01-11', 'active', 0),
(3, 22, 25000, 30000, 30000, 'daily', '6 months', 15, 1000, '2022-07-15', '2023-01-15', 'active', 0),
(4, 21, 777, 1000, 1000, 'weekly', '3 months', 5, 100, '2022-07-15', '2022-10-15', 'test', 0),
(6, 3, 6500, 8950, 8950, 'daily', '4 months', 5, 300, '2022-07-19', '2022-11-19', 'active', 1),
(7, 24, 45000, 54792, 54792, 'daily', '4 months', 21.76, 596, '0000-00-00', '0000-00-00', 'Active', 0),
(9, 27, 85000, 110279, 110279, 'daily', '6 months', 29.74, 800, '0000-00-00', '0000-00-00', 'Active', 0),
(10, 9, 10000, 11777, 11777, 'daily', '3 months', 17.77, 171, '0000-00-00', '0000-00-00', 'Active', 0);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `p_id` int(11) NOT NULL,
  `b_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `type` varchar(50) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  ADD PRIMARY KEY (`l_id`),
  ADD UNIQUE KEY `b_id` (`b_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `b_id` (`b_id`),
  ADD UNIQUE KEY `c_id` (`c_id`);

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
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `collectors`
--
ALTER TABLE `collectors`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `l_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
