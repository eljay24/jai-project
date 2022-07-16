-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2022 at 06:51 AM
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
  `datecreated` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`b_id`, `picture`, `firstname`, `middlename`, `lastname`, `address`, `contactno`, `birthday`, `businessname`, `occupation`, `comaker`, `comakerno`, `remarks`, `datecreated`) VALUES
(3, 'pictures/QWERTY QWERTY QWERTY/testimage1.png', 'QWERTY', 'QWERTY', 'QWERTY', 'QWERTY', 'QWERTY', 12345, 'QWERTY', 'QWERTY', 'QWERTY', '', 'hehe', 2022),
(9, 'pictures/m m m/testimageblack.png', 'm', 'm', 'm', 'm', 'm', 0, 'm', 'm', 'm', '', '', 2022),
(10, 'pictures/2 2 2/testimageblack.png', '2', '2', '2', '2', '2', 2, '2', '2', '2', 'etst', '', 2022),
(14, 'pictures/TEST TEST ASDF/testimageblack.png', 'tEsT', 'TEST', 'ASDF', '9', '9', 9, '9', '9', '9', '', '9', 2022),
(16, 'pictures/Lee Jordan Garcia Bernardo/testimage4.png', 'Lee Jordan', 'Garcia', 'Bernardo', '1273 Pulong Camias', '+639566911727', 0, 'JAI Fair Loan', 'IT', 'TEST', '', 'ASDF', 2022),
(17, 'pictures/anabel garcia bernardo/testimage5.png', 'anabel', 'garcia', 'bernardo', '654', '654', 654, '654', '546', '654', '', '654', 2022),
(19, '', 'aNgeLo', 'HMMM', 'mmmhh', '654', '654', 654, '654', '654', '654', '', '654', 2022),
(21, '', 'TEST', 'ASDF', 'ZCSX', 'Address test', '123456test', 0, 'asdf', 'asdfasdf', 'testcomaker', 'testcomakerno', 'test', 2022),
(22, 'pictures/Willie Solabo Bernardo/testimage1.png', 'Willie', 'Solabo', 'Bernardo', '1273 Pulong Camias, San Simon, Pampanga', '09123456789', 0, 'JAI Fair Loan', 'Business', 'Anabel Garcia Bernardo', '09987654321', 'Test Remarks', 2022),
(23, 'pictures/NEW1 NEW1 NEW1/testimageblack.png', 'NEW1', 'NEW1', 'NEW1', 'NEW1', 'NEW1', 0, 'NEW1', 'NEW1', 'NEW1', 'NEW1', 'NEW1', 2022);

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
(4, 21, 777, 1000, 1000, 'weekly', '3 months', 5, 100, '2022-07-15', '2022-10-15', 'test', 0);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `collectors`
--
ALTER TABLE `collectors`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `l_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
