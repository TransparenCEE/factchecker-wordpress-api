-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2016 at 09:29 AM
-- Server version: 5.6.29-76.2-log
-- PHP Version: 5.4.16

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `factualdev2`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_audit`
--

CREATE TABLE IF NOT EXISTS `admin_audit` (
  `audit_id` int(11) NOT NULL AUTO_INCREMENT,
  `audit_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `audit_user_id` int(11) NOT NULL DEFAULT '0',
  `audit_ip` varchar(16) NOT NULL DEFAULT '',
  `audit_page_name` varchar(50) NOT NULL DEFAULT '',
  `audit_timestamp` int(11) NOT NULL DEFAULT '0',
  `audit_session_ident` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`audit_id`),
  KEY `audit_session_ident` (`audit_session_ident`),
  KEY `audit_date` (`audit_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `admin_menu_celkotranstable`
--

CREATE TABLE IF NOT EXISTS `admin_menu_celkotranstable` (
  `IDTransaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `TableName` tinytext,
  `Differ` tinytext,
  `InTransaction` tinyint(1) DEFAULT NULL,
  `TStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`IDTransaction`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `admin_menu_celkotranstable`
--

INSERT INTO `admin_menu_celkotranstable` (`IDTransaction`, `TableName`, `Differ`, `InTransaction`, `TStamp`) VALUES
(5, 'factualdev.admin_pages', '', 0, '2016-09-29 06:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `admin_pages`
--

CREATE TABLE IF NOT EXISTS `admin_pages` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `NSLeft` int(10) unsigned DEFAULT '0',
  `NSRight` int(10) unsigned DEFAULT '0',
  `NSLevel` int(10) unsigned DEFAULT '0',
  `NSOrder` int(10) unsigned DEFAULT '1',
  `NSDiffer` tinytext,
  `NSIgnore` int(10) unsigned DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `id_zone` int(11) NOT NULL DEFAULT '0',
  `is_visible` enum('Y','N') NOT NULL DEFAULT 'Y',
  `id_page_inherited_rights` int(11) NOT NULL DEFAULT '0',
  `is_blocked` enum('Y','N') NOT NULL DEFAULT 'N',
  `deals_with_rights` enum('Y','N') NOT NULL DEFAULT 'N',
  `get` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `NSLeft` (`NSLeft`),
  KEY `NSRight` (`NSRight`),
  KEY `NSLevel` (`NSLevel`),
  KEY `NSIgnore` (`NSIgnore`),
  KEY `name` (`name`),
  KEY `url_identifier` (`url`),
  KEY `NSOrder` (`NSOrder`),
  KEY `is_visible` (`is_visible`),
  KEY `is_blocked` (`is_blocked`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

--
-- Dumping data for table `admin_pages`
--

INSERT INTO `admin_pages` (`id`, `parent_id`, `NSLeft`, `NSRight`, `NSLevel`, `NSOrder`, `NSDiffer`, `NSIgnore`, `name`, `url`, `id_zone`, `is_visible`, `id_page_inherited_rights`, `is_blocked`, `deals_with_rights`, `get`) VALUES
(1, 0, 1, 168, 0, 1, '', 0, 'root_category', '', 0, 'N', 0, 'N', 'N', ''),
(2, 1, 2, 5, 1, 1, '', 0, 'Admin sessions', 'admin_sessions.php', 1, 'Y', 0, 'Y', 'N', ''),
(3, 2, 3, 4, 2, 1, '', 0, 'Admin Sessions Details', 'admin_session_details.php', 1, 'Y', 2, 'Y', 'N', ''),
(4, 1, 26, 33, 1, 55, '', 0, 'Admin Pages', 'admin_pages.php', 1, 'Y', 0, 'Y', 'N', ''),
(5, 4, 27, 28, 2, 3, '', 0, 'Add Page', 'admin_pages_add_modify.php', 1, 'Y', 0, 'Y', 'N', ''),
(6, 4, 29, 30, 2, 2, '', 0, 'Admin Pages exec', 'admin_pages_exec.php', 1, 'Y', 4, 'Y', 'N', ''),
(7, 4, 31, 32, 2, 1, '', 0, 'List Pages', 'admin_pages.php', 1, 'Y', 0, 'Y', 'N', ''),
(8, 1, 34, 41, 1, 56, '', 0, 'Admin Users', 'admin_users.php', 1, 'Y', 0, 'N', 'N', ''),
(12, 8, 35, 38, 2, 3, '', 0, 'List users', 'admin_users.php', 1, 'Y', 0, 'N', 'N', ''),
(13, 8, 39, 40, 2, 4, '', 0, 'Add user', 'admin_users_add_modify.php', 1, 'Y', 0, 'N', 'N', ''),
(14, 12, 36, 37, 3, 1, '', 0, 'Admin users exec', 'admin_users_exec.php', 1, 'Y', 12, 'N', 'N', ''),
(18, 1, 6, 9, 1, 57, '', 0, 'Import Factchecks', 'factcheck_content.php', 1, 'Y', 0, 'N', 'N', ''),
(21, 1, 10, 15, 1, 58, '', 0, 'Factchecks List', 'factchecks_list.php', 1, 'Y', 0, 'N', 'N', ''),
(20, 18, 7, 8, 2, 2, '', 0, 'factcheck_content_exec', 'factcheck_content_exec.php', 1, 'Y', 18, 'N', 'N', ''),
(22, 21, 11, 12, 2, 1, '', 0, 'factchecks_list_exec.php', 'factchecks_list_exec.php', 1, 'Y', 21, 'N', 'N', ''),
(23, 21, 13, 14, 2, 2, '', 0, 'factchecks_list_ajax.php', 'factchecks_list_ajax.php', 1, 'Y', 21, 'N', 'N', ''),
(24, 1, 16, 19, 1, 59, '', 0, 'Factchechs Links', 'factchecks_links_list.php', 1, 'Y', 0, 'N', 'N', ''),
(25, 24, 17, 18, 2, 1, '', 0, 'factchecks links list ajax', 'factchecks_links_list_ajax.php', 1, 'Y', 24, 'N', 'N', ''),
(26, 1, 20, 21, 1, 60, '', 0, 'factchecks_csv_import_ajax.php', 'factchecks_csv_import_ajax.php', 1, 'Y', 21, 'N', 'N', ''),
(27, 1, 22, 23, 1, 61, '', 0, 'API stats', 'api_stats.php', 1, 'Y', 0, 'N', 'N', ''),
(28, 1, 24, 25, 1, 62, '', 0, 'API stats exec', 'api_stats_exec.php', 1, 'Y', 27, 'N', 'N', '');

-- --------------------------------------------------------

--
-- Table structure for table `admin_pages_seq`
--

CREATE TABLE IF NOT EXISTS `admin_pages_seq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

--
-- Dumping data for table `admin_pages_seq`
--

INSERT INTO `admin_pages_seq` (`id`) VALUES
(28);

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id_user` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `cookie_user_login` varchar(32) NOT NULL DEFAULT '',
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `admin_order` int(11) NOT NULL,
  PRIMARY KEY (`id_user`),
  KEY `username` (`username`),
  KEY `password` (`password`),
  KEY `cookie_user_login` (`cookie_user_login`),
  KEY `email` (`email`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id_user`, `username`, `password`, `email`, `first_name`, `last_name`, `cookie_user_login`, `is_active`, `admin_order`) VALUES
