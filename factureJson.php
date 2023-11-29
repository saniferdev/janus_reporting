<?php

session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else {
   // if (isset($_POST['rechercher'])) {
//$tsql = "SELECT * FROM admin WHERE username LIKE '%' + ? + '%'";
        /*$tsql = "SELECT
        dbo.facture_entete.DO_Piece,
        dbo.facture_entete.DO_Type,
        dbo.facture_entete.DO_Date,
        dbo.facture_entete.DO_Ref,
        dbo.facture_entete.DO_Tiers,
        dbo.facture_entete.CO_No,
        dbo.facture_entete.DO_Souche,
        dbo.facture_entete.DO_Provenance,
        dbo.facture_entete.statut,
        dbo.facture_entete.DO_Coord01,
        dbo.facture_entete.DO_Coord02,
        dbo.facture_entete.DO_Coord03,
        dbo.facture_entete.DO_Coord04,
        dbo.facture_ligne.DO_Type,
        dbo.facture_ligne.CT_Num,
        dbo.facture_ligne.DO_Piece,
        dbo.facture_ligne.DO_Date,
        dbo.facture_ligne.DL_Ligne,
        dbo.facture_ligne.DO_Ref,
        dbo.facture_ligne.AR_Ref,
        dbo.facture_ligne.DL_Design,
        CAST(dbo.facture_ligne.DL_Qte AS DECIMAL(19,2)) AS DL_Qte,
        dbo.facture_ligne.CO_No,
        dbo.facture_ligne.DE_No,
        dbo.facture_ligne.DL_MontantHT,
        CAST(dbo.facture_ligne.DL_MontantTTC  AS DECIMAL(19,2)) AS DL_MontantTTC,
        dbo.facture_ligne.statut,
        dbo.facture_ligne.DL_QteP,
        dbo.client.CT_Num,
        dbo.client.CT_Intitule,
        dbo.client.CT_Telephone

        FROM
        dbo.facture_entete
        INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
        INNER JOIN dbo.client ON dbo.facture_ligne.CT_Num = dbo.client.CT_Num
        WHERE
        dbo.facture_entete.DO_Piece LIKE '%' + ? + '%'";*///= 02809025 WHERE date_naissance BETWEEN 2016-01-01 AND 2016-12-31
//$tsql = "SELECT * FROM facture_entete WHERE DO_Date >= ? AND DO_Date <= ?";
// $params = array($_POST['query']);
        $d_b = date_create($_POST['date_debut']);
        $d_f = date_create($_POST['date_fin']);
        $dateDebut = date_format($d_b, "Y-d-m H:i:s");
        $dateFin = date_format($d_f, "Y-d-m H:i:s");

        $current_date_time = new DateTime("now");
        $user_current_date = $current_date_time->format("Y-d-m");
        $tsql = "SELECT * FROM facture_entete WHERE DO_Date = '" . $user_current_date. "'";
       // $tsql = "SELECT * FROM facture_entete WHERE DO_Date >= '" . $dateDebut . "' AND DO_Date <= '" . $dateFin . "'";
   // $tsql = "SELECT * FROM facture_entete";
//var_dump($dateDebut);
//var_dump($dateFin);

        /*  $params = array($dateDebut, $dateFin);

        $getData = $dbh->prepare($tsql);

        $getData->execute($params);*/
//var_dump($tsql);
        $getData = $dbh->query($tsql);
// $data = $getData->fetchAll(PDO::FETCH_OBJ);
//var_dump($data);
        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        echo json_encode($data);

  //  }

   // header("location:facture.php");
}

//header('location:index.php');
?>
