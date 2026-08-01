<?php
class Examination_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_examination";
	}

	function get_info_exam($uc_exam = NULL){
		$sql  = " SELECT e.*, l.`label` AS `level_name`, f.`label` AS `function_name` ";
		$sql .= " , c.`label` AS `competency_name`, p.`code_package` ";
		$sql .= " FROM `tech_examination` e ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON e.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON e.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON e.`uc_competency` = c.`uc` ";
		$sql .= " LEFT JOIN `tech_package` p ";
		$sql .= " ON e.`uc_package` = p.`uc` ";
		if ($uc_exam != NULL) {
			$sql .= " WHERE e.`uc` = '".$uc_exam."' ";
		}

		return $this->exec_query($sql);
	}

	function get_filtered_info($uc_function = NULL, $uc_competency = NULL) {
		$sql  = " SELECT e.*, l.`label` AS `level_name`, f.`label` AS `function_name`, c.`label` AS `competency_name`, p.`code_package` ";
		$sql .= " FROM `tech_examination` e ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON e.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON e.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON e.`uc_competency` = c.`uc` ";
		$sql .= " LEFT JOIN `tech_package` p ";
		$sql .= " ON e.`uc_package` = p.`uc` ";
		if ($uc_competency != NULL) {
			$sql .= " WHERE e.`uc_function` = '".$uc_function."' ";
			$sql .= " AND e.`uc_competency` = '".$uc_competency."' ";
		}
		$sql .= " ORDER BY e.`id` DESC ";

		return $this->exec_query($sql);
	}

	function get_all_score($uc_exam = NULL) {
		$sql  = " SELECT e.`uc` AS `uc_exam`, e.`exam_code`, e.`periode`, e.`time_exam`, l.`label` AS `level_name` ";
		$sql .= " , f.`label` AS `function_name`, c.`label` AS `competency_name`, p.`code_package` ";
		$sql .= " , pr.`full_name`, pr.`seafarer_code`, pr.`participant_no` ";
		$sql .= " , ea.`uc` AS `uc_attempt`, MAX(ea.`score`) AS `score` ";
		$sql .= " FROM `tech_examination` e ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON e.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON e.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON e.`uc_competency` = c.`uc` ";
		$sql .= " LEFT JOIN `tech_package` p ";
		$sql .= " ON e.`uc_package` = p.`uc` ";
		$sql .= " LEFT JOIN `tech_exam_participant` ep ";
		$sql .= " ON e.`uc` = ep.`uc_exam` ";
		$sql .= " LEFT JOIN `tech_participant` pr ";
		$sql .= " ON ep.`seafarer_code` = pr.`seafarer_code` ";
		$sql .= " LEFT JOIN `tech_exam_attempt` ea ";
		$sql .= " ON ea.`uc_exam` = e.`uc` ";
		$sql .= " AND ea.`seafarer_code` = ep.`seafarer_code` ";
		if ($uc_exam != NULL) {
			$sql .= " WHERE e.`uc` = '".$uc_exam."' ";
		}
		$sql .= " GROUP BY ep.`seafarer_code`ORDER BY ea.`score` DESC ";

		// echo $sql;

		return $this->exec_query($sql);
	}

	function get_all_level() {
    	$sql  = " SELECT `label`, `uc`, '0' AS `uc_parent`, '1' AS `type` ";
		$sql .= " FROM `tech_level` ";

		$sql .= " UNION ALL ";

		$sql .= " SELECT `label`, `uc`, `uc_level` AS `uc_parent`, '2' AS `type` ";
		$sql .= " FROM `tech_function` ";

		/*$sql .= " UNION ALL ";

		$sql .= " SELECT `label`, `uc`, `uc_function` AS `uc_parent`, '3' AS `type` ";
		$sql .= " FROM `tech_competency` ";*/
		$sql .= " ORDER BY `label` ASC ";

		return $this->exec_query($sql);
    }

    function get_all_participant($uc = NULL, $order = "seafarer_code", $sort = "ASC") {
    	$sql  = " SELECT e.`uc`, e.`exam_code`, d.`date`, p.`period`, p.`uc` AS `uc_period` ";
		$sql .= " , l.`label` AS `level_name`, f.`label` AS `function_name` ";
		$sql .= " , par.`seafarer_code`, par.`full_name`, par.`born_place`, par.`born_date` ";
		$sql .= " , ea.`uc` AS `ea_uc`, eac.`score_normal` AS `score_competency`   ";
		$sql .= " , eac.`uc` AS `uc_exam_attempt_competency` ,  c.`label` AS `competency_name` ";
		$sql .= " FROM `tech_examination` e ";
		$sql .= " LEFT JOIN `tech_session` s ";
		$sql .= " ON e.`uc_session` = s.`uc` ";
		$sql .= " LEFT JOIN `tech_day` d ";
		$sql .= " ON s.`uc_day` = d.`uc` ";
		$sql .= " LEFT JOIN `tech_period` p ";
		$sql .= " ON d.`uc_period` = p.`uc` ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON e.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON e.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_participant` par ";
		$sql .= " ON par.`uc_exam` = e.`uc` ";
		$sql .= " LEFT JOIN `tech_exam_attempt` ea ";
		$sql .= " ON ea.`uc_exam` = e.`uc` ";
		$sql .= " AND ea.`seafarer_code` = par.`seafarer_code` ";
		$sql .= " LEFT JOIN `tech_exam_attempt_competency` eac ";
		$sql .= " ON ea.`uc` = eac.`uc_exam_attempt` ";
		$sql .= " AND ea.`seafarer_code` = eac.`seafarer_code` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON eac.`uc_competency` = c.`uc` ";

		if ($uc != NULL) {
			$sql .= " WHERE e.`uc` = '".$uc."' ";
		}
		if ($sort != NULL) {
			$sql .= " ORDER BY `".$order."` ".$sort." , c.`sequence` ASC ";
		}

		// echo $sql;

    	return $this->exec_query($sql);
    }

    function show_exam_detail($uc_exam = NULL) {
    	$sql  = " SELECT e.`uc` AS `uc_exam`, e.`exam_code`, e.`is_language`, d.`date`, pe.`period`, l.`label` AS `level_name`, f.`label` AS `function_name`, c.`label` AS `competency_name`, pac.`code_package` ";
		$sql .= " , p.`seafarer_code`, p.`full_name`, p.`born_place`, p.`born_date`, p.`participant_no` ";
		$sql .= " , ea.`questions`, ea.`keys`, ea.`answers`, ea.`pairs`, ea.`score` ";
		$sql .= " FROM `tech_examination` e ";
		$sql .= " LEFT JOIN `tech_session` s ";
		$sql .= " ON e.`uc_session` = s.`uc` ";
		$sql .= " LEFT JOIN `tech_day` d ";
		$sql .= " ON s.`uc_day` = d.`uc` ";
		$sql .= " LEFT JOIN `tech_period` pe ";
		$sql .= " ON d.`uc_period` = pe.`uc` ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON e.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON e.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ";
		$sql .= " ON e.`uc_competency` = c.`uc` ";
		$sql .= " LEFT JOIN `tech_package` pac ";
		$sql .= " ON e.`uc_package` = pac.`uc` ";
		$sql .= " LEFT JOIN `tech_participant` p ";
		$sql .= " ON p.`uc_exam` = e.`uc` ";
		$sql .= " LEFT JOIN `tech_exam_attempt` ea ";
		$sql .= " ON ea.`uc_exam` = e.`uc` ";
		$sql .= " AND ea.`seafarer_code` = p.`seafarer_code` ";
		$sql .= " WHERE ea.`is_done` = '1' ";
		if ($uc_exam != NULL) {
			$sql .= " AND e.`uc` = '".$uc_exam."' ORDER BY p.`seafarer_code` ASC ";
		}

		return $this->exec_query($sql);
    }

    function show_exam($uc_exam = NULL, $questions = NULL) {
    	$sql  = " SELECT eq.`uc` AS `uc_question`, eq.`question_title_in`, eq.`question_title_en`, eq.`question_text_in`, eq.`question_text_en`, eq.`question_att_type`, eq.`question_att_file`, eq.`question_type`, eq.`answer_truefalse`, eq.`answer_multiplechoice` ";
		$sql .= " , eo.`id` AS `option_id`, eo.`option_text_in`, eo.`option_text_en`, eo.`option_att_type`, eo.`option_att_file`, eo.`is_correct` ";
		$sql .= " , em.`id` AS `match_id`, em.`question_field_in`, em.`question_field_en`, em.`question_att_type` AS `field_att_type`, em.`question_att_file` AS `field_att_file`, em.`answer_field_in`, em.`answer_field_en`, em.`answer_att_type`, em.`answer_att_file` ";
		$sql .= " FROM `tech_exam_question` eq ";
		$sql .= " LEFT JOIN `tech_exam_options` eo ";
		$sql .= " ON eo.`uc_exam_question` = eq.`uc` ";
		$sql .= " LEFT JOIN `tech_exam_match` em ";
		$sql .= " ON em.`uc_exam_question` = eq.`uc` ";
		if ($uc_exam != NULL) {
			$sql .= " WHERE eq.`uc_exam` = '".$uc_exam."' ";
			if ($questions != NULL) {
				$sql .= " AND eq.`uc` IN (".$questions.") ";
				$sql .= " ORDER BY FIELD(eq.`uc`, ".$questions.") ";
			}
		}

		return $this->exec_query($sql);
    }

    function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_examination_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_examination`) ";
		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_examination_temp');
	}

	function get_period_exam($uc_period = NULL,$uc_day = NULL,$uc_exam = NULL){
		$sql = " SELECT e.exam_code, e.uc as uc_exam, p.`period`, u.`upt_label`, pu.`pukp_label` ,d.`date`,
				l.label as level
				FROM `tech_period` p
				LEFT JOIN `tech_day` d ON d.`uc_period` = p.`uc`
				LEFT JOIN `tech_session` s ON s.`uc_day` = d.`uc`
				LEFT JOIN `tech_examination` e ON s.`uc` = e.`uc_session`
				LEFT JOIN `tech_upt` u ON p.`uc_upt` = u.`uc`
				LEFT JOIN `tech_pukp` pu ON pu.`uc` = u.`uc_pukp`
				LEFT JOIN `tech_level` l ON e.`uc_level` = l.`uc`				
				 
				";


		$sql .= "WHERE p.`uc` = '".$uc_period."'";


		if ($uc_day != NULL) {
			$sql .= " AND d.`uc` = '".$uc_day."' ";
		}

		if ($uc_exam != NULL) {
			$sql .= " AND e.`uc` = '".$uc_exam."' ";
		}
		
		return $this->exec_query($sql);
	}

	function get_level_in_period($uc_period){
		$sql = " SELECT DISTINCT l.`label` , e.`uc_level`
					FROM `tech_examination` e
					LEFT JOIN `tech_level` l ON l.`uc` = e.`uc_level`
					WHERE `uc_session`
					IN (
						SELECT `uc`
						FROM `tech_session`
						WHERE `uc_day`
						IN (
							SELECT `uc`
							FROM `tech_day`
							WHERE `uc_period` = '$uc_period'
						)
					)
					ORDER BY l.`label` ASC";
		
		return $this->exec_query($sql);
	}

	//backup per day
	function get_exam_on_session($sess_ucs){
		$sql = " SELECT * FROM `tech_examination` WHERE `uc_session` IN (".$sess_ucs.") ORDER BY `id` ASC ";

		return $this->exec_query($sql);
	}		
}
?>