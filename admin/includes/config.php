<?php 
// DB credentials.
if($_SERVER["HTTP_HOST"]== "www.netsutra.com" || $_SERVER["HTTP_HOST"]== "netsutra.com"){
    define('DB_HOST','db132b.pair.com');
	define('DB_USER','netsutra_29');
	define('DB_PASS','himtUtGb');
	define('DB_NAME','netsutra_attendence');
    define('SITE_URL','http://www.netsutra.com/leave/');
}else{
	define('DB_HOST','localhost');
	define('DB_USER','root');
	define('DB_PASS','');
	define('DB_NAME','netsutra_attendence_2022');
      define('SITE_URL','http://localhost/leave_portal/');
}
try
{
$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e)
{
exit("Error: " . $e->getMessage());
}

$gbl_statusArr = array("Waiting for approval","Approved","Not Approved","Cancelled","Requested for edit","Requested for cancellation");
$gbl_statColorArr = array("txt-blue","txt-green","txt-red","txt-red","txt-blue","txt-blue");
?>
