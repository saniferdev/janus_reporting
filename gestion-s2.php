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
    }elseif($_SESSION['depot'] == "SANIFER IV"){
        $de_No = 34;
    }

    if(isset($_POST['rechercher'])){

        if($_SESSION['depot'] == "SEC"){
            $tsql = "SELECT * FROM facture_entete fe
                 INNER JOIN facture_ligne fl ON fe.DO_Piece = fl.DO_Piece AND fe.DO_Type = fl.DO_Type
                 LEFT JOIN client c ON c.CT_Num = fe.DO_Tiers
                 INNER JOIN article a ON fl.AR_Ref = a.AR_Ref_New
                 WHERE fe.DO_Piece = ?";
        }else{
            $tsql = "SELECT * FROM facture_entete 
                 INNER JOIN facture_ligne  ON dbo.facture_ligne.DO_Piece = dbo.facture_entete.DO_Piece AND dbo.facture_entete.DO_Type = dbo.facture_ligne.DO_Type
                 LEFT JOIN client c ON c.CT_Num = dbo.facture_entete.DO_Tiers
                 INNER JOIN article a ON AR_Ref = a.AR_Ref_New
                 WHERE dbo.facture_entete.DO_Piece = ? AND ".$_SESSION['where'];
        }
      
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
        $refarticle = $_POST['refarticle'][0];
        $dlLigne = $_POST['dlLigne'][0];
        $numBont = $_POST['numBont'][0];
         $dlqtte = (double)$_POST['dlqtte'][0];
        $dlqtep=str_replace(' ','',$_POST['dlqtep'][0]);
        $dlqtep = (double)$dlqtep;
       // $statut = $_POST['statut'];

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
       /* (double)$dlqtte;
        (double)$dlqtep;*/

        if($dlqtep >= 0 && $dlqtep <= $dlqtte ){
            $error = "";
            $sel = "SELECT DL_QteP,DL_Qte FROM facture_ligne  WHERE dbo.facture_ligne.AR_Ref = '".$refarticle."' AND dbo.facture_ligne.DO_Piece ='".$numBont."' AND dbo.facture_ligne.DL_Ligne = '".$dlLigne."'";

           

            $get = $dbh->query($sel);
            while ($rowA = $get->fetch(PDO::FETCH_OBJ)) {
                $dP = $rowA->DL_QteP;
                $dQ = $rowA->DL_Qte;
            }

            $qres = (float)$dQ - ((float)$dP + (float)$dlqtep);
            $qpre = (float)$dP + (float)$dlqtep;


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
                     fl.DO_Piece = '".$numBont."' AND fl.statut = 1 
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
                        fl.DO_Piece = '".$numBont."' 
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

            
        }


    }else{
            $error = "La valeur n'est correspond pas !!";
        }

       // var_dump($data);

        header("Location:?numBont=".$_POST['numBont'][0]);



    }


    if(isset($_GET['numBont'])){
        if($_SESSION['depot'] == "SEC"){
             $tsql = "SELECT * FROM facture_entete 
                     INNER JOIN facture_ligne  ON DO_Piece = DO_Piece AND DO_Type = DO_Type
                     LEFT JOIN client c ON c.CT_Num = DO_Tiers
                     INNER JOIN article a ON AR_Ref = a.AR_Ref_New
                     WHERE fe.DO_Piece = ?";
        }else{
             $tsql = "SELECT * FROM facture_entete 
                     INNER JOIN facture_ligne  ON dbo.facture_ligne.DO_Piece = dbo.facture_entete.DO_Piece AND dbo.facture_ligne.DO_Type = dbo.facture_entete.DO_Type
                     LEFT JOIN dbo.client ON dbo.client.CT_Num = dbo.facture_entete.DO_Tiers
                     INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                     WHERE dbo.facture_entete.DO_Piece = ? AND ".$_SESSION['where'];
        }
     
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

                                               <!-- <div class="form-group">
                                                    <label class="col-sm-4 control-label">Dépots :</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="numBont" class="form-control depot" disabled value="<?php if(isset($_SESSION['depot'])){echo $_SESSION['depot'];} ?>">
                                                    </div>
                                                </div>-->

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
                                                    <!--<a href="<?php echo $link; ?>/janus/gestion.php" class="btn btn-default">reset</a>-->

                                                </div>
                                            </div>
                                        </form>
                                         <!-- tout valider DEBUT -->

                                         <?php
                                            if($_SESSION['depot'] == "SEC"){

                                            }else{
                                            ?>
                                                <input type="submit" style="margin-bottom: 10px;" name="toutValider" value="Tout Valider" class="submit btn btn-success pull-right toutvalide" onclick="$(this).attr('disabled', true);">
                                            <?php
                                            }
                                            ?>

                                        <!-- tout valider Fin -->
                                            <table id="listeFacture" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                <tr style="background: #0d72d8;color: #fff;">
                                                    <th>N° Article</th>
                                                    <th>Description</th>

                                                    <th>Qte Commandé</th>
                                                    <th>Qte Reste livrer</th>
                                                    <th>Motif</th>
                                                    <th>Qte Preparé</th>
                                                    <th>Famille</th>
                                                   <!-- <th>Dépots</th>-->
                                                    <th>Statut</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <?php foreach($data as $val){
                                                    ?>
                                                    <tr class="">
                                                      <form method="post">
                                                        <td>
                                                        <?php echo $val->AR_Ref_New; ?><input type="hidden" name="refarticle[]" value="<?php echo $val->AR_Ref_New; ?>">
                                                        <input type="hidden" name="dlLigne[]" value="<?php echo $val->DL_Ligne; ?>">
                                                        </td>
                                                        <td><?php echo $val->DL_Design; ?><input type="hidden" name="numBont[]" class="form-control numBon" id="numBon" value="<?php if(isset($numBon)){echo $numBon;} ?>"></td>

                                                        <td><?php echo number_format($val->DL_Qte, 3, '.', ' '); ?><input type="hidden" name="dlqtte[]" value="<?php echo $val->DL_Qte; ?>"></td>
                                                        <td style="color:red;font-weight: bold;"><?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?></td>
                                                        <td><?php echo $val->MOTIF; ?></td>

                                                          <td><?php
                                                               if($val->statut == 1){
                                                                    if($val->DL == $_SESSION['depot']){?>
                                                                        <input type="text" name="dlqtep[]" class="btn_disabled" value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>" disabled>
                                                                        <?php
                                                                    }elseif($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"){?>
 <input type="text" name="dlqtep[]" class="btn_disabled" value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>" disabled>
                                                                    <?php
                                                                    }else{?>
                                                                        <input type="text" name="dlqtep[]" class="btn_disabled" value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>" <?php } ?>
                                                                    <?php
                                                                }else{
                                                                    if($val->DL == $_SESSION['depot']){?>
                                                                        <input type="text" name="dlqtep[]" class="btn_disabled" value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>"
                                                                        <?php
                                                                   }elseif($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"){?>
<input type="text" name="dlqtep[]" class="btn_disabled" value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>"
                                                                    <?php
                                                                    }else{?>
                                                                        <input type="text" name="dlqtep[]" class="btn_disabled" disabled value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>" <?php } ?>
                                                                    <?php
                                                                }?>
                                                          </td>

                                                        <td><?php echo $val->FA_CodeFamille; ?></td>
                                                         <!-- <td><?php echo $val->DL; ?></td>-->
                                                          <?php
                                                          if($val->statut == 1) {
                                                              if ($val->DL == $_SESSION['depot']) {
                                                                  ?>
                                                                  <td><img src="images/status1.png" style="margin-left: 10px;"/></td>
                                                                  <td><input type="submit" value="Valider" name="valider" class="btn btn-primary nonvalide <?php echo $val->DL; ?>" disabled  onclick="$(this).attr('disabled', true);">
                                                                  </td>
                                                              <?php
                                                               }elseif($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"){?>
                                                                  <td><img src="images/status1.png" style="margin-left: 10px;"/></td>
                                                                  <td><input type="submit" value="Valider" name="valider" class="btn btn-primary nonvalide <?php echo $val->DL; ?>" disabled  onclick="$(this).attr('disabled', true);">
                                                                  </td>
                                                                    <?php
                                                                    }else{
                                                                  ?>
                                                                  <td><img src="images/status1.png" style="margin-left: 10px;"/></td>
                                                                  <td><input type="submit" value="Valider" name="valider" class="btn btn-primary valide <?php echo $val->DL; ?>"
                                                                             disabled  onclick="$(this).attr('disabled', true);"></td>
                                                              <?php
                                                              }
                                                          }elseif($val->statut == 9){
                                                             if ($val->DL == $_SESSION['depot']) {
                                                                  ?>
                                                                  <td><img src="images/status9.png" style="margin-left: 10px;"/></td>
                                                                  <td>Annuler</td>
                                                              <?php
                                                              }elseif($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"){?>
                                                                 <td><img src="images/status9.png" style="margin-left: 10px;"/></td>
                                                                  <td>Annuler</td>
                                                                    <?php
                                                              }else{
                                                                  ?>
                                                                  <td><img src="images/status9.png" style="margin-left: 10px;"/></td>
                                                                  <td>Annuler</td>
                                                              <?php
                                                              }
                                                          }else {
                                                              if ($val->DL == $_SESSION['depot']) {
                                                                  ?>
                                                                  <td><img src="images/status0.png" style="margin-left: 10px;"/></td>
                                                                  <td><input type="submit" value="Valider" name="valider" class="btn btn-primary valide <?php echo $val->DL; ?>"
                                                                               onclick="$(this).attr('disabled', true);"></td>
                                                              <?php
                                                              }elseif($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"  || $_SESSION['depot'] == "SANIFER IV"){?>
                                                                 <td><img src="images/status0.png" style="margin-left: 10px;"/></td>
                                                                  <td><input type="submit" value="Valider" name="valider" class="btn btn-primary valide <?php echo $val->DL; ?>"></td>
                                                                    <?php
                                                                    }else{
                                                                  ?>
                                                                  <td><img src="images/status0.png" style="margin-left: 10px;"/></td>
                                                                  <td><input type="submit" value="Valider" name="valider" class="btn btn-primary nonvalide <?php echo $val->DL; ?>" disabled  onclick="$(this).attr('disabled', true);">
                                                                  </td>
                                                              <?php
                                                              }
                                                          }
                                                          ?>



                                                      </form>
                                                    </tr>
                                               <?php } ?>
                                                </tbody>
                                            </table>
                                            <div id="toutVal"></div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<?php include('includes/footer.php'); ?>