<?php
	$send = '';
	error_reporting(0);

	$servidor	=	'localhost';
	$basedados	=	'maycon_casa';
	$utilizador	=	'maycon_admin';
	$chavepass	=	'casainteligente123';
	
	$connect = mysql_connect($servidor, $utilizador, $chavepass);
	if (!$connect) die('Estamos em manutenção, tente mais tarde [1].');
	
	mysql_query("SET NAMES 'utf8'");
	mysql_query('SET character_set_connection=utf8');
	mysql_query('SET character_set_client=utf8');
	mysql_query('SET character_set_results=utf8');
	
	function acionarDispositivo($disp, $action){
		$servidor	=	'localhost';
		$basedados	=	'maycon_casa';
		$utilizador	=	'maycon_admin';
		$chavepass	=	'casainteligente123';
		
		$connect = mysql_connect($servidor, $utilizador, $chavepass);
		if (!$connect) die('Estamos em manutenção, tente mais tarde [2].');
		
		mysql_query("SET NAMES 'utf8'");
		mysql_query('SET character_set_connection=utf8');
		mysql_query('SET character_set_client=utf8');
		mysql_query('SET character_set_results=utf8');

		mysql_select_db($basedados, $connect);
		$sql = "SELECT * FROM configs WHERE id = ".(int)1;
		$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde [3].');
		$configs = mysql_fetch_array($resultado, MYSQL_ASSOC);
	
		$ip    = $configs['ip'];
		$porta = $configs['porta'];
		
		$sql2 = "SELECT * FROM tbl_devices WHERE device_id = ".(int)$disp;
		$resultado2 = mysql_query($sql2) or die;
		$dev = mysql_fetch_array($resultado2, MYSQL_ASSOC);
		
		global $send;
		
		//Ligar
		if($action == 2) $send = $dev['device_cmd_on'];
		//Desligar
		if($action == 3) $send = $dev['device_cmd_off'];
		//Abrir
		if($action == 4) $send = $dev['device_cmd_toggle'];

		require_once("phpws/vendor/autoload.php");

		$loop = \React\EventLoop\Factory::create();

		$logger = new \Zend\Log\Logger();
		$writer = new Zend\Log\Writer\Stream("php://output");
		$logger->addWriter($writer);

		$client = new \Devristo\Phpws\Client\WebSocket('ws://' . $ip . ':' . $porta . '/', $loop, $logger);

		$client->on("connect", function() use ($logger, $client){
			// $logger->notice("Or we can use the connect event!");
			global $send;
			$client->send($send);
		});

		$client->on("message", function($message) use ($client, $logger){
			// $logger->notice("Got message: ".$message->getData());
			// $client->close();
			die;
		});

		$client->open()->then(function() use($logger, $client){
			// $logger->notice("We can use a promise to determine when the socket has been connected!");
		});

		$loop->run();
	}
	
	$tempo = time();
	date_default_timezone_set('America/Fortaleza');

	if(isset($_SESSION['status'])) $status = $_SESSION['status']; else $status = '';

	mysql_select_db($basedados, $connect);
	$sql = "SELECT * FROM tbl_tasks WHERE task_status = '1' ORDER BY task_id DESC";
	$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde [4].');
	
	while ($linha = mysql_fetch_array($resultado, MYSQL_ASSOC)){
		$data_atual  = date("d",$tempo);
		$data_atual .= '/';
		$data_atual .= date("m",$tempo);
		$data_atual .= '/';
		$data_atual .= date("Y",$tempo);
		
		if(date("w",$tempo) < 1 || date("w",$tempo) > 5) $dia_util = 0;
		else $dia_util = 1;
		
		$hora_atual  = date("H",$tempo);
		$hora_atual .= ':';
		$hora_atual .= date("i",$tempo);
		
		if($hora_atual == $linha['task_time'] && isset($linha['task_device_id'])){
				 if($linha['task_frequency'] == 1) acionarDispositivo($linha['task_device_id'], $linha['task_action']);
			else if($linha['task_frequency'] == 2 && $dia_util == 1) acionarDispositivo($linha['task_device_id'], $linha['task_action']);
			else if($linha['task_frequency'] == 3 && $dia_util == 0) acionarDispositivo($linha['task_device_id'], $linha['task_action']);
			else if($linha['task_frequency'] == 4 && $data_atual == $linha['task_date']) acionarDispositivo($linha['task_device_id'], $linha['task_action']);
		}
	}
?>