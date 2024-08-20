<?php
require_once('wspnla'.DIRECTORY_SEPARATOR.'rr'.DIRECTORY_SEPARATOR.'wspnla.inc.php');

$sql='select top 1 nome from '.PNLA_DOC;
$filtros=IGUAL($parametros,'identificador','identificador');
$controle->executar_consulta(CONSULTA($sql,$parametros,$filtros),new CallbackPesquisaArquivoFS('nome'));
