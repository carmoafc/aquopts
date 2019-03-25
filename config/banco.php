<?
function ConectaDB(){
	

	
	$host = "localhost";
	$port = "5432";
	$db = "aquopts";
	$user = "postgres";
	$pass = "123aquopts123";
			
	$dbcon = "";
    if(!$dbcon = pg_connect("port=$port dbname=$db user=$user password=$pass connect_timeout=5")) die ("Error to connect database. Set the parameters on the file config/banco.php<br>".pg_last_error($dbcon));

    pg_query("SET statement_timeout='1min'");
    pg_query("SET NAMES 'UNICODE'");
    pg_query("SET CLIENT_ENCODING TO 'UNICODE'");
    pg_set_client_encoding("UNICODE");

    return $dbcon;
}

function DesconectaDB($conexao){
	if($conexao)
		pg_close($conexao);
}

?>
