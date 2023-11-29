<?php
/*$sqlServerHost = 'localhost';
$sqlServerDatabase = 'wms';

$sqlServerUser = 'root';
$sqlServerPassword = 'root';
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','wms');*/
$sqlServerHost = '192.168.120.210';
$sqlServerDatabase = 'APPSAN';
$sqlServerUser = 'dev';
$sqlServerPassword = 'dev';

// Establish database connection.
try
{
//$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    //$dbh =   new PDO("sqlsrv:Server=".DB_HOST.";Database=".DB_NAME.", ".DB_USER.",".DB_PASS);
    $dbh = new PDO("sqlsrv:Server=$sqlServerHost;Database=$sqlServerDatabase", $sqlServerUser,$sqlServerPassword);
}
catch (PDOException $e)
{
    exit("Error: " . $e->getMessage());
}

?>
