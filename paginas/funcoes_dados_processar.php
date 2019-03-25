<?

include_once "../config/funcoes.php";

function integraTabs(&$uni_tab, $tab2, $chave) {
    if (count($uni_tab) == 0) {//nenhuma tabela agregada ainda
        $uni_tab = $tab2;
        echo "PRIMEIRA";
    } else {
        $key1 = $chave[0];
        $key2 = $chave[1];
        if ($key1 == $key2) {
            //$uni_tab[$key1] = array_merge
        }
    }

    return $uni_tab;
}

function criarIndices() {
    pg_query("BEGIN; SET statement_timeout TO 1800000") or die("543Could not start transaction\n" . pg_last_error());


    //CTD
    gravaLog("Creating index tempo_ms em CTD");
    pg_query("CREATE INDEX ind_ctd_tempo_ms ON d_medidas_ctd USING btree (tempo_ms)") or die(pg_last_error());
    gravaLog("Creating index id_conjunto_fk em CTD");
    pg_query("CREATE INDEX ind_ctd_id_conjunto_fk ON d_medidas_ctd USING btree (id_conjunto_fk)") or die(pg_last_error());

    //ACS
    gravaLog("Creating index tempo_ms em ACS");
    pg_query("CREATE INDEX ind_acs_tempo_ms ON d_medidas_acs USING btree (tempo_ms)") or die(pg_last_error());
    gravaLog("Creating index id_conjunto_fk em ACS");
    pg_query("CREATE INDEX ind_acs_id_conjunto_fk ON d_medidas_acs USING btree (id_conjunto_fk)") or die(pg_last_error());
    gravaLog("Creating index tipo em ACS");
    pg_query("CREATE INDEX ind_acs_tipo ON d_medidas_acs USING btree (tipo)") or die(pg_last_error());

    //HYDROSCAT
    gravaLog("Creating index profundidade_m em Hydro");
    pg_query("CREATE INDEX profundidade_m ON d_medidas_hydroscat USING btree (profundidade_m)") or die(pg_last_error());
    gravaLog("Creating index id_conjunto_fk em Hydro");
    pg_query("CREATE INDEX ind_hydro_id_conjunto_fk ON d_medidas_hydroscat USING btree (id_conjunto_fk)") or die(pg_last_error());
    gravaLog("Creating index tipo em Hydro");
    pg_query("CREATE INDEX ind_hydro_tipo ON d_medidas_hydroscat USING btree (tipo)") or die(pg_last_error());
    //ECO
    gravaLog("Creating index tempo_ms em Eco");
    pg_query("CREATE INDEX ind_eco_tempo_ms ON d_medidas_ecobb9 USING btree (tempo_ms)") or die(pg_last_error());
    gravaLog("Creating index id_conjunto_fk em Eco");
    pg_query("CREATE INDEX ind_eco_id_conjunto_fk ON d_medidas_ecobb9 USING btree (id_conjunto_fk)") or die(pg_last_error());
    gravaLog("Creating index tipo em Eco");
    pg_query("CREATE INDEX ind_eco_tipo ON d_medidas_ecobb9 USING btree (tipo)") or die(pg_last_error());
    
    
    
    pg_query("COMMIT") or die("1432Could not start transaction\n" . pg_last_error());
}

function removerIndices() {
    pg_query("BEGIN") or die("543Could not start transaction\n" . pg_last_error());
    pg_query("SET statement_timeout TO 1800000") or die(pg_last_error());

    //CTD
    gravaLog("Removing index tempo_ms em CTD");
    pg_query("DROP INDEX IF EXISTS ind_ctd_tempo_ms") or die(pg_last_error());
    
    //CTD
    gravaLog("Removing index tempo_ms em CTD");
    pg_query("DROP INDEX IF EXISTS ind_ctd_tempo_ms") or die(pg_last_error());
    gravaLog("Removing index id_conjunto_fk em CTD");
    pg_query("DROP INDEX IF EXISTS ind_ctd_id_conjunto_fk") or die(pg_last_error());

    //ACS
    gravaLog("Removing index tempo_ms em ACS");
    pg_query("DROP INDEX IF EXISTS ind_acs_tempo_ms") or die(pg_last_error());
    gravaLog("Removing index id_conjunto_fk em ACS");
    pg_query("DROP INDEX IF EXISTS ind_acs_id_conjunto_fk") or die(pg_last_error());
    gravaLog("Removing index tipo em ACS");
    pg_query("DROP INDEX IF EXISTS ind_acs_tipo ") or die(pg_last_error());

    //HYDROSCAT
    gravaLog("Removing index profundidade_m em Hydro");
    pg_query("DROP INDEX IF EXISTS profundidade_m") or die(pg_last_error());
    gravaLog("Removing index id_conjunto_fk em Hydro");
    pg_query("DROP INDEX IF EXISTS ind_hydro_id_conjunto_fk") or die(pg_last_error());
    gravaLog("Removing index tipo em Hydro");
    pg_query("DROP INDEX IF EXISTS ind_hydro_tipo") or die(pg_last_error());
    //ECO
    gravaLog("Removing index tempo_ms em Eco");
    pg_query("DROP INDEX IF EXISTS ind_eco_tempo_ms") or die(pg_last_error());
    gravaLog("Removing index id_conjunto_fk em Eco");
    pg_query("DROP INDEX IF EXISTS ind_eco_id_conjunto_fk") or die(pg_last_error());
    gravaLog("Removing index tipo em Eco");
    pg_query("DROP INDEX IF EXISTS ind_eco_tipo") or die(pg_last_error());
    

    pg_query("COMMIT") or die("1432Could not start transaction\n" . pg_last_error());
}

function getEquipCampo($campo) {
    $equip = "";
    if ($campo > 0) {
        $equip = getAtrsQuery("SELECT * FROM d_campo LEFT JOIN d_equipamento ON id_equipamento_fk = d_equipamento.id WHERE d_campo.id = '$campo'");
        while($equip["id_equipamentopai_fk"] > 0){
        	$equip = getAtrsQuery("SELECT * FROM d_equipamento WHERE id = '".$equip["id_equipamentopai_fk"]."'");
        }
    }
    return @$equip["nome"];
}

function getSensorEquip($equip) {
	//trace("Equi: ".$equip);
	if ($equip != "") {
		if ($equip == 'ACS')
			$sensor = 'acs';
			else if ($equip == 'CTD')
				$sensor = 'ctd';
				else if ($equip == 'HydroScat')
					$sensor = 'hydroscat';
					else if ($equip == 'EcoBB9')
						$sensor = 'ecobb9';
						else if (strpos($equip, 'TriOS') !== false)
							$sensor = 'trios';
							else if (strpos($equip, 'Sonda') !== false)
								$sensor = 'limnologicas';
	}
	return $sensor;
}
function getSensorCampo($campo) {
	$sensor = "";
	if ($campo > 0) {
		$equip = getAtrQuery("SELECT d_equipamento.nome FROM d_campo LEFT JOIN d_equipamento ON id_equipamento_fk = d_equipamento.id WHERE d_campo.id = '$campo'");
		return getSensorEquip($equip);
	}
	return $sensor;
}

function getSensorFromId($id_sensor) {
	$sensor = "";
	if ($id_sensor > 0) {
		$equip = getAtrQuery("SELECT d_equipamento.nome FROM d_equipamento WHERE id = '$id_sensor'");
		return getSensorEquip($equip);
	}
	return $sensor;
}

