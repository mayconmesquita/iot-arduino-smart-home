<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 5){

	$_SESSION['modo_nome'] = $_POST['modo_nome'];
	$_SESSION['modo_seq']  = $_POST['modo_seq'];
	
	include('../../config/connect_bd.php');
	mysql_select_db($basedados, $connect);
	
	if(empty($_POST['modo_nome'])){
		$_SESSION['status'] = 100;
		echo '<script>history.go(-1);</script>';
	}
	else if(empty($_POST['modo_seq'])){
		$_SESSION['status'] = 200;
		echo '<script>history.go(-1);</script>';
	}
	else{
		if($_GET[id] < 1){
			$sql = "INSERT INTO modos (
				modo_nome,
				modo_seq,
			) VALUES (
				'".mysql_real_escape_string(trim(strip_tags($_POST['modo_nome'])))."',
				'".mysql_real_escape_string(trim(strip_tags($_POST['modo_seq'])))."'
			)";
			if($sql){
				$_SESSION['status'] = 3;
				echo"<meta http-equiv='refresh' content='0;URL=../../index.php?p=modos'>";
			}
			else{
				$_SESSION['status'] = 4;
				echo"<meta http-equiv='refresh' content='0;URL=../../index.php?p=modos'>";
			}
			echo"<meta http-equiv='refresh' content='0;URL=../../index.php?p=modos'>";
		}
		else{
			$sql = "SELECT * FROM modos WHERE modo_id = ".(int)$_GET['id'];
			$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
			$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);
		
			$sql = "UPDATE modos SET
				modo_nome = '".mysql_real_escape_string(trim(strip_tags($_POST['modo_nome'])))."',
				modo_seq = '".mysql_real_escape_string(trim(strip_tags($_POST['modo_seq'])))."'
			WHERE modo_id = ".(int)$_GET['id'];	
			
			if($sql){
				$_SESSION['status'] = 5;
				echo"<meta http-equiv='refresh' content='0;URL=../../index.php?p=modos'>";
			}
			else{
				$_SESSION['status'] = 6;
				echo"<meta http-equiv='refresh' content='0;URL=../../index.php?p=modos'>";
			}
			echo"<meta http-equiv='refresh' content='0;URL=../../index.php?p=modos'>";	
		}
	}
	$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
?>
<?php } ?>