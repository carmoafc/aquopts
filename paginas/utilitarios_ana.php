<?

include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

$pagina = new Pagina("utilitarios_ana.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

ini_set('post_max_size', '50M');
ini_set('upload_max_filesize', '50M');

$diretorio_arquivos = "../arquivos_utilitarios/";
$separadores = array("\t" => "TAB", "," => "Virgula", ";" => "Ponto e Virgula", " " => "Espaço");
$dias_meses = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

echo date('Y-m-d',strtotime('10/16/2003'));

if(get("a") == "inc"){
	foreach($_FILES as $campo_arq => $file){
		$nome_original = $file["name"];
		if($nome_original != ""){
			$pagina->addAviso("Processando: ".$file["name"]);
			$partes = explode(".", strtolower($nome_original));
			$nome = $partes[0];
			$extensao = $partes[1];
			$nome_novo = $nome.".".$extensao.".".date("YmdHis");
			//$pagina->addAviso("Salvando: ".$nome_novo);
			if(SalvaArquivoServidor($campo_arq, $diretorio_arquivos, $nome_novo) != ""){
				$caminho_arquivo = $diretorio_arquivos . $nome_novo;		
				$separador = $_POST["ana_separador"];
				$ana_vazio = $_POST["ana_vazio"];
				$ana_data = $_POST["ana_data"];
				
				$file_handle = fopen($caminho_arquivo, "r");
				
				//printFileCSV($file_handle, 10, $separador);
				//return;
				
				$line_of_text = fgetcsv($file_handle, 0, $separador); //linha de cabeçalho
				
				$quant = 0;
				$saida = array();
				$dia_ant = "";
				for ($linha = 0;!feof($file_handle)/* && $quant < 80*/; $linha++) {
					$line_of_text = fgetcsv($file_handle, 0, $separador);
					
					$data = $line_of_text[0];
					$dia = date('d', $data);
					$mes = date('m', $data);
					
					if($linha == 0){	
						$saida[] = $data;
						//echo $data . "<br />";
					}
					for($i = 1; $i < count($line_of_text); $i++){//para cada parâmetro da linha
						//echo $line_of_text[$i] . "<br />";
						$valor = $line_of_text[$i];
						if($valor == "") $valor = $ana_vazio;
						$saida[] = $valor;
						$quant++;
					}
				}		
				
				exportarVetorTxt($saida, $nome . "_processado");
				
			}
			else{
				$pagina->addAdvertencia("We have problem to save the file $nome_original");
			}
		}
	}
}







include "../layout/cima.php";

?>


<form action="utilitarios_ana.php?a=inc" method="post" enctype='multipart/form-data'>

<h1>Interface to process the data from ANA system</h1>
<div class="blocoOr">
	<h2>1 - Guidelines</h2>
	<p>This interface process the data from a specific ANA station</p>
	<p>Usuallt the ANA data are stored on a bidimentional table. This tool transform the table to sequential data.</p>
	
	<p>After the processing, the data will be available to download.</p>
</div>

<div class="blocoOr">
	<h2>2 - dataset from ANA in CSV structure:</h2>
	
	<table>
		<tr>
			<th>Structure</th>
			<th>File</th>
			<th>Settings</th>
		</tr>
		<?//para cada arquivo, selecionar o equipamento?>
		<tr>
			<td><b>ANA</b></td>
			<td><?inputFile("ana_arquivo", "80%")?></td>
			<td>
				<div class="box">
					<div class="box_titulo titulo_expansivel">Edit</div>
					<div class="conteudo_expansivel">
						<table>
							<tr>
								<td>separator</td>
								<td><?inputSelect("ana_separador", $separadores, ";")?></td>
							</tr>
							
							<tr>
								<td>Fill empty data</td>
								<td><?inputText("ana_vazio", "0")?></td>
							</tr>
							
							<tr>
								<td>Date format</td>
								<td><?inputText("ana_data", "mm/dd/yyyy")?></td>
							</tr>
							
						</table>
					</div>
				</div>
			</td>
		</tr>
		
	</table>
	
	
</div>


<div class="blocoOr">

	<h2>3 - Processing</h2>
	
	<p>Start the dataset processing.</p>
	<p>The server will read all dataset files and will process using the defined settings.</p>
	<p>The time of processing is dependent of the amount of data. In some cases, this process will require some minutes.</p>
	<p><b>Do not refresh the page until the finish of processing!</b></p>
	
	
	<? inputSubmit("Iniciar processamento")?>	
	
</div>


</form>

<?include "../layout/baixo.php"?>
