<?

include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

$pagina = new Pagina("principal.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

include "../layout/cima.php";

?>
<style>
	#conteudo{
		width:97% !important;
	}

</style>

			
	<p>Em construção</p>


	<?include "../layout/baixo.php"?>
