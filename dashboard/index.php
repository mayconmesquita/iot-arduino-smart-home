<?php
	session_start();

	require('login/verifica_log.php');
	require('configuration.php');
	require(DOCROOT.'/lang.php');
	require(DOCROOT.'/functions/querystring.php');
	require(DOCROOT.'/functions/querystring-metatag.php');

	if(file_exists('install.php')) { echo'<meta charset="utf-8">'; die($lang['del_install']); }
	$sub 		= (isset($_REQUEST['sub'])) ? $_REQUEST['sub'] : '';
	$p 			= (isset($_REQUEST['p'])) ? $_REQUEST['p'] : '';
	$isAjax		= (isset($_POST['isAjax'])) ? $_POST['isAjax'] : '';

	$title 		= _title();
	$menu_ativo = _menu_ativo();

	if ($p == 'usuarios' || $p == 'cadastro' || $p == 'novo_usuario' || $p == 'editar_usuario' || $p == 'alterar_senha'){
		$pasta 	 = 'content/cadastro';
		$inicial = 'cadastro';
	}
	else if ($p == 'task'){
		$pasta 	 = 'content/tasks';
		$inicial = 'task';
	}
	else if ($p == 'device'){
		$pasta 	 = 'content/devices';
		$inicial = 'device';
	}
	else if ($p == 'modo'){
		$pasta 	 = 'content/modos';
		$inicial = 'modo';
	}
	else{
		$pasta 	 = 'content';
		$inicial = 'supervisorio';
	}

	if (!empty($isAjax) && $isAjax > 0 &&
		!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
		strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		
		include('login/permissoes.php');
		QueryString($_GET['p'], $inicial, $pasta);
		die;
	}

	$app		= (isset($_SESSION['app'])) ? $_SESSION['app'] : '';
	$mobile		= (isset($_SESSION['mobile'])) ? $_SESSION['mobile'] : '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex, nofollow" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<title><?php echo $_SESSION['title'] . ' - ' . $title; ?></title>
<style>#main-page-content{display:none}</style>
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
	$(document).ready(function(){ /*execute when DOM is ready*/ });
	$("html").append('<div style="display:block;position:absolute;top:50%;left:50%;margin:-11px 0px 0px -11px;" id="loading-page"><img src="assets/images/loading-icon.gif"/></div>');
</script>
<script src="assets/js/img-preload.js?v=2"></script>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/bootstrap/bootstrap-theme.min.css">
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css">
<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.3.0/bootstrap-table.min.css">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" />
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="assets/css/pace/themes/blue/pace-theme-minimal.css">
<link rel="shortcut icon" href="assets/images/shortcut.ico" />
<link rel="stylesheet" href="assets/css/theme-panel.css?v=2" />
<script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.3.0/bootstrap-table.min.js"></script>
<?php if($mobile == true){ ?>
	<script src="assets/js/hammer.min.js"></script>
	<link rel="stylesheet" href="assets/css/jquery/mmenu/jquery.mmenu.all.css"  />
	<script src="assets/js/jquery/mmenu/jquery.mmenu.min.all.js"></script>
	<link rel="stylesheet" href="assets/css/jquery/mmenu/addons/jquery.mmenu.dragopen.css"  />
	<script src="assets/js/jquery/mmenu/addons/jquery.mmenu.dragopen.min.js"></script>
	<script>
		$(document).ready(function(){
			$("#menu").mmenu({
				"transitionDuration": 400,
				"width": 250,
				"slidingSubmenus": false,
				"offCanvas":{
					"zposition": "front",
				},
				"classes": "mm-light",
				dragOpen:{
					open: true,
					threshold: 0,
					maxStartPos: 50
				},
				classNames:{
					selected: "active_li"
				}
			});

			$("#menu").mmenu().on("opening.mm", function(){
				$("html.mm-opened .overlay-content").css('background-color','rgba(0,0,0,0.5)');
				$("html.mm-opened .overlay-content").css('-webkit-transition','background-color 300ms linear');
				$("html.mm-opened .overlay-content").css('transition','background-color 300ms linear');
			});
			$("#menu").mmenu().on("close.mm", function(){
				$("html.mm-opened .overlay-content").css('background-color','rgba(0,0,0,0.0)');
				$("html.mm-opened .overlay-content").css('-webkit-transition','background-color 300ms linear');
				$("html.mm-opened .overlay-content").css('transition','background-color 300ms linear');
			});
			$("#menu").mmenu().on("closing.mm", function(){
				$("html.mm-opened .overlay-content").css('background-color','rgba(0,0,0,0.0)');
				$("html.mm-opened .overlay-content").css('-webkit-transition','background-color 300ms linear');
				$("html.mm-opened .overlay-content").css('transition','background-color 300ms linear');
			});

			$(".navbar-toggle").click(function(){
				$("#menu").trigger("close.mm");
			});
			$(".overlay-content").click(function(){
				$("#menu").trigger("close.mm");
			});
		});
	</script>
	<link rel="stylesheet" href="assets/css/app/app.css?v=2">
	<?php
		if($app == 'android') 	echo'<link rel="stylesheet" href="assets/css/app/android.css?v=2">';
		if($app == 'desktop') 	echo'<link rel="stylesheet" href="assets/css/app/desktop.css?v=2">';
		if($app == 'ios') 		echo'<link rel="stylesheet" href="assets/css/app/ios.css?v=2">';
		if($app == 'winphone') 	echo'<link rel="stylesheet" href="assets/css/app/winphone.css?v=2">';
	?>
<?php } ?>

