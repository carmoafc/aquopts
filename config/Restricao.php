<?
class Restricao{
	private $tabela = "";
	private $coluna = "";
	private $operador = "";
	private $valor = "";
	
	public function __construct($tabela = "", $coluna = "", $operador = "", $valor = "") {
		$this->setTabela($tabela);
		$this->setColuna($coluna);
		$this->setOperador($operador);
		$this->setValor($valor);
    }
    
    public function __destruct() {

	}

	public function getTabela(){
		return $this->tabela;
	}
	
	public function getColuna(){
		return $this->coluna;
	}
	
	public function getOperador(){
		return $this->operador;
	}
	
	public function getValor(){
		return $this->valor;
	}
	
	public function setTabela($tabela){
		$this->tabela = $tabela;
	}
	
	public function setColuna($coluna){
		$this->coluna = $coluna;
	}
	
	public function setOperador($operador){
		$this->operador = $operador;
	}
	
	public function setValor($valor){
		$this->valor = $valor;
	}
	
	//######################################
	//######################################
	//######################################
	
	public function toSql(){
		return " (". $this->getTabela() .".". $this->getColuna() ." ". $this->getOperador() ." '". $this->getValor() . "') ";
	}
}

?>
