<?php
include_once "SETTINGS.php";
include_once "banco.php";
include_once "phplot.php";
include_once "DynamicForm.php";
include_once "processamento_dados.php";
include_once "bibliotecas_php/PHPExcel-1.8/Classes/PHPExcel.php";

ConectaDB ();

pg_query ( "BEGIN; SET statement_timeout TO 1800000; COMMIT" ) or die ( pg_last_error () );

set_time_limit ( 10800 ); // 3 horas

function getDOMatributte($node, $name){
	$att = $node->attributes;
	foreach($att as $i){
		if($i->name==$name){
			#if(strpos($i->name, $name) !== false)
			#print "</br>".$i->name.": ".$i->value;
			return $i->value;
		}
	}
	return null;
}

function excludeDOMNodes(DOMNode $domNode, $exclude_id, $exclude_class) {
	#print "aqui ";
	$excluiu = false;
	foreach ($domNode->childNodes as $node){
		#print "</br>1: ".($node->attributes);
		if($node->attributes != null){
			$id_node = getDOMatributte($node, 'id');
			$class_node = getDOMatributte($node, 'class');
			if (in_array($id_node, $exclude_id)){
				$domNode->removeChild($node);
				#print "<br />Exclude ".$id_node;
				$excluiu = true;
				continue;
			}
			else if (in_array($class_node, $exclude_class)){
				$domNode->removeChild($node);
				#print "<br />Exclude ".$class_node;
				$excluiu = true;
				continue;
			}
			
		}
		if($node->hasChildNodes()) {
			$excluiu = $excluiu || excludeDOMNodes($node, $exclude_id, $exclude_class);
		}
		#print "acabou";
	}
	return $excluiu;
}

function excludeContent($url, $exclude_id, $exclude_class){
	$out = "";
	$page = file_get_contents($url);
	$doc = new DOMDocument();
	$doc->loadHTML($page);
	
	while ( excludeDOMNodes($doc, $exclude_id, $exclude_class));
	$out = $doc->saveHtml();
	
	return $out;
}

function str_contains($str, $token) {
	if (strpos ( $str, $token ) !== false)
		return true;
	else
		return false;
}
function initTime() {
	// Iniciamos o "contador"
	list ( $usec, $sec ) = explode ( ' ', microtime () );
	$script_start = ( float ) $sec + ( float ) $usec;
	
	return $script_start;
}
function elapsed_time($init) {
	list ( $usec, $sec ) = explode ( ' ', microtime () );
	$script_end = ( float ) $sec + ( float ) $usec;
	$elapsed_time = round ( $script_end - $init, 5 );
	
	return $elapsed_time;
}
function stringArrayTable($a) {
	if (! is_array ( $a )) {
		return "$a";
	}
	$str = "";
	$str .= "<table border=1 width=100%>";
	foreach ( $a as $campo => $valor ) {
		$str .= "<tr>";
		$str .= "<td>$campo</td>";
		$str .= "<td>" . stringArrayTable ( $valor ) . "</td>";
		$str .= "</tr>";
	}
	$str .= "</table>";
	
	return $str;
}
function rs2Array($rs) {
	$a = array ();
	while ( $linha = pg_fetch_array ( $rs ) ) {
		$a [] = $linha [0];
	}
	return $a;
}
function toJSon($array) {
	return json_encode ( $array );
	
	/*
	 * $json = "{";
	 * $json .= toJSon1($array);
	 * $json = substr($json, 0, strlen($json)-1);
	 * $json .="}";
	 *
	 * return $json;
	 */
}
function inteiro($s) {
	return ( int ) preg_replace ( '/[^\-\d]*(\-?\d*).*/', '$1', $s );
}
function getDiaSemana($data_dia) {
	$data = $data_dia;
	$part = strptime ( $data, "%d/%m/%Y" );
	$wday = explode ( "/", sprintf ( "Domingo/%s", implode ( "-feira/", array (
			"Segunda",
			"Terça",
			"Quarta",
			"Quinta",
			"Sexta",
			"Sábado" 
	) ) ) );
	
	return $wday [$part ["tm_wday"]];
}
function formataMoeda($valor) {
	return "R$" . number_format ( $valor, 2, ",", "." );
}
function formataData($sqlData) {
	if ($sqlData != "")
		return date ( "d/m/Y", strtotime ( $sqlData ) );
	else
		return "";
}
function formataDataInsert($sqlData) { // transforma dd/mm/aaaa[ HH:MM] para yyyy-mm-dd HH:MM
	if ($sqlData != "") {
		$part = explode ( " ", $sqlData );
		
		$aux = explode ( "/", $part [0] );
		$nova = $aux [2] . "-" . $aux [1] . "-" . $aux [0];
		
		$aux = explode ( ":", $part [1] );
		if (! (@$aux [0] > 0))
			@$aux [0] = "00";
		if (! (@$aux [1] > 0))
			@$aux [1] = "00";
		if (! (@$aux [2] > 0))
			@$aux [2] = "00";
		@$nova .= " " . @$aux [0] . ":" . @$aux [1] . ":" . @$aux [2];
		return $nova;
	} else
		return "";
}
function formataHorario($sqlData) {
	if ($sqlData != "")
		return date ( "H:i", strtotime ( $sqlData ) );
	return "";
}

