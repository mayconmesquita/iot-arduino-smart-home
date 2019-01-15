<?php
	error_reporting(0);
	session_start();

	if ($_SESSION['permissao_user'] < 5) {
		die('Você não tem permissão para acessar esta página.');
	}

	if (isset($_POST['cadastrar'])) {
		$nome  = strip_tags(trim($_POST['nome_user']));
		$email = strip_tags(trim(strtolower($_POST['email_user'])));
		$senha = strip_tags(trim($_POST['senha_user']));
		$repetir_senha = strip_tags(trim($_POST['repetir_senha_user']));
		
		include('../config/connect_bd.php');
		mysql_select_db($basedados, $connect);

		$sql = "SELECT * FROM users WHERE email_user = '$email'";
		$resultado = mysql_query($sql, $connect) or die('Nao foi possivel conectar...');
		$pegar = mysql_fetch_array($resultado);
		
		$Verifica = mysql_query("SELECT * FROM users WHERE email_user = '$email'");
		$Resultado = mysql_num_rows($Verifica);
		
		if ($nome == '') {
			$msg_resposta = 'Digite o seu nome!';
			$error = 1;
		} else if ($email == '') {
			$msg_resposta = 'Digite o seu e-mail!';
			$error = 1;
		} else if (substr_count($email, "@") == 0 || substr_count($email, ".") == 0 || strlen($email) < 5) {
			$msg_resposta = 'Digite um e-mail válido!';
			$error = 1;
		} else if ($Resultado > 0) {
			$msg_resposta = 'O e-mail fornecido já existe em nosso sistema!';
			$error = 1;
		} else if ($senha == '') {
			$msg_resposta = 'Digite a sua senha!';
			$error = 1;
		} else if ($repetir_senha == '') {
			$msg_resposta = 'Digite a sua senha novamente!';
			$error = 1;
		} else if ($senha !== $repetir_senha) {
			$msg_resposta = 'Suas senhas não conferem!';
			$error = 1;
		} else {
			/*------------- GENERATE PASSWORD ----------------*/
			/*-------------- SALT GENERATOR ------------------*/
			$tamanho   = 23;
			$possible  = '0123456789'; // numbers
			$possible .= 'abcdefghijklmnopqrstuvwxyz'; // lowcase char
			$possible .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // uppercase char
			$possible .= './@#%&^*()'; // symbols

			$salt = '';
			mt_srand((double) microtime() * 1000000);

			$tp = strlen($possible); // total possible chars

			while (strlen($salt) < $tamanho) {
				$id       = rand() % $tp;
				$car      = $possible[$id];
				$salt    .= $car;
			}

			$salt = sha1($salt);
			/*------------------------------------------*/

			// Cria um hash
			$hash = sha1($senha . $salt);

			// Encripta esse hash 1000 vezes
			for ($i = 0; $i < 1000; $i++) {
				$hash = hash("whirlpool", $hash);
			}
			
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
				'" . mysql_real_escape_string($nome) . "',
				'" . mysql_real_escape_string($email) . "',
				'" . mysql_real_escape_string(trim(strip_tags($hash))) . "',
				'" . mysql_real_escape_string(1) . "',
				'" . mysql_real_escape_string(trim(strip_tags($salt))) . "',
				NOW(),
				NOW(),
				'" . mysql_real_escape_string(2) . "'
			)";

			$resultado = mysql_query($sql, $connect) or die('Nao foi possivel conectar...');

			$nome = '';
			$email = '';
			$senha = '';
			$repetir_senha = '';
			$error = 0;
			$msg_resposta = 'Cadastro realizado com sucesso!';
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Painel de Controle - Novo Cadastro</title>
<meta name="robots" content="noindex, nofollow" />
<link href="../css/reset.css" rel="stylesheet" type="text/css" />
<style>
	html { overflow-x: hidden; overflow-y: hidden; } /* Remover Barra de Rolagem Automática IE 7 e 8 */
	body {
		color: #999294;
		font-family: "Lucida Grande", Verdana,Arial, "Bitstream Vera Sans", sans-serif;
		font-size: 11px;
		line-height: 1.7em;
		background-color: #ffffff;
		padding: 0pt;
		margin: 0pt;
	}
	
	fieldset {
		border: 1px solid #cccccc;
		padding: 15px 0pt;
		margin: 10px 10px 10px 10px;
	}
	
	legend {
		color: #df0b0b;
		font-size: 18px;
		font-weight: body;
		padding: 0pt 5px;
		cursor: default;
	}
	#novo_cadastro {
		padding: 10px;
	}
	
	#novo_cadastro label {
		font-size: 15px;
		color: #1a1a1a;
		padding: 0pt;
		margin: 0pt;
		cursor: pointer;
	}
	
	form input {
		width: 360px;
		height: 20px;
		color: #555555;
		font-size: 17px;
		background-color: #fafafa;
		border: 1px solid #aaa;	
		padding: 3px;
		float: left;
	}

	form input:focus {
		background-color: #ffeeee;
		border: 1px solid #ff3333;	
	}
	
	form #bt_submit {
		text-decoration: none;
		font-weight: bold;
		font-family: "trebuchet ms","Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
		color: #ffffff;
		width: 96px;
		height: 28px;
		font-size: 11px;
		background-color: #a41c20;
		border: 1px solid #5e0c0e;
		padding: 4px 5px 6px 5px;
	   *padding: 2px 1px; /* Apenas o IE7 reconhece*/
		cursor: pointer;
		text-shadow: 0pt 0pt 5px #555555;
		float: left;
	}

	form #bt_submit:hover {
		background-color: #c72126;
	}

	#msg_resposta {
		font-size: 16px;
		height: 20px;
		border: 1px solid #cccccc;
		padding: 10px;
		margin: 10px 10px 10px 10px;
	}

	#msg_fail {
		background: url(../images/cross.png) no-repeat left bottom;
		color: #f02d2d;
		text-indent: 22px;
	}

	#msg_ok {
		background: url(../images/tick.png) no-repeat left bottom;
		color: #007716;
		text-indent: 22px;
	}
