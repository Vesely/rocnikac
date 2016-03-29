-- Adminer 4.0.3 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = '+02:00';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL COMMENT 'Text otázky',
  `question_img` varchar(64) DEFAULT NULL COMMENT 'Obrázek k otázce',
  `answer_a` varchar(255) DEFAULT NULL COMMENT 'Odpověď A',
  `answer_b` varchar(255) DEFAULT NULL COMMENT 'Odpověď B',
  `answer_c` varchar(255) DEFAULT NULL COMMENT 'Odpověď C',
  `answer_d` varchar(255) DEFAULT NULL COMMENT 'Odpověď D',
  `answer_text` varchar(255) DEFAULT NULL COMMENT 'Textová odpověď',
  `correct_answer` varchar(24) DEFAULT NULL COMMENT 'Správná odpověď (a/b/c/d)',
  `correct_answers` tinytext COMMENT 'Správné odpověďi',
  `type` varchar(64) NOT NULL COMMENT 'radio X checkbox',
  `del_flag` int(11) NOT NULL DEFAULT '0',
  `ins_dt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`),
  CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tests`;
CREATE TABLE `tests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'autor testu',
  `attempts` int(11) DEFAULT NULL COMMENT 'počet pokusů',
  `name` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `del_flag` int(11) NOT NULL DEFAULT '0',
  `ins_dt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `test_results`;
CREATE TABLE `test_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `percentage` int(11) NOT NULL COMMENT 'Procento úspěšnosti [%]',
  `answers` longtext NOT NULL,
  `del_flag` int(11) NOT NULL DEFAULT '0',
  `ins_dt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `test_results_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`),
  CONSTRAINT `test_results_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL COMMENT 'student X teacher X superadmin',
  `del_flag` int(11) NOT NULL DEFAULT '0',
  `ins_dt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2016-03-29 11:06:37
