<?php
	if ($_SESSION['permissao_user'] >= 5){
		if (!empty($_POST['isAjax']) && $_POST['isAjax'] == 2 && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && !empty($_POST)){
			if (empty($_POST['title']))
				echo json_encode(array('status' => 'error','message'=> 'Título do painel não pode ficar em branco.'));
			else if(empty($_POST['url']))
				echo json_encode(array('status' => 'error','message'=> 'URL do painel não pode ficar em branco.'));
			else if(empty($_POST['ip']))
				echo json_encode(array('status' => 'error','message'=> 'IP não pode ficar em branco.'));
			else if(empty($_POST['porta']))
				echo json_encode(array('status' => 'error','message'=> 'Porta não pode ficar em branco.'));
			else if(empty($_POST['tempo_receber']))
				echo json_encode(array('status' => 'error','message'=> 'Tempo de recepção não pode ficar em branco.'));
			else{
				$_SESSION['title']			= $_POST['title'];
				$_SESSION['ip']				= $_POST['ip'];
				$_SESSION['porta']			= $_POST['porta'];
				$_SESSION['tempo_receber']	= $_POST['tempo_receber'];
				$_SESSION['url']			= $_POST['url'];

				$sqlConfig = "UPDATE configs SET 
					id=1,
					title='".mysql_real_escape_string(trim(strip_tags($_POST['title'])))."',
					ip='".mysql_real_escape_string(trim(strip_tags($_POST['ip'])))."',
					porta='".mysql_real_escape_string(trim(strip_tags($_POST['porta'])))."',
					tempo_receber='".mysql_real_escape_string(trim(strip_tags($_POST['tempo_receber'])))."',
					url='".mysql_real_escape_string(trim(strip_tags($_POST['url'])))."'
				";
				$resultadoConfig = mysql_query($sqlConfig) or die;

				echo json_encode(array('status' => 'success','message'=> 'Configurações salvas com sucesso!'));
			}
			die;
		} else{
			$sqlConfig = 'SELECT * FROM configs';
			$resultadoConfig = mysql_query($sqlConfig) or die ($lang['err_query']);
			$linhaConfig = mysql_fetch_array($resultadoConfig, MYSQL_ASSOC);
		}
?>
<div id="tabs">
	<ul><li><a href="#tabs-1">Configurações</a></li></ul>
	<div id="tabs-1">
		<form method="post" role="form">
			<div class="elemento_form">
				<input required name="title" class="form-control" placeholder="Título do painel" maxlength="40" id="title" type="text" value="<?php if(isset($linhaConfig['title'])) echo $linhaConfig['title'] ?>" />
			</div>
			<div class="elemento_form">
				<input required name="url" class="form-control" placeholder="Url do painel" maxlength="255" id="url" type="text" value="<?php if(isset($linhaConfig['url'])) echo $linhaConfig['url'] ?>" />
			</div>
			<div class="elemento_form">
				<input required name="ip" class="form-control" placeholder="Websocket Host" maxlength="48" type="text" id="ip" type="number" value="<?php if(isset($linhaConfig['ip'])) echo $linhaConfig['ip'] ?>" />
			</div>
			<div class="elemento_form">
				<input required name="porta" class="form-control" placeholder="Websocket Port" maxlength="5" id="porta" min="0" type="number" value="<?php if(isset($linhaConfig['porta'])) echo $linhaConfig['porta'] ?>" />
			</div>
			<div class="elemento_form input-group">
				<input required name="tempo_receber" class="form-control" placeholder="Tempo de recepção" maxlength="8" type="number" min="0" id="tempo_receber" type="text" value="<?php if(isset($linhaConfig['tempo_receber'])) echo $linhaConfig['tempo_receber'] ?>" />
				<span class="input-group-addon">milissegundos</span>
			</div>
			<input type="hidden" name="isAjax" value="2" />
			<button class="btn btn-default" id="save-button" type="submit">Salvar</button>
		</form>
	</div>
</div>
<?php } ?>
