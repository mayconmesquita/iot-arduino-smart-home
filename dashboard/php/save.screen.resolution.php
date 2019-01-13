<?php
	error_reporting(0);
	session_start();
	if(isset($_POST['width']) && isset($_POST['height'])) {
		$_SESSION['screen_width']  = $_POST['width'];
		$_SESSION['screen_height'] = $_POST['height'];
		echo 'success';
	} else echo 'no data';
?>