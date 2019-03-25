<?
include_once "../paginas/funcoes_dados_processar.php";


function pulaLinhaArquivoToken($file, $token){
	//PULAR LINHAS ATÉ ENCONTRAR UM TOKEN
	$init_key = $token;//palavra da linha anterior à linha válida
	while (!feof($file)) {
		$line_of_text = fgetcsv($file, 0, ",", '"');
		for($i = 0; $i < count($line_of_text); $i++){
			if($line_of_text[$i] == $init_key) return;
		}
	}
}

function pulaLinhaArquivoQuantidade($file, $qtd){
	//PULAR UMA QUANTIDADE X DE LINHAS
	$l = 0;
	while (!feof($file) && ++$l < $qtd)fgetcsv($file, 0);
	
	return;
}

function printFileCSV($file, $limit = null, $separador = ',', $enclosure = '"'){
	
	
	echo "<table border=1>";
	
	
	$l = 0;
	while (!feof($file) && ($limit == null || $l++ < $limit)) {

		$line_of_text = fgetcsv($file, 0, $separador, $enclosure);
		
		echo "<tr>";
		
		for($i = 0; $i < count($line_of_text); $i++){
			echo "<td>";
			$value = $line_of_text[$i];
			if(is_numeric($value)) $value = (float)$value;
			echo $value;
			echo "</td>";
		}
		
		echo "</tr>";

	}
	
	echo "</table>";
}
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
function lerAcs($id_cam, $caminho_arquivo, $separador, $linha_rotulos){
	$file_handle = fopen($caminho_arquivo, "r");
	
	pulaLinhaArquivoQuantidade($file_handle, $linha_rotulos);
	
	pg_query("BEGIN; SET statement_timeout TO 1800000") or die("54df3Could not start transaction\n". pg_last_error());		
	
	$rsCon = pg_query("INSERT INTO d_conjunto (id_campo_fk, processamento, id_conjuntopai_fk) VALUES ('$id_cam', 'original', null) RETURNING id") or die(pg_last_error());
	$row = pg_fetch_row($rsCon);  
	$id_conjunto = $row['0'];
	
	
	//printFileCSV($file_handle, 10, $separador);
	
	//linha de cabeçalho
	$line_of_text = fgetcsv($file_handle, 0, $separador);
	$campos = array();
	for($i = 0; $i < count($line_of_text); $i++){//pulando o tempo
		$medida = array();
		$medida["tipo"] = substr($line_of_text[$i], 0, 1);//primeiro caractere
		$medida["c_onda"] = (float)substr($line_of_text[$i], 1);//a partir do segundo
		if(($medida["tipo"] == "a" || $medida["tipo"] == "c") && $medida["c_onda"] > 0 )//apenas variáveis válidas
			$campos[$i] = $medida;
		else
			$campos[$i] = null;
	}
	//linhas de dados
	$quant = 0;
	for ($linha = 0;!feof($file_handle)/* && $quant < 800*/; $linha++) {
		//trace("[".date("d/m/Y H:i:s")."]ACS: Processando linha $linha");
		$line_of_text = fgetcsv($file_handle, 0, $separador);
		
		$time = $line_of_text[0];
		for($i = 1; $i < count($line_of_text); $i++){//para cada parâmetro da linha
			if($campos[$i] != null){
				$tipo = $campos[$i]["tipo"];
				$c_onda = $campos[$i]["c_onda"];
				$value = (float)$line_of_text[$i];
				
					$valores[] = array(
										"id_conjunto_fk" => $id_conjunto, 
										"tempo_ms" => $time, 
										"comprimento_onda" => $c_onda, 
										"intensidade" => $value, 
										"tipo" => $tipo
					);
				
				
				//$sql = "INSERT INTO d_medidas_acs (id_conjunto_fk, tempo_ms, comprimento_onda, intensidade, tipo) VALUES ('$id_conjunto', '$time', '$c_onda', '$value', '$tipo')";
				//pg_query($sql) or die(pg_last_error() . " ::::: ".$sql);
				$quant++;
			}
		}

	} 
	
	inserirRegistrosBanco("d_medidas_acs", $valores);

	fclose($file_handle);
	
	pg_query("COMMIT") or die("1432Could not start transaction\n". pg_last_error());	
	
	return $quant;
}

