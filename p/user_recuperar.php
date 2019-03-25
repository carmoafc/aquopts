<?

include_once "../config/funcoes.php"; //OBRIGATÓRIO EM TODAS AS PÁGINAS

	$pagina = new Pagina("user_recuperar.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


	$email = post("email"); 
	
	if($email != ""){
		$alu = pg_query("SELECT * FROM usuarios WHERE email_usu = '$email' AND status_usu = 'A'") or die(pg_last_error());
		if(pg_num_rows($alu) == 1){
			$alu = pg_fetch_assoc($alu);
			$senha = randomString(6);
			$senha_crip = criptografa($senha);
			pg_query("UPDATE usuarios SET senha_usu = '$senha_crip' WHERE email_usu = '$email'") or die(pg_last_error());
			$nome = $alu["nome_usu"];
			$login = $alu["login_usu"];
			
			$assunto = "Restore password";
			$mensagem = 
			"Dear $nome,\n\n
			This is your login and password: \n\n
			Login: $login \n
			password: $senha \n\n
			We generates a new random password,\n
			so go to login and change your password,\n\n";
                        
			if(email("$email", "$assunto", "$mensagem"))
				$pagina->addAviso("Thank you $nome. A new password was sent to your e-mail");
			else
				$pagina->addAdvertencia("We have a problem to send a e-mail, please, contact the administrator");
		}
		else if(pg_num_rows($alu) > 1){
			$pagina->addAdvertencia("Duplicated e-mail! The system can not change this case, please, contact the administrator or make a new register");
		}
		else{//não possui este email ativado
			//$pagina->addAdvertencia("Email não está cadastrado em nossa base de dados.");
			
			$alu = pg_query("SELECT * FROM usuarios WHERE email_usu = '$email' AND status_usu = 'I' ORDER BY id_usu DESC") or die(pg_last_error());
			
			if(pg_num_rows($alu) > 0){
				$alu = pg_fetch_assoc($alu);
				$senha = randomString(6);
				$senha_crip = criptografa($senha);
				pg_query("UPDATE usuarios SET senha_usu = '$senha_crip' WHERE email_usu = '$email'") or die(pg_last_error());
				$nome = $alu["nome_usu"];
				$login = $alu["login_usu"];
				$chave = $alu["chave_usu"];
				
				$assunto = "Recuperar Senha";
				$mensagem = 
				"Dear $nome,\n\n
				This is your login and password: \n\n
				Login: $login \n
				password: $senha \n\n
				Your register is not activated yet.\n
				To activate, access the activation page and insert your login, password, and the key:
					$chave

				";
				
				if(email("$email", "$assunto", "$mensagem"))
					$pagina->addAviso("Thank you $nome. A new password was sent to your e-mail");
				else
					$pagina->addAdvertencia("We have a problem to send the e-mail, please, contact the administrator");
			
			}else {
				$pagina->addAdvertencia("E-mail not found. Please, make a new register.");
			}
			
			
		}
	}
	else{
		$pagina->addAviso("Insert your e-mail");
		//header("Location: index.php");
	}
?>

<?include("../layout/cima.php");?>


					<div class="format_cont">
						<p><?echo @$msg?></p>
						<form action="user_recuperar.php" method="post">
							<p>E-mail: <input type="text" name="email" size="40"/></p>
							<p><input type="submit" value="Send"/></p>
						</form>
					</div>


<?include("../layout/baixo.php");?>

