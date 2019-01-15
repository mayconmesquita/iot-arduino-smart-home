<?php
	header("Content-Type: application/json; charset=utf-8'");
	header("access-control-allow-origin: *");
	session_start();

	if ($_SESSION['permissao_user'] >= 1) {

		include('../config/connect_bd.php');

		mysql_select_db($basedados, $connect);
		$sql = "SELECT `tbl_tasks`.*, `tbl_devices`.`device_name` FROM `tbl_tasks` INNER JOIN `tbl_devices` ON `tbl_tasks`.`task_device_id` = `tbl_devices`.`device_id` ORDER BY `tbl_tasks`.`task_id` DESC";
		$resultado = mysql_query($sql) or die;

		function is_valid_callback($subject) {
		    $identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

		    $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
									'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 
									'for', 'switch', 'while', 'debugger', 'function', 'this', 'with', 
									'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 
									'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 
									'private', 'public', 'yield', 'interface', 'package', 'protected', 
									'static', 'null', 'true', 'false');

		    return preg_match($identifier_syntax, $subject) && !in_array(strtolower($subject), $reserved_words);
		}

		$data = array();

		while ($linha = mysql_fetch_array($resultado, MYSQL_ASSOC)) {
			$linha['task_time'] = date("H:i", strtotime($linha['task_time']));
			$linha['task_date'] = date("d/m/Y", strtotime($linha['task_date']));
			if ($linha['task_action']  == 2) $linha['task_action']  = 'Ligar';
			if ($linha['task_action']  == 3) $linha['task_action']  = 'Desligar';
			if ($linha['task_action']  == 4) $linha['task_action']  = 'Abrir';
			if ($linha['task_frequency'] == 1) $linha['task_frequency'] = 'Diariamente';
			if ($linha['task_frequency'] == 2) $linha['task_frequency'] = 'Dias úteis';
			if ($linha['task_frequency'] == 3) $linha['task_frequency'] = 'Fins de semana';
			if ($linha['task_frequency'] == 4) $linha['task_frequency'] = 'Data específica (' . $linha['task_date'] . ')';

			$data[] = $linha;
		}

		$json = json_encode($data);

		if (!isset($_GET['callback'])) exit($json);
		if (is_valid_callback($_GET['callback'])) exit($_GET['callback'].$json);
	}
?>