function lerCtd($id_cam, $caminho_arquivo, $separador, $linha_rotulos){
	$file_handle = fopen($caminho_arquivo, "r");
	
	pg_query("BEGIN; SET statement_timeout TO 1800000") or die("543Could not start transaction\n". pg_last_error());
	
	$rsCon = pg_query("INSERT INTO d_conjunto (id_campo_fk, processamento, id_conjuntopai_fk) VALUES ('$id_cam', 'original', null) RETURNING id") or die(pg_last_error());
	$row = pg_fetch_row($rsCon);
	$id_conjunto = $row['0'];
	
	
	pulaLinhaArquivoQuantidade($file_handle, $linha_rotulos);
	
	//printFileCSV($file_handle, 10, $separador);
	
	//linha de cabeçalho
	$line_of_text = fgetcsv($file_handle, 0, $separador);
	//linhas de dados
	$quant = 0;
	$valores = array();
	for ($linha = 0;!feof($file_handle)/* && $quant < 800*/; $linha++) {
		//trace("[".date("d/m/Y H:i:s")."]CTD: Processando linha $linha");
		$line_of_text = fgetcsv($file_handle, 0, $separador);
		
		$time = (float)$line_of_text[0];
		$pressao = (float)$line_of_text[1];
		$temperatura = (float)$line_of_text[2];
		$condutividade = (float)$line_of_text[3];
		$salinidade = (float)$line_of_text[4];
		
		$valores[] = array(
				"id_conjunto_fk" => $id_conjunto,
				"tempo_ms" => $time,
				"pressao_dbar" => $pressao,
				"temperatura_c" => $temperatura,
				"condutividade_sm" => $condutividade,
				"salinidade_psu" => $salinidade
		);
		
		//$sql = "INSERT INTO d_medidas_ctd (id_conjunto_fk, tempo_ms, pressao_dbar, temperatura_c, condutividade_sm, salinidade_psu) VALUES ('$id_conjunto', '$time', '$pressao', '$temperatura', '$condutividade', '$salinidade')";
		//pg_query($sql) or die(pg_last_error() . " ::::: ".$sql);
		$quant++;
		
	}
	
	
	fclose($file_handle);
	
	inserirRegistrosBanco("d_medidas_ctd", $valores);
	pg_query("COMMIT") or die("1432Could not start transaction\n". pg_last_error());
	
	return $quant;
}

function fgetcol($file_handle, $separador, $trim, $del_char_arr){
	$linha = fgets($file_handle);
	//trace($linha);
	if($trim)
		$linha= preg_replace('/\s+/', ' ',$linha);
	$linha = str_replace($del_char_arr, "", $linha);
	$v = explode($separador, $linha);
	return $v;
}

function lerCtdRaw($id_cam, $caminho_arquivo, $separador, $trim, $del_char_arr){
	$file_handle = fopen($caminho_arquivo, "r");
	
	pg_query("BEGIN; SET statement_timeout TO 1800000") or die("543Could not start transaction\n". pg_last_error());
	
	$rsCon = pg_query("INSERT INTO d_conjunto (id_campo_fk, processamento, id_conjuntopai_fk) VALUES ('$id_cam', 'original', null) RETURNING id") or die(pg_last_error());
	$row = pg_fetch_row($rsCon);
	$id_conjunto = $row['0'];
	
	
	//pulaLinhaArquivoQuantidade($file_handle, $linha_rotulos);
	
	//printFileCSV($file_handle, 10, $separador);
	
	//linha de cabeçalho
	//$line_of_text = fgetcol($file_handle, $separador, $trim, $del_char_arr);
	//linhas de dados
	$quant = 0;
	$valores = array();
	for ($linha = 0;!feof($file_handle)/* && $quant < 800*/; $linha++) {
		//trace("[".date("d/m/Y H:i:s")."]CTD: Processando linha $linha");
		$line_of_text = fgetcol($file_handle, $separador, $trim, $del_char_arr);
		
		$time = (float)$line_of_text[0];
		if(!($time > 0)) break;
		$T = $temperatura = (float)$line_of_text[1];
		$C = $condutividade= (float)$line_of_text[2];
		$P = $pressao = (float)$line_of_text[3];
		$S = null;
		#################
		//$salinidade = CALCULAR SALINIDADEEEEEEEEEEEEEE
		$C_35_15_0 = 42.914;
		
		$A = array();
		$A[1] = 2.070E-5;
		$A[2] = -6.370E-10;
		$A[3] = 3.989E-15;
		
		$B = array();
		$B[1] = 3.426E-2;
		$B[2] = 4.464E-4;
		$B[3] = 4.215E-1;
		$B[4] = -3.107E-3;
		
		$c = array();
		$c[0] = 6.766097E-1;
		$c[1] = 2.00564E-2;
		$c[2] = 1.104259E-4;
		$c[3] = -6.9698E-7;
		$c[4] = 1.0031E-9;
		
		$a = array();
		$a[0] = 0.0080;
		$a[1] = -0.1692;
		$a[2] = 25.3851;
		$a[3] = 14.0941;
		$a[4] = -7.0261;
		$a[5] = 2.7081;
		
		$b = array();
		$b[0] = 0.0005;
		$b[1] = -0.0056;
		$b[2] = -0.0066;
		$b[3] = -0.0375;
		$b[4] = 0.0636;
		$b[5] = -0.0144;
		
		$k = 0.0162;
		
		$R = $C/$C_35_15_0;
		
		$Rp = 1 + ($P * ($A[1] + $A[2]*$P + $A[3]*pow($P, 2))) / (1 + $B[1]*$T +$B[2]*pow($T, 2) + $B[3]*$R + $B[4]*$R*$T);
		
		$rT = $c[0] + $c[1]*$T + $c[2]*pow($T, 2) + $c[3]*pow($T, 3) + $c[4]*pow($T, 4);
		
		$RT = $R/($Rp*$rT);
		
		$S1 = 0;
		for($j=0; $j<=5; $j++) $S1 += $a[$j] * pow($RT, $j/2.0);
		$S2 = ($T - 15) / (1 + $k*($T-15));
		$S3 = 0;
		for($j=0; $j<=5; $j++) $S3 += $b[$j] * pow($RT, $j/2.0);
		$S = $S1 + $S2*$S3;
		################################################
		//trace($S);
		$salinidade = $S;
		$v = array(
				"id_conjunto_fk" => $id_conjunto,
				"tempo_ms" => $time,
				"pressao_dbar" => $pressao,
				"temperatura_c" => $temperatura,
				"condutividade_sm" => $condutividade,
				"salinidade_psu" => $salinidade
		);
		$valores[] = $v; 
		//trace(implode(', ', $v));
		
		//$sql = "INSERT INTO d_medidas_ctd (id_conjunto_fk, tempo_ms, pressao_dbar, temperatura_c, condutividade_sm, salinidade_psu) VALUES ('$id_conjunto', '$time', '$pressao', '$temperatura', '$condutividade', '$salinidade')";
		//pg_query($sql) or die(pg_last_error() . " ::::: ".$sql);
		$quant++;
		
	}
	
	
	fclose($file_handle);
	
	inserirRegistrosBanco("d_medidas_ctd", $valores);
	pg_query("COMMIT") or die("1432Could not start transaction\n". pg_last_error());
	
	return $quant;
}


