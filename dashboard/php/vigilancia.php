<?php
	error_reporting(0);
	session_start();

	if($_SESSION['permissao_user'] < 3) die;
?>

<script src="http://192.168.1.75/public.js"></script>
<script src="http://192.168.1.75/check_user.cgi"></script>
<script src="http://192.168.1.75/get_status.cgi"></script>
<script src="http://192.168.1.75/get_camera_params.cgi"></script>
<script src="http://192.168.1.75/get_params.cgi"></script>
<script src="http://192.168.1.75/login.cgi"></script>
<script type="text/javascript">
	if (alias == '') alias = str_anonymous;
	alias = alias;
	// document.title=str_device+'('+alias+')';

	var sSnapUrl = "http://192.168.1.75/snapshot.cgi?user=" + top.cookieuser + "&pwd=" + top.cookiepass;
	var img = new Image();
	var imgObj;

	function preload() {
		img.src = sSnapUrl+new Date;
	}

	function changesrc() {
		img1.src = img.src;
		preload();
		setTimeout(changesrc, 3500);
	}

	function update() {
		imgObj = document.getElementById('img1');
		imgObj.src = img.src;
		img.src = sSnapUrl + (new Date()).getTime();
	}

	function takeError() {
		img.src = sSnapUrl + (new Date()).getTime();
	}

	function startonload() {
		img.src = sSnapUrl + (new Date()).getTime();
		img.onerror = takeError;
		img.onload = update;
	}

	function load() {
		if (navigator.appName.indexOf("Microsoft IE Mobile") != -1) {
			preload();
			changesrc();
			return;
		}
		
		//alert(loginuser);
    	top.cookieuser = loginuser;
    	top.cookiepass = loginpass;
    	top.cookiepri = pri;
		startonload();
	}

	var szCmdUrl = "http://192.168.1.75/decoder_control.cgi?onestep=1&user=" + top.cookieuser + "&pwd=" + top.cookiepass;

	function ptzUpSubmit() {
		new Image().src = szCmdUrl + "&command=0&" + (new Date()).getTime();
	}

	function ptzDownSubmit() {
		new Image().src = szCmdUrl + "&command=2&" + (new Date()).getTime();
	}

	function ptzLeftSubmit() {
		new Image().src = szCmdUrl + "&command=4&" + (new Date()).getTime();
	}

	function ptzRightSubmit() {
		new Image().src = szCmdUrl + "&command=6&" + (new Date()).getTime();
	}

	function callcmd(cmd) {
		new Image().src = "http://192.168.1.75/decoder_control.cgi?command=" + cmd + "&user=" + top.cookieuser + "&pwd=" + top.cookiepass;
	}
</script>

<body onload="load()">

<img id="img1" border="0" src='http://192.168.1.75/snapshot.cgi?user=admin&pwd=' />

<br>

<div align="left">
	<div id="painel_camera">
		<input name="btnDown" type="button" class="button" id="btnDown" onClick="ptzDownSubmit()" value="Descer"/>
		<input name="btnUP" type="button" class="button" id="btnUP" onClick="ptzUpSubmit()" value="Subir"/>
		<input name="btnRight" type="button" class="button" id="btnRight" onClick="ptzRightSubmit()" value="Esquerda"/>
		<input name="btnLeft" type="button" class="button" id="btnLeft" onClick="ptzLeftSubmit()" value="Direita"/>
	</div>
</div>

<!--<table><tr><td style="font-size:12px"><script>document.write(str_point);</script>
 <input name="btmyzw1" type="button" class=button id="btmyzw1" onClick="callcmd(31)" value=" 1 "/><input name="btmyzw2" type="button" class=button id="btmyzw2" onClick="callcmd(33)" value=" 2 "/><input name="btmyzw3" type="button" class=button id="btmyzw3" onClick="callcmd(35)" value=" 3 "/><input name="btmyzw4" type="button" class=button id="btmyzw4" onClick="callcmd(37)" value=" 4 "/><input name="btmyzw5" type="button" class=button id="btmyzw5" onClick="callcmd(39)" value=" 5 "/></td></tr></table>
</div>-->

<script language="Javascript">
	btnUP.value = _ptz_up2;
	btnLeft.value = _ptz_left2;
	btnRight.value = _ptz_right2;
	btnDown.value = _ptz_down2;
	btnRefresh.value = str_refresh;
	btnswitchon.value = str_switchoff;
	btnswitchoff.value = str_switchon;
</script>

<?php } ?>