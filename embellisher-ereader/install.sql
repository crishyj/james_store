-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Feb 01, 2016 at 12:39 AM
-- Server version: 5.5.47-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `emrepubl_ereader`
--

-- --------------------------------------------------------

--
-- Table structure for table `private_audio`
--

CREATE TABLE IF NOT EXISTS `private_audio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bookid` int(11) NOT NULL,
  `audiofile` varchar(256) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `private_chapters`
--

CREATE TABLE IF NOT EXISTS `private_chapters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bookid` int(11) NOT NULL,
  `chapter_nr` int(11) NOT NULL DEFAULT '',
  `title` varchar(128) NOT NULL DEFAULT '',
  `content` longblob NOT NULL DEFAULT '',
  `headercontent` blob NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `private_library`
--

CREATE TABLE IF NOT EXISTS `private_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL,
  `author` varchar(128) NOT NULL DEFAULT '',
  `coverHref` varchar(256) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `packagePath` varchar(256) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `rootUrl` varchar(256) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `description` varchar(512) NOT NULL DEFAULT '',
  `genre` varchar(128) NOT NULL DEFAULT '',
  `coverloc` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `template` int(11) NOT NULL DEFAULT '0',
  `lang` varchar(3) NOT NULL DEFAULT 'en',
  `pagination` varchar(4) NOT NULL DEFAULT 'ltr',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
CREATE TABLE `private_audio` (
  `id` int(11) NOT NULL,
  `bookid` int(11) NOT NULL,
  `audiofile` varchar(256) NOT NULL,
  `name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `private_chapters` (
  `id` int(11) NOT NULL,
  `bookid` int(11) NOT NULL,
  `chapter_nr` int(11) NOT NULL DEFAULT 0,
  `title` varchar(128) NOT NULL DEFAULT '',
  `content` longblob NOT NULL DEFAULT '',
  `headercontent` blob NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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


ALTER TABLE `private_audio`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `private_chapters`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `private_library`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `private_audio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `private_chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `private_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
