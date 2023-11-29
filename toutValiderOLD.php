<?php

session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{

$count = count($_POST['refarticle']);

for ($i=0; $i < $count; $i++) { 
	

	 $refarticle = $_POST['refarticle'][$i];
        $numBont = $_POST['numBont'][$i];
        $dlqtte = $_POST['dlqtte'][$i];
        $dlqtep = $_POST['dlqtep'][$i];

        if($dlqtep >= 0 && $dlqtep <= $dlqtte ){
            $error = "";
        $sql = "UPDATE facture_ligne
                SET DL_QteP= $dlqtep
                WHERE AR_Ref = $refarticle";
        $dbh->query($sql);

        if($dlqtte == $dlqtep){
            $sql = "UPDATE facture_ligne
                SET statut= 1
                WHERE AR_Ref = $refarticle";
            $dbh->query($sql);
        }

            /* Debut Update stock */
        $tsql = "SELECT
                article.AR_Ref,
                article.DL,
                stock.AR_Ref,
                stock.DE_No,
                stock.AS_QteSto,
                article.AR_Design,
                article.FA_CodeFamille

                FROM
                article
                INNER JOIN stock ON article.AR_Ref = stock.AR_Ref
                WHERE
                stock.DE_No = 31 AND
                article.AR_Ref = ?";

      //  $params = array($_POST['refarticle']);
        $params = array($_POST['refarticle'][$i]);

        $getData = $dbh->prepare($tsql);
        $getData->execute($params);
        $data = $getData->fetchAll(PDO::FETCH_OBJ);
        foreach($data as $val){
            $qteSto = (double)$val->AS_QteSto;
            $qtep = (double)$dlqtep;

            $valqt = $qteSto - $qtep;

            $sqlt = "UPDATE stock
                SET AS_QteSto = $valqt
                WHERE  DE_No = 31 AND AR_Ref ='".$refarticle."'";
            $dbh->query($sqlt);
            /* Fin Update stock */

           

        }
        }else{
            $error = "La valeur n'est correspond pas !!";
        }
}
//var_dump($_POST);

 /* Compter valeur status */
$numBont = $_POST['numBont'][0];

            $sqlCount = "SELECT
                        COUNT(*)
                        FROM
                        dbo.facture_entete AS fe
                        INNER JOIN dbo.facture_ligne AS fl ON fe.DO_Piece = fl.DO_Piece
                        WHERE
                        fl.DO_Piece = '".$numBont."'";
            $countStat = $dbh->query($sqlCount);
            $resStat = $countStat->fetch();

            /* Compter valeur status egal a 1*/
            $sqlC= "SELECT
                        COUNT(*)
                        FROM
                        dbo.facture_entete AS fe
                        INNER JOIN dbo.facture_ligne AS fl ON fe.DO_Piece = fl.DO_Piece
                        WHERE
                        fl.DO_Piece = '".$numBont."' AND fl.statut = 1";
            $countS = $dbh->query($sqlC);
            $resS= $countS->fetch();
            //var_dump($resStat);
            //var_dump($resS);
            /* update valeur statut entete 03048718*/
            if($resStat[0] == $resS[0]){
                 $sql = "UPDATE facture_entete
                SET statut= 1
                WHERE DO_Piece = '".$numBont."'";
                $dbh->query($sql);
            }
}
//header("Location:?numBont=".$_POST['numBont']);
?>

