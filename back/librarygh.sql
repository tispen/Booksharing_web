-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июн 15 2022 г., 07:12
-- Версия сервера: 8.0.11
-- Версия PHP: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `librarygh`
--

DELIMITER $$
--
-- Процедуры
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `generate_due_list` ()  NO SQL
SELECT I.issue_id, M.email, B.isbn, B.title
FROM book_issue_log I INNER JOIN member M on I.member = M.username INNER JOIN book B ON I.book_isbn = B.isbn$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `book`
--

CREATE TABLE `book` (
  `isbn` char(13) NOT NULL,
  `title` varchar(80) NOT NULL,
  `author` varchar(80) NOT NULL,
  `category` varchar(80) NOT NULL,
  `price` int(4) UNSIGNED NOT NULL DEFAULT '5',
  `copies` int(10) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `book`
--

INSERT INTO `book` (`isbn`, `title`, `author`, `category`, `price`, `copies`) VALUES
('5050032415', 'The book of enlish literature', 'Carol Luisse', 'Literarure', 5, 3),
('6900152484440', 'V for Vendetta', 'Alan Moore', 'Comics', 5, 5),
('9782616052277', 'X-Men: God Loves, Man Kills', 'Chris', 'Comics', 5, 3),
('9783161484100', 'Mike Tyson : Undisputed Truth', 'Larry Sloman, Mike Tyson', 'Sports', 5, 2),
('9789996245442', 'When Breath Becomes Air', 'Paul Kalanithi', 'Medical', 5, 2),
('9885691200700', 'The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', 5, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `librarian`
--

CREATE TABLE `librarian` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `librarian`
--

INSERT INTO `librarian` (`id`, `username`, `password`) VALUES
(1, 'jul', 'no');

-- --------------------------------------------------------

--
-- Структура таблицы `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(40) NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `balance` int(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `member`
--

INSERT INTO `member` (`id`, `username`, `password`, `name`, `email`, `balance`) VALUES
(1, 'julya', 'no', 'Khodyreva Julia Aleksandrovna', 'one@gmail.com', 20),
(2, 'christina', 'one', 'Shunaeva Christina Olegovna', 'christine400eer@gmail.com', 5),
(3, 'cake', 'spicy', 'Ivanova Julia Alekseevna', 'cake@gmail.com', 15),
(4, 'human', 'dog', 'Ivanov Maksim Olegovich', 'oleg4ivanov@gmail.com', 10),
(5, 'tispen', 'vinne', 'Remizova Sofia Victorovna', 'tispenvinne@gmail.com', 4),
(6, 'supergirl', 'yes', 'Kachenkova Olga Borisovna', 'olgaolga@gmail.com', 7),
(7, 'Hollyboy', 'holly', 'Garov Georgy Timurovich', 'hollycat@mail.ru', 0),
(10, 'one', 'one', 'Ivanov Ivan Ivanovich', 'ivanov@gmail.com', 0);

--
-- Триггеры `member`
--
DELIMITER $$
CREATE TRIGGER `add_member` AFTER INSERT ON `member` FOR EACH ROW DELETE FROM pending_registrations WHERE username = NEW.username
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_member` AFTER DELETE ON `member` FOR EACH ROW DELETE FROM pending_book_requests WHERE member = OLD.username
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `pending_book_insert`
--

CREATE TABLE `pending_book_insert` (
  `insert_id` int(11) NOT NULL,
  `member` varchar(20) NOT NULL,
  `book_isbn` varchar(13) NOT NULL,
  `book_title` varchar(80) NOT NULL,
  `book_author` varchar(80) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `book_category` varchar(80) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `pending_book_requests`
--

CREATE TABLE `pending_book_requests` (
  `request_id` int(11) NOT NULL,
  `member` varchar(20) NOT NULL,
  `book_isbn` varchar(13) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `pending_book_requests`
--

INSERT INTO `pending_book_requests` (`request_id`, `member`, `book_isbn`, `time`) VALUES
(20, 'julya', '9789996245442', '2022-06-14 20:28:29');

-- --------------------------------------------------------

--
-- Структура таблицы `pending_registrations`
--

CREATE TABLE `pending_registrations` (
  `username` varchar(20) NOT NULL,
  `password` char(40) NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`isbn`);

--
-- Индексы таблицы `librarian`
--
ALTER TABLE `librarian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Индексы таблицы `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `pending_book_insert`
--
ALTER TABLE `pending_book_insert`
  ADD PRIMARY KEY (`insert_id`);

--
-- Индексы таблицы `pending_book_requests`
--
ALTER TABLE `pending_book_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Индексы таблицы `pending_registrations`
--
ALTER TABLE `pending_registrations`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `librarian`
--
ALTER TABLE `librarian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `pending_book_insert`
--
ALTER TABLE `pending_book_insert`
  MODIFY `insert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT для таблицы `pending_book_requests`
--
ALTER TABLE `pending_book_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
