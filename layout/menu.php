
		<?
		
			$id_gru = $pagina->getUsuario()->getIdGrupo();
			$id_usu = $pagina->getUsuario()->getIdUsuario();

			
			//$id_menu_h = getAtrQuery("SELECT id_ite FROM itens WHERE titulo_ite = 'horizontal'");
			$strOcultos="";
			if(!@$mostrar_ocultos)
				$strOcultos = "AND i.visivel_ite = 1";
			
			
			$rsMen = pg_query("SELECT * FROM itens i WHERE i.id_ite_fk is NULL $strOcultos ORDER BY i.posicao_ite, i.titulo_ite") or die(pg_last_error());

			if(pg_num_rows($rsMen)){
				while($men = pg_fetch_assoc($rsMen)){
					
					if(permissaoItem($men['id_ite'], $id_gru, $id_usu)){
						echo "<ul>";
							echo "<li><a href='".getLinkItem($men["id_ite"])."' class='top_parent'>".$men["titulo_ite"]."</a>";
							$rsSub = pg_query("SELECT * FROM itens  i WHERE i.id_ite_fk = '".$men["id_ite"]."' $strOcultos ORDER BY i.posicao_ite, i.titulo_ite") or die(pg_last_error());
							if(pg_num_rows($rsSub)){
								echo "<ul>";
								while($sub = pg_fetch_assoc($rsSub)){
									if(permissaoItem($sub['id_ite'], $id_gru, $id_usu))
										echo "<li><a href='".getLinkItem($sub["id_ite"])."'>".$sub["titulo_ite"]."</a></li>";
								}
								echo "</ul>";
							}
							echo "</li>";
						echo "</ul>";
					}
				}
			}
		?>
<!--

						<ul>
								<li>
									<a href="../paginas/autor.php">Sobre o autor</a>
								</li>
								<li>
									<a href="../paginas/projeto.php">Sobre o projeto</a>
								</li>
								<li>
									<a href="../paginas/mapa.php">Mapa</a>
								</li>
						</ul>
						
-->		
		


<!--
    <div class="menu" width="100%">
        <table width="100%">
            <tr>
                <td><a href="faces/index.xhtml">Portais</a></td>
                <td><a href="faces/pages/Cursos.xhtml">Cursos</a></td>
                <td><a href="faces/pages/Usuarios.xhtml">Usuários</a></td>
                <td><a href="faces/pages/Foruns.xhtml">Fóruns</a></td>
                <td><a href="faces/pages/Avaliacao.xhtml">Avaliações</a></td>
                                
                
                
                
                <td class="logout_admin">
                    <table width="50%">
                        <tr>
                            <td><a href="../p/login.php?a=out">LogOut</a></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
-->

<!--
    <div id="menuh">
    	<ul>
    		<li><a href="#" class="top_parent">O Evento</a>
    			<ul>
    				<li><a href="index.php"> Início</a></li>
    				<li><a href="evento.php"> Sobre</a></li>
    				<li><a href="objetivos.php"> Objetivos</a></li>
    			</ul>
    		</li>
    	</ul>
      <ul>
        <li><a href="inscricoes.php" class="top_parent">Inscrições</a>
          <ul>
			<li><a href="#"> Datas</a></li>
          </ul>
        </li>
      </ul>
      <ul>
        <li><a href="eixos.php" class="top_parent">Eixos</a>
        </li>
      </ul>
      <ul>
        <li><a href="#" class="top_parent">Normas</a> </li>
      </ul>
      <ul>
        <li><a href="#" class="top_parent">Programação</a> 
			<ul>
				<li><a href="palestrantes.php#palestrantes"> Palestrantes</a></li>
				<li><a href="palestrantes.php#conferencistas"> Conferencistas</a></li>
			</ul>
		</li>
      </ul>
      <ul>
        <li><a href="#" class="top_parent">Contato</a> </li>
      </ul>
    </div>

-->


