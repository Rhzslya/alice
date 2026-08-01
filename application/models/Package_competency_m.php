<?php
Class Package_competency_m extends MY_Model{
	function __construct(){
		parent::__construct();
		$this->table_name = 'tech_package_competency';
	}

	function get_packages($uc, $uc_level) {
		$sql  = " SELECT pc.*, f.`label` `function_label`, c.`label` `competency_label`, c.`category` ";
		$sql .= " FROM `tech_package_competency` pc ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON f.`uc` = pc.`uc_function` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON c.`uc` = pc.`uc_competency` ";
		$sql .= " WHERE pc.`uc_level` = '".$uc_level."' ";
		$sql .= " AND pc.`uc_package` = '".$uc."' ";
		$sql .= " ORDER BY f.`label`, f.`id`, c.`sequence` ASC ";

		return $this->exec_query($sql);
	}

	function get_question_for_level($uc, $level_ucs) {
		$sql  = " SELECT pc.* ";
		$sql .= " FROM `tech_package_competency` pc ";
		$sql .= " WHERE pc.`uc_level` IN (".$level_ucs.") ";
		$sql .= " AND pc.`uc_package` = '".$uc."' ";

		return $this->exec_query($sql);
	}
}
?>