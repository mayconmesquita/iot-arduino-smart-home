<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 3){
?>	
<img id="img1" border="0" src='http://localhost:8081/video.mjpg?q=80&fps=30&id=0.14330713790785765&r=1384369371366'><br />
<div align="left">
	<div id="painel_camera">
		
	</div>
</div>
<?php } ?>