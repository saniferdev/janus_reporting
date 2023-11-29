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

    //if(isset($_GET['ref'])){
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
                   ,dbo.facture_ligne.[DL_Ligne] AS LIGNE
                   ,dbo.facture_ligne.[AR_Ref] AS REF
                   ,dbo.facture_ligne.[DL_Design] AS DESS
                   ,dbo.facture_ligne.[DL_Qte] AS QTE
                   ,dbo.facture_entete.[Commercial] AS COM
                   ,dbo.facture_ligne.statut AS STAT
                   ,dbo.facture_ligne.statut_resa AS STAT_R
                   ,dbo.facture_ligne.statut_resa_prepa AS STAT_R_P

                FROM
                    dbo.facture_entete
                    INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New

                WHERE
                    dbo.facture_entete.DO_type = 111
                    AND ( dbo.facture_ligne.statut_resa = 0 OR dbo.facture_ligne.statut_resa IS NULL OR dbo.facture_ligne.statut_resa = 1)
                    ".$dl."

                ORDER BY     
                    dbo.facture_ligne.[DO_Date] DESC ";
       
                $params = array();
                $getData = $dbh->prepare($tsql);
                $getData->execute($params);
                $data = $getData->fetchAll(PDO::FETCH_OBJ);

    //}
        if( $_POST["valider"] ){
            $ref    = $_POST['ref'][0];
            $statut = $_POST['stat'][0];
            $num    = $_POST['num'][0];
            $ligne  = $_POST['ligne'][0];

            if($statut == 0){
                $stat_ = 1;
            }
            else if($statut == 1){
                $stat_ = 2;
            }
            else{
                $stat_ = 1;
            }
            $update = "UPDATE facture_ligne SET statut_resa = '".$stat_."' WHERE AR_Ref = '".$ref."' AND DO_Piece = '".$num."' AND dbo.facture_ligne.DL_Ligne = '".$ligne."'";
            $q_     = $dbh->query($update);

            if($q_) header("Location:reservations.php");
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
                                        <div class="col-md-12 expResa" style="padding-bottom: 25px;">
                                            <button class="btn btn-success xlsxResa" >Export to Excel</button>
                                        </div>
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
                                                    <th>Commercial</th>
                                                    <th>Statut</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                        <?php
                                                foreach($data as $val){
                                                    $statut = ($val->STAT == 0) ? 'status0' : 'status1' ;
                                                    if($val->STAT_R == 0){
                                                        $stat = 'status0';
                                                    }
                                                    else if($val->STAT_R == 1){
                                                        $stat = 'status2';
                                                    }
                                                    else{
                                                        $stat = 'status1';
                                                    }

                                        ?>
                                                    <tr>
                                                        <form method="post">
                                                            <td><?php echo $val->DATES;?></td>
                                                            <td>
                                                                <?php echo $val->NUM;?>
                                                                <input type="hidden" name="num[]" value="<?php echo $val->NUM; ?>">
                                                            </td>
                                                            <td><?php echo $val->CLI;?></td>
                                                            <td><?php echo $val->CLI_DESS;?></td>
                                                            <td><?php echo $val->ADRR;?></td>
                                                            <td>
                                                                <?php echo $val->REF;?>
                                                                <input type="hidden" name="stat[]" value="<?php echo $val->STAT_R; ?>">
                                                                <input type="hidden" name="ligne[]" value="<?php echo $val->LIGNE; ?>">
                                                                <input type="hidden" name="ref[]" value="<?php echo $val->REF; ?>">
                                                            </td>
                                                            <td><?php echo $val->DESS;?></td>
                                                            <td><?php echo number_format($val->QTE, 2, ',', ' ');?></td>
                                                            <td><?php echo $val->COM;?></td>
                                                            <td><img src="images/<?php echo $statut; ?>.png" style="margin-left: 10px;"/></td>
                                                            <td><input type="image" src="images/<?php echo $stat; ?>.png" id="statut_resa" border="0" style="margin-left: 10px;cursor: pointer;"></td>
                                                            <input type="hidden" name="valider" value="valider">
                                                        </form>
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