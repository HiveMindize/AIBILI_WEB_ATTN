<?php
define("CEO", 4);
define("FINANCEIRO", 3);
define("DIRETOR", 2);
define("COORDENADOR", 1);
define("COLABORADOR", 0);

// connect
// argumentos: $dbtype: tipo de base de dados: MySQL, PostgreSQL, etc.
//             $host: endereço IP do servidor de bases de dados
//             $port: porto de ligacao a base de dados
//             $dbname: nome da base de dados a usar
//             $user: nome de utilizador para login na base de dados
//             $password: para login na base de dados
// retorno: PDO que permite fazer queries na base de dados especificada
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


// authenticate
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador a autenticar
// retorno: nível hierarquico do colaborador, ou erro se nao existir
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

        exit ("Não foi possível autenticá-lo. Verifique que está registado.");
    }
}


// is_colaborador
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador a autenticar
// retorno: booleano que indica se o colaborador existe
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
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador a autenticar
// retorno: booleano que indica se o colaborador e' CEO
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
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador a autenticar
// retorno: booleano que indica se o colaborador e' financeiro
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
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador a autenticar
// retorno: booleano que indica se o colaborador e' diretor de unidade
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
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador a autenticar
// retorno: booleano que indica se o colaborador e' coordenador de equipa
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
// argumentos: $db: PDO para a base de dados usada
//             $query: query MySQL a executar
//             $parameters: parametros a aplicar na query, default e' sem parametros
// retorno: resultado da query (PDOStatement)
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
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador para o qual determinar superiores
//             $hierarquia: nivel hierarquico do colaborador
// retorno: array: superiores hierarquicos do utilizador dado
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

    return $superiores;
}


// determinaCoordenadores
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador para o qual determinar superiores
// retorno: array: coordenadores do utilizador dado
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
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador para o qual determinar diretores
// retorno: array: diretores das unidades a que o utilizador pertence
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


function getAdmins($db) {

    $query = "SELECT username
              FROM administrador;";

    $result = execute($db, $query);

    $admins = $result->fetchAll(PDO::FETCH_COLUMN);

    return $admins;

}


// submeteRequerimento
// submete um requerimento de ausencia na base de dados
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador para o qual determinar superiores
//             $tipo: ausencia ou ferias
//             $datas: array com data de inicio e fim, em formato YYYY-MM-DD HH:ss
//             $destinatarios: destinatarios do requerimento
//             $motivo: motivo a que se deve o requerimento
function submeteRequerimento($db, $username, $tipo, $datas, $destinatarios, $motivo, $hierarquia) {

    $id = uniqid($username);

    //submete requerimento
    $query = "INSERT INTO requerimento(id, nivel, colaborador, inicio, fim, estado, observacoes)
              VALUES (:id, :nivel, :username, :inicio, :fim, 'PENDENTE', :observacoes);";

    $parameters = array(':id' => $id, ':nivel' => $hierarquia, ':username' => $username, ':inicio' => $datas[0], ':fim' => $datas[1], ':observacoes' => $motivo);

    execute($db, $query, $parameters);

    //submete tambem na tabela respetiva
    if ($tipo === "ausencia") {

        $query = "INSERT INTO requerimento_ausencia(id)
                  VALUES (:id);";

    }

    else if ($tipo === "ferias") {

         $query = "INSERT INTO requerimento_ferias(id)
                   VALUES (:id);";
    }

    $parameters = array(':id' => $id);

    execute($db, $query, $parameters);

    registaDestinatarios($db, $id, $username, $destinatarios);
}


function registaDestinatarios($db, $id, $username, $destinatarios) {

    foreach($destinatarios as $destinatario) {

        $query = "INSERT INTO aprovacoes_necessarias(id, username)
                  VALUES (:id, :username);";

        $parameters = array(':id' => $id, ':username' => $destinatario);

        execute($db, $query, $parameters);
    }
}


function consultaRequerimentos($db, $username, $hierarquia) {

    $query = "SELECT *
              FROM requerimento R INNER JOIN aprovacoes_necessarias A
              ON R.id = A.id
              WHERE A.username = :username
              AND R.nivel < :hierarquia;";

    $parameters = array(':username' => $username, ':hierarquia' => $hierarquia);

    $requerimentos = execute($db, $query, $parameters);

    return $requerimentos;
}


function nivelAprovado($db, $id, $hierarquia) {

    $query = "SELECT COUNT(*)
              FROM aprovacoes_necessarias A INNER JOIN requerimento R
              ON A.id = R.id 
              WHERE A.id = :id
              AND R.nivel = :hierarquia;";

    $parameters = array(':id' => $id);

    $result = execute($db, $query, $parameters);

    return $result->fetchColumn() == 0;
}


