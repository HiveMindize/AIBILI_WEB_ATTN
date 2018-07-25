<?php
// connect
// argumentos: $dbtype: tipo de base de dados: MySQL, PostgreSQL, etc.
//             $host: endereço IP do servidor de bases de dados
//             $port: porto de ligacao a base de dados
//             $dbname: nome da base de dados a usar
//             $user: nome de utilizador para login na base de dados
//             $password: para login na base de dados
// retorno: PDO que permite fazer queries na base de dados especificada
define("CEO", 0);
define("FINANCEIRO", 1);
define("DIRETOR", 2);
define("COORDENADOR", 3);
define("COLABORADOR", 4);


function connect($dbtype, $host, $port, $dbname, $user, $password) {

    try {

        $db= new PDO($dbtype . ':host=' . $host . ';port=' . $port . ';dbname=' . $dbname . ';', $user, $password);
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

    $db->query("START TRANSACTION;");

    //se o colaborador nao existir, termina o processo de autenticacao
    if (is_colaborador($db, $username)) {

        if (is_ceo($db, $username) == 1) {

            return CEO; 
        }

        else if (is_financeiro($db, $username) == 1) {

            return FINANCEIRO; 
        }

        else if (is_diretor($db, $username) == 1) {

            return DIRETOR;
        }

        else if (is_coordenador($db, $username) == 1) {

            return COORDENADOR;
        }

        else {

            return COLABORADOR;
        }
    }

    else  {

        exit("Não foi possível autenticá-lo. Verifique que está registado.");
    }

    $db->query("COMMIT;");
}


// is_colaborador
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador a autenticar
// retorno: booleano que indica se o colaborador existe
function is_colaborador($db, $username) {

    $query = "SELECT EXISTS (
                             SELECT * 
                             FROM colaborador 
                             WHERE username = :username
                            ) AS existe;";

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

    $query = "SELECT EXISTS (
                                       SELECT *
                                       FROM administrador
                                       WHERE username = :username
                                       AND funcao = 'CEO'
                                      ) AS existe;";

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

    $query = "SELECT EXISTS (
                             SELECT *
                             FROM administrador
                             WHERE username = :username
                             AND funcao = 'financeiro'
                            ) AS existe;";

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

    $query = "SELECT EXISTS (
                                        SELECT * 
                                        FROM unidade 
                                        WHERE diretor = :username
                                      ) AS existe;";
    
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

    $query = "SELECT EXISTS (
                                        SELECT * 
                                        FROM supervisiona 
                                        WHERE supervisor = :username
                                      ) AS existe;";
    

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
        echo ($e->getMessage());
    }
}


// determinaSuperiores
// determina os superiores hierarquicos no nível imediatamente superior ao utilizador dado
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador para o qual determinar superiores
//             $hierarquia: nivel hierarquico do colaborador
// retorno: array: superiores hierarquicos do utilizador dado
function determinaSuperiores($db, $username, $hierarquia) {

    $db->query("START TRANSACTION");

    $superiores = determinaCoordenadores($db, $username);

    //se não houver supervisores, consideram-se os diretores das areas a que pertence
    if (!empty($superiores)) {

        return $superiores;
    }

    $superiores = determinaDiretores($db, $username);

    $db->query("COMMIT;");

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
              WHERE colaborador = :username";

    $parameters = array(':username' => $username);

    $result = execute($db, $query,  $parameters);

    $diretores = $result->fetchAll(PDO::FETCH_COLUMN);

    return $diretores;
}


// submeteRequerimento
// submete um requerimento de ausencia na base de dados
// argumentos: $db: PDO para a base de dados usada
//             $username: utilizador para o qual determinar superiores
//             $tipo: 
function submeteRequerimento($db, $username, $tipo, $datas, $contador, $motivo) {

    $db->query("START TRANSACTION;");

    $id = uniqid($username);

    $datas = explode(" - ", $datas);

    $query = "INSERT INTO requerimento(id, colaborador, inicio, fim, contador, estado, observacoes)
              VALUES (:id, :username, :inicio, :fim, :contador, :estado, :observacoes);";

    $parameters = array(':id' => $id,':username' => $username, ':inicio' => $datas[0], ':fim' => $datas[1], ':contador' => $contador, ':estado' => "PENDENTE", ':observacoes' => $motivo);

    execute($db, $query, $parameters);

    if ($tipo = "ausencia") {

        $query = "INSERT INTO requerimento_ausencia(id)
                  VALUES (:id);";

        $parameters = array(':id' => $id);

        execute($db, $query, $parameters);
    }

    $db->query("COMMIT;");
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