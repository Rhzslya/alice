<?php
class Package_question_m extends MY_Model{	
	function __construct(){
		parent::__construct();

		$this->table_name = "tech_package_question";
	}

	function get_question_picked($uc_question = NULL,$limit = NULL,$offset = 0){
		$sql  = " SELECT q.*  ";
		$sql .= " FROM `tech_question` q ";
		$sql .= " WHERE q.`is_exist` = '1' ";

		if ($uc_question != NULL) {
			
			$sql .= " AND q.`uc` IN (".$uc_question.") ";
		}

		$sql .= " ORDER by q.`id` DESC ";

		if ($limit != NULL) {
			$sql .= " LIMIT ".$offset.", ".$limit." ";
		}

		// echo $sql;

		return $this->exec_query($sql);
	}	

	function get_notin_qb($uc_competency, $uc_row_package, $limit = NULL, $offset = 0){
		$sql  = " SELECT q.*  ";
		$sql .= " FROM `tech_question` q  ";
		$sql .= " WHERE q.`is_exist` = '1' ";
		
		if ($uc_competency  != NULL) {
			$sql .= " AND q.`uc_competency` = '".$uc_competency ."' ";	
		}

		$sql .= " AND q.`uc` NOT IN (".$uc_row_package.") ";

		$sql .= " ORDER by q.`id` DESC ";

		if ($limit != NULL) {
			$sql .= " LIMIT ".$offset.", ".$limit." ";
		}

		// echo $sql;

		return $this->exec_query($sql);
	}

	function empty_temp(){
		$this->db->empty_table('tech_package_question_temp');
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_package_question_temp` ";
		$sql .= " WHERE `uc_package` NOT IN (SELECT `uc_package` FROM `tech_package_question`) ";
		
		return $this->exec_query($sql);
	}	

	//backup per day
    function get_pack_quest($uc_package){
        $sql = " SELECT * FROM `tech_package_question` WHERE `uc_package` IN (".$uc_package.") " ;

        return $this->exec_query($sql);
    } 	 	


}
?>  