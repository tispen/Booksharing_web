<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "../header.php";
?>

<html>
	<head>
		<title>Онлайн-платформа букшеринга</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css">
		<link rel="stylesheet" href="css/register_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
			<center><legend>Регистрация пользователя</legend><p>Заполните форму, не оставляя пустых полей латинскими буквами:</p></center>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>

				<div class="icon">
					<input class="m-name" type="text" name="m_name" placeholder="Ваше ФИО латинскими буквами" required />
				</div>

				<div class="icon">
					<input class="m-email" type="email" name="m_email" id="m_email" placeholder="Email" required />
				</div>
				
				<div class="icon">
					<input class="m-user" type="text" name="m_user" id="m_user" placeholder="Логин" required />
				</div>
				
				<div class="icon">
					<input class="m-pass" type="password" name="m_pass" placeholder="Пароль" required />
				</div>
				<br />
				<input type="submit" name="m_register" value="Зарегистироваться" />
		</form>
	<?php
		if(isset($_POST['m_register']))
		{
			$m_pass = $_POST['m_pass'];
			$m_name = $_POST['m_name'];
			$m_email = $_POST['m_email'];
			$query = $con->prepare("SELECT username FROM member WHERE username = ?);");
			$query->bind_param("s",$_POST['m_user']);
			$query->execute();
			if(!$query->execute())
				echo error_with_field("Пользователь с таким логином уже существует", "m_user");	
			else {
				$query = $con->prepare("SELECT email FROM member WHERE email = ?);");
				$query->bind_param("s", $m_email);
				$query->execute();
				if(mysqli_num_rows($query->get_result()) != 0)
				echo error_with_field("Аккаунт с такой почтой уже создан", "m_email");
				else {

					$query = $con->prepare("INSERT INTO pending_registrations(username, password, name, email) VALUES(?, ?, ?, ?);");
					$query->bind_param("ssss", $m_user, $m_pass, $m_name, $m_email);
					$query->execute();
					if($query->execute())
						echo success("Отлично, ваша заявка уже проверяется! Вскоре вам на почту придет уведомление.");
					else
						echo error_without_field("Couldn\\'t Попробуйте позже");
				}
			}
		}
	?>
</body>
</html>