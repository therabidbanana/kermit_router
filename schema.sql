-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 10, 2009 at 06:09 PM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `rflow`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_host` tinyint(1) NOT NULL,
  `ip` varchar(15) DEFAULT '',
  `service` varchar(255) DEFAULT NULL,
  `level` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `akteth`
--

CREATE TABLE IF NOT EXISTS `akteth` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(17) NOT NULL DEFAULT '',
  `mac` varchar(17) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL DEFAULT '',
  `lasttraffic` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `device` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aktrouter`
--

CREATE TABLE IF NOT EXISTS `aktrouter` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(17) NOT NULL DEFAULT '',
  `flowsequenz` int(11) NOT NULL DEFAULT '0',
  `lastflow` varchar(7) NOT NULL DEFAULT '0',
  `ploss` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `globals`
--

CREATE TABLE IF NOT EXISTS `globals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `value` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `kermit_host`
--

CREATE TABLE IF NOT EXISTS `kermit_host` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `mac` varchar(17) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `allowed` int(1) NOT NULL DEFAULT '0',
  `image` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac` (`mac`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xmlrpc_call` varchar(255) DEFAULT NULL,
  `args` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `xmlrpc_return` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `speed`
--

CREATE TABLE IF NOT EXISTS `speed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `up_mbps` float NOT NULL,
  `down_mbps` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `totaltraffic`
--
CREATE TABLE IF NOT EXISTS `totaltraffic` (
`ID` int(11)
,`zeit` varchar(10)
,`datum` varchar(10)
,`name` varchar(100)
,`uloktets` int(11)
,`dloktets` int(11)
,`porttraffic` text
,`mac` varchar(17)
,`ip` varchar(15)
,`device` varchar(10)
,`oktets` int(11)
,`srcip` varchar(15)
,`dstip` varchar(15)
,`srcport` varchar(11)
,`dstport` varchar(11)
,`min` varchar(10)
,`max` varchar(10)
,`bytes` decimal(32,0)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `total_traffic`
--
CREATE TABLE IF NOT EXISTS `total_traffic` (
`ID` int(11)
,`zeit` varchar(10)
,`datum` varchar(10)
,`name` varchar(100)
,`uloktets` int(11)
,`dloktets` int(11)
,`porttraffic` text
,`mac` varchar(17)
,`ip` varchar(15)
,`device` varchar(10)
,`oktets` int(11)
,`srcip` varchar(15)
,`dstip` varchar(15)
,`srcport` varchar(11)
,`dstport` varchar(11)
,`min` varchar(10)
,`max` varchar(10)
,`bytes` decimal(32,0)
);
-- --------------------------------------------------------

--
-- Table structure for table `traffic`
--

CREATE TABLE IF NOT EXISTS `traffic` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `zeit` varchar(10) NOT NULL DEFAULT '',
  `datum` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `uloktets` int(11) NOT NULL DEFAULT '0',
  `dloktets` int(11) NOT NULL DEFAULT '0',
  `porttraffic` text NOT NULL,
  `mac` varchar(17) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `device` varchar(10) NOT NULL DEFAULT '',
  `oktets` int(11) NOT NULL DEFAULT '0',
  `srcip` varchar(15) NOT NULL DEFAULT '',
  `dstip` varchar(15) NOT NULL DEFAULT '',
  `srcport` varchar(11) NOT NULL DEFAULT '',
  `dstport` varchar(11) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `traffic_history`
--

CREATE TABLE IF NOT EXISTS `traffic_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `up` int(10) NOT NULL DEFAULT '0',
  `down` int(10) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL,
  `up_avg` int(10) NOT NULL DEFAULT '0',
  `down_avg` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wlanclients`
--

CREATE TABLE IF NOT EXISTS `wlanclients` (
  `id` int(11) NOT NULL DEFAULT '0',
  `mac` varchar(17) NOT NULL DEFAULT '',
  `rssi` varchar(5) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL DEFAULT '',
  `location` varchar(5) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure for view `totaltraffic`
--
DROP TABLE IF EXISTS `totaltraffic`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `totaltraffic` AS select `traffic`.`ID` AS `ID`,`traffic`.`zeit` AS `zeit`,`traffic`.`datum` AS `datum`,`traffic`.`name` AS `name`,`traffic`.`uloktets` AS `uloktets`,`traffic`.`dloktets` AS `dloktets`,`traffic`.`porttraffic` AS `porttraffic`,`traffic`.`mac` AS `mac`,`traffic`.`ip` AS `ip`,`traffic`.`device` AS `device`,`traffic`.`oktets` AS `oktets`,`traffic`.`srcip` AS `srcip`,`traffic`.`dstip` AS `dstip`,`traffic`.`srcport` AS `srcport`,`traffic`.`dstport` AS `dstport`,min(`traffic`.`zeit`) AS `min`,max(`traffic`.`zeit`) AS `max`,sum(`traffic`.`oktets`) AS `bytes` from `traffic` group by `traffic`.`srcip`;

-- --------------------------------------------------------

--
-- Structure for view `total_traffic`
--
DROP TABLE IF EXISTS `total_traffic`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `total_traffic` AS select `traffic`.`ID` AS `ID`,`traffic`.`zeit` AS `zeit`,`traffic`.`datum` AS `datum`,`traffic`.`name` AS `name`,`traffic`.`uloktets` AS `uloktets`,`traffic`.`dloktets` AS `dloktets`,`traffic`.`porttraffic` AS `porttraffic`,`traffic`.`mac` AS `mac`,`traffic`.`ip` AS `ip`,`traffic`.`device` AS `device`,`traffic`.`oktets` AS `oktets`,`traffic`.`srcip` AS `srcip`,`traffic`.`dstip` AS `dstip`,`traffic`.`srcport` AS `srcport`,`traffic`.`dstport` AS `dstport`,min(`traffic`.`zeit`) AS `min`,max(`traffic`.`zeit`) AS `max`,sum(`traffic`.`oktets`) AS `bytes` from `traffic` group by concat(concat(`traffic`.`srcip`,`traffic`.`dstip`),`traffic`.`datum`);
