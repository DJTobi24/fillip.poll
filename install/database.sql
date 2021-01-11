CREATE DATABASE IF NOT EXISTS `phppoll` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phppoll`;

CREATE TABLE IF NOT EXISTS `polls` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` text NOT NULL,
	`desc` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `polls` (`id`, `title`, `desc`) VALUES (1, 'What''s your favorite programming language?', '');

CREATE TABLE IF NOT EXISTS `poll_answers` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`poll_id` int(11) NOT NULL,
	`title` text NOT NULL,
	`votes` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `poll_answers` (`id`, `poll_id`, `title`, `votes`) VALUES (1, 1, 'PHP', 0), (2, 1, 'Python', 0), (3, 1, 'C#', 0), (4, 1, 'Java', 0);