function str_lreplace($search, $replace, $subject) {
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

function implode2D($sep, $vec) {
    $str = "";
    if ($vec != null) {
        foreach ($vec as $v) {
            if (is_array($v)) {
                foreach ($v as $var) {
                    $str .= $var . $sep;
                }
            } else {
                $str .= $v . $sep;
            }
        }
        $str = str_lreplace($sep, "", $str);
    }
    return $str;
}

function trace0($str) {
    echo "<br />$str";
    flush();
    ob_flush();
    
    echo " ";
    flush();
    ob_flush();
}

function gravaLog($msg) {

    pg_query("INSERT INTO log_indice (msg) VALUES ('$msg')") or die(pg_last_error());

    trace0($msg);
}

function unsetArray(&$dados, $index){
    //trace0("Remover até $index");
    foreach($dados as $c => $v){
        if($c == $index) 
            return;
        else{ 
            //trace0("Removendo $c: ".$dados[$c]["tempo_ms"]);
            unset($dados[$c]);
        }
    }
}

//dados deve estar ordenado crescentemente por $referencia
//referencia é uma string que indica qual variável é referenencia para interpolação (variavel a ser interpolada)
//variaveis deve conter todas as variaveis a serem interpoladas, exceto referencia
//dados é um vetor de medidas, cada medida é uma vetor associativo
//salto é o salto para interpolação
function interpolarDados_old($referencia, $variaveis, $constantes, &$dados, $salto, $referencias = null) {
	$quant = count($dados);
	trace0("Size fo the dataset to interpolate i: ".$quant);
	//trace0("ERRADO, PEGAR DO VETOR. Intervalo do conjunto amostrado de $referencia para interpolar: [".$dados[0][$referencia].", ".$dados[count($dados)-1][$referencia]."]");
	trace0("Size of reference dataset: ".count($referencias));
	trace0("Range of the reference dataset: [".$referencias[0].", ".$referencias[count($referencias)-1]."]");
	
	
	$i = 0;
	$novos_dados = array();
	//trace0("Iniciando interpolação");
	//print_r($referencias);
	
	while ($i < $quant) {
		//trace0("@@@@Partindo i de $i");
		if ($referencias == null)
			$inicio = (ceil($dados[$i][$referencia]/$salto))*$salto;
			else
				$inicio = $referencias[0];
				
				if ($referencias == null) {
					for ($j = $i+1; $j < $quant && $dados[$j - 1][$referencia] <= $dados[$j][$referencia]; $j++) ;//melhorar, desnecessario
					$fim = (floor($dados[$j - 1][$referencia]/$salto))*$salto;
				} else
					$fim = $referencias[count($referencias) - 1];
					//        echo "Iniciando um em $i<br />";
					trace0("Starting the range interpolation in [$inicio, $fim] with step $salto");
					$s = 0;
					
					//trace0("descartando inicio inútil");
					while ($i < $quant && $dados[$i][$referencia] < $inicio){ trace0("NUNCA ENTRAR--Descarta ".$dados[$i][$referencia]); $i++;}
					if ($i >= $quant){ return $novos_dados;}
					//trace0("Iniciando Bloco de interpolação na posição $i");
					//        trace0("Partindo de i $i com: ".  implode(', ', $dados[$i]));
					//trace0("Partindo de i-1 com: ".  implode(', ', $dados[$i-1]));
					//trace0("Partindo de i+1 com: ".  implode(', ', $dados[$i+1]));
					
					for ($ref = $inicio; $ref <= $fim; $ref += $salto) {
						//trace0("proxima ref $ref entre [$inicio, $fim]");
						//$out = "$ref";
						//if($i > 0) $i--;//se já percorreu
						
						
						
						//trace0("Próximo Salto definido como ".$salto. " para a referencia $s");
						//trace0("Interpolando para valor alvo de referencia $ref. Buscando o próximo valor menor que a referenciaa partir de [$i] = ".$dados[$i][$referencia]);
						while ($i < $quant && $dados[$i][$referencia] > $ref){ /*trace0("Descarta ".$dados[$i][$referencia]);*/ $i++;}
						if ($i >= $quant){ return $novos_dados;} //não da pra interpolar com $ref (fora do intervalo inicial)
						//trace0("[[[[[[[[Valor menor encontrado na posicao i $i com o valor amostra ".$dados[$i][$referencia]);
						
						//trace0("Buscando proximo valor amostral maior que a referencia a partir de $i");
						$j=0;
						while ($i < $quant && $dados[$i][$referencia] < $ref && (@$dados[$i+1][$referencia] > $dados[$i][$referencia])){ /*trace0("Descarta ".$dados[$i][$referencia]);*/ $j++;$i++;}//travando aqui, percorrendo até o fim
						//trace0("Descartou $j linhas");
						if ($i >= $quant){ return $novos_dados;}
						//trace0("]]]]]]]]]Valor maior encontrado na posição $i para o valor amostrado ".$dados[$i][$referencia]);
						//trace0($i);
						//echo "Processando $i para $ref<br />";
						//certeza que $ref está entre [$i-1, $i]
						//trace0("#######Referencia $ref está entre i [".($i-1).", $i] ou os valores [".($dados[$i-1][$referencia]).", ".$dados[$i][$referencia]."]");
						//if(($dados[$i][$referencia] - $dados[$i - 1][$referencia]) == 0) trace0 ("Erro divisão por zero, resultado: ".$dados[$i][$referencia]."     ". $dados[$i-1][$referencia]);
						
						//trace0("Antes: ".  sizeof($dados));
						//if($i>10) unsetArray($dados, $i-10);
						//trace0("Depois: ".sizeof($dados));
						trace("Processing i $i from $quant");
						trace("Valor REF a ser calculado ".$ref);
						$prop = ($ref - $dados[$i - 1][$referencia]) / ($dados[$i][$referencia] - $dados[$i - 1][$referencia]); //-1
						trace0("Factor calculated: $prop");
						$novo_dado = array();
						$novo_dado[$referencia] = $ref;
						foreach ($constantes as $cons) {
							$novo_dado[$cons] = $dados[$i][$cons];
							//$out .= ", $cons: ".$novo_dado[$cons];
							//trace0("interpolando cons $cons: ".$novo_dado[$cons]);
						}
						foreach ($variaveis as $var) {
							$novo_dado[$var] = $dados[$i - 1][$var] + ($prop * ($dados[$i][$var] - $dados[$i - 1][$var])); //-1
							//trace0("interpolando var $var: ".$novo_dado[$var]);
							//$out .= ", $var: ".$novo_dado[$var];
						}
						//trace0($out);
						//trace0(implode(',', $novo_dado));
						$novos_dados[] = $novo_dado;
						
						
						if ($referencias != null) {
							if($s + 1 < count($referencias))
								$salto = $referencias[$s + 1] - $referencias[$s];
								else
									break;
									$s++;
						}
						
					}
					//echo "Acabou um em $i<br />";
					//trace0("descartando final inútil em $i");
					while ($i < $quant && $dados[$i][$referencia] >= $fim) $i++;
					//trace0("dados descartados. Reiniciando processo em $i");
	}
	//trace0("Não descartou");
	
	//$uni_tab = construirMatriz(array("tempo_ms"), array("tipo", "comprimento_onda"), array("intensidade"), $novos_dados);
	//printTable($uni_tab);
	//trace0("Primeiro a sumir: ".  implode(",", $novos_dados[100000]));
	
	return $novos_dados;
}


//dados deve estar ordenado crescentemente por $referencia
//referencia é uma string que indica qual variável é referenencia para interpolação (variavel a ser interpolada)
//variaveis deve conter todas as variaveis a serem interpoladas, exceto referencia
//dados é um vetor de medidas, cada medida é uma vetor associativo
//salto é o salto para interpolação
function interpolarDados($referencia, $variaveis, $constantes, &$dados, $salto, $referencias = null) {
	$quant = count($dados);
	trace0("Size fo the dataset to interpolate i: ".$quant);
	//trace0("ERRADO, PEGAR DO VETOR. Intervalo do conjunto amostrado de $referencia para interpolar: [".$dados[0][$referencia].", ".$dados[count($dados)-1][$referencia]."]");
	trace0("Size of reference dataset: ".count($referencias));
	trace0("Range of the reference dataset: [".$referencias[0].", ".$referencias[count($referencias)-1]."]");
	
	$i = 0;
	$novos_dados = array();
	//trace0("Iniciando interpolação");
	//print_r($referencias);
	
	while ($i < $quant) {
		//trace0("@@@@Partindo i de $i");
		if ($referencias == null)
			$inicio = (ceil($dados[$i][$referencia]/$salto))*$salto;
			else
				$inicio = $referencias[0];
				
				if ($referencias == null) {
					for ($j = $i+1; $j < $quant && $dados[$j - 1][$referencia] <= $dados[$j][$referencia]; $j++) ;//melhorar, desnecessario
					$fim = (floor($dados[$j - 1][$referencia]/$salto))*$salto;
				} else
					$fim = $referencias[count($referencias) - 1];
					//        echo "Iniciando um em $i<br />";
					//trace0("iniciando intervalo de interpolacao em [$inicio, $fim] COM SALTO $salto");
					$s = 0;
					
					//trace0("descartando inicio inútil");
					while ($i < $quant && $dados[$i][$referencia] < $inicio){ /*trace0("NUNCA ENTRAR--Descarta ".$dados[$i][$referencia]);*/ $i++;}
					if ($i >= $quant){ return $novos_dados;}
					//trace0("Iniciando Bloco de interpolação na posição $i");
					//        trace0("Partindo de i $i com: ".  implode(', ', $dados[$i]));
					//trace0("Partindo de i-1 com: ".  implode(', ', $dados[$i-1]));
					//trace0("Partindo de i+1 com: ".  implode(', ', $dados[$i+1]));
					
					for ($ref = $inicio; $ref <= $fim; $ref += $salto) {
						//trace("Processando i $i de $quant");
						//trace("Valor REF a ser calculado ".$ref);
						while ($i < $quant && $ref > $dados[$i][$referencia]){ /*trace0("Descarta i $i: ".$dados[$i][$referencia]);*/ $i++;}
						if ($i >= $quant){ return $novos_dados;} //não da pra interpolar com $ref (fora do intervalo inicial)
						
						//Calcula a interpolação de ref
						
						
						$prop = ($ref - $dados[$i - 1][$referencia]) / ($dados[$i][$referencia] - $dados[$i - 1][$referencia]); //-1
						//trace0("Fator encontrado: $prop");
						$novo_dado = array();
						$novo_dado[$referencia] = $ref;
						foreach ($constantes as $cons) {
							$novo_dado[$cons] = $dados[$i][$cons];
							//$out .= ", $cons: ".$novo_dado[$cons];
							//trace0("interpolando cons $cons: ".$novo_dado[$cons]);
						}
						foreach ($variaveis as $var) {
							$novo_dado[$var] = $dados[$i - 1][$var] + ($prop * ($dados[$i][$var] - $dados[$i - 1][$var])); //-1
							//trace0("interpolando var $var: ".$novo_dado[$var]);
							//$out .= ", $var: ".$novo_dado[$var];
						}
						//trace0($out);
						//trace0(implode(',', $novo_dado));
						$novos_dados[] = $novo_dado;
						
						
						if ($referencias != null) {
							if($s + 1 < count($referencias))
								$salto = $referencias[$s + 1] - $referencias[$s];
							else
								break;
							$s++;
						}	
						
					}
					//trace("Acabou um em $i");
					//trace0("descartando final inútil em $i");
					while ($i < $quant && $dados[$i][$referencia] >= $fim) $i++;
					//trace0("dados descartados. Reiniciando processo em $i");
	}
	//trace0("Não descartou");
	
	//$uni_tab = construirMatriz(array("tempo_ms"), array("tipo", "comprimento_onda"), array("intensidade"), $novos_dados);
	//printTable($uni_tab);
	//trace0("Primeiro a sumir: ".  implode(",", $novos_dados[100000]));
	
	return $novos_dados;
}


function iniciarInterpolacaoDados($processamento, $campo, $interpolar_intervalo, $interpolar_var, $constantes, $variaveis, $referencias = null, $n_processamento = null) {
    if($n_processamento == null) $n_processamento = "$processamento, $interpolar_var interpolada";
    $sensor = getAtrQuery("SELECT nome FROM d_equipamento LEFT JOIN d_campo ON id_equipamento_fk = d_equipamento.id WHERE d_campo.id = '$campo'");

    trace0("Interpolating $sensor using $interpolar_var from field " . $campo);

//gravaLog("Interpolando");

    $conjunto = (int) getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$campo' AND processamento = '$processamento' ORDER BY id DESC");
    $tabela = "d_medidas_$sensor";
    $passo = $interpolar_intervalo;
    $referencia = $interpolar_var;

    $select_var = array_merge($constantes, array($referencia), $variaveis);
    $order_var = array_merge($constantes, array($referencia));


    trace("dataset: $conjunto");
    trace("table: $tabela");
    trace("step: $passo");
    trace("reference: $referencia");
    trace("references: $referencias");
    
print_r($variaveis);trace("");
print_r($constantes);trace("");
print_r($select_var);trace("");
print_r($order_var);trace("");

$qtd_linhas = getAtrQuery("SELECT count(*) FROM $tabela WHERE id_conjunto_fk = '$conjunto' ");
//$tam_pag_min = getAtrQuery("SELECT COUNT(DISTINCT(" . implode(', ', $order_var) . ")) FROM $tabela WHERE id_conjunto_fk = '$conjunto'");


//while($l = pg_fetch_row($tam_pag_min)) trace($l[0]);

$tam_pag = 100000;
trace("Size of tuples: ".$qtd_linhas);
trace("Size on the page: ".$tam_pag);
//trace("Quantidade agrupamento minimo: ".$tam_pag_min);

$id_conjunto = criaNovoConjunto($campo, $n_processamento, $conjunto);

for($pag = 0; $pag * $tam_pag < $qtd_linhas; $pag++){
	trace("<br/>Recovering page: ".$pag);
    $sql = "SELECT " . implode(', ', $select_var) . " FROM $tabela WHERE id_conjunto_fk = '$conjunto' ORDER BY " . implode(', ', $order_var) . " LIMIT $tam_pag OFFSET ".($pag * $tam_pag)." ";
	trace("Selecting data. Query: <br/>".$sql."");

    //echo "<br>".$sql."<br>";
    //die("ok");
    $init = initTime();
    pg_query("BEGIN") or die("1Could not start transaction\n" . pg_last_error());
    pg_query("SET statement_timeout TO 1800000") or die(pg_last_error());
    $rsMed = pg_query($sql) or die("(" . elapsed_time($init) . ") tempo. Erro 798 " . pg_last_error());
    pg_query("COMMIT") or die("1Transaction commit failed\n" . pg_last_error());
    if (pg_num_rows($rsMed) > 0) {

//trace0("<br/>Query executada com sucesso. Atribuindo resultado a vetor<br/>");		//echo "Encontrados ".pg_num_rows($rsMed)." linhas<br /><br />";
        $dados = pg_fetch_all($rsMed); //transformando todas as tuplas em um vetor
        trace0("Found ".count($dados)." values");
        
        $novos_dados = interpolarDados($referencia, $variaveis, $constantes, $dados, $passo, $referencias);
        //trace0("Primeiro a sumir: ".  implode(",", $novos_dados[100000]));
        trace0("Finihed interpolation. " . count($novos_dados) . " data interpolated");
//return;
//die("OK, dados interpolados");

        pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());

        
        //$id_conjunto = 0;
        /*
        if (!($rsCon = pg_query("INSERT INTO d_conjunto (id_campo_fk, processamento, id_conjuntopai_fk) VALUES ('$campo', '$processamento, $interpolar_var interpolada', $conjunto) RETURNING id"))) {
            pg_query("ROLLBACK") or die("Transaction rollback failed1\n" . pg_last_error());
            die("Error1 Query: " . pg_last_error());
        }
        $row = pg_fetch_row($rsCon);
        $id_conjunto = $row['0'];
*/
        
        inserirRegistrosBanco($tabela, $novos_dados, array("id_conjunto_fk" => $id_conjunto));
        
        /*
        $valores = "";
        $i = 0;
        $quant = count($novos_dados);
        foreach ($novos_dados as $novo_dado) {
            $i++;
            if ($i == 100000 || $i == $quant) {
                $valores .= "('" . implode("', '", $novo_dado) . "', '$id_conjunto') ";
                if (!pg_query("INSERT INTO $tabela (" . implode(', ', array_keys($novo_dado)) . ", id_conjunto_fk) VALUES $valores")) {
                    pg_query("ROLLBACK") or die("Transaction rollback failed\n" . pg_last_error());
                    die("Error Query: " . pg_last_error());
                }
                $i = 0;
                $valores = "";
            } else {
                $valores .= "('" . implode("', '", $novo_dado) . "', '$id_conjunto'), ";
            }
        }
         * 
         */
        pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());

        gravaLog("Data inserted sucessfully on databse");
    } else {
        gravaLog("Finish processing or none data found to the dataset ($conjunto)");
    }
}
    if (isset($dados))
        unset($dados);
    if (isset($novos_dados))
        unset($novos_dados);
}

//contrói uma matriz (2D), a partir de valores em vetores
//$linha é um array de chaves das variáveis que formarão o identificador da linha
//$coluna é um array de chaves das variáveis que formarão o identificador da coluna
//$celula é um array de chaves das variáveis que formarão o conteúdo de cada célula interna
//$dados é um array bidimensional com os dados
function construirMatriz($linha, $coluna, $celula, $dados) {
	//trace("Construindo Matriz. <br/> Linha (".json_encode($linha)."), <br/>Coluna (".json_encode($coluna)."), <br/>Celula (".json_encode($celula).")");
    //die();
	$quant = count($dados);
    $tabela = array();
    for ($i = 0; $i < $quant; $i++) {//cada uma das tuplas

        foreach ($linha as $lin) {
            $q_l = 0;
            $l = "";
            if (count($linha) > 1) {//lin pode ter mais de uma posição, caso seja variável combinada
                $l .= $lin[0]; //rotulos estáticos
                $q_l++;
            } else {
                foreach ($lin as $var) {
                    $l .= "  ".$dados[$i][$var]; //rotulos dinamicos
                    $q_l++;
                }
            }

            $l = trim($l);
            
            foreach ($coluna as $col) {
                $q_c = 0;
                $c = "";
                //print_r($coluna);
                if (count($coluna) > 1) {
                    $c .= $col[0]; //rotulos estaticos
                    $q_c++;
                } else {
                    foreach ($col as $var) {//col pode ter mais de uma posição, caso seja variável combinada
                        $c .= "  ".$dados[$i][$var]; //rotulos dinamicos
                        $q_c++;
                    }
                }
                
                $c = trim($c);

                $v = "";
                if ($celula != null) {
                    foreach ($celula as $key) {
                        $v .= "  ".$dados[$i][$key];
                    }
                } else {//valor da celula é null, significa que deve pegar o rotulo
                    if ($q_c == 1) {
                        $v .= $dados[$i][$col[array_keys($col)[0]]];
                    } else if ($q_l == 1) {
                        $v .= $dados[$i][$lin[array_keys($lin)[0]]];
                    } else {
                        die("Error: Invalid Matrix");
                    }
                }

                $v = trim($v);
                
                if (!isset($tabela[$l]))
                    $tabela[$l] = array();
                $tabela[$l][$c] = $v;
            }
        }


//die();


        if (!isset($tabela[$l]))
            $tabela[$l] = array();
        $tabela[$l][$c] = $v;
    }
    return $tabela;
}

