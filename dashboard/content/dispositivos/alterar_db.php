<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 5){

		$device_personalizado = strtoupper(trim(strip_tags($_POST['device_personalizado'])));
		if(empty($device_personalizado) || isset($device_personalizado) == false) $device_personalizado = 'X';
		else $device_personalizado = $device_personalizado;

		function device_cmd_toggle($device_tipo,$device_ordem){
			$device_cmd = array();
			$device_cmd[0] = 'S';

			global $device_personalizado;

				 if($device_tipo == 1) $device_cmd[1] = 'L';
			else if($device_tipo == 2) $device_cmd[1] = 'P';
			else if($device_tipo == 3) $device_cmd[1] = 'I';
			else if($device_tipo == 4) $device_cmd[1] = 'D';
			else if($device_tipo == 5) $device_cmd[1] = $device_personalizado;
			   					  else $device_cmd[1] = 'D';
			
			for($i = 1; $i < $device_ordem; $i++) $device_cmd[$i+1] = 0;

			if($device_ordem == 0) $device_cmd[$device_ordem+2] = 1;
			else $device_cmd[$device_ordem+1] = 1;
			
			$device_cmd = implode($device_cmd);
			
			return $device_cmd;
		}
	
		function device_cmd_on($device_tipo,$device_ordem){
			$device_cmd = array();
			$device_cmd[0] = 'S';

			global $device_personalizado;

				 if($device_tipo == 1) $device_cmd[1] = 'L';
			else if($device_tipo == 2) $device_cmd[1] = 'P';
			else if($device_tipo == 3) $device_cmd[1] = 'I';
			else if($device_tipo == 4) $device_cmd[1] = 'D';
			else if($device_tipo == 5) $device_cmd[1] = $device_personalizado;
			   					  else $device_cmd[1] = 'D';
			
			for($i = 1; $i < $device_ordem; $i++) $device_cmd[$i+1] = 0;

			if($device_ordem == 0) $device_cmd[$device_ordem+2] = 2;
			else $device_cmd[$device_ordem+1] = 2;
			
			$device_cmd = implode($device_cmd);
			
			return $device_cmd;
		}
	
		function device_cmd_off($device_tipo,$device_ordem){
			$device_cmd = array();
			$device_cmd[0] = 'S';

			global $device_personalizado;

				 if($device_tipo == 1) $device_cmd[1] = 'L';
			else if($device_tipo == 2) $device_cmd[1] = 'P';
			else if($device_tipo == 3) $device_cmd[1] = 'I';
			else if($device_tipo == 4) $device_cmd[1] = 'D';
			else if($device_tipo == 5) $device_cmd[1] = $device_personalizado;
			   					  else $device_cmd[1] = 'D';
			
			for($i = 1; $i < $device_ordem; $i++) $device_cmd[$i+1] = 0;

			if($device_ordem == 0) $device_cmd[$device_ordem+2] = 3;
			else $device_cmd[$device_ordem+1] = 3;
			
			$device_cmd = implode($device_cmd);
			
			return $device_cmd;
		}

		$_SESSION['device_tipo']      	  = $_POST['device_tipo'];
		$_SESSION['device_nome']     	  = $_POST['device_nome'];
		$_SESSION['device_ordem']     	  = $_POST['device_ordem'];
		$_SESSION['device_voz_on']    	  = $_POST['device_voz_on'];
		$_SESSION['device_voz_off']   	  = $_POST['device_voz_off'];
		$_SESSION['device_personalizado'] = $_POST['device_personalizado'];
		
		include('../../config/connect_bd.php');
		mysql_select_db($basedados, $connect);
		
		if(empty($_POST['device_tipo'])){
			$_SESSION['status'] = 100;
			echo '<script>history.go(-1);</script>';
		}
		else if(empty($_POST['device_nome'])){
			$_SESSION['status'] = 200;
			echo '<script>history.go(-1);</script>';
		}
		else if(empty($_POST['device_ordem'])){
			$_SESSION['status'] = 300;
			echo '<script>history.go(-1);</script>';
		}
		else{
			$device_cmd_toggle = device_cmd_toggle($_POST['device_tipo'],$_POST['device_ordem']);
			$device_cmd_on     = device_cmd_on($_POST['device_tipo'],$_POST['device_ordem']);
			$device_cmd_off    = device_cmd_off($_POST['device_tipo'],$_POST['device_ordem']);
			$device_pos_x 	   = '50'; 
			$device_pos_y	   = '50';
		
			if($_GET[id] < 1){
				$sql = "INSERT INTO devices (
					device_ordem,
					device_tipo,
					device_nome,
					device_cmd_toggle,
					device_cmd_on,
					device_cmd_off,
					device_voz_on,
					device_voz_off,
					device_pos_x,
					device_pos_y
				) VALUES (
					'".mysql_real_escape_string(trim(strip_tags($_POST['device_ordem'])))."',
					'".mysql_real_escape_string(trim(strip_tags($_POST['device_tipo'])))."',
					'".mysql_real_escape_string(trim(strip_tags($_POST['device_nome'])))."',
					'".mysql_real_escape_string(trim(strip_tags($device_cmd_toggle)))."',
					'".mysql_real_escape_string(trim(strip_tags($device_cmd_on)))."',
					'".mysql_real_escape_string(trim(strip_tags($device_cmd_off)))."',
					'".mysql_real_escape_string(trim(strip_tags($_POST['device_voz_on'])))."',
					'".mysql_real_escape_string(trim(strip_tags($_POST['device_voz_off'])))."',
					'".mysql_real_escape_string(trim(strip_tags($device_pos_x)))."',
					'".mysql_real_escape_string(trim(strip_tags($device_pos_y)))."'
				)";
				if($sql){
					$_SESSION['status'] = 3;  //Sucesso
					echo"<meta http-equiv='refresh' content='0;URL=../../dispositivos'>";
				}
				else{
					$_SESSION['status'] = 4;  //Erro
					echo"<meta http-equiv='refresh' content='0;URL=../../dispositivos'>";
				}
				echo"<meta http-equiv='refresh' content='0;URL=../../dispositivos'>";
			}
			else{
				$sql = "SELECT * FROM devices WHERE device_id = ".(int)$_GET['id'];
				$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
				$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);
			
				$sql = "UPDATE devices SET
					device_ordem = '".mysql_real_escape_string(trim(strip_tags($_POST['device_ordem'])))."',
					device_tipo = '".mysql_real_escape_string(trim(strip_tags($_POST['device_tipo'])))."',
					device_nome = '".mysql_real_escape_string(trim(strip_tags($_POST['device_nome'])))."',
					device_cmd_toggle = '".mysql_real_escape_string(trim(strip_tags($device_cmd_toggle)))."',
					device_cmd_on = '".mysql_real_escape_string(trim(strip_tags($device_cmd_on)))."',
					device_cmd_off = '".mysql_real_escape_string(trim(strip_tags($device_cmd_off)))."',
					device_voz_on = '".mysql_real_escape_string(trim(strip_tags($_POST['device_voz_on'])))."',
					device_voz_off = '".mysql_real_escape_string(trim(strip_tags($_POST['device_voz_off'])))."'
				WHERE device_id = ".(int)$_GET['id'];	
				
				if($sql){
					$_SESSION['status'] = 5;
					echo"<meta http-equiv='refresh' content='0;URL=../../dispositivos'>";
				}
				else{
					$_SESSION['status'] = 6;
					echo"<meta http-equiv='refresh' content='0;URL=../../dispositivos'>";
				}
				echo"<meta http-equiv='refresh' content='0;URL=../../dispositivos'>";	
			}
	}
	$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
?>
<?php } ?>