<?

include_once "../config/funcoes.php";

$pagina = new Pagina("gerir_metadados_sensor.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();


	$tabela["nome"] = "d_metadados_sensor";
	$tabela["identificador"] = "id";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();

	$arraySensor = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_sensor ORDER BY DESCRICAO"));

	$campos["id_sensor_fk"] = array("nome" => "Equipamento", "tipo" => "SELECT", "editavel" => true, "valores" => $arraySensor);
	$campos["atributo"] = array("nome" => "Atributo", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["valor"] = array("nome" => "Valor", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");

	include("../registrar/registrar.php.inc");
?>

