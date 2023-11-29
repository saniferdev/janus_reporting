<?php 
$sqlServerHost = '192.168.130.64';
$sqlServerDatabase = 'JANUS';
$sqlServerUser = 'appli_janus';
$sqlServerPassword = 'janus';


if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $link = "https";
else
    $link = "http";

// Here append the common URL characters.
$link .= "://";

// Append the host(domain name, ip) to the URL.
$link .= $_SERVER['HTTP_HOST'];



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
