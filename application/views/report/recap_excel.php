<?php
require_once APPPATH."/third_party/PHPExcel.php"; 
$objPHPExcel = new PHPExcel();

$objPHPExcel->getActiveSheet()->mergeCells('B2:G2');
$objPHPExcel->getActiveSheet()->setCellValue('B2','Recapitulation Report');
$objPHPExcel->getActiveSheet()->getStyle('B2:G2')->applyFromArray(
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

// Isi
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

if ($category == 1) {
	$label_cat = "Pra";
}
else if($category == 2){
	$label_cat = "Pasca";
}
else if($category == 3){
	$label_cat = "DP";
}

// PUKP
$objPHPExcel->getActiveSheet()->setCellValue('B4','Subject Title');
$objPHPExcel->getActiveSheet()->setCellValue('C4',$period);

// UPT
$objPHPExcel->getActiveSheet()->setCellValue('B5','PUKP/UPT');
$objPHPExcel->getActiveSheet()->setCellValue('C5',$pukp_label."/".$upt_label);

// Level
$objPHPExcel->getActiveSheet()->setCellValue('B6','Level');
$objPHPExcel->getActiveSheet()->setCellValue('C6',$level);

// Category
$objPHPExcel->getActiveSheet()->setCellValue('B7','Category');
$objPHPExcel->getActiveSheet()->setCellValue('C7',$label_cat);

$objPHPExcel->getActiveSheet()->getStyle('B4:B7')->getFont()->setBold(true)->setSize(11);

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
	$objPHPExcel->getActiveSheet()->setCellValue('A9','No');
	$objPHPExcel->getActiveSheet()->getStyle('A9:A9')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
	$objPHPExcel->getActiveSheet()->getStyle('A9:A10')->applyFromArray($style);

	//	Seafarer ID
	$objPHPExcel->getActiveSheet()->setCellValue('B9','Seafarer Code');
	$objPHPExcel->getActiveSheet()->getStyle('B9:B9')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getStyle('B9:B10')->applyFromArray($style);

	//	Participant No
	$objPHPExcel->getActiveSheet()->setCellValue('C9','Participant No');
	$objPHPExcel->getActiveSheet()->getStyle('C9:C9')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getStyle('C9:C10')->applyFromArray($style);

	// Full Name
	$objPHPExcel->getActiveSheet()->setCellValue('D9','Full Name');
	$objPHPExcel->getActiveSheet()->getStyle('D9:E9')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getStyle('D9:D10')->applyFromArray($style);

	$col = "E";
	$no  = 1;
	foreach ($comp as $co) {
		$comp_name = html_entity_decode(word_limiter($co->label,5),ENT_QUOTES,'UTF-8');
		$objPHPExcel->getActiveSheet()->setCellValue($col.'9', $comp_name);
		$objPHPExcel->getActiveSheet()->getStyle($col.'9:'.$col.'9')->getFont()->setBold(false)->setSize(9);		
		$objPHPExcel->getActiveSheet()->getStyle($col.'9:'.$col.'9')->getAlignment()->setTextRotation(90);		
		$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(6);
		$objPHPExcel->getActiveSheet()->getStyle($col.'9:'.$col.'10')->applyFromArray($style_com);

		$objPHPExcel->getActiveSheet()->setCellValue($col.'10', $no);

		$col++;
		$no++;
	}

	$baris_data = 11;
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

			if (isset($score[$part['seafarer_code']][$co->uc])) {
				if (($score[$part['seafarer_code']][$co->uc] == "NA") ||
					($score[$part['seafarer_code']][$co->uc] == "UF") ||
					($score[$part['seafarer_code']][$co->uc] == "UA")
					) {
					$the_score = $score[$part['seafarer_code']][$co->uc];
					
				}
				else {
					$the_score = $score[$part['seafarer_code']][$co->uc];
					
				} 
			}
			elseif (isset($status[$part['seafarer_code']][$co->uc])) {
				if ($status[$part['seafarer_code']][$co->uc] == 1) {
					$the_score = "SL";
					
				}
				else {
					$the_score = "BL";
					
				}
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

$filename = "Recapitulation-".$upt_label."-[".$period."][".$level."][".$label_cat."].xls";  //save our workbook as this file name

ob_end_clean();
header( "Content-type: application/vnd.ms-excel" );
header('Content-Disposition: attachment;filename="'.$filename.'"'); 
header("Pragma: no-cache");
header("Expires: 0");
ob_end_clean();
            
//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
//if you want to save it as .XLSX Excel 2007 format
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
//force user to download the Excel file without writing it to server's HD
$objWriter->save('php://output');

?>