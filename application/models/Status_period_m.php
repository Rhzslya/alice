<?php
class Status_period_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_status_period";
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_status_period_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_status_period`) ";
		
		return $this->exec_query($sql);
	}

	// function get_on_status_period(){
	// 	$sql  = " SELECT st.*, s.`score_max` ";
	// 	$sql .= " FROM `tech_score_temp` st";
	// 	$sql .= " LEFT JOIN `tech_status_period` s ";
	// 	$sql .= " ON s.`seafarer_code` = st.`seafarer_code` ";
	// 	$sql .= " AND s.`uc_competency` = st.`uc_competency` ";
	// 	$sql .= " AND s.`pra_pasca`= st.`pra_pasca` ";

	// 	return $this->exec_query($sql);
	// }

	function get_compare_with_score(){
		$sql  = " SELECT st.*, sp.`uc` AS `uc_status_period`, sp.`status` 
					FROM `tech_score_temp` st 
					LEFT JOIN `tech_status_period` sp 
					ON sp.`uc_diklat_participant` = st.`uc_diklat_participant` 
					AND sp.`uc_competency` = st.`uc_competency`  
					AND sp.`uc_period` = st.`uc_period`
					";

		$sql .= " AND sp.`diklat_type` = st.`pra_pasca` ";

		return $this->exec_query($sql);
	}

	function get_status_period($uc_period){
        $sql  = " SELECT pp.*, sp.`uc_competency`, sp.`is_pass`, sp.`status` ";
        $sql .= " FROM `tech_period_participant` pp ";
        $sql .= " LEFT JOIN `tech_status_period` sp ";
        $sql .= " ON sp.`uc_diklat_participant` = pp.`uc_diklat_participant` ";
        $sql .= " AND sp.`uc_period` = '".$uc_period."' ";
        $sql .= " WHERE pp.`uc_period` = '".$uc_period."' ";

        return $this->exec_query($sql);
    }
}
?>