function lerEco($id_cam, $caminho_arquivo, $separador, $linha_rotulos){
	$file_handle = fopen($caminho_arquivo, "r");
	
	pulaLinhaArquivoQuantidade($file_handle, $linha_rotulos);
	
	pg_query("BEGIN; SET statement_timeout TO 1800000") or die("543Could not start transaction\n". pg_last_error());
	$rsCon = pg_query("INSERT INTO d_conjunto (id_campo_fk, processamento, id_conjuntopai_fk) VALUES ('$id_cam', 'original', null) RETURNING id") or die(pg_last_error());
	$row = pg_fetch_row($rsCon);
	$id_conjunto = $row['0'];
	
	
	//printFileCSV($file_handle, 10, $separador);
	
	//linha de cabeçalho
	$line_of_text = fgetcsv($file_handle, 0, $separador);
	$campos = array();
	for($i = 1; $i < count($line_of_text); $i++){//pulando o tempo
		$medida = array();
		$coluna = $line_of_text[$i];
		$medida["tipo"] = explode("(", $coluna)[0];
		$medida["c_onda"] = (float)str_replace(")", "", explode("(", $coluna)[1]);//a partir do segundo
		if($medida["c_onda"] > 0 )//apenas variáveis válidas
			$campos[$i] = $medida;
			else
				$campos[$i] = null;
	}
	
	$valores = array();
	
	//linhas de dados
	$quant = 0;
	for ($linha = 0;!feof($file_handle)/* && $quant < 800*/; $linha++) {
		//trace("[".date("d/m/Y H:i:s")."]ECO: Processando linha $linha");
		
		$line_of_text = fgetcsv($file_handle, 0, $separador);
		
		$time = $line_of_text[0];
		for($i = 1; $i < count($line_of_text); $i++){//para cada parâmetro da linha
			if($campos[$i] != null){
				$tipo = $campos[$i]["tipo"];
				$c_onda = $campos[$i]["c_onda"];
				$value = (float)$line_of_text[$i];
				
				$valores[] = array(
						"id_conjunto_fk" => $id_conjunto,
						"tempo_ms" => $time,
						"comprimento_onda" => $c_onda,
						"intensidade" => $value,
						"tipo" => $tipo
				);
				
				//$sql = "INSERT INTO d_medidas_ecobb9 (id_conunto_fk, tempo_ms, comprimento_onda, intensidade, tipo) VALUES ('$id_conjunto', '$time', '$c_onda', '$value', '$tipo')";
				//pg_query($sql) or die(pg_last_error() . " ::::: ".$sql);
				$quant++;
			}
		}
	}
	
	inserirRegistrosBanco("d_medidas_ecobb9", $valores);
	
	pg_query("COMMIT") or die("1436552Could not start transaction\n". pg_last_error());
	
	fclose($file_handle);
	
	return $quant;
}


function lerEcoRaw($id_cam, $caminho_arquivo, $separador, $tem_tempo, $tipo){
	$file_handle = fopen($caminho_arquivo, "r");
	
	//pulaLinhaArquivoQuantidade($file_handle, $linha_rotulos);
	
	pg_query("BEGIN; SET statement_timeout TO 1800000") or die("543Could not start transaction\n". pg_last_error());
	$rsCon = pg_query("INSERT INTO d_conjunto (id_campo_fk, processamento, id_conjuntopai_fk) VALUES ('$id_cam', 'original $tipo', null) RETURNING id") or die(pg_last_error());
	$row = pg_fetch_row($rsCon);
	$id_conjunto = $row['0'];
	
	
	//printFileCSV($file_handle, 10, $separador);
	
	
	$valores = array();
	
	//linhas de dados
	$quant = 0;
	$time = 0;
	for ($linha = 0;!feof($file_handle)/* && $quant < 800*/; $linha++) {
		//trace("[".date("d/m/Y H:i:s")."]ECO: Processando linha $linha");
		
		$line_of_text = fgetcsv($file_handle, 0, $separador);
		
		$primeira_col_componda = 4;
		
		if($tem_tempo == "1"){
			$time = $line_of_text[0];
			$primeira_col_componda = 5;
		}else{
			$time++;
			//trace($time);
		}
		for($i = $primeira_col_componda; $i+1 < count($line_of_text); $i+=2){//pulando coluna de dados, direto para o primeiro comprimento de onda
			$c_onda = $line_of_text[$i];
			$count = $line_of_text[$i+1];
			
			$valores[] = array(
					"id_conjunto_fk" => $id_conjunto,
					"tempo_ms" => $time,
					"comprimento_onda" => $c_onda,
					"intensidade" => $count,
					"tipo" => $tipo
			);
			$quant++;
			
		}
	}
	
	//print_r($valores);
	inserirRegistrosBanco("d_medidas_ecobb9", $valores);
	
	pg_query("COMMIT") or die("143fds6552Could not start transaction\n". pg_last_error());
	
	fclose($file_handle);
	
	return $quant;
}



