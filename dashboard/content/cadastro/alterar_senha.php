<?php
	if($_SESSION['permissao_user'] >= 1){
		if(isset($_SESSION['id_user']))		$id_user 	= $_SESSION['id_user'];
		if(isset($_SESSION['email_user']))	$email_user = $_SESSION['email_user'];
		if(isset($_SESSION['senha_user']))	$senha_user = $_SESSION['senha_user'];

		if(!empty($_POST['isAjax']) && $_POST['isAjax'] == 2 && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && !empty($_POST)){
			$senha_antiga 		= $_POST['senha_antiga'];
			$senha_nova 		= $_POST['senha_nova'];
			$senha_nova_confirm = $_POST['senha_nova_confirm'];
			
			$sqlUser = "SELECT * FROM users WHERE email_user = '$email_user' and senha_user = '$senha_user' ";
			$resultUser = mysql_query($sqlUser) or die;
			$linhaUser = mysql_fetch_array($resultUser, MYSQL_ASSOC);

			$salt = $linhaUser['link_user'];
			$senha_antiga = sha1($senha_antiga . $salt);
			for ($i = 0; $i < 1000; $i++) {
				$senha_antiga = hash("whirlpool", $senha_antiga);
			}
		
			if(empty($senha_antiga))
				echo json_encode(array('status' => 'error','message'=> 'Digite sua senha antiga.'));
			else if($senha_antiga !== $senha_user)
				echo json_encode(array('status' => 'error','message'=> 'Senha antiga errada.'));
			else if(empty($senha_nova))
				echo json_encode(array('status' => 'error','message'=> 'Digite sua nova senha.'));
			else if(empty($senha_nova_confirm))
				echo json_encode(array('status' => 'error','message'=> 'Confirme sua nova senha.'));
			else if($senha_nova !== $senha_nova_confirm)
				echo json_encode(array('status' => 'error','message'=> 'Senhas diferentes.'));
			else{
				////////////Gera um Salt///////////////
				$tamanho   = 23;
				$possible  = '0123456789'; // numbers
				$possible .= 'abcdefghijklmnopqrstuvwxyz'; // lowcase char
				$possible .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // uppercase char
				$possible .= './@#%&^*()'; // symbols

				$salt = '';
				mt_srand((double) microtime() * 1000000);

				$tp = strlen($possible); // total possible chars
				while(strlen($salt) < $tamanho){
					$id       = rand() % $tp;
					$car      = $possible[$id];
					$salt     .= $car;
				}
				$salt = sha1($salt);
				////////////////////////////////////

				// Senha do usuário
				$senha = $_POST['senha_nova'];
				
				// Cria um hash
				$hash = sha1($senha . $salt);

				// Encripta esse hash 1000 vezes
				for ($i = 0; $i < 1000; $i++) {$hash = hash("whirlpool", $hash);}

				$_SESSION["senha_user"] = $hash;
				$sql = "UPDATE users SET 
						senha_user = '".mysql_real_escape_string(trim(strip_tags($hash)))."',
						link_user  = '".mysql_real_escape_string(trim(strip_tags($salt)))."'
					WHERE email_user='$email_user' and senha_user='$senha_user' 
				";
				$resultado = mysql_query($sql) or die;

				echo json_encode(array('status' => 'success','message'=> 'Senha atualizada com sucesso!'));
			}
			die;
		} else{
			$sql = "SELECT * FROM users WHERE email_user = '$email_user' and senha_user = '$senha_user' ";
			$resultado = mysql_query($sql) or die;
			$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);
		}
?>
<div id="tabs">
	<ul><li><a href="#tabs-1">Alterar senha</a></li></ul>
	<div id="tabs-1">
		<form method="post" role="form">
			<div class="elemento_form">
				<input name="senha_antiga" class="form-control" placeholder="Senha antiga" id="senha_antiga" type="password" value="<?php if(isset($_POST['senha_antiga'])) echo $_POST['senha_antiga'] ?>" />
			</div>
			<div class="elemento_form">
				<input name="senha_nova" class="form-control" placeholder="Nova senha" id="senha_nova" type="password" value="<?php if(isset($_POST['senha_nova'])) echo $_POST['senha_nova'] ?>" />
			</div>
			<div class="elemento_form">
				<input name="senha_nova_confirm" class="form-control" placeholder="Confirmar nova senha" id="senha_nova_confirm" type="password" value="<?php if(isset($_POST['senha_nova_confirm'])) echo $_POST['senha_nova_confirm'] ?>" />
			</div>
			<input type="hidden" name="isAjax" value="2" />
			<button class="btn btn-default" id="save-button" type="submit">Salvar</button>
		</form>
	</div>
</div>
<?php } ?>