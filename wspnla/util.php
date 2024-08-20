<?php

define('CODIFICACAO_WS','UTF-8');

/**
 * Se o parâmetro for um vetor, retorna o primeiro elemento.
 * Caso contrário, retorna o próprio elemento.
 */
function extrair_unico_elemento($vetor)
{
	if(is_array($vetor) and (count($vetor) > 0)) return $vetor[0];
	else return $vetor;
}

/**
 * Extrai as chaves especificadas do mapa, na ordem.
 *
 * Exemplo de uso: 
 * list($a,$b) = extrair_elementos_por_nome($mapa,"a","b");
 */
function extrair_elementos_por_nome($mapa)
{
	$args = func_get_args();
	$chaves = array_filter(array_slice($args,1));
	$resultado = array();
	foreach($chaves as $chave)
	{
		$resultado[] = (($mapa !== null) and isset($mapa[$chave])) ? $mapa[$chave] : null;
	}
	return $resultado;
}

function converter_codificacao_array_se_necessario($array,$de,$para)
{
	if($de != $para) return converter_codificacao_array($array,$de,$para);
	else return $array;
}

function converter_codificacao_array($array,$de,$para)
{
	$retorno = array();
	$para = $para.'//TRANSLIT';
	foreach ($array as $key => $value)
	{
		if(is_string($value))
		{
			$value = iconv($de,$para,$value);
		}
		$retorno[$key] = $value;
	}
	return $retorno;

}

/*
 * Se valor for uma string contendo apenas digitos (0-9), retorna a string.
 * Caso contrário, retorna nulo.
 */
function obterStringNumerica($valor)
{
	if(is_string($valor) && ctype_digit($valor))
	{
		return $valor;
	}
	return NULL;
}

/**
 * Interface que deve ser implementada para mostrar os resultados das pesquisas.
 */
interface CallbackPesquisa
{
	/*
	 * Chamada em uma consulta bem sucedida,
	 * antes do primeiro parâmetro.
	 */
	public function inicio();

	/*
	 * Chamada após o último parâmetro ter sido retornado.
	 */
	public function fim();

	/*
 	 * Passa os dados da linha retornada, na forma de uma
	 * lista associativa.
	 */
	public function linhaRetornada($linha);

	/*
	 * Deve mostrar a mensagem de erro.
	 */
	public function erro($descricao="");
}