function lerHydro($id_cam, $caminho_arquivo, $separador, $linha_rotulos){
	$file_handle = fopen($caminho_arquivo, "r");
pg_query("BEGIN; SET statement_timeout TO 1800000") or die("543Could not start transaction\n". pg_last_error());		
	$rsCon = pg_query("INSERT INTO d_conjunto (id_campo_fk, processamento, id_conjuntopai_fk) VALUES ('$id_cam', 'original', null) RETURNING id") or die(pg_last_error());
	$row = pg_fetch_row($rsCon);  
	$id_conjunto = $row['0'];
		
		
	pulaLinhaArquivoQuantidade($file_handle, $linha_rotulos);
	
	//printFileCSV($file_handle, 10, $separador);

//linha de cabeçalho
	//linha de cabeçalho
	$line_of_text = fgetcsv($file_handle, 0, $separador);
	$campos = array();
	for($i = 0; $i < count($line_of_text); $i++){//pulando o tempo e a profundidade
		$medida = array();
		$coluna = $line_of_text[$i];
		$medida["c_onda"] = (int)inteiro($coluna);//a partir do segundo
		$medida["tipo"] = str_replace($medida["c_onda"], "", $coluna);
		if($medida["c_onda"] > 0 )//apenas variáveis válidas
			$campos[$i] = $medida;
		else
			$campos[$i] = null;
	}
	//linha de TAG
	$line_of_text = fgetcsv($file_handle, 0, $separador);
	//linhas de dados
	$quant = 0;
	$valores = array();
	for ($linha = 0;!feof($file_handle)/* && $quant < 800*/; $linha++) {
		$line_of_text = fgetcsv($file_handle, 0, $separador);
		if(count($line_of_text) > 1){
			trace("[".date("d/m/Y H:i:s")."]Hydro: Processando linha $linha [".implode(", ", $line_of_text)."]");
			
			$time = $line_of_text[0];
			$profundidade = $line_of_text[1];
			for($i = 0; $i < count($line_of_text); $i++){//para cada parâmetro da linha
				if($campos[$i] != null){
					$tipo = $campos[$i]["tipo"];
					$c_onda = $campos[$i]["c_onda"];
					$value = (float)$line_of_text[$i];
					$valores[] = array(
										"id_conjunto_fk" => $id_conjunto, 
										"tempo_ms" => $time, 
										"comprimento_onda" => $c_onda, 
										"intensidade" => $value, 
										"tipo" => $tipo, 
										"profundidade_m" => $profundidade
					);
					/*$sql = "INSERT INTO d_medidas_hydroscat
									(id_conjunto_fk, tempo_ms, comprimento_onda, intensidade, tipo, profundidade_m) VALUES 
									('$id_conjunto', '$time', '$c_onda', '$value', '$tipo', '$profundidade')";
					
					if(!pg_query($sql)){
						pg_query("ROLLBACK") or die("Transaction rollback failed1\n".pg_last_error());
						die(pg_last_error() . " ::::: ".$sql);
					}*/
					$quant++;
				}
			}
		}
	}
	//printTable($valores);
	//pg_query("ROLLBACK") or die("Transaction rollback failed1\n".pg_last_error()); die();
	inserirRegistrosBanco("d_medidas_hydroscat", $valores);
	
	fclose($file_handle);
pg_query("COMMIT") or die("1432Could not start transaction\n". pg_last_error());	
	return $quant;
}

function getNomeArquivoPath($path){
	//removendo o path, caso exista
	$nome_original = explode('/', $path);
	$nome_original = $nome_original[count($nome_original)-1];
	
	return $nome_original;
	
}

function novoNomeArquivo($nome_original){
	
	
	$partes = explode(".", strtolower($nome_original));
	$nome = $partes[0];
	$extensao = $partes[1];
	$nome_novo = $nome.".".$extensao.".".date("YmdHis");
	
	return $nome_novo;
}

function novoNomeDir($nome_original){
	$nome_novo = $nome_original."_".date("YmdHis")."/";
	
	return $nome_novo;
}

function extrairTempoIDTrios($IDData){
	$missao_data = preg_replace("/_(\d*)_(\d*)$/", "", $IDData);
	preg_match("/\d*-\d*-\d*_\d*-\d*-\d*$/", $missao_data, $v_data);
	$data = $v_data[0];
	
	return $data;
}

