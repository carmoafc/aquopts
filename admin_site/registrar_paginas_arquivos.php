<?

include_once "../config/funcoes.php";

$pagina = new Pagina("registrar_paginas_arquivos.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();


	$tabela["nome"] = "paginas_arquivos";
	$tabela["identificador"] = "id_pag_arq";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();

	$arrayMenu = getInputFromSQL(pg_query("SELECT id_ite AS ID, titulo_ite AS DESCRICAO FROM itens ORDER BY titulo_ite"));
        $arrayDir = getInputFromSQL(pg_query("SELECT id_dir AS ID, caminho_dir AS DESCRICAO FROM diretorios ORDER BY caminho_dir"));

	$campos["id_ite_fk"] = array("nome" => "Item", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayMenu);
        $campos["id_dir_fk"] = array("nome" => "Diretório", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayDir);
        $campos["link_pag_arq"] = array("nome" => "Link da página", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");

	include("../registrar/registrar.php.inc");
?>

