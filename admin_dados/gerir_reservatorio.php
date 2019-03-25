<?

include_once "../config/funcoes.php";

$pagina = new Pagina("gerir_reservatorio.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();


	$tabela["nome"] = "d_reservatorio";
	$tabela["identificador"] = "id";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();

	$campos["nome"] = array("nome" => "Nome", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
    $campos["descricao"] = array("nome" => "Descrição", "tipo" => "TEXTAREA", "editavel" => true, "tamanho" => "30");
//    $campos["localizacao_geo"] = array("nome" => "Localização", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
    
	include("../registrar/registrar.php.inc");
?>

