<?php
/*
 * Consulta cada uma das views utilizadas pelo webservice.
 * Em caso de erro, mostra qual view está com problema e a mensagem de erro.
 */ 
require_once('wspnla'.DIRECTORY_SEPARATOR.'rr'.DIRECTORY_SEPARATOR.'wspnla.inc.php');
/*
 * Consultas a serem feitas. 
 */
$consultas=array(
	PNLA_LIC => "select top 1 identificador from ".PNLA_LIC,
	PNLA_DOC => "select top 1 identificador from ".PNLA_DOC,
	PNLA_MULTA => "select top 1 cpfCnpj from ".PNLA_MULTA
);
$callback=new CallbackStatus();
foreach($consultas as $tabela => $consulta)
{
	$controle->executar_consulta(CONSULTA($consulta,$parametros),$callback);
	if($callback->getErroEncontrado())
	{
		$msg = "Tabela ".$tabela.". ".$callback->getMsgErro();
		exibir_erro($msg);
		exit;
	}
}
$callback->mostrarMsgSucesso();
