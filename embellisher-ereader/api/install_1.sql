/*
SQLyog Community v13.1.2 (64 bit)
MySQL - 10.1.38-MariaDB : Database - ereader
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ereader` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `ereader`;

/*Table structure for table `emailtemplates` */

DROP TABLE IF EXISTS `emailtemplates`;

CREATE TABLE `emailtemplates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `content` longtext NOT NULL,
  `day` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `emailtemplates` */

/*Table structure for table `library` */

DROP TABLE IF EXISTS `library`;

CREATE TABLE `library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL DEFAULT '',
  `author` varchar(128) NOT NULL DEFAULT '',
  `coverHref` varchar(256) NOT NULL DEFAULT '',
  `packagePath` varchar(256) NOT NULL DEFAULT '',
  `rootUrl` varchar(256) NOT NULL DEFAULT '',
  `price` varchar(45) NOT NULL DEFAULT '0',
  `description` varchar(512) NOT NULL DEFAULT '',
  `genre` varchar(128) NOT NULL DEFAULT '',
  `preload` int(11) NOT NULL DEFAULT '0',
  `owner` int(11) NOT NULL DEFAULT '0',
  `excerpt` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `library` */

/*Table structure for table `private_audio` */

DROP TABLE IF EXISTS `private_audio`;

CREATE TABLE `private_audio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bookid` int(11) NOT NULL,
  `audiofile` varchar(256) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `private_audio` */

/*Table structure for table `private_chapters` */

DROP TABLE IF EXISTS `private_chapters`;

CREATE TABLE `private_chapters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bookid` int(11) NOT NULL,
  `chapter_nr` int(11) NOT NULL DEFAULT '0',
  `title` varchar(128) NOT NULL DEFAULT '',
  `content` longblob NOT NULL,
  `headercontent` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `private_chapters` */

/*Table structure for table `private_library` */

DROP TABLE IF EXISTS `private_library`;

CREATE TABLE `private_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL,
  `author` varchar(128) NOT NULL DEFAULT '',
  `coverHref` varchar(256) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `packagePath` varchar(256) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `rootUrl` varchar(256) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `description` varchar(512) NOT NULL DEFAULT '',
  `genre` varchar(128) NOT NULL DEFAULT '',
  `coverloc` varchar(128) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `template` int(11) NOT NULL DEFAULT '0',
  `lang` varchar(3) NOT NULL DEFAULT 'en',
  `pagination` varchar(4) NOT NULL DEFAULT 'ltr',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `private_library` */

/*Table structure for table `private_video` */

DROP TABLE IF EXISTS `private_video`;

CREATE TABLE `private_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bookid` int(11) NOT NULL,
  `videofile` text NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `private_video` */

/*Table structure for table `private_vimeo` */

DROP TABLE IF EXISTS `private_vimeo`;

CREATE TABLE `private_vimeo` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `bookid` int(10) DEFAULT NULL,
  `vimeoLink` text,
  `name` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `private_vimeo` */

/*Table structure for table `promocodes` */

DROP TABLE IF EXISTS `promocodes`;

CREATE TABLE `promocodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(45) NOT NULL,
  `bookid` int(11) NOT NULL DEFAULT '0',
  `discount` int(11) NOT NULL DEFAULT '0',
  `free` int(11) NOT NULL DEFAULT '0',
  `maxuses` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_UNIQUE` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `promocodes` */

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `sessionid` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `sessions` */

insert  into `sessions`(`id`,`userid`,`sessionid`) values 
(2,1,'5l1QL4tqMB');

/*Table structure for table `transactions` */

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `libraryid` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `transactiondate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `transactions` */

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL DEFAULT '',
  `name` varchar(128) NOT NULL DEFAULT '',
  `type` varchar(128) NOT NULL DEFAULT '',
  `genre_of_writing` varchar(512) NOT NULL DEFAULT '',
  `status` varchar(45) NOT NULL DEFAULT '',
  `public_private` varchar(128) NOT NULL DEFAULT '',
  `interests` varchar(512) NOT NULL DEFAULT '',
  `sessionid` varchar(256) NOT NULL DEFAULT '',
  `password` varchar(256) NOT NULL DEFAULT '',
  `maxsessions` int(11) NOT NULL DEFAULT '1',
  `storeid` int(11) NOT NULL DEFAULT '0',
  `admin` int(5) NOT NULL DEFAULT '0',
  `allfree` int(11) NOT NULL DEFAULT '0',
  `stripe_public` varchar(64) NOT NULL DEFAULT '',
  `stripe_private` varchar(64) NOT NULL DEFAULT '',
  `registerdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `user` */

insert  into `user`(`id`,`email`,`name`,`type`,`genre_of_writing`,`status`,`public_private`,`interests`,`sessionid`,`password`,`maxsessions`,`storeid`,`admin`,`allfree`,`stripe_public`,`stripe_private`,`registerdate`) values 
(1,'admin@admin.com','admin','','','','','','','$2a$07$hallothisisa22stringhOoRZCFu5RcMDLlXkme8GttQ9kKfyG98e',1,0,1,0,'','','2020-07-10 00:55:33');

/*Table structure for table `user_library` */

DROP TABLE IF EXISTS `user_library`;

CREATE TABLE `user_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `libraryid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `user_library` */

insert  into `user_library`(`id`,`userid`,`libraryid`) values 
(1,1,3);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
