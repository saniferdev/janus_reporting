<?php
session_start();
error_reporting(1);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else {

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $dopiece = $request->do_piece;

    $datetime = new DateTime();
    //$datetime->setTimezone($tz_object);
    $dEntrer = $datetime->format('Y-d-m h:i:s');


    $sql = ("INSERT INTO historique_entre(entre_date, DO_Piece) VALUES(GETDATE(),'" . $dopiece . "')");
    //$sql = "INSERT INTO historique_entre(entre_date, DO_Piece) VALUES('2019-13-08 10:52:17','6634')";


    $result = $dbh->query($sql);

}
