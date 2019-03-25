<?
include_once "../config/funcoes.php";

$pagina = new Pagina("registrar_diretorios.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

//$con = ConectaDB();


	$tabela["nome"] = "diretorios";
	$tabela["identificador"] = "id_dir";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();
	
	$campos["caminho_dir"] = array("nome" => "Caminho do Diretório", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");

	include("../registrar/registrar.php.inc");
?>

