<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_member.php";
	require "header_member.php";

?>

<html>
	<head>
		<title>Онлайн-платформа букшеринга</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
		<link rel="stylesheet" href="css/insert_book_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
			<center><legend>Поделиться книгой</legend></center>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				
				<div class="icon">
					<input class="b-isbn" id="b_isbn" type="number" name="b_isbn" placeholder="ISBN (указан на книге)" required />
				</div>
				
				<div class="icon">
					<input class="b-title" type="text" name="b_title" placeholder="Название книги" required />
				</div>

				<div class="icon">
					<input class="b-author" type="text" name="b_author" placeholder="Автор" required />
				</div>

				<h4>Жанр</h4>
				
					<p class="cd-select icon">
						<select class="b-category" name="b_category">
							<option>History</option>
							<option>Comics</option>
							<option>Fiction</option>
							<option>Non-Fiction</option>
							<option>Biography</option>
							<option>Medical</option>
							<option>Fantasy</option>
							<option>Education</option>
							<option>Sports</option>
							<option>Technology</option>
							<option>Literature</option>
						</select>
					</p>
				</div>
				
				<br />
				<input class="b-isbn" type="submit" name="b_add" value="Поделиться" />
		</form>
	<?php
		if(isset($_POST['b_add']))
		{
			$b_isbn = $_POST['b_isbn'];
			$b_title = $_POST['b_title'];
			$b_author = $_POST['b_author'];
			$b_category = $_POST['b_category'];

			$query = $con->prepare("INSERT INTO pending_book_insert(member, book_isbn, book_title, book_author, book_category) VALUES(?, ?, ?, ?, ?);");
			$query->bind_param("sssss", $_SESSION['username'], $b_isbn, $b_title, $b_author, $b_category);

			if(!$query->execute())
				die(error_without_field("ERROR: Couldn't add book"));

			echo success("Книга отправлена. Вам на почту в течении 1-3 дней придет уведомление о решении администратора. Если книга принята, то вы сможете принести ее на любой пункт букшеринга.");
		}
	?>

</html>