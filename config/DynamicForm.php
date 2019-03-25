<?

function makeArrayAssoc($arr){
	$n_arr = array();
	foreach($arr as $a){
		$n_arr["$a"] = $a;
	}
	return $n_arr;
}

function getInputFromSQL($resSQL){
	$array_dados = array();
	if($resSQL){
		pg_result_seek($resSQL, 0);
		while ($linha = pg_fetch_assoc($resSQL)){
			$array_dados[$linha["id"]] = $linha["descricao"];
		}
	}
	return $array_dados;
}

function previaArquivo($caminho, $tamanho){
	$extensao = end(explode(".", strtolower($caminho)));
	if($extensao == "jpg" || $extensao == "bmp" || $extensao == "png" || $extensao == "gif"){
		
		echo "<br><img src='$caminho' width='$tamanho' /></br>";
	}
}

function inputText($name, $value=null, $size=60, $maxlength=255, $other=""){
	$valor = (($value!==null)?$value:post("$name"));
	$valor = br2nl($valor);
	echo "<input type='text' $other name='$name' value='" . $valor . "' size='$size' maxlength='$maxlength' />";
}

function inputData($name, $value=null, $size=100, $maxlength=255, $other=""){
	$valor = (($value!==null)?formataData($valor) . " " . formataHorario($valor):@$_POST["$name"]);
	//$valor = br2nl($valor);
	echo "<input type='text' class='DatepickerField' placeholder='dd/mm/aaaa HH:MM:SS' $other name='$name' value='" .$valor. "' size='$size' maxlength='$maxlength' />";
}


function inputTextarea($name, $value=null, $rows=10, $cols=10){
	echo "<textarea class='ckeditor' name='$name' rows='$rows' cols='$cols' >" . (($value!==null)?$value:post("$name")) . "</textarea>";
}

function inputHidden($name, $value=null){
	echo "<input type='hidden' name='$name' value='" . (($value!==null)?$value:post("$name")) . "' />";
}

function inputPassword($name, $value=null, $size=100, $maxlength=100){
	echo "<input type='password' name='$name' value='" . (($value!==null)?$value:post("$name")) . "' size='$size' maxlength='$maxlength' />";
}

function inputRadiobutton($name, $value, $selecionado = null){
	$selecionado = ($selecionado!==null)?$selecionado : post("$name");
	echo "<input type='radio' name='$name' value='$value' " . (($selecionado == "$value")?" CHECKED ":"") . "/>";
	
}

function inputCheckbox($name, $value, $selecionado = null){
	$selecionado = ($selecionado!==null)?$selecionado : post("$name");
	echo "<input type='checkbox' name='$name' value='$value' " . (($selecionado == "$value")?" CHECKED ":"") . "/>";
}

function inputSelect($name, $value, $selecionado = null, $size=1, $other="", $default=""){
	$selecionado = ($selecionado!==null)?$selecionado : post("$name");
	$default = ($default!=="")?$default : "SELECT";
	
	echo "<select name='$name' size='$size' $other >";
			echo "<option value='' >$default</option>";
		foreach($value as $campo=>$valor)	
			echo "<option value='$campo'" . (($selecionado == "$campo")?" SELECTED ":"") . ">$valor</option>";

	echo "</select>";
}

function inputFile($name, $size = 50){
	echo "<input type='file' name='$name' size='$size' class='jfilestyle' data-theme='black'/>";
}


function inputSubmit($value, $name = ''){
	echo "<input type='submit' value='$value' name='$name'/>";
}

function inputReset($value){
	echo "<input type='reset' value='$value' />";
}

function inputButton($value, $function=''){
	echo "<input type='button' value='$value' onClick='$function'/>";
}

function inputImage($src, $function=''){
	echo "<input type='image' class='image' src='$src' onClick='$function'/>";
}




?>
