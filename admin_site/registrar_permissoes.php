<?

include_once "../config/funcoes.php";

$pagina = new Pagina("registrar_permissoes.php", "Título desta página", "palavras chaves, separadas por vírgula", "Descrição desta página");


//$con = ConectaDB();

$grup = post("grupo");


if(get("b") == "gravar"){
	pg_query("DELETE FROM permissao_grupo WHERE permissao_grupo.id_gru_fk = '$grup'") or die(pg_last_error());
	foreach($_POST["permissao"] as $c => $id_pag) //todas as paginas selecionadas
		pg_query("INSERT INTO permissao_grupo (id_gru_fk, id_ite_fk) VALUES ('$grup', '$id_pag')");
	
}
else if(get("b") == "grupo"){
	
}


$rsPag = pg_query("SELECT * FROM itens");
$rsGru = pg_query("SELECT * FROM grupos");


//DesconectaDB($con);
?>

<?include("../layout/cima.php");?>

<script>
	function selecionarCheckbox(nome, status){
			var cbs = document.getElementsByName(nome);
			var i;
			for(i=0; i<cbs.length; i++)
				cbs[i].checked = status;
		}

</script>

					<form action="registrar_permissoes.php" method="post">Grupo 
						<select name="grupo" size="1" onchange="action = 'registrar_permissoes.php?b=grupo'; submit();">
							<option value="">Selecione</option>
							<?while($linha = pg_fetch_assoc($rsGru)){?>
							<option value="<?echo $linha["id_gru"]?>" <? if($linha["id_gru"] == post("grupo")) echo "SELECTED";?>> <?echo $linha["nome_gru"]?> </option>
							<?}?>
						</select>
						<br/>
						<table>
							<tr style="text-align: center; font-weight:bold">
								<td>Página</td>
								<td><input type="checkbox" onclick="selecionarCheckbox('permissao[]', this.checked)">Permissão</td>
							</tr>
							<?
							while($linha = pg_fetch_assoc($rsPag)){?>
							<tr>
								<td><?echo $linha["titulo_ite"]?></td>
								<td style="text-align: center">
									<input type="checkbox" value="<?echo $linha["id_ite"]?>" name="permissao[]" 
									<?if(@pg_num_rows(@pg_query("SELECT * FROM permissao_grupo WHERE permissao_grupo.id_gru_fk = '".post("grupo")."' AND permissao_grupo.id_ite_fk = '".$linha["id_ite"]."'")) == 1)
										echo "CHECKED";
									?>
									/>
								</td>
							</tr>
							<?}?>
						</table>
						<br/>
						<input type="submit" value="Gravar" onclick="action = 'registrar_permissoes.php?b=gravar';"/>
					</form>


<?include("../layout/baixo.php");?>

