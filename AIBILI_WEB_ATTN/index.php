<!DOCTYPE html>
<html lang="pt-PT">
    <head>
        <title>AIBILI WEB ATTN</title>
        <meta charset="UTF-8">
    </head>

    <body>
    	<?php
    		require_once 'php/lib.php';  
            require_once 'php/setup.php';

            include_once 'php/header.php';
       
       		if ($hierarquia != CEO && $hierarquia != FINANCEIRO) {

			    echo("<a href='php/request_form.php'>Pedir dispensa</a>");
			}

			else {

				echo("<a href='php/request_form.php'>Registar ausência</a>");
			}
			
			echo("<br />");
		?>
	    <a href="php/get_requests.php">Requerimentos pendentes</a>
	    <br />
	    <a href="php/get_approved_requests.php">Requerimentos aprovados</a>
	    <br />
	    <a href="php/vacation_map.php">Mapa de férias</a>
	</body>
</html>