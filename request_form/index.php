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

            if ($hierarquia == COLABORADOR || $hierarquia == COORDENADOR) {
                
                $superiores = determinaSuperiores($db, $username, $hierarquia);
            }

            $contador = count($superiores);

            $tipo = $datas = $motivo = "";

            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $tipo = testInput($_POST["tipo"]);
                $datas = testInput($_POST["datas"]);
                $motivo = testInput($_POST["motivo"]);

                submeteRequerimento($db, $username, $tipo, $datas, $contador, $motivo);
            }
        ?>
        
        <h1>Pedido de dispensa</h1>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <h3>Tipo</h3>
            <label for="ausencia">Ausência</label>  
            <input type="radio" name="tipo" id="ausencia" value="ausencia" checked>
            <br />
            <label for="ferias">Férias</label>  
            <input type="radio" name="tipo" id="ferias" value="ferias">
            <br />

            <h3>Período</h3>
            <input type="text" name="datas">
            
            <h3>Motivo</h3>
            <textarea name="motivo" rows="3" cols="30" placeholder="Motivo a que se deve o requerimento..."></textarea>
            <br />
            <br />
            <input type="submit">
        </form>
        
    </body>
</html> 