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
          /*  $tsql = "SELECT DISTINCT(DO_Piece),DO_Tiers,CT_Intitule FROM (SELECT
                facture_entete.DO_Piece,
                facture_entete.DO_Tiers,
                dbo.client.CT_Intitule,
                facture_ligne.AR_Ref,
                article.DL,
                facture_ligne.statut AS lineStat,
                facture_entete.statut AS entStat,
                facture_entete.DO_Date

                FROM
                facture_entete
                INNER JOIN facture_ligne ON facture_entete.DO_Piece = facture_ligne.DO_Piece
                INNER JOIN article ON facture_ligne.AR_Ref = article.AR_Ref_New
                INNER JOIN dbo.client ON dbo.facture_entete.DO_Tiers = dbo.client.CT_Num
                LEFT JOIN dbo.historique_sortie ON  dbo.historique_sortie.DO_Piece = dbo.facture_entete.DO_Piece
                WHERE  dbo.historique_sortie.DO_Piece IS NULL AND ".$_SESSION['where']."  AND facture_entete.DO_Type IN (6,7,30) AND facture_entete.DO_Provenance = 0)
                AS TabENT
                WHERE CAST(DO_Date AS date) = '" . $user_current_date. "'";*/
                 $tsql = "SELECT DISTINCT TOP 50  (DO_Piece),DO_Ref,lineStat,
            CASE 
                 WHEN DO_Tiers = '29' THEN 'SANIFER II'
                WHEN DO_Tiers = '30' THEN 'SANIFER II'
                WHEN DO_Tiers = '27' THEN 'SANIFER III'
                WHEN DO_Tiers = '32' THEN 'SANIFER III'
                ELSE CT_Intitule
            END AS CT_Intitule
            ,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa FROM (SELECT
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
                     LEFT JOIN dbo.historique_sortie ON  dbo.historique_sortie.DO_Piece = dbo.facture_entete.DO_Piece
                    WHERE
                    dbo.historique_sortie.DO_Piece IS NULL AND ".$_SESSION['where']." 
                    AND dbo.facture_entete.DO_type IN(6,7,23,30)
                    AND CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "' 
                    AND dbo.facture_entete.DO_Provenance = 0)
                    AS tabEnt

                    WHERE
                    CAST(entDate AS date) = '" . $user_current_date. "'
                    ORDER BY entDate DESC";

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
            /*$tsql = "SELECT DISTINCT(DO_Piece),DO_Tiers,CT_Intitule FROM (SELECT
                facture_entete.DO_Piece,
                facture_entete.DO_Tiers,
                dbo.client.CT_Intitule,
                facture_ligne.AR_Ref,
                article.DL,
                facture_ligne.statut AS lineStat,
                facture_entete.statut AS entStat,
                facture_entete.DO_Date

                FROM
                facture_entete
                INNER JOIN facture_ligne ON facture_entete.DO_Piece = facture_ligne.DO_Piece
                INNER JOIN article ON facture_ligne.AR_Ref = article.AR_Ref_New
                INNER JOIN dbo.client ON dbo.facture_entete.DO_Tiers = dbo.client.CT_Num
                LEFT JOIN dbo.historique_sortie ON  dbo.historique_sortie.DO_Piece = dbo.facture_entete.DO_Piece
                WHERE dbo.historique_sortie.DO_Piece IS NULL AND ".$_SESSION['where']."  AND facture_entete.DO_Type IN (6,7,30) AND facture_entete.DO_Provenance = 0)
                AS TabENT
                WHERE CAST(DO_Date AS date) = '" . $user_current_date. "'";*/
                  $tsql = "SELECT DISTINCT TOP 50  (DO_Piece),DO_Ref,lineStat,
            CASE 
                 WHEN DO_Tiers = '29' THEN 'SANIFER II'
                WHEN DO_Tiers = '30' THEN 'SANIFER II'
                WHEN DO_Tiers = '27' THEN 'SANIFER III'
                WHEN DO_Tiers = '32' THEN 'SANIFER III'
                ELSE CT_Intitule
            END AS CT_Intitule
            ,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa FROM (SELECT
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
                     LEFT JOIN dbo.historique_sortie ON  dbo.historique_sortie.DO_Piece = dbo.facture_entete.DO_Piece
                    WHERE
                    dbo.historique_sortie.DO_Piece IS NULL AND ".$_SESSION['where']." 
                    AND dbo.facture_entete.DO_type IN(6,7,23,30)
                    AND CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "' 
                    AND dbo.facture_entete.DO_Provenance = 0)
                    AS tabEnt

                    WHERE
                    CAST(entDate AS date) = '" . $user_current_date. "'
                    ORDER BY entDate DESC";
//echo $tsql;
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
                            <form method="get" action="facture_sortie.php" class="form-horizontal" enctype="multipart/form-data">

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
                                    <th>N° Bon</th>
                                    <th>Commantaire</th>
                                    <th>Date de commande</th>

                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr ng-repeat="data in namesData | filter:numFacture">
                                    <td><a href="<?php echo $link; ?>/janus/preparer.php?numBont={{ data.DO_Piece }}">{{ data.DO_Piece }}</a></td>
                                    <td><a href="<?php echo $link; ?>/janus/preparer.php?numBont={{ data.DO_Piece }}">{{ data.DO_Tiers }} - {{ data.CT_Intitule }}</a></td>
                                    <td><a href="<?php echo $link; ?>/janus/preparer.php?numBont={{ data.DO_Piece }}"><?php echo  $user_current_date; ?></a></td>

                                    <td>
                                        <!--<input type="submit" value="Entrer" class="btn btn-success" ng-click="insertEntrer()">-->
                                        <input type="submit" value="Sortie" class="btn btn-danger" ng-click="insertSortie()">
                                    </td>

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