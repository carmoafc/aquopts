<?

session_start ();

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
ini_set ( 'max_execution_time', 10800 ); // 3 hours
//ini_set ( 'memory_limit', '1GB' );
ini_set ( 'post_max_size', 60 * 1024 * 1024 * 8 );
ini_set ( 'upload_max_filesize', 30 * 1024 * 1024 * 8 );

$GLOBALS ['url_base'] = "www.aquopts.com/";
$GLOBALS ['url_base_full'] = $GLOBALS ['url_base'] . "";
$GLOBALS ['sigla_base'] = "Aquopts";
$GLOBALS ['nome_base'] = "Hydrological Optical Data Processing System";

$GLOBALS ['email_contact'] = array()
$GLOBALS ['email_contact']['email'] = 'contact-aquopts@gmail.com';
$GLOBALS ['email_contact']['host_smtp'] = 'smtp.gmail.com';
$GLOBALS ['email_contact']['port_smtp'] = 465;
$GLOBALS ['email_contact']['username'] = $GLOBALS ['email_contact']['email']; 
$GLOBALS ['email_contact']['password'] = '123aquopts123'; 


?>
