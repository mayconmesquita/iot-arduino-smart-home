<?php
	$servidor	=	'localhost';
	$basedados	=	'basedados';
	$utilizador	=	'utilizador';
	$chavepass	=	'chavepass';
	
	$connect = mysql_connect($servidor, $utilizador, $chavepass);
	if (!$connect) die('Estamos em manutenção, tente mais tarde.');
	
	mysql_query("SET NAMES 'utf8'");
	mysql_query('SET character_set_connection=utf8');
	mysql_query('SET character_set_client=utf8');
	mysql_query('SET character_set_results=utf8');
?>