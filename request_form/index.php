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
		<img src="https://www.aibili.pt/ficheiros/Logo_AIBILI_portugus.jpg" alt="Logo_AIBILI_portugus" width="250" height="50" >
	
		<h1>Pedido de dispensa</h1>

 		<form method="post" >
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

			require 'php/requerimento.php';

			$define("ADMIN", 0);

			$dbtype = "mysql";
			$host = "localhost";
			$port = 3306;
			$dbname = "aibili_web_attn";
			$user = "root";
			$password = "";

			$username = "dfcoimbra";

			echo("Autenticar...<br/>");



			echo ("Determinar supervisores...<br />");

			$db = connect($dbtype, $host, $port, $dbname, $user, $password);

			$sql = "SELECT supervisor FROM supervisiona WHERE colaborador = 'dfcoimbra';";

			$result = $db->query($sql);

			foreach ($result as $row) {
				
				echo ($row['supervisor'] . "<br />");
			}

			echo("Submeter requerimento...<br />");
		?>
	</body>
</html> 