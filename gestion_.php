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
dbo.facture_entete.DO_Piece LIKE '%' + ? + '%'";*///= 02809025
        $tsql = "SELECT * FROM facture_entete fe
 INNER JOIN facture_ligne fl ON fe.DO_Piece = fl.DO_Piece AND fe.DO_Type = fl.DO_Type
 INNER JOIN client c ON c.CT_Num = fe.DO_Tiers
 INNER JOIN article a ON fl.AR_Ref = a.AR_Ref
 WHERE fe.DO_Piece LIKE '%' + ? + '%'";
        // $params = array($_POST['query']);
      //  if(isset($_POST['submit'])){
            $params = array($_POST['numBont']);

            $getData = $dbh->prepare($tsql);
            $getData->execute($params);
            $data = $getData->fetchAll(PDO::FETCH_OBJ);
           // var_dump($data);
            foreach($data as $val){
                $intitule = $val->CT_Num." - ".$val->CT_Intitule;
                $numBon = $val->DO_Piece;
                $commentaire = $val->CT_Telephone." | ".$val->DO_Coord01." | ".$val->DO_Coord02." | ".$val->DO_Coord03." | ".$val->DO_Coord04;
                $dataComande = $val->DO_Date;
             }
       // }
    }
    if($_POST['valider']){
       // var_dump($_POST);
        $refarticle = $_POST['refarticle'];
        $numBont = $_POST['numBont'];
        $dlqtte = $_POST['dlqtte'];
        $dlqtep = $_POST['dlqtep'];
        $statut = $_POST['statut'];

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

        $params = array($_POST['refarticle']);

        $getData = $dbh->prepare($tsql);
        $getData->execute($params);
        $data = $getData->fetchAll(PDO::FETCH_OBJ);
        foreach($data as $val){
            $qteSto = (int)$val->AS_QteSto;
            $qtep = (int)$dlqtep;

            $valqt = $qteSto - $qtep;

            $sqlt = "UPDATE stock
                SET AS_QteSto = $valqt
                WHERE  DE_No = 31 AND AR_Ref ='".$refarticle."'";
            $dbh->query($sqlt);
        }


       // var_dump($data);

        header("Location:?numBont=".$_POST['numBont']);



    }


    if(isset($_GET['numBont'])){
        $tsql = "SELECT * FROM facture_entete fe
         INNER JOIN facture_ligne fl ON fe.DO_Piece = fl.DO_Piece AND fe.DO_Type = fl.DO_Type
         INNER JOIN client c ON c.CT_Num = fe.DO_Tiers
         INNER JOIN article a ON fl.AR_Ref = a.AR_Ref
         WHERE fe.DO_Piece LIKE '%' + ? + '%'";
                // $params = array($_POST['query']);
                //  if(isset($_POST['submit'])){
                $params = array($_GET['numBont']);

                $getData = $dbh->prepare($tsql);
                $getData->execute($params);
                $data = $getData->fetchAll(PDO::FETCH_OBJ);
                // var_dump($data);
                foreach($data as $val){
                    $intitule = $val->CT_Num." - ".$val->CT_Intitule;
                    $numBon = $val->DO_Piece;
                    $commentaire = $val->CT_Telephone." | ".$val->DO_Coord01." | ".$val->DO_Coord02." | ".$val->DO_Coord03." | ".$val->DO_Coord04;
                    $dataComande = $val->DO_Date;
                }
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
                                        <h5>Gestion Consultation</h5>
                                    </div>


                                    <div class="panel-body">
                                        <form method="post" class="form-horizontal" enctype="multipart/form-data">

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Client :</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="client" class="form-control" value="<?php if(isset($intitule)){echo $intitule;} ?>" disabled>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Dépots :</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="numBont" class="form-control" disabled value="<?php if(isset($_SESSION['depot'])){echo $_SESSION['depot'];} ?>">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">N° Bon :</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="numBont" class="form-control" value="<?php if(isset($numBon)){echo $numBon;} ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Commentaire :</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="commentaire" class="form-control" disabled value="<?php if(isset($commentaire)){echo $commentaire;} ?>">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Date de commande :</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="dateCommande" class="form-control" disabled value="<?php if(isset($dataComande)){$date=date_create($dataComande);echo date_format($date,"d/m/Y");} ?>">
                                                    </div>
                                                </div>


                                            </div>



                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-2">
                                                    <button class="btn btn-primary" name="rechercher" type="submit">Chercher</button>
                                                    <a href="http://192.168.123.54/wms_test/gestion.php" class="btn btn-default">reset</a>

                                                </div>
                                            </div>
                                        </form>
                                            <table id="listeFacture" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                <tr style="background: #0d72d8;color: #fff;">
                                                    <th>N° Article</th>
                                                    <th>Description</th>

                                                    <th>QTT Commandée</th>
                                                    <th>QTT Reste livrer</th>
                                                    <th>Motif</th>
                                                    <th>QTT Preparé</th>
                                                    <th>Famille</th>
                                                    <th>Statut</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <?php foreach($data as $val){
                                                    ?>
                                                    <tr>
                                                      <form method="post">
                                                        <td><?php echo $val->AR_Ref; ?><input type="hidden" name="refarticle" value="<?php echo $val->AR_Ref; ?>"></td>
                                                        <td><?php echo $val->DL_Design; ?><input type="hidden" name="numBont" class="form-control numBon" id="numBon" value="<?php if(isset($numBon)){echo $numBon;} ?>"></td>

                                                        <td><?php echo number_format($val->DL_Qte); ?><input type="hidden" name="dlqtte" value="<?php echo number_format($val->DL_Qte); ?>"></td>
                                                        <td style="color:red;font-weight: bold;"><?php echo (number_format($val->DL_Qte) - number_format($val->DL_QteP)); ?></td>
                                                        <td><?php echo $val->MOTIF; ?></td>
                                                        <td><?php if($val->statut == 1){?> <input type="text" name="dlqtep" disabled value="<?php echo number_format($val->DL_QteP); ?>" <?php }else{?> <input type="text" name="dlqtep" value="<?php echo number_format($val->DL_QteP); ?>" <?php } ?>></td>
                                                        <td><?php echo $val->FA_CodeFamille; ?></td>
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

                                                                ?>
                                                                <!--<td><?php //echo $val->statut; ?><input type="hidden" name="statut" class="statut" value="<?php //echo $val->statut; ?>"></td>-->
                                                                <td><input type="submit" value="Valider" name="valider" class="btn btn-primary valide"></td>
                                                                <?php
                                                            } ?>

                                                      </form>
                                                    </tr>
                                               <?php } ?>
                                                </tbody>
                                            </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<?php include('includes/footer.php'); ?>