<?php
	if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad')){
		echo '<script src="assets/js/app/apple/apple-webapp.js?v=2"></script>';
	}
	include('php/status-usuario.php'); 
?>
<!--[if lt IE 9]>
  <script src="http://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="http://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body id="main-page">
	<div class="overlay-content"></div>
	<div id="main-page-content"> 
		<div class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header pull-left">
					<?php 
						if($mobile == true) echo'<a type="button" class="navbar-toggle pull-left" href="#menu">';
						else echo'<button type="button" class="navbar-toggle pull-left" data-toggle="collapse" data-target=".navbar-collapse">';
					?>
					<span class="sr-only"><?php echo $lang['menu'] ?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					
					<p class="pageTitle"><?php echo $title; ?></p>
					<?php 
						if($mobile == true) echo'</a>'; 
						else echo'</button>';
					?>
					<?php
						$perm = $pegar['permissao_user'];
						if($perm >= 1) echo"<a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['supervisory']}\" class=\"link navbar-brand btAjaxLoader";
						if($menu_ativo == 1) echo" active";
						echo"\" href=\"supervisorio\"><span id=\"navbrand-inner\"><span class=\"glyphicon glyphicon-globe\"></span>{$lang['supervisory']}</span></a>";
					?>
				</div>
				<div id="nav-bar-right" style="height:47px;" class="navbar-header pull-right">
					<ul style="display:inline-flex" class="nav navbar-nav pull-right">
						<?php if($mobile == true){ ?><li><a data-extra-action="" data-main-title="<?php echo $_SESSION['title']; ?>" data-page-title="<?php echo $title; ?>" class="refresh-bt btAjaxLoader" style="color:#777;font-size:23px;" href="<?php echo $p; ?>"><i class="fa fa-refresh"></i></a></li><?php } ?>
						<?php if($mobile == true){ ?><li>
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false" style="color:#777;font-size:23px;display:inline-table;" >
								<i class="fa fa-ellipsis-h" id="config-bt"></i>
							</a>
							<ul id="dropdown-settings" class="dropdown-menu" role="menu">
								<?php
									if($perm >= 5){
										echo"<li";
										if($menu_ativo == 6) echo" class=\"active\"";
										echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['settings']}\" href=\"configuracoes\" class=\"link btAjaxLoader"; if($menu_ativo == 6) echo" active"; echo"\"><i class=\"fa fa-wrench\"></i>{$lang['settings']}</a></li>";
									}
								?>
								<li class="divider"></li>
								<?php if($perm >= 1) echo"<li><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['logout']}\" class=\"link btAjaxLoader\" onclick=\"localStorage.username='';localStorage.password='';localStorage.remember_me='';$('body').delay(50).fadeOut(150);\" href=\"login/sair.php\"><i class=\"fa fa-power-off\"></i>{$lang['logout']}</a></li>"; ?>
							</ul></li>
						<?php } ?>
					</ul>
				</div>
				<div class="collapse navbar-collapse navbar-left">
					<ul class="nav navbar-nav">
						<?php
							if($perm >= 1){
								echo"<li style=\"display:none\" class=\"navbar-no-brand";
								//if($menu_ativo == 1) echo" active";
								echo"\"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['supervisory']}\" href=\"supervisorio\" class=\"link btAjaxLoader"; if($menu_ativo == 1) echo" active"; echo"\"><span class=\"glyphicon glyphicon-globe\"></span>{$lang['supervisory']}</a></li>";
							} if($perm >= 3){
								echo"<li";
								//if($menu_ativo == 2) echo" class=\"active\"";
								echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['tasks']}\" href=\"tasks\" class=\"link btAjaxLoader"; if($menu_ativo == 2) echo" active"; echo"\"><span class=\"glyphicon glyphicon-tasks\"></span>{$lang['tasks']}</a></li>";
							} if($perm >= 3){
								echo"<li";
								//if($menu_ativo == 5) echo" class=\"active\"";
								echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['surveillance']}\" href=\"vigilancia\" class=\"link btAjaxLoader"; if($menu_ativo == 5) echo" active"; echo"\"><span class=\"glyphicon glyphicon-facetime-video\"></span>{$lang['surveillance']}</a></li>";
							} if($perm >= 5){
								echo"<li";
								//if($menu_ativo == 3) echo" class=\"active\"";
								echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['devices']}\" href=\"devices\" class=\"link btAjaxLoader"; if($menu_ativo == 3) echo" active"; echo"\"><span class=\"glyphicon glyphicon-phone\"></span>{$lang['devices']}</a></li>";
							} if($perm >= 9){
								echo"<li";
								//if($menu_ativo == 4) echo" class=\"active\"";
								echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['modes']}\" href=\"modos\" class=\"link btAjaxLoader"; if($menu_ativo == 4) echo" active"; echo"\"><span class=\"glyphicon glyphicon-wrench\"></span>{$lang['modes']}</a></li>";
							} if($perm >= 5){
								echo"<li";
								//if($menu_ativo == 6) echo" class=\"active\"";
								echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['settings']}\" href=\"configuracoes\" class=\"link btAjaxLoader"; if($menu_ativo == 6) echo" active"; echo"\"><span class=\"glyphicon glyphicon-wrench\"></span>{$lang['settings']}</a></li>";
							}
						?>
						<li class="dropdown<?php //if($menu_ativo == 7) echo" active";?>">
							<a href="#" class="link btAjaxLoader dropdown-toggle <?php if($menu_ativo == 7) echo" active";?>" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nome_user']; ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<?php
									if($perm >= 1) echo"<li><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['myaccount']}\" class=\"link btAjaxLoader\" href=\"cadastro\"><span class=\"glyphicon glyphicon-pencil\"></span>{$lang['myaccount']}</a></li>";
									if($perm >= 1) echo"<li><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['chpassword']}\" class=\"link btAjaxLoader\" href=\"alterar_senha\"><span class=\"glyphicon glyphicon-lock\"></span>{$lang['chpassword']}</a></li>";
									if($perm >= 1) echo"<li><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['logout']}\" class=\"link btAjaxLoader\" onclick=\"localStorage.username='';localStorage.password='';localStorage.remember_me='';$('body').delay(50).fadeOut(150);\" href=\"login/sair.php\"><span class=\"glyphicon glyphicon-off\"></span>{$lang['logout']}</a></li>";
									if($perm >= 4) echo"
										<li class=\"divider\"></li>
										<li><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"Usuários\" class=\"link btAjaxLoader\" href=\"usuarios\"><span class=\"glyphicon glyphicon-list-alt\"></span>Usuários</a></li>
										<li class=\"divider\"></li>
									";
								?>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<?php if($mobile == true){ ?>
			<nav id="menu">
				<ul>
					<?php
						if($perm >= 1){
							echo"<li";
							if($menu_ativo == 1) echo" class=\"mm-selected\"";
							echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['supervisory']}\" href=\"supervisorio\" class=\"link btAjaxLoader"; if($menu_ativo == 1) echo" active"; echo"\"><i class=\"fa fa-globe\"></i>{$lang['supervisory']}</a></li>";
						} if($perm >= 3){
							echo"<li";
							if($menu_ativo == 2) echo" class=\"mm-selected\"";
							echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['tasks']}\" href=\"tasks\" class=\"link btAjaxLoader"; if($menu_ativo == 2) echo" active"; echo"\"><i class=\"fa fa-tasks\"></i>{$lang['tasks']}</a></li>";
						} if($perm >= 3){
							echo"<li";
							if($menu_ativo == 5) echo" class=\"mm-selected\"";
							echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['surveillance']}\" href=\"vigilancia\" class=\"link btAjaxLoader"; if($menu_ativo == 5) echo" active"; echo"\"><i class=\"fa fa-video-camera\"></i>{$lang['surveillance']}</a></li>";
						} if($perm >= 5){
							echo"<li";
							if($menu_ativo == 3) echo" class=\"mm-selected\"";
							echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['devices']}\" href=\"devices\" class=\"link btAjaxLoader"; if($menu_ativo == 3) echo" active"; echo"\"><i class=\"fa fa-lightbulb-o\"></i>{$lang['devices']}</a></li>";
						} if($perm >= 9){
							echo"<li";
							if($menu_ativo == 4) echo" class=\"mm-selected\"";
							echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['modes']}\" href=\"modos\" class=\"link btAjaxLoader"; if($menu_ativo == 4) echo" active"; echo"\"><i class=\"fa fa-list-ul\"></i>{$lang['modes']}</a></li>";
						} if($perm >= 5){
							echo"<li";
							if($menu_ativo == 6) echo" class=\"mm-selected\"";
							echo"><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['settings']}\" href=\"configuracoes\" class=\"link btAjaxLoader"; if($menu_ativo == 6) echo" active"; echo"\"><i class=\"fa fa-wrench\"></i>{$lang['settings']}</a></li>";
						}
					?>
					<li>
						<a href="#mm-1" class="dropdown-toggle <?php if($menu_ativo == 7) echo" active";?>" data-toggle="dropdown"><i class="fa fa-user"></i><?php echo $_SESSION['nome_user']; ?> <b class="caret"></b></a>
						<ul>
							<?php
								if($perm >= 1) echo"<li><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['myaccount']}\" class=\"link btAjaxLoader\" href=\"cadastro\"><i class=\"fa fa-pencil\"></i>{$lang['myaccount']}</a></li>";
								if($perm >= 1) echo"<li><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['chpassword']}\" class=\"link btAjaxLoader\" href=\"alterar_senha\"><i class=\"fa fa-lock\"></i>{$lang['chpassword']}</a></li>";
								if($perm >= 4) echo"<li><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['users']}\" class=\"link btAjaxLoader\" href=\"usuarios\"><i class=\"fa fa-users\"></i>{$lang['users']}</a></li>";
								if($perm >= 1) echo"<li><a data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['logout']}\" class=\"link btAjaxLoader\" onclick=\"localStorage.username='';localStorage.password='';localStorage.remember_me='';$('body').delay(50).fadeOut(150);\" href=\"login/sair.php\"><i class=\"fa fa-power-off\"></i>{$lang['logout']}</a></li>";
							?>
						</ul>
					</li>
				</ul>
			</nav>
		<?php } ?>

		<div id="main-content" class="container">
			<div id="error-alert" style="display:none" class="alert alert-danger alert-dismissible" role="alert">
				<i class="fa fa-exclamation-triangle"></i>
				<span id="error-alert-content"></span>
				<button type="button" style="top:0px" class="close" data-hide="alert">
					<span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span>
				</button>
			</div>
			<div id="success-alert" style="display:none" class="alert alert-success alert-dismissible" role="alert">
				<i class="fa fa-check"></i>
				<span id="success-alert-content"></span>
				<button type="button" style="top:0px" class="close" data-hide="alert">
					<span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span>
				</button>
			</div>
			<div id="contentAjax">
				<?php
					include('login/permissoes.php');
					QueryString($_GET['p'], $inicial, $pasta);
				?>
			</div>
		</div>
		<div id="footer" class="container">
			<nav class="navbar navbar-default navbar-fixed-bottom">
				<div class="navbar-inner navbar-content-center">
					<div class="btn-group btn-group-justified">
						<div class="btn-group">
							<?php 
								echo"<button onclick=\"wsToggle(this)\" value=\"SL2\" type=\"button\" class=\"btn btn-default btn-footer\">Modo Acordar</button>"; 
							?>
						</div>
						<div class="btn-group">
							<?php 
								if(strstr($_SERVER['HTTP_USER_AGENT'],'Chrome')){
									if($app == 'android'){
										echo"<button id=\"recognizeButton\" type=\"button\" class=\"btn btn-default btn-footer mySpeech\"><span style=\"font-size:30px\" id=\"speechIcon\" class=\"fa fa-microphone-slash\"></span>&nbsp;</button>";
									} else{
										echo"<script src=\"{$_SESSION['url']}/assets/js/app/desktop/desktop-recv.js\"></script>";
										echo"<button id=\"recognizeButton\" onclick=\"recognizer(this, false, event);\" type=\"button\" class=\"btn btn-default btn-footer mySpeech\"><span style=\"font-size:30px\" id=\"speechIcon\" class=\"fa fa-microphone\"></span>&nbsp;</button>";
									}
								}
								else echo"<a style=\"padding-top:13px\" data-main-title=\"{$_SESSION['title']}\" data-page-title=\"{$lang['surveillance']}\" href=\"vigilancia\" type=\"button\" class=\"btn btn-default btn-footer link btAjaxLoader\">Vigilância</a>";	
							?>
						</div>
						<div class="btn-group">
							<?php 
								echo"<button onclick=\" wsToggle(this)\" value=\"SL3\" type=\"button\" class=\"btn btn-default btn-footer\">Modo Dormir</button>";
							?>
						</div>
					</div>
		        </div>
		    </nav>
		</div>

		<?php if($app == 'desktop'){ ?>
			<script src="assets/js/pace/pace.min.js"></script>
			<script>
				paceOptions = {
					elements: false,
					restartOnPushState: false,
					restartOnRequestAfter: false,
					ajax: false,
					document: false,
					eventLag: false
				}
			</script>
		<?php } ?>

		<script>
			if(history.pushState){
				window.addEventListener("popstate",function(e){
					// loadPageByAjax(document.URL);
					// window.location = document.URL;
				});
			}
			$(document).ready(function loadPageByAjax(){
				$("a.btAjaxLoader").on("click",function(e){
					var href = $(this).attr('href');
					var pageTitle = $(this).data("page-title");
					var mainTitle = $(this).data("main-title");
					var extraAction = $(this).data("extra-action");

					$(".navbar-collapse").removeClass("in");
					$("#menu").trigger("close.mm");

					if(href != '#' && href != 'login/sair.php'){
						e.preventDefault();
						var activeHref = $('a[href=' + href + ']');

						$(".refresh-bt i").addClass("fa-spin");
						$('.refresh-bt').attr('href',href);
						$('.refresh-bt').data("page-title",pageTitle);
						$('.refresh-bt').data("main-title",mainTitle);

						$("a.btAjaxLoader").removeClass("active");
						activeHref.addClass("active");

						if(href == 'cadastro' || href == 'alterar_senha' || href == 'usuarios'){
							$("a.dropdown-toggle.btAjaxLoader").addClass("active");
						}

						$('.refresh-bt').removeClass("active");

						$('.pageTitle').html($(this).data("page-title"));
						
						var refererTitle = document.getElementsByTagName('title')[0].innerHTML.split('-');
						// alert(refererTitle[1]); Título da pagina anterior
						// alert(refererHref); Url da pagina anterior
						document.getElementsByTagName('title')[0].innerHTML = mainTitle + ' - ' + pageTitle;
						window.history.pushState({},mainTitle + ' - ' + pageTitle, href);

						$.ajax({
							type: "post",
							cache: true,
							url: href,
							data: "isAjax=1",
							success: function(html){
								$("#contentAjax").html(html);
								$(".refresh-bt i").removeClass("fa-spin");
								if(href != 'supervisorio' || href != 'vigilancia'){
									$("#tabs").tabs();

									$.ajax({cache:true,url:'assets/js/post-ajax.js?v=2'});
									$.ajax({cache:true,url:'assets/js/data-table.js?v=2'});
									if(window.timeoutAlertOn == 1){
										window.timeoutAlertOn = 0;
										clearTimeout(timeoutAlert);
										$('#error-alert').hide();
										$('#success-alert').hide();
									}						
								}
								if(href == 'supervisorio'){
									$('.refresh-bt').data("extra-action","refreshWs");
									if(extraAction == '' || extraAction == 'refreshWs'){
										if(window.wsConnected == 0) window.wsInit();
									}
									if(window.wsConnected == 1) ws.send("S*");
								}
								else{
									$('.refresh-bt').data("extra-action","");
								}
								$("#contentAjax").delay(10).hide().fadeIn(130);
							}, 
							beforeSend:function(){
							}
						});
					}
				});
			});
		</script>

		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
		<script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
		
		<script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.3.0/locale/bootstrap-table-pt-BR.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
		<script src="http://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

		<script src="assets/js/post-ajax.js?v=2"></script>

		<script src="assets/js/setup-conn.js.php?v=2"></script>
		<script src="assets/js/conn.js?v=2"></script>

		<script src="assets/js/data-table.js?v=2"></script>

		<script src="http://cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.min.js"></script>
		<script src="assets/js/jquery/thermometer/jquery.thermometer.js"></script>
		<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.3.1/jquery.maskedinput.min.js"></script>

		<script src="assets/js/scripts.js.php?<?php if(isset($_SESSION['tempo_receber'])) echo'refresh='.$_SESSION['tempo_receber']; ?>"></script>

		<script>
			$(document).ready(function(){
				$("#main-page-content").hide().delay(1500).fadeIn(150);
				$("#loading-page").delay(1500).fadeOut(150);
			});
		</script>
	</div>
</body>
</html>