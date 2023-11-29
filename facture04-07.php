<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{
    if(isset($_GET['date_debut'])){
         $current_date_time = date_create($_GET['date_debut']);
        $user_current_date = date_format($current_date_time, "Y-d-m");
       // $tsql = "SELECT * FROM facture_entete WHERE DO_Date = '" . $user_current_date. "'";

     $tsql = "SELECT dbo.facture_ligne.DO_Piece
        FROM
        dbo.facture_entete
        INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
        INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref
        WHERE
        dbo.article.DL = '".$_SESSION['depot']."' AND dbo.facture_entete.DO_Date = '" . $user_current_date. "'

        GROUP BY dbo.facture_ligne.DO_Piece";

          $getData = $dbh->query($tsql);

        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $val = json_encode($data);
    }else{
          $current_date_time = new DateTime("now");
        $user_current_date = $current_date_time->format("Y-d-m");
       // $tsql = "SELECT * FROM facture_entete WHERE DO_Date = '" . $user_current_date. "'";
         $tsql = "SELECT dbo.facture_ligne.DO_Piece
        FROM
        dbo.facture_entete
        INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
        INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref
        WHERE
        dbo.article.DL = '".$_SESSION['depot']."' AND dbo.facture_entete.DO_Date = '" . $user_current_date. "'

        GROUP BY dbo.facture_ligne.DO_Piece";

        $getData = $dbh->query($tsql);

        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $val =  json_encode($data);
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
                                    <h5>Préparation</h5>
                                </div>


                                <div class="panel-body">
                                    <div class="col-sm-5" style="margin-bottom: 20px">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">N° Facture :</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control numFact" ng-model="numFacture"/>
                                            </div>
                                        </div>
                                    </div>
                                    <form method="get" action="facture.php" class="form-horizontal" enctype="multipart/form-data">

                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Date du :</label>
                                                <div class="col-sm-8">
                                                    <input id="datepicker_debut" name="date_debut" value="<?php if(isset($_GET['date_debut'])){ echo $_GET['date_debut']; } ?>"/>
                                                </div>
                                            </div>
                                        </div>

                                     <!--   <div class="col-sm-5">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Fin du :</label>
                                                <div class="col-sm-8">
                                                    <input id="datepicker_fin" class="form-control"  name="date_fin" value="<?php if(isset($dateFin)){ echo date_format($d_f,"Y-m-d"); } ?>"/>
                                                </div>
                                            </div>
                                        </div>-->

                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-2">
                                                    <button class="btn btn-primary" name="rechercher" type="submit">Rechercher</button>
                                                  
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    

                                    <table id="listeBon" name="listeBon" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                        <thead>
                                        <tr style="background: #0d72d8;color: #fff;">
                                            <th>N° Bon</th>
                                            <th>Date de commande</th>
                                          <!--  <th>Bon Ref</th>
                                            <th>Statut</th>-->
                                            <th>Action</th>
                                        </tr>
                                        </thead>

                                        <tbody>

                                                <tr ng-repeat="data in namesData | filter:numFacture">
                                                    <td>{{ data.DO_Piece }}</td>
                                                    <td><?php echo  $user_current_date; ?></td>
                                                   <!-- <td>{{ data.DO_Ref }}</td>

                                                            <td class="stat">{{ data.statut }}<!--<img src="images/status0.png" style="margin-left: 10px;"/></td>-->
                                                            <td><a href="http://192.168.123.54/wms_test/gestion.php?numBont={{ data.DO_Piece }}">Details</a> </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <script type="text/javascript" src="js/angular.js"></script>
    <script type="text/javascript">
        var app = angular.module('dynamicApp', []);
        app.controller('dynamicCtrl', function($scope, $http){
            $scope.fetchData = function(){
                $http.get('facture.php').success(function(data){
                    $scope.namesData = <?php echo $val; ?>;
                   // console.log($scope.namesData);
                });
            }

            $scope.query = "";
        });


    </script>

<?php include('includes/footer.php'); ?>