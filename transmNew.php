<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{
    if($_SESSION['depot'] == "SEC"){
        header("Location:facture_entrer.php");
    }

    if(isset($_GET['DO_Piece'])){

        $numBon = $_GET['DO_Piece'];

        $sql = "UPDATE facture_entete
                SET prepa = 1
                WHERE DO_Piece = '".$numBon."'";

        $c= $dbh->query($sql);
        // var_dump($data);

        header("Location:facture.php");
    }
   
   if(isset($_GET['transmission'])){
       $current_date_time = date_create($_GET['date_debut']);
        $user_current_date = date_format($current_date_time, "Y-d-m");

        
        $d = date('Y-m-d', strtotime($_GET['date_fin']. ' + 1 days'));
        $t= date_create($d);
        $dateFin = date_format($t, "Y-d-m");
 
        
     $refreshIntvInMin = 5;


$tsql = "
            SELECT
                  dbo.facture_ligne.DO_Piece,
                  dbo.facture_ligne.DO_Ref,
                  dbo.facture_entete.statut,
                  dbo.client.CT_Intitule,
                  dbo.client.CT_Adresse,
                  dbo.facture_entete.DO_Coord01,
                  dbo.facture_entete.DO_Coord02,
                  dbo.facture_entete.DO_Coord03,
                  dbo.facture_entete.DO_Coord04,
                  dbo.facture_entete.DO_Date,
                  dbo.facture_entete.DO_Tiers,
                  dbo.facture_entete.prepa,
                  SUM(dbo.facture_ligne.DL_Qte) AS qtt_total_com,
                  Count(dbo.facture_ligne.DO_Piece) AS Nb_ligne

                  FROM
                  dbo.facture_entete
                  INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
                  INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New
                  INNER JOIN dbo.client ON dbo.facture_entete.DO_Tiers = dbo.client.CT_Num
                  WHERE
                                      ".$_SESSION['where']." 
                                      AND dbo.facture_entete.DO_type IN(6,7,23,30)
                                      AND (dbo.facture_entete.DO_Date >= '" . $user_current_date. "' AND dbo.facture_entete.DO_Date <='" . $dateFin. "')
                                      AND dbo.facture_entete.statut = 1 AND dbo.facture_entete.DO_Provenance = 0
                  GROUP BY
                  dbo.facture_ligne.DO_Piece,
                  dbo.facture_ligne.DO_Ref,
                  dbo.facture_entete.statut,
                  dbo.client.CT_Intitule,
                  dbo.client.CT_Adresse,
                  dbo.facture_entete.DO_Coord01,
                  dbo.facture_entete.DO_Coord02,
                  dbo.facture_entete.DO_Coord03,
                  dbo.facture_entete.DO_Coord04,
                  dbo.facture_entete.DO_Date,
                  dbo.facture_entete.DO_Tiers,
                  dbo.facture_entete.prepa
                  ORDER BY
                  dbo.facture_entete.DO_Date DESC";


$result = $dbh->query($tsql);


      if ($result !== FALSE)
      {

      	require_once dirname(__FILE__) . '/./php_classes/PHPExcel.php';

      	$objPHPExcel = new PHPExcel();


      	$xlsxSheet = $objPHPExcel->setActiveSheetIndex(0);
                    $xlsxSheet->setCellValue('A1', 'Num Facture');
                    $xlsxSheet->setCellValue('B1', 'Statut');
                    $xlsxSheet->setCellValue('C1', 'Nb Ligne');
                    $xlsxSheet->setCellValue('D1', 'Client');
                    $xlsxSheet->setCellValue('E1', 'Commentaire');
                    $xlsxSheet->setCellValue('F1','Date de commande');
                    $xlsxSheet->setCellValue('G1', 'Qtt-Total-Cmd');

              $lin = 2;
              $iter = 0;
      	$totalParModesReglements = array();
      	$totalParModesReglementsAcomptes = array();
      	$totalParModesReglementsSansAcomptes = array();
              while ( $row = $result->fetch(PDO::FETCH_ASSOC) )
              {
                $da[] = $row;
              
        		        $xlsxSheet->setCellValue('A'.$lin, $row['DO_Piece']);
                    $xlsxSheet->setCellValue('B'.$lin, 'Fermé');
                    $xlsxSheet->setCellValue('C'.$lin, $row['Nb_ligne']);
                    $xlsxSheet->setCellValue('D'.$lin, $row['DO_Tiers'].' - '.$row['CT_Intitule']);
                    $xlsxSheet->setCellValue('E'.$lin, $row['DO_Coord01'].'|'.$row['DO_Coord02'].'|'.$row['DO_Coord03'].'|'.$row['DO_Coord04']);
                    $xlsxSheet->setCellValue('F'.$lin, $row['DO_Date']);
                    $xlsxSheet->setCellValue('G'.$lin, $row['qtt_total_com']);
                    
        		        next($row);
        	    
        	      ++$lin;
        	  
              }
      $dataStyleArray = array(
          'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
          )
        );



        $headerLin = 1;

        $globalStyleArray = array(
            'font'  => array(
              'size'  => 10,
            )
        );

        $xlsxSheet->getStyle('A1:' . $xlsxSheet->getHighestColumn() . $xlsxSheet->getHighestRow())->applyFromArray($globalStyleArray);

        $xlsxSheet->getStyle('A' . ($headerLin + 1) . ':' . $xlsxSheet->getHighestColumn() . $xlsxSheet->getHighestRow())->applyFromArray($dataStyleArray);

      $xlsxSheet
          ->getStyle( $xlsxSheet->calculateWorksheetDimension() )
          ->getAlignment()
          ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $xlsxSheet
          ->getStyle( $xlsxSheet->calculateWorksheetDimension() )
          ->getAlignment()
          ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $headerStyleArray = array(
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '7aa2e2'),
          ),
          'font'  => array(
              'bold'  => true,
              'color' => array('rgb' => 'FFFFFF'),
              'size'  => 12,
          )
        );


        $xlsxSheet->getStyle('A' . $headerLin . ':' . $xlsxSheet->getHighestColumn() . $headerLin)->applyFromArray($headerStyleArray);

        $xlsxSheet->setAutoFilter('A' . $headerLin . ':' . $xlsxSheet->getHighestColumn() . $xlsxSheet->getHighestRow());

        $dataLin = $headerLin + 1;

        $xlsxSheet->getStyle('A' . $dataLin . ':A' . $xlsxSheet->getHighestRow())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $xlsxSheet->getStyle('B' . $dataLin . ':B' . $xlsxSheet->getHighestRow())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $xlsxSheet->getStyle('C' . $dataLin . ':C' . $xlsxSheet->getHighestRow())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $xlsxSheet->getStyle('D' . $dataLin . ':D' . $xlsxSheet->getHighestRow())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $xlsxSheet->getStyle('E' . $dataLin . ':E' . $xlsxSheet->getHighestRow())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $xlsxSheet->getStyle('F' . $dataLin . ':F' . $xlsxSheet->getHighestRow())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $xlsxSheet->getStyle('G' . $dataLin . ':G' . $xlsxSheet->getHighestRow())->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


      $objPHPExcel->getActiveSheet()->setTitle('Simple');
            //$objPHPExcel->setActiveSheetIndex(0);


      // Redirect output to a client’s web browser (Excel5)
      //header('Content-Type: application/vnd.ms-excel');
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xls"');
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');

      // If you're serving to IE over SSL, then the following may be needed
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header ('Pragma: public'); // HTTP/1.0

      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      $objWriter->save('php://output');
      exit;
}
        
        
   }
   
    if(isset($_GET['rechercher'])){
        $current_date_time = date_create($_GET['date_debut']);
        $user_current_date = date_format($current_date_time, "Y-d-m");
        
       // $d_Fin = date_create($_GET['date_fin']);
        
      //  $notreDate = date_format($d_Fin, "Y-d-m");
        
        $d = date('Y-m-d', strtotime($_GET['date_fin']. ' + 1 days'));
        $t= date_create($d);
        $dateFin = date_format($t, "Y-d-m");
   
   
        if($_SESSION['depot'] == "SEC"){
            $tsql = "SELECT DISTINCT(DO_Piece) FROM (SELECT
                facture_entete.DO_Piece,
                facture_ligne.AR_Ref,
                article.DL,
                facture_ligne.statut AS lineStat,
                facture_entete.statut AS entStat,
                facture_entete.DO_Date
                FROM
                facture_entete
                INNER JOIN facture_ligne ON facture_entete.DO_Piece = facture_ligne.DO_Piece
                INNER JOIN article ON facture_ligne.AR_Ref = article.AR_Ref_New
                WHERE facture_entete.DO_Provenance = 0)
                AS TabENT
                WHERE DO_Date = '" . $user_current_date. "'";
        }else{           
                    
                    $tsql = "SELECT DISTINCT(DO_Piece),DO_Ref,entStat,CT_Intitule,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa FROM (SELECT
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
                    AND dbo.facture_entete.DO_type IN(6,7,23,30)
                    AND (dbo.facture_entete.DO_Date >= '" . $user_current_date. "' AND dbo.facture_entete.DO_Date <='" . $dateFin. "')
                    AND dbo.facture_entete.statut = 1 AND dbo.facture_entete.DO_Provenance = 0)
                    AS tabEnt

                    ORDER BY entDate DESC";
                    
        }


       $getData = $dbh->query($tsql);
         
