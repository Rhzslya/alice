<?php
class Template_examination_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_template_examination";
	}

    function get_exam_info($uc_exam = NULL) {
    	$sql  = " SELECT e.*, p.`uc` AS `uc_template`, p.`template`, d.`day`, l.`label` AS `level_name`, f.`label` AS `function_name` , ec.`uc` AS `uc_exam_competency`, ec.`uc_competency` ";
		$sql .= " FROM `tech_template_examination` e ";
		$sql .= " LEFT JOIN `tech_template_session` s ";
		$sql .= " ON e.`uc_session` = s.`uc` ";
		$sql .= " LEFT JOIN `tech_template_day` d ";
		$sql .= " ON s.`uc_day` = d.`uc` ";
		$sql .= " LEFT JOIN `tech_template_schedule` p ";
		$sql .= " ON d.`uc_template` = p.`uc` ";
		$sql .= " LEFT JOIN `tech_level` l ";
		$sql .= " ON e.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ";
		$sql .= " ON e.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_template_exam_competency` ec ";
		$sql .= " ON ec.`uc_exam` = e.`uc` ";

    	if ($uc_exam != NULL) {
    		$sql .= " WHERE e.`uc` = '".$uc_exam."' ";
    	}

    	return $this->exec_query($sql);
    }

	function get_exam_on_session($sess_ucs){
		$sql = " SELECT * FROM `tech_template_examination` WHERE `uc_session` IN (".$sess_ucs.") ORDER BY `id` ASC ";

		return $this->exec_query($sql);
	}    

}
?>