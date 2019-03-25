<?
class Modelagem{
	private $modelPath= "../config/"; #caminho do mapfile
	private $modelName = "";
	private $xml = null;
	private $debug = "";
	private $restricoes = array(); //array bidimensional [i][j] de Restricao.php, onde i representa um conjunto de restrições OR, composta de j restrições AND
	private $unioesModelagens = array(); //array de modelagens para operação AND
	
	public function __construct($modelName = "", $modelPath = "") {
		if($modelName != "")
			$this->setModelName($modelName);
		if($modelPath != "")
			$this->setModelPath($modelPath);
		if($this->modelPath != "" && $this->modelName != "")	
			$this->xml = simplexml_load_file($this->modelPath . $this->modelName); 
    }
    
    public function __destruct() {

	}

	public function setXml($xml){
		$this->xml = $xml;
	}
	
	public function getXml(){
		return $this->xml;
	}
	
	public function setModelPath($valor = ""){
		$this->modelPath = $valor;
	}
	
	public function setModelName($valor = ""){
		$this->modelName = $valor;
	}
	
	
	public function getModelPath(){
		return $this->modelPath;
	}
	
	public function getModelName(){
		return $this->modelName;
	}
	
	public function addDebug($text){
		$this->debug .= "<br />".$text;
	}
	
	public function getDebug(){
		return $this->debug;
	}
	
	//#########################################################
	//#########################################################
	//#########################################################
	//#########################################################
	public function addNewRestricaoOr(){
		//echo "\nAdicionando OR\n";
		//se a utlima não está vazia
		if(count($this->restricoes) == 0 || count($this->restricoes[count($this->restricoes)-1]) != 0)
			$this->restricoes[] = array();
	}
	
	public function addRestricaoAnd($restricao){
		$posicaoOrAtual = count($this->restricoes) - 1;
		if($posicaoOrAtual < 0) $posicaoOrAtual = 0;
		
		//echo "\nAdicionando AND no BLOCO OR".$posicaoOrAtual;
		$this->restricoes[$posicaoOrAtual][] = $restricao;
	}
	
	public function unirModelagem($modelagem){
		$this->unioesModelagens[] = $modelagem;
	}
	
	public function temRestricao(){
		for($i = 0; $i < count($this->restricoes); $i++)
			if(count($this->restricoes[$i]) > 0)
				return true;
		
		return false;
	}
	
	//CHAMAR PARA A CLAUSULA WHERE
	public function getSqlRestricao(){
		$texto = " ( 1=1 "; // para funcionar com quando estiver vazio
		if ($this->temRestricao()){
			$texto .= " AND ( 1>1 ";
			for($i = 0; $i < count($this->restricoes); $i++){
				if(count($this->restricoes[$i]) > 0){
					//if($i != 0)
						$texto .= " OR ";
					$texto .= " ( ";
					for($j=0; $j<count($this->restricoes[$i]); $j++){
						if($j != 0)
							$texto .= " AND ";
						$texto .= $this->restricoes[$i][$j]->toSql();
					}
					$texto .= " )";
				}
			}
			$texto .= " ) ";
		}
		$texto .= " ) ";
		
		foreach($this->unioesModelagens as $modelagem){
			$texto .= " AND ".$modelagem->getSqlRestricao();
		}
		
		return $texto;
	}
	
	public function getTablesNamesRestricoes(){
		$tables = array();
		for($i = 0; $i < count($this->restricoes); $i++){
			for($j = 0; $j < count($this->restricoes[$i]); $j++){
				$table = $this->restricoes[$i][$j]->getTabela();
				if(!in_array($table, $tables)){
					$tables[] = $table;
				}
			}
		}
		return $tables;
	}
	
	//CHAMAR PARA A CLAUSULA FROM
	public function getJoinTables(){
		return "  br_uf ";		
		return " usuario INNER JOIN portal ON usuario.id_portal = portal.id_portal INNER JOIN curso ON usuario.id_curso = curso.id INNER JOIN br_uf ON upper(usuario.estado::text) = upper(br_uf.sigla::text) INNER JOIN br_muni ON upper(usuario.cidade::text) = upper(br_muni.nome::text)";		
		
		/*return " lote INNER JOIN propriedade ON lote.ssqqllff = propriedade.ssqqllff INNER JOIN proprietario ON propriedade.id_proprietario2 = proprietario.id_proprietario ";
		
		$tablesRest = $this->getTablesNamesRestricoes();
		$tablesXml = $this->getTablesNamesXml();
		
		//DJKSTRA com os identificadores de tablesXml
		
		
		
		for($i = 0; $i < count($tables); $i++){
			
		}
		*/ 
	}
	
	public function getTablesNamesXml(){
		$tables = array();
		foreach($this->getXml()->banco->tabelas->tabela as $table){
			$tables[] = $table->nome;
		}
		return $tables;
	}
	
	public function getTablesNamesDB(){
		$tables = array();
		$rs = pg_query("SELECT tabela_bas FROM bases ORDER BY tabela_bas");
		$linha = pg_fetch_assoc($rs);
		
		foreach($linha as $campo => $valor){
			$tables[] = $campo;
		}
		
		return $tables;
	}
	
	public function getFieldNamesXml($tableName){
		$fields = array();
		foreach($this->getXml()->banco->tabelas->tabela as $table){
			if($table->nome == $tableName){
				foreach($table->campos->campo as $field){
					$fields[] = $field;
				}
				break;
			}
		}
		return $fields;
	}
	
	public function getFieldNamesDB($tableName){
		$fields = array();
		
		$rs = pg_query("SELECT * FROM $tableName LIMIT 1");
		$linha = pg_fetch_assoc($rs);
		
		foreach($linha as $campo => $valor){
			$fields[] = $campo;
		}
		
		return $fields;
	}
}

?>
