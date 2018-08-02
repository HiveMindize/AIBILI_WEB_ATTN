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

            $meus_requerimentos = requerimentosColaborador($db, $username);

            if (is_ceo($db, $username) || is_financeiro($db, $username)) {

                $requerimentos_organizacao = requerimentosOrganizacao($db);
            }

            if (is_diretor($db, $username)) {

                $unidade = getUnidadePorDiretor($db, $username);
                $requerimentos_unidade = requerimentosUnidade($db, $unidade); 
            }

            if (is_coordenador($db, $username)) {

                $requerimentos_equipas = requerimentosEquipas($db, $username);
            }

            $db->query("COMMIT;");

            if (isset($meus_requerimentos)) {

                echo("<h3>Os meus requerimentos</h3>");

                echo("<table style='width:75%'>
                        <tr>
                            <th>ID</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Estado</th>
                            <th>Observações</th>
                        </tr>");

                foreach($meus_requerimentos as $row) {

                    echo("<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['inicio']}</td>
                            <td>{$row['fim']}</td>
                            <td>{$row['estado']}</td>
                            <td>{$row['observacoes']}</td>");

                    if ($row['estado'] === "PENDENTE") {

                        echo("<td><a href=\"my_requests.php?id={$row['id']}\">Cancelar</a></td>");
                    }

                    echo("</tr>");
                }

                echo("</table>");
            }

            if (isset($requerimentos_organizacao)) {

                echo("<h3>Requerimentos da minha unidade</h3>");

                tabelaRequerimentos($requerimentos_organizacao);
            }

            if (isset($requerimentos_unidade)) {

                echo("<h3>Requerimentos da minha unidade</h3>");

                tabelaRequerimentos($requerimentos_unidade);
            }

            if (isset($requerimentos_equipas)) {

                echo("<h3>Requerimentos das minhas equipas</h3>");

                tabelaRequerimentos($requerimentos_equipas);
            }

            if (isset($_GET['id'])) {

                $id = testInput($_GET['id']);

                $db->query("START TRANSACTION;");

                cancelaRequerimento($db, $id);

                $db->query("COMMIT;");

                header('Location: my_requests.php');
            }

        ?>
    </body>
</html>