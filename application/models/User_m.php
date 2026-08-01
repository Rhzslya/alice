<?php
Class User_m extends MY_Model{
	function __construct(){
		parent::__construct();
		$this->table_name = 'tech_user';
		
	}

	function get_list($limit = NULL, $offset = 0){
		$sql  =  " SELECT * FROM `tech_user` WHERE `category` != 3 " ;
		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}

		return $this->exec_query($sql);
	}
}