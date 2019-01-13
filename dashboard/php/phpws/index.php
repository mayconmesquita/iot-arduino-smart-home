<?php
	error_reporting(0);
	require_once("vendor/autoload.php");

	$loop = \React\EventLoop\Factory::create();

	$logger = new \Zend\Log\Logger();
	$writer = new Zend\Log\Writer\Stream("php://output");
	$logger->addWriter($writer);

	$client = new \Devristo\Phpws\Client\WebSocket('ws://put_your_vps_ip_here:8080/', $loop, $logger);

	$client->on("connect", function() use ($logger, $client){
		//$logger->notice("Or we can use the connect event!");
		$client->send("");
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
?>