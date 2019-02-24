-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2019 at 06:29 PM
-- Server version: 5.7.25-0ubuntu0.18.04.2
-- PHP Version: 7.2.15-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `AplicatieSO`
--

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(150) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `name`, `description`, `status`) VALUES
(1, 'Commands', 'Commands are fun!', 'posted'),
(2, 'C Linux', 'C Linux is the best!', 'posted');

-- --------------------------------------------------------

--
-- Table structure for table `chapter_1`
--

CREATE TABLE `chapter_1` (
  `user_id` int(11) NOT NULL,
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `last_question_id` int(11) NOT NULL,
  `deleted_questions` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chapter_1`
--

INSERT INTO `chapter_1` (`user_id`, `right_answers`, `last_question_id`, `deleted_questions`) VALUES
(4, 23, 8, 0),
(5, 0, 4, 0),
(6, 0, 2, 0),
(10, 26, 11, 0),
(11, 20, 8, 0),
(12, 20, 2, 0),
(13, 2, 1, 9),
(14, 20, 4, 0),
(15, 0, 6, 0),
(16, 1, 7, 0),
(19, 0, 6, 0),
(21, 0, 6, 0),
(22, 0, 6, 0),
(25, 0, 6, 0),
(31, 0, 6, 0);

-- --------------------------------------------------------

--
-- Table structure for table `chapter_2`
--

CREATE TABLE `chapter_2` (
  `user_id` int(11) NOT NULL,
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `last_question_id` int(11) NOT NULL,
  `deleted_questions` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chapter_2`
--

INSERT INTO `chapter_2` (`user_id`, `right_answers`, `last_question_id`, `deleted_questions`) VALUES
(4, 2, 8, 0),
(5, 0, 4, 0),
(6, 0, 2, 0),
(10, 31, 9, 0),
(15, 0, 9, 0),
(16, 0, 9, 0),
(20, 0, 9, 0),
(22, 0, 9, 0);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` varchar(500) NOT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `user_id`, `text`, `date_created`) VALUES
(3, 16, 'Welcome to AplicatieSO!', '2019-02-11');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `date_created` date NOT NULL,
  `all_answers` int(11) NOT NULL DEFAULT '0',
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `validation` varchar(20) NOT NULL DEFAULT 'None',
  `reports_nr` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `user_id`, `chapter_id`, `status`, `date_created`, `all_answers`, `right_answers`, `validation`, `reports_nr`) VALUES
(6, 16, 1, 'posted', '2019-02-11', 0, 0, 'None', 0),
(7, 15, 1, 'posted', '2019-02-11', 1, 0, 'None', 0),
(9, 15, 2, 'posted', '2019-02-20', 0, 0, 'None', 0),
(10, 16, 1, 'posted', '2019-02-23', 0, 0, 'None', 0),
(11, 15, 1, 'posted', '2019-02-24', 1, 1, 'None', 0);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `text` varchar(500) NOT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `question_id`, `text`, `date_created`) VALUES
(9, 10, 9, 'Enter report message', '2018-10-19'),
(10, 10, 8, 'Enter report message', '2018-10-19'),
(11, 10, 10, 'Enter report message', '2018-10-19'),
(12, 14, 4, 'Ce faci?', '2018-12-07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(5) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` date NOT NULL,
  `hash_pass` varchar(256) NOT NULL,
  `ssh_pass` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `is_admin`, `date_created`, `hash_pass`, `ssh_pass`) VALUES
(15, 'test', 0, '2019-02-10', '$2y$10$X09EVaRaAT6ruLPuMS2ZkucjSogloSUDIf6lXqqIp4hTb64Tysx3u', 'KRxOaeRE7mLsUwveMSKJ'),
(16, 'dorin.haloca', 1, '2019-02-11', '$2y$10$YIwtBi8evvbHpRPnLSeYrOTHeiHHczpw2iRSJ7PKZjjsu7uAwdXuW', '2YGCf22KLo3aw6uws59T'),
(17, 'dorin', 0, '2019-02-14', '$2y$10$5Ci38nuBJ1LKjlTXYE3Mm.XpdSwvH9MMI9hwNIYyiPYzhpjLWsLKK', 'Lh0iJX7yzMJHK8Sz3zP0'),
(18, 'hdorin', 0, '2019-02-23', '$2y$10$iTFS1IpvlYWgCDBRpnggl.rLKv3PiBXx6ODOBc.v9LErE.KjqNknq', 'K6VB0q1D6PHFeZSvwimF'),
(22, 'test1', 0, '2019-02-23', '$2y$10$eUluGdzXiMwbIFERQVloOuCCvNdLKd.hK78PIg91D7ENdI8Fhh6Oa', 'B6S3WtVEvRamtwdCyxkq');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chapter_1`
--
ALTER TABLE `chapter_1`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `chapter_2`
--
ALTER TABLE `chapter_2`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
