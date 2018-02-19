-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mer 02 Juillet 2014 à 09:43
-- Version du serveur: 5.5.8
-- Version de PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `cf_finder`
--

-- --------------------------------------------------------

--
-- Structure de la table `c2w_menu`
--

CREATE TABLE IF NOT EXISTS `c2w_menu` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `type_link` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `titre` varchar(20) NOT NULL,
  `lien` varchar(50) NOT NULL,
  `parent` varchar(50) NOT NULL,
  `logo` varchar(50) NOT NULL,
  `position` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=774 ;

--
-- Contenu de la table `c2w_menu`
--

