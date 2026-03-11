-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2026 at 12:16 PM
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
-- Database: `quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `quiestion` text NOT NULL,
  `answers` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `quiestion`, `answers`) VALUES
(1, 1, 'TEST', '[{\"text\":\"ee\",\"correct\":false},{\"text\":\"eef\",\"correct\":true}]'),
(2, 1, 'erg', '[{\"text\":\"WW\",\"correct\":false},{\"text\":\"w\",\"correct\":true}]'),
(3, 2, 'are you pregnant', '[{\"text\":\"yes\",\"correct\":true},{\"text\":\"no\",\"correct\":false}]'),
(4, 2, 'what is 2+2', '[{\"text\":\"1\",\"correct\":false},{\"text\":\"2\",\"correct\":true},{\"text\":\"3\",\"correct\":false},{\"text\":\"4\",\"correct\":false},{\"text\":\"5\",\"correct\":false}]'),
(5, 2, 'LONGUS', '[{\"text\":\"true\",\"correct\":false},{\"text\":\"false\",\"correct\":false},{\"text\":\"null\",\"correct\":true}]'),
(6, 3, 'erf', '[{\"text\":\"ee\",\"correct\":true},{\"text\":\"dd\",\"correct\":false}]'),
(7, 4, '1', '[{\"text\":\"incor\",\"correct\":true},{\"text\":\"correct1\",\"correct\":false}]'),
(8, 4, '2', '[{\"text\":\"cor\",\"correct\":false},{\"text\":\"incor\",\"correct\":true}]'),
(9, 4, '3', '[{\"text\":\"incor\",\"correct\":false},{\"text\":\"cor\",\"correct\":false},{\"text\":\"\",\"correct\":true}]'),
(10, 5, 'ddd', '[{\"text\":\"inc\",\"correct\":false},{\"text\":\"cor\",\"correct\":false}]'),
(11, 6, 'e', '[{\"text\":\"inc\",\"correct\":false},{\"text\":\"cor\",\"correct\":true}]');

-- --------------------------------------------------------

--
-- Table structure for table `quiziz`
--

CREATE TABLE `quiziz` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `date_added` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiziz`
--

INSERT INTO `quiziz` (`id`, `name`, `description`, `date_added`) VALUES
(1, '1', '1111', '2026-03-06'),
(2, 'TEST PREGNANCY', 'quick pregnancy test', '2026-03-06'),
(3, 'd', 'ef', '2026-03-06'),
(4, 'ddddd', '', '2026-03-06'),
(5, 'TEST PREGNANCY2', '', '2026-03-06'),
(6, 'TEST PREGNANCY3', '', '2026-03-06');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `quizId` int(11) NOT NULL,
  `result` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `username`, `quizId`, `result`) VALUES
(1, 'root', 1, 1),
(2, 'root', 1, 1),
(3, 'root', 1, 2),
(4, 'root', 2, 0),
(5, 'root', 3, 0),
(6, 'root', 4, 0),
(7, 'root', 4, 0),
(8, 'root', 4, 1),
(9, 'root', 5, 0),
(10, 'root', 6, 1),
(11, 'root', 6, 1),
(12, 'root', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'root', 'lbhtrnjh');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiziz`
--
ALTER TABLE `quiziz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `quiziz`
--
ALTER TABLE `quiziz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
