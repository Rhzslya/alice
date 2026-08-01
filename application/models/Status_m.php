<?php

class Status_m extends MY_Model 
{
	public function __construct() 
	{
		parent::__construct();

		$this->table_name = "tech_status";
	}

	public function get_status_in($seafarer_code = NULL, $uc_competency = NULL, $diklat_type = NULL){
		$sql  = " SELECT *  ";
		$sql .= " FROM ".$this->table_name." ";

		if ($seafarer_code != NULL) {			
			$sql .= " WHERE seafarer_code IN (".$seafarer_code.") ";
		}

		if ($uc_competency != NULL) {			
			$sql .= " AND uc_competency IN (".$uc_competency.") ";
		}

		if ($diklat_type != NULL) {			
			$sql .= " AND pra_pasca IN (".$diklat_type.") ";
		}

		$sql .= " ORDER BY `seafarer_code` ASC ";

		return $this->exec_query($sql);
	}

	function get_status_export($uc_pukp = NULL,$uc_upt = NULL,$uc_level = NULL,$category = NULL)
	{
		$sql   = " SELECT s.*, sc.*, p.`period` ";
		$sql  .= " FROM `".$this->table_name."` s ";
		$sql  .= " LEFT JOIN `tech_score` sc ON s.`uc_score` = sc.`uc` ";
		$sql  .= " LEFT JOIN `tech_period` p ON sc.`uc_period` = p.`uc` ";
		$sql  .= " LEFT JOIN `tech_upt` up ON p.`uc_upt` = up.`uc` ";
		$sql  .= " LEFT JOIN `tech_participant_master` pm ON s.`seafarer_code` = pm.`seafarer_code` ";
		$sql  .= " LEFT JOIN `tech_competency` c ON s.`uc_competency` = c.`uc` ";
		$sql  .= " LEFT JOIN `tech_function` f ON c.`uc_function` = f.`uc` ";
		$sql  .= " LEFT JOIN `tech_level` l ON f.`uc_level` = l.`uc`  ";

		if ($uc_pukp != NULL) {
			$sql  .= " WHERE up.`uc_pukp` = '".$uc_pukp."' ";
		}
		if ($uc_upt != NULL) {
			$sql  .= " AND up.`uc` = '".$uc_upt."' ";
		}
		if ($uc_level != NULL) {
			$sql  .= " AND l.`uc` = '".$uc_level."' ";
		}
		if ($category != NULL) {
			$sql  .= " AND s.`pra_pasca` = '".$category."' ";
		}
	
		$sql  .= " GROUP BY pm.`seafarer_code` , c.`sequence` ";
		$sql  .= " ORDER BY pm.`full_name` , pm.`seafarer_code` , c.`sequence` ASC  ";

		return $this->exec_query($sql);
	}

	function get_uc_diklat_par($uc_period = NULL, $seafarer_code = NULL)
	{
		$sql  = " SELECT p.* , dp.`uc` `uc_diklat_participant` ";
		$sql .= " FROM `pangkalan_data_2_0`.`tech_period` p ";
		$sql .= " LEFT JOIN `pangkalan_data_2_0`.`tech_diklat_participant` dp ON p.`uc_level` = dp.`uc_level` ";

		if ($uc_period != NULL) {
			$sql .= " WHERE p.`uc` = '".$uc_period."' ";
		}

		if ($seafarer_code != NULL) {
			$sql .= " AND dp.`seafarer_code` = '".$seafarer_code."' ";
		}

		//echo "<br />".$sql."<br />";

		return $this->exec_query($sql);
	}

	public function get_participant_competency($per_ucs, $seafcs){
		$sql  = " SELECT * FROM `tech_status` ";
		$sql .= " WHERE `seafarer_code` IN (".$seafcs.") ";
		$sql .= " AND `uc_competency` IN ( 
					SELECT ec.`uc_competency` 
					FROM `tech_exam_competency` ec 
					WHERE ec.`uc_exam` IN ( 
						SELECT e.`uc` `uc_exam` FROM `tech_examination` e WHERE e.`uc_period` IN (".$per_ucs.") 
					) 
				) ";

		return $this->exec_query($sql);
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_status_temp` WHERE `uc` NOT IN ( SELECT `uc` FROM `tech_status` ) ";
		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_status_temp');
	}
}
?>