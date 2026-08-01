<?php
require_once APPPATH."/third_party/PHPExcel.php"; 

$objPHPExcel = new PHPExcel();

$objPHPExcel->getActiveSheet()->mergeCells('B2:D2');
$objPHPExcel->getActiveSheet()->setCellValue('B2','Score Recapitulation Report');
$objPHPExcel->getActiveSheet()->getStyle('B2:F2')->applyFromArray(
		array(
			'font'    => array(
				'bold'	=> true,
				'size'	=> 16
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			)
		)
);


switch ($category) {
 	case 1:
 		$cat = "Pra";
 		break;
 	case 2:
 		$cat = "Pasca";
 		break;
 	case 3:
 		$cat = "DP";
 		break;
 	default:
 		$cat = "N/A";
 		break;
 }							

$objPHPExcel->getActiveSheet()->setCellValue('B4','Level');
$objPHPExcel->getActiveSheet()->setCellValue('C4',$level.' ('.$cat.')');

$objPHPExcel->getActiveSheet()->setCellValue('B5','Subject Title');
$format = 'd M Y';
$objPHPExcel->getActiveSheet()->setCellValue('C5',$period.' ('.time_format($start_date,$format).')');

$objPHPExcel->getActiveSheet()->setCellValue('B6','UPT');
$objPHPExcel->getActiveSheet()->setCellValue('C6',$upt_name);

$objPHPExcel->getActiveSheet()->getStyle('B4:B6')->getFont()->setBold(true)->setSize(11);

// Style Table
$style = array(
			'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' 	=> PHPExcel_Style_Alignment::VERTICAL_CENTER
			),

			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),

			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'B5B5B5')
			),

			'font'  => array(
				'color' => array('rgb' => '000000')
			)
		);

$style_com = array(
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
				'color' => array('rgb' => 'B5B5B5')
			),

			'font'  => array(
				'color' => array('rgb' => '000000')
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

if (isset($comp)) {
	
	$this->load->helper('text');

	//	No
	$objPHPExcel->getActiveSheet()->setCellValue('A8','No');
	$objPHPExcel->getActiveSheet()->getStyle('A8:A8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
	$objPHPExcel->getActiveSheet()->getStyle('A8:A9')->applyFromArray($style);

	//	Seafarer ID
	$objPHPExcel->getActiveSheet()->setCellValue('B8','Seafarer ID');
	$objPHPExcel->getActiveSheet()->getStyle('B8:B8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getStyle('B8:B9')->applyFromArray($style);

	// Participant No
	$objPHPExcel->getActiveSheet()->setCellValue('C8','Participant No');
	$objPHPExcel->getActiveSheet()->getStyle('C8:C8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle('C8:C9')->applyFromArray($style);

	// Full Name
	$objPHPExcel->getActiveSheet()->setCellValue('D8','Full Name');
	$objPHPExcel->getActiveSheet()->getStyle('D8:D8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getStyle('D8:D9')->applyFromArray($style);

	$col = "E";
	foreach ($comp as $co) {
		$comp_name = html_entity_decode(word_limiter($co['label'],5),ENT_QUOTES,'UTF-8');
		$objPHPExcel->getActiveSheet()->setCellValue($col.'8', $comp_name);
		$objPHPExcel->getActiveSheet()->getStyle($col.'8:'.$col.'8')->getFont()->setBold(false)->setSize(9);		
		$objPHPExcel->getActiveSheet()->getStyle($col.'8:'.$col.'8')->getAlignment()->setTextRotation(90);		
		$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(6);
		$objPHPExcel->getActiveSheet()->getStyle($col.'8:'.$col.'9')->applyFromArray($style_com);

		$objPHPExcel->getActiveSheet()->setCellValue($col.'9', $co['sequence']);

		$col++;
	}

	$baris_data = 10;
	$no = 1;

	foreach ($part as $part) {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$baris_data, $no);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$baris_data)->applyFromArray($isi);

		$objPHPExcel->getActiveSheet()->setCellValue('B'.$baris_data, $part['seafarer_code']);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$baris_data)->applyFromArray($isi);

		$objPHPExcel->getActiveSheet()->setCellValue('C'.$baris_data, $part['participant_no']);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$baris_data)->applyFromArray($isi);

		$objPHPExcel->getActiveSheet()->setCellValue('D'.$baris_data, $part['full_name']);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$baris_data)->applyFromArray($isi);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$baris_data)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$col = "E";
		foreach ($comp as $co) {
			if (isset($score[$part['seafarer_code']][$co['uc_competency']])) {
				$the_score = $score[$part['seafarer_code']][$co['uc_competency']];
			}
			else {
				$the_score = "-";
			}
			$objPHPExcel->getActiveSheet()->setCellValue($col.$baris_data, $the_score);
			$objPHPExcel->getActiveSheet()->getStyle($col.$baris_data)->applyFromArray($isi);

			$col++;
		}

		$baris_data++;
		$no++;
	}

	
}


//$filename ="test.xls";  //save our workbook as this file name
$filename = $upt_name."-".$period."(".time_format($start_date,'dMy').")-".$level."(".$cat.").xls";

header('Content-Type: application/vnd.ms-excel'); //mime type
header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
header('Cache-Control: max-age=0'); //no cache
            
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
//force user to download the Excel file without writing it to server's HD
$objWriter->save('php://output');


?>