$da = array();
        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {
        
            $sql =  "SELECT
                     Count(dbo.facture_ligne.DO_Piece) AS Nb_ligne,
                     Sum(dbo.facture_ligne.DL_Qte) AS qtt_total_com

                     FROM
                     dbo.facture_ligne

                     WHERE
                      dbo.facture_ligne.DO_Piece = '" . $row['DO_Piece']. "' ";

          
            $get = $dbh->query($sql);
            
            while ($rowA = $get->fetch(PDO::FETCH_ASSOC)) {
                $da[] = $rowA; 
                //array_push($da,$rowA);
                //var_dump($da);
            }
 
            $data[] = $row;

        }

        //var_dump($data);  
        /*echo "-------------------------";*/
         for($i=0; $i < count($da); $i++){
            $ta[] = [$da[$i],$data[$i]];
           
           }
      
        $transm = json_encode($ta);
        $val =  json_encode($data);
        $articles = json_encode($da);
        
    }else{
        $current_date_time = new DateTime("now");
        $user_current_date = $current_date_time->format("Y-m-d");

        if($_SESSION['depot'] == "SEC"){
            $tsql = "SELECT DISTINCT(DO_Piece) FROM (SELECT
                facture_entete.DO_Piece,
                facture_ligne.AR_Ref,
                article.DL,
                facture_ligne.statut AS lineStat,
                facture_entete.statut AS entStat,
                facture_entete.DO_Date

                FROM
                facture_entete
                INNER JOIN facture_ligne ON facture_entete.DO_Piece = facture_ligne.DO_Piece
                INNER JOIN article ON facture_ligne.AR_Ref = article.AR_Ref_New
                WHERE facture_entete.DO_Provenance = 0)
                AS TabENT
                WHERE DO_Date = '" . $user_current_date. "'";
        }else{

            $tsql = "SELECT DISTINCT(DO_Piece),DO_Ref,entStat,CT_Intitule,CT_Adresse,DO_Coord01,DO_Coord02,DO_Coord03,DO_Coord04,entDate,DO_Tiers,prepa FROM (SELECT
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
                    AND dbo.facture_entete.DO_type IN(6,7,23,30)
                    AND CAST(dbo.facture_entete.DO_Date AS date) = '" . $user_current_date. "' 
                    AND dbo.facture_entete.statut = 1 AND dbo.facture_entete.DO_Provenance = 0)
                    AS tabEnt

                    WHERE
                    CAST(entDate AS date) = '" . $user_current_date. "'
                    ORDER BY entDate DESC";

        }


        $getData = $dbh->query($tsql);
         

        while ($row = $getData->fetch(PDO::FETCH_ASSOC)) {
        
            $sql =  "SELECT
                     Count(dbo.facture_ligne.DO_Piece) AS Nb_ligne,
                     Sum(dbo.facture_ligne.DL_Qte) AS qtt_total_com

                     FROM
                     dbo.facture_ligne

                     WHERE
                      dbo.facture_ligne.DO_Piece = '" . $row['DO_Piece']. "' ";

            
            $get = $dbh->query($sql);
            while ($rowA = $get->fetch(PDO::FETCH_ASSOC)) {
                $d[] = $rowA;
               
            }
 
            $data[] = $row;

        }

        
        /*echo "-------------------------";*/
         for($i=0; $i < count($d); $i++){
            $t[] = [$d[$i],$data[$i]];
           }
       // var_dump($t);
        $transm = json_encode($t);
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
                            <h5>Transmission</h5>
                            <!--<a href="prepaFini.php" class="pull-right btn btn-success livrFini" style="margin-top: -32px;">Livraison fini</a>-->
                        </div>


                        <div class="panel-body">
                            <div class="col-sm-4" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">N&deg; Facture :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control numFact" ng-model="numFacture"/>
                                    </div>
                                </div>
                            </div>
                            <form method="get" action="transm.php" class="form-horizontal" enctype="multipart/form-data">

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Date du :</label>
                                        <div class="col-sm-8">
                                            <input id="datepicker_debut" name="date_debut" value="<?php if(isset($_GET['date_debut'])){ echo $_GET['date_debut']; }else{ echo $current_date_time->format("Y-m-d"); } ?>"/>
                                        </div>
                                    </div>
                                </div>

                                  <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label"> au :</label>
                                                <div class="col-sm-8">
                                                    <input id="datepicker_fin" class="form-control"  name="date_fin" value="<?php if($_GET['date_fin']){ echo $_GET['date_fin']; }else{ echo $current_date_time->format("Y-m-d"); } ?>"/>
                                                </div>
                                            </div>
                                        </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <div class="col-sm-8 col-sm-offset-2">
                                            <button class="btn btn-primary" name="rechercher" type="submit">Rechercher</button>

                                        </div>
                                    </div>
                                </div>
                      <!--<input type="button" value="Export to Excel" name="transmission" class="btn btn-success" style="margin-bottom:10px;margin-top:-20px;margin-left: 45px;"> -->
                      <button class="btn btn-success" name="transmission" type="submit">Export to Excel</button>         
                                
                            </form>

