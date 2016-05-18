-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 18, 2016 at 04:40 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bugtracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `bugedits`
--

DROP TABLE IF EXISTS `bugedits`;
CREATE TABLE IF NOT EXISTS `bugedits` (
  `EditID` int(12) NOT NULL AUTO_INCREMENT,
  `EditorID` int(12) NOT NULL,
  `DateEdited` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Closure` tinyint(1) NOT NULL,
  `Comment` varchar(5000) NOT NULL,
  PRIMARY KEY (`EditID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bugs`
--

DROP TABLE IF EXISTS `bugs`;
CREATE TABLE IF NOT EXISTS `bugs` (
  `BugID` int(12) NOT NULL AUTO_INCREMENT,
  `Number` int(12) NOT NULL,
  `Summary` varchar(5000) NOT NULL,
  `CreatorID` int(12) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DateClosed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Fixed` tinyint(1) NOT NULL,
  `Closed` tinyint(1) NOT NULL,
  `ClosedByUserID` int(12) NOT NULL,
  PRIMARY KEY (`BugID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `bugs`
--

INSERT INTO `bugs` (`BugID`, `Number`, `Summary`, `CreatorID`, `DateCreated`, `DateClosed`, `Fixed`, `Closed`, `ClosedByUserID`) VALUES
(1, 1, 'Big bug!', 1, '2016-05-16 07:05:11', '0000-00-00 00:00:00', 1, 1, 1),
(2, 2, 'Small bug! Ah!!!', 1, '2016-05-18 16:35:52', '0000-00-00 00:00:00', 1, 1, 1),
(3, 3, 'Tiny bug.', 2, '2016-05-16 07:05:23', '0000-00-00 00:00:00', 1, 1, 1),
(4, 4, 'HUGE bug.', 2, '2016-05-16 07:14:00', '0000-00-00 00:00:00', 0, 1, 2),
(5, 5, 'Little bug!', 2, '2016-05-16 07:14:32', '0000-00-00 00:00:00', 1, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `loginattempts`
--

DROP TABLE IF EXISTS `loginattempts`;
CREATE TABLE IF NOT EXISTS `loginattempts` (
  `UserID` int(12) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Success` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `loginattempts`
--

INSERT INTO `loginattempts` (`UserID`, `Time`, `Success`) VALUES
(1, '2016-05-16 08:37:32', 1),
(1, '2016-05-16 18:46:59', 1),
(1, '2016-05-16 20:17:43', 1),
(2, '2016-05-16 20:17:57', 1),
(1, '2016-05-16 20:36:44', 1),
(1, '2016-05-16 20:41:10', 0),
(1, '2016-05-16 20:41:13', 0),
(1, '2016-05-16 20:41:15', 0),
(1, '2016-05-16 20:41:16', 0),
(1, '2016-05-16 20:46:19', 0),
(1, '2016-05-16 20:46:39', 1),
(1, '2016-05-16 20:47:02', 0),
(1, '2016-05-16 20:47:06', 0),
(1, '2016-05-16 20:47:08', 0),
(1, '2016-05-16 20:47:11', 0),
(1, '2016-05-16 22:22:23', 0),
(1, '2016-05-16 22:22:25', 1),
(1, '2016-05-16 22:25:36', 1),
(1, '2016-05-18 16:35:42', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notify`
--

DROP TABLE IF EXISTS `notify`;
CREATE TABLE IF NOT EXISTS `notify` (
  `NotifyID` int(12) NOT NULL AUTO_INCREMENT,
  `BugID` int(12) NOT NULL,
  `UserID` int(12) NOT NULL,
  PRIMARY KEY (`NotifyID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(12) NOT NULL AUTO_INCREMENT,
  `EmployeeNumber` int(4) NOT NULL,
  `Username` varchar(20) COLLATE utf16_unicode_ci NOT NULL,
  `Email` varchar(256) COLLATE utf16_unicode_ci NOT NULL,
  `PasswordHash` varchar(60) COLLATE utf16_unicode_ci NOT NULL,
  `ResetFlag` tinyint(1) NOT NULL,
  `ResetPassword` varchar(12) COLLATE utf16_unicode_ci NOT NULL,
  `LoginAttempts` int(11) NOT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf16 COLLATE=utf16_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `EmployeeNumber`, `Username`, `Email`, `PasswordHash`, `ResetFlag`, `ResetPassword`, `LoginAttempts`) VALUES
(1, 1111, 'alex', 'afando@gmail.com', '$2y$10$TEF1pbakChf9gJEDQNpdHuSMrtZDnoblKgvoTkPIwn3wVBgvW6o0u', 0, '', 0),
(2, 2222, 'bill', 'afando@hotmail.com', '$2y$10$ugwq0/gG3xXVV6Meq.amVOz8nm2yZxZ1qygqdGXlbY0P9n12PqJ5a', 0, '', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
