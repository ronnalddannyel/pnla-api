<?php
require_once('wspnla'.DIRECTORY_SEPARATOR.'rr'.DIRECTORY_SEPARATOR.'wspnla.pesquisalicenciamentos.inc.php');

$sql='select count(*) as quantidade from '.PNLA_LIC;
$controle->executar_consulta(CONSULTA($sql,$parametros,$filtros),new CallbackPesquisaListaJSON());
