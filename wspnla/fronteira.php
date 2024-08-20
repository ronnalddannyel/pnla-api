<?php

/*
 * Superclasse de reúso para callbacks que retornam JSON
 */
abstract class CallbackPesquisaJSONAbstrato implements CallbackPesquisa
{
	public function cabecalho()
	{
		exibir_cabecalhos_resposta_json();
	}

	public function erro($descricao="")
	{
		exibir_erro($descricao);
	}
}

#Retorna uma lista de objetos no formato JSON
class CallbackPesquisaListaJSON extends CallbackPesquisaJSONAbstrato
{
	private $separador = "";

	public function inicio()
	{
		$this->cabecalho();
		echo '[';
	}

	public function fim()
	{
		echo ']';
	}

	public function linhaRetornada($linha)
	{
		if(defined("CODIFICACAO_BD"))
		{
			$linha = converter_codificacao_array_se_necessario($linha,CODIFICACAO_BD,CODIFICACAO_WS);
		}
		echo $this->separador.json_encode($linha);
		$this->separador = ",";
	}
}

#Retorna uma lista de objetos no formato JSON
class CallbackPesquisaListaJSONDeslocamento extends CallbackPesquisaListaJSON
{
	private $deslocamento;
	private $processados;

	public function __construct($deslocamento)
	{
		$this->deslocamento = $deslocamento;
		$this->processados = 0;
	}

	public function linhaRetornada($linha)
	{
		if($this->processados >= $this->deslocamento)
		{
			parent::linhaRetornada($linha);
		}
		$this->processados++;
	}
}


#Superclasse para os Callbacks que retornam o conteúdo de um arquivo.
abstract class CallbackPesquisaArquivo implements CallbackPesquisa
{
	private $colunaNome;
	private $colunaConteudo;
	private $exibido = false;

	public function inicio()
	{
	}

	public function fim()
	{
		if(!$this->exibido)
		{
			mostrar_erro_arquivo_nao_encontrado();
		}
	}

	public function linhaRetornada($linha)
	{
		if(!$this->exibido)
		{
			$this->exibirUnicaLinha($linha);
			$this->exibido = true;
		}
	}

	public abstract function exibirUnicaLinha($linha);

	public function erro($descricao="")
	{
		exibir_erro($descricao);
	}
}

#Retorna um arquivo que está no sistema de arquivos.
class CallbackPesquisaArquivoFS extends CallbackPesquisaArquivo
{
	private $colunaNome;
	private $prefixo;

	public function __construct($colunaNome,$prefixo="")
	{
		$this->colunaNome = $colunaNome;
		$this->prefixo = $prefixo;
	}
	public function exibirUnicaLinha($linha)
	{
		$nome = isset($linha[$this->colunaNome])?$linha[$this->colunaNome]:null ;
		if(strlen($nome) > 0 and file_exists($this->prefixo.$nome))
		{
			$arquivo = $this->prefixo.$nome;
			exibir_cabecalhos_transferencia_arquivo($arquivo,filesize($arquivo),filemtime($arquivo));
			if(pode_exibir_corpo())
			{
				ob_clean();
				flush();
				readfile($arquivo);
			}
		}
		else mostrar_erro_arquivo_nao_encontrado();
	}
}

#Retorna o status.
class CallbackStatus implements CallbackPesquisa
{
	private $erroEncontrado = false;
	private $msgErro = "";

	public function inicio()
	{
	}

	public function fim()
	{
	}

	public function linhaRetornada($linha)
	{
	}


	public function erro($descricao="")
	{
		$this->erroEncontrado = true;
		$this->msgErro = $this->msgErro.(strlen($this->msgErro) > 0 ?" ":"").$descricao;
	}

	public function mostrarMsgSucesso()
	{
		if(!$this->erroEncontrado)
		{
			exibir_cabecalhos_resposta_texto();
			echo "OK";
		}
	}

	public function getErroEncontrado()
	{
		return $this->erroEncontrado;
	}

	public function getMsgErro()
	{
		return $this->msgErro;
	}
}


# Extrai os parâmetros passados na requisição, transformando os parâmetros
# multivalorados em arrays.
function extrair_parametros_consulta($queryString=-1)
{
	if($queryString === -1) 
	{
		if($_SERVER['REQUEST_METHOD'] === 'GET')
		{
			$queryString = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : null;
		}
		elseif($_SERVER['REQUEST_METHOD'] === 'POST' 
			&& isset($_SERVER["CONTENT_TYPE"]) 
			&& (strpos($_SERVER["CONTENT_TYPE"],'application/x-www-form-urlencoded')!==false) )
		{
			$queryString = file_get_contents('php://input');
		}
		else $queryString = NULL;
	}

	$params = array();
	if(strlen($queryString))
	{
		$query  = explode('&', $queryString);

		if(count($query) > 0) foreach($query as $param)
		{
			if(strlen($param))
			{
				list($name, $value) = explode('=', $param);
				$nomeDecodificado = trim(urldecode($name));
				$valorDecodificado = trim(urldecode($value));
				if(strlen($nomeDecodificado) && strlen($valorDecodificado))
				{
					$resultado = NULL;
					if(isset($params[$nomeDecodificado]))
					{
						$valorAtual = $params[$nomeDecodificado];
						if(!is_array($valorAtual)) $valorAtual = array($valorAtual);
						$valorAtual[] = $valorDecodificado;
						$resultado = $valorAtual;
					}
					else
					{
						$resultado = $valorDecodificado;
					}

					if(isset($resultado)) $params[$nomeDecodificado] = $resultado;
				}
			}
		}
	}
	return $params;
}

# Adiciona à resposta os cabeçalhos de requisição para o download de arquivo.
function exibir_cabecalhos_transferencia_arquivo($nome=NULL,$tamanho=-1,$dataAlteracao=NULL)
{

	exibir_cabecalho_cors();
	$sulfixoContentDisposition = strlen($nome) ? "; filename = ".basename($nome):"";
	header('Content-Description: File Transfer');
	header('Content-Type: application/x-file-download');
	header('Content-Disposition: attachment'.$sulfixoContentDisposition);
	if($dataAlteracao != NULL)
	{
		header('Last-Modified'.gmdate('D, d M Y H:i:s T',$dataAlteracao));
	}
	if($tamanho >= 0)
	{
		header('Content-Length: '.$tamanho);
	}
}
# Adiciona à resposta os cabeçalhos de requisição para dados no formato JSON
function exibir_cabecalhos_resposta_json()
{
	exibir_cabecalho_cors();
	header('Content-Type: application/json;charset=UTF-8');
}

# Adiciona à resposta os cabeçalhos de requisição para dados em formato texto puro 
function exibir_cabecalhos_resposta_texto()
{
	exibir_cabecalho_cors();
	header('Content-Type: text/plain;charset=UTF-8');
}

function exibir_cabecalho_cors()
{
	header('Access-Control-Allow-Origin: *');
}

# Resposta quando a arquivo não é encontrado.
function mostrar_erro_arquivo_nao_encontrado()
{
		header("HTTP/1.0 404 Not Found",true,404);
		exibir_cabecalhos_resposta_texto();
		echo "Arquivo não encontrado";
}

# Exibe uma mensagem de erro caso os cabeçalhos ainda não tenham sido enviados.
function exibir_erro($descricao="")
{
	if(!headers_sent())
	{
		header('500 Internal Server Error',true,500);
		if(strlen($descricao)>0) echo $descricao;
	}
}

# retorna se a requisição pode ter corpo(entity-body)
function pode_exibir_corpo()
{
	return $_SERVER['REQUEST_METHOD'] != 'HEAD';
}
