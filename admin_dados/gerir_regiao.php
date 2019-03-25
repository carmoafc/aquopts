<?

include_once "../config/funcoes.php";

$pagina = new Pagina("gerir_regiao.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();


	$tabela["nome"] = "d_regiao";
	$tabela["identificador"] = "id";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();

	$arrayReservatorio = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_reservatorio ORDER BY DESCRICAO"));

	$campos["nome"] = array("nome" => "Nome", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["descricao"] = array("nome" => "Descrição", "tipo" => "TEXTAREA", "editavel" => true, "tamanho" => "30");
	$campos["latitude"] = array("nome" => "Latitude", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["longitude"] = array("nome" => "Longitude", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["id_reservatorio_fk"] = array("nome" => "Reservatório", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayReservatorio);
        
	include("../registrar/registrar.php.inc");
?>

