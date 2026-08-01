<?php
Class Exam_comp_pack_m extends MY_Model{
	function __construct(){
		parent::__construct();

		$this->table_name = 'tech_exam_competency_package';
	}


	function empty_temp(){
		$this->db->empty_table('tech_exam_competency_package_temp');
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_exam_competency_package_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_exam_competency_package`) ";
		
		return $this->exec_query($sql);
	}

	function delete_by_pack_in($uc_package = 0) {
		$sql = " DELETE FROM `".$this->table_name."` WHERE `uc_exam_package` IN (".$uc_package.") ";

		return $this->exec_query($sql);
	}

	//backup per day
	function get_exam_com_pack($exam_ucp){
		$sql = " SELECT * FROM `tech_exam_competency_package` WHERE `uc_exam_package` IN (".$exam_ucp.") " ;

		return $this->exec_query($sql);
	}		


}