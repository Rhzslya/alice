<?php
class Exam_attempt_competency_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_exam_attempt_competency";
	}

	function temp_not_in_real(){
		$sql  = " SELECT eac.* , e.`diklat_type`, d.`uc_period`, p.`uc_upt` ";
		$sql .= " FROM `tech_exam_attempt_competency_temp` eac ";
		$sql .= " LEFT JOIN `tech_exam_attempt_temp` ea ON  eac.`uc_exam_attempt` = ea.`uc` ";
		$sql .= " LEFT JOIN `tech_examination` e ON ea.`uc_exam` = e.`uc` ";
		$sql .= " LEFT JOIN `tech_session` s ON e.`uc_session` = s.`uc` ";
		$sql .= " LEFT JOIN `tech_day` d ON s.`uc_day` = d.`uc` ";
		$sql .= " LEFT JOIN `tech_period` p ON d.`uc_period` = p.`uc` ";
		$sql .= " WHERE eac.`uc` NOT IN (SELECT `uc` FROM `tech_exam_attempt_competency`) ";
		$sql .= " AND eac.`uc` NOT IN (SELECT `uc_eac` FROM `tech_score`) ";

		return $this->exec_query($sql);
	}

	// function temp_not_in_real_group(){
	// 	$sql  = " SELECT eac.`uc`,eac.`uc_competency`,eac.`seafarer_code`, max(eac.`score_normal`) score_normal, e.`diklat_type` ";  
	// 	$sql .= " FROM `tech_exam_attempt_competency_temp` eac ";
	// 	$sql .= " LEFT JOIN `tech_exam_attempt_temp` ea ON  eac.`uc_exam_attempt` = ea.`uc` ";
	// 	$sql .= " LEFT JOIN `tech_examination` e ON ea.`uc_exam` = e.`uc` ";
	// 	$sql .= " WHERE eac.`uc` NOT IN (SELECT `uc` FROM `tech_exam_attempt_competency`) ";
	// 	$sql .= " GROUP BY eac.`uc_competency`,eac.`seafarer_code` ";

	// 	return $this->exec_query($sql);
	// }

	function empty_temp(){
		$this->db->empty_table('tech_exam_attempt_competency_temp');
	}

	function get_competency($uc = NULL){
		$sql  = " SELECT eac.* , c.`label` ";
		$sql .= " FROM `tech_exam_attempt_competency` eac ";
		$sql .= " LEFT JOIN `tech_competency` c ON eac.`uc_competency` = c.`uc` ";
		
		if ($uc != NULL) {
			$sql .= " WHERE eac.`uc` = '".$uc."' ";
		}
		
		return $this->exec_query($sql);
	}

	function get_all_score($uc_period, $uc_exam, $uc_competency){
		$sql  = " SELECT ea.`uc_exam` ";
		$sql .= " , etc.`seafarer_code`, etc.`score` ";
		$sql .= " , pm.`full_name`, pp.`participant_no` ";

		$sql .= " FROM `tech_exam_attempt` ea ";
		$sql .= " LEFT JOIN `tech_exam_attempt_competency` etc ";
		$sql .= " ON etc.`uc_exam_attempt` = ea.`uc` "; 
		$sql .= " AND etc.`uc_competency` = '".$uc_competency."' ";
		$sql .= " LEFT JOIN `tech_participant_master` pm ";
		$sql .= " ON pm.`seafarer_code` = etc.`seafarer_code` ";
		$sql .= " LEFT JOIN `tech_period_participant` pp ";
		$sql .= " ON pp.`seafarer_code` = etc.`seafarer_code` ";
		$sql .= " AND pp.`uc_period` = '".$uc_period."' ";

		$sql .= " WHERE ea.`uc_exam` = '".$uc_exam."' ";
		$sql .= " ORDER BY pm.`full_name` ASC ";
		
		return $this->exec_query($sql);
	}

	function delete_attempt_competency($uc_exam_attempt){
		$sql = " DELETE FROM `tech_exam_attempt_competency` WHERE `uc_exam_attempt` IN (".$uc_exam_attempt.") ";

		return $this->exec_query($sql);
	}	

	//backup per day
	function get_score_for_attempt_in($att_ucs){
		$sql = " SELECT * FROM `tech_exam_attempt_competency` WHERE `uc_exam_attempt` IN (".$att_ucs.") ORDER BY `id` ASC ";
    	
		return $this->exec_query($sql);
	}

	function get_my_attempt($uc_period, $uc_competency) {
		$sql = " SELECT eac.`uc_exam_attempt`, eac.`seafarer_code`, eac.`uc_competency`, ea.`uc_exam`, e.`uc_period`
					FROM `tech_exam_attempt_competency` eac 
					LEFT JOIN `tech_exam_attempt` ea
					ON ea.`uc` = eac.`uc_exam_attempt`
					LEFT JOIN `tech_examination` e
					ON e.`uc` = ea.`uc_exam` 
					WHERE eac.`uc_competency` = '".$uc_competency."'
					AND e.`uc_period` = '".$uc_period."'
					";
		//echo $sql;
		return $this->exec_query($sql);			
	}

	function get_my_attempt_only($uc_period, $uc_competency, $seafarer_code) {
		$sql = " SELECT eac.`uc_exam_attempt`, eac.`seafarer_code`, eac.`uc_competency`, ea.`uc_exam`, e.`uc_period`
					FROM `tech_exam_attempt_competency` eac 
					LEFT JOIN `tech_exam_attempt` ea
					ON ea.`uc` = eac.`uc_exam_attempt`
					LEFT JOIN `tech_examination` e
					ON e.`uc` = ea.`uc_exam` 
					WHERE eac.`uc_competency` = '".$uc_competency."'
					AND eac.`seafarer_code` = '".$seafarer_code."'
					AND e.`uc_period` = '".$uc_period."'
					";
		//echo $sql;
		return $this->exec_query($sql);			
	}

	function get_lost_uc_exam_competency($uc_exam) {
		$sql = "SELECT eac.`uc_exam_attempt` 
				FROM `tech_exam_attempt_competency` eac 
				LEFT JOIN `tech_exam_attempt` ea
				ON ea.`uc` = eac.`uc_exam_attempt`
				WHERE ea.`uc_exam` = '".$uc_exam."'";

		return $this->exec_query($sql);			
	}

	function update_uc_competency($ea_ucs, $uc_competency) {
		$sql = "UPDATE `tech_exam_attempt_competency` SET `uc_competency`= '".$uc_competency."' 
				WHERE `uc_exam_attempt` IN (".$ea_ucs.")";

		return $this->exec_query($sql);			
	}
}
?>