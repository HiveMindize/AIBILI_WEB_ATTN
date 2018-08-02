<?php
define("CEO", 4);
define("FINANCEIRO", 3);
define("DIRETOR", 2);
define("COORDENADOR", 1);
define("COLABORADOR", 0);

// connect
// argumentos: $dbtype: string: tipo de base de dados: MySQL, PostgreSQL, etc.
//             $host: string: endereço IP do servidor de bases de dados
//             $port: inteiro: porto de ligacao a base de dados
//             $dbname: string: nome da base de dados a usar
//             $user: string: nome de utilizador para login na base de dados
//             $password: string: para login na base de dados
// retorno: PDO: permite fazer queries na base de dados especificada
function connect($dbtype, $host, $port, $dbname, $user, $password) {

    try {

        $db = new PDO($dbtype . ':host=' . $host . ';port=' . $port . ';dbname=' . $dbname . ';', $user, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }

    catch (PDOException $e) {
        echo($e->getMessage());
    }
}


// uploadFiles
// argumento: string: $id identificador unico do requerimento a que se associam os ficheiros
// retorno: string: caminho para a pasta onde of ficheiros se encontram
function uploadFiles($id) {
        
    // Count # of uploaded files in array
    $total = count($_FILES['upload']['name']);

    mkdir("../docs/$id/");

    // Loop through each file
    for($i = 0; $i < $total; $i++) {

        // Get the temp file path
        $tmpFilePath = $_FILES['upload']['tmp_name'][$i];

        // Make sure we have a file path

        if ($tmpFilePath != "") {
        
            // Setup our new file path
            $newFilePath = "../docs/$id/" . $_FILES['upload']['name'][$i];

            // Upload the file into the new dir
            move_uploaded_file($tmpFilePath, $newFilePath);
        }
    }

    return $_SERVER['DOCUMENT_ROOT'] . "/docs/$id/";
}

// authenticate
// argumentos: $db: PDO: para a base de dados usada
//             $username: string:  utilizador a autenticar
// retorno: inteiro: nível hierarquico do colaborador, ou erro se nao existir
function authenticate($db, $username) {

    //se o colaborador nao existir, termina o processo de autenticacao
    if (is_colaborador($db, $username)) {

        if (is_ceo($db, $username)) {

            return CEO; 
        }

        else if (is_financeiro($db, $username)) {

            return FINANCEIRO; 
        }

        else if (is_diretor($db, $username)) {

            return DIRETOR;
        }

        else if (is_coordenador($db, $username)) {

            return COORDENADOR;
        }

        else {

            return COLABORADOR;
        }
    }

    else  {

        exit ("Não foi possível verificar a identidade do utilizador $username. Verifique que está registado.<br />");
    }
}


// is_colaborador
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: utilizador a autenticar
// retorno: booleano: indica se o colaborador existe
function is_colaborador($db, $username) {

    $query = "SELECT EXISTS (SELECT * 
                             FROM colaborador 
                             WHERE username = :username) 
                             AS existe;";

    $parameters = array(':username' => $username);
    
    $result = execute($db, $query, $parameters);
    
    $row = $result->fetch();

    return $row['existe'];
}


// is_ceo
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: utilizador a autenticar
// retorno: booleano: indica se o colaborador e' CEO
function is_ceo($db, $username) {

    $query = "SELECT EXISTS (SELECT *
                             FROM administrador
                             WHERE username = :username
                             AND funcao = 'CEO') 
                             AS existe;";

    $parameters = array(':username' => $username);
    
    $result = execute($db, $query, $parameters);

    $row = $result->fetch();

    return $row['existe'];
}

// is_financeiro
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: utilizador a autenticar
// retorno: booleano: indica se o colaborador e' financeiro
function is_financeiro($db, $username) {

    $query = "SELECT EXISTS (SELECT *
                             FROM administrador
                             WHERE username = :username
                             AND funcao = 'financeiro') 
                             AS existe;";

    $parameters = array(':username' => $username);
    
    $result = execute($db, $query, $parameters);

    $row = $result->fetch();

    return $row['existe'];
}


// is_diretor
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: utilizador a autenticar
// retorno: booleano: indica se o colaborador e' diretor de unidade
function is_diretor($db, $username) {

    $query = "SELECT EXISTS (SELECT * 
                             FROM unidade 
                             WHERE diretor = :username) 
                             AS existe;";
    
    $parameters = array(':username' => $username);
    
    $result = execute($db, $query, $parameters);

    $row = $result->fetch();

    return $row['existe'];
}


// is_coordenador
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: utilizador a autenticar
// retorno: booleano: indica se o colaborador e' coordenador de equipa
function is_coordenador($db, $username) {

    $query = "SELECT EXISTS (SELECT * 
                             FROM supervisiona 
                             WHERE supervisor = :username) 
                             AS existe;";
    

    $parameters = array(':username' => $username);
    
    $result = execute($db, $query, $parameters);

    $row = $result->fetch();

    return $row['existe'];
}