</div>

      
                                <!--Drop down -->
                               <table id="dataTable" name="listeBon" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                <tr style="background: #0d72d8;color: #fff;">
                                    <th data-cell-type="text" >N&deg; Facture</th>
                                    <th>Statut</th>
                                    <th>Nb Ligne</th>
                                    <th>Client</th>
                                    <th>Commentaire</th>
                                    <th style="width: 140px;">Date de commande</th>
                                    <th style="width: 50px;">Qtt-Total-Cmd</th>
                                </tr>
                                </thead>

                                <tbody>

                                <tr ng-repeat="data in transmission | filter:numFacture">
                                    <td data-cell-type="text" data-cell-format-str="">{{ data[1].DO_Piece }}</td>
                                    <td>Ferme</td>
                                    <td>{{ data[0].Nb_ligne }}</td>
                                    <td>{{ data[1].DO_Tiers }} - {{ data[1].CT_Intitule }}</td>
                                    <td>{{ data[1].DO_Coord01 }} | {{ data[1].DO_Coord02 }} | {{ data[1].DO_Coord03 }} | {{ data[1].DO_Coord04 }}</td>
                                    <td>{{ data[1].entDate| toDate | date:'dd/MM/yyyy HH:mm' }}</td>
                                    <td>{{ data[0].qtt_total_com | number}}</td>

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
                $http.get('transm.php').success(function(data){
                    $scope.namesData = <?php echo $val; ?>;
                    // console.log($scope.namesData);
                });

                $http.get('transm.php').success(function(data){
                    $scope.articleData = <?php echo $articles; ?>;
                    // console.log($scope.namesData);
                });
                
                 $http.get('transm.php').success(function(data){
                    $scope.transmission = <?php echo $transm; ?>;
                     //console.log($scope.transmission);
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

        /*function refresh(time)
        {
          setTimeout(function () { window.location.reload(); }, time*1000);
        }
        refresh(120);*/
        
        /* validation formulaire BL */

        function validerForm(){
            console.log("ok");
                  
            var blfacture = $("#blfacture").val();
        
           window.location.href = "facture.php?blfacture="+blfacture;
        }
        
        var tableToExcel = (function() {
            var uri = 'data:application/vnd.ms-excel;base64,'
              , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
              , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
              , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
            return function(table, name) {
              if (!table.nodeType) table = document.getElementById(table)
              var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
              window.location.href = uri + base64(format(template, ctx))
            }
          })();
          
          

    </script>


<?php include('includes/footer.php'); ?>

