<?
include_once "../config/funcoes.php"; // OBRIGATÓRIO EM TODAS AS PÁGINAS

$pagina = new Pagina ( "dados_visualizar.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página" );

// //////////////////////////////////////////////////////////////////////////////
// //////////////////////////////////////////////////////////////////////////////
// //////////////////////////////////////////////////////////////////////////////
// //////////////////////////////////////////////////////////////////////////////
$campos = array ();
$campo ["acs"] = array (
		"comprimento_onda" 
);

$integracao = array();
$integracao["acs"] = array();
$integracao["ctd"] = array();
$integracao["hydroscat"] = array();
$integracao["ecobb9"] = array();
$integracao["trios"] = array();
$integracao["liminologicas"] = array();

$integracao["acs"]["ctd"] = array("tempo_ms", "tempo_ms");							$integracao["ctd"]["acs"] = array("tempo_ms", "tempo_ms");
$integracao["ctd"]["hydroscat"] = array("profundidade_m", "profundidade_m");    	$integracao["hydroscat"]["ctd"] = array("profundidade_m", "profundidade_m");
$integracao["ecobb9"]["ctd"] = array("tempo_ms", "tempo_ms");						$integracao["ctd"]["ecobb9"] = array("tempo_ms", "tempo_ms");
$integracao["ecobb9"]["acs"] = array("tempo_ms", "tempo_ms");						$integracao["acs"]["ecobb9"] = array("tempo_ms", "tempo_ms");
$integracao["ecobb9"]["ecobb9"] = array("tempo_ms", "tempo_ms");
$integracao["ctd"]["trios"] = array("profundidade_m", "profundidade_m");			$integracao["trios"]["ctd"] = array("profundidade_m", "profundidade_m");
$integracao["hydroscat"]["trios"] = array("profundidade_m", "profundidade_m");		$integracao["hydroscat"]["trios"] = array("profundidade_m", "profundidade_m");
$integracao["hydroscat"]["hydroscat"] = array("profundidade_m", "profundidade_m");	$integracao["hydroscat"]["hydroscat"] = array("profundidade_m", "profundidade_m");


$tabulacao = array();
$tabulacao["acs"] = array();
$tabulacao["ctd"] = array();
$tabulacao["hydroscat"] = array();
$tabulacao["ecobb9"] = array();
$tabulacao["trios"] = array();
$tabulacao["liminologicas"] = array();

$tabulacao["acs"]["tabela"] = "d_medidas_acs";
$tabulacao["acs"]["linha"] = array(array("tempo_ms"));
$tabulacao["acs"]["coluna"] = array(array("tipo", "comprimento_onda"));
$tabulacao["acs"]["celula"] = array("intensidade");//matriz 3d

$tabulacao["ctd"]["tabela"] = "d_medidas_ctd";
$tabulacao["ctd"]["linha"] = array(array("tempo_ms"));
$tabulacao["ctd"]["coluna"] = array(array("profundidade_m"), array("pressao_dbar"), array("temperatura_c"), array("condutividade_sm"), array("salinidade_psu"));
$tabulacao["ctd"]["celula"] = null;//próprios valores ddas colunas

$tabulacao["hydroscat"]["tabela"] = "d_medidas_hydroscat";
$tabulacao["hydroscat"]["linha"] = array(array("profundidade_m"));
$tabulacao["hydroscat"]["coluna"] = array(array("tipo", "comprimento_onda"));
$tabulacao["hydroscat"]["celula"] = array("intensidade");//matriz 3d

$tabulacao["ecobb9"]["tabela"] = "d_medidas_ecobb9";
$tabulacao["ecobb9"]["linha"] = array(array("tempo_ms"));
$tabulacao["ecobb9"]["coluna"] = array(array("tipo", "comprimento_onda"));
$tabulacao["ecobb9"]["celula"] = array("intensidade");//matriz 3d

$tabulacao["trios"]["tabela"] = "(select d_medidas_trios.*, d_medidas_trios_espectro.comprimento_onda, d_medidas_trios_espectro.intensidade FROM d_medidas_trios_espectro LEFT JOIN d_medidas_trios ON id_medidas_trios_fk = d_medidas_trios.id)";
$tabulacao["trios"]["linha"] = array(array("comprimento_onda"));
$tabulacao["trios"]["coluna"] = array(array("tipo", "profundidade_m"));
$tabulacao["trios"]["celula"] = array("intensidade");//matriz 3d

