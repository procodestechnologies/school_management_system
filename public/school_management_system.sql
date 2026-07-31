-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 31, 2026 at 12:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('school-management-cache-38049f26c5931dae174cdadfcb83c054', 'i:2;', 1785501551),
('school-management-cache-38049f26c5931dae174cdadfcb83c054:timer', 'i:1785501551;', 1785501551),
('school-management-cache-7cdea7b5113fec2b51fbaa3a510d391d', 'i:1;', 1785496803),
('school-management-cache-7cdea7b5113fec2b51fbaa3a510d391d:timer', 'i:1785496803;', 1785496803),
('school-management-cache-e5c6aeb227fc693020725577a0cde4e4', 'i:1;', 1785498448),
('school-management-cache-e5c6aeb227fc693020725577a0cde4e4:timer', 'i:1785498448;', 1785498448),
('school-management-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:84:{i:0;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"view timetable\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:6;}}i:1;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:16:\"view examination\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:6;}}i:2;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:11:\"view parent\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:3;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"view student\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;i:4;i:6;}}i:4;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:18:\"view feemanagement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:5;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"view curriculum\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:5;i:2;i:6;}}i:6;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:16:\"view institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:7;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:12:\"view teacher\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:8;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:15:\"view attendance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:6;}}i:9;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:11:\"edit parent\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:10;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:13:\"delete parent\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:11;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:13:\"update parent\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:12;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:13:\"create parent\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:13;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:14:\"create student\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:14;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:12:\"edit student\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:15;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:14:\"update student\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:16;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:14:\"delete student\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:17;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:14:\"create teacher\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:18;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:12:\"edit teacher\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:19;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:14:\"update teacher\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:20;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:14:\"delete teacher\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:21;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:18:\"create institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:22;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:16:\"edit institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:23;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:18:\"update institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:24;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:18:\"delete institution\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:25;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:17:\"create curriculum\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:26;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:15:\"edit curriculum\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:27;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:17:\"update curriculum\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:28;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:17:\"delete curriculum\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:29;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:17:\"create attendance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:30;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:15:\"edit attendance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:31;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:17:\"update attendance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:32;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:17:\"delete attendance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:33;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:18:\"create examination\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:34;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:16:\"edit examination\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:35;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:18:\"update examination\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:36;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:18:\"delete examination\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:37;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:20:\"create feemanagement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:38;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:18:\"edit feemanagement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:39;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:20:\"update feemanagement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:40;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:20:\"delete feemanagement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:41;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:16:\"create timetable\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:42;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:14:\"edit timetable\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:43;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:16:\"update timetable\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:44;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:16:\"delete timetable\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:45;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:9:\"view user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:46;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:11:\"create user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:47;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:9:\"edit user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:48;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:11:\"update user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:49;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:11:\"delete user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:50;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:9:\"view role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:51;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:11:\"create role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:52;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:9:\"edit role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:53;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:11:\"update role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:54;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:11:\"delete role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:55;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:15:\"view permission\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:56;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:17:\"create permission\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:57;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:15:\"edit permission\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:58;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:17:\"update permission\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:59;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:17:\"delete permission\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:60;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:12:\"view setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:61;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:14:\"create setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:62;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:12:\"edit setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:63;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:14:\"update setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:64;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:14:\"delete setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:65;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:14:\"view dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;}}i:66;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:11:\"view report\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;}}i:67;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:13:\"create report\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:68;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:13:\"export report\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:69;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:12:\"view account\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:70;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:14:\"create account\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:71;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:12:\"edit account\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:72;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:14:\"update account\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:73;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:14:\"delete account\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:74;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:12:\"view finance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:75;a:4:{s:1:\"a\";i:79;s:1:\"b\";s:14:\"create finance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:76;a:4:{s:1:\"a\";i:80;s:1:\"b\";s:12:\"edit finance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:77;a:4:{s:1:\"a\";i:81;s:1:\"b\";s:14:\"update finance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:78;a:4:{s:1:\"a\";i:82;s:1:\"b\";s:14:\"delete finance\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:79;a:4:{s:1:\"a\";i:83;s:1:\"b\";s:12:\"view classes\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:6;}}i:80;a:4:{s:1:\"a\";i:84;s:1:\"b\";s:14:\"create classes\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:81;a:4:{s:1:\"a\";i:85;s:1:\"b\";s:12:\"edit classes\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:82;a:4:{s:1:\"a\";i:86;s:1:\"b\";s:14:\"update classes\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:83;a:4:{s:1:\"a\";i:87;s:1:\"b\";s:14:\"delete classes\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}}s:5:\"roles\";a:6:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:6:\"Parent\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:7:\"Student\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:8:\"Director\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:7:\"Teacher\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:10:\"Accountant\";s:1:\"c\";s:3:\"web\";}}}', 1785586324),
('school-management-cache-xifydyv@mailinator.com|127.0.0.1', 'i:1;', 1785496803),
('school-management-cache-xifydyv@mailinator.com|127.0.0.1:timer', 'i:1785496803;', 1785496803);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` varchar(255) DEFAULT NULL,
  `class_teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `capacity` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `institution_id`, `name`, `level`, `class_teacher_id`, `capacity`, `created_at`, `updated_at`) VALUES
(2, 1, 'Grade 7 East', 'Grade 7', NULL, 35, '2026-07-31 12:12:56', '2026-07-31 12:12:56'),
(3, 1, 'Grade 8 East', 'Grade 8', NULL, 35, '2026-07-31 12:12:56', '2026-07-31 12:12:56'),
(4, 1, 'Grade 9 East', 'Grade 9', NULL, 30, '2026-07-31 12:12:56', '2026-07-31 12:12:56'),
(5, 1, 'Grade 9 West', 'Grade 9', NULL, 30, '2026-07-31 12:12:56', '2026-07-31 12:12:56');

-- --------------------------------------------------------

--
-- Table structure for table `curricula`
--

