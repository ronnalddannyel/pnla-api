<?php
#Placeholder para a cláusula where
define('WHERE','_FILTROS_');

#Placeholder para cláusula where na forma de expressão regular
define('REGEX_WHERE',"/".WHERE."/");

#Placeholder para um parâmetro
define('PARAMETRO','<!PARAMETRO!>');

#Placeholder para um parâmetro na forma de expressão regular
define('REGEX_PARAMETRO','/'.PARAMETRO.'/');

#Armazena os parâmetros de pesquisa provenientes da requisição.
class Parametros
{
	#Parametros encontrados na requisição
	private $parametrosRequisicao;

	/*
	 * Lista ordenada dos parâmetros que foram
	 * efetivamente utilizados na construção do sql
	 */
	private $parametrosUtilizados;
	#Placeholder utilizado para os parêmtros
	private $placeholder;

	/*
	 * Se 'true', os parâmetros serão na forma 
	 * 	:p1,:p2, ... pn (ex: driver Oracle)
         * Se false, serão simplesmente um '?'
         */
	private $usarNamedParameters; 

	public function __construct($parametrosRequisicao)
	{
		$this->parametrosRequisicao = $parametrosRequisicao;
		$this->parametrosUtilizados = array();
		$this->placeholder = defined('PLACEHOLDER') ? PLACEHOLDER :'?';
		if(defined('USAR_NAMED_PARAMETERS'))
		{
			$this->usarNamedParameters = USAR_NAMED_PARAMETERS;
		}else
		{
			$this->usarNamedParameters = false;		
		}

	}
	
	/*
	 * Adiciona $valor ao mara de parâmetros disponívels.
     * Se o parâmetro já existir, ele é substituído.	 
	 */
	public function definirParametroDisponivel($nome,$valor)
	{
		$this->parametrosRequisicao[$nome]=$valor;
	}

	/*
	 * Adiciona um parâmetros à lista de parâmetros
	 * utilizados e retorna o placeholder a ser
	 * colocado no sql.
	 *
	 * Esse é um método interno, usado pelo
	 * método consumirParametro.
	 */
	private function adicionarParametro($valor)
	{
		$resultado = null;
		if($this->usarNamedParameters)
		{
			$indice = count($this->parametrosUtilizados);
			$resultado = ":p".$indice;
			$this->parametrosUtilizados['p'.$indice] = $valor;
		}
		else
		{
			$resultado = $this->placeholder;
			$this->parametrosUtilizados[] = $valor;
		}
		return $resultado;
	}

	/*
	 * Retorna o valor do parâmetro indicado.
	 */
	public function obterValor($nome)
	{
		if(isset($this->parametrosRequisicao[$nome])) return $this->parametrosRequisicao[$nome];
		else return null;
	}

	/*
	 * Se o parâmetro indicado for uma lista,
	 * retorna o primeiro elemento. Caso contrário,
	 * retorna o próprio valor do parâmetro.
	 */
	public function obterValorUnico($nome)
	{
		$resultado = $this->obterValor($nome);
		if(is_array($resultado)) return $resultado[0];
		else return $resultado;
	}

	/**
	 * Armazaena o valor do parâmetro e retorna o fragmento
	 * de sql a ser adicionado na consulta
	 */
	public function consumirParametro($valor)
	{
		$fragmentoSql = "";
		if(is_array($valor))
		{
			$resultado = array();
			foreach($valor as $v) $resultado[] = $this->adicionarParametro($v);
			$fragmentoSql=implode(",",$resultado); 
		}
		else 
		{
			$fragmentoSql=$this->adicionarParametro($valor);
		}
		return $fragmentoSql;
	}

	/*
	 * Retorna os parâmetros que foram efetivamente
	 * utilizados na consulta sql
	 */
	public function getParametrosUtilizados()
	{
		return $this->parametrosUtilizados;
	}

	/*
	 * Armazena os valores dos parâmetros de paginação,
	 * marcando-os como utilizados.
	 */
	public function consumirParametrosPaginacao($parametrosPaginacao=NULL)
	{
		if($parametrosPaginacao === NULL)
		{
			$parametrosPaginacao = unserialize(PARAMETROS_PAGINACAO);
		}
		foreach($parametrosPaginacao as $parametro)
		{
			$valor = $this->obterValor($parametro);
			if($valor === null || !is_numeric($valor) || ($valor < 0)) $valor = 0; 
			if($this->usarNamedParameters)
			{
				$this->parametrosUtilizados[$parametro] = $valor;
			}
			else
			{
				$this->parametrosUtilizados[] = $valor;
			}
		}
	}
}
/*
 * Verifica se o parâmetros contem o placeholder.
 */
function formatacaoParametroValida($formatacao)
{
	if(preg_match(REGEX_PARAMETRO,$formatacao)) return TRUE;
	else return FALSE;
}
/*
 * Gera um filtro na forma 
 * 	coluna = :parametro
 * ou
 *	coluna in (:parametro1,:parametro2,...,:parametroN)
 */
function IGUAL($parametros,$coluna,$parametro)
{
	$sql = null;
	$valor = $parametros->obterValor($parametro);
	if($valor !== null)
	{
		if(is_array($valor))
		{
			$sql = $coluna." in (".$parametros->consumirParametro($valor).")";
		}
		else
		{
			$sql = $coluna." = " .$parametros->consumirParametro($valor);
		}	
	}
	return $sql;
}

