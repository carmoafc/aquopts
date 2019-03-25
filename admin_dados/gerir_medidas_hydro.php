<?

include_once "../config/funcoes.php";

$pagina = new Pagina("gerir_medidas_hydro.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();


	$tabela["nome"] = "d_medidas_hydroscat";
	$tabela["identificador"] = "id";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();
	
	$arrayCampo = getInputFromSQL(pg_query("SELECT d_campo.id AS ID, concat(tempoinicio, '-', nome) AS DESCRICAO FROM d_campo LEFT JOIN d_regiao ON id_regiao_fk = d_regiao.id ORDER BY DESCRICAO"));

	$campos["id_campo_fk"] = array("nome" => "Campo", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayCampo);
	$campos["tempo_ms"] = array("nome" => "Tempo", "tipo" => "DATA", "editavel" => true, "tamanho" => "30");
	$campos["comprimento_onda"] = array("nome" => "Onda", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["intensidade"] = array("nome" => "Intensidade", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["profundidade_m"] = array("nome" => "Intensidade", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["tipo"] = array("nome" => "Tipo", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["processamento"] = array("nome" => "Processamento", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	    
        

	include("../registrar/registrar.php.inc");
?>

