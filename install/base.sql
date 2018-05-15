CREATE TABLE IF NOT EXISTS `c2w_language` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(32) NOT NULL,
	`active` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`isoCode` VARCHAR(2) NOT NULL,
	`languageCode` VARCHAR(5) NULL,
	`dateFormatLite` VARCHAR(32) NULL DEFAULT 'Y-m-d',
	`dateFormatFull` VARCHAR(32) NULL DEFAULT 'Y-m-d H:i:s',
	`rtl` tinyint(1) NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_language_isoCode` (`isoCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT = 1 ;

CREATE TABLE IF NOT EXISTS `c2w_group` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`idParent` int(11) UNSIGNED,
	type                 int(11) NOT NULL,
	dateAdd              datetime,
	dateUpdate           datetime,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `c2w_group_lang` (
	`id_group` int(11) UNSIGNED NOT NULL,
	`lang` varchar(2) NOT NULL,
	`name` varchar(50) NOT NULL,
	`description` text,
	PRIMARY KEY (`id_group`, `lang`),
	CONSTRAINT FK_group_lang_group FOREIGN KEY (`id_group`) REFERENCES c2w_group(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `c2w_wrapper` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` int(11) UNSIGNED NOT NULL,
	`module` varchar(50),
	`target` varchar(50),
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `c2w_wrapper_lang` (
	`id_wrapper` int(11) UNSIGNED NOT NULL,
	`lang` varchar(2) NOT NULL,
	`name` varchar(50),
	`description` text,
	PRIMARY KEY (`id_wrapper`, `lang`),
	CONSTRAINT FK_wrapper_lang_wrapper FOREIGN KEY (id_wrapper) REFERENCES c2w_wrapper(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `c2w_action` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`code` varchar(50) NOT NULL,
	`dependentOnId` bool DEFAULT '1' NOT NULL,
	PRIMARY KEY (`id`), 
	UNIQUE KEY `UNIQUE_action_code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `c2w_action_lang` (
	`id_action` int(11) UNSIGNED NOT NULL,
	`lang` varchar(2) NOT NULL,
	`name` varchar(50),
	`description` text,
	PRIMARY KEY (`id_action`, `lang`),
	CONSTRAINT FK_action_lang_action FOREIGN KEY (id_action) REFERENCES c2w_action(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `c2w_right` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`idWrapper` int(11) UNSIGNED NOT NULL,
	`idAction` int(11) UNSIGNED NOT NULL,
	`active`              bool DEFAULT '1' NOT NULL,
	PRIMARY KEY (`id`),
	CONSTRAINT FK_right_wrapper FOREIGN KEY (idWrapper) REFERENCES c2w_wrapper(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT FK_right_action FOREIGN KEY (idAction) REFERENCES c2w_action(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*CREATE TABLE IF NOT EXISTS `c2w_right_lang` (
	`id_right` int(11) UNSIGNED NOT NULL,
	`lang` varchar(2) NOT NULL,
	`label` varchar(200),
	`description` text,
	PRIMARY KEY (`id_right`, `lang`),
	CONSTRAINT FK_right_lang_right FOREIGN KEY (`id_right`) REFERENCES c2w_right(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;*/


/*==============================================================*/
/* Table : c2w_configuration                                    */
/*==============================================================*/
create table c2w_configuration
(
	id                   int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	name                 varchar(255) not null,
	value                text,
	dateUpdate           datetime,
	primary key (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

/*==============================================================*/
/* Table : c2w_configuration_lang                               */
/*==============================================================*/
create table c2w_configuration_lang
(
   id_configuration     int(11) UNSIGNED NOT NULL,
   lang                 varchar(2) not null,
   valueLang            text,
   primary key (id_configuration, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*==============================================================*/
/* Table : c2w_user                                             */
/*==============================================================*/
create table c2w_user
(
   id                   int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   lastName             varchar(35)  NOT NULL,
   firstName            varchar(35) NOT NULL,
   gender            	int(11),
   phone                varchar(20),
   active               bool DEFAULT '1' NOT NULL,
   email                varchar(128) NOT NULL,
   preferredLang        varchar(2),
   avatar               varchar(200),
   type                 int(11) NOT NULL,
   password            varchar(32) NOT NULL,
   additionalInfos     text,
   dateAdd              datetime,
   dateUpdate           datetime,
   lastPasswordGeneratedTime      datetime,
   lastConnectionDate      datetime,
   lastConnectionData      text,
   deleted               bool DEFAULT '0',
   primary key (id),
   UNIQUE KEY `UNIQUE_user_email_type` (`type`, `email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

/*==============================================================*/
/* Table : c2w_log                                              */
/*==============================================================*/
create table c2w_log
(
   id                   int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   idUser               int(11) UNSIGNED,
   type                 int,
   data                 text,
   action               int,
   dateAdd              datetime,
   trackingData      text,
   additionalInfos     text,
   primary key (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


create table c2w_access
(
   id                  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   idGroup int(11) UNSIGNED,
   idRight int(11) UNSIGNED NOT NULL,
   idUser int(11) UNSIGNED,
   `active`              bool DEFAULT '1' NOT NULL,
   dateAdd              datetime,
   dateUpdate           datetime,
   primary key (id),
   constraint FK_access_group foreign key (idGroup) references c2w_group (id) on delete cascade on update cascade,
   constraint FK_access_user foreign key (idUser) references c2w_user (id) on delete cascade on update cascade,
   constraint FK_access_right foreign key (idRight) references c2w_right (id) on delete cascade on update cascade
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `c2w_mail_format` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`template` varchar(100) NOT NULL,
	`active` tinyint(1) NOT NULL DEFAULT '1',
	dateAdd              datetime,
	dateUpdate           datetime,
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_mail_format_template` (`template`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `c2w_mail_format_lang` (
	`id_mail_format` int(11) NOT NULL,
	`lang` varchar(2) NOT NULL,
	`title` varchar(255) NOT NULL,
	`content` text NULL,
	PRIMARY KEY (`id_mail_format`, `lang`),
	CONSTRAINT FK_mail_format_lang_mail_format FOREIGN KEY (`id_mail_format`) 
	REFERENCES c2w_mail_format(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*==============================================================*/
/* Table : c2w_user_group                                             */
/*==============================================================*/
create table c2w_user_group
(
   idUser                   int(11) UNSIGNED NOT NULL,
   idGroup           int(11) UNSIGNED NOT NULL,
   primary key (idUser, idGroup)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `c2w_admin_menu` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`idWrapper` int(11) UNSIGNED,
	`idAction` int(11) UNSIGNED,
	`idParent` int(11) UNSIGNED,
	`clickable` tinyint(1),                                                                                                                                                                  
	`position` int(11),                                                                                                                                                                      
	`linkType` int(11),                                                                                                                                                                     
	`level` int(11),                                                                                                                                                                         
	`newTab` tinyint(1),      
	`active`              bool DEFAULT '1' NOT NULL,
	`iconClass`             varchar(15),
	PRIMARY KEY (`id`),
	CONSTRAINT FK_admin_menu_wrapper FOREIGN KEY (idWrapper) REFERENCES c2w_wrapper(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT FK_admin_menu_action FOREIGN KEY (idAction) REFERENCES c2w_action(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `c2w_admin_menu_lang` (
	`id_admin_menu` int(11) UNSIGNED NOT NULL,
	`lang` varchar(2) NOT NULL,
	`name` varchar(50) NOT NULL,                                                                                                                                                     
	`title` varchar(50),                                                                                                                                                    
	`link` varchar(60), 
	PRIMARY KEY (`id_admin_menu`, `lang`),
	CONSTRAINT FK_admin_menu_lang_admin_menu FOREIGN KEY (`id_admin_menu`) REFERENCES c2w_admin_menu(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table c2w_admin_menu add constraint FK_admin_menu_idParent_admin_menu foreign key (idParent) references c2w_admin_menu (id) on delete cascade on update cascade;
alter table c2w_group add constraint FK_group_idParent_group foreign key (idParent) references c2w_group (id) on delete cascade on update cascade;

alter table c2w_log add constraint FK_log_user foreign key (idUser) references c2w_user (id) on delete restrict on update cascade;
	  
alter table c2w_configuration_lang add constraint configuration_lang_configuration foreign key (id_configuration) references c2w_configuration (id) on delete cascade on update cascade;

alter table c2w_user_group add constraint FK_user_group_user foreign key (idUser) references c2w_user (id) on delete cascade on update cascade;

alter table c2w_user_group add constraint FK_user_group_group foreign key (idGroup) references c2w_group (id) on delete cascade on update cascade;

--
-- Contenu de la table `c2w_group`
--

/*INSERT INTO `c2w_group` (`id`, `technical`) VALUES
(1, 'register'),
(2, 'admin'),
(3, 'superadmin');

INSERT INTO `c2w_group_lang` (`id_group`, `lang`, `name`, `description`) VALUES
(1, 'fr', 'Enregistre', 'Enregistre'),
(2, 'fr', 'Administrateur', 'Administrateur'),
(3, 'fr', 'Super Administrateur', 'Super Administrateur'),

(1, 'en', 'Register', 'Register'),
(2, 'en', 'Admin', 'Admin'),
(3, 'en', 'Super Admin', 'Super Admin');*/