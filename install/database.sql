CREATE DATABASE IF NOT EXISTS `phpumfrage` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phpumfrage`

CREATE TABLE IF NOT EXISTS `umfragen` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`frage` text NOT NULL,
	`besch` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `umfragen` (`id`, `frage`, `besch`) VALUES (1, 'Was ist deine Lieblings Programmiersprache?', '');

CREATE TABLE IF NOT EXISTS `umfrage_antwort` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`umfrage_id` int(11) NOT NULL,
	`antworten` text NOT NULL,
	`stimmen` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `umfrage_antwort` (`id`, `umfrage_id`, `antworten`, `stimmen`) VALUES (1, 1, 'PHP', 0), (2, 1, 'Python', 0), (3, 1, 'C#', 0), (4, 1, 'Java', 0);