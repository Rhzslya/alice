<?php
Class Competency_m extends MY_Model{
	function __construct(){
		parent::__construct();
		$this->table_name = 'tech_competency';
	}

	function get_list($level = NULL, $limit = NULL, $offset = 0){
		$sql = " SELECT l.`uc` AS `uc_level`, l.`label` AS `label_level`, f.`uc` AS `uc_function`, f.`label` AS `label_fungsi`, f.`label_long`, c.* ";
		$sql .= " FROM `".$this->table_name."` c ";
		$sql .= " LEFT JOIN `tech_function` f ON c.`uc_function` = f.`uc` ";
		$sql .= " LEFT JOIN `tech_level` l ON f.`uc_level` = l.`uc`";
		if ($level != NULL) {
			$sql .= " WHERE l.`uc` = '".$level."' ";
		}
		
		$sql .= " ORDER BY l.`label` ASC, f.`label` ASC, c.`sequence` ASC  " ;
		
		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}

		return $this->exec_query($sql);
	}

	function get_detail_competency($uc){
		$sql  = " SELECT c.`label` AS `competency_name`, c.`sequence`, c.`pack_amt` ";
		$sql .= " , f.`label` AS `function_name`, l.`uc` AS `uc_level`, l.`label` AS `level`, l.`major` ";
		$sql .= " FROM `tech_competency` c ";
		$sql .= " JOIN `tech_function` f ";
		$sql .= " ON f.`uc` = c.`uc_function` ";
		$sql .= " JOIN `tech_level` l ";
		$sql .= " ON l.`uc` = f.`uc_level` ";
		$sql .= " WHERE c.`uc` = '".$uc."' ";

		return $this->exec_query($sql);
	}

	function get_competency($uc_function = NULL, $category){
		$sql  = " SELECT c.* , f.`label` `label_function` , l.`label` `label_level` ";
		$sql .= " FROM `tech_competency` c ";
		$sql .= " LEFT JOIN `tech_function` f ON f.`uc` = c.`uc_function` ";
		$sql .= " LEFT JOIN `tech_level` l ON l.`uc` = f.`uc_level` ";

		if ($uc_function != NULL) {
			$sql .= " WHERE c.`uc_function` = '".$uc_function."' ";
		}

		if ($category != NULL) {
			$sql .= " AND c.`category` IN ".$category." ";
		}
		
		$sql .= " ORDER BY `c`.`sequence` ASC ";

		return $this->exec_query($sql);
	}

	function get_competency_score($uc_level = NULL, $category = NULL, $cat = NULL)
	{
		$sql  = " SELECT c.* , f.`label` `label_function` , l.`label` `label_level` ";
		$sql .= " FROM `".$this->table_name."` c ";
		$sql .= " LEFT JOIN `tech_function` f ON f.`uc` = c.`uc_function` ";
		$sql .= " LEFT JOIN `tech_level` l ON l.`uc` = f.`uc_level` ";

		if ($uc_level != NULL) {
			$sql .= " WHERE l.`uc` = '".$uc_level."' ";
		}

		if ($category != NULL) {
			$sql .= " AND c.`category` IN ".$category." ";
		}

		$sql .= " ORDER BY `c`.`sequence` ASC ";

		// // ANT III menghilangkan season 1 dan season 2
		// if ($uc_level == '49-85742-23' && $cat == '3') {

		// 	// Remove competency season 1 (pra) and competency season 1 (pasca)
		// 	$sql .= " AND c.`uc` NOT IN ('18-36625-65', '14-40926-36') ";
		// }
		
		// // ATT III menghilangkan season 1 dan season 2
		// else if ($uc_level == '36-50872-29' && $cat == '3') {
		// 	// Remove competency season 1 (pra) and competency season 1 (pasca)
		// 	$sql .= " AND c.`uc` NOT IN ('28-97005-10', '49-40433-52') ";	
		// }

		// if ($cat == 3) {
			
		// 	// Order berdasarkan category
		// 	$sql .= " ORDER BY c.`category`,c.`sequence` ASC ";
		// }
		// else{

		// 	$sql .= " ORDER BY `c`.`sequence` ASC ";
		// }

		// echo $sql;

		return $this->exec_query($sql);
	}

	function get_all_random_question(){
		
	}

    function temp_not_in_real()
    {
        $sql  = " SELECT * FROM `".$this->table_name."_temp` ";
        $sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `".$this->table_name."`) ";
        
        return $this->exec_query($sql);
    }
}