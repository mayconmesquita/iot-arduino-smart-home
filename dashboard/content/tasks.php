<?php
	if($_SESSION['permissao_user'] >= 3){
		if(!empty($_POST['isAjax']) && $_POST['isAjax'] == 2 && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && !empty($_POST)){

			$_POST['task_date'] = (isset($_POST['task_date'])) ? $_POST['task_date'] : '';

			if(isset($_POST['moduleName']) && $_POST['moduleName'] == 'tasks'){
				if(isset($_POST['moduleAction']) && $_POST['moduleAction'] == 'delete'){
					if(isset($_POST['id']) && $_POST['id'] > 0){
						$sql = "DELETE FROM tbl_tasks WHERE task_id = ".(int)$_POST['id'];
						$resultado = mysql_query($sql) or die;

						if($resultado) echo json_encode(array('status' => 'success','message'=> 'Tarefa excluída com sucesso!'));
						else echo json_encode(array('status' => 'error','message'=> 'Erro ao excluir tarefa.'));
					}
				}
				else if(isset($_POST['moduleAction']) && $_POST['moduleAction'] == 'select'){
					if(isset($_POST['id']) && $_POST['id'] > 0){
						$sql = "SELECT * FROM tbl_tasks WHERE task_id = ".(int)$_POST['id'];
						$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde [1.1].');
						$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);

						if($resultado) echo json_encode($linha);
						else echo json_encode(array('status' => 'error','message'=> 'Erro ao listar tarefa.'));
					}
				} 
				else if(isset($_POST['moduleAction']) && $_POST['moduleAction'] == 'edit'){
					if(empty($_POST['id']) || $_POST['id'] <= 0)
						echo json_encode(array('status' => 'error','message'=> 'Esta tarefa não existe mais.'));
					else if(empty($_POST['task_device_id']))
						echo json_encode(array('status' => 'error','message'=> 'Informe o dipositivo da tarefa.'));
					else if(empty($_POST['task_time']))
						echo json_encode(array('status' => 'error','message'=> 'Informe o horário da tarefa.'));
					else if(empty($_POST['task_frequency']))
						echo json_encode(array('status' => 'error','message'=> 'Informe a frequência da tarefa.'));
					else if($_POST['task_frequency'] == 4 && $_POST['task_date'] == '')
						echo json_encode(array('status' => 'error','message'=> 'Informe a data da tarefa.'));
					else{
						$sql = "SELECT * FROM tbl_tasks WHERE task_id = ".(int)$_POST['id'];
						$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde [2].');
						$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);

						$sql = "UPDATE tbl_tasks SET
							task_device_id = '".mysql_real_escape_string(trim(strip_tags($_POST['task_device_id'])))."',
							task_action = '".mysql_real_escape_string(trim(strip_tags($_POST['task_action'])))."',
							task_frequency = '".mysql_real_escape_string(trim(strip_tags($_POST['task_frequency'])))."',
							task_date = '".mysql_real_escape_string(trim(strip_tags($_POST['task_date'])))."',
							task_time = '".mysql_real_escape_string(trim(strip_tags($_POST['task_time'])))."',
							task_status = '".(($_POST['task_status']))."'
						WHERE task_id = ".(int)$_POST['id'];	

						$resultado = mysql_query($sql) or die;

						if($resultado) echo json_encode(array('status' => 'success','message'=> 'Tarefa editada com sucesso!'));
						else echo json_encode(array('status' => 'error','message'=> 'Erro ao editar tarefa.'));
					}
				} 
				else if(isset($_POST['moduleAction']) && $_POST['moduleAction'] == 'create'){
					if(empty($_POST['task_device_id']))
						echo json_encode(array('status' => 'error','message'=> 'Informe o dipositivo da tarefa.'));
					else if(empty($_POST['task_time']))
						echo json_encode(array('status' => 'error','message'=> 'Informe o horário da tarefa.'));
					else if(empty($_POST['task_frequency']))
						echo json_encode(array('status' => 'error','message'=> 'Informe a frequência da tarefa.'));
					else if($_POST['task_frequency'] == 4 && $_POST['task_date'] == '')
						echo json_encode(array('status' => 'error','message'=> 'Informe a data da tarefa.'));
					else{
						$sql = "INSERT INTO tbl_tasks (
							task_device_id,
							task_action,
							task_frequency,
							task_date,
							task_time,
							task_status
						) VALUES (
							'".mysql_real_escape_string(trim(strip_tags($_POST['task_device_id'])))."',
							'".mysql_real_escape_string(trim(strip_tags($_POST['task_action'])))."',
							'".mysql_real_escape_string(trim(strip_tags($_POST['task_frequency'])))."',
							'".mysql_real_escape_string(trim(strip_tags($_POST['task_date'])))."',
							'".mysql_real_escape_string(trim(strip_tags($_POST['task_time'])))."',
							'".mysql_real_escape_string(trim(strip_tags($_POST['task_status'])))."'
						)";

						$resultado = mysql_query($sql) or die;

						if($resultado) echo json_encode(array('status' => 'success','message'=> 'Tarefa criada com sucesso!'));
						else echo json_encode(array('status' => 'error','message'=> 'Erro ao criar tarefa.'));
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
				<h4 class="modal-title" id="add-new-modal">Nova tarefa</h4>
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
							<label for="task_device_id" class="control-label">Dispositivo: </label>
							<select name="task_device_id" class="form-control" id="task_device_id">
								<?php
									$sql = "SELECT * FROM tbl_devices ORDER BY device_type ASC";
									$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde [3].');
									while ($dev = mysql_fetch_array($resultado, MYSQL_ASSOC)){
										echo "<option data-device-type=\"{$dev['device_type']}\" value=\"{$dev['device_id']}\" ";
										if(isset($device_id) && $device_id == $dev['device_id']) echo'selected="selected" ';
										echo "class=\"opc_motivo\">{$dev['device_name']}</option>";
									}
								?>
							</select>
						</div>
						
						<div class="form-group">
							<label for="task_action" class="control-label">Ação: </label>
							<select name="task_action" class="form-control" id="task_action"></select>
						</div>
						
						<div class="form-group">
							<label for="task_frequency" class="control-label">Frequência: </label>
							<select name="task_frequency" class="form-control" id="task_frequency">
								<option value="1" class="opc_motivo">Diariamente</option>
								<option value="2" class="opc_motivo">Dias úteis</option>	
								<option value="3" class="opc_motivo">Fins de semana</option>					
								<option value="4" class="opc_motivo">Data específica</option>
							</select>
						</div>
						
						<div class="form-group">
							<label for="task_date" class="control-label">Data: </label>
							<input name="task_date" maxlength="10" class="form-control" placeholder="Data" id="task_date" type="date" />
						</div>
						
						<div class="form-group">
							<label for="task_time" class="control-label">Horário: </label>
							<input name="task_time" maxlength="8" class="form-control" placeholder="Horário" id="task_time" type="time" required />
						</div>
						
						<div class="form-group">
							<label for="status_ativo" class="control-label">Status: </label>
							<div class="radio">
								<label>
									<input checked id="status_ativo" name="task_status"  type="radio" value="1" />
									Ativo
								</label>

								<label>
									<input id="status_inativo" name="task_status" type="radio" value="0" />
									Inativo
								</label>
							</div>
						</div>
					</div>
					<div class="advanced-form" style="display:none"></div>
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
<script>$.ajax({cache:true,url:'assets/js/tasks-config.js?v=2'});</script>
<script>
	$('#data-table').bootstrapTable({
		url: 'api/tasks.php',
	});
</script>
<div id="data-table-toolbar">
	<a class="link btn btn-default" data-toggle="modal" data-target="#add-new-modal" onclick="hideAdvancedForm($('#form-type'));$('.advanced-form').hide();$('#modal-form').trigger('reset');window.actionForm='isAjax=2&moduleName=tasks&moduleAction=create';" role="button">Adicionar nova</a>
</div>
<table id="data-table" data-toolbar="#data-table-toolbar" data-select-item-name="toolbar1" data-card-view="true" data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-row-style="rowStyle" data-toggle="table" data-module="tasks" data-url="api/tasks.php" data-cache="false" data-height="410" data-search="false">
    <thead>
    <tr>
        <th data-field="device_name" data-sortable="true">Dispositivo</th>
        <th data-field="task_action" data-sortable="true">Ação</th>
        <th data-field="task_frequency" data-sortable="true">Frequência</th>
        <th data-field="task_time" data-sortable="true">Horário</th>
        <th data-field="operate" data-formatter="operateFormatter" data-events="operateEvents">Opções</th>
    </tr>
    </thead>
</table>
<?php } } ?>