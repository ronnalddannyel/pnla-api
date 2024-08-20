<?php
/**
 * Diretório onde foram descompactados os arquivos do framework.
 */
define('BASE_FRAMEWORK',__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);

/**
 * Parâmetros 
 */
define('SQLSERVER',BASE_FRAMEWORK.'bd'.DIRECTORY_SEPARATOR.'sqlserver'.DIRECTORY_SEPARATOR);

require_once('wspnla.config.php');
require_once(BASE_FRAMEWORK.'util.php');
require_once(BASE_FRAMEWORK.'sql.php');
require_once(BASE_FRAMEWORK.'fronteira.php');
require_once(SQLSERVER.'sqlserver.inc.php');

$dsn = "Driver={SQL Server};Server=".BD_HOST.";Database=".BD_DATABASE.";";

$controle = new ControlePesquisa('odbc_mssql',array($dsn,BD_USUARIO,BD_SENHA));
$parametros = new Parametros(extrair_parametros_consulta());
