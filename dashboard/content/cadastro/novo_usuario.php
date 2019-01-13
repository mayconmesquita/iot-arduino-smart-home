<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 4){
		include('config/connect_bd.php');
		mysql_select_db($basedados, $connect);
		
		$sql = 'SELECT * FROM users';
		$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');

		if(isset($erro)) $erro = $erro; else $erro = '';
		if(isset($_POST['salvar'])) $salvar = $_POST['salvar'];
		if(isset($_POST['email_user'])) $email_user = $_POST['email_user']; else $email_user = '';

		$Verifica  = mysql_query("SELECT * FROM users WHERE email_user = '$email_user'");
		$Resultado = mysql_num_rows($Verifica);
		
		if(isset($salvar)){
			if(empty($_POST['nome_user'])){
				$msg_status = 'Informe o nome do usuário.';
				$erro = 1;
			}
			else if(empty($_POST['email_user'])){
				$msg_status = 'Informe o e-mail do usuário.';
				$erro = 1;
			}
			else if($Resultado > 0){
				$msg_status = 'O e-mail fornecido já existe em nosso sistema.';
				$erro = 1;
			}
			else if(empty($_POST['senha_user'])){
				$msg_status = 'Informe a senha do usuário.';
				$erro = 1;
			}
			else if($_POST['permissao_user'] > 4 or $_POST['id_user'] == 1 or $_POST['status_user'] > 2){
				$msg_status = 'Por favor, tente novamente mais tarde.';
				$erro = 1;
			}
			// Checa se não houver registro
			else{
				////////////Gera um Salt///////////////
				$tamanho = 23;
				$possible  = '0123456789'; // numbers
				$possible .= 'abcdefghijklmnopqrstuvwxyz'; // lowcase char
				$possible .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // uppercase char
				$possible .= './@#%&^*()'; // symbols

				$salt = "";
				mt_srand((double) microtime() * 1000000);

				$tp = strlen($possible); // total possible chars
				while (strlen($salt) < $tamanho){
					$id       = rand() % $tp;
					$car      = $possible[$id];
					$salt     .= $car;
				}
				$salt = sha1($salt);
				////////////////////////////////////

				// Senha do usuário
				
				$senha = $_POST['senha_user'];
				
				// Cria um hash
				$hash = sha1($senha . $salt);

				// Encripta esse hash 1000 vezes
				for ($i = 0; $i < 1000; $i++) {
					$hash = hash("whirlpool", $hash);
				}
		
				$_SESSION['msg_status'] = 2;
				$erro = 2;
			
				$sql = "INSERT INTO users(
					nome_user,
					email_user,
					senha_user,
					permissao_user,
					link_user,
					data_cadastro,
					hora_cadastro,
					status_user
				) VALUES (
					'".mysql_real_escape_string(trim(strip_tags($_POST['nome_user'])))."',
					'".mysql_real_escape_string(trim(strip_tags($_POST['email_user'])))."',
					'".mysql_real_escape_string(trim(strip_tags($hash)))."',
					'".mysql_real_escape_string(trim(strip_tags($_POST['permissao_user'])))."',
					'".mysql_real_escape_string(trim(strip_tags($salt)))."',
					NOW(), 
					NOW(),
					'".mysql_real_escape_string(trim(strip_tags($_POST['status_user'])))."'
				)";
				
				$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
			}
		}

		if($erro == 1) echo"<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>$msg_status</div>";
		else if($erro == 2) echo"<meta http-equiv=\"refresh\" content=\"0;URL=usuarios\">";
	}
	else echo "<meta http-equiv='refresh' content='0;URL=../usuarios'>";
?>

<div id="tabs">
	<ul><li><a href="#tabs-1">Adicionar usuário</a></li></ul>
	<div id="tabs-1">
		<form id="artigos_form" action="../novo_usuario" method="post">
			<div class="elemento_form input-group">
				<input placeholder="Nome" required="required" name="nome_user" class="form-control" id="nome_user" type="text" value="<?php if(isset($_POST['nome_user'])) echo $_POST['nome_user'] ?>" />
				<span title="Seu nome" class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>
			</div>
			<div class="elemento_form input-group">
				<input placeholder="E-mail" required="required" name="email_user" class="form-control" id="email_user" type="email" value="<?php if(isset($_POST['email_user'])) echo $_POST['email_user'] ?>" />
				<span title="Seu e-mail" class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>
			</div>
			<div class="elemento_form input-group">
				<input placeholder="Senha" required="required" name="senha_user" class="form-control" id="senha_user" type="password" value="<?php if(isset($_POST['senha_user'])) echo $_POST['senha_user'] ?>" />
				<span title="Sua senha" class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>
			</div>
			<div class="elemento_form">
				<select name="permissao_user" class="form-control" id="permissao_user">
					<option value="1" <?php if(isset($_POST['permissao_user']) && $_POST['permissao_user'] == 1) echo 'SELECTED'; else if(isset($_POST['permissao_user']) && $_POST['permissao_user'] == '') echo 'SELECTED'; ?> class="opc_motivo">Visitante</option> 
					<option value="2" <?php if(isset($_POST['permissao_user']) && $_POST['permissao_user'] == 2) echo 'SELECTED'; ?> class="opc_motivo">Membro</option>
					<option value="3" <?php if(isset($_POST['permissao_user']) && $_POST['permissao_user'] == 3) echo 'SELECTED'; ?> class="opc_motivo">Moderador</option>
					<option value="4" <?php if(isset($_POST['permissao_user']) && $_POST['permissao_user'] == 4) echo 'SELECTED'; ?> class="opc_motivo">Administrador</option>
				</select>
			</div>

			<div class="elemento_form">
				<div class="radio">
					<label>
					<input id="status_user_ativo" name="status_user"  type="radio" value="1" <?php if(isset($_POST['status_user']) && $_POST['status_user'] == 1) echo "checked"; if(!isset($_POST['status_user'])) echo 'checked'; ?>/>
					Ativo
					</label>
				</div>
				<div class="radio">
					<label>
						<input id="status_user_inativo" name="status_user" type="radio" value="2" <?php if(isset($_POST['status_user']) && $_POST['status_user'] == 2) echo "checked"; ?>/>
						Inativo
					</label>
				</div>
			</div>

			<p><button class="btn btn-default" role="button" name="salvar" type="submit">Salvar</button></p>	
		</form>
	</div>	
</div>