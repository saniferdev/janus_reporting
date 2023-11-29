<?php
session_start();
error_reporting(1);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{

   if(isset($_GET['blfacture'])){

        $current_date_time = new DateTime("now");
        $user_current_date = $current_date_time->format("Y-m-d");

        $tsql = "SELECT DISTINCT(DO_Piece),DO_Ref,CT_Intitule,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa FROM (SELECT
                    dbo.facture_ligne.DO_Piece,
                    dbo.facture_entete.statut AS entStat,
                    dbo.facture_ligne.DO_Ref,
                    dbo.facture_entete.DO_Date AS entDate,
                    dbo.facture_ligne.statut AS lineStat,
                    dbo.facture_ligne.AR_Ref,
                    dbo.facture_ligne.DL_Qte,
                    dbo.facture_ligne.DO_Date AS lineDate,
                    dbo.facture_ligne.DL_Design,
                    dbo.article.AR_Design,
                    dbo.article.DL,
                    dbo.facture_entete.DO_Tiers,
                    dbo.facture_entete.prepa,
                    dbo.facture_entete.DO_Coord01,
                    dbo.facture_entete.DO_Coord02,
                    dbo.facture_entete.DO_Coord03,
                    dbo.facture_entete.DO_Coord04,
                    dbo.client.CT_Intitule,
                    dbo.client.CT_Adresse
                    FROM
                    dbo.facture_entete
                    INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                    LEFT JOIN dbo.client ON dbo.facture_entete.DO_Tiers = dbo.client.CT_Num
                    )
                    AS tabEnt

                    WHERE
                    DO_Piece = '".$_GET['blfacture']."'";

                    $getData = $dbh->query($tsql);
                

                while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {


                    $data[] = $row;

                }


                $val =  json_encode($data);

            
        
    }else{
        $current_date_time = new DateTime("now");
        $user_current_date = $current_date_time->format("Y-m-d");

 

            $tsql = "SELECT DISTINCT(DO_Piece),DO_Ref,CT_Intitule,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa FROM (SELECT
                    dbo.facture_ligne.DO_Piece,
                    dbo.facture_entete.statut AS entStat,
                    dbo.facture_ligne.DO_Ref,
                    dbo.facture_entete.DO_Date AS entDate,
                    dbo.facture_ligne.statut AS lineStat,
                    dbo.facture_ligne.AR_Ref,
                    dbo.facture_ligne.DL_Qte,
                    dbo.facture_ligne.DO_Date AS lineDate,
                    dbo.facture_ligne.DL_Design,
                    dbo.article.AR_Design,
                    dbo.article.DL,
                    dbo.facture_entete.DO_Tiers,
                    dbo.facture_entete.prepa,
                    dbo.facture_entete.DO_Coord01,
                    dbo.facture_entete.DO_Coord02,
                    dbo.facture_entete.DO_Coord03,
                    dbo.facture_entete.DO_Coord04,
                    dbo.client.CT_Intitule,
                    dbo.client.CT_Adresse
                    FROM
                    dbo.facture_entete
                    INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                    LEFT JOIN dbo.client ON dbo.facture_entete.DO_Tiers = dbo.client.CT_Num
                    )
                    AS tabEnt

                    WHERE
                    DO_Piece = ''";



        $getData = $dbh->query($tsql);

        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {

            $data[] = $row;

        }



    }


}
?>

<?php include('includes/entete.php'); ?>
    <div class="row">
        <div class="col-md-12"  ng-app="dynamicApp" ng-controller="dynamicCtrl" class="container" ng-init="fetchData()">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5>Annulation</h5>
                            <!--<a href="prepaFini.php" class="pull-right btn btn-success livrFini" style="margin-top: -32px;">Livraison fini</a>-->
                        </div>


                        <div class="panel-body">
                            <div class="col-sm-5" style="margin-bottom: 0px">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">N&#176; Facture :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control numFact" ng-model="numFacture" id="blfacture" name="blfacture" onKeyPress="if(event.keyCode == 13) validerForm()";/>
                                    </div>
                                </div>
                            </div>
                            <form method="get" action="facturep.php" class="form-horizontal" enctype="multipart/form-data">

                              <!--   <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Date du :</label>
                                        <div class="col-sm-8">
                                            <input id="datepicker_debut" name="date_debut" value="<?php if(isset($_GET['date_debut'])){ echo $_GET['date_debut']; }else{ echo $current_date_time->format("Y-m-d"); } ?>"/>
                                        </div>
                                    </div>
                                </div> -->

                                <!--   <div class="col-sm-5">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Fin du :</label>
                                                <div class="col-sm-8">
                                                    <input id="datepicker_fin" class="form-control"  name="date_fin" value="<?php if(isset($dateFin)){ echo date_format($d_f,"Y-m-d"); } ?>"/>
                                                </div>
                                            </div>
                                        </div>-->

                              <!--   <div class="col-sm-2">
                                    <div class="form-group">
                                        <div class="col-sm-8 col-sm-offset-2">
                                            <button class="btn btn-primary" name="rechercher" type="submit">Rechercher</button>

                                        </div>
                                    </div>
                                </div> -->
                            </form>

</div>


   <table id="listeFacture" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                <tr style="background: #0d72d8;color: #fff;">
                                                    <th>N&#176; Facture</th>
                                                    <th>Client</th>
                                                    <th>Commentaire</th>
                                                    <th>Date de commande</th>
                                                    <th>Action</th>
                                                    
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <?php foreach($data as $val){
                                                    ?>
                                                    <tr class="">
                                                      <td><?php echo $val['DO_Piece']; ?></td>
                                                      <td><?php echo $val['DO_Tiers'].'-'.$val['CT_Intitule']; ?></td>
                                                      <td><?php echo $val['DO_Coord01'].'|'.$val['DO_Coord02'].'|'.$val['DO_Coord03'].'|'.$val['DO_Coord04']; ?></td>
                                                      <td><?php echo $val['entDate']; ?></td>
                                                      <td><a href="annuler.php?numBont=<?php echo $val['DO_Piece']; ?>">Details</a></td>
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
            </div>
        </div>
    </div>


    <script type="text/javascript">
   
       
        /* validation formulaire BL */

        function validerForm(){
            console.log("ok");
                  
            var blfacture = $("#blfacture").val();
        
           window.location.href = "annulation.php?blfacture="+blfacture;
        }

    </script>

<?php include('includes/footer.php'); ?>