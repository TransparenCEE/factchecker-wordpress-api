-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 21, 2016 at 01:07 PM
-- Server version: 5.6.29-76.2-log
-- PHP Version: 5.4.16

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `factualdev`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_factchecks`
--

CREATE TABLE IF NOT EXISTS `api_factchecks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_factcheck` bigint(20) NOT NULL,
  `q` varchar(32) NOT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `insert_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_factcheck` (`id_factcheck`),
  KEY `q` (`q`),
  KEY `insert_datetime` (`insert_datetime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1684380 ;

-- --------------------------------------------------------

--
-- Table structure for table `factcheck_content`
--

CREATE TABLE IF NOT EXISTS `factcheck_content` (
  `id_factcheck` bigint(20) NOT NULL AUTO_INCREMENT,
  `factcheck_link` varchar(255) NOT NULL,
  `import_datetime` datetime NOT NULL,
  `update_import_datetime` datetime NOT NULL,
  `ID` bigint(20) NOT NULL,
  `post_title` text NOT NULL,
  `post_name` varchar(200) NOT NULL,
  `post_type` varchar(20) NOT NULL,
  `context` longtext NOT NULL,
  `declaratie` longtext NOT NULL,
  `status` varchar(255) NOT NULL,
  `concluzie` longtext NOT NULL,
  `ce_verificam` varchar(255) NOT NULL,
  `verificare` longtext NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `url_sursa` varchar(255) NOT NULL,
  `post_datetime` datetime NOT NULL,
  `post_modified_datetime` datetime NOT NULL,
  PRIMARY KEY (`id_factcheck`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=151 ;

-- --------------------------------------------------------

--
-- Table structure for table `factcheck_content2links`
--

CREATE TABLE IF NOT EXISTS `factcheck_content2links` (
  `id_link` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_factcheck` bigint(20) NOT NULL,
  `id_post` bigint(20) NOT NULL,
  `link_content` varchar(255) NOT NULL,
  `link_identifier` varchar(255) NOT NULL,
  `md5_link_identifier` varchar(255) NOT NULL,
  `snipped` text,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `insert_datetime` datetime NOT NULL,
  PRIMARY KEY (`id_link`),
  UNIQUE KEY `id_post_2` (`id_post`,`md5_link_identifier`),
  KEY `md5_link_identifier` (`md5_link_identifier`),
  KEY `id_factcheck` (`id_factcheck`),
  KEY `id_post` (`id_post`),
  KEY `md5_link_identifier_2` (`md5_link_identifier`,`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