function construirMatriz_join($linha, $coluna, $celula, $dados) {
    $quant = count($dados);
    $tabela = array();
    for ($i = 0; $i < $quant; $i++) {//para cada uma das linhas de dados do banco
        foreach ($linha as $lin) {//para cada conjunto das variáveis que compõem as linhas da matriz, esperado normalmente que exista apenas um conjunto
            $l = "";
            $q_l = 0;
            foreach ($lin as $var) {//para cada variável que corresponde ao conjunto
                $l .= $dados[$i][$var]; // agrupar as variáveis concatenando (identificador da linha)
                $q_l++;
            }

            foreach ($coluna as $col) {//para cada conjunto principal das variáveis que compõem as colunas da matriz
                $c = "";
                $q_c = 0;
                if (count($col) > 1) {// se existe um subconjunto, é necessário combinar as variáveis
                    foreach ($col as $var) {//para cada variável do subconjunto
                        $c .= $dados[$i][$var]; //concatenar (forma uma coluna, mas com as variáveis combinadas)
                        $q_c++;
                        //echo "<br />Usando #1: ".$dados[$i][$var];
                    }
                } else {//se não tem subconjunto, então é uma variável correspondente à coluna da tabela
                    $q_c++;
                    $c .= $col[array_keys($col)[0]];
                    //echo "<br />Usando #2: ".$col[array_keys($col)[0]];
                }

                $v = "";
                if ($celula != null) {
                    foreach ($celula as $key) {
                        if ($key == null) {
                            $celula = null;
                        } else {
                            $v .= $dados[$i][$key];
                        }
                    }
                }
                if ($celula == null) {
                    if ($q_c == 1)
                        $v = $dados[$i][$col[array_keys($col)[0]]];
                    else if ($q_l == 1)
                        $v = $dados[$i][$lin[array_keys($lin)[0]]];
                    else
                        die("Error: Invalid Matrix");
                }


                if (!isset($tabela[$l]))
                    $tabela[$l] = array();
                $tabela[$l][$c] = $v;
                //echo "<br/>[".$l."][".$c."]";
            }
        }
    }
    return $tabela;
}



//pega uma matriz (array 2d) e escreve em um arquivo CSV para download
function exportarMatriz($m, $arquivo = null, $download = true) {
    if($arquivo == null) $arquivo = "dados_".  date("YmdHis").".csv";
    else $arquivo = str_replace (" ", "", $arquivo).".csv";
    
    if($download){
        $fp = fopen("php://output", 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $arquivo);
    }else{//save in folder
        $fp = fopen("/tmp/".$arquivo, 'w');
        gravaLog("Criating file $arquivo");
    }


    foreach ($m as $l_j) {
        //echo "<td>#</td>";
        $keys = array_keys($l_j);
        array_unshift($keys, "#");
        fputcsv($fp, $keys, ';', ' ');
        break;
    }

    foreach ($m as $l_i => $l_j) {
        array_unshift($l_j, $l_i);
        fputcsv($fp, $l_j, ';', ' ');
    }

    fclose($fp);

    if($download){
        die();
    }
    
    //printTable($m); return;
    
    
}

//pega uma matriz (array 2d) e escreve em um arquivo CSV para download
function exportarVetor($v, $arquivo = null, $download = true) {
    if($arquivo == null) $arquivo = "dados_".  date().".csv";
    else $arquivo = str_replace (" ", "", $arquivo).".csv";
    
    if($download){
        $fp = fopen("php://output", 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $arquivo);
    }else{//save in folder
        $fp = fopen("/tmp/".$arquivo, 'w');
        gravaLog("Creating file $arquivo");
    }

    foreach ($v as $l_i => $l_j) {
        fputcsv($fp, array($l_j), ';', ' ');
    }

    fclose($fp);

    if($download){
        die();
    }    
    
}

//pega uma matriz (array 2d) e escreve em um arquivo CSV para download
function exportarVetorTxt($v, $arquivo = null, $download = true) {
    if($arquivo == null) $arquivo = "dados_".  date().".txt";
    else $arquivo = str_replace (" ", "", $arquivo).".txt";
    
    if($download){
        $fp = fopen("php://output", 'w');
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment;filename=' . $arquivo);
    }else{//save in folder
        $fp = fopen("/tmp/".$arquivo, 'w');
        gravaLog("Creating file $arquivo");
    }

    foreach ($v as $l_i => $l_j) {
        fwrite($fp, $l_j.PHP_EOL);
    }

    fclose($fp);

    if($download){
        die();
    }    
    
}




//pega uma matriz (array 2d) e imprime na tela em formato de tabela
function printTable($m, $max = null) {
	echo "<table class='datatable' style='display:none'>";

    foreach ($m as $l_j) {
    	echo "<thead><tr>";
        //echo "<td>#</td>";
        if(is_array($l_j)){
	        $keys = array_keys($l_j);
	        foreach ($keys as $k) {
	            echo "<th>$k</th>";
	        }
        }
        echo "</tr></thead>";
        break;
    }

    echo "<tbody>";
    
    $i = 0;
    foreach ($m as $l_i => $l_j) {
        if ($max != null && $i++ == $max) break;
        echo "<tr>";
        //echo "<td>$l_i</td>";
        if(is_array($l_j)){
	        foreach ($l_j as $c_i => $c_j) {
	            if(is_array($c_j)){//print matrix
	            	echo "<td>";
	            	printTable($c_j, 10);
	            	echo "</td>";
	            }
	            //else if(is_vetor())//print vetor linear
	            else
	        		echo "<td>$c_j</td>";
	        }
        }else{
        	echo "<td>$l_j</td>";
        }
        echo "</tr>";
    }
    //if($i >= $max) echo "<tr><td>[...]</td></tr>";
    
    echo "</tbody>";
    
    echo "</table>";
}

function calculaProfundidadeCTD($id_conjunto, $constante) {

    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());
    pg_query("SET statement_timeout TO 1800000") or die(pg_last_error());

    if (!pg_query("UPDATE d_medidas_ctd SET profundidade_m = (pressao_dbar * $constante) WHERE id_conjunto_fk = '$id_conjunto'"))
        pg_query("ROLLBACK") or die("Transaction rollback failed1\n" . pg_last_error());
    if (!pg_query("UPDATE d_conjunto SET processamento = CONCAT(processamento, ', profundidade calculada') WHERE id = '$id_conjunto'"))
        pg_query("ROLLBACK") or die("Transaction rollback failed1\n" . pg_last_error());

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
}

/*passagem:
 *      negativo: subida
 *      positivo: descida
 *      valor identifica a passagem (ex: -1 é a primeira subida, -2 é a segunda subida)
 *      valor indica a variação da pressão
 */

function validaPassagemCTD($conjunto, $passagem = -1) {
    $sentido = 1;
    $str_pass = array();
    $str_pass[1] = "Primeira";
    $str_pass[2] = "Segunda";
    $str_pass[3] = "Terceira";
    $str_proc = "";
    if($passagem < 0){
        $sentido = -1;
        $passagem *= -1;
        $str_proc = $str_pass[$passagem] . " " . "Subida";
    }
    else{
        $str_proc = $str_pass[$passagem] . " " . "Descida";
    }
    //trace0("passagem ($sentido, $passagem)");
    
    $rsMed = pg_query("SELECT * FROM d_medidas_ctd WHERE id_conjunto_fk = '$conjunto' ORDER BY tempo_ms") or die(pg_last_error());
    $dados = pg_fetch_all($rsMed); //transformando todas as tuplas em um vetor

    $quant = count($dados) - 1;

    $i = -1;
    $descartando = false;
    for($p = 1; ($p <= $passagem || $descartando); $p++){//buscando a passagem p
        do {//buscar o início do intervalo da passagem
            $i++;
            while (($i < $quant) && (($dados[$i + 1]["pressao_dbar"]*$sentido) < ($dados[$i]["pressao_dbar"]*$sentido))) {
                $i++; //enquanto houver dados e o próximo for no sentido contrario
            }
            //$i é um possível candidato a ser início da passagem. Mas é necessário verificar
            $salto = 30;
            if(count($dados) - $i < $salto) $salto = count($dados) - $i - 1;
        } while (($i < $quant) && (($dados[$i + $salto]["pressao_dbar"]*$sentido) < ($dados[$i]["pressao_dbar"]*$sentido) || abs($dados[$i]["pressao_dbar"] - $dados[$i+$salto]["pressao_dbar"]) > 3));//se não é um salto muito grande
        
        if($descartando){
            $descartando = false;
            $sentido = $sentido *-1;
        }
        else if($p < $passagem){//se não é a passagem de interesse, descartar passagem
            $p--;//não conta esta próxima passagem
            $descartando = true;
            $sentido = $sentido *-1;
        }
        
    }
    
    //trace0("Inicio: $i");
    //$i é o início da passagem, remover as medidas que vão no outro sentido (balanço)
    $novos_dados = array();
    unset($dados[$i]["id"]);
    $novos_dados[] = $dados[$i]; $i++;
    $descartou = 0;
    $fim = 0;
    while ($i < $quant) {
        if (($dados[$i]["pressao_dbar"]*$sentido) > ($novos_dados[count($novos_dados)-1]["pressao_dbar"]*$sentido)) {
            unset($dados[$i]["id"]);
            $novos_dados[] = $dados[$i];
            $descartou = 0;
            $fim = $i;
        } else {
            //echo "$descartou: Remove ".$dados[$i]["pressao_dbar"] . " de ".$dados[$i]["tempo_ms"]."<br />";
            $descartou++;
            if ($descartou > 30)
                break;
        }
        $i++;
    }
    
    
    trace0("dados encontrados: ".count($novos_dados));
    if(count($novos_dados) <= 1){
        gravaLog("empty dataset found");
        return null;
    }
    
    //trace0("Final: $fim");
    
    //printTable($novos_dados);
    
    //return 0;
    
    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());
    $a_conjunto = getAtrsQuery("SELECT * FROM d_conjunto WHERE id = '$conjunto'");

    $id_conjunto_n = criaNovoConjunto($a_conjunto["id_campo_fk"], ($a_conjunto["processamento"] . ", ".$str_proc), $conjunto);
    
    inserirRegistrosBanco("d_medidas_ctd", $novos_dados, array("id_conjunto_fk" => $id_conjunto_n));

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
    gravaLog(count($novos_dados)." new data inserted with sucess");
    return $id_conjunto_n;
}

function validaPrimeiraDescidaCTD($conjunto) {
    gravaLog("Processing first CTD down-travel from dataset: $conjunto");
    return validaPassagemCTD($conjunto, 1);
}

function validaPrimeiraSubidaCTD($conjunto) {
	gravaLog("Processing first CTD up-travel from dataset: $conjunto");
    return validaPassagemCTD($conjunto, -1);
}
function validaSegundaDescidaCTD($conjunto) {
    gravaLog("Processing second CTD down-travel from dataset: $conjunto");
    return validaPassagemCTD($conjunto, 2);
}

function validaSegundaSubidaCTD($conjunto) {
    gravaLog("Processing second CTD up-travel from dataset: $conjunto");
    return validaPassagemCTD($conjunto, -2);
}


function getConjuntoAuxSelecionados($qtdmax_integracao) {
    $conjuntos = array();
    for ($i = 1; $i <= $qtdmax_integracao; $i++) {
        $campo_i = (int) post("campo$i");
        $conjunto_i = (int) post("conjunto$i");
        if ($campo_i > 0) {//conjunto selecionada
            $equip_i = getEquipCampo($campo_i);
            $sensor_i = getSensorCampo($campo_i);
            $tabela_i = "d_medidas_$sensor_i";

            $conjunto = array();
            $conjunto["campo"] = $campo_i;
            $conjunto["conjunto"] = $conjunto_i;
            $conjunto["equipamento"] = getEquipCampo($campo_i);
            $conjunto["sensor"] = getSensorCampo($campo_i);
            $conjunto["tabela"] = "d_medidas_$sensor_i";

            $conjuntos[] = $conjunto;
        }
    }
    return $conjuntos;
}  

function inserirRegistrosBanco($tabela, $novos_dados, $a_constantes = null) {
    trace("iniciando inserção no banco");
	if($a_constantes == null) $a_constantes = array();
    $i = 0;
    $quant = count($novos_dados);
    $valores = "";
    foreach ($novos_dados as $novo_dado) {
        $novo_dado = array_merge($novo_dado, $a_constantes);
        $i++;
        if($i % 100000 == 0)
			trace("[".date("d/m/Y H:i:s")."]: Processing line $i from $quant");
        if (($i % 100000 == 0) || $i == $quant) {
            $campos = "(" . implode(', ', array_keys($novo_dado)) . ")";
            $valores .= "('" . implode("', '", $novo_dado) . "') ";
            //trace0("Insert com ultimo: ".implode(",", $novo_dado));
            $valores = str_replace("''", "NULL", $valores);
            $sql = "INSERT INTO $tabela $campos VALUES $valores";
            if (!pg_query($sql)) {
                pg_query("ROLLBACK") or die("Transaction rollback failed\n" . pg_last_error());
                die("Error Query: [[[[[[[[[[[[[$sql]]]]]]]]]]]]]" . pg_last_error());
            }
            //$i = 0;
            $valores = "";
        } else {
            $valores .= "('" . implode("', '", $novo_dado) . "'), ";
        }
    }
}

