<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "../verify_logged_out.php";
	require "../header.php";
?>

<html>
	<head>
		<title>Онлайн-платформа</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css">
		<link rel="stylesheet" type="text/css" href="css/index_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
		
		<center><legend>Вход администратора</legend></center>

			<div class="error-message" id="error-message">
				<p id="error"></p>
			</div>
			
			<div class="icon">
				<input class="l-user" type="text" name="l_user" placeholder="Логин" required />
			</div>
			
			<div class="icon">
				<input class="l-pass" type="password" name="l_pass" placeholder="Пароль" required />
			</div>
			
			<input type="submit" value="Войти" name="l_login"/>

			
			
		</form>
		<p align="center"><a href="../index.php" style="text-decoration:none;">Назад</a>
	</body>
	
	<?php
		if(isset($_POST['l_login']))
		{
			$query = $con->prepare("SELECT id FROM librarian WHERE username = ? AND password = ?;");
			$l_user = $_POST['l_user'];
			$l_pass = $_POST['l_pass'];
			$query->bind_param("ss", $l_user, $l_pass);
			$query->execute();
			if(mysqli_num_rows($query->get_result()) != 1)
				echo error_without_field("Invalid username/password combination");
			else
			{
				$_SESSION['type'] = "librarian";
				$_SESSION['id'] = mysqli_fetch_array($result)[0];
				$_SESSION['username'] = $_POST['l_user'];
				header('Location: home.php');
			}
		}
	?>
	
</html>