/* funcoes de apoio, conversao e criacao de graficos */
function annotate_plot($img, $plot) {
	// We can also use the PHPlot internal function for text.
	// It does the center/bottom alignment calculations for us.
	// Specify the font argument as NULL or '' to use the generic one.
	/*
	 * Draws a block of text. See comments above before ProcessText().
	 * $which_font : PHPlot font array, or NULL or empty string to use 'generic'
	 * $which_angle : Text angle in degrees
	 * $which_xpos, $which_ypos: Reference point for the text
	 * $which_color : GD color index to use for drawing the text
	 * $which_text : The text to draw, with newlines (\n) between lines.
	 * $which_halign : Horizontal (relative to the image) alignment: left, center, or right.
	 * $which_valign : Vertical (relative to the image) alignment: top, center, or bottom.
	 * Note: This function should be considered 'protected', and is not documented for public use.
	 */
	global $texto;
	$black = imagecolorresolve ( $img, 0, 0, 0 ); // black
	$plot->DrawText ( '', // fonte
0, // angulo
$plot->image_width / 2, // coordenada gd x - meio do mapa
$plot->image_height - 10, // coordenada gd y - embaixo + 10px
$black, // cor
$texto, // texto
'center', // alinhamento horizontal
'bottom' ); // alinhamento vertical
}

/**
 * desenha um grafico tipo pizza com parametros especificados
 *
 * @param
 *        	data array de arrays com valores na forma ('atributo', valor)
 * @param
 *        	arq_saida caminho da imagem de saida
 * @param
 *        	largura largura da imagem de saida
 * @param
 *        	altura altura da imagem de saida
 * @param
 *        	titulo do mapa - exemplo: 'Distribuição por idade'
 */
function desenhaPizza($data, $arq_saida, $largura, $altura, $nome_grafico) {
	require_once 'phplot.php'; // inclui a classe
	
	if (count ( $data ) == 0)
		$data [] = array (
				"",
				"" 
		);
	
	$plot = new PHPlot ( $largura, $altura ); // cria objeto
	$plot->setDefaultTTFont ( "../fonts/Vera.ttf" );
	$plot->SetTitle ( utf8_decode ( $nome_grafico ) ); // titulo do grafico
	$plot->SetPlotType ( 'pie' ); // grafico tipo pizza
	$plot->SetDataType ( 'text-data-single' ); // dados da forma texto,valor
	$plot->SetShading ( 0 ); // remove sombra - grafico plano
	$plot->SetLabelScalePosition ( 0.2 ); // posicao dos labels
	$plot->SetPlotAreaPixels ( - $largura / 4 ); // posicao x inicial do desenho
	$plot->SetImageBorderType ( 'plain' ); // borda
	                                    
	// retirando os dados perdidos (*)
	$c_data = array ();
	foreach ( $data as $indice => $row ) {
		if ($row [0] === '*') {
			$miss += $row [1];
			// unset($data[$indice]);
		} else {
			$c_data [] = $row;
		}
	}
	$data = $c_data;
	
	if ($data == null || count ( $data ) == 0)
		$data [] = array (
				"",
				"" 
		);
	
	$plot->SetDataValues ( $data ); // fonte de dados
	
	foreach ( $data as $row ) // legenda
		$plot->SetLegend ( implode ( ': ', $row ) );
	
	$plot->SetIsInline ( true ); // parametros para ger. de arquivo
	$plot->SetOutputFile ( $arq_saida );
	
	if ($miss > 0) {
		// Establish the drawing callback to do the annotation:
		global $texto;
		$texto = "Dados não aproveitados: {$miss}.";
		$plot->SetPlotAreaPixels ( - $largura / 4, null, null, ($altura - 60) );
		$plot->SetCallback ( 'draw_all', 'annotate_plot', $plot );
	}
	
	$plot->DrawGraph (); // desenha
} // fim desenha pizza

/**
 * desenha um grafico tipo colunas/barras com parametros especificados
 *
 * @param
 *        	data array de arrays com valores na forma ('atributo', valor)
 * @param
 *        	arq_saida caminho da imagem de saida
 * @param
 *        	largura largura da imagem de saida
 * @param
 *        	altura altura da imagem de saida
 * @param
 *        	titulo do mapa - exemplo: 'Distribuição por idade'
 * @param
 *        	intervalo_x distancia entre colunas no eixo x
 * @param
 *        	intervalo_y distancia entre delimitadores de valores exibidos do eixo y
 */
