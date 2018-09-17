-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2018 at 02:25 PM
-- Server version: 5.7.23-0ubuntu0.18.04.1
-- PHP Version: 7.2.7-0ubuntu0.18.04.2

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
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `name`, `status`) VALUES
(1, 'Commands', 'posted'),
(2, 'scripts', 'posted');

-- --------------------------------------------------------

--
-- Table structure for table `chapter_1`
--

CREATE TABLE `chapter_1` (
  `user_id` int(11) NOT NULL,
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `last_question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chapter_1`
--

INSERT INTO `chapter_1` (`user_id`, `right_answers`, `last_question_id`) VALUES
(4, 23, 8),
(5, 0, 4),
(6, 0, 2),
(8, 20, 9);

-- --------------------------------------------------------

--
-- Table structure for table `chapter_2`
--

CREATE TABLE `chapter_2` (
  `user_id` int(11) NOT NULL,
  `right_answers` int(11) NOT NULL DEFAULT '0',
  `last_question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chapter_2`
--

INSERT INTO `chapter_2` (`user_id`, `right_answers`, `last_question_id`) VALUES
(4, 23, 8),
(5, 0, 4),
(6, 0, 2),
(10, 2, 9);

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
  `validation` varchar(20) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `user_id`, `chapter_id`, `status`, `date_created`, `all_answers`, `right_answers`, `validation`) VALUES
(8, 4, 1, 'posted', '2018-09-06', 37, 3, 'none'),
(9, 4, 1, 'posted', '2018-09-06', 33, 15, 'none'),
(10, 8, 1, 'posted', '2018-09-06', 25, 4, 'none'),
(11, 8, 1, 'posted', '2018-09-17', 0, 0, 'none');

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
  `hash_pass` varchar(256) NOT NULL,
  `ssh_pass` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `is_admin`, `date_created`, `hash_pass`, `ssh_pass`) VALUES
(4, 'dorin.haloca', 1, '2018-09-05', '$2y$10$Co6c4EZAgxyCMQntOf6KBOEn9U/rq5zsYSPkjC12WywDSDhPuE2P6', 'tqIrdq7UK5sdKlN'),
(6, 'test1', 0, '2018-09-06', '$2y$10$3EWnEkvk9u.Wpt1CoiVG6e4opBZJ3QokTzHjCBlYMETo5izocQPXa', 'VEZxxmuiNV3PcmJ'),
(7, 'test2', 0, '2018-09-17', '$2y$10$6DV4lWDtQD0Vm0CGqlSuq.56YA9HUJQ/qOAiD2ZuK6BRzGZw7OxyK', 'z2HlegKOkOMPywJnSYgA'),
(8, '123', 0, '2018-09-17', '$2y$10$CccCwlh9xSeb15cC4lDlRuyvYHycqmubxcHncc4wEInVPlGBW9IVy', '3l8oeT6JwpFHFSAq7qM2'),
(10, 'test', 0, '2018-09-17', '$2y$10$V/dJpfqjLuOGYjgan4Gfie7CMcvlmkcxiGbHO6XT2DpIGhq6uNkba', 'kV7h7fGGvuJBw4osJnxQ');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
