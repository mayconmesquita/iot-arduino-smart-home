<?php
	function geraSenha ($tamanho = 8, $maiusculas = true, $numeros = true){
		// Caracteres de cada tipo
		$lmin = 'abcdefghijkmnpqrstuvwxyz';
		$lmai = 'ABCDEFGHIJKMNPQRSTUVWXYZ';
		$num  = '123456789';

		// Variáveis internas
		$retorno = '';
		$caracteres = '';

		// Agrupamos todos os caracteres que poderão ser utilizados
		$caracteres .= $lmin;

		if ($maiusculas) $caracteres .= $lmai;
		if ($numeros) $caracteres .= $num;

		// Calculamos o total de caracteres possíveis
		$len = strlen($caracteres);

		for ($n = 1; $n <= $tamanho; $n++) {
			// Criamos um número aleatório, para pegar um dos caracteres
			$rand = mt_rand(1, $len);

			// Concatenamos um dos caracteres na variável $retorno
			$retorno .= $caracteres[$rand-1];
		}

		return $retorno;
	}
?>