-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2020 at 06:46 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `staff_id` varchar(255) NOT NULL,
  `student_id` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `permission_required` tinyint(1) NOT NULL,
  `permission_list` text NOT NULL,
  `name` text NOT NULL,
  `attendance` text NOT NULL,
  `other_supervisors` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `staff_id`, `student_id`, `start_date`, `end_date`, `type`, `description`, `permission_required`, `permission_list`, `name`, `attendance`, `other_supervisors`) VALUES
(1, 'd7wLlX3D', '3cAPtDQw,52KjAKXH,5327RNFJ,c2naKQRm,CVPX4Dqh,e2OZmTia,TV3Gd4SV,XfskckQ0,ykAz0WGp,ynRjeXZj', '2020-05-28 09:00:00', '2020-05-28 14:20:00', 'Subject', 'Geography trip to the river Bann.', 1, '', '6th year geography trip', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `date` datetime NOT NULL,
  `staff_id` varchar(255) NOT NULL,
  `message` mediumtext NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `student_name` text NOT NULL,
  `student_dob` text NOT NULL,
  `primary_school` text NOT NULL,
  `parent_name` text NOT NULL,
  `parent_email` text NOT NULL,
  `parent_number` text NOT NULL,
  `parent_addr_1` text NOT NULL,
  `parent_addr_2` text NOT NULL,
  `parent_county` text NOT NULL,
  `second_parent_name` text NOT NULL,
  `second_parent_email` text NOT NULL,
  `second_parent_number` text NOT NULL,
  `second_parent_addr_1` text NOT NULL,
  `second_parent_addr_2` text NOT NULL,
  `second_parent_county` text NOT NULL,
  `processed_by` text NOT NULL,
  `status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `student_name`, `student_dob`, `primary_school`, `parent_name`, `parent_email`, `parent_number`, `parent_addr_1`, `parent_addr_2`, `parent_county`, `second_parent_name`, `second_parent_email`, `second_parent_number`, `second_parent_addr_1`, `second_parent_addr_2`, `second_parent_county`, `processed_by`, `status`) VALUES
(1, 'Jack Dunne', '2001-04-03', 'sample school', 'Molly Dunne', 'mollydunne@example.com', '4565581123', 'example road', 'sample town', 'Wexford', '', '', '', '', '', '', 'abcde123', 'accepted'),
(2, 'Kate Stone', '2001-08-06', 'example school', 'Tony Stone', 'tonystone@example.com', '7589216254', 'sample lane', 'example town', 'Leitrim', '', '', '', '', '', '', '', ''),
(3, 'Ciara Carter', '2001-12-05', 'sample school', 'Bill Carter', 'billcarter@example.com', '7432611258', 'example road', 'sample town', 'Wexford', '', '', '', '', '', '', 'abcde123', 'accepted'),
(4, 'Bob Ross', '2001-12-13', 'sample school', 'Lydia Ross', 'lydiaross@example.com', '1785542326', 'example road', 'sample town', 'Wexford', '', '', '', '', '', '', 'abcde123', 'accepted'),
(5, 'Carole Baskin', '2001-06-05', 'sample school', 'John Baskin', 'johnmaskin@example.com', '5957515352', 'example road', 'sample town', 'Wexford', '', '', '', '', '', '', 'abcde123', 'accepted'),
(6, 'Cian Murphy', '2001-04-07', 'sample school', 'Roisin Murphy', 'roisinmurphy@example.com', '1375985547', 'easy house', 'sample town', 'Wexford', '', '', '', '', '', '', 'abcde123', 'accepted'),
(7, 'Molly Hogan', '2001-06-06', 'sample school', 'Sarah Hogan', 'sarahhogan@example.com', '9874326465', 'example road', 'sample town', 'Wexford', '', '', '', '', '', '', 'abcde123', 'accepted'),
(8, 'Andrew Kehoe', '2001-04-06', 'sample school', 'Jake Kehoe', 'jakekehoe@example.com', '9731645521', 'easy house', 'sample town', 'Wexford', '', '', '', '', '', '', 'abcde123', 'accepted'),
(9, 'Julia Doran', '2001-05-06', 'example school', 'Pat Doran', 'patdoran@example.com', '4826351595', 'sample lane', 'example town', 'Leitrim', '', '', '', '', '', '', 'abcde123', 'accepted'),
(10, 'Teghan Doyle', '2001-05-09', 'example school', 'Brendon Doyle', 'brendandoyle@example.com', '4845147432', 'sample lane', 'example town', 'Leitrim', '', '', '', '', '', '', 'abcde123', 'accepted'),
(11, 'Tom Quinn', '2001-04-05', 'example school', 'Mary Quinn', 'maryquinn@example.com', '7952104766', 'sample lane', 'example town', 'Leitrim', '', '', '', '', '', '', 'abcde123', 'accepted'),
(12, 'Adam O\'Neill', '2001-07-05', 'example school', 'Sam O\'Neill', 'samoneill@example.com', '4713621220', 'sample drive', 'example town', 'Leitrim', '', '', '', '', '', '', 'abcde123', 'accepted'),
(13, 'Liam Williams', '2001-02-06', 'example school', 'Fiona Williams', 'fionawilliams@example.com', '6398887167', 'sample drive', 'example town', 'Leitrim', '', '', '', '', '', '', 'abcde123', 'accepted'),
(14, 'Owen Travers', '2001-12-08', 'sample school', 'Yvonne Travers', 'yvonnetravers@example.com', '3269741154', 'easy house', 'sample town', 'Wexford', '', '', '', '', '', '', 'abcde123', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `student_id` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `period1` tinytext NOT NULL,
  `period2` tinytext NOT NULL,
  `period3` tinytext NOT NULL,
  `period4` tinytext NOT NULL,
  `period5` tinytext NOT NULL,
  `period6` tinytext NOT NULL,
  `period7` tinytext NOT NULL,
  `period8` tinytext NOT NULL,
  `period9` tinytext NOT NULL,
  `reason` tinytext NOT NULL,
  `description` text NOT NULL,
  `missedPeriods` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`student_id`, `date`, `period1`, `period2`, `period3`, `period4`, `period5`, `period6`, `period7`, `period8`, `period9`, `reason`, `description`, `missedPeriods`) VALUES
('3cAPtDQw', '2020-02-19', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-02-26', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-03-04', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-03-11', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-03-18', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-03-25', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-04-01', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-04-08', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-04-15', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-04-22', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-04-29', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('3cAPtDQw', '2020-05-06', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-02-19', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-02-26', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-03-04', '', '', 'late-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-03-11', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-03-18', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-03-25', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-04-01', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-04-08', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-04-15', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-04-22', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-04-29', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('52KjAKXH', '2020-05-06', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-02-19', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-02-26', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-03-04', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-03-11', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-03-18', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-03-25', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-04-01', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-04-08', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-04-15', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-04-22', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-04-29', '', '', 'late-geo61', '', '', '', '', '', '', '', '', ''),
('5327RNFJ', '2020-05-06', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-02-19', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-02-26', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-03-04', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-03-11', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-03-18', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-03-25', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-04-01', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-04-08', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-04-15', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-04-22', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-04-29', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('c2naKQRm', '2020-05-06', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('CVPX4Dqh', '2020-02-19', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('CVPX4Dqh', '2020-02-26', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('CVPX4Dqh', '2020-03-04', '', '', 'absent-geo61', '', '', '', '', '', '', 'appointment', 'Ciara had a dental appointment at 10.', ''),
('CVPX4Dqh', '2020-03-11', '', '', 'absent-geo61', '', '', '', '', '', '', 'illness', 'Ciara was unwell the night before and the morning of Wednesday.', ''),
('CVPX4Dqh', '2020-03-18', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('CVPX4Dqh', '2020-03-25', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('CVPX4Dqh', '2020-04-01', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('CVPX4Dqh', '2020-04-08', '', '', 'absent-geo61', '', '', '', '', '', '', '', '', ''),
('CVPX4Dqh', '2020-04-15', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('CVPX4Dqh', '2020-04-22', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('CVPX4Dqh', '2020-04-29', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('CVPX4Dqh', '2020-05-06', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-02-19', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-02-26', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-03-04', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-03-11', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-03-18', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-03-25', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-04-01', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-04-08', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-04-15', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-04-22', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-04-29', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('e2OZmTia', '2020-05-06', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-02-19', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-02-26', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-03-04', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-03-11', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-03-18', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-03-25', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-04-01', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-04-08', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-04-15', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-04-22', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-04-29', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('TV3Gd4SV', '2020-05-06', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-02-19', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-02-26', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-03-04', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-03-11', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-03-18', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-03-25', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-04-01', '', '', 'absent-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-04-08', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-04-15', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-04-22', '', '', 'absent-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-04-29', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('XfskckQ0', '2020-05-06', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-02-19', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-02-26', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-03-04', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-03-11', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-03-18', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-03-25', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-04-01', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-04-08', '', '', 'late-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-04-15', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-04-22', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-04-29', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ykAz0WGp', '2020-05-06', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-02-19', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-02-26', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-03-04', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-03-11', '', '', 'late-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-03-18', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-03-25', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-04-01', 'aef', '', 'absent-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-04-08', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-04-15', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-04-22', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-04-29', '', '', 'present-geo61', '', '', '', '', '', '', '', '', ''),
('ynRjeXZj', '2020-05-06', '', '', 'present-geo61', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `classactivities`
--

CREATE TABLE `classactivities` (
  `staff_id` varchar(255) NOT NULL,
  `subjectCode` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `marked` tinyint(1) NOT NULL,
  `testType` text NOT NULL,
  `schoolTestType` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `classactivities`
--

INSERT INTO `classactivities` (`staff_id`, `subjectCode`, `date`, `description`, `marked`, `testType`, `schoolTestType`) VALUES
('fre61', 'fre61', '2020-02-10', 'Vocab test on mots cles', 1, 'Class test', ''),
('fre61', 'fre61', '2020-04-06', 'Verb test', 1, 'Class test', ''),
('fre61', 'fre61', '2020-05-11', 'Vocab test on fashion', 1, 'Class test', ''),
('geo61', 'geo61', '2018-12-02', 'test on chapters 1-12', 1, 'School test', 'Christmas Exam'),
('geo61', 'geo61', '2019-12-04', 'test on entire course covered so far', 1, 'School test', 'Christmas Exam'),
('geo61', 'geo61', '2020-04-06', 'the rock cycle', 1, 'Class test', ''),
('geo61', 'geo61', '2020-04-23', 'rainforest biome', 1, 'Class test', '');

-- --------------------------------------------------------

--
-- Table structure for table `detention`
--

CREATE TABLE `detention` (
  `student_id` varchar(255) NOT NULL,
  `staff_id` varchar(255) NOT NULL,
  `detention_type` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `assigned_at` datetime NOT NULL,
  `reason` text NOT NULL,
  `status` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `detention`
--

INSERT INTO `detention` (`student_id`, `staff_id`, `detention_type`, `date`, `assigned_at`, `reason`, `status`) VALUES
('CVPX4Dqh', 'd7wLlX3D', 'Lunch Time', '2020-05-15', '2020-05-14 00:00:00', 'Geography homework was not attempted', '');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `student_id` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `subjectCode` varchar(255) NOT NULL,
  `mark` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`student_id`, `date`, `subjectCode`, `mark`, `type`, `reason`) VALUES
('3cAPtDQw', '2019-12-04', 'geo61', 42, 0, ''),
('3cAPtDQw', '2020-02-10', 'fre61', 42, 0, ''),
('3cAPtDQw', '2020-04-06', 'fre61', 47, 0, ''),
('3cAPtDQw', '2020-04-06', 'geo61', 34, 0, ''),
('3cAPtDQw', '2020-04-23', 'geo61', 40, 0, ''),
('3cAPtDQw', '2020-05-11', 'fre61', 66, 0, ''),
('52KjAKXH', '2019-12-04', 'geo61', 48, 0, ''),
('52KjAKXH', '2020-04-06', 'geo61', 41, 0, ''),
('52KjAKXH', '2020-04-23', 'geo61', 50, 0, ''),
('5327RNFJ', '2019-12-04', 'geo61', 55, 0, ''),
('5327RNFJ', '2020-02-10', 'fre61', 51, 0, ''),
('5327RNFJ', '2020-04-06', 'fre61', 55, 0, ''),
('5327RNFJ', '2020-04-06', 'geo61', 49, 0, ''),
('5327RNFJ', '2020-04-23', 'geo61', 60, 0, ''),
('5327RNFJ', '2020-05-11', 'fre61', 55, 0, ''),
('c2naKQRm', '2019-12-04', 'geo61', 64, 0, ''),
('c2naKQRm', '2020-02-10', 'fre61', 66, 0, ''),
('c2naKQRm', '2020-04-06', 'fre61', 61, 0, ''),
('c2naKQRm', '2020-04-06', 'geo61', 55, 0, ''),
('c2naKQRm', '2020-04-23', 'geo61', 65, 0, ''),
('c2naKQRm', '2020-05-11', 'fre61', 71, 0, ''),
('CVPX4Dqh', '2019-12-04', 'geo61', 68, 0, ''),
('CVPX4Dqh', '2020-02-10', 'fre61', 74, 0, ''),
('CVPX4Dqh', '2020-04-06', 'fre61', 68, 0, ''),
('CVPX4Dqh', '2020-04-06', 'geo61', 63, 0, ''),
('CVPX4Dqh', '2020-04-23', 'geo61', 68, 0, 'grade changed from 65. incorrectly recorded'),
('CVPX4Dqh', '2020-05-11', 'fre61', 73, 0, ''),
('e2OZmTia', '2019-12-04', 'geo61', 72, 0, ''),
('e2OZmTia', '2020-04-06', 'geo61', 68, 0, ''),
('e2OZmTia', '2020-04-23', 'geo61', 70, 0, ''),
('pastrand1', '2018-12-02', 'geo61', 43, 0, ''),
('pastrand1', '2019-02-05', 'geo61', 73, 0, ''),
('pastrand11', '2018-12-02', 'geo61', 72, 0, ''),
('pastrand11', '2019-02-05', 'geo61', 67, 0, ''),
('pastrand2', '2018-12-02', 'geo61', 53, 0, ''),
('pastrand2', '2019-02-05', 'geo61', 58, 0, ''),
('pastrand3', '2018-12-02', 'geo61', 62, 0, ''),
('pastrand3', '2019-02-05', 'geo61', 55, 0, ''),
('pastrand4', '2018-12-02', 'geo61', 64, 0, ''),
('pastrand4', '2019-02-05', 'geo61', 59, 0, ''),
('pastrand5', '2018-12-02', 'geo61', 65, 0, ''),
('pastrand5', '2019-02-05', 'geo61', 62, 0, ''),
('pastrand6', '2018-12-02', 'geo61', 68, 0, ''),
('pastrand6', '2019-02-05', 'geo61', 82, 0, ''),
('pastrand7', '2018-12-02', 'geo61', 78, 0, ''),
('pastrand7', '2019-02-05', 'geo61', 75, 0, ''),
('pastrand8', '2018-12-02', 'geo61', 83, 0, ''),
('pastrand8', '2019-02-05', 'geo61', 88, 0, ''),
('pastrand9', '2018-12-02', 'geo61', 85, 0, ''),
('pastrand9', '2019-02-05', 'geo61', 80, 0, ''),
('TV3Gd4SV', '2019-12-04', 'geo61', 73, 0, ''),
('TV3Gd4SV', '2020-04-06', 'geo61', 71, 0, ''),
('TV3Gd4SV', '2020-04-23', 'geo61', 72, 0, ''),
('XfskckQ0', '2019-12-04', 'geo61', 79, 0, ''),
('XfskckQ0', '2020-02-10', 'fre61', 77, 0, ''),
('XfskckQ0', '2020-04-06', 'fre61', 58, 0, ''),
('XfskckQ0', '2020-04-06', 'geo61', 77, 0, ''),
('XfskckQ0', '2020-04-23', 'geo61', 77, 0, ''),
('XfskckQ0', '2020-05-11', 'fre61', 41, 0, ''),
('ykAz0WGp', '2019-12-04', 'geo61', 82, 0, ''),
('ykAz0WGp', '2020-02-10', 'fre61', 73, 0, ''),
('ykAz0WGp', '2020-04-06', 'fre61', 67, 0, ''),
('ykAz0WGp', '2020-04-06', 'geo61', 80, 0, ''),
('ykAz0WGp', '2020-04-23', 'geo61', 80, 0, ''),
('ykAz0WGp', '2020-05-11', 'fre61', 80, 0, ''),
('ynRjeXZj', '2019-12-04', 'geo61', 86, 0, ''),
('ynRjeXZj', '2020-02-10', 'fre61', 82, 0, ''),
('ynRjeXZj', '2020-04-06', 'fre61', 55, 0, ''),
('ynRjeXZj', '2020-04-06', 'geo61', 83, 0, ''),
('ynRjeXZj', '2020-04-23', 'geo61', 86, 0, ''),
('ynRjeXZj', '2020-05-11', 'fre61', 77, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `journal`
--

CREATE TABLE `journal` (
  `subjectCode` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `due_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `journal`
--

INSERT INTO `journal` (`subjectCode`, `date`, `description`, `due_date`) VALUES
('fre61', '2020-05-11', 'sheet 4 for oral practice', '0000-00-00'),
('fre61', '2020-05-12', 'sheet 5 and 6 for oral practice', '0000-00-00'),
('fre61', '2020-05-15', 'sheet 7 and 8 for oral practice', '0000-00-00'),
('geo61', '2020-05-13', 'Read page 61,62 and 63 on desert biomes', '0000-00-00'),
('geo61', '2020-05-14', '1 page report on biome of choice', '2020-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `sender_type` varchar(255) NOT NULL,
  `sender_id` varchar(255) NOT NULL,
  `reciever_id` varchar(255) NOT NULL,
  `note_type` varchar(255) NOT NULL,
  `note` varchar(767) NOT NULL,
  `date` date NOT NULL,
  `assigned_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `name` varchar(255) NOT NULL,
  `parent_id` varchar(255) NOT NULL,
  `login_id` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `email` tinytext NOT NULL,
  `phone` tinytext NOT NULL,
  `address` text NOT NULL,
  `p_code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`name`, `parent_id`, `login_id`, `password`, `student_id`, `email`, `phone`, `address`, `p_code`) VALUES
('Jake Kehoe', '2lyME1qt', '7CHCShmEZtLLTW80', '', 'XfskckQ0', 'jakekehoe@example.com', '9731645521', 'easy house,sample town,Wexford', ''),
('John Baskin', '2PpaFyu4', '9UhNbMEAsleSGf7z', '', '52KjAKXH', 'johnmaskin@example.com', '5957515352', 'example road,sample town,Wexford', ''),
('Molly Dunne', 'bif2jxJ2', 'svIKcDZ1jlVtbTzz', '', '0YD8tJPR', 'mollydunne@example.com', '4565581123', 'example road,sample town,Wexford', ''),
('Yvonne Travers', 'BvSKIq5p', 'EHMHZFiZDDqsnIkh', '', '3cAPtDQw', 'yvonnetravers@example.com', '3269741154', 'easy house,sample town,Wexford', ''),
('Pat Doran', 'Cn3jEjBN', 'OKtFWhoqhS6Ycp9d', '', '5327RNFJ', 'patdoran@example.com', '4826351595', 'sample lane,example town,Leitrim', ''),
('Lydia Ross', 'GpxZhZGW', 'VnjzZqCZE9eGxfPh', '', 'e2OZmTia', 'lydiaross@example.com', '1785542326', 'example road,sample town,Wexford', ''),
('Brendon Doyle', 'IzP5YZiy', 'nNRpWaJgXoRm9Y5g', '', 'J6xHx9iv', 'brendandoyle@example.com', '4845147432', 'sample lane,example town,Leitrim', ''),
('Mary Quinn', 'JfNPZlIs', 'O3zSvxUYP20v34W5', '', 'ykAz0WGp', 'maryquinn@example.com', '7952104766', 'sample lane,example town,Leitrim', ''),
('Bill Carter', 'psXXTIMt', 'bcarter', 'c2fe677a63ffd5b7ffd8facbf327dad0', 'CVPX4Dqh,e2OZmTia', 'billcarter@example.com', '7432611258', 'example road,sample town,Wexford', ''),
('Sarah Hogan', 'RVgqsHFo', 'dviAFRJFJRvXiE8E', '', 'aKfnUP0w', 'sarahhogan@example.com', '9874326465', 'example road,sample town,Wexford', ''),
('Fiona Williams', 'tD77dHo2', 'yCzyk1i2NAGoRkdl', '', 'TV3Gd4SV', 'fionawilliams@example.com', '6398887167', 'sample drive,example town,Leitrim', ''),
('Roisin Murphy', 'tSdUdoeD', '1lvwUK2wA856nbaw', '', 'c2naKQRm', 'roisinmurphy@example.com', '1375985547', 'easy house,sample town,Wexford', ''),
('Sam O\'Neill', 'VzDV9PHg', '1shJt26fZmHjGEw3', '', 'ynRjeXZj', 'samoneill@example.com', '4713621220', 'sample drive,example town,Leitrim', '');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `name` varchar(255) NOT NULL,
  `staff_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `subjectCode` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `email` text NOT NULL,
  `p_code` text NOT NULL,
  `login_id` text NOT NULL,
  `qualified_in` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`name`, `staff_id`, `password`, `subjectCode`, `type`, `email`, `p_code`, `login_id`, `qualified_in`) VALUES
('timmy tommy', 'd7wLlX3D', 'c2fe677a63ffd5b7ffd8facbf327dad0', 'geo61,fre61', 'higher', 'timmytommy@example.com', '', 'timmy', 'French,Geography'),
('sam son', 'hjD3Yxa8', 'c2fe677a63ffd5b7ffd8facbf327dad0', 'eng61', 'standard', 'samson@example.com', '', 'sahn', 'English,Maths');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `name` varchar(255) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `login_id` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `subjectCode` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `address` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`name`, `student_id`, `login_id`, `password`, `subjectCode`, `year`, `address`) VALUES
('Jack Dunne', '0YD8tJPR', 'WCni6oFXvJLBaz5i', '', '', 1, 'example road,sample town,Wexford'),
('Owen Travers', '3cAPtDQw', 'TraversO22', '8ab70b0f3bc42b32645f26e52fbf26cf', 'mat61,geo61,fre61,eng61', 6, 'easy house,sample town,Wexford'),
('Carole Baskin', '52KjAKXH', 'BaskinC37', '8b95df69aa9fdfa66d840d16ce27493b', 'mat61,geo61,eng61', 6, 'example road,sample town,Wexford'),
('Julia Doran', '5327RNFJ', 'DoranJ49', '2c5cafd0e5c1e04a2afc8bf556aa037b', 'geo61,fre61', 6, 'sample lane,example town,Leitrim'),
('Molly Hogan', 'aKfnUP0w', 'HoganM83', '83a58f9cdbad7fd81dd6e93daf8a720b', 'mat61,eng61', 6, 'example road,sample town,Wexford'),
('Cian Murphy', 'c2naKQRm', 'MurphyC17', '00945000694ba47d331f162a02ee8d9b', 'geo61,fre61', 6, 'easy house,sample town,Wexford'),
('Ciara Carter', 'CVPX4Dqh', 'CarterC10', 'c2fe677a63ffd5b7ffd8facbf327dad0', 'geo61,fre61', 6, 'example road,sample town,Wexford'),
('Bob Ross', 'e2OZmTia', 'RossB19', 'bba609e8c931c7c26531a13c3ccb18a2', 'geo61,eng61', 6, 'example road,sample town,Wexford'),
('Teghan Doyle', 'J6xHx9iv', 'DoyleT07', '1befcf6c52e10e8cf337537cdad79dd1', 'mat61,eng61', 6, 'sample lane,example town,Leitrim'),
('Liam Williams', 'TV3Gd4SV', 'WilliamsL85', 'd45f846314b949766f85482b6f2cac3d', 'mat61,geo61', 6, 'sample drive,example town,Leitrim'),
('Andrew Kehoe', 'XfskckQ0', 'KehoeA48', '646633e961575bd5c7d824f3c7e49c46', 'geo61,fre61', 6, 'easy house,sample town,Wexford'),
('Tom Quinn', 'ykAz0WGp', 'QuinnT83', '4470d59bd8b6b9173f11e42b2f1a2c23', 'mat61,geo61,fre61', 6, 'sample lane,example town,Leitrim'),
('Adam O\'Neill', 'ynRjeXZj', 'O\'NeillA03', 'ba6684eb7d6e578dba04c1b45a3469fe', 'geo61,fre61', 6, 'sample drive,example town,Leitrim');

-- --------------------------------------------------------

--
-- Table structure for table `supervision`
--

CREATE TABLE `supervision` (
  `supervision_id` int(11) NOT NULL,
  `staff_id` text NOT NULL,
  `assigned_by` text NOT NULL,
  `assigned_at` datetime NOT NULL,
  `date` date NOT NULL,
  `type` text NOT NULL,
  `removed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `subject` tinytext NOT NULL,
  `day` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `subjectCode` varchar(255) NOT NULL,
  `period` int(11) NOT NULL,
  `room` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`subject`, `day`, `start_time`, `end_time`, `subjectCode`, `period`, `room`) VALUES
