<?
	include_once "../config/funcoes.php"; //OBRIGATRIO EM TODAS AS PGINAS
     
    $pagina = new Pagina("user_trocar.php", "TÌtulo da P·gina", "palavras chaves, separadas por vÌrgula", "DescriÁ„o da p·gina");
     
    $id_usu = post("id_usu");
	if($id_usu > 0){
		$alu = pg_query("SELECT * FROM usuarios WHERE id_usu = '$id_usu'") or die(pg_last_error());
		if(pg_num_rows($alu) == 1){
			//session_start();
			$alu = pg_fetch_assoc($alu);
			if($alu["status_usu"] != "I"){
				$usuario = new Usuario($alu["id_usu"], $alu["nome_usu"], $alu["id_gru_fk"]);
				 
				$pagina->setUsuario($usuario);
				 
				//echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=principal.php'>";
				//header("Location: principal.php");
				$pagina->addAviso("Bem-vindo ".$usuario->getNomeUsuario());
				 
				$id_gru = $pagina->getUsuario()->getIdGrupo();
				$id_usu = $pagina->getUsuario()->getIdUsuario();
				 
				$sql = "SELECT * FROM usuarios WHERE
								id_usu = '$id_usu' AND
								(
								nome_usu = '' OR
								login_usu = '' OR
								senha_usu = '' OR
								email_usu = ''
								)";
				 
				pg_query($sql) or die(pg_last_error());
				 
				if(pg_num_rows(pg_query($sql)) > 0){
					header("Location: ../p/user_alterar.php");
				}
			}
			else{
				$email = $alu["email_usu"];
				$pagina->addAdvertencia("Seu usu√°rio ainda n√£o validou o email, e n√£o pode acessar o sistema<br>
				Verifique na caixa de entrada de $email e insira a chave enviada no sistema<br>
				Caso encontre dificuldade, voc√™ poder√° efetuar um novo cadastro, e seu antigo ser√° removido.");
			}
		}
		else{
			$pagina->addAdvertencia("Dados inv√°lidos");
			$pagina->setUsuario(new Usuario());
			//echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL=principal.php'>";
			//header("Location: principal.php");
		}
	}
    
     
    $arrayUsuario = getInputFromSQL(pg_query("SELECT id_usu AS ID, nome_usu AS DESCRICAO FROM usuarios ORDER BY DESCRICAO"));
?>

    <?include("../layout/cima.php");?>
     
     
    <form action="user_trocar.php" method="post">
		Logar como: 
		<?inputSelect("id_usu", $arrayUsuario)?>
		<input type="submit" value="Logar">
    </form>
    
    <?include("../layout/baixo.php");?>
     
