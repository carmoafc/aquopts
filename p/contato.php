<?
include_once "../config/funcoes.php";

$pagina = new Pagina("contato.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


function secure($string) {
	$string = strip_tags($string);
	$string = htmlspecialchars($string);
	$string = trim($string);
	$string = stripslashes($string);
	//$string = mysql_real_escape_string($string);
	return $string;
}

	$nome = post("nome");
	$email = post("email");
	$assunto_ = post("assunto");
	$mensagem = post("mensagem");
	$data = date("d/m/y H:i:s");

	if($nome != "" && $email != "" && $assunto_ != "" && $mensagem != ""){
			$assunto = "[CONTATO-SITE] - ".$assunto_;
			$mensagem = secure($mensagem);
			$mensagem = "Contato realizado através do site:\n
			Nome: $nome,
			Email: $email,
			Assunto: $assunto_,
			Data: $data,
			Mensagem:
			------------------------------------------------------------
$mensagem
			------------------------------------------------------------
			";
			if(email(array("alisondocarmo@gmail.com"), "$assunto", "$mensagem"))
				$pagina->addAviso("Thank you $nome. Your message was sent with success.");
			else
				$pagina->addAdvertencia("We have a problem to send your message, please, contact the administrator");
	}
	else{
		$pagina->addAviso("All fields are required!");
		//header("Location: index.php");
	}

?>

<?include "../layout/cima.php"?>

<h1>Contact form</h1>

<style>
	/*######## Smart Green ########*/
.smart-green {
    margin-left:auto;
    margin-right:auto;

    max-width: 500px;
    background: #F8F8F8;
    padding: 30px 30px 20px 30px;
    font: 12px Arial, Helvetica, sans-serif;
    color: #666;
    border-radius: 5px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
}
.smart-green h1 {
    font: 24px "Trebuchet MS", Arial, Helvetica, sans-serif;
    padding: 20px 0px 20px 40px;
    display: block;
    margin: -30px -30px 10px -30px;
    color: #FFF;
    background: #332299;
    text-shadow: 1px 1px 1px #949494;
    border-radius: 5px 5px 0px 0px;
    -webkit-border-radius: 5px 5px 0px 0px;
    -moz-border-radius: 5px 5px 0px 0px;
    border-bottom:1px solid #89AF4C;

}
.smart-green h1>span {
    display: block;
    font-size: 11px;
    color: #FFF;
}

.smart-green label {
    display: block;
    margin: 0px 0px 5px;
}
.smart-green label>span {
    float: left;
    margin-top: 10px;
    color: #5E5E5E;
}
.smart-green input[type="text"], .smart-green input[type="email"], .smart-green textarea, .smart-green select {
    color: #555;
    height: 30px;
    line-height:15px;
    width: 100%;
    padding: 0px 0px 0px 10px;
    margin-top: 2px;
    border: 1px solid #E5E5E5;
    background: #FBFBFB;
    outline: 0;
    -webkit-box-shadow: inset 1px 1px 2px rgba(238, 238, 238, 0.2);
    box-shadow: inset 1px 1px 2px rgba(238, 238, 238, 0.2);
    font: normal 14px/14px Arial, Helvetica, sans-serif;
}
.smart-green textarea{
    height:100px;
    padding-top: 10px;
}
.smart-green select {
    background: url('down-arrow.png') no-repeat right, -moz-linear-gradient(top, #FBFBFB 0%, #E9E9E9 100%);
    background: url('down-arrow.png') no-repeat right, -webkit-gradient(linear, left top, left bottom, color-stop(0%,#FBFBFB), color-stop(100%,#E9E9E9));
   appearance:none;
    -webkit-appearance:none; 
   -moz-appearance: none;
    text-indent: 0.01px;
    text-overflow: '';
    width:100%;
    height:30px;
}
.smart-green .button {
    background-color: #9DC45F;
    border-radius: 5px;
    -webkit-border-radius: 5px;
    -moz-border-border-radius: 5px;
    border: none;
    padding: 10px 25px 10px 25px;
    color: #FFF;
    text-shadow: 1px 1px 1px #949494;
}
.smart-green .button:hover {
    background-color:#80A24A;
}
</style>


<form class="smart-green" action="contato.php" method="post" class="STYLE-NAME">
    <h1>Contact form
        <span>All fields are required!</span>
    </h1>
    <label>
        <span>Your name :</span>
        <input id="name" type="text" name="nome" placeholder="Full name" />
    </label>
    
    <label>
        <span>Your e-mail :</span>
        <input id="email" type="email" name="email" placeholder="Insert a valid e-mail" />
    </label>
    
    <label>
        <span>Subject :</span>
        <input id="email" type="text" name="assunto" placeholder="Subject of contact" />
    </label>
    
    <label>
        <span>Message :</span>
        <textarea id="message" name="mensagem" placeholder="Your message"></textarea>
    </label> 
     <label>
        <span>&nbsp;</span> 
        <input type="submit" class="button" value="Send" /> 
    </label>    
</form>

<?include "../layout/baixo.php"?>
