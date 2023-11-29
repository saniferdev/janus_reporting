<?php

session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{

	if($_SESSION['depot'] == "SANIFER II"){
        $de_No = 29;
    }elseif($_SESSION['depot'] == "SANIFER III"){
        $de_No = 27;
    }else{
         $de_No = 31;
    }

$count = count($_POST['refarticle']);

for ($i=0; $i < $count; $i++) { 
	

	 $refarticle = $_POST['refarticle'][$i];
    $dlLigne = $_POST['dlLigne'][$i];
        $numBont = $_POST['numBont'][$i];
        /*$dlqtte = (double)$_POST['dlqtte'][$i];
        $dlqtep = (double)$_POST['dlqtep'][$i];*/
         $dlqtte = (double)$_POST['dlqtte'][$i];
        $dlqtep=str_replace(' ','',$_POST['dlqtep'][$i]);
        $dlqtep = (double)$dlqtep;

        /* insert histo article */
        $datetime = new DateTime();
        $date= $datetime->format('d-m-Y H:i:s');

        $sqlHisto = "SELECT * FROM dbo.facture_ligne WHERE dbo.facture_ligne.DO_Piece = '".$numBont."' AND dbo.facture_ligne.AR_Ref = '".$refarticle."' AND dbo.facture_ligne.DL_Ligne = '".$dlLigne."'";

        $rH = $dbh->query($sqlHisto);
        $dataH = $rH->fetchAll(PDO::FETCH_OBJ);
        //var_dump($dataH);
        if($dataH[0]->cbModification == null){
            $qtrestant = $dlqtte - $dlqtep;
            $histo = array(['qtpreparer'=>$dlqtep, 'qtrestant'=>$qtrestant, 'date'=>$date]);
            $serialize = serialize($histo);
            //var_dump($serialize);
            $sqlH = "UPDATE facture_ligne SET cbModification = '".$serialize."' WHERE AR_Ref = '".$refarticle."' AND DO_Piece = '".$numBont."' AND dbo.facture_ligne.DL_Ligne = '".$dlLigne."'";
            //var_dump($sqlH);
            $update = $dbh->query($sqlH);
        }else{
            $qtrestant = $dlqtte - $dlqtep;
            $data_uncompressed = unserialize($dataH[0]->cbModification);
            array_push($data_uncompressed,['qtpreparer'=>$dlqtep, 'qtrestant'=>$qtrestant, 'date'=>$date]);
            //var_dump($data_uncompressed);
            $serialize = serialize($data_uncompressed);
            $sqlH = "UPDATE facture_ligne SET cbModification = '".$serialize."' WHERE AR_Ref = '".$refarticle."' AND DO_Piece = '".$numBont."' AND dbo.facture_ligne.DL_Ligne = '".$dlLigne."'";
            //var_dump($sqlH);
            $update = $dbh->query($sqlH);
        }

        /* fin insert histo article */

  /*  (double)$dlqtte;
    (double)$dlqtep;*/

        if($dlqtep >= 0 && $dlqtep <= $dlqtte ){
            $error = "";

            $sel = "SELECT DL_QteP,DL_Qte FROM facture_ligne  WHERE dbo.facture_ligne.AR_Ref = '".$refarticle."' AND dbo.facture_ligne.DO_Piece ='".$numBont."' AND dbo.facture_ligne.DL_Ligne = '".$dlLigne."'";
            $get = $dbh->query($sel);
            while ($rowA = $get->fetch(PDO::FETCH_OBJ)) {
                $dP = $rowA->DL_QteP;
                $dQ = $rowA->DL_Qte;
            }
          //  var_dump($refarticle);
          //  var_dump((float)$dP)/*qtt prepare table*/;
          //  var_dump((float)$dQ)/*qtt prepare table*/;
          //  var_dump((float)$dlqtep)/*qtt prepare champ*/;
            $qres = (float)$dQ - ((float)$dP + (float)$dlqtep);
            $qpre = (float)$dP + (float)$dlqtep;
           // var_dump($qpre)/*qtt reste livrer*/;
           // var_dump($qres)/*qtt reste livrer*/;
//die();
        $sql = "UPDATE facture_ligne
                SET DL_QteP= $qpre
                WHERE AR_Ref = '".$refarticle."' AND dbo.facture_ligne.DO_Piece ='".$numBont."' AND dbo.facture_ligne.DL_Ligne = '".$dlLigne."'";
        $dbh->query($sql);

        if($dlqtte == $qpre){
            $sql = "UPDATE facture_ligne
                SET statut= 1
                 WHERE AR_Ref = '".$refarticle."' AND dbo.facture_ligne.DO_Piece ='".$numBont."' AND dbo.facture_ligne.DL_Ligne = '".$dlLigne."'";
            $dbh->query($sql);
        }else{
            $sqlt = "UPDATE facture_ligne
                SET statut= 3
                WHERE AR_Ref = '".$refarticle."' AND dbo.facture_ligne.DO_Piece ='".$numBont."' AND dbo.facture_ligne.DL_Ligne = '".$dlLigne."'";
            $dbh->query($sqlt);
        }

         /* Compter valeur status */

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
            $sqlST ="SELECT
                     COUNT(fe.DO_Piece) as nb_ligne  
                     FROM
                     dbo.facture_entete AS fe
                     INNER JOIN dbo.facture_ligne AS fl ON fe.DO_Piece = fl.DO_Piece
                     INNER JOIN dbo.article ON fl.AR_Ref = dbo.article.AR_Ref_New
                     WHERE
                     fl.DO_Piece = '".$numBont."' AND fl.statut = 1 
                     GROUP BY fe.DO_Piece";

            $countST = $dbh->query($sqlST);
            $resST= $countST->fetch();

            /* Compter valeur status egal a 1 par depot */
            $sqlS ="SELECT
                     COUNT(fe.DO_Piece) as nb_ligne  
                     FROM
                     dbo.facture_entete AS fe
                     INNER JOIN dbo.facture_ligne AS fl ON fe.DO_Piece = fl.DO_Piece
                     INNER JOIN dbo.article ON fl.AR_Ref = dbo.article.AR_Ref_New
                     WHERE
                     fl.DO_Piece = '".$numBont."' AND fl.statut = 1 AND dbo.article.DL = '".$_SESSION['depot']."'
                     GROUP BY fe.DO_Piece";

            $countS = $dbh->query($sqlS);
            $resS= $countS->fetch();
            /*var_dump($resStat);
            var_dump($resS);*/
            /* update valeur statut entete 03048718*/
            
            /* Compter valeur status egal par depot */
            $sqlCD = "SELECT
                        COUNT(fe.DO_Piece) as nb_ligne  
                        FROM
                        dbo.facture_entete AS fe
                        INNER JOIN dbo.facture_ligne AS fl ON fe.DO_Piece = fl.DO_Piece
                        INNER JOIN dbo.article ON fl.AR_Ref = dbo.article.AR_Ref_New
                        WHERE
                        fl.DO_Piece = '".$numBont."' AND dbo.article.DL = '".$_SESSION['depot']."'
                        GROUP BY fe.DO_Piece";
                        
            $countCD = $dbh->query($sqlCD);
            $resCD= $countCD->fetch();
            
             if($resS[0] == $resCD[0]){
                $sql = "UPDATE facture_entete
                SET statut= 4
                WHERE DO_Piece = '".$numBont."'";
                $dbh->query($sql);
            }
            
            if($resStat[0] == $resST[0]){
                $sql = "UPDATE facture_entete
                SET statut= 1
                WHERE DO_Piece = '".$numBont."'";
                $dbh->query($sql);
            }

            /* Debut Update stock */
        $tsql = "SELECT
                article.AR_Ref_New,
                article.DL,
                stock.AR_Ref,
                stock.DE_No,
                stock.AS_QteSto,
                article.AR_Design,
                article.FA_CodeFamille

                FROM
                article
                INNER JOIN stock ON article.AR_Ref_New = stock.AR_Ref
                WHERE
                stock.DE_No = '".$de_No."' AND
                article.AR_Ref_New = ?";

        $params = array($refarticle);

        $getData = $dbh->prepare($tsql);
        $getData->execute($params);
        $data = $getData->fetchAll(PDO::FETCH_OBJ);
        foreach($data as $val){
            $qteSto = (double)$val->AS_QteSto;
            $qtep = (double)$dlqtep;

            $valqt = $qteSto - $qtep;

            $sqlt = "UPDATE stock
                SET AS_QteSto = '".$valqt."'
                WHERE  DE_No = '".$de_No."' AND AR_Ref ='".$refarticle."'";
            $dbh->query($sqlt);
            /* Fin Update stock */

            

        }
        }else{
            $error = "La valeur n'est correspond pas !!";
        }
}
//var_dump($_POST);
}

?>

