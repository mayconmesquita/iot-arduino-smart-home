<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 1){
?>	
<script type="text/javascript">
	var digital = new Date();
	digital.setHours(<?php echo date("H,i,s"); date_default_timezone_set('America/Fortaleza'); ?>);

	function clock() {
		var hours = digital.getHours();
		var minutes = digital.getMinutes();
		var seconds = digital.getSeconds();
		digital.setSeconds(seconds + 1);

		if (hours   <= 10) hours   = "0" + hours;
		if (minutes <= 9)  minutes = "0" + minutes;
		if (seconds <= 9)  seconds = "0" + seconds;

		dispTime = hours + ":" + minutes + ":" + seconds;
		//document.getElementById("time").innerHTML = '(' +dispTime+ ')';
		setTimeout("clock()", 1000);
	}

	window.onload = clock;
</script>
<?php } ?>