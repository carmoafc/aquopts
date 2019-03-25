<?
include_once "../config/funcoes.php";

$pagina = new Pagina("registrar_variaveis.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

	$tabela["nome"] = "variaveis";
	$tabela["identificador"] = "id_var";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();
	
	$campos["nome_var"] = array("nome" => "Nome da Variável", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["valor_var"] = array("nome" => "Valor da Variável", "tipo" => "TEXTAREA", "editavel" => true, "tamanho" => "50");

	include_once("../registrar/registrar.php.inc");
?>

