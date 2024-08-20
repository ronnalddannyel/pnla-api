<?php
require_once(__DIR__.DIRECTORY_SEPARATOR.'wspnla.inc.php');
$filtros = E(
	OU(
		IGUAL($parametros,"numeroProcesso","numeroProcesso"),
		LIKE($parametros,"nomeEmpreendedor","nomeEmpreendedor",FORMATO_LIKE_SQL_SERVER),
		IGUAL($parametros,"cpfCnpjEmpreendedor","cpfCnpjEmpreendedor"),
		LIKE($parametros,"nomeEmpreendimento","nomeEmpreendimento",FORMATO_LIKE_SQL_SERVER),
		IGUAL($parametros,"cpfCnpjEmpreendimento","cpfCnpjEmpreendimento")
	),
	LIKE($parametros,"descricaoTipologia","palavraChave",FORMATO_LIKE_SQL_SERVER),
	INTERVALO($parametros,"dataProtocolo","dataProtocoloDe","dataProtocoloAte",FORMATACAO_PARAMETRO_DATA_SQL_SERVER),
	INTERVALO($parametros,"dataEmissao","dataEmissaoDe","dataEmissaoAte",FORMATACAO_PARAMETRO_DATA_SQL_SERVER),
	INTERVALO($parametros,"dataVencimento","dataVencimentoDe","dataVencimentoAte",FORMATACAO_PARAMETRO_DATA_SQL_SERVER),
	IGUAL($parametros,"descricaoTipologia","descricaoTipologia"),
	IGUAL($parametros,"tipo","tipo"),
	IGUAL($parametros,"situacao","situacao")
);
