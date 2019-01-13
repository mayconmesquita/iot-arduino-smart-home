<?php
	require('../configuration.php');
	include('../config/connect_bd.php');

	$html_header = "<!DOCTYPE html><html><head><title>Api 1.0</title><meta charset=\"utf-8\"></head><body>";
	$html_footer = "</body></html>";
	$send = '';

	$connect = mysql_connect($servidor, $utilizador, $chavepass);
	if (!$connect) die('Estamos em manutenção, tente mais tarde.');
	
	mysql_query("SET NAMES 'utf8'");
	mysql_query('SET character_set_connection=utf8');
	mysql_query('SET character_set_client=utf8');
	mysql_query('SET character_set_results=utf8');
	
	function listarDispositivos(){
		global $servidor;
		global $basedados;
		global $utilizador;
		global $chavepass;

		$connect = mysql_connect($servidor, $utilizador, $chavepass);
		if (!$connect) die('Estamos em manutenção, tente mais tarde.');

		mysql_select_db($basedados, $connect);
		$sql = "SELECT * FROM tbl_devices ORDER BY device_id ASC";
		$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');

		global $html_header;
		echo $html_header;

		global $html_footer;
		echo $html_footer;

		while ($dev = mysql_fetch_array($resultado, MYSQL_ASSOC)){
			echo $dev['device_id'] . ' - ';
			echo $dev['device_name'];
			echo '<br />';
		}
	}

	function acionarDispositivo($disp, $action, $fromVoice){
		global $servidor;
		global $basedados;
		global $utilizador;
		global $chavepass;

		$connect = mysql_connect($servidor, $utilizador, $chavepass);
		if (!$connect) die('Estamos em manutenção, tente mais tarde.');
		
		mysql_query("SET NAMES 'utf8'");
		mysql_query('SET character_set_connection=utf8');
		mysql_query('SET character_set_client=utf8');
		mysql_query('SET character_set_results=utf8');
	
		mysql_select_db($basedados, $connect);
		$sql = "SELECT * FROM configs WHERE id = ".(int)1;
		$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
		$configs = mysql_fetch_array($resultado, MYSQL_ASSOC);
	
		$ip    = $configs['ip'];
		$porta = $configs['porta'];
		
		$sql2 = "SELECT * FROM tbl_devices WHERE device_id = ".(int)$disp;
		$resultado2 = mysql_query($sql2) or die ('Estamos em manutenção, tente mais tarde.');
		$dev = mysql_fetch_array($resultado2, MYSQL_ASSOC);
		
		global $send;
		global $offset;

		if($action == 2 && !empty($dev['device_cmd_on'])){
			$send = $dev['device_cmd_on'];
			if($dev['device_type'] == 1) echo printMsg('success',$dev['device_name'].' ligada com sucesso.');
			else if($dev['device_type'] == 2) echo printMsg('success',$dev['device_name'].' aberta com sucesso.');
			else if($dev['device_type'] == 3) echo printMsg('success',$dev['device_name'].' ligado com sucesso.');
			else echo printMsg('success','Dispositivo '.$dev['device_name'].' ligado com sucesso.');
		} else if($action == 3 && !empty($dev['device_cmd_off'])){
			$send = $dev['device_cmd_off'];
			if($dev['device_type'] == 1) echo printMsg('success',$dev['device_name'].' desligada com sucesso.');
			else if($dev['device_type'] == 2) echo printMsg('success',$dev['device_name'].' fechada com sucesso.');
			else if($dev['device_type'] == 3) echo printMsg('success',$dev['device_name'].' desligado com sucesso.');
			else echo printMsg('success','Dispositivo '.$dev['device_name'].' desligado com sucesso.');
		} else if($action == 1 && !empty($dev['device_cmd_toggle'])){
			$send = $dev['device_cmd_toggle'];
			if($dev['device_type'] == 1) echo printMsg('success',$dev['device_name'].' acionada com sucesso.');
			else if($dev['device_type'] == 2) echo printMsg('success',$dev['device_name'].' aberta com sucesso.');
			else if($dev['device_type'] == 3) echo printMsg('success',$dev['device_name'].' acionado com sucesso.');
			else echo printMsg('success','Dispositivo '.$dev['device_name'].' acionado com sucesso.');
		} else{
			if($fromVoice) echo printMsg('error','Não entendi. Tente falar novamente.');
			else echo printMsg('error','Este comando não realiza nenhuma tarefa.');
		}

		require_once("../php/phpws/vendor/autoload.php");

		$loop = \React\EventLoop\Factory::create();

		$logger = new \Zend\Log\Logger();
		$writer = new Zend\Log\Writer\Stream("php://output");
		$logger->addWriter($writer);

		$client = new \Devristo\Phpws\Client\WebSocket('ws://' . $ip . ':' . $porta . '/', $loop, $logger);

		$client->on("connect", function() use ($logger, $client){
			//$logger->notice("Or we can use the connect event!");
			global $send;
			global $offset;

			if(isset($offset) && $offset > 0){
				if($offset > 10) $offset = '10';
				for($i = 0; $i < $offset; $i++){ 
					$client->send($send);
				}
			}
			else $client->send($send);
		});

		$client->on("message", function($message) use ($client, $logger){
			//$logger->notice("Got message: ".$message->getData());
			//$client->close();
			die;
		});

		$client->open()->then(function() use($logger, $client){
			//$logger->notice("We can use a promise to determine when the socket has been connected!");
		});

		$loop->run();
	}

	function verifyCommand($recv){
		global $servidor;
		global $basedados;
		global $utilizador;
		global $chavepass;


		$connect = mysql_connect($servidor, $utilizador, $chavepass);
		if (!$connect) die('Estamos em manutenção, tente mais tarde.');
		
		mysql_query("SET NAMES 'utf8'");
		mysql_query('SET character_set_connection=utf8');
		mysql_query('SET character_set_client=utf8');
		mysql_query('SET character_set_results=utf8');

		mysql_select_db($basedados, $connect);
		$queryVoice = mysql_query("SELECT device_voice_on, device_voice_off, device_id, device_cmd_on, device_cmd_off FROM tbl_devices WHERE device_status = '1'") or die;

		while($row = mysql_fetch_assoc($queryVoice)){
			$voiceOnCommands  = explode(',', $row['device_voice_on']);
			$voiceOnCommands  = array_map('trim',$voiceOnCommands);
			$voiceOffCommands = explode(',', $row['device_voice_off']);
			$voiceOffCommands = array_map('trim',$voiceOffCommands);

			if(in_array($recv,$voiceOnCommands)){
		        acionarDispositivo($row['device_id'],2,true);
		        $voice_found = true;
		    }
		    if(in_array($recv,$voiceOffCommands)){
		    	acionarDispositivo($row['device_id'],3,true);
		    	$voice_found = true;
		    }
		}

		if(!isset($voice_found)) echo printMsg('error','Não entendi. Tente falar novamente.');
	}

	function printMsg($status,$message){
		global $html_header;
		global $html_footer;
		if(!empty($_GET['isAjax']) && $_GET['isAjax'] == 1 && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && !empty($_GET))
			return json_encode(array('status' => $status,'message'=> $message));
		else return $html_header.$message.$html_footer;
	}

	$key		= (isset($_GET['key'])) ? trim(strip_tags($_GET['key'])) : '';
	$device		= (isset($_GET['device'])) ? trim(strip_tags($_GET['device'])) : '';
	$recv		= (isset($_GET['recv'])) ? trim(strip_tags($_GET['recv'])) : '';
	$action		= (isset($_GET['action'])) ? trim(strip_tags($_GET['action'])) : '';
	$offset		= (isset($_GET['offset'])) ? trim(strip_tags($_GET['offset'])) : '';
	$help		= (isset($_GET['help'])) ? trim(strip_tags($_GET['help'])) : '';
	$list		= (isset($_GET['list'])) ? trim(strip_tags($_GET['list'])) : '';

	if(isset($_GET['help'])) 
		echo printMsg('error','
			<b>key:</b> chave de api (0-9|a-z|A-Z)<br />
			<b>device:</b> número referente ao dispositivo para acionamento (0-9)<br />
			<b>action:</b> 1 - toggle, 2 - ligar, 3 - desligar dispositivo (0-9)<br />
			<b>offset:</b> multiplicador de acionamento (0-9)<br />
			<b>list:</b> lista todos os dispositivos cadastrados (id - título)
			<b>ver:</b> 1.0.0
		');
	else if(isset($_GET['list'])) listarDispositivos();
	else{
		if(isset($key) && !empty($key)){
			if($key == $settings['api_key']){
				if(isset($device) && !empty($device)){
					if($device == '') echo printMsg('error','Informe um dispositivo para acionamento (device).');
					else{
						if(isset($action)){
							if($action == '') echo printMsg('error','Informe uma ação a ser executada (action).');
							else{
								acionarDispositivo($device,$action);
							}
						} else echo printMsg('error','Informe uma ação a ser executada (action).');
					}
				}
				else if(isset($recv) && !empty($recv)){
					verifyCommand($recv);
				} else echo printMsg('error','Informe um dispositivo para acionamento (device).');
			} else echo printMsg('error','Informe uma chave de api válida (key).');
		} else echo printMsg('error','Use o parâmetro <b>help</b> para listar os comandos disponíveis.');
	}
?>