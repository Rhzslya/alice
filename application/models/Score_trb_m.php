<?php
class Score_trb_m extends MY_Model {
	public function __construct() {
		parent::__construct();

		$this->table_name = "tech_score_trb";
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `".$this->table_name."_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `".$this->table_name."`) ";
		$sql .= " ORDER BY ";
		
		return $this->exec_query($sql);
	}
}
?>