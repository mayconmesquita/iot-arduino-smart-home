<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 4){
	
		$id_user = trim(strip_tags($_GET['id']));
		
		if($id_user == 1){
			$_SESSION['msg_status'] = 4;  //Erro
			echo "<meta http-equiv='refresh' content='0;URL=../../usuarios'>";
		}
		else if($_SESSION['status_user'] == 2){
			$_SESSION['msg_status'] = 4;  //Erro
			echo "<meta http-equiv='refresh' content='0;URL=../../usuarios'>";
		}
		else if($_SESSION['permissao_user'] == NULL or $_SESSION['permissao_user'] < 4){
			$_SESSION['msg_status'] = 4;  //Erro
			echo "<meta http-equiv='refresh' content='0;URL=../../usuarios'>";
		}
		else{
			include('../../config/connect_bd.php');
			mysql_select_db($basedados, $connect);
			$sql = "DELETE FROM users WHERE id_user = ".(int)$id_user;
			$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');

			if($sql){
				$_SESSION['msg_status'] = 3;  //Sucesso
				echo "<meta http-equiv='refresh' content='0;URL=../../usuarios'>";
			}
		}
	}
	else echo "<meta http-equiv='refresh' content='0;URL=../../usuarios'>";
?>