function criaNovoConjunto($id_campo_fk, $processamento, $id_conjuntopai_fk) {
    $id_conjunto_n = "";
    if ($rsCon = pg_query("INSERT INTO d_conjunto (id_campo_fk, processamento, id_conjuntopai_fk) VALUES ('$id_campo_fk', '$processamento', '$id_conjuntopai_fk') RETURNING id")) {
        $row = pg_fetch_row($rsCon);
        $id_conjunto_n = $row['0'];
    } else {
        pg_query("ROLLBACK") or die("Transaction rollback failed1\n" . pg_last_error());
        die("Error1 Query: " . pg_last_error());
    }
    return $id_conjunto_n;
}

function corrigeTemperaturaSalinidadeACS($id_conjunto_acs, $id_conjunto_ctd = null, $temperatura_referencia = null) {
    global $pagina;

    if ($temperatura_referencia == null)
        $temperatura_referencia = (float) getAtrQuery("select AVG(intensidade) FROM d_medidas_acs WHERE id_conjunto_fk = '$id_conjunto_acs' AND tipo = 'a' AND comprimento_onda = '740'");

    $pagina->addAviso("<br />Correcting field $id_conjunto_acs with reference temperature $temperatura_referencia]");

    $campo_acs = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_acs'");
    $conjunto_acs = getAtrsQuery("SELECT * FROM d_conjunto WHERE id = '$id_conjunto_acs'");


    //se nenhum conjunto foi selecionado, então tentar buscar o conjunto do CTD com a mesma data do ACS selecionado
    if ($id_conjunto_ctd == null) {
        $id_equip_ctd = '3';

        $rsCtd = pg_query("SELECT * FROM d_campo WHERE tempoinicio = '" . $campo_acs["tempoinicio"] . "' AND id_regiao_fk = '" . $campo_acs["id_regiao_fk"] . "' AND id_equipamento_fk = '$id_equip_ctd'") or die(pg_last_error());

        if (pg_num_rows($rsCtd) == 1) {
            $campo_ctd = pg_fetch_assoc($rsCtd);
            $conjunto_ctd = getAtrsQuery("SELECT * FROM d_conjunto WHERE id_campo_fk = '" . $campo_ctd["id"] . "' ORDER BY data DESC LIMIT 1");
            $id_conjunto_ctd = $conjunto_ctd["id"];
        } else {
            if (pg_num_rows($rsCtd) > 1)
                $pagina->addAdvertencia("Warning! We found many CTD dataset related to this point and date. Please, select the CTD dataset manually.");
            else
                $pagina->addAdvertencia("Warning! We not found any CTD dataset related to this point and date. Please, select the CTD dataset manually.");
            return;
        }
    }
    $conjunto_ctd = getAtrsQuery("SELECT * FROM d_conjunto WHERE id = '" . $id_conjunto_ctd . "' LIMIT 1");
    $campo_ctd = getAtrsQuery("SELECT * FROM d_campo WHERE id = '" . $conjunto_ctd["id_campo_fk"] . "' LIMIT 1");

    $pagina->addAviso("Correcting the ACS dataset ($id_conjunto_acs) with processing [" . $conjunto_ctd["processamento"] . "] of CTD dataset ($id_conjunto_ctd)");

    //INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS	
    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());
    //preparar tabela auxiliar para correção
    $rsAux = pg_query("SELECT * FROM d_dependencias_psi ORDER BY comprimento_onda") or die(pg_last_error());
    $psi = array();
    while ($linha = pg_fetch_assoc($rsAux)) $psi[$linha["comprimento_onda"]] = array("T" => $linha["psi_T"], "Sc" => $linha["psi_Sc"], "Sa" => $linha["psi_Sa"]);

    //pegando os dados do acs que serão corrigidos, após integração com CTD
    $sql = "SELECT * FROM d_medidas_acs as acs INNER JOIN d_medidas_ctd as ctd ON acs.tempo_ms = ctd.tempo_ms WHERE acs.id_conjunto_fk = '$id_conjunto_acs' AND ctd.id_conjunto_fk = '$id_conjunto_ctd'";
    $rsACS = pg_query($sql) or die(pg_last_error());

    //criar novo conjunto de dados corrigido
    $id_conjunto_n = criaNovoConjunto($campo_acs["id"], $conjunto_acs["processamento"] . ", corrigido por temperatura e salinidade", $id_conjunto_acs);
    //$id_conjunto_n = 0;

    //constantes
    $tr = $temperatura_referencia;
    $acs_corrigido = array();
    while ($linha = pg_fetch_assoc($rsACS)) {
        $corrigido = array();
        $t = $linha["temperatura_c"];
        $S = $linha["salinidade_psu"];
        $comprimento_onda = $linha["comprimento_onda"];
        $psiT = $psi[$comprimento_onda]["T"];
        $psiSa = $psi[$comprimento_onda]["Sa"];
        $psiSc = $psi[$comprimento_onda]["Sc"];
        $tipo = $linha["tipo"];

        if ($tipo == "a") {
            $am = $linha["intensidade"];

            $cor = $am - ($psiT * ($t - $tr) + $psiSa * $S);
        } else if ($tipo == "c") {
            $cm = $linha["intensidade"];

            $cor = $cm - ($psiT * ($t - $tr) + $psiSc * $S);
        }

        $corrigido["tempo_ms"] = $linha["tempo_ms"];
        $corrigido["comprimento_onda"] = $comprimento_onda;
        $corrigido["intensidade"] = $cor;
        $corrigido["tipo"] = $tipo;
        $corrigido["id_conjunto_fk"] = $id_conjunto_n;


        $acs_corrigido[] = $corrigido;
    }

    inserirRegistrosBanco("d_medidas_acs", $acs_corrigido);
    $pagina->addAviso(count($acs_corrigido)." corrigidos e inseridos com sucesso");

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
}

function corrigeFlatACS($id_conjunto_acs, $comprimentoonda_referencia) {
    global $pagina;

    $pagina->addAviso("Corrigindo campo ACS $id_conjunto_acs com método FLAT e comprimento de onda de referencia $comprimentoonda_referencia");

    $campo_acs = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_acs'");
    $conjunto_acs = getAtrsQuery("SELECT * FROM d_conjunto WHERE id = '$id_conjunto_acs'");

    //INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS	
    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());

    //pegando os dados do acs que serão corrigidos
    $sql = "SELECT * FROM d_medidas_acs as acs WHERE acs.id_conjunto_fk = '$id_conjunto_acs'";
    $rsACS = pg_query($sql) or die(pg_last_error());

    //criar novo conjunto de dados corrigido
    $id_conjunto_n = criaNovoConjunto($campo_acs["id"], $conjunto_acs["processamento"] . ", absorção corrigida com Flat($comprimentoonda_referencia)", $id_conjunto_acs);
    //$id_conjunto_n = 0;
    
    //preparar tabela auxiliar para correção
    $rsAux = pg_query("SELECT * FROM d_medidas_acs WHERE id_conjunto_fk = '$id_conjunto_acs' AND comprimento_onda = '$comprimentoonda_referencia' AND tipo = 'a'") or die(pg_last_error());
    $a_ref = array();
    while ($linha = pg_fetch_assoc($rsAux))
        $a_ref[$linha["tempo_ms"]] = $linha["intensidade"];

    //print_r($a_ref);
    //constantes
    $acs_corrigido = array();
    while ($linha = pg_fetch_assoc($rsACS)) {
        $corrigido = array();
        $a = $linha["intensidade"];
        $aref = $a_ref[$linha["tempo_ms"]];

        if ($linha["tipo"] == "a") {
            $cor = $a - $aref;
        } else {//atenuação
            $cor = $a;
        }

        $corrigido["tempo_ms"] = $linha["tempo_ms"];
        $corrigido["comprimento_onda"] = $linha["comprimento_onda"];
        $corrigido["intensidade"] = $cor;
        $corrigido["tipo"] = $linha["tipo"];
        $corrigido["id_conjunto_fk"] = $id_conjunto_n;


        $acs_corrigido[] = $corrigido;
    }

    inserirRegistrosBanco("d_medidas_acs", $acs_corrigido);
    $pagina->addAviso(count($acs_corrigido)." corrigidos e inseridos com sucesso");

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
}

function corrigeKirkACS($id_conjunto_acs, $constante_espalhamento) {
    global $pagina;

    $pagina->addAviso("Corrigindo campo ACS $id_conjunto_acs com método Kirk e constante de espalhamento $constante_espalhamento");

    $campo_acs = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_acs'");
    $conjunto_acs = getAtrsQuery("SELECT * FROM d_conjunto WHERE id = '$id_conjunto_acs'");

    //INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS	
    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());

    //pegando os dados do acs que serão corrigidos
    $sql = "select acs1.*, acs2.intensidade as c FROM d_medidas_acs as acs1 LEFT JOIN d_medidas_acs as acs2 ON (acs1.tempo_ms = acs2.tempo_ms AND acs1.id_conjunto_fk = acs2.id_conjunto_fk AND acs1.comprimento_onda = acs2.comprimento_onda) WHERE acs1.id_conjunto_fk = '$id_conjunto_acs' AND acs2.tipo='c'";
    $rsACS = pg_query($sql) or die(pg_last_error());

    //criar novo conjunto de dados corrigido
    $id_conjunto_n = criaNovoConjunto($campo_acs["id"], $conjunto_acs["processamento"] . ", absorção corrigida com Kirk($constante_espalhamento)", $id_conjunto_acs);

    $acs_corrigido = array();
    while ($linha = pg_fetch_assoc($rsACS)) {
        $corrigido = array();
        $a = $linha["intensidade"];
        $c = $linha["c"];
        $cfs = $constante_espalhamento;
        $bcor = $c - $a;

        if ($linha["tipo"] == "a") {
            $cor = $a - ($cfs * $bcor);
        } else {//atenuação
            $cor = $c;
        }

        $corrigido["tempo_ms"] = $linha["tempo_ms"];
        $corrigido["comprimento_onda"] = $linha["comprimento_onda"];
        $corrigido["intensidade"] = $cor;
        $corrigido["tipo"] = $linha["tipo"];
        $corrigido["id_conjunto_fk"] = $id_conjunto_n;


        $acs_corrigido[] = $corrigido;
    }

    inserirRegistrosBanco("d_medidas_acs", $acs_corrigido);
    $pagina->addAviso(count($acs_corrigido)." corrigidos e inseridos com sucesso");

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
}

function corrigePropACS($id_conjunto_acs, $comprimentoonda_referencia) {
    global $pagina;

    $pagina->addAviso("Corrigindo campo ACS $id_conjunto_acs com método FLAT e comprimento de onda de referencia $comprimentoonda_referencia");

    $campo_acs = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_acs'");
    $conjunto_acs = getAtrsQuery("SELECT * FROM d_conjunto WHERE id = '$id_conjunto_acs'");

    //INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS	
    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());

    //pegando os dados do acs que serão corrigidos
    $sql = "select acs1.*, acs2.intensidade as c FROM d_medidas_acs as acs1 LEFT JOIN d_medidas_acs as acs2 ON (acs1.tempo_ms = acs2.tempo_ms AND acs1.id_conjunto_fk = acs2.id_conjunto_fk AND acs1.comprimento_onda = acs2.comprimento_onda) WHERE acs1.id_conjunto_fk = '$id_conjunto_acs' AND acs2.tipo='c'";
    $rsACS = pg_query($sql) or die(pg_last_error());

    //criar novo conjunto de dados corrigido
    $id_conjunto_n = criaNovoConjunto($campo_acs["id"], $conjunto_acs["processamento"] . ", absorção corrigida com Proporcional($comprimentoonda_referencia)", $id_conjunto_acs);
    
    //$id_conjunto_n = 0;

    //preparar tabela auxiliar para correção
    $sql = "SELECT tempo_ms, intensidade FROM d_medidas_acs WHERE id_conjunto_fk = '$id_conjunto_acs' AND comprimento_onda = '$comprimentoonda_referencia' AND tipo = 'a'";
    //trace0($sql);
    $rsAux = pg_query($sql) or die(pg_last_error());
    $a_ref = array();
    while ($linha = pg_fetch_assoc($rsAux))
        $a_ref[$linha["tempo_ms"]] = $linha["intensidade"];

    $sql = "SELECT tempo_ms, intensidade FROM d_medidas_acs WHERE id_conjunto_fk = '$id_conjunto_acs' AND comprimento_onda = '$comprimentoonda_referencia' AND tipo = 'c'";
    //trace0($sql);
    $rsAux = pg_query($sql) or die(pg_last_error());
    $c_ref = array();
    while ($linha = pg_fetch_assoc($rsAux))
        $c_ref[$linha["tempo_ms"]] = $linha["intensidade"];

    //print_r($a_ref);
    //print_r($c_ref);


    //constantes
    $acs_corrigido = array();
    $erro = 0;
    while ($linha = pg_fetch_assoc($rsACS)) {
        if(isset($a_ref[$linha["tempo_ms"]]) && isset($c_ref[$linha["tempo_ms"]])){
            $corrigido = array();
            $a = $linha["intensidade"];
            $c = $linha["c"];
            $b = $c - $a;
            $aref = $a_ref[$linha["tempo_ms"]];
            $cref = $c_ref[$linha["tempo_ms"]];
            $bref = $cref - $aref;

            if ($linha["tipo"] == "a") {
                $cor = $a - ($aref * ($b / $bref));
            } else {//atenuação
                $cor = $a;
            }



            $corrigido["tempo_ms"] = $linha["tempo_ms"];
            $corrigido["comprimento_onda"] = $linha["comprimento_onda"];
            $corrigido["intensidade"] = $cor;
            $corrigido["tipo"] = $linha["tipo"];
            $corrigido["id_conjunto_fk"] = $id_conjunto_n;


            $acs_corrigido[] = $corrigido;
        }else{
            trace0("Erro em (tempo, comp_onda, tipo): (".$linha["tempo_ms"].", ".$linha["comprimento_onda"].", ".$linha["tipo"].")"); 
            $erro++; 
        }
    }
    if($erro > 0)
        trace0("####################################################Erro: coeficientes de referencia indefinidos [$id_conjunto_acs, $comprimentoonda_referencia] $erro vezes");

    //return;
    
    inserirRegistrosBanco("d_medidas_acs", $acs_corrigido);
    $pagina->addAviso(count($acs_corrigido)." corrigidos e inseridos com sucesso");

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
}

