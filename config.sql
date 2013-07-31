-- phpMyAdmin SQL Dump
-- version 3.5.4
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jul 29, 2013 at 04:06 AM
-- Server version: 5.5.28
-- PHP Version: 5.4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `studip`
--

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `config_id` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `parent_id` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `field` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `value` text COLLATE latin1_german1_ci NOT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT '0',
  `type` enum('boolean','integer','string','array') COLLATE latin1_german1_ci NOT NULL DEFAULT 'boolean',
  `range` enum('global','user') COLLATE latin1_german1_ci NOT NULL DEFAULT 'global',
  `section` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT '0',
  `mkdate` int(20) NOT NULL DEFAULT '0',
  `chdate` int(20) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `comment` text COLLATE latin1_german1_ci NOT NULL,
  `message_template` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`config_id`),
  KEY `parent_id` (`parent_id`),
  KEY `field` (`field`,`range`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`config_id`, `parent_id`, `field`, `value`, `is_default`, `type`, `range`, `section`, `position`, `mkdate`, `chdate`, `description`, `comment`, `message_template`) VALUES
('1ef97bd2674abe4e453c8436bc8b5676', '', 'HTML_FILTER', 'purify', 0, 'string', 'global', '', 0, 1374670164, 1374670164, 'Definiert wie mit HTML Code umgegangen wird:\n\n''purify'' - HTML wird vor der Ausgabe geprüft, unzulässige HTML Tags werden entfernt.\n\n''convert'' - HTML-Code wird in normalen Text umgewandelt. Dadurch ist es nicht mehr möglich HTML-Befehle zu verwenden.', '', ''),
('1ef97bd2674abe4e453c8436bc8b5678', '', 'MARKUP_EDITOR', 'text', 1, 'string', 'global', '', 0, 1374670164, 1374670164, 'Definiert wie Markup editiert wird (Default ist ''text''):\n\n''text'' - Markup-Sourcecode als Text bearbeiten (Benutzer muss Markup-Befehle kennen).\n\n''visual'' - Markup-Formatierungen in visuellem Editor (WYSIWYG) bearbeiten (Benutzer benötigt JavaScript).', '', ''),
('1ef97bd2674abe4e453c8436bc9b5677', '', 'MARKUP_MODE', 'studip', 0, 'string', 'global', '', 0, 1374670164, 1375094953, 'Verwendete Markup-Definitionen. Sollte ein ungültiger Wert gesetzt sein, dann wird die Einstellung ''studip'' verwendet:\n\n''none'' - Markup wird ignoriert und als Text ausgegeben.\n\n''studip'' - Die gewöhnlichen Stud.IP-Markup Befehle werden verwendet.', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
