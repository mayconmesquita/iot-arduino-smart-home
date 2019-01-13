<?php 
	header("Content-type: application/x-javascript");
	session_start();
	include('../../config/connect_bd.php');
	mysql_select_db($basedados, $connect);
	$sql = "SELECT * FROM configs WHERE id = ".(int)1;
	$resultado = mysql_query($sql) or die ('');
	$config = mysql_fetch_array($resultado, MYSQL_ASSOC);
	
	if($_SESSION['permissao_user'] >= 1){
?>
		$(document).ready(function(){
			window.wsIp   = '<?php echo $config['ip'] ?>';
			window.wsPort = '<?php echo $config['porta'] ?>';

			<?php if(isset($_SESSION['nome_user']) && isset($_SESSION['id_user'])){ ?>
				window.wsUserId = <?php echo $_SESSION['id_user'] ?>;
				window.wsUserName = '<?php echo $_SESSION['nome_user'] ?>';
			<?php } else{ ?>
				window.wsUserId = 0;
				window.wsUserName = 'guest';
			<?php } ?>
		});
<?php } ?>