function avaliaRequerimento($db, $decisao, $username, $hierarquia, $id) {

    if ($hierarquia == CEO) {

        $query = "DELETE FROM aprovacoes_necessarias
                  WHERE id = :id;";

        $parameters = array(':id' => $id);
    }

    else {

        $query = "DELETE FROM aprovacoes_necessarias
                  WHERE id = :id
                  AND username = :username;";

        $parameters = array(':id' => $id, ':username' => $username);
    }

    execute($db, $query, $parameters);

    if ($decisao === "aprovado") {

        $superiores = determinaSuperiores($db, $username, $hierarquia);

        registaDestinatarios($db, $id, $username, $superiores);

        if ($hierarquia == CEO) {

            $query = "UPDATE requerimento
                      SET estado = 'APROVADO'
                      WHERE id = :id;";

            $parameters = array(':id' => $id);

            execute($db, $query, $parameters);
        }

        else if (nivelAprovado($db, $id, $hierarquia)) {

            escalonaRequerimento($db, $username, $id, $hierarquia);
        }
    }

    else {

        $query = "UPDATE requerimento
                  SET estado = 'REJEITADO'
                  WHERE id = :id;

                  DELETE FROM aprovacoes_necessarias
                  WHERE id = :id;";

        $parameters = array(':id' => $id);

        execute($db, $query, $parameters);
    }
}


function escalonaRequerimento($db, $username, $id, $hierarquia) {

    $query = "UPDATE requerimento
              SET nivel = :hierarquia
              WHERE id = :id;";

    $parameters = array(':hierarquia' => $hierarquia, ':id' => $id);

    execute($db, $query, $parameters);
}


function requerimentosAprovadosColaborador($db, $username) {

    $query = "SELECT colaborador, inicio, fim
              FROM requerimento
              WHERE colaborador = :username 
              AND estado = 'APROVADO';";

    $parameters = array(':username' => $username);

    $result = execute($db, $query, $parameters);

    $mapa = $result->fetchAll();

    return $mapa;
}


function requerimentosAprovadosEquipas($db, $coordenador) {

    if (!is_coordenador($db, $coordenador)) {

        exit ("Não coordena equipas.");
    }

    $query = "SELECT colaborador, inicio, fim
              FROM requerimento R INNER JOIN supervisiona S
              ON R.colaborador = S.colaborador
              WHERE S.supervisor = :username
              AND estado = 'APROVADO';";

    $parameters = array(':username' => $coordenador);

    $result = execute($db, $query, $parameters);

    $mapa = $result->fetchAll();

    return $mapa;
}


function requerimentosAprovadosUnidade($db, $unidade) {

    $query = "SELECT colaborador, inicio, fim
              FROM requerimento R INNER JOIN pertence P
              ON R.colaborador = P.colaborador
              WHERE P.unidade = :unidade
              AND estado = 'APROVADO';";

    $parameters = array(':unidade' => $unidade);

    $result = execute($db, $query, $parameters);

    $mapa = $result->fetchAll();

    return $mapa;
}


function requerimentosAprovadosOrganizacao($db) {

    $query = "SELECT colaborador, inicio, fim
              FROM requerimento;";

    $result = execute($db, $query, $parameters);

    $mapa = $result->fetchAll();

    return $mapa;
}


function mapaFeriasColaborador($db, $username) {

    $query = "SELECT colaborador, inicio, fim
              FROM requerimento R INNER JOIN requerimento_ferias F
              ON R.id = F.id
              WHERE colaborador = :username 
              estado = 'APROVADO';";

    $parameters = array(':username' => $username);

    $result = execute($db, $query, $parameters);

    $mapa = $result->fetchAll();

    return $mapa;
}


function mapaFeriasEquipas($db, $coordenador) {

    if (!is_coordenador($db, $coordenador)) {

        exit ("Não coordena equipas.");
    }

    $query = "SELECT colaborador, inicio, fim
              FROM requerimento R INNER JOIN requerimento_ferias F INNER JOIN supervisiona S
              ON R.colaborador = S.colaborador
              AND R.id = F.id
              WHERE S.supervisor = :username
              AND estado = 'APROVADO';";

    $parameters = array(':username' => $coordenador);

     $result = execute($db, $query, $parameters);

    $mapa = $result->fetchAll();

    return $mapa;
}


function mapaFeriasUnidade($db, $unidade) {

    $query = "SELECT colaborador, inicio, fim
              FROM requerimento R INNER JOIN requerimento_ferias F INNER JOIN pertence P
              ON R.colaborador = P.colaborador
              AND R.id = F.id
              WHERE P.unidade = :unidade
              AND estado = 'APROVADO';";

    $parameters = array(':unidade' => $unidade);

    $result = execute($db, $query, $parameters);

    $mapa = $result->fetchAll();

    return $mapa;
}


function mapaFeriasOrganizacao($db) {

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