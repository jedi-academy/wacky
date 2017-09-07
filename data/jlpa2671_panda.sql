-- phpMyAdmin SQL Dump
-- version 4.0.10.18
-- https://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Feb 10, 2017 at 04:04 PM
-- Server version: 5.6.35
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jlpa2671_panda`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `plants`
--

DROP TABLE IF EXISTS `plants`;
CREATE TABLE IF NOT EXISTS `plants` (
  `id` varchar(20) NOT NULL,
  `token` varchar(128) NOT NULL,
  `org` varchar(128) NOT NULL,
  `repo` varchar(128) NOT NULL,
  `branch` varchar(128) NOT NULL,
  `website` varchar(128) NOT NULL,
  `db` varchar(10) NOT NULL,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pushed` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deployed` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `balance` int(11) DEFAULT NULL,
  `boxes_bought` int(11) DEFAULT NULL,
  `parts_returned` int(11) DEFAULT NULL,
  `parts_made` int(11) DEFAULT NULL,
  `bots_built` int(11) DEFAULT NULL,
  `last_made` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `plants`
--

INSERT INTO `plants` (`id`, `token`, `org`, `repo`, `branch`, `website`, `db`, `updated`, `pushed`, `deployed`, `balance`, `boxes_bought`, `parts_returned`, `parts_made`, `bots_built`, `last_made`) VALUES
('apple', '22156b', '', '', '', '', 'a01', '2017-02-09 11:16:32', '2017-02-09 11:16:32', '2017-02-09 11:16:32', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:16:32'),
('banana', '117c11', '', '', '', '', 'a02', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('cherry', '4a7ce8', '', '', '', '', 'a03', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('durian', '28e10d', '', '', '', '', 'a04', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('elderberry', '47d1c9', '', '', '', '', 'a05', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('fig', '35b7b0', '', '', '', '', 'a06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('goji', '2e70e9', '', '', '', '', 'a07', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('house', '$2y$10$hYgFjoHfVvUl7jRJv3kP/.3YV/zGlLGKSqxMfBfdPqDG/tvUhzPdi', 'jedi-academy', 'umbrella', 'master', '', 'panda', '2017-02-09 11:15:28', '2017-02-09 11:15:28', '2017-02-09 11:15:28', 0, 0, 0, 0, 0, '2017-02-09 11:15:28'),
('huckleberry', '23bdec', '', '', '', '', 'a08', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('jambul', '17f8ff', '', '', '', '', 'a09', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('kiwi', '291f97', '', '', '', '', 'a10', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('lemon', '36bec6', '', '', '', '', 'a11', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('mango', '1c96d5', '', '', '', '', 'a12', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('nectarine', '11bf83', '', '', '', '', 'a13', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('olive', '1ea799', '', '', '', '', 'b01', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('papaya', '247843', '', '', '', '', 'b02', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('quince', '38908f', '', '', '', '', 'b03', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('raspberry', '18070e', '', '', '', '', 'b04', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('strawberry', '415157', '', '', '', '', 'b05', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('tamarind', '1f511c', '', '', '', '', 'b06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('ugli', '48fd21', '', '', '', '', 'b07', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('yuzu', '4676bd', '', '', '', '', 'b08', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06'),
('zuchhini', '49bd9f', '', '', '', '', 'b09', '2017-02-09 11:18:06', '2017-02-09 11:18:06', '2017-02-09 11:18:06', NULL, NULL, NULL, NULL, NULL, '2017-02-09 11:18:06');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
CREATE TABLE IF NOT EXISTS `properties` (
  `id` varchar(16) NOT NULL,
  `value` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `value`) VALUES
('alarm', '1460659500'),
('next_event', '0'),
('potd', 'tuesday'),
('priceperpack', '20'),
('round', '31'),
('startcash', '100'),
('state', '3');

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

DROP TABLE IF EXISTS `series`;
CREATE TABLE IF NOT EXISTS `series` (
  `code` int(2) NOT NULL DEFAULT '0',
  `description` varchar(16) DEFAULT NULL,
  `frequency` int(3) DEFAULT NULL,
  `value` int(3) DEFAULT NULL,
  `starts` varchar(1) NOT NULL,
  `ends` varchar(1) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `series`
--

INSERT INTO `series` (`code`, `description`, `frequency`, `value`, `starts`, `ends`) VALUES
(11, 'Basic house bots', 100, 20, 'a', 'l'),
(13, 'House butlers', 50, 50, 'm', 'w'),
(26, 'Home companions', 20, 200, 'x', 'z');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
