-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2026 at 12:04 PM
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
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_text`, `is_correct`) VALUES
(1, 1, '17', 0),
(2, 1, '18', 0),
(3, 1, '19', 1),
(4, 1, '20', 0),
(5, 2, '42', 0),
(6, 2, '54', 1),
(7, 2, '56', 0),
(8, 2, '64', 0),
(9, 3, '1/9', 0),
(10, 3, '1/3', 1),
(11, 3, '3/1', 0),
(12, 3, '9/3', 0),
(13, 4, '\"null\"', 0),
(14, 4, '\"object\"', 1),
(15, 4, '\"undefined\"', 0),
(16, 4, '\"number\"', 0),
(17, 5, '4', 0),
(18, 5, '\"4\"', 0),
(19, 5, '\"22\"', 1),
(20, 5, 'NaN', 0),
(21, 6, 'string', 0),
(22, 6, 'boolean', 0),
(23, 6, 'number', 0),
(24, 6, 'array', 1),
(25, 7, 'Sahara Desert', 0),
(26, 7, 'Arabian Desert', 0),
(27, 7, 'Gobi Desert', 0),
(28, 7, 'Antarctic Desert', 1),
(29, 8, 'Venus', 0),
(30, 8, 'Mars', 1),
(31, 8, 'Jupiter', 0),
(32, 8, 'Jupiter', 0),
(33, 9, '1987', 0),
(34, 9, '1989', 1),
(35, 9, '1488', 0),
(36, 9, '2011', 0),
(37, 10, 'Aldous Huxley', 0),
(38, 10, 'George Orwell', 1),
(39, 10, 'Ray Bradbury', 0),
(40, 10, 'Ernest Hemingway', 0),
(41, 11, 'Liver', 0),
(42, 11, 'Brain', 0),
(43, 11, 'Skin', 1),
(44, 11, 'Heart', 0),
(45, 12, 'Germany', 0),
(46, 12, 'Brazil', 0),
(47, 12, 'France', 1),
(48, 12, 'Argentina', 0),
(49, 13, '1965', 0),
(50, 13, '1967', 0),
(51, 13, '1972', 0),
(52, 13, '1969', 1),
(53, 14, 'Vince Gilligan', 1),
(54, 14, 'James Cameron', 0),
(55, 14, 'Christopher Nolan', 0),
(56, 14, 'Steven Spielberg', 0),
(57, 15, 'Oxygen', 0),
(58, 15, 'Nitrogen', 0),
(59, 15, 'Carbon dioxide', 1),
(60, 15, 'Hydrogen', 0),
(61, 16, 'Tax fraud', 0),
(62, 16, 'Drug trafficking', 0),
(63, 16, 'Soliciting prostitution from a minor and related charges', 1),
(64, 16, 'Money laundering', 0);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `quiestion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `quiestion`) VALUES
(1, 1, 'What is 12 + 7?'),
(2, 1, 'What is 9 × 6?'),
(3, 1, 'Simplify: 3/9'),
(4, 2, 'What is the result of typeof null?'),
(5, 2, 'What does \"2\" + 2 evaluate to in JavaScript?'),
(6, 2, 'Which is NOT a JavaScript primitive type?'),
(7, 3, 'What is the largest desert in the world?'),
(8, 3, 'Which planet is known as the “Red Planet”?'),
(9, 3, 'In which year did the Fall of the Berlin Wall happen?'),
(10, 3, 'Who wrote 1984?'),
(11, 3, 'What is the largest organ in the human body?'),
(12, 3, 'Which country won the 2018 FIFA World Cup?'),
(13, 3, 'The Apollo 11 Moon Landing happened in which year?'),
(14, 3, 'Who directed Breaking Bad?'),
(15, 3, 'What gas do plants absorb from the atmosphere during Photosynthesis?'),
(16, 3, 'For what crimes was Jeffrey Epstein convicted in 2008 in Florida?');

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
(1, 'Math', 'Basic arithmetic, fractions, and algebra.', '2026-03-13'),
(2, 'JavaScript', 'Core JavaScript syntax, types, and runtime behavior.', '2026-03-13'),
(3, 'Third quiz on this site', 'Core iq test for minors', '2026-03-13');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `quizId` int(11) NOT NULL,
  `result` int(11) NOT NULL,
  `date_completed` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `username`, `quizId`, `result`, `date_completed`) VALUES
(1, 'root', 3, 9, '2026-03-13 12:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `is_admin`) VALUES
(1, 'root', 'lbhtrnjh', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiziz`
--
ALTER TABLE `quiziz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quizId` (`quizId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `quiziz`
--
ALTER TABLE `quiziz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiziz` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`quizId`) REFERENCES `quiziz` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
