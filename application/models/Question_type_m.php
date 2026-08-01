<?php
Class Question_type_m extends MY_Model{
	function __construct(){
		parent::__construct();

		$this->table_name = 'tech_question_type';
	}
}