// execute
// executa uma dada query MySQL através de prepared statements
// argumentos: $db: PDO: para a base de dados usada
//             $query: string: query MySQL a executar
//             $parameters: array(key => value) parametros a aplicar na query, default e' sem parametros
// retorno: PDOStatement:
function execute($db, $query, $parameters = array()) {

    try {

        $stmt = $db->prepare($query);
        $stmt->execute($parameters);

        return $stmt;
    }
    
    catch (PDOException $e) {
        
        $db->query("ROLLBACK;");
        exit ($e->getMessage());
    }
}


// determinaSuperiores
// determina os superiores hierarquicos no nível imediatamente superior ao utilizador dado
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: utilizador para o qual determinar superiores
//             $hierarquia: inteiro: nivel hierarquico do colaborador
// retorno: array string: usernames dos superiores hierarquicos do utilizador dado
function determinaSuperiores($db, $username, $hierarquia) {

    if ($hierarquia == COLABORADOR) {

        $superiores = determinaCoordenadores($db, $username);

        //se não houver supervisores, consideram-se os diretores das areas a que pertence
        if (!empty($superiores)) {

            return $superiores;
        }

         $superiores = determinaDiretores($db, $username);
    }

    //se for um coordenador, pode enviar diretamente para os diretores
    else if ($hierarquia == COORDENADOR) {

        $superiores = determinaDiretores($db, $username);
    }

    else if ($hierarquia == DIRETOR) {

        $superiores = getAdmins($db);
    }

    else if ($hierarquia == FINANCEIRO) {

        $superiores = getCEO($db);
    }

    if (empty($superiores)) {

      exit("O utilizador $username não tem superiores registados.<br />");
    }

    return $superiores;
}


// determinaCoordenadores
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: utilizador para o qual determinar superiores
// retorno: array string: usernames dos coordenadores do utilizador dado
function determinaCoordenadores($db, $username) {

    $query = "SELECT supervisor
              FROM supervisiona
              WHERE colaborador = :username;";

    $parameters = array(':username' => $username);

    $result = execute($db, $query, $parameters);

    $coordenadores = $result->fetchAll(PDO::FETCH_COLUMN);

    return $coordenadores;
}


// determinaDiretores
// argumentos: $db: PDO: paraa base de dados usada
//             $username: utilizador para o qual determinar diretores
// retorno: array string: usernames dos diretores das unidades a que o utilizador pertence
function determinaDiretores($db, $username) {

    $query = "SELECT diretor
              FROM pertence P INNER JOIN unidade U
              ON unidade = nome
              WHERE colaborador = :username;";

    $parameters = array(':username' => $username);

    $result = execute($db, $query,  $parameters);

    $diretores = $result->fetchAll(PDO::FETCH_COLUMN);

    return $diretores;
}

// getAdmins
// argumento: $db: PDO: para a base de dados usada
// retorno: array string: usernames dos administradores da organizacao
function getAdmins($db) {

    $query = "SELECT username
              FROM administrador;";

    $result = execute($db, $query);

    $admins = $result->fetchAll(PDO::FETCH_COLUMN);

    return $admins;
}

// getCEO
// argumento: $db: PDO: para a base de dados usada 
function getCEO($db) {

    $query = "SELECT username
              FROM administrador
              WHERE funcao = 'CEO';";

    $result = execute($db, $query);

    $ceo = $result->fetchColumn();

    return $ceo['username'];
}


// submeteRequerimento
// submete um requerimento de ausencia na base de dados
// argumentos: $db: PDO: para a base de dados usada
//             $nivel: inteiro: nivel de hierarquia necessario para avaliar o requerimento
//             $username: string: utilizador remetente do requerimento
//             $tipo: string: ausencia ou ferias
//             $datas: array string: datas de inicio e fim, em formato YYYY-MM-DD HH:ss
//             $destinatarios: array string: usernames dos destinatarios do requerimento
//             $motivo: string: motivo a que se deve o requerimento
function submeteRequerimento($db, $nivel, $username, $tipo, $datas, $destinatarios, $motivo) {

    $id = uniqid($username); // produz-se um ID unico a partir da timestamp atual

    //submete requerimento
    $query = "INSERT INTO requerimento(id, nivel, colaborador, inicio, fim, estado, observacoes)
              VALUES (:id, :nivel, :username, :inicio, :fim, 'PENDENTE', :observacoes);";

    $parameters = array(':id' => $id, ':nivel' => $nivel, ':username' => $username, ':inicio' => $datas[0], ':fim' => $datas[1], ':observacoes' => $motivo);

    execute($db, $query, $parameters);

    //submete tambem na tabela respetiva
    if ($tipo === "ausencia") {

        $path = uploadFiles($id);

        $query = "INSERT INTO requerimento_ausencia(id, url_doc)
                  VALUES (:id, :doc_url);";

        $parameters = array(':id' => $id, ':doc_url' => $path);
    }

    else if ($tipo === "ferias") {

        $query = "INSERT INTO requerimento_ferias(id)
                  VALUES (:id);";

        $parameters = array(':id' => $id);
    }

    execute($db, $query, $parameters);

    registaDestinatarios($db, $id, $destinatarios);
}

