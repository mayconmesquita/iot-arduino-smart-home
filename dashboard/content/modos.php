<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 5){
	
	if(isset($_SESSION['status'])) $status = $_SESSION['status']; else $status = '';
	include('config/connect_bd.php');
	mysql_select_db($basedados, $connect);
	$sql = "SELECT * FROM modos ORDER BY modo_id DESC";
	$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');

	if($status == 1){
		echo"
		<div class=\"divOk\">
			<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
				<tr>
					<td class=\"imgOk\">&nbsp;</td>
					<td class=\"conteudosDiv\">Modo excluído com sucesso!</td>
				</tr>
			</table>
		</div>";
		unset($_SESSION['status']);
	}
	else if($status == 2){
		echo"
	<div class=\"divErro\">
		<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
			<tr>
				<td class=\"imgErro\">&nbsp;</td>
				<td class=\"conteudosDiv\">Erro ao excluir modo!</td>
			</tr>
		</table>
	</div>";
	unset($_SESSION['status']);
	}
	else if($status == 3){
		echo"
	<div class=\"divOk\">
			<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
				<tr>
					<td class=\"imgOk\">&nbsp;</td>
					<td class=\"conteudosDiv\">Modo adicionado com sucesso!</td>
				</tr>
			</table>
		</div>";
		unset($_SESSION['status']);
	}
	else if($status == 4){
		echo"
	<div class=\"divErro\">
		<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
			<tr>
				<td class=\"imgErro\">&nbsp;</td>
				<td class=\"conteudosDiv\">Erro ao adicionar modo!</td>
			</tr>
		</table>
	</div>";
	unset($_SESSION['status']);
	}
	else if($status == 5){
		echo"
		<div class=\"divOk\">
			<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
				<tr>
					<td class=\"imgOk\">&nbsp;</td>
					<td class=\"conteudosDiv\">Modo alterado com sucesso!</td>
				</tr>
			</table>
		</div>";
		unset($_SESSION['status']);
	}
	else if($status == 6){
		echo"<div class=\"divErro\">
		<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
			<tr>
				<td class=\"imgErro\">&nbsp;</td>
				<td class=\"conteudosDiv\">Erro ao alterar modo!</td>
			</tr>
		</table>
	</div>";
	unset($_SESSION['status']);
	}

	else{unset($_SESSION['status']);}
?>

<p><a class="link btn btn-default" role="button" href="?p=modos&amp;sub=1">Adicionar novo</a></p>

<?php
	echo"<div class=\"box-datagrid\">
		<table cellspacing=\"0\" class=\"gridPadrao\">
			<thead>            
				<tr>
					<th>Modo</th>
				</tr>
			</thead>";

	while ($linha = mysql_fetch_array($resultado, MYSQL_ASSOC)){
		if(isset($coralternada)) $coralternada = $coralternada; else $coralternada = '';
		$cor1 = 'tr1';
		$cor2 =  'tr2';
		$cor = ($coralternada++ %2 ? $cor2 : $cor1);
		
		$nome = $linha['modo_nome'];
		$ordem = $linha['modo_ordem'];
		
		echo"<tbody>";
				echo"<tr class=\"$cor\">;
						<td>
							<h3 class=\"titTableGrid\">$nome<p></p></h3>
						</td>
						<td class=\"alignCenter\">$ordem</td>
					</tr>
				<tr>
					<td class=\"colAcoes\" colspan='99'>
						<div class=\"acoes\">
							<span class=\"edit\">
	<a class=\"link\" title=\"Clique aqui para alterar este modo\" href=\"?p=modos&amp;sub=1&amp;id={$linha['modo_id']}\" alt = \"update\" onmouseover=\"tip('Clique aqui para alterar este modo',this);\" onmouseout=\"notip();\">Editar</a> |</span>";
					echo"	<span class=\"edit\">
	<a class=\"link\" title=\"Clique aqui para excluir este modo\" href=\"content/modos/excluir.php?id={$linha['modo_id']}\" alt = \"delete\" "; echo" onclick = \"if(!confirm('Tem certeza que quer excluir este modo?')){ return false; };\" onmouseover=\"tip('Clique aqui para excluir este modo',this);\" onmouseout=\"notip();\"> Excluir</a></span>";
						echo"</div>
					</td>
				</tr>
			</tbody>";
	}
?>
    	<tfoot>
        	<tr>
                <th>Modo</th>
        	</tr>
    	</tfoot>
	</table>
</div>
<?php } ?>