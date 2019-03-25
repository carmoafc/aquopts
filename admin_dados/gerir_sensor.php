<?

include_once "../config/funcoes.php";

$pagina = new Pagina("gerir_sensor.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();


	$tabela["nome"] = "d_sensor";
	$tabela["identificador"] = "id";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = $pagina->getNomeArquivo();

	$arrayEquipamento = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_equipamento ORDER BY DESCRICAO"));
        
    
    $campos["nome"] = array("nome" => "Nome", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
    $campos["modelo"] = array("nome" => "Modelo", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
    $campos["id_equipamento_fk"] = array("nome" => "Equipamento", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayEquipamento);
    
	include("../registrar/registrar.php.inc");
?>

