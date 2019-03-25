<?
	include_once "../config/funcoes.php";
	$pagina = new Pagina("", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


	$pagina->addAviso("Restrict Access!");
?>

<?include "../layout/cima.php"?>


<img style="float:left" src="../layout/imagens/cadeado_restrito.png" />

<p>Some resources are only available for authorized access!</p>

<p>Public information can be found by accessing the main menu.</p>

<p>The other resources, including the insertion, processing and analysis of the data set, can be used with authorized access and identification by login and password.</p>

<p>Please, use the contact form to require your access.</p>

<?include "../layout/baixo.php"?>