function desenhaColunas($data, $arq_saida, $largura, $altura, $nome_grafico, $nome_x, $nome_y, $intervalo_y, $valores2eixo = null) {
	require_once 'phplot.php'; // inclui a classe
	$miss = 0;
	
	if (count ( $data ) == 0)
		$data [] = array (
				"",
				"" 
		);
	
	$plot = new PHPlot ( $largura, $altura ); // cria objeto
	
	$plot->setDefaultTTFont ( "../config/mapfile/fonts/Vera.ttf" );
	$plot->SetTitle ( utf8_decode ( $nome_grafico ) ); // titulo do grafico
	
	$plot->SetXTitle ( utf8_decode ( $nome_x ) ); // titulo - eixo x
	$plot->SetYTitle ( utf8_decode ( $nome_y ) ); // titulo - eixo y
	                                        
	// $plot->SetXTickIncrement($valor);
	
	$plot->SetYTickIncrement ( $intervalo_y ); // incremento de marcadores - eixo y
	$plot->SetPlotAreaWorld ( 0, 0 );
	
	// Turn on Y data labels:
	$plot->SetYDataLabelPos ( 'none' );
	$plot->SetXDataLabelAngle ( 90 );
	// $plot->SetNumYTicks(10);
	
	// $plot->SetBackgroundColor('yellow');
	// $plot->SetTransparentColor('yellow');
	
	// Turn off X axis ticks and labels because they get in the way:
	// $plot->SetXTickLabelPos('none');
	// $plot->SetXTickPos('none');
	
	// With Y data labels, we don't need Y ticks or their labels, so turn them off.
	// $plot->SetYTickLabelPos('none');
	// $plot->SetYTickPos('none');
	
	// $plot->SetPlotAreaWorld(0, 0, 10);
	
	// Desativando marcadores do eixo x
	$plot->SetXTickLabelPos ( 'none' );
	$plot->SetXTickPos ( 'none' );
	
	$plot->SetPlotType ( 'bars' ); // adequado p/ tipo histograma
	$plot->SetDataType ( 'text-data' ); // dados da forma label, valor y
	
	$plot->SetImageBorderType ( 'plain' ); // borda
	                                    
	// retirando os dados perdidos (*)
	$c_data = array ();
	$legend = array ();
	// print_r($data);
	foreach ( $data as $indice => $row ) {
		if ($row [0] === '*') {
			$miss += $row [1];
			$legend [] = "*";
			// unset($data[$indice]);
		} else {
			$c_data [] = $row;
			if (! in_array ( $row [1], $legend ))
				$legend [] = $row [1];
		}
	}
	$data = $c_data;
	if ($valores2eixo == null)
		$plot->SetLegend ( $legend );
	else
		$plot->SetLegend ( $valores2eixo );
	if ($data == null || count ( $data ) == 0)
		$data [] = array (
				"",
				"" 
		);
	
	$plot->SetDataValues ( $data ); // fonte de dados
	
	$plot->SetIsInline ( true ); // parametros para ger. de arquivo
	$plot->SetOutputFile ( $arq_saida );
	
	if ($miss > 0) {
		// Establish the drawing callback to do the annotation:
		global $texto;
		$texto = "Dados não aproveitados: {$miss}.";
		$plot->SetPlotAreaPixels ( null, null, null, ($altura - 60) );
		$plot->SetCallback ( 'draw_all', 'annotate_plot', $plot );
	}
	
	$plot->DrawGraph (); // desenha
} // fim desenha histograma

