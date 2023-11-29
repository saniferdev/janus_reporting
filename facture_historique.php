<?php
session_start();
error_reporting(1);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{
    if(isset($_GET['date_debut'])){
        $current_date_time = date_create($_GET['date_debut']);
        $user_current_date = date_format($current_date_time, "Y-m-d");

        if($_SESSION['depot'] == "SEC"){
            $tsql = "SELECT
                    dbo.historique_entre.entre_date,
                    dbo.historique_sortie.sortie_date,
                    dbo.facture_entete.DO_Piece,
                    dbo.facture_entete.DO_Date

                    FROM
                    dbo.historique_entre
                    INNER JOIN dbo.historique_sortie ON dbo.historique_entre.DO_Piece = dbo.historique_sortie.DO_Piece
                    INNER JOIN dbo.facture_entete ON dbo.facture_entete.DO_Piece = dbo.historique_entre.DO_Piece
                    WHERE
                    CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "'";

            $getData = $dbh->query($tsql);

            while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }

            $val = json_encode($data);
        }


    }else{
        $current_date_time = new DateTime("now");
        $user_current_date = $current_date_time->format("Y-m-d");

        if($_SESSION['depot'] == "SEC"){
            $tsql = "SELECT
                    dbo.historique_entre.entre_date,
                    dbo.historique_sortie.sortie_date,
                    dbo.facture_entete.DO_Piece,
                    dbo.facture_entete.DO_Date

                    FROM
                    dbo.historique_entre
                    INNER JOIN dbo.historique_sortie ON dbo.historique_entre.DO_Piece = dbo.historique_sortie.DO_Piece
                    INNER JOIN dbo.facture_entete ON dbo.facture_entete.DO_Piece = dbo.historique_entre.DO_Piece
                    WHERE
                    CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "'";

            $getData = $dbh->query($tsql);

            while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }

            $val =  json_encode($data);
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
                            <h5>Facture Sortie</h5>
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
                            <form method="get" action="facture_historique.php" class="form-horizontal" enctype="multipart/form-data">

                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Date du :</label>
                                        <div class="col-sm-8">
                                            <input id="datepicker_debut" name="date_debut" value="<?php if(isset($_GET['date_debut'])){ echo $_GET['date_debut']; }else{ echo $current_date_time->format("Y-m-d"); } ?>"/>
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
                                    <th>N° Facture</th>
                                    <th>Date Entrée</th>
                                    <th>Date Sortie</th>
                                    <th>Date du jour</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr ng-repeat="data in namesData | filter:numFacture">
                                    <td><a href="<?php echo $link; ?>/janus/preparer.php?numBont={{ data.DO_Piece }}">{{ data.DO_Piece }}</a></td>
                                    <td><a href="<?php echo $link; ?>/janus/preparer.php?numBont={{ data.DO_Piece }}" style="color:#93c54b;font-weight: bold;">{{ data.entre_date }}</a></td>
                                    <td><a href="<?php echo $link; ?>/janus/preparer.php?numBont={{ data.DO_Piece }}" style="color:#d9534f;font-weight: bold;">{{ data.sortie_date }}</a></td>
                                    <td><a href="<?php echo $link; ?>/janus/preparer.php?numBont={{ data.DO_Piece }}"><?php echo  $user_current_date; ?></a></td>



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
            };

            $scope.insertEntrer = function(){
                console.log($scope.numFacture);
                $http.post("insertHistory.php",{'do_piece':$scope.numFacture})
                    .success(function(data){
                        console.log("data insert succesfully");
                        console.log(data);

                    });

            }

            $scope.insertSortie = function(){
                var request = $http({
                    method: "post",
                    url: "insertHistorySortie.php",
                    data: {
                        do_piece: $scope.numFacture

                    },
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).then(function(data, status, headers, config) {
                    console.log(data);
                    window.location.href = 'facture_sortie.php';
                }, function(data, status, headers, config) {
                    //an error occurred
                });

            }

            $scope.query = "";
        });


    </script>

<?php include('includes/footer.php'); ?>