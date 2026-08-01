<?php
class Exam_attempt_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_exam_attempt";
	}

	function get_by_user($uc = NULL){
		$sql = " SELECT at.*, p.`seafarer_code`, p.`full_name`, am.`exam_code`, am.`is_language` ";
		$sql.= " FROM `".$this->table_name."` at ";
		$sql.= " LEFT JOIN `tech_participant` p ON at.`seafarer_code` = p.`seafarer_code` ";
		$sql.= " LEFT JOIN `tech_examination` am ON at.`uc_exam` = am.`uc` ";

		if ($uc != NULL) {
			$sql .= " WHERE at.`uc` = '".$uc."' " ;
		}
		
		return $this->exec_query($sql);
	}

	function get_my_review($uc = 0, $uc_exam){
		$sql  = " SELECT eq . * , eo.`option_text_in`, eo.`option_text_en` ";
		$sql .= " , eo.`option_att_type`, eo.`option_att_file`, eo.`is_correct` ";
		$sql .= " , eo.`id` AS `option_id`, eo.`uc` AS `option_uc`, em.`id` AS `pair_id`, em.`question_field_in` ";
		$sql .= " , em.`question_field_en`, em.`question_att_type` AS `m_q_type` ";
		$sql .= " , em.`question_att_file` AS `m_q_file`, em.`answer_field_in` ";
		$sql .= " , em.`answer_field_en`, em.`answer_att_type` AS `m_a_type` ";
		$sql .= " , em.`answer_att_file` AS `m_a_file` ";
		$sql .= " , e.`exam_code` ";
		$sql .= " FROM `tech_exam_question` eq ";
		$sql .= " LEFT JOIN `tech_exam_options` eo ON eq.`uc` = eo.`uc_exam_question` AND eo.`uc_exam` = '".$uc_exam."' ";
		$sql .= " LEFT JOIN `tech_exam_match` em ON eq.`uc` = em.`uc_exam_question` ";
		$sql .= " LEFT JOIN `tech_examination` e ON eq.uc_exam = e.uc";
		$sql .= " WHERE eq.`uc` IN (".$uc.") "; 
		$sql .= " AND eq.`uc_exam` = '".$uc_exam."' ";
		$sql .= " ORDER BY FIELD(eq.`uc`, ".$uc.") ";
		
		return $this->exec_query($sql);
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_exam_attempt_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_exam_attempt`) ";
		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_exam_attempt_temp');
	}

	// -- This function is temporary only - by Immu --
	function get_attempt_of_period($exam_ucs){
		$sql = " SELECT *
					FROM `tech_exam_attempt`
					WHERE `uc_exam`
					IN (".$exam_ucs.")
				";

		return $this->exec_query($sql);
	}

	function get_score_of_period($exam_ucs){
		$sql = " SELECT p.`seafarer_code`, p.`uc_diklat_participant` , p.`participant_no` , p.`full_name` , p.`uc_exam` 
					, ea.`uc` , ec.`uc_competency` , ea.`is_done` 
					, eac.`uc` uc_eac , eac.`uc_exam_attempt`, eac.`score`, eac.`score_2`, eac.`score_normal`
					FROM `tech_participant` p
					LEFT JOIN `tech_exam_competency` ec ON ec.`uc_exam` = p.`uc_exam`
					LEFT JOIN `tech_exam_attempt` ea ON ea.`uc_exam` = p.`uc_exam`
					AND ea.`seafarer_code` = p.`seafarer_code`
					LEFT JOIN `tech_exam_attempt_competency` eac ON eac.`uc_exam_attempt` = ea.`uc`
					AND eac.`uc_competency` = ec.`uc_competency`
					AND eac.`seafarer_code` = p.`seafarer_code`
					WHERE p.`uc_exam`
					IN (".$exam_ucs.")
					ORDER BY p.`full_name` ASC , eac.`score` ASC ";
		
		return $this->exec_query($sql);
	}

	function delete_attempt($uc_exam_attempt){
		$sql = " DELETE FROM `tech_exam_attempt` WHERE `uc` IN (".$uc_exam_attempt.") ";

		return $this->exec_query($sql);
	}

	function get_info_user($uc_exam_attempt = NULL, $uc_competency = NULL){
		$sql  = " SELECT ea.*, p.`full_name`, e.`exam_code`, e.`is_language`, p.`participant_no`, eac.`score` AS `comp_score`, eac.`score_2` AS `comp_score_2`, eac.`score_normal` AS `comp_score_normal`, eac.`uc_competency`, c.`label` AS `competency_name` ";
		$sql .= " FROM `tech_exam_attempt` ea ";
		$sql .= " LEFT JOIN `tech_exam_attempt_competency` eac ON eac.`uc_exam_attempt` = ea.`uc` ";
		$sql .= " LEFT JOIN `tech_competency` c ON eac.`uc_competency` = c.`uc` ";
		$sql .= " LEFT JOIN `tech_examination` e ON ea.`uc_exam` = e.`uc` ";
		$sql .= " LEFT JOIN `tech_participant` p ON p.`seafarer_code` = ea.`seafarer_code` " ;

		if ($uc_exam_attempt != NULL) {
			$sql .= "  WHERE ea.`uc` = '".$uc_exam_attempt."' AND eac.`uc_competency` = '".$uc_competency."' ";
		}

		return $this->exec_query($sql);
		
	}

	function get_detail_att_in($uc_ea = '', $uc_competency = '') {
		$sql  = " SELECT DISTINCT ea.`uc`, ea.`seafarer_code`, eac.`uc` AS `uc_eac`, eac.`score`, eac.`score_2`, eac.`score_normal` ";
		$sql .= " , p.`full_name`, p.`participant_no` ";
		$sql .= " FROM `tech_exam_attempt` ea ";
		$sql .= " LEFT JOIN `tech_exam_attempt_competency` eac ";
		$sql .= " ON eac.`uc_exam_attempt` = ea.`uc` ";
		$sql .= " LEFT JOIN `tech_participant` p ";
		$sql .= " ON p.`seafarer_code` = ea.`seafarer_code` ";
		$sql .= " AND p.`uc_exam` = ea.`uc_exam` ";
		$sql .= " WHERE ea.`uc` IN (".$uc_ea.") ";
		$sql .= " AND eac.`uc_competency` = '".$uc_competency."' ";
		$sql .= " ORDER BY p.`full_name` ";

		return $this->exec_query($sql);
	}

	//backup per day
    function get_attempt_for_exam_in($exam_ucs){
    	$sql = " SELECT * FROM `tech_exam_attempt` WHERE `uc_exam` IN (".$exam_ucs.") ORDER BY `id` ASC ";
    	
		return $this->exec_query($sql);
    }		

    function get_unfinish($uc_exam){
    	$sql = " SELECT *  FROM `tech_exam_attempt` WHERE `uc_exam` = '".$uc_exam."'  AND `is_done` = '0' " ;

    	return $this->exec_query($sql);
    }

    function get_with_uc_diklat($uc, $uc_period){
    	$sql = "
    			SELECT ea. * , p.`uc_diklat_participant`, per.`uc_upt`, per.`pra_pasca`
				FROM `tech_exam_attempt` ea
				LEFT JOIN `tech_participant` p ON p.`seafarer_code` = ea.`seafarer_code`
				AND p.`uc_period` = '".$uc_period."'
				LEFT JOIN `tech_period` per 
				ON per.`uc` = '".$uc_period."'
				WHERE ea.`uc` = '".$uc."'
    			";
    	echo "<br />".$sql;
    	echo "<hr />";		

    	return $this->exec_query($sql);		
    }
}
?>