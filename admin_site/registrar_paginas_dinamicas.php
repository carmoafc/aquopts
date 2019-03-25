<?

include_once "../config/funcoes.php";

$pagina = new Pagina("registrar_paginas_dinamicas.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();


	$tabela["nome"] = "paginas_dinamicas";
	$tabela["identificador"] = "id_pag_din";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();

	$arrayMenu = getInputFromSQL(pg_query("SELECT id_ite AS ID, titulo_ite AS DESCRICAO FROM itens ORDER BY posicao_ite, titulo_ite"));

	
	$campos["id_ite_fk"] = array("nome" => "Item do Menu", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayMenu);
	$campos["texto_pag_din"] = array("nome" => "Conteúdo", "tipo" => "TEXTAREA", "editavel" => true, "tamanho" => "30");
	

	include("../registrar/registrar.php.inc");
?>

