<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 5){
	if(isset($_SESSION['status'])) $status = $_SESSION['status']; else $status = '';
	if(isset($_GET['id'])) $_GET['id'] = $_GET['id']; else $_GET['id'] = '';

	include('config/connect_bd.php');
	mysql_select_db($basedados, $connect);
	$sql = "SELECT * FROM modos WHERE modo_id = ".(int)$_GET['id'];
	$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
	$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);

	if($status == 100){
		echo"
			<div class=\"divErro\">
				<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
					<tr>
						<td class=\"imgErro\">&nbsp;</td>
						<td class=\"conteudosDiv\">Informe o nome do modo!</td>
					</tr>
				</table>
			</div>
		";

	}
	else if($status == 200){
		echo"
			<div class=\"divErro\">
				<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
					<tr>
						<td class=\"imgErro\">&nbsp;</td>
						<td class=\"conteudosDiv\">Informe a sequência do modo!</td>
					</tr>
				</table>
			</div>
		";
	}

	if($_SESSION['status'] > 0){
		$modo_nome = $_SESSION['modo_nome'];
		unset($_SESSION['modo_nome']);
		$modo_seq = $_SESSION['modo_seq'];
		unset($_SESSION['modo_seq']);
	}
	else{ 
		$modo_nome  = $linha['modo_nome'];
		$modo_ordem = $linha['modo_seq'];
	}
?>

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Modos</a></li>
	</ul>
	<div id="tabs-1">
		<form id="modos_form" action="content/modos/alterar_db.php?id=<?php echo $_GET['id'] ?>" method="post">
			
			<div class="elemento_form">
				<p title="Preenchimento obrigatório"><label for="modo_nome">Nome do modo</label><span title="Preenchimento obrigatório" class="asterisc_important">*</span></p>
				<input name="modo_nome" class="campo_pequeno" id="modo_nome" type="text" value="<?php if(isset($modo_nome)) echo $modo_nome ?>" /><span class="dica_form">Ex: Cinema, Acordar</span>
			</div>
			
			<div class="elemento_form">
				<p title="Preenchimento obrigatório"><label for="modo_seq">Sequência do modo</label><span title="Preenchimento obrigatório" class="asterisc_important">*</span></p>
				<input name="modo_seq" class="campo_pequeno" id="modo_seq" type="text" value="<?php if(isset($modo_seq)) echo $modo_seq ?>" /><span class="dica_form"></span>
			</div>

			<input id="salvar" name="salvar" type="submit" value="" />	
		</form>
	</div>
</div>
<?php } ?>