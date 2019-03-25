<?

include_once "../config/funcoes.php";

$pagina = new Pagina("dados_processar.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

include_once "funcoes_dados_processar.php";
include_once "dados_processar_auto.php";

//print_r(getConjuntoCampo(341, array("sigma")));

$integracao = array();
$integracao["acs"] = array();
$integracao["ctd"] = array();
$integracao["hydroscat"] = array();
$integracao["ecobb9"] = array(); 
$integracao["trios"] = array();

$integracao["acs"]["ctd"] = array("tempo_ms", "tempo_ms");								$integracao["ctd"]["acs"] = array("tempo_ms", "tempo_ms");
$integracao["ctd"]["hydroscat"] = array("profundidade_m", "profundidade_m");    		$integracao["hydroscat"]["ctd"] = array("profundidade_m", "profundidade_m");
$integracao["ecobb9"]["ctd"] = array("tempo_ms", "tempo_ms");							$integracao["ctd"]["ecobb9"] = array("tempo_ms", "tempo_ms");
$integracao["ecobb9"]["acs"] = array("tempo_ms", "tempo_ms");							$integracao["acs"]["ecobb9"] = array("tempo_ms", "tempo_ms");
$integracao["ecobb9"]["ecobb9"] = array("tempo_ms", "tempo_ms");
$integracao["ctd"]["trios"] = array("profundidade_m", "profundidade_m");				$integracao["trios"]["ctd"] = array("profundidade_m", "profundidade_m");
$integracao["hydroscat"]["trios"] = array("profundidade_m", "profundidade_m");			$integracao["hydroscat"]["trios"] = array("profundidade_m", "profundidade_m");
$integracao["hydroscat"]["hydroscat"] = array("profundidade_m", "profundidade_m");		$integracao["hydroscat"]["hydroscat"] = array("profundidade_m", "profundidade_m");


$tabulacao = array();
$tabulacao["acs"] = array();
$tabulacao["ctd"] = array();
$tabulacao["hydroscat"] = array();
$tabulacao["ecobb9"] = array();
$tabulacao["trios"] = array();

$tabulacao["acs"]["consulta"] = "d_medidas_acs AS ";
$tabulacao["acs"]["tabela"] = "d_medidas_acs";
$tabulacao["acs"]["linha"] = array(array("tempo_ms"));
$tabulacao["acs"]["coluna"] = array(array("tipo", "comprimento_onda"));
$tabulacao["acs"]["celula"] = array("intensidade");//matriz 3d

$tabulacao["ctd"]["consulta"] = "d_medidas_ctd AS ";
$tabulacao["ctd"]["tabela"] = "d_medidas_ctd";
$tabulacao["ctd"]["linha"] = array(array("tempo_ms"));
$tabulacao["ctd"]["coluna"] = array(array("profundidade_m"), array("pressao_dbar"), array("temperatura_c"), array("condutividade_sm"), array("salinidade_psu"));
$tabulacao["ctd"]["celula"] = null;//próprios valores ddas colunas

$tabulacao["hydroscat"]["consulta"] = "d_medidas_hydroscat AS ";
$tabulacao["hydroscat"]["tabela"] = "d_medidas_hydroscat";
$tabulacao["hydroscat"]["linha"] = array(array("profundidade_m"));
$tabulacao["hydroscat"]["coluna"] = array(array("tipo", "comprimento_onda"));
$tabulacao["hydroscat"]["celula"] = array("intensidade");//matriz 3d

$tabulacao["ecobb9"]["consulta"] = "d_medidas_ecobb9 AS ";
$tabulacao["ecobb9"]["tabela"] = "d_medidas_ecobb9";
$tabulacao["ecobb9"]["linha"] = array(array("tempo_ms"));
$tabulacao["ecobb9"]["coluna"] = array(array("tipo", "comprimento_onda"));
$tabulacao["ecobb9"]["celula"] = array("intensidade");//matriz 3d

$tabulacao["trios"]["consulta"] = "(select * FROM d_medidas_trios_espectro LEFT JOIN d_medidas_trios ON id_medidas_trios_fk = d_medidas_trios.id) AS ";
$tabulacao["trios"]["tabela"] = "trios_tabela";
$tabulacao["trios"]["linha"] = array(array("comprimento_onda"));
$tabulacao["trios"]["coluna"] = array(array("tipo", "profundidade_m"));
$tabulacao["trios"]["celula"] = array("intensidade");//matriz 3d



function verificaIndices(){
    $id_acs = '2';
    $processamento_entrada = "original, comprimento_onda interpolada, tempo_ms interpolada, corrigido por temperatura e salinidade";
    
    trace0("Iniciando verificação");
    
    $rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_acs' ORDER BY id") or die(pg_last_error());
    for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_acs = getAtrQuery("SELECT id FROM d_conjunto WHERE processamento = '$processamento_entrada' AND id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                
                $sql = "select acs1.*, acs1.intensidade as a, acs2.intensidade as c FROM d_medidas_acs as acs1 LEFT JOIN d_medidas_acs as acs2 ON (acs1.tempo_ms = acs2.tempo_ms AND acs1.id_conjunto_fk = acs2.id_conjunto_fk AND acs1.comprimento_onda = acs2.comprimento_onda) WHERE acs1.id_conjunto_fk = '$id_conjunto_acs' AND acs2.tipo='c'";
                $rsACS = pg_query($sql) or die(pg_last_error());
                
                while ($linha = pg_fetch_assoc($rsACS)) {
                    
                    
                        $a = $linha["a"];
                        $c = $linha["c"];
                        
                }
    }
    trace0("Verificação concluída");
} 

if($pagina->getUsuario()->getIdUsuario() == '1'){
    //trace0("Processando");
    //testes
    //pg_query("DELETE FROM d_conjunto WHERE id > 4227") or die(pg_last_error());

    //verificaIndices();
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //criarIndices();
    //removerIndices();

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    //processarvalidar_profundidadeCTD();
    //interpolarProfundidadeCTD(); 

    //interpolaTempoAcsAuto();
    //interpolaAcsAuto();//profundidade
    //corrigeAcsTempSal(); /// processamento de referencia para as outras correções ACS
    //corrigeAcsFlat(); 
    //corrigeAcsKirk(); 
    //corrigeAcsProp(); 

    //interpolaHydroAuto();
    //CalculaKbbHydro_lote();
    //CalculaSigmaHydro_lote();
    //aplicaSigmaHydro_lote();

    //interpolarEcoTempo();
    //calculaBetaCorrEco();
    //calculaBetaPEco();
    //calculaBbpEco();
    //calculaBbEco();

    //exportarDadosHydro();
    
    //campo 135
    //exportarConjuntoPontos(2814);
    
    //campo 506
    //exportarConjuntoPontos(2814, array(1166));
    
}

$res = post("reservatorio");
$ponto = post("ponto");
$campo = post("campo");
$conjunto = post("conjunto");

if($campo > 0){
    $tempoinicio = getAtrQuery("SELECT tempoinicio FROM d_campo WHERE id = '$campo'");
    $id_equip = getAtrQuery("SELECT id_equipamento_fk FROM d_campo WHERE id = '$campo'");
}



//definir o sensor a partir do campo
$qtdmax_integracao = 5;//quantidade máxima de conjuntos que podem ser integrados
$equip = getEquipCampo($campo);
$sensor = getSensorCampo($campo);

//---------------------------------------------------------------
//---------------------------------------------------------------
//---------------------------------------------------------------
//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ BOtões de Ação
if(isset($_POST["exportar"])){
    $conjuntos = array();
    for($i=1; $i<= $qtdmax_integracao; $i++){
	if((int)post("conjunto$i") > 0){
            $conjuntos[] = (int)post("conjunto$i");
        }
    }
    exportarConjunto($conjunto, $conjuntos, true);
}
if(isset($_POST["exportar_all"])){
    $conjuntos = array();
    for($i=1; $i<= $qtdmax_integracao; $i++){
	if((int)post("conjunto$i") > 0){
            $conjuntos[] = (int)post("conjunto$i");
        }
    }
    exportarConjuntoPontos($conjunto, $conjuntos);
}
else if(isset($_POST["interpolar"])){
	$interpolar_intervalo = (float)post("interpolar_intervalo");
	$interpolar_var = post("interpolar_var");
	
	if(($interpolar_intervalo > 0 || isset($_POST["interpolar_compat"]))){
		$tabela = "d_medidas_$sensor";
		$passo = $interpolar_intervalo;
		$referencia = "";
		
		if($equip == 'ACS'){
			if($interpolar_var == "comprimento_onda"){
				$constantes = array("tipo", "tempo_ms");
				$referencia = $interpolar_var;
				$variaveis = array("intensidade");
			}
			else if($interpolar_var == "tempo_ms"){
				$constantes = array("tipo", "comprimento_onda");
				$referencia = $interpolar_var;
				$variaveis = array("intensidade");
			}
			else{
				$pagina->addAdvertencia("I can not interpolate the parameter $interpolar_var");
			}
		}
		else if($equip == 'CTD'){
			$constantes = array();
			if($interpolar_var == "profundidade_m"){
				$referencia = $interpolar_var;
				$variaveis = array("tempo_ms", "pressao_dbar", "temperatura_c", "condutividade_sm", "salinidade_psu");
			}
			else{
				$pagina->addAdvertencia("I can not interpolate the parameter $interpolar_var");
			}
		}
		else if($equip == 'HydroScat'){
                        $constantes = array("tipo", "comprimento_onda");
			if($interpolar_var == "profundidade_m"){
				$referencia = $interpolar_var;
				$variaveis = array("intensidade", "tempo_ms");  
			}
			else{
				$pagina->addAdvertencia("I can not interpolate the parameter $interpolar_vars");
			}
		}
		else if($equip == 'EcoBB9'){
			$constantes = array("tipo", "comprimento_onda");
			if($interpolar_var == "tempo_ms"){
				$referencia = $interpolar_var;
				$variaveis = array("intensidade");  
			}
			else{
				$pagina->addAdvertencia("I can not interpolate the parameter $interpolar_var");
			}
		}
		
		if($referencia != ""){
			$processamento = getAtrQuery("SELECT processamento FROM d_conjunto WHERE id = '$conjunto'");//conjunto de dados a ser utilizado
			$referencias = null;
			if(isset($_POST["interpolar_compat"])){
                                
                            $integrado = "";
				for($i=1; $i<= $qtdmax_integracao; $i++){
                                        $campo_i = (int)post("campo$i");
					$conjunto_i = (int)post("conjunto$i");
					$sensor_i = getSensorCampo($campo_i);
                    $processamento_in = getAtrQuery("SELECT processamento FROM d_conjunto WHERE id = '$conjunto_i'");
					if( $conjunto_i > 0){//integração selecionada
						$integrado .= "($sensor_i-$conjunto_i: $processamento_in)";
                                                
                                                $tabela_i = "d_medidas_$sensor_i";
						
						$referencias = array();
						$sql = "SELECT $interpolar_var FROM $tabela_i WHERE id_conjunto_fk = '$conjunto_i' AND $interpolar_var <> '0' ORDER BY $interpolar_var";
						//echo $sql;
						$rsRefs = pg_query($sql) or die(pg_last_error());
						while($linha = pg_fetch_row($rsRefs)) $referencias[] = $linha[0];
					}
				}
				
				if(count($referencias) > 0)
                                    iniciarInterpolacaoDados($processamento, $campo, $interpolar_intervalo, $interpolar_var, $constantes, $variaveis, $referencias, "$processamento, $interpolar_var interpolada com $integrado");
                                else{//não inseriu conjunto
                                    $pagina->addAdvertencia("Insert a reference set to integrate");
                                }
				
			}
                        else{
                            //print_r($referencias);
                            //trace0("INICIANDO INTERPOLAÇÃO EM DISCO $processamento");
                            iniciarInterpolacaoDados($processamento, $campo, $interpolar_intervalo, $interpolar_var, $constantes, $variaveis, $referencias);
                        }
		}
		
	}
}
else if(isset($_POST["ctd_validar_subida"])){
	if($conjunto > 0){
            validaSegundaDescidaCTD($conjunto);
	}
	else{
		$pagina->addAdvertencia("Select a dataset");
	}
}
else if(isset($_POST["ctd_processar_profundidade"])){
	$constante = post("constante_profundidade");
	if($constante != "" && $constante != 0){
		calculaProfundidadeCTD($conjunto, $constante);
	}
	else{
		$pagina->addAdvertencia("Insert the constant!");
	}
}
else if(isset($_POST["acs_corrigir_temperatura_salinidade"])){
	if(isset($_POST["temperatura_referencia_media"])) $temperatura_referencia = null;
	else $temperatura_referencia = (float)post("temperatura_referencia");
	
	if($equip == "ACS"){
		$id_conjunto_acs = $conjunto;
		$id_conjunto_ctd = null;
		
		//primeiro verificar se algum conjunto do ctd foi selecionado para correção
		$conjuntos = getConjuntoAuxSelecionados($qtdmax_integracao);
		foreach($conjuntos as $c){
			if($c["equipamento"] == "CTD"){
				$id_conjunto_ctd = $c["conjunto"];
			}
		}
		
		corrigeTemperaturaSalinidadeACS($id_conjunto_acs, $id_conjunto_ctd, $temperatura_referencia);
		
	}else{
		$pagina->addAdvertencia("Select a dataset from ACS sensor");
	}
	
}
else if(isset($_POST["acs_corrigir_flat"])){
	$comprimentoonda_referencia = (float)post("comprimentoonda_referencia_flat");
	
	if($equip == "ACS"){
		$id_conjunto_acs = $conjunto;
		
		corrigeFlatACS($id_conjunto_acs, $comprimentoonda_referencia);
		
	}else{
		$pagina->addAdvertencia("Select a dataset from ACS sensor");
	}
	
}
else if(isset($_POST["acs_corrigir_kirk"])){
	$constante_espalhamento = (float)post("constante_espalhamento");
	
	if($equip == "ACS"){
		$id_conjunto_acs = $conjunto;
		
		corrigeKirkACS($id_conjunto_acs, $constante_espalhamento);
		
	}else{
		$pagina->addAdvertencia("Select a dataset from ACS sensor");
	}
	
}
else if(isset($_POST["acs_corrigir_proporcional"])){
	$comprimentoonda_referencia = (float)post("comprimentoonda_referencia_prop");
	
	if($equip == "ACS"){
		$id_conjunto_acs = $conjunto;
		
		corrigePropACS($id_conjunto_acs, $comprimentoonda_referencia);
		
	}else{
		$pagina->addAdvertencia("Select a dataset from ACS sensor");
	}
	
}
else if(isset($_POST["hydro_calcular_kbb"])){
	if($equip == "HydroScat"){
		$id_conjunto_hydro = $conjunto;
		$id_conjunto_acs = null;
		$id_conjunto_ctd = null;
		
		//primeiro verificar se algum conjunto do ctd foi selecionado para correção
		$conjuntos = getConjuntoAuxSelecionados($qtdmax_integracao);
		foreach($conjuntos as $c){
			if($c["equipamento"] == "CTD"){
				$id_conjunto_ctd = $c["conjunto"];
			}
                        if($c["equipamento"] == "ACS"){
				$id_conjunto_acs = $c["conjunto"];
			}
		}
                
                
                
                calculaKbbHydro($id_conjunto_hydro, $id_conjunto_ctd, $id_conjunto_acs);
		
	}else{
		$pagina->addAdvertencia("Select a dataset from Hydroscat sensor");
	}
	
}
else if(isset($_POST["hydro_calcular_sigma"])){
    
        $k1= (float)post("hydro_k1");
        $kexp = array();
        for($i = 0; $i < count($_POST["hydro_kexp_onda"]); $i++){
            $c_onda = $_POST["hydro_kexp_onda"][$i];
            $k = $_POST["hydro_kexp"][$i];
            $kexp[$c_onda] = $k;
        }
	
	if($equip == "HydroScat"){
		$id_conjunto_hydro = $conjunto;
                
                calculaSigmaHydro($id_conjunto_hydro, $k1, $kexp);
	}else{
		$pagina->addAdvertencia("Select a dataset from Hydroscat sensor");
	}
	
}else if(isset($_POST["hydro_aplicar_sigma"])){
    
	if($equip == "HydroScat"){
		$id_conjunto_hydro = $conjunto;
                
                $id_conjunto_hydro_bb = null;
		
		//primeiro verificar se algum conjunto do ctd foi selecionado para correção
		$conjuntos = getConjuntoAuxSelecionados($qtdmax_integracao);
		foreach($conjuntos as $c){
			if($c["equipamento"] == "HydroScat"){
				$id_conjunto_hydro_bb = $c["conjunto"];
			}
		}
                
                
                aplicarSigmaHydro($id_conjunto_hydro, $id_conjunto_hydro_bb);
	}else{
		$pagina->addAdvertencia("Select a dataset from Hydroscat sensor");
	}
	
}else if(isset($_POST["eco_calcular_beta"])){
	if($equip == "EcoBB9"){
		$id_conjunto_eco = $conjunto;
		$id_conjunto_eco_dark= null;
		
		
		
		//primeiro verificar se algum conjunto do ctd foi selecionado para correção
		$conjuntos = getConjuntoAuxSelecionados($qtdmax_integracao);
		foreach($conjuntos as $c){
			if($c["equipamento"] == "EcoBB9"){
				$id_conjunto_eco_dark= $c["conjunto"];
			}
		}
		
		$fs = array();
		//RECUPERAR OS COMPRIMENTOS DE ONDAS NA ORDEM E ASSOCIAR COM A ORDEM DO POST
		$sql = "SELECT distinct(comprimento_onda) as c_onda FROM d_medidas_ecobb9 WHERE id_conjunto_fk = '$id_conjunto_eco' ORDER BY comprimento_onda";
		$rs = pg_query($sql) or die(pg_last_error());
		$fs = array();
		for($i=1; $linha = pg_fetch_assoc($rs); $i++){
			$fs[$linha['c_onda']] = post("fs_ch".$i);
		}
		
		calculaBeta($id_conjunto_eco, $id_conjunto_eco_dark, $fs);
		
	}else{
		$pagina->addAdvertencia("Select a dataset from EcoBB9 sensor");
	}
	
}else if(isset($_POST["eco_calcular_betacorr"])){
	if($equip == "EcoBB9"){
		$id_conjunto_eco = $conjunto;
		$id_conjunto_acs = null;
		
		
		
		//primeiro verificar se algum conjunto do ctd foi selecionado para correção
		$conjuntos = getConjuntoAuxSelecionados($qtdmax_integracao);
		foreach($conjuntos as $c){
			if($c["equipamento"] == "ACS"){
				$id_conjunto_acs = $c["conjunto"];
			}
		}
		
		
		calculaBetaCorr($id_conjunto_eco, $id_conjunto_acs);
		
	}else{
		$pagina->addAdvertencia("Select a dataset from ECO-BB9 sensor");
	}
	
}else if(isset($_POST["eco_calcular_betap"])){
	if($equip == "EcoBB9"){
		$id_conjunto_eco = $conjunto;
		$id_conjunto_acs = null;
		$id_conjunto_ctd = null;
		
		$salinidade = null;
		if(post('usar_salinidade_constante') == '1')
			$salinidade= (float)post("salinidade_betap");
		
		//primeiro verificar se algum conjunto do ctd foi selecionado para correção
		$conjuntos = getConjuntoAuxSelecionados($qtdmax_integracao);
		foreach($conjuntos as $c){
			if($c["equipamento"] == "ACS"){
				$id_conjunto_acs = $c["conjunto"];
			}
			if($c["equipamento"] == "CTD"){
				$id_conjunto_ctd = $c["conjunto"];
			}
		}
		
		
		calculaBetaP($id_conjunto_eco, $id_conjunto_ctd, $salinidade);
		
	}else{
		$pagina->addAdvertencia("Select a dataset from ECOBB9 sensor");
	}
	
}else if(isset($_POST["eco_calcular_bbp"])){
	$X= (float)post("fator_transformacao");
    
        if($equip == "EcoBB9"){
		$id_conjunto_eco = $conjunto;
		
		calculaBbp($id_conjunto_eco, $X);
		
	}else{
		$pagina->addAdvertencia("Select a dataset from ECOBB9 sensor");
	}
	
}else if(isset($_POST["eco_calcular_bb"])){
    
        if($equip == "EcoBB9"){
		$id_conjunto_eco = $conjunto;
		
		calculaBb($id_conjunto_eco);
		
	}else{
		$pagina->addAdvertencia("Select a dataset from ECOBB9 sensor");
	}
	
}
else if(isset($_POST["trios_calc_refl_mobley"])){
	if($equip == "TriOS"){
		$id_conjunto = $conjunto;
		$rho= (float)post("trios_rho_refl_mobley");

		calculaReflectanciaMobley($id_conjunto, $rho);

	}else{
		$pagina->addAdvertencia("Select a dataset from TriOS sensor");
	}

}
else if(isset($_POST["trios_calc_refl_gitelson"])){
	if($equip == "TriOS"){
		$id_conjunto = $conjunto;
		$tau= (float)post("trios_tau_refl_gitelson");
		$n= (float)post("trios_n_refl_gitelson");

		calculaReflectanciaGitelson($id_conjunto, $tau, $n);

	}else{
		$pagina->addAdvertencia("Select a dataset from TriOS sensor");
	}

}

//**********************************************************************
//**********************************************************************
//**********************************************************************
//**********************************************************************
//**********************************************************************
//**********************************************************************
include "../layout/cima.php";

?>

	<a style="text-align: center; display: block"href="../layout/imagens/workflow.png" target="_blank"><img src="../layout/imagens/workflow.png" height="100px"><br/>Click here to see the entire fluxogram of processing</a>


	<div class="blocoOr">
	<h1>Selection of dataset</h1>
	
<form action="dados_processar.php" method="post">
	<table class='tabela_sem_borda'>
		<tr>
			<td><b>Reservoir</b>:</td>
			<td>
					<?$arrayReservatorio = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_reservatorio ORDER BY DESCRICAO"));?>
					<?inputSelect("reservatorio", $arrayReservatorio, null, 1, "onchange=submit()");?>
			</td>
			<td><small><a href='../admin_dados/gerir_reservatorio.php?ACAO=inserir'>Insert new reservoir</a></small></td>
		</tr>
		
		<tr>
			<td><b>Region (point)</b>:</td>
			<td>
					<?$arrayPonto = @getInputFromSQL(pg_query("SELECT d_regiao.id AS ID, CONCAT(d_reservatorio.nome, ': ', d_regiao.nome) AS DESCRICAO FROM d_regiao LEFT JOIN d_reservatorio on id_reservatorio_fk = d_reservatorio.id WHERE d_reservatorio.id = '$res' ORDER BY DESCRICAO"));?>
					<?inputSelect("ponto", $arrayPonto, null, 1, "onchange=submit()");?>
			</td>
			<td><small><a href='../admin_dados/gerir_regiao.php?ACAO=inserir'>insert new point</a></small></td>
		</tr>		
		
		<tr>
			<td><b>Field</b>:</td>
			<td>
					<?$arrayCampos = @getInputFromSQL(pg_query("SELECT d_campo.id AS ID, CONCAT('(', d_campo.id , ') ', tempoinicio, ' - ', d_equipamento.nome) AS DESCRICAO FROM d_campo LEFT JOIN d_equipamento ON id_equipamento_fk = d_equipamento.id WHERE id_regiao_fk = '$ponto' ORDER BY tempoinicio, d_equipamento.nome"));?>
					<?inputSelect("campo", $arrayCampos, null, 1, "onchange=submit()");?>
			</td>
                        <?if($campo > 0){?>
                            <?$nome_arq = getAtrQuery("SELECT nome_arq FROM d_campo LEFT JOIN arquivos ON id_arq_fk = id_arq WHERE id = '$campo'");?>
                            <td>
								<small>
									Raw file: <?=$nome_arq?> <br/>
									<a target='_blank' href='../arquivos/<?=$nome_arq?>' download>Download</a><br/>
									<a target='_blank' href='../arquivos/<?=$nome_arq?>'>Show</a>
								</small>
							</td>
                        <?}?>
		</tr>	
		
		<tr>
			<td><b>Dataset</b>:</td>
			<td>
					<?$arrayProcessado = @getInputFromSQL(pg_query("SELECT id AS ID, CONCAT('[', id, '] ', processamento) AS DESCRICAO FROM d_conjunto WHERE id_campo_fk = '$campo' ORDER BY id"));?>
					<?inputSelect("conjunto", $arrayProcessado, null, 1, "onchange=submit() style='max-width:300px'");?>
			</td>
		</tr>		
		
	</table>

	</div>
	
	
	<div class="blocoOr">
		<h1>Dataset integration</h1>
		
		<table class='tabela_sem_borda'>
			<?for($i = 1; $i<=$qtdmax_integracao; $i++){?>
				<tr><?$id_c = $i;?>
					<td>#<?=$id_c?><b>Dataset</b>:</td>
					<td>
							<?
								$arrayCampos = @getInputFromSQL(pg_query("SELECT d_campo.id AS ID, CONCAT('(', d_campo.id , ') ', tempoinicio, ' - ', d_equipamento.nome) AS DESCRICAO FROM d_campo LEFT JOIN d_equipamento ON id_equipamento_fk = d_equipamento.id WHERE id_regiao_fk = '$ponto' AND tempoinicio = '$tempoinicio' AND id_equipamento_fk <> '$id_equip' ORDER BY tempoinicio, d_equipamento.nome"));
								inputSelect("campo$id_c", $arrayCampos, null, 1, "onchange=submit()");
							
								$arrayProcessado = @getInputFromSQL(pg_query("SELECT id AS ID, CONCAT('[', id,'] ', processamento) AS DESCRICAO FROM d_conjunto WHERE id_campo_fk = '".post("campo$id_c")."' ORDER BY id"));
								inputSelect("conjunto$id_c", $arrayProcessado, null, 1, "onchange=submit()");
							?>
							
					</td>
				</tr>	
			<?}?>
				
			
		</table>
	</div>
	
	
	
	
	
	
	
		<div class="blocoOr">
			<h1 style="text-align: left">Processing the dataset of <?= strtoupper($sensor)?></h1>
			
				<table>
					<tr>
						<th>Process</th>
						<th>Settings</th>
					</tr>
					
					<tr>
						<td><? inputSubmit("Export datasheet", "exportar")?>	</td>
					</tr>
					
					<tr>
						<td><? inputSubmit("Export all points", "exportar_all")?>	</td>
					</tr>
					
					<?if($sensor != "" && $conjunto != ""){?>
					
						<tr>
							<td><? inputSubmit("Interpolate parameter", "interpolar")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>Parameter</td>
												<?
													$arrayKeys = array_keys(pg_fetch_assoc(pg_query("SELECT * FROM d_medidas_$sensor LIMIT 1")));
													foreach($arrayKeys as $campo => $valor) 
														if($valor != "id" && strpos($valor, 'id_') === false ) 
															$arrayVars[$valor] = $valor;
												?>
												<td><?inputSelect("interpolar_var", $arrayVars)?></td>
											</tr>
											<tr>
												<td>Range (Look the unity measurement of selected parameter)</td>
												<td><?inputText("interpolar_intervalo")?></td>
											</tr>
											<tr>
												<td>Turn compatible the parameter from other dataset</td>
												<td><?inputCheckbox("interpolar_compat", "true")?> Turn compatible</td> 
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
					<?}?>
					
					<?if($equip == "ACS" && $conjunto != ""){?>
						<tr>
							<td><? inputSubmit("Temperature and salinity correction", "acs_corrigir_temperatura_salinidade")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>Reference temperature</td>
												<td>
													<?inputText("temperatura_referencia", "22.2", "25")?>
													<br />
													<?inputCheckbox("temperatura_referencia_media", "true")?> Use the temperature average of wavelength 740 nm
												</td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						
						<tr>
							<td><? inputSubmit("Correct absorption (Flat)", "acs_corrigir_flat")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>Reference wavelength</td>
												<td><?inputText("comprimentoonda_referencia_flat", "740", "25")?></td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						
						<tr>
							<td><? inputSubmit("Correct absorption (Kirk)", "acs_corrigir_kirk")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>Proportion of scattering lost by the sensor</td>
												<td><?inputText("constante_espalhamento", "0.18", "25")?></td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						
						<tr>
							<td><? inputSubmit("Correct absorption (Proportional)", "acs_corrigir_proporcional")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>Reference wavelength</td>
												<td><?inputText("comprimentoonda_referencia_prop", "740", "25")?></td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						
					<?}?>
					
					
					<?if($equip == "CTD" && $conjunto != ""){?>
						
						<tr>
							<td><? inputSubmit("Validate first up-travel", "ctd_validar_subida")?>	</td>
							<td>
							</td>
						</tr>
						
						<tr>
							<td><? inputSubmit("Calculate depth", "ctd_processar_profundidade")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>Constant <small>(depth (meters) = pressure (decibars) * CONSTANTE)</small></td>
												<td><?inputText("constante_profundidade", "1.019716")?></td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						
					<?}?>
					
					<?if($equip == "HydroScat" && $conjunto != ""){?>
						<tr>
							<td><? inputSubmit("Calculate Kbb", "hydro_calcular_kbb")?>	</td> 
							<td>
								
							</td>
						</tr>
						
						<tr>
							<td><? inputSubmit("Calculate Sigma Kbb", "hydro_calcular_sigma")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>K1</td>
												<td><?inputText("hydro_k1", "1", "10")?></td>
											</tr>
                                                                                        <tr>
												<td>Kexp 1</td>
												<td>
                                                                                                    <?inputText("hydro_kexp_onda[]", "420", "5")?>
                                                                                                    <?inputText("hydro_kexp[]", "0.143", "5")?>
                                                                                                </td>
											</tr>
                                                                                        <tr>
												<td>Kexp 2</td>
												<td>
                                                                                                    <?inputText("hydro_kexp_onda[]", "510", "5")?>
                                                                                                    <?inputText("hydro_kexp[]", "0.148", "5")?>
                                                                                                </td>
											</tr>
                                                                                        <tr>
												<td>Kexp 3</td>
												<td>
                                                                                                    <?inputText("hydro_kexp_onda[]", "442", "5")?>
                                                                                                    <?inputText("hydro_kexp[]", "0.142", "5")?>
                                                                                                </td>
											</tr>
                                                                                        <tr>
												<td>Kexp 4</td>
												<td>
                                                                                                    <?inputText("hydro_kexp_onda[]", "700", "5")?>
                                                                                                    <?inputText("hydro_kexp[]", "0.149", "5")?>
                                                                                                </td>
											</tr>
                                                                                        <tr>
												<td>Kexp 5</td>
												<td>
                                                                                                    <?inputText("hydro_kexp_onda[]", "470", "5")?>
                                                                                                    <?inputText("hydro_kexp[]", "0.145", "5")?>
                                                                                                </td>
											</tr>
                                                                                        <tr>
												<td>Kexp 6</td>
												<td>
                                                                                                    <?inputText("hydro_kexp_onda[]", "590", "5")?>
                                                                                                    <?inputText("hydro_kexp[]", "0.145", "5")?>
                                                                                                </td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
                                                
                                                <tr>
							<td><? inputSubmit("Apply sigma factor", "hydro_aplicar_sigma")?>	</td> 
							<td>
								
							</td>
						</tr>
                                                
						
					<?}?>
					
					
					<?if($equip == "EcoBB9" && $conjunto != ""){?>
						<tr>
							<td><? inputSubmit("Calculate Beta", "eco_calcular_beta")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>Scale factor (wavelength 1)</td>
												<td><?inputText("fs_ch1", "1.198E-05")?></td>
											</tr>
											<tr>
												<td>Scale factor (wavelength 2)</td>
												<td><?inputText("fs_ch2", "1.250E-05")?></td>
											</tr>
											<tr>
												<td>Scale factor (wavelength 3)</td>
												<td><?inputText("fs_ch3", "1.071E-05")?></td>
											</tr>
											<tr>
												<td>Scale factor (wavelength 4)</td>
												<td><?inputText("fs_ch4", "9.980E-06")?></td>
											</tr>
											<tr>
												<td>Scale factor (wavelength 5)</td>
												<td><?inputText("fs_ch5", "8.092E-06")?></td>
											</tr>
											<tr>
												<td>Scale factor (wavelength 6)</td>
												<td><?inputText("fs_ch6", "3.507E-06")?></td>
											</tr>
											<tr>
												<td>Scale factor (wavelength 7)</td>
												<td><?inputText("fs_ch7", "2.934E-06")?></td>
											</tr>
											<tr>
												<td>Scale factor (wavelength 8)</td>
												<td><?inputText("fs_ch8", "2.772E-06")?></td>
											</tr>
											<tr>
												<td>Scale factor (wavelength 9)</td>
												<td><?inputText("fs_ch9", "2.544E-06")?></td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						
						<tr>
							<td><? inputSubmit("Calculate Beta corr", "eco_calcular_betacorr")?>	</td>
							<td>
								
							</td>
						</tr>
						
						<tr>
							<td><? inputSubmit("Calculate Beta p", "eco_calcular_betap")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>Use salinity constant</td>
												<td><?inputSelect("usar_salinidade_constante", array('0' => 'No', '1' => 'Yes'), "0")?></td>
											</tr>
											<tr>
												<td>Salinity</td>
												<td><?inputText("salinidade_betap", "0.1", "1")?></td>
											</tr>
											
										</table>
									</div>
								</div>
		
							</td>
						</tr>
						
						<tr>
							<td><? inputSubmit("Calculate bbp", "eco_calcular_bbp")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>X - transformation factor</td>
												<td><?inputText("fator_transformacao", "1", "1")?></td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						
						<tr>
							<td><? inputSubmit("Calculate bb", "eco_calcular_bb")?>	</td>
							<td>
								
							</td>
						</tr>
						
					<?}?>
					
					
					
					
					<?if($equip == "TriOS" && $conjunto != ""){?>
						<tr>
							<td><? inputSubmit("Calculate Reflectance - Mobley", "trios_calc_refl_mobley")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>&rho; (reflectance of the interface air-water)</td>
												<td><?inputText("trios_rho_refl_mobley", "0.028", "1")?></td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						
						<tr>
							<td><? inputSubmit("Calculate reflectance - Gitelson", "trios_calc_refl_gitelson")?>	</td>
							<td>
								<div class="box">
									<div class="box_titulo titulo_expansivel">Edit</div>
									<div class="conteudo_expansivel">
										<table>
											<tr>
												<td>&tau; (transmitance air-water)</td>
												<td><?inputText("trios_tau_refl_gitelson", "0,98", "1")?></td>
											</tr>
											<tr>
												<td><i>n</i> (refractive index air-water)</td>
												<td><?inputText("trios_n_refl_gitelson", "1.33", "1")?></td>
											</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
												
					<?}?>

				</table>
			
			
			
		</div>

	</form>
	
<?
include "../layout/baixo.php";

?>
