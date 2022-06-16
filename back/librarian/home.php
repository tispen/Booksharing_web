<?php
	require "../db_connect.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Онлайн-платформа букшеринга</title>
		<link rel="stylesheet" type="text/css" href="css/home_style.css" />
	</head>
	<body>
		<div id="allTheThings">

			<a href="pending_book_insert.php">
				<input type="button" value="Управление запросами на добавление" />
			</a><br />

			<a href="pending_book_requests.php">
				<input type="button" value="Управление запросами на выдачу" />
			</a><br />

			<a href="pending_registrations.php">
				<input type="button" value="Управление запросами на регистрацию" />
			</a><br />

			<a href="update_copies.php">
				<input type="button" value="Обновить количество копий книги" />
			</a><br />

			<a href="delete_book.php">
				<input type="button" value="Удалить книгу" />
			</a><br />

			<a href="display_books.php">
				<input type="button" value="Отображение всех книг" />
			</a><br />

			<a href="display_user.php">
				<input type="button" value="Отображение всех пользователей" />
			</a><br />

			<a href="update_balance.php">
				<input type="button" value="Обновить баланс пользователя вручную" />
			</a><br />

		</div>
	</body>
</html>