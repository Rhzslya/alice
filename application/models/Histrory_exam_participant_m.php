<?php
class History_exam_participant_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_history_exam_participant";
	}

	function get_data_score($uc_period = NULL, $seafarer_code = NULL){
		$sql  = " SELECT * ";
		$sql .= " FROM `tech_score` ";

		if ($uc_period != NULL) {
			$sql .= " WHERE `uc_period` = '".$uc_period."' ";
		}

		if ($seafarer_code != NULL) {
			$sql .= " AND `seafarer_code` = '".$seafarer_code."' ";
		}
		
		$sql .= " GROUP BY uc_period, seafarer_code ";
		
		return $this->exec_query($sql);
	}

}
?>