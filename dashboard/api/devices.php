<?php
	header("Content-Type: application/json; charset=utf-8'");
	header("access-control-allow-origin: *");
	session_start();
	if($_SESSION['permissao_user'] >= 1){

		include('../config/connect_bd.php');

		mysql_select_db($basedados, $connect);
		$sql = "SELECT * FROM tbl_devices ORDER BY device_id ASC";
		$resultado = mysql_query($sql) or die;

		function is_valid_callback($subject){
			$identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

			$reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
									'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 
									'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 
									'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 
									'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 
									'private', 'public', 'yield', 'interface', 'package', 'protected', 
									'static', 'null', 'true', 'false');

		    return preg_match($identifier_syntax, $subject) && ! in_array(strtolower($subject), $reserved_words);
		}

		$data = array();

		while ($linha = mysql_fetch_array($resultado, MYSQL_ASSOC)){
				 if($linha['device_type'] == 1)  $linha['device_type'] = 'Lâmpada';
			else if($linha['device_type'] == 2)  $linha['device_type'] = 'Porta';
			else if($linha['device_type'] == 3)  $linha['device_type'] = 'Ar-condicionado';
			else if($linha['device_type'] == 4)  $linha['device_type'] = 'Eletroeletrônico';
			else if($linha['device_type'] == 5)  $linha['device_type'] = 'Alarme';
			else if($linha['device_type'] == 6)  $linha['device_type'] = 'Sensor de temperatura';
			else if($linha['device_type'] == 7)  $linha['device_type'] = 'Sensor de corrente';
			else if($linha['device_type'] == 8)  $linha['device_type'] = 'Sensor de presença';
			else if($linha['device_type'] == 9)  $linha['device_type'] = 'Sensor de contato';
			else if($linha['device_type'] == 10) $linha['device_type'] = 'Sensor de gás';

			$linha['device_ordertype'] = $linha['device_type'].' '.$linha['device_order'];

			$data[] = $linha;
		}

		$json = json_encode($data);

		if(!isset($_GET['callback'])) exit($json);
		if(is_valid_callback($_GET['callback'])) exit($_GET['callback'].$json);
	}
	else
?>