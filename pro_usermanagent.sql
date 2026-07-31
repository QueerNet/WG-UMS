-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2020 at 03:01 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12




-- NOTES:
-- RoleID 1=admin 0=user
-- Rolename sysadmin or user
-- Define presets:

SET @sysadmin_name = 'Morrigan';
SET @sysadmin = 'mj.qls@tuta.io';
SET @db_passwd = 'zRnFvtt7@1p2bJv#&R^F';



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `APP_AUTH`
--


CREATE TABLE `APP_AUTH` (
  `id_autho` int(11) NOT NULL,
  `allow_email` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `APP_AUTH`
--

INSERT INTO `APP_AUTH` (`id_autho`, `allow_email`, `status`) VALUES
(143, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `APP_SETTINGS`
--

CREATE TABLE `APP_SETTINGS` (
  `app_id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `front_name` varchar(255) NOT NULL,
  `favicon` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `APP_SETTINGS`
--

INSERT INTO `APP_SETTINGS` (`app_id`, `app_name`, `title`, `front_name`, `favicon`, `logo`) VALUES
(1, 'QLS', 'QLS Dashboard', 'Login/User Management', 'app/uploads/logo/favicon.png', 'app/uploads/logo/QLS.png');

-- --------------------------------------------------------

--
-- Table structure for table `PERMISSIONS`
--

CREATE TABLE `PERMISSIONS` (
  `perid` int(11) NOT NULL,
  `per_access` varchar(255) NOT NULL,
  `per_create` varchar(255) NOT NULL,
  `per_show` varchar(255) NOT NULL,
  `per_edit` varchar(255) NOT NULL,
  `per_delete` varchar(255) NOT NULL,
  `ban_activ_user` varchar(255) NOT NULL,
  `per_onlyUser` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `PERMISSIONS`
--

INSERT INTO `PERMISSIONS` (`perid`, `per_access`, `per_create`, `per_show`, `per_edit`, `per_delete`, `ban_activ_user`, `per_onlyUser`) VALUES
(1, 'Access', 'Create', 'Show', 'Edit', 'Delete', 'Ban/Active user', 'User only');

-- --------------------------------------------------------

--
-- Table structure for table `ROLES`
--

CREATE TABLE `ROLES` (
  `roleid` int(11) NOT NULL,
  `rolename` varchar(255) NOT NULL,
  `roledname` varchar(255) NOT NULL,
  `permission_items` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ROLES`
--

INSERT INTO `ROLES` (`roleid`, `rolename`, `roledname`, `permission_items`, `status`) VALUES
(212, 'sysadmin', @sysadmin_name, 'Access,Create,Show,Edit,Delete,Ban/Active user', 0);

INSERT INTO `ROLES` (`roleid`, `rolename`, `roledname`, `permission_items`, `status`) VALUES
(101, 'user', 'user', 'Access', 0);

-- --------------------------------------------------------

--
-- Table structure for table `USERS`
--

CREATE TABLE `USERS` (
  `userid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rolename` varchar(255) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL,
  `lastactivity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add sysadmin record
INSERT INTO `USERS` (`userid`, `name`, `email`, `password`, `rolename`, `status`, `create_date`, `lastactivity`) VALUES
(1, 'sysadmin', @sysadmin, 'password', 'sysadmin', 0, '2026-07-11 23:50:55', 0);

--
-- Dumping data for table `USERS`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `APP_AUTH`
--
ALTER TABLE `APP_AUTH`
  ADD PRIMARY KEY (`id_autho`);

--
-- Indexes for table `APP_SETTINGS`
--
ALTER TABLE `APP_SETTINGS`
  ADD PRIMARY KEY (`app_id`);

--
-- Indexes for table `PERMISSIONS`
--
ALTER TABLE `PERMISSIONS`
  ADD PRIMARY KEY (`perid`);

--
-- Indexes for table `ROLES`
--
ALTER TABLE `ROLES`
  ADD PRIMARY KEY (`roleid`);

--
-- Indexes for table `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `APP_AUTH`
--
ALTER TABLE `APP_AUTH`
  MODIFY `id_autho` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `APP_SETTINGS`
--
ALTER TABLE `APP_SETTINGS`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `PERMISSIONS`
--
ALTER TABLE `PERMISSIONS`
  MODIFY `perid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ROLES`
--
ALTER TABLE `ROLES`
  MODIFY `roleid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT for table `USERS`
--
ALTER TABLE `USERS`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



CREATE TABLE WG (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    userid INT UNSIGNED NOT NULL,
    devid VARCHAR(64) NOT NULL,
    AllowedIPs VARCHAR(45) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_user_device (userid, devid),
    UNIQUE KEY uniq_allowed_ip (AllowedIPs)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO WG (userid, devid, AllowedIPs)
VALUES (0, 'wg-server', '10.200.200.1/32');
