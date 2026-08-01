<?php
class PUKP_m extends MY_Model{	
	function __construct(){
		parent::__construct();

		$this->table_name = "tech_pukp";
	}

	function get_list($limit = NULL, $offset = 0){
		$sql  = " SELECT * FROM `tech_pukp` ORDER BY `tech_pukp`.`pukp_label` ASC " ;

		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}

		return $this->exec_query($sql);
	}

    function temp_not_in_real()
    {
        $sql  = " SELECT * FROM `".$this->table_name."_temp` ";
        $sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `".$this->table_name."`) ";
        
        return $this->exec_query($sql);
    }
	
}
?>