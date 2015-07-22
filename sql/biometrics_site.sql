-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2015 at 01:59 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `biometrics_site`
--
CREATE DATABASE IF NOT EXISTS `biometrics_site` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `biometrics_site`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `username` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `password`) VALUES
('admin', '$2y$10$9MrHJWKX0vzM9VGTGV0OZ.3AI6frX0PfJGsVCl/HThMYMc1jt0Y7K');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day` date NOT NULL,
  `staff_id` varchar(6) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `logout_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `day`, `staff_id`, `login_time`, `logout_time`) VALUES
(1, '2015-06-01', 'S-0001', '2015-06-01 07:00:00', '2015-06-01 15:00:00'),
(2, '2015-06-02', 'S-0001', '2015-06-02 07:00:00', '2015-06-02 15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `fingerprint`
--

DROP TABLE IF EXISTS `fingerprint`;
CREATE TABLE IF NOT EXISTS `fingerprint` (
  `staff_id` varchar(6) NOT NULL,
  `LEFT_PINKY` blob NOT NULL,
  `LEFT_RING` blob NOT NULL,
  `LEFT_MIDDLE` blob NOT NULL,
  `LEFT_INDEX` blob NOT NULL,
  `LEFT_THUMB` blob NOT NULL,
  `RIGHT_THUMB` blob NOT NULL,
  `RIGHT_INDEX` blob NOT NULL,
  `RIGHT_MIDDLE` blob NOT NULL,
  `RIGHT_RING` blob NOT NULL,
  `RIGHT_PINKY` blob NOT NULL,
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fingerprint`
--

INSERT INTO `fingerprint` (`staff_id`, `LEFT_PINKY`, `LEFT_RING`, `LEFT_MIDDLE`, `LEFT_INDEX`, `LEFT_THUMB`, `RIGHT_THUMB`, `RIGHT_INDEX`, `RIGHT_MIDDLE`, `RIGHT_RING`, `RIGHT_PINKY`) VALUES
('s-0001', '', '', '', '', '', 0x00f88101c82ae3735cc0413709ab7130a0145592bed036226e505cb9a865a362366b15f9507df2e8c0854e33e5a61af00c0053cbb67fefe1f0bb436dd7ac03662b896afefa3b8feb417716b1ac2d9dfeed89765584a0567a2b5996b371130e088e0b05688837c809a2538aa97bda3eae3527c6d8c85d312b79c55355dc1515832450fb00749d78f7f64d1f6dd24df3979a6634895b68312e4b05f81fe31d802dbbd9e1ee014bea626852a393e796666a727dc842831372e869c4bc35874c6e3ae92afa64f43d21b8669b58e16ebc7e1f18175994d3f6907bd32ebd26b7b50ff673ee4474decb63d53d605cc48428e11ea59a5978ea4d1e94e5dc685d790bef2d53e33f13c874aebc6cdc9d56ac60e809a7e3b4c50cb5c7c82ed1103077fa359429a110b089c9e07cf3ab1b2dd13fdca2bf888a27b2fe57266498f7b4f6092a9b99106ed63f64f567724eacf22d97916757318518e140bc81e76f5dd417054528d3483f7f123b2bb2629a5d8f85cc49caad0c9eff6d7457128e5ad09e6fc658f1e98d81b5666f00f88001c82ae3735cc0413709ab71f0be1455923fb1b1a6caaee54dbf90b1b5af48341b7df6678c819f49e404bdcf19b0d8455d06e8bacbb39da9613fe7b6b0f0f89e25fdb56dc6cc214ccf2b642a3e0b8b7494055bb5001ba146cef9f0a486f6e167122afd5fec9263883c92fbb0d83616960af49fc91da1a33e454a2b61b1ca42761292a34634164045e228dff84c8ac83bb775a0d21809a6184e7d7abdcb6a3b90d5892fa4c3148363632231c93a9245ce621994f45c9e37ff4562c80055cfa752fd5958976819e9a589ff36bd1b75e883a13c0ab93913b44b16024ea85319b3f94dc7bdbbb0c4f6e9a8ec4fa6457aabe669a69b19492538869d02a9124e490b3adc917dc86bd05980d575a09d9f37ac41481a3755a0fb45e1a0e246cc901c96d275395ed03ad82f5e5e220f2227a5ab7aaea2a08516012131efee8d34201b0d6d0222ae1d62319351b2aa3c4e12bbc28780b99dbe72c5401a90a38b676033bada2f9ebe10588741f407c7f424acc784bf33f6e1a471a0ef1e57c9facab0ee4e016d6f00f87f01c82ae3735cc0413709ab71f0a11455921fc81964ae858b8d91c323bcbcbc9e78a994c81145c5919134c6d21e9087c8ae9622a5cff264c9226c2fb0f6c072d8cd8e582d830c2402c945a094e43ae3c3fe49f9d15024feb22c1ab9f286bb9c0d800ae804368dda6496fe3ad468a281ec91c6466a0ae51e61d68b21303b2450db6a071d364307abe76ac176c1606f3a155ccbc08fba0d1848e06186fa11aa987347520414520d6d9eb075546ed36609d3ab3b4e2a8347e14fc10c5673ed3b8f7246023c439af36477e96ad7af9fbf8ff9d9b79884e4076085e4619e2c81590b37c34f4710237fb2cdad8767648b64979a4a2c971e98a70583a005a4c35b32b7d95bac5fbc6cc7f809eabed8e1644099e9cc0dcc6a8e5056f7fdc42273543032cc0efea20b5a472d11be447793160ed43a583d7311bc53a58f0f945ef5f920a80e018b2c7699e20c783b247f77e36f2b171f54e76cfb51c55628b5deeb13a38213a9b8a35ac14dec0df6e5e60e760a65499a8deb6aeee0ea6770d519a9bfa445c86f00e87f01c82ae3735cc0413709ab71b09614559221e744641ec091d62ae1d401270e584cb32b440e7c70611f8de7bab8905893c70f92e915d1aa617f7a5e2f46247586e5e5cdf22d81efcc2643bb432ac3e7523b6ada610e3033a51eb3ad7c0ab779ebce05795aa9374d5a91b1e57d08bdc22ec5f2ef298a0f2d79038cb0be0153918aa1d52a2dbed37189fa90c5c89e093dfe36348d295f705d03f89bbbe1f763d6cc81df489f2780b9e5fdc32543d34a0f1baae0de9a879eadb9377892e1c2a927df658ac7dc8bc487c00f4cf8b3702e9098d6aaad40b4e4b46a4726e462d3e2a3febe83eecb1846868b925d8197c03430442ec082e33014f2acb0bba340ce749a9bdc8e375eea41732a60f4c18076a15af054267ffdef4e7d4c6153ec9e74a769a0bbfc4114c593cd27ae2770f5f70f14d616d150c87db236d060b47c7f560a658f9939e9d3b350a1ba28e7c54f08100091a598547e7a3932512cb2046aeb84e72290954f6069903a0a3c680b919692e274b40fb70f53c5ca8fa789c9e2449d0d186f0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000, '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
CREATE TABLE IF NOT EXISTS `leave_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` varchar(6) NOT NULL,
  `request_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_approved` enum('PENDING','APPROVED','DISAPPROVED') NOT NULL,
  `type` enum('CASUAL','MATERNITY') NOT NULL,
  `reason` text NOT NULL,
  `request_start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `request_duration` int(11) NOT NULL,
  `approved_start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `approved_duration` int(11) NOT NULL,
  `date_approved` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `staff_id`, `request_time`, `is_approved`, `type`, `reason`, `request_start_time`, `request_duration`, `approved_start_time`, `approved_duration`, `date_approved`) VALUES
(3, 'S-0001', '2015-06-04 05:42:27', 'PENDING', 'CASUAL', 'you know', '2014-12-31 23:00:00', 35, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(4, 'S-0001', '2015-06-04 05:43:45', 'APPROVED', 'CASUAL', '', '2014-12-31 23:00:00', 50, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(5, 'S-0001', '2015-06-04 05:43:50', 'DISAPPROVED', 'CASUAL', '', '2014-12-31 23:00:00', 25, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `staff_id` varchar(6) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `other_names` varchar(30) NOT NULL,
  `department` varchar(30) NOT NULL,
  `designation` varchar(30) NOT NULL,
  `finger_print` blob NOT NULL,
  `pic_url` text NOT NULL,
  `sex` enum('M','F') NOT NULL,
  `dob` date NOT NULL,
  `email` text NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(13) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_suspended` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `password`, `first_name`, `last_name`, `other_names`, `department`, `designation`, `finger_print`, `pic_url`, `sex`, `dob`, `email`, `address`, `phone`, `is_deleted`, `is_suspended`) VALUES
('S-0001', '$2y$10$9MrHJWKX0vzM9VGTGV0OZ.3AI6frX0PfJGsVCl/HThMYMc1jt0Y7K', 'Chukwuemeka', 'Nwobodo', 'Joseph', 'ENGINEERS', 'MANAGER Designation', '', '', '', '2015-06-01', 'phoenixlabs.ng@gmail.com', '', '08133621591', 0, 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_staff_id_constraint` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_request_staff_id_constraint` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
