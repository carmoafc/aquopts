<?
//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################
//acs
function interpolaAcsAuto(){//comprimento_onda
	$id_acs = '2';
	$interpolar_intervalo = 1;
	$interpolar_var = "comprimento_onda";
	$constantes = array("tipo", "tempo_ms"); 
	$variaveis = array("intensidade");
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_acs' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 2*/; $i++){
		$campo = $linha["id"];
		
		iniciarInterpolacaoDados('original, tempo_ms interpolada', $campo, $interpolar_intervalo, $interpolar_var, $constantes, $variaveis);
	}
	gravaLog("Processamento Finalizado");
}
//**********************************************************************
//********************************************************************** calcular profundidade ctd
//**********************************************************************
function processarvalidar_profundidadeCTD(){
	//pg_query("DELETE FROM d_conjunto WHERE id > 424 AND id_conjuntopai_fk is not null");
	
	$constante = 1.019716;
	$id_ctd = '3';
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_ctd' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 3*/; $i++){
		gravaLog("Processando Profundidade CTD. Campo ".$linha["id"]);
		$campo = $linha["id"];
		
		$conjunto = (int)getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$campo' AND processamento = 'original'");
		
		
		//$n_conjunto = validaPrimeiraSubidaCTD($conjunto);
                
                //$n_conjunto = validaPrimeiraDescidaCTD($conjunto);
                //$n_conjunto = validaSegundaSubidaCTD($conjunto);
                $n_conjunto = validaSegundaDescidaCTD($conjunto);
                
                
                
		if($n_conjunto > 0){
                    calculaProfundidadeCTD($n_conjunto, $constante);

                    gravaLog("Novo conjunto CTD criado: ".$n_conjunto);
                }
                else{
                    gravaLog("Nenhum conjunto criado");
                }
		
	}
}

function interpolarProfundidadeCTD(){
	$id_sensor = '3';
	$interpolar_intervalo = 0.1;
	
	$constantes = array();
	$interpolar_var = "profundidade_m";
	$variaveis = array("tempo_ms", "pressao_dbar", "temperatura_c", "condutividade_sm", "salinidade_psu");
	
	
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_sensor' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 3*/; $i++){
		$campo = $linha["id"];
		 
		iniciarInterpolacaoDados('original, Segunda Descida, profundidade calculada', $campo, $interpolar_intervalo, $interpolar_var, $constantes, $variaveis);
	}
	gravaLog("Processamento Finalizado");
	
}

//Hydroscat
function interpolaHydroAuto(){
	$id_hydro = '5';
	$interpolar_intervalo = 0.1;
	$interpolar_var = "profundidade_m";
	$constantes = array("tipo", "comprimento_onda");
	$variaveis = array("intensidade", "tempo_ms");
		
	
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_hydro' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 3*/; $i++){
		$campo = $linha["id"];
		
		iniciarInterpolacaoDados('original', $campo, $interpolar_intervalo, $interpolar_var, $constantes, $variaveis);
	}
	gravaLog("Processamento Finalizado");
}

