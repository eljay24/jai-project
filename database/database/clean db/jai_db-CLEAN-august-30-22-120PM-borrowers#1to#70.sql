-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2022 at 07:19 AM
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
  `address` varchar(100) NOT NULL,
  `contactno` varchar(50) NOT NULL,
  `birthday` date NOT NULL,
  `businessname` varchar(100) NOT NULL,
  `occupation` varchar(50) NOT NULL,
  `comaker` varchar(50) NOT NULL,
  `comakerno` varchar(50) NOT NULL,
  `remarks` varchar(100) NOT NULL,
  `datecreated` date NOT NULL,
  `activeloan` tinyint(1) NOT NULL,
  `isdeleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`b_id`, `picture`, `firstname`, `middlename`, `lastname`, `address`, `contactno`, `birthday`, `businessname`, `occupation`, `comaker`, `comakerno`, `remarks`, `datecreated`, `activeloan`, `isdeleted`) VALUES
(1, 'assets/icons/borrower-picture-placeholder.jpg', 'Rolen', 'Avellana', 'De Guzman', '74 Triangulo St., Sto. Nino, San Fernando, Pampanga', '+63 955-466-9500', '1971-03-24', 'Rolens Sari Sari Store', 'Business Woman', 'Jocelyn Almario', '+63 000-000-0000', '', '2022-08-28', 0, 0),
(2, 'assets/icons/borrower-picture-placeholder.jpg', 'Sheena Kariz', ' ', 'Santos', 'Sto Nino, San Simon, Pampanga', '+63 935-400-0768', '1993-12-10', 'Beth Store', 'Dole', 'Elizabeth G Santos', '+63 935-400-0768', '', '2022-08-28', 0, 0),
(3, 'assets/icons/borrower-picture-placeholder.jpg', 'Roselyn', 'Gorais', 'Tobio', 'Triangulo St., Sto. Nino, San Fernando, Pampanga', '+63 920-828-9215', '1991-02-12', 'Rose Sari Sari Store', 'Na', 'Ruth Lim Guinto', '+63 965-721-5316', '', '2022-08-28', 0, 0),
(4, 'assets/icons/borrower-picture-placeholder.jpg', 'Jocelyn', 'A', 'Almario', '78 Triangulo, Sto. Nino, San Fernando, Pampanga', '+63 936-619-1845', '1969-10-10', 'Lyn Sari Sari Store', 'Store Owner', 'Rolen De Guzman', '+63 936-619-1845', '', '2022-08-28', 0, 0),
(5, 'assets/icons/borrower-picture-placeholder.jpg', 'Reyna', 'Lizcano', 'Cortez', '282 6th St. Looban, Dolores, San Fernando, Pampanga', '+63 908-277-3894', '1963-07-01', 'Reyna Rolling Store', 'Rolling Store', 'Elena Noveno', '+63 908-277-3894', '', '2022-08-28', 0, 0),
(6, 'assets/icons/borrower-picture-placeholder.jpg', 'Elena', 'Mojal', 'Noveno', '299 6th Street Looban, Dolores, San Fernando, Pampanga', '+63 908-277-3894', '1955-08-10', 'Elena Sari Sari Store', 'Vendor', 'Reyna Cortez', '+63 908-277-3894', '', '2022-08-28', 0, 0),
(7, 'assets/icons/borrower-picture-placeholder.jpg', 'Olivia', 'Pineda', 'Alfante', '33 P. Gomez Street, San Matias, Santo Tomas, Pampanga', '+63 915-454-1984', '1970-07-01', 'Sari Sari Store', 'Business Owner', 'Marie Andrelei A Callao', '+63 927-388-5126', '', '2022-08-28', 0, 0),
(8, 'assets/icons/borrower-picture-placeholder.jpg', 'Rolando', 'G', 'Dagdag Jr', '1324 Pulong Camias, San Simon, Pampanga', '+63 910-613-2328', '2022-01-01', 'Na', 'Rolling Utility', 'Vangie Dagdag Ignacio', '+63 000-000-0000', '', '2022-08-28', 0, 0),
(9, 'assets/icons/borrower-picture-placeholder.jpg', 'Mary Jane', 'Dagdag', 'Sulla', '878 San Jose Control, San Simon, Pampanga', '+63 905-880-5987', '1979-04-17', 'Na', 'Na', 'Myrna Leones Colindon', '+63 905-880-5987', '', '2022-08-28', 0, 0),
(10, 'assets/icons/borrower-picture-placeholder.jpg', 'Erlinda', 'Real', 'Canlas', '101 L. Gomez Subdivision, San Matias, Santo Tomas, Pampanga', '+63 929-195-0174', '1950-07-11', 'Linda Sari Sari Store', 'Business Owner', 'Cristine Marie R Canlas', '+63 918-404-1809', '', '2022-08-28', 0, 0),
(11, 'assets/icons/borrower-picture-placeholder.jpg', 'Marissa', 'Ignacio', 'Dela Cruz', '0144 Be Centro Street, Balucuc, Apalit, Pampanga', '+63 946-691-4124', '1984-05-09', 'Roven Street Foods And Buko Juice', 'Vendor', 'Jocelyn Cruz Purca', '+63 946-691-4124', '', '2022-08-28', 0, 0),
(12, 'assets/icons/borrower-picture-placeholder.jpg', 'Maria Joanna', ' ', 'Manalese', '25 P. Gomez Street, San Matias, Santo Tomas, Pampanga', '+63 998-945-7908', '2002-08-14', 'Maan Store', 'Business Owner', 'Susana Laxamana Dela Cruz', '+63 943-055-8912', '', '2022-08-28', 0, 0),
(13, 'assets/icons/borrower-picture-placeholder.jpg', 'Princess Diane', 'V', 'Catangal', 'Blk. 23 Lot 20 Northville, Calulut, San Fernando, Pampanga', '+63 955-157-3272', '2001-06-02', 'Street Food', 'Vendor', 'Zenaida C Mirabel', '+63 975-706-6561', '', '2022-08-28', 0, 0),
(14, 'assets/icons/borrower-picture-placeholder.jpg', 'Analiza', 'Pineda', 'Pineda', '321 Salapungan, San Nicolas, San Fernando, Pampanga', '+63 936-272-7859', '1972-04-06', 'Analiza Sari Sari Store', 'Vendor', 'Noemi Dela Cruz Arquiza', '+63 000-000-0000', '', '2022-08-28', 0, 0),
(15, 'assets/icons/borrower-picture-placeholder.jpg', 'Remedios', 'Calaquian', 'Pineda', '33 P. Gomez Street, San Matias, Santo Tomas, Pampanga', '+63 998-478-3351', '1955-12-01', 'Rc Food Vending', 'Vendor', 'Ramona Guinto Baa', '+63 000-000-0000', '', '2022-08-28', 0, 0),
(16, 'assets/icons/borrower-picture-placeholder.jpg', 'Danilo', 'Palisoc', 'Pablo', '225 Ilang Ilang Street, San Nicolas, San Fernando, Pampanga', '+63 936-962-6432', '1961-06-30', 'Na', 'Na', 'Cristina Pablo Calalang', '+63 000-000-0000', '', '2022-08-28', 0, 0),
(17, 'assets/icons/borrower-picture-placeholder.jpg', 'Candida', 'Yamuta', 'Ignacio', 'San Juan Control, San Simon, Pampanga', '+63 926-210-2804', '1966-09-06', 'Inday Store', 'Business Owner', 'Marites E Santiago', '+63 932-633-4342', '', '2022-08-28', 0, 0),
(18, 'assets/icons/borrower-picture-placeholder.jpg', 'Elpinida', 'Gutierrez', 'Dagdag', 'Pulong Camias, San Jose, San Simon, Pampanga', '+63 936-299-1925', '1973-03-18', 'Pork Dealer', 'Pork Dealer', 'Chriselda S Estillore', '+63 000-000-0000', '', '2022-08-28', 0, 0),
(19, 'assets/icons/borrower-picture-placeholder.jpg', 'Shirley', 'Talucod', 'Torres', '1031 Dalan Baka Street, Sulipan, Apalit, Pampanga', '+63 000-000-0000', '1980-03-28', 'Shirley Store', 'Business Owner', 'Dyna Tulawe Mendoza', '+63 000-000-0000', '', '2022-08-28', 0, 0),
(20, 'assets/icons/borrower-picture-placeholder.jpg', 'Sam', 'Tullao', 'Mercado', 'Sitio Banlic, Cabalantian, Bacolor, Pampanga', '+63 949-141-4765', '1995-04-23', 'Fruits And Egg Store', 'Vendor', 'Lydia L Bagsic', '+63 926-198-0050', '', '2022-08-28', 0, 0),
(21, 'assets/icons/borrower-picture-placeholder.jpg', 'Lydia', 'Liangco', 'Bagsic', 'Sitio Banlic, Cabalantian, Bacolor, Pampanga', '+63 926-198-0050', '1962-11-26', 'Lydias Talipapa', 'Business Woman', 'Sam Mercado', '+63 949-141-4165', '', '2022-08-28', 0, 0),
(22, 'assets/icons/borrower-picture-placeholder.jpg', 'Morel Amore', 'C', 'Cosio', '0981 Control, San Jose, San Simon, Pampanga', '+63 917-452-3041', '1971-12-01', 'Amors Footwear', 'Sg Farm', 'Jonathan Simbulan Pascual', '+63 915-127-5864', '', '2022-08-28', 0, 0),
(23, 'assets/icons/borrower-picture-placeholder.jpg', 'Jennifer', 'A', 'Angeles', 'NA', '+63 000-000-0000', '2022-01-01', 'NA', 'NA', 'NA', '+63 000-000-0000', '', '2022-08-28', 0, 0),
(24, 'assets/icons/borrower-picture-placeholder.jpg', 'Chriselda', 'Santos', 'Estillore', 'Sitio Pulong Camias, San Jose, San Simon, Pampanga', '+63 945-840-7134', '1997-02-27', 'Chris Barbeque And Ihaw Ihaw', 'Vendor', 'Elpinida Dagdag', '+63 936-299-1925', '', '2022-08-28', 0, 0),
(25, 'assets/icons/borrower-picture-placeholder.jpg', 'Lourdes', 'Bugayong', 'Hina', '131 L. Gomez Subdivision, San Matias, Santo Tomas, Pampanga', '+63 905-411-3893', '1979-05-08', 'Hina Store', 'Business Owner', 'Belinda Manlapig Lingat', '+63 997-732-4818', '', '2022-08-28', 0, 0),
(26, 'assets/icons/borrower-picture-placeholder.jpg', 'Belinda', 'Manlapig', 'Lingat', '023 Santo Rosario Pau, Santo Tomas, Pampanga', '+63 997-732-4818', '1962-08-12', 'Belinda Lingat Merchandising Store', 'Vendor', 'Lourdes Bugayong Hina', '+63 905-411-3893', '', '2022-08-28', 0, 0),
(27, 'assets/icons/borrower-picture-placeholder.jpg', 'Argie', 'Sanchez', 'Bulanadi', '#36 Juliana Subdivision, San Fernando, Pampanga', '+63 929-273-7196', '1987-10-05', 'AA Store', 'Vendor', 'Demetria D Rimando', '+63 000-000-0000', '', '2022-08-29', 0, 0),
(28, 'assets/icons/borrower-picture-placeholder.jpg', 'Jocris', 'Dela Cruz', 'Carlos', '1026 Dalan Baka, Sulipan, Apalit, Pampanga', '+63 965-967-6521', '1998-11-15', 'Cristy Store', 'Businessman', 'Shirley Talucod Torres', '+63 963-739-2790', '', '2022-08-29', 0, 0),
(29, 'assets/icons/borrower-picture-placeholder.jpg', 'Charmanne', 'Gutierrez', 'Dagdag', '1331 Pulong Camias, San Jose, San Simon, Pampanga', '+63 997-700-3124', '1993-10-27', 'Maine\'s Frozen Meat', 'Business Owner', 'Chabilita Dagdag Navarro', '+63 916-863-0156', '', '2022-08-29', 0, 0),
(30, 'assets/icons/borrower-picture-placeholder.jpg', 'Chabilita', 'Dagdag', 'Navarro', '1331 Pulong Camias, San Jose, San Simon, Pampanga', '+63 916-863-0156', '1999-08-07', 'Chabilita\'s Crab Seller', 'Seller', 'Charmanne Gutierrez Dagdag', '+63 997-700-3124', '', '2022-08-29', 0, 0),
(31, 'assets/icons/borrower-picture-placeholder.jpg', 'Marlon', 'Canlas', 'Hipolito', '1193 Blue Diamond Subdivision, San Vicente, Santo Tomas, Pampanga', '+63 910-821-5042', '1981-09-16', '??? Sewer', 'Tricycle Driver', 'Sharalyn Hipolito Manalili', '+63 000-000-0000', '', '2022-08-29', 0, 0),
(32, 'assets/icons/borrower-picture-placeholder.jpg', 'Emily', 'Castro', 'Dumandan', '570 Sto. Rosario Pau, Santo Tomas, Pampanga', '+63 936-272-0711', '1967-11-02', 'Jerico Store', 'Business Owner', 'Clarissa Dumandan', '+63 000-000-0000', '', '2022-08-29', 0, 0),
(33, 'assets/icons/borrower-picture-placeholder.jpg', 'Riza', 'Cabacas', 'Policarpio', '467 Astros, Del Pilar, San Fernando, Pampanga', '+63 921-552-8236', '1980-07-28', 'Sari Sari Store', 'Business Owner', 'May Christine Calapan', '+63 905-717-2672', '', '2022-08-29', 0, 0),
(34, 'assets/icons/borrower-picture-placeholder.jpg', 'Maribel', 'Pineda', 'Lagman', '38 Sto. Rosario Pau, Santo Tomas, Pampanga', '+63 955-483-9291', '1978-09-04', 'Maribel Store', 'Store Owner', 'Edwin Mercado Quiambao', '+63 935-100-8518', '', '2022-08-29', 0, 0),
(35, 'assets/icons/borrower-picture-placeholder.jpg', 'Rebecca', 'Sunga', 'Bernardo', 'San Jose Poblacion, San Simon, Pampanga', '+63 997-106-5265', '1964-06-26', 'Mama Beck\'s Goto-Lugaw', 'Business Owner', 'Morel Amore C Cosio', '+63 917-452-3041', '', '2022-08-30', 0, 0),
(36, 'assets/icons/borrower-picture-placeholder.jpg', 'Arnel', 'Mamangun', 'Cortez', '1196 Pulong Camias, San Jose, San Simon, Pampanga', '+63 905-184-8644', '1974-04-08', 'FMTOL Motorcycle Parts and Services', 'Technician', 'Danilo Cortez Alfanta Jr', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(37, 'assets/icons/borrower-picture-placeholder.jpg', 'Vilma', 'Dela Pena', 'Mallari', '124 Sto. Nino Triangulo, San Fernando, Pampanga', '+63 932-374-0415', '1976-05-31', 'Sari Sari Store', 'Business Owner', 'Monette Manaloto Mallari', '+63 939-719-2317', '', '2022-08-30', 0, 0),
(38, 'assets/icons/borrower-picture-placeholder.jpg', 'Nieves', 'Verzola', 'Tongol', '18 Paralaya Street, San Matias, Santo Tomas, Pampanga', '+63 936-297-5505', '1961-02-09', 'Tiffany Store', 'Store Owner', 'Belinda Manlapig Lingat', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(39, 'assets/icons/borrower-picture-placeholder.jpg', 'Priscila', 'Bernarte', 'Manalang', 'Blk. 9 Lot 40 Phase 1, Northville, Calulut, San Fernando, Pampanga', '+63 956-692-6086', '1971-01-15', 'General Merchandise', 'Business Woman', 'Zenaida Catangal Mirabel', '+63 975-706-6561', '', '2022-08-30', 0, 0),
(40, 'assets/icons/borrower-picture-placeholder.jpg', 'Roselyn', 'Caporte', 'Lapaz', '436 San Juan Control, San Simon, Pampanga', '+63 956-420-0737', '1985-01-23', 'Ukay Ukay Business', 'Store Owner', 'Melissa Jamelano Mercado', '+63 968-537-4291', '', '2022-08-30', 0, 0),
(41, 'assets/icons/borrower-picture-placeholder.jpg', 'Janice', 'De Mesa', 'Dela Cruz', 'Blk. 12 Lot 2729 Summerfield San Rafael, Mexico, Pampanga', '+63 921-552-7893', '1982-10-03', 'Vegetable Wholesale and Retailer', 'Vendor', 'Jenny Joyce Vallarta Reyes', '+63 955-562-4382', '', '2022-08-30', 0, 0),
(42, 'assets/icons/borrower-picture-placeholder.jpg', 'Loren', 'Calingacion', 'Bagalawis', '478 San Juan, San Simon, Pampanga', '+63 965-294-8205', '1989-11-06', 'NA', 'Housewife', 'Emmalyn Bacudio Calingacion', '+63 963-671-0898', '', '2022-08-30', 0, 0),
(43, 'assets/icons/borrower-picture-placeholder.jpg', 'Melissa', 'Calera', 'Barandino', '22 St. P. Gomez, San Matias, Sto. Tomas, Pampanga', '+63 948-073-0602', '1978-04-23', 'Meat Products and Vegetables', 'Vendor', 'Nieves Verzola Tongol', '+63 936-297-5505', '', '2022-08-30', 0, 0),
(44, 'assets/icons/borrower-picture-placeholder.jpg', 'Rosalie', 'Mempin', 'Duenas', 'Cabio Bakal, Balucuc, Apalit, Pampanga', '+63 932-518-2857', '1980-10-04', 'Lugawan Pares', 'Vendor', 'Michelle Duenas Tolentino', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(45, 'assets/icons/borrower-picture-placeholder.jpg', 'Angelina', 'Tongol', 'Salvador', '516 San Isidro, Minalin, Pampanga', '+63 956-693-3793', '1973-09-20', 'Street Foods', 'Vendor', 'Jenny Tongol Salvador', '+63 936-248-0648', '', '2022-08-30', 0, 0),
(46, 'assets/icons/borrower-picture-placeholder.jpg', 'Leticia', 'Sanchez', 'Pamintuan', '36 Juliana Subdivision, San Fernando, Pampanga', '+63 960-600-8892', '1971-05-25', 'Fresh Peanut Ube Chicharon', 'Retail Dealer', 'Argie Sanchez Bulanadi', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(47, 'assets/icons/borrower-picture-placeholder.jpg', 'Maria Gilla', 'Guevarra', 'Romero', 'Blk. 105 Lot 9 Northville 14, Calulut, San Fernando, Pampanga', '+63 948-284-0559', '1976-01-05', 'Sari Sari Store', 'Online Seller', 'Elsa B Quizon', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(48, 'assets/icons/borrower-picture-placeholder.jpg', 'Emelita', 'Onas', 'Abrenica', 'San Juan Control, San Simon, Pampanga', '+63 968-698-6844', '1976-03-04', 'Denmark Store', 'Vendor', 'Melissa Jamelano Mercado', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(49, 'assets/icons/borrower-picture-placeholder.jpg', 'Gilbert', 'Ganzon', 'Enriquez', 'Purok 4 Bangcal, Guagua, Pampanga', '+63 926-507-8449', '1989-06-21', 'Gilbert Ganzon Enriquez Sari Sari Store', 'Store Owner', 'Roselyn Gorais Tobio', '+63 920-828-9215', '', '2022-08-30', 0, 0),
(50, 'assets/icons/borrower-picture-placeholder.jpg', 'Artemio', 'NA', 'De Mesa', '???????????????????????', '+63 000-000-0000', '2022-01-01', 'NA', 'NA', 'NA', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(51, 'assets/icons/borrower-picture-placeholder.jpg', 'Alvin', 'Greon', 'Dagdag', 'Pulong San Jose, San Simon, Pampanga', '+63 910-613-2328', '2002-08-05', 'Eggs Dealer', 'Business Owner', 'Maria Magdalena Bacani Alvaro', '+63 997-155-9562', '', '2022-08-30', 0, 0),
(52, 'assets/icons/borrower-picture-placeholder.jpg', 'Precy', 'Ducha', 'Didase', 'New Public Market, San Fernando, Pampanga', '+63 950-572-1140', '1981-01-02', 'Bansiong Bakery', 'Business Owner', 'Analyn Carino Alday', '+63 936-677-7982', '', '2022-08-30', 0, 0),
(53, 'assets/icons/borrower-picture-placeholder.jpg', 'Jhonedel', 'Dagdag', 'Lopez', 'San Jose Village, San Simon, Pampanga', '+63 956-087-5574', '1989-07-04', 'Alimango/Betting Station', 'Business Owner', 'Philipp Francis Guintu', '+63 953-420-7627', '', '2022-08-30', 0, 0),
(54, 'assets/icons/borrower-picture-placeholder.jpg', 'Elizabeth', 'Metante', 'Bacay', 'P. Gomez St. San Matias, Santo Tomas, Pampanga', '+63 922-628-5558', '1974-10-06', 'Egg Dealer/Sari Sari Store', 'Business Owner', 'Melissa Calera Barandino', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(55, 'assets/icons/borrower-picture-placeholder.jpg', 'Analyn', 'Carino', 'Alday', 'San Fernando Public Market, San Fernando, Pampanga', '+63 936-677-7982', '1975-01-28', 'Buko & Coconut Vendor', 'Vendor', 'Priscilla Didasi', '+63 936-677-7982', '', '2022-08-30', 0, 0),
(56, 'assets/icons/borrower-picture-placeholder.jpg', 'Monette', 'Manaloto', 'Mallari', '124 Barangay Sto. Nino, Triangulo, San Fernando, Pampanga', '+63 932-374-0415', '1984-05-31', 'OFW', 'Seaman', 'Vilma Mallari', '+63 926-151-3505', '', '2022-08-30', 0, 0),
(57, 'assets/icons/borrower-picture-placeholder.jpg', 'Bimbo', 'Timbol', 'Nicdao', 'Moras Dela Paz, Sto. Tomas, Pampanga', '+63 997-108-4663', '1976-04-20', 'Bimbo\'s Buko', 'Buko Vendor', 'Priscilla Didasi', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(58, 'assets/icons/borrower-picture-placeholder.jpg', 'Rosemarie', 'Maniago', 'Cuellar', '36 Villa Barosa, Phase 3, Dolores, San Fernando, Pampanga', '+63 997-450-9483', '1979-07-19', 'Sari Sari Store', 'Vendor', 'Lyka M Roble', '+63 966-736-5577', '', '2022-08-30', 0, 0),
(59, 'assets/icons/borrower-picture-placeholder.jpg', 'Laurence Joy', 'Roble', 'Garcia', '36 Villa Barosa, Phase 3, Dolores, San Fernando, Pampanga', '+63 905-175-2079', '1988-07-16', 'Bea Burger & Almusal', 'Vendor', 'Rosemarie Maniago Cuellar', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(60, 'assets/icons/borrower-picture-placeholder.jpg', 'Clarissa', 'Castro', 'Dumandan', 'Sto. Rosario Pau, Sto. Tomas, Pampanga', '+63 927-687-7992', '1994-08-23', 'Joy Ice Cream', 'Vendor', 'Emily Castro Dumandan', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(61, 'assets/icons/borrower-picture-placeholder.jpg', 'Elmer', 'Aquino', 'Lacson', 'B42 L34 Northville 14, Calulut, San Fernando, Pampanga', '+63 912-089-5951', '1966-03-04', 'Electronics Shop', 'Electrician', 'Beatriz R Samson', '+63 961-082-4385', '', '2022-08-30', 0, 0),
(62, 'assets/icons/borrower-picture-placeholder.jpg', 'Analyn', 'C', 'David', '158 3rd St. Dolores Crossing, San Fernando, Pampanga', '+63 997-733-5284', '1986-02-03', 'NA', 'Office Staff', 'Lyka M Roble', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(63, 'assets/icons/borrower-picture-placeholder.jpg', 'Leonida', 'Jarin', 'Mendoza', '328 Salapungan St., San Nicolas, San Fernando, Pampanga', '+63 926-957-9461', '1960-11-26', 'Leoni Sari Sari Store', 'Online Seller', 'Analiza P Pineda', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(64, 'assets/icons/borrower-picture-placeholder.jpg', 'Nelia', 'Miranda', 'Azucena', 'Dalan Baka, Sulipan, Apalit, Pampanga', '+63 935-297-2856', '1964-08-03', 'Nelia Carinderia', 'Vendor', 'Julieta C Mananasala', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(65, 'assets/icons/borrower-picture-placeholder.jpg', 'Leonora', 'B', 'Bondoc', 'Compound Pabian, Sulipan, Apalit, Pampanga', '+63 905-804-0622', '1969-02-06', 'Rolling Lugawan', 'Business Owner', 'Rosalie M Gidoc', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(66, 'assets/icons/borrower-picture-placeholder.jpg', 'Yolanda', 'Mercado', 'Reyes', 'Blk. 2 Lot 27 Northville 27, San Vicente, Sto. Tomas, Pampanga', '+63 917-617-1477', '1976-04-24', 'Yolly\'s Store', 'Business Owner', 'Virginia M Laxamana', '+63 936-293-0689', '', '2022-08-30', 0, 0),
(67, 'assets/icons/borrower-picture-placeholder.jpg', 'Elgie', 'Paras', 'Luquiaz', '18 Dolores, Macabakle, San Fernando, Pampanga', '+63 998-957-3321', '1975-12-25', 'LPG Center', 'LPG Dealer', 'Joel Dela Pena', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(68, 'assets/icons/borrower-picture-placeholder.jpg', 'Lyka', 'Maniago', 'Roble', 'Villa Barosa Subdivision Ph3, Dolores, San Fernando, Pampanga', '+63 966-736-5577', '1992-10-11', 'Online Selling', 'Online Seller', 'Dolores Calica', '+63 000-000-0000', '', '2022-08-30', 0, 0),
(69, 'assets/icons/borrower-picture-placeholder.jpg', 'Rico', 'Cunanan', 'Maniago', '36 Villa Barosa 3, Dolores, San Fernando, Pampanga', '+63 908-594-3902', '1970-05-24', 'NA', 'Garbage Truck Driver', 'Rosemarie M Cuellar', '+63 997-950-9483', '', '2022-08-30', 0, 0),
(70, 'assets/icons/borrower-picture-placeholder.jpg', 'Evangeline', 'F', 'Cantos', 'RP Palad St., Moras Dela Paz, Sto. Tomas, Pampanga', '+63 933-823-4587', '1975-08-14', 'Edgie Buko Juice', 'Buko Niyog Supplier', 'Analayn Alday', '+63 936-677-7982', '', '2022-08-30', 0, 0);

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
  `mode` varchar(50) NOT NULL,
  `term` varchar(50) NOT NULL,
  `interestrate` float NOT NULL,
  `amortization` float NOT NULL,
  `releasedate` date NOT NULL,
  `duedate` date NOT NULL,
  `c_id` int(11) NOT NULL,
  `activeloan` tinyint(1) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(110, 75000, 94313, 821, 25.7507, 'daily', '5 months'),
(111, 80000, 100600, 875, 25.75, 'daily', '5 months'),
(112, 85000, 106888, 930, 25.7506, 'daily', '5 months'),
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
(138, 100000, 129740, 941, 29.74, 'daily', '6 months'),
(139, 5000, 5536, 1384, 0, 'weekly', '1 month'),
(140, 8000, 8737, 2185, 0, 'weekly', '1 month'),
(141, 10000, 10871, 2718, 0, 'weekly', '1 month'),
(142, 12000, 13106, 3277, 0, 'weekly', '1 month'),
(143, 14000, 15240, 3810, 0, 'weekly', '1 month'),
(144, 15000, 16307, 4077, 0, 'weekly', '1 month'),
(145, 20000, 21742, 5436, 0, 'weekly', '1 month'),
(146, 25000, 27178, 6795, 0, 'weekly', '1 month'),
(147, 30000, 32613, 8154, 0, 'weekly', '1 month'),
(148, 35000, 38049, 9513, 0, 'weekly', '1 month'),
(149, 40000, 43484, 10871, 0, 'weekly', '1 month'),
(150, 45000, 48920, 12230, 0, 'weekly', '1 month'),
(151, 50000, 54355, 13589, 0, 'weekly', '1 month'),
(152, 55000, 59791, 14948, 0, 'weekly', '1 month'),
(153, 60000, 65226, 16307, 0, 'weekly', '1 month'),
(154, 65000, 70662, 17666, 0, 'weekly', '1 month'),
(155, 70000, 76097, 19025, 0, 'weekly', '1 month'),
(156, 75000, 81533, 20384, 0, 'weekly', '1 month'),
(157, 80000, 86968, 21742, 0, 'weekly', '1 month'),
(158, 85000, 92404, 23101, 0, 'weekly', '1 month'),
(159, 90000, 97839, 24460, 0, 'weekly', '1 month'),
(160, 95000, 103275, 25819, 0, 'weekly', '1 month'),
(161, 100000, 108710, 27178, 0, 'weekly', '1 month'),
(162, 5000, 5806, 726, 0, 'weekly', '2 months'),
(163, 8000, 9168, 1146, 0, 'weekly', '2 months'),
(164, 10000, 11410, 1427, 0, 'weekly', '2 months'),
(165, 12000, 13752, 1719, 0, 'weekly', '2 months'),
(166, 14000, 15994, 2000, 0, 'weekly', '2 months'),
(167, 15000, 17115, 2140, 0, 'weekly', '2 months'),
(168, 20000, 22819, 2853, 0, 'weekly', '2 months'),
(169, 25000, 28525, 3566, 0, 'weekly', '2 months'),
(170, 30000, 34229, 4279, 0, 'weekly', '2 months'),
(171, 35000, 39934, 4992, 0, 'weekly', '2 months'),
(172, 40000, 45638, 5705, 0, 'weekly', '2 months'),
(173, 45000, 51344, 6418, 0, 'weekly', '2 months'),
(174, 50000, 57048, 7131, 0, 'weekly', '2 months'),
(175, 55000, 62753, 7845, 0, 'weekly', '2 months'),
(176, 60000, 68457, 8558, 0, 'weekly', '2 months'),
(177, 65000, 74163, 9271, 0, 'weekly', '2 months'),
(178, 70000, 79867, 9984, 0, 'weekly', '2 months'),
(179, 75000, 85572, 10697, 0, 'weekly', '2 months'),
(180, 80000, 91276, 11410, 0, 'weekly', '2 months'),
(181, 85000, 96982, 12123, 0, 'weekly', '2 months'),
(182, 90000, 102686, 12836, 0, 'weekly', '2 months'),
(183, 95000, 108391, 13549, 0, 'weekly', '2 months'),
(184, 100000, 114095, 14262, 0, 'weekly', '2 months'),
(185, 5000, 6074, 507, 0, 'weekly', '3 months'),
(186, 8000, 9599, 800, 0, 'weekly', '3 months'),
(187, 10000, 11948, 996, 0, 'weekly', '3 months'),
(188, 12000, 14398, 1200, 0, 'weekly', '3 months'),
(189, 14000, 16748, 1396, 0, 'weekly', '3 months'),
(190, 15000, 17922, 1494, 0, 'weekly', '3 months'),
(191, 20000, 23896, 1992, 0, 'weekly', '3 months'),
(192, 25000, 29870, 2490, 0, 'weekly', '3 months'),
(193, 30000, 35844, 2987, 0, 'weekly', '3 months'),
(194, 35000, 41818, 3485, 0, 'weekly', '3 months'),
(195, 40000, 47792, 3983, 0, 'weekly', '3 months'),
(196, 45000, 53766, 4481, 0, 'weekly', '3 months'),
(197, 50000, 59740, 4979, 0, 'weekly', '3 months'),
(198, 55000, 65714, 5477, 0, 'weekly', '3 months'),
(199, 60000, 71688, 5974, 0, 'weekly', '3 months'),
(200, 65000, 77662, 6472, 0, 'weekly', '3 months'),
(201, 70000, 83636, 6970, 0, 'weekly', '3 months'),
(202, 75000, 89610, 7468, 0, 'weekly', '3 months'),
(203, 80000, 95584, 7966, 0, 'weekly', '3 months'),
(204, 85000, 101558, 8464, 0, 'weekly', '3 months'),
(205, 90000, 107532, 8961, 0, 'weekly', '3 months'),
(206, 95000, 113506, 9459, 0, 'weekly', '3 months'),
(207, 100000, 119480, 9957, 0, 'weekly', '3 months'),
(208, 5000, 6302, 394, 0, 'weekly', '4 months'),
(209, 8000, 9964, 623, 0, 'weekly', '4 months'),
(210, 10000, 12404, 776, 0, 'weekly', '4 months'),
(211, 12000, 14945, 935, 0, 'weekly', '4 months'),
(212, 14000, 17386, 1087, 0, 'weekly', '4 months'),
(213, 15000, 18606, 1163, 0, 'weekly', '4 months'),
(214, 20000, 24808, 1551, 0, 'weekly', '4 months'),
(215, 25000, 31010, 1939, 0, 'weekly', '4 months'),
(216, 30000, 37212, 2326, 0, 'weekly', '4 months'),
(217, 35000, 43414, 2714, 0, 'weekly', '4 months'),
(218, 40000, 49616, 3101, 0, 'weekly', '4 months'),
(219, 45000, 55818, 3489, 0, 'weekly', '4 months'),
(220, 50000, 62020, 3877, 0, 'weekly', '4 months'),
(221, 55000, 68222, 4264, 0, 'weekly', '4 months'),
(222, 60000, 74424, 4652, 0, 'weekly', '4 months'),
(223, 65000, 80626, 5040, 0, 'weekly', '4 months'),
(224, 70000, 86828, 5427, 0, 'weekly', '4 months'),
(225, 75000, 93030, 5815, 0, 'weekly', '4 months'),
(226, 80000, 99232, 6202, 0, 'weekly', '4 months'),
(227, 85000, 105434, 6590, 0, 'weekly', '4 months'),
(228, 90000, 111636, 6978, 0, 'weekly', '4 months'),
(229, 95000, 117838, 7365, 0, 'weekly', '4 months'),
(230, 100000, 124040, 7753, 0, 'weekly', '4 months'),
(231, 5000, 6530, 327, 0, 'weekly', '5 months'),
(232, 8000, 10328, 517, 0, 'weekly', '5 months'),
(233, 10000, 12860, 643, 0, 'weekly', '5 months'),
(234, 12000, 15492, 775, 0, 'weekly', '5 months'),
(235, 14000, 18024, 902, 0, 'weekly', '5 months'),
(236, 15000, 19290, 965, 0, 'weekly', '5 months'),
(237, 20000, 25720, 1286, 0, 'weekly', '5 months'),
(238, 25000, 32150, 1608, 0, 'weekly', '5 months'),
(239, 30000, 38580, 1929, 0, 'weekly', '5 months'),
(240, 35000, 45010, 2251, 0, 'weekly', '5 months'),
(241, 40000, 51440, 2572, 0, 'weekly', '5 months'),
(242, 45000, 57870, 2894, 0, 'weekly', '5 months'),
(243, 50000, 64300, 3215, 0, 'weekly', '5 months'),
(244, 55000, 70730, 3537, 0, 'weekly', '5 months'),
(245, 60000, 77160, 3858, 0, 'weekly', '5 months'),
(246, 65000, 83590, 4180, 0, 'weekly', '5 months'),
(247, 70000, 90020, 4501, 0, 'weekly', '5 months'),
(248, 75000, 96450, 4823, 0, 'weekly', '5 months'),
(249, 80000, 102880, 5144, 0, 'weekly', '5 months'),
(250, 85000, 109310, 5466, 0, 'weekly', '5 months'),
(251, 90000, 115740, 5787, 0, 'weekly', '5 months'),
(252, 95000, 122170, 6109, 0, 'weekly', '5 months'),
(253, 100000, 128600, 6430, 0, 'weekly', '5 months'),
(254, 5000, 6758, 282, 0, 'weekly', '6 months'),
(255, 8000, 10693, 446, 0, 'weekly', '6 months'),
(256, 10000, 13316, 555, 0, 'weekly', '6 months'),
(257, 14000, 18663, 778, 0, 'weekly', '6 months'),
(258, 15000, 19974, 833, 0, 'weekly', '6 months'),
(259, 20000, 26632, 1110, 0, 'weekly', '6 months'),
(260, 25000, 33290, 1388, 0, 'weekly', '6 months'),
(261, 30000, 39948, 1665, 0, 'weekly', '6 months'),
(262, 35000, 46606, 1942, 0, 'weekly', '6 months'),
(263, 40000, 53264, 2220, 0, 'weekly', '6 months'),
(264, 45000, 59922, 2497, 0, 'weekly', '6 months'),
(265, 50000, 66580, 2775, 0, 'weekly', '6 months'),
(266, 55000, 73238, 3052, 0, 'weekly', '6 months'),
(267, 60000, 79896, 3329, 0, 'weekly', '6 months'),
(268, 65000, 86554, 3607, 0, 'weekly', '6 months'),
(269, 70000, 93212, 3884, 0, 'weekly', '6 months'),
(270, 75000, 99870, 4162, 0, 'weekly', '6 months'),
(271, 80000, 106528, 4439, 0, 'weekly', '6 months'),
(272, 85000, 113186, 4717, 0, 'weekly', '6 months'),
(273, 90000, 119844, 4994, 0, 'weekly', '6 months'),
(274, 95000, 126502, 5271, 0, 'weekly', '6 months'),
(275, 100000, 133160, 5549, 0, 'weekly', '6 months');

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
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `collectors`
--
ALTER TABLE `collectors`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `l_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=276;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
