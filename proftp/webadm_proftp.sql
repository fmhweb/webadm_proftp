-- phpMyAdmin SQL Dump
-- version 4.2.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 23. Okt 2014 um 03:48
-- Server Version: 5.5.37-MariaDB
-- PHP-Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `webadm_proftp`
--
DROP DATABASE `webadm_proftp`;
CREATE DATABASE IF NOT EXISTS `webadm_proftp` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `webadm_proftp`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `folders`
--

DROP TABLE IF EXISTS `folders`;
CREATE TABLE IF NOT EXISTS `folders` (
`id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `path` varchar(250) NOT NULL,
  `name` varchar(50) NOT NULL,
  `depth` int(11) NOT NULL DEFAULT '0',
  `created_by` varchar(30) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=498 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `groupacl`
--

DROP TABLE IF EXISTS `groupacl`;
CREATE TABLE IF NOT EXISTS `groupacl` (
  `groupid` varchar(30) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL,
  `read_acl` varchar(5) NOT NULL DEFAULT 'false',
  `write_acl` varchar(5) NOT NULL DEFAULT 'false',
  `delete_acl` varchar(5) NOT NULL DEFAULT 'false',
  `create_acl` varchar(5) NOT NULL DEFAULT 'false',
  `modify_acl` varchar(5) NOT NULL DEFAULT 'false',
  `move_acl` varchar(5) NOT NULL DEFAULT 'false',
  `view_acl` varchar(5) NOT NULL DEFAULT 'false',
  `navigate_acl` varchar(5) NOT NULL DEFAULT 'false',
  `created_by` varchar(30) NOT NULL,
  `updated_by` varchar(30) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `groupmembers`
--

DROP TABLE IF EXISTS `groupmembers`;
CREATE TABLE IF NOT EXISTS `groupmembers` (
  `groupid` varchar(30) NOT NULL,
  `userid` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `groupid` varchar(30) NOT NULL,
`gid` int(11) NOT NULL,
  `members` text,
  `type` enum('init','user') NOT NULL,
  `comment` text NOT NULL,
  `created_by` varchar(30) NOT NULL,
  `updated_by` varchar(30) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=10003 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `guicmds`
--

DROP TABLE IF EXISTS `guicmds`;
CREATE TABLE IF NOT EXISTS `guicmds` (
`id` int(11) NOT NULL,
  `command` varchar(25) NOT NULL,
  `params` text NOT NULL,
  `result` varchar(250) NOT NULL DEFAULT '',
  `created_by` varchar(30) NOT NULL,
  `created` datetime NOT NULL,
  `completed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=open,1=failed,2=completed'
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `guihistory`
--

DROP TABLE IF EXISTS `guihistory`;
CREATE TABLE IF NOT EXISTS `guihistory` (
  `entry` varchar(255) NOT NULL,
  `guiuserid` varchar(30) NOT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `guisettings`
--

DROP TABLE IF EXISTS `guisettings`;
CREATE TABLE IF NOT EXISTS `guisettings` (
  `time_check_folder` int(11) NOT NULL,
  `time_delete_logs` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `guiusers`
--

DROP TABLE IF EXISTS `guiusers`;
CREATE TABLE IF NOT EXISTS `guiusers` (
  `username` varchar(50) NOT NULL,
  `edit_folder` tinyint(4) NOT NULL DEFAULT '0',
  `edit_user` tinyint(4) NOT NULL DEFAULT '0',
  `edit_settings` tinyint(4) NOT NULL DEFAULT '0',
  `chart_animate` tinyint(4) NOT NULL DEFAULT '0',
  `max_list_items` smallint(3) NOT NULL DEFAULT '20',
  `max_list_log_items` smallint(3) NOT NULL DEFAULT '50',
  `created` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hostkeys`
--

DROP TABLE IF EXISTS `hostkeys`;
CREATE TABLE IF NOT EXISTS `hostkeys` (
  `host` varchar(30) NOT NULL,
  `key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `quotalimits`
--

DROP TABLE IF EXISTS `quotalimits`;
CREATE TABLE IF NOT EXISTS `quotalimits` (
  `nameid` varchar(30) DEFAULT NULL,
  `quota_type` enum('user','group','class','all') NOT NULL,
  `per_session` enum('false','true') NOT NULL,
  `limit_type` enum('soft','hard') NOT NULL,
  `bytes_in_avail` float NOT NULL DEFAULT '10000000000',
  `bytes_out_avail` float NOT NULL DEFAULT '10000000000',
  `bytes_xfer_avail` float NOT NULL DEFAULT '10000000000',
  `files_in_avail` int(10) unsigned NOT NULL DEFAULT '1000',
  `files_out_avail` int(10) unsigned NOT NULL DEFAULT '1000',
  `files_xfer_avail` int(10) unsigned NOT NULL DEFAULT '1000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `quotatallies`
--

DROP TABLE IF EXISTS `quotatallies`;
CREATE TABLE IF NOT EXISTS `quotatallies` (
  `nameid` varchar(30) NOT NULL,
  `quota_type` enum('user','group','class','all') NOT NULL,
  `bytes_in_used` float NOT NULL,
  `bytes_out_used` float NOT NULL,
  `bytes_xfer_used` float NOT NULL,
  `files_in_used` int(10) unsigned NOT NULL,
  `files_out_used` int(10) unsigned NOT NULL,
  `files_xfer_used` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `useracl`
--

DROP TABLE IF EXISTS `useracl`;
CREATE TABLE IF NOT EXISTS `useracl` (
  `userid` varchar(30) NOT NULL,
  `groupid` varchar(30) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL,
  `type` enum('user','group') NOT NULL,
  `read_acl` varchar(5) NOT NULL DEFAULT 'false',
  `write_acl` varchar(5) NOT NULL DEFAULT 'false',
  `delete_acl` varchar(5) NOT NULL DEFAULT 'false',
  `create_acl` varchar(5) NOT NULL DEFAULT 'false',
  `modify_acl` varchar(5) NOT NULL DEFAULT 'false',
  `move_acl` varchar(5) NOT NULL DEFAULT 'false',
  `view_acl` varchar(5) NOT NULL DEFAULT 'false',
  `navigate_acl` varchar(5) NOT NULL DEFAULT 'false',
  `created_by` varchar(30) NOT NULL,
  `updated_by` varchar(30) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userkeys`
--

DROP TABLE IF EXISTS `userkeys`;
CREATE TABLE IF NOT EXISTS `userkeys` (
  `userid` varchar(30) NOT NULL,
  `key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userid` varchar(30) NOT NULL,
  `passwd` varchar(80) NOT NULL,
  `surname` varchar(50) NOT NULL DEFAULT '',
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `telephon` varchar(20) NOT NULL,
  `company` varchar(50) NOT NULL DEFAULT '',
  `address` text NOT NULL,
  `comment` text NOT NULL,
`uid` int(11) NOT NULL,
  `gid` int(11) DEFAULT NULL,
  `homedir` varchar(255) DEFAULT NULL,
  `shell` varchar(25) DEFAULT '/bin/sh',
  `created_by` varchar(30) NOT NULL,
  `updated_by` varchar(30) NOT NULL DEFAULT 'n/a',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expires` datetime DEFAULT '0000-00-00 00:00:00',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `login` datetime DEFAULT '0000-00-00 00:00:00',
  `logout` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login_count` int(7) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=10005 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `xfer`
--

DROP TABLE IF EXISTS `xfer`;
CREATE TABLE IF NOT EXISTS `xfer` (
  `userid` varchar(30) NOT NULL,
  `command` varchar(5) NOT NULL,
  `response` varchar(30) NOT NULL DEFAULT '',
  `filename` text NOT NULL,
  `size` bigint(20) NOT NULL DEFAULT '0',
  `hostname` text NOT NULL,
  `ip` text NOT NULL,
  `timespent` float NOT NULL,
  `time` datetime NOT NULL,
  `errorlevel` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
 ADD PRIMARY KEY (`path`), ADD UNIQUE KEY `path` (`path`), ADD KEY `id` (`id`);

--
-- Indexes for table `groupacl`
--
ALTER TABLE `groupacl`
 ADD PRIMARY KEY (`groupid`), ADD KEY `acl_path_idx` (`path`), ADD KEY `path` (`path`);

--
-- Indexes for table `groupmembers`
--
ALTER TABLE `groupmembers`
 ADD KEY `FK_member_group` (`groupid`), ADD KEY `FK_member_user` (`userid`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
 ADD UNIQUE KEY `groupid` (`groupid`), ADD KEY `groups_gid_idx` (`gid`);

--
-- Indexes for table `guicmds`
--
ALTER TABLE `guicmds`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostkeys`
--
ALTER TABLE `hostkeys`
 ADD KEY `hostkeys_idx` (`host`);

--
-- Indexes for table `quotalimits`
--
ALTER TABLE `quotalimits`
 ADD KEY `FK_quotalimits` (`nameid`);

--
-- Indexes for table `quotatallies`
--
ALTER TABLE `quotatallies`
 ADD KEY `FK_quotatallies` (`nameid`);

--
-- Indexes for table `useracl`
--
ALTER TABLE `useracl`
 ADD KEY `acl_path_idx` (`path`), ADD KEY `userid` (`userid`), ADD KEY `path` (`path`), ADD KEY `userid_2` (`userid`), ADD KEY `groupid` (`groupid`);

--
-- Indexes for table `userkeys`
--
ALTER TABLE `userkeys`
 ADD KEY `userkeys_idx` (`userid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`userid`), ADD UNIQUE KEY `userid` (`userid`), ADD UNIQUE KEY `uid` (`uid`), ADD KEY `users_userid_idx` (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `groups`
MODIFY `gid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10000;
--
-- AUTO_INCREMENT for table `guicmds`
--
ALTER TABLE `guicmds`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10000;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
