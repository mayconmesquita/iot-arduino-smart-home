$(document).ready(function(){
	var debug = false;

	// var y = 10;
	// $('.thermometer').thermometer();

	lamp = new Array();
	port = new Array();

	largura = $(window).width();
	altura 	= $(window).height();

	$(window).on("resize", function () {
		largura = $(window).width();
		altura 	= $(window).height();

		//regrasResize();
	}).resize();

	window.addEventListener("orientationchange", function() {
		window.initCheckOrientation();
	}, false);

	window.initCheckOrientation = (function(){
		largura = $(window).width();

		if(window.orientation == 0 || window.orientation == 180){
			if(largura > 0 && largura <= 360){
				$("#ground").css('min-height','266px');
			}
		} else if(window.orientation == -90 || window.orientation == 90){
			if(largura > 0 && largura <= 598){
				document.getElementById('ground').style['min-height'] = "478px";
				$("#ground").css('min-height','478px');
			}
		}

	}); window.initCheckOrientation();

	window.onbeforeunload = function(e){
		try{
			if(window.wsConnected == 1){
				if(window.userId > 0) ws.send(window.wsUserName + '(' + window.wsUserId + ')' + ' disconnected');
				else ws.send(window.wsUserName + ' disconnected');
				ws.close();
				if(debug) window.console.log('ws closed by onbeforeunload');
			} else{
				if(debug) window.console.log('Error: can\'t to disconnect ws because it\'s not connected');
			}
		}
		catch(e){
			if(debug) window.console.log('Error OnBeforeUnload: '+e);
		}
	}

	function regrasResize(){
		for(var i = 2;i <= 32; i++){
			if(document.getElementById('lamp'+(i-1)) != null){
				if(largura > 0 && largura <= 360){
					document.getElementById('lamp'+(i-1)).style['width']  = "48px";
					document.getElementById('lamp'+(i-1)).style['height'] = "86px";

					if(lamp[i] = 1) document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lampS2.png) 0px 0px";
					else document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lampS2.png) 48px 0px";
				}
				else if(largura > 360 && largura <= 720){ 
					document.getElementById('lamp'+(i-1)).style['width']  = "72px";
					document.getElementById('lamp'+(i-1)).style['height'] = "129px";
					
					if(lamp[i] = 1) document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lampS1.png) 0px 0px";
					else document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lampS1.png) 72px 0px";
				}
				else if(largura > 720){ 
					document.getElementById('lamp'+(i-1)).style['width']  = "96px";
					document.getElementById('lamp'+(i-1)).style['height'] = "172px";
					
					if(lamp[i] = 1) document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lamp.png) 0px 0px";
					else document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lamp.png) 96px 0px";
				}
			}
			if(document.getElementById('port'+(i-1)) != null){
				if(largura > 0 && largura <= 360){
					document.getElementById('port'+(i-1)).style['background-image'] = "url(assets/images/portS2.png)";
					document.getElementById('port'+(i-1)).style['width']  = "49px";
					document.getElementById('port'+(i-1)).style['height'] = "64px";
				}
				else if(largura > 360 && largura <= 720){ 
					document.getElementById('port'+(i-1)).style['background-image'] = "url(assets/images/portS1.png)";
					document.getElementById('port'+(i-1)).style['width']  = "73.5px";
					document.getElementById('port'+(i-1)).style['height'] = "96px";
				}
				else if(largura > 720){ 
					document.getElementById('port'+(i-1)).style['background-image'] = "url(assets/images/port.png)";
					document.getElementById('port'+(i-1)).style['width']  = "98px";
					document.getElementById('port'+(i-1)).style['height'] = "128px";
				}
			}
		}
	}

	window.ground = (function(state){
		if(state == 1){
			$(".tag").css('color','#3b79b6');
			document.getElementById('ground').style['background'] = "url(assets/images/ground-on.jpg)"; //100%
		}
		else if(state == 0){
			$(".tag").css('color','#ffffff');
			document.getElementById('ground').style['background'] = "url(assets/images/ground-off.jpg)";
		}
		window.initCheckOrientation();
	});

	window.wsInit = (function(){
		try {
			if(debug) window.console.log("Configurando Websocket...");
			ws = new WebSocket("ws://"+window.wsIp+":"+window.wsPort+"/");
			ws.onerror = function(evt){
				window.wsConnected = 0;
				if(debug) window.console.log('wsConnected: ' + window.wsConnected);
				if(debug) window.console.log(evt.data);
			};
			ws.onclose = function(evt){
				window.wsConnected = 0;
				if(debug) window.console.log('wsConnected: ' + window.wsConnected);
				if(debug) window.console.log("onclose called");
			};
			ws.onopen = function(evt){
				window.wsConnected = 1;
				if(debug) window.console.log('wsConnected: ' + window.wsConnected);
				if(window.userId > 0) ws.send(window.wsUserName + '(' + window.wsUserId + ')' + ' connected');
				else ws.send(window.wsUserName + ' connected');
				if(debug) window.console.log("onopen called");
				ws.send("S*");
			};
			ws.onmessage = function(evt){
				if(debug) window.console.log(evt.data);
				if(evt.data[0] == "R"){
					switch(evt.data[1]){
						case "L":
							for(var i = 2;i <= 32; i++){
								if(document.getElementById('lamp'+(i-1)) != null){
									if(evt.data[i] == "1"){
										window.ground(1);

										if(largura > 0 && largura <= 360){
											document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lampS2.png) 48px 0px";
											document.getElementById('lamp'+(i-1)).style['width'] = "48px";
											document.getElementById('lamp'+(i-1)).style['height'] = "86px";
										}
										else if(largura > 360 && largura <= 720){
											document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lampS1.png) 72px 0px";
											document.getElementById('lamp'+(i-1)).style['width'] = "72px";
											document.getElementById('lamp'+(i-1)).style['height'] = "129px";
										}
										else if(largura > 720){
											document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lamp.png) 96px 0px";
											document.getElementById('lamp'+(i-1)).style['width'] = "96px";
											document.getElementById('lamp'+(i-1)).style['height'] = "172px";
										}
										lamp[i] = 1;
									}
									else if(evt.data[i] == "0"){
										window.ground(0);

										if(largura > 0 && largura <= 360){
											document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lampS2.png) 0px 0px";
											document.getElementById('lamp'+(i-1)).style['width'] = "48px";
											document.getElementById('lamp'+(i-1)).style['height'] = "86px";
										}
										else if(largura > 360 && largura <= 720){
											document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lampS1.png) 0px 0px";
											document.getElementById('lamp'+(i-1)).style['width'] = "72px";
											document.getElementById('lamp'+(i-1)).style['height'] = "129px";
										}
										else if(largura > 720){
											document.getElementById('lamp'+(i-1)).style['background'] = "url(assets/images/lamp.png) 0px 0px";
											document.getElementById('lamp'+(i-1)).style['width'] = "96px";
											document.getElementById('lamp'+(i-1)).style['height'] = "172px";
										}
										lamp[i] = 0;
									}
								}
							}
						break;
							
						case "P":
							for(var i = 2;i <= 32; i++){
								if(document.getElementById('port'+(i-1)) != null){
									if(evt.data[i] == "2"){
										document.getElementById('port'+(i-1)).style['cursor'] = "not-allowed";
										document.getElementById('port'+(i-1)).onclick = function(){ return false }

										if(largura > 0 && largura <= 360){
											document.getElementById('port'+(i-1)).style['background'] = "url(assets/images/portS2.png) 98px 0px";
											document.getElementById('port'+(i-1)).style['width'] = "49px";
											document.getElementById('port'+(i-1)).style['height'] = "64px";
										}
										else if(largura > 360 && largura <= 720){
											document.getElementById('port'+(i-1)).style['background'] = "url(assets/images/portS1.png) 147px 0px";
											document.getElementById('port'+(i-1)).style['width'] = "73.5px";
											document.getElementById('port'+(i-1)).style['height'] = "96px";
										}
										else if(largura > 720){
											document.getElementById('port'+(i-1)).style['background'] = "url(assets/images/port.png) 196px 0px";
											document.getElementById('port'+(i-1)).style['width'] = "98px";
											document.getElementById('port'+(i-1)).style['height'] = "128px";
										}
										port[i] = 2;
									}
									else if(evt.data[i] == "1"){
										document.getElementById('port'+(i-1)).style['cursor'] = "not-allowed";
										document.getElementById('port'+(i-1)).onclick = function(){ return false }
										
										if(largura > 0 && largura <= 360){
											document.getElementById('port'+(i-1)).style['background'] = "url(assets/images/portS2.png) 147px 0px";
											document.getElementById('port'+(i-1)).style['width'] = "49px";
											document.getElementById('port'+(i-1)).style['height'] = "64px";
										}
										else if(largura > 360 && largura <= 720){
											document.getElementById('port'+(i-1)).style['background'] = "url(assets/images/portS1.png) 220.5px 0px";
											document.getElementById('port'+(i-1)).style['width'] = "73.5px";
											document.getElementById('port'+(i-1)).style['height'] = "96px";
										}
										else if(largura > 720){
											document.getElementById('port'+(i-1)).style['background'] = "url(assets/images/port.png) 295px 0px";
											document.getElementById('port'+(i-1)).style['width'] = "98px";
											document.getElementById('port'+(i-1)).style['height'] = "128px";
										}
										port[i] = 1;
									}
									else if(evt.data[i] == "0"){
										document.getElementById('port'+(i-1)).style['cursor'] = "pointer";
										document.getElementById('port'+(i-1)).onclick = function(){ if(confirm('Tem certeza que quer abrir esta porta?')) toggle(this); };
										
										if(largura > 0 && largura <= 360){
											document.getElementById('port'+(i-1)).style['background'] = "url(assets/images/portS2.png) 0px 0px";
											document.getElementById('port'+(i-1)).style['width'] = "49px";
											document.getElementById('port'+(i-1)).style['height'] = "64px";
										}
										else if(largura > 360 && largura <= 720){
											document.getElementById('port'+(i-1)).style['background'] = "url(assets/images/portS1.png) 0px 0px";
											document.getElementById('port'+(i-1)).style['width'] = "73.5px";
											document.getElementById('port'+(i-1)).style['height'] = "96px";
										}
										else if(largura > 720){
											document.getElementById('port'+(i-1)).style['background'] = "url(assets/images/port.png) 0px 0px";
											document.getElementById('port'+(i-1)).style['width'] = "98px";
											document.getElementById('port'+(i-1)).style['height'] = "128px";
										}
										port[i] = 0;
									}
								}
							}
						break;
						
						case "T":
							var temp = evt.data.split("#");
							// $("#temp1").html('Temp: '+temp[1]+'°C');
							// $('.thermometer').thermometer({ percent: temp[1] });
							// window.temp = temp[1];
							$("#temp1").button("option","label",''+temp[1]+'°C');
						break;

						case "I":
							$("#air-control").css('display','block');
						break;
					}
				}
			};
		} catch(e) {
			window.wsConnected = 0;
			if(debug) window.console.log(window.wsConnected);
			if(debug) window.console.log('Error: '+e);
		}
	}); window.wsInit();

	window.wsToggle = (function(a){
		if(window.wsConnected == 1){
			if(a.value == 'SM1'){
				ws.send('SL3');
				ws.send('SI3');
			} else if(a.value == 'SM01'){
				ws.send('SL3');
				ws.send('SI2');
			} else{
				ws.send(a.value);
			}
		} else{
			if(debug) window.console.log('Error: can\'t to send data because ws is not connected');
		}
	});
	window.wsSend = (function(a){
		if(window.wsConnected == 1) ws.send(a);
		else{
			if(debug) window.console.log('Error: can\'t to send data because ws is not connected');
		}
	});
});