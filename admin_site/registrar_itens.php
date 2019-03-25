<?

include_once "../config/funcoes.php";

$pagina = new Pagina("registrar_itens.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

//$con = ConectaDB();


	$tabela["nome"] = "itens";
	$tabela["identificador"] = "id_ite";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();

	$arrayMenu = getInputFromSQL(pg_query("SELECT id_ite AS ID, titulo_ite AS DESCRICAO FROM itens ORDER BY posicao_ite, titulo_ite"));
	$arrayVisivel = array("0" => "Não", "1" => "Sim");

	$campos["id_ite_fk"] = array("nome" => "Dentro de", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayMenu);
	$campos["posicao_ite"] = array("nome" => "Posição do Item", "tipo" => "TEXT", "editavel" => true, "tamanho" => "3");
	$campos["titulo_ite"] = array("nome" => "Nome do Item", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["visivel_ite"] = array("nome" => "Visível", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayVisivel);

	include("../registrar/registrar.php.inc");
?>

