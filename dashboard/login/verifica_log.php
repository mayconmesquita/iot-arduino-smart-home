<?php
	error_reporting(0);
	session_start();
	
	$email_user = $_SESSION['email_user'];
	$senha_user = $_SESSION['senha_user'];
	
	if (isset($_GET['randval'])) require('../configuration.php');
	else require('configuration.php');

	mysql_select_db($settings['db_name'], $mysql_connect);
	$sql = "SELECT * FROM users WHERE email_user = '$email_user' and senha_user = '$senha_user' ";
	$resultado = mysql_query($sql) or die('');
	$pegar = mysql_fetch_array($resultado);
	$row = mysql_num_rows($resultado);

	if ($row == 0 || $pegar['status_user'] == 2) {
		session_unset();
		session_destroy();

		$_SESSION['kick'] = 1;
		
		if (isset($_SESSION['id_user'])) echo $_SESSION['kick'];

		if (!isset($_GET['randval'])) {
			header('Location: login.php');
			die;
		}
	} else {
		$_SESSION['id_user']        = $pegar['id_user'];
		$_SESSION['nome_user']      = $pegar['nome_user'];
		$_SESSION['email_user']     = $pegar['email_user'];
		$_SESSION['senha_user']     = $pegar['senha_user'];
		$_SESSION['permissao_user'] = $pegar['permissao_user'];
		$_SESSION['status_user']    = $pegar['status_user'];
		$_SESSION['sys_lang']		= $pegar['language_user'];

		$tempo = time(); 
		date_default_timezone_set('America/Sao_Paulo');

		$hora_atual  = date('H',$tempo);
		$hora_atual .= ':';
		$hora_atual .= date('i',$tempo);
		$hora_atual .= ':';
		$hora_atual .= date('s',$tempo);
		
		$sql = "UPDATE users SET 
			ping_user = '$hora_atual'
			WHERE email_user = '$email_user' and senha_user = '$senha_user' 
		";

		$resultado = mysql_query($sql) or die('Nao foi possivel conectar...');
		$_SESSION['kick'] = '0';
	}
?>