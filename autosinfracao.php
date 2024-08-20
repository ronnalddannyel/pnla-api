<?php
require_once('wspnla'.DIRECTORY_SEPARATOR.'rr'.DIRECTORY_SEPARATOR.'wspnla.inc.php');

/*
 * Consulta que será feita. O filtro é opcional.
 */
$sql='select cpfCnpj,autuado from '.PNLA_MULTA;
$filtros=IGUAL($parametros,'cpfCnpj',"cpfCnpj");
$controle->executar_consulta(CONSULTA($sql,$parametros,$filtros),new CallbackPesquisaListaJSON());

