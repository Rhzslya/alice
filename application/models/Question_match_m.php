<?php
Class Question_match_m extends MY_Model{
	function __construct(){
		parent::__construct();

		$this->table_name = 'tech_question_match';
	}

	function get_by_question($uc_question = 0) {
		$sql  = " SELECT * FROM `".$this->table_name."` WHERE `uc_question` IN (".$uc_question.") ";

		return $this->exec_query($sql);
	}


}