// cancelaRequerimento
// apaga um requerimento dos registos
// argumentos: $db: PDO: para a base de dados usada
//             $id: string: identificador unico do requerimento a cancelar
function cancelaRequerimento($db, $id) {

    $query = "DELETE FROM requerimento_ausencia
              WHERE id = :id;

              DELETE FROM requerimento_ferias
              WHERE id = :id;

              DELETE FROM aprovacoes_necessarias
              WHERE id = :id;

              DELETE FROM requerimento
              WHERE id = :id;";

    $parameters = array(':id' => $id);

    execute($db, $query, $parameters);
}


// registaDestinatarios
// guarda os usernames dos colaboradores que devem avaliar um requerimento
// argumentos: $db: PDO: para a base de dados usada
//             $id: string: identificador unico do requerimento
//             $destinatarios: array string: usernames dos colaboradores que vao avaliar o requerimento
function registaDestinatarios($db, $id, $destinatarios) {

    foreach($destinatarios as $destinatario) {

        $query = "INSERT IGNORE INTO aprovacoes_necessarias(id, username)
                  VALUES (:id, :username);";

        $parameters = array(':id' => $id, ':username' => $destinatario);

        execute($db, $query, $parameters);
    }
}


// requerimentosPendentes
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: utilizador que acede aos requerimentos
//             $hierarquia: inteiro: nivel hierarquico do utilizador
// retorna: array requerimentos (id, colaborador, periodo, estado, observacoes)
function requerimentosPendentes($db, $username, $hierarquia) {

    $query = "SELECT *
              FROM requerimento R INNER JOIN aprovacoes_necessarias A
              ON R.id = A.id
              WHERE A.username = :username
              AND R.nivel = :hierarquia;";

    $parameters = array(':username' => $username, ':hierarquia' => $hierarquia);

    $requerimentos = execute($db, $query, $parameters);

    return $requerimentos;
}

// nivelAprovado
// argumentos: $db: PDO: para a base de dados usada
//             $id: string: identificador unico do requerimento
//             $nivel: nivel a aprovar o requerimento
// retorna: booleano: se todos os destinatarios de um requerimento num certo nivel hierarquico aprovaram o requerimento
function nivelAprovado($db, $id, $nivel) {

    $query = "SELECT username
              FROM aprovacoes_necessarias A INNER JOIN requerimento R
              ON A.id = R.id 
              WHERE A.id = :id;";

    $parameters = array(':id' => $id);

    $result = execute($db, $query, $parameters);

    $destinatarios = $result->fetchAll(PDO::FETCH_COLUMN);

    foreach ($destinatarios as $destinatario) {

        if (authenticate($db, $destinatario) == $nivel) {

            return FALSE;
        }
    }

    return TRUE;
}

// avaliaRequerimento
// aprova ou rejeita um requerimento. Escalona para o nivel superior quando aprovado.
// $db: PDO: para a base de dados usada
// $decisao: string: APROVADO ou REJEITADO
// $username: string utilizador que avalia o requerimento
// $hierarquia: string: nivel hierarquico do utilizador
// $id: string: identificador unico do requerimento a avaliar
function avaliaRequerimento($db, $decisao, $username, $hierarquia, $id) {

    if ($decisao === "REJEITADO") {

        $query = "DELETE FROM aprovacoes_necessarias
                  WHERE id = :id;

                  UPDATE requerimento
                  SET estado = 'REJEITADO'
                  WHERE id = :id;";

        $parameters = array(':id' => $id);

        execute($db, $query, $parameters);
    }

    else {

        // se o CEO aprova o requerimento, considera-se aprovado
        if ($hierarquia == CEO) {

            $query = "DELETE FROM aprovacoes_necessarias
                      WHERE id = :id;

                      UPDATE requerimento
                      SET estado = 'APROVADO'
                      WHERE id = :id;";

            $parameters = array(':id' => $id);

            execute($db, $query, $parameters);      
        }


        // caso seja outro colaborador, envia-se o requerimento para os seus superiores
        else {

            $query = "DELETE FROM aprovacoes_necessarias
                      WHERE id = :id
                      AND username = :username;";

            $parameters = array(':id' => $id, ':username' => $username);

            execute($db, $query, $parameters);

            $superiores = determinaSuperiores($db, $username, $hierarquia);

            registaDestinatarios($db, $id, $superiores);

            // quando todos os destinatarios de um nivel hierarquico aprovarem, escalonar
            if (nivelAprovado($db, $id, $hierarquia)) {

                $hierarquia_superiores = authenticate($db, $superiores[0]);

                escalonaRequerimento($db, $id, $hierarquia_superiores);
            }
        }
    }
}


