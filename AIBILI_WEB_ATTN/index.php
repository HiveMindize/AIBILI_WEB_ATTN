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

            $str1 = $str2 = $str3 = $str4 = $str5 = $str6 = $str7 = null;

            if ($hierarquia == COLABORADOR) {

            	$str1 = "Pedir dispensa";
            	$str2 = "Os meus requerimentos";
            	$str3 = "Mapa de assiduidade";
            }

            else if ($hierarquia == COORDENADOR) {

            	$str1 = "Pedir dispensa";
            	$str2 = "Os meus requerimentos";
            	$str3 = "Mapa de assiduidade";
            	$str4 = "Requerimentos pendentes";
            	$str5 = "Requerimentos das minha equipas";
            }

            else if ($hierarquia == DIRETOR) {

            	$str1 = "Pedir dispensa";
            	$str2 = "Os meus requerimentos";
            	$str3 = "Mapa de assiduidade";
            	$str4 = "Requerimentos pendentes";
            	$str5 = "Requerimentos das minhas equipas";
            	$str6 = "Requerimentos da minha unidade";
            }

            else if ($hierarquia == FINANCEIRO) {

            	$str1 = "Marcar dispensa";
            	$str3 = "Mapa de assiduidade";
            	$str7 = "Ver requerimentos";
            }

            else if ($hierarquia == CEO) {

            	$str1 = "Marcar dispensa";
            	$str3 = "Mapa de assiduidade";
            	$str4 = "Requerimentos pendentes";
            	$str7 = "Ver requerimentos";
            }

       
       	 	echo("<a href='php/request_form.php'>$str1</a><br />");
       	 	echo("<a href='php/my_requests.php'>$str2</a><br />");
       	 	echo("<a href='php/attendance_map.php'>$str3</a></br />");
       	 	echo("<a href='php/pending_requests.php'>$str4</a></br />");
       	 	echo("<a href='php/team_requests.php'>$str5</a></br />");
       	 	echo("<a href='php/department_requests.php'>$str6</a></br />");
       	 	echo("<a href='php/all_requests.php'>$str7</a></br />");
       	 ?>
	</body>
</html>