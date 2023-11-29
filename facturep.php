<?php
session_start();
error_reporting(1);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{
    if($_SESSION['depot'] == "SEC"){
        if(isset($_GET['p']) && $_GET['p'] == 1)
            echo '';
         else
            header("Location:facture_entrer.php");
    }

    if($_SESSION['site'] == "S3"){
        $q = " AND dbo.facture_ligne.AR_Ref IN ( SELECT dbo.article.AR_Ref_New FROM dbo.dep_s3_s4 INNER JOIN dbo.article ON dbo.article.AR_Ref = dbo.dep_s3_s4.S3_DEP ) ";
    }
    elseif($_SESSION['site'] == "S4"){
        $q = " AND dbo.facture_ligne.AR_Ref IN ( SELECT dbo.article.AR_Ref_New FROM dbo.dep_s3_s4 INNER JOIN dbo.article ON dbo.article.AR_Ref = dbo.dep_s3_s4.S4_DEP ) ";
    }
    else{
        $q = "";
    }

    if(isset($_GET['DO_Piece'])){

        $numBon = $_GET['DO_Piece'];

        $sql = "UPDATE facture_entete
                SET prepa = 1
                WHERE DO_Piece = '".$numBon."'";

        $c= $dbh->query($sql);
        // var_dump($data);

        header("Location:facturep.php");
    }

    if(isset($_GET['date_debut'])){
        $current_date_time = date_create($_GET['date_debut']);
        $user_current_date = date_format($current_date_time, "Y-m-d");

       

            $tsql = "SELECT DISTINCT TOP 50 (DO_Piece),DO_Ref,lineStat,
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
                    WHERE
                    ".$_SESSION['where']." 
                    ".$q."
                    AND dbo.facture_entete.DO_type IN(6,7,23,30)
                    AND CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "' 
                    AND dbo.facture_ligne.statut = 0 AND dbo.facture_entete.DO_Provenance IN(0,3))
                    AS tabEnt

                    WHERE
                    CAST(entDate AS date) = '" . $user_current_date. "'
                    ORDER BY entDate DESC";




        $getData = $dbh->query($tsql);

        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {


            $data[] = $row;

        }

        $val =  json_encode($data);
        $articles = json_encode($d);

    }elseif(isset($_GET['blfacture'])){

        $current_date_time = new DateTime("now");
        $user_current_date = $current_date_time->format("Y-m-d");

        $tsql = "SELECT DISTINCT TOP 50 (DO_Piece),DO_Ref,
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
                    )
                    AS tabEnt

                    WHERE
                    DO_Piece = '".$_GET['blfacture']."'";

                    $getData = $dbh->query($tsql);
                    

                while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {

                    $sql = "SELECT * FROM dbo.facture_ligne
                            INNER JOIN dbo.facture_entete ON dbo.facture_ligne.DO_Piece = dbo.facture_entete.DO_Piece
                            INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                            WHERE
                            dbo.facture_ligne.DO_Piece = '" . $row['DO_Piece']. "' AND dbo.article.DL = '".$_SESSION['depot']."'";
                    $get = $dbh->query($sql);
                    while ($rowA = $get->fetch(PDO::FETCH_ASSOC)) {
                        $d[] = $rowA;
                    }

                    $data[] = $row;

                }

                $val =  json_encode($data);
                $articles = json_encode($d);
        
    }else{
        $current_date_time = new DateTime("now");
        $user_current_date = $current_date_time->format("Y-m-d");

 

            $tsql = "SELECT DISTINCT TOP 50 (DO_Piece),DO_Ref,lineStat,
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
                    WHERE
                    ".$_SESSION['where']." 
                    ".$q."
                    AND dbo.facture_entete.DO_type IN(6,7,23,30)
                    AND CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "' 
                    AND dbo.facture_ligne.statut = 0 AND dbo.facture_entete.DO_Provenance IN (0,3))
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
        $articles = json_encode($d);

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
                            <!--<a href="prepaFini.php" class="pull-right btn btn-success livrFini" style="margin-top: -32px;">Livraison fini</a>-->
                        </div>


                        <div class="panel-body">
                            <div class="col-sm-5" style="margin-bottom: 0px">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">N° Facture :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control numFact" ng-model="numFacture" id="blfacture" name="blfacture" onKeyPress="if(event.keyCode == 13) validerForm()";/>
                                    </div>
                                </div>
                            </div>
                            <form method="get" action="facturep.php" class="form-horizontal" enctype="multipart/form-data">

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
                               <!-- <div class="enteteDrop">
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-2">N° Facture</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-3">Client</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-3">Commentaire</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-2">Date du commande</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-1">Status</span>
                                        <span style="font-size: 14px;font-weight: bold;" class="col-md-1">Action</span>
                                        <td title="'DO_Piece'" filter="{ DO_Piece: 'text'}" sortable="'DO_Piece'">
           {{facture.DO_Piece}}</td>
                                </div>-->


   <table ng-table="tableParams" class="table table-striped table-bordered table-hover" show-filter="true">
       <tr ng-repeat="facture in $data | filter:numFacture"" >
        <td title="'N° Facture'" filter="{ DO_Piece: 'text'}" sortable="'DO_Piece'" style="color:#0D72D8;">
           {{facture.DO_Piece}}</td>
         <td title="'Client'"  sortable="'Client'" style="color:#0D72D8;">
           {{ facture.DO_Tiers }} - {{ facture.CT_Intitule }}</td>
           <td title="'Commentaire'"  sortable="'Commentaire'" style="color:#0D72D8;">
           {{ facture.DO_Coord01 }} | {{ facture.DO_Coord02 }} | {{ facture.DO_Coord03 }} | {{ facture.DO_Coord04 }}</td>
           <td title="'Date de commande'"  sortable="'Date_du_commande'" style="width: 200px;color:#0D72D8;">
           {{facture.entDate| toDate | date:'dd/MM/yyyy HH:mm'}}</td>
           <td title="'Status'" sortable="'prepa'">
           <span ng-if="facture.prepa=='0'">
               <img src="images/status0.png" style="margin-left: 10px;"/>
           </span>
           <span ng-if="facture.prepa=='1'">
               <img src="images/status1.png" style="margin-left: 10px;"/>
           </span></td>

           <td title="'Action'" sortable="'Action'">
           <a href="gestion.php?numBont={{ facture.DO_Piece }}">Details</a></td>
       </tr>
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

    <script type="text/javascript" src="js/angular.js"></script>
    <script type="text/javascript" src="js/ng-table1.0.0.js"></script>
    <script type="text/javascript">
        var app = angular.module('dynamicApp', ['ngTable']);
        app.controller('dynamicCtrl', function($scope, $http, NgTableParams){
            $scope.fetchData = function(){
                $http.get('facturep.php').success(function(data){
                   // $scope.namesData = <?php echo $val; ?>;
                    // console.log($scope.namesData);

                    var namesData = <?php echo $val; ?>;

                     $scope.tableParams = new NgTableParams({ 
                        page: 1,            // show first page
                        count: 25
                      }, {
                       dataset: namesData
                     }); 
                });

                $http.get('facturep.php').success(function(data){
                    $scope.articleData = <?php echo $articles; ?>;
                    // console.log($scope.namesData);
                  //  var articleData = <?php echo $articles; ?>;

                   
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
        
           window.location.href = "facturep.php?blfacture="+blfacture;
        }

    </script>

<?php include('includes/footer.php'); ?>