<?
include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

$pagina = new Pagina("", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

$url_base_sensor = "http://200.145.184.161/sensors/";


$dados = $_POST;
$saida = array();

$type = "JSON"; // JSON | HTML //FORMATO PADRÂO, SOBRESCRITO POR GET['type']

//######################################################################
//######################################################################
//######################################################################
if(!isset($dados["acao"])){//services via GET
	$dados = array();
	$req = $_GET;
	if(isset($req["type"]))
		$type = strtoupper($req["type"]);
		
	
	//fazer and entre os parametros
	$filtros = array();
	$filtros["usuario"]["campo"] = "d_campo.id_usuario_fk";
	$filtros["equipamento"]["campo"] = "d_campo.id_equipamento_fk";
	$filtros["reservatorio"]["campo"] = "d_reservatorio.id";
	$filtros["ponto"]["campo"] = "d_regiao.id";
	$filtros["data"]["campo"] = "d_campo.tempoinicio";
	$filtros["campo"]["campo"] = "d_campo.id";
	
	$filtros["usuario"]["valor"] = @$req["u"];
	$filtros["equipamento"]["valor"] = @$req["e"];
	$filtros["reservatorio"]["valor"] = @$req["r"];
	$filtros["ponto"]["valor"] = @$req["p"];
	$filtros["data"]["valor"] = @$req["d"];
	$filtros["campo"]["valor"] = @$req["c"];
	
	$strFiltro = " 1=1 ";
	foreach($filtros as $filtro){
		if($filtro["valor"] != ""){//filtro selecionado
			$strFiltro .= "AND ".$filtro["campo"]. " = '".$filtro["valor"]."'";
		}
	}
	
	echo "Filtros: $strFiltro";
	
	//fonte dos dados
	$fonte = $req["f"];
	
	if($fonte == "") $fonte = "medidas";
		
	if($fonte == "medidas"){
		$saidaGet["publicacoes"] = array();
		$rs = pg_query("SELECT * FROM publicacao ORDER BY nome");
		while($linha = pg_fetch_assoc($rs)){
			$url_service = $url_base_mapper."paginas/services.php?c=".$linha["hash"];
			$url_imagem = $url_base_mapper."paginas/".$linha["imagem"];
			
			$saidaGet["publicacoes"][] = array(
				"autor" => getAtrQuery("SELECT nome_usu FROM usuarios WHERE id_usu = '".$linha["id_usu"]."'"),
				"titulo" => $linha["nome"],
				"comentario" => $linha["descricao"],
				"identificador" => $linha["hash"],
				"gerada" => $linha["data"],
				"url_service" => $url_service, 
				"url_imagem" => $url_imagem,
				"link_service" => "<a href='$url_service'>$url_service</a>",
				"link_imagem" => "<a href='$url_imagem'><img src='$url_imagem' /></a>"
			);
		}
	}else{//código identificador definido -> exibir dados sobre a publicação
		$rs = pg_query("SELECT * FROM publicacao WHERE hash = '".$req["c"]."'");
		if(pg_num_rows($rs) == 1){
			$linha = pg_fetch_assoc($rs);
			$dados = unserialize($linha["dados"]);
			$url_service = $url_base_mapper."paginas/services.php?c=".$linha["hash"];
			$url_imagem = $url_base_mapper."paginas/".$linha["imagem"];
			$saidaGet = array(
					"autor" => getAtrQuery("SELECT nome_usu FROM usuarios WHERE id_usu = '".$linha["id_usu"]."'"),
					"titulo" => $linha["nome"],
					"comentario" => $linha["descricao"],
					"identificador" => $linha["hash"],
					"gerada" => $linha["data"],
					"dados" => $dados,
					"url_service" => $url_service, 
					"url_imagem" => $url_imagem,
					"link_service" => "<a href='$url_service'>$url_service</a>",
					"link_imagem" => "<a href='$url_imagem'><img src='$url_imagem' /></a>"
			);
		}
	}	
	unset($dados["acao"]);
}
//######################################################################
//######################################################################
//######################################################################
if(isset($dados["acao"])){//service via POST


	$saida["acao"] = $dados["acao"];


	if($dados["acao"] == "valores" || $dados["acao"] == "valores_camada"){
		//$mapper->addDebug("Executando ação valores | valores_camada");
		
		$tabela_dados = @$dados["tabela"];
		$cidade_dados = $tabela_dados . "." . getAtrQuery("SELECT cidade_bas FROM bases WHERE tabela_bas = '$tabela_dados'");
		$estado_dados = $tabela_dados . "." . getAtrQuery("SELECT estado_bas FROM bases WHERE tabela_bas = '$tabela_dados'");

		
		$tableName = $dados["tabela"];
		$columName = $dados["campo"];
		$tabela_dados = $tableName . "." . $columName;
		
		if($columName != "" && $tableName != ""){
			//$clausulasFiltros = "".$mapper->getModelagem()->getSqlRestricao()." AND upper($estado_dados::text) = upper(br_uf.sigla::text) AND upper($cidade_dados::text) = upper(br_muni.nome::text) AND upper(br_uf.nome::text) = upper(br_muni.uf::text)";
			$tabelasFiltros = "$tableName $tableName LEFT JOIN  br_uf ON upper($estado_dados::text) = upper(br_uf.sigla::text) LEFT JOIN br_muni ON (upper($cidade_dados::text) = upper(br_muni.nome::text) AND upper(br_uf.nome::text) = upper(br_muni.uf::text))";
		
			$sql = "SELECT $tableName.$columName as valor, $agregacao as qtd FROM $tabelasFiltros GROUP BY valor ORDER BY valor";
			//echo $sql;
			$mapper->addDebug("SQL Query $sql");
			
			$rs = pg_query($sql) or die(pg_last_error());
			//$linha = pg_fetch_assoc($rs);
			
			while($linha = pg_fetch_assoc($rs)){
				$saida["valores"][$linha["valor"]] = $linha["valor"] ." (".$linha["qtd"].")";
				$saida["valores_cores"][] = $linha["valor"];
			}
		}
				
	}
	
	@$saida["debug"] .= $mapper->getDebug();
	@$saida["debug"] .= "ACAO: ".$dados["acao"];
}



	if(isset($saidaGet)){
		$saida = $saidaGet;
	}

	if($type == "HTML"){
		echo stringArrayTable($saida);
	}else{
		echo toJSon($saida);
	}

?>
