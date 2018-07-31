<!DOCTYPE html>
<html lang="pt-PT">
    <head>
        <title>Requerimentos pendentes</title>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body>
        <?php
            require_once 'lib.php';  
            require_once 'setup.php';

            include_once 'header.php';

            $db->query("START TRANSACTION;");

            $requerimentos = requerimentosColaborador($db, $username);

            $db->query("COMMIT;");

            echo("<h3>Os meus requerimentos</h3>");

            echo("<table style='width:75%'>
                    <tr>
                        <th>ID</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Estado</th>
                        <th>Observações</th>
                    </tr>");

            foreach($requerimentos as $row) {

                echo("<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['inicio']}</td>
                        <td>{$row['fim']}</td>
                        <td>{$row['estado']}</td>
                        <td>{$row['observacoes']}</td>
                      </tr>");
            }

            echo("</table>");
        ?>
    </body>
</html>