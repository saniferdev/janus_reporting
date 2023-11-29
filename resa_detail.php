<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{
    if($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"  || $_SESSION['depot'] == "SANIFER IV"   || $_SESSION['depot'] == "SEC"){
      header('location:index.php');
    }

    if(isset($_GET['ref'])){
        $ref    = $_GET['ref'];
        $dl     = " AND dbo.article.DL = '".$_SESSION['depot']."'";
        if($_SESSION['site'] == "LIVRAISON" || $_SESSION['site'] == "LOGISTIQUE"){
            $dl = "";
        }
        $tsql   = "
                SELECT
                    CONVERT(varchar,dbo.facture_ligne.[DO_Date],3) AS DATES
                   ,dbo.facture_ligne.[DO_Piece] AS NUM
                   ,dbo.facture_ligne.[CT_Num] AS CLI                 
                   ,dbo.facture_entete.DO_Coord01 AS CLI_DESS
                   ,dbo.facture_entete.DO_Coord02 + ' | ' + dbo.facture_entete.DO_Coord03 + ' | ' + dbo.facture_entete.DO_Coord04 AS ADRR
                   ,dbo.facture_ligne.[AR_Ref] AS REF
                   ,dbo.facture_ligne.[DL_Design] AS DESS
                   ,dbo.facture_ligne.[DL_Qte] AS QTE
                   ,dbo.facture_ligne.statut AS STAT

                FROM
                    dbo.facture_entete
                    INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New

                WHERE
                    dbo.facture_entete.DO_type = 111
                    AND ( dbo.facture_ligne.statut_resa = 0 OR dbo.facture_ligne.statut_resa IS NULL OR dbo.facture_ligne.statut_resa = 1)
                    ".$dl."
                    AND dbo.facture_ligne.AR_Ref = ?

                ORDER BY     
                    dbo.facture_ligne.[DO_Date]
                    ,dbo.facture_ligne.DO_Piece";
       
                $params = array($_GET['ref']);
                $getData = $dbh->prepare($tsql);
                $getData->execute($params);
                $data = $getData->fetchAll(PDO::FETCH_OBJ);

    }
}
?>

<?php include('includes/entete.php'); ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h5>Détail Réservation</h5>
                                    </div>


                                    <div class="panel-body">
                                            <table id="listeReservation" class="display table table-striped table-bordered table-hover table_" cellspacing="0" width="100%">
                                                <thead>
                                                <tr style="background: #0d72d8;color: #fff;">
                                                    <th>Date</th>
                                                    <th>N° Pièce</th>
                                                    <th>Client</th>
                                                    <th>Intitulé</th>
                                                    <th>Adresse</th>
                                                    <th>N° Article</th>
                                                    <th>Désignation</th>
                                                    <th>Qte</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                        <?php
                                                foreach($data as $val){
                                                    $statut = ($val->STAT == 0) ? 'status0' : 'status1' ;
                                        ?>
                                                    <tr>
                                                        <td><?php echo $val->DATES;?></td>
                                                        <td><?php echo $val->NUM;?></td>
                                                        <td><?php echo $val->CLI;?></td>
                                                        <td><?php echo $val->CLI_DESS;?></td>
                                                        <td><?php echo $val->ADRR;?></td>
                                                        <td><?php echo $val->REF;?></td>
                                                        <td><?php echo $val->DESS;?></td>
                                                        <td><?php echo number_format($val->QTE, 2, ',', ' ');?></td>
                                                        <td><img src="images/<?php echo $statut; ?>.png" style="margin-left: 10px;"/><input type="hidden" class="st"></td>
                                                    </tr>
                                        <?php
                                                }
                                        ?>
                                                </tbody>
                                            </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<?php include('includes/footer.php'); ?>