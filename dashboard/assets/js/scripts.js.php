<?php header("Content-type: application/x-javascript"); ?>
$(document).ready(function() {
	$(document).tooltip({
		track: true,
		show:{
			effect: "fade",
			delay: 300
		}
	});
	
	$('.device').tooltip({
		show:{
			effect: "fade",
			delay: 2000
		},
		hide:{
			effect: "fade",
			delay: 750
		}
	});

	$( "#tabs" ).tabs();

	var refreshId = setInterval(
		function() {
			$.get('login/verifica_log.php?randval=', function(data) { 
				if(data == 1) window.location.href="../../login/sair.php";
			});
		}, <?php echo $_GET['refresh']; ?>
	);
	$.ajaxSetup({ cache: false });
});