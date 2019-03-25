<?
include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

	$pagina = new Pagina("user_ativar.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");



	$login = post("login");
	$senha = post("senha");
	$chave = post("chave");
	
if($login!= "" && $senha != "" && $chave != ""){
		$senha = criptografa($senha);
		$rsAlu = pg_query("SELECT * FROM usuarios WHERE login_usu = '$login' AND senha_usu = '$senha' AND chave_usu = '$chave'") or die(pg_last_error());
		$alu = pg_fetch_assoc($rsAlu);
		if(pg_num_rows($rsAlu) == 1){
			if($alu["status_usu"] == "I"){
				if($chave == $alu["chave_usu"]){
					//session_start();

					$id_usu = $alu["id_usu"];
					$login = $alu["login_usu"];
					$senha = $alu["senha_usu"];
					$email = $alu["email_usu"];
					$nome = $alu["nome_usu"];

					pg_query("UPDATE usuarios SET status_usu = 'A' WHERE login_usu = '$login' AND senha_usu = '$senha' AND chave_usu = '$chave'") or die("Erro na mudança de status: ".pg_last_error());
					//atualizarIds($alu["id_usu"], $alu["email_usu"], $alu["cpf_usu"]);
					
					$mensagem =
"Dear $nome,

your register was made with success.
This is your data to access:

	Login: $login,
	E-mail: $email,

";

					if(email($alu["email_usu"], "Confirmed register", $mensagem)){
						
						$usuario = new Usuario($alu["id_usu"], $alu["nome_usu"], $alu["id_gru_fk"]);
						
						$pagina->setUsuario($usuario);
						
						//echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=principal.php'>";
						//header("Location: principal.php");
						$pagina->addAviso("Welcome ".$usuario->getNomeUsuario());
					}
					else
						$pagina->addAdvertencia("We have problem to send e-mail 'confirmed register', please, contact the administrator");
				}
				else
					$pagina->addAdvertencia("Invalid Key");
			}
			else
				$pagina->addAdvertencia("Your register is already activated!");
		}
		else if(pg_num_rows($rsAlu) == 1){
			$pagina->addAdvertencia("Duplicated register!Please, contact the administrator to inform this error");
			$pagina->setUsuario(new Usuario());
		}
		else{
			$pagina->addAdvertencia("Invalid login, password and/or key");
			$pagina->setUsuario(new Usuario());
			//echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=principal.php'>";
			//header("Location: principal.php");
			
		}
		//DesconectaDB($con);
}
else{
	$pagina->addAdvertencia("Fill all fields");
	//header("Location: index_new2.php");
}

if (get("acao") == "logout"){
	$pagina->setUsuario(new Usuario());
	echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=principal.php'>";
	//header("Location: principal.php");
}

?>
<?include("../layout/cima.php");?>


	<div class="box">
		<div class="box-titulo"></div>
		<div class="box-base">

			<?$c = (($chave == "")?get("c"):$chave)?>

			<form action="user_ativar.php" method="post">
				<table>
					<tr>
						<td>Key:</td>
						<td> <input type="text" name="chave" size="30" value="<?echo $c?>"/> </td>
					</tr>
					<tr>
						<td>Login:</td>
						<td> <input type="text" name="login" size="30"/> </td>
					</tr>
					<tr>
						<td>Password:</td>
						<td> <input type="password" name="senha" size="30"/> </td>
					</tr>
				</table>
				<div class="noCentro"><a><input type="submit" value="Enviar" /></a></div>
				<div class="noCentro">
					<a class="naEsquerda" href="user_recuperar.php">Forgot my password</a>
					<a class="naDireita" href="user_cadastrar.php">New register</a>
				</div>			
			</form>
		</div>
	</div>
 
<?include("../layout/baixo.php");?>