function extrairVariaveis($mascara, $texto){
	//echo "<br/>extraindo [$mascara] de '$texto'";
	$padrao_reg = '/\$\{([^\}\{\$]*)\}/';
	
	//encontrando as variaveis
	preg_match_all($padrao_reg, $mascara, $matches);
	$vars = $matches[1];
	//echo "<br/>vars: ";print_r($vars);
	
	
	//encontrando os valores
	$identificador = "############";
	$padrao_var =  preg_quote(preg_replace($padrao_reg, $identificador, $mascara), '/');//removendo as variaveis, deixar apenas as constantes
	//echo "<br/>sem var: ".$padrao_var;
	$padrao_var = str_replace($identificador, '(.*)', $padrao_var);//recolocando as variáveis a serem identificadas, construindo a regex
	$padrao_var .= "\.raw";//extensão do arquivo também será desconsiderada
	preg_match("/$padrao_var/", $texto, $valores);//aplicando o padrão construído no texto
	
	
	//echo "<br/>valores: ";print_r($valores);
	//echo count($vars)." ou ".count($valores);
	
	if(count($valores) < count($vars) )
		die("<br/>Erro ao processar a máscara: Não condiz com os nomes dos arquivos. <br/><br/>Mascara: $mascara <br/>Arquivo: $texto");
	
	$dados = array();
	for($i = 0; $i < count($vars); $i++){
		$dados[$vars[$i]] = $valores[$i+1];//reconstruindo o vetor resultado, no formato [atributo] = valor
	}
	
	//print_r($dados);
	
	return $dados;
	
}
/**
 * //busca o sensor samip e retorna
 */
function getSensorSamip($sensores){
	foreach($sensores as $sensor => $dados){
		if(str_contains($sensor, 'SAMIP')) 
			return $dados;
	}
	echo "<BR/><BR/>############# ATENÇÃO!!! NÃO EXISTEM DADOS DO SAMIP";
}
/**
 * pega os atributos do SAMPI, exceto curva
 */
function getTriosSamipData($sensor_samip){
	if($sensor_samip == "" || $sensor_samip == null) return;//caso não exista registro do SAMIP
	
	$registro = array();
	$registro["arquivo"] = $sensor_samip["file"];
	$registro["ponto"] = $sensor_samip["MissionSub"];
	$registro["sensor"] = $sensor_samip["IDDevice"];
	$registro["data"] = $sensor_samip["time"];

	$file_handle = fopen($registro["arquivo"], "r");
	$separador = "";

	if(str_contains($registro["sensor"], 'SAMIP')){
		pulaLinhaArquivoQuantidade($file_handle, 3);
		$linha = explode(' ', preg_replace('/\s+/', ' ',fgets($file_handle))); //lendo a linha e substituindo espaços duplicados para ler o array
		$registro["pressao"] = $linha[1];// * 10;//conversão estranho do dado de pressão hpa³ do trios
		pulaLinhaArquivoQuantidade($file_handle, 2);
	}
	return $registro;
}

/**
 * //pega atributos comuns a todos os sensores trios, inclusive a curva
 */
function getTriosSensorData($sensor){
	$registro = array();
	$registro["arquivo"] = $sensor["file"];
	$registro["ponto"] = $sensor["MissionSub"];
	$registro["sensor"] = $sensor["IDDevice"];
	$registro["data"] = $sensor["time"];
	$registro["alvo"] = $sensor["Comment0"];
	$registro["posicao"] = $sensor["Comment1"];
	$registro["novo_ponto"] = $sensor["Comment3"];

	$file_handle = fopen($registro["arquivo"], "r");
	$separador = "";

	if(str_contains($registro["sensor"], 'SAMIP')) pulaLinhaArquivoQuantidade($file_handle, 5);
	//else pulaLinhaArquivoQuantidade($file_handle, 1);
		
	$curva = array();
	for ($linha = 0;!feof($file_handle)/* && $linha < 800*/; $linha++) {
		$linha = explode(' ', preg_replace('/\s+/', ' ',fgets($file_handle))); //lendo a linha e substituindo espaços duplicados para ler o array
		if($linha[0] > 0){
			$c = $linha[0];
			$v = $linha[1];
			//$curva[$c] = $v;
			$curva[] = array("comprimento_onda" => $c, "intensidade" => $v);
		}
	}
	$registro["curva"] = $curva;
		
	
	return $registro;
}

/**
 * A partir do vetor de registros (uma curva + atributos), define qual a grandeza que o representa
 * @param unknown $registro
 */
function defineTipoGrandezaTrios($registro){
	$grandeza = "";
	$posicao = strtolower(trim($registro["posicao"]));
	
	if($registro["sensor"] == "SAM_8496"){
		if($registro["alvo"] == "agua") $grandeza = "Lt";
		else if($registro["alvo"] == "ceu") $grandeza = "Ls";
		else if($registro["alvo"] == "placa") $grandeza = "Lg";
		else{
			echo "<br />Erro no sensor externo: ";
			echo $registro["data"] . " possui alvo [".$registro['alvo']."], o qual não é conhecido";
			return;
		}
	}
	
	if($registro["pressao"]<0 && ($posicao == "fora" || $posicao == "" || true)){
		if($registro["sensor"] == "SAM_84A8") $grandeza = "Ed+";
		if($registro["sensor"] == "SAMIP_509E") $grandeza = "Eu+";
		if($registro["sensor"] == "SAM_849B") $grandeza = "Lt_";
		
		//if($registro["sensor"] == "SAM_8496") $grandeza = "Depende do alvo, agua, ceu, placa";
	}
	else if($registro["pressao"]>0 && ($posicao == "dentro" || $posicao == "" || true)){
		if($registro["sensor"] == "SAM_84A8") $grandeza = "Ed-";
		if($registro["sensor"] == "SAMIP_509E") $grandeza = "Eu-";
		if($registro["sensor"] == "SAM_849B") $grandeza = "Lu";
		
		//if($registro["sensor"] == "SAM_8496") $grandeza = "Depende do alvo, mas só pode ser placa";
	}else{
		echo "<br />Erro na posição: ";
		echo $registro["data"] . " possui flag [".$posicao."] mas a pressao é ".$registro["pressao"];
		return;
	}
	
	if($grandeza == ""){
		echo "<br />Erro na grandeza para o sensor: ";
		print_r($registro);
	}
	
	return $grandeza;
}


