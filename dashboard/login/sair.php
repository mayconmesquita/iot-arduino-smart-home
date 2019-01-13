<?php
	error_reporting(0);
	session_start();
	
	unset($_SESSION['id_user']);
	unset($_SESSION['nome_user']);
	unset($_SESSION['email_user']);
	unset($_SESSION['senha_user']);
	unset($_SESSION['permissao_user']);
	unset($_SESSION['status_user']);
	unset($_SESSION["title"]);
	unset($_SESSION["ip"]);
	unset($_SESSION["porta"]);
	unset($_SESSION["tempo_receber"]);
	
	$app 	= $_SESSION['app'];
	$mobile = $_SESSION['mobile'];

	if($mobile == true && $app != ''){
		session_unset();
		session_destroy();
		$_SESSION['kick'] = '1';
		header('Location: ../login.php?app='.$app);
	} else {
		session_unset();
		session_destroy();
		$_SESSION['kick'] = '1';
		header('Location: ../login.php');
	}
?>