<?php
class Period_participant_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_period_participant";
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_period_participant_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_period_participant`) ";
		
		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_period_participant_temp');
	}

	function get_in_temp(){
        $sql = " SELECT * FROM `tech_period_participant_temp` ";

        return $this->exec_query($sql);
    }

    function get_uc_dikpar_in_period($uc_period, $sea_ucs) {
    	$sql = " SELECT * FROM `".$this->table_name."` WHERE `uc_period` = '".$uc_period."' AND `seafarer_code` IN (".$sea_ucs.") ";

    	return $this->exec_query($sql);	
    }
}
?>