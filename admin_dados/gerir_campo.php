<?

include_once "../config/funcoes.php";

$pagina = new Pagina("gerir_campo.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();


	$tabela["nome"] = "d_campo";
	$tabela["identificador"] = "id";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();

	$arrayRegiao = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_regiao ORDER BY DESCRICAO"));
	$arrayUsuario = getInputFromSQL(pg_query("SELECT id_usu AS ID, nome_usu AS DESCRICAO FROM usuarios ORDER BY DESCRICAO"));
	$arrayArquivo = getInputFromSQL(pg_query("SELECT id_arq AS ID, nome_arq AS DESCRICAO FROM arquivos ORDER BY DESCRICAO"));
	$arrayEquipamento = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_equipamento ORDER BY DESCRICAO"));

	$campos["tempoinicio"] = array("nome" => "Data de início", "tipo" => "DATA", "editavel" => true, "tamanho" => "30");
	$campos["id_regiao_fk"] = array("nome" => "Região(ponto)", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayRegiao);
	$campos["id_usuario_fk"] = array("nome" => "Usuário", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayUsuario);
	$campos["id_arq_fk"] = array("nome" => "Arquivo", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayArquivo);
	$campos["id_equipamento_fk"] = array("nome" => "Equipamento", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayEquipamento);
        
        

	include("../registrar/registrar.php.inc");
?>