function getConjunto($id_conjunto){
	$conjunto = getAtrsQuery("Select * from d_conjunto where d_conjunto.id = '$id_conjunto'");
   return $conjunto;
}

function getCampo($id_campo){
	$campo = getAtrsQuery("Select d_campo.*, d_regiao.id_reservatorio_fk from d_campo LEFT JOIN d_regiao ON id_regiao_fk = d_regiao.id where d_campo.id = '$id_campo'");
   return $campo;
}

function getCampoEquivalente($tempoinicio, $id_regiao, $equip, $id_campo_ref = null, $id_reservatorio = null, $processamento = null) {
    global $pagina;

    $siglas = array();
    $siglas["ACS"] = "2";
    $siglas["CTD"] = "3";
    $siglas["EcoBB9"] = "4";
    $siglas["HydroScat"] = "5";
    $siglas["TriOS"] = array(6,7, 8, 9, 10);

    
    $str_camp = "";
    if($id_campo_ref != null){
    	$str_camp = "AND d_campo.id != '$id_campo_ref'";
    }
    
    $str_reservatorio = "";
    if($id_reservatorio != null){//pesquisar pelo reservatorio
    	$str_reservatorio = " AND id_reservatorio_fk = '$id_reservatorio' ";
    }else{//pesquisar pela região
    	$str_reservatorio = " AND id_regiao_fk = '$id_regiao' ";
    }
    
    $str_processamento = "";
    $join_conjunto = "";
    if($processamento != null){
    	$str_processamento = " AND processamento LIKE '%$processamento%'";
    	$join_conjunto = " LEFT JOIN d_conjunto ON (id_campo_fk = d_campo.id) ";
    }

    if(is_array($siglas[$equip])){
    	$ids_equip = implode(',', $siglas[$equip]);
    	$rsCampo = pg_query("SELECT d_campo.* FROM d_campo LEFT JOIN d_regiao ON (id_regiao_fk = d_regiao.id) $join_conjunto WHERE tempoinicio::date = '" . $tempoinicio . "'::date AND id_equipamento_fk IN ($ids_equip) $str_camp $str_reservatorio $str_processamento") or die(pg_last_error());
    }else{
	    $id_equip = $siglas[$equip];
	    $rsCampo = pg_query("SELECT d_campo.* FROM d_campo LEFT JOIN d_regiao ON (id_regiao_fk = d_regiao.id) $join_conjunto WHERE tempoinicio::date = '" . $tempoinicio . "'::date AND id_equipamento_fk = '$id_equip' $str_camp $str_reservatorio $str_processamento") or die(pg_last_error());
    }
	    
    if (pg_num_rows($rsCampo) == 1) {
        $campo = pg_fetch_assoc($rsCampo);

        return $campo;
    } else {
        if (pg_num_rows($rsCampo) > 1)
            $pagina->addAdvertencia("Atenção! Foram encontrados mais de um campo $equip (".pg_num_rows($rsCampo).") correspondente ao mesmo ponto e data. Selecione Manualmente");
        else
            $pagina->addAdvertencia("Atenção! Não foi encontrado nenhum campo $equip (".pg_num_rows($rsCampo).") correspondente ao mesmo ponto e data. Selecione Manualmente");
        return null;
    }
}

//a_tipos traz uma restrição de que o conjuunto deve ter aqueles tipos declarados no array
//se a_tipos é null, então retorna o ultimo conjunto inserido
//se a_tipo não é null, retorna o último conjunto inserido que possui aquele campo
function getConjuntoCampo($id_campo, $a_tipos = null){
    $rsConjunto = pg_query("SELECT * FROM d_conjunto WHERE id_campo_fk = '$id_campo' ORDER BY data DESC") or die(pg_last_error());
    
    
    if($a_tipos == null || count($a_tipos) == 0){
        if(pg_num_rows($rsConjunto) > 0){
            $conjunto = pg_fetch_assoc($rsConjunto);
        }else{
        	trace("Nenhum conjunto encontrado para o campo $id_campo");
        }
        return $conjunto;
    }else{
    	trace("SEnsor do campo $id_campo");
        $sensor = getSensorCampo($id_campo);
        while($conjunto = pg_fetch_assoc($rsConjunto)){
            $continuar = true;
            $id_conjunto = $conjunto["id"];
            foreach ($a_tipos as $tipo){
                //trace0("SELECT * FROM d_medidas_$sensor WHERE id_conjunto_fk = '$id_conjunto' AND tipo = '$tipo' LIMIT 1");
                if(getAtrQuery("SELECT count(*) as qtd FROM d_medidas_$sensor WHERE id_conjunto_fk = '$id_conjunto' AND tipo = '$tipo' LIMIT 1") == 0){//não existe este tipo
                    $continuar = false;
                    break;
                }
            }
            if($continuar){
                return $conjunto;
            }
        }
        trace("Nenhum dos ".  pg_num_rows($rsConjunto)." conjuntos encontrados para o campo $id_campo tem o tipo (". implode(', ', $a_tipos).")");
    }
}

function getConjuntoProcessadoCampo($id_campo, $processamento = null){
    $rsConjunto = pg_query("SELECT * FROM d_conjunto WHERE id_campo_fk = '$id_campo' AND processamento = '$processamento' ORDER BY data DESC") or die(pg_last_error());
    
    
        if(pg_num_rows($rsConjunto) > 0){
            $conjunto = pg_fetch_assoc($rsConjunto);
            return $conjunto;
        }else{
            gravaLog("Nenhum conjunto encontrado para o campo $id_campo");
            return null;
        }
        
}


function calculaKbbHydro($id_conjunto_hydro, $id_conjunto_ctd = null, $id_conjunto_acs = null) {
    global $pagina;

    $pagina->addAviso("Calculando Kbb do campo $id_conjunto_hydro");

    $campo_hydro = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_hydro'");
    $conjunto_hydro = getConjuntoCampo($campo_hydro["id"], array("bbuncorr"));

    //se nenhum conjunto foi selecionado, então tentar buscar o conjunto do CTD com a mesma data do Hydro selecionado
    $campo_ctd = null;
    $conjunto_ctd = null;
    if ($id_conjunto_ctd == null) {
        $campo_ctd = getCampoEquivalente($campo_hydro["tempoinicio"], $campo_hydro["id_regiao_fk"], "CTD");
        if($campo_ctd != null){
            $conjunto_ctd = getAtrsQuery("SELECT * FROM d_conjunto WHERE id_campo_fk = '" . $campo_ctd["id"] . "' ORDER BY data DESC LIMIT 1");
            $id_conjunto_ctd = $conjunto_ctd["id"];
        }
    }else{//$id_conjunto_hydro_bb DEFINIDO, PREENCHER CAMPO E CONJUNTO
        $campo_ctd = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_ctd'");
        $conjunto_ctd = getConjuntoCampo($campo_ctd["id"]);
    }
    
    $campo_acs = null;
    $conjunto_acs = null;
    if ($id_conjunto_acs == null) {
        $campo_acs = getCampoEquivalente($campo_hydro["tempoinicio"], $campo_hydro["id_regiao_fk"], "ACS");
        if($campo_acs != null){
            $conjunto_acs = getConjuntoCampo($campo_acs["id"], array("a", "c"));
            $id_conjunto_acs = $conjunto_acs["id"];
        }
    }else{//$id_conjunto_hydro_bb DEFINIDO, PREENCHER CAMPO E CONJUNTO
        $campo_acs = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_acs'");
        $conjunto_acs = getConjuntoCampo($campo_acs["id"], array("a", "c"));
    }
    
    $pagina->addAviso("Calculando Kbb ($id_conjunto_hydro) com conjunto [" . $conjunto_ctd["processamento"] . "] do CTD ($id_conjunto_ctd) e com conjunto [" . $conjunto_acs["processamento"] . "] do ACS ($id_conjunto_acs)");

    //INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS	
    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());
    
    //pegando os dados do Hydro que serão corrigidos, após integração com CTD E ACS
    $sql = "SELECT h.*, a1.intensidade as a, a2.intensidade as c FROM d_medidas_hydroscat as h INNER JOIN d_medidas_ctd as c ON h.profundidade_m = c.profundidade_m INNER JOIN d_medidas_acs as a1 ON a1.tempo_ms = c.tempo_ms INNER JOIN d_medidas_acs as a2 ON (a1.tempo_ms = a2.tempo_ms AND a1.comprimento_onda = a2.comprimento_onda AND h.comprimento_onda = a1.comprimento_onda) WHERE h.tipo = 'bbuncorr' AND a1.tipo = 'a' AND a2.tipo = 'c' AND a1.id_conjunto_fk = '$id_conjunto_acs' AND a2.id_conjunto_fk = '$id_conjunto_acs' AND c.id_conjunto_fk = '$id_conjunto_ctd' AND h.id_conjunto_fk = '$id_conjunto_hydro'";
    
    //trace0($sql);
    
    $rsHydro = pg_query($sql) or die(pg_last_error());
    
    //die("Selecionou ".  pg_num_rows($rsHydro));
    
    //criar novo conjunto de dados corrigido
    $id_conjunto_n = criaNovoConjunto($campo_hydro["id"], $conjunto_hydro["processamento"] . ", calculado Kbb", $id_conjunto_hydro);


    //constantes
    $hydro_corrigido = array();
    while ($linha = pg_fetch_assoc($rsHydro)) {
        $corrigido = array();
        $a = (float)$linha["a"];
        $c = (float)$linha["c"];
        $b = $c - $a;
        
        $cor = $a + (0.4 * $b);

        $corrigido["comprimento_onda"] = $linha["comprimento_onda"];
        $corrigido["tempo_ms"] = $linha["tempo_ms"];
        $corrigido["profundidade_m"] = $linha["profundidade_m"];
        $corrigido["intensidade"] = $cor;
        $corrigido["tipo"] = "Kbb_cal";
        $corrigido["id_conjunto_fk"] = $id_conjunto_n;


        $hydro_corrigido[] = $corrigido;
    }

    inserirRegistrosBanco("d_medidas_hydroscat", $hydro_corrigido);
    $pagina->addAviso(count($hydro_corrigido)." corrigidos e inseridos com sucesso");

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
}

function calculaSigmaHydro($id_conjunto_hydro, $k1, $a_kexp) {
    global $pagina;

    $pagina->addAviso("Calculando Sigma Kbb do conjunto $id_conjunto_hydro");

    $campo_hydro = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_hydro'");
    $conjunto_hydro = getConjuntoCampo($campo_hydro["id"], array("Kbb_cal"));

    //INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS
    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());
    
    //pegando os dados do Hydro que serão corrigidos, após integração com CTD E ACS
    $sql = "SELECT * FROM d_medidas_hydroscat as h WHERE h.tipo = 'Kbb_cal' AND h.id_conjunto_fk = '$id_conjunto_hydro'";
    
    //trace0($sql);
    
    $rsHydro = pg_query($sql) or die(pg_last_error());
    
    //die("Selecionou ".  pg_num_rows($rsHydro));
    
    //criar novo conjunto de dados corrigido
    $id_conjunto_n = criaNovoConjunto($campo_hydro["id"], $conjunto_hydro["processamento"] . ", calculado sigma Kbb", $id_conjunto_hydro);


    //constantes
    $hydro_corrigido = array();
    while ($linha = pg_fetch_assoc($rsHydro)) {
        $corrigido = array();
        $c_onda = $linha["comprimento_onda"];
        $kexp = $a_kexp[$c_onda];
        $Kbb = $linha["intensidade"];
        
        $cor = $k1 * pow(M_E, $kexp * $Kbb);

        $corrigido["comprimento_onda"] = $linha["comprimento_onda"];
        $corrigido["tempo_ms"] = $linha["tempo_ms"];
        $corrigido["profundidade_m"] = $linha["profundidade_m"];
        $corrigido["intensidade"] = $cor;
        $corrigido["tipo"] = "sigma_cal";
        $corrigido["id_conjunto_fk"] = $id_conjunto_n;


        $hydro_corrigido[] = $corrigido;
    }

    inserirRegistrosBanco("d_medidas_hydroscat", $hydro_corrigido);
    $pagina->addAviso(count($hydro_corrigido)." corrigidos e inseridos com sucesso");

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
    
}