function lerTriosMulti($id_reservatorio, $datainicio, $caminho_arquivo, $separador, $trios_mascara, $trios_profundidade, $trios_ponto){
	//echo "$id_reservatorio, $datainicio, $caminho_arquivo, $separador, $trios_mascara";
	
	
	$zip = new ZipArchive;
	$res = $zip->open($caminho_arquivo);
	
	if($res === TRUE){
		
		//$temp_dir = "/tmp/dir_zip__20160717153427/";
		
		$temp_dir = "/tmp/".novoNomeDir("dir_zip_");
		$zip->extractTo($temp_dir);
		$zip->close();
		//echo "ok, salvo em $temp_dir";
		 
		
		$files = glob("$temp_dir*.{raw}", GLOB_BRACE);
		$conjunto = array();
		foreach($files as $file) {//le cada arquivo e extrai as informações da mascara, agrupando por data e sensor
		  //echo "<br/>$file";
		  //aplicar a mascara ${Mission}%${MissionSub}%${IDData}$%${IDDevice}%{Comment0}%${Comment1} em cada arquivo
		  //$trios_mascara = '${Mission}%${MissionSub}%${IDData}$%${IDDevice}%{Comment0}%${Comment1}'; //errada
		  $trios_mascara = '${Mission}%${MissionSub}%${IDData}%${IDDevice}%${Comment0}%${Comment1}%${Comment2}%${Comment3}';
		  $nome_arquivo = str_replace($temp_dir, '', $file);
		  
		  $dados = extrairVariaveis($trios_mascara, $nome_arquivo);
		  $dados["arquivo"] = $temp_dir . $nome_arquivo;
		  
		  $data = extrairTempoIDTrios($dados["IDData"]);
		  //$conjunto[$data] = array();
		  $conjunto[$data][$dados["IDDevice"]] = $dados;//copiando o vetor de variaveis da mascara
		  $conjunto[$data][$dados["IDDevice"]]["file"] = $file;
		  $conjunto[$data][$dados["IDDevice"]]["time"] = $data;
		  
		  //echo "<br><br>" ;echo "<br>";print_r($dados);echo "<br>";
		}
		//echo "<br/>"; print_r($conjunto);
		
	}else{
		echo "Error";
	}
	
	$registros = array();
	while(list($tempo, $sensores) = each($conjunto)){
	//foreach($conjunto as $tempo => $sensores){
		trace("<br>=================");
		$pressao = getTriosSamipData(getSensorSamip($sensores))["pressao"];
		
		if($pressao == ""){//pegando o valor ponto medio de profundidade do ponto anterior e do proximo
			prev($conjunto);
			$pressao_ant = getTriosSamipData(getSensorSamip(prev($conjunto)))["pressao"];
			next($conjunto);
			$pressao_prox = getTriosSamipData(getSensorSamip(next($conjunto)))["pressao"];
			//prev($conjunto);
			trace("Pressao ANT: ".$pressao_ant);
			trace("Pressao PRO: ".$pressao_prox);
			$pressao = $pressao_ant + (($pressao_prox - $pressao_ant)/2);
			
		}
		trace("Pressao: ".$pressao);
		
		foreach($sensores as $sensor => $dados){
			trace($dados["file"]);
			$registro = getTriosSensorData($dados);
			$registro["pressao"] = $pressao;
			$registro["tipo"] = defineTipoGrandezaTrios($registro);
			
			//anotação do ponto no comentario
			if($registro["novo_ponto"] != "") $registro["ponto"] = $registro["novo_ponto"]; 
			unset($registro["novo_ponto"]);
			
			//$registro = array_merge($registro["curva"], $curva);
			//print_r($registro); 
			$registros[] = $registro;
			
			
			
		}
	}
	
	//printTable($registros);
	
	//return;
	
	return inserir_matriz_trios($id_reservatorio, $datainicio, $registros);
	
	
	
}

