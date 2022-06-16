<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_member.php";
	require "header_member.php";
?>

<html>
	<head>
		<title>Онлайн-платформа букшеринга</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="css/home_style.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_radio_button_style.css">
	</head>
	<body>
		<?php
			$query = $con->prepare("SELECT * FROM book ORDER BY title");
			$query->execute();
			$result = $query->get_result();
			if(!$result)
				die("ERROR: Couldn't fetch books");
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>Пусто</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<center><legend>Доступные книги для заказа</legend></center>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding=10 cellspacing=10>";
				echo "<tr>
						<th></th>
						<th>ISBN<hr></th>
						<th>Название<hr></th>
						<th>Автор<hr></th>
						<th>Жанр<hr></th>
						<th>Цена в баллах<hr></th>
						<th>Количество копий<hr></th>
					</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					echo "<tr>
							<td>
								<label class='control control--radio'>
									<input type='radio' name='rd_book' value=".$row[0]." />
								<div class='control__indicator'></div>
							</td>";
					for($j=0; $j<6; $j++)
						if($j == 4)
							echo "<td>".$row[$j]."</td>";
						else
							echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br /><br /><input type='submit' name='m_request' value='Взять книгу' />";
				echo "</form>";
			}
			
			if(isset($_POST['m_request']))
			{
				if(empty($_POST['rd_book']))
					echo error_without_field("Выберите книгу для заказа");
				else
				{
					$query = $con->prepare("SELECT copies FROM book WHERE isbn = ?;");
					$query->bind_param("s", $_POST['rd_book']);
					$query->execute();
					$copies = mysqli_fetch_array($query->get_result())[0];
					if($copies == 0)
						echo error_without_field("Книгу кто-то читает!");
					else
					{
						$query = $con->prepare("SELECT request_id FROM pending_book_requests WHERE member = ?;");
						$query->bind_param("s", $_SESSION['username']);
						$query->execute();
						if(mysqli_num_rows($query->get_result()) == 1)
							echo error_without_field("Вы можете взять только одну книгу за раз");
						else
						{
								$query = $con->prepare("SELECT balance FROM member WHERE username = ?;");
								$query->bind_param("s", $_SESSION['username']);
								$query->execute();
								$memberBalance = mysqli_fetch_array($query->get_result())[0];
								
								$query = $con->prepare("SELECT price FROM book WHERE isbn = ?;");
								$query->bind_param("s", $_POST['rd_book']);
								$query->execute();
								$bookPrice = mysqli_fetch_array($query->get_result())[0];
								if($memberBalance < $bookPrice)
									echo error_without_field("У вас недостаточно баллов");
								else
								{
									$query = $con->prepare("INSERT INTO pending_book_requests(member, book_isbn) VALUES(?, ?);");
									$query->bind_param("ss", $_SESSION['username'], $_POST['rd_book']);
									if(!$query->execute())
										echo error_without_field("ERROR: Couldn\'t request book");
									else
										echo success("Книга запрошена. Вам на почту придет уведомление с адресом пукнта выдачию Также книга появится в спике ваших книг");
									}
								}
							}
					}
			}
		?>
	</body>
</html>