-- (A) Umfrage -> Frage
CREATE TABLE `poll_questions` (
  `poll_id` int(11) NOT NULL,
  `poll_question` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `poll_questions` (`poll_id`, `poll_question`) VALUES
(1, 'How much wood would a woodchuck chuck if a woodchuck could chuck wood?');

ALTER TABLE `poll_questions`
  ADD PRIMARY KEY (`poll_id`);

ALTER TABLE `poll_questions`
  MODIFY `poll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


-- (B) Umfrage -> Optionen
CREATE TABLE `poll_options` (
  `poll_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `poll_options` (`poll_id`, `option_id`, `option_text`) VALUES
(1, 1, '300 pounds'),
(1, 2, '500 pounds'),
(1, 3, '700 pounds');

ALTER TABLE `poll_options`
  ADD PRIMARY KEY (`poll_id`,`option_id`);


-- (C) Umfrage -> Stimmen
CREATE TABLE `poll_votes` (
  `user_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `poll_votes` (`user_id`, `poll_id`, `option_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 2),
(4, 1, 2),
(5, 1, 1),
(6, 1, 3),
(7, 1, 3),
(8, 1, 2),
(9, 1, 1),
(10, 1, 3);

ALTER TABLE `poll_votes`
  ADD PRIMARY KEY (`user_id`, `poll_id`),
  ADD KEY `option_id` (`option_id`);