function aplicarSigmaHydro($id_conjunto_hydro, $id_conjunto_hydro_bb, $id_conjunto_ctd = null){
    global $pagina;

    $pagina->addAviso("Aplicando Sigma do campo $id_conjunto_hydro [em $id_conjunto_hydro_bb]");

    $campo_hydro = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_hydro'");
    $conjunto_hydro = getConjuntoCampo($campo_hydro["id"], array("sigma_cal"));
    
    $campo_hydro_bb = null;
    $conjunto_hydro_bb = null;
    //se nenhum conjunto foi selecionado, então tentar buscar o conjunto do CTD com a mesma data do Hydro selecionado
    if ($id_conjunto_hydro_bb == null) {
        $campo_hydro_bb = getCampoEquivalente($campo_hydro["tempoinicio"], $campo_hydro["id_regiao_fk"], "HydroScat");
        if($campo_hydro_bb != null){
            $conjunto_hydro_bb = getConjuntoCampo($campo_hydro_bb["id"], array("bbuncorr"));
            $id_conjunto_hydro_bb = $conjunto_hydro_bb["id"];
        }
    }else{//$id_conjunto_hydro_bb DEFINIDO, PREENCHER CAMPO E CONJUNTO
        $campo_hydro_bb = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_hydro_bb'");
        $conjunto_hydro_bb = getConjuntoCampo($campo_hydro_bb["id"], array("bbuncorr"));
    }
    
    $campo_ctd = null;
    $conjunto_ctd = null;
    //se nenhum conjunto foi selecionado, então tentar buscar o conjunto do CTD com a mesma data do Hydro selecionado
    if ($id_conjunto_ctd == null) {
        $campo_ctd = getCampoEquivalente($campo_hydro["tempoinicio"], $campo_hydro["id_regiao_fk"], "CTD");
        if($campo_ctd != null){
            $conjunto_ctd = getConjuntoCampo($campo_ctd["id"]);
            $id_conjunto_ctd = $conjunto_ctd["id"];
        }
    }else{//$id_conjunto_hydro_bb DEFINIDO, PREENCHER CAMPO E CONJUNTO
        $campo_ctd = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_ctd'");
        $conjunto_ctd = getConjuntoCampo($campo_ctd["id"]);
    }
    
    
    $pagina->addAviso("Calculando bbb com sigma ($id_conjunto_hydro) no conjunto bb (".$id_conjunto_hydro_bb.")[" . $conjunto_hydro_bb["processamento"] . "] do HydroScat");

    //INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS	
    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());
    
    //pegando os dados do Hydro que serão corrigidos, após integração com CTD E ACS
    $sql = "SELECT h.*, hbb.intensidade as intensid, ctd.salinidade_psu as salinidade FROM d_medidas_hydroscat as h INNER JOIN d_medidas_hydroscat as hbb ON (h.comprimento_onda = hbb.comprimento_onda AND h.profundidade_m = hbb.profundidade_m) INNER JOIN d_medidas_ctd as ctd ON h.profundidade_m = ctd.profundidade_m WHERE h.tipo = 'sigma_cal' AND hbb.tipo = 'betabbuncorr' AND h.id_conjunto_fk = '$id_conjunto_hydro' AND hbb.id_conjunto_fk = '$id_conjunto_hydro_bb'";
    
    //trace0($sql);
    
    $rsHydro = pg_query($sql) or die(pg_last_error());
    
    //die("Selecionou ".  pg_num_rows($rsHydro));
    
    //criar novo conjunto de dados corrigido
    $id_conjunto_n = criaNovoConjunto($campo_hydro["id"], $conjunto_hydro["processamento"] . ", calculado Bbb", $id_conjunto_hydro);


    $rsAux = pg_query("SELECT * FROM d_retroespalhamento_agua ORDER BY comprimento_onda") or die(pg_last_error());
    $bbw = array();
    while ($linha = pg_fetch_assoc($rsAux)) $bbw[$linha["comprimento_onda"]] = $linha["bbw"];
    
    
    //constantes
    $hydro_corrigido = array();
    while ($linha = pg_fetch_assoc($rsHydro)) {
        $corrigido = array();
        $sigma = (float)$linha["intensidade"];
        $beta_uncorr = (float)$linha["intensid"];
        $c_onda = $linha["comprimento_onda"];
        $S = $linha["salinidade"];
        
        //de acordo com o manual
        //step of equation 8
        $beta = $sigma * $beta_uncorr;
        
        //Cálculo de betaW, de acordo com 
        // Emmanuel Boss and W. Scott Pegau. Relationship of light scattering at an angle in the backward direction to the backscattering coefficient. (2001)
        $delta = 0.09;
        $theta = 140;
        $theta_rad = deg2rad($theta);
        $e1 = 1.38 * pow($c_onda/500.0, -4.32);
        $e2 = (1 + (0.3*$S/37.0)) * 0.0001;
        $e3 = (1+ ((cos($theta_rad) * cos($theta_rad) * (1-$delta))/(1+$delta)));
        $betaW = $e1 * $e2 * $e3;

        //step of equation 9 
        $fator = 1.08;
        $cor = (2 * M_PI * $fator * ($beta - $betaW)) + $bbw[$c_onda];

        $corrigido["comprimento_onda"] = $linha["comprimento_onda"];
        $corrigido["tempo_ms"] = $linha["tempo_ms"];
        $corrigido["profundidade_m"] = $linha["profundidade_m"];
        $corrigido["intensidade"] = $cor;
        $corrigido["tipo"] = "bb_cal";
        $corrigido["id_conjunto_fk"] = $id_conjunto_n;


        $hydro_corrigido[] = $corrigido;
    }

    inserirRegistrosBanco("d_medidas_hydroscat", $hydro_corrigido);
    $pagina->addAviso(count($hydro_corrigido)." corrigidos e inseridos com sucesso");

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
    
}
/////***************************************************************************
function calculaBeta($id_conjunto_eco, $id_conjunto_eco_dark, $fs){
	global $pagina;
	
	$pagina->addAviso("Calculando Beta do campo $id_conjunto_eco usando o dark $id_conjunto_eco_dark");
	
	$campo_eco = getAtrsQuery("SELECT d_campo.*, d_regiao.id_reservatorio_fk FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk LEFT JOIN d_regiao ON (id_regiao_fk = d_regiao.id) WHERE d_conjunto.id = '$id_conjunto_eco'");
	$conjunto_eco = getConjuntoCampo($campo_eco["id"], array("count"));
	
	$campo_eco_dark= null;
	$conjunto_eco_dark = null;
	//se nenhum conjunto foi selecionado, então tentar buscar o conjunto do ECO DARK com a mesma data do ECO selecionado
	if ($id_conjunto_eco_dark== null) {
		$campo_eco_dark= getCampoEquivalente($campo_eco["tempoinicio"], $campo_eco["id_regiao_fk"], "EcoBB9", $campo_eco["id"], $campo_eco["id_reservatorio_fk"], "original dark");
		if($campo_eco_dark!= null){
			$conjunto_eco_dark= getConjuntoCampo($campo_eco_dark["id"], array("dark"));
			trace("Pegou");
			print_r($conjunto_eco_dark);
			$id_conjunto_eco_dark= $conjunto_eco_dark["id"];
		}
		else{
			trace("ERRO AO ENCONTRAR DARK");
			return;
		}
	}else{
		$campo_eco_dark= getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_eco_dark'");
		$conjunto_eco_dark= getConjuntoCampo($campo_eco_dark["id"], array("dark"));
	}
	
	
	$pagina->addAviso("Calculando Beta ($id_conjunto_eco) com conjunto [" . $conjunto_eco_dark["processamento"] . "] do ECO_DARK ($id_conjunto_eco_dark)");
	
	//INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS
	pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());
	
	//pegando os dados do ECO que serão corrigidos, após integração com ECO DARK
	$sql = "SELECT e.*, d.intensidade as dark FROM d_medidas_ecobb9 as e INNER JOIN d_medidas_ecobb9 as d ON (d.tempo_ms = '1' AND e.comprimento_onda = d.comprimento_onda) WHERE e.tipo = 'count' AND d.tipo = 'dark' AND d.id_conjunto_fk = '$id_conjunto_eco_dark' AND e.id_conjunto_fk = '$id_conjunto_eco'";
	
	//trace0($sql);
	
	$rsEco = pg_query($sql) or die(pg_last_error());
	
	//die("Selecionou ".  pg_num_rows($rsHydro));
	
	//criar novo conjunto de dados corrigido
	$id_conjunto_n = criaNovoConjunto($campo_eco["id"], $conjunto_eco["processamento"] . ", calculado Beta", $id_conjunto_eco);
	
	
	//constantes
	$eco_corrigido = array();
	while ($linha = pg_fetch_assoc($rsEco)) {
		$corrigido = array();
		$dark = (float)$linha["dark"];
		$count = (float)$linha["intensidade"];
		
		$beta = $fs[$linha["comprimento_onda"]] * ($count - $dark);
		
		//        trace0("beta:$beta; potencia: ".pow(M_E, 0.0391 * $a) . " ======= ".$cor);
		
		
		$corrigido["comprimento_onda"] = $linha["comprimento_onda"];
		$corrigido["tempo_ms"] = $linha["tempo_ms"];
		$corrigido["intensidade"] = $beta;
		$corrigido["tipo"] = "beta";
		$corrigido["id_conjunto_fk"] = $id_conjunto_n;
		
		
		$eco_corrigido[] = $corrigido;
	}
	
	inserirRegistrosBanco("d_medidas_ecobb9", $eco_corrigido);
	$pagina->addAviso(count($eco_corrigido)." corrigidos e inseridos com sucesso");
	
	pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
	
}


function calculaBetaCorr($id_conjunto_eco, $id_conjunto_acs){
	global $pagina;
	
	$pagina->addAviso("Calculando BetaCorr do campo $id_conjunto_eco");
	
	$campo_eco = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_eco'");
	$conjunto_eco = getConjuntoCampo($campo_eco["id"], array("beta"));
	
	$campo_acs = null;
	$conjunto_acs = null;
	//se nenhum conjunto foi selecionado, então tentar buscar o conjunto do ACS com a mesma data do ECO selecionado
	if ($id_conjunto_acs == null) {
		$campo_acs = getCampoEquivalente($campo_eco["tempoinicio"], $campo_eco["id_regiao_fk"], "ACS");
		if($campo_acs != null){
			$conjunto_acs = getConjuntoCampo($campo_acs["id"], array("a"));
			$id_conjunto_acs = $conjunto_acs["id"];
		}
		else{
			return;
		}
	}else{
		$campo_acs = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_acs'");
		$conjunto_acs = getConjuntoCampo($campo_acs["id"], array("a"));
	}
	
	
	
	$pagina->addAviso("Calculando BetaCorr ($id_conjunto_eco) com conjunto [" . $conjunto_acs["processamento"] . "] do ACS ($id_conjunto_acs)");
	
	//INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS
	pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());
	
	//pegando os dados do ECO que serão corrigidos, após integração com CTD E ACS
	$sql = "SELECT e.*, a.intensidade as a FROM d_medidas_ecobb9 as e INNER JOIN d_medidas_acs as a ON (a.tempo_ms = e.tempo_ms AND e.comprimento_onda = a.comprimento_onda) WHERE e.tipo = 'beta' AND a.tipo = 'a' AND a.id_conjunto_fk = '$id_conjunto_acs' AND e.id_conjunto_fk = '$id_conjunto_eco'";
	
	//trace0($sql);
	
	$rsEco = pg_query($sql) or die(pg_last_error());
	
	//die("Selecionou ".  pg_num_rows($rsHydro));
	
	//criar novo conjunto de dados corrigido
	$id_conjunto_n = criaNovoConjunto($campo_eco["id"], $conjunto_eco["processamento"] . ", calculado Beta corr", $id_conjunto_eco);
	
	
	//constantes
	$eco_corrigido = array();
	while ($linha = pg_fetch_assoc($rsEco)) {
		$corrigido = array();
		$a = (float)$linha["a"];
		$beta = (float)$linha["intensidade"];
		
		$cor = $beta * pow(M_E, 0.0391 * $a);
		
		//        trace0("beta:$beta; potencia: ".pow(M_E, 0.0391 * $a) . " ======= ".$cor);
		
		
		$corrigido["comprimento_onda"] = $linha["comprimento_onda"];
		$corrigido["tempo_ms"] = $linha["tempo_ms"];
		$corrigido["intensidade"] = $cor;
		$corrigido["tipo"] = "betacorr_cal";
		$corrigido["id_conjunto_fk"] = $id_conjunto_n;
		
		
		$eco_corrigido[] = $corrigido;
	}
	
	inserirRegistrosBanco("d_medidas_ecobb9", $eco_corrigido);
	$pagina->addAviso(count($eco_corrigido)." corrigidos e inseridos com sucesso");
	
	pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
	
}


