-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 24 Février 2015 à 13:00
-- Version du serveur: 5.6.12-log
-- Version de PHP: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `cf_core`
--
-- --------------------------------------------------------

--
-- Structure de la table `c2w_group`
--

CREATE TABLE IF NOT EXISTS `c2w_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_groupe` varchar(20) NOT NULL,
  `technical` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `c2w_group`
--

INSERT INTO `c2w_group` (`id`, `nom_groupe`, `technical`) VALUES
(1, 'Enregistre', 'register'),
(2, 'Administrateur', 'admin'),
(3, 'Super Administrateur', 'superadmin');

-- --------------------------------------------------------

--
-- Structure de la table `c2w_group_module_access`
--

CREATE TABLE IF NOT EXISTS `c2w_group_module_access` (
  `id_groupe` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  PRIMARY KEY (`id_groupe`,`id_module`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `c2w_group_module_access`
--

INSERT INTO `c2w_group_module_access` (`id_groupe`, `id_module`) VALUES
(3, 2),
(3, 6);

-- --------------------------------------------------------

--
-- Structure de la table `c2w_group_right`
--

CREATE TABLE IF NOT EXISTS `c2w_group_right` (
  `idGroup` int(11) NOT NULL,
  `idDroit` int(11) NOT NULL,
  PRIMARY KEY (`idGroup`,`idDroit`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `c2w_group_right`
--

INSERT INTO `c2w_group_right` (`idGroup`, `idDroit`) VALUES
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(8, 2);

-- --------------------------------------------------------

--
-- Structure de la table `c2w_lang`
--

CREATE TABLE IF NOT EXISTS `c2w_lang` (
  `id_lang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `iso_code` char(2) NOT NULL,
  `language_code` char(5) NOT NULL,
  `date_format_lite` char(32) NOT NULL DEFAULT 'Y-m-d',
  `date_format_full` char(32) NOT NULL DEFAULT 'Y-m-d H:i:s',
  `is_rtl` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_lang`),
  KEY `lang_iso_code` (`iso_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `c2w_lang`
--

INSERT INTO `c2w_lang` (`id_lang`, `name`, `active`, `iso_code`, `language_code`, `date_format_lite`, `date_format_full`, `is_rtl`) VALUES
(1, 'English (English)', 1, 'en', 'en-us', 'm/d/Y', 'm/d/Y H:i:s', 0),
(2, 'Français (French)', 1, 'fr', 'fr', 'd/m/Y', 'd/m/Y H:i:s', 0),
(3, 'Español (Spanish)', 0, 'es', 'es', 'd/m/Y', 'd/m/Y H:i:s', 0),
(4, 'Deutsch (German)', 0, 'de', 'de', 'd.m.Y', 'd.m.Y H:i:s', 0),
(5, 'Italiano (Italian)', 1, 'it', 'it', 'd/m/Y', 'd/m/Y H:i:s', 0);

-- --------------------------------------------------------

--
-- Structure de la table `c2w_mailsformat`
--

CREATE TABLE IF NOT EXISTS `c2w_mailsformat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template` (`template`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `c2w_module`
--

CREATE TABLE IF NOT EXISTS `c2w_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `actived` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `c2w_module`
--

INSERT INTO `c2w_module` (`id`, `name`, `actived`, `description`) VALUES
(2, 'Configurations', 1, 'Gestion des configurations du site'),
(6, 'Utilisateurs', 1, 'Gérez les utilisateurs, les groupes, les accès aux modules...');

-- --------------------------------------------------------

--
-- Structure de la table `c2w_right`
--

CREATE TABLE IF NOT EXISTS `c2w_right` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(20) NOT NULL,
  `uniq_id` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `c2w_right`
--

INSERT INTO `c2w_right` (`id`, `libelle`, `uniq_id`) VALUES
(1, 'Ajouter', 'add'),
(2, 'Modifier', 'edit'),
(3, 'Supprimer', 'delete'),
(4, 'acces admin', 'admin_access');

-- --------------------------------------------------------

--
-- Structure de la table `c2w_user`
--

CREATE TABLE IF NOT EXISTS `c2w_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(100) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `adresse` varchar(100) DEFAULT NULL,
  `avatar` varchar(100) DEFAULT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `newsletter` tinyint(1) NOT NULL,
  `pays` varchar(20) NOT NULL,
  `ville` varchar(20) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `tel1` varchar(20) NOT NULL,
  `tel2` varchar(20) NOT NULL,
  `infos_complementaires` text NOT NULL,
  `type_user` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `c2w_user`
--

INSERT INTO `c2w_user` (`id`, `pseudo`, `nom`, `prenom`, `adresse`, `avatar`, `password`, `email`, `is_active`, `newsletter`, `pays`, `ville`, `code_postal`, `tel1`, `tel2`, `infos_complementaires`, `type_user`) VALUES
(1, 'crystals', 'Crystals Services', 'Gaston', '646878', '', 'b05b7b0aba18a15d98ea84d1b6c0cdec', 'contact@crystals-services.com', 1, 1, '', '', '', '96155706', '', '', ''),
(8, 'finder', 'finder', 'finder', 'rue des accacia', '', 'd820dc81cbe25c0941437082a340ed8f', 'fozeutakoudjou@gmail.com', 1, 0, 'Cameroun', 'YaoundÃ©', '30162', '96155706', '', '', ''),
(9, 'crystals2', 'crystals fw', '', 'rue des accacia', '', '173c79b164fa82a32338cf61aff4e821', 'contact2@crystals-services.com', 1, 0, 'Cameroun', 'YaoundÃ©', '30162', '96155706', '', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `c2w_user_group`
--

CREATE TABLE IF NOT EXISTS `c2w_user_group` (
  `idUser` int(11) NOT NULL,
  `idGroup` int(11) NOT NULL,
  PRIMARY KEY (`idUser`,`idGroup`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `c2w_user_group`
--

INSERT INTO `c2w_user_group` (`idUser`, `idGroup`) VALUES
(1, 3),
(8, 3),
(9, 3);

-- --------------------------------------------------------

--
-- Structure de la table `c2w_webservice_account`
--

CREATE TABLE IF NOT EXISTS `c2w_webservice_account` (
  `id_webservice_account` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `description` text,
  `class_name` varchar(64) NOT NULL DEFAULT 'WebserviceRequest',
  `is_module` tinyint(2) NOT NULL DEFAULT '0',
  `module_name` varchar(50) DEFAULT NULL,
  `active` tinyint(2) NOT NULL,
  PRIMARY KEY (`id_webservice_account`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `c2w_webservice_permission`
--

CREATE TABLE IF NOT EXISTS `c2w_webservice_permission` (
  `id_webservice_permission` int(11) NOT NULL AUTO_INCREMENT,
  `resource` varchar(50) NOT NULL,
  `method` enum('GET','POST','PUT','DELETE','HEAD') NOT NULL,
  `id_webservice_account` int(11) NOT NULL,
  PRIMARY KEY (`id_webservice_permission`),
  UNIQUE KEY `resource_2` (`resource`,`method`,`id_webservice_account`),
  KEY `resource` (`resource`),
  KEY `method` (`method`),
  KEY `id_webservice_account` (`id_webservice_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
