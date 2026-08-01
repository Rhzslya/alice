<?php
class Participant_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_participant";
	}

	function get_filtered_info($seafarer_code = NULL) {
		$sql  = " SELECT p.*, l.`label` AS `level_name`, f.`label` AS `function_name` ";
		$sql .= " , c.`label` AS `competency_name` ";
		$sql .= " FROM `tech_participant` p ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON p.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON p.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON p.`uc_competency` = c.`uc` ";
		if ($seafarer_code != NULL) {
			$sql .= " WHERE p.`seafarer_code` = '".$seafarer_code."' ";
		}
		$sql .= " ORDER BY p.`id` ASC ";

		return $this->exec_query($sql);
	}

	function get_all_info($uc_level = NULL) {
		$sql  = " SELECT p.*, l.`label` AS `level_name`, f.`label` AS `function_name` ";
		$sql .= " , c.`label` AS `competency_name` ";
		$sql .= " FROM `tech_participant` p ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON p.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON p.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON p.`uc_competency` = c.`uc` ";
		if ($uc_level != NULL) {
			$sql .= " WHERE p.`uc_level` = '".$uc_level."' ";
		}
		$sql .= " GROUP BY p.`seafarer_code` ORDER BY p.`seafarer_code` ASC ";

		return $this->exec_query($sql);
	}

	function get_info_search($name = NULL) {
		$sql  = " SELECT p.*, l.`label` AS `level_name`, f.`label` AS `function_name` ";
		$sql .= " , c.`label` AS `competency_name` ";
		$sql .= " FROM `tech_participant` p ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON p.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON p.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON p.`uc_competency` = c.`uc` ";
		if ($name != NULL) {
			$sql .= " WHERE p.`full_name` LIKE '%".$name."%' ";
			$sql .= " OR p.`seafarer_code` LIKE '%".$name."%' ";
		}
		$sql .= " GROUP BY p.`seafarer_code` ORDER BY p.`seafarer_code` ASC ";

		return $this->exec_query($sql);
	}

	function get_info_search_exam($uc_exam = NULL, $name = NULL) {
		$sql  = " SELECT p.*, ea.`uc` AS `ea_uc`, eac.`score_normal` AS `score_competency`, eac.`uc` AS `uc_exam_attempt_competency` ";
		$sql .= " FROM `tech_examination` e ";
		$sql .= " LEFT JOIN `tech_participant` p ";
		$sql .= " ON p.`uc_exam` = e.`uc` ";
		$sql .= " LEFT JOIN `tech_exam_attempt` ea ";
		$sql .= " ON ea.`uc_exam` = e.`uc` ";
		$sql .= " AND p.`seafarer_code` = ea.`seafarer_code` ";
		$sql .= " LEFT JOIN `tech_exam_attempt_competency` eac ";
		$sql .= " ON eac.`uc_exam_attempt` = ea.`uc` ";
		if ($name != NULL) {
			$sql .= " WHERE e.`uc` = '".$uc_exam."' ";
			$sql .= " AND (p.`full_name` LIKE '%".$name."%' OR p.`seafarer_code` LIKE '%".$name."%') ";

			/*
			$sql .= " AND CONCAT(p.`full_name`, p.`seafarer_code`) LIKE '%".$name."%' ";
			$sql .= " AND p.`full_name` LIKE '%".$name."%' ";
			$sql .= " OR e.`uc` = '".$uc_exam."' ";
			$sql .= " AND p.`seafarer_code` LIKE '%".$name."%' ";*/
		}

		// echo $sql;	

		return $this->exec_query($sql);
	}

	function get_all_report_student($seafarer_code = NULL) {
		$sql  = " SELECT p.`seafarer_code`, f.`uc_level`, f.`label` AS `function_name`, c.`uc_function` ";
		$sql .= " , c.`label` AS `competency_name`, p.`uc_competency`, MAX(ea.`score`) AS `max_score` ";
		$sql .= " , e.`exam_code`, ea.`score`, ea.`uc` AS 'uc_attempt', d.`date` AS `time_exam`, per.`period` ";
		$sql .= " FROM `tech_participant` p ";
		$sql .= " LEFT JOIN `tech_examination` e ";
		$sql .= " ON p.`uc_exam` = e.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON e.`uc_competency` = c.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON e.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_session` s ";
		$sql .= " ON e.`uc_session` = s.`uc` ";
		$sql .= " LEFT JOIN `tech_day` d ";
		$sql .= " ON s.`uc_day` = d.`uc` ";
		$sql .= " LEFT JOIN `tech_period` per ";
		$sql .= " ON d.`uc_period` = per.`uc` ";
		$sql .= " LEFT JOIN `tech_exam_attempt` ea ";
		$sql .= " ON p.`seafarer_code` = ea.`seafarer_code` ";
		$sql .= " AND ea.`uc_exam` = p.`uc_exam` ";
		if ($seafarer_code != NULL) {
			$sql .= " WHERE p.`seafarer_code` = '".$seafarer_code."' ";
		}
		$sql .= " GROUP BY p.`uc_exam` ORDER BY f.`label` ASC, c.`sequence` ASC, e.`exam_code` ";

		return $this->exec_query($sql);
	}

	function get_all_score($uc_period, $uc_exam, $uc_competency){
		$sql  = " SELECT DISTINCT p.`seafarer_code`, p.`participant_no`, p.`full_name` "; 
		$sql .= " , ea.`uc`, ea.`is_done`, eac.`uc` AS `eac_uc`, eac.`score`, eac.`score_2`, eac.`score_normal`, e.`exam_code` ";
		$sql .= " FROM `tech_participant` p ";
		$sql .= " LEFT JOIN `tech_exam_attempt` ea ";
		$sql .= " ON ea.`seafarer_code` = p.`seafarer_code` ";
		$sql .= " AND ea.`uc_exam` = '".$uc_exam."' ";
		$sql .= " LEFT JOIN `tech_exam_attempt_competency` eac ";
		$sql .= " ON eac.`uc_exam_attempt` = ea.`uc` ";
		$sql .= " AND eac.`uc_competency` = '".$uc_competency."' ";
		$sql .= " LEFT JOIN `tech_examination` e ON ea.`uc_exam` = e.`uc` " ;

		$sql .= " WHERE p.`uc_period` = '".$uc_period."' "; 
		//$sql .= " AND p.`uc_exam` = '".$uc_exam."' ";

		$sql .= " ORDER BY p.`full_name` ASC ";
		//echo $sql;
		return $this->exec_query($sql);
	}

	function temp_not_in_real(){
		$sql  = " SELECT pt.*, p.`uc_upt`, u.`uc_pukp` ";
		$sql .= " FROM `tech_participant_temp` pt ";
		$sql .= " LEFT JOIN `tech_period` p ON pt.`uc_period` = p.`uc` ";
		$sql .= " LEFT JOIN `tech_upt` u ON p.`uc_upt` = u.`uc` ";
		$sql .= " WHERE pt.`uc` NOT IN (SELECT `uc` FROM `tech_participant`) ";
		$sql .= " ORDER BY seafarer_code ASC ";
		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_participant_temp');
	}
}
?>