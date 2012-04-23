-- phpMyAdmin SQL Dump
-- version 
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 20, 2012 at 04:21 PM
-- Server version: 5.1.61
-- PHP Version: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `windy_quotes`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_jobs`
--

CREATE TABLE `client_jobs` (
  `client_job_id` int(10) NOT NULL AUTO_INCREMENT,
  `client_job_name` varchar(255) NOT NULL,
  `client_job_jobno` char(40) NOT NULL,
  `client_job_notes` text NOT NULL,
  `client_job_amount` decimal(6,2) NOT NULL,
  `client_job_timestamp` datetime NOT NULL,
  `client_job_user` int(2) NOT NULL COMMENT 'This is who saved the estimate. Don''t want to show estimates that Monica created to Wendy',
  `client_project_type` int(10) NOT NULL,
  `client_job_del` tinyint(1) NOT NULL,
  PRIMARY KEY (`client_job_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `estimates`
--

CREATE TABLE `estimates` (
  `estimate_id` int(12) NOT NULL AUTO_INCREMENT,
  `estimate_item_id` int(10) NOT NULL,
  `estimate_item_amount` decimal(6,2) NOT NULL,
  `estimate_item_clientjob` int(10) NOT NULL,
  `estimate_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`estimate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Items`
--

CREATE TABLE `Items` (
  `item_id` int(10) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(40) NOT NULL,
  `item_type` tinyint(1) NOT NULL,
  `item_amount` decimal(6,2) NOT NULL,
  `item_qty` decimal(4,2) NOT NULL,
  `item_notes` text NOT NULL,
  `item_user` int(10) NOT NULL,
  `item_del` tinyint(4) NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(10) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(40) NOT NULL,
  `project_notes` text NOT NULL,
  `project_total` decimal(10,2) NOT NULL,
  `project_user` int(10) NOT NULL,
  `project_private` tinyint(1) NOT NULL,
  `project_del` tinyint(1) NOT NULL,
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_items`
--

CREATE TABLE `project_items` (
  `pitem_id` int(10) NOT NULL AUTO_INCREMENT,
  `pitem_item` int(10) NOT NULL,
  `pitem_type` tinyint(1) NOT NULL,
  `pitem_project` int(10) NOT NULL,
  `pitem_qty` int(10) NOT NULL,
  `pitem_notes` text NOT NULL,
  `pitem_del` tinyint(1) NOT NULL,
  PRIMARY KEY (`pitem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `password` char(40) NOT NULL,
  `user_fn` varchar(40) NOT NULL,
  `user_ln` varchar(40) NOT NULL,
  `user_email` varchar(80) NOT NULL,
  `user_admin` tinyint(1) NOT NULL,
  `user_del` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

