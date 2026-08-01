<?php
class Template_exam_competency_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_template_exam_competency";
	}

	// Used
	function get_competency_in($uc_exam = 0){
		$sql  = " SELECT ec . * , c.`sequence` , c.`uc` `uc_competency`, c.`label` ";
		$sql .= " FROM `tech_template_exam_competency` ec ";
		$sql .= " JOIN `tech_competency` c ON c.`uc` = ec.`uc_competency` ";
		$sql .= " WHERE `uc_exam` IN (".$uc_exam.") ";
		$sql .= " ORDER BY `uc_exam`, c.`sequence` ASC ";


		return $this->exec_query($sql);
	}

	function get_competency($uc_function = NULL, $category){
		$sql  = " SELECT c.* , f.`label` `label_function` , l.`label` `label_level` ";
		$sql .= " FROM `tech_competency` c ";
		$sql .= " LEFT JOIN `tech_function` f ON f.`uc` = c.`uc_function` ";
		$sql .= " LEFT JOIN `tech_level` l ON l.`uc` = f.`uc_level` ";

		if ($uc_function != NULL) {
			$sql .= " WHERE c.`uc_function` = '".$uc_function."' ";
		}

		if ($category != NULL) {
			$sql .= " AND c.`category` IN ".$category." ";
		}
		
		$sql .= " ORDER BY `c`.`sequence` ASC ";

		return $this->exec_query($sql);
	}	

	function get_exam_competency($exam_ucs){
		$sql = " SELECT ec.`uc`, ec.`uc_exam`, ec.`uc_competency`, c.`sequence`, c.`label` AS `competency_name` ";
		$sql .= " FROM `".$this->table_name."` ec ";
		$sql .= " LEFT JOIN `tech_competency` c ON ec.`uc_competency` = c.`uc` ";
		$sql .= " WHERE ec.`uc_exam` IN (".$exam_ucs.") ";
		$sql .= " ORDER BY ec.`uc_exam`, c.`sequence` ASC ";
		
		return $this->exec_query($sql);
	}	

	function delete_child_exam_com($table = NULL, $uc = "") {
		$sql = " DELETE FROM `".$table."` WHERE `uc_exam` IN (".$uc.") ";
		
		return $this->exec_query($sql);
	}		
}
?>