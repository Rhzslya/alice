<?php
Class Setting_m extends MY_Model{
	function __construct(){
		parent::__construct();

		$this->table_name = "tech_setting";
		$this->db_setting = "cba_ukp_3_6";
	}

	function get_filter(){
		$sql = " SELECT *  FROM ".$this->db_setting.".`tech_setting` WHERE `parameter` LIKE 'scoring' " ;


		return $this->exec_query($sql);
	}

	function update_mode($data=NULL,$where=NULL){
		$sql = " UPDATE ".$this->db_setting.".`tech_setting` SET `value`= '".$data."' WHERE `parameter` = '".$where."' " ;


		return $this->exec_query($sql);
	}


}