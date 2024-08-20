<?php
/**
 * Controle de pesquisa que usa o Adodb
 */
class ControlePesquisa
{
	private $driver;
	private $parametrosConexao;

	public function __construct($driver,$parametrosConexao)
	{
			 $this->driver = $driver;
			 $this->parametrosConexao = $parametrosConexao;
	}

	function executar_consulta($consulta,$callback)
	{
		$conn = &ADONewConnection($this->driver);
		call_user_func_array(array(&$conn, 'PConnect'),$this->parametrosConexao);
		if(defined('ADODB_HABILITAR_DEBUG'))
		{
			$conn->debug = ADODB_HABILITAR_DEBUG;
		}
		list($sql,$parametros) = $consulta;
		if(defined("CODIFICACAO_BD"))
		{
			$parametros = converter_codificacao_array_se_necessario($parametros,CODIFICACAO_WS,CODIFICACAO_BD);
		}
		$rs= &$conn->Execute($sql,$parametros);
		if($rs)
		{
			$callback->inicio();
			while(!$rs->EOF)
			{
				$callback->linhaRetornada($rs->GetRowAssoc());
				$rs->MoveNext();
			}
			$callback->fim();
		}
		
	}

	private function validar($callback,$obj,$msg_erro)
	{
		if(!$obj)
		{
			$callback->erro($msg_erro);
			return false;
		}
		else
		{
			return true;
		}
	}

}


