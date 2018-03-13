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

CREATE TABLE IF NOT EXISTS `c2w_right_container` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`entity` text,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `c2w_right_container_lang` (
	`id_right_container` int(11) UNSIGNED NOT NULL,
	`lang` varchar(2) NOT NULL,
	`name` varchar(50),
	PRIMARY KEY (`id_right_container`, `lang`),
	CONSTRAINT FK_right_container_lang_right_container FOREIGN KEY (id_right_container) REFERENCES c2w_right_container(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `c2w_right` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`idContainer` int(11) UNSIGNED,
	`code` varchar(50) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_right_code` (`code`),
	CONSTRAINT FK_right_right_container FOREIGN KEY (idContainer) REFERENCES c2w_right_container(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `c2w_right_lang` (
	`id_right` int(11) UNSIGNED NOT NULL,
	`lang` varchar(2) NOT NULL,
	`label` varchar(200),
	`description` text,
	PRIMARY KEY (`id_right`, `lang`),
	CONSTRAINT FK_right_lang_right FOREIGN KEY (`id_right`) REFERENCES c2w_right(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
);

/*==============================================================*/
/* Table : c2w_user                                             */
/*==============================================================*/
create table c2w_user
(
   id                   int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   idProposer           int(11) UNSIGNED,
   lastName             varchar(35),
   firstName            varchar(35),
   gender            varchar(10),
   phone                varchar(20),
   balance              float,
   active               bool DEFAULT '1',
   email                varchar(40),
   preferredLang        varchar(2),
   avatar               varchar(200),
   type                int,
   additionalInfos     text,
   dateAdd              datetime,
   dateUpdate           datetime,
   deleted               bool DEFAULT '0',
   primary key (id)
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
   primary key (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


create table c2w_access
(
   id                  int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   idGroup int(11) UNSIGNED,
   idRight int(11) UNSIGNED NOT NULL,
   idUser int(11) UNSIGNED,
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


alter table c2w_log add constraint FK_log_user foreign key (idUser) references c2w_user (id) on delete restrict on update cascade;
	  
alter table c2w_user add constraint FK_user_idProposer_user foreign key (idProposer) references c2w_user (id) on delete cascade on update cascade;
	  
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