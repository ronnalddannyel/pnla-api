<?php
#Funções específicas do adodb

function adodb_exibir_conteudo_arquivo_bd($conteudo)
{
	ob_clean();
	flush();
	echo $conteudo;
}

function adodb_calcular_hash_arquivo_bd($conteudo)
{
	$resultado = hash_init('md5');
	hash_update($resultado, $conteudo);
	return hash_final($resultado);
}
