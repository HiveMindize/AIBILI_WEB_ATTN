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

            $requerimentos = requerimentosPendentes($db, $username, $hierarquia);

            $db->query("COMMIT;");

            if ($hierarquia != COLABORADOR && $hierarquia != FINANCEIRO) {

                if (isset($_GET['id']) && isset($_GET['decisao'])) {
                
                    $id = testInput($_GET['id']);
                    $decisao = testInput($_GET['decisao']);

                    $db->query("START TRANSACTION;");

                    avaliaRequerimento($db, $decisao, $username, $hierarquia, $id);

                    $db->query("COMMIT;");


                    header('Location: pending_requests.php');
                }
            }

            echo("<h3>Requerimentos</h3>");

            echo("<table style='width:75%'>
                    <tr>
                        <th>ID</th>
                        <th>Colaborador</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Estado</th>
                        <th>Observações</th>
                    </tr>");

            foreach($requerimentos as $row) {

                echo("<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['colaborador']}</td>
                        <td>{$row['inicio']}</td>
                        <td>{$row['fim']}</td>
                        <td>{$row['estado']}</td>
                        <td>{$row['observacoes']}</td>");

                if ($hierarquia != COLABORADOR && $hierarquia != FINANCEIRO) {

                    echo("<td><a href=\"pending_requests.php?id={$row['id']}&decisao=APROVADO\">Aprovar</a>
                            <br />
                            <a href=\"pending_requests.php?id={$row['id']}&decisao=REJEITADO\">Rejeitar</a></td>");
                }

                echo("</tr>");
            }

            echo("</table>");
        ?>
    </body>
</html>