/* linhas para variacoes de nota */
function desenhaLinhas($data, $arq_saida, $largura, $altura, $nome_grafico, $nome_x, $nome_y, /*$intervalo_x, */$intervalo_y) {
	$maxQtdXLabel = 3;
	require_once 'phplot.php'; // inclui a classe
	
	if (count ( $data ) == 0)
		$data [] = array (
				"",
				"" 
		);
	
	$plot = new PHPlot ( $largura, $altura ); // cria objeto
	$plot->setDefaultTTFont ( "../fonts/Vera.ttf" );
	$plot->SetTitle ( utf8_decode ( $nome_grafico ) ); // titulo do grafico
	$plot->SetXTitle ( utf8_decode ( $nome_x ) ); // titulo - eixo x
	$plot->SetYTitle ( utf8_decode ( $nome_y ) ); // titulo - eixo y
	                                        // $plot->SetXTickIncrement($intervalo_x);
	$plot->SetYTickIncrement ( $intervalo_y ); // incremento - eixo y
	$plot->SetPlotAreaWorld ( NULL, 0, NULL, NULL );
	
	// Desativando marcadores do eixo x
	/*
	 * $plot->SetXTickLabelPos('none');
	 * $plot->SetXTickPos('none');
	 * $plot->SetPlotType('thinbarline'); //adequado p/ tipo linhas
	 * $plot->SetDataType('text-data'); //dados da forma label, valor y
	 * $plot->SetDataValues($data); //fonte de dados
	 * $plot->SetImageBorderType('plain'); //borda
	 *
	 *
	 * # Make the lines wider:
	 * $plot->SetLineWidths(3);
	 * $plot->SetIsInline(true); //parametros para ger. de arquivo
	 * $plot->SetOutputFile($arq_saida);
	 * $plot->DrawGraph(); //desenha
	 */
	$plot->SetImageBorderType ( 'plain' ); // borda
	                                    // $plot->SetXTickLabelPos('none');
	                                    // $plot->SetXTickPos('none');
	$plot->SetPlotType ( 'lines' ); // adequado p/ tipo linhas
	$plot->SetDataType ( 'data-data' ); // dados da forma label, valor y
	$plot->SetLineWidths ( 2 );
	
	// retirando os dados perdidos (*)
	$c_data = array ();
	$i = 1;
	foreach ( $data as $indice => $row ) {
		if ($row [0] === '*') {
			$miss += $row [1];
			// unset($data[$indice]);
		} else {
			$c_data [] = array (
					'',
					$row [0],
					$row [1] 
			);
			// $c_data[] = array($i++, $row[1]);
		}
	}
	$data = $c_data;
	
	if ($data == null || count ( $data ) == 0)
		$data [] = array (
				"",
				"" 
		);
	
	$plot->SetDataValues ( $data ); // fonte de dados
	
	$plot->SetXLabelType ( 'time', '%d/%m/%Y' );
	// if(count($data) < $maxQtdXLabel) $maxQtdXLabel = count($data);
	// $plot->SetNumXTicks($maxQtdXLabel);
	// $plot->SetNumYTicks($maxQtdXLabel);
	// $plot->SetXTickIncrement(2);
	// trace("Quantidade de labels: ".$maxQtdXLabel);
	
	$plot->SetIsInline ( true ); // parametros para ger. de arquivo
	$plot->SetOutputFile ( $arq_saida );
	
	if ($miss > 0) {
		// Establish the drawing callback to do the annotation:
		global $texto;
		$texto = "Dados não aproveitados: {$miss}.";
		$plot->SetPlotAreaPixels ( null, null, null, ($altura - 60) );
		$plot->SetCallback ( 'draw_all', 'annotate_plot', $plot );
	}
	
	$plot->DrawGraph (); // desenha
} // fim desenha linhas
function getVar($var) {
	return getAtrQuery ( "SELECT valor_var FROM variaveis WHERE nome_var = '$var'" );
}
function trace($str) {
	echo "<br/>$str";
	flush ();
	ob_flush ();
}
function permissaoItem($id_ite, $id_gru, $id_usu) {
	$rsPer = pg_query ( "SELECT * FROM itens  i, permissao_grupo p WHERE i.id_ite = p.id_ite_fk AND p.id_gru_fk = '$id_gru' AND i.id_ite = '$id_ite'" ) or die ( pg_last_error () );
	if (pg_num_rows ( $rsPer ) > 0) {
		return true;
	}
	
	$rsPer = pg_query ( "SELECT * FROM itens  i, permissao_grupo p, usuarios_grupos ug WHERE i.id_ite = p.id_ite_fk AND p.id_gru_fk = ug.id_gru_fk AND ug.id_usu_fk = '$id_usu' AND i.id_ite = '$id_ite'" ) or die ( pg_last_error () );
	if (pg_num_rows ( $rsPer ) > 0) {
		return true;
	}
	
	return false;
}
function getLinkItem($id_item) {
	$link = getAtrQuery ( "SELECT link_pag_arq FROM paginas_arquivos WHERE id_ite_fk = '$id_item'" );
	if ($link != "") {
		$dir = getAtrQuery ( "SELECT caminho_dir FROM diretorios, paginas_arquivos WHERE diretorios.id_dir = paginas_arquivos. id_dir_fk AND paginas_arquivos.id_ite_fk = '$id_item'" );
		return $dir . $link;
	}
	$link = getAtrQuery ( "SELECT id_pag_din FROM paginas_dinamicas WHERE id_ite_fk = '$id_item'" );
	if ($link != "")
		return "../p/din.php?p=" . $link;
	return "#";
}
function randomString($dim) {
	$conteudo = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$str = "";
	for($i = 0; $i < $dim; $i ++) {
		$str .= $conteudo {rand ( 0, 35 )};
	}
	
	return $str;
}
function gerar_chave_randomica() {
	$chave = "";
	for($i = 0; $i < 32; $i ++) {
		$chave = substr_replace ( $chave, rand ( 0, 9 ), $i, 1 );
		if (strlen ( $chave ) == 32)
			if (pg_num_rows ( pg_query ( "SELECT id_usu FROM usuarios WHERE chave_usu = '$chave'" ) ) == 0)
				return $chave;
			else if ($i == 31)
				$i = - 1;
	}
}
function email_smtp($para, $assunto, $mensagem, $assinatura = true) {
	// Inclui o arquivo class.phpmailer.php localizado na pasta phpmailer
	require_once ("../config/bibliotecas_php/phpmailer/class.phpmailer.php");
	
	// Inicia a classe PHPMailer
	$mail = new PHPMailer ();
	
	// Define os dados do servidor e tipo de conexão
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsSMTP (); // Define que a mensagem será SMTP
	$mail->SMTPDebug = 1; // enable debug mode
	$mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
	                        

        $email_from = $GLOBALS ['email_contact']['email'];
        $mail->Host = $GLOBALS ['email_contact']['host_smtp']; // Endereço do servidor SMTP
        $mail->Port = $GLOBALS ['email_contact']['port_smtp'];
        $mail->Username = $GLOBALS ['email_contact']['username']; // Usuário do servidor SMTP
        $mail->Password = $GLOBALS ['email_contact']['password']; // Senha do servidor SMTP
        $mail->SMTPSecure = "ssl";



	// Define o remetente
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->FromName = $GLOBALS ['sigla_base']; // Seu nome
	$mail->AddReplyTo ( $email_from, $GLOBALS ['sigla_base'] );
	$mail->From = $email_from; // Seu e-mail
	                                          
	// Define os dados técnicos da Mensagem
	                                          // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->IsHTML ( true ); // Define que o e-mail será enviado como HTML
	                     // $mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)
	$mail->CharSet = "UTF-8";
	// ###################################################################
	if ($assinatura)
		$rodape = "\n
			<a href='" . $GLOBALS ["url_base_full"] . "'>" . $GLOBALS ["nome_base"] . " \n (" . $GLOBALS ["url_base_full"] . ")</a>.";
	
	$message = nl2br2 ( $mensagem . $rodape );
	
	// ###################################################################
	// Define a mensagem (Texto e Assunto)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	$mail->Subject = "[" . $GLOBALS ["sigla_base"] . "] " . $assunto;
	$mail->Body = $message;
	// $mail->AltBody = "Este é o corpo da mensagem de teste, em Texto Plano! \r\n <img src="http://i2.wp.com/blog.thiagobelem.net/wp-includes/images/smilies/icon_smile.gif?w=625" alt=":)" class="wp-smiley" width="15" height="15"> ";
	
	// Define os anexos (opcional)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// $mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf"); // Insere um anexo
	
	// Define os destinatário(s)
	// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// $mail->AddAddress($para);
	// $mail->AddCC($para); // Copia
	$quant = 0;
	$emails_aux = array ();
	$enviados = array ();
	$nao_enviados = array ();
	if (is_array ( $para )) {
		foreach ( $para as $campo => $valor ) {
			// $mail->AddAddress($valor);
			// $mail->AddCC($valor); // Copia
			$mail->AddBCC ( $valor ); // Cópia Oculta
			$emails_aux [] = $valor;
			if (++ $quant > 80) {
				$quant = 0;
				// Envia o e-mail
				if (! $mail->Send ())
					$nao_enviados [] = $emails_aux;
				$emails_aux = array ();
				// Limpa os destinatários e os anexos
				$mail->ClearAllRecipients ();
				$mail->ClearAttachments ();
			}
		}
	} else {
		$emails_aux [] = $para;
		$mail->AddAddress ( $para );
		// $mail->AddCC($para); // Copia
		// $mail->AddBCC($para); // Cópia Oculta
	}
	
	// $enviado = $mail->Send();
	if (! $mail->Send ())
		$nao_enviados [] = $emails_aux;
		// Limpa os destinatários e os anexos
	$mail->ClearAllRecipients ();
	$mail->ClearAttachments ();
	
	// Exibe uma mensagem de resultado
	if (count ( $nao_enviados ) > 0) {
		$saida = "<br />Erro: " . $mail->ErrorInfo;
		$saida .= "<br />Emails não enviados:";
		foreach ( $nao_enviados as $campo => $email ) {
			$saida .= "<br />" . $email . ", ";
		}
		return $saida;
	} else
		return true;
	// die ("Não foi possível enviar o e-mail.<br /><br />"."<b>Informações do erro:</b> <br />" . $mail->ErrorInfo);
	// return $enviado;
}
function email($para, $assunto, $mensagem, $assinatura = true) {
	/*
	 * global $pagina;
	 * $pagina->addAviso("enviado pára $para");
	 * $pagina->addAviso("$assunto: $mensagem");
	 * $pagina->addAviso("####################################");
	 * $pagina->addAviso("####################################");
	 * $pagina->addAviso("####################################");
	 * return;
	 */
	return email_smtp ( $para, $assunto, $mensagem, $assinatura );
}
function __autoload($class_name) {
	include_once "../config/" . $class_name . '.php';
}

