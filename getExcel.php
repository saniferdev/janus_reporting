<?php
session_start();


$refreshIntvInMin = 5;

/*$sqlServerHost = '192.168.120.210';
$sqlServerDatabase = 'APPSAN';
$sqlServerUser = 'dev';
$sqlServerPassword = 'dev';*/
$sqlServerHost = '192.168.120.171';
$sqlServerDatabase = 'APPSAN_V2';
$sqlServerUser = 'appli_janus';
$sqlServerPassword = 'janus';



    $dbh = new PDO("sqlsrv:Server=$sqlServerHost;Database=$sqlServerDatabase", $sqlServerUser,$sqlServerPassword);



         $current_date_time = date_create($_POST['date_debut']);

//var_dump($current_date_time);

        $user_current_date = date_format($current_date_time, "Y-d-m");

        
        $d = date('Y-m-d', strtotime($_POST['date_fin']. ' + 1 days'));
        $t= date_create($d);
        $dateFin = date_format($t, "Y-d-m");
 
        
     $refreshIntvInMin = 5;


/*$tsql = "
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
                                      AND (dbo.facture_entete.statut = 1 OR dbo.facture_entete.statut = 4) AND dbo.facture_entete.DO_Provenance = 0
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
                  dbo.facture_entete.DO_Date DESC";*/

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
                    AND (dbo.facture_entete.statut = 1 OR dbo.facture_entete.statut = 4) AND dbo.facture_entete.DO_Provenance IN(0,3))
                    AS tabEnt

                    ORDER BY entDate DESC";


$result = $dbh->query($tsql);

//var_dump($result); die();

if ($result !== FALSE)
{

	require_once dirname(__FILE__) . '/./php_classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	/*$magasinName = str_replace(' ', '', 'Transmission');

	$etatTitle = "Stock S1 au " . date('d.m.Y');
        $filename = "STOCK_ARTICLES_S1__" . date('Y-m-d_h:i:s') . ".xlsx";

	$objPHPExcel->getProperties()->setCreator("SANIFER - Etats informatisés")
                                                      ->setLastModifiedBy("SANIFER - Etats informatisés")
                                                      ->setTitle($etatTitle)
                                                      ->setSubject($etatTitle)
                                                      ->setDescription($etatTitle);*/

	$xlsxSheet = $objPHPExcel->setActiveSheetIndex(0);
              $xlsxSheet->setCellValue('A1', 'N° Facture');
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
            $sql =  "SELECT
                     Count(dbo.facture_ligne.DO_Piece) AS Nb_ligne,
                     Sum(dbo.facture_ligne.DL_Qte) AS qtt_total_com

                     FROM
                     dbo.facture_ligne

                     WHERE
                      dbo.facture_ligne.DO_Piece = '" . $row['DO_Piece']. "' ";

          
            $get = $dbh->query($sql);
            
            while ($rowA = $get->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $rowA; 
                //array_push($da,$rowA);
                //var_dump($da);

            }

          $da[] = $row;
     //  var_dump($data); die();
/*  		        $xlsxSheet->setCellValue('A'.$lin, $row['DO_Piece']);
              $xlsxSheet->setCellValue('B'.$lin, 'Fermé');
              $xlsxSheet->setCellValue('C'.$lin, $data[0]['Nb_ligne']);
              $xlsxSheet->setCellValue('D'.$lin, $row['DO_Tiers'].' - '.$row['CT_Intitule']);
              $xlsxSheet->setCellValue('E'.$lin, $row['DO_Coord01'].'|'.$row['DO_Coord02'].'|'.$row['DO_Coord03'].'|'.$row['DO_Coord04']);
              $xlsxSheet->setCellValue('F'.$lin, $row['entDate']);
              $xlsxSheet->setCellValue('G'.$lin, $data[0]['qtt_total_com']);
              
  		        next($row);
  	    
  	      ++$lin;
  	  */
        }

  for($i=0; $i < count($da); $i++){
            $ta[] = [$da[$i],$data[$i]];
// var_dump($ta[0][0]);die(); 
            $xlsxSheet->setCellValue('A'.$lin, $da[$i]['DO_Piece']);
              $xlsxSheet->setCellValue('B'.$lin, 'Fermé');
              $xlsxSheet->setCellValue('C'.$lin, $data[$i]['Nb_ligne']);
              $xlsxSheet->setCellValue('D'.$lin, $da[$i]['DO_Tiers'].' - '.$da[$i]['CT_Intitule']);
              $xlsxSheet->setCellValue('E'.$lin, $da[$i]['DO_Coord01'].'|'.$da[$i]['DO_Coord02'].'|'.$da[$i]['DO_Coord03'].'|'.$da[$i]['DO_Coord04']);
              $xlsxSheet->setCellValue('F'.$lin, $da[$i]['entDate']);
              $xlsxSheet->setCellValue('G'.$lin, $data[$i]['qtt_total_com']);
              
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
/*var_dump($colWidth);
die();*/

$objPHPExcel->getActiveSheet()->setTitle('Simple');
      //$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
//header('Content-Type: application/vnd.ms-excel');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename="Transmission.xls"');
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


?>
