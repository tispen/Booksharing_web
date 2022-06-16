<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>LMS</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_checkbox_style.css">
		<link rel="stylesheet" type="text/css" href="css/pending_registrations_style.css">
	</head>
	<body>
		<?php
			$query = $con->prepare("SELECT username, name, email FROM pending_registrations");
			$query->execute();
			$result = $query->get_result();
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>Нет запросов</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<center><legend>Управление запросами на регистрацию</legend></center>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding=10 cellspacing=10>
						<tr>
							<th></th>
							<th>Логин<hr></th>
							<th>ФИО<hr></th>
							<th>Email<hr></th>>
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
					$j;
					for($j=0; $j<3; $j++)
						echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table><br /><br />";
				echo "<div style='float: right;'>";
				
				echo "<input type='submit' value='Принять' name='l_confirm' />&nbsp;&nbsp;&nbsp;";
				echo "<input type='submit' value='Отклонить' name='l_delete' />";
				echo "</div>";
				echo "</form>";
			}
			
			$header = 'From: <noreply@libraryms.com>' . "\r\n";
			
			if(isset($_POST['l_confirm']))
			{
				$members = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$username =  $_POST['cb_'.$i];
						$query = $con->prepare("SELECT * FROM pending_registrations WHERE username = ?;");
						$query->bind_param("s", $username);
						$query->execute();
						$row = mysqli_fetch_array($query->get_result());
						
						$query = $con->prepare("INSERT INTO member(username, password, name, email) VALUES(?, ?, ?, ?);");
						$query->bind_param("ssss", $username, $row[1], $row[2], $row[3]);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t insert values"));
						$members++;
						
						$to = $row[3];
						$subject = "Ваша заявка одобрена";
						$message = "Ваша заявка одобрена. Теперь вы можете обмениваться книгами, используя свою учетную запись";

						$query = $con->prepare("DELETE FROM pending_registrations WHERE username = ?");
						$query->bind_param("d", $username);
						mail($to, $subject, $message, $header);
					}
				}
				if($members > 0)
					echo success("Успешно добавлены ".$members." участники");
				else
					echo error_without_field("Нет");
			}
			
			if(isset($_POST['l_delete']))
			{
				$requests = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$username =  $_POST['cb_'.$i];
						$query = $con->prepare("SELECT email FROM pending_registrations WHERE username = ?;");
						$query->bind_param("s", $username);
						$query->execute();
						$email = mysqli_fetch_array($query->get_result())[0];
						
						$query = $con->prepare("DELETE FROM pending_registrations WHERE username = ?;");
						$query->bind_param("s", $username);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));
						$requests++;
						
						$to = $email;
						$subject = "Ваша заявка отклонена";
						$message = "Ваша заявка отклонена. Свяжитесь с администратором.";

						
						$query = $con->prepare("DELETE FROM pending_registrations WHERE username = ?");
						$query->bind_param("d", $username);
						mail($to, $subject, $message, $header);
					}
				}
				if($requests > 0)
					echo success("Удалены ".$requests." запросы");
				else
					echo error_without_field("Пусто");
			}
		?>
	</body>
</html>