// Criptografa a senha para armazenar no banco de dados
function criptografa($senha) {
	return crypt ( $senha, "MP" );
}

/*
 * function br2nl($text) {
 * return preg_replace('/<br\\s*?\/??>/i', '', $text);
 * }
 * function nl2br2($text){
 * return str_replace("\r\n","",trim(nl2br($text)));
 * }
 */

// retorna o atributo unico da SQL
function getAtrQuery($sql) {
	// $con = ConectaDB();
	$rs = pg_query ( $sql/*, $con*/) or die ( pg_last_error () );
	if ($rs) {
		$rs = pg_fetch_row ( $rs );
		return $rs [0];
	}
	
	return "";
	// DesconectaDB($con);
}
// retorna o atributo unico da SQL
function getAtrsQuery($sql) {
	// $con = ConectaDB();
	$rs = pg_query ( $sql/*, $con*/) or die ( pg_last_error () . "<br> $sql");
	if ($rs) {
		$rs = pg_fetch_assoc ( $rs );
		return $rs;
	}
	
	return array ();
	// DesconectaDB($con);
}

// =====================================================================CONTROLE DE USUARIO
function getNomeGrupo() {
	// session_start();
	return $_SESSION ["nome_grupo_user_S"];
}
function getIdGrupo() {
	// session_start();
	return $_SESSION ["id_grupo_user_S"];
}
function getIdEquipe() {
	// session_start();
	return $_SESSION ["id_equipe_user_S"];
}
function getNome() {
	// session_start();
	return $_SESSION ["nome_user_S"];
}
function getId() {
	// session_start();
	return $_SESSION ["id_user_S"];
}
function setNomeGrupo($v) {
	// session_start();
	$_SESSION ["nome_grupo_user_S"] = $v;
}
function setIdGrupo($v) {
	// session_start();
	$_SESSION ["id_grupo_user_S"] = $v;
}
function setIdEquipe($v) {
	// session_start();
	$_SESSION ["id_equipe_user_S"] = $v;
}
function setNome($v) {
	// session_start();
	$_SESSION ["nome_user_S"] = $v;
}
function setId($v) {
	// session_start();
	$_SESSION ["id_user_S"] = $v;
}
function getIdAdminGroup() {
	return 2;
}
function getEmailsAdmins() {
	$rsAdm = pg_query ( "SELECT * FROM integrantes WHERE integrantes.id_gru_FK = '" . getIdAdminGroup () . "'" );
	$dest = "";
	while ( $linha = pg_fetch_assoc ( $rsAdm ) ) {
		$dest .= $linha ["email_integ"] . ", ";
	}
	return $dest;
}
function VerificaPermissao($pagina) {
	if (! PodeVerPagina ( $pagina ))
		die ( "Voce nao tem permissao para ver" );
}
function PodeVerPagina($pagina) {
	$grupo = getIdGrupo ();
	// internauta
	if ($grupo == "") {
		setIdGrupo ( 1 );
		setNome ( "Internauta" );
		setNomeGrupo ( "Internauta" );
		$grupo = 1;
	}
	
	$sql = "SELECT * FROM permissao_grupo, paginas
			WHERE permissao_grupo.id_pag_FK = paginas.id_pag AND permissao_grupo.id_gru_FK =  '$grupo' AND paginas.caminho_pag = '$pagina'";
	
	$res = pg_query ( $sql ) or die ( pg_last_error () );
	if (pg_num_rows ( $res ) != 1)
		return false;
	
	return true;
}
function UltimoIDInserido(/*$conexao*/){
	$res = pg_fetch_row ( pg_query ( "SELECT LASTVAL()"/*, $conexao*/) );
	$id = $res [0];
	return $id;
}
function getUltimoIDInserido($tabela = "arquivos", $id = "id_arq") {
	$res = pg_fetch_row ( pg_query ( "SELECT currval(pg_get_serial_sequence('$tabela','$id'))" ) );
	$id = $res [0];
	return $id;
}

