-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 19, 2013 at 01:45 AM
-- Server version: 5.1.66
-- PHP Version: 5.3.19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ticket`
--

-- --------------------------------------------------------

--
-- Table structure for table `auto_code`
--

CREATE TABLE IF NOT EXISTS `auto_code` (
  `prefix` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT '1000',
  PRIMARY KEY (`prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `change_history`
--

CREATE TABLE IF NOT EXISTS `change_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `history_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `object_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `object_id` int(11) NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `object_type` (`object_type`,`object_id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=77 ;

--
-- Dumping data for table `change_history`
--

INSERT INTO `change_history` (`id`, `history_date`, `user_id`, `object_type`, `object_id`, `message`, `client_id`) VALUES
(1, '2013-03-17 23:46:49', 1, 'users', 4, 'Edit user 4 ferdhie', 0),
(2, '2013-03-17 23:49:56', 1, 'users', 5, 'Edit user 5 jokonet', 0),
(3, '2013-03-17 23:50:24', 1, 'users', 6, 'Edit user 6 aryo', 0),
(4, '2013-03-17 23:50:46', 1, 'users', 7, 'Edit user 7 asri', 0),
(5, '2013-03-17 23:51:33', 1, 'users', 8, 'Edit user 8 herry', 0),
(6, '2013-03-17 23:52:04', 1, 'users', 9, 'Edit user 9 tejo', 0),
(7, '2013-03-17 23:53:15', 1, 'users', 10, 'Edit user 10 dwi', 0),
(8, '2013-03-17 23:53:38', 1, 'users', 11, 'Edit user 11 sasmito', 0),
(9, '2013-03-17 23:54:10', 1, 'users', 12, 'Edit user 12 hasan', 0),
(10, '2013-03-17 23:55:47', 1, 'projects', 1, 'Edit project 1 LintasGPS', 0),
(11, '2013-03-17 23:56:15', 9, 'projects', 2, 'Edit project 2 Icount - Kompos', 1),
(12, '2013-03-17 23:56:17', 1, 'projects', 1, 'Edit project 1 LintasGPS', 0),
(13, '2013-03-17 23:56:23', 1, 'projects', 1, 'Edit project 1 LintasGPS', 0),
(14, '2013-03-17 23:56:34', 1, 'projects', 1, 'Edit project 1 LintasGPS', 0),
(15, '2013-03-17 23:56:43', 1, 'projects', 1, 'Edit project 1 LintasGPS', 0),
(16, '2013-03-17 23:56:51', 1, 'projects', 1, 'Edit project 1 LintasGPS', 0),
(17, '2013-03-17 23:58:08', 1, 'tasks', 1, 'edit task Array', 0),
(18, '2013-03-17 23:58:30', 1, 'tasks', 2, 'edit task Array', 0),
(19, '2013-03-17 23:58:49', 1, 'tasks', 3, 'edit task Array', 0),
(20, '2013-03-18 02:10:12', 1, 'users', 13, 'Edit user 13 mega', 0),
(21, '2013-03-18 02:20:13', 1, 'tasks', 4, 'edit task Array', 0),
(22, '2013-03-18 02:21:35', 1, 'tasks', 5, 'edit task Array', 0),
(23, '2013-03-18 02:22:07', 1, 'tasks', 1, 'mark Protokol untuk GT06 as done', 0),
(24, '2013-03-18 02:22:11', 1, 'tasks', 2, 'mark Protokol untuk GT07 as done', 0),
(25, '2013-03-18 02:22:15', 1, 'tasks', 3, 'mark Protokol AL900 as done', 0),
(26, '2013-03-18 02:22:18', 1, 'tasks', 4, 'mark Kirim command via GPRS as done', 0),
(27, '2013-03-18 02:22:20', 1, 'tasks', 5, 'mark Penanganan timezone dari device as done', 0),
(28, '2013-03-18 02:25:50', 1, 'users', 14, 'Edit user 14 test', 0),
(29, '2013-03-18 02:31:02', 1, 'groups', 6, 'Update group 6 with permission 1, 8, 38, 39, 28, 29, 30, 32, 33, 34, 35, 36, 37', 0),
(30, '2013-03-18 02:31:19', 1, 'groups', 7, 'Update group 7 with permission 1, 38, 28, 29, 30', 0),
(31, '2013-03-18 03:07:06', 1, 'perms', 40, 'Add permission 40 with name Send Message', 0),
(32, '2013-03-18 03:14:29', 1, 'perms', 40, 'Edit permission 40 with name Send Message', 0),
(33, '2013-03-18 03:16:53', 1, 'groups', 1, 'Update group 1 with permission 1, 40, 2, 6, 14, 13, 12, 3, 9, 10, 11, 5, 17, 16, 15, 4, 19, 20, 18, 8, 39, 38, 21, 22, 23, 24, 25, 26, 27, 36, 37, 28, 33, 32, 35, 30, 29, 34', 0),
(34, '2013-03-18 03:17:04', 1, 'groups', 3, 'Update group 3 with permission 1, 40, 2, 5, 17, 16, 15, 8, 39, 38, 21, 22, 23, 24, 25, 26, 27, 36, 37, 28, 33, 32, 35, 30, 29, 34', 0),
(35, '2013-03-18 03:17:15', 1, 'groups', 4, 'Update group 4 with permission 1, 40, 2, 5, 17, 16, 15, 8, 39, 38, 21, 22, 23, 24, 25, 26, 27, 36, 37, 28, 33, 32, 35, 30, 29, 34', 0),
(36, '2013-03-18 03:17:26', 1, 'groups', 5, 'Update group 5 with permission 1, 40, 8, 39, 38, 36, 37, 28, 30, 29', 0),
(37, '2013-03-18 03:17:39', 1, 'groups', 6, 'Update group 6 with permission 1, 40, 8, 39, 38, 36, 37, 28, 33, 32, 35, 30, 29, 34', 0),
(38, '2013-03-18 03:18:04', 1, 'groups', 7, 'Update group 7 with permission 1, 40, 38, 28, 30, 29', 0),
(39, '2013-03-18 03:18:11', 1, 'groups', 7, 'Update group 7 with permission 1, 38, 28, 30, 29', 0),
(40, '2013-03-18 03:18:21', 1, 'groups', 6, 'Update group 6 with permission 1, 40, 8, 39, 38, 36, 37, 28, 33, 32, 35, 30, 29, 34', 0),
(41, '2013-03-18 03:22:54', 1, 'projects', 3, 'Edit project 3 Simetri Tickets', 0),
(42, '2013-03-18 06:48:17', 1, 'tasks', 6, 'edit task Array', 0),
(43, '2013-03-18 06:49:21', 1, 'tasks', 7, 'edit task Array', 0),
(44, '2013-03-18 06:50:12', 1, 'tasks', 8, 'edit task Array', 0),
(45, '2013-03-18 06:50:45', 1, 'tasks', 9, 'edit task Array', 0),
(46, '2013-03-18 06:51:14', 1, 'tasks', 10, 'edit task Array', 0),
(47, '2013-03-18 07:08:51', 1, 'groups', 6, 'Update group 6 with permission 1, 40, 8, 39, 38, 36, 37, 28, 33, 32, 30, 29, 34', 0),
(48, '2013-03-18 07:10:54', 7, 'tickets', 1, 'mark test as done', 1),
(49, '2013-03-18 07:21:55', 4, 'tasks', 11, 'edit task Array', 1),
(50, '2013-03-18 07:22:26', 4, 'tickets', 1, 'mark test ticket as done', 1),
(51, '2013-03-18 15:02:59', 4, 'tasks', 12, 'edit task Array', 1),
(52, '2013-03-18 15:03:48', 4, 'tasks', 13, 'edit task Array', 1),
(53, '2013-03-18 15:05:30', 4, 'projects', 4, 'Edit project 4 AlatKasir POS & Accounting', 1),
(54, '2013-03-18 15:06:19', 4, 'projects', 4, 'Edit project 4 AlatKasir POS & Accounting', 1),
(55, '2013-03-18 15:07:20', 4, 'projects', 4, 'Edit project 4 AlatKasir POS & Accounting', 1),
(56, '2013-03-18 15:07:30', 4, 'projects', 4, 'Edit project 4 AlatKasir POS & Accounting', 1),
(57, '2013-03-18 15:11:44', 4, 'projects', 4, 'Edit project 4 AlatKasir POS & Accounting', 1),
(58, '2013-03-18 15:13:19', 4, 'projects', 5, 'Edit project 5 Afganworld Web & Community', 1),
(59, '2013-03-18 15:13:25', 4, 'projects', 5, 'Edit project 5 Afganworld Web & Community', 1),
(60, '2013-03-18 15:14:04', 4, 'tasks', 14, 'edit task Array', 1),
(61, '2013-03-18 15:14:04', 4, 'tasks', 14, 'edit task Array', 1),
(62, '2013-03-18 15:14:26', 4, 'tasks', 15, 'edit task Array', 1),
(63, '2013-03-18 15:14:32', 4, 'tasks', 14, 'edit task Array', 1),
(64, '2013-03-18 15:14:43', 4, 'tasks', 14, 'edit task Array', 1),
(65, '2013-03-18 15:15:36', 4, 'projects', 6, 'Edit project 6 GPSPlatinum', 1),
(66, '2013-03-18 15:15:39', 4, 'projects', 6, 'Edit project 6 GPSPlatinum', 1),
(67, '2013-03-18 15:15:41', 4, 'projects', 6, 'Edit project 6 GPSPlatinum', 1),
(68, '2013-03-18 15:16:16', 4, 'projects', 7, 'Edit project 7 Sitemon NMS', 1),
(69, '2013-03-18 15:16:22', 4, 'projects', 7, 'Edit project 7 Sitemon NMS', 1),
(70, '2013-03-18 15:16:34', 4, 'projects', 7, 'Edit project 7 Sitemon NMS', 1),
(71, '2013-03-18 15:16:40', 4, 'projects', 7, 'Edit project 7 Sitemon NMS', 1),
(72, '2013-03-18 15:17:01', 4, 'tasks', 16, 'edit task Array', 1),
(73, '2013-03-18 15:17:16', 4, 'tasks', 17, 'edit task Array', 1),
(74, '2013-03-18 15:17:45', 4, 'tasks', 18, 'edit task Array', 1),
(75, '2013-03-18 15:18:04', 4, 'tasks', 19, 'edit task Array', 1),
(76, '2013-03-18 15:19:25', 4, 'projects', 2, 'Edit project 2 Icount - Kompos', 1);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `name`, `description`) VALUES
(1, 'SIMETRI 1', 'PT Sinar Media Tiga');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `comment_text` text COLLATE utf8_unicode_ci NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `comment_date`, `comment_text`, `project_id`, `task_id`, `ticket_id`, `client_id`, `user_id`) VALUES
(1, '2013-03-18 07:22:34', 'test comment ya', 0, 0, 1, 1, 4),
(2, '2013-03-18 07:23:07', 'test lagi pake attachment', 0, 0, 1, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `filename` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `ticket_id`, `project_id`, `client_id`, `filename`, `user_id`, `comment_id`) VALUES
(1, 0, 0, 1, '1/4/mobil_contoh.png', 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `admin_group` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`),
  KEY `admin_group` (`admin_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`group_id`, `group_name`, `admin_group`) VALUES
