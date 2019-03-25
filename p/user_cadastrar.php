<?
include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

	$pagina = new Pagina("user_cadastrar.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

$nome=post("nome");
$email=post("email");

$login=post("login");
$senha=post("senha");
$re_senha=post("re_senha");
$grup=3; //permissão de cadastrado


/*foreach($_POST as $campo => $value){
	echo "($campo): ($value)<br />";
}
*/

$_POST["senha"] = criptografa($senha);
$_POST["re_senha"] = criptografa($re_senha);

if($senha == $re_senha){
	if($nome != "" && $email != "" && $login != "" && $senha != "" && $grup != "" ){
		if(pg_num_rows(pg_query("SELECT login_usu FROM usuarios WHERE login_usu = '$login'")) == 0){
			if(pg_num_rows(pg_query("SELECT email_usu FROM usuarios WHERE email_usu = '$email' AND status_usu <> 'I' ")) == 0){
				$c_senha = criptografa($senha);
				$chave = gerar_chave_randomica();
				pg_query("DELETE FROM usuarios WHERE login_usu = '$login' AND senha_usu = '$c_senha'");
				$SQL = "INSERT INTO usuarios (nome_usu, email_usu, login_usu, senha_usu, id_gru_fk, status_usu, chave_usu
				)
				VALUES ('$nome', '$email', '$login', '$c_senha', '$grup', 'I', '$chave'
				)";
				
				pg_query($SQL) or die(pg_last_error());
				
				$assunto = "Register confirmation";
				//Enviar email para o cadastrado
				$dest = $email;
				$mensagem = 
	"Dear $nome,\n
	Your register was performed with success, but stay inactive. \n
	To activate access the login page and insert your login, password and the key:
		$chave
	";
		
				if(email("$dest", "$assunto", "$mensagem")){ 
				//=========================================================== 
					$pagina->addAviso("Register with success!<br>The e-mail confirmation was sent to: $dest");
					unset($_POST);
				}else{
					$pagina->addAdvertencia("We have problem to send e-mail, please, contact the administrator.");
					if(count($_POST) > 0) $pagina->gravaLog("POST: ".implode(",", $_POST));
				}
			}
			else{
				$pagina->addAdvertencia("E-mail already exists in our database");
				if(count($_POST) > 0) $pagina->gravaLog("POST: ".implode(",", $_POST));
			}
		}
		else{
			$pagina->addAdvertencia("Login already exists");
			if(count($_POST) > 0) $pagina->gravaLog("POST: ".implode(",", $_POST));
		}
		
	}
	else{
		$pagina->addAdvertencia("Fill all fields");
		if(count($_POST) > 0) $pagina->gravaLog("POST: ".implode(",", $_POST));
	}
}
else{
	$pagina->addAdvertencia("Worng password confirmation!");
	if(count($_POST) > 0) $pagina->gravaLog("POST: ".implode(",", $_POST));
}


?>
<?include("../layout/cima.php");?>

<script language="javascript">
function elemento_oculto(id, mostrar){
	//alert("aeeeeeee");
	if(mostrar == true || mostrar == 'true')
		document.getElementById(id).style.display = "inline";
	else
		document.getElementById(id).style.display = "none";
	//document.getElementsByName("nome")[0].focus();
}
</script>


					<div class="format_cont">
						<form action="user_cadastrar.php" method="post" onsubmit="if(this.senha.value != this.re_senha.value){alert('Password is not equal!'); return false;}">
							<table>
								<tr>
									<td>Name:</td>
									<td><?inputText("nome", null, 30);?></td>
								</tr>
								<tr>
									<td>E-mail:</td>
									<td><?inputText("email", null, 30);?></td>
								</tr>
								
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								
								<tr>
									<td>Login:</td>
									<td><?inputText("login", null, 30);?></td>
								</tr>
								<tr>
									<td>Password:</td>
									<td><input type="password" name="senha" size="30"/></td>
								</tr>
								<tr>
									<td>Password confirmation:</td>
									<td><input type="password" name="re_senha" size="30"/></td>
								</tr>
							</table>
							<p><input type="submit" value="Send"/></p>
						</form>
					</div>


<?include("../layout/baixo.php");?>
