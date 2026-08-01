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

switch ($row->diklat_type) {
	case 1:
		$prapas = "Pra";
		break;

	case 2:
		$prapas = "Pasca";
		break;

	case 3:
		$prapas = "DP";
		break;
	
	default:
		$prapas = "-";
		break;
}

// Isi
if (isset($result)) {
	// UPT
	$objPHPExcel->getActiveSheet()->mergeCells('B4:C4');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'UPT');
	$objPHPExcel->getActiveSheet()->getStyle('B4:D4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D4', $upt->upt_label);


	// Date
	$objPHPExcel->getActiveSheet()->mergeCells('B5:C5');
	$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Subject Title / Date');
	$objPHPExcel->getActiveSheet()->getStyle('B5:B5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D5', $upt->period.' / '.time_format($upt->date_start, 'd M Y').' - '.time_format($upt->date_finish, 'd M Y'));


	// Level
	$objPHPExcel->getActiveSheet()->mergeCells('B6:C6');
	$objPHPExcel->getActiveSheet()->setCellValue('B6', 'Level');
	$objPHPExcel->getActiveSheet()->getStyle('B6:B6')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D6', $level." (".$prapas.") ");


	// Function
	$objPHPExcel->getActiveSheet()->mergeCells('B7:C7');
	$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Function');
	$objPHPExcel->getActiveSheet()->getStyle('B7:B7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D7', $function_name);


	// Competency
	$objPHPExcel->getActiveSheet()->mergeCells('B8:C8');
	$objPHPExcel->getActiveSheet()->setCellValue('B8', 'Competency');
	$objPHPExcel->getActiveSheet()->getStyle('B8:B8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->setCellValue('D8', $competency_name_title);


	// Result of Report
	// No
	$objPHPExcel->getActiveSheet()->setCellValue('B10','No');
	$objPHPExcel->getActiveSheet()->getStyle('B10:B10')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(4);
	$objPHPExcel->getActiveSheet()->getStyle('B10')->applyFromArray($style);

	// Seafarer ID
	$objPHPExcel->getActiveSheet()->setCellValue('C10','Seafarer ID');
	$objPHPExcel->getActiveSheet()->getStyle('C10:C10')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getStyle('C10')->applyFromArray($style);

	// Full Name
	$objPHPExcel->getActiveSheet()->setCellValue('D10','Participant No');
	$objPHPExcel->getActiveSheet()->getStyle('D10:D10')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle('D10')->applyFromArray($style);

	// Date Of Birth
	$objPHPExcel->getActiveSheet()->setCellValue('E10','Full Name');
	$objPHPExcel->getActiveSheet()->getStyle('E10:E10')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getStyle('E10')->applyFromArray($style);

	// Score
	$objPHPExcel->getActiveSheet()->setCellValue('F10','Score');
	$objPHPExcel->getActiveSheet()->getStyle('F10:F10')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getStyle('F10')->applyFromArray($style);

	$baris_data = 11;
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

		if ($res->is_done == NULL) {
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$baris_data, "Unattempt");
		}elseif ($res->is_done == 0){
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$baris_data, "Unfinish");
		}else{
			// $objPHPExcel->getActiveSheet()->setCellValue('F'.$baris_data, $this->encrypt->decode($res->score));
			$score = "";
			if ($setting->value == 2) {
				$score = decryptIt($res->score_normal);
			} elseif ($setting->value == 3) {
				$score = decryptIt($res->score_2);
			} else {
				$score = decryptIt($res->score);
			}
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$baris_data, $score);
		}
		$objPHPExcel->getActiveSheet()->getStyle('F'.$baris_data)->applyFromArray($isi);

		$baris_data++;
		$no++;
	}
	
}

$filename = $level."-".$row->exam_code."(".$prapas.")"."-".$sequence."-".$competency_name.".xls";  //save our workbook as this file name
header('Content-Type: application/vnd.ms-excel'); //mime type
header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
header('Cache-Control: max-age=0'); //no cache
            
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
//force user to download the Excel file without writing it to server's HD
$objWriter->save('php://output');

?>