function calculaBetaP($id_conjunto_eco, $id_conjunto_ctd, $salinidade = null){
    global $pagina;

    $pagina->addAviso("Calculando BetaP do campo $id_conjunto_eco");

    $campo_eco = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_eco'");
    $conjunto_eco = getConjuntoCampo($campo_eco["id"], array("betacorr_cal"));        
    
    $campo_ctd = null;
    $conjunto_ctd = null;
    //se nenhum conjunto foi selecionado, então tentar buscar o conjunto do ACS com a mesma data do ECO selecionado
    if($salinidade != null){
    	$id_conjunto_ctd = null;
    }
    else{
	    if ($id_conjunto_ctd == null){
	        $campo_ctd = getCampoEquivalente($campo_eco["tempoinicio"], $campo_eco["id_regiao_fk"], "CTD");
	        if($campo_ctd != null){
	            $conjunto_ctd = getConjuntoCampo($campo_ctd["id"]);
	            $id_conjunto_ctd = $conjunto_ctd["id"];
	        }
	    }else{//$id_conjunto_hydro_bb DEFINIDO, PREENCHER CAMPO E CONJUNTO
	        $campo_ctd = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_ctd'");
	        $conjunto_ctd = getConjuntoCampo($campo_ctd["id"]);                
	    }
    }

    //INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS	
    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());
    
    if($id_conjunto_ctd != null){
    	$pagina->addAviso("Calculando BetaP ($id_conjunto_eco) com conjunto [" . $conjunto_ctd["processamento"] . "] do CTD ($id_conjunto_ctd)");
    	//pegando os dados do ECO que serão corrigidos, após integração com CTD E ACS
    	$sql = "SELECT e.*, c.salinidade_psu as s FROM d_medidas_ecobb9 as e INNER JOIN d_medidas_ctd as c ON (e.tempo_ms = c.tempo_ms) WHERE e.tipo = 'betacorr_cal' AND e.id_conjunto_fk = '$id_conjunto_eco' AND c.id_conjunto_fk = '$id_conjunto_ctd'";
    }else{
    	$pagina->addAviso("Calculando BetaP ($id_conjunto_eco) com salinidade constante [" . $salinidade . "]");
    	$sql = "SELECT e.*, ".$salinidade." as s FROM d_medidas_ecobb9 as e WHERE e.tipo = 'betacorr_cal' AND e.id_conjunto_fk = '$id_conjunto_eco'";
    }
    
    
    
    //trace0($sql);
    
    $rsEco = pg_query($sql) or die(pg_last_error());
    
    //echo("Selecionou ".  pg_num_rows($rsEco));
    //return;
    //criar novo conjunto de dados corrigido
    $id_conjunto_n = criaNovoConjunto($campo_eco["id"], $conjunto_eco["processamento"] . ", calculado Beta P", $id_conjunto_eco);
    //$id_conjunto_n = 0;

    //constantes
    $eco_corrigido = array();
    while ($linha = pg_fetch_assoc($rsEco)) {
        $corrigido = array();
        $S = (float)$linha["s"];
        $betacorr = (float)$linha["intensidade"];
        $c_onda = $linha["comprimento_onda"];
        $delta = 0.09;
        $theta = 117;
        $theta_rad = deg2rad($theta);
        
        #Morell ou Boss
        $e1 = 1.38 * pow($c_onda/500.0, -4.32);
        $e2 = (1 + (0.3*$S/37.0)) * 0.0001;
        $e3 = (1+ ((cos($theta_rad) * cos($theta_rad) * (1-$delta))/(1+$delta)));
        $betaW = $e1 * $e2 * $e3;
        
        //$betaW = (1+ ((cos($theta_rad) * cos($theta_rad) * (1-$delta))/(1+$delta)));
        
        //$betaWsem = 1+ (   cos($theta_rad) * cos($theta_rad) * (1-$delta)  /   (1+$delta)  );
        
        //$betaWFer = 1+ ((cos($theta_rad) * cos($theta_rad)) * ((1-$delta)/(1+$delta)));
        
        //trace0("Diferença: ".($betaW - $betaW2));
        
        $cor = $betacorr - $betaW;
        
        //trace0("<br />(Salinidade, $S), (betacorr, $betacorr), (c_onda, $c_onda), (delta, $delta), (theta, $theta), (theta_rad, $theta_rad)");
        //trace0('betaW = 1.38 * pow($c_onda/500.0, -4.32) * pow((1 + (0.3*$S/37.0)), 0.0001) * (1+ ((cos($theta_rad) * cos($theta_rad) * (1-$delta))/(1+$delta)))');
        //trace0("E: ($e1, $e2, $e3)");
        //trace0($betacorr." - ".$betaW. " = ".$cor);
        
        $corrigido["comprimento_onda"] = $linha["comprimento_onda"];
        $corrigido["tempo_ms"] = $linha["tempo_ms"];
        $corrigido["intensidade"] = $cor;
        $corrigido["tipo"] = "betap_cal";
        $corrigido["id_conjunto_fk"] = $id_conjunto_n;


        $eco_corrigido[] = $corrigido;
    }

    inserirRegistrosBanco("d_medidas_ecobb9", $eco_corrigido);
    $pagina->addAviso(count($eco_corrigido)." corrigidos e inseridos com sucesso");

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
    
}


function calculaBbp($id_conjunto_eco, $X){
    global $pagina;

    $pagina->addAviso("Calculando bbp do campo $id_conjunto_eco");

    $campo_eco = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_eco'");
    $conjunto_eco = getConjuntoCampo($campo_eco["id"], array("betap_cal"));
    
    
    //$pagina->addAviso("Calculando bbp no conjunto ($id_conjunto_eco)");

    //INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS	
    pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());
    
    //pegando os dados do ECO que serão corrigidos, após integração com CTD E ACS
    $sql = "SELECT e.* FROM d_medidas_ecobb9 as e WHERE e.tipo = 'betap_cal' AND e.id_conjunto_fk = '$id_conjunto_eco'";
    
//    trace0($sql);
    
    $rsEco = pg_query($sql) or die(pg_last_error());
    
    //die("Selecionou ".  pg_num_rows($rsHydro));
    
    //criar novo conjunto de dados corrigido
    $id_conjunto_n = criaNovoConjunto($campo_eco["id"], $conjunto_eco["processamento"] . ", calculado bbp", $id_conjunto_eco);


    //constantes
    $eco_corrigido = array();
    while ($linha = pg_fetch_assoc($rsEco)) {
        $corrigido = array();
        $betap = (float)$linha["intensidade"];
        
        $cor = 2 * M_PI * $X * $betap;
        
        $corrigido["comprimento_onda"] = $linha["comprimento_onda"];
        $corrigido["tempo_ms"] = $linha["tempo_ms"];
        $corrigido["intensidade"] = $cor;
        $corrigido["tipo"] = "bbp_cal";
        $corrigido["id_conjunto_fk"] = $id_conjunto_n;


        $eco_corrigido[] = $corrigido;
    }

    inserirRegistrosBanco("d_medidas_ecobb9", $eco_corrigido);
    $pagina->addAviso(count($eco_corrigido)." corrigidos e inseridos com sucesso");

    pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());
    
}

function calculaBb($id_conjunto_eco){
	global $pagina;

	$campo_eco = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_eco'");
	$conjunto_eco = getConjuntoCampo($campo_eco["id"], array("bbp_cal"));


	$pagina->addAviso("Calculando bb no conjunto ($id_conjunto_eco)");

	//INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS
	pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());

	//pegando os dados do ECO que serão corrigidos, após integração com CTD E ACS
	$sql = "SELECT e.* FROM d_medidas_ecobb9 as e WHERE e.tipo = 'bbp_cal' AND e.id_conjunto_fk = '$id_conjunto_eco'";

	//    trace0($sql);

	$rsEco = pg_query($sql) or die(pg_last_error());

	//die("Selecionou ".  pg_num_rows($rsHydro));

	//criar novo conjunto de dados corrigido
	$id_conjunto_n = criaNovoConjunto($campo_eco["id"], $conjunto_eco["processamento"] . ", calculado bb", $id_conjunto_eco);


	//constantes
	$eco_corrigido = array();
	while ($linha = pg_fetch_assoc($rsEco)) {
		$corrigido = array();
		$bbp = (float)$linha["intensidade"];
		$c_onda = $linha["comprimento_onda"];

		$bw = 0.0022533 * pow($c_onda/500.0, -4.23);

		$cor = $bbp + $bw;//alterar aqui

		$corrigido["comprimento_onda"] = $linha["comprimento_onda"];
		$corrigido["tempo_ms"] = $linha["tempo_ms"];
		$corrigido["intensidade"] = $cor;
		$corrigido["tipo"] = "bb_cal";
		$corrigido["id_conjunto_fk"] = $id_conjunto_n;


		$eco_corrigido[] = $corrigido;
	}

	inserirRegistrosBanco("d_medidas_ecobb9", $eco_corrigido);
	$pagina->addAviso(count($eco_corrigido)." corrigidos e inseridos com sucesso");

	pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());

}
//To-DO, calcular reflectancia
//(Lu - (rho x Ls))/(pi x Lg)
function calculaReflectanciaMobley($id_conjunto, $rho){
	global $pagina;
	
	$campo_acs = getCampoEquivalente($campo_eco["tempoinicio"], $campo_eco["id_regiao_fk"], "ACS");
	if($campo_acs != null){
		$conjunto_acs = getConjuntoCampo($campo_acs["id"], array("a"));
		$id_conjunto_acs = $conjunto_acs["id"];
	}
	else{
		return;
	}

	$campo = getAtrsQuery("SELECT d_campo.* FROM d_campo LEFT JOIN d_conjunto ON d_campo.id = d_conjunto.id_campo_fk WHERE d_conjunto.id = '$id_conjunto_eco'");
	$conjunto = getConjuntoCampo($campo["id"], array("Ls"));
	
	$id_conjunto_Ls = $conjunto["id"]; 


	$pagina->addAviso("Calculando Reflecntância no conjunto ($id_conjunto_Ls)");

	//INICIA CORREÇÃO APOS DEFINICAO DE TODOS OS PARAMETROS
	pg_query("BEGIN") or die("Could not start transaction\n" . pg_last_error());

	//pegando os dados do ECO que serão corrigidos, após integração com CTD E ACS
	$sql = "SELECT e.* FROM 
					d_medidas_trios as Lu 
					LEFT JOIN d_medidas_trios as Ls ON Lu.id_conjunto_fk = Ls.id_conjunto_fk
					LEFT JOIN d_medidas_trios as Lg ON Ls.id_conjunto_fk = Lg.id_conjunto_fk
				WHERE 
					Lu.tipo = 'Lu' AND
					Ls.tipo = 'Ls' AND
					Lg.tipo = 'Lg' AND
					Ls.id_conjunto_fk = '$id_conjunto_Ls'";

	//    trace0($sql);

	$rsEco = pg_query($sql) or die(pg_last_error());

	//die("Selecionou ".  pg_num_rows($rsHydro));

	//criar novo conjunto de dados corrigido
	$id_conjunto_n = criaNovoConjunto($campo_eco["id"], $conjunto_eco["processamento"] . ", calculado bb", $id_conjunto_eco);


	//constantes
	$eco_corrigido = array();
	while ($linha = pg_fetch_assoc($rsEco)) {
		$corrigido = array();
		$bbp = (float)$linha["intensidade"];
		$c_onda = $linha["comprimento_onda"];

		$bw = 0.0022533 * pow($c_onda/500.0, -4.23);

		$cor = $bbp + $bw;//alterar aqui

		$corrigido["comprimento_onda"] = $linha["comprimento_onda"];
		$corrigido["tempo_ms"] = $linha["tempo_ms"];
		$corrigido["intensidade"] = $cor;
		$corrigido["tipo"] = "bb_cal";
		$corrigido["id_conjunto_fk"] = $id_conjunto_n;


		$eco_corrigido[] = $corrigido;
	}

	inserirRegistrosBanco("d_medidas_ecobb9", $eco_corrigido);
	$pagina->addAviso(count($eco_corrigido)." corrigidos e inseridos com sucesso");

	pg_query("COMMIT") or die("Transaction commit failed\n" . pg_last_error());

}