$tabulacao["limnologicas"]["tabela"] = "d_medidas_limnologicas";
$tabulacao["limnologicas"]["linha"] = array(array("profundidade_m"));
$tabulacao["limnologicas"]["coluna"] = array(array("tipo"));
$tabulacao["limnologicas"]["celula"] = array("valor");//matriz 3d

$traducao = array();
$traducao["t0.profundidade_m"] = "t0.depth_m";
$traducao["t0.id_conjunto_fk"] = "t0.id_set_fk";
$traducao["t0.id"] = "t0.id";
$traducao["t0.pressao_hpa3"] = "t0.pressure_hpa3";
$traducao["t0.tempo"] = "t0.time";
$traducao["t0.tipo"] = "t0.type";
$traducao["t0.comprimento_onda"] = "t0.wavelength";
$traducao["t0.intensidade"] = "t0.intensity";
$traducao["d_conjunto.id"] = "d_conjunto.id";
$traducao["d_conjunto.id_campo_fk"] = "d_set.id_field_fk";
$traducao["d_conjunto.processamento"] = "d_set.processing";
$traducao["d_conjunto.data"] = "d_set.date";
$traducao["d_conjunto.id_conjuntopai_fk"] = "d_set.id_setparent_fk";
$traducao["d_campo.id"] = "d_set.id";
$traducao["d_campo.tempoinicio"] = "d_set.starttime";
$traducao["d_campo.id_regiao_fk"] = "d_set.id_region_fk";
$traducao["d_campo.id_usuario_fk"] = "d_set.id_user_fk";
$traducao["d_campo.id_arq_fk"] = "d_field.id_file_fk";
$traducao["d_campo.id_equipamento_fk"] = "d_set.id_equipament_fk";
$traducao["d_campo.data_insercao"] = "d_set.insertion_date";
$traducao["d_equipamento.id"] = "d_equipament.id";
$traducao["d_equipamento.nome"] = "d_equipament.name";
$traducao["d_equipamento.modelo"] = "d_equipament.model";
$traducao["d_equipamento.id_equipamentopai_fk"] = "d_equipament.id_equipamentparent_fk";
$traducao["d_regiao.id"] = "d_region.id";
$traducao["d_regiao.nome"] = "d_region.name";
$traducao["d_regiao.descricao"] = "d_region.description";
$traducao["d_regiao.id_reservatorio_fk"] = "d_region.id_reservoir_fk";
$traducao["d_regiao.latitude"] = "d_region.latitude";
$traducao["d_regiao.longitude"] = "d_region.longitude";
$traducao["d_reservatorio.id"] = "d_reservoir.id";
$traducao["d_reservatorio.nome"] = "d_reservoir.name";
$traducao["d_reservatorio.descricao"] = "d_reservoir.description";
$traducao["d_reservatorio.localizacao_geo"] = "d_reservoir.geo_location";
$traducao["d_reservatorio.sigla"] = "d_reservoir.acron";


// //////////////////////////////////////////////////////////////////////////////
                                                      // //////////////////////////////////////////////////////////////////////////////
                                                      // //////////////////////////////////////////////////////////////////////////////
                                                      // //////////////////////////////////////////////////////////////////////////////
                                                      // //////////////////////////////////////////////////////////////////////////////

$id_res = post ( "reservatorio" );
$id_sensor = post("sensor");
// definir o sensor a partir do campo
$qtdmax_integracao = 5; // quantidade máxima de conjuntos que podem ser integrados



?>

<? include "../layout/cima.php"?>


<h1>Exploring the data</h1>

<form action="" method="post">

	<div class="blocoOr">
		<h2>1 - Filtering:</h2>

		<table class='tabela_sem_borda'>
			<tr>
				<td><b>Reservoir</b>:</td>
				<td>
<? $arrayReservatorio = getInputFromSQL(pg_query("SELECT id AS ID, nome AS DESCRICAO FROM d_reservatorio ORDER BY DESCRICAO")); ?>
<? inputSelect("reservatorio", $arrayReservatorio, null, 1, "onchange=submit()"); ?>
                </td>
				<td><small><a
						href='../admin_dados/gerir_reservatorio.php?ACAO=inserir'>Insert new reservoir</a></small></td>
			</tr>

			<tr>
				<td><b>Sensor</b>:</td>
				<td>
