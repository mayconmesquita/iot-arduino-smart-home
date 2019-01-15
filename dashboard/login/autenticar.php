<?php
	error_reporting(0);
	session_start();

	include('../config/connect_bd.php');
	mysql_select_db($basedados, $connect);
	
	$email_user = mysql_real_escape_string(strip_tags(trim(strtolower($_POST['email_user']))));
	$senha_usuario = strip_tags(trim($_POST['senha_user']));
	
	$sql = "SELECT * FROM users WHERE email_user = '$email_user'";
	$resultado = mysql_query($sql, $connect) or die('syserror_1');
	$linha = mysql_fetch_array($resultado);
	
	$salt = $linha['link_user'];
	$senha_user = sha1($senha_usuario . $salt);

	for ($i = 0; $i < 1000; $i++) {
		$senha_user = hash("whirlpool", $senha_user);
	}
	
	$sql = "SELECT * FROM users WHERE email_user = '$email_user' and senha_user = '$senha_user' ";
	$resultado = mysql_query($sql, $connect) or die('syserror_1');
	$pegar = mysql_fetch_array($resultado);
	$row = mysql_num_rows($resultado);
	
	if (!empty($email_user) && !empty($senha_usuario) && $row == 0) { 
		$_SESSION['kick'] = '1';
		echo 'false_1';
	} else if ($pegar['status_user'] == 2) {
		$_SESSION['kick'] = '1';
		echo 'false_2';
	} else if (empty($email_user) || empty($senha_usuario)) {
		$_SESSION['kick'] = '1';
		echo 'false_3';
	} else {

		$subdomain = explode('.', $_SERVER['HTTP_HOST']);

		if ($subdomain[0] == 'm') {
			$_SESSION['mobile'] = true;
			if($_GET['app'] == 'android') $_SESSION['app'] = 'android';
			if($_GET['app'] == 'desktop') $_SESSION['app'] = 'desktop';
			if($_GET['app'] == 'ios')	  $_SESSION['app'] = 'ios';
			if($_GET['app'] == 'winphone')  $_SESSION['app'] = 'winphone';
		} else $_SESSION['mobile'] = false;

		$_SESSION['id_user'] 			= $pegar['id_user'];
		$_SESSION['nome_user'] 			= $pegar['nome_user'];
		$_SESSION['email_user'] 		= $pegar['email_user'];
		$_SESSION['senha_user'] 		= $pegar['senha_user'];
		$_SESSION['permissao_user'] 	= $pegar['permissao_user'];
		$_SESSION['status_user'] 		= $pegar['status_user'];
		$_SESSION['sys_lang']	 		= $pegar['language_user'];
		$_SESSION['kick'] 				= '0';
		
		$sql = "SELECT * FROM configs WHERE id = " . (int)1;
		$resultado = mysql_query($sql) or die;
		$configs = mysql_fetch_array($resultado, MYSQL_ASSOC);
		
		$_SESSION['title']			= $configs['title'];
		$_SESSION['ip'] 			= $configs['ip'];
		$_SESSION['porta'] 			= $configs['porta'];
		$_SESSION['tempo_receber']	= $configs['tempo_receber'];
		$_SESSION['url']			= $configs['url'];
		
		$sql = "UPDATE users SET 
				data_ultimo_login = NOW(),
				hora_ultimo_login = NOW()
				WHERE email_user  = '$email_user' and senha_user = '$senha_user' 
		";
			
		$resultado = mysql_query($sql, $connect) or die('syserror_1');
		echo 'true_1';
	}
?>