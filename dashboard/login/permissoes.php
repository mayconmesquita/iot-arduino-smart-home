<?php
	if (isset($_GET['p'])) $pagina = $_GET['p'];
	else $pagina = '';

	if (isset($pegar['permissao_user'])) $perm = $pegar['permissao_user'];
	else $perm = '';
	
	if ($pagina == 'inicio' and $perm < 1) {
		header('Location: index.php?p=inicio'); exit;
	} else if ($pagina == 'tarefas' and $perm < 3) {
		header('Location: index.php?p=inicio'); exit;
	} else if ($pagina == 'dispositivos' and $perm < 5) {
		header('Location: index.php?p=inicio'); exit;
	} else if ($pagina == 'modos' and $perm < 5) {
		header('Location: index.php?p=inicio'); exit;
	} else if ($pagina == 'vigilancia' and $perm < 3) {
		header('Location: index.php?p=inicio'); exit;
	} else if ($pagina == 'configuracoes' and $perm < 5) {
		header('Location: index.php?p=inicio'); exit;
	} else if ($pagina == 'usuarios' and $perm < 4) {
		header('Location: index.php?p=inicio'); exit;
	} else if ($pagina == 'editar_usuario' and $perm < 4) {
		header('Location: index.php?p=inicio'); exit;
	}
?>