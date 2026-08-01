<?php
Class Exam_match_m extends MY_Model{
	function __construct(){
		parent::__construct();
		$this->table_name = 'tech_exam_match';
	}

	function empty_temp(){
		$this->db->empty_table('tech_exam_match_temp');
	}	
	
	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_exam_match_temp` ";
		$sql .= " WHERE `uc_exam_question` NOT IN (SELECT `uc_exam_question` FROM `tech_exam_match`) ";
		
		return $this->exec_query($sql);
	}

	//backup per day
    function get_ex_match($exam_quc){
        $sql = " SELECT * FROM `tech_exam_match` WHERE `uc_exam_question` IN (".$exam_quc.") " ;

        return $this->exec_query($sql);
    } 		

}