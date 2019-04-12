-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2019 at 07:51 PM
-- Server version: 5.7.25-0ubuntu0.18.04.2
-- PHP Version: 7.2.15-0ubuntu0.18.04.2

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
(2, 'Scripts', 'Like commands, but better!', 'posted'),
(3, 'C Linux', 'C Linux is the best!', 'posted'),
(4, 'Forking in C', 'Fork usage is being checked!', 'posted');

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
(15, 2, 32, 0),
(16, 11, 32, 0),
(19, 0, 6, 0),
(21, 0, 6, 0),
(22, 0, 6, 0),
(23, 0, 6, 0),
(25, 0, 6, 0),
(26, 0, 6, 0),
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
-- Table structure for table `chapter_3`
--

CREATE TABLE `chapter_3` (
  `user_id` int(11) NOT NULL,
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `last_question_id` int(11) NOT NULL,
  `deleted_questions` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chapter_3`
--

INSERT INTO `chapter_3` (`user_id`, `right_answers`, `last_question_id`, `deleted_questions`) VALUES
(15, 0, 37, 0),
(16, 1, 41, 0);

-- --------------------------------------------------------

--
-- Table structure for table `chapter_4`
--

CREATE TABLE `chapter_4` (
  `user_id` int(11) NOT NULL,
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `last_question_id` int(11) NOT NULL,
  `deleted_questions` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chapter_4`
--

INSERT INTO `chapter_4` (`user_id`, `right_answers`, `last_question_id`, `deleted_questions`) VALUES
(15, 8, 43, 0),
(16, 11, 44, 0);

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
(32, 15, 1, 'posted', '2019-02-26', 19, 1, 'None', 0),
(33, 16, 1, 'posted', '2019-03-05', 16, 5, 'None', 1),
(34, 15, 1, 'posted', '2019-03-05', 66, 6, 'None', 0),
(35, 16, 1, 'posted', '2019-03-30', 3, 0, 'None', 0),
(36, 16, 3, 'posted', '2019-04-08', 7, 0, 'None', 0),
(37, 16, 3, 'posted', '2019-04-08', 1, 0, 'None', 0),
(38, 16, 3, 'posted', '2019-04-08', 1, 0, 'None', 0),
(39, 16, 3, 'posted', '2019-04-08', 1, 0, 'None', 0),
(40, 15, 3, 'posted', '2019-04-08', 4, 1, 'None', 0),
(41, 15, 3, 'posted', '2019-04-08', 2, 0, 'None', 0),
(42, 16, 1, 'posted', '2019-04-08', 0, 0, 'None', 0),
(43, 15, 4, 'posted', '2019-04-12', 12, 11, 'None', 0),
(44, 15, 4, 'posted', '2019-04-12', 9, 8, 'None', 0),
(45, 16, 4, 'posted', '2019-04-12', 0, 0, 'None', 0);

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
(12, 14, 4, 'Ce faci?', '2018-12-07'),
(13, 16, 33, 'De ce nu a venit Tiplea?', '2019-03-05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(5) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` date NOT NULL,
  `pass_hash` varchar(256) NOT NULL,
  `ssh_pass` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `is_admin`, `date_created`, `pass_hash`, `ssh_pass`) VALUES
(15, 'test', 0, '2019-02-10', '$2y$10$cVdU54xSntC5fk4O.d9luO0RkpmLTovany3beYqmRY/i.mz6fPrgO', 'KRxOaeRE7mLsUwveMSKJ'),
(16, 'dorin.haloca', 1, '2019-02-11', '$2y$10$1NlZhOMGqax5zVQB7VrL8u4BBn1baybFEhV8QggwT3qGiLUr0S94C', '2YGCf22KLo3aw6uws59T'),
(17, 'dorin', 0, '2019-02-14', '$2y$10$5Ci38nuBJ1LKjlTXYE3Mm.XpdSwvH9MMI9hwNIYyiPYzhpjLWsLKK', 'Lh0iJX7yzMJHK8Sz3zP0'),
(18, 'hdorin', 0, '2019-02-23', '$2y$10$iTFS1IpvlYWgCDBRpnggl.rLKv3PiBXx6ODOBc.v9LErE.KjqNknq', 'K6VB0q1D6PHFeZSvwimF'),
(22, 'test1', 0, '2019-02-23', '$2y$10$eUluGdzXiMwbIFERQVloOuCCvNdLKd.hK78PIg91D7ENdI8Fhh6Oa', 'B6S3WtVEvRamtwdCyxkq'),
(23, 'test2', 0, '2019-02-26', '$2y$10$YdVPHHpBH2XGcYS3rJchAejuSFIgCJiNMZLdNIyRuSWHriVYx0A2u', 'XAwftlN1YQsLvWGeCWlm'),
(24, 'test3', 0, '2019-02-26', '$2y$10$VFeB2IT7BG.TqSO8/KGPy.ihGW9FeJrE.ndReg29ALQm7HKC4BOgO', 'Q5EZo1RAr1yLOc851hS3'),
(25, 'test4', 0, '2019-02-26', '$2y$10$pxYe/FMAcPE5Blap0EbW7eyr3i1vVTshOpPFYCAJkCiY0h0DnAM9K', 'gj7sB8h7mO1UipBK5SZF'),
(26, 'test5', 0, '2019-02-26', '$2y$10$7HGsBgSRBV3KKP3UN1zUz.NaE5kikdniWO2STBGdFfQTkk9UKTzXS', 'M4p838kUEt434QvSfY5P');

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
-- Indexes for table `chapter_3`
--
ALTER TABLE `chapter_3`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `chapter_4`
--
ALTER TABLE `chapter_4`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
