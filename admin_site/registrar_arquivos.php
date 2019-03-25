<?
include_once "../config/funcoes.php";

$pagina = new Pagina("registrar_arquivos.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

	$tabela["nome"] = "arquivos";
	$tabela["identificador"] = "id_arq";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();

	$tabela["tem_arquivo"] = true;
	
	$arrayDir = getInputFromSQL(pg_query("SELECT id_dir AS ID, caminho_dir AS DESCRICAO FROM diretorios ORDER BY caminho_dir"));
	
	$campos["id_dir_fk"] = array("nome" => "Diretório", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayDir);
	$campos["nome_arq"] = array("nome" => "Caminho do Arquivo", "tipo" => "FILE", "editavel" => true, "tamanho" => "30", "diretorio" => "id_dir_fk");
	$campos["data_arq"] = array("nome" => "Data do Arquivo", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");

	include("../registrar/registrar.php.inc");
?>

