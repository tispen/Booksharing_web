<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Онлайн-платформа букшеринга</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
		<link rel="stylesheet" type="text/css" href="../css/custom_checkbox_style.css">
		<link rel="stylesheet" type="text/css" href="css/pending_book_insert_style.css">
	</head>
	<body>
	<?php
			$query = $con->prepare("SELECT * FROM pending_book_insert;");
			$query->execute();
			$result = $query->get_result();;
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>Нет запросов.</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<center><legend>Запрос</legend></center>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding=10 cellspacing=10>
						<tr>
							<th></th>
							<th>Имя читателя<hr></th>
							<th>ISBN<hr></th>
							<th>Название книги<hr></th>
							<th>Автор<hr></th>
							<th>Жанр<hr></th>
							<th>Время<hr></th>
						</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					echo "<tr>";
					echo "<td>
							<label class='control control--checkbox'>
								<input type='checkbox' name='cb_".$i."' value='".$row[0]."' />
								<div class='control__indicator'></div>
							</label>
						</td>";
					for($j=1; $j<7; $j++)
						echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br /><br /><div style='float: right;'>";
				echo "<input type='submit' value='Отклонить' name='l_reject' />&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input type='submit' value='Принять' name='l_grant'/>";
				echo "</div>";
				echo "</form>";
			}
			
			$header = 'From: <noreply@library.com>' . "\r\n";
			
			if(isset($_POST['l_grant']))
			{
				$inserts = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$insert_id =  $_POST['cb_'.$i];
						$query = $con->prepare("SELECT member, book_isbn, book_title, book_author, book_category FROM pending_book_insert WHERE insert_id = ?;");
						$query->bind_param("d", $insert_id);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						$title= $resultRow[2];
						$author= $resultRow[3];
						$category= $resultRow[4];

						$query = $con->prepare("INSERT INTO book(isbn, title, author, category) VALUES(?, ?, ?, ?);");
						$query->bind_param("ssss", $isbn, $title, $author, $category);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t insert values"));
						$inserts++;

						$query = $con->prepare("SELECT email FROM member WHERE username = ?;");
						$query->bind_param("s", $member);
						$query->execute();
						$to = mysqli_fetch_array($query->get_result())[0];

						$subject = "Книга добавлена в электронную библиотеку";
						$message = "Вы можете сдать книгу '".$title."' с Id ".$isbn." в течении недели в любой пункт букшеринга. Баллы за книгу вам автоматически начислены. Спасибо!";;	


						$query = $con->prepare("DELETE FROM pending_book_insert WHERE insert_id = ?");
						$query->bind_param("d", $insert_id);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));

						$query = $con->prepare("UPDATE member SET balance = balance + 5 WHERE username = ?;");
						$query->bind_param("s", $member);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));
						mail($to, $subject, $message, $header);
					}
				}
				
				if($inserts > 0)
					echo success("Выполнено ".$inserts."запросов");
				else
					echo error_without_field("Запрос не выполнен");
			}
			
			if(isset($_POST['l_reject']))
			{
				$inserts = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$inserts++;
						$insert_id =  $_POST['cb_'.$i];
						
						$query = $con->prepare("SELECT * FROM pending_book_insert WHERE insert_id = ?;");
						$query->bind_param("d", $insert_id);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						$title= $resultRow[2];
						$author= $resultRow[3];
						$category= $resultRow[4];
						
						$query = $con->prepare("SELECT email FROM member WHERE username = ?;");
						$query->bind_param("s", $member);
						$query->execute();
						$to = mysqli_fetch_array($query->get_result())[0];
						$subject = "Сдача книги отклонена";
						$message = "Ваш запрос на сдачу книги'".$title."' с Id".$isbn." отклонен. Свяжитесь с администатором";
						
						$query = $con->prepare("DELETE FROM pending_book_insert WHERE insert_id = ?");
						$query->bind_param("d", $insert_id);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));
						mail($to, $subject, $message, $header);
					}
				}
				if($inserts > 0)
					echo success("Удалены ".$inserts." запросы");
				else
					echo error_without_field("Запрос не выбран");
			}