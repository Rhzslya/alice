<?php
require_once APPPATH."/third_party/PHPExcel.php"; 
$objPHPExcel = new PHPExcel();

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5.2);
$objPHPExcel->getActiveSheet()->mergeCells('B2:F2');
$objPHPExcel->getActiveSheet()->setCellValue('B2','Score Recapitulation Report');
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

// Style Table
$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),

			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),

			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '363636')
			),

			'font'  => array(
				'color' => array('rgb' => 'ffffff')
			)
		);

$isi = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

// Isi
if (isset($result)) {
	// Level
	$objPHPExcel->getActiveSheet()->mergeCells('B4:C4');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'Level');
	$objPHPExcel->getActiveSheet()->getStyle('B4:B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D4', $level);


	// Function
	$objPHPExcel->getActiveSheet()->mergeCells('B5:C5');
	$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Function');
	$objPHPExcel->getActiveSheet()->getStyle('B5:B5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D5', $function_name);


	// Competency
	$objPHPExcel->getActiveSheet()->mergeCells('B6:C6');
	$objPHPExcel->getActiveSheet()->setCellValue('B6', 'Competency');
	$objPHPExcel->getActiveSheet()->getStyle('B6:B6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D6', $competency_name);


	// Result of Report
	// No
	$objPHPExcel->getActiveSheet()->setCellValue('B8','No');
	$objPHPExcel->getActiveSheet()->getStyle('B8:B8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(4);
	$objPHPExcel->getActiveSheet()->getStyle('B8')->applyFromArray($style);

	// Seafarer ID
	$objPHPExcel->getActiveSheet()->setCellValue('C8','Seafarer ID');
	$objPHPExcel->getActiveSheet()->getStyle('C8:C8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($style);

	// Full Name
	$objPHPExcel->getActiveSheet()->setCellValue('D8','Participant No');
	$objPHPExcel->getActiveSheet()->getStyle('D8:D8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle('D8')->applyFromArray($style);

	// Date Of Birth
	$objPHPExcel->getActiveSheet()->setCellValue('E8','Full Name');
	$objPHPExcel->getActiveSheet()->getStyle('E8:E8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getStyle('E8')->applyFromArray($style);

	// Score
	$objPHPExcel->getActiveSheet()->setCellValue('F8','Score');
	$objPHPExcel->getActiveSheet()->getStyle('F8:F8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getStyle('F8')->applyFromArray($style);

	$baris_data = 9;
	$no = 1;

	foreach ($result as $res) {
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$baris_data, $no);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$baris_data)->applyFromArray($isi);

		$objPHPExcel->getActiveSheet()->setCellValue('C'.$baris_data, $res->seafarer_code);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$baris_data)->applyFromArray($isi);

		$objPHPExcel->getActiveSheet()->setCellValue('D'.$baris_data, $res->participant_no);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$baris_data)->applyFromArray($isi);

		$objPHPExcel->getActiveSheet()->setCellValue('E'.$baris_data, $res->full_name);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$baris_data)->applyFromArray(
			array(
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			)
		);

		// $objPHPExcel->getActiveSheet()->setCellValue('F'.$baris_data, $this->encrypt->decode($res->score));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$baris_data, $res->score);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$baris_data)->applyFromArray($isi);

		$baris_data++;
		$no++;
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