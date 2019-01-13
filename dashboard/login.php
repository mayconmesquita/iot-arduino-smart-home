<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<?php
	require('config/connect_bd.php');
	mysql_select_db($basedados, $connect);

	$sqlConfig = 'SELECT * FROM configs';
	$resultadoConfig = mysql_query($sqlConfig) or die;
	$linhaConfig = mysql_fetch_array($resultadoConfig, MYSQL_ASSOC);
?>
<title><?php if(isset($linhaConfig['title'])) echo $linhaConfig['title'] ?> - Login</title>
<meta name="robots" content="noindex, nofollow" />
<meta name="viewport" content="initial-scale=1, user-scalable=0">
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>$(document).ready(function(){$("body").delay(500).hide().fadeIn(180);})</script>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="assets/css/theme-login.css?v=2">
<link rel="shortcut icon" href="assets/images/shortcut.ico" />
<script src="assets/js/login-ajax.js?v=3"></script>
<?php
	if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad')){
		echo '<script src="assets/js/app/apple/apple-webapp.js?v=2"></script>';
	}

	session_start();

	$subdomain = explode('.', $_SERVER['HTTP_HOST']);
	if($subdomain[0] == 'm'){
		$_SESSION['mobile'] = true;
		echo '<link rel="stylesheet" href="assets/css/app/app.css?v=2">';

		if($_GET['app'] == 'android'){
			$_SESSION['app'] = 'android';
			echo '<link rel="stylesheet" href="assets/css/app/android.css?v=2">';
		}
		if($_GET['app'] == 'desktop') {
			$_SESSION['app'] = 'desktop';
			echo '<link rel="stylesheet" href="assets/css/app/desktop.css?v=2">';

		}
		if($_GET['app'] == 'ios') {
			$_SESSION['app'] = 'ios';
			echo '<link rel="stylesheet" href="assets/css/app/ios.css?v=2">';

		} 
		if($_GET['app'] == 'winphone') {
			$_SESSION['app'] = 'winphone';
			echo '<link rel="stylesheet" href="assets/css/app/winphone.css?v=2">';
		}
	}
	else $_SESSION['mobile'] = false;
?>
</head>
<body id="login-page">
	<div class="container">
		<div class="panel-full">&nbsp;</div>
	    <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">              
	        <div class="panel panel-info">
	            <div class="panel-heading">
	                <div class="panel-title">Entrar</div>
	                <div style="float:right;font-size:85%;position:relative;top:-18px"><span style="color:#333">Não tem uma conta? </span><a href="#" onclick="$('#loginbox').hide(); $('#signupbox').show()">Cadastra-se</a></div>
	            </div>
	            <div style="padding-top:20px;padding-bottom:2px" class="panel-body">
	                <div id="login-alert" style="display:none" class="alert alert-danger alert-dismissible col-sm-12" role="alert">
	                	<span id="login-alert-content"></span>
						<button type="button" style="top:0px" class="close" data-hide="alert">
							<span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span>
						</button>
	                </div>

	                <form action="./" method="post" id="loginform" class="form-horizontal" role="form">
	                    <div style="margin-bottom: 25px" class="input-group">
	                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
	                        <input id="login-email" type="text" class="form-control" name="email" placeholder="E-mail">
	                    </div>
	                    <div style="margin-bottom: 25px" class="input-group">
	                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
	                        <input id="login-password" type="password" class="form-control" name="password" placeholder="Senha">
	                    </div>

	                    <?php /* <div class="input-group"> <div class="checkbox"><label><input id="login-remember" type="checkbox" checked="checked" name="remember" value="1">Mantenha-me conectado</label></div></div>?> */ ?>

	                    <!-- Button -->
	                    <div style="margin-top:10px" class="form-group">
	                        <div class="col-sm-12 controls">
	                            <button id="btn-login" style="width:40%" data-loading-text="Entrar" class="btn btn-success">Entrar</button>
	                            <?php if($_SESSION['mobile'] && $_SESSION['app'] == 'android') echo'&nbsp;<button id="recognizeButton" onclick="recognizeSpeech();" type="button" class="btn btn-primary">Comando de voz</button>';?>
	                        	<div id="cbox-remember" class="checkbox" style="display:inline;margin-left:8px;">
	                            	<label><input id="login-remember" type="checkbox" name="remember" value="1">Mantenha-me conectado</label>
	                        	</div>
	                        </div>
	                    </div>

	                    <div class="form-group">
	                        <div class="col-md-12 control">
	                            <div style="padding-top:0pt;font-size:90%" ><a href="#">Esqueceu sua senha?</a>
	                            </div>
	                        </div>
	                    </div>
	                </form>
	            </div>
	        </div>
	    </div>
	    <div id="signupbox" style="display:none; margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
	        <div class="panel panel-info">
	            <div class="panel-heading">
	                <div class="panel-title">Inscrever-se</div>
	                <div style="float:right;font-size:85%;position:relative;top:-18px"><span style="color:#333">Já tem uma conta? </span><a id="signinlink" href="#" onclick="$('#signupbox').hide(); $('#loginbox').show()">Entrar</a></div>
	            </div>  
		        <div class="panel-body" style="padding-bottom:5px;padding-top:20px">
		            <form id="signupform" class="form-horizontal" role="form">
		            	<div id="signup-alert" style="display:none" class="alert alert-danger alert-dismissible" role="alert">
		                	<span id="signup-alert-content"></span>
							<button type="button" style="top:0px" class="close" data-hide="alert">
								<span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span>
							</button>
		                </div>
		                <div class="form-group">
		                    <label for="icode" class="col-md-3 control-label">CPF/CNPJ do titular</label>
		                    <div class="col-md-9">
		                        <input type="text" id="icode" class="form-control" name="icode" placeholder="">
		                    </div>
		                </div>
		                <div class="form-group">
		                    <label for="password" class="col-md-3 control-label">Senha</label>
		                    <div class="col-md-9">
		                        <input type="password" id="password" class="form-control" name="passwd" placeholder="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <!-- Button -->
		                    <div class="col-md-offset-3 col-md-9">
		                        <button id="btn-signup" type="button" style="width:40%" class="btn btn-info"><i class="icon-hand-right"></i>Cadastre-se</button>
		                        <?php /*<span>ou</span><button id="btn-fbsignup" type="button" class="btn btn-primary"><i class="icon-facebook"></i>Inscrever-se com o Facebook</button>*/ ?>
		                    </div>
		                </div>
		                <?php /*<div class="form-group" style="border-top: 1px solid #999; padding-top:20px"><div class="col-md-offset-3 col-md-9"></div></div>*/ ?>
		            </form>
		        </div>
	    	</div>
	    </div> 
	</div>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</body>
</html>