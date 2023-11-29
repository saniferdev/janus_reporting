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
    }


    
    // Debut Annulation ligne //
    
    if($_POST['Annuler']){
       // var_dump($_POST);
        $refarticle = $_POST['refarticle'][0];
        $dlLigne = $_POST['dlLigne'][0];
        $numBont = $_POST['numBont'][0];
        $dlqtte = $_POST['dlqtte'][0];
        $dlqtep = $_POST['dlqtep'][0];
        $statut = $_POST['statut'];

  //var_dump($_POST);
  
 // $tsql = "DELETE FROM dbo.facture_ligne WHERE dbo.facture_ligne.AR_Ref = '".$refarticle."' AND dbo.facture_ligne.DL_Ligne = '".$dlLigne."'";
  echo $sqlt = "UPDATE dbo.facture_ligne
                SET statut= 9
                WHERE AR_Ref = '".$refarticle."' AND dbo.facture_ligne.DO_Piece ='".$numBont."' AND dbo.facture_ligne.DL_Ligne = '".$dlLigne."'";
                
              
            $dbh->query($sqlt);
  
  

        header("Location:?numBont=".$_POST['numBont'][0]);



    }
    
    
    //Fin annulation ligne //
    
    
 

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
                    /* LEFT JOIN dbo.client ON dbo.client.CT_Num = dbo.facture_entete.DO_Tiers*/
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
                                        <h5>Annulation</h5>
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
                                                    <label class="col-sm-4 control-label">N&#176; Facture :</label>
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



                          
                                            <table id="listeFacture" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                <tr style="background: #0d72d8;color: #fff;">
                                                    <th>N&#176; Article</th>
                                                    <th>Description</th>

                                                    <th>Qte Commandé</th>
                                                    <th>Qte Reste livrer</th>
                                                    <th>Prix net</th>
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
                                                        <?php echo $val->AR_Ref_New; ?><input type="hidden" name="<?php if($val->statut == 9){echo 'refarticleAnn[]';}else{echo 'refarticle[]';} ?>" value="<?php echo $val->AR_Ref_New; ?>">
                                                        <input type="hidden" name="<?php if($val->statut == 9){echo 'dlLigneAnn[]';}else{echo 'dlLigne[]';} ?>" value="<?php echo $val->DL_Ligne; ?>">
                                                        </td>
                                                        <td><?php echo $val->DL_Design; ?><input type="hidden" name="<?php if($val->statut == 9){echo 'numBonAnn[]';}else{echo 'numBont[]';} ?>" class="form-control numBon" id="numBon" value="<?php if(isset($numBon)){echo $numBon;} ?>"></td>

                                                        <td><?php echo number_format($val->DL_Qte, 3, '.', ' '); ?><input type="hidden" name="<?php if($val->statut == 9){echo 'dlqtteAnn[]';}else{echo 'dlqtte[]';} ?>" value="<?php echo number_format($val->DL_Qte, 3, '.', ' '); ?>"></td>
                                                        <td style="color:red;font-weight: bold;"><?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?></td>
                                                        <td><?php echo number_format($val->DL_MontantTTC,2,",","."); ?></td>

                                                          <td><?php
                                                               if($val->statut == 1){
                                                                    if($val->DL == $_SESSION['depot']){?>
                                                                        <input type="text" name="<?php if($val->statut == 9){echo 'dlqtepAnn[]';}else{echo 'dlqtep[]';} ?>" class="btn_disabled" value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>" disabled>
                                                                        <?php
                                                                    }elseif($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"){?>
 <input type="text" name="<?php if($val->statut == 9){echo 'dlqtepAnn[]';}else{echo 'dlqtep[]';} ?>" class="btn_disabled" value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>" disabled>
                                                                    <?php
                                                                    }else{?>
                                                                        <input type="text" name="<?php if($val->statut == 9){echo 'dlqtepAnn[]';}else{echo 'dlqtep[]';} ?>" class="btn_disabled" value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>" <?php } ?>
                                                                    <?php
                                                                }else{
                                                                    if($val->DL == $_SESSION['depot']){?>
                                                                        <input type="text" name="<?php if($val->statut == 9){echo 'dlqtepAnn[]';}else{echo 'dlqtep[]';} ?>" class="btn_disabled" value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>"
                                                                        <?php
                                                                   }elseif($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"){?>
<input type="text" name="<?php if($val->statut == 9){echo 'dlqtepAnn[]';}else{echo 'dlqtep[]';} ?>" class="btn_disabled" value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>"
                                                                    <?php
                                                                    }else{?>
                                                                        <input type="text" name="<?php if($val->statut == 9){echo 'dlqtepAnn[]';}else{echo 'dlqtep[]';} ?>" class="btn_disabled" disabled value="<?php $cv = $val->DL_Qte - $val->DL_QteP; echo number_format($cv, 3, '.', ' '); ?>" <?php } ?>
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
                                                                  <td><input type="submit" value="Annuler" name="Annuler" class="btn btn-primary nonvalide <?php echo $val->DL; ?>" >
                                                                  </td>
                                                              <?php
                                                               }elseif($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"){?>
                                                                  <td><img src="images/status1.png" style="margin-left: 10px;"/></td>
                                                                  <td><input type="submit" value="Annuler" name="Annuler" class="btn btn-primary nonvalide <?php echo $val->DL; ?>" >
                                                                  </td>
                                                                    <?php
                                                                    }else{
                                                                  ?>
                                                                  <td><img src="images/status1.png" style="margin-left: 10px;"/></td>
                                                                  <td><input type="submit" value="Annuler" name="Annuler" class="btn btn-primary valide <?php echo $val->DL; ?>"
                                                                             ></td>
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
                                                                  <td><input type="submit" value="Annuler" name="Annuler" class="btn btn-primary"
                                                                             ></td>
                                                              <?php
                                                              }elseif($_SESSION['depot'] == "SANIFER II" || $_SESSION['depot'] == "SANIFER III"){?>
                                                                 <td><img src="images/status0.png" style="margin-left: 10px;"/></td>
                                                                  <td><input type="submit" value="Annuler" name="Annuler" class="btn btn-primary"
                                                                             ></td>
                                                                    <?php
                                                              }else{
                                                                  ?>
                                                                  <td><img src="images/status0.png" style="margin-left: 10px;"/></td>
                                                                  <td><input type="submit" value="Annuler" name="Annuler" class="btn btn-primary" >
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