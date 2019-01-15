<?php
	function _title() {
		$p = (isset($_GET['p'])) ? $_GET['p'] : 'supervisorio';

		switch ($p) {
			case 'supervisorio' : $title = 'Supervisório'; break;
			case 'tasks' : $title = 'Tarefas'; break;
			case 'vigilancia' : $title = 'Vigilância'; break;
			case 'devices' : $title = 'Dispositivos'; break;
			case 'device' : $title = 'Dispositivo'; break;
			case 'modos' : $title = 'Modos'; break;
			case 'configuracoes' : $title = 'Configurações'; break;
			case 'alterar_senha' : $title = 'Alterar Senha'; break;
			case 'cadastro' : $title = 'Cadastro'; break;
			case 'usuarios' : $title = 'Usuários'; break;
			case 'novo_usuario' : $title = 'Novo usuário'; break;
			case 'editar_usuario' : $title = 'Editar Usuário'; break;
			default	: $title = 'Supervisório'; break;
		}

		return $title;
	}

	function _menu_ativo() {
		$p = (isset($_GET['p'])) ? $_GET['p'] : 'supervisorio';
		$p = str_replace('/', '', $p);

		switch ($p) {
			case 'supervisorio' : $menu_ativo = 1; break;
			case 'tasks' : $menu_ativo = 2; break;
			case 'devices' : $menu_ativo = 3; break;
			case 'device' : $menu_ativo = 3; break;
			case 'modos' : $menu_ativo = 4; break;
			case 'vigilancia' : $menu_ativo = 5; break;
			case 'configuracoes' : $menu_ativo = 6; break;
			case 'alterar_senha' : $menu_ativo = 7; break;
			case 'cadastro' : $menu_ativo = 7; break;
			case 'usuarios' : $menu_ativo = 7; break;	
			case 'novo_usuario' : $menu_ativo = 7; break;
			case 'editar_usuario' : $menu_ativo = 7; break;
			default	: $menu_ativo = 1; break;;
		}

		return $menu_ativo;
	}		
?>