/*
 * function post($post)
 * {
 * // $var = htmlspecialchars(trim($_POST["$post"]));
 * $var = (trim($_POST["$post"]));
 * $var = str_replace("'", "`", $var);
 * return $var;
 * }
 *
 * function get($get)
 * {
 * // $var = htmlspecialchars(trim($_GET["$get"]));
 * $var = trim($_GET["$get"]);
 * $var = str_replace("'", "`", $var);
 * return $var;
 * }
 */
function ArquivoPermitido($nome) {
	$arquivo = isset ( $_FILES [$nome] ) ? $_FILES [$nome] : FALSE;
	if ($arquivo ["name"] == "" && $nome == "") {
		echo "<br />Nome de arquivo vazio";
		return false;
	} 	/*
	 * else if (
	 * ($arquivo["type"] != "application/msword") &&
	 * ($arquivo["type"] != "text/plain") &&
	 * ($arquivo["type"] != "text/richtext") &&
	 * ($arquivo["type"] != "application/vnd.ms-excel") &&
	 * ($arquivo["type"] != "application/zip") &&
	 * ($arquivo["type"] != "application/x-zip-compressed") &&
	 * ($arquivo["type"] != "application/rar") &&
	 * ($arquivo["type"] != "application/x-rar") &&
	 * ($arquivo["type"] != "application/x-php") &&
	 * ($arquivo["type"] != "application/octet-stream") &&
	 * ($arquivo["type"] != "text/html") && (0!=0)
	 * ){
	 * echo "<br />Arquivo com extensão ".$arquivo["type"];
	 * echo "<br />São permitidos apenas arquivos .zip, .rar, Word, Excel, .txt, .rtx";
	 * return false;
	 * }
	 * else if ($arquivo["size"] > (10*1024*1024)){ //10MB
	 * echo "<br />Arquivo muito grande, tamanho: ".($arquivo["size"]/1024.0)." KB";
	 * echo "<br />São permitidos apenas arquivos menores que 10 MB";
	 * return false;
	 * }
	 */
	else {
		return true;
	}
}
function getExtensao($mime) {
	if ($mime == "application/msword")
		return ".doc";
	if ($mime == "text/plain")
		return ".txt";
	if ($mime == "text/richtext")
		return ".rtx";
	if ($mime == "application/vnd.ms-excel")
		return ".xlt";
	if ($mime == "application/zip")
		return ".zip";
	if ($mime == "application/x-rar")
		return ".rar";
	if ($mime == "application/rar")
		return ".rar";
	if ($mime == "application/x-php")
		return "";
	if ($mime == "text/html")
		return "";
	if ($mime == "application/octet-stream")
		return "";
}
function fileUploadError($error) {
	$message = "";
	switch ($error) {
		case UPLOAD_ERR_OK :
			$message = false;
			;
			break;
		case UPLOAD_ERR_INI_SIZE :
		case UPLOAD_ERR_FORM_SIZE :
			$message .= ' - file too large (limit of bytes).';
			break;
		case UPLOAD_ERR_PARTIAL :
			$message .= ' - file upload was not completed.';
			break;
		case UPLOAD_ERR_NO_FILE :
			$message .= ' - zero-length file uploaded.';
			break;
		default :
			$message .= ' - internal error #' . $_FILES ['newfile'] ['error'];
			break;
	}
	return $message;
}
function SalvaArquivoServidor($nome, $pasta = "../imagens/", $nome_arquivo = "") {
	$arq = $_FILES [$nome];
	$file_temp = $arq ["tmp_name"];
	$nome_orig = $arq ["name"];
	
	if ($nome_arquivo != "")
		$novo_nome = $pasta . $nome_arquivo;
	else
		$novo_nome = $pasta . $nome_orig;
	if ($novo_nome != "") {
		if (ArquivoPermitido ( $nome )) {
			// ENVIA O ARQUIVO PARA A PASTA
			if (! copy ( $file_temp, $novo_nome )) {
				die ( "Erro 654: Ocorreu um erro na cópia do arquivo $file_temp para o servidor $novo_nome: " . fileUploadError ( $arq ["error"] ) );
			} else {
				return $novo_nome;
			}
		} else {
			die ( "Tipo de Arquivo não permitido" );
		}
	} else
		die ( "ERRO 123: Erro ao configurar novo nome do arquivo no servidor" );
}
function MoveArquivoServidor($arquivo, $pasta = "../imagens/", $nome_arquivo = "") {
	$file_temp = $arquivo;
	$nome_orig = getNomeArquivoPath ( $arquivo );
	
	if ($nome_arquivo != "")
		$novo_nome = $pasta . $nome_arquivo;
	else
		$novo_nome = $pasta . $nome_orig;
	if ($novo_nome != "") {
		if (ArquivoPermitido ( $nome_orig )) {
			// ENVIA O ARQUIVO PARA A PASTA
			if (! copy ( $file_temp, $novo_nome )) {
				die ( "Erro 654: Ocorreu um erro na cópia do arquivo $file_temp para o servidor $novo_nome: " . fileUploadError ( $arq ["error"] ) );
			} else {
				return $novo_nome;
			}
		} else {
			die ( "Tipo de Arquivo não permitido" );
		}
	} else
		die ( "ERRO 123: Erro ao configurar novo nome do arquivo no servidor" );
}
function getStringConsulta($sql/*, $con*/){
	$rs = pg_query ( $sql/*, $con*/) or die ( pg_last_error () );
	$rs = pg_fetch_array ( $rs );
	return $rs [0];
}

