<?
include_once "../config/funcoes.php";

$pagina = new Pagina("contato.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

?>

<?include "../layout/cima.php"?>

<h1>Our team</h1>


<?=excludeContent('http://sertie.fct.unesp.br/equipe/', array("main-navigation", "primary"), array("header", "section section-blog-info", "footer footer-black footer-big"))?>

<?include "../layout/baixo.php"?>
