-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 01:51 PM
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
-- Database: `group_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `eoi`
--

CREATE TABLE `eoi` (
  `EOInumber` int(11) NOT NULL,
  `JobReferenceNumber` varchar(10) NOT NULL,
  `FirstName` varchar(20) NOT NULL,
  `LastName` varchar(20) NOT NULL,
  `DOB` date NOT NULL,
  `Gender` varchar(10) NOT NULL,
  `StreetAddress` varchar(40) DEFAULT NULL,
  `SuburbTown` varchar(40) DEFAULT NULL,
  `State` varchar(20) DEFAULT NULL,
  `Postcode` varchar(4) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `PhoneNumber` varchar(12) DEFAULT NULL,
  `Skills` varchar(100) NOT NULL,
  `OtherSkills` text DEFAULT NULL,
  `Status` enum('New','Current','Final') DEFAULT 'New'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eoi`
--

INSERT INTO `eoi` (`EOInumber`, `JobReferenceNumber`, `FirstName`, `LastName`, `DOB`, `Gender`, `StreetAddress`, `SuburbTown`, `State`, `Postcode`, `Email`, `PhoneNumber`, `Skills`, `OtherSkills`, `Status`) VALUES
(10, 'cs1', 'Lim', 'Yee Kuan', '2006-02-01', 'female', 'NO 22 JALAN PEEPOO, 3/3J', 'SUBANG JAYA', 'VIC', '3000', 'yeekuan1229@gmail.com', '01126119821', 'Degree in Cybersecurity/CS/related, 3-5 yrs Security Eng/IR/SecOps, Firewalls, IDS/IPS, TCP/IP, moni', 'no', 'New');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `JobReferenceNumber` varchar(5) NOT NULL,
  `JobTitle` varchar(100) NOT NULL,
  `JobDescription` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`JobReferenceNumber`, `JobTitle`, `JobDescription`) VALUES
('cs1', 'Cybersecurity Specialist', 'As a Cybersecurity Specialist, you\'re responsible for protecting computer systems, networks, and data from unauthorized access, theft, or damage. You play a critical role in defending computer systems, networks, and sensitive data against unauthorized access, cyberattacks, and potential breaches. Your expertise ensures the confidentiality, integrity, and availability of information across an organization\'s infrastructure.'),
('ne1', 'Network Administrator', 'The Network Administrator is responsible for managing, implementing and maintaining the company’s network infrastructure and security. This role requires the ideal candidate to have a strong technical foundation, proactive problem-solving skills and experience with designing, securing and maintaining the overall network performance and security of the company.');

-- --------------------------------------------------------

--
-- Table structure for table `managers`
--

CREATE TABLE `managers` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `last_attempt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `managers`
--

INSERT INTO `managers` (`id`, `username`, `password`, `login_attempts`, `last_attempt`) VALUES
(2, 'HELLO', '$2y$10$S9ntjCr127FTyvykDsNnJeoiEc3rqUly46/PlCkEN7eOZpwTSrAB.', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `eoi`
--
ALTER TABLE `eoi`
  ADD PRIMARY KEY (`EOInumber`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`JobReferenceNumber`);

--
-- Indexes for table `managers`
--
ALTER TABLE `managers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eoi`
--
ALTER TABLE `eoi`
  MODIFY `EOInumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `managers`
--
ALTER TABLE `managers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
