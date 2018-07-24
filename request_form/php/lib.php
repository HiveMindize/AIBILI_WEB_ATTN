<?php
// argumentos: $dbtype: tipo de base de dados: MySQL, PostgreSQL, etc.
//			   $host: endereço IP do servidor de bases de dados
//             $port: porto de ligação a base de dados
//			   $dbname: nome da base de dados a usar
//			   $user: nome de utilizador para login na base de dados
//			   $password: para login na base de dados
// retorno: PDO que permite fazer queries na base de dados especificada
function connect($dbtype, $host, $port, $dbname, $user, $password) {

	try {

	    $db= new PDO($dbtype . ':host=' . $host . ';port=' . $port . ';dbname=' . $dbname . ';', $user, $password);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		return $db;
	}

	catch (PDOException $e) {
    	echo $e->getMessage();
    }
}
?>