<?php  
	if (isset($_POST['redefinir'])) {
		$email = strip_tags(trim($_POST['email_user']));

		include('../config/connect_bd.php');
		mysql_select_db($basedados, $connect);

		$sql = "SELECT * FROM users WHERE email_user = '$email'";
		$resultado = mysql_query($sql, $connect) or die('Nao foi possível conectar...');
		$pegar = mysql_fetch_array($resultado);
		$row = mysql_num_rows($resultado);
		
		if (empty($email)) {
			$msg_resposta = 'Digite o seu e-mail!';
			$error = 1;
		} else if (substr_count($email,"@") == 0 || substr_count($email,".") == 0 || strlen($email) < 5) {
			$msg_resposta = 'Digite um e-mail válido!';
			$error = 1;
		} else if ($row == 0) {
			$msg_resposta = 'O e-mail não existe em nosso sistema!';
			$error = 1;
		} else {
			$nome_user = $pegar['nome_user']; //Nome referente ao e-mail
			$senha_antiga = $pegar['senha_user']; //Senha antiga referente ao e-mail
	
			// Define uma nova senha
			
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
				$id    = rand() % $tp;
				$car   = $possible[$id];
				$salt .= $car;
			}

			$salt = sha1($salt);
			/*------------------------------------------*/

			// SENHA DO USUÁRIO
			include('gera_senha.php');

			// Gera uma senha com 8 carecteres: letras (min e mai) e números aleatórios
			$senha = geraSenha(8, true, true);

			// Cria um hash
			$hash = sha1($senha . $salt);

			// Encripta esse hash 1000 vezes
			for ($i = 0; $i < 1000; $i++) {
				$hash = hash("whirlpool", $hash);
			}
			
			$sql = "UPDATE users SET 
					senha_user = '" . mysql_real_escape_string(trim(strip_tags($hash))) . "',
					link_user  = '" . $salt . "'
				WHERE email_user='$email' and senha_user='$senha_antiga' 
			";
			
			$resultado = mysql_query($sql, $connect) or die('Nao foi possivel conectar');
		
			// Definindo o cabeçalho do e-mail
			// $headers  = "From: \"$header_title\"<$header_email>\n";
			$headers  = "From: Smart Home <home@domain.com>\n";
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";

			// Definindo o aspecto da mensagem
			$msg_content  = "<html><head></head><body>";
			$msg_content .= "<p><big>Ol&aacute; $nome_user,</big></p>";
			$msg_content .= "<p>&nbsp;</p>";
			$msg_content .= "<p><big>Voc&ecirc; solicitou a redefini&ccedil;&atilde;o da sua senha do Painel de Controle - Minha casa recentemente.</big></p>";
			$msg_content .= "<p style=\"font-size:18px;\">Sua nova senha &eacute;:</p>";
			$msg_content .= "<div style=\"background:#eee;border:1px solid #ccc;padding:5px 10px;font-size:17px;\"><strong><big>$senha</big></strong></div>";
			$msg_content .= "<p>&nbsp;</p>";
			$msg_content .= "<h3 style=\"color:#0595ca;font-size:14px;font-style:italic;\"><big><em>N&atilde;o &eacute; necess&aacute;rio responder este e-mail.</em></big></h3>";

			// Enviando a mensagem para o email do site...
			$envia =  mail($email, 'Você solicitou uma nova senha', $msg_content, $headers);

			$email = '';
			$error = 0;
			$msg_resposta = 'Uma nova senha foi enviada para seu e-mail!';
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Painel de Controle - Redefinir Senha</title>
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
	#redefinir_senha {
		padding: 10px;
	}
	
	#redefinir_senha label {
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
		font-family: "trebuchet ms", "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
		color: #ffffff;
		width: 96px;
		height: 28px;
		font-size: 11px;
		background-color: #a41c20;
		border: 1px solid #5e0c0e;
		padding: 4px 5px 6px 5px;
	   *padding: 2px 1px; /* Apenas o IE7 reconhece*/
		margin: 0pt 0pt 0pt 6px;
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
		<legend>Informe seu e-mail para receber sua nova senha de acesso</legend>
		<form id="redefinir_senha" enctype="application/x-www-form-urlencoded" method="post" action="redefinir_senha.php">
			<table align="left" border="0" cellpadding="1" cellspacing="1" style="width: 550px;">
				<tr>
					<td width="70px"><label for="email" class="required">E-mail:</label></td>
					<td>
						<input type="text" name="email_user" id="email" value="<?php if(isset($email)) echo $email; ?>" class="input_login" maxlength="50" />
						<input type="submit" name="redefinir" id="bt_submit" value="Redefinir Senha" />
					</td>
				</tr>
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