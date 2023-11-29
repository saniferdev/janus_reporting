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



            $tsql = "SELECT DISTINCT(DO_Piece),DO_Ref,CT_Intitule,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa,DO_Type FROM (SELECT
                    dbo.facture_ligne.DO_Piece,                 
                    dbo.facture_ligne.DO_Ref,
                    dbo.facture_entete.DO_Date AS entDate,
                    dbo.facture_entete.DO_Type,
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
                    WHERE                    
										dbo.facture_entete.DO_type IN(16,20,21)
                    AND CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "')                   
                    AS tabEnt
                    WHERE
                    CAST(entDate AS date) = '" . $user_current_date. "' 
                    ORDER BY entDate DESC";



        $getData = $dbh->query($tsql);

        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {
           
            $data[] = $row;
        }

        $val =  json_encode($data);
       

    }elseif(isset($_GET['blfacture'])){

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

      

            $tsql = "SELECT DISTINCT(DO_Piece),DO_Ref,CT_Intitule,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa,DO_Type FROM (SELECT
                    dbo.facture_ligne.DO_Piece,                 
                    dbo.facture_ligne.DO_Ref,
                    dbo.facture_entete.DO_Date AS entDate,
                    dbo.facture_entete.DO_Type,
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
                    WHERE                    
										dbo.facture_entete.DO_type IN(16,20,21)
                    AND CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "')                   
                    AS tabEnt
                    WHERE
                    CAST(entDate AS date) = '" . $user_current_date. "' 
                    ORDER BY entDate DESC";

        


        $getData = $dbh->query($tsql);

        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {

           
            $data[] = $row;

        }

        /*var_dump($data);
        echo "-------------------------";*/
        // var_dump($d);
        
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
                            <h5>Logistique</h5>
                            <!--<a href="prepaFini.php" class="pull-right btn btn-success livrFini" style="margin-top: -32px;">Livraison fini</a>-->
                        </div>


                        <div class="panel-body">
                            <div class="col-sm-5" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">N&#176; Facture :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control numFact" ng-model="numFacture" id="blfacture" name="blfacture" onKeyPress="if(event.keyCode == 13) validerForm()";/>
                                    </div>
                                </div>
                            </div>
                            <form method="get" action="logistique.php" class="form-horizontal" enctype="multipart/form-data">

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

</div>
                                <!--Drop down -->
                                <div class="enteteDrop">
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-2">N&#176; Facture</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-3">Client</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-4">Commentaire</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-2">Date du commande</span>
                                       
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-1">Action</span>
                                </div>

                                <div ng-repeat="data in namesData | filter:numFacture">
                                    <div class="containerDrop">


                                        <div class="card">
                                            <div class="card-header" id="headingThree">
                                                <h2 class="mb-0">
                                                    <button style="width: 1130px;text-align: left;font-size: 13px;" class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#{{ data.DO_Piece }}" aria-expanded="false" aria-controls="{{ data.DO_Piece }}">

                                                        <span class="col-md-2 test">{{ data.DO_Piece }}</span>

                                                        <span class="col-md-3">{{ data.DO_Tiers }} - {{ data.CT_Intitule }}</span>

                                                        <span class="col-md-4">{{ data.DO_Coord01 }} | {{ data.DO_Coord02 }} | {{ data.DO_Coord03 }} | {{ data.DO_Coord04 }}</span>


                                                        <span class="col-md-2">{{data.entDate| toDate | date:'dd/MM/yyyy HH:mm'}}</span>
                                                       
                                                        <span class="col-md-1"><a href="logistique-details.php?numBont={{ data.DO_Piece }}">Details</a></span>


                                                    </button>
                                                </h2>
                                            </div>
                                          
                                        </div>
                                    </div>

                                </div>

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



            $scope.insertSortie = function(){
                var request = $http({
                    method: "post",
                    url: "insertHistory.php",
                    data: {
                        do_piece: $scope.numFacture,

                    },
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).then(function(data, status, headers, config) {
                    console.log(data);
                }, function(data, status, headers, config) {
                    //an error occurred
                });

            }

            $scope.query = "";
        });

        app.filter('toDate', function() {
                      return function(items) {
                        return new Date(items);
                      };
                    });

        /* rafraichir page tous les 5min */

        function refresh(time)
        {
          setTimeout(function () { window.location.reload(); }, time*1000);
        }
        refresh(120);
        
        /* validation formulaire BL */

        function validerForm(){
            console.log("ok");
                  
            var blfacture = $("#blfacture").val();
        
           window.location.href = "logistique.php?blfacture="+blfacture;
        }

    </script>

<?php include('includes/footer.php'); ?>