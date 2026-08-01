<?php
require_once APPPATH."/third_party/PHPExcel.php"; 
$objPHPExcel = new PHPExcel();

$objPHPExcel->getActiveSheet()->mergeCells('B2:F2');
$objPHPExcel->getActiveSheet()->setCellValue('B2','Score Rekapitulation Report');
$objPHPExcel->getActiveSheet()->getStyle('B2:F2')->applyFromArray(
		array(
			'font'    => array(
				'bold'	=> true,
				'size'	=> 16
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		)
);


/*$test  = "- Elvin";
$test .= "\n";
$test .= "- Immu";*/

if (isset($result)) {
	// Examination Code
	$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Examination Code');
	$objPHPExcel->getActiveSheet()->getStyle('B5:B5')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D5', $result[0]->exam_code);

	// Period
	$objPHPExcel->getActiveSheet()->setCellValue('B6', 'Subject Title');
	$objPHPExcel->getActiveSheet()->getStyle('B6:B6')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D6', $result[0]->period);

	// Date
	$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Date');
	$objPHPExcel->getActiveSheet()->getStyle('B7:B7')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D7', ($result[0]->date != NULL ? time_format($result[0]->date, 'd M Y') : "-"));

	// Session
	$objPHPExcel->getActiveSheet()->setCellValue('B8', 'Session');
	$objPHPExcel->getActiveSheet()->getStyle('B8:B8')->getFont()->setBold(true);
		// $objPHPExcel->getActiveSheet()->setCellValue('D8', (isset($session) ? $session : "-"));
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('D8', (isset($session) ? $session : "-"), PHPExcel_Cell_DataType::TYPE_STRING);

	// Level
	$objPHPExcel->getActiveSheet()->setCellValue('B9', 'Level');
	$objPHPExcel->getActiveSheet()->getStyle('B9:B9')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D9', $result[0]->level_name);

	// Function
	$objPHPExcel->getActiveSheet()->setCellValue('B10', 'Function');
	$objPHPExcel->getActiveSheet()->getStyle('B10:B10')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D10', $result[0]->function_name);

	// Competency
	$objPHPExcel->getActiveSheet()->setCellValue('B11', 'Competency');
	$objPHPExcel->getActiveSheet()->getStyle('B11:B11')->getFont()->setBold(true);
		$baris_com = 11;
		if (isset($res_comp)) {
			foreach ($res_comp as $rc) {
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$baris_com, $rc->sequence.'. '.$rc->competency_name);

				$baris_com++;
			}
		}



	// Result of Report
	$objPHPExcel->getActiveSheet()->setCellValue('B18','No');
	$objPHPExcel->getActiveSheet()->getStyle('B18:B18')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(4);
	$objPHPExcel->getActiveSheet()->setCellValue('C18','Seafarer ID');
	$objPHPExcel->getActiveSheet()->getStyle('C18:C18')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->setCellValue('D18','Full Name');
	$objPHPExcel->getActiveSheet()->getStyle('D18:D18')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
	$objPHPExcel->getActiveSheet()->setCellValue('E18','Date Of Birth');
	$objPHPExcel->getActiveSheet()->getStyle('E18:E18')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->setCellValue('F18','Score');
	$objPHPExcel->getActiveSheet()->getStyle('F18:F18')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9);

	$baris_data = 19;
	
	if (isset($result)){
		$no = 1;
	
		if ($max == 1) {
			for ($i=0; $i < $max; $i++) { 
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$baris_data, $no);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$baris_data, $seafarer_code[$i]);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$baris_data, $full_name[$i]);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$baris_data, $born_place[$i].", ".($born_date[$i] != NULL ? time_format($born_date[$i], 'd-M-Y') : "-"));

				$baris_data++;
				$no++;
			}			
		}
		else{
			for ($i=0; $i < $max; $i++) { 
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$baris_data, $no);

				$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$baris_data, $seafarer_code[$i], PHPExcel_Cell_DataType::TYPE_STRING);

				$objPHPExcel->getActiveSheet()->setCellValue('D'.$baris_data, $full_name[$i]);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$baris_data, $born_place[$i].", ".($born_date[$i] != NULL ? time_format($born_date[$i], 'd-M-Y') : "-"));

				for ($j=0; $j < count($score[$i]); $j++) { 
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$baris_data, $competency_name[$i][$j]);
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$baris_data, $score[$i][$j]);

					$baris_data++;
				}

				$baris_data++;
				$no++;
			}
		}	
	} else {
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$baris_data,'Empty ...');
	}	
}

$filename = "score_recapitulation(".time_format(current_time(), "d-m-Y").").xls"; //save our workbook as this file name
header('Content-Type: application/vnd.ms-excel'); //mime type
header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
header('Cache-Control: max-age=0'); //no cache
            
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
//force user to download the Excel file without writing it to server's HD
$objWriter->save('php://output');
?>