function lerHannaMulti($id_reservatorio, $datainicio, $caminho_arquivo){
	$zip = new ZipArchive;
	$res = $zip->open($caminho_arquivo);
	
	if($res === TRUE){
	
		//$temp_dir = "/tmp/dir_zip__20160717153427/";
	
		$temp_dir = "/tmp/".novoNomeDir("dir_zip_");
		$zip->extractTo($temp_dir);
		$zip->close();
		//echo "ok, salvo em $temp_dir";
			
	
		$files = glob("$temp_dir*.{xls}", GLOB_BRACE);
		$conjunto = array();
		foreach($files as $file) {//le cada arquivo xls e extrai as informações do [ponto][tempo] e variaveis associadas
			//
			//trace($file);
			
			//  Read your Excel workbook
			try {
				$inputFileType = PHPExcel_IOFactory::identify($file);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($file);
			} catch(Exception $e) {
				die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
			}
			
			
			//  Get worksheet dimensions
			$sheet = $objPHPExcel->getSheet(0);
			$ponto_str = $sheet->getCell("B19")->getValue();
			preg_match("/[pP_]{0,}([0-9]*)/", $ponto_str, $arr_regex);//aceita apenas [Pp_] + o número
			$ponto = $arr_regex[1];
			trace($ponto . "    " . $file);
			
			$sheet = $objPHPExcel->getSheet(1);
			$highestRow = $sheet->getHighestDataRow();
			$highestColumn = $sheet->getHighestDataColumn();
			//recupera o nome das colunas, pode requerer padronização para jogar no banco
			$labels = array_values($sheet->rangeToArray('A' . '1' . ':' . $highestColumn . '1', NULL, TRUE, FALSE)[0]);
			
			//trace("Lendo Ponto: $ponto");
			$conjunto[$ponto]  =array();
			
			//  Loop through each row of the worksheet in turn
			for ($row = 2; $row <= $highestRow; $row++){//jump the header
				//  Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE)[0];
				$tempo = $rowData[0] . " " .$rowData[1];
					
				//trace("Lendo Tempo: $tempo");
				if(trim($tempo) != ""){
						
					$conjunto[$ponto][$tempo] = array();
					for($col = 2; $col < count($rowData); $col++){
						if(isset($labels[$col])){
							$conjunto[$ponto][$tempo][$labels[$col]] = (float)$rowData[$col];
						}
					}
					//print_r($conjunto[$ponto][$tempo]);
				}
			}
			//echo "<br><br>" ;echo "<br>";print_r($dados);echo "<br>";
		}
		
		//construir os registros a partir do conjunto
		$registros = array();
		foreach ($conjunto as $ponto => $arr_ponto){
			$registros[$ponto]  = array();
			$registros[$ponto]["sensor"] = "sonda";
			$registros[$ponto]["arquivo"] = $file;
			$registros[$ponto]["celulas"] = array();
			foreach($arr_ponto as $tempo => $arr_tempo){
				foreach ($arr_tempo as $col => $val){
					$registro = array();
					$registro["tempo"] = $tempo;
					$registro["tipo"] = $col;
					$registro["valor"] = $val;
		
					//$registro["ponto"] = $ponto;
					//$registro["arquivo"] = $file;
					//$registro["sensor"] = "Sonda";
					$registros[$ponto]["celulas"][] = $registro;
				}
			}
		}
		return(inserir_matriz_hanna($id_reservatorio, $datainicio, $registros));
		
		//echo "<br/>"; print_r($conjunto);
	
	}else{
		echo "Error";
	}
	
}

function moverArquivoDiscoBanco($arquivo){
	global $pagina;
	$diretorio_arquivos = "../arquivos/";
	$nome_novo = novoNomeArquivo(getNomeArquivoPath($arquivo));
	//$pagina->addAviso("Salvando arquivo [$arquivo] como: ".$nome_novo);
	if(MoveArquivoServidor($arquivo, $diretorio_arquivos, $nome_novo) != ""){
		$caminho_arquivo = $diretorio_arquivos . $nome_novo;
		pg_query("INSERT INTO arquivos (nome_arq, id_dir_fk) VALUES ('$nome_novo', '2')") or die(pg_last_error());
		$id_arq = getUltimoIDInserido();
		return $id_arq;
	}else{
		$pagina->addAdvertencia("Erro ao salvar arquivo $nome_original");
	}
}

function getSalvaNovoPonto($id_reservatorio, $ponto){
	$id_ponto = getAtrQuery("SELECT id FROM d_regiao WHERE nome = '$ponto' AND id_reservatorio_fk = '$id_reservatorio'");
	if($id_ponto == ""){
		$rsCon = pg_query("INSERT INTO d_regiao (nome, id_reservatorio_fk, latitude, longitude) VALUES ('$ponto', '$id_reservatorio', '0', '0') RETURNING id") or die(pg_last_error());
		$row = pg_fetch_row($rsCon);
		$id_ponto = $row['0'];
	}
	return $id_ponto;
}

function getSalvaNovoCampo($datainicio, $id_ponto, $id_usu, $id_arq, $id_equipamento){
	//tudo certo para inserir o novo campo no banco de dados
	
	$id_cam = getAtrQuery("SELECT id FROM d_campo WHERE tempoinicio = '$datainicio' AND id_regiao_fk = '$id_ponto' AND id_usuario_fk = '$id_usu' AND id_equipamento_fk = '$id_equipamento'");
	if($id_cam == ''){
		$sql = "INSERT INTO d_campo (tempoinicio, id_regiao_fk, id_usuario_fk, id_arq_fk, id_equipamento_fk) VALUES
		('$datainicio', '$id_ponto', '$id_usu', '$id_arq', '$id_equipamento')";
		//echo $sql;
		pg_query($sql) or die(pg_last_error());
		$id_cam = getUltimoIDInserido("d_campo", "id");
	}
	return $id_cam;
}

