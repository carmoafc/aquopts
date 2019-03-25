<h1><?=pg_num_rows($res) ?> Registros</h1>

<form id="formListagem" method="POST" action="<?echo $tabela["pagina"]?>">
	<?inputHidden("order_by")?>
	<span class="dadosFundo">
		<a href="<?echo $tabela["pagina"]?>?ACAO=listar"><img alt="listar" title="listar" src="../registrar/icon_search.png" /></a>
		<a href="<?echo $tabela["pagina"]?>?ACAO=inserir"><img alt="inserir" title="inserir" src="../registrar/icon_add.png" /></a>
		<a href="javascript:removerDados()"><img alt="remover selecionados" title="remover selecionados" src="../registrar/icon_delete.png" /></a>
	</span><br />
	

	<table id="blocoListagem" class="dadosFrente" border="1">
		<tr>
			<th><input type="checkbox" onclick="selecionarCheckbox('entrada[]', this.checked)"></th>
			<th>
				Campos<br />
				<input class="botao" type="submit" value="Buscar" />
			</th>
			<th>
				<a href="javascript:ordenar('<?echo $tabela["nome_identificador"]?>')"><?echo $tabela["nome_identificador"]?></a><br />
				<?inputText($tabela["identificador"], post($tabela["identificador"]), "4");?>
			</th>
			<?foreach($campos as $campo => $valor){?>
				<?if($valor["editavel"] != false){?>
					<th>
						<a href="javascript:ordenar('<?echo $valor["nome"]?>')"><?echo $valor["nome"]?></a><br />
						<?
							if($valor["tipo"] == "SELECT")
								inputSelect($campo, $valor["valores"], post($campo));
							else
								inputText($campo, post($campo), $valor["tamanho"]);
						?>
					</th>
				<?}?>
			<?}?>
		</tr>
			<?while($linha = pg_fetch_assoc($res)){?>
				<tr VALIGN=MIDDLE class="dadosFundo">
					<td><?inputCheckbox("entrada[]", $linha[$tabela["identificador"]])?></td>
					<td><a href="<?echo $tabela["pagina"]?>?ACAO=alterar&ID=<?echo $linha[$tabela["identificador"]]?>" ><img alt="editar" title="editar" src="../registrar/icon_edit.png" ></a></td>
					<td><?echo $linha[$tabela["identificador"]];?></td>
					<?
					foreach($campos as $campo => $valor){
						if($valor["editavel"] != false){
							echo "<td class='celula_lista'>";
							if($valor["tipo"] == "SELECT"){
								echo @$valor["valores"][$linha[$campo]];
							}else if($valor["tipo"] == "TEXT"){
								echo $linha[$campo];
							}else if($valor["tipo"] == "DATA"){
								echo formataData($linha[$campo]) . " ". formataHorario($linha[$campo]);
							}else if($valor["tipo"] == "TEXTAREA"){
								echo $linha[$campo];
							}else if($valor["tipo"] == "FILE"){
								previaArquivo($linha[$campo], $valor["tamanho"]);
								echo "<a href='".$campos["id_dir_fk"]["valores"][$linha["id_dir_fk"]] . $linha[$campo]."'>$linha[$campo]<a>";
							}
							else die("Nao conheço");
							echo "</td>";
						}
					}?>
				</tr>
			<?}?>
	</table>
</form>
