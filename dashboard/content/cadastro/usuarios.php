<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 4){
	
	include('config/connect_bd.php');
	mysql_select_db($basedados, $connect);
	
	if($_SESSION['permissao_user'] == 4){
		$sql = "SELECT * FROM users WHERE permissao_user <= '3' ORDER BY 'id_user' DESC";
		$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
	}
	else if($_SESSION['permissao_user'] == 5){
		$sql = "SELECT * FROM users WHERE permissao_user <= '4' ORDER BY 'id_user' DESC";
		$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
	}

		 if(isset($_SESSION['msg_status']) && $_SESSION['msg_status'] == 1) echo"<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><i class=\"fa fa-check\"></i>&nbsp;Usuário editado com sucesso!</div>";
	else if(isset($_SESSION['msg_status']) && $_SESSION['msg_status'] == 2) echo"<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><i class=\"fa fa-check\"></i>&nbsp;Usuário criado com sucesso!</div>";
	else if(isset($_SESSION['msg_status']) && $_SESSION['msg_status'] == 3) echo"<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><i class=\"fa fa-check\"></i>&nbsp;Usuário excluído com sucesso!</div>";
	else if(isset($_SESSION['msg_status']) && $_SESSION['msg_status'] == 4) echo"<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><i class=\"fa fa-exclamation-triangle\"></i>&nbsp;Você não tem permissão para esta ação.</div>";
	else unset($_SESSION['msg_status']);
		 unset($_SESSION['msg_status']);

?>

<p><a class="link btn btn-default" role="button" href="novo_usuario">Adicionar novo</a></p>

<?php
	echo"<div class=\"box-datagrid\">
		<table cellspacing=\"0\" class=\"gridPadrao\">
			<thead>            
				<tr>
					<th width=\"15\">Status</th>
					<th width=\"15\">Logado</th>
					<th width=\"80\">Permissão</th>
					<th width=\"300\">Nome</th>
					<th width=\"240\">Email</th>
					<th width=\"240\">Cadastro</th>
					<th width=\"240\">Último Login</th>
				</tr>
			</thead>
	";

	while ($linha = mysql_fetch_array($resultado, MYSQL_ASSOC) and $_SESSION['permissao_user'] >= 4){
		$perm = $linha['permissao_user'];
		if($perm == 1) $perm_status = 'Visitante'; 
		else if($perm == 2) $perm_status = 'Membro'; 
		else if($perm == 3) $perm_status = 'Moderador'; 
		else if($perm == 4) $perm_status = 'Administrador'; 
		else if($perm == 5) $perm_status = 'Super Administrador'; 
		else $perm_status = 'Desconhecido';

		if(isset($coralternada)) $coralternada = $coralternada; else $coralternada = '';
		$cor1 = 'tr1';
		$cor2 =  'tr2';
		$cor = ($coralternada++ %2 ? $cor2 : $cor1);
			
		$novadata1 = substr($linha['data_cadastro'],8,2) . "/" .
		substr($linha['data_cadastro'],5,2) . "/" . 
		substr($linha['data_cadastro'],0,4);

		$novahora1 = substr($linha['hora_cadastro'],0,2) . "h" .
		substr($linha['hora_cadastro'],3,2) . "min";
		
		$novadata2 = substr($linha['data_ultimo_login'],8,2) . "/" .
		substr($linha['data_ultimo_login'],5,2) . "/" . 
		substr($linha['data_ultimo_login'],0,4);

		$novahora2 = substr($linha['hora_ultimo_login'],0,2) . "h" .
		substr($linha['hora_ultimo_login'],3,2) . "min";
		
		$tempo = time();
		date_default_timezone_set('America/Fortaleza');
		$tempo_atual  = date("H",$tempo);
		$tempo_atual .= ':';
		$tempo_atual .= date("i",$tempo);
		$tempo_atual .= ':';
		$tempo_atual .= date("s",$tempo);

		$dataFuturo = $linha['ping_user'];
		$dataAtual  = $tempo_atual;

		$date_time  = new DateTime($dataAtual);
		$diff       = $date_time->diff( new DateTime($dataFuturo));
		$diff_horas = $diff->format('%H');
		$diff_minutos  = $diff->format('%i');
		$diff_segundos = $diff->format('%s');
		
		$diff_total = ($diff_minutos * 60) + ($diff_horas * 3600) + $diff_segundos;
		/*****************************************************************************/			
			echo"<tbody>";
					echo"
					<tr class=\"$cor\">";
						echo"<td class=\"form_td_status\" rowspan=\"2\">";
						if(($linha['status_user']) == 1){	
							echo"<div class=\"legenda legenda_true\"><span>1</span></div></td>";
						}
						else{
							echo"<div class=\"legenda legenda_false\"><span>0</span></div></td>";
						}
						echo"<td class=\"form_td_status\" rowspan=\"2\">";
						
						if($diff_total < 25) echo"<div class=\"legenda legenda_true\"><span>1</span></div></td>";
						else 				echo"<div class=\"legenda legenda_false\"><span>0</span></div></td>";
						
						echo"
						<td class=\"form_td_status\" rowspan=\"2\">$perm_status</td>
						<td><h3 class=\"titTableGrid\">{$linha['nome_user']}</h3></td>
						<td>{$linha['email_user']}</td>
						<td>$novadata1 | $novahora1</td>
						<td>$novadata2 | $novahora2</td>
					</tr>
					<tr>
						<td class=\"colAcoes\" colspan=\"6\">
							<div class=\"acoes\">
								<span class=\"edit\">
		<a class=\"link\" title=\"Clique aqui para editar este usuário\" href=\"editar_usuario/{$linha['id_user']}\" alt = \"Editar\" onmouseover=\"tip('Clique aqui para editar este usuário',this);\" onmouseout=\"notip();\">Editar</a> |</span>";//Acaba o primeiro ECHO
						echo"	<span class=\"edit\">
		<a class=\"link\" title=\"Clique aqui para excluir este usuário\" href=\"content/cadastro/excluir_usuario.php?id={$linha['id_user']}\" alt = \"Excluir\" "; echo" onclick = \"if(!confirm('Tem certeza que quer excluir este usuário?')){ return false; };\" onmouseover=\"tip('Clique aqui para excluir este usuário',this);\" onmouseout=\"notip();\"> Excluir</a></span>";
							echo"</div>
						</td>
					</tr>
				</tbody>";
			}
?>
    	<tfoot>
        	<tr>
            	<th width="15">Status</th>
				<th width="15">Logado</th>
				<th width="80">Permissão</th>
                <th width="300">Nome</th>
				<th width="240">Email</th>
                <th width="240">Cadastro</th>
				<th width="240">Último Login</th>
        	</tr>
    	</tfoot>
	</table>
</div>
<?php } ?>