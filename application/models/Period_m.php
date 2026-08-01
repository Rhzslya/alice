<?php
class Period_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_period";
	}

	function get_list($uc = NULL, $limit = NULL, $offset = 0) {
		/*$sql  = " SELECT p.*, u.`upt_label`, pu.`pukp_label` ";
		$sql .= " FROM `tech_period` p ";
		$sql .= " LEFT JOIN `tech_upt` u ";
		$sql .= " ON p.`uc_upt` = u.`uc` ";
		$sql .= " LEFT JOIN `tech_pukp` pu ";
		$sql .= " ON u.`uc_pukp` = pu.`uc` ";
		if ($uc != NULL) {
			$sql .= " WHERE p.`uc` = '".$uc."' ";
		}
		$sql .= " ORDER BY p.`period` DESC ";*/

		$sql  = " SELECT p. * , u.`upt_label` , pu.`pukp_label` ";
		$sql .= " FROM `tech_exam_attempt_competency` eat ";
		$sql .= " LEFT JOIN `tech_exam_attempt` ea ON eat.`uc_exam_attempt` = ea.`uc` ";
		$sql .= " LEFT JOIN `tech_examination` e ON ea.`uc_exam` = e.`uc` ";
		$sql .= " LEFT JOIN `tech_session` s ON e.`uc_session` = s.`uc` ";
		$sql .= " LEFT JOIN `tech_day` d ON s.`uc_day` = d.`uc` ";
		$sql .= " LEFT JOIN `tech_period` p ON d.`uc_period` = p.`uc` ";
		$sql .= " LEFT JOIN `tech_upt` u ON p.`uc_upt` = u.`uc` ";
		$sql .= " LEFT JOIN `tech_pukp` pu ON u.`uc_pukp` = pu.`uc` ";
		$sql .= " GROUP BY p.`period` ";
		$sql .= " ORDER BY p.`date_start` DESC, p.`date_finish` DESC, p.`id` DESC ";

		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}

		// echo $sql;

		return $this->exec_query($sql);
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_period_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_period`) ";
		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_period_temp');
	}

	function detail_period_upt($uc){
		$sql  = " SELECT p.`period`, p.`date_start` , p.`date_finish`, u.`upt_label`, pu.`pukp_label` ";
        $sql .= " FROM `tech_period` p ";
        $sql .= " JOIN `tech_upt` u ON u.`uc` = p.`uc_upt` ";
        $sql .= " JOIN `tech_pukp` pu ON pu.`uc` = u.`uc_pukp` ";
        $sql .= " WHERE p.`uc` = '".$uc."' ";

		return $this->exec_query($sql);
	}

	// 	Immu : Saha ieu nu nyieun? kapake teu? lain na teu bs truncate sakaligus???
	//			Mun teu kapake, geura hapus!
	function empty_all_temp(){

		$sql  = " TRUNCATE `tech_day_temp`; ";
		$sql .= " TRUNCATE `tech_examination_temp`; " ;
		$sql .= " TRUNCATE `tech_exam_attempt_competency_temp`; " ;
		$sql .= " TRUNCATE `tech_exam_attempt_temp`; " ;
		$sql .= " TRUNCATE `tech_exam_competency_temp`; " ;
		$sql .= " TRUNCATE `tech_participant_master_temp`; " ;
		$sql .= " TRUNCATE `tech_participant_temp`; " ;
		$sql .= " TRUNCATE `tech_period_participant_temp`; " ;
		$sql .= " TRUNCATE `tech_period_temp`; " ;
		$sql .= " TRUNCATE `tech_session_temp`; " ;
		
		// return $this->exec_query($sql);
	}

	function get_delete_period($uc_period){
		$sql  = " SELECT p.`uc`, p.`period`, e.`exam_code`, d.`uc` AS `uc_day` , e.`uc` AS `uc_exam` " ;
		$sql .= " FROM `tech_period` p " ;
		$sql .= " LEFT JOIN `tech_day` d ON d.`uc_period` = p.`uc` " ;
		$sql .= " LEFT JOIN `tech_session` s ON s.`uc_day` = d.`uc` " ;
		$sql .= " LEFT JOIN `tech_examination` e ON e.`uc_session` = s.`uc` " ;
		$sql .= " WHERE p.`uc` = '".$uc_period."' " ;

		return $this->exec_query($sql);
	}


	function get_info_exam($uc_exam = NULL, $uc_period = NULL){
		$sql  =  " SELECT e.`exam_code`, e.`pra_pasca` ,ep.`uc` AS `uc_ep`, ep.`package_code`, l.`label` AS `label_level` " ;
		$sql .= " , f.`label` AS `label_function`, d.`date`, p.`period`, u.`upt_label`, pu.`pukp_label` " ;
		$sql .= " , p.`date_start`, p.`date_finish` " ;
		$sql .= " FROM `tech_examination` e " ;
		$sql .= " LEFT JOIN `tech_exam_package` ep ON ep.`uc_exam` = e.`uc` " ;
		$sql .= " LEFT JOIN `tech_level` l ON l.`uc` = e.`uc_level` " ;
		$sql .= " LEFT JOIN `tech_function` f ON f.`uc` = e.`uc_function` " ;
		$sql .= " LEFT JOIN `tech_session` s ON s.`uc` = e.`uc_session` " ;
		$sql .= " LEFT JOIN `tech_day` d ON d.`uc` = s.`uc_day` " ;
		$sql .= " LEFT JOIN `tech_period` p ON p.`uc` = d.`uc_period` " ;
		$sql .= " LEFT JOIN `tech_upt` u ON u.`uc` = p.`uc_upt` " ;
		$sql .= " LEFT JOIN `tech_pukp` pu ON pu.`uc` = u.`uc_pukp` " ;

		if ($uc_exam != NULL) {
			$sql .= " WHERE e.`uc` = '".$uc_exam."' " ;
			$sql .= " AND p.`uc` = '".$uc_period."' " ;
		}

		return $this->exec_query($sql);
	}

	function get_score($uc_period = NULL){
		$sql  = " SELECT p.`uc`, p.`uc_upt`, eac.`uc_competency`, e.`diklat_type`, eac.`seafarer_code`, eac.`score_normal` ";
		$sql .= " FROM `tech_period` p ";
		$sql .= " LEFT JOIN `tech_examination` e ON p.`uc` = e.`uc_period` ";
		$sql .= " LEFT JOIN `tech_exam_attempt` ea ON e.`uc` = ea.`uc_exam` ";
		$sql .= " LEFT JOIN `tech_exam_attempt_competency` eac ON ea.`uc` = eac.`uc_exam_attempt` ";

		if ($uc_period != NULL) {
			$sql .= " WHERE e.`uc_period` = '".$uc_period."' ";
		}

		return $this->exec_query($sql);
	}

	function get_by_schedule($uc_ukp = NULL, $limit = NULL, $offset = 0, $key = NULL) {
		$sql  = " SELECT p.*, u.`upt_label`, pu.`pukp_label`, pu.`uc` AS `uc_pukp` , u.`uc` AS `uc_upt`, l.`label` AS `level` ";
		$sql .= " FROM `tech_period` p ";
		$sql .= " LEFT JOIN `tech_upt` u ";
		$sql .= " ON p.`uc_upt` = u.`uc` ";
		$sql .= " LEFT JOIN `tech_pukp` pu ";
		$sql .= " ON u.`uc_pukp` = pu.`uc` ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON l.`uc` = p.`uc_level` ";
		$sql .= " WHERE p.`uc_ukp` = '".$uc_ukp."' ";
		$sql .= " ORDER BY u.`upt_label` , l.`label`, p.`pra_pasca`, p.`period` ASC ";
		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}
		//echo $sql;
		return $this->exec_query($sql);
	}

	function get_exams($uc_period) {
		$sql  = " SELECT e.`uc` AS `uc_exam` ";
		$sql .= " , p.`period` , p.`uc_upt`, p.`uc_level`, p.`pra_pasca`, p.`category` ";
		$sql .= " , u.`uc_pukp`, u.`upt_label`, pu.`pukp_label`, l.`label` ";
		$sql .= " FROM `tech_examination` e ";
		$sql .= " LEFT JOIN `tech_period` p ON p.`uc` = e.`uc_period` ";
		$sql .= " LEFT JOIN `tech_upt` u ON u.`uc` = p.`uc_upt` ";
		$sql .= " LEFT JOIN `tech_pukp` pu ON pu.`uc` = u.`uc_pukp` ";
		$sql .= " LEFT JOIN `tech_level` l ON l.`uc` = p.`uc_level` ";
		$sql .= " WHERE `uc_period` = '".$uc_period."' ";

		return $this->exec_query($sql);
	}
}
?>