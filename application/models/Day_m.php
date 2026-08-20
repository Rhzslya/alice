<?php
class Day_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_day";
	}

	function get_days_by_ukp_date($uc_ukp, $date){
		$sql  = " SELECT `uc`, `uc_period`, `date` FROM `tech_day` ";
		$sql .= " WHERE `date` = '".$date."' ";
		$sql .= " AND `uc_period` IN ( SELECT `uc` FROM `tech_period` WHERE `uc_ukp` = '".$uc_ukp."' ) ";
		$sql .= " ORDER BY `id` ASC ";

		return $this->exec_query($sql);
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_day_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_day`) ";
		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_day_temp');
	}
}
?>