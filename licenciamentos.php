<?php
/*
 * Retorna os dados dos licenciamentos ambientais, em formato JSON.
 */
require_once('wspnla'.DIRECTORY_SEPARATOR.'rr'.DIRECTORY_SEPARATOR.'wspnla.pesquisalicenciamentos.inc.php');

/*
 * Consulta que será feita.
 */
$sql="select 
		identificador,
		numeroProcesso,
		numeroLicenca,
		tipo,
		situacao,
		convert(varchar,dataProtocolo,126) as dataProtocolo,
		convert(varchar,dataEmissao,126) as dataEmissao,
		convert(varchar,dataVencimento,126) as dataVencimento,
		textoLicenca,
		extratoLicenca as resumoLicenca,
		orgaoResponsavel,
		estado,
		codigoTipologia,
		descricaoTipologia,
		potencialPoluidor,
		porteEmpreendimento,
		nomeEmpreendedor,
		cpfCnpjEmpreendedor,
		logradouroEmpreendedor,
	 	numeroEnderecoEmpreendedor as numeroEmpreendedor,
		distritoEmpreendedor,
		municipioEmpreendedor,
		cepEmpreendedor,
		ufEmpreendedor,
		nomeEmpreendimento,
		classeEmpreendimento,
		cpfCnpjEmpreendimento,
		logradouroEmpreendimento,
		numeroEnderecoEmpreendimento as numeroEmpreendimento,
		distritoEmpreendimento,
		municipioEmpreendimento,
		cepEmpreendimento,
		ufEmpreendimento,
		outrosMunicipios,
		bacia as baciaHidrografica,
		rio as nomeRio,
		latitude,
		longitude,
		projecao,
		fuso,
		datum
	from ".PNLA_LIC;

/*
 * Filtros. Aos filtros de pesquisa da tela, adiciona-se o filtro de pesquisa por identificador.
 */
$filtros=E($filtros,IGUAL($parametros,"identificador","identificador"));

$controle->executar_consulta(CONSULTA($sql,$parametros,$filtros),new CallbackPesquisaListaJSON());


