function createAutoClosingAlert(selector, delay) {
	var alert 	 = $(selector).alert();
	timeoutAlert = setTimeout(function(){alert.hide();},delay);
	window.timeoutAlertOn = 1;
}

$(document).ready(function(){
	function noInternetConnection(){
		$("#login-alert").css('display', 'block', 'important');
		$("#login-alert-content").html("Verifique sua conexão com a internet.");
		$("#btn-login").attr('disabled', false);

		createAutoClosingAlert("#login-alert", 4000);
		return false;
	}

	$(function(){
		if (localStorage.remember_me && localStorage.remember_me != ''){
			$('#login-remember').attr('checked', 'checked');
			$("#login-email").val(localStorage.username);
			$("#login-password").val(localStorage.password);
			// $("html").css("display","none");
			// $("#btn-login").trigger("click");
		} else {
			$('#login-remember').removeAttr('checked');
			$("#login-email").val('');
			$("#login-password").val('');
		}
	});

	function saveLogin(){
		if ($('#login-remember').is(':checked')){
			localStorage.username = $("#login-email").val();
			localStorage.password = $("#login-password").val();
			localStorage.remember_me = $('#login-remember').val();
		} else {
			localStorage.username = '';
			localStorage.password = '';
			localStorage.remember_me = '';
		}
	}

	var urlAuth = "login/autenticar.php";
	var urlSaveRes = "php/save.screen.resolution.php";
	var urlLoad = "supervisorio";

	windowWidth  = $(window).width();
	windowHeight = $(window).height();
	$("#login-alert").css('display', 'none', 'important');
	$("#btn-login").click(function(){
		/*
		var navOnline = navigator.onLine;
		if(!navOnline){
			$("#login-alert").css('display', 'block', 'important');
			$("#login-alert-content").html("Verifique sua conexão com a internet.");
			$("#btn-login").attr('disabled', false);
			return false;	
		}
		*/
		username = $("#login-email").val();
		password = $("#login-password").val();
		$.ajax({
			type: "POST",
			url: urlAuth,
			data: "email_user="+username+"&senha_user="+password,
			success: function(html){
				if(html == 'true_1'){
					$("#btn-login").attr('disabled', true);
					$.ajax({
						type: "POST",
						url: urlSaveRes,
						data: "width="+windowWidth+"&height="+windowHeight,
						success: function(html){
							$("body").fadeOut(100).delay(400);
							saveLogin();
							window.location = urlLoad;
						}, 
						error: function(jqXHR, exception) {}
					});
				} else if(html == 'false_1'){
					$("#login-alert").css('display', 'block', 'important');
					$("#login-alert-content").html("E-mail ou senha incorreta.");
					$("#btn-login").attr('disabled', false);
					createAutoClosingAlert("#login-alert", 4000);
				} else if(html == 'false_2'){
					$("#login-alert").css('display', 'block', 'important');
					$("#login-alert-content").html("Aguarde a liberação do seu cadastro.");
					$("#btn-login").attr('disabled', false);
					createAutoClosingAlert("#login-alert", 4000);
				} else if(html == 'false_3'){
					$("#login-alert").css('display', 'block', 'important');
					$("#login-alert-content").html("Preencha todos os campos.");
					$("#btn-login").attr('disabled', false);
					createAutoClosingAlert("#login-alert", 4000);
				} else if(html == 'syserror_1'){
					$("#login-alert").css('display', 'block', 'important');
					$("#login-alert-content").html("Tente novamente mais tarde.");
					$("#btn-login").attr('disabled', false);
					createAutoClosingAlert("#login-alert", 4000);
				} 
			}, 
			beforeSend:function(){ 
				if(window.timeoutAlertOn == 1){
					window.timeoutAlertOn = 0;
					clearTimeout(timeoutAlert);
				}
				$("#btn-login").attr('disabled', true);
			},
			error: function(jqXHR, exception){ // Not connect. Verify Network.
				if (jqXHR.status === 0){
					noInternetConnection();
				} else if (jqXHR.status == 404){ // Requested page not found. [404]
					noInternetConnection();
				} else if (jqXHR.status == 500){ // Internal Server Error [500].
					noInternetConnection();
				} else if (exception === 'parsererror'){ // Requested JSON parse failed.
					noInternetConnection();
				} else if (exception === 'timeout'){ // Time out error.
					noInternetConnection();
				} else if (exception === 'abort'){ // Ajax request aborted.
					noInternetConnection();
				} else{ // 'Uncaught Error' + jqXHR.responseText
					noInternetConnection();
				}
			}
		});
		return false;
	});
});