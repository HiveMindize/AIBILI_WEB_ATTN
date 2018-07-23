<?php

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