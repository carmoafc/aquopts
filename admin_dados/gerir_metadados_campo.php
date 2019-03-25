<?

include_once "../config/funcoes.php";

$pagina = new Pagina("gerir_metadados_campo.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();


	$tabela["nome"] = "d_metadados_campo";
	$tabela["identificador"] = "id";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();

	$arrayCampo = getInputFromSQL(pg_query("SELECT id AS ID, tempoinicio AS DESCRICAO FROM d_campo ORDER BY DESCRICAO"));

	$campos["id_campo_fk"] = array("nome" => "Equipamento", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayCampo);
	$campos["atributo"] = array("nome" => "Atributo", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["valor"] = array("nome" => "Valor", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");

	include("../registrar/registrar.php.inc");
?>

