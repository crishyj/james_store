-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 30, 2020 at 02:02 AM
-- Server version: 10.3.23-MariaDB
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ereader`
--

-- --------------------------------------------------------

--
-- Table structure for table `emailtemplates`
--

CREATE TABLE `emailtemplates` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `content` longtext NOT NULL,
  `day` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `library`
--

CREATE TABLE `library` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL DEFAULT '',
  `author` varchar(128) NOT NULL DEFAULT '',
  `coverHref` varchar(256) NOT NULL DEFAULT '',
  `packagePath` varchar(256) NOT NULL DEFAULT '',
  `rootUrl` varchar(256) NOT NULL DEFAULT '',
  `price` varchar(45) NOT NULL DEFAULT '0',
  `description` varchar(512) NOT NULL DEFAULT '',
  `genre` varchar(128) NOT NULL DEFAULT '',
  `preload` int(11) NOT NULL DEFAULT 0,
  `owner` int(11) NOT NULL DEFAULT 0,
  `excerpt` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `private_audio`
--

CREATE TABLE `private_audio` (
  `id` int(11) NOT NULL,
  `bookid` int(11) NOT NULL,
  `audiofile` varchar(256) NOT NULL,
  `name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `private_chapters`
--

CREATE TABLE `private_chapters` (
  `id` int(11) NOT NULL,
  `bookid` int(11) NOT NULL,
  `chapter_nr` int(11) NOT NULL DEFAULT 0,
  `title` varchar(128) NOT NULL DEFAULT '',
  `content` longblob NOT NULL,
  `headercontent` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `private_library`
--

CREATE TABLE `private_library` (
  `id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL,
  `author` varchar(128) NOT NULL DEFAULT '',
  `coverHref` varchar(256) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `packagePath` varchar(256) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `rootUrl` varchar(256) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `description` varchar(512) NOT NULL DEFAULT '',
  `genre` varchar(128) NOT NULL DEFAULT '',
  `coverloc` varchar(128) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `template` int(11) NOT NULL DEFAULT 0,
  `lang` varchar(3) NOT NULL DEFAULT 'en',
  `pagination` varchar(4) NOT NULL DEFAULT 'ltr'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `private_video`
--

CREATE TABLE `private_video` (
  `id` int(11) NOT NULL,
  `bookid` int(11) NOT NULL,
  `videofile` text NOT NULL,
  `name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `private_vimeo`
--

CREATE TABLE `private_vimeo` (
  `id` int(10) NOT NULL,
  `bookid` int(10) DEFAULT NULL,
  `vimeoLink` text DEFAULT NULL,
  `name` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `promocodes`
--

CREATE TABLE `promocodes` (
  `id` int(11) NOT NULL,
  `code` varchar(45) NOT NULL,
  `bookid` int(11) NOT NULL DEFAULT 0,
  `discount` int(11) NOT NULL DEFAULT 0,
  `free` int(11) NOT NULL DEFAULT 0,
  `maxuses` int(11) NOT NULL DEFAULT -1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `sessionid` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `libraryid` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `transactiondate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(128) NOT NULL DEFAULT '',
  `name` varchar(128) NOT NULL DEFAULT '',
  `type` varchar(128) NOT NULL DEFAULT '',
  `genre_of_writing` varchar(512) NOT NULL DEFAULT '',
  `status` varchar(45) NOT NULL DEFAULT '',
  `public_private` varchar(128) NOT NULL DEFAULT '',
  `interests` varchar(512) NOT NULL DEFAULT '',
  `sessionid` varchar(256) NOT NULL DEFAULT '',
  `password` varchar(256) NOT NULL DEFAULT '',
  `maxsessions` int(11) NOT NULL DEFAULT 1,
  `storeid` int(11) NOT NULL DEFAULT 0,
  `admin` int(5) NOT NULL DEFAULT 0,
  `allfree` int(11) NOT NULL DEFAULT 0,
  `genres` TEXT NOT NULL DEFAULT '',
  `category` varchar(256) NOT NULL DEFAULT '',
  `job` TEXT NOT NULL DEFAULT '',
  `stripe_public` varchar(64) NOT NULL DEFAULT '',
  `stripe_private` varchar(64) NOT NULL DEFAULT '',
  `registerdate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `name`, `type`, `genre_of_writing`, `status`, `public_private`, `interests`, `sessionid`, `password`, `maxsessions`, `storeid`, `admin`, `allfree`, `genres`, `category`, `job`, `stripe_public`, `stripe_private`, `registerdate`) VALUES
(1, 'admin@admin.com', 'admin', '', '', '', '', '', '', '21232f297a57a5a743894a0e4a801fc3', 1, 0, 1, 0, '', '', '', '', '','2020-07-30 08:56:43'),
(2, 'jamesmusgrave2122@att.net', 'James Musgrave', 'Author', '', '2', '', '', '', '37f40b53458b52d884fc22ed04b24857', 1, 1, 1, 1, '', '', '', '', '', '2020-07-30 09:00:02');

-- --------------------------------------------------------

--
-- Table structure for table `user_library`
--

CREATE TABLE `user_library` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `libraryid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `emailtemplates`
--
ALTER TABLE `emailtemplates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `library`
--
ALTER TABLE `library`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `private_audio`
--
ALTER TABLE `private_audio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `private_chapters`
--
ALTER TABLE `private_chapters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `private_library`
--
ALTER TABLE `private_library`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `private_video`
--
ALTER TABLE `private_video`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `private_vimeo`
--
ALTER TABLE `private_vimeo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promocodes`
--
ALTER TABLE `promocodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- Indexes for table `user_library`
--
ALTER TABLE `user_library`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `emailtemplates`
--
ALTER TABLE `emailtemplates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `library`
--
ALTER TABLE `library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `private_audio`
--
ALTER TABLE `private_audio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `private_chapters`
--
ALTER TABLE `private_chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `private_library`
--
ALTER TABLE `private_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `private_video`
--
ALTER TABLE `private_video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `private_vimeo`
--
ALTER TABLE `private_vimeo`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promocodes`
--
ALTER TABLE `promocodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_library`
--
ALTER TABLE `user_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