CREATE TABLE `curricula` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('active','dismissed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `curricula`
--

INSERT INTO `curricula` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(6, 'C.B.C', 'active', '2026-07-24 16:07:33', '2026-07-28 08:20:05'),
(7, '8-4-4', 'active', '2026-07-24 16:51:58', '2026-07-24 16:51:58'),
(8, 'IGCSE', 'active', '2026-07-28 18:54:48', '2026-07-28 18:54:48');

-- --------------------------------------------------------

--
-- Table structure for table `curriculums`
--

CREATE TABLE `curriculums` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('active','dismissed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `zkteco_device_id` bigint(20) UNSIGNED DEFAULT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `serial_number`, `zkteco_device_id`, `institution_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'GED7261800014', 1, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `examinations`
--

CREATE TABLE `examinations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `term` varchar(255) DEFAULT NULL,
  `exam_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `total_marks` int(10) UNSIGNED NOT NULL DEFAULT 100,
  `passing_marks` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `fee_type` varchar(255) NOT NULL DEFAULT 'tuition',
  `amount` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `institution_id`, `student_id`, `parent_id`, `title`, `fee_type`, `amount`, `amount_paid`, `due_date`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 139, 138, 'Term 1 Tuition (Updated)', 'tuition', 15000.00, 15000.00, '2026-09-01', 'Fully paid', '2026-07-31 09:51:30', '2026-07-31 09:52:13', '2026-07-31 09:52:13'),
(2, 1, 139, 138, 'Term 1', 'boarding', 30000.00, 15000.00, '2026-07-30', 'the rest  paid', '2026-07-31 12:44:25', '2026-07-31 12:45:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `institutions`
--

CREATE TABLE `institutions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'School',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `alternate_phone` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'Kenya',
  `county` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `postal_address` varchar(255) DEFAULT NULL,
  `physical_address` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `principal_name` varchar(255) DEFAULT NULL,
  `principal_phone` varchar(255) DEFAULT NULL,
  `curriculum` bigint(20) UNSIGNED NOT NULL,
  `education_level` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) NOT NULL DEFAULT 'Africa/Nairobi',
  `subscription_plan` varchar(255) DEFAULT NULL,
  `subscription_expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`id`, `user_id`, `name`, `code`, `type`, `email`, `phone`, `alternate_phone`, `website`, `country`, `county`, `city`, `postal_address`, `physical_address`, `logo`, `favicon`, `principal_name`, `principal_phone`, `curriculum`, `education_level`, `timezone`, `subscription_plan`, `subscription_expires_at`, `is_active`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 'Imaara Secondary School', '424518654', 'School', 'imaara@gmail.com', '+254727740998', '+254727740985', 'https://www.cywurynepov.tv', 'Kenya', 'Nairobi', 'Nairobi', 'P.O.Box 10200-10302', 'Embakasi', 'fd', 're', 'Sara Bray', '0711777992', 7, 'High School', 'Africa/Nairobi', 'Free', '2026-08-07 18:00:00', 1, 'active', 'Located in Embakasi south constituency', '2026-07-29 00:56:25', '2026-07-29 06:57:43');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"6167ffe0-05d7-484c-aed0-285ae1174bd6\",\"displayName\":\"App\\\\Jobs\\\\RemindMe\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\RemindMe\",\"command\":\"O:17:\\\"App\\\\Jobs\\\\RemindMe\\\":1:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\",\"batchId\":null},\"createdAt\":1785088751,\"delay\":null}', 0, NULL, 1785088751, 1785088751),
(2, 'default', '{\"uuid\":\"5503215a-25fe-4264-9024-b4b5cd398547\",\"displayName\":\"App\\\\Jobs\\\\RemindMe\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\RemindMe\",\"command\":\"O:17:\\\"App\\\\Jobs\\\\RemindMe\\\":1:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\",\"batchId\":null},\"createdAt\":1785088821,\"delay\":null}', 0, NULL, 1785088821, 1785088821),
(3, 'default', '{\"uuid\":\"73568df8-5648-4311-a1bd-96963018102b\",\"displayName\":\"App\\\\Jobs\\\\RemindMe\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\RemindMe\",\"command\":\"O:17:\\\"App\\\\Jobs\\\\RemindMe\\\":1:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\",\"batchId\":null},\"createdAt\":1785088907,\"delay\":null}', 0, NULL, 1785088907, 1785088907),
(4, 'default', '{\"uuid\":\"2ed47d8c-94c3-4f7f-93f4-62f7afed828c\",\"displayName\":\"App\\\\Jobs\\\\RemindMe\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\RemindMe\",\"command\":\"O:17:\\\"App\\\\Jobs\\\\RemindMe\\\":1:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\",\"batchId\":null},\"createdAt\":1785088973,\"delay\":null}', 0, NULL, 1785088973, 1785088973),
(5, 'default', '{\"uuid\":\"623e87ee-00ad-4836-b9d6-480284478738\",\"displayName\":\"App\\\\Jobs\\\\RemindMe\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\RemindMe\",\"command\":\"O:17:\\\"App\\\\Jobs\\\\RemindMe\\\":1:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\",\"batchId\":null},\"createdAt\":1785088980,\"delay\":null}', 0, NULL, 1785088980, 1785088980),
(6, 'default', '{\"uuid\":\"b5096690-4399-4386-b696-a70dba869447\",\"displayName\":\"App\\\\Jobs\\\\RemindMe\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"App\\\\Jobs\\\\RemindMe\",\"command\":\"O:17:\\\"App\\\\Jobs\\\\RemindMe\\\":1:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}\",\"batchId\":null},\"createdAt\":1785089053,\"delay\":null}', 0, NULL, 1785089053, 1785089053);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000000_create_passkeys_table', 1),
(5, '2025_08_14_170933_add_two_factor_columns_to_users_table', 1),
(6, '2026_07_24_125557_create_permission_tables', 2),
(7, '2026_07_24_184305_create_curriculums_table', 3),
(8, '2026_07_24_184305_create_curricula_table', 4),
(9, '2026_07_26_200728_create_landlord_tenants_table', 5),
(10, '2026_07_24_182232_create_examinations_table', 6),
(11, '2026_07_28_113218_create_zkteco_devices_table', 7),
(12, '2026_07_28_113219_create_zkteco_users_table', 7),
(13, '2026_07_28_113220_create_zkteco_attendance_logs_table', 7),
(14, '2026_07_28_113221_create_zkteco_device_commands_table', 7),
(15, '2026_07_28_113222_create_zkteco_device_events_table', 7),
(16, '2026_07_28_113223_add_occurred_at_to_zkteco_attendance_logs_table', 7),
(18, '2026_07_28_115115_create_institutions_table', 8),
(24, '2026_07_29_142708_student_details', 10),
(25, '2026_07_29_145240_parent_details', 11),
(26, '2026_07_31_000140_add_parent_id_to_parent_details_form', 11),
(28, '2026_07_31_025120_create_devices_table', 12),
(29, '2026_07_31_030000_add_zkteco_sync_columns_to_users_table', 13),
(30, '2026_07_31_030100_add_zkteco_device_id_to_devices_table', 13),
(31, '2026_07_31_030200_backfill_zkteco_users_app_user_id', 13),
(32, '2026_07_31_040000_create_fees_table', 14),
(33, '2026_07_31_050000_create_teacher_details_table', 15),
(34, '2026_07_31_060000_create_timetable_entries_table', 16),
(35, '2026_07_31_070000_add_details_to_examinations_table', 17),
(36, '2026_07_31_080000_create_classes_table', 18),
(37, '2026_07_31_090000_add_class_id_to_timetable_entries_table', 19);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(4, 'App\\Models\\User', 1),
(5, 'App\\Models\\User', 1),
(6, 'App\\Models\\User', 1),
(7, 'App\\Models\\User', 1),
(8, 'App\\Models\\User', 1),
(9, 'App\\Models\\User', 1),
(10, 'App\\Models\\User', 1),
(11, 'App\\Models\\User', 1),
(12, 'App\\Models\\User', 1),
(13, 'App\\Models\\User', 2),
(14, 'App\\Models\\User', 2),
(15, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 89),
(2, 'App\\Models\\User', 96),
(2, 'App\\Models\\User', 104),
(2, 'App\\Models\\User', 107),
(2, 'App\\Models\\User', 110),
(2, 'App\\Models\\User', 112),
(2, 'App\\Models\\User', 114),
(2, 'App\\Models\\User', 116),
(2, 'App\\Models\\User', 118),
(2, 'App\\Models\\User', 123),
(2, 'App\\Models\\User', 125),
(2, 'App\\Models\\User', 138),
(3, 'App\\Models\\User', 141),
(4, 'App\\Models\\User', 139),
(5, 'App\\Models\\User', 2),
(5, 'App\\Models\\User', 140),
(6, 'App\\Models\\User', 146),
(6, 'App\\Models\\User', 147);

-- --------------------------------------------------------

--
-- Table structure for table `parent_details`
--

CREATE TABLE `parent_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `parent_phone` varchar(255) DEFAULT NULL,
  `parent_occupation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parent_details`
--

INSERT INTO `parent_details` (`id`, `parent_id`, `parent_phone`, `parent_occupation`, `created_at`, `updated_at`) VALUES
(22, 138, '+1 (734) 878-2748', 'Voluptas unde facere', '2026-07-30 22:40:54', '2026-07-30 22:40:54');

-- --------------------------------------------------------

--
-- Table structure for table `passkeys`
--

CREATE TABLE `passkeys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `credential_id` varchar(255) NOT NULL,
  `credential` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`credential`)),
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(4, 'view timetable', 'web', '2026-07-26 02:22:28', '2026-07-26 02:22:28'),
(5, 'view examination', 'web', '2026-07-26 02:22:29', '2026-07-26 02:22:29'),
(6, 'view parent', 'web', '2026-07-26 02:22:30', '2026-07-26 02:22:30'),
(7, 'view student', 'web', '2026-07-26 02:22:30', '2026-07-26 02:22:30'),
(8, 'view feemanagement', 'web', '2026-07-26 02:22:30', '2026-07-26 02:22:30'),
(9, 'view curriculum', 'web', '2026-07-26 02:23:27', '2026-07-26 02:23:27'),
(10, 'view institution', 'web', '2026-07-26 02:27:02', '2026-07-26 02:27:02'),
(11, 'view teacher', 'web', '2026-07-28 09:47:47', '2026-07-28 09:47:47'),
(12, 'view attendance', 'web', '2026-07-28 09:47:47', '2026-07-28 09:47:47'),
(13, 'edit parent', 'web', '2026-07-31 09:28:43', '2026-07-31 09:28:43'),
(14, 'delete parent', 'web', '2026-07-31 09:28:43', '2026-07-31 09:28:43'),
(15, 'update parent', 'web', '2026-07-31 09:28:44', '2026-07-31 09:28:44'),
(16, 'create parent', 'web', '2026-07-31 09:38:24', '2026-07-31 09:38:24'),
(17, 'create student', 'web', '2026-07-31 09:38:24', '2026-07-31 09:38:24'),
(18, 'edit student', 'web', '2026-07-31 09:38:24', '2026-07-31 09:38:24'),
(19, 'update student', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(20, 'delete student', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(21, 'create teacher', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(22, 'edit teacher', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(23, 'update teacher', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(24, 'delete teacher', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(25, 'create institution', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(26, 'edit institution', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(27, 'update institution', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(28, 'delete institution', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(29, 'create curriculum', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(30, 'edit curriculum', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(31, 'update curriculum', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(32, 'delete curriculum', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(33, 'create attendance', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(34, 'edit attendance', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(35, 'update attendance', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(36, 'delete attendance', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(37, 'create examination', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(38, 'edit examination', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(39, 'update examination', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(40, 'delete examination', 'web', '2026-07-31 09:38:25', '2026-07-31 09:38:25'),
(41, 'create feemanagement', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(42, 'edit feemanagement', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(43, 'update feemanagement', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(44, 'delete feemanagement', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(45, 'create timetable', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(46, 'edit timetable', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(47, 'update timetable', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(48, 'delete timetable', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(49, 'view user', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(50, 'create user', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(51, 'edit user', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(52, 'update user', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(53, 'delete user', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(54, 'view role', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(55, 'create role', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(56, 'edit role', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(57, 'update role', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(58, 'delete role', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(59, 'view permission', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(60, 'create permission', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(61, 'edit permission', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(62, 'update permission', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(63, 'delete permission', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(64, 'view setting', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(65, 'create setting', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(66, 'edit setting', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(67, 'update setting', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(68, 'delete setting', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(69, 'view dashboard', 'web', '2026-07-31 09:38:26', '2026-07-31 09:38:26'),
(70, 'view report', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(71, 'create report', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(72, 'export report', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(73, 'view account', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(74, 'create account', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(75, 'edit account', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(76, 'update account', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(77, 'delete account', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(78, 'view finance', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(79, 'create finance', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(80, 'edit finance', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(81, 'update finance', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(82, 'delete finance', 'web', '2026-07-31 09:38:27', '2026-07-31 09:38:27'),
(83, 'view classes', 'web', '2026-07-31 12:11:55', '2026-07-31 12:11:55'),
(84, 'create classes', 'web', '2026-07-31 12:11:55', '2026-07-31 12:11:55'),
(85, 'edit classes', 'web', '2026-07-31 12:11:55', '2026-07-31 12:11:55'),
(86, 'update classes', 'web', '2026-07-31 12:11:55', '2026-07-31 12:11:55'),
(87, 'delete classes', 'web', '2026-07-31 12:11:55', '2026-07-31 12:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2026-07-24 10:04:47', '2026-07-24 10:04:47'),
(2, 'Parent', 'web', '2026-07-24 10:04:47', '2026-07-24 10:04:47'),
(3, 'Accountant', 'web', '2026-07-24 10:04:47', '2026-07-24 10:04:47'),
(4, 'Student', 'web', '2026-07-24 10:04:47', '2026-07-24 10:04:47'),
(5, 'Director', 'web', '2026-07-24 12:15:16', '2026-07-24 12:15:16'),
(6, 'Teacher', 'web', '2026-07-31 11:53:05', '2026-07-31 11:53:05');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(4, 1),
(4, 2),
(4, 4),
(4, 5),
(4, 6),
(5, 1),
(5, 2),
(5, 4),
(5, 5),
(5, 6),
(6, 1),
(6, 3),
(6, 5),
(7, 1),
(7, 2),
(7, 3),
(7, 5),
(7, 6),
(8, 1),
(8, 2),
(8, 3),
(8, 5),
(9, 1),
(9, 5),
(9, 6),
(10, 1),
(10, 5),
(11, 1),
(11, 5),
(12, 1),
(12, 2),
(12, 4),
(12, 5),
(12, 6),
(13, 1),
(13, 5),
(14, 1),
(14, 5),
(15, 1),
(15, 5),
(16, 1),
(16, 5),
(17, 1),
(17, 5),
(18, 1),
(18, 5),
(19, 1),
(19, 5),
(20, 1),
(20, 5),
(21, 1),
(21, 5),
(22, 1),
(22, 5),
(23, 1),
(23, 5),
(24, 1),
(24, 5),
(25, 1),
(26, 1),
(26, 5),
(27, 1),
(27, 5),
(28, 1),
(29, 1),
(29, 5),
(30, 1),
(30, 5),
(31, 1),
(31, 5),
(32, 1),
(32, 5),
(33, 1),
(33, 5),
(34, 1),
(34, 5),
(35, 1),
(35, 5),
(36, 1),
(36, 5),
(37, 1),
(37, 5),
(38, 1),
(38, 5),
(39, 1),
(39, 5),
(40, 1),
(40, 5),
(41, 1),
(41, 3),
(41, 5),
(42, 1),
(42, 3),
(42, 5),
(43, 1),
(43, 3),
(43, 5),
(44, 1),
(44, 3),
(44, 5),
(45, 1),
(45, 5),
(46, 1),
(46, 5),
(47, 1),
(47, 5),
(48, 1),
(48, 5),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(69, 2),
(69, 3),
(69, 4),
(69, 5),
(69, 6),
(70, 1),
(70, 2),
(70, 3),
(70, 4),
(70, 5),
(70, 6),
(71, 1),
(71, 3),
(71, 5),
(72, 1),
(72, 3),
(72, 5),
(73, 1),
(73, 3),
(73, 5),
(74, 1),
(74, 3),
(74, 5),
(75, 1),
(75, 3),
(75, 5),
(76, 1),
(76, 3),
(76, 5),
(77, 1),
(78, 1),
(78, 3),
(78, 5),
(79, 1),
(79, 3),
(79, 5),
(80, 1),
(80, 3),
(80, 5),
(81, 1),
(81, 3),
(81, 5),
(82, 1),
(83, 1),
(83, 2),
(83, 4),
(83, 5),
(83, 6),
(84, 1),
(84, 5),
(85, 1),
(85, 5),
(86, 1),
(86, 5),
(87, 1),
(87, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4K0gK1rvCiitqeFVVdtdfctlU8BQuyr9uoKOxOu5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI0czZwNnNRNzZhSEd3ZVBBVmlnZkVRZmtDa3Ywc2dBcno2aE5ENFRGIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785496736),
('4pkXFbdResoTTDfk0RCQPUzpEj9p6AAqjdzhmWJa', 138, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJ4SWZDMDNkUHpYR290NXRJZFdHT1BhdTAzWmJjVTlFRGVVWE9Ib1NzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785499440),
('5goDOvfmvp90TV1xiNLcEyDO9ev0r42VDy377FoM', 145, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJjMjVhRW5Mb1o0WXdScTZ3RkxhMU1hd1ZjWFdUNUhJV1JlaVRjcEZEIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785498833),
('7OLOyvvh6VlDhYBvjfR3hiFu6QFWUndPVcd9Gdar', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ5Yk9TUFM3aTJlVEJadHdtdWJiM2JkZWVwYjQ3QU82V3V3SFV2WHE0IiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL3YxXC9pbnN0aXR1dGlvbnNcLzFcL2F0dGVuZGFuY2U/cGVyX3BhZ2U9MTAwIiwicm91dGUiOiJhcGkuYXR0ZW5kYW5jZS5pbnN0aXR1dGlvbiJ9fQ==', 1785501969),
('9ldVDY0QE2n6WsLuaQao1zsazYbOL60jPudJS876', 2, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiI3WDFZcEdVRGxYeGhnajhGazZXSnRJVFRPcGZIdHl1RjEzbFJNYjY0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC90aW1ldGFibGVzIiwicm91dGUiOiJ0aW1ldGFibGUuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785501262),
('A171VD5wNDEOnxHhPd71BrHZoia9TxLVjYQQLoPR', 2, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJUZ3M2M2dCTVZZeDlzdDh2NTBDdFJuSU9hZndFMmg1UndtQk1OS2QzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC90aW1ldGFibGVzIiwicm91dGUiOiJ0aW1ldGFibGUuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785500195),
('aBP9b1uFkf3vBdcILeE3lmixupAEPkaKcc6cHqQB', 1, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJuVkpvcnVSTm52WmFnRFc5RW80YnZPQ3RkcjRzamVnVGhpeU93dEs2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC90ZWFjaGVycyIsInJvdXRlIjoidGVhY2hlci5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1785498804),
('AhrtorbmJ2cCK4h6yLzGhbH24UrZBYJIIdMVwja0', 1, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJwS2xmY2dYbEZWZThEdElmM0wxcUVEWFhlZDZtOE05N3FLb3o5Um52IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC9jdXJyaWN1bGEiLCJyb3V0ZSI6ImN1cnJpY3VsdW0uaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785498491),
('bkdRkV8wrZ3ky6bgex6fvI88N7lcBIN99S5eSeW5', 140, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJHNHNTRGU1dnZPeWxHejVJOUVVUzV1MjBjZHhIc2c0SnV3WlFpMlpQIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785494012),
('cag9citHJbsn9kBc8opTibawx3JJHCowtDd1d1WV', 139, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJwak94d09VczBQMkRGbVpwU3NPbGxEQ29QWW10QjdraEZFa0Y4VFRHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC9pbnN0aXR1dGlvbnMiLCJyb3V0ZSI6Imluc3RpdHV0aW9uLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1785499398),
('Ci7YPcJNDoJwgg6oiMsxJQDr2C9OofLvvX7ELKMv', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJYM2JXZG04cVZrVjlndnJBMmU3NDk5VWZaTnZJa2s5Y1V6QjdhcFhxIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvbG9jYWxob3N0XC9kYXNoYm9hcmQifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785499371),
('dHNqoy5TnBhfMIBiTV720ZtJQ6o8vIci6mueCXyn', 1, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJ5bW1pOHptMEtoMWQ5RGE3aWtWNGtubXJVU3NTZU10VTZVemNQZnRTIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785494001),
('DoFqeMbexmq1lXkkheuSqcCb0egM2tAuHW8P8fUd', 2, '127.0.0.1', 'Symfony', 'eyJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwiX3Rva2VuIjoiY2ZLdWhuSTVDRXo2cGYwaHhmUElaWUVkanNtNFFlNjBqdmtXZGdCYSIsInN1Y2Nlc3MiOiJQYXJlbnQgY3JlYXRlZCBzdWNjZXNzZnVsbHkhIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6WyJzdWNjZXNzIl19LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0XC9kYXNoYm9hcmRcL3BhcmVudHNcLzE0M1wvZWRpdCIsInJvdXRlIjoicGFyZW50LmVkaXQifX0=', 1785498309),
('dTQ4hIEALNL8U11WyEGMqJcvTcJJoXLk0yii7evP', 2, '192.168.88.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJRVnRMTjFHakNyQlhOdFJrTFNRZzhjeTc5NTZ3RWxxQ1hmd1VVb1Y5IiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsIl9wcmV2aW91cyI6eyJ1cmwiOiJodHRwOlwvXC8xOTIuMTY4Ljg4LjI1MDo4MDAwXC9kYXNoYm9hcmRcL3N0dWRlbnRzXC9jcmVhdGUiLCJyb3V0ZSI6InN0dWRlbnQuY3JlYXRlIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1785496434),
('fG49jowWRdN3a1yA4obuRmQQV1crezuVdZlrJvWX', 1, '192.168.88.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ5dnkwakZkNzV2MjZSMjNxalByUEI5RDZhc1diNGFPbVRqVHJkZGprIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTkyLjE2OC44OC4yNTA6ODAwMFwvZGFzaGJvYXJkXC9wYXJlbnRzXC8xMzgiLCJyb3V0ZSI6InBhcmVudC5zaG93In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1785498332),
('fiDUMhci2UhcULCjwxTG7WhezHjYogWLGzjZWVLG', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJjNGNqdzdkRGdJMHBYRnBEYzhmYVZXbGhXS29GVVdsaTdFbnEwZW1jIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6Mn0=', 1785501492),
('fNKIMSHJpPK4Vkf5HhAdR58Wv0XqDmRFc0Mb3MjF', 1, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiIzVVg4bWttRUJFc3ZGTVQybmdZbWFNR3FIVzFieEhQb3hKVTdNWUVBIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC9leGFtaW5hdGlvbnMiLCJyb3V0ZSI6ImV4YW1pbmF0aW9ucy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1785499252),
('g5dj7G2RgnO1xeM7fo9GyKwuMbjEmxe46MVtItoe', 2, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJ6MnkyNnJYS2ZyaFExYVJpSnB0dnRPakhON0lMeXBacEJ2dTJCV0RCIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC9jbGFzc2VzIiwicm91dGUiOiJjbGFzc2VzLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1785499926),
('HUvEf85j97VkaTfqiQ22YKq2kcikoHzVhZgeOHYQ', 1, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiI2RUUzVWhiRHhPU3o2bjBiSVVsM3FaRHE4V3ptOHVuRXVyekNKblE3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC9pbnN0aXR1dGlvbnMiLCJyb3V0ZSI6Imluc3RpdHV0aW9uLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1785499393),
('kEhKdzcioQgf5iRsQAYJC2Oq3kAlVxhKQcaBH6yh', 140, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJ0ZXN0Iiwic3VjY2VzcyI6Ikluc3RpdHV0aW9uIGNyZWF0ZWQgc3VjY2Vzc2Z1bGx5ISIsIl9mbGFzaCI6eyJuZXciOltdLCJvbGQiOlsic3VjY2VzcyJdfX0=', 1785494039),
('lory50GBMtKfP5jpEtULdC5hCr3hM8uTtohUgzWz', 1, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJLSkg0SkZqaFlEN2cyenkxbE1ZVlZkNndOSFVhVmRuNWlMSlh2WTc4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC90aW1ldGFibGVzIiwicm91dGUiOiJ0aW1ldGFibGUuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785499071),
('nZ9p7DOd30xIxjP62qVSRKnBfP9ky5eAfNpxdVFN', 138, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJZTkQwTmNDVWxVMVJtRHR1MmVGZlQ1VTdod0VObkZuUHRqMVpBelRtIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC9pbnN0aXR1dGlvbnMiLCJyb3V0ZSI6Imluc3RpdHV0aW9uLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1785499396),
('okSTgbxqTJPfinIeTpjTjFzDTRphqWN8pzT35xe6', 138, '192.168.88.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJGdFpxM3VzMmF5RVpmUUhyczJyc0VWRnp1ZHgwdGVKOXZQaG5Ud3ZHIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzE5Mi4xNjguODguMjUwOjgwMDBcL2Rhc2hib2FyZFwvZXhhbWluYXRpb25zIiwicm91dGUiOiJleGFtaW5hdGlvbnMuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MTM4fQ==', 1785497099),
('PutQEOkL1IzXXXxAd2JIri1fKrAUAVoU9g1hZIRu', 1, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJPallTY3ZZMlV0SXE1QVI1bjB3Y2FVaVR1NGxsOE10YzhmN3BhTVdiIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785499313),
('Q2LNCZx50P85rfyzZHuBWG5meZOr91b3qyOAcQ6j', 2, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJ4QURKSVdqWXk4OVJ4TnF5YVNydXpjRDBHRzJEamJtWXJGV1JubGlxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC9pbnN0aXR1dGlvbnMiLCJyb3V0ZSI6Imluc3RpdHV0aW9uLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1785499395),
('XWC3g5ANU6Y4ksir2tKlTPAA38QzYUEWU3C0Yynw', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJtclJlTFgxQlJmMzlGN2NnQXNzeEIyYTRUT1ZBUnZFQVhvT01YeFVXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmRcL3RpbWV0YWJsZXMiLCJyb3V0ZSI6InRpbWV0YWJsZS5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1785501520),
('yMzAMNgbGIoguvt1yH0gYxJ75k9Ox95nH1VTKtQi', 1, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJabFcyeFdiY0g1eFJKczNoMHpxZjNOTzByM2w0Yjl2ZDdmNzV6QmM1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkXC9wYXJlbnRzIiwicm91dGUiOiJwYXJlbnQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785498269),
('yubLKMmcgCcNnPRJuv0etBEcWwMWJcTNNctWKp6g', 139, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJGTmtKS1pIdVltSUpHYTc0UFJCSmdnOUZXYll6bzZ5T0s4NEVuMmtqIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvZGFzaGJvYXJkIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785499441);

-- --------------------------------------------------------

--
-- Table structure for table `student_details`
--

CREATE TABLE `student_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `admission_number` varchar(255) NOT NULL,
  `student_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_phone` varchar(255) DEFAULT NULL,
  `guardian_email` varchar(255) DEFAULT NULL,
  `guardian_relationship` varchar(255) DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `special_needs` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `enrollment_status` enum('active','transferred','graduated','dropped','suspended','expelled','withdrawn') NOT NULL DEFAULT 'active',
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_details`
--

INSERT INTO `student_details` (`id`, `user_id`, `student_id`, `phone`, `date_of_birth`, `gender`, `admission_number`, `student_number`, `address`, `city`, `state`, `country`, `parent_id`, `guardian_name`, `guardian_phone`, `guardian_email`, `guardian_relationship`, `medical_conditions`, `allergies`, `special_needs`, `is_active`, `enrollment_status`, `institution_id`, `class_id`, `profile_photo`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 139, 139, '+1 (864) 475-9439', '2010-08-11', 'other', '871', '867', 'Sit voluptas dolore', 'Aperiam pariatur Mo', 'Distinctio Pariatur', 'Sed minus sunt id ni', 138, 'Glenna Tyler', '+1 (949) 304-5009', 'xozyni@mailinator.com', 'Saepe veniam evenie', 'Eum qui proident fa', 'In laborum Recusand', 'Velit perspiciatis', 1, 'active', 1, NULL, NULL, 'Pariatur Ea nisi su', '2026-07-30 22:40:54', '2026-07-30 23:39:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_details`
--

CREATE TABLE `teacher_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `employee_number` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','on_leave','suspended','resigned','terminated') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_details`
--

INSERT INTO `teacher_details` (`id`, `teacher_id`, `institution_id`, `phone`, `employee_number`, `department`, `qualification`, `hire_date`, `address`, `is_active`, `status`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 147, 1, '0725463252', '25463252', 'Mathematics', 'Upper Class', '2026-07-01', 'Pipeline, Embakasi', 1, 'active', 'nice teacher', '2026-07-31 12:41:02', '2026-07-31 12:41:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `database` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `name`, `domain`, `database`, `created_at`, `updated_at`) VALUES
(1, 'Tenant One', 'tenant1.test', 'tenant_database_name', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `timetable_entries`
--

CREATE TABLE `timetable_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timetable_entries`
--

INSERT INTO `timetable_entries` (`id`, `institution_id`, `class_id`, `teacher_id`, `class_name`, `subject`, `day_of_week`, `start_time`, `end_time`, `room`, `notes`, `created_at`, `updated_at`) VALUES
(15, 1, 2, 147, 'Grade 7 East', 'MATHS', 'Monday', '08:20:00', '08:55:00', NULL, NULL, '2026-07-31 12:37:05', '2026-07-31 12:41:40'),
(16, 1, 2, 147, 'Grade 7 East', 'CRE', 'Monday', '08:55:00', '09:30:00', '12', 'ghfdsa', '2026-07-31 12:37:05', '2026-07-31 12:42:14'),
(17, 1, 2, NULL, 'Grade 7 East', 'ENG', 'Monday', '09:50:00', '10:25:00', NULL, NULL, '2026-07-31 12:37:05', '2026-07-31 12:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `zkteco_synced` tinyint(1) NOT NULL DEFAULT 0,
  `zkteco_synced_at` timestamp NULL DEFAULT NULL,
  `zkteco_sync_error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `zkteco_synced`, `zkteco_synced_at`, `zkteco_sync_error`, `created_at`, `updated_at`) VALUES
(1, 'Isaac Nyamari', 'jablessions76@gmail.com', NULL, '$2y$12$7Oe0KglVFstpDr6//WTX3eUwtEKFxHJiNA7qfDg39g/HZPSCnqvJu', NULL, NULL, NULL, 'cFHwlWcNxV4xtqHa3dBDquPfc5am7NtwKYm6vFKxZqJcMXeCJNakWjuYPmwC', 0, NULL, NULL, '2026-07-24 09:53:25', '2026-07-26 14:19:24'),
(2, 'Isaac O. Nyamari', 'procodes41@gmail.com', NULL, '$2y$12$e5/IVNMSntNWYJsg/0ETsuzfHx76qqBDtjcyVf1JYJ1bJXaeFdbb6', NULL, NULL, NULL, 'homPOi43fHfcJMO3NltaCfJGi7nq0TwXZEuid9lrQIFGStBRI0CfDlLaFHbI', 0, NULL, NULL, '2026-07-24 12:11:06', '2026-07-24 12:11:06'),
(138, 'Caldwell Osbora', 'pamek@mailinator.com', NULL, '$2y$12$Pc/t5/0lmEqHP6ua37MRQ.95EZSgrNiCekOl/aqv4WIYhzCc3P0Si', NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-07-30 22:40:53', '2026-07-31 12:43:18'),
(139, 'Adam Rodgers', 'pimenyvoh@mailinator.com', NULL, '$2y$12$SVCZVHqDyHrd7WB48ETOrOjNyZDprnjzCTRE/EWIDZB32OzUkDH3G', NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-07-30 22:40:54', '2026-07-30 22:40:54'),
(147, 'John Doe', 'johndoe@gmail.com', NULL, '$2y$12$Hi4p3Hd5wG.GiKuhPzdR.uD5zNuUOBux0IO0JZBa//SfWEjMHe/j2', NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-07-31 12:41:02', '2026-07-31 12:41:02');

-- --------------------------------------------------------

--
-- Table structure for table `zkteco_attendance_logs`
--

CREATE TABLE `zkteco_attendance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_id` bigint(20) UNSIGNED NOT NULL,
  `pin` varchar(255) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `verify_mode` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `work_code` varchar(32) NOT NULL DEFAULT '',
  `reserved_1` varchar(255) DEFAULT NULL,
  `reserved_2` varchar(255) DEFAULT NULL,
  `raw_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `occurred_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zkteco_attendance_logs`
--

INSERT INTO `zkteco_attendance_logs` (`id`, `device_id`, `pin`, `recorded_at`, `status`, `verify_mode`, `work_code`, `reserved_1`, `reserved_2`, `raw_data`, `created_at`, `updated_at`, `occurred_at`) VALUES
(13, 1, '1', '2026-07-29 14:59:03', 4, 1, '0', NULL, NULL, NULL, '2026-07-29 09:59:08', '2026-07-29 09:59:08', '2026-07-29 14:59:03'),
(14, 1, '35779255', '2026-07-29 15:03:26', 4, 1, '0', NULL, NULL, NULL, '2026-07-29 10:03:27', '2026-07-29 10:03:27', '2026-07-29 15:03:26'),
(15, 1, '35779255', '2026-07-31 05:09:19', 0, 1, '0', NULL, NULL, NULL, '2026-07-31 00:10:07', '2026-07-31 00:10:07', '2026-07-31 05:09:19'),
(16, 1, '35779255', '2026-07-31 05:11:56', 0, 1, '0', NULL, NULL, NULL, '2026-07-31 00:11:58', '2026-07-31 00:11:58', '2026-07-31 05:11:56'),
(17, 1, '35779255', '2026-07-31 05:45:00', 0, 1, '0', NULL, NULL, NULL, '2026-07-31 00:45:03', '2026-07-31 00:45:03', '2026-07-31 05:45:00'),
(18, 1, '139', '2026-07-31 06:11:31', 0, 0, '0', NULL, NULL, NULL, '2026-07-31 01:11:33', '2026-07-31 01:11:33', '2026-07-31 06:11:31'),
(19, 1, '35779255', '2026-07-31 12:09:55', 0, 1, '0', NULL, NULL, NULL, '2026-07-31 07:09:59', '2026-07-31 07:09:59', '2026-07-31 12:09:55'),
(20, 1, '35779255', '2026-07-31 12:10:10', 0, 1, '0', NULL, NULL, NULL, '2026-07-31 07:10:14', '2026-07-31 07:10:14', '2026-07-31 12:10:10'),
(21, 1, '35779255', '2026-07-31 12:11:52', 0, 1, '0', NULL, NULL, NULL, '2026-07-31 07:11:56', '2026-07-31 07:11:56', '2026-07-31 12:11:52'),
(22, 1, '35779255', '2026-07-31 12:51:10', 0, 1, '0', NULL, NULL, NULL, '2026-07-31 07:51:14', '2026-07-31 07:51:14', '2026-07-31 12:51:10'),
(23, 1, '35779255', '2026-07-31 15:41:22', 0, 1, '0', NULL, NULL, NULL, '2026-07-31 10:41:26', '2026-07-31 10:41:26', '2026-07-31 15:41:22'),
(24, 1, '35779255', '2026-07-31 15:44:21', 1, 0, '0', NULL, NULL, NULL, '2026-07-31 10:44:25', '2026-07-31 10:44:25', '2026-07-31 15:44:21'),
(25, 1, '139', '2026-07-31 15:50:34', 0, 0, '0', NULL, NULL, NULL, '2026-07-31 10:50:38', '2026-07-31 10:50:38', '2026-07-31 15:50:34');

-- --------------------------------------------------------

--
-- Table structure for table `zkteco_devices`
--

CREATE TABLE `zkteco_devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(64) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `firmware_version` varchar(255) DEFAULT NULL,
  `push_version` varchar(255) DEFAULT NULL,
  `device_type` varchar(255) DEFAULT NULL,
  `language` varchar(30) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'unknown',
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `last_sync_at` timestamp NULL DEFAULT NULL,
  `att_stamp` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `op_stamp` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `timezone` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zkteco_devices`
--

INSERT INTO `zkteco_devices` (`id`, `serial_number`, `name`, `ip_address`, `model`, `firmware_version`, `push_version`, `device_type`, `language`, `status`, `last_activity_at`, `last_sync_at`, `att_stamp`, `op_stamp`, `options`, `timezone`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'GED7261800014', 'Device GED7261800014', '192.168.88.248', NULL, NULL, '2.4.1', NULL, '69', 'online', '2026-07-31 11:45:39', NULL, 0, 0, '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=164,~MaxAttLogCount=20,UserCount=2,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=2,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.248,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', 'Africa/Nairobi', '2026-07-28 08:35:02', '2026-07-31 11:45:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `zkteco_device_commands`
--

CREATE TABLE `zkteco_device_commands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_id` bigint(20) UNSIGNED NOT NULL,
  `command_id` bigint(20) UNSIGNED DEFAULT NULL,
  `command_type` varchar(20) NOT NULL,
  `command_content` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `return_code` int(11) DEFAULT NULL,
  `queued_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `response` text DEFAULT NULL,
  `retry_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zkteco_device_commands`
--

INSERT INTO `zkteco_device_commands` (`id`, `device_id`, `command_id`, `command_type`, `command_content`, `status`, `return_code`, `queued_at`, `sent_at`, `acknowledged_at`, `response`, `retry_count`, `created_at`, `updated_at`) VALUES
(36, 1, 36, 'DATA', 'DATA QUERY USERINFO', 'acknowledged', 0, '2026-07-31 01:24:55', '2026-07-31 01:25:07', '2026-07-31 01:25:10', NULL, 0, '2026-07-31 01:24:55', '2026-07-31 01:25:10'),
(37, 1, 37, 'DATA', 'DATA UPDATE USERINFO	PIN=139	Name=Adam Rodgers	Pri=0	Card=867	Password=871', 'acknowledged', 0, '2026-07-31 01:24:56', '2026-07-31 01:25:07', '2026-07-31 01:25:10', NULL, 0, '2026-07-31 01:24:56', '2026-07-31 01:25:10'),
(38, 1, 38, 'DATA', 'DATA QUERY USERINFO', 'acknowledged', 0, '2026-07-31 01:27:02', '2026-07-31 01:27:11', '2026-07-31 01:27:16', NULL, 0, '2026-07-31 01:27:02', '2026-07-31 01:27:16'),
(39, 1, 39, 'DATA', 'DATA UPDATE USERINFO PIN=139	Name=Adam Rodgers	Pri=0	Card=867	Password=871', 'acknowledged', 0, '2026-07-31 01:27:03', '2026-07-31 01:27:11', '2026-07-31 01:27:16', NULL, 0, '2026-07-31 01:27:03', '2026-07-31 01:27:16'),
(40, 1, 40, 'DATA', 'DATA QUERY USERINFO', 'acknowledged', 0, '2026-07-31 01:28:56', '2026-07-31 01:28:59', '2026-07-31 01:29:03', NULL, 0, '2026-07-31 01:28:56', '2026-07-31 01:29:03'),
(41, 1, 41, 'DATA', 'DATA UPDATE USERINFO PIN=139	Name=Adam Rodgers	Pri=0	Password=871	Card=867	Password=871', 'acknowledged', 0, '2026-07-31 01:28:56', '2026-07-31 01:28:59', '2026-07-31 01:29:03', NULL, 0, '2026-07-31 01:28:56', '2026-07-31 01:29:03'),
(42, 1, 42, 'DATA', 'DATA QUERY USERINFO', 'acknowledged', 0, '2026-07-31 01:31:41', '2026-07-31 01:31:48', '2026-07-31 01:31:51', NULL, 0, '2026-07-31 01:31:41', '2026-07-31 01:31:51'),
(43, 1, 43, 'DATA', 'DATA UPDATE USERINFO PIN=139	Name=Adam Rodgers	Pri=0	Password=871', 'acknowledged', 0, '2026-07-31 01:31:41', '2026-07-31 01:31:48', '2026-07-31 01:31:51', NULL, 0, '2026-07-31 01:31:41', '2026-07-31 01:31:51'),
(44, 1, 44, 'DATA', 'DATA QUERY USERINFO', 'acknowledged', 0, '2026-07-31 07:39:48', '2026-07-31 07:39:50', '2026-07-31 07:39:55', NULL, 0, '2026-07-31 07:39:48', '2026-07-31 07:39:55'),
(45, 1, 45, 'DATA', 'DATA UPDATE USERINFO PIN=139	Name=Adam Rodgers	Pri=0	Password=871', 'acknowledged', 0, '2026-07-31 07:39:49', '2026-07-31 07:39:50', '2026-07-31 07:39:55', NULL, 0, '2026-07-31 07:39:49', '2026-07-31 07:39:55'),
(46, 1, 46, 'DATA', 'DATA QUERY USERINFO', 'acknowledged', 0, '2026-07-31 07:46:58', '2026-07-31 07:47:18', '2026-07-31 07:47:23', NULL, 0, '2026-07-31 07:46:58', '2026-07-31 07:47:23'),
(47, 1, 47, 'DATA', 'DATA UPDATE USERINFO PIN=139	Name=Adam Rodgers	Pri=0	Card=867	Passwd=871', 'acknowledged', 0, '2026-07-31 07:46:58', '2026-07-31 07:47:18', '2026-07-31 07:47:23', NULL, 0, '2026-07-31 07:46:58', '2026-07-31 07:47:23'),
(48, 1, 48, 'DATA', 'DATA QUERY USERINFO', 'acknowledged', 0, '2026-07-31 07:48:20', '2026-07-31 07:48:33', '2026-07-31 07:48:36', NULL, 0, '2026-07-31 07:48:20', '2026-07-31 07:48:36'),
(49, 1, 49, 'DATA', 'DATA UPDATE USERINFO PIN=139	Name=Adam Rodgers	Pri=0	Card=867	Passwd=871', 'acknowledged', 0, '2026-07-31 07:48:20', '2026-07-31 07:48:33', '2026-07-31 07:48:37', NULL, 0, '2026-07-31 07:48:20', '2026-07-31 07:48:37'),
(50, 1, 50, 'DATA', 'DATA QUERY USERINFO', 'sent', NULL, '2026-07-31 09:17:57', '2026-07-31 10:41:22', NULL, NULL, 0, '2026-07-31 09:17:57', '2026-07-31 10:41:22'),
(51, 1, 51, 'DATA', 'DATA UPDATE USERINFO PIN=139	Name=Adam Rodgers	Pri=0	Card=867	Passwd=871', 'sent', NULL, '2026-07-31 09:17:57', '2026-07-31 10:41:23', NULL, NULL, 0, '2026-07-31 09:17:57', '2026-07-31 10:41:23');

-- --------------------------------------------------------

--
-- Table structure for table `zkteco_device_events`
--

CREATE TABLE `zkteco_device_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_id` bigint(20) UNSIGNED NOT NULL,
  `event_type` varchar(30) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zkteco_device_events`
--

INSERT INTO `zkteco_device_events` (`id`, `device_id`, `event_type`, `payload`, `ip_address`, `created_at`) VALUES
(1, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-28 08:35:23'),
(2, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-28 08:35:25'),
(3, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-28 08:45:41'),
(4, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-28 08:47:27'),
(5, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-28 08:48:10'),
(6, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-28 09:29:40'),
(7, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-28 09:31:32'),
(8, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-28 09:38:08'),
(9, 1, 'connected', NULL, '192.168.88.250', '2026-07-28 18:16:25'),
(10, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=142,~MaxAttLogCount=20,UserCount=3,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=4,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.250,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.250', '2026-07-28 18:16:28'),
(11, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-28 18:16:31'),
(12, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-28 18:16:32'),
(13, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-28 18:16:36'),
(14, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-28 18:16:50'),
(15, 1, 'connected', NULL, '192.168.88.250', '2026-07-28 18:48:08'),
(16, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-28 18:48:14'),
(17, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-28 18:58:29'),
(18, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-28 18:58:31'),
(19, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-28 18:59:26'),
(20, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-28 19:00:00'),
(21, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-28 19:00:14'),
(22, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-28 19:09:34'),
(23, 1, 'connected', NULL, '192.168.88.250', '2026-07-29 03:54:44'),
(24, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=144,~MaxAttLogCount=20,UserCount=3,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=4,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.250,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.250', '2026-07-29 03:54:46'),
(25, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-29 03:55:06'),
(26, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 03:55:07'),
(27, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 03:55:08'),
(28, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 03:55:26'),
(29, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-29 03:55:34'),
(30, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=145,~MaxAttLogCount=20,UserCount=3,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=4,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.250,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.250', '2026-07-29 04:14:38'),
(31, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 04:15:39'),
(32, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=145,~MaxAttLogCount=20,UserCount=3,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=4,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.250,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.250', '2026-07-29 04:48:46'),
(33, 1, 'connected', NULL, '192.168.88.250', '2026-07-29 09:54:52'),
(34, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=145,~MaxAttLogCount=20,UserCount=3,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=4,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.250,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.250', '2026-07-29 09:54:52'),
(35, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 09:54:55'),
(36, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 09:55:54'),
(37, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-29 09:58:09'),
(38, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-29 09:59:08'),
(39, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 09:59:36'),
(40, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 09:59:58'),
(41, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 10:01:24'),
(42, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 10:01:58'),
(43, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 10:02:44'),
(44, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-29 10:03:27'),
(45, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-29 10:03:35'),
(46, 1, 'connected', NULL, '192.168.88.248', '2026-07-31 00:10:03'),
(47, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=154,~MaxAttLogCount=20,UserCount=1,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=2,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.248,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.248', '2026-07-31 00:10:04'),
(48, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 00:10:07'),
(49, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 00:10:08'),
(50, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 00:11:58'),
(51, 1, 'command_sent', '{\"command_id\":1,\"command\":\"USERINFO Query=All\"}', NULL, '2026-07-31 00:42:00'),
(52, 1, 'command_sent', '{\"command_id\":2,\"command\":\"SETUSER PIN=139\\tName=Adam Rodgers\\tPri=0\\tCard=867\\tPassword=871\"}', NULL, '2026-07-31 00:42:01'),
(53, 1, 'command_acknowledged', '{\"command_id\":1,\"return_code\":-1002}', NULL, '2026-07-31 00:42:10'),
(54, 1, 'command_acknowledged', '{\"command_id\":2,\"return_code\":-1002}', NULL, '2026-07-31 00:42:10'),
(55, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 00:42:15'),
(56, 1, 'command_sent', '{\"command_id\":3,\"command\":\"USERINFO Query=All\"}', NULL, '2026-07-31 00:42:40'),
(57, 1, 'command_sent', '{\"command_id\":4,\"command\":\"SETUSER PIN=139\\tName=Adam Rodgers\\tPri=0\\tCard=867\\tPassword=871\"}', NULL, '2026-07-31 00:42:40'),
(58, 1, 'command_acknowledged', '{\"command_id\":3,\"return_code\":-1002}', NULL, '2026-07-31 00:42:44'),
(59, 1, 'command_acknowledged', '{\"command_id\":4,\"return_code\":-1002}', NULL, '2026-07-31 00:42:44'),
(60, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 00:45:03'),
(61, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 00:45:08'),
(62, 1, 'command_sent', '{\"command_id\":5,\"command\":\"USERINFO Query=All\"}', NULL, '2026-07-31 00:47:48'),
(63, 1, 'command_sent', '{\"command_id\":6,\"command\":\"USERINFO Query=All\"}', NULL, '2026-07-31 00:47:50'),
(64, 1, 'command_sent', '{\"command_id\":7,\"command\":\"USERINFO Query=All\"}', NULL, '2026-07-31 00:48:02'),
(65, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 00:48:09'),
(66, 1, 'command_acknowledged', '{\"command_id\":5,\"return_code\":-1002}', NULL, '2026-07-31 00:48:15'),
(67, 1, 'command_acknowledged', '{\"command_id\":6,\"return_code\":-1002}', NULL, '2026-07-31 00:48:15'),
(68, 1, 'command_acknowledged', '{\"command_id\":7,\"return_code\":-1002}', NULL, '2026-07-31 00:48:15'),
(69, 1, 'command_sent', '{\"command_id\":8,\"command\":\"USERINFO Query=All\"}', NULL, '2026-07-31 00:53:23'),
(70, 1, 'command_acknowledged', '{\"command_id\":8,\"return_code\":-1002}', NULL, '2026-07-31 00:53:40'),
(71, 1, 'command_sent', '{\"command_id\":9,\"command\":\"USERINFO Query=All\"}', NULL, '2026-07-31 00:53:51'),
(72, 1, 'command_acknowledged', '{\"command_id\":9,\"return_code\":-1002}', NULL, '2026-07-31 00:53:54'),
(73, 1, 'command_sent', '{\"command_id\":10,\"command\":\"USERINFO Query=All\"}', NULL, '2026-07-31 00:54:28'),
(74, 1, 'command_acknowledged', '{\"command_id\":10,\"return_code\":-1002}', NULL, '2026-07-31 00:54:30'),
(75, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 00:54:42'),
(76, 1, 'command_sent', '{\"command_id\":11,\"command\":\"USERINFO Query=All\"}', NULL, '2026-07-31 00:55:01'),
(77, 1, 'command_acknowledged', '{\"command_id\":11,\"return_code\":-1002}', NULL, '2026-07-31 00:55:05'),
(78, 1, 'command_sent', '{\"command_id\":12,\"command\":\"USERINFO Query=All\"}', NULL, '2026-07-31 00:55:19'),
(79, 1, 'command_sent', '{\"command_id\":13,\"command\":\"SETUSER PIN=139\\tName=Adam Rodgers\\tPri=0\\tCard=867\\tPassword=871\"}', NULL, '2026-07-31 00:55:19'),
(80, 1, 'command_acknowledged', '{\"command_id\":12,\"return_code\":-1002}', NULL, '2026-07-31 00:55:22'),
(81, 1, 'command_acknowledged', '{\"command_id\":13,\"return_code\":-1002}', NULL, '2026-07-31 00:55:22'),
(82, 1, 'command_sent', '{\"command_id\":14,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 00:56:49'),
(83, 1, 'command_sent', '{\"command_id\":15,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPrivilege=0\\tCard=867\"}', NULL, '2026-07-31 00:56:49'),
(84, 1, 'command_sent', '{\"command_id\":16,\"command\":\"DATA UPDATE USERINFO PIN=139\\tPassword=871\"}', NULL, '2026-07-31 00:56:49'),
(85, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 00:56:58'),
(86, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 00:57:07'),
(87, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 00:57:08'),
(88, 1, 'command_acknowledged', '{\"command_id\":14,\"return_code\":0}', NULL, '2026-07-31 00:57:10'),
(89, 1, 'command_acknowledged', '{\"command_id\":15,\"return_code\":0}', NULL, '2026-07-31 00:57:10'),
(90, 1, 'command_acknowledged', '{\"command_id\":16,\"return_code\":0}', NULL, '2026-07-31 00:57:11'),
(91, 1, 'command_sent', '{\"command_id\":17,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 00:57:16'),
(92, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 00:57:21'),
(93, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 00:57:23'),
(94, 1, 'command_acknowledged', '{\"command_id\":17,\"return_code\":0}', NULL, '2026-07-31 00:57:24'),
(95, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 00:59:15'),
(96, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=156,~MaxAttLogCount=20,UserCount=2,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=2,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.248,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.248', '2026-07-31 00:59:57'),
(97, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:00:04'),
(98, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:00:43'),
(99, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:00:59'),
(100, 1, 'command_sent', '{\"command_id\":18,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:02:46'),
(101, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:02:55'),
(102, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:02:56'),
(103, 1, 'command_acknowledged', '{\"command_id\":18,\"return_code\":0}', NULL, '2026-07-31 01:02:58'),
(104, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:03:22'),
(105, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:03:34'),
(106, 1, 'command_sent', '{\"command_id\":19,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:03:37'),
(107, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:03:45'),
(108, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:03:46'),
(109, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:03:46'),
(110, 1, 'command_acknowledged', '{\"command_id\":19,\"return_code\":0}', NULL, '2026-07-31 01:03:48'),
(111, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=156,~MaxAttLogCount=20,UserCount=1,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=2,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.248,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.248', '2026-07-31 01:04:35'),
(112, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:04:41'),
(113, 1, 'command_sent', '{\"command_id\":20,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:04:58'),
(114, 1, 'command_sent', '{\"command_id\":21,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPrivilege=0\\tCard=867\"}', NULL, '2026-07-31 01:04:59'),
(115, 1, 'command_sent', '{\"command_id\":22,\"command\":\"DATA UPDATE USERINFO PIN=139\\tPassword=871\"}', NULL, '2026-07-31 01:04:59'),
(116, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:05:01'),
(117, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:05:03'),
(118, 1, 'command_acknowledged', '{\"command_id\":20,\"return_code\":0}', NULL, '2026-07-31 01:05:04'),
(119, 1, 'command_acknowledged', '{\"command_id\":21,\"return_code\":0}', NULL, '2026-07-31 01:05:04'),
(120, 1, 'command_acknowledged', '{\"command_id\":22,\"return_code\":0}', NULL, '2026-07-31 01:05:05'),
(121, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:05:36'),
(122, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:05:53'),
(123, 1, 'command_sent', '{\"command_id\":23,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:05:57'),
(124, 1, 'command_sent', '{\"command_id\":24,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPrivilege=0\\tCard=\"}', NULL, '2026-07-31 01:05:57'),
(125, 1, 'command_sent', '{\"command_id\":25,\"command\":\"DATA UPDATE USERINFO PIN=139\\tPassword=867\"}', NULL, '2026-07-31 01:05:57'),
(126, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:06:07'),
(127, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:06:08'),
(128, 1, 'command_acknowledged', '{\"command_id\":23,\"return_code\":0}', NULL, '2026-07-31 01:06:10'),
(129, 1, 'command_acknowledged', '{\"command_id\":24,\"return_code\":0}', NULL, '2026-07-31 01:06:10'),
(130, 1, 'command_acknowledged', '{\"command_id\":25,\"return_code\":0}', NULL, '2026-07-31 01:06:10'),
(131, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:07:19'),
(132, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:07:32'),
(133, 1, 'command_sent', '{\"command_id\":26,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:07:45'),
(134, 1, 'command_sent', '{\"command_id\":27,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPrivilege=0\\tCard=\"}', NULL, '2026-07-31 01:07:45'),
(135, 1, 'command_sent', '{\"command_id\":28,\"command\":\"DATA UPDATE USERINFO PIN=139\\tPassword=871\"}', NULL, '2026-07-31 01:07:45'),
(136, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:07:54'),
(137, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:07:56'),
(138, 1, 'command_acknowledged', '{\"command_id\":26,\"return_code\":0}', NULL, '2026-07-31 01:07:57'),
(139, 1, 'command_acknowledged', '{\"command_id\":27,\"return_code\":0}', NULL, '2026-07-31 01:07:57'),
(140, 1, 'command_acknowledged', '{\"command_id\":28,\"return_code\":0}', NULL, '2026-07-31 01:07:57'),
(141, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:08:03'),
(142, 1, 'command_sent', '{\"command_id\":29,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:10:02'),
(143, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:10:19'),
(144, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:10:21'),
(145, 1, 'command_acknowledged', '{\"command_id\":29,\"return_code\":0}', NULL, '2026-07-31 01:10:23'),
(146, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:10:42'),
(147, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:11:20'),
(148, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:11:22'),
(149, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 01:11:33'),
(150, 1, 'command_sent', '{\"command_id\":30,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:11:48'),
(151, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:11:51'),
(152, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:11:52'),
(153, 1, 'command_acknowledged', '{\"command_id\":30,\"return_code\":0}', NULL, '2026-07-31 01:11:54'),
(154, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:12:41'),
(155, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:12:53'),
(156, 1, 'command_sent', '{\"command_id\":31,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:12:57'),
(157, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:13:05'),
(158, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:13:06'),
(159, 1, 'command_acknowledged', '{\"command_id\":31,\"return_code\":0}', NULL, '2026-07-31 01:13:07'),
(160, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:13:12'),
(161, 1, 'command_sent', '{\"command_id\":32,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:16:20'),
(162, 1, 'command_sent', '{\"command_id\":33,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPri=0\\tCard=867\\tPassword=871\"}', NULL, '2026-07-31 01:16:20'),
(163, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:16:32'),
(164, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:16:38'),
(165, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:16:39'),
(166, 1, 'command_acknowledged', '{\"command_id\":32,\"return_code\":0}', NULL, '2026-07-31 01:16:41'),
(167, 1, 'command_acknowledged', '{\"command_id\":33,\"return_code\":0}', NULL, '2026-07-31 01:16:41'),
(168, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:16:53'),
(169, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:19:47'),
(170, 1, 'command_sent', '{\"command_id\":34,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:19:54'),
(171, 1, 'command_sent', '{\"command_id\":35,\"command\":\"DATA UPDATE USERINFO\\tPIN=139\\tName=Adam Rodgers\\tPri=0\\tCard=867\\tPassword=871\"}', NULL, '2026-07-31 01:19:54'),
(172, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:20:13'),
(173, 1, 'command_acknowledged', '{\"command_id\":34,\"return_code\":0}', NULL, '2026-07-31 01:20:15'),
(174, 1, 'command_acknowledged', '{\"command_id\":35,\"return_code\":0}', NULL, '2026-07-31 01:20:15'),
(175, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:24:42'),
(176, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:24:52'),
(177, 1, 'command_sent', '{\"command_id\":36,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:24:56'),
(178, 1, 'command_sent', '{\"command_id\":37,\"command\":\"DATA UPDATE USERINFO\\tPIN=139\\tName=Adam Rodgers\\tPri=0\\tCard=867\\tPassword=871\"}', NULL, '2026-07-31 01:24:56'),
(179, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:25:09'),
(180, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:25:09'),
(181, 1, 'command_acknowledged', '{\"command_id\":36,\"return_code\":0}', NULL, '2026-07-31 01:25:10'),
(182, 1, 'command_acknowledged', '{\"command_id\":37,\"return_code\":0}', NULL, '2026-07-31 01:25:10'),
(183, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:25:19'),
(184, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:26:40'),
(185, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:26:53'),
(186, 1, 'command_sent', '{\"command_id\":38,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:27:02'),
(187, 1, 'command_sent', '{\"command_id\":39,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPri=0\\tCard=867\\tPassword=871\"}', NULL, '2026-07-31 01:27:03'),
(188, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:27:13'),
(189, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:27:14'),
(190, 1, 'command_acknowledged', '{\"command_id\":38,\"return_code\":0}', NULL, '2026-07-31 01:27:16'),
(191, 1, 'command_acknowledged', '{\"command_id\":39,\"return_code\":0}', NULL, '2026-07-31 01:27:16'),
(192, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:27:24'),
(193, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:28:37'),
(194, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:28:49'),
(195, 1, 'command_sent', '{\"command_id\":40,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:28:56'),
(196, 1, 'command_sent', '{\"command_id\":41,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPri=0\\tPassword=871\\tCard=867\\tPassword=871\"}', NULL, '2026-07-31 01:28:56'),
(197, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:29:01'),
(198, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:29:01'),
(199, 1, 'command_acknowledged', '{\"command_id\":40,\"return_code\":0}', NULL, '2026-07-31 01:29:03'),
(200, 1, 'command_acknowledged', '{\"command_id\":41,\"return_code\":0}', NULL, '2026-07-31 01:29:03'),
(201, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:29:22'),
(202, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:31:26'),
(203, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 01:31:37'),
(204, 1, 'command_sent', '{\"command_id\":42,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 01:31:41'),
(205, 1, 'command_sent', '{\"command_id\":43,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPri=0\\tPassword=871\"}', NULL, '2026-07-31 01:31:41'),
(206, 1, 'user_synced', '{\"operation_count\":1}', NULL, '2026-07-31 01:31:50'),
(207, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 01:31:50'),
(208, 1, 'command_acknowledged', '{\"command_id\":42,\"return_code\":0}', NULL, '2026-07-31 01:31:51'),
(209, 1, 'command_acknowledged', '{\"command_id\":43,\"return_code\":0}', NULL, '2026-07-31 01:31:51'),
(210, 1, 'connected', NULL, '192.168.88.248', '2026-07-31 06:52:24'),
(211, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=157,~MaxAttLogCount=20,UserCount=2,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=2,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.248,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.248', '2026-07-31 06:52:25'),
(212, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 06:53:26'),
(213, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 07:05:48'),
(214, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 07:10:00'),
(215, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 07:10:14'),
(216, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 07:11:56'),
(217, 1, 'command_sent', '{\"command_id\":44,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 07:39:48'),
(218, 1, 'command_sent', '{\"command_id\":45,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPri=0\\tPassword=871\"}', NULL, '2026-07-31 07:39:49'),
(219, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 07:39:52'),
(220, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 07:39:53'),
(221, 1, 'command_acknowledged', '{\"command_id\":44,\"return_code\":0}', NULL, '2026-07-31 07:39:55'),
(222, 1, 'command_acknowledged', '{\"command_id\":45,\"return_code\":0}', NULL, '2026-07-31 07:39:55'),
(223, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 07:40:02'),
(224, 1, 'command_sent', '{\"command_id\":46,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 07:46:58'),
(225, 1, 'command_sent', '{\"command_id\":47,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPri=0\\tCard=867\\tPasswd=871\"}', NULL, '2026-07-31 07:46:58'),
(226, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 07:47:20'),
(227, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 07:47:21'),
(228, 1, 'command_acknowledged', '{\"command_id\":46,\"return_code\":0}', NULL, '2026-07-31 07:47:23'),
(229, 1, 'command_acknowledged', '{\"command_id\":47,\"return_code\":0}', NULL, '2026-07-31 07:47:23'),
(230, 1, 'command_sent', '{\"command_id\":48,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 07:48:20'),
(231, 1, 'command_sent', '{\"command_id\":49,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPri=0\\tCard=867\\tPasswd=871\"}', NULL, '2026-07-31 07:48:21'),
(232, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 07:48:29'),
(233, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 07:48:35'),
(234, 1, 'user_synced', '{\"operation_count\":2}', NULL, '2026-07-31 07:48:36'),
(235, 1, 'command_acknowledged', '{\"command_id\":48,\"return_code\":0}', NULL, '2026-07-31 07:48:37'),
(236, 1, 'command_acknowledged', '{\"command_id\":49,\"return_code\":0}', NULL, '2026-07-31 07:48:37'),
(237, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 07:51:14'),
(238, 1, 'command_sent', '{\"command_id\":50,\"command\":\"DATA QUERY USERINFO\"}', NULL, '2026-07-31 09:17:57'),
(239, 1, 'command_sent', '{\"command_id\":51,\"command\":\"DATA UPDATE USERINFO PIN=139\\tName=Adam Rodgers\\tPri=0\\tCard=867\\tPasswd=871\"}', NULL, '2026-07-31 09:17:57'),
(240, 1, 'connected', NULL, '192.168.88.248', '2026-07-31 10:41:22'),
(241, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=161,~MaxAttLogCount=20,UserCount=2,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=2,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.248,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.248', '2026-07-31 10:41:23'),
(242, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 10:41:26'),
(243, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 10:42:23'),
(244, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 10:44:25'),
(245, 1, 'attendance_synced', '{\"count\":1}', NULL, '2026-07-31 10:50:38'),
(246, 1, 'info_received', '{\"~DeviceName\":\"K40 Pro,MAC=00:17:61:11:21:91,TransactionCount=164,~MaxAttLogCount=20,UserCount=2,~MaxUserCount=30,PhotoFunOn=1,~MaxUserPhotoCount=1000,FingerFunOn=1,FPVersion=10,~MaxFingerCount=30,FPCount=2,FaceFunOn=0,FaceVersion=7,~MaxFaceCount=400,FaceCount=0,FvFunOn=0,FvVersion=3,~MaxFvCount=10,FvCount=0,PvFunOn=,PvVersion=,~MaxPvCount=,PvCount=0,Language=69,IPAddress=192.168.88.248,~Platform=ZLM60_TFT,~OEMVendor=ZKTECO CO., LTD.,FWVersion=Ver 8.0.4.3-20230515,PushVersion=Ver 2.0.33S-20220613,RegDeviceType=,VisilightFun=,MultiBioDataSupport=,MultiBioPhotoSupport=,IRTempDetectionFunOn=,MaskDetectionFunOn=,UserPicURLFunOn=1,VisualIntercomFunOn=,VideoTID=,QRCodeDecryptFunList=,VideoProtocol=,IsSupportQRcode=,QRCodeEnable=,SubcontractingUpgradeFunOn=1\"}', '192.168.88.248', '2026-07-31 11:21:09'),
(247, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 11:21:09'),
(248, 1, 'user_synced', '{\"operation_count\":0}', NULL, '2026-07-31 11:21:19');

-- --------------------------------------------------------

--
-- Table structure for table `zkteco_users`
--

CREATE TABLE `zkteco_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pin` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `card_number` varchar(255) DEFAULT NULL,
  `privilege` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `password` varchar(255) DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `fingerprints` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fingerprints`)),
  `face_templates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`face_templates`)),
  `device_id` bigint(20) UNSIGNED DEFAULT NULL,
  `app_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zkteco_users`
--

INSERT INTO `zkteco_users` (`id`, `pin`, `name`, `card_number`, `privilege`, `password`, `group`, `is_enabled`, `fingerprints`, `face_templates`, `device_id`, `app_user_id`, `created_at`, `updated_at`) VALUES
(2, '35779255', 'Isaac', '4605189', 14, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2026-07-31 00:57:07', '2026-07-31 00:57:07'),
(3, '139', 'Adam Rodgers', '867', 0, NULL, NULL, 1, NULL, NULL, NULL, 139, '2026-07-31 01:16:20', '2026-07-31 07:48:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `classes_institution_id_name_unique` (`institution_id`,`name`),
  ADD KEY `classes_class_teacher_id_foreign` (`class_teacher_id`);

--
-- Indexes for table `curricula`
--
ALTER TABLE `curricula`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `curriculums`
--
ALTER TABLE `curriculums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `devices_institution_id_foreign` (`institution_id`),
  ADD KEY `devices_zkteco_device_id_foreign` (`zkteco_device_id`);

--
-- Indexes for table `examinations`
--
ALTER TABLE `examinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `examinations_institution_id_exam_date_index` (`institution_id`,`exam_date`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fees_student_id_foreign` (`student_id`),
  ADD KEY `fees_parent_id_foreign` (`parent_id`),
  ADD KEY `fees_institution_id_student_id_index` (`institution_id`,`student_id`),
  ADD KEY `fees_due_date_index` (`due_date`);

--
-- Indexes for table `institutions`
--
ALTER TABLE `institutions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `institutions_code_unique` (`code`),
  ADD KEY `institutions_user_id_foreign` (`user_id`),
  ADD KEY `institutions_curriculum_foreign` (`curriculum`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `parent_details`
--
ALTER TABLE `parent_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_phone` (`parent_phone`),
  ADD KEY `parent_details_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `passkeys`
--
ALTER TABLE `passkeys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `passkeys_credential_id_unique` (`credential_id`),
  ADD KEY `passkeys_user_id_index` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `student_details`
--
ALTER TABLE `student_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_details_admission_number_unique` (`admission_number`),
  ADD UNIQUE KEY `student_details_student_number_unique` (`student_number`),
  ADD KEY `student_details_student_id_foreign` (`student_id`),
  ADD KEY `student_details_parent_id_foreign` (`parent_id`),
  ADD KEY `student_details_institution_id_index` (`institution_id`),
  ADD KEY `student_details_admission_number_index` (`admission_number`),
  ADD KEY `student_details_enrollment_status_index` (`enrollment_status`),
  ADD KEY `student_details_is_active_index` (`is_active`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `teacher_details`
--
ALTER TABLE `teacher_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_details_employee_number_unique` (`employee_number`),
  ADD KEY `teacher_details_teacher_id_foreign` (`teacher_id`),
  ADD KEY `teacher_details_institution_id_status_index` (`institution_id`,`status`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenants_domain_unique` (`domain`),
  ADD UNIQUE KEY `tenants_database_unique` (`database`);

--
-- Indexes for table `timetable_entries`
--
ALTER TABLE `timetable_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `timetable_entries_teacher_id_foreign` (`teacher_id`),
  ADD KEY `timetable_entries_institution_id_day_of_week_index` (`institution_id`,`day_of_week`),
  ADD KEY `timetable_entries_class_id_foreign` (`class_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `zkteco_attendance_logs`
--
ALTER TABLE `zkteco_attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `zkteco_attendance_logs_device_id_pin_recorded_at_index` (`device_id`,`pin`,`recorded_at`),
  ADD KEY `zkteco_attendance_logs_recorded_at_index` (`recorded_at`),
  ADD KEY `zkteco_attendance_logs_pin_index` (`pin`),
  ADD KEY `zkteco_attendance_logs_occurred_at_index` (`occurred_at`);

--
-- Indexes for table `zkteco_devices`
--
ALTER TABLE `zkteco_devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `zkteco_devices_serial_number_unique` (`serial_number`),
  ADD KEY `zkteco_devices_last_activity_at_index` (`last_activity_at`),
  ADD KEY `zkteco_devices_status_index` (`status`);

--
-- Indexes for table `zkteco_device_commands`
--
ALTER TABLE `zkteco_device_commands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `zkteco_device_commands_device_id_status_index` (`device_id`,`status`),
  ADD KEY `zkteco_device_commands_status_index` (`status`),
  ADD KEY `zkteco_device_commands_command_id_index` (`command_id`);

--
-- Indexes for table `zkteco_device_events`
--
ALTER TABLE `zkteco_device_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `zkteco_device_events_device_id_event_type_index` (`device_id`,`event_type`),
  ADD KEY `zkteco_device_events_created_at_index` (`created_at`);

--
-- Indexes for table `zkteco_users`
--
ALTER TABLE `zkteco_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `zkteco_users_pin_unique` (`pin`),
  ADD KEY `zkteco_users_device_id_foreign` (`device_id`),
  ADD KEY `zkteco_users_card_number_index` (`card_number`),
  ADD KEY `zkteco_users_is_enabled_index` (`is_enabled`),
  ADD KEY `zkteco_users_app_user_id_index` (`app_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `curricula`
--
ALTER TABLE `curricula`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `curriculums`
--
ALTER TABLE `curriculums`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `examinations`
--
ALTER TABLE `examinations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `institutions`
--
ALTER TABLE `institutions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `parent_details`
--
ALTER TABLE `parent_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `passkeys`
--
ALTER TABLE `passkeys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_details`
--
ALTER TABLE `student_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher_details`
--
ALTER TABLE `teacher_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `timetable_entries`
--
ALTER TABLE `timetable_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `zkteco_attendance_logs`
--
ALTER TABLE `zkteco_attendance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `zkteco_devices`
--
ALTER TABLE `zkteco_devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `zkteco_device_commands`
--
ALTER TABLE `zkteco_device_commands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `zkteco_device_events`
--
ALTER TABLE `zkteco_device_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT for table `zkteco_users`
--
ALTER TABLE `zkteco_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_class_teacher_id_foreign` FOREIGN KEY (`class_teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `classes_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `devices_zkteco_device_id_foreign` FOREIGN KEY (`zkteco_device_id`) REFERENCES `zkteco_devices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `examinations`
--
ALTER TABLE `examinations`
  ADD CONSTRAINT `examinations_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fees_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fees_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `institutions`
--
ALTER TABLE `institutions`
  ADD CONSTRAINT `institutions_curriculum_foreign` FOREIGN KEY (`curriculum`) REFERENCES `curricula` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `institutions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parent_details`
--
ALTER TABLE `parent_details`
  ADD CONSTRAINT `parent_details_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `passkeys`
--
ALTER TABLE `passkeys`
  ADD CONSTRAINT `passkeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_details`
--
ALTER TABLE `student_details`
  ADD CONSTRAINT `student_details_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_details_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_details`
--
ALTER TABLE `teacher_details`
  ADD CONSTRAINT `teacher_details_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_details_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `timetable_entries`
--
ALTER TABLE `timetable_entries`
  ADD CONSTRAINT `timetable_entries_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `timetable_entries_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timetable_entries_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `zkteco_attendance_logs`
--
ALTER TABLE `zkteco_attendance_logs`
  ADD CONSTRAINT `zkteco_attendance_logs_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `zkteco_devices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `zkteco_device_commands`
--
ALTER TABLE `zkteco_device_commands`
  ADD CONSTRAINT `zkteco_device_commands_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `zkteco_devices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `zkteco_device_events`
--
ALTER TABLE `zkteco_device_events`
  ADD CONSTRAINT `zkteco_device_events_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `zkteco_devices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `zkteco_users`
--
ALTER TABLE `zkteco_users`
  ADD CONSTRAINT `zkteco_users_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `zkteco_devices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;