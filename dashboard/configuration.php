<?php
	$settings['db_host']     = 'localhost'; // Your database host
	$settings['db_name']     = 'your_db_name'; // Your database name
	$settings['db_username'] = 'your_username'; // Your database username
	$settings['db_password'] = 'your_password'; // Your database password
	$settings['docroot']     = '/home/user/public_html/'; // Set to the full path to installation (e.g. /home/user/public_html/)
	$settings['enc_key']     = 'mm9fDU8rhR9ia47jOrWCo8H0N8fPd7fW9SZTlBrJlfNloJZPouQF1SlBntgBzhP'; // No need to change this
	$settings['api_key']     = '12qwaszx'; // Api key
	$settings['def_lang']    = 'portuguese';
	$settings['debug']       = true;

	$mysql_connect = mysql_connect($settings['db_host'], $settings['db_username'], $settings['db_password']);
	if (!$mysql_connect) die;
	
	mysql_query("SET NAMES 'utf8'");
	mysql_query('SET character_set_connection=utf8');
	mysql_query('SET character_set_client=utf8');
	mysql_query('SET character_set_results=utf8');

	/* No need to edit these! */
	if (!defined('DOCROOT')) {
	    define('DOCROOT', $settings['docroot']);
	    define('DEBUG', $settings['debug']);
	}

	date_default_timezone_set('America/Sao_Paulo');

	if ($settings['debug']) error_reporting(E_ALL);
	else error_reporting(E_ERROR);
?>