// escalonaRequerimento
// torna o requerimento visivel para um certo nivel do worklow de aprovacao
// argumentos: $db: PDO: para a base de dados usada
//             $id: string: identificador unico do requerimento
//             $nivel: inteiro: nivel para o qual escalonar 
function escalonaRequerimento($db, $id, $nivel) {

    $query = "UPDATE requerimento
              SET nivel = :nivel
              WHERE id = :id;";

    $parameters = array(':nivel' => $nivel, ':id' => $id);

    execute($db, $query, $parameters);
}


// getUnidadePorDiretor
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: diretor da unidade
// retorno: string: unidade dirigida pelo colaborador dado
function getUnidadePorDiretor($db, $username) {

    $query = "SELECT nome
              FROM unidade
              WHERE diretor = :username;";

    $parameters = array(':username' => $username);

    $result = execute($db, $query, $parameters);

    $unidade = $result->fetchColumn();

    return $unidade;
}


// requerimentosColaborador
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: username do colaborador
// retorno: array: requerimentos remetidos pelo colaborador dado
function requerimentosColaborador($db, $username) {

    $query = "SELECT id, inicio, fim, estado, observacoes
              FROM requerimento
              WHERE colaborador = :username;";

    $parameters = array(':username' => $username);

    $result = execute($db, $query, $parameters);

    $requerimentos = $result->fetchAll();

    return $requerimentos;
}


// requerimentosEquipas
// argumentos: $db: PDO: para a base de dados usada
//             $username: string: username do coordenador das equipas
// retorno: array: requerimentos remetidos pelas equipas coordenadas
function requerimentosEquipas($db, $coordenador) {

    if (!is_coordenador($db, $coordenador)) {

        exit ("O utilizador $coordenador não coordena equipas.<br />");
    }

    $query = "SELECT id, R.colaborador, inicio, fim, estado, observacoes
              FROM requerimento R INNER JOIN supervisiona S
              ON R.colaborador = S.colaborador
              WHERE S.supervisor = :username";

    $parameters = array(':username' => $coordenador);

    $result = execute($db, $query, $parameters);

    $requerimentos = $result->fetchAll();

    return $requerimentos;
}



// requerimentosUnidade
// argumentos: $db: PDO: para a base de dados usada
//             $unidade: string: nome da unidade
// retorno: array: requerimentos remetidos pelos colaboradores da unidade
function requerimentosUnidade($db, $unidade) {

    $query = "SELECT id, R.colaborador, inicio, fim, estado, observacoes
              FROM requerimento R INNER JOIN pertence P
              ON R.colaborador = P.colaborador
              WHERE P.unidade = :unidade;";

    $parameters = array(':unidade' => $unidade);

    $result = execute($db, $query, $parameters);

    $requerimentos = $result->fetchAll();

    return $requerimentos;
}

// requerimentosOrganizacao
// argumentos: $db: PDO: para a base de dados usada
// retorno: array: requerimentos remetidos pelos colaboradores da organizacao
function requerimentosOrganizacao($db) {

    $query = "SELECT id, colaborador, inicio, fim, estado, observacoes
              FROM requerimento;";

    $result = execute($db, $query);

    $requerimentos = $result->fetchAll();

    return $requerimentos;
}


// tabelaRequerimentos
// apresenta os requerimentos dados numa tabela HTML
// argumentos: $requerimentos: array: requerimentos a apresentar
function tabelaRequerimentos($requerimentos) {

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
              </tr>");
    }

    echo("</table>");
}



// mapaFerias
// argumentos: $db: PDO: para a base de dados usada
// retorno: periodos de ferias aprovados para todos os colaboradores
function mapaFerias($db) {

    $query = "SELECT colaborador, inicio, fim
              FROM requerimento R INNER JOIN requerimento_ferias F
              ON R.id = F.id
              WHERE estado = 'APROVADO';";

    $result = execute($db, $query);

    $mapa = $result->fetchAll();

    return $mapa;
}


//testInput
// argumentos: $data: dados recebidos de um formulario
// retorno: dados recebidos apos ser retirado espaco em branco do inicio e fim,
//          retiradas aspas e preservadas entidades HTML
function testInput($data) {
  
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    
    return $data;
}
?>