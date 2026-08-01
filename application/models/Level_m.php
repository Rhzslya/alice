<?php
Class Level_m extends MY_Model{

	function __construct(){
		parent::__construct();
		$this->table_name = 'tech_level';
	}

	function get_cek_level()
    {
		$sql = "
				SELECT l.* , ts.`template`
				FROM `tech_level` l
				LEFT JOIN `tech_template_schedule` ts ON ts.`uc_level` = l.`uc` 
				ORDER BY `l`.`label` ASC
		" ;

		return $this->exec_query($sql);
	}

    function temp_not_in_real()
    {
        $sql  = " SELECT * FROM `".$this->table_name."_temp` ";
        $sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `".$this->table_name."`) ";
        
        return $this->exec_query($sql);
    }
}