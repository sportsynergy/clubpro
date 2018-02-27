-- phpMy SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jan 10, 2012 at 10:20 PM
-- Server version: 5.1.54
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";



/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- --------------------------------------------------------

CREATE TABLE `tblPreferencesOverride` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `preference` VARCHAR(45) NOT NULL COMMENT 'the column name from the tblClubSites ',
  `parameteroptionid` VARCHAR(45) NOT NULL COMMENT 'The parameter value that applies this override' ,
  `override` VARCHAR(45) NULL COMMENT 'This is what will be overridden',
  PRIMARY KEY (`id`));

--
-- Table structure for table `tblMatchScore`
--

CREATE TABLE IF NOT EXISTS `tblMatchScore` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `courttypeid` INT NOT NULL,
  `gameswon` INT NOT NULL,
  `gameslost` INT NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('2', '3', '0');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('2', '3', '1');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('2', '3', '2');

INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('3', '3', '0');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('3', '3', '1');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('3', '3', '2');

INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('4', '3', '0');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('4', '3', '1');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('4', '3', '2');

INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('5', '3', '0');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('5', '3', '1');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('5', '3', '2');

INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('6', '3', '0');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('6', '3', '1');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('6', '3', '2');

INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('7', '7', '6');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('7', '7', '5');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('7', '6', '4');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('7', '6', '3');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('7', '6', '2');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('7', '6', '1');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('7', '6', '0');

INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('8', '3', '0');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('8', '3', '1');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('8', '3', '2');

INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('9', '7', '6');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('9', '7', '5');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('9', '6', '4');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('9', '6', '3');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('9', '6', '2');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('9', '6', '1');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('9', '6', '0');

INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('10', '3', '0');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('10', '3', '1');
INSERT INTO `tblMatchScore` (`courttypeid`, `gameswon`, `gameslost`) VALUES ('10', '3', '2');

-- --------------------------------------------------------

--
-- Table structure for table `tblBoxHistory`
--

