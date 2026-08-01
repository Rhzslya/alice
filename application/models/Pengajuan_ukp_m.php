<?php
class Pengajuan_ukp_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_pengajuan_ukp";
	}

	function get_detail($uc){
		$sql  = " SELECT p. * , u.`upt_label` , pu.`pukp_label`, pu.`uc` AS `uc_pukp` , u.`uc` AS `uc_upt` ";
		$sql .= " FROM `tech_pengajuan_ukp` p ";
		$sql .= " LEFT JOIN `tech_upt` u ON p.`uc_upt` = u.`uc` ";
		$sql .= " LEFT JOIN `tech_pukp` pu ON u.`uc_pukp` = pu.`uc` ";
		$sql .= " WHERE p.`uc` = '".$uc."' ";
		
		return $this->exec_query($sql);
	}

	function get_trb_schedule() {
		$sql  = " SELECT s.`uc`, u.`upt_label` , pu.`pukp_label` , s.`date_start` , s.`date_finish`
					FROM `tech_period` p
					LEFT JOIN `tech_pengajuan_ukp` s ON s.`uc` = p.`uc_ukp`
					LEFT JOIN `tech_upt` u ON u.`uc` = s.`uc_upt`
					LEFT JOIN `tech_pukp` pu ON pu.`uc` = s.`uc_pukp`
					WHERE p.`pra_pasca` = '4'  ";

		return $this->exec_query($sql);			
	}

	function get_detail_info($uc = NULL){
		$sql = "
			SELECT pu.* , u.`upt_label`, p.`pukp_label`
			FROM `tech_pengajuan_ukp` pu 
			LEFT JOIN `tech_upt` u ON u.`uc` = pu.`uc_upt`
			LEFT JOIN `tech_pukp` p ON p.`uc` = pu.`uc_pukp`
			WHERE pu.`uc` = '".$uc."'
		";

		return $this->exec_query($sql);
	}

	function get_days($uc) {
		$sql = " SELECT DISTINCT(`date`) 
					FROM `tech_day` 
					WHERE `uc_period` IN 
					( SELECT `uc` FROM `tech_period` WHERE `uc_ukp` = '".$uc."' ) 
					ORDER BY `date` ASC ";

		return $this->exec_query($sql);			
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_pengajuan_ukp_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_pengajuan_ukp`) ";
		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_pengajuan_ukp_temp');
	}
}
?>