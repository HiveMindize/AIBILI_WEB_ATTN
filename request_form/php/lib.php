<?php
// argumentos: $dbtype: tipo de base de dados: MySQL, PostgreSQL, etc.
//			   $host: endereço IP do servidor de bases de dados
//             $port: porto de ligação a base de dados
//			   $dbname: nome da base de dados a usar
//			   $user: nome de utilizador para login na base de dados
//			   $password: para login na base de dados
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


function authenticate($db, $username) {

	$db->query("START TRANSACTION;");

	if (exists_ceo($db, $username) == 1) {

		return CEO; 
	}

	else if (exists_financeiro($db, $username) == 1) {

		return FINANCEIRO; 
	}

	else if (exists_diretor($db, $username) == 1) {

		return DIRETOR;
	}

	else if (exists_coordenador($db, $username) == 1) {

		return COORDENADOR;
	}

	else if (exists_colaborador($db, $username) == 1) {

		return COLABORADOR;
	}

	else  {

		exit("Não foi possível autenticá-lo. Verifique que está registado.");
	}

	$db->query("COMMIT;");
}


function exists_ceo($db, $username) {

	$exists_template = "SELECT EXISTS (
									   SELECT *
									   FROM administrador
									   WHERE username = :username
									   AND funcao = 'CEO'
									) AS existe;";

	$parameters = array(':username' => $username);
	
	$result = execute($db, $exists_template, $parameters);

	$row = $result->fetch();

	return $row['existe'];
}


function exists_financeiro($db, $username) {

	$exists_template = "SELECT EXISTS (
									   SELECT *
									   FROM administrador
									   WHERE username = :username
									   AND funcao = 'financeiro'
									) AS existe;";

	$parameters = array(':username' => $username);
	
	$result = execute($db, $exists_template, $parameters);

	$row = $result->fetch();

	return $row['existe'];
}


function exists_diretor($db, $username) {

	$exists_template = "SELECT EXISTS (
										SELECT * 
										FROM unidade 
										WHERE diretor = :username
									  ) AS existe;";
	
	$parameters = array(':username' => $username);
	
	$result = execute($db, $exists_template, $parameters);

	$row = $result->fetch();

	return $row['existe'];
}


function exists_coordenador($db, $username) {

	$exists_template = "SELECT EXISTS (
										SELECT * 
										FROM supervisiona 
										WHERE supervisor = :username
									  ) AS existe;";
	

	$parameters = array(':username' => $username);
	
	$result = execute($db, $exists_template, $parameters);

	$row = $result->fetch();

	return $row['existe'];
}


function exists_colaborador($db, $username) {

	$exists_template = "SELECT EXISTS (
										SELECT * 
										FROM colaborador 
										WHERE username = :username
									  ) AS existe;";

	$parameters = array(':username' => $username);
	
	$result = execute($db, $exists_template, $parameters);
	
	$row = $result->fetch();

	return $row['existe'];
}


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


function determinaSuperiores($db, $username, $hierarquia) {

	// se for colaborador, determinar todos os supervisores
	if ($hierarquia == COLABORADOR) {

		$superiores = determinaCoordenadores($db, $username);

		//se não houver supervisores, consideram-se os diretores das areas a que pertence
		if (!empty($superiores)) {

			return $superiores;
		}

		$superiores = determinaDiretores($db, $username);

		return $superiores;
	}
}


function determinaCoordenadores($db, $username) {

	$query = "SELECT supervisor
			  FROM supervisiona
			  WHERE colaborador = :username;";

	$parameters = array(':username' => $username);

	$result = execute($db, $query, $parameters);

	$coordenadores = pdoStatement2Array($result, 'supervisor');

	return $coordenadores;
}


function determinaDiretores($db, $username) {

	$query = "SELECT diretor
			  FROM pertence P INNER JOIN unidade U
			  ON unidade = nome
			  WHERE colaborador = :username";

	$parameters = array(':username' => $username);

	$result = execute($db, $query, $parameters);

	$diretores = pdoStatement2Array($result, 'diretor');

	return $diretores;
}


function pdoStatement2Array($pdoStatement, $columnName) {

	$array = array();

	foreach($pdoStatement as $row) {

		array_push($array, $row[$columnName]);
	}

	return $array;
}


function test_input($data) {
  
	$data = trim($data);
  	$data = stripslashes($data);
  	$data = htmlspecialchars($data);
  	return $data;
}
?>