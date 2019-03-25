<?
include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

	$pagina = new Pagina("mapa_site.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

?>
<?include("../layout/cima.php");?>

	<h1>Site Map</h1>

	
		<?
			$mostrar_ocultos = 1;
			require("../layout/menu.php");
		?>
	

<?include("../layout/baixo.php");?>

