<?php
session_start();
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else {
    if (isset($_POST['rechercher'])) {

        $tsql = "SELECT * FROM facture_entete fe
         INNER JOIN facture_ligne fl ON fe.DO_Piece = fl.DO_Piece AND fe.DO_Type = fl.DO_Type
         INNER JOIN client c ON c.CT_Num = fe.DO_Tiers
         INNER JOIN article a ON fl.AR_Ref = a.AR_Ref
         WHERE fe.DO_Piece LIKE '%' + ? + '%' ";
        // $params = array($_POST['query']);
        //  if(isset($_POST['submit'])){
        $params = array($_POST['numBont']);

        $getData = $dbh->prepare($tsql);
        $getData->execute($params);
        $data = $getData->fetchAll(PDO::FETCH_OBJ);
        //var_dump($data);
        foreach($data as $val){
            $intitule       = $val->CT_Num." - ".$val->CT_Intitule;
            $numBon         = $val->DO_Piece;
            $commentaire    = $val->CT_Telephone." | ".$val->DO_Coord01." | ".$val->DO_Coord02." | ".$val->DO_Coord03." | ".$val->DO_Coord04;
            $dataComande    = $val->DO_Date;
        }
    }

    if(isset($_POST['fact'])){
       // var_dump($_POST);
        $refarticle     = $_POST['refarticle'];
        $numBont        = $_POST['numBont'];
        $dlqtte         = $_POST['dlqtte'];
        $dlretourner    = $_POST['dlretourner'];
        $statut         = $_POST['statut'];
        $l              = explode('_',$_POST['valider']);
        $DL_Ligne       = $l[1];


        if($dlretourner >= 0 && $dlretourner <= $dlqtte ){
            $error = "";
            echo $sql = "UPDATE facture_ligne
                SET DL_QteP = '" . $dlretourner . "'
                WHERE DL_Ligne ='" . $DL_Ligne . "' AND DO_Piece = '" . $numBont . "' ";
            $dbh->query($sql);

            if($dlqtte == $dlretourner){
                echo $sql = "UPDATE facture_ligne
                SET statut= 1
                WHERE DL_Ligne ='" . $DL_Ligne . "' AND DO_Piece = '" . $numBont . "' ";
                $dbh->query($sql);
            }

            echo $sqlt = "UPDATE stock
                SET AS_QteSto = AS_QteSto + '" . $valqt . "'
                WHERE  DE_No = (SELECT DE_No FROM facture_ligne WHERE DL_Ligne ='" . $DL_Ligne . "' AND DO_Piece = '" . $numBont . "' ) AND AR_Ref ='" . $refarticle . "' ";
                $dbh->query($sqlt);
        }else{
            $error = "La valeur n'est correspond pas !!";
        }





        //header("Location:?numBont=".$_POST['numBont']);



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
         //var_dump($data);
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
                            <h5>Gestion de Retour</h5>
                            <?php

                                echo "<span class='text-danger'>".$error."</span>";
                            ?>
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
                                        <a href="<?php echo $link; ?>/janus/retour.php" class="btn btn-default">reset</a>

                                    </div>
                                </div>
                            </form>
                            <table id="listeFacture" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                <tr style="background: #0d72d8;color: #fff;">

                                    <th>N° Article</th>
                                    <th>Description</th>
                                    <th>Qte Attendue</th>
                                    <th>Qte Retournée</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach($data as $val): ?>
                                    <tr class="tr">
                                        <form method="post">
                                            <td><?php echo $val->AR_Ref; ?><input type="hidden" name="refarticle" value="<?php echo $val->AR_Ref; ?>"></td>
                                            <td><?php echo $val->DL_Design; ?><input type="hidden" name="numBont" class="form-control numBon" id="numBon" value="<?php if(isset($numBon)){echo $numBon;} ?>"></td>
                                            <td><?php echo -($val->DL_Qte); ?><input type="hidden" name="dlqtte" value="<?php echo -($val->DL_Qte); ?>"></td>
                                            <td style="color:red;font-weight: bold;"><input type="text" name="dlretourner" value="<?php echo -($val->DL_Qte); ?>"></td>
                                            <input type="hidden" name="statut" value="<?php echo $val->statut; ?>">
                                            <input type="hidden" name="DL_Ligne" value="<?php echo $val->DL_Ligne; ?>">
                                            <input type="hidden" name="fact" value="<?php echo $val->DO_Piece.'_'.$val->DL_Ligne;?>">
                                            <?php
                                                $disabled = "";
                                                $stat     = "status0";
                                                if($val->statut == 1) {
                                                    $disabled = "disabled";
                                                    $stat     = "status1";
                                                }
                                            ?>
                                            <td><img src="images/<?php echo $stat;?>.png" style="margin-left: 10px;"/></td>
                                            <td><input type="submit" value="Valider" name="valider" class="btn btn-primary valide" <?php echo $disabled;?> ></td>                                                
                                        </form>
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