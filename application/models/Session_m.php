<?php
class Session_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_session";
	}

	/*function get_manage_period($uc_period = NULL) {
		$sql  = " SELECT p.*, up.`upt_label`, pu.`pukp_label` ";
		$sql .= " , d.`date`, d.`uc` AS `uc_day`, s.`uc` AS `uc_session`, s.`add_time`, s.`is_active` ";
		$sql .= " , e.`exam_code`, l.`label` AS `level_name`, f.`label` AS `function_name`, c.`label` AS `competency_name`, e.`uc` AS 'uc_exam' ";
		$sql .= " , e.`duration`, pac.`code_package`, COUNT(par.`uc`) AS `no_participant` ";
		$sql .= " FROM `tech_period` p ";
		$sql .= " LEFT JOIN `tech_upt` up ";
		$sql .= " ON p.`uc_upt` = up.`uc` ";
		$sql .= " LEFT JOIN `tech_pukp` pu ";
		$sql .= " ON up.`uc_pukp` = pu.`uc` ";
		$sql .= " LEFT JOIN `tech_day` d ";
		$sql .= " ON d.`uc_period` = p.`uc` ";
		$sql .= " LEFT JOIN `tech_session` s ";
		$sql .= " ON s.`uc_day` = d.`uc` ";
		$sql .= " LEFT JOIN `tech_examination` e ";
		$sql .= " ON e.`uc_session` = s.`uc` ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON e.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON e.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON e.`uc_competency` = c.`uc` ";
		$sql .= " LEFT JOIN `tech_package` pac ";
		$sql .= " ON e.`uc_package` = pac.`uc` ";
		$sql .= " LEFT JOIN `tech_participant` par ";
		$sql .= " ON par.`uc_exam` = e.`uc` ";
		if ($uc_period != NULL) {
			$sql .= " WHERE p.`uc` = '".$uc_period."' ";
		}
		$sql .= " GROUP BY d.`date`, s.`add_time`, e.`uc` ORDER BY d.`date` ASC, s.`add_time` ASC ";

		// echo $sql;

		return $this->exec_query($sql);
	}*/

	function get_manage_period($uc_period = NULL) {
		$sql  = " SELECT p.* ";
		$sql .= " , d.`uc` AS `uc_day`, d.`date`";
		$sql .= " , s.`uc` AS `uc_session`, s.`add_time`, s.`is_active` ";
		$sql .= " , e.`uc` AS 'uc_exam', e.`exam_code`, e.`duration`, e.show_score ";
		$sql .= " , e.`diklat_type` ";
		$sql .= " , ec.`uc_competency` ";
		$sql .= " , co.`sequence`, co.`label` ";
		$sql .= " , l.`label` AS `level_name`, f.`label` AS `function_name` ";
		// $sql .= " , COUNT(par.`uc`) AS `count_part` ";
		$sql .= " FROM `tech_period` p ";
		$sql .= " LEFT JOIN `tech_day` d ";
		$sql .= " ON d.`uc_period` = p.`uc` ";
		$sql .= " LEFT JOIN `tech_session` s ";
		$sql .= " ON s.`uc_day` = d.`uc` ";
		$sql .= " LEFT JOIN `tech_examination` e ";
		$sql .= " ON e.`uc_session` = s.`uc` ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON e.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON e.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_exam_competency` ec ";
		$sql .= " ON ec.`uc_exam` = e.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` AS co ";
		$sql .= " ON co.`uc` = ec.`uc_competency` ";
		/*$sql .= " LEFT JOIN `tech_participant` par ";
		$sql .= " ON par.`uc_exam` = e.`uc` ";*/
		if ($uc_period != NULL) {
			$sql .= " WHERE p.`uc` = '".$uc_period."' ";
		}
		// $sql .= " GROUP BY d.`date`, s.`add_time`, ec.`uc_competency` ";
		$sql .= " ORDER BY d.`date` ASC, s.`id` ASC, e.`id` ASC, co.`sequence` ";
		
		// echo $sql;

		return $this->exec_query($sql);
	}

	function get_multiple($uc_period = NULL, $uc_day = NULL){
		$sql  = " SELECT p . `uc` , d.`uc` AS `uc_day` , d.`date` , s.`uc` AS `uc_session` , s.`add_time`, e.`exam_code` , co.`sequence` , co.`label` , l.`label` AS `level_name` , f.`label` AS `function_name`, e.`uc` AS `uc_exam`,ec.`uc_competency` " ;
		$sql .= " FROM `tech_period` p " ;
		$sql .= " LEFT JOIN `tech_day` d ON d.`uc_period` = p.`uc` " ;
		$sql .= " LEFT JOIN `tech_session` s ON s.`uc_day` = d.`uc` " ;
		$sql .= " LEFT JOIN `tech_examination` e ON e.`uc_session` = s.`uc` " ;
		$sql .= " LEFT JOIN `tech_level` l ON e.`uc_level` = l.`uc` " ;
		$sql .= " LEFT JOIN `tech_function` f ON e.`uc_function` = f.`uc` " ;
		$sql .= " LEFT JOIN `tech_exam_competency` ec ON ec.`uc_exam` = e.`uc` " ;
		$sql .= " LEFT JOIN `tech_competency` AS co ON co.`uc` = ec.`uc_competency` " ;

		if ($uc_period != NULL) {
			$sql .= " WHERE p.`uc` = '".$uc_period."' ";
			$sql .= " AND d.`uc` = '".$uc_day."' " ;
		}

		$sql .= " GROUP BY d.`date` , s.`add_time` , ec.`uc_competency` " ;
		$sql .= " ORDER BY d.`date` ASC , s.`add_time` ASC , e.`id` ASC , co.`sequence` " ;
		
		return $this->exec_query($sql);
	}

	function get_report_pra(){
		$sql  = " SELECT p . `uc` , d.`uc` AS `uc_day` , d.`date` , s.`uc` AS `uc_session` , s.`add_time`, e.`exam_code` , co.`sequence` , co.`label` , l.`label` AS `level_name` , f.`label` AS `function_name`, e.`uc` AS `uc_exam`,ec.`uc_competency` " ;
		$sql .= " FROM `tech_period` p " ;
		$sql .= " LEFT JOIN `tech_day` d ON d.`uc_period` = p.`uc` " ;
		$sql .= " LEFT JOIN `tech_session` s ON s.`uc_day` = d.`uc` " ;
		$sql .= " LEFT JOIN `tech_examination` e ON e.`uc_session` = s.`uc` " ;
		$sql .= " LEFT JOIN `tech_level` l ON e.`uc_level` = l.`uc` " ;
		$sql .= " LEFT JOIN `tech_function` f ON e.`uc_function` = f.`uc` " ;
		$sql .= " LEFT JOIN `tech_exam_competency` ec ON ec.`uc_exam` = e.`uc` " ;
		$sql .= " LEFT JOIN `tech_competency` AS co ON co.`uc` = ec.`uc_competency` " ;

		// if ($uc_period != NULL) {
			$sql .= " WHERE p.`uc` = '534-93523-96-84' " ;
			$sql .= " AND l.`uc` = '49-85742-23' " ;
			$sql .= " AND e.`pasca_prala` = '2'	" ;
		// }

		
		return $this->exec_query($sql);
	}

	function get_add_exam($uc = NULL){
		$sql  = " SELECT s.*  , d.`date`, p.`period`, p.`uc` AS 'uc_period' " ;
		$sql .= " FROM `tech_session` s " ;
		$sql .= " LEFT JOIN `tech_day` d ON s.`uc_day` = d.`uc` " ;
		$sql .= " LEFT JOIN `tech_period` p ON d.`uc_period` = p.`uc` " ;
		if ($uc != NULL) {
			$sql .= " WHERE s.`uc` = '".$uc."' " ;	
		}
	
		return $this->exec_query($sql);
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_session_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_session`) ";
		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_session_temp');
	}

	function delete_session($uc_day){
		$sql = " DELETE FROM `tech_session` WHERE `uc_day` IN (".$uc_day.") ";

		return $this->exec_query($sql);
	}	

	//backup per day
	function get_session_on_day($uc_day){
		$sql  = " SELECT * FROM `tech_session` ";
		$sql .= " WHERE `uc_day` IN (".$uc_day.") ";
		$sql .= " ORDER BY `id` ASC ";
		
		return $this->exec_query($sql);
	}				
}
?>