/*
 * Faz um filtro da forma:
 *	coluna operador parametro
 * Ex: id < :parametro
 */
function COMPARACAO($parametros,$operador,$coluna,$parametro,$formatacaoParametro=NULL)
{
	$sql = null;
	$valor = $parametros->obterValorUnico($parametro);
	if($valor !== null)
	{
		$placeholder = $parametros->consumirParametro($valor);
		if(strlen($formatacaoParametro)>0 and formatacaoParametroValida($formatacaoParametro)) $placeholder = preg_replace(REGEX_PARAMETRO,$placeholder,$formatacaoParametro);
		$sql = $coluna." ".$operador." ".$placeholder;
	}
	return $sql;
}
/*
 * Faz um filtro na forma:
 * coluna like :parametro
 * O parâmetro é formatado para começar e terminar com '%'
 *
 * O parâmetro opcional formato especifica como será gerado o fragmento.
 * Ao especificá-lo, ele deve receber 2 strings, sendo a primeira
 * a coluna e a segunda o placeholder do parâmetro.
 */
function LIKE($parametros,$coluna,$parametro,$formato="%s like %s")
{
	$sql = null;
	$valores = $parametros->obterValor($parametro);
	if($valores !== null)
	{
		if(is_array($valores))
		{
			$partes=array();
			foreach($valores as $valor)
			{
				$partes[] = gerarClausulaLike($parametros,$coluna,$valor,$formato);
			}
			if(count($partes) > 0)
			{
				$sql = call_user_func_array("OU",$partes);
			}
		}
		else
		{
			$sql = gerarClausulaLike($parametros,$coluna,$valores,$formato);
		}
	}
	return $sql;
}

function gerarClausulaLike($parametros,$coluna,$valor,$formato)
{

	$valorFormatado = '%'.preg_replace('/%/','\\%', preg_replace('/\\\\/','\\\\\\\\',$valor)).'%';
	return sprintf($formato,$coluna,$parametros->consumirParametro($valorFormatado));
}

/*
 * Filtro de intervalo para inteiros e datas.
 * Retorna um filtro na forma
 * coluna >= :parametro1 and coluna <= :parametro2
 */
function INTERVALO($parametros,$coluna,$de,$ate,$formatacaoParametro=NULL)
{
	return E(COMPARACAO($parametros,">=",$coluna,$de,$formatacaoParametro),COMPARACAO($parametros,"<",$coluna,$ate,$formatacaoParametro));
}

/*
 * Um filtro não suportado. Se o parâmetro foi passado, esse filtro 
 * fará com que a consulta não retorne nenhum resultado
 */
function NAO_SUPORTADO($parametros,$parametro)
{

	$sql = null;
	$valor = $parametros->obterValor($parametro);
	if($valor !== null)
	{
		$sql="1=2";
	}
	return $sql;
}

/*
 * Agrupa vários filtros, utilizando um separador.
 *
 * Ex: SEPARADOR: AND
 *	a = :p1 AND b = :p2 AND c < :p3
 */
function MULTIFILTRO($separador)
{
	$args = func_get_args();
	$fragmentos = array_filter(array_slice($args,1));
	$qntd = count($fragmentos);
	if($qntd>0)
	{
		$sql = implode(" ".$separador." ",$fragmentos);
		if($qntd>1) $sql = "(".$sql.")";
		return $sql;
	}
}

/*
 * Agrupa vários filtros, utilizando o separador AND.
 *
 * Ex: a = :p1 AND b = :p2 AND c < :p3
 */
function E()
{
	$parametros = func_get_args();
	return call_user_func_array("MULTIFILTRO",array_merge(array("AND"),$parametros));
}

/*
 * Agrupa vários filtros, utilizando o separador OR.
 *
 * Ex: a = :p1 OR b = :p2 OR c < :p3
 */
function OU()
{
	$parametros = func_get_args();
	return call_user_func_array("MULTIFILTRO",array_merge(array("OR"),$parametros));
}

/*
 * Consome os parâmetros de paginação e gera a consulta
 */
function CONSULTA_PAGINADA($sql,$parametros,$filtro,$parametrosPaginacao=NULL)
{
	$parametros->consumirParametrosPaginacao($parametrosPaginacao);
	return CONSULTA($sql,$parametros,$filtro);
}

/*
 * Retorna um array, contendo o sql final e os parâmetros de pesquisa.
 * O sql final o template passado no parâmetro "$sql", com a adição
 *  dos filtros de pesquisa.
 */
function CONSULTA($sql,$parametros,$filtro="",$complemento="")
{
	$resultado = null;
	$where = " ";
	if(strlen(trim($filtro))) 
	{
		#Adiciona o where se foram especificados filtros.
		$where = " where ".$filtro." ";
	}
	if(preg_match(REGEX_WHERE,$sql))
	{
		#Se existe o placeholder para o where, colocar os filtros no lugar do placeholder
		$resultado = array(preg_replace(REGEX_WHERE,$where,$sql).$complemento,$parametros->getParametrosUtilizados());
	} else 
	{
		#Não há placeholder. Colocar no fim do sql, antes do complemento.
		$resultado = array($sql.$where.$complemento,$parametros->getParametrosUtilizados());
	}
	return $resultado;
}
