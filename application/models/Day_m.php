<?php
class Day_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_day";
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