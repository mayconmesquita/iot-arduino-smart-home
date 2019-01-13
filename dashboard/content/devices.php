<?php
	if($_SESSION['permissao_user'] >= 5){
		if(!empty($_POST['isAjax']) && $_POST['isAjax'] == 2 && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && !empty($_POST)){
			function device_cmd_toggle($device_tipo,$device_ordem){
				$device_cmd = array();
				$device_cmd[0] = 'S';

					 if($device_tipo == 1)  $device_cmd[1] = 'L';
				else if($device_tipo == 2)  $device_cmd[1] = 'P';
				else if($device_tipo == 3)  $device_cmd[1] = 'I';
				else if($device_tipo == 4)  $device_cmd[1] = 'D';
				else if($device_tipo == 5)  $device_cmd[1] = 'D';
				else if($device_tipo == 6)  $device_cmd[1] = 'T';
				else if($device_tipo == 7)  $device_cmd[1] = 'S';
				else if($device_tipo == 8)  $device_cmd[1] = 'Z';
				else if($device_tipo == 9)  $device_cmd[1] = 'Z';
				else if($device_tipo == 10) $device_cmd[1] = 'S';
				   					  else  $device_cmd[1] = 'D';
				
				for($i = 1; $i < $device_ordem; $i++) $device_cmd[$i+1] = 0;

				if($device_ordem == 0) $device_cmd[$device_ordem+2] = 1;
				else $device_cmd[$device_ordem+1] = 1;
				
				$device_cmd = implode($device_cmd);
				
				return $device_cmd;
			}
		
			function device_cmd_on($device_tipo,$device_ordem){
				$device_cmd = array();
				$device_cmd[0] = 'S';

					 if($device_tipo == 1)  $device_cmd[1] = 'L';
				else if($device_tipo == 2)  $device_cmd[1] = 'P';
				else if($device_tipo == 3)  $device_cmd[1] = 'I';
				else if($device_tipo == 4)  $device_cmd[1] = 'D';
				else if($device_tipo == 5)  $device_cmd[1] = 'D';
				else if($device_tipo == 6)  $device_cmd[1] = 'T';
				else if($device_tipo == 7)  $device_cmd[1] = 'S';
				else if($device_tipo == 8)  $device_cmd[1] = 'Z';
				else if($device_tipo == 9)  $device_cmd[1] = 'Z';
				else if($device_tipo == 10) $device_cmd[1] = 'S';
				   					  else  $device_cmd[1] = 'D';
				
				for($i = 1; $i < $device_ordem; $i++) $device_cmd[$i+1] = 0;

				if($device_ordem == 0) $device_cmd[$device_ordem+2] = 2;
				else $device_cmd[$device_ordem+1] = 2;
				
				$device_cmd = implode($device_cmd);
				
				return $device_cmd;
			}
		
			function device_cmd_off($device_tipo,$device_ordem){
				$device_cmd = array();
				$device_cmd[0] = 'S';

					 if($device_tipo == 1)  $device_cmd[1] = 'L';
				else if($device_tipo == 2)  $device_cmd[1] = 'P';
				else if($device_tipo == 3)  $device_cmd[1] = 'I';
				else if($device_tipo == 4)  $device_cmd[1] = 'D';
				else if($device_tipo == 5)  $device_cmd[1] = 'D';
				else if($device_tipo == 6)  $device_cmd[1] = 'T';
				else if($device_tipo == 7)  $device_cmd[1] = 'S';
				else if($device_tipo == 8)  $device_cmd[1] = 'Z';
				else if($device_tipo == 9)  $device_cmd[1] = 'Z';
				else if($device_tipo == 10) $device_cmd[1] = 'S';
				   					  else  $device_cmd[1] = 'D';
				
				for($i = 1; $i < $device_ordem; $i++) $device_cmd[$i+1] = 0;

				if($device_ordem == 0) $device_cmd[$device_ordem+2] = 3;
				else $device_cmd[$device_ordem+1] = 3;
				
				$device_cmd = implode($device_cmd);
				
				return $device_cmd;
			}

			if(isset($_POST['moduleName']) && $_POST['moduleName'] == 'devices'){
				if(isset($_POST['moduleAction']) && $_POST['moduleAction'] == 'delete'){
					if(isset($_POST['id']) && $_POST['id'] > 0){
						$sql = "DELETE FROM tbl_devices WHERE device_id = ".(int)$_POST['id'];
						$resultado = mysql_query($sql) or die;

						if($resultado) echo json_encode(array('status' => 'success','message'=> 'Dispositivo excluído com sucesso!'));
						else echo json_encode(array('status' => 'error','message'=> 'Erro ao excluir dispositivo.'));
					}
				}
				else if(isset($_POST['moduleAction']) && $_POST['moduleAction'] == 'select'){
					if(isset($_POST['id']) && $_POST['id'] > 0){
						$sql = "SELECT * FROM tbl_devices WHERE device_id = ".(int)$_POST['id'];
						$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde [1.1].');
						$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);

						if($resultado) echo json_encode($linha);
						else echo json_encode(array('status' => 'error','message'=> 'Erro ao listar dispositivo.'));
					}
				} 
				else if(isset($_POST['moduleAction']) && $_POST['moduleAction'] == 'edit'){
					$device_cmd_toggle = device_cmd_toggle($_POST['device_type'],$_POST['device_order']);
					$device_cmd_on     = device_cmd_on($_POST['device_type'],$_POST['device_order']);
					$device_cmd_off    = device_cmd_off($_POST['device_type'],$_POST['device_order']);

					if(empty($_POST['device_type']) || $_POST['device_type'] < 1 || $_POST['device_type'] > 10)
						echo json_encode(array('status' => 'error','message'=> 'Informe o tipo do dispositivo.'));
					else if(empty($_POST['device_name']))
						echo json_encode(array('status' => 'error','message'=> 'Informe o nome do dispositivo.'));
					else if($_POST['device_status'] == '' || $_POST['device_status'] != 0 && $_POST['device_status'] != 1)
						echo json_encode(array('status' => 'error','message'=> 'Informe o status do dispositivo.'));
					else{
						$sql = "SELECT * FROM tbl_devices WHERE device_id = ".(int)$_POST['id'];
						$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde [2].');
						$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);

						$sql = "UPDATE tbl_devices SET
							device_order = '".mysql_real_escape_string(trim(strip_tags($_POST['device_order'])))."',
							device_type = '".mysql_real_escape_string(trim(strip_tags($_POST['device_type'])))."',
							device_name = '".mysql_real_escape_string(trim(strip_tags($_POST['device_name'])))."',
							device_cmd_toggle = '".mysql_real_escape_string(trim(strip_tags($device_cmd_toggle)))."',
							device_cmd_on = '".mysql_real_escape_string(trim(strip_tags($device_cmd_on)))."',
							device_cmd_off = '".mysql_real_escape_string(trim(strip_tags($device_cmd_off)))."',
							device_voice_on = '".mysql_real_escape_string(trim(strip_tags($_POST['device_voice_on'])))."',
							device_voice_off = '".mysql_real_escape_string(trim(strip_tags($_POST['device_voice_off'])))."',
							device_status = '".(($_POST['device_status']))."'
						WHERE device_id = ".(int)$_POST['id'];	

						$resultado = mysql_query($sql) or die;

						if($resultado) echo json_encode(array('status' => 'success','message'=> 'Dispositivo editado com sucesso!'));
						else echo json_encode(array('status' => 'error','message'=> 'Erro ao editar dispositivo.'));
					}
				} 
				else if(isset($_POST['moduleAction']) && $_POST['moduleAction'] == 'create'){
					$device_cmd_toggle = device_cmd_toggle($_POST['device_type'],$_POST['device_order']);
					$device_cmd_on     = device_cmd_on($_POST['device_type'],$_POST['device_order']);
					$device_cmd_off    = device_cmd_off($_POST['device_type'],$_POST['device_order']);
					$_POST['device_voice_on']  = (isset($_POST['device_voice_on'])) ? $_POST['device_voice_on'] : '';
					$_POST['device_voice_off'] = (isset($_POST['device_voice_off'])) ? $_POST['device_voice_off'] : '';
					$device_pos_x 	   = '50'; 
					$device_pos_y	   = '50';
					$device_last_window_width	= '1024';
					$device_last_window_height	= '768';

					if(empty($_POST['device_type']) || $_POST['device_type'] < 1 || $_POST['device_type'] > 10)
						echo json_encode(array('status' => 'error','message'=> 'Informe o tipo do dispositivo.'));
					else if(empty($_POST['device_name']))
						echo json_encode(array('status' => 'error','message'=> 'Informe o nome do dispositivo.'));
					else if($_POST['device_status'] == '' || $_POST['device_status'] != 0 && $_POST['device_status'] != 1)
						echo json_encode(array('status' => 'error','message'=> 'Informe o status do dispositivo.'));
					else{
						$sql = "INSERT INTO tbl_devices (
							device_order,
							device_type,
							device_name,
							device_cmd_toggle,
							device_cmd_on,
							device_cmd_off,
							device_voice_on,
							device_voice_off,
							device_pos_x,
							device_pos_y,
							device_last_window_width,
							device_last_window_height,
							device_status
						) VALUES (
							'".mysql_real_escape_string(trim(strip_tags($_POST['device_order'])))."',
							'".mysql_real_escape_string(trim(strip_tags($_POST['device_type'])))."',
							'".mysql_real_escape_string(trim(strip_tags($_POST['device_name'])))."',
							'".mysql_real_escape_string(trim(strip_tags($device_cmd_toggle)))."',
							'".mysql_real_escape_string(trim(strip_tags($device_cmd_on)))."',
							'".mysql_real_escape_string(trim(strip_tags($device_cmd_off)))."',
							'".mysql_real_escape_string(trim(strip_tags($_POST['device_voice_on'])))."',
							'".mysql_real_escape_string(trim(strip_tags($_POST['device_voice_off'])))."',
							'".mysql_real_escape_string(trim(strip_tags($device_pos_x)))."',
							'".mysql_real_escape_string(trim(strip_tags($device_pos_y)))."',
							'".mysql_real_escape_string(trim(strip_tags($device_last_window_width)))."',
							'".mysql_real_escape_string(trim(strip_tags($device_last_window_height)))."',
							'".mysql_real_escape_string(trim(strip_tags($_POST['device_status'])))."'
						)";

						$resultado = mysql_query($sql) or die;

						if($resultado) echo json_encode(array('status' => 'success','message'=> 'Dispositivo criado com sucesso!'));
						else echo json_encode(array('status' => 'error','message'=> 'Erro ao criar dispositivo.'));
					}
					die;
				}
			}
		}
		else{
?>

<div class="modal" id="add-new-modal" tabindex="-1" role="dialog" aria-labelledby="add-new-modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
				<h4 class="modal-title" id="add-new-modal">Novo dispositivo</h4>
			</div>

			<div class="modal-body" style="padding-bottom:0px">
				<div id="error-alert-modal" style="display:none" class="alert alert-danger alert-dismissible" role="alert">
					<i class="fa fa-exclamation-triangle"></i>
					<span id="error-alert-content-modal"></span>
					<button type="button" style="top:0px" class="close" data-hide="alert">
						<span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span>
					</button>
				</div>

				<form id="modal-form" method="post" role="form">
					<div class="basic-form">
						<div class="form-group">
							<label for="device_type" class="control-label">Tipo: </label>
							<select name="device_type" class="form-control" id="device_type">
								<option value="1" class="opc_motivo">Lâmpada</option>
								<option value="2" class="opc_motivo">Porta</option>
								<option value="3" class="opc_motivo">Ar-condicionado</option>
								<option value="4" class="opc_motivo">Eletroeletrônico</option>
								<option value="5" class="opc_motivo">Alarme</option>
								<option value="6" class="opc_motivo">Sensor de temperatura</option>
								<option value="7" class="opc_motivo">Sensor de corrente</option>
								<option value="8" class="opc_motivo">Sensor de presença</option>
								<option value="9" class="opc_motivo">Sensor de contato</option>
								<option value="10" class="opc_motivo">Sensor de gás</option>
							</select>
						</div>

						<div class="form-group">
							<label for="device_name" class="control-label">Nome: </label>
							<input name="device_name" maxlength="64" class="form-control" id="device_name" type="text" />
						</div>

						<div class="form-group">
							<label for="device_order" class="control-label">Ordem: </label>
							<input name="device_order" maxlength="5" class="form-control" id="device_order" min="0" type="number" />
						</div>

						<div class="form-group">
							<label for="status_ativo" class="control-label">Status: </label>
							<div class="radio">
								<label>
									<input checked id="status_ativo" name="device_status" type="radio" value="1" />
									Ativo
								</label>

								<label>
									<input id="status_inativo" name="device_status" type="radio" value="0" />
									Inativo
								</label>
							</div>
						</div>
					</div>
					<div class="advanced-form" style="display:none">
						<div class="form-group">
							<label for="device_voice_on" class="control-label">Comando de voz para ativar:</label>
							<textarea name="device_voice_on" class="form-control" placeholder="Comandos separados por virgula" id="device_voice_on"></textarea>
						</div>
						<div class="form-group">
							<label for="device_voice_off" class="control-label">Comando de voz para desativar:</label>
							<textarea name="device_voice_off" class="form-control" placeholder="Comandos separados por virgula" id="device_voice_off"></textarea>
						</div>
					</div>
				</form>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" style="float:left" value="advanced" id="form-type">Avançado</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
				<button type="button" id="save-button-modal" class="btn btn-success">Salvar</button>
			</div>
		</div>
	</div>
</div>
<script>$.ajax({cache:true,url:'assets/js/devices-config.js?v=2'});</script>
<script>
	$('#data-table').bootstrapTable({
		url: 'api/devices.php',
	});
</script>
<div id="data-table-toolbar">
	<a class="link btn btn-default" data-toggle="modal" data-target="#add-new-modal" onclick="hideAdvancedForm($('#form-type'));$('#modal-form').trigger('reset');$('#modal-form textarea').val('');window.actionForm='isAjax=2&moduleName=devices&moduleAction=create';" role="button">Adicionar nova</a>
</div>
<table id="data-table" data-toolbar="#data-table-toolbar" data-select-item-name="toolbar1" data-card-view="true" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-row-style="rowStyle" data-toggle="table" data-module="devices" data-url="api/devices.php" data-cache="false" data-height="410" data-search="false">
    <thead>
    <tr>
    	<th data-field="device_ordertype" data-sortable="true">Tipo</th>
    	<th data-field="device_name" data-sortable="true">Dispositivo</th>
        <th data-field="operate" data-formatter="operateFormatter" data-events="operateEvents">Opções</th>
    </tr>
    </thead>
</table>
<?php } } ?>