<?

include_once("../config/funcoes.php");
include_once("../config/DynamicForm.php");

//$con = ConectaDB();

if(!isset($tabela)){
	/*$tabela["nome"] = "paginas_menu";
	$tabela["identificador"] = "id_pag";
	$tabela["nome_identificador"] = "ID";
	$tabela["pagina"] = "registrar.php";
	*/
	die("Dados da tabela não estão setados!");
}
if(!isset($campos)){
	/*
	$arrayMenu = getInputFromSQL(pg_query("SELECT id_men AS ID, nome_men AS DESCRICAO FROM menus ORDER BY posicao_men, nome_men"));
	$arrayVisivel = array("1" => "Sim", "0" => "Não");

	$campos["id_men_fk"] = array("nome" => "Menu", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayMenu);
	$campos["posicao_pag"] = array("nome" => "Posição", "tipo" => "TEXT", "editavel" => true, "tamanho" => "2");
	$campos["nome_pag"] = array("nome" => "Nome da página", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["caminho_pag"] = array("nome" => "Link", "tipo" => "TEXT", "editavel" => true, "tamanho" => "30");
	$campos["visivel_pag"] = array("nome" => "Visível no Menu", "tipo" => "SELECT", "editavel" => true, "valores" => $arrayVisivel);
	*/
	die("Dados dos campos da tabela não estão setados!");
}

$acao = get("ACAO");
$id = get("ID");
$msg = "";

if($acao == "remover"){
	if($id == "") $entradas = $_POST["entrada"];
	else $entradas = array( 0 => $id);
	if(count($entradas) > 0){
		$whereEntradas = $entradas[0];
		for($i=1; $i<count($entradas); $i++){
			$whereEntradas .= ", ".(int)$entradas[$i];
		}
		pg_query("DELETE FROM ".$tabela["nome"]." WHERE ".$tabela["identificador"]." IN ($whereEntradas)") or die(pg_last_error());
		$msg = count($entradas)." registros foram excluídos com sucesso!";
	}
	else
		$msg = "Selecione alguma entrada!";
}
else if($acao == "alterar"){
		if(count($_POST) > 0 || count($_FILES) > 0){
			$strSet = "";	
			foreach($campos as $campo => $valor){
				if($valor["editavel"]){
					if($valor["tipo"] == "FILE"){
						if($_FILES["$campo"] != ""){
							$id_dir = post($valor["diretorio"]);
							$dir = getAtrQuery("SELECT caminho_dir FROM diretorios WHERE id_dir = '$id_dir'");
							if(SalvaArquivoServidor($campo, $dir) != ""){
								$strSet .= "$campo = '".$_FILES["$campo"]["name"]."',";
							}
						}
					}
					else if($valor["tipo"] == "DATA"){
							$strSet .= "$campo = '".formataDataInsert(post($campo))."',";
					}
					else if(post("$campo") != ""){
								$strSet .= "$campo = '".post("$campo")."',";
					}
				}
			}
			$strSet = substr($strSet, 0, strlen($strSet)-1);
			
			pg_query("UPDATE ".$tabela["nome"]." SET $strSet WHERE ".$tabela["identificador"]." = '$id'") or die(pg_last_error());
			$msg = "Alteração efetuada com sucesso!";
		}
}
else if($acao == "inserir"){
	if(count($_POST) > 0 || count($_FILES) > 0){
		$strCampos = $strValores = "(";
		foreach($campos as $campo => $valor){
			if($valor["editavel"]){
				if($valor["tipo"] == "FILE"){
					if($_FILES["$campo"] != ""){
						$id_dir = post($valor["diretorio"]);
						$dir = getAtrQuery("SELECT caminho_dir FROM diretorios WHERE id_dir = '$id_dir'");
						if(SalvaArquivoServidor($campo, $dir) != ""){
							$strValores .= "'".$_FILES["$campo"]["name"]."',";
							$strCampos .= "$campo,";
						}
					}
				}
				else if($valor["tipo"] == "DATA"){
							$strValores .= "'".formataDataInsert(post($campo))."',";
							$strCampos .= "$campo,";
				}
				else if(post("$campo") != ""){
							$strValores .= "'".post($campo)."',";
							$strCampos .= "$campo,";
				}
			}
		}
		$strCampos = substr($strCampos, 0, strlen($strCampos)-1);
		$strValores = substr($strValores, 0, strlen($strValores)-1);
		$strCampos .= ")";
		$strValores .= ")";
		
		pg_query("INSERT INTO ".$tabela["nome"]." $strCampos VALUES $strValores") or die("Erro: ".pg_last_error());
		$msg = "Registro adicionado com sucesso!";
	}
}
//else{ listar
	$strCampos = "";
	$campo_ordenar = post("order_by");
	foreach($campos as $campo => $valor){
		if($campo_ordenar != ""){
			if($campo_ordenar == $valor["nome"]){
				$strCampos = $campo;
			}
		}
		else
			$strCampos .= "$campo,";
	}
	if($campo_ordenar == "")
		$strCampos = substr($strCampos, 0, strlen($strCampos)-1);
	$strWhere = " 1 = 1 ";
	if($id != "") $strWhere .= " AND ".$tabela["identificador"]." = '$id' ";
	foreach($campos as $campo => $value){
		
		if($value["tipo"] == "DATA"){
				if(post($campo) != "")
					$post_campo= formataDataInsert(post($campo));
		}
		else $post_campo = post($campo);
		
		$post_campo = post($campo);
		if($post_campo != "" ){
			$strWhere .= "AND $campo::text LIKE '%$post_campo%' ";
		}		
	}
	
	$res = pg_query("SELECT * FROM ".$tabela["nome"]." WHERE $strWhere ORDER BY $strCampos") or die(pg_last_error());
//}
//DesconectaDB($con);
?>
<?include("../layout/cima.php");?>

	<link rel="stylesheet" href="../registrar/estilo.css" type="text/css" />
	<script>
		function removerDados(param){
			var msg = ""
			if ( typeof param != 'undefined' ) {
				msg = "Deseja realmente excluir esta entrada selecionada?";
				if(confirm(msg)){
					window.location = "<?echo $tabela["pagina"]?>?ACAO=remover&ID="+param;
				}
			}
			else{
				msg = "Deseja realmente excluir todos os itens selecionados?";
				if(confirm(msg)){
					var formListagem = document.getElementById("formListagem");
					formListagem.action = "<?echo $tabela["pagina"]?>?ACAO=remover";
					formListagem.submit();
				}
			}
		}
		function alterarDados(param){
			window.location = "<?echo $tabela["pagina"]?>?ACAO=alterar&ID="+param;
		}

		function selecionarCheckbox(nome, status){
			var cbs = document.getElementsByName(nome);
			var i;
			for(i=0; i<cbs.length; i++)
				cbs[i].checked = status;
		}

		function ordenar(nome_campo){
			var formListagem = document.getElementById("formListagem");
			formListagem.order_by.value = nome_campo;
			formListagem.action = "<?echo $tabela["pagina"]?>?ACAO=listar";
			formListagem.submit();
		}

		
	</script>
	<?if($msg){?>
		<script>alert('<?echo $msg?>')</script>
	<?}?>
<?

	if($acao == "alterar") include("alterar.php.inc");
	else if($acao == "remover") include("listar.php.inc");
	else if($acao == "inserir") include("inserir.php.inc");
	else include("listar.php.inc");
?>

<?include("../layout/baixo.php");?>