(1, 'admin', 1),
(3, 'director', 0),
(4, 'manager', 0),
(5, 'executor', 0),
(6, 'quality', 0),
(7, 'customer', 0);

-- --------------------------------------------------------

--
-- Table structure for table `group_perms`
--

CREATE TABLE IF NOT EXISTS `group_perms` (
  `group_id` int(11) NOT NULL,
  `perm_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`perm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `group_perms`
--

INSERT INTO `group_perms` (`group_id`, `perm_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(3, 1),
(3, 2),
(3, 5),
(3, 8),
(3, 15),
(3, 16),
(3, 17),
(3, 21),
(3, 22),
(3, 23),
(3, 24),
(3, 25),
(3, 26),
(3, 27),
(3, 28),
(3, 29),
(3, 30),
(3, 32),
(3, 33),
(3, 34),
(3, 35),
(3, 36),
(3, 37),
(3, 38),
(3, 39),
(3, 40),
(4, 1),
(4, 2),
(4, 5),
(4, 8),
(4, 15),
(4, 16),
(4, 17),
(4, 21),
(4, 22),
(4, 23),
(4, 24),
(4, 25),
(4, 26),
(4, 27),
(4, 28),
(4, 29),
(4, 30),
(4, 32),
(4, 33),
(4, 34),
(4, 35),
(4, 36),
(4, 37),
(4, 38),
(4, 39),
(4, 40),
(5, 1),
(5, 8),
(5, 28),
(5, 29),
(5, 30),
(5, 36),
(5, 37),
(5, 38),
(5, 39),
(5, 40),
(6, 1),
(6, 8),
(6, 28),
(6, 29),
(6, 30),
(6, 32),
(6, 33),
(6, 34),
(6, 36),
(6, 37),
(6, 38),
(6, 39),
(6, 40),
(7, 1),
(7, 28),
(7, 29),
(7, 30),
(7, 38);

-- --------------------------------------------------------

--
-- Table structure for table `perms`
--

CREATE TABLE IF NOT EXISTS `perms` (
  `perm_id` int(11) NOT NULL AUTO_INCREMENT,
  `perm_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `perm_path` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `children_count` int(11) NOT NULL DEFAULT '0',
  `public` tinyint(4) NOT NULL DEFAULT '0',
  `perm_order` int(11) DEFAULT '0',
  `perm_class` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`perm_id`),
  KEY `parent_id` (`parent_id`),
  KEY `perm_path` (`perm_path`,`public`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=41 ;

--
-- Dumping data for table `perms`
--

INSERT INTO `perms` (`perm_id`, `perm_name`, `perm_path`, `parent_id`, `children_count`, `public`, `perm_order`, `perm_class`) VALUES
(1, 'Dashboard', 'dashboard/', 0, 1, 0, 0, ''),
(2, 'Setting', '', 0, 3, 0, 1, ''),
(3, 'Permission', 'perms/', 2, 3, 0, 0, ''),
(4, 'Groups', 'groups/', 2, 3, 0, 0, ''),
(5, 'Users', 'users/', 2, 3, 0, 0, ''),
(6, 'Clients', 'clients/', 2, 3, 0, 0, ''),
(8, 'Projects', 'projects/', 0, 9, 0, 2, ''),
(9, 'Edit Permission', 'perms/edit', 3, 0, 2, 0, ''),
(10, 'Delete Permission', 'perms/delete', 3, 0, 2, 0, ''),
(11, 'Add Permission', 'perms/add', 3, 0, 2, 0, ''),
(12, 'Add Client', 'clients/add', 6, 0, 2, 0, ''),
(13, 'Edit Client', 'clients/edit', 6, 0, 2, 0, ''),
(14, 'Delete Clients', 'clients/delete', 6, 0, 2, 0, ''),
(15, 'Edit User', 'users/edit', 5, 0, 2, 0, ''),
(16, 'Add User', 'users/add', 5, 0, 2, 0, ''),
(17, 'Delete User', 'users/delete', 5, 0, 2, 0, ''),
(18, 'Add Group', 'groups/add', 4, 0, 2, 0, ''),
(19, 'Edit Groups', 'groups/edit', 4, 0, 2, 0, ''),
(20, 'Delete Group', 'groups/delete', 4, 0, 2, 0, ''),
(21, 'Add Project', 'projects/add', 8, 0, 2, 0, ''),
(22, 'Edit Project', 'projects/edit', 8, 0, 2, 1, ''),
(23, 'Delete Project', 'projects/delete', 8, 0, 2, 2, ''),
(24, 'Add Task', 'tasks/add', 8, 0, 2, 3, ''),
(25, 'Edit Task', 'tasks/edit', 8, 0, 2, 4, ''),
(26, 'Delete Task', 'tasks/delete', 8, 0, 2, 5, ''),
(27, 'Mark Task as Done', 'tasks/done', 8, 0, 2, 6, ''),
(28, 'Tickets', 'tickets/', 0, 6, 0, 4, ''),
(29, 'My Tickets', 'tickets/', 28, 1, 0, 0, ''),
(30, 'Add Tickets', 'tickets/add', 28, 0, 2, 0, ''),
(32, 'Edit Tickets', 'tickets/edit', 28, 0, 2, 0, ''),
(33, 'Delete Tickets', 'tickets/delete', 28, 0, 2, 0, ''),
(34, 'Mark Ticket as Done', 'tickets/done', 28, 0, 2, 0, ''),
(35, 'Assign Tickets', 'tickets/assign', 28, 0, 2, 0, ''),
(36, 'Tasks', 'tasks/', 0, 1, 0, 3, ''),
(37, 'My Tasks', 'tasks/', 36, 0, 0, 0, ''),
(38, 'All Projects', 'projects/', 8, 0, 0, 0, ''),
(39, 'Project Tasks', 'projects/tasks', 8, 0, 2, 0, ''),
(40, 'Send Message', 'dashboard/broadcast', 1, 0, 0, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `created_on`, `user_id`, `client_id`) VALUES
(1, 'LintasGPS', 'LintasGPS adalah aplikasi asset tracking dan fleet management menggunakan GPS tracker, online mapping system dan mobile apps.', '2013-03-17 23:55:47', 6, 1),
(2, 'Icount - Kompos', 'cloud accouting web', '2013-03-17 23:56:15', 9, 1),
(3, 'Simetri Tickets', 'Project management and issue tracking', '2013-03-18 03:22:54', 8, 1),
(4, 'AlatKasir POS & Accounting', 'Solusi lengkap untuk personal dan small business dalam menggunakan software Point of Sale', '2013-03-18 15:05:30', 9, 1),
(5, 'Afganworld Web & Community', 'Aplikasi web untuk afganworld menggunakan wordpress & buddypress', '2013-03-18 15:13:19', 4, 1),
(6, 'GPSPlatinum', 'Rebranding LintasGPS v1 dan v3 dan admin ke GPSPLATINUM.COM', '2013-03-18 15:15:36', 6, 1),
(7, 'Sitemon NMS', 'BTS Power monitoring', '2013-03-18 15:16:16', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `timeline_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `task` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `due` date NOT NULL,
  `complete_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`,`timeline_id`,`parent_id`,`status`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `project_id`, `timeline_id`, `parent_id`, `task`, `description`, `status`, `client_id`, `user_id`, `due`, `complete_date`) VALUES
(1, 1, 1, 0, 'Protokol untuk GT06', '', 1, 1, 6, '2013-03-17', '2013-03-18 02:22:07'),
(2, 1, 1, 0, 'Protokol untuk GT07', '', 1, 1, 6, '2013-03-17', '2013-03-18 02:22:11'),
(3, 1, 1, 0, 'Protokol AL900', '', 1, 1, 6, '2013-03-17', '2013-03-18 02:22:15'),
(4, 1, 1, 0, 'Kirim command via GPRS', 'Membuat mekanisme untuk kirim command via GPRS', 1, 1, 6, '2013-03-18', '2013-03-18 02:22:17'),
(5, 1, 1, 0, 'Penanganan timezone dari device', 'Bagaimana bisa menangani timezone device yang beragam', 1, 1, 6, '2013-03-18', '2013-03-18 02:22:20'),
(6, 1, 2, 0, 'Timezone support', 'tambahkan timezone support bagi user yg login', 0, 1, 6, '2013-03-22', '0000-00-00 00:00:00'),
(7, 1, 2, 0, 'Laporan GPS', 'Menambahkan beberapa laporan', 0, 1, 6, '2013-03-22', '0000-00-00 00:00:00'),
(8, 1, 2, 7, 'Laporan rekap harian', '', 0, 1, 6, '2013-03-22', '0000-00-00 00:00:00'),
(9, 1, 2, 7, 'Laporan log', '', 0, 1, 6, '2013-03-26', '0000-00-00 00:00:00'),
(10, 1, 3, 0, 'Aplikasi Android', '', 0, 1, 6, '2013-03-29', '0000-00-00 00:00:00'),
(11, 1, 3, 10, 'tambahin lokasi device', '', 0, 1, 6, '2013-03-29', '0000-00-00 00:00:00'),
(12, 3, 4, 0, 'Attach / upload file pada saat add/edit task', 'Tambahkan upload file pas edit/add task\n', 0, 1, 8, '2013-03-29', '0000-00-00 00:00:00'),
(13, 3, 4, 0, 'View Calendar', 'Tambahkan view calendar di List Task, jadi nanti user bisa milih view tree / view calendar', 0, 1, 8, '2013-03-29', '0000-00-00 00:00:00'),
(14, 5, 8, 0, 'Implementasi desain baru', '', 0, 1, 12, '2013-03-22', '0000-00-00 00:00:00'),
(15, 5, 9, 0, 'Implementasi desain baru', '', 0, 1, 12, '2013-03-22', '0000-00-00 00:00:00'),
(16, 7, 11, 0, 'android version', '', 0, 1, 6, '2013-03-29', '0000-00-00 00:00:00'),
(17, 7, 11, 0, 'blackberry version', '', 0, 1, 6, '2013-03-29', '0000-00-00 00:00:00'),
(18, 7, 10, 0, 'menyesuaikan format sensor', '', 0, 1, 4, '2013-03-29', '0000-00-00 00:00:00'),
(19, 7, 12, 0, 'instalasi server', '', 0, 1, 6, '2013-04-18', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `severity` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `assigned_user` int(11) NOT NULL,
  `complete_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `created_on`, `user_id`, `project_id`, `client_id`, `title`, `description`, `status`, `severity`, `type`, `assigned_user`, `complete_date`) VALUES
(1, '2013-03-18 07:22:26', 4, 1, 1, 'test ticket', 'test ticket doang', 'open', 'minor', 'enhancement', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tickets32`
--

CREATE TABLE IF NOT EXISTS `tickets32` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stub` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stub` (`stub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `timelines`
--

CREATE TABLE IF NOT EXISTS `timelines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

--
-- Dumping data for table `timelines`
--

INSERT INTO `timelines` (`id`, `project_id`, `title`, `client_id`) VALUES
(1, 1, 'Tracker Server', 1),
(2, 1, 'Web Client', 1),
(3, 1, 'Mobile Client', 1),
(4, 3, 'Initial Release', 1),
(5, 4, 'Initial Release', 1),
(6, 4, 'Multi Bahasa', 1),
(7, 4, 'Bundling Hardware', 1),
(8, 5, 'Community', 1),
(9, 5, 'Web', 1),
(10, 7, 'monitoring web', 1),
(11, 7, 'aplikasi mobile', 1),
(12, 7, 'migrasi server', 1),
(13, 7, 'live version', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` int(11) NOT NULL DEFAULT '0',
  `photo` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `confirm_hash` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `confirm_hash` (`confirm_hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `name`, `client_id`, `photo`, `confirm_hash`, `is_confirmed`) VALUES
(1, 'admin', 'c34883ecbb2db47a3a250ae80bd65ab02d948c0f', 'info@simetri.web.id', 'Admin', 0, NULL, NULL, 1),
(4, 'ferdhie', '4fd79634c11e96680df86695896366f1fad50d89', 'ferdhie@simetri.web.id', 'Herdian Ferdianto', 1, NULL, NULL, 0),
(5, 'jokonet', '27867280af5d042b95212846cf9f8e94f5333235', 'jokonet@simetri.web.id', 'Joko Siswanto', 1, NULL, NULL, 0),
(6, 'aryo', '1a90bdb3e0d05d5a17137305f8b9f340549addb7', 'aryo@simetri.web.id', 'Aryo Sanjaya', 1, NULL, NULL, 0),
(7, 'asri', '3ae0b43b2758d6c05a016388bd393905f6245850', 'asri@simetri.web.id', 'Asri Kusuma', 1, NULL, NULL, 0),
(8, 'herry', '3ca179436575ce6bb1f4f609975e0e842c768978', 'herry@simetri.in', 'Herry Satrio', 1, NULL, NULL, 0),
(9, 'tejo', '4ca48017dc881d2ca268e51fa5492af9e3029c88', 'tejo@simetri.web.id', 'Bambang Sutejo', 1, NULL, NULL, 0),
(10, 'dwi', '9ad46c5f446efe41ce7580b8c6b63b401caa8362', 'dwi@simetri.web.id', 'Dwi Mulyono', 1, NULL, NULL, 0),
(11, 'sasmito', 'd84f542f4b4dd9491ccd98c74754dfa146830aab', 'sasmito@simetri.web.id', 'Sasmito', 1, NULL, NULL, 0),
(12, 'hasan', 'c8ce94ff69e8476c11bc216d14c84f41ed6d2748', 'hasanrosidi@simetri.in', 'Hasan Rosidi', 1, NULL, NULL, 0),
(13, 'mega', '7e58209c23e15b83aea65c61f65e893afa9936ef', 'mega.ayu@simetri.in', 'Mega Ayu', 1, NULL, NULL, 0),
(14, 'test', '35bd3634c813e508fee546cea0b22ea6f847b89e', 'herdian@smsabadi.com', 'test user', 1, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`user_id`, `group_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 3),
(5, 4),
(6, 4),
(7, 6),
(8, 5),
(9, 4),
(10, 5),
(11, 5),
(12, 5),
(13, 4),
(14, 6);

-- --------------------------------------------------------

--
-- Table structure for table `user_perms`
--

CREATE TABLE IF NOT EXISTS `user_perms` (
  `user_id` int(11) NOT NULL,
  `perm_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`perm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
