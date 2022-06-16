<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Онлайн-платформа букшеринга</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_checkbox_style.css">
		<link rel="stylesheet" type="text/css" href="css/pending_book_requests_style.css">
	</head>
	<body>
		<?php
			$query = $con->prepare("SELECT * FROM pending_book_requests;");
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
							<th>Книга<hr></th>
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
					for($j=1; $j<4; $j++)
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
				$requests = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$request_id =  $_POST['cb_'.$i];
						$query = $con->prepare("SELECT member, book_isbn, time FROM pending_book_requests WHERE request_id = ?;");
						$query->bind_param("d", $request_id);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						$time = $resultRow[2];
						$requests++;
						
						$query = $con->prepare("SELECT email FROM member WHERE username = ?;");
						$query->bind_param("s", $member);
						$query->execute();
						$to = mysqli_fetch_array($query->get_result())[0];
						$subject = "Заявка на выдачу книги успешна";
						
						$query = $con->prepare("SELECT title FROM book WHERE isbn = ?;");
						$query->bind_param("s", $isbn);
						$query->execute();
						$title = mysqli_fetch_array($query->get_result())[0];

						$message = "Книга '".$title."' с Id ".$isbn." ждет выдачи с даты отправки письма в пункте".$time.".";	


						$query = $con->prepare("SELECT copies FROM book WHERE isbn = ?;");
						$query->bind_param("s", $isbn);
						$query->execute();
						$copies = mysqli_fetch_array($query->get_result())[0];

						if ($copies>1){
								$query = $con->prepare("UPDATE book SET copies = copies - 1 WHERE isbn = ?;");
								$query->bind_param("s", $isbn);
								if(!$query->execute())
									die(error_without_field("ERROR: Couldn\'t delete values"));
						}
						else {
								$query = $con->prepare("DELETE FROM book WHERE isbn = ?");
								$query->bind_param("s", $isbn);
								if(!$query->execute())
									die(error_without_field("ERROR: Couldn\'t delete values"));
						}
					

						$query = $con->prepare("UPDATE member SET balance = balance - 5 WHERE username = ?;");
						$query->bind_param("s", $member);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));

						$query = $con->prepare("DELETE FROM pending_book_requests WHERE request_id = ?");
						$query->bind_param("d", $request_id);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));
						mail($to, $subject, $message, $header);

					}
				}

				if($requests > 0)
					echo success("Успешно выполнены ".$requests."запросы");
				else
					echo error_without_field("Запрос не выполнен");
			}
			
			if(isset($_POST['l_reject']))
			{
				$requests = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$requests++;
						$request_id =  $_POST['cb_'.$i];
						
						$query = $con->prepare("SELECT member, book_isbn FROM pending_book_requests WHERE request_id = ?;");
						$query->bind_param("d", $request_id);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						
						$query = $con->prepare("SELECT email FROM member WHERE username = ?;");
						$query->bind_param("s", $member);
						$query->execute();
						$to = mysqli_fetch_array($query->get_result())[0];
						$subject = "Выдача книги отклонена";
						
						$query = $con->prepare("SELECT title FROM book WHERE isbn = ?;");
						$query->bind_param("s", $isbn);
						$query->execute();
						$title = mysqli_fetch_array($query->get_result())[0];
						$message = "Ваш запрос на выдачу книги'".$title."' с Id".$isbn." отклонен. Свяжитесь с администатором";
						
						$query = $con->prepare("DELETE FROM pending_book_requests WHERE request_id = ?");
						$query->bind_param("d", $request_id);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));
						mail($to, $subject, $message, $header);
						
					}
				}
				if($requests > 0)
					echo success("Удалены ".$requests." запросы");
				else
					echo error_without_field("Запрос не выбран");
			}