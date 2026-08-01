<?php
class Exam_competency_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_exam_competency";
	}

	function get_competency($uc_exam = 0) {
		$sql = " SELECT ec.`uc`, ec.`uc_competency`, c.`sequence`, c.`label` AS `competency_name` ";
		$sql .= " FROM `".$this->table_name."` ec ";
		$sql .= " LEFT JOIN `tech_competency` c ON ec.`uc_competency` = c.`uc` ";
		$sql .= " WHERE ec.`uc_exam` = '".$uc_exam."' ";

		return $this->exec_query($sql);
	}

	//backup per day
	function get_exam_competency($exam_ucs){
		//echo $exam_ucs;

		$sql = " SELECT ec.`uc`, ec.`uc_exam`, ec.`uc_competency`, c.`sequence`, c.`label` AS `competency_name` ";
		$sql .= " FROM `".$this->table_name."` ec ";
		$sql .= " LEFT JOIN `tech_competency` c ON ec.`uc_competency` = c.`uc` ";
		$sql .= " WHERE ec.`uc_exam` IN (".$exam_ucs.") ";
		$sql .= " ORDER BY ec.`uc_exam`, c.`sequence` ASC ";
		
		//echo "<br />".$sql;
		return $this->exec_query($sql);
	}

	function get_package_in($uc_exam = 0){
		$sql  = " SELECT ec.*, c.`label` AS `competency_name`, c.`sequence` ";
		$sql .= " FROM `".$this->table_name."` ec ";
		$sql .= " LEFT JOIN `tech_competency` c ON ec.`uc_competency` = c.`uc` ";
		$sql .= " WHERE ec.`uc_exam` IN (".$uc_exam.") ";
		$sql .= " ORDER BY ec.`uc_exam`, c.`sequence` ASC ";

		return $this->exec_query($sql);
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_exam_competency_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_exam_competency`) ";
		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_exam_competency_temp');
	}

	function get_competency_of_period($uc_period, $uc_level, $category){
		$sql  = " SELECT ec.`uc_exam` , ec.`uc_competency` , c.`sequence` , c.`label` ";
		$sql .= " FROM `tech_exam_competency` ec ";
		$sql .= " LEFT JOIN `tech_competency` c ON c.`uc` = ec.`uc_competency` ";
		$sql .= " WHERE ec.`uc_exam` ";
		$sql .= " IN ( ";
			$sql .= " SELECT `uc` ";
			$sql .= " FROM `tech_examination` ";
			$sql .= " WHERE `uc_session` ";
			$sql .= " IN ( ";
				$sql .= " SELECT `uc`";
				$sql .= " FROM `tech_session` ";
				$sql .= " WHERE `uc_day` ";
				$sql .= " IN ( ";
					$sql .= " SELECT `uc` ";
					$sql .= " FROM `tech_day` ";
					$sql .= " WHERE `uc_period` = '".$uc_period."' ";
					$sql .= " ) ";
					$sql .= " AND `diklat_type` = '".$category."' ";
			$sql .= " ) ";
			$sql .= " AND `uc_level` = '".$uc_level."' ";
		$sql .= " ) ";
		$sql .= " ORDER BY c.`sequence` ASC ";

		// $sql = "SELECT ec.`uc_exam` , ec.`uc_competency` , c.`sequence` , c.`label`
		// 		FROM `tech_exam_competency` ec
		// 		LEFT JOIN `tech_competency` c ON c.`uc` = ec.`uc_competency`
		// 		WHERE ec.`uc_exam`
		// 		IN (

		// 			SELECT `uc`
		// 			FROM `tech_examination`
		// 			WHERE `uc_session`
		// 			IN (

		// 				SELECT `uc`
		// 				FROM `tech_session`
		// 				WHERE `uc_day`
		// 				IN (

		// 					SELECT `uc`
		// 					FROM `tech_day`
		// 					WHERE `uc_period` = '798-67415-52-53'
		// 				)
		// 				AND `pra_pasca` = '3'
		// 			)
		// 			AND `uc_level` = '15-08101-68'
		// 		)

		// 		ORDER BY c.`sequence` ASC";
		

		return $this->exec_query($sql);
	}

	function delete_exam_competency($uc_exam_package){
		$sql = " DELETE FROM `tech_exam_competency` WHERE `uc` IN (".$uc_exam_package.") ";

		return $this->exec_query($sql);
	}	

	function get_exams($comp_ucs, $uc_period) {
		$sql = " SELECT ec.*, e.`uc_period` 
					FROM `tech_exam_competency` ec 
					LEFT JOIN `tech_examination` e ON e.`uc` = ec.`uc_exam` 
					WHERE `uc_competency` IN 
						(".$comp_ucs.") 
					AND e.`uc_period` = '".$uc_period."' ";

					//echo $sql;

		return $this->exec_query($sql);

	}
}
?>