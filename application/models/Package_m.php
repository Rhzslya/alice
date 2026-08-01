<?php
Class Package_m extends MY_Model{
	function __construct(){
		parent::__construct();
		$this->table_name = 'tech_package';
	}

	function get_all_competency() {
		$sql = " SELECT l.`uc` uc_level, f.`uc` uc_function, tc.`uc` uc_competency
					FROM `tech_competency` tc
					LEFT JOIN `tech_function` f ON f.`uc` = tc.`uc_function`
					LEFT JOIN `tech_level` l ON l.`uc` = f.`uc_level` 
					ORDER BY l.`id` , f.`id` , tc.`id` ASC ";

		return $this->exec_query($sql);
	}

}
?>