<?php
Class Exam_package_m extends MY_Model{
	function __construct(){
		parent::__construct();

		$this->table_name = 'tech_exam_package';
	}

	function get_package_in($uc_exam = 0){
		$sql  = " SELECT * ";
		$sql .= " FROM `tech_exam_package` ";
		$sql .= " WHERE `uc_exam` IN (".$uc_exam.") ";
		$sql .= " ORDER BY `uc_exam` ";

		return $this->exec_query($sql);
	}

	function get_competency_package($uc_exam = 0){

		/*
		SELECT ec. * , c.`label` , e.`exam_code`, ecp.`uc_package` , p.`code_package` 

		FROM `tech_exam_competency` ec 
		JOIN `tech_competency` c ON ec.`uc_competency` = c.`uc` 
		JOIN `tech_examination` e ON e.`uc` = ec.`uc_exam` 
		JOIN `tech_exam_competency_package` ecp ON ecp.`uc_competency` = ec.`uc_competency` 
		JOIN `tech_package` p ON p.`uc` = ecp.`uc_package` 

		WHERE ec.`uc_exam` = '6549845494' 

		ORDER BY c.`sequence` ASC 
		*/

		/*$sql  = " SELECT ec. * , c.`sequence`, c.`label` , e.`exam_code` , p.`uc` AS `uc_package` , p.`code_package`";
		$sql .= " FROM `tech_exam_competency` ec ";
		$sql .= " JOIN `tech_competency` c ON ec.`uc_competency` = c.`uc` ";
		$sql .= " JOIN `tech_examination` e ON e.`uc` = ec.`uc_exam` ";
		$sql .= " JOIN `tech_package` p ON p.`uc_competency` = ec.`uc_competency` ";
		$sql .= " WHERE ec.`uc_exam` = '".$uc_exam."' ";
		$sql .= " GROUP BY p.`uc` ";
		$sql .= " ORDER BY c.`sequence` ASC , p.`code_package` ASC ";*/

		// Adding From KS
		$sql  = " SELECT ec. * , c.`sequence` , c.`label` , e.`exam_code` , p.`uc` AS `uc_package` , p.`code_package` ";
		$sql .= " FROM `tech_examination` e ";
		$sql .= " LEFT JOIN `tech_exam_competency` ec ON ec.`uc_exam` = e.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ON ec.`uc_competency` = c.`uc` ";
		$sql .= " LEFT JOIN `tech_package` p ON p.`uc_competency` = c.`uc` ";
		$sql .= " WHERE e.`uc` = '".$uc_exam."' ";
		$sql .= " GROUP BY c.`uc` , p.`uc` ";
		$sql .= " ORDER BY c.`sequence` ASC , p.`code_package` ASC ";

		// echo $sql;
		
		return $this->exec_query($sql);
	}

	function get_detail($uc_exam_pack){
		$sql  = " SELECT ecp.*, ep.`package_code`, p.`code_package`, c.`sequence`, c.`label`, e.`exam_code`  ";
		$sql .= " FROM `tech_exam_competency_package` ecp ";
		$sql .= " JOIN `tech_exam_package` ep ";
		$sql .= " ON ep.`uc` = ecp.`uc_exam_package` ";
		$sql .= " JOIN `tech_package` p ";
		$sql .= " ON p.`uc` = ecp.`uc_package` ";
		$sql .= " JOIN `tech_competency` c ";
		$sql .= " ON  c.`uc` = ecp.`uc_competency` ";
		$sql .= " JOIN `tech_examination` e ";
		$sql .= " ON e.`uc` = ep.`uc_exam` ";
		$sql .= " WHERE ecp.`uc_exam_package` = '".$uc_exam_pack."' ";
		$sql .= " ORDER BY c.`sequence` ASC ";

		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_exam_package_temp');
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_exam_package_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_exam_package`) ";
		
		return $this->exec_query($sql);
	}

	//backup per day
	function get_exam_package($exam_ucs){
		$sql = " SELECT * FROM `tech_exam_package` WHERE `uc_exam` IN (".$exam_ucs.") " ;

		return $this->exec_query($sql);
	}			

}