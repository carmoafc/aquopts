<?

include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

$pagina = new Pagina("dados_visualizar.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

ini_set('post_max_size', '50M');
ini_set('upload_max_filesize', '50M');

function columnfy($atr){
	$atr = str_replace('.', "_", $atr);
	$atr = str_replace(' ', "", $atr);
	return $atr;
}
/*
$_POST["sql_from"] = "(select d_medidas_trios.*, d_medidas_trios_espectro.comprimento_onda, d_medidas_trios_espectro.intensidade FROM d_medidas_trios_espectro LEFT JOIN d_medidas_trios ON id_medidas_trios_fk = d_medidas_trios.id) as t0  LEFT JOIN d_conjunto as d_conjunto ON t0.id_conjunto_fk = d_conjunto.id LEFT JOIN d_campo as d_campo ON id_campo_fk = d_campo.id  LEFT JOIN d_equipamento as d_equipamento ON id_equipamento_fk = d_equipamento.id  LEFT JOIN d_regiao as d_regiao ON id_regiao_fk = d_regiao.id  LEFT JOIN d_reservatorio as d_reservatorio ON id_reservatorio_fk = d_reservatorio.id";
$_POST["sql_order"] = "t0.profundidade_m";
$_POST["sql_filter"] = "d_reservatorio.id = 13 AND d_regiao.nome = '34' AND t0.tipo = 'Lu'";
$_POST["eixo_x"] = "t0.comprimento_onda";
$_POST["eixo_y"] = "t0.intensidade";
$_POST["label"] = "t0.profundidade_m";
$_POST["acao"] = "dados_grafico";
* */

$dados = array();
$dados["enviados"] = array(); 
foreach ($_POST as $c => $v) $dados["enviados"]["$c"] = $v;
$dados["saida"] = array();
//print_r($dados);

$acao = post('acao');

if($acao == "valores_distintos"){
	$sql_where = post2("sql_where");
	$coluna = post2("coluna");
	$sql_from = post2("sql_from");
	
	$sql = "SELECT DISTINCT($coluna) FROM $sql_from WHERE $sql_where ORDER BY $coluna";
	$rsDados = pg_query($sql) or die("Error SQL");
	$valores = array();
	while($linha = pg_fetch_row($rsDados)) $valores[] = $linha[0];
	
	$dados["saida"]["sql"] = $sql;
	$dados["saida"]["valores"] = $valores;
}
else if($acao == "dados_grafico"){
	/**
	 * ENTRADAS
	 * @var unknown $sql_from
	 */
	$sql_from = post2("sql_from");
	$sql_order = post2("sql_order");
	$sql_filter = post2("sql_filter");
	$eixo_x = post2("eixo_x");
	$eixo_y = post2("eixo_y");
	$label = post2("label");
	//die($sql_order);
	
	//if($eixo_x == "") $eixo_x = "(rank() over (partittion by $label order by t0.id))";
	
	/**
	 * ALterando a ordem da ordenação
	 * @var unknown $campo
	 */
	$campo = explode(',', $sql_order);
	for($i = 0; $i < count($campo); $i++){
		if($campo[$i] == $eixo_x)
			unset($campo[$i]);
	}
	if($eixo_x != "")
		$campo[] = $eixo_x;
	//$campo[] = 't0.id';
	$sql_order = implode(', ', $campo);
	
	$sql_campos = "";
	foreach($campo as $c) $sql_campos .= $c." as ".columnfy($c).", ";
	foreach(explode(',', $label) as $l) $sql_campos .= $l." as ".columnfy($l).", ";
	$sql_campos .= $eixo_y." as ".columnfy($eixo_y);
	
	if($eixo_x == ""){
		$eixo_x = "rank_id";
		$sql_campos .= ", ". "(rank() over (partition by $label order by t0.id)) as $eixo_x";
		$sql_order .= ", $eixo_x";
	}
	
	$sql = "SELECT $sql_campos FROM $sql_from WHERE $sql_filter ORDER BY $sql_order";
	//die($sql);
	$dados["saida"]["sql"] = $sql;
	
	
	//die($sql);
	
	$curvas = array();
	#trace("Executando: $sql");
	$rs = pg_query($sql) or die(pg_last_error());
	#trace("Ok! Rodou");
	do{
		$linha = pg_fetch_assoc($rs);
		//print_r($linha);
		$x = $linha[columnfy($eixo_x)];
		$y = $linha[columnfy($eixo_y)];
		if(!is_numeric($x) || !is_numeric($y)) continue;
		$curva = array();
		$curva["type"] = "line";
		$lbls = array(); foreach(explode(',', $label) as $l) $lbls[] = $linha[columnfy($l)]; $n_lbl = implode(",", $lbls);
		$curva["name"] = $n_lbl;
		//$curva["name"] = $linha[columnfy($label)];
		$curva["dataPoints"] = array();
		$curva["dataPoints"][] = array("x"=>floatval($x), "y"=>floatval($y));
		$cursor_atualizado = false;
		while($linha = pg_fetch_assoc($rs)){
			$xn = $linha[columnfy($eixo_x)];
			$yn = $linha[columnfy($eixo_y)];
			if(!is_numeric($xn) || !is_numeric($yn)) continue;
			
			$lbls = array(); foreach(explode(',', $label) as $l) $lbls[] = $linha[columnfy($l)]; $n_lbl = implode(",", $lbls);
			
			if($xn < $x || $n_lbl != $curva["name"]){ 
				$cursor_atualizado = true;
				break;
			}
			//senao
			$x = $xn; $y = $yn;
			$curva["dataPoints"][] = array("x"=>floatval($x), "y"=>floatval($y));
		}
		$curvas[] = $curva;
		if(!$cursor_atualizado)
			$linha = pg_fetch_assoc($rs);
	}while($linha);
	
	/*
	$curvas = array();
	for($c = 0; $c<2; $c++){
		$curva = array();
		$curva["type"] = "line";
		$curva["dataPoints"] = array();
		for($i = 0; $i<10; $i++){
			$curva["dataPoints"][] = array("x"=>floatval("$i"), "y"=>rand(-10, 20)/3.0);
		}
		$curvas[] = $curva;
	}
	*/
	
	$dados["curvas"] = $curvas;
}
//echo "Dados";
echo json_encode($dados);


?>
