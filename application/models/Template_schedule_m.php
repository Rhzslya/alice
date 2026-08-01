<?php
class Template_schedule_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_template_schedule";
	}

	function get_list($category = 0,$limit = NULL, $offset = NULL){
		$sql  = " SELECT ts.* , l.`label` " ;
		$sql .= " FROM `tech_template_schedule` ts " ;
		$sql .= " LEFT JOIN `tech_level` l ON l.`uc` = ts.`uc_level` " ;
		$sql .= " WHERE ts.`category` = ".$category." " ;

		$sql .= " ORDER BY l.`label` ASC " ;

		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}
		
		return $this->exec_query($sql);
	}

	function get_manage_template($uc = NULL){
		$sql  = " SELECT ts.* , td.`uc` as `uc_day` , td.`day` " ;
		$sql .= " , tss.`uc` as `uc_session` " ;
		$sql .= " , tte.`uc` AS 'uc_exam', tte.`exam_code`, tte.`pra_pasca` ";
		$sql .= " , l.`label` AS `level_name`, f.`label` AS `function_name` ";
		$sql .= " FROM `tech_template_schedule` ts " ;
		$sql .= " LEFT JOIN `tech_template_day` td ON td.`uc_template` = ts.`uc` " ;
		$sql .= " LEFT JOIN `tech_template_session` tss ON tss.`uc_day` = td.`uc` " ;
		$sql .= " LEFT JOIN `tech_template_examination` tte ON tte.`uc_session` = tss.`uc` ";
		$sql .= " LEFT JOIN `tech_level` l ON tte.`uc_level` = l.`uc` ";
		$sql .= " LEFT JOIN `tech_function` f ON tte.`uc_function` = f.`uc`  ";

		if ($uc != NULL) {
			$sql .= "  WHERE ts.`uc` = '".$uc."' ";
		}		

		$sql .= " ORDER BY td.`day` ASC, tss.`id` ASC , tte.`id` ASC ";

		// echo $sql;
		return $this->exec_query($sql);
	}

}
?>