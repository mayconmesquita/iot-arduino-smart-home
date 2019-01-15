<?php
	error_reporting(0);
	session_start();
	
	$app 	= $_SESSION['app'];
	$mobile = $_SESSION['mobile'];

	session_unset();
	session_destroy();

	if ($mobile && !empty($app)) {

		$_SESSION['kick'] = '1';

		header('Location: ../login.php?app=' . $app);

	} else {

		$_SESSION['kick'] = '1';

		header('Location: ../login.php');

	}
?>