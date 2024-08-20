<?php
require_once('wspnla'.DIRECTORY_SEPARATOR.'rr'.DIRECTORY_SEPARATOR.'wspnla.inc.php');

$sql="select 
	identificador,
	licenciamento,
	numeroProcesso,
	numeroDocumento,
	tipoDocumento,
	tamanhoDocumento,
	resumo as assuntoDocumento,
	convert(varchar,data,126),
	nome
	from ".PNLA_DOC;
$filtros=IGUAL($parametros,'licenciamento','licenciamento');

$controle->executar_consulta(CONSULTA($sql,$parametros,$filtros),new CallbackPesquisaListaJSON());
