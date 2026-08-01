<?php
class Schedule_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_pengajuan_ukp";
	}

	function get_list($uc = NULL, $limit = NULL, $offset = 0) {
		$sql  = " SELECT p. * , u.`upt_label` , pu.`pukp_label` ";
		$sql .= " FROM `tech_pengajuan_ukp` p ";

		// $sql .= " LEFT JOIN `tech_exam_attempt` ea ON eat.`uc_exam_attempt` = ea.`uc` ";
		// $sql .= " LEFT JOIN `tech_examination` e ON ea.`uc_exam` = e.`uc` ";
		// $sql .= " LEFT JOIN `tech_session` s ON e.`uc_session` = s.`uc` ";
		// $sql .= " LEFT JOIN `tech_day` d ON s.`uc_day` = d.`uc` ";
		// $sql .= " LEFT JOIN `tech_period` p ON d.`uc_period` = p.`uc` ";
		$sql .= " LEFT JOIN `tech_upt` u ON u.`uc` = p.`uc_upt` ";
		$sql .= " LEFT JOIN `tech_pukp` pu ON pu.`uc` = p.`uc_pukp` ";
		// $sql .= " GROUP BY p.`period` ";
		$sql .= " ORDER BY p.`date_start` DESC, p.`date_finish` DESC, p.`id` DESC ";

		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}

		// echo $sql;

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