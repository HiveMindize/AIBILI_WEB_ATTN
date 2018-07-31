<!DOCTYPE html>
<html lang="pt-PT">
    <head>
        <title>Pedido de dispensa</title>
        <meta charset="UTF-8">

        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script type="text/javascript" src="../js/form.js"></script>
    </head>
    
    <body>
        <?php
            require_once 'lib.php';  
            require_once 'setup.php';

            include_once 'header.php';
            include_once 'upload.php';

            if ($hierarquia != CEO && $hierarquia != FINANCEIRO) {
                
                $superiores = determinaSuperiores($db, $username, $hierarquia);

                $hierarquia_superiores = authenticate($db, $superiores[0]);

                $tipo = $datas = $motivo = "";

                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                    $tipo = testInput($_POST["tipo"]);
                    $datas = explode(" - ", testInput($_POST["datas"]));
                    $motivo = testInput($_POST["motivo"]);

                    $id = uniqid($username);

                    if ($tipo === "ausencia") {
                        $path = uploadFiles($id);
                    }

                    submeteRequerimento($db, $id, $hierarquia_superiores, $username, $tipo, $datas, $superiores, $motivo, $path);
                }
            }
        ?>
        
        <h1>Pedido de dispensa</h1>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  enctype="multipart/form-data">
            <h3>Tipo</h3>
            <label for="ausencia">Ausência</label>  
            <input type="radio" name="tipo" id="ausencia" value="ausencia" checked>
            
            <label for="ferias">Férias</label>  
            <input type="radio" name="tipo" id="ferias" value="ferias">

            <h3>Período</h3>
            <input type="text" name="datas">
            
            <h3>Motivo</h3>
            <textarea name="motivo" rows="3" cols="30" placeholder="Motivo a que se deve o requerimento..."></textarea>
            <br />

            <div id="upload">
                <h4>Documentos</h4>
                <input type="file" name="upload[]" id="upload" multiple>
            </div>
            <br />
            <br />

            <input type="submit">
        </form>
    </body>
</html> 