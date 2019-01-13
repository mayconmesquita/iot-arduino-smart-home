<?php
	include('../config/connect_bd.php');
	mysql_select_db($basedados, $connect);
	$sql = "UPDATE tbl_devices SET
		device_pos_x = '".mysql_real_escape_string(trim(strip_tags($_GET['pos_x'])))."',
		device_pos_y = '".mysql_real_escape_string(trim(strip_tags($_GET['pos_y'])))."',
		device_last_window_width = '".mysql_real_escape_string(trim(strip_tags($_GET['resWidth'])))."',
		device_last_window_height = '".mysql_real_escape_string(trim(strip_tags($_GET['resHeight'])))."'
	WHERE device_id = ".(int)$_GET['device_id'];
	if($sql) echo 'success';
	$resultado = mysql_query($sql) or die;
?>