<? $arraySensores = @getInputFromSQL(pg_query("SELECT d_equipamento.id as ID, d_equipamento.nome as DESCRICAO FROM d_campo LEFT JOIN d_equipamento ON id_equipamento_fk = d_equipamento.id LEFT JOIN d_regiao ON d_campo.id_regiao_fk = d_regiao.id WHERE d_regiao.id_reservatorio_fk = '$id_res' GROUP BY d_equipamento.id ORDER BY DESCRICAO")); ?>
<? inputSelect("sensor", $arraySensores, null, 1, "onchange=submit()"); ?>
                </td>
            </tr>

		</table>

	</div>
<?php if($id_res > 0 && $id_sensor >0){?>
	<div class="blocoOr">
		<h1>Data integration with another sensors</h1>
		<?php 
			$sql = "SELECT d_equipamento.id as ID, d_equipamento.nome as DESCRICAO FROM d_campo LEFT JOIN d_equipamento ON id_equipamento_fk = d_equipamento.id LEFT JOIN d_regiao ON d_campo.id_regiao_fk = d_regiao.id WHERE d_regiao.id_reservatorio_fk = '$id_res' AND d_equipamento.id <> '$id_sensor' GROUP BY d_equipamento.id ORDER BY DESCRICAO"; 
			$rsSens = pg_query($sql);
		?>

		<table class='tabela_sem_borda'>
<? for ($i = 1; $i <= $qtdmax_integracao && $i <= pg_num_rows($rsSens); $i++) { ?>
                <tr><? $id_c = $i; ?>
                    <td>#<?= $id_c ?><b>Dataset</b>:</td>
					<td>
	                	<?
	                		$arraySensoresInt = getInputFromSQL($rsSens);
	                		inputSelect ( "sensor$id_c", $arraySensoresInt, null, 1, "onchange=submit()" );
	                	?>
	                </td>
				</tr>	
<? } ?>
        </table>
	</div>
<?php }?>
<?php 
// //////////////////////////////////////////////////////////////////////////////
// ////////////////////////////////////////////////////////////////////////////////
// ////////////////////////////////////////////////////////////////////////////////
// ////////////////////////////////////////////////////////////////////////////////
// ////////////////////////////////////////////////////////////////////////////////
$sql1 = "";

 if ($id_sensor > 0) {
 // $tabela = "d_medidas_$sensor";
 $sensor = getSensorFromId($id_sensor);
 $tabela = $tabulacao [$sensor] ["tabela"];
 $linha = $tabulacao [$sensor] ["linha"];
 $coluna = $tabulacao [$sensor] ["coluna"];
 $celula = $tabulacao [$sensor] ["celula"];

	 if ($celula != null)
 		$sel = array_merge ( $coluna, $linha, $celula );
 	else
 		$sel = array_merge ( $coluna, $linha );

 		$ord = array_merge ( $linha, $coluna );
 		$join_i = "";
 		$sel_i = array ();
 		$ord_i = array ();
 		$strOnd = "";
 		$strSel = "";
 		$strOrd = "";

 		// ver sensores integrados
 		for($i = 1; $i <= $qtdmax_integracao; $i ++) {
 			$id_sensor_i = ( int ) post ( "sensor$i" );
 			$sensor_i = getSensorFromId($id_sensor_i);
 			if ($sensor_i > 0) { // integração selecionada
 				$tabela_i = $tabulacao [$sensor_i] ["tabela"];
 			
 				if (isset ( $integracao [$sensor] [$sensor_i] )) { // possível integrar estes sensores
 					$key1 = $integracao [$sensor] [$sensor_i] [0];
 					$key2 = $integracao [$sensor] [$sensor_i] [1];

			 		$linha_i = $tabulacao [$sensor_i] ["linha"];
			 		$coluna_i = $tabulacao [$sensor_i] ["coluna"];
			 		$celula_i = $tabulacao [$sensor_i] ["celula"];


 					$ord_i = array_merge ( $linha_i, $coluna_i );
 					if ($celula_i != null)
 						$sel_i = array_merge ( $coluna_i, $linha_i, $celula_i );
 					else
 						$sel_i = array_merge ( $coluna_i, $linha_i );

 					
 					$strSel .= ", t$i." . implode2D ( ", t$i.", $sel_i );
	 				$join_i .= " INNER JOIN $tabela_i as t$i ON t0.$key1 = t$i.$key2 ";
	 				//$strOnd .= " AND t$i.id_conjunto_fk = '$conjunto_i'";//tirar isso, inserir a restrição para o sensor
	 				$strOrd .= ", t$i." . implode2D ( ", t$i.", $ord_i );
	 				
 				} else {
 					trace0 ( "Warning! Is not possible to integrate the dataset of sensor $sensor with the sensor $sensor_i" );
 					$pagina->addAdvertencia ( "Warning! Is not possible to integrate the dataset of sensor $sensor with the sensor $sensor_i" );
 				}
 			}
 		}

 		// $sql1 = "SELECT * FROM $tabela $join_i WHERE $tabela.id_conjunto_fk = '$conjunto' $strOnd ORDER BY $tabela.".implode2D(", $tabela.", $ord) . " $strOrd ";
 		$sql1 = "SELECT * FROM $tabela as t0 $join_i WHERE 1=1 ORDER BY t0." . implode2D ( ", t0.", $ord ) . " $strOrd LIMIT 1000";
		//echo $sql1;
 		$rsDados1 = pg_query ( $sql1 ) or die ( pg_last_error () );
 }
 				
 			// //////////////////////////////////////////////////////////////////////////////
 			// //////////////////////////////////////////////////////////////////////////////
 			// //////////////////////////////////////////////////////////////////////////////
 			// //////////////////////////////////////////////////////////////////////////////
 			// //////////////////////////////////////////////////////////////////////////////



