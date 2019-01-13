<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 4){
		include('config/connect_bd.php');
		mysql_select_db($basedados, $connect);
		
		$id_user = trim(strip_tags($_GET['id']));

		if(isset($_POST['email_user'])){ 
			$email_user_novo = strip_tags(trim(strtolower($_POST['email_user']))); 
			$_POST['email_user'] = strip_tags(trim(strtolower($_POST['email_user'])));
		}
		
		if($_SESSION['permissao_user'] == 4){
			$sql = "SELECT * FROM users WHERE permissao_user < 4 and id_user = ".(int)$id_user;
			$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
		}
		elseif($_SESSION['permissao_user'] == 5){
			$sql = "SELECT * FROM users WHERE permissao_user < 5 and id_user = ".(int)$id_user;
			$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
		}

		$linha = mysql_fetch_array($resultado, MYSQL_ASSOC);
		
		$Verifica = mysql_query("SELECT * FROM users WHERE email_user = '$email_user_novo'");
		$Resultado = mysql_num_rows($Verifica);
		
		if($_SESSION['permissao_user'] == 4 && $linha['permissao_user'] > 3){
			echo "<meta http-equiv='refresh' content='0;URL=../usuarios'>";
		}
		else if($id_user == 1){
			echo "<meta http-equiv='refresh' content='0;URL=../usuarios'>";
		}			
		elseif($_SESSION['permissao_user'] == 5 && $linha['permissao_user'] > 4){
			echo "<meta http-equiv='refresh' content='0;URL=../usuarios'>";
		}
		
		if(isset($erro)) $erro = $erro; else $erro = '';
		if(isset($_POST['salvar'])) $salvar = $_POST['salvar'];
		
		if(isset($salvar)){
			if(empty($_POST['nome_user'])){
				$msg_status = 'Informe o nome do usuário.';
				$erro = 1;
			}
			elseif(empty($email_user_novo)){
				$msg_status = 'Informe o e-mail do usuário.';
				$erro = 1;
			}
			elseif($Resultado > 0 and $email_user_novo != $linha['email_user']){
				$msg_status = 'O e-mail fornecido já existe em nosso sistema.';
				$erro = 1;
			}
			elseif($_POST['permissao_user'] > 4 or $_POST['id_user'] == 1 or $_POST['status_user'] > 2){
				$msg_status = 'Por favor, tente novamente daqui a alguns minutos.';
				$erro = 1;
			}
			elseif($email_user_novo == $_SESSION['email_user'] and $_SESSION['permissao_user'] >= 5 && $_POST['permissao_user'] != $_SESSION['permissao_user']){
				$msg_status = 'Super-administrador não pode perder permissões.';
				$erro = 1;
			}
			elseif($email_user_novo == $_SESSION['email_user'] and $_SESSION['permissao_user'] >= 5 && $_POST['status_user'] != $_SESSION['status_user']){
				$msg_status = 'Super-administrador não pode ficar inativo.';
				$erro = 1;
			}
			else {
				$_SESSION['msg_status'] = 1;
				$erro = 2;
				
				if($linha['id_user'] == $_SESSION['id_user']){
					$_SESSION['nome_user'] = mysql_real_escape_string(trim(strip_tags($_POST['nome_user'])));
					$_SESSION['email_user'] = mysql_real_escape_string(trim(strip_tags($email_user_novo)));
					$_SESSION['permissao_user'] = mysql_real_escape_string(trim(strip_tags($_POST['permissao_user'])));
					$_SESSION['status_user'] = mysql_real_escape_string(trim(strip_tags($_POST['status_user'])));
				}
				$sql = "UPDATE users SET 
					nome_user='".mysql_real_escape_string(trim(strip_tags($_POST['nome_user'])))."', 
					email_user='".mysql_real_escape_string(trim(strip_tags($email_user_novo)))."', 
					permissao_user='".mysql_real_escape_string(trim(strip_tags($_POST['permissao_user'])))."',
					status_user='".mysql_real_escape_string(trim(strip_tags($_POST['status_user'])))."'  
				WHERE id_user = ".(int)$id_user;
				
				$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
				
				if($_SESSION['permissao_user'] == 4){
					$sql = "SELECT * FROM users WHERE permissao_user < 4 and id_user = ".(int)$id_user;
					$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
				}
				elseif($_SESSION['permissao_user'] == 5){
					$sql = "SELECT * FROM users WHERE permissao_user < 5 and id_user = ".(int)$id_user;
					$resultado = mysql_query($sql) or die ('Estamos em manutenção, tente mais tarde.');
				}
			}
		}
		if($erro == 1) echo"<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>$msg_status</div>";
		else if($erro == 2) echo"<meta http-equiv=\"refresh\" content=\"0;URL=../usuarios\">";
	}
	else echo "<meta http-equiv='refresh' content='0;URL=../usuarios'>";
?>

<div id="tabs">
	<ul><li><a href="#tabs-1">Editar usuário</a></li></ul>
	<div id="tabs-1">
		<form id="artigos_form" action="../editar_usuario/<?php echo $linha['id_user']; ?>" method="post">
			<div class="elemento_form input-group">
				<input required="" placeholder="Nome" name="nome_user" class="form-control" id="nome_user" type="text" value="<?php echo $linha['nome_user'] ?>" />
				<span title="Seu nome" class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>
			</div>
			<div class="elemento_form input-group">
				<input required="" placeholder="E-mail" name="email_user" class="form-control" id="email_user" type="email" value="<?php echo $linha['email_user'] ?>" />
				<span title="Seu e-mail" class="input-group-addon"><span class="glyphicon glyphicon-info-sign"></span></span>
			</div>
			<div class="elemento_form">
				<select name="permissao_user" class="form-control" id="permissao_user">
					<option value="1" <?php if($linha['permissao_user'] == 1){ echo"SELECTED"; } ?> class="opc_motivo">Visitante</option> 
					<option value="2" <?php if($linha['permissao_user'] == 2){ echo"SELECTED"; } ?> class="opc_motivo">Membro</option> 
					<option value="3" <?php if($linha['permissao_user'] == 3){ echo"SELECTED"; } ?> class="opc_motivo">Moderador</option>
					<option value="4" <?php if($linha['permissao_user'] == 4){ echo"SELECTED"; } ?> class="opc_motivo">Administrador</option>
					<?php if($linha['permissao_user'] >= 5){ echo"<option value=\"5\" SELECTED class=\"opc_motivo\">Super Administrador</option>";} ?>
				</select>
			</div>

			<div class="elemento_form">
				<div class="radio">
					<label>
					<input id="status_user_ativo" name="status_user"  type="radio" value="1" <?php if ($linha["status_user"] == 1) { echo "checked"; } ?>/>
					Ativo
					</label>
				</div>
				<div class="radio">
					<label>
						<input id="status_user_inativo" name="status_user" type="radio" value="2" <?php if ($linha["status_user"] == 2) { echo "checked"; } ?>/>
						Inativo
					</label>
				</div>
			</div>
			
			<p><button class="btn btn-default" role="button" name="salvar" type="submit">Salvar</button></p>
		</form>
	</div>	
</div>