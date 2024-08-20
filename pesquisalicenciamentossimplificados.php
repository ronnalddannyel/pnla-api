<?php
require_once('wspnla'.DIRECTORY_SEPARATOR.'rr'.DIRECTORY_SEPARATOR.'wspnla.pesquisalicenciamentos.inc.php');

$min = obterStringNumerica($parametros->obterValor("deslocamento"));
$limite = obterStringNumerica($parametros->obterValor("limite"));
$max=($min + $limite);
$sql="select top ".$max."
	identificador,
	numeroProcesso,
	descricaoTipologia,
	convert(varchar,dataEmissao,126) as dataEmissao,
	convert(varchar,dataVencimento,126) as dataVencimento,
	nomeEmpreendimento,
	tipo,
	situacao,
	rio as nomeRio
	from ".PNLA_LIC." ".WHERE;
$controle->executar_consulta(CONSULTA($sql,$parametros,$filtros),new CallbackPesquisaListaJSONDeslocamento($min));

