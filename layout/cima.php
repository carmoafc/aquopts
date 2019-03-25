<?
include_once "../config/funcoes.php";

//if(strpos(array_shift(explode("?", $_SERVER["PHP_SELF"])), "login.php") === false)	verificaPermissao();

	pg_query("SET statement_timeout TO 0");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="content-language" content="pt-br" />
		<meta name="robots" content="index,follow" />
		<meta name="author" content="Alisson Fernando Coelho do Carmo" />
		<meta name="description" content="Processing system" />
		<meta name="keywords" content="Processing, system" />
		
		
		<link rel="shortcut icon" href="../layout/imagens/logo_icon2.gif" type="image/gif">
		
		
		<script src="../config/bibliotecas_js/jquery-1.11.2.min.js" type="text/javascript"></script>
		<script src="../config/bibliotecas_js/jquery-ui-1.11.4/jquery-ui.js" type="text/javascript"></script>
		<script src="../config/bibliotecas_js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
		<script src="../config/bibliotecas_js/jquery-ui-sliderAccess.js" type="text/javascript"></script>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		
		<link rel="stylesheet" type="text/css" href="../layout/reset.css" />		
		
		<link rel="stylesheet" type="text/css" href="../config/bibliotecas_js/jquery-ui-1.11.4/jquery-ui.css" />	
		<link rel="stylesheet" type="text/css" href="../layout/estilos.css" />	        
		
		<script src="../config/bibliotecas_js/jquery-filestyle-2.1.0/src/jquery-filestyle.min.js" type="text/javascript"></script>
		
		
		<script src="../config/bibliotecas_js/scripts.js" type="text/javascript"></script>
		<script src="../config/bibliotecas_js/expandable-list.js" type="text/javascript"></script>
		
		
		<!--Data table pagination-->
		<link rel="stylesheet" type="text/css" href="../config/bibliotecas_js/DataTables-1.10.13/media/css/jquery.dataTables.css"/>
		<script type="text/javascript" src="../config/bibliotecas_js/DataTables-1.10.13/media/js/jquery.dataTables.js"></script>
		
		<!--<script type="text/javascript" src="../config/bibliotecas_js/DataTables-1.10.13/extensions/Buttons/js/dataTables.buttons.js"></script>
		<script type="text/javascript" src="../config/bibliotecas_js/DataTables-1.10.13/extensions/Buttons/js/buttons.flash.js"></script>
		<script type="text/javascript" src="../config/bibliotecas_js/DataTables-1.10.13/extensions/Buttons/js/buttons.html5.js"></script>
		<script type="text/javascript" src="../config/bibliotecas_js/DataTables-1.10.13/extensions/Buttons/js/buttons.print.js"></script>
		-->
		
		<script type="text/javascript" src="../config/bibliotecas_js/canvasjs-1.9.6/canvasjs.min.js"></script>
		
		
		<script type="text/javascript" src="../config/bibliotecas_js/jquery.query-builder/js/query-builder.standalone.min.js"></script>
		<script type="text/javascript" src="../config/bibliotecas_js/sql-parser.js"></script>
		<link rel="stylesheet" type="text/css" href="../config/bibliotecas_js/jquery.query-builder/css/query-builder.default.min.css"/>
		
		<link rel="stylesheet" type="text/css" href="../config/bibliotecas_js/jquery-modal-master/jquery.modal.min.css"/>
		<script type="text/javascript" src="../config/bibliotecas_js/jquery-modal-master/jquery.modal.min.js"></script>
		
		<title> <?echo $pagina->getTitulo();?> </title>
        

    </head>
<body>
		
		<?if($pagina->userLogado()) $pagina->gravaLog("Navegando...");?>
		
		<?if($pagina->getUsuario()->getIdGrupo() == "2"){?>
			<div id="usuarios_logados">
				<?
					$sql = "
						SELECT 
							nome_usu, 
							MIN((EXTRACT(EPOCH FROM (NOW()-data_log))/60)::int) as ociosidade
						FROM 
							usuarios JOIN 
							log ON id_usu_fk = id_usu
						WHERE
							EXTRACT(EPOCH FROM NOW()-data_log)/60 < 10
						GROUP BY 
							id_usu
						ORDER BY 
							nome_usu
						";
					
					//$sql = "SELECT *, '0' as ociosidade FROM usuarios LIMIT 1";
				
					$rsUsuLog = pg_query($sql) or die(pg_last_error());
				?>
					<div><b><?=pg_num_rows($rsUsuLog)?> Usuários Online</b></div>
					<?while($usuLog = pg_fetch_assoc($rsUsuLog)){?>
					<div> (<?=$usuLog["ociosidade"]?> min) <?=$usuLog["nome_usu"]?> </div>
				<?}?>
			</div>
		<?}?>
		
		<div id="conteinerSite">
			
			<div id="conteinerTopo">
				<div id="topo">
						<a href="../index.php" class="link_sistema" title="SR-GEO">
							<img  src="../layout/imagens/logo_ppgcc.gif"></img>
							<?=$GLOBALS ['sigla_base'] . ' - ' . $GLOBALS ['nome_base']?>
						</a>    
						<span class='clear'></span>
						
						<div id='bloco_usuario' style='text-align: right'>
							<?if($pagina->userLogado()){?>
								<a title='Dados Pessoais' href='../p/user_alterar.php'> <b><?echo $pagina->getUsuario()->getNomeUsuario();?> </b></a>
								<br />
								<a title='Logout' href='../p/user_logar.php?a=out'><b><i>Logout</i></b></a>
							<?}else{?>
								<a href="../p/user_logar.php">Restrict access</a>
							<?}?>
						</div>
						
						
				</div>

			</div>
			
			<div id="conteinerBarraCima">

					<nav class='menu_horizontal'>
						<?include("menu.php");?>
					</nav>

			</div>
							
			
			<div id="conteinerCorpo">	
				
				<div id="conteinerConteudo">
					<div class="box">
						<div class="box_conteudo">
							
							<?if($pagina->temAvisos() || $pagina->temAdvertencias()){?>
								<div class="mensagens">
									<?if($pagina->temAvisos()){?>
										<div class="aviso"> <?echo $pagina->getAvisos();?> </div>
									<?}if($pagina->temAdvertencias()){?>
										<div class="advertencia"> <?echo $pagina->getAdvertencias();?> </div>
									<?}?>
								</div>
							<?}?>    
						
                   
            
<!--#################################################################-->
<!--#################################################################-->
<!--#################################################################-->
<!--#################################################################-->
