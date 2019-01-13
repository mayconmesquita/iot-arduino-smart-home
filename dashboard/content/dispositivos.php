<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 5){

	if(isset($_SESSION['status'])) $status = $_SESSION['status']; else $status = '';
	include('config/connect_bd.php');
	mysql_select_db($basedados, $connect);
	$sql = "SELECT * FROM devices ORDER BY device_id DESC";
	$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');

		 if($status == 1) echo"<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><i class=\"fa fa-check\"></i>&nbsp;Dispositivo excluído com sucesso!</div>";
	else if($status == 2) echo"<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><i class=\"fa fa-exclamation-triangle\"></i>&nbsp;Erro ao excluir dispositivo!</div>";
	else if($status == 3) echo"<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><i class=\"fa fa-check\"></i>&nbsp;Dispositivo adicionado com sucesso!</div>";
	else if($status == 4) echo"<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><i class=\"fa fa-exclamation-triangle\"></i>&nbsp;Erro ao adicionar dispositivo!</div>";
	else if($status == 5) echo"<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><i class=\"fa fa-check\"></i>&nbsp;Dispositivo alterado com sucesso!</div>";
	else if($status == 6) echo"<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><i class=\"fa fa-exclamation-triangle\"></i>&nbsp;Erro ao alterar dispositivo!</div>";
	else unset($_SESSION['status']);
		 unset($_SESSION['status']);
?>

<p><a class="link btn btn-default btAjaxLoader" role="button" href="dispositivo">Adicionar novo</a></p>

<style>.device_tipo{ margin: 0px auto; }</style>
<?php
	echo"<div class=\"box-datagrid\">
		<table cellspacing=\"0\" class=\"gridPadrao\">
			<thead>            
				<tr>
					<th class=\"alignCenter\" width=\"120\">Tipo</th>
					<th width=\"450\">Dispositivo</th>
					<th class=\"alignCenter\">Ordem</th>
				</tr>
			</thead>";

	while ($linha = mysql_fetch_array($resultado, MYSQL_ASSOC)){
		if(isset($coralternada)) $coralternada = $coralternada; else $coralternada = '';
		$cor1 = 'tr1';
		$cor2 =  'tr2';
		$cor = ($coralternada++ %2 ? $cor2 : $cor1);
		
		$nome = $linha['device_nome'];
		$ordem = $linha['device_ordem'];
		
		echo"<tbody>";
				echo"<tr class=\"$cor\">";
					echo"<td style=\"text-align:center\" class=\"form_td_status\" rowspan=\"2\"><a class=\"link\" href=\"dispositivo/{$linha['device_id']}\">";
					
					if(($linha['device_tipo']) == 1){ 
						echo"<div><img height=\"80\" class=\"device_tipo\" src=\"assets/images/devices/lamp.png\"/><span>Lâmpada</span></div></td>";
					}
					else if(($linha['device_tipo']) == 2){
						echo"<div><img height=\"80\" class=\"device_tipo\" src=\"assets/images/devices/lock.png\"/><span>Porta</span></div></td>";
					}
					else if(($linha['device_tipo']) == 3){
						echo"<div><img height=\"80\" class=\"device_tipo\" src=\"assets/images/devices/computer.png\"/><span>Ar-condicionado</span></div></td>";
					}
					else if(($linha['device_tipo']) == 4){
						echo"<div><img height=\"80\" class=\"device_tipo\" src=\"assets/images/devices/computer.png\"/><span>Eletroeletrônico</span></div></td>";
					}
					else if(($linha['device_tipo']) == 5){
						echo"<div><img height=\"80\" class=\"device_tipo\" src=\"assets/images/devices/custom.png\"/><span>Personalizado</span></div></td>";
					}
					else
						echo"<div><img height=\"80\" class=\"device_tipo\" src=\"assets/images/devices/unknown.png\"/><span>Desconhecido</span></div></a></td>";

					 echo"<td>
						<h3 class=\"titTableGrid\">$nome<p></p></h3>
					</td>
					<td class=\"alignCenter\">$ordem</td>
				</tr>
				<tr>
					<td class=\"colAcoes\" colspan='2'>
						<div class=\"acoes\">
							<span class=\"edit\">
	<a class=\"link\" title=\"Clique aqui para alterar este dispositivo\" href=\"dispositivo/{$linha['device_id']}\" alt = \"update\">Editar</a> |</span>";
					echo"	<span class=\"edit\">
	<a class=\"link\" title=\"Clique aqui para excluir este dispositivo\" href=\"content/dispositivos/excluir.php?id={$linha['device_id']}\" alt = \"delete\" "; echo" onclick = \"if(!confirm('Tem certeza que quer excluir este dispositivo?')){ return false; };\"> Excluir</a></span>";
						echo"</div>
					</td>
				</tr>
			</tbody>";
	}
?>
    	<tfoot>
        	<tr>
            	<th class="alignCenter" width="120">Tipo</th>
                <th width="450">Dispositivo</th>
                <th class="alignCenter">Ordem</th>
        	</tr>
    	</tfoot>
	</table>
</div>
<?php } ?>