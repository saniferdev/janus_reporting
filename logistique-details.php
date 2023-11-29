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

 
          echo  $tsql = "SELECT * FROM facture_entete fe
                 INNER JOIN facture_ligne fl ON fe.DO_Piece = fl.DO_Piece AND fe.DO_Type = fl.DO_Type
                 LEFT JOIN client c ON c.CT_Num = fe.DO_Tiers
                 INNER JOIN article a ON fl.AR_Ref = a.AR_Ref
                 WHERE fe.DO_Piece = ? ";
    
      
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
   


    if(isset($_GET['numBont'])){

            $tsql = "SELECT * FROM facture_entete fe
                     INNER JOIN facture_ligne fl ON fe.DO_Piece = fl.DO_Piece AND fe.DO_Type = fl.DO_Type
                     LEFT JOIN client c ON c.CT_Num = fe.DO_Tiers
                     INNER JOIN article a ON fl.AR_Ref = a.AR_Ref
                     WHERE fe.DO_Piece = ? ";

       
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
                                        <h5>Logistique</h5>
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
                                                    <label class="col-sm-4 control-label">N&#176; Bon :</label>
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



                                          
                                        </form>
                                         
                                            <table id="listeFacture" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                <tr style="background: #0d72d8;color: #fff;">
                                                    <th>N&#176; Article</th>
                                                    <th>Description</th>

                                                    <th>Quantite</th>
                                                    <th>Famille</th>
                                                    <th>D&eacute;pots</th>

                                                </tr>
                                                </thead>

                                                <tbody>
                                                <?php foreach($data as $val){
                                                    ?>
                                                    <tr class="">
                                                      <form method="post">
                                                        <td>
                                                        <?php echo $val->AR_Ref; ?><input type="hidden" name="refarticle[]" value="<?php echo $val->AR_Ref; ?>">
                                                        <input type="hidden" name="dlLigne[]" value="<?php echo $val->DL_Ligne; ?>">
                                                        </td>
                                                        <td><?php echo $val->DL_Design; ?><input type="hidden" name="numBont[]" class="form-control numBon" id="numBon" value="<?php if(isset($numBon)){echo $numBon;} ?>"></td>

                                                        <td><?php echo number_format($val->DL_Qte, 3, '.', ' '); ?><input type="hidden" name="dlqtte[]" value="<?php echo number_format($val->DL_Qte, 3, '.', ' '); ?>"></td>
                                                        

                                                        <td><?php echo $val->FA_CodeFamille; ?></td>
                                                          <td><?php echo $val->DL; ?></td>
                                                        

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