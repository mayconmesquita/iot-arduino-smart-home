<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 5){

	if(isset($_SESSION['status'])) $status = $_SESSION['status']; else $status = '';
	if(isset($_GET['id'])) $_GET['id'] = $_GET['id']; else $_GET['id'] = '';

	include('config/connect_bd.php');
	mysql_select_db($basedados, $connect);
	$sql = "SELECT * FROM devices WHERE device_id = ".(int)$_GET['id'];
	$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
	$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);

	if($status == 100) 		echo"<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Informe o tipo do dispositivo.</div>";
	else if($status == 200) echo"<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Informe o nome do dispositivo.</div>";
	else if($status == 300) echo"<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Informe a ordem do dispositivo.</div>";

	if($_SESSION['status'] > 0){
		$device_tipo = $_SESSION['device_tipo'];
		unset($_SESSION['device_tipo']);
		$device_nome = $_SESSION['device_nome'];
		unset($_SESSION['device_nome']);
		$device_ordem = $_SESSION['device_ordem'];
		unset($_SESSION['device_ordem']);
		$device_voz_on = $_SESSION['device_voz_on'];
		unset($_SESSION['device_voz_on']);
		$device_voz_off = $_SESSION['device_voz_off'];
		unset($_SESSION['device_voz_off']);
		$device_personalizado = $_SESSION['device_personalizado'];
		unset($_SESSION['device_personalizado']);
	}
	else{ 
		$device_tipo  	      = $linha['device_tipo'];
		$device_nome  	      = $linha['device_nome'];
		$device_ordem 	      = $linha['device_ordem'];
		$device_voz_on        = $linha['device_voz_on'];
		$device_voz_off  	  = $linha['device_voz_off'];
		$device_personalizado = explode('S', $linha['device_cmd_on'], 8);
		$device_personalizado = explode('2', $device_personalizado[1], 3);
		$device_personalizado = substr($device_personalizado[0],0,1);
	}
?>
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Dispositivos</a></li>
	</ul>
	<div id="tabs-1">
		<form id="artigos_form" action="../content/dispositivos/alterar_db.php?id=<?php echo $_GET['id'] ?>" method="post">
			
			<div class="elemento_form">
				<select name="device_tipo" class="form-control" id="device_tipo">
					<option value="1" <?php if(isset($device_tipo) && $device_tipo == 1){ echo 'SELECTED'; } else if(isset($device_tipo) && $device_tipo == ''){echo 'SELECTED';} ?> class="opc_motivo">Lâmpada</option> 
					<option value="2" <?php if(isset($device_tipo) && $device_tipo == 2){ echo 'SELECTED'; } else if(isset($device_tipo) && $device_tipo == ''){echo '';} ?> class="opc_motivo">Porta</option>
					<option value="3" <?php if(isset($device_tipo) && $device_tipo == 3){ echo 'SELECTED'; } else if(isset($device_tipo) && $device_tipo == ''){echo '';} ?> class="opc_motivo">Ar-condicionado</option> 
					<option value="4" <?php if(isset($device_tipo) && $device_tipo == 4){ echo 'SELECTED'; } else if(isset($device_tipo) && $device_tipo == ''){echo '';} ?> class="opc_motivo">Eletroeletrônico</option> 
					<option value="5" <?php if(isset($device_tipo) && $device_tipo == 5){ echo 'SELECTED'; } else if(isset($device_tipo) && $device_tipo == ''){echo '';} ?> class="opc_motivo">Personalizado</option> 
				</select>
			</div>
			
			<div class="elemento_form input-group">
				<input required="" name="device_nome" class="form-control" placeholder="Nome do dispositivo" maxlength="40" id="device_nome" type="text" value="<?php if(isset($device_nome)) echo $device_nome ?>" />
				<span title="Ex: Lâmpada da sala" class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>
			</div>

			<div class="elemento_form input-group">
				<input required="" name="device_ordem" class="form-control" placeholder="Ordem do dispositivo" maxlength="5" id="device_ordem" type="number" value="<?php if(isset($device_ordem)) echo $device_ordem ?>" />
				<span title="Ex: número do dispositivo (1, 2, 3...)" class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>
			</div>

			<div class="elemento_form input-group">
				<input name="device_personalizado" class="form-control" maxlength="1" placeholder="Letra de identificação" id="device_personalizado" type="text" value="<?php if(isset($device_personalizado)) echo $device_personalizado ?>" />
				<span title="Ex: L, P, I, X... (apenas para dispositivos personalizados)" class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>
			</div>
			
			<div class="elemento_form input-group">
				<textarea placeholder="Comandos de voz para ligar (separadas por vírgula)" name="device_voz_on" class="form-control" id="device_voz_on" /><?php if(isset($device_voz_on)) echo $device_voz_on ?></textarea>
				<span title="Ex: Acender Lâmpada do quarto" class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>
			</div>
			
			<div class="elemento_form input-group">
				<textarea placeholder="Comandos de voz para desligar (separadas por vírgula)" name="device_voz_off" class="form-control" id="device_voz_off" /><?php if(isset($device_voz_off)) echo $device_voz_off ?></textarea>
				<span title="Ex: Apagar Lâmpada do quarto" class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>
			</div>

			<p><button class="btn btn-default" role="button" name="salvar" type="submit">Salvar</button></p>
		</form>
	</div>
</div>
<?php } ?>