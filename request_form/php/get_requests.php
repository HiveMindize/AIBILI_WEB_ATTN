<!DOCTYPE html>
<html lang="pt-PT">
    <head>
        <title>Pedido de dispensa</title>
        <meta charset="UTF-8">

        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script type="text/javascript" src="../js/datepicker.js"></script>
    </head>

    <body>
        <?php
            require_once 'lib.php';  
            require_once 'setup.php';

            include_once 'header.php';

            $db->query("START TRANSACTION;");

            $requerimentos = consultaRequerimentos($db, $username, $hierarquia);

            $db->query("COMMIT;");

            if ($hierarquia != COORDENADOR) {

                if (isset($_GET['id']) && isset($_GET['decisao'])) {
                
                    $id = testInput($_GET['id']);
                    $decisao = testInput($_GET['decisao']);

                    $db->query("START TRANSACTION;");

                    avaliaRequerimento($db, $decisao, $username, $hierarquia, $id);

                    $db->query("COMMIT;");
                }
            }
        ?>

        <h3>Requerimentos</h3>

        <?php

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
                        <td>{$row['observacoes']}</td>
                        <td><a href=\"get_requests.php?id={$row['id']}&decisao=aprovado\">Aprovar</a>
                            <br />
                            <a href=\"get_requests.php?id={$row['id']}&decisao=rejeitado\">Rejeitar</a></td>
                      </tr>");
            }
        ?>

        </table>
    </body> 