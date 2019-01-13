<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 5){
		include('../../config/connect_bd.php');
		mysql_select_db($basedados, $connect);
		$sql = "SELECT * FROM modos WHERE modo_id = ".(int)$_GET['id'];
		$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
		$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);
		
		$sql = "DELETE FROM devices WHERE modo_id = ".(int)$_GET['id'];
		$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');

		if($sql){
			$_SESSION['status'] = 1;
			echo"<meta http-equiv='refresh' content='0;URL=../../modos'>";
		}
		else{
			$_SESSION['status'] = 2;
			echo"<meta http-equiv='refresh' content='0;URL=../../modos'>";
		}
	}
?>