// ######################################################################
// ######################################################################
// ######################################################################
// ######################################################################
// ######################################################################
// ######################################################################
// ######################################################################
// ######################################################################
// ######################################################################

// getVarConfig("URL_INTERFACE_SELECAO")
// http://200.145.1.72:8080/Unesp/
function getVarConfig($var) {
	// ##################################################################
	$arquivoConfig = "/etc/conf/parametros.conf";
	// ABRE O ARQUIVO TXT
	$ponteiro = fopen ( $arquivoConfig, "r" );
	
	// LÊ O ARQUIVO ATÉ CHEGAR AO FIM
	while ( ! feof ( $ponteiro ) ) {
		// ////LÊ UMA LINHA DO ARQUIVO
		$linha = trim ( fgets ( $ponteiro, 4096 ) );
		
		$comando = explode ( " ", $linha );
		// IMPRIME NA TELA O RESULTADO
		if (trim ( $comando [0] ) == trim ( $var )) {
			return trim ( $comando [1] );
		}
	} // FECHA WHILE
	  // FECHA O PONTEIRO DO ARQUIVO
	fclose ( $ponteiro );
	return "";
}
function br2nl($text) {
	return preg_replace ( '/<br\\s*?\/??>/i', '', $text );
}
function nl2br2($text) {
	return str_replace ( "\r\n", "", trim ( nl2br ( $text ) ) );
}
function tempo() {
	$sec = explode ( " ", microtime () );
	$tempo = $sec [1] + $sec [0];
	return $tempo;
}
function getUrlAnterior() {
	if (array_shift ( explode ( "?", $_SERVER ["HTTP_REFERER"] ) ) != array_shift ( explode ( "?", $_SERVER ["PHP_SELF"] ) ))
		$_SESSION ["p_anterior"] = $_SERVER ["HTTP_REFERER"];
	
	return $_SESSION ["p_anterior"];
}
function post($post) {
	// $var = htmlspecialchars(trim($_POST["$post"]));
	$var = (trim ( @$_POST ["$post"] ));
	$var = str_replace ( "'", "`", $var );
	return $var;
}
function post2($post) {
	// $var = htmlspecialchars(trim($_POST["$post"]));
	$var = (trim ( @$_POST ["$post"] ));
	//$var = str_replace ( "'", "`", $var );
	return $var;
}
function get($get) {
	// $var = htmlspecialchars(trim($_GET["$get"]));
	$var = trim ( @$_GET ["$get"] );
	$var = str_replace ( "'", "`", $var );
	return $var;
}
function isAdmin() {
	if (isLogado ()) {
		$email = getEmailUser ();
		if (pg_num_rows ( pg_query ( "SELECT * FROM system_users WHERE email = '$email' AND tipo_usuario = '1'" ) ) > 0) {
			return true;
		}
	}
	return false;
}
function isLogado() {
	$email = getEmailUser ();
	$senha = getSenhaUser ();
	
	if ($email != "" && $senha != "")
		return true;
	else
		return false;
}

/*
 * function verificaPermissao(){
 * $email = getEmailUser();
 * $senha = getSenhaUser();
 * setPaginaAnterior();
 * if($email != "" && $senha != ""){
 * if(pg_num_rows(pg_query("SELECT * FROM system_users WHERE email = '$email' AND senha = '$senha'")) != 1){
 * header("Location: ../p/login.php");
 * die("Redirecionando...");
 * }
 * }
 * else{
 * header("Location: ../p/login.php");
 * die("Redirecionando...");
 * }
 * }
 */
