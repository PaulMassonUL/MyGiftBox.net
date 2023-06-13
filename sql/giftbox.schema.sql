-- Adminer 4.8.1 MySQL 5.5.5-10.3.11-MariaDB-1:10.3.11+maria~bionic dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `box`;
CREATE TABLE `box` (
  `id` varchar(128) NOT NULL PRIMARY KEY,
  `token` varchar(64) NOT NULL,
  `libelle` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `montant` decimal(12,2) NOT NULL DEFAULT 0.00,
  `kdo` tinyint(4) NOT NULL DEFAULT 0,
  `message_kdo` text NOT NULL DEFAULT '',
  `statut` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE `categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(128) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `prestation`;
CREATE TABLE `prestation` (
  `id` varchar(128) NOT NULL,
  `libelle` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(256) DEFAULT NULL,
  `unite` varchar(128) DEFAULT NULL,
  `tarif` decimal(10,2) NOT NULL,
  `img` varchar(128) NOT NULL,
  `cat_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `box2presta`;
CREATE TABLE `box2presta` (
  `box_id` varchar(128) NOT NULL,
  `presta_id` varchar(128) NOT NULL,
  `quantite` int(11) NOT NULL,
  PRIMARY KEY (`box_id`, `presta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `BoxTemplate`;
CREATE TABLE `BoxTemplate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `montant` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `template2presta`;
CREATE TABLE `template2presta` (
  `template_id` int(11) NOT NULL,
  `presta_id` varchar(128) NOT NULL,
  `quantite` int(11) NOT NULL,
  PRIMARY KEY (`template_id`, `presta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `box2user`;
CREATE TABLE `box2user` (
  `box_id` varchar(128) NOT NULL,
  `user_id` varchar(128) NOT NULL,
  PRIMARY KEY (`box_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `email` varchar(128) NOT NULL,
  `password` text NOT NULL,
  `nom` varchar(128) NOT NULL,
  `prenom` varchar(128) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2023-04-07 14:49:49