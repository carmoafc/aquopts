<?

	class Pagina{
		private $titulo;
		private $palavras_chaves;
		private $nomeArquivo;
		private $descricao;
		private $avisos = ""; 
		private $advertencias = "";

		public function __construct($nomeArquivo, $titulo, $palavras_chaves = "", $descricao = ""){
			ConectaDB();

			$this->identificaUsuario();
			
			$this->nomeArquivo = $nomeArquivo;
			$this->titulo = $titulo;
			$this->palavras_chaves = $palavras_chaves;
			$this->descricao = $descricao;

			if(!$this->isPermitido() && $nomeArquivo != ""){
				header('Location: ../p/sem_permissao.php');
			}

			$this->setPagina($this);
		}
/*******************      SETs  *********************/
		public function setPagina($pagina){
			$_SESSION["pagina_S"] = $pagina;
		}
		public function getPagina(){
			return $_SESSION["pagina_S"];
		}
/*******************      GETs  *********************/
		public function getTitulo(){
			return $GLOBALS ['sigla_base'];
			//return $this->titulo;
		}
		public function getPalavras_chaves(){
			return $this->palavras_chaves;
		}
		public function getNomeArquivo(){
			return $this->nomeArquivo;
		}
		public function getDescricao(){
			return $this->descricao;
		}
		public function getAvisos(){
			return $this->avisos;
		}
		public function getAdvertencias(){
			return $this->advertencias;
		}
/*******************      USER SESSION  *********************/	
		public function getUsuario(){
			return $_SESSION["usuario_S"];
		}
		public function setUsuario($usuario){
			$_SESSION["usuario_S"] = $usuario;
		}
		public function identificaUsuario(){
			if(!isset($_SESSION["usuario_S"]))
				$this->setUsuario(new Usuario());
		}
		public function userLogado(){
			if($this->getUsuario()->getIdGrupo() == 1)
				return false;
			else
				return true;
		}
/*******************      FUNCOES  *********************/
		public function gravaLog($msg){
			#trace("[".date("d/m/Y H:i:s")."] $msg");
			$pagina = $this->getNomeArquivo();
			$ip = $_SERVER['REMOTE_ADDR'];
			$navegador = $_SERVER['HTTP_USER_AGENT'];
			$usu = $this->getUsuario()->getIdUsuario();
			$msg = str_replace("'", "\"", $msg);
			$sql = "INSERT INTO log (pagina_log, ip_log, navegador_log, id_usu_fk, mensagem_log) VALUES ('$pagina', '$ip', '$navegador', $usu, 'MENSAGEM:___$msg')";
			pg_query($sql) or die(pg_last_error()."<br />". $sql);
		}

		public function addAviso($aviso){
			$this->gravaLog($aviso);
			$this->avisos .= "$aviso<br>";
                        //trace0($aviso);
		}
		public function addAdvertencia($advertencia){
			$this->gravaLog($advertencia);
			$this->advertencias .= "$advertencia<br>";
                        //trace0($advertencia);
		}
		
		public function temAvisos(){
			if($this->avisos == "")
				return false;
			else
				return true;
		}
		public function temAdvertencias(){
			if($this->advertencias == "")
				return false;
			else
				return true;
		}
		public function isPermitido(){
			$usu = $this->getUsuario();
			
			$sql = "SELECT id_ite FROM itens i, paginas_arquivos pa 
			WHERE 
			i.id_ite = pa.id_ite_fk
			AND pa.link_pag_arq = '".$this->getNomeArquivo()."'";
			
			$id_ite = getAtrQuery($sql);
			
			if($id_ite > 0 )
				return permissaoItem( $id_ite, $usu->getIdGrupo(), $usu->getIdUsuario());
			else
				return false;
			
			
			$res = pg_query($sql) or die(pg_last_error());
			
			if(pg_num_rows($res) != 1)
				return false;

			return true;
		}

	}
?>
