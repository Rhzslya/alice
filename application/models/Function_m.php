<?php
Class Function_m extends MY_Model{
	function __construct(){
		parent::__construct();
		$this->table_name = 'tech_function';
	}

	function get_list($limit = NULL, $offset = 0)
    {
		$sql = " SELECT f.* , l.`label` AS `label_level`, l.`uc` AS `uc_level` ";
		$sql .= " FROM `".$this->table_name."` f ";
		$sql .= " LEFT JOIN `tech_level` l ON f.`uc_level` = l.`uc`  ";

		$sql .= " ORDER BY l.`label` ASC, f.`label` ASC  " ;
		
		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}

		//echo $sql;
		return $this->exec_query($sql);
	}

    function temp_not_in_real()
    {
        $sql  = " SELECT * FROM `".$this->table_name."_temp` ";
        $sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `".$this->table_name."`) ";
        
        return $this->exec_query($sql);
    }


}