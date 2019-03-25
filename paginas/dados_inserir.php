<?

include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

$pagina = new Pagina("dados_inserir.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

ini_set('post_max_size', '50M');
ini_set('upload_max_filesize', '50M');

$separadores = array("\t" => "TAB", "," => "Colon", ";" => "Semicolon", " " => "Space");

$id_usu = $pagina->getUsuario()->getIdUsuario();

$diretorio_arquivos = "../arquivos/";
$id_reservatorio = (int)post("reservatorio");
$id_ponto = (int)post("ponto");
$datainicio = formataDataInsert(post("datainicio"));

if(get("a") == "inc"){
	if(($id_ponto > 0 || post("ponto") == "MULTIPLOS") && $datainicio != ""){
		foreach($_FILES as $campo_arq => $file){
			$nome_original = $file["name"];
			if($nome_original != ""){
				$pagina->addAviso("Processing: ".$file["name"]);
				$nome_novo = novoNomeArquivo($nome_original);
				//$pagina->addAviso("Salvando: ".$nome_novo);
				if(SalvaArquivoServidor($campo_arq, $diretorio_arquivos, $nome_novo) != ""){
					$caminho_arquivo = $diretorio_arquivos . $nome_novo;
					pg_query("INSERT INTO arquivos (nome_arq, id_dir_fk) VALUES ('$nome_novo', '2')") or die(pg_last_error());
					$id_arq = getUltimoIDInserido();
					//$pagina->addAviso("Arquivo Salvo com sucesso: ".$nome_novo);
					$equip_sigla = explode("_", $campo_arq)[0];//SIGLA É RECUPERADA A PARTIR DO NAME FIELD do campo de arquivo, separada por '_'
					$id_equipamento = post($equip_sigla."_equipamento");
					if($id_equipamento != ""){
						
						if($id_ponto > 0){//inserindo um unico arquivo, unico ponto
						
							//tudo certo para inserir o novo campo no banco de dados
							$sql = "INSERT INTO d_campo (tempoinicio, id_regiao_fk, id_usuario_fk, id_arq_fk, id_equipamento_fk) VALUES ('$datainicio', '$id_ponto', '$id_usu', '$id_arq', '$id_equipamento')";
							//echo $sql;
							pg_query($sql) or die(pg_last_error());
							$id_cam = getUltimoIDInserido("d_campo", "id");
							
							//tudo inserido no banco, resta processar o arquivo e inserir as medidas, de acordo com o sensor
							if($equip_sigla == "acs"){
								$separador = $_POST["acs_separador"];
								$linha_rotulos = (int)post("acs_linha");
								
								$res = lerAcs($id_cam, $caminho_arquivo, $separador, $linha_rotulos);
								if($res === false){
									$pagina->addAdvertencia("Error to process the file. $res");
								}
								else{
									$pagina->addAviso("File processed and data was inserted with sucess: $res registers inserted");
								}
							}else if($equip_sigla == "ctd"){
								$separador = $_POST["ctd_separador"];
								$linha_rotulos = (int)post("ctd_linha");
								
								$res = lerCtd($id_cam, $caminho_arquivo, $separador, $linha_rotulos);
								if($res === false){
									$pagina->addAdvertencia("Error to process the file. $res");
								}
								else{
									$pagina->addAviso("File processed and data was inserted with sucess: $res registers inserted");
								}
							}else if($equip_sigla == "ctdraw"){
								$separador = $_POST["ctdraw_separador"];
								$trim = (int)post("ctdraw_trim");
								$del_char = $_POST["ctdraw_del_char"];
								
								$del_char_arr = explode('#', $del_char);
								
								$res = lerCtdRaw($id_cam, $caminho_arquivo, $separador, $trim, $del_char_arr);
								if($res === false){
									$pagina->addAdvertencia("Error to process the file. $res");
								}
								else{
									$pagina->addAviso("File processed and data was inserted with sucess: $res registers inserted");
								}
							}else if($equip_sigla == "eco"){
								$separador = $_POST["ecoraw_separador"];
								$linha_rotulos = (int)post("ecoraw_linha");
								$res = lerEco($id_cam, $caminho_arquivo, $separador, $linha_rotulos);
								if($res === false){
									$pagina->addAdvertencia("Error to process the file. $res");
								}
								else{
									$pagina->addAviso("File processed and data was inserted with sucess: $res registers inserted");
								}
							}else if($equip_sigla == "ecoraw"){
								$separador = $_POST["eco_separador"];
								$tem_tempo = post("ecoraw_tempo");
								$tipo = post("ecoraw_tipo");
								
								$res = lerEcoRaw($id_cam, $caminho_arquivo, $separador, $tem_tempo, $tipo);
								if($res === false){
									$pagina->addAdvertencia("Error to process the file. $res");
								}
								else{
									$pagina->addAviso("File processed and data was inserted with sucess: $res registers inserted");
								}
							}else if($equip_sigla == "hydro"){
								$separador = $_POST["hydro_separador"];
								$linha_rotulos = (int)post("hydro_linha");
								$res = lerHydro($id_cam, $caminho_arquivo, $separador, $linha_rotulos);
								if($res === false){
									$pagina->addAdvertencia("Error to process the file. $res");
								}
								else{
									$pagina->addAviso("File processed and data was inserted with sucess: $res registers inserted");
								}
							}
							
							
						}else{//inserir multiplos arquivos, multiplos pontos
							//enviar $datainicio e $reservatorio
							
							if($equip_sigla == "trios"){
								$separador = $_POST["trios_separador"];
								$trios_mascara = post("trios_mascara");
								$trios_profundidade_padrao = post("trios_profundidade_padrao");
								$trios_profundidade_sensor = post("trios_profundidade_sensor");
								$trios_ponto_sensor = post("trios_ponto_sensor");
								
								$trios_profundidade = $trios_profundidade_sensor;
								if($trios_profundidade_sensor == ""){
									$trios_profundidade = $trios_profundidade_padrao;
								}
								
								//return;
								
								$res = lerTriosMulti($id_reservatorio, $datainicio, $caminho_arquivo, $separador, $trios_mascara, $trios_profundidade, $trios_ponto_sensor);
								if($res === false){
									$pagina->addAdvertencia("Error to process the file. $res");
								}
								else{
									$pagina->addAviso("File processed and data was inserted with sucess: $res registers inserted");
								}
							}
							else if($equip_sigla == "hanna"){
								$res = lerHannaMulti($id_reservatorio, $datainicio, $caminho_arquivo);
								if($res === false){
									$pagina->addAdvertencia("Error to process the file. $res");
								}
								else{
									$pagina->addAviso("File processed and data was inserted with sucess: $res registers inserted");
								}
							}
						}
							
						
					}else{
						$pagina->addAdvertencia("Is needed to select the equipament [$equip_sigla] of file [$nome_original]");
					}
				}
				else{
					$pagina->addAdvertencia("Error to save the file $nome_original");
				}
			}
		}
	}
	else{
		$pagina->addAdvertencia("Insert the global parameters");
	}
}







include "../layout/cima.php";

?>


<form action="dados_inserir.php?a=inc" method="post" enctype='multipart/form-data'>

<h1>Interface of data insertion</h1>
<div class="blocoOr">
	<p>This interface is used to insert the new data to the system database</p>
	<p>You need to use the specific files from the sensor</p>
	
	<p>After the end of insertion, the data will be available to process and analysis.</p>
</div>

<div class="blocoOr">
	<h2>1 - Global parameters of field campaign:</h2>
	
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
				<?
				if($id_reservatorio > 0){
					$arrayPonto = getInputFromSQL(pg_query("SELECT d_regiao.id AS ID, CONCAT(d_reservatorio.nome, ': ', d_regiao.nome) AS DESCRICAO FROM d_regiao LEFT JOIN d_reservatorio on id_reservatorio_fk = d_reservatorio.id WHERE d_reservatorio.id = '".$id_reservatorio."' ORDER BY DESCRICAO"));
					$arrayPonto["MULTIPLOS"] = "MÚLTIPLOS PONTOS";
					inputSelect("ponto", $arrayPonto);
				}
				?>
			</td>
			<td><small><a href='../admin_dados/gerir_regiao.php?ACAO=inserir'>Insert new point</a></small></td>
		</tr>
		
		<tr>
			<td><b>Date of first field campaing day</b>:</td>
			<td>
				<?inputData("datainicio", null, "20");?>
			</td>
		</tr>
		
		
	</table>
	
	
</div>


<div class="blocoOr">
	<h2>2 - Data files of equipaments:</h2>
	
	<table>
		<tr>
			<th>Structure</th>
			<th>Equipament <br /> <small><a href='../admin_dados/gerir_equipamento.php?ACAO=inserir'>Insert new equipament</a></small> </th>
			<th>File</th>
			<th>Settings</th>
		</tr>
		<?//para cada arquivo, selecionar o equipamento?>
		<tr>
			<td><b>ACS</b></td>
			<td>
				<?$arrayEquipamento = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_equipamento ORDER BY DESCRICAO"));?>
				<?inputSelect("acs_equipamento", $arrayEquipamento);?>
			</td>
			<td><?inputFile("acs_arquivo", "80%")?></td>
			<td>
				<div class="box">
					<div class="box_titulo titulo_expansivel">Edit</div>
					<div class="conteudo_expansivel">
						<table>
							<tr>
								<td>Separator</td>
								<td><?inputSelect("acs_separador", $separadores, "\t")?></td>
							</tr>
							<tr>
								<td>Line of header</td>
								<td><?inputText("acs_linha", "100", "5")?></td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
		
		<tr>
			<td><b>CTD</b></td>
			<td>
				<?$arrayEquipamento = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_equipamento ORDER BY DESCRICAO"));?>
				<?inputSelect("ctd_equipamento", $arrayEquipamento);?>
			</td>
			<td><?inputFile("ctd_arquivo", "80%")?></td>
			<td>
				<div class="box">
					<div class="box_titulo titulo_expansivel">Edit</div>
					<div class="conteudo_expansivel">
						<table>
							<tr>
								<td>Separator</td>
								<td><?inputSelect("ctd_separador", $separadores, "\t")?></td>
								
							</tr>
							<tr>
								<td>Line of header</td>
								<td><?inputText("ctd_linha", "1", "5")?></td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
		
		<tr>
			<td><b>CTD-RAW</b></td>
			<td>
				<?$arrayEquipamento = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_equipamento ORDER BY DESCRICAO"));?>
				<?inputSelect("ctdraw_equipamento", $arrayEquipamento);?>
			</td>
			<td><?inputFile("ctdraw_arquivo", "80%")?></td>
			<td>
				<div class="box">
					<div class="box_titulo titulo_expansivel">Edit</div>
					<div class="conteudo_expansivel">
						<table>
							<tr>
								<td>Separator</td>
								<td><?inputSelect("ctdraw_separador", $separadores, " ")?></td>
							</tr>
							<tr>
								<td>Remove Characters (Use # to insert many chars)</td>
								<td><?inputText("ctdraw_del_char", ",#;")?></td>
							</tr>
							<tr>
								<td>Remove double spaces?</td>
								<td><?inputSelect("ctdraw_trim", array('0' => 'No', '1' => 'Yes'), "1")?></td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
		
		<tr>
			<td><b>EcoBB9</b></td>
			<td>
				<?$arrayEquipamento = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_equipamento ORDER BY DESCRICAO"));?>
				<?inputSelect("eco_equipamento", $arrayEquipamento);?>
			</td>
			<td><?inputFile("eco_arquivo", "80%")?></td>
			<td>
				<div class="box">
					<div class="box_titulo titulo_expansivel">Edit</div>
					<div class="conteudo_expansivel">
						<table>
							<tr>
								<td>Separator</td>
								<td><?inputSelect("eco_separador", $separadores, "\t")?></td>
							</tr>
							<tr>
								<td>Line of header</td>
								<td><?inputText("eco_linha", "1", "5")?></td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
		
		<tr>
			<td><b>EcoBB9-RAW</b></td>
			<td>
				<?$arrayEquipamento = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_equipamento ORDER BY DESCRICAO"));?>
				<?inputSelect("ecoraw_equipamento", $arrayEquipamento);?>
			</td>
			<td><?inputFile("ecoraw_arquivo", "80%")?></td>
			<td>
				<div class="box">
					<div class="box_titulo titulo_expansivel">Edit</div>
					<div class="conteudo_expansivel">
						<table>
							<tr>
								<td>Separator</td>
								<td><?inputSelect("ecoraw_separador", $separadores, "\t")?></td>
							</tr>
							<tr>
								<td>Time column</td>
								<td><?inputSelect("ecoraw_tempo", array("1"=>"Yes", "0" => "No"), "1")?></td>
							</tr>
							<tr>
								<td>Data type</td>
								<td><?inputSelect("ecoraw_tipo", array("count"=>"Count", "dark" => "Dark count"), "count")?></td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
		
		<tr>
			<td><b>HydroScat</b></td>
			<td>
				<?$arrayEquipamento = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_equipamento ORDER BY DESCRICAO"));?>
				<?inputSelect("hydro_equipamento", $arrayEquipamento);?>
			</td>
			<td><?inputFile("hydro_arquivo", "80%")?></td>
			<td>
				<div class="box">
					<div class="box_titulo titulo_expansivel">Edit</div>
					<div class="conteudo_expansivel">
						<table>
							<tr>
								<td>Separator</td>
								<td><?inputSelect("hydro_separador", $separadores, ",")?></td>
							</tr>
							<tr>
								<td>Line of header</td>
								<td><?inputText("hydro_linha", "36", "5")?></td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
		
		<tr>
			<td><b>TriOS</b></td>
			<td>
				<?$arrayEquipamento = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_equipamento ORDER BY DESCRICAO"));?>
				<?inputSelect("trios_equipamento", $arrayEquipamento);?>
			</td>
			<td> Insert a zip file with all TriOS files (ZIP) <br />
				exported using the format MULTIPLE FILE RAW<br />
				<?inputFile("trios_arquivo", "80%")?>
			</td>
			<td>
				<div class="box">
					<div class="box_titulo titulo_expansivel">Edit</div>
					<div class="conteudo_expansivel">
						<table>
							<tr>
								<td>Separator</td>
								<td><?inputSelect("trios_separador", $separadores, " ")?></td>
							</tr>
							<tr>
								<td>Mask</td>
								<td><?inputText("trios_mascara", '${Mission}%${MissionSub}%${IDData}%${IDDevice}%${Comment0}%${Comment1}', "60")?></td>
							</tr>
							<tr>
								<td>Default depth (use -1 to above water and 0 for "just below")</td>
								<td><?inputText("trios_profundidade_padrao", '-1', "60")?></td>
							</tr>
							<tr>
								<td>Sensor depth</td>
								<td><?inputSelect("trios_profundidade_sensor", array(""=>"Defalt", "SAMIP_509E" => "SAMIP_509E", "Comment0" => "Comment0", "Comment1" => "Comment1", "Comment2" => "Comment2"), " ")?></td>
							</tr>
							<tr>
								<td>Point label</td>
								<td><?inputSelect("trios_ponto_sensor", array("MissionSub"=>"MissionSub", "Mission" => "Mission", "Comment0" => "Comment0", "Comment1" => "Comment1", "Comment2" => "Comment2"), " ")?></td>
							</tr>
							
						</table>
					</div>
				</div>
			</td>
		</tr>


<tr>
			<td><b>Multiparameter probe</b></td>
			<td>
				<?$arrayEquipamento = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_equipamento ORDER BY DESCRICAO"));?>
				<?inputSelect("hanna_equipamento", $arrayEquipamento);?>
			</td>
			<td> Insert a zip file with all TriOS files (ZIP) <br />
				exported in XLS format<br />
				<?inputFile("hanna_arquivo", "80%")?>
			</td>
			<td>
				<div class="box">
					<div class="box_titulo titulo_expansivel">Edit</div>
					<div class="conteudo_expansivel">
					</div>
				</div>
			</td>
		</tr>

		
		
	</table>
	
	
</div>




<div class="blocoOr">

	<h2>3 - Processing</h2>
	
	<p>Start the processing of dataset.</p>
	<p>The server will read all dataset files and will process using the defined settings.</p>
	<p>The time of processing is dependent of the amount of data. In some cases, this process will require some minutes.</p>
	<p><b>Do not refresh the page until the finish of processing!</b></p>
	
	<? inputSubmit("Start processing")?>	
	
</div>


</form>

<?include "../layout/baixo.php"?>
