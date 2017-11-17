-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2017 at 03:41 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eventsdb`
--
CREATE DATABASE IF NOT EXISTS `eventsdb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `eventsdb`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `sID` int(10) UNSIGNED NOT NULL,
  `adminType` smallint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `alleventsbystudent`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `alleventsbystudent`;
CREATE TABLE `alleventsbystudent` (
`sID` bigint(20) unsigned
,`eID` int(11) unsigned
,`name` varchar(150)
,`description` mediumtext
,`category` varchar(50)
,`startTime` datetime
,`endTime` datetime
,`eventType` smallint(6) unsigned
,`approved` tinyint(4)
,`manager` int(11) unsigned
,`locID` int(11) unsigned
,`rsoEvent` int(11) unsigned
,`privUniversity` int(11) unsigned
,`externID` varchar(50)
,`externSource` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `alleventslocs`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `alleventslocs`;
CREATE TABLE `alleventslocs` (
`eID` int(10) unsigned
,`name` varchar(150)
,`eventType` smallint(1) unsigned
,`latitude` double
,`longitude` double
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `attendevents`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `attendevents`;
CREATE TABLE `attendevents` (
`eID` int(10) unsigned
,`name` varchar(150)
,`description` text
,`category` varchar(50)
,`startTime` datetime
,`endTime` datetime
,`eventType` smallint(1) unsigned
,`approved` tinyint(1)
,`manager` int(10) unsigned
,`locID` int(10) unsigned
,`rsoEvent` int(10) unsigned
,`privUniversity` int(10) unsigned
,`externID` varchar(50)
,`externSource` varchar(50)
,`sID` int(10) unsigned
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `attendeventslocs`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `attendeventslocs`;
CREATE TABLE `attendeventslocs` (
`eID` int(10) unsigned
,`sID` int(10) unsigned
,`name` varchar(150)
,`eventType` smallint(1) unsigned
,`latitude` double
,`longitude` double
);

-- --------------------------------------------------------

--
-- Table structure for table `attendingevents`
--

DROP TABLE IF EXISTS `attendingevents`;
CREATE TABLE `attendingevents` (
  `sID` int(10) UNSIGNED NOT NULL,
  `eID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `availeventslocs`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `availeventslocs`;
CREATE TABLE `availeventslocs` (
`eID` int(11) unsigned
,`sID` bigint(20) unsigned
,`name` varchar(150)
,`eventType` smallint(6) unsigned
,`latitude` double
,`longitude` double
);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `eID` int(10) UNSIGNED NOT NULL,
  `sID` int(10) UNSIGNED NOT NULL,
  `dateTime` datetime NOT NULL,
  `comment` text,
  `rating` smallint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `comments`
--
DROP TRIGGER IF EXISTS `Comments_BEFORE_INSERT`;
DELIMITER $$
CREATE TRIGGER `Comments_BEFORE_INSERT` BEFORE INSERT ON `comments` FOR EACH ROW BEGIN
    DECLARE msg VARCHAR(128);
    IF (NEW.rating < 0 OR NEW.rating > 5) then
        SET msg = concat('Events Error: Ratings must be between 0-5 inclusive: rating=', CAST(NEW.rating AS CHAR(5)));
        SIGNAL SQLSTATE '45000' SET message_text = msg;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `Comments_BEFORE_UPDATE`;
DELIMITER $$
CREATE TRIGGER `Comments_BEFORE_UPDATE` BEFORE UPDATE ON `comments` FOR EACH ROW BEGIN
    DECLARE msg VARCHAR(128);
    IF (NEW.rating < 0 OR NEW.rating > 5) then
        SET msg = concat('Events Error: Ratings must be between 0-5 inclusive: rating=', CAST(NEW.rating AS CHAR(5)));
        SIGNAL SQLSTATE '45000' SET message_text = msg;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `uID` int(10) UNSIGNED NOT NULL,
  `sID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `eID` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text,
  `category` varchar(50) DEFAULT NULL,
  `startTime` datetime NOT NULL,
  `endTime` datetime NOT NULL,
  `eventType` smallint(1) UNSIGNED DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `manager` int(10) UNSIGNED NOT NULL,
  `locID` int(10) UNSIGNED NOT NULL,
  `rsoEvent` int(10) UNSIGNED DEFAULT NULL,
  `privUniversity` int(10) UNSIGNED DEFAULT NULL,
  `externID` varchar(50) DEFAULT NULL,
  `externSource` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `events`
--
DROP TRIGGER IF EXISTS `Events_BEFORE_INSERT`;
DELIMITER $$
CREATE TRIGGER `Events_BEFORE_INSERT` BEFORE INSERT ON `events` FOR EACH ROW BEGIN
    DECLARE msg VARCHAR(128);
    DECLARE existing INT;
    SET existing = (
		SELECT eID FROM Events E
			WHERE locID = NEW.locID AND (
            (E.startTime >= NEW.startTime AND E.startTime <= NEW.endTime) OR
			(E.endTime   >= NEW.startTime AND E.endTime   <= NEW.endTime) OR
			(E.startTime >= NEW.startTime AND E.endTime   <= NEW.endTime) OR
			(E.startTime <= NEW.startTime AND E.endTime   >= NEW.endTime))
    );
    IF (existing IS NOT NULL) THEN
        SET msg = 'Events Error: Event overlaps with another event for the same timeframe / location.';
        SIGNAL SQLSTATE '45000' SET message_text = msg;
    END IF;
        IF ((NEW.eventType = 2) AND (NEW.privUniversity IS NULL)) THEN
		SET msg = 'Events Error: Private events must have an associated University.';
        SIGNAL SQLSTATE '45000' SET message_text = msg;
	END IF;
    IF ((NEW.eventType = 3) AND (NEW.rsoEvent IS NULL)) THEN
		SET msg = 'Events Error: Private events must have an associated University.';
        SIGNAL SQLSTATE '45000' SET message_text = msg;
	END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `Events_BEFORE_UPDATE`;
DELIMITER $$
CREATE TRIGGER `Events_BEFORE_UPDATE` BEFORE UPDATE ON `events` FOR EACH ROW BEGIN
    DECLARE msg VARCHAR(128);
    DECLARE existing INT;
    SET existing = (
		SELECT eID FROM Events E
			WHERE locID = NEW.locID AND (
            (E.startTime >= NEW.startTime AND E.startTime <= NEW.endTime) OR
			(E.endTime   >= NEW.startTime AND E.endTime   <= NEW.endTime) OR
			(E.startTime >= NEW.startTime AND E.endTime   <= NEW.endTime) OR
			(E.startTime <= NEW.startTime AND E.endTime   >= NEW.endTime))
    );
    IF (existing IS NOT NULL) THEN
        SET msg = 'Events Error: Event overlaps with another event for the same timeframe / location.';
        SIGNAL SQLSTATE '45000' SET message_text = msg;
    END IF;
    IF ((NEW.eventType = 2) AND (NEW.privUniversity IS NULL)) THEN
		SET msg = 'Events Error: Private events must have an associated University.';
        SIGNAL SQLSTATE '45000' SET message_text = msg;
	END IF;
    IF ((NEW.eventType = 3) AND (NEW.rsoEvent IS NULL)) THEN
		SET msg = 'Events Error: Private events must have an associated University.';
        SIGNAL SQLSTATE '45000' SET message_text = msg;
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `eventswithlocations`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `eventswithlocations`;
CREATE TABLE `eventswithlocations` (
`eID` int(10) unsigned
,`name` varchar(150)
,`description` text
,`category` varchar(50)
,`startTime` datetime
,`endTime` datetime
,`eventType` smallint(1) unsigned
,`approved` tinyint(1)
,`manager` int(10) unsigned
,`locID` int(10) unsigned
,`rsoEvent` int(10) unsigned
,`privUniversity` int(10) unsigned
,`externID` varchar(50)
,`externSource` varchar(50)
,`locName` varchar(50)
,`locDescription` text
,`latitude` double
,`longitude` double
);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
  `locID` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` text,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `privateeventsbystudent`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `privateeventsbystudent`;
CREATE TABLE `privateeventsbystudent` (
`sID` int(10) unsigned
,`eID` int(10) unsigned
,`name` varchar(150)
,`description` text
,`category` varchar(50)
,`startTime` datetime
,`endTime` datetime
,`eventType` smallint(1) unsigned
,`approved` tinyint(1)
,`manager` int(10) unsigned
,`locID` int(10) unsigned
,`rsoEvent` int(10) unsigned
,`privUniversity` int(10) unsigned
,`externID` varchar(50)
,`externSource` varchar(50)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `rsoeventsbystudent`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `rsoeventsbystudent`;
CREATE TABLE `rsoeventsbystudent` (
`sID` int(10) unsigned
,`eID` int(10) unsigned
,`name` varchar(150)
,`description` text
,`category` varchar(50)
,`startTime` datetime
,`endTime` datetime
,`eventType` smallint(1) unsigned
,`approved` tinyint(1)
,`manager` int(10) unsigned
,`locID` int(10) unsigned
,`rsoEvent` int(10) unsigned
,`privUniversity` int(10) unsigned
,`externID` varchar(50)
,`externSource` varchar(50)
);

-- --------------------------------------------------------

--
-- Table structure for table `rsomemberships`
--

DROP TABLE IF EXISTS `rsomemberships`;
CREATE TABLE `rsomemberships` (
  `rID` int(10) UNSIGNED NOT NULL,
  `sID` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rsos`
--

DROP TABLE IF EXISTS `rsos`;
CREATE TABLE `rsos` (
  `rID` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `description` text,
  `belongsTo` int(10) UNSIGNED NOT NULL,
  `studentAdmin` int(10) UNSIGNED NOT NULL,
  `rsoManager` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `rsosanduniversity`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `rsosanduniversity`;
CREATE TABLE `rsosanduniversity` (
`rID` int(10) unsigned
,`name` varchar(150)
,`description` text
,`belongsTo` int(10) unsigned
,`studentAdmin` int(10) unsigned
,`rsoManager` int(10) unsigned
,`universityName` varchar(75)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `rsosuniversityandmembers`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `rsosuniversityandmembers`;
CREATE TABLE `rsosuniversityandmembers` (
`rID` int(10) unsigned
,`name` varchar(150)
,`description` text
,`belongsTo` int(10) unsigned
,`studentAdmin` int(10) unsigned
,`rsoManager` int(10) unsigned
,`universityName` varchar(75)
,`sID` int(10) unsigned
);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `sID` int(10) UNSIGNED NOT NULL,
  `email` varchar(150) NOT NULL,
  `firstName` varchar(25) DEFAULT NULL,
  `lastName` varchar(25) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `universities`
--

DROP TABLE IF EXISTS `universities`;
CREATE TABLE `universities` (
  `uID` int(10) UNSIGNED NOT NULL,
  `name` varchar(75) DEFAULT NULL,
  `description` text,
  `numStudents` int(11) DEFAULT NULL,
  `picture` varchar(350) DEFAULT NULL,
  `univManager` int(10) UNSIGNED NOT NULL,
  `emailSuffix` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for view `alleventsbystudent`
--
DROP TABLE IF EXISTS `alleventsbystudent`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `alleventsbystudent`  AS  select `privateeventsbystudent`.`sID` AS `sID`,`privateeventsbystudent`.`eID` AS `eID`,`privateeventsbystudent`.`name` AS `name`,`privateeventsbystudent`.`description` AS `description`,`privateeventsbystudent`.`category` AS `category`,`privateeventsbystudent`.`startTime` AS `startTime`,`privateeventsbystudent`.`endTime` AS `endTime`,`privateeventsbystudent`.`eventType` AS `eventType`,`privateeventsbystudent`.`approved` AS `approved`,`privateeventsbystudent`.`manager` AS `manager`,`privateeventsbystudent`.`locID` AS `locID`,`privateeventsbystudent`.`rsoEvent` AS `rsoEvent`,`privateeventsbystudent`.`privUniversity` AS `privUniversity`,`privateeventsbystudent`.`externID` AS `externID`,`privateeventsbystudent`.`externSource` AS `externSource` from `privateeventsbystudent` union select `rsoeventsbystudent`.`sID` AS `sID`,`rsoeventsbystudent`.`eID` AS `eID`,`rsoeventsbystudent`.`name` AS `name`,`rsoeventsbystudent`.`description` AS `description`,`rsoeventsbystudent`.`category` AS `category`,`rsoeventsbystudent`.`startTime` AS `startTime`,`rsoeventsbystudent`.`endTime` AS `endTime`,`rsoeventsbystudent`.`eventType` AS `eventType`,`rsoeventsbystudent`.`approved` AS `approved`,`rsoeventsbystudent`.`manager` AS `manager`,`rsoeventsbystudent`.`locID` AS `locID`,`rsoeventsbystudent`.`rsoEvent` AS `rsoEvent`,`rsoeventsbystudent`.`privUniversity` AS `privUniversity`,`rsoeventsbystudent`.`externID` AS `externID`,`rsoeventsbystudent`.`externSource` AS `externSource` from `rsoeventsbystudent` union select 0 AS `sID`,`evt`.`eID` AS `eID`,`evt`.`name` AS `name`,`evt`.`description` AS `description`,`evt`.`category` AS `category`,`evt`.`startTime` AS `startTime`,`evt`.`endTime` AS `endTime`,`evt`.`eventType` AS `eventType`,`evt`.`approved` AS `approved`,`evt`.`manager` AS `manager`,`evt`.`locID` AS `locID`,`evt`.`rsoEvent` AS `rsoEvent`,`evt`.`privUniversity` AS `privUniversity`,`evt`.`externID` AS `externID`,`evt`.`externSource` AS `externSource` from `events` `evt` where (`evt`.`eventType` = 1) ;

-- --------------------------------------------------------

--
-- Structure for view `alleventslocs`
--
DROP TABLE IF EXISTS `alleventslocs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `alleventslocs`  AS  select `e`.`eID` AS `eID`,`e`.`name` AS `name`,`e`.`eventType` AS `eventType`,`l`.`latitude` AS `latitude`,`l`.`longitude` AS `longitude` from (`events` `e` join `locations` `l` on((`l`.`locID` = `e`.`locID`))) ;

-- --------------------------------------------------------

--
-- Structure for view `attendevents`
--
DROP TABLE IF EXISTS `attendevents`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `attendevents`  AS  select `e`.`eID` AS `eID`,`e`.`name` AS `name`,`e`.`description` AS `description`,`e`.`category` AS `category`,`e`.`startTime` AS `startTime`,`e`.`endTime` AS `endTime`,`e`.`eventType` AS `eventType`,`e`.`approved` AS `approved`,`e`.`manager` AS `manager`,`e`.`locID` AS `locID`,`e`.`rsoEvent` AS `rsoEvent`,`e`.`privUniversity` AS `privUniversity`,`e`.`externID` AS `externID`,`e`.`externSource` AS `externSource`,`a`.`sID` AS `sID` from (`events` `e` join `attendingevents` `a` on((`a`.`eID` = `e`.`eID`))) ;

-- --------------------------------------------------------

--
-- Structure for view `attendeventslocs`
--
DROP TABLE IF EXISTS `attendeventslocs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `attendeventslocs`  AS  select `e`.`eID` AS `eID`,`a`.`sID` AS `sID`,`e`.`name` AS `name`,`e`.`eventType` AS `eventType`,`l`.`latitude` AS `latitude`,`l`.`longitude` AS `longitude` from ((`events` `e` join `locations` `l` on((`l`.`locID` = `e`.`locID`))) join `attendingevents` `a` on((`a`.`eID` = `e`.`eID`))) ;

-- --------------------------------------------------------

--
-- Structure for view `availeventslocs`
--
DROP TABLE IF EXISTS `availeventslocs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `availeventslocs`  AS  select `ae`.`eID` AS `eID`,`ae`.`sID` AS `sID`,`ae`.`name` AS `name`,`ae`.`eventType` AS `eventType`,`l`.`latitude` AS `latitude`,`l`.`longitude` AS `longitude` from (`alleventsbystudent` `ae` join `locations` `l` on((`l`.`locID` = `ae`.`locID`))) ;

-- --------------------------------------------------------

--
-- Structure for view `eventswithlocations`
--
DROP TABLE IF EXISTS `eventswithlocations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `eventswithlocations`  AS  select `evt`.`eID` AS `eID`,`evt`.`name` AS `name`,`evt`.`description` AS `description`,`evt`.`category` AS `category`,`evt`.`startTime` AS `startTime`,`evt`.`endTime` AS `endTime`,`evt`.`eventType` AS `eventType`,`evt`.`approved` AS `approved`,`evt`.`manager` AS `manager`,`evt`.`locID` AS `locID`,`evt`.`rsoEvent` AS `rsoEvent`,`evt`.`privUniversity` AS `privUniversity`,`evt`.`externID` AS `externID`,`evt`.`externSource` AS `externSource`,`l`.`name` AS `locName`,`l`.`description` AS `locDescription`,`l`.`latitude` AS `latitude`,`l`.`longitude` AS `longitude` from (`events` `evt` join `locations` `l` on((`evt`.`locID` = `l`.`locID`))) ;

-- --------------------------------------------------------

--
-- Structure for view `privateeventsbystudent`
--
DROP TABLE IF EXISTS `privateeventsbystudent`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `privateeventsbystudent`  AS  select `s`.`sID` AS `sID`,`evt`.`eID` AS `eID`,`evt`.`name` AS `name`,`evt`.`description` AS `description`,`evt`.`category` AS `category`,`evt`.`startTime` AS `startTime`,`evt`.`endTime` AS `endTime`,`evt`.`eventType` AS `eventType`,`evt`.`approved` AS `approved`,`evt`.`manager` AS `manager`,`evt`.`locID` AS `locID`,`evt`.`rsoEvent` AS `rsoEvent`,`evt`.`privUniversity` AS `privUniversity`,`evt`.`externID` AS `externID`,`evt`.`externSource` AS `externSource` from ((`students` `s` join `enrollments` `e` on((`s`.`sID` = `e`.`sID`))) join `events` `evt` on((`e`.`uID` = `evt`.`privUniversity`))) ;

-- --------------------------------------------------------

--
-- Structure for view `rsoeventsbystudent`
--
DROP TABLE IF EXISTS `rsoeventsbystudent`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `rsoeventsbystudent`  AS  select `s`.`sID` AS `sID`,`evt`.`eID` AS `eID`,`evt`.`name` AS `name`,`evt`.`description` AS `description`,`evt`.`category` AS `category`,`evt`.`startTime` AS `startTime`,`evt`.`endTime` AS `endTime`,`evt`.`eventType` AS `eventType`,`evt`.`approved` AS `approved`,`evt`.`manager` AS `manager`,`evt`.`locID` AS `locID`,`evt`.`rsoEvent` AS `rsoEvent`,`evt`.`privUniversity` AS `privUniversity`,`evt`.`externID` AS `externID`,`evt`.`externSource` AS `externSource` from ((`students` `s` join `rsomemberships` `r` on((`s`.`sID` = `r`.`sID`))) join `events` `evt` on((`r`.`rID` = `evt`.`rsoEvent`))) ;

-- --------------------------------------------------------

--
-- Structure for view `rsosanduniversity`
--
DROP TABLE IF EXISTS `rsosanduniversity`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `rsosanduniversity`  AS  select `r`.`rID` AS `rID`,`r`.`name` AS `name`,`r`.`description` AS `description`,`r`.`belongsTo` AS `belongsTo`,`r`.`studentAdmin` AS `studentAdmin`,`r`.`rsoManager` AS `rsoManager`,`u`.`name` AS `universityName` from (`rsos` `r` join `universities` `u` on((`u`.`uID` = `r`.`belongsTo`))) ;

-- --------------------------------------------------------

--
-- Structure for view `rsosuniversityandmembers`
--
DROP TABLE IF EXISTS `rsosuniversityandmembers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `rsosuniversityandmembers`  AS  select `r`.`rID` AS `rID`,`r`.`name` AS `name`,`r`.`description` AS `description`,`r`.`belongsTo` AS `belongsTo`,`r`.`studentAdmin` AS `studentAdmin`,`r`.`rsoManager` AS `rsoManager`,`u`.`name` AS `universityName`,`m`.`sID` AS `sID` from ((`rsos` `r` join `universities` `u` on((`u`.`uID` = `r`.`belongsTo`))) join `rsomemberships` `m` on((`m`.`rID` = `r`.`rID`))) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`sID`);

--
-- Indexes for table `attendingevents`
--
ALTER TABLE `attendingevents`
  ADD PRIMARY KEY (`sID`,`eID`),
  ADD KEY `AttdEvent_eID_idx` (`eID`),
  ADD KEY `AttdEvent_sID_idx` (`sID`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`eID`,`sID`,`dateTime`),
  ADD KEY `Cmnt_sID_idx` (`sID`),
  ADD KEY `Cmnt_eID_idx` (`eID`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`uID`,`sID`),
  ADD KEY `Enroll_sID_idx` (`sID`),
  ADD KEY `Enroll_uID_idx` (`uID`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eID`),
  ADD KEY `Events_locID_idx` (`locID`),
  ADD KEY `Events_sID_idx` (`manager`),
  ADD KEY `Events_rID_idx` (`rsoEvent`),
  ADD KEY `Events_uID_idx` (`privUniversity`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`locID`);

--
-- Indexes for table `rsomemberships`
--
ALTER TABLE `rsomemberships`
  ADD PRIMARY KEY (`rID`,`sID`),
  ADD KEY `RSOMemb_rID_idx` (`rID`),
  ADD KEY `RSOMemb_sID_idx` (`sID`);

--
-- Indexes for table `rsos`
--
ALTER TABLE `rsos`
  ADD PRIMARY KEY (`rID`),
  ADD KEY `RSOs_uID_idx` (`belongsTo`),
  ADD KEY `RSOs_admin_sID_idx` (`rsoManager`),
  ADD KEY `RSOs_sID_idx` (`studentAdmin`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`sID`),
  ADD UNIQUE KEY `Stud_email_UNIQUE` (`email`);

--
-- Indexes for table `universities`
--
ALTER TABLE `universities`
  ADD PRIMARY KEY (`uID`),
  ADD KEY `Univ_sID_idx` (`univManager`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `locID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `rsos`
--
ALTER TABLE `rsos`
  MODIFY `rID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `sID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12345703;
--
-- AUTO_INCREMENT for table `universities`
--
ALTER TABLE `universities`
  MODIFY `uID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `Admins_sID` FOREIGN KEY (`sID`) REFERENCES `students` (`sID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `attendingevents`
--
ALTER TABLE `attendingevents`
  ADD CONSTRAINT `AttdEvent_eID` FOREIGN KEY (`eID`) REFERENCES `events` (`eID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `AttdEvent_sID` FOREIGN KEY (`sID`) REFERENCES `students` (`sID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `Cmnt_eID` FOREIGN KEY (`eID`) REFERENCES `events` (`eID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Cmnt_sID` FOREIGN KEY (`sID`) REFERENCES `students` (`sID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `Enroll_sID` FOREIGN KEY (`sID`) REFERENCES `students` (`sID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Enroll_uID` FOREIGN KEY (`uID`) REFERENCES `universities` (`uID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `Events_locID` FOREIGN KEY (`locID`) REFERENCES `locations` (`locID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Events_rID` FOREIGN KEY (`rsoEvent`) REFERENCES `rsos` (`rID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Events_sID` FOREIGN KEY (`manager`) REFERENCES `admins` (`sID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Events_uID` FOREIGN KEY (`privUniversity`) REFERENCES `universities` (`uID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `rsomemberships`
--
ALTER TABLE `rsomemberships`
  ADD CONSTRAINT `RSOMemb_rID` FOREIGN KEY (`rID`) REFERENCES `rsos` (`rID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `RSOMemb_sID` FOREIGN KEY (`sID`) REFERENCES `students` (`sID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `rsos`
--
ALTER TABLE `rsos`
  ADD CONSTRAINT `RSOs_admin_sID` FOREIGN KEY (`rsoManager`) REFERENCES `admins` (`sID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `RSOs_sID` FOREIGN KEY (`studentAdmin`) REFERENCES `students` (`sID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `RSOs_uID` FOREIGN KEY (`belongsTo`) REFERENCES `universities` (`uID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `universities`
--
ALTER TABLE `universities`
  ADD CONSTRAINT `Univ_sID` FOREIGN KEY (`univManager`) REFERENCES `admins` (`sID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