function exportarConjunto($id_conjunto, $conjuntos, $download = true){
    	global $pagina, $tabulacao, $integracao;
        //$pagina->addAviso("Exportar");
	
        //pegar tudo a partir do conjunto
        $a_conjunto = getAtrsQuery("SELECT * FROM d_conjunto WHERE id = '$id_conjunto'");
        //$id_conjunto = $a_conjunto["id"];
        $id_campo = $a_conjunto["id_campo_fk"];
        $a_campo = getAtrsQuery("SELECT * FROM d_campo WHERE id = '$id_campo'");
        $sensor = getSensorCampo($id_campo);
        
        //$tabela = "d_medidas_$sensor";
        $tabela = $tabulacao[$sensor]["tabela"];
        $consulta = $tabulacao[$sensor]["consulta"];
        
	$linha = $tabulacao[$sensor]["linha"];
	$coluna = $tabulacao[$sensor]["coluna"];
	$celula = $tabulacao[$sensor]["celula"];	
        
        //echo "aqui ".count($tabulacao);
        //print_r($celula);
		
	if($celula != null) $sel = array_merge($coluna, $linha, $celula);
	else $sel = array_merge($coluna, $linha);
	$ord = array_merge($linha, $coluna);
			
	
			
		$join_i = "";
		$sel_i = array();
		$ord_i = array();
		$strOnd = "";
		
		$strSel = "";
		$strOrd = "";
		for($i=0; $i< count($conjuntos); $i++){
			$conjunto_i = $conjuntos[$i];
                        $campo_i = (int)getAtrQuery("SELECT id_campo_fk FROM d_conjunto WHERE id = '$conjunto_i'");;
			if( $campo_i > 0){//integração selecionada
				$equip_i = getEquipCampo($campo_i);
				$sensor_i = getSensorCampo($campo_i);
				$tabela_i = "d_medidas_$sensor_i";
				
				
				if(isset($integracao[$sensor][$sensor_i])){//possível integrar estes sensores
					//trace0("Integrando $sensor e $sensor_i");
					$key1 = $integracao[$sensor][$sensor_i][0];
					$key2 = $integracao[$sensor][$sensor_i][1];
					
					$linha_i = $tabulacao[$sensor_i]["linha"];
					$coluna_i = $tabulacao[$sensor_i]["coluna"];
					$celula_i = $tabulacao[$sensor_i]["celula"];	
					
					$join_i .= " INNER JOIN $tabela_i ON $tabela.$key1 = $tabela_i.$key2 ";
					$ord_i = array_merge($linha_i, $coluna_i);
					if($celula_i != null) 
						$sel_i = array_merge($coluna_i, $linha_i, $celula_i);
					else 
						$sel_i = array_merge($coluna_i, $linha_i);
					
					$strOnd .= " AND $tabela_i.id_conjunto_fk = '$conjunto_i'";
					
					$strSel = ", $tabela_i.".implode2D(", $tabela_i.", $sel_i);
					$strOrd = ", $tabela_i.".implode2D(", $tabela_i.", $ord_i);
						
					
				}
				else{
					trace0("Atenção! Não é possível integrar os dados do sensor $sensor com o $sensor_i");
					$pagina->addAdvertencia("Atenção! Não é possível integrar os dados do sensor $sensor com o $sensor_i");
				}
			}
		}
		
		//return;
		//$sql = "SELECT * FROM $tabela as t1 $join_i WHERE AND t1.id_conjunto_fk = '$id_conjunto' $strOnd ORDER BY t1.".implode2D(", t1.", $ord) . " $strOrd ";
		
		//$sql = "SELECT * FROM $consulta $tabela $join_i WHERE tipo = 'Ed+' AND $tabela.id_conjunto_fk = '$id_conjunto' $strOnd ORDER BY $tabela.".implode2D(", $tabela.", $ord) . " $strOrd ";
		$sql = "SELECT * FROM $consulta $tabela $join_i WHERE  $tabela.id_conjunto_fk = '$id_conjunto' $strOnd ORDER BY $tabela.".implode2D(", $tabela.", $ord) . " $strOrd";
		//trace0($sql);
	
	//die();
		
		pg_query("BEGIN") or die("1Could not start transaction\n". pg_last_error());
		pg_query("SET statement_timeout TO 1800000") or die(pg_last_error());
		$rsMed = pg_query($sql) or die(pg_last_error()."<br /><br />:::::::::::::<br /><br /> $sql");
		pg_query("COMMIT") or die("1Could not start transaction\n". pg_last_error());
		
		
		//echo "Encontrados ".pg_num_rows($rsMed)." linhas<br /><br />";
		//die();
		$dados = pg_fetch_all($rsMed);//transformando todas as tuplas em um vetor
		//////////////////////////////////////// construir tabelas, pois já tem os dados
		$tabs = array();
		//die("Dados: ".count($dados));
		$linha = $tabulacao[$sensor]["linha"];
		$coluna = $tabulacao[$sensor]["coluna"];
		$celula = $tabulacao[$sensor]["celula"];		
		
		$tab = construirMatriz($linha, $coluna, $celula, $dados);
	//trace0("Construi Tab com ".count($tab)." linhas");
	//die();
		$tabs[$sensor] = $tab;
		
		for($i=0; $i< count($conjuntos); $i++){
			$conjunto_i = $conjuntos[$i];
                        $campo_i = (int)getAtrQuery("SELECT id_campo_fk FROM d_conjunto WHERE id = '$conjunto_i'");
			if( $campo_i > 0){//integração selecionada
				$equip_i = getEquipCampo($campo_i);
				$sensor_i = getSensorCampo($campo_i);
				$tabela_i = "d_medidas_$sensor_i";
				
				
				if(isset($integracao[$sensor][$sensor_i])){//possível integrar estes sensores
					$linha_i = $tabulacao[$sensor_i]["linha"];//mesma chave entre os sensores
					$coluna_i = $tabulacao[$sensor_i]["coluna"];
					//echo "Aqui: ".$tabulacao[$sensor_i]["linha"][0][0];
					//$coluna_i[] = $tabulacao[$sensor_i]["linha"][0];//adicionar a ex-chave a lista de campos
					$celula_i = $tabulacao[$sensor_i]["celula"];	
					
					$tab = construirMatriz($linha_i, $coluna_i, $celula_i, $dados);
	//			trace0("Construi Tab com ".count($tab)." linhas");
					$tabs[$sensor_i] = $tab;
				}
			}
		}
		/*
		//JUNTAR TABELAS DO JEITO CERTO
		$uni_tab = array();
		$sensores_integrados = array();
		foreach($tabs as $sensor => $tab){//cada tab é uma matriz 2d
			$chave = null;
			foreach($sensores_integrados as $s){//buscando a chave entre os dados já inclusos na tabela
				if(isset($integracao[$s][$sensor])){
					$chave = $integracao[$s][$sensor];
					break;
				}
			}
			$uni_tab = integraTabs($uni_tab, $tab, $chave);
			$sensores_integrados[] = $sensor;//adicionando novo sensor
		}*/
		
		
		// JUNTAR TABELAS
		$uni_tab = array();
		foreach($tabs as $tab){//cada tab é uma matriz 2d
	//		printTable($tab);
			foreach($tab as $key => $linha){
				if(!isset($uni_tab[$key])) $uni_tab[$key] = array();
				//errado, juntar as 3 considerando a chave
				$uni_tab[$key] = array_merge($uni_tab[$key], $linha);
			}
			
		}
		
                //trace0("SELECT d_reservatorio.nome FROM d_campo LEFT JOIN d_regiao ON (id_regiao_fk = d_regiao.id) LEFT JOIN d_equipamento ON (id_equipamento_fk = d_equipamento.id) LEFT JOIN d_reservatorio ON (id_reservatorio_fk = d_reservatorio.id) WHERE d_campo.id = '$id_campo'");
                $reservatorio = getAtrQuery("SELECT d_reservatorio.sigla FROM d_campo LEFT JOIN d_regiao ON (id_regiao_fk = d_regiao.id) LEFT JOIN d_equipamento ON (id_equipamento_fk = d_equipamento.id) LEFT JOIN d_reservatorio ON (id_reservatorio_fk = d_reservatorio.id) WHERE d_campo.id = '$id_campo'");
                $data_campo = date("Ymd", strtotime($a_campo["tempoinicio"]));
                $sensor_s = getAtrQuery("SELECT d_equipamento.nome FROM d_campo LEFT JOIN d_regiao ON (id_regiao_fk = d_regiao.id) LEFT JOIN d_equipamento ON (id_equipamento_fk = d_equipamento.id) LEFT JOIN d_reservatorio ON (id_reservatorio_fk = d_reservatorio.id) WHERE d_campo.id = '$id_campo'");
                $ponto = getAtrQuery("SELECT d_regiao.nome FROM d_campo LEFT JOIN d_regiao ON (id_regiao_fk = d_regiao.id) LEFT JOIN d_equipamento ON (id_equipamento_fk = d_equipamento.id) LEFT JOIN d_reservatorio ON (id_reservatorio_fk = d_reservatorio.id) WHERE d_campo.id = '$id_campo'");
                $p = explode(',', $a_conjunto["processamento"]);
                $processamento = end($p); 
                //die($processamento);
		//$tab = construirMatriz($linha, $coluna, $celula, $dados);
		$arquivo = $data_campo."_".$sensor_s."_".$reservatorio."_".$ponto."_".$processamento;
                //trace0($arquivo);
		
		//printTable($uni_tab);
		
                exportarMatriz($uni_tab, $arquivo, $download);
		
		//printTable($uni_tab);

}


//exportar todos os pontos que tenham estes processamentos

//pegar os campos
	//todas as regioes
	//deste reservatorio
	//desta data
	//deste equipamento
	//que tenha conjunto
		//com este processamento

function exportarConjuntoPontos($id_conjunto, $id_conjuntos_integrados = array()){
    global $pagina;
        
    gravaLog("Processamento em lote Iniciado. Exportação de todos os pontos");
        
        array_map('unlink', glob("/tmp/*.csv"));
        array_map('unlink', glob("/tmp/*.xls"));
        array_map('unlink', glob("/tmp/*.zip"));
        
    $conjunto = getConjunto($id_conjunto);
    $campo = getCampo($conjunto["id_campo_fk"]);
    					
	$conjuntos_integrados = array();										
	foreach($id_conjuntos_integrados as $id_int){
		$conjuntos_integrados[] = getAtrsQuery("SELECT * FROM d_conjunto LEFT JOIN d_campo ON id_campo_fk = d_campo.id WHERE d_conjunto.id = '$id_int'");
	}
	//trace("Conjunto");
    //print_r($conjunto);
    //trace("Campo");
    //print_r($campo);
	
	$rsCampos = pg_query("SELECT d_campo.* FROM d_campo LEFT JOIN d_regiao ON id_regiao_fk = d_regiao.id WHERE  id_reservatorio_fk = '" . $campo["id_reservatorio_fk"] . "' AND (tempoinicio BETWEEN timestamp '" . $campo["tempoinicio"] . "' - interval '5 days' AND  timestamp '" . $campo["tempoinicio"] . "' + interval '5 days') AND id_equipamento_fk = '".$campo["id_equipamento_fk"]."'") or die(pg_last_error());
	
	gravaLog("Quantidade de campos encontrados: ".pg_num_rows($rsCampos));
	
	//trace0("SQL: "."SELECT d_campo.* FROM d_campo LEFT JOIN d_regiao ON id_regiao_fk = d_regiao.id WHERE  id_reservatorio_fk = '" . $campo["id_reservatorio_fk"] . "' AND tempoinicio = '" . $campo["tempoinicio"] . "' AND id_equipamento_fk = '".$campo["id_equipamento_fk"]."'");

	for($i = 0; ($linha = pg_fetch_assoc($rsCampos))/* && $i < 10*/; $i++){
		$id_campo = $linha["id"];
		$conjunto_i = getConjuntoProcessadoCampo($id_campo, $conjunto["processamento"]);
		$campo_i = getCampo($id_campo);
		$id_conjunto = $conjunto_i["id"];
		
		$id_conjuntos_int = array();
		foreach($conjuntos_integrados as $int){
			$id_conjuntos_int[] = getAtrQuery("SELECT d_conjunto.id FROM d_conjunto LEFT JOIN d_campo ON id_campo_fk = d_campo.id WHERE (tempoinicio BETWEEN timestamp '" . $campo["tempoinicio"] . "' - interval '5 days' AND  timestamp '" . $campo["tempoinicio"] . "' + interval '5 days') AND id_regiao_fk = '".$campo_i["id_regiao_fk"]."' AND id_equipamento_fk = '".$int["id_equipamento_fk"]."' AND processamento = '".$int["processamento"]."' ");
		}
		
		gravaLog("Exportando conjunto $id_conjunto, integrado com ".implode(",", $id_conjuntos_int));
		
		
		exportarConjunto($id_conjunto, $id_conjuntos_int, false);
	}
	
		
		$tmp_dir = "/tmp/";
		$html_dir = "/var/www/html/sensors/tmp/";
	
	
		gravaLog("Criando arquivo compactado: ZIP");
        exec("zip $tmp_dir/output.zip $tmp_dir*.csv");
        print_r(exec("cp ${tmp_dir}output.zip ${html_dir} 2>&1"));  
          
        
        gravaLog("Criando planilha: XLS");
        $command = escapeshellcmd('/var/www/html/sensors/config/bibliotecas_python/juntar_csv_xls.py');
        exec("python " . $command. " 2>&1", $output);
        //trace0($output);
        print_r($output);
        $arq_out = $output[count($output) - 1];
        //gravaLog("Salvo em $arq_out");
        
        exec("cp ${tmp_dir}$arq_out ${html_dir} 2>&1");
        //print_r(exec("cp ${tmp_dir}$arq_out ${html_dir} 2>&1"));
        
        $pagina->addAdvertencia("Download <a href='../tmp/'>aqui</a>");
        
	//gravaLog("Processamento em lote Hydro Finalizado. Calculo do Sigma");
        
        //die();
}




?>
