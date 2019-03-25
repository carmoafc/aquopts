<?

include_once "../config/funcoes.php";

$pagina = new Pagina("gerir_equipamento.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();


	$tabela["nome"] = "d_equipamento";
	$tabela["identificador"] = "id";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();
	
	$campos["nome"] = array("nome" => "Nome", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["modelo"] = array("nome" => "Modelo", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");

	include("../registrar/registrar.php.inc");
?>