CREATE TABLE IF NOT EXISTS `tblBoxHistory` (
  `boxid` int(11) NOT NULL DEFAULT '0',
  `reservationid` int(11) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `tblBoxLeagues`
--

CREATE TABLE IF NOT EXISTS `tblBoxLeagues` (
  `boxid` int(11) NOT NULL AUTO_INCREMENT,
  `boxname` text NOT NULL,
  `siteid` int(11) NOT NULL DEFAULT '0',
  `boxrank` smallint(6) NOT NULL DEFAULT '0',
  `courttypeid` int(11) NOT NULL DEFAULT '0',
  `enddate` date NOT NULL DEFAULT '0000-00-00',
  `enddatestamp` int(11) NOT NULL DEFAULT '0',
  `enable` int(11) NOT NULL DEFAULT '1',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`boxid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;



-- --------------------------------------------------------

--
-- Table structure for table `tblBuddies`
--

CREATE TABLE IF NOT EXISTS `tblBuddies` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT '0',
  `buddyid` int(11) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bid`),
  KEY `buddyid` (`buddyid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;


-- --------------------------------------------------------

--
-- Table structure for table `tblChallengeMatch`
--

CREATE TABLE IF NOT EXISTS `tblChallengeMatch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `challengerid` int(11) NOT NULL,
  `challengeeid` int(11) NOT NULL,
  `courttypeid` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `enddate` timestamp NULL DEFAULT NULL,
  `score` float DEFAULT NULL COMMENT 'the loser score, null is not scored',
  `siteid` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `siteid` (`siteid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1  ;


-- --------------------------------------------------------

--
-- Table structure for table `tblClubEventParticipants`
--

CREATE TABLE IF NOT EXISTS `tblClubEventParticipants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clubeventid` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `enddate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='A place to keep track of people that sign up for events' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `tblClubEvents`
--

CREATE TABLE IF NOT EXISTS `tblClubEvents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL DEFAULT '',
  `clubid` int(11) NOT NULL DEFAULT '0',
  `eventdate` date NOT NULL DEFAULT '0000-00-00',
  `description` text NOT NULL,
  `enddate` timestamp NULL DEFAULT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creator` int(11) NOT NULL DEFAULT '0',
  `lastmodifier` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `clubid` (`clubid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblClubLadder`
--

CREATE TABLE IF NOT EXISTS `tblClubLadder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT '0',
  `courttypeid` int(11) NOT NULL DEFAULT '0',
  `ladderposition` int(11) NOT NULL DEFAULT '0',
  `clubid` int(11) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `enddate` timestamp NULL DEFAULT NULL,
  `going` enum('steady','down','up') NOT NULL DEFAULT 'steady',
  `locked` enum('y','n') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;


-- --------------------------------------------------------

--
-- Table structure for table `tblClubSiteLadders`
--

CREATE TABLE IF NOT EXISTS `tblClubSiteLadders` (
  `id` int(11) NOT NULL,
  `siteid` int(11) NOT NULL,
  `courttypeid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `enddate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `clubid` (`siteid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblClubSiteLadders`
--

INSERT INTO `tblClubSiteLadders` (`id`, `siteid`, `courttypeid`, `name`, `enddate`) VALUES
(1, 10, 2, 'Singles Ladder', NULL),
(2, 10, 3, 'Doubles Ladder', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblClubSites`
--

CREATE TABLE IF NOT EXISTS `tblClubSites` (
  `siteid` mediumint(9) NOT NULL AUTO_INCREMENT,
  `clubid` mediumint(9) NOT NULL DEFAULT '0',
  `sitename` text NOT NULL,
  `sitecode` text NOT NULL,
  `allowselfcancel` enum('y','n','2') NOT NULL DEFAULT 'y',
  `enableautologin` enum('y','n') NOT NULL DEFAULT 'n',
  `rankingadjustment` int(11) NOT NULL DEFAULT '0',
  `allowsoloreservations` enum('y','n') NOT NULL DEFAULT 'y',
  `allowselfscore` enum('y','n') NOT NULL DEFAULT 'y',
  `daysahead` tinyint(4) NOT NULL DEFAULT '0',
  `displaytime` time DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `isliteversion` enum('y','n') NOT NULL DEFAULT 'n',
  `enable` enum('y','n') NOT NULL DEFAULT 'y',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `allowallsiteadvertising` enum('y','n') NOT NULL DEFAULT 'n',
  `enableguestreservation` enum('y','n') NOT NULL DEFAULT 'n',
  `displaysitenavigation` enum('y','n') NOT NULL DEFAULT 'y',
  `displayrecentactivity` enum('y','n') NOT NULL DEFAULT 'y',
  `allownearrankingadvertising` enum('y','n') NOT NULL DEFAULT 'y',
  `rankingscheme` enum('point','ladder') NOT NULL DEFAULT 'point',
  `challengerange` tinyint(4) DEFAULT '2',
  `facebookurl` varchar(255) DEFAULT NULL,
  `twitterurl` varchar(255) DEFAULT NULL,
  `reminders` ENUM(  'none',  '24',  '5',  '6',  '7',  '8',  '9',  '10' ) NOT NULL DEFAULT  'none',
  `displaycourttype` ENUM(  'y',  'n' ) NOT NULL DEFAULT  'y',
  `showplayernames` ENUM('y','n') NOT NULL DEFAULT 'y' COMMENT 'Display the player names on the main reservation page',
  `requirelogin` ENUM('y','n') NOT NULL DEFAULT 'n' COMMENT 'require login before accessing main booking page'
  PRIMARY KEY (`siteid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

--
-- Dumping data for table `tblClubSites`
--

INSERT INTO `tblClubSites` (`siteid`, `clubid`, `sitename`, `sitecode`, `allowselfcancel`, `enableautologin`, `rankingadjustment`, `allowsoloreservations`, `allowselfscore`, `daysahead`, `displaytime`, `password`, `isliteversion`, `enable`, `lastmodified`, `allowallsiteadvertising`, `enableguestreservation`, `displaysitenavigation`, `displayrecentactivity`, `allownearrankingadvertising`, `rankingscheme`, `challengerange`, `facebookurl`) VALUES
(10, 13, 'Demo Club Site', 'DemoClub', 'y', 'n', 0, 'y', 'y', 3, NULL, NULL, 'n', 'y', '2011-02-01 05:56:56', 'y', 'n', 'y', 'y', 'y', 'ladder', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblClubUser`
--

CREATE TABLE IF NOT EXISTS `tblClubUser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT '0',
  `clubid` int(11) NOT NULL DEFAULT '0',
  `msince` text,
  `roleid` tinyint(4) NOT NULL DEFAULT '0',
  `recemail` enum('y','n') NOT NULL DEFAULT 'y',
  `enable` enum('y','n') NOT NULL DEFAULT 'y',
  `memberid` varchar(255) DEFAULT NULL,
  `lastlogin` int(11) DEFAULT NULL,
  `lastmodified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `enddate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

--
-- Dumping data for table `tblClubUser`
--

INSERT INTO `tblClubUser` ( `userid`, `clubid`, `msince`, `roleid`, `recemail`, `enable`, `memberid`, `lastlogin`, `lastmodified`, `enddate`) VALUES
(1, 13, '14-Jan-00', 2, 'y', 'y', '', NULL, '2011-12-17 15:31:36', NULL),
(2, 0, '14-Jan-00', 3, 'y', 'y', '', NULL, NULL, NULL);


-- --------------------------------------------------------

--
-- Table structure for table `tblClubs`
--

CREATE TABLE IF NOT EXISTS `tblClubs` (
  `clubid` int(8) NOT NULL DEFAULT '0',
  `clubname` text NOT NULL,
  `clubaddress` text NOT NULL,
  `contactid` int(11) NOT NULL DEFAULT '0',
  `clubphone` text NOT NULL,
  `timezone` tinyint(4) NOT NULL DEFAULT '0',
  `rankdev` float NOT NULL DEFAULT '0',
  `enable` int(11) NOT NULL DEFAULT '1',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`clubid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblClubs`
--

INSERT INTO `tblClubs` (`clubid`, `clubname`, `clubaddress`, `contactid`, `clubphone`, `timezone`, `rankdev`, `enable`, `lastmodified`) VALUES
(13, 'Demo Club', 'One Racquet Lane Monroeville, SD', 0, '333-444-2222', -6, 0.5, 1, '0000-00-00 00:00:00'),
(0, 'System', '', 0, '', -6, 0, 1, '2008-03-03 21:23:09');

-- --------------------------------------------------------

--
-- Table structure for table `tblCourtEventParticipants`
--

CREATE TABLE IF NOT EXISTS `tblCourtEventParticipants` (
  `reservationid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `lastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `enddate` timestamp NULL DEFAULT NULL,
  KEY `reservationid` (`reservationid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `tblCourtGrouping`
--

CREATE TABLE IF NOT EXISTS `tblCourtGrouping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;


-- --------------------------------------------------------

--
-- Table structure for table `tblCourtGroupingEntry`
--

CREATE TABLE IF NOT EXISTS `tblCourtGroupingEntry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courtid` int(11) NOT NULL DEFAULT '0',
  `groupingid` int(11) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;



-- --------------------------------------------------------

--
-- Table structure for table `tblCourtHours`
--

CREATE TABLE IF NOT EXISTS `tblCourtHours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dayid` tinyint(4) NOT NULL DEFAULT '0',
  `courtid` int(11) NOT NULL DEFAULT '0',
  `opentime` time NOT NULL DEFAULT '00:00:00',
  `closetime` time NOT NULL DEFAULT '00:00:00',
  `hourstart` tinyint(4) NOT NULL DEFAULT '0',
  `duration` double NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

--
-- Dumping data for table `tblCourtHours`
--

INSERT INTO `tblCourtHours` (`id`, `dayid`, `courtid`, `opentime`, `closetime`, `hourstart`, `duration`, `lastmodified`) VALUES
(1, 0, 1, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(2, 1, 1, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(3, 2, 1, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(4, 3, 1, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(5, 4, 1, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(6, 5, 1, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(7, 6, 1, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(8, 0, 2, '06:00:00', '23:00:00', 15, 1, '2007-08-11 22:36:14'),
(9, 1, 2, '06:00:00', '23:00:00', 15, 1, '2007-08-11 22:36:14'),
(10, 2, 2, '06:00:00', '23:00:00', 15, 1, '2007-08-11 22:36:14'),
(11, 3, 2, '06:00:00', '23:00:00', 15, 1, '2007-08-11 22:36:14'),
(12, 4, 2, '06:00:00', '23:00:00', 15, 1, '2007-08-11 22:36:14'),
(13, 5, 2, '06:00:00', '23:00:00', 15, 1, '2007-08-11 22:36:14'),
(14, 6, 2, '06:00:00', '23:00:00', 15, 1, '2007-08-11 22:36:14'),
(15, 0, 3, '06:00:00', '23:00:00', 30, 1, '2007-08-11 22:36:14'),
(16, 1, 3, '06:00:00', '23:00:00', 30, 1, '2007-08-11 22:36:14'),
(17, 2, 3, '06:00:00', '23:00:00', 30, 1, '2007-08-11 22:36:14'),
(18, 3, 3, '06:00:00', '23:00:00', 30, 1, '2007-08-11 22:36:14'),
(19, 4, 3, '06:00:00', '23:00:00', 30, 1, '2007-08-11 22:36:14'),
(20, 5, 3, '06:00:00', '23:00:00', 30, 1, '2007-08-11 22:36:14'),
(21, 6, 3, '06:00:00', '23:00:00', 30, 1, '2007-08-11 22:36:14'),
(22, 0, 4, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(23, 1, 4, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(24, 2, 4, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(25, 3, 4, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(26, 4, 4, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(27, 5, 4, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14'),
(28, 6, 4, '06:00:00', '23:00:00', 0, 1, '2007-08-11 22:36:14');


-- --------------------------------------------------------

--
-- Table structure for table `tblCourtType`
--

CREATE TABLE IF NOT EXISTS `tblCourtType` (
  `courttypeid` int(11) NOT NULL AUTO_INCREMENT,
  `sportid` int(8) NOT NULL DEFAULT '0',
  `courttypename` text NOT NULL,
  `reservationtype` int(11) NOT NULL DEFAULT '0',
  `enable` tinyint(4) NOT NULL DEFAULT '1',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`courttypeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `tblCourtType`
--

INSERT INTO `tblCourtType` (`courttypeid`, `sportid`, `courttypename`, `reservationtype`, `enable`, `lastmodified`) VALUES
(2, 4, 'International Squash'),
(3, 4, 'Hardball Squash', 2, 1),
(4, 4, 'North American Squash', 0, 1),
(5, 4, 'North American Singles-Multi', 0, 1),
(6, 6, 'Racquetball \\ Squash', 1, 1),
(7, 5, 'Tennis', 1, 1),
(8, 7, 'Massage Therapy', 0, 1),
(11, 11, 'Stationary Bike', 3, 1),
(14, 11, 'Golf Putting/Chipping', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblCourts`
--


CREATE TABLE IF NOT EXISTS `tblCourts` (
  `courtid` int(8) NOT NULL AUTO_INCREMENT,
  `courttypeid` int(8) NOT NULL DEFAULT '0',
  `clubid` int(8) NOT NULL DEFAULT '0',
  `courtname` text NOT NULL,
  `enable` tinyint(4) NOT NULL DEFAULT '1',
  `siteid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `displayorder` smallint(6) NOT NULL DEFAULT '0',
  `variableduration` ENUM( 'n', 'y' ) NOT NULL DEFAULT 'n',
  `variableduration_admin` ENUM( 'n', 'y' ) NOT NULL DEFAULT 'n',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`courtid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

--
-- Dumping data for table `tblCourts`
--

INSERT INTO `tblCourts` (`courtid`, `courttypeid`, `clubid`, `courtname`, `enable`, `siteid`, `displayorder`, `lastmodified`) VALUES
(1, 2, 13, 'Court 1', 1, 10, 1, now() ),
(2, 2, 13, 'Court 2', 1, 10, 2, now() ),
(3, 3, 13, 'Court 3', 1, 10, 3, now() ),
(4, 3, 13, 'Court 4', 1, 10, 4, now() );


-- --------------------------------------------------------

--
-- Table structure for table `tblDays`
--

CREATE TABLE IF NOT EXISTS `tblDays` (
  `dayid` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`dayid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblDays`
--

INSERT INTO `tblDays` (`dayid`, `name`) VALUES
(0, 'Sunday'),
(1, 'Monday'),
(2, 'Tuesday'),
(3, 'Wednesday'),
(4, 'Thursday'),
(5, 'Friday'),
(6, 'Saturday');

-- --------------------------------------------------------

--
-- Table structure for table `tblEvents`
--

CREATE TABLE IF NOT EXISTS `tblEvents` (
  `eventid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `eventname` text NOT NULL,
  `siteid` mediumint(9) NOT NULL DEFAULT '0',
  `playerlimit` tinyint(4) NOT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`eventid`),
  KEY `siteid` (`siteid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;



-- --------------------------------------------------------

--
-- Table structure for table `tblFooterMessage`
--

CREATE TABLE IF NOT EXISTS `tblFooterMessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(255) NOT NULL DEFAULT '',
  `enddate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;


-- --------------------------------------------------------

--
-- Table structure for table `tblHoursException`
--

CREATE TABLE IF NOT EXISTS `tblHoursException` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` time NOT NULL DEFAULT '00:00:00',
  `siteid` mediumint(9) NOT NULL DEFAULT '0',
  `courtid` int(11) NOT NULL DEFAULT '0',
  `duration` double NOT NULL DEFAULT '0',
  `dayid` tinyint(4) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;



-- --------------------------------------------------------

--
-- Table structure for table `tblHoursPolicy`
--

CREATE TABLE IF NOT EXISTS `tblHoursPolicy` (
  `policyid` int(8) NOT NULL AUTO_INCREMENT,
  `siteid` int(8) NOT NULL DEFAULT '0',
  `day` tinyint(4) NOT NULL DEFAULT '0',
  `month` tinyint(4) NOT NULL DEFAULT '0',
  `year` int(8) NOT NULL DEFAULT '0',
  `opentime` time NOT NULL DEFAULT '00:00:00',
  `closetime` time NOT NULL DEFAULT '00:00:00',
  `enable` tinyint(4) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`policyid`),
  KEY `siteid` (`siteid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1  ;



-- --------------------------------------------------------

--
-- Table structure for table `tblMatchType`
--

CREATE TABLE IF NOT EXISTS `tblMatchType` (
  `id` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblMatchType`
--

INSERT INTO `tblMatchType` (`id`, `name`, `lastmodified`) VALUES
(0, 'practice', '2006-10-23 15:49:27'),
(1, 'league', '2006-10-23 15:49:27'),
(2, 'challenge', '2006-10-23 15:49:27'),
(3, 'buddy', '2006-10-23 15:49:27'),
(4, 'lesson', '2006-10-23 15:49:27'),
(5, 'solo', '2006-10-23 15:49:27');

-- --------------------------------------------------------

--
-- Table structure for table `tblMessageType`
--

CREATE TABLE IF NOT EXISTS `tblMessageType` (
  `messagetypeid` smallint(6) NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`messagetypeid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tblMessageType`
--

INSERT INTO `tblMessageType` (`messagetypeid`, `name`) VALUES
(1, 'scrolling message'),
(2, 'news message');

-- --------------------------------------------------------

--
-- Table structure for table `tblMessages`
--

CREATE TABLE IF NOT EXISTS `tblMessages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(8) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `messagetypeid` smallint(6) NOT NULL,
  `enable` tinyint(4) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;


-- --------------------------------------------------------

--
-- Table structure for table `tblParameter`
--

CREATE TABLE IF NOT EXISTS `tblParameter` (
  `parameterid` smallint(6) NOT NULL DEFAULT '0',
  `parametertypeid` smallint(6) NOT NULL DEFAULT '0',
  `siteid` smallint(6) NOT NULL DEFAULT '0',
  `parameterlabel` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`parameterid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Table structure for table `tblParameterAccess`
--

CREATE TABLE IF NOT EXISTS `tblParameterAccess` (
  `parameteraccessid` int(11) NOT NULL DEFAULT '0',
  `parameteraccesstypeid` int(11) NOT NULL DEFAULT '0',
  `roleid` smallint(6) NOT NULL DEFAULT '0',
  `parameterid` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`parameteraccessid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `tblParameterAccessType`
--

CREATE TABLE IF NOT EXISTS `tblParameterAccessType` (
  `parameteraccesstypeid` int(11) NOT NULL DEFAULT '0',
  `parameteraccesstypename` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`parameteraccesstypeid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblParameterAccessType`
--

INSERT INTO `tblParameterAccessType` (`parameteraccesstypeid`, `parameteraccesstypename`) VALUES
(1, 'read'),
(2, 'write');

-- --------------------------------------------------------

--
-- Table structure for table `tblParameterOptions`
--

CREATE TABLE IF NOT EXISTS `tblParameterOptions` (
  `parameteroptionid` smallint(6) NOT NULL AUTO_INCREMENT,
  `parameterid` smallint(6) NOT NULL DEFAULT '0',
  `optionname` varchar(45) NOT NULL DEFAULT '',
  `optionvalue` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`parameteroptionid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;



-- --------------------------------------------------------

--
-- Table structure for table `tblParameterType`
--

CREATE TABLE IF NOT EXISTS `tblParameterType` (
  `parametertypeid` smallint(6) NOT NULL DEFAULT '0',
  `parametertypename` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`parametertypeid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblParameterType`
--

INSERT INTO `tblParameterType` (`parametertypeid`, `parametertypename`) VALUES
(1, 'text'),
(2, 'select');

-- --------------------------------------------------------

--
-- Table structure for table `tblParameterValue`
--

CREATE TABLE IF NOT EXISTS `tblParameterValue` (
  `userid` int(11) NOT NULL DEFAULT '0',
  `parameterid` smallint(6) NOT NULL DEFAULT '0',
  `parametervalue` varchar(45) NOT NULL DEFAULT '',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `enddate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`userid`,`parameterid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tblReoccuringEvents`
--

CREATE TABLE IF NOT EXISTS `tblReoccuringEvents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courtid` int(11) NOT NULL DEFAULT '0',
  `eventinterval` int(11) NOT NULL DEFAULT '0',
  `starttime` int(11) NOT NULL DEFAULT '0',
  `endtime` int(11) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `courtid` (`courtid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;



-- --------------------------------------------------------

--
-- Table structure for table `tblReoccurringBlockEvent`
--

CREATE TABLE IF NOT EXISTS `tblReoccurringBlockEvent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;


-- --------------------------------------------------------

--
-- Table structure for table `tblReoccurringBlockEventEntry`
--

CREATE TABLE IF NOT EXISTS `tblReoccurringBlockEventEntry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reoccuringblockeventid` int(11) NOT NULL DEFAULT '0',
  `reoccuringentryid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `reoccuringblockeventid` (`reoccuringblockeventid`,`reoccuringentryid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1  ;


-- --------------------------------------------------------

--
-- Table structure for table `tblReservationType`
--

CREATE TABLE IF NOT EXISTS `tblReservationType` (
  `reservationtypeid` tinyint(4) NOT NULL DEFAULT '0',
  `reservationtypename` text NOT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`reservationtypeid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblReservationType`
--

INSERT INTO `tblReservationType` (`reservationtypeid`, `reservationtypename`, `lastmodified`) VALUES
(0, 'Singles'),
(1, 'Multiple Type'),
(2, 'Doubles'),
(3, 'Resource');


-- --------------------------------------------------------

--
-- Table structure for table `tblReservations`
--

CREATE TABLE IF NOT EXISTS `tblReservations` (
  `reservationid` int(11) NOT NULL AUTO_INCREMENT,
  `courtid` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `usertype` tinyint(4) NOT NULL DEFAULT '0',
  `matchtype` tinyint(4) NOT NULL DEFAULT '0',
  `eventid` tinyint(4) NOT NULL DEFAULT '0',
  `guesttype` tinyint(4) NOT NULL DEFAULT '0',
  `creator` int(11) NOT NULL DEFAULT '0',
  `createdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastmodifier` int(11) NOT NULL DEFAULT '0',
  `enddate` timestamp NULL DEFAULT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `locked` enum('y','n') NOT NULL DEFAULT 'n',
  `duration` int(11) NULL,
  PRIMARY KEY (`reservationid`),
  KEY `courtid` (`courtid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;


-- --------------------------------------------------------

--
-- Table structure for table `tblRoles`
--

CREATE TABLE IF NOT EXISTS `tblRoles` (
  `roleid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `rolename` text NOT NULL,
  `roleaccesslevel` tinyint(4) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`roleid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblRoles`
--

INSERT INTO `tblRoles` (`roleid`, `rolename`, `roleaccesslevel`, `lastmodified`) VALUES
(1, 'Player', 1, '0000-00-00 00:00:00'),
(2, 'Club Administrator', 2, '0000-00-00 00:00:00'),
(3, 'System Administrator', 3, '0000-00-00 00:00:00'),
(4, 'Desk User', 4, '2009-02-17 13:55:11'),
(5, 'Limited Access Player', 5, '2009-02-17 13:55:11'),
(6, 'Junior', 6, '2009-02-17 13:55:11');

-- --------------------------------------------------------

--
-- Table structure for table `tblSchedulingPolicy`
--

CREATE TABLE IF NOT EXISTS `tblSchedulingPolicy` (
  `policyid` int(11) NOT NULL AUTO_INCREMENT,
  `policyname` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `schedulelimit` int(11) NOT NULL DEFAULT '0',
  `dayid` smallint(6) DEFAULT NULL,
  `courtid` int(11) DEFAULT NULL,
  `siteid` mediumint(9) DEFAULT NULL,
  `starttime` time DEFAULT NULL,
  `endtime` time DEFAULT NULL,
  `allowlooking` enum('y','n') NOT NULL DEFAULT 'y',
  `allowback2back` enum('n','y') NOT NULL DEFAULT 'y',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`policyid`),
  KEY `siteid` (`siteid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;


-- --------------------------------------------------------

--
-- Table structure for table `tblSiteActivity`
--

CREATE TABLE IF NOT EXISTS `tblSiteActivity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activitydate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `siteid` mediumint(9) DEFAULT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `enddate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siteid` (`siteid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1  ;



-- --------------------------------------------------------

--
-- Table structure for table `tblSkillRangePolicy`
--

CREATE TABLE IF NOT EXISTS `tblSkillRangePolicy` (
  `policyid` int(11) NOT NULL AUTO_INCREMENT,
  `policyname` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `skillrange` float NOT NULL DEFAULT '0',
  `dayid` smallint(6) DEFAULT NULL,
  `courtid` int(11) DEFAULT NULL,
  `siteid` mediumint(9) DEFAULT NULL,
  `starttime` time DEFAULT NULL,
  `endtime` time DEFAULT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`policyid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1  ;



-- --------------------------------------------------------

--
-- Table structure for table `tblSportType`
--

CREATE TABLE IF NOT EXISTS `tblSportType` (
  `sportid` int(11) NOT NULL AUTO_INCREMENT,
  `sportname` text NOT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sportid`),
  KEY `sportid` (`sportid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `tblSportType`
--

INSERT INTO `tblSportType` (`sportid`, `sportname`, `lastmodified`) VALUES
(4, 'Squash'),
(3, 'Badmitton'),
(5, 'Tennis'),
(6, 'Racquetball'),
(7, 'Massage');
(7, 'Equipment');

-- --------------------------------------------------------

--
-- Table structure for table `tblTeams`
--

CREATE TABLE IF NOT EXISTS `tblTeams` (
  `teamid` int(11) NOT NULL AUTO_INCREMENT,
  `courttypeid` int(11) NOT NULL DEFAULT '0',
  `enable` tinyint(4) NOT NULL DEFAULT '1',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`teamid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=195 ;



-- --------------------------------------------------------

--
-- Table structure for table `tblTimezones`
--

CREATE TABLE IF NOT EXISTS `tblTimezones` (
  `tzid` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `offset` tinyint(4) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tzid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;

--
-- Dumping data for table `tblTimezones`
--

INSERT INTO `tblTimezones` (`tzid`, `name`, `offset`, `lastmodified`) VALUES
(1, 'Central Time (US & Canada)', -6, '0000-00-00 00:00:00'),
(2, 'Mountain Time (US & Canada)', -7, '0000-00-00 00:00:00'),
(3, 'Pacific Time (US & Canada)', -8, '2009-11-17 02:06:37'),
(4, 'Eastern Time (US & Canada)', -5, '2009-11-17 02:06:37');

-- --------------------------------------------------------

--
-- Table structure for table `tblUserRankings`
--

CREATE TABLE IF NOT EXISTS `tblUserRankings` (
  `userid` int(8) NOT NULL DEFAULT '0',
  `courttypeid` int(11) NOT NULL DEFAULT '0',
  `ranking` float NOT NULL DEFAULT '0',
  `hot` tinyint(4) NOT NULL DEFAULT '0',
  `usertype` tinyint(4) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `tblUsers`
--

CREATE TABLE IF NOT EXISTS `tblUsers` (
  `userid` int(8) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `email` text NOT NULL,
  `workphone` text NOT NULL,
  `homephone` text NOT NULL,
  `cellphone` text NOT NULL,
  `pager` text NOT NULL,
  `password` text NOT NULL,
  `useraddress` text NOT NULL,
  `gender` tinyint(1) NOT NULL DEFAULT '1',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `enddate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

--
-- Dumping data for table `tblUsers`
--

INSERT INTO `tblUsers` ( `username`, `firstname`, `lastname`, `email`, `workphone`, `homephone`, `cellphone`, `pager`, `password`, `useraddress`, `gender`, `lastmodified`, `enddate`) VALUES
('demoboy', 'Demo', 'Boy', 'adam704a@gmail.com', '333-222-3333', '654-333-2222', '', '', '21686ee8b57c6263fb09a63ce4552058', '123 Fleet St.\r\nLondon, GB  ', 1, '2010-08-24 11:41:32', NULL),
('system', 'System', 'Administrator', 'adam704a@gmail.com', '333-222-3333', '654-333-2222', '', '', '21686ee8b57c6263fb09a63ce4552058', '123 Fleet St.\r\nLondon, GB  ', 1, '2010-08-24 11:41:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblkpBoxLeagues`
--

CREATE TABLE IF NOT EXISTS `tblkpBoxLeagues` (
  `boxid` int(11) NOT NULL ,
  `userid` int(11) NOT NULL ,
  `boxplace` int(11) NOT NULL ,
  `games` tinyint(4) NOT NULL ,
  `score` smallint(6) NOT NULL ,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Table structure for table `tblkpGuestReservations`
--

CREATE TABLE IF NOT EXISTS `tblkpGuestReservations` (
  `reservationid` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `id` (`reservationid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;



-- --------------------------------------------------------

--
-- Table structure for table `tblkpTeams`
--

CREATE TABLE IF NOT EXISTS `tblkpTeams` (
  `userid` int(11) NOT NULL DEFAULT '0',
  `teamid` int(11) NOT NULL DEFAULT '0',
  `enable` tinyint(4) NOT NULL DEFAULT '1',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `teamid` (`teamid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Table structure for table `tblkpUserReservations`
--

CREATE TABLE IF NOT EXISTS `tblkpUserReservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservationid` int(11) NOT NULL DEFAULT '0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `outcome` tinyint(4) NOT NULL DEFAULT '0',
  `usertype` tinyint(4) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reservationid` (`reservationid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;



-- --------------------------------------------------------

--
-- Table structure for table `tblkupSiteAuth`
--

CREATE TABLE IF NOT EXISTS `tblkupSiteAuth` (
  `userid` mediumint(9) NOT NULL DEFAULT '0',
  `siteid` mediumint(9) NOT NULL DEFAULT '0',
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

