-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 12 月 23 日 10:49
-- 服务器版本: 5.5.20
-- PHP 版本: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `ctest`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `aId` int(11) NOT NULL AUTO_INCREMENT,
  `alogName` varchar(12) NOT NULL,
  `aName` varchar(12) NOT NULL,
  `aEmail` varchar(30) NOT NULL,
  `aSex` tinyint(4) NOT NULL,
  `aAddr` varchar(50) NOT NULL,
  `aTel` bigint(20) NOT NULL,
  `aQQ` bigint(20) NOT NULL,
  `aPsw` varchar(128) NOT NULL,
  `aCookie` varchar(128) DEFAULT NULL,
  `aIp_Login` int(11) DEFAULT NULL,
  `aTime_Login` datetime DEFAULT NULL,
  `aTime_Reg` datetime DEFAULT NULL,
  PRIMARY KEY (`aId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;



-- --------------------------------------------------------

--
-- 表的结构 `admin_login_log`
--

CREATE TABLE IF NOT EXISTS `admin_login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aId` int(11) NOT NULL,
  `aIp_Login` int(11) NOT NULL,
  `aTime_Login` datetime DEFAULT NULL,
  `aFlag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aId` (`aId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `class`
--

CREATE TABLE IF NOT EXISTS `class` (
  `classId` int(11) NOT NULL AUTO_INCREMENT,
  `className` varchar(20) NOT NULL,
  `classAbbr` varchar(10) NOT NULL,
  `classColleage` varchar(20) NOT NULL,
  `classNote` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`classId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `course`
--

CREATE TABLE IF NOT EXISTS `course` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `cName` varchar(20) NOT NULL,
  `cNote` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `job`
--

CREATE TABLE IF NOT EXISTS `job` (
  `jId` int(11) NOT NULL AUTO_INCREMENT,
  `jChapNo` tinyint(4) NOT NULL DEFAULT '0',
  `jAssNo` tinyint(4) NOT NULL DEFAULT '0',
  `tId` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `jJob_Title` varchar(50) NOT NULL,
  `jURI_Prob` varchar(64) NOT NULL,
  `jDifficulty` tinyint(4) NOT NULL DEFAULT '5',
  `jIp_pub` varchar(39) DEFAULT NULL,
  `jTime_Start` datetime NOT NULL,
  `jTime_End` datetime NOT NULL,
  `jTime_Pub` datetime DEFAULT NULL,
  `jIsPub` tinyint(4) DEFAULT NULL,
  `jType` tinyint(4) NOT NULL DEFAULT '0',
  `jNote` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`jId`),
  KEY `jChapNo` (`jChapNo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `job_submit`
--

CREATE TABLE IF NOT EXISTS `job_submit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jId` int(11) NOT NULL,
  `sId` int(11) NOT NULL,
  `JURI_Response` varchar(64) NOT NULL,
  `JDesc` varchar(140) NOT NULL,
  `jScore` float DEFAULT NULL,
  `jResult` int(11) DEFAULT NULL,
  `jTime_Sub` datetime DEFAULT NULL,
  `jsNote` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`,`jId`,`sId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `student`
--

CREATE TABLE IF NOT EXISTS `student` (
  `sId` int(11) NOT NULL AUTO_INCREMENT,
  `sNo` varchar(11) NOT NULL,
  `sName` varchar(12) NOT NULL,
  `sClassId` int(11) NOT NULL,
  `sEmail` varchar(30) NOT NULL,
  `sSex` tinyint(4) NOT NULL,
  `sAddr` varchar(50) NOT NULL,
  `sTel` bigint(20) NOT NULL,
  `sQQ` bigint(20) NOT NULL,
  `sPsw` varchar(128) NOT NULL,
  `sCookie` varchar(128) DEFAULT NULL,
  `sIp_Login` int(11) DEFAULT NULL,
  `sTime_Login` datetime DEFAULT NULL,
  `sTime_Reg` datetime DEFAULT NULL,
  `sflag` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`sId`,`sNo`),
  KEY `sClassId` (`sClassId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `student_login_log`
--

CREATE TABLE IF NOT EXISTS `student_login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sNo` varchar(11) NOT NULL,
  `sName` varchar(12) NOT NULL,
  `sIp_Login` int(11) DEFAULT NULL,
  `sTime_Login` datetime DEFAULT NULL,
  `sflag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sNo` (`sNo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `teacher`
--

CREATE TABLE IF NOT EXISTS `teacher` (
  `tId` int(11) NOT NULL AUTO_INCREMENT,
  `tNo` varchar(11) NOT NULL,
  `tName` varchar(12) NOT NULL,
  `tColleage` varchar(20) NOT NULL,
  `tEmail` varchar(30) NOT NULL,
  `tSex` tinyint(4) NOT NULL,
  `tAddr` varchar(50) NOT NULL,
  `tTel` bigint(20) NOT NULL,
  `tQQ` bigint(20) NOT NULL,
  `tPsw` varchar(128) NOT NULL,
  `tCookie` varchar(128) DEFAULT NULL,
  `tIp_Login` int(11) DEFAULT NULL,
  `tTime_Login` datetime DEFAULT NULL,
  `tTime_Reg` datetime DEFAULT NULL,
  `tright` tinyint(4) NOT NULL DEFAULT '0',
  `tflag` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tId`,`tNo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `teacher_course`
--

CREATE TABLE IF NOT EXISTS `teacher_course` (
  `tcId` int(11) NOT NULL AUTO_INCREMENT,
  `tNo` varchar(11) NOT NULL,
  `cId` int(11) NOT NULL,
  `classId` int(11) NOT NULL,
  `term` varchar(25) DEFAULT NULL,
  `tcNote` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`tcId`),
  KEY `cId` (`cId`),
  KEY `classId` (`classId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `teacher_login_log`
--

CREATE TABLE IF NOT EXISTS `teacher_login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tNo` varchar(11) NOT NULL,
  `tName` varchar(12) NOT NULL,
  `tIp_Login` int(11) DEFAULT NULL,
  `tTime_Login` datetime DEFAULT NULL,
  `tflag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