function setPaginaAnterior() {
	if (array_shift ( explode ( "?", $_SERVER ["REQUEST_URI"] ) ) != array_shift ( explode ( "?", $_SESSION ["pagina_atual"] ) )) {
		$_SESSION ["pagina_anterior"] = $_SESSION ["pagina_atual"];
	}
	$_SESSION ["pagina_anterior"] = $_SERVER ["REQUEST_URI"];
}
function getPaginaAnterior() {
	return $_SESSION ["pagina_anterior"];
}

// //////////////////////////////////////////////////////////////////////
function loginUser($email, $senha) {
	setEmailUser ( $email );
	setSenhaUser ( $senha );
}
function logoutUser() {
	setEmailUser ( '' );
	setSenhaUser ( '' );
}

// //////////////////////////////////////////////////////////////////////
function setEmailUser($email) {
	$_SESSION ["email"] = $email;
}
function setSenhaUser($senha) {
	$_SESSION ["senha"] = $senha;
}
function getEmailUser() {
	return @$_SESSION ["email"];
}
function getSenhaUser() {
	return @$_SESSION ["senha"];
}
// //////////////////////////////////////////////////////////////////////
function getProxCampo(&$str, $tokens) {
	$campo = array ();
	
	for($i = 0; $i < count ( $tokens ); $i ++) {
		$token = $tokens [$i];
		if (strpos ( $str, $token ) === 0) {
			$campo ["nome"] = $token;
			$str = substr ( $str, strlen ( $token ), strlen ( $str ) - strlen ( $token ) );
			$ids = "";
			for($j = 0; $j < strlen ( $str ); $j ++) {
				if (preg_match ( "/[0-9-]/", $str [$j] )) {
					$ids .= $str [$j];
				} else
					break;
			}
			$str = substr ( $str, strlen ( $ids ), strlen ( $str ) - strlen ( $ids ) );
			
			$ids [0] = ($ids [0] == "-") ? "#" : $ids [0];
			$ids = str_replace ( "--", ",#", $ids );
			$ids = str_replace ( "-", ",", $ids );
			$ids = str_replace ( "#", "-", $ids );
			$campo ["ids"] = "";
			
			for($j = 0; $j < strlen ( $ids ); $j ++) {
				$ultimo_char_inserido = substr ( $campo ["ids"], - 1, 1 );
				if ($ids [$j] == ",") {
					if (preg_match ( "/[0-9]/", $ultimo_char_inserido )) { // anterior é numero
						if (preg_match ( "/[0-9]/", $ids [$j + 1] ) || preg_match ( "/[-]/", $ids [$j + 1] )) { // proximo é numero ou sinal
							$campo ["ids"] .= $ids [$j];
						}
					}
				} else if ($ids [$j] == "-") {
					if ($ultimo_char_inserido == "" || $ultimo_char_inserido == ",") { // anterior é numero
						if (preg_match ( "/[0-9]/", $ids [$j + 1] )) { // proximo é numero
							$campo ["ids"] .= $ids [$j];
						}
					}
				} else {
					$campo ["ids"] .= $ids [$j];
				}
			}
			
			break;
		}
	}
	
	if ($campo ["ids"] != "")
		return $campo;
	else
		return array ();
}
function formatSqlWhere($str, $tokens = array("portal", "curso", "usuario")) {
	$sql = "( (1<>1) ";
	
	$str = str_replace ( " ", "", $str );
	$campo = getProxCampo ( $str, $tokens );
	while ( $campo ["nome"] != "" ) {
		if ($campo ["nome"] == "portal") {
			$sql .= " OR ( portal.id_portal IN ( " . $campo ["ids"] . " )";
			$campo = getProxCampo ( $str, $tokens );
			if ($campo ["nome"] == "curso") {
				$sql .= " AND curso.id IN ( " . $campo ["ids"] . " )";
				$campo = getProxCampo ( $str, $tokens );
				if ($campo ["nome"] == "usuario") {
					$sql .= " AND usuario.id IN ( " . $campo ["ids"] . " )";
				}
				if ($campo ["nome"] == "forum") {
					$sql .= " AND forum.id IN ( " . $campo ["ids"] . " )";
				}
			}
			$sql .= ")";
		} else {
			$campo = getProxCampo ( $str, $tokens );
		}
	}
	$sql .= " ) AND ";
	
	return $sql;
}
/*
 * function SalvaArquivoServidor($nome, $pasta = "../tmp/", $nome_arquivo = ""){
 * $arq = $_FILES[$nome];
 * $file_temp = $arq["tmp_name"];
 * $nome_orig = $arq["name"];
 * $ext_orig = end(explode(".", strtolower($nome_orig)));
 * //$novo_nome = $pasta . str_replace(" ", "_", $nome_arquivo).getExtensao($arq["type"]);
 * if($nome_arquivo != "")
 * $novo_nome = $pasta . $nome_arquivo;
 * else
 * $novo_nome = $pasta . $nome_orig;
 * if($novo_nome != ""){
 * //////ENVIA O ARQUIVO PARA A PASTA
 * if(!copy($file_temp, $novo_nome)){
 * die("Erro 654: Ocorreu um erro na cópia do arquivo $novo_nome para o servidor!");
 * }
 * else{
 * return $novo_nome;
 * }
 *
 * }
 * else
 * die("ERRO 123: Erro ao configurar novo nome do arquivo no servidor");
 * }
 */

?>
