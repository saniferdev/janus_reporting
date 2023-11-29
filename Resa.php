<?php
session_start();

/*ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);*/

include('includes/config.php');

date_default_timezone_set('Indian/Antananarivo');
setlocale(LC_ALL, "fr_FR.utf8");

require_once dirname(__FILE__) . '/php_classes/PHPExcel.php';

if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else{
	if($_REQUEST['g']==1){
		$dl     = " AND dbo.article.DL = '".$_SESSION['depot']."'";
        if($_SESSION['site'] == "LIVRAISON" || $_SESSION['site'] == "LOGISTIQUE"){
            $dl = "";
        }
	    $tsql = "
	            SELECT
                    CONVERT(varchar,dbo.facture_ligne.[DO_Date],3) AS DATES
                   ,dbo.facture_ligne.[DO_Piece] AS NUM
                   ,dbo.facture_ligne.[CT_Num] AS CLI                 
                   ,dbo.facture_entete.DO_Coord01 AS CLI_DESS
                   ,dbo.facture_entete.DO_Coord02 + ' | ' + dbo.facture_entete.DO_Coord03 + ' | ' + dbo.facture_entete.DO_Coord04 AS ADRR
                   ,dbo.facture_ligne.[DL_Ligne] AS LIGNE
                   ,dbo.facture_ligne.[AR_Ref] AS REF
                   ,dbo.facture_ligne.[DL_Design] AS DESS
                   ,dbo.facture_ligne.[DL_Qte] AS QTE
                   ,dbo.facture_ligne.statut AS STAT
                   ,dbo.facture_ligne.statut_resa AS STAT_R
                   ,dbo.facture_ligne.statut_resa_prepa AS STAT_R_P

                FROM
                    dbo.facture_entete
                    INNER JOIN dbo.facture_ligne ON dbo.facture_entete.DO_Piece = dbo.facture_ligne.DO_Piece
                    INNER JOIN dbo.article ON dbo.facture_ligne.AR_Ref = dbo.article.AR_Ref_New

                WHERE
                    dbo.facture_entete.DO_type = 111
                    AND ( dbo.facture_ligne.statut_resa = 0 OR dbo.facture_ligne.statut_resa IS NULL OR dbo.facture_ligne.statut_resa = 1)
                    ".$dl."

                ORDER BY     
                    dbo.facture_ligne.[DO_Date] DESC";

	        $getData = $dbh->query($tsql);
	        $objPHPExcel    = new PHPExcel();
	        $etatTitle      = "Reservation Clients";

	        $titre_         = 'Reservation Clients:   du '.date('d/m/Y');
	        

	        
	        $colWidth = 'K';    
	        $objPHPExcel->getProperties()->setCreator("SANIFER - Etats informatisés")
	                    ->setLastModifiedBy("SANIFER - Etats informatisés")
	                    ->setTitle($etatTitle)
	                    ->setSubject($etatTitle)
	                    ->setDescription($etatTitle);

	        $xlsxSheet      = $objPHPExcel->setActiveSheetIndex(0); 
	        $lin = 1;
	    while ($val = $getData->fetch(PDO::FETCH_ASSOC)) {          	        
        	$statut = ($val['STAT'] == 0) ? 'Alloué' : 'Désalloué' ;
            if($val['STAT_R'] == 0){
                $stat = 'Non préparé';
            }
            else if($val['STAT_R'] == 1){
                $stat = 'Préparé';
            }
            else{
                $stat = 'Non préparé';
            }
             
	        if ($lin == 1) {
	                    $xlsxSheet->setCellValue("A1", "Date");
	                    $xlsxSheet->setCellValue("B1", "N° Pièce");
	                    $xlsxSheet->setCellValue("C1", "Client");
	                    $xlsxSheet->setCellValue("D1", "Intitulé");
	                    $xlsxSheet->setCellValue("E1", "Adresse");
	                    $xlsxSheet->setCellValue("F1", "N° Article");
	                    $xlsxSheet->setCellValue("G1", "Désignation");
	                    $xlsxSheet->setCellValue("H1", "Qte");
	                    $xlsxSheet->setCellValue("I1", "Statut");
	                    $xlsxSheet->setCellValue("J1", "Action");
	               $lin++;
	                    
	        }
	        for ($col = 'A'; $col < $colWidth; ++$col) {
	                        $xlsxSheet->setCellValue("A".$lin, $val["DATES"]);
		                    $xlsxSheet->setCellValue("B".$lin, $val["NUM"]);
		                    $xlsxSheet->setCellValue("C".$lin, $val["CLI"]);
		                    $xlsxSheet->setCellValue("D".$lin, $val["CLI_DESS"]);
		                    $xlsxSheet->setCellValue("E".$lin, $val["ADRR"]);
		                    $xlsxSheet->setCellValue("F".$lin, $val["REF"]);
		                    $xlsxSheet->setCellValue("G".$lin, $val["DESS"]);
		                    $xlsxSheet->setCellValue("H".$lin, $val["QTE"]);
		                    $xlsxSheet->setCellValue("I".$lin, $statut);
		                    $xlsxSheet->setCellValue("J".$lin, $stat);
	                    }

	         $lin++;     
	        
	    }
	        

					 
	    			$globalStyleArray = array(
					    'borders' => array(
					      'allborders' => array(
					        'style' => PHPExcel_Style_Border::BORDER_THIN,
					    ),
					    'font'  => array(
					        'size'  => 10,
					    )

					    )
					  );

					  $xlsxSheet->getStyle('A1:' . $xlsxSheet->getHighestColumn() . $xlsxSheet->getHighestRow())->applyFromArray($globalStyleArray);

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

					  $xlsxSheet->getStyle('A1:' . $xlsxSheet->getHighestColumn() . '1')->applyFromArray($headerStyleArray);

					  $numberFormat = '###\ ###\ ###\ ###\ ##0.00';

	                $xlsxSheet->getStyle('A2:A' . $lin)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	                $xlsxSheet->getStyle('B2:B' . $lin)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	                $xlsxSheet->getStyle('C2:C' . $lin)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	                $xlsxSheet->getStyle('D2:D' . $lin)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	                $xlsxSheet->getStyle('E2:E' . $lin)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	                $xlsxSheet->getStyle('F2:F' . $lin)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	                $xlsxSheet->getStyle('G2:G' . $lin)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	                $xlsxSheet->getStyle('H2:H' . $lin)->getNumberFormat()->setFormatCode($numberFormat);
	                $xlsxSheet->getStyle('I2:I' . $lin)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	                $xlsxSheet->getStyle('J2:J' . $lin)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


	              
	                $sheetName = 'RESA_'.date('d-m-Y');
	                
	                $xlsxSheet->setTitle($sheetName);

	                $objPHPExcel->setActiveSheetIndex(0);

	               

	                $excelFileName = "RESA".'_'.date('d-m-Y').".xlsx";

	                for($col = 'A'; $col < $colWidth; ++$col)
				    $xlsxSheet->getColumnDimension($col)->setAutoSize(true);


				  $xlsxSheet->freezePane('A2');

				  $objPHPExcel->setActiveSheetIndex(0);

				  $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
				  $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
				  $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);
				  $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				  $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);

				  $objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter($etatTitle . ' - Page &P / &N');
				  $objPHPExcel->getActiveSheet()->getHeaderFooter()->setEvenFooter($etatTitle . ' - Page &P / &N');

	            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	              header('Content-Disposition: attachment;filename="' . $excelFileName . '"');
	              header('Cache-Control: max-age=0');
	              header('Cache-Control: max-age=1');

	              header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
	              header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
	              header ('Cache-Control: cache, must-revalidate'); 
	              header ('Pragma: public'); // HTTP/1.0

	              $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	              ob_end_clean();
	              $objWriter->save('php://output');
	              exit;
	    }
}


?>