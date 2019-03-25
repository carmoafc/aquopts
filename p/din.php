<?
	include_once "../config/funcoes.php";
	$pagina = new Pagina("", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


	$id_pag = (int)get("p");

	if($id_pag > 0){
		$textoPag = getAtrQuery("SELECT texto_pag_din FROM paginas_dinamicas WHERE id_pag_din = '$id_pag'");
	}


	include "../layout/cima.php"
?>

	<?echo $textoPag;?>
	

<?include "../layout/baixo.php"?>
