<?php
class Upt_m extends MY_Model{	
	function __construct(){
		parent::__construct();

		$this->table_name = "tech_upt";
	}

	function get_list($search = NULL, $limit = NULL, $offset = 0)
    {
		$sql  = " SELECT u.*, p.`pukp_label` " ;
		$sql .= " FROM `tech_upt` u" ;
		$sql .= " LEFT JOIN `tech_pukp` p ON u.`uc_pukp` = p.`uc` " ;
		if ($search != NULL) {
			$sql .= " WHERE u.`code` LIKE '%".$search."%' OR u.`upt_label` LIKE '%".$search."%' ";
		}
		$sql .= " ORDER BY p.`pukp_label` DESC ";
		if ($limit != NULL) {
			$sql .= " LIMIT ".$offset.", ".$limit." ";
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