?>




<?if(isset($rsDados1)){?> 
	<!-- ########################################################################################### -->
	<!-- ########################################################################################### -->
	<!-- ########################################################################################### -->    
    
    <?php if(false){?> 
        <div class="box">
			<div class="box_titulo titulo_expansivel">Data preview</div>
			<div class="conteudo_expansivel" style="display: block">
				<h2>Amount of data found: <?=  pg_num_rows($rsDados1)?></h2>
	           	<?  printTable(pg_fetch_all($rsDados1))?>
	        </div>
		</div>
	<?php }?>
	
	<!-- ########################################################################################### -->
	<!-- ########################################################################################### -->
	<!-- ########################################################################################### -->    
    
    	<?
    	
    	//pega os nomes de tables com apelidos
    	function getd_TablesFromSQL($sql){
    		$tables = array();
    		//trace("SQL: $sql");
    		preg_match_all('~( as \w+)~', $sql, $matches, PREG_PATTERN_ORDER);
    		//print_r($matches);
    		foreach ($matches[1] as $word) {
    			$tables[] = trim(str_replace('as', '', $word));
    		}
    		return $tables;
    	}
    	
    	function getTypeFilterColumn($tabela, $coluna, $valor = null){
    		$t = 'string';
    		$valor = trim($valor);
    		$str_type = getAtrQuery("select data_type from information_schema.columns where table_name = '$tabela' AND column_name = '$coluna' LIMIT 1");
    		//echo "<br>$tabela.$coluna ($valor): $str_type ";
    		
    		if($str_type == 'double precision') $t = 'double';
    		else if($str_type == 'real') $t = 'double';
    		else if($str_type == 'bigint') $t = 'integer';
    		else if($str_type == 'smallint') $t = 'integer';
    		else if($str_type == 'boolean') $t = 'boolean';
    		else if($str_type == 'integer') $t = 'integer';
    		else if($str_type == 'numeric') $t = 'double';
    		else if($str_type == 'timestamp with time zone') $t = 'datetime';
    		else if($str_type == 'timestamp without time zone') $t = 'datetime';
    		else if($str_type == 'numeric') $t = 'double';
    		else if($str_type == 'numeric') $t = 'double';
    		else if($str_type == ""){
    			if(is_bool($valor)) $t = "boolean";
    			else if(is_int($valor)) $t = "integer";
    			else if(is_float($valor)) $t = "double";
    			else if(is_numeric($valor)){ 
    				if(preg_match('/[.]/',$valor)) $t = "double";
    				else if(preg_match('/[,]/',$valor)) $t = "double";
    				else $t = "integer";
    			}

    		}
    		//echo " ($t)   - ";
    		return $t;
    	}
    	
    	function makeFilterRules($filter_tables, $sql_from){
    			$filters  = array();
    			global $campos_consulta; 
    			global $traducao;
    		
    		foreach($filter_tables as $tb){
    			$campos = array_keys(getAtrsQuery("SELECT $tb.* FROM $sql_from LIMIT 1"));
    			foreach($campos as $cp){
    				
    				//echo "$sql_from";
    				/*$rsValor = pg_query("SELECT DISTINCT($tb.$cp) FROM $sql_from LIMIT 10");//lento
	    			$v = array();
	    			if($rsValor != null && pg_num_rows($rsValor) < 100){
		    			while($linha = pg_fetch_assoc($rsValor)){
		    				$v[] = "'".$linha[$cp]."': '".$linha[$cp]."'";	
		    			}
	    			}
	    			//eliminar os parametros que só possuem uma única opção
	    			//if(count($v) == 1) continue;
	    			$values = "";
	    			if(count($v) > 1){
	    				$values = "
							,values: {
								".implode(', ', $v)."
							}, 
							input: 'select'
						";
	    			}
	    			*/
    				$v = getAtrQuery("SELECT $tb.$cp FROM $sql_from LIMIT 1");
	    			$campos_consulta[] = "$tb.$cp";
	    			$lbl = "$tb.$cp";
	    			$lbl = (array_key_exists($lbl, $traducao))?$traducao[$lbl]:$lbl;
    				$filters[] = "
						{
	    					id:'$tb.$cp',
							label:'$lbl',
							type: '".getTypeFilterColumn($tb, $cp, $v)."'
	    				}
					";
    			}
    		}
    		return "[".implode(',', $filters)."]";
    	}
    	
    		$join_data = "LEFT JOIN d_conjunto as d_conjunto ON t0.id_conjunto_fk = d_conjunto.id LEFT JOIN d_campo as d_campo ON id_campo_fk = d_campo.id  LEFT JOIN d_equipamento as d_equipamento ON id_equipamento_fk = d_equipamento.id  LEFT JOIN d_regiao as d_regiao ON id_regiao_fk = d_regiao.id  LEFT JOIN d_reservatorio as d_reservatorio ON id_reservatorio_fk = d_reservatorio.id";
			$where = " d_reservatorio.id = '$id_res' ";
    		$from = " $tabela as t0 $join_i $join_data ";
    		$order = "t0." . implode2D ( ", t0.", $ord ) . " $strOrd ";
			$from_where = " $from WHERE $where";
    		$sql1 = "SELECT * FROM" .$from_where . " ORDER BY $order ";
    		$filter_tables = getd_TablesFromSQL($sql1);
			$campos_consulta = array();
			$filters_txt = makeFilterRules($filter_tables, $from_where);//lento
			
		?>
    	<!-- Modal HTML embedded directly into document -->
	  	<div id="ex1" style="display:none;"></div>
    	<div id="builder-basic"></div>
	<script>
				var last_sql = '';//global var
				var last_input = '';
				var focus_name = '';

				function retornaBox(valor, name){
					$("[name='"+name+"']").val(valor);
					$("[name='"+name+"']").trigger('change');
					$.modal.close();
				}
				
				function mostrarBoxSelecao(coluna, sql, input_name){
					$('#ex1').html("<h1>Loading data...</h1>"); 
					$('#ex1').modal();
    				$('#ex1').on($.modal.AFTER_CLOSE, function(event, modal) {
    					$("[name='"+input_name+"']").focus();
    				});


					$.ajax({
						   url: 'processar_query.php',
						   type: 'POST',
						   dataType: 'json',
						   data: {
							   	"sql_from": "<?=$from?>",
								"coluna": coluna,
								"sql_where": sql,
							    "acao": 'valores_distintos'
						   },
						   error: function(ts) {
						      alert('<p>An error has occurred</p>');
						      alert(ts.responseText);
						      console.log(ts);
						   },
						   success: function(data) {
							   var texto = "<h3>Click to select a value</h3>";
								texto += "<table class='datatable'>";
								texto += "<thead>";
								texto += "<tr><th>Value</th></tr>";
								texto += "</thead>";
								texto += "<tbody>";
								var valores = data.saida.valores;

								for(var v in valores){
									texto += "<tr><td style='text-align:center'>";
									texto += "<a style='cursor:pointer' onclick=\"retornaBox('"+valores[v]+"', '"+input_name+"')\">"+valores[v]+"</a>";
									texto += "</td></tr>";
								}	

								texto += "</tbody>";
								texto += "</table>";

								$('#ex1').html(texto); 
											
								activeDataTable();
								
						   }
					});



				}

				
    			$('#builder-basic').queryBuilder({
    			  plugins: ['bt-tooltip-errors'],
    			  
    			  filters: <?=$filters_txt?>
    			});
    			$('#builder-basic').queryBuilder('setRulesFromSQL', "<?=$where?>");
				
    			$('#builder-basic').on('beforeAddRule.queryBuilder', function(e) {
        			try{
    					last_sql = $('#builder-basic').queryBuilder('getSQL').sql;
        			}catch(err){
        				//last_sql = "";
        			}
    			});

    			$('#builder-basic').off('getRuleInput.queryBuilder');
    			$('#builder-basic').on('getRuleInput.queryBuilder.filter', function(e, rule, name) {
    				last_input = name;
    				});
				
    			$('#builder-basic').off('afterCreateRuleInput.queryBuilder');
    			$('#builder-basic').on('afterCreateRuleInput.queryBuilder.filter', function(e, rule) {
    				$("[name='"+last_input+"']").on( "dblclick", function() {
	    					if(rule.operator.type == 'equal' || rule.operator.type == 'not_equal'){
	    						//console.log( last_sql );
	    						mostrarBoxSelecao(rule.filter.field, last_sql, last_input);
	    					}
  					});
    			});   
    			
    	</script>
	<!-- ########################################################################################### -->
	<!-- ########################################################################################### -->
	<!-- ########################################################################################### -->    
    	<?
					// $sql1 = "SELECT * FROM $tabela as t1 $join_i WHERE t1.id_conjunto_fk = '$conjunto' $strOnd ORDER BY t1.".implode2D(", t1.", $ord) . " $strOrd ";
					pg_result_seek ( $rsDados1, 0 );
					$campos = makeArrayAssoc ( $campos_consulta );
					foreach ($traducao as $c => $v)
						if(array_key_exists($c, $campos))
							$campos[$c] = $v;
					?>
    <table style="display: block; float: left">
		<tr>
			<td>X-axis</td>
			<td><?inputSelect("eixo_x", $campos, null, 1);?></td>
		</tr>
		<tr>
			<td>Y-axis</td>
			<td><?inputSelect("eixo_y", $campos, null, 1);?></td>
		</tr>
		<tr>
			<td>Curve label</td>
			<td><?inputSelect("label", $campos, null, 10, "MULTIPLE");?></td>
		</tr>
		
		<tr>
			<td colspan="2"><?inputButton("Generate/Update the graph", 'gerarGraficoAJAX()')?></td>
		</tr>
		
	</table>
    	<div id="chartContainer" style="height: 400px; width: 66%;"></div>
    	<script>
			function gerarGraficoAJAX(){
				var sql = "<?=$sql1?>";
				var eixo_x = $('select[name=eixo_x]').val();
				var eixo_y = $('select[name=eixo_y]').val();
				var label = $('select[name=label]').val().join(', ');
				
				var eixo_x_en = $('select[name=eixo_x] option:selected').text();
				var eixo_y_en = $('select[name=eixo_y] option:selected').text();
				var label_en = "";
				$('select[name=label] option:selected').each(function() { label_en += $( this ).text() + ",";});
				label_en = label_en.slice(0,-1);
				
				//alert(label);

				/*************fazer requisição ajax**/
				$.ajax({
				   url: 'processar_query.php',
				   type: 'POST',
				   dataType: 'json',
				   data: {
						"sql_from": "<?=$from?>",
						"sql_order": label,
					   "sql_filter": $('#builder-basic').queryBuilder('getSQL').sql,
					   "eixo_x": eixo_x,
					   "eixo_y": eixo_y,
					   "label": label,
					   "eixo_x_en": eixo_x_en,
					   "eixo_y_en": eixo_y_en,
					   "label_en": label_en,
					   "acao": 'dados_grafico'
				   },
				   error: function(ts) {
				      alert('<p>An error has occurred</p>');
				      alert(ts.responseText);
				      console.log(ts);
				   },
				   success: function(data) {
						console.log("Data received: ");
						console.log(JSON.stringify(data));

					   var chart = new CanvasJS.Chart("chartContainer",
							    {
							      zoomEnabled: true,
							      zoomType: "xy",
							      axisY:{
							    	  //minimum: 15,
							    	  //maximum: 26,
							   		},
							      toolTip: {
										shared: false,
										contentFormatter: function (e) {
											var content = " ";
											for (var i = 0; i < e.entries.length; i++) {
												content += "("+e.entries[i].dataSeries.name + ") " + "<strong>(" + e.entries[i].dataPoint.x + ", "+e.entries[i].dataPoint.y+")</strong>";
												content += "<br/>";
											}
											return content;
										}
								  },
							      title:{
							        text: "("+label_en+"): ("+eixo_x_en+", "+eixo_y_en+")"
							      },
							      data: data.curvas,  // random generator below
							
							   });
							
						chart.render();
				   }
				});
					 
			}
    	</script>
     	
    <!-- ########################################################################################### -->
	<!-- ########################################################################################### -->
	<!-- ########################################################################################### -->	
    
<?}?>

</form>

<? include "../layout/baixo.php"?>
