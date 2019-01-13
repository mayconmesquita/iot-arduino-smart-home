<?php
	if($_SESSION['permissao_user'] >= 1){

		$id_user 	= $_SESSION['id_user'];
		$email_user = $_SESSION['email_user'];
		$senha_user = $_SESSION['senha_user'];

		if(!empty($_POST['isAjax']) && $_POST['isAjax'] == 2 && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && !empty($_POST)){
			if(isset($_POST['nome_user_novo']))		$nome_user_novo = $_POST['nome_user_novo'];
			if(isset($_POST['email_user_novo']))	$email_user_novo = $_POST['email_user_novo']; else $email_user_novo = '';
			if(isset($_POST['email_user_novo'])){
				$email_user_novo = strtolower($_POST['email_user_novo']); 
				$_POST['email_user_novo'] = strtolower($_POST['email_user_novo']);
			}

			$Verifica = mysql_query("SELECT * FROM users WHERE email_user = '$email_user_novo'");
			$Resultado = mysql_num_rows($Verifica);

			if(empty($nome_user_novo))
				echo json_encode(array('status' => 'error','message'=> 'Preencha seu nome corretamente.'));
			else if($Resultado > 0 and $_POST['email_user_novo'] != $email_user)
				echo json_encode(array('status' => 'error','message'=> 'O e-mail fornecido já existe em nosso sistema.'));
			else if(empty($email_user_novo))
				echo json_encode(array('status' => 'error','message'=> 'Preencha seu e-mail corretamente.'));
			else{
				$_SESSION['nome_user']	= mysql_real_escape_string(trim(strip_tags($nome_user_novo)));
				$_SESSION['email_user']	= mysql_real_escape_string(trim(strip_tags($email_user_novo)));
				
				$sql = "UPDATE users SET 
					nome_user='".mysql_real_escape_string(trim(strip_tags($nome_user_novo)))."',
					email_user='".mysql_real_escape_string(trim(strip_tags($email_user_novo)))."'
					WHERE email_user = '$email_user' and senha_user = '$senha_user' 
				";
				$resultado = mysql_query($sql) or die;

				echo json_encode(array('status' => 'success','message'=> 'Cadastro atualizado com sucesso!'));
			}
			die;
		} else{
			$sql = "SELECT * FROM users WHERE email_user = '$email_user' and senha_user = '$senha_user' ";
			$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
			$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);
		}
?>
<div id="tabs">
	<ul><li><a href="#tabs-1">Meus dados</a></li></ul>
	<div id="tabs-1">
		<form method="post" role="form">
			<div class="elemento_form">
				<input name="nome_user_novo" class="form-control" placeholder="Nome" id="nome_user_novo" type="text" value="<?php echo $linha['nome_user']; ?>" required />
			</div>
			<div class="elemento_form">
				<input name="email_user_novo" class="form-control" id="email_user_novo" type="text" value="<?php echo $linha['email_user']; ?>" required />
			</div>
			<input type="hidden" name="isAjax" value="2" />
			<button class="btn btn-default" id="save-button" type="submit">Salvar</button>
		</form>
	</div>
</div>
<?php } ?>