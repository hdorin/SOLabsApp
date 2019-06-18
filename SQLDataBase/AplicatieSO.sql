-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2019 at 10:06 PM
-- Server version: 5.7.26-0ubuntu0.18.04.1
-- PHP Version: 7.2.19-0ubuntu0.18.04.1

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
(11, 'Commands', 'Commands are fun!', 'posted'),
(21, 'Scripts', 'Like commands, but better!', 'posted'),
(31, 'C Linux', 'C Linux is the best!', 'posted'),
(32, 'Forking in C', 'Fork usage is being checked!', 'posted');

-- --------------------------------------------------------

--
-- Table structure for table `chapter_11`
--

CREATE TABLE `chapter_11` (
  `user_id` int(11) NOT NULL,
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `last_question_id` int(11) NOT NULL,
  `posted_questions` int(11) NOT NULL DEFAULT '0' COMMENT 'Only questions with status of "posted"',
  `deleted_questions` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `chapter_21`
--

CREATE TABLE `chapter_21` (
  `user_id` int(11) NOT NULL,
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `last_question_id` int(11) NOT NULL,
  `posted_questions` int(11) NOT NULL DEFAULT '0',
  `deleted_questions` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chapter_21`
--

INSERT INTO `chapter_21` (`user_id`, `right_answers`, `last_question_id`, `posted_questions`, `deleted_questions`) VALUES
(15, 1, 69, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `chapter_31`
--

CREATE TABLE `chapter_31` (
  `user_id` int(11) NOT NULL,
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `last_question_id` int(11) NOT NULL,
  `posted_questions` int(11) NOT NULL DEFAULT '0',
  `deleted_questions` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `chapter_32`
--

CREATE TABLE `chapter_32` (
  `user_id` int(11) NOT NULL,
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `last_question_id` int(11) NOT NULL,
  `posted_questions` int(11) NOT NULL DEFAULT '0',
  `deleted_questions` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `validation` varchar(20) NOT NULL DEFAULT 'Unvalidated',
  `reports_nr` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `user_id`, `chapter_id`, `status`, `date_created`, `all_answers`, `right_answers`, `validation`, `reports_nr`) VALUES
(68, 16, 21, 'posted', '2019-06-18', 1, 1, 'Unvalidated', 0),
(69, 16, 21, 'posted', '2019-06-18', 0, 0, 'Unvalidated', 0);

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

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(5) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` date NOT NULL,
  `pass_hash` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `is_admin`, `date_created`, `pass_hash`) VALUES
(15, 'test', 0, '2019-02-10', '$2y$10$u0WtHiTQIxaP/NTiBxXYaeQM68qxKjutZ2HWc7HNXQTqhW8fdcIm6'),
(16, 'dorin.haloca', 1, '2019-02-11', '$2y$10$ugtUTomp2AXDdDL4IcoEwuobnhxis1yW2WWJK5K4zXO1mijZ6xule'),
(17, 'dorin', 0, '2019-02-14', '$2y$10$5Ci38nuBJ1LKjlTXYE3Mm.XpdSwvH9MMI9hwNIYyiPYzhpjLWsLKK'),
(18, 'hdorin', 0, '2019-02-23', '$2y$10$lr3Rdr3QbmRpBcAzi8J9DudLTSyZEGCc4Eb9q508sTjnoum/CBzlS'),
(29, 'test2', 0, '2019-05-15', '$2y$10$nkKwnQ8irIu6NmjLzBCL0ehcoELOTph81gMDm9xdRq8VS25RX3FD.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chapter_11`
--
ALTER TABLE `chapter_11`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `chapter_21`
--
ALTER TABLE `chapter_21`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `chapter_31`
--
ALTER TABLE `chapter_31`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `chapter_32`
--
ALTER TABLE `chapter_32`
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
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chapter_id` (`chapter_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
