<?php
	if($_SESSION['permissao_user'] >= 3){
?>	
<style>
	#vigilancia{
		border: 1px dashed #666;
		padding: 10px;
		width: 640px;
		height: 480px;
		margin: 0pt auto 30px;
		box-shadow: 0px 0px 15px #ccc;
	}
	#painel_camera{
		padding-top: 20px;
		width: 330px;
		margin: 0pt auto;
	}
</style>

<div id="vigilancia"><?php include('php/vigilancia.php'); ?></div>
<?php } ?>