</style>
</head>

<body>
	<fieldset>
		<legend>Informe seus dados para efetuar o seu cadastro</legend>
		<form id="novo_cadastro" enctype="application/x-www-form-urlencoded" method="post" action="novo_cadastro.php">
			<table align="left" border="0" cellpadding="1" cellspacing="1" style="width: 550px;">
				<tbody>
					<tr>
						<td width="170px" height="50px"><label for="nome" class="required">Nome:</label></td>
						<td width="300px"><input type="text" name="nome_user" id="nome" value="<?php if(isset($nome)) echo $nome; ?>" maxlength="50" /></td>
					</tr>	
					<tr>
						<td width="170px" height="50px"><label for="email" class="required">E-mail:</label></td>
						<td><input type="text" name="email_user" id="email" value="<?php if(isset($email)) echo $email; ?>" maxlength="50" /></td>
					</tr>
					<tr>
						<td width="170px" height="50px"><label for="senha" class="required">Senha:</label></td>
						<td><input type="password" name="senha_user" id="senha" value="<?php if(isset($senha)) echo $senha; ?>" maxlength="50" /></td>
					</tr>
					</tr>
						<td width="170px" height="50px"><label for="repetir_senha" class="required">Repetir Senha:</label></td>
						<td><input type="password" name="repetir_senha_user" id="repetir_senha" value="<?php if(isset($repetir_senha)) echo $repetir_senha; ?>" maxlength="50" /></td>
					</tr align="right">
						
						</td><td width="170px" height="80px"><input type="submit" name="cadastrar" id="bt_submit" value="Efetuar Cadastro" /></td>
					</tr>
				</tbody>
			</table>
		</form>
	</fieldset>
	<div id="msg_resposta">
		<?php 
			if(isset($msg_resposta) && $error == 1) echo"<p id=\"msg_fail\">$msg_resposta</p>";
			if(isset($msg_resposta) && $error == 0) echo"<p id=\"msg_ok\">$msg_resposta</p>";
		?>
	</div>
</body>
</html>