<?php
session_start();
error_reporting(1);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{ 
    if($_SESSION['site'] == "S2" || $_SESSION['site'] == "S3" || $_SESSION['site'] == "S4"){
        header('location:index.php');
    }
    $dl = " AND dbo.article.DL = '".$_SESSION['depot']."'";
    if($_SESSION['site'] == "LIVRAISON" || $_SESSION['site'] == "LOGISTIQUE"){
        $dl = "";
    }

    $tsql = "
                SELECT
                    dbo.facture_ligne.AR_Ref AS AR_Ref,
                    dbo.facture_ligne.DL_Design AS DL_Design,
                    SUM(dbo.facture_ligne.DL_Qte) AS DL_Qte

                FROM
                    dbo.facture_entete
                    INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                WHERE
                    dbo.facture_entete.DO_type = 111
                    ".$dl."
                    AND dbo.facture_ligne.statut = 0
               
               GROUP BY
                    dbo.facture_ligne.AR_Ref,
                    dbo.facture_ligne.DL_Design

                ORDER BY     
                    dbo.facture_ligne.AR_Ref ";

        $getData = $dbh->query($tsql);

}
?>

<?php include('includes/entete.php'); ?>
    <div class="row">
        <div class="col-md-12"  class="container" >
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5>Réservation</h5>
                        </div>


                        <div class="panel-body">

                            <table id="resa" name="resa" class="display table table-striped table-bordered table-hover table_" cellspacing="0" width="100%">
                                <thead>
                                <tr style="background: #0d72d8;color: #fff;">
                                    <th>N° Ref</th>
                                    <th>Désignation</th>
                                    <th>Qte</th>
                                </tr>
                                </thead>

                                <tbody>
        <?php
                             while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {
        ?>
                                <tr>
                                    <td><a href="resa_detail.php?ref=<?php echo $row['AR_Ref'];?>"><?php echo $row['AR_Ref'];?></a></td>
                                    <td><a href="resa_detail.php?ref=<?php echo $row['AR_Ref'];?>"><?php echo $row['DL_Design'];?></a></td>
                                    <td><a href="resa_detail.php?ref=<?php echo $row['AR_Ref'];?>"><?php echo number_format($row['DL_Qte'], 2, ',', ' ');?></a></td>
                                </tr>
        <?php
                            }
        ?>
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
       /* var app = angular.module('dynamicApp', []);
        app.controller('dynamicCtrl', function($scope, $http){
            $scope.fetchData = function(){
                $http.get('facture.php').success(function(data){
                    $scope.namesData = <?php echo $val; ?>;
                    // console.log($scope.namesData);
                });
            }

            $scope.query = "";
        });
         app.filter('toDate', function() {
                      return function(items) {
                        return new Date(items);
                      };
                    });*/

    </script>

<?php include('includes/footer.php'); ?>