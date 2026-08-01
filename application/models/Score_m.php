<?php
class Score_m extends MY_Model {
	public function __construct() {
		parent::__construct();

		$this->table_name = "tech_score";
	}

	function get_on_status(){
		$sql  = " SELECT st.*, s.`score_max` ";
		$sql .= " FROM `tech_score_temp` st";
		$sql .= " LEFT JOIN `tech_status` s ";
		$sql .= " ON s.`seafarer_code` = st.`seafarer_code` ";
		$sql .= " AND s.`uc_competency` = st.`uc_competency` ";
		$sql .= " AND s.`pra_pasca`= st.`pra_pasca` ";

		return $this->exec_query($sql);
	}

	function get_unexported(){
		$sql  = " SELECT s.*, p.`period` ";
		$sql .= " FROM `".$this->table_name."` s ";
		$sql .= " LEFT JOIN `tech_period` p ON p.`uc` =  s.`uc_period` ";
		$sql .= " WHERE s.is_exported = '0' ";

		return $this->exec_query($sql);
	}

	function get_list_score($uc_pukp = NULL)
	{
		$sql  = " SELECT pu.`uc`, pu.`pukp_label`, u.`upt_label`, p.`date_start`, p.`date_finish` ";
		$sql .= " FROM `tech_score` s ";
		$sql .= " LEFT JOIN `tech_status` st ON st.`uc_score` = s.`uc` ";
		$sql .= " LEFT JOIN `tech_period` p ON s.`uc_period` = p.`uc` ";
		$sql .= " LEFT JOIN `tech_upt` u ON p.`uc_upt` = u.`uc` ";
		$sql .= " LEFT JOIN `tech_pukp` pu ON u.`uc_pukp` = pu.`uc` ";
		
		if ($uc_pukp != NULL) {
			
			$sql .= " WHERE pu.`uc` = '".$uc_pukp."' ";
		}
		
		$sql .= " GROUP BY pu.`uc` ";

		// echo $sql;

		return $this->exec_query($sql);
	}

	function get_recap($uc_level = NULL, $category = NULL,$uc_pukp = NULL,$uc_upt = NULL){
		$sql   = " SELECT DISTINCT pm.`seafarer_code`, pm.`full_name` , s.`uc_competency` , c.`sequence` , c.`label` , s.`pra_pasca` , s.`is_pass` , s.`score_max` ";
		$sql  .= " FROM `tech_status` s ";
		$sql  .= " LEFT JOIN `tech_score` sc ON s.`uc_score` = sc.`uc` ";
		$sql  .= " LEFT JOIN `tech_upt` up ON sc.`uc_upt` = up.`uc` ";
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

	function get_score_upt($uc_pukp = NULL, $uc_upt = NULL, $uc_level = NULL, $pra_pasca = NULL)
	{
		$sql  = " SELECT s.* ";
		$sql .= " FROM `tech_level_participant` p ";
		$sql .= " LEFT JOIN `tech_score` s ON p.`seafarer_code` = s.`seafarer_code` ";

		if ($uc_level != NULL) {
			
			$sql .= " WHERE p.`uc_level` = '".$uc_level."' ";
		}

		if ($uc_pukp != NULL) {
			
			$sql .= " AND p.`uc_pukp` = '".$uc_pukp."' ";
		}

		if ($uc_upt != NULL) {
			
			$sql .= " AND p.`uc_upt` = '".$uc_upt."' ";
		}

		if ($pra_pasca != NULL) {
			
			$sql .= " AND s.`pra_pasca` = '".$pra_pasca."' ";
		}

		// echo $sql;

		return $this->exec_query($sql);
	}	

	function empty_temp(){
		$this->db->empty_table('tech_score_temp');
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `".$this->table_name."_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `".$this->table_name."`) ";

		return $this->exec_query($sql);
	}

	function delete_where_in($arr_uc_att_comp){
		$this->db->where_in('uc_eac', $arr_uc_att_comp);
		$this->db->delete($this->table_name);
	}
}
?>