//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################
//#######################################################################
function interpolaTempoAcsAuto(){
	$id_acs = '2';
	$interpolar_var = "tempo_ms";
	$constantes = array("tipo", "comprimento_onda"); 
	$variaveis = array("intensidade");
        
        $proc_acs_in = "original";
        //$proc_ctd_in = 'original, Primeira subida, profundidade calculada, profundidade_m interpolada';
        $proc_ctd_in = 'original, Primeira Descida, profundidade calculada, profundidade_m interpolada';
        //$proc_ctd_in = 'original, Segunda Subida, profundidade calculada, profundidade_m interpolada';
        //$proc_ctd_in = 'original, Segunda Descida, profundidade calculada, profundidade_m interpolada';
        
        $proc_acs_out = "$proc_acs_in, $interpolar_var interpolada com ($proc_ctd_in)";
	
        gravaLog("Processamento ACS Iniciado. Interpolação por tempo");
	
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_acs' AND id = '203' ORDER BY id") or die(pg_last_error());
        $total = pg_num_rows($rsCampos);
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 3*/; $i++){
                $campo = $linha["id"];
                trace0("<br/>Processamento $i de $total para campo $campo");
		
                $campo_ctd = getCampoEquivalente($linha["tempoinicio"], $linha["id_regiao_fk"], "CTD");
                $conjunto_ctd = getAtrsQuery("SELECT * FROM d_conjunto WHERE id_campo_fk = '".$campo_ctd["id"]."' AND processamento = '$proc_ctd_in' ORDER BY data DESC LIMIT 1");
                if($conjunto_ctd["id"] > 0){
                    trace0("Referecnia CTD: COnjunto ". $conjunto_ctd["id"]);
                    $referencias = array();
                    $sql = "SELECT $interpolar_var FROM d_medidas_ctd WHERE id_conjunto_fk = '".$conjunto_ctd["id"]."' AND $interpolar_var <> '0' ORDER BY $interpolar_var";
                    //echo $sql;
                    $rsRefs = pg_query($sql) or die(pg_last_error());
                    while($linha = pg_fetch_row($rsRefs)) $referencias[] = $linha[0];

                    if(count($referencias) > 0)
                        iniciarInterpolacaoDados($proc_acs_in, $campo, null, $interpolar_var, $constantes, $variaveis, $referencias, $proc_acs_out);
                    else
                        trace0("$$$$$$$$$$$$$$$$$$$$$$$$$$#Não encontrado CTD de referência para campo ACS $campo");
                }else{
                    trace0("[##########] Campo CTD não existe");
                }
	}
	trace0("Processamento ACS Finalizado. Interpolação por tempo");
}
function corrigeAcsTempSal(){
        $id_acs = '2';
	$temperatura_referencia = "22.2";
        
        gravaLog("Processamento em lote ACS Iniciado. Correção Temperatura ACS");
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_acs' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 3*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_acs = getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                $id_conjunto_ctd = null;
                
               corrigeTemperaturaSalinidadeACS($id_conjunto_acs, $id_conjunto_ctd, $temperatura_referencia);
	}
	gravaLog("Processamento em lote ACS Finalizado. Correção Temperatura ACS");
}
function corrigeAcsFlat(){
        $id_acs = '2';
	$comprimentoonda_referencia = "740";
        $processamento_entrada = "original, tempo_ms interpolada, comprimento_onda interpolada, corrigido por temperatura e salinidade";
        
        gravaLog("Processamento em lote ACS Iniciado. Correção por Flat");
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_acs' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_acs = getAtrQuery("SELECT id FROM d_conjunto WHERE processamento = '$processamento_entrada' AND id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                
               corrigeFlatACS($id_conjunto_acs, $comprimentoonda_referencia);
	}
	gravaLog("Processamento em lote ACS Finalizado. Correção por Flat");
}
function corrigeAcsKirk(){
        $id_acs = '2';
	$constante_espalhamento = "0.18";
        $processamento_entrada = "original, tempo_ms interpolada, comprimento_onda interpolada, corrigido por temperatura e salinidade";
        
        gravaLog("Processamento em lote ACS Iniciado. Correção por Kirk");
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_acs' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_acs = getAtrQuery("SELECT id FROM d_conjunto WHERE processamento = '$processamento_entrada' AND id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                
               corrigeKirkACS($id_conjunto_acs, $constante_espalhamento);
	}
	gravaLog("Processamento em lote ACS Finalizado. Correção por Kirk");
}

function corrigeAcsProp(){
        $id_acs = '2';
	$comprimentoonda_referencia = "740";
        $processamento_entrada = "original, tempo_ms interpolada, comprimento_onda interpolada, corrigido por temperatura e salinidade";
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_acs' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_acs = getAtrQuery("SELECT id FROM d_conjunto WHERE processamento = '$processamento_entrada' AND id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                
               corrigePropACS($id_conjunto_acs, $comprimentoonda_referencia);
	}
	gravaLog("Processamento em lote ACS Finalizado. Correção por Prop");
}

function CalculaKbbHydro_lote(){
        $id_hydro = '5';
        
        gravaLog("Processamento em lote Hydro Iniciado. Calculo do Kbb");
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_hydro' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_hydro = getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                $id_conjunto_acs = null;
		$id_conjunto_ctd = null;
		
                
               calculaKbbHydro($id_conjunto_hydro, $id_conjunto_ctd, $id_conjunto_acs);
	}
	gravaLog("Processamento em lote Hydro Finalizado. Calculo do Kbb");
}

function CalculaSigmaHydro_lote(){
        $id_hydro = '5';
        
        gravaLog("Processamento em lote Hydro Iniciado. Calculo do Sigma");
        
        $k1= 1;
        $kexp = array();
        $kexp["420"] = 0.143;
        $kexp["510"] = 0.148;
        $kexp["442"] = 0.142;
        $kexp["700"] = 0.149;
        $kexp["470"] = 0.145;
        $kexp["590"] = 0.145;
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_hydro' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_hydro = getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                
               calculaSigmaHydro($id_conjunto_hydro, $k1, $kexp);
	}
	gravaLog("Processamento em lote Hydro Finalizado. Calculo do Sigma");
}

function aplicaSigmaHydro_lote(){
        $id_hydro = '5';
        
        gravaLog("Processamento em lote Hydro Iniciado. Aplicação do Sigma");
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_hydro' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_hydro = getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                $id_conjunto_hydro_bb = null;
                
               aplicarSigmaHydro($id_conjunto_hydro, $id_conjunto_hydro_bb);
	}
	gravaLog("Processamento em lote Hydro Finalizado. Calculo do Sigma");
}


