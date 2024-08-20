<?php
require_once('wspnla'.DIRECTORY_SEPARATOR.'rr'.DIRECTORY_SEPARATOR.'wspnla.pesquisalicenciamentos.inc.php');

$sql='select 
		count(*) as quantidade,
		descricaoTipologia,
		situacao,
		tipo
	from '.PNLA_LIC;
$complemento='group by
			descricaoTipologia,
			situacao,
			tipo';
$controle->executar_consulta(CONSULTA($sql,$parametros,$filtros,$complemento),new CallbackPesquisaListaJSON());
