<?
include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

	$pagina = new Pagina("user_logar.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");

	$login = post("login");
	$senha = post("senha"); 

if(get("a") == "out"){
	$pagina->setUsuario(new Usuario());
	header("Location: ../index.php");
}
	
if($login!= "" && $senha != ""){
		$senha = criptografa($senha);
		$alu = pg_query("SELECT * FROM usuarios WHERE login_usu = '$login' AND senha_usu = '$senha' AND status_usu != 'I'") or die(pg_last_error());
		if(pg_num_rows($alu) == 1){
			//session_start();
			$alu = pg_fetch_assoc($alu);
			if($alu["status_usu"] != "I"){
				$usuario = new Usuario($alu["id_usu"], $alu["nome_usu"], $alu["id_gru_fk"]);
				
				$pagina->setUsuario($usuario);
				
				//echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=principal.php'>";
				//header("Location: principal.php");
				$pagina->addAviso("Welcome ".$usuario->getNomeUsuario());

				$id_gru = $pagina->getUsuario()->getIdGrupo();
				$id_usu = $pagina->getUsuario()->getIdUsuario();
				
				//if($id_usu == "27") email("alisondocarmo@gmail.com", "Usuário 27 entrou", "Usuário 27 entrou");

				$sql = "SELECT * FROM usuarios WHERE
					id_usu = '$id_usu' AND
					(
						nome_usu = ''
					)";

				pg_query($sql) or die(pg_last_error());
				
				if(pg_num_rows(pg_query($sql)) > 0){
						header("Location: ../p/user_alterar.php");
				}
			}
			else{
				$email = $alu["email_usu"];
				$pagina->addAdvertencia("Your e-mail is not validated yet!<br>
				Check the inbox of $email and insert the key sent by the system<br>
				In case of any trouble, fill again the signin form to overwrite your old register.");

				$pagina->addAdvertencia("If you not received the activation e-mail, check your spam. We recomends add <rs.ppgcc@gmail.com> to your contact list.");
			}
		}else if(pg_num_rows($alu) > 1){
			$pagina->addAdvertencia("Duplicated data register.");
			$pagina->setUsuario(new Usuario());
		}
		else{
			$pagina->addAdvertencia("Invalid data");
			$pagina->setUsuario(new Usuario());
			//echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=principal.php'>";
			//header("Location: principal.php");
			
		}
		//DesconectaDB($con);
}
else{
	$pagina->addAdvertencia("All input fields are required");
	//header("Location: index_new2.php");
}

if (get("acao") == "logout"){
	$pagina->setUsuario(new Usuario());
	//echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=principal.php'>";
	header("Location: ../index.php");
}

?>
<?include("../layout/cima.php");?>

	

	<p>Start the exploration of the system from two stepes:</p>
		<ol style="margin-left: 50px;">
			<li>Fill the form register of the system. Click <a href="../p/user_cadastrar.php">here</a> to register your self.</li>
			<li>Confirm your register activating with the link sent to your registered e-mail.<small>(This step is required to use the system.)</small></li>
		</ol>



	<div class="box" style='float: none; margin: 0 auto; display: block;'>
		<div class="box-titulo"></div>
		<div class="box-base">

			<form action="user_logar.php" method="post">
				<table>
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
					<a class="naEsquerda" href="user_recuperar.php">I forgot my password</a>
					<a class="naDireita" href="user_cadastrar.php">New register</a>
				</div>
			
			</form>
		</div>
	</div>

	
 	<script>window.onload=document.forms[0].login.focus()</script>
<?include("../layout/baixo.php");?>
