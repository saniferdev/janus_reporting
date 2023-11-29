<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{
    if(isset($_POST['rechercher'])){
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
        $dateDebut = date_format($d_b,"Y-d-m H:i:s");
        $dateFin = date_format($d_f,"Y-d-m H:i:s");
        $tsql = "SELECT * FROM facture_entete WHERE DO_Date >= '".$dateDebut."' AND DO_Date <= '".$dateFin."'";
        //var_dump($dateDebut);
        //var_dump($dateFin);

      /*  $params = array($dateDebut, $dateFin);

        $getData = $dbh->prepare($tsql);

        $getData->execute($params);*/
        //var_dump($tsql);
        $getData = $dbh->query($tsql);
        $data = $getData->fetchAll(PDO::FETCH_OBJ);
        //var_dump($data);

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
                                    <h5>Bon Facture</h5>
                                </div>


                                <div class="panel-body">
                                    <form method="post" class="form-horizontal" enctype="multipart/form-data">

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Date du :</label>
                                                <div class="col-sm-8">
                                                    <input id="datepicker_debut" name="date_debut" value="<?php if(isset($dateDebut)){ echo date_format($d_b,"Y-m-d"); } ?>"/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Fin du :</label>
                                                <div class="col-sm-8">
                                                    <input id="datepicker_fin" class="form-control"  name="date_fin" value="<?php if(isset($dateFin)){ echo date_format($d_f,"Y-m-d"); } ?>"/>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <div class="col-sm-8 col-sm-offset-2">
                                                <button class="btn btn-primary" name="rechercher" type="submit">Chercher</button>
                                                <!--<a href="http://localhost/wms/gestion.php" class="btn btn-default">reset</a>-->

                                            </div>
                                        </div>
                                    </form>
                                    <table id="listeBon" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                        <thead>
                                        <tr style="background: #0d72d8;color: #fff;">
                                            <th>N° Bon</th>
                                            <th>Date de commande</th>
                                            <th>Bon Ref</th>
                                            <th>Statut</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach($data as $val) : ?>
                                                <tr>
                                                    <td><?php echo $val->DO_Piece; ?></td>
                                                    <td><?php echo $val->DO_Date; ?></td>
                                                    <td><?php echo $val->DO_Ref; ?></td>
                                                     <?php if($val->statut == 1){
                                                             ?>
                                                                <td><img src="images/status1.png" style="margin-left: 10px;"/></td>
                                                               <!-- <td><?php echo $val->statut; ?><input type="hidden" name="statut" class="statut" value="<?php echo $val->statut; ?>"></td>-->
                                                                <td><input type="submit" value="Valider" name="valider" class="btn btn-primary valide" disabled></td>
                                                            <?php
                                                            }else{

                                                                    ?>
                                                                    <td><img src="images/status0.png" style="margin-left: 10px;"/></td>
                                                                <?php
                                                            }
                                                                ?>
                                                    <td><a href="http://192.168.123.54/wms_test/gestion.php?numBont=<?php echo $val->DO_Piece; ?>">Details</a> </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php include('includes/footer.php'); ?>