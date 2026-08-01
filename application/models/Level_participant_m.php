<?php
Class Level_participant_m extends MY_Model{

	function __construct(){
		parent::__construct();
		$this->table_name = 'tech_level_participant';
	}

	function get_participant_master_upt($pukp = NULL, $upt = NULL, $level = NULL)
	{
		$sql  = " SELECT p.* ";
		$sql .= " FROM `tech_level_participant` lp ";
		$sql .= " LEFT JOIN `tech_participant` p ON lp.`seafarer_code` = p.`seafarer_code` ";
		
		if ($pukp != NULL) {
			
			$sql .= " WHERE lp.`uc_pukp` = '".$pukp."' ";
		}

		if ($upt != NULL) {
			
			$sql .= " AND lp.`uc_upt` = '".$upt."' ";
		}

		if ($level != NULL) {
			
			$sql .= " AND lp.`uc_level` = '".$level."' ";
		}
		
		$sql .= " GROUP BY p.`seafarer_code` ";

		// echo $sql;

		return $this->exec_query($sql);
	}

	function get_status_upt($pukp = NULL, $upt = NULL, $level = NULL)
	{
		$sql  = " SELECT s.*  ";
		$sql .= " FROM `tech_level_participant` lp ";
		$sql .= " LEFT JOIN `tech_status` s ON lp.`seafarer_code` = s.`seafarer_code` ";
		
		if ($pukp != NULL) {
			
			$sql .= " WHERE lp.`uc_pukp` = '".$pukp."' ";
		}

		if ($upt != NULL) {
			
			$sql .= " AND lp.`uc_upt` = '".$upt."' ";
		}

		if ($level != NULL) {
			
			$sql .= " AND lp.`uc_level` = '".$level."' ";
		}

		// echo $sql;

		return $this->exec_query($sql);
	}
}