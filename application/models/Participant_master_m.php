<?php
class Participant_master_m extends MY_Model {	
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_participant_master";
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_participant_master_temp` ";
		$sql .= " WHERE `seafarer_code` NOT IN (SELECT `seafarer_code` FROM `tech_participant_master`) ";

		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_participant_master_temp'); 
	}

	function get_list($uc_exam = 0, $uc_period = 0, $search = NULL,  $limit = NULL, $offset = 0) {
 		$sql  = " SELECT tpm.`seafarer_code`, tpm.`full_name`, tpm.`born_place`, tpm.`born_date`, tp.`uc_exam`, tp.`participant_no`, tp.`uc_period`  ";
		$sql .= " FROM `tech_participant_master`  tpm ";
		$sql .= " LEFT JOIN `tech_participant` tp ON tpm.`seafarer_code` = tp.`seafarer_code` " ;
		$sql .= " WHERE tp.`uc_period` = '".$uc_period."'  AND  tpm.`seafarer_code` NOT IN  ";
		$sql .= " ( ";
			$sql .= " SELECT `seafarer_code` ";
			$sql .= " FROM `tech_participant` ";
			if ($uc_exam != NULL) {
				$sql .= " WHERE `uc_exam` = '".$uc_exam."' AND `uc_period` = '".$uc_period."' ";
			}
		$sql .= " ) ";
		if ($search != NULL) {
			$sql .= " AND CONCAT(tpm.`seafarer_code` , tpm.`full_name`) LIKE '%".$search."%' ";
		}
		$sql .= " GROUP BY tpm.`seafarer_code` ";
		$sql .= " ORDER BY tpm.`seafarer_code` ASC ";
		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}
		
		return $this->exec_query($sql);
 	}

 	function get_search($search = NULL, $limit = NULL, $offset = 0) {
 		$sql = " SELECT * FROM `".$this->table_name."` ";
 		if ($search != NULL) {
 			$sql .= " WHERE `seafarer_code` LIKE '%".$search."%' ";
			$sql .= " OR `full_name` LIKE '%".$search."%' ";
 		}
 		$sql .= " ORDER BY `seafarer_code` ASC ";
		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}

		return $this->exec_query($sql);
 	}

 	//backup per day
	function get_in_period($parper_ucs){
		$sql = " SELECT * FROM `tech_participant_master` WHERE `seafarer_code` IN (".$parper_ucs.") ORDER BY `id` ASC ";

		return $this->exec_query($sql);
	}

	function get_not_in_temp(){
		$sql = " SELECT pmt.* FROM `tech_participant_master_temp` pmt WHERE pmt.`seafarer_code` NOT IN (SELECT pm.`seafarer_code` FROM `tech_participant_master` pm) ORDER BY pmt.id DESC ";

		return $this->exec_query($sql);
	}
}
?>