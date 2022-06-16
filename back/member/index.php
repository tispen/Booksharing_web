<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "../verify_logged_out.php";
	require "../header.php";
?>

<html>
	<head>
		<title>Онлайн-платформа букшеринга</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css">
		<link rel="stylesheet" type="text/css" href="css/index_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
		
		<center><legend>Вход пользователя</legend></center>
			
			<div class="error-message" id="error-message">
				<p id="error"></p>
			</div>
			
			<div class="icon">
				<input class="m-user" type="text" name="m_user" placeholder="Логин" required />
			</div>
			
			<div class="icon">
				<input class="m-pass" type="password" name="m_pass" placeholder="Пароль" required />
			</div>
			
			<input type="submit" value="Войти" name="m_login" />
			
			<br /><br /><br /><br />
			
			<p align="center">У вас нет аккаунта?&nbsp;<a href="register.php" style="text-decoration:none; color:red;">Зарегистрируйтесь!</a>

			<p align="center"><a href="../index.php" style="text-decoration:none;">Назад</a>
		</form>
	
	<?php
		if(isset($_POST['m_login']))
		{
			$m_login = $_POST['m_user'];
			$m_pass = $_POST['m_pass'];
			$query = $con->prepare("SELECT id, balance FROM member WHERE username = ? AND password = ?;");
			$query->bind_param("ss", $m_login, $m_pass);
			$query->execute();
			$result = $query->get_result();
			
			$resultRow = mysqli_fetch_array($result);
			$balance = $resultRow[1];
			if($balance < 0){
				echo error_without_field("Ваш аккаунт заблокирован! Уточните у администратора.");
			}
			else {
					$_SESSION['type'] = "member";
					$_SESSION['id'] = $resultRow[0];
					$_SESSION['username'] = $_POST['m_user'];
					header('Location: home.php');
			}
		}
	?>
</body>
</html>