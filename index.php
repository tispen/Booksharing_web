<!DOCTYPE html>
<html lang="en">
<head>
	<?php include 'front/includes/head.php';?>
</head>
<body>
	<?php include 'front/includes/navbar.php';?>

	<div class="carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active"><img src="front/img/moscow.png"></div>
		</div>
	</div>
	<div class="container">
		<div class="row justify-content-center text-center">
			<div class="col-10 py-5">
				<h2>Крупнейший шеринг бумажными книгами в Москве</h2>
				<p class="lead">Наша платформа позволяет делиться своими печатными книгами с друзьями и другими людьми в своем городе. Делись своими книгами, обменивай на новые и экономь! </p><a class="btn btn-purple btn-lg" href="back/index.php" target="_blank">Начать!</a>
			</div>
		</div>
	</div>

	<!--- Start Jumbotron -->
	<div class="jumbotron">
		<div class="container">
			<h2 class="text-center pt-5 pb-3">Почему тебе это понравится?</h2>
			<div class="row justify-content-center text-center">
				<div class="col-10 col-md-4">
					<div class="feature">
						<img src="front/img/books.svg">
						<h3>Освободи место для новых книг</h3>
						<p>Расхлами свои книжные полки. Находи новые книги и ставь их на место старых.</p>
					</div>
				</div>
				<div class="col-10 col-md-4">
					<div class="feature">
						<img src="front/img/love.svg">
						<h3>Найди ту самую книгу</h3>
						<p>В частных библиотеках других москвичей часто можно найти редкие и интересные книги.</p>
					</div>
				</div>
				<div class="col-10 col-md-4">
					<div class="feature">
						<img src="front/img/mon.svg">
						<h3>Читай бесплатно</h3>
						<p>Книги дорожают с каждым годом. Обмен книгами - отличная идея сохранить деньги на покупку новых полок для книг.</p>
					</div>
				</div>
			</div><!--- End Row -->
		</div><!--- End Container -->
	</div>
	<!--- End Jumbotron -->

	<?php include 'front/includes/footer.php';?>

	<?php include 'front/includes/scripts.php';?>

</body>
</html>