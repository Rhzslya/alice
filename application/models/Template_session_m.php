<?php
class Template_session_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_template_session";
	}

	function get_add_exam($uc_session = NULL){
		$sql  = " SELECT tss.* , ts.`uc` as `uc_template`, ts.`uc_level`, td.`day` , ts.`template` , ts.`pra_pasca`" ;
		$sql .= " FROM `tech_template_session` tss" ;
		$sql .= " LEFT JOIN `tech_template_day` td ON td.`uc` = tss.`uc_day` " ;
		$sql .= " LEFT JOIN `tech_template_schedule` ts ON ts.`uc` = td.`uc_template`" ;

		if ($uc_session != NULL) {
			$sql .= " WHERE tss.`uc` = '".$uc_session."' " ;
		}

		return $this->exec_query($sql);
	}

	function delete_child_session($table = NULL, $uc = "") {
		$sql = " DELETE FROM `".$table."` WHERE `uc` IN (".$uc.") ";
		
		return $this->exec_query($sql);
	}		

	function get_session_on_template($uc_template){
		$sql  = " SELECT * FROM `tech_template_session` ";
		$sql .= " WHERE `uc_day` IN ";
		$sql .= " (SELECT `uc` FROM `tech_template_day` WHERE `uc_template` = '".$uc_template."') ";
		$sql .= " ORDER BY `id` ASC ";

		return $this->exec_query($sql);
	}	
}
?>