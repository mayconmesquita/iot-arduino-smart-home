	document.write('<meta name="apple-mobile-web-app-capable" content="yes" />');
	document.write('<meta name="apple-mobile-web-app-status-bar-style" content="black" />');
	//iPhone 3 and 4 Non-Retina.
	//document.write('<link rel="apple-touch-startup-image" media="(device-width: 320px)" href="assets/images/icons/startup/apple-touch-startup-image-320x460.png">');
	//iPhone 4 Retina.
	//document.write('<link rel="apple-touch-startup-image" media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)" href="assets/images/icons/startup/apple-touch-startup-image-640x920.png">');
	//iPhone 5 Retina.
	//document.write('<link rel="apple-touch-startup-image" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" href="assets/images/icons/startup/apple-touch-startup-image-640x1096.png">');
	//iPad Non-Retina Portrait.
	//document.write('<link rel="apple-touch-startup-image" media="(device-width: 768px) and (orientation: portrait)" href="assets/images/icons/startup/apple-touch-startup-image-768x1004.png">');
	//iPad Non-Retina Landscape.
	//document.write('<link rel="apple-touch-startup-image" media="(device-width: 768px) and (orientation: landscape)" href="assets/images/icons/startup/apple-touch-startup-image-748x1024.png">');
	//iPad Retina Portrait
	//document.write('<link rel="apple-touch-startup-image" media="(device-width: 1536px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" href="assets/images/icons/startup/apple-touch-startup-image-1536x2008.png">');
	//iPad Retina Landscape
	//document.write('<link rel="apple-touch-startup-image" media="(device-width: 1536px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" href="assets/images/icons/startup/apple-touch-startup-image-2048x1496.png">');	
	
	document.write('<link rel="apple-touch-icon" href="assets/images/icons/app/apple/touch-icon-iphone.png">');
	document.write('<link rel="apple-touch-icon" sizes="76x76" href="assets/images/app/apple/icons/touch-icon-ipad.png">');
	document.write('<link rel="apple-touch-icon" sizes="120x120" href="assets/images/app/apple/icons/touch-icon-iphone-retina.png">');
	document.write('<link rel="apple-touch-icon" sizes="152x152" href="assets/images/app/apple/icons/touch-icon-ipad-retina.png">');
	document.write('<script src="assets/js/jquery/stayInWebApp/jquery.stayInWebApp.min.js"></script>');
	
	
	var viewportmeta = document.querySelector('meta[name="viewport"]');
	if (viewportmeta){
		viewportmeta.content = 'width=device-width, minimum-scale=0.25, maximum-scale=10, user-scalable=0';
		document.addEventListener('gesturestart', 
		function (){
			viewportmeta.content = 'width=device-width, minimum-scale=0.25, maximum-scale=10, user-scalable=0';
		}, false);
	}
	
	window.scrollTo(0, 1);
	$(function(){ $.stayInWebApp('a.link'); });