(1, 'admin', 'test', 'admin@test.com', 'admin', 'test', 'f9ed16b604708667c7ecffb493a54e31', 'Y', 0);

-- --------------------------------------------------------

--
-- Table structure for table `admin_users_2_pages`
--

CREATE TABLE IF NOT EXISTS `admin_users_2_pages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) NOT NULL DEFAULT '0',
  `page_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`,`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16478 ;

--
-- Dumping data for table `admin_users_2_pages`
--

INSERT INTO `admin_users_2_pages` (`id`, `id_user`, `page_id`) VALUES
(16476, 1, 24),
(16475, 1, 21),
(16474, 1, 18),
(16473, 1, 13),
(16472, 1, 12),
(16471, 1, 8),
(16470, 1, 5),
(16469, 1, 7),
(16468, 1, 4),
(16477, 1, 27);

-- --------------------------------------------------------

--
-- Table structure for table `admin_users_seq`
--

CREATE TABLE IF NOT EXISTS `admin_users_seq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin_users_seq`
--

INSERT INTO `admin_users_seq` (`id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_zones`
--

CREATE TABLE IF NOT EXISTS `admin_zones` (
  `zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`zone_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `api_factchecks`
--

CREATE TABLE IF NOT EXISTS `api_factchecks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_factcheck` bigint(20) NOT NULL,
  `q` varchar(32) NOT NULL,
  `user` varchar(100) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `insert_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_factcheck` (`id_factcheck`),
  KEY `q` (`q`),
  KEY `insert_datetime` (`insert_datetime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=440 ;

-- --------------------------------------------------------

--
-- Table structure for table `api_requests`
--

CREATE TABLE IF NOT EXISTS `api_requests` (
  `api_requests` bigint(20) NOT NULL AUTO_INCREMENT,
  `q` varchar(50) DEFAULT NULL,
  `user` varchar(32) DEFAULT NULL,
  `items` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(50) DEFAULT NULL,
  `request` varchar(200) DEFAULT NULL,
  `error` enum('N','Y') NOT NULL DEFAULT 'N',
  `message` varchar(150) DEFAULT NULL,
  `server` text,
  `insert_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`api_requests`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9602 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=161 ;

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
  `snippet` text,
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `insert_datetime` datetime NOT NULL,
  PRIMARY KEY (`id_link`),
  UNIQUE KEY `id_post_2` (`id_post`,`md5_link_identifier`),
  KEY `md5_link_identifier` (`md5_link_identifier`),
  KEY `id_factcheck` (`id_factcheck`),
  KEY `id_post` (`id_post`),
  KEY `md5_link_identifier_2` (`md5_link_identifier`,`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `factcheck_content2links_seq`
--

CREATE TABLE IF NOT EXISTS `factcheck_content2links_seq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `general_values`
--

CREATE TABLE IF NOT EXISTS `general_values` (
  `var_name` varchar(255) NOT NULL,
  `var_value` longtext NOT NULL,
  UNIQUE KEY `var_name` (`var_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
