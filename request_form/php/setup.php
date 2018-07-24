<?php

	$dbtype = "mysql";
	$host = "localhost";
	$port = 3306;
	$dbname = "aibili_web_attn";
	$user = "root";
	$password = "";

	$username = "dfcoimbra";

	$db = connect($dbtype, $host, $port, $dbname, $user, $password);
	$hierarquia = authenticate($db, $username);
?>