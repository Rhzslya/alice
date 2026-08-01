<?php
Class Question_option_m extends MY_Model{
	function __construct(){
		parent::__construct();

		$this->table_name = 'tech_question_options';
	}

	function get_by_question($uc_question = 0) {
		$sql  = " SELECT * FROM `".$this->table_name."` WHERE `uc_question` IN (".$uc_question.") ";

		return $this->exec_query($sql);
	}

	function temp_not_in_real(){
        $sql  = " SELECT * FROM `tech_question_options_temp` ";
        $sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_question_options`) ";
        
        return $this->exec_query($sql);
    }

	//  EMERGENCY FUNCTION DURING DEV & DEBUGING
    function get_all_temp() {
        $sql  = " SELECT * FROM `tech_question_options_temp` ";
        
        return $this->exec_query($sql);   
    }
}