('French', 'friday', '09:40:00', '10:20:00', 'fre61', 2, '12'),
('English', 'monday', '09:00:00', '09:40:00', 'eng61', 1, '8'),
('French', 'monday', '09:00:00', '09:40:00', 'fre61', 1, '12'),
('Maths', 'monday', '09:40:00', '10:20:00', 'mat61', 2, '8'),
('Geography', 'thursday', '13:50:00', '14:30:00', 'geo61', 6, '12'),
('Geography', 'tuesday', '09:00:00', '09:40:00', 'geo61', 1, '12'),
('French', 'tuesday', '11:05:00', '11:45:00', 'fre61', 4, '12'),
('Maths', 'wednesday', '09:00:00', '09:40:00', 'mat61', 1, '8'),
('Geography', 'wednesday', '10:20:00', '11:00:00', 'geo61', 3, '12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD UNIQUE KEY `student_id` (`student_id`,`date`);

--
-- Indexes for table `classactivities`
--
ALTER TABLE `classactivities`
  ADD UNIQUE KEY `subjectCode` (`subjectCode`,`date`,`description`);

--
-- Indexes for table `detention`
--
ALTER TABLE `detention`
  ADD UNIQUE KEY `student_id` (`student_id`,`detention_type`,`date`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD UNIQUE KEY `student_id` (`student_id`,`date`,`subjectCode`);

--
-- Indexes for table `journal`
--
ALTER TABLE `journal`
  ADD UNIQUE KEY `subjectCode` (`subjectCode`,`date`,`description`,`due_date`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD UNIQUE KEY `sender_type` (`sender_type`,`sender_id`,`reciever_id`,`note_type`,`note`,`date`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`parent_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `supervision`
--
ALTER TABLE `supervision`
  ADD PRIMARY KEY (`supervision_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD UNIQUE KEY `day` (`day`,`start_time`,`subjectCode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
