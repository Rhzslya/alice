<?php
Class Competency_trb_m extends MY_Model{
	function __construct(){
		parent::__construct();
		$this->table_name = 'tech_competency_trb';
	}

	function get_exist($uc_level = 0, $comp_name = "") {
        $sql = " SELECT * FROM `".$this->table_name."` WHERE `uc_level` = '".$uc_level."' AND `label` LIKE '%".$comp_name."%' ";

        return $this->exec_query($sql);
    }
}
?>