<?php
class Recap_participant_temp_m extends MY_Model {
	public function __construct() {
		parent::__construct();

		$this->db_other 	= "`cba_ukp_recap_1_0`";
		$this->table_name 	= "".$this->db_other.".`tech_participant_temp`";
	}

	public function get_participant_master(){
		$sql   = " SELECT * ";
		$sql  .= " FROM ".$this->table_name." ";
		$sql  .= " WHERE seafarer_code  ";
		$sql  .= " NOT IN ";
		$sql  .= " ( ";
		$sql  .= " select seafarer_code from ".$this->db_other.".`tech_participant` ";
		$sql  .= " ) ";

		return $this->exec_query($sql);
	}

	public function empty_temp(){
		$this->db->empty_table(''.$this->db_other.'.`tech_participant_temp`'); 
	}
}