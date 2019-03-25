<?

include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

	$pagina = new Pagina("user_alterar.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

	$id_usu = $pagina->getUsuario()->getIdUsuario();

$nome=post("nome");
$email=post("email");

$senha=post("senha");
$re_senha=post("re_senha");

$_POST["senha"] = criptografa($senha);
$_POST["re_senha"] = criptografa($re_senha);


if($senha == $re_senha){
	if($nome != "" || $email != "" || $senha != ""){
		$senha_crip = criptografa($senha);
		
		$setSenha = $setNome = $setEmail = "";
		if($senha != "")
			$setSenha = " senha_usu = '$senha_crip', ";
		if($nome != "")
			$setNome = " nome_usu = '$nome', ";
			
		if($email != "")
			$setEmail = " email_usu = '$email' ";
			
		pg_query("UPDATE usuarios SET $setNome $setSenha $setEmail
		WHERE id_usu = '$id_usu'")or die(pg_last_error());
		$pagina->addAviso("Seus dados foram alterados com sucesso ");
	}
	else{
		$pagina->addAviso("Insira todos os campos.");
		if(count($_POST) > 0) $pagina->gravaLog("POST: ".implode(",", $_POST));
	}
}
else{
	$pagina->addAviso("As senhas não são iguais!");
	if(count($_POST) > 0) $pagina->gravaLog("POST: ".implode(",", $_POST));
}

$rsUsu = pg_query("SELECT * FROM usuarios WHERE id_usu = '$id_usu'") or die(pg_last_error());
$usu = pg_fetch_assoc($rsUsu);
	
?>

<?include("../layout/cima.php");?>


					<div class="format_cont">

						<h1>Profile update </h1>
						
						<strong>Please, keep all data updated!</strong>
						
						<form action="user_alterar.php" method="post" onsubmit="if(this.senha.value != this.re_senha.value){alert('Password is not equal!'); return false;}">
							<table>
								<tr>
									<td>Name:</td>
									<td>
											<input type="text" name="nome" size="30" value="<?echo $usu["nome_usu"];?>"/>
									</td>
								</tr>
								<tr>
									<td>E-mail:</td>
									<td>
										<input type="text" name="email" size="30" value="<?echo $usu["email_usu"];?>"/>
									</td>
								</tr>
								
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								
								<tr>
									<td>Login:</td>
									<td><?echo $usu["login_usu"];?></td>
								</tr>
								<tr>
									<td>Password:</td>
									<td><input type="password" name="senha" size="30"/></td>
								</tr>
								<tr>
									<td>Repeat password:</td>
									<td><input type="password" name="re_senha" size="30"/></td>
								</tr>

							</table>
							<p><input type="submit" value="Enviar"/></p>
						</form>
					</div>



<?include("../layout/baixo.php");?>