function getSalvaConjunto($id_cam, $processamento){
	$id_conjunto = getAtrQuery("SELECT id FROM d_conjunto WHERE id_campo_fk = '$id_cam' AND processamento = '$processamento'");
	if($id_conjunto == ''){
		$rsCon = pg_query("INSERT INTO d_conjunto (id_campo_fk, processamento, id_conjuntopai_fk) VALUES ('$id_cam', '$processamento', null) RETURNING id") or die(pg_last_error());
		$row = pg_fetch_row($rsCon);
		$id_conjunto = $row['0'];
	}
	
	return $id_conjunto;
}

function inserir_matriz_trios($id_reservatorio, $datainicio, $registros){
	global $pagina;


	pg_query("BEGIN; SET statement_timeout TO 1800000") or die("543Could not start transaction\n". pg_last_error());
	$quant = 0;
	//para cada arquivo/registro
	foreach ($registros as $linha => $registro){
		$quant++;
		//inserir em regiao/Ponto($id_reservatorio), caso não exista
		//inserir em campo ($datainicio, regiao, usuario, arq, equipamento)
		//inserir em conjunto(campo)
		//inserir em medidas

		$id_usu = $pagina->getUsuario()->getIdUsuario();
		$id_ponto = getSalvaNovoPonto($id_reservatorio, $registro["ponto"]);
		$id_arq = moverArquivoDiscoBanco($registro["arquivo"]);
		$id_equipamento = getAtrQuery("SELECT id FROM d_equipamento WHERE nome ILIKE '%".$registro["sensor"]."%'");

		$caminho_arquivo = $registro["arquivo"];
		$file_handle = fopen($caminho_arquivo, "r");

		$id_cam = getSalvaNovoCampo($datainicio, $id_ponto, $id_usu, $id_arq, $id_equipamento);


		$id_conjunto = getSalvaConjunto($id_cam, 'original');

		$tupla = array();

		$data = $registro["data"];
		$tupla["tempo"] = explode('_', $data)[0] ." ". str_replace('-', ":", explode('_', $data)[1]);
		$tupla["profundidade_m"] = $registro["pressao"]*10;
		$tupla["id_conjunto_fk"] = $id_conjunto;
		$tupla["pressao_hpa3"] = $registro["pressao"];
		$tupla["tipo"] = $registro["tipo"];

		inserirRegistrosBanco("d_medidas_trios", array($tupla));

		$id_med = getUltimoIDInserido("d_medidas_trios", "id");
		inserirRegistrosBanco("d_medidas_trios_espectro", $registro["curva"], array("id_medidas_trios_fk" => $id_med));
	}


	//inserirRegistrosBanco("d_medidas_trios", $registros);

	pg_query("COMMIT") or die("1432Could not start transaction\n". pg_last_error());

	echo "<br/><br/><br/><br/>Dados Processados e inseridos:";

	return $quant;
}

function inserir_matriz_hanna($id_reservatorio, $datainicio, $registros){
	global $pagina;


	pg_query("BEGIN; SET statement_timeout TO 1800000") or die("543Could not start transaction\n". pg_last_error());
	$quant = 0;
	//para cada arquivo/registro
	$tuplas = array();
	foreach ($registros as $ponto => $registros_p){
		trace("Trabalhando ponto $ponto");
		//inserir em regiao/Ponto($id_reservatorio), caso não exista
		//inserir em campo ($datainicio, regiao, usuario, arq, equipamento)
		//inserir em conjunto(campo)
		$id_usu = $pagina->getUsuario()->getIdUsuario();
		$id_ponto = getSalvaNovoPonto($id_reservatorio, $ponto);
		$id_arq = moverArquivoDiscoBanco($registros_p["arquivo"]);
		$id_equipamento = getAtrQuery("SELECT id FROM d_equipamento WHERE nome ILIKE '%".$registros_p["sensor"]."%'");
		$id_cam = getSalvaNovoCampo($datainicio, $id_ponto, $id_usu, $id_arq, $id_equipamento);
		$id_conjunto = getSalvaConjunto($id_cam, 'original');
		
		foreach ($registros_p["celulas"] as $linha => $registro){
			$quant++;
			//inserir em medidas
			$tupla = array();
			
			$tupla["tempo"] = $registro["tempo"];
			$tupla["id_conjunto_fk"] = $id_conjunto;
			$tupla["tipo"] = $registro["tipo"];
			$tupla["valor"] = $registro["valor"];
			
			$tuplas[] = $tupla;
		}
	}

	inserirRegistrosBanco("d_medidas_limnologicas", $tuplas);

	//inserirRegistrosBanco("d_medidas_trios", $registros);

	pg_query("COMMIT") or die("1432Could not start transaction\n". pg_last_error());

	echo "<br/><br/><br/><br/>Dados Processados e inseridos:";

	return $quant;
}


//lerTriosMulti('7', '2016-05-10 00:00:00', '../arquivos/dados_trios_system.zip.20160617142930', ' ', '${Mission}%${MissionSub}%${IDData}%${IDDevice}%${Comment0}%${Comment1}', -1);
//lerTriosMulti('7', '2016-05-10 00:00:00', '../arquivos/trios_todos_exportados.zip.20160717152710', ' ', '${Mission}%${MissionSub}%${IDData}%${IDDevice}%${Comment0}%${Comment1}', -1);


//inserir_dados_acs("../files/archive_21_ACS.csv");

//inserir_dados_ctd("../files/archive_22_CTD-ENGR.023");

//inserir_dados_hydro("../files/Hydroscat_28_P03.csv");

?>