<!DOCTYPE html>
<html lang="pt-PT">
	<head>
		<title>Pedido de dispensa</title>
		<meta charset="UTF-8">

		<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
		<script type="text/javascript" src="js/datepicker.js"></script>
	</head>
	
	<body>

		<?php
			require 'php/lib.php';	
			require 'php/setup.php';

			include 'php/header.php';

			$superiores = determinaSuperiores($db, $username, $hierarquia);

			echo(implode($superiores));

			$type = $dates = $motive = "";

			if ($_SERVER["REQUEST_METHOD"] == "POST") {

				$type = test_input($_POST["type"]);
				$dates = test_input($_POST["dates"]);
				$motive = test_input($_POST["motive"]);
			}
		?>
		
	
		<h1>Pedido de dispensa</h1>

 		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<h3>Tipo</h3>
			<label for="absence">Ausência</label>	
			<input type="radio" name="type" id="absence" value="absence" checked>
			<br />
			<label for="vacation">Férias</label>	
			<input type="radio" name="type" id="vacation" value="vacation">
			<br />

			<h3>Período</h3>
			<input type="text" name="dates">
			
			<h3>Motivo</h3>
			<textarea name="motive" rows="3" cols="30" placeholder="Motivo a que se deve o requerimento..."></textarea>
			<br />
			<br />
			<input type="submit">
		</form>

		<?php
			echo($type . '<br />');
			echo($dates . '<br />');
			echo($motive . '<br />');

		?>

		
	</body>
</html> 