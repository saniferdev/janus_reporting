<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{

    if($_POST['Toutpreparer']){
         //var_dump($_POST);

        $numBon = $_POST['numBont'];

        $sql = "UPDATE facture_entete
                SET prepa = 1
                WHERE DO_Piece = '".$numBon."'";

        $c= $dbh->query($sql);
        // var_dump($data);

        header("Location:facture.php");



    }


    if(isset($_GET['numBont'])){
        if($_SESSION['depot'] == "SEC"){
            $tsql = "SELECT * FROM facture_entete fe
         INNER JOIN facture_ligne fl ON fe.DO_Piece = fl.DO_Piece AND fe.DO_Type = fl.DO_Type
         INNER JOIN client c ON c.CT_Num = fe.DO_Tiers
         INNER JOIN article a ON fl.AR_Ref = a.AR_Ref
         WHERE fe.DO_Piece LIKE '%' + ? + '%'";
        }else{
            $tsql = "SELECT * FROM facture_entete fe
         INNER JOIN facture_ligne fl ON fe.DO_Piece = fl.DO_Piece AND fe.DO_Type = fl.DO_Type
         INNER JOIN client c ON c.CT_Num = fe.DO_Tiers
         INNER JOIN article a ON fl.AR_Ref = a.AR_Ref
         WHERE fe.DO_Piece = ? AND a.DL = '".$_SESSION['depot']."'";
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
                            <h5>Preparation facture</h5>
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

                            <!-- tout valider DEBUT -->
                            <?php
                            if($_SESSION['depot'] == "SEC"){

                            }else{
                                ?>
                                 <input type="submit" style="margin-bottom: 10px;" name="Toutpreparer" value="Tout Préparer" class="btn btn-success pull-right tout-preparer">
                                <input type="submit" style="margin-bottom: 10px;margin-right: 10px;" name="imprimer" value="Imprimer" class="btn btn-info pull-right imprimer" onclick="window.print();return false;">
                            <?php
                            }
                            ?>
                            </form>
                            <!-- tout valider Fin -->
                            <table id="listeFacture" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                <tr style="background: #0d72d8;color: #fff;">
                                    <th>N° Article</th>
                                    <th>Description</th>

                                    <th>Qte Commandée</th>
                                    <th>Qte Reste livrer</th>
                                    <th class="printNone">Prix net</th>
                                    <th class="printNone">Qte Preparé</th>
                                    <th>Dépots</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach($data as $val){
                                    ?>
                                    <tr class="">
                                        <form method="post">
                                            <td><?php echo $val->AR_Ref; ?><input type="hidden" name="refarticle[]" value="<?php echo $val->AR_Ref; ?>"></td>
                                            <td><?php echo $val->DL_Design; ?><input type="hidden" name="numBont[]" class="form-control numBon" id="numBon" value="<?php if(isset($numBon)){echo $numBon;} ?>"></td>

                                            <td><?php echo number_format($val->DL_Qte); ?><input type="hidden" name="dlqtte[]" value="<?php echo number_format($val->DL_Qte); ?>"></td>
                                            <td style="color:red;font-weight: bold;"><?php echo (number_format($val->DL_Qte) - number_format($val->DL_QteP)); ?></td>
                                            <td class="prixnet"><?php echo number_format($val->DL_MontantTTC,2,",","."); ?></td>

                                            <td class="qttprepare"><?php
                                                if($val->statut == 1){
                                                    if($val->DL == $_SESSION['depot']){?>
                                                        <input type="text" name="dlqtep[]" class="<?php echo $val->DL; ?>" value="<?php echo number_format($val->DL_QteP); ?>"
                                                        <?php
                                                    }else{?>
                                                        <input type="text" name="dlqtep[]" class="<?php echo $val->DL; ?>" disabled value="<?php echo number_format($val->DL_QteP); ?>" <?php } ?>
                                                <?php
                                                }else{
                                                    if($val->DL == $_SESSION['depot']){?>
                                                        <input type="text" name="dlqtep[]" class="<?php echo $val->DL; ?>" value="<?php echo number_format($val->DL_QteP); ?>"
                                                        <?php
                                                    }else{?>
                                                        <input type="text" name="dlqtep[]" class="<?php echo $val->DL; ?>" disabled value="<?php echo number_format($val->DL_QteP); ?>" <?php } ?>
                                                <?php
                                                }?>
                                            </td>

                                            <td><?php echo $val->DL; ?></td>

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