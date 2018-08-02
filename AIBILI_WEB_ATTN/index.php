<!DOCTYPE html>
<html lang="pt-PT">
    <head>
        <title>AIBILI WEB ATTN</title>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
    	<?php
    		require_once 'php/lib.php';  
            require_once 'php/setup.php';

            include_once 'php/header.php';

            echo("<a href='php/request_form.php'>Pedir dispensa</a><br />");
            echo("<a href='php/my_requests.php'>Ver requerimentos</a><br />");
            echo("<a href='php/attendance_map.php'>Mapa de assiduidade</a></br />");

            if ($hierarquia != FINANCEIRO && $hierarquia != COLABORADOR) {

            	echo("<a href='php/pending_requests.php'>Requerimentos pendentes</a></br />");
            }
       	 ?>
	</body>
</html>