function interpolarEcoTempo(){
	$id_eco = '4';
                
	$interpolar_var = "tempo_ms";
	$constantes = array("tipo", "comprimento_onda");
	$variaveis = array("intensidade");
	
        gravaLog("Processamento ECO Iniciado. Interpolação por tempo");
	
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_eco' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 3*/; $i++){
		$campo = $linha["id"];
		
                $campo_acs = getCampoEquivalente($linha["tempoinicio"], $linha["id_regiao_fk"], "ACS");
                $conjunto_acs = getAtrsQuery("SELECT * FROM d_conjunto WHERE id_campo_fk = '".$campo_acs["id"]."' ORDER BY data DESC LIMIT 1");
                gravaLog("Referencia ACS: COnjunto ". $conjunto_acs["id"]);
                $referencias = array();
                $sql = "SELECT $interpolar_var FROM d_medidas_acs WHERE id_conjunto_fk = '".$conjunto_acs["id"]."' AND $interpolar_var <> '0' GROUP BY $interpolar_var ORDER BY $interpolar_var";
                //echo $sql;
                $rsRefs = pg_query($sql) or die(pg_last_error());
                while($linha = pg_fetch_row($rsRefs)) $referencias[] = $linha[0];
                
                if(count($referencias) > 0)
                    iniciarInterpolacaoDados("original", $campo, null, $interpolar_var, $constantes, $variaveis, $referencias);
                else
                    gravaLog("Não encontrado ACS de referência para campo ECO $campo");
	}
	gravaLog("Processamento ECO Finalizado. Interpolação por tempo");
}


function calculaBetaCorrEco(){
	$id_eco = '4';
        
        gravaLog("Processamento em lote Eco iniciado. Calculo do BetaCorr");
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_eco' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_eco = getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                $id_conjunto_acs = null;
                
                calculaBetaCorr($id_conjunto_eco, $id_conjunto_acs);
	}
	gravaLog("Processamento em lote Eco Finalizado. Calculo do BetaCorr");
}

function calculaBetaPEco(){
	$id_eco = '4';
        
        gravaLog("Processamento em lote Eco iniciado. Calculo do BetaP");
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_eco' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_eco = getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                $id_conjunto_ctd = null;
                
                calculaBetaP($id_conjunto_eco, $id_conjunto_ctd);
	}
	gravaLog("Processamento em lote Eco Finalizado. Calculo do BetaP");
}


function calculaBbpEco(){
	$id_eco = '4';
        $X = 1;
        
        gravaLog("Processamento em lote Eco iniciado. Calculo do Bbp com X = $X");
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_eco' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_eco = getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                
                calculaBbp($id_conjunto_eco, $X);
	}
	gravaLog("Processamento em lote Eco Finalizado. Calculo do Bbp com X = $X");
}


function calculaBbEco(){
	$id_eco = '4';
        
        gravaLog("Processamento em lote Eco iniciado. Calculo do Bb");
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_eco' ORDER BY id") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
		$id_conjunto_eco = getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$campo' ORDER BY data DESC LIMIT 1");
                
                calculaBb($id_conjunto_eco);
	}
	gravaLog("Processamento em lote Eco Finalizado. Calculo do Bb");
}

function exportarDadosHydro(){
    global $pagina;
    $id_hydro = '5';
        
        //gravaLog("Processamento em lote Hydro Iniciado. Exportação dos dados");
        
        array_map('unlink', glob("/tmp/*.csv"));
        array_map('unlink', glob("/tmp/*.xls"));
        
	$rsCampos = pg_query("SELECT * FROM d_campo WHERE id_equipamento_fk = '$id_hydro' ORDER BY id LIMIT 3") or die(pg_last_error());
	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$campo = $linha["id"];
                $conjunto = getConjuntoCampo($campo, array("Bbb_cal"));
				$id_conjunto = $conjunto["id"];
                
               exportarConjunto($id_conjunto, null, false);
	}
        
        /*
        $command = escapeshellcmd('/var/www/html/sensors/config/bibliotecas_python/juntar_csv_xls.py');
        $output = shell_exec("python " . $command. " 2>&1");
        trace0($output);
        */
          
        $command = escapeshellcmd('/var/www/html/sensors/config/bibliotecas_python/juntar_csv_xls.py');
        exec("python " . $command. " 2>&1", $output);
        //trace0($output);
        //print_r($output);
        $arq_out = $output[count($output) - 1];
        //trace0("Salvo em $arq_out");
        
        print_r(exec("cp $arq_out /var/www/html/sensors/tmp/ 2>&1"));
        
        $pagina->addAdvertencia("Download <a href='../tmp/'>aqui</a>");
        
	//gravaLog("Processamento em lote Hydro Finalizado. Calculo do Sigma");
        
        //die();
}


?>
