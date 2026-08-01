<?php
Class Question_m extends MY_Model{
	function __construct(){
		parent::__construct();

		$this->table_name = 'tech_question';
	}

	function get_list($filter = NULL,$limit = NULL, $offset = 0){
		$sql  =" SELECT q.* , le.`label`, co.`label` as 'label_com', f.`label` as 'label_fun', qt.`type_name` ";
		$sql .=" FROM `tech_question` q ";
		$sql .=" LEFT JOIN `tech_level` le ON q.`uc_level` = le.`uc` ";
		$sql .=" LEFT JOIN `tech_function` f ON q.`uc_function` = f.`uc` " ;
		$sql .=" LEFT JOIN `tech_competency` co ON q.`uc_competency` = co.`uc` ";
		$sql .=" LEFT JOIN `tech_question_type` qt ON q.`question_type` = qt.`id` ";
		$sql .=" WHERE q.`is_exist` = '1' " ;

        if (@$filter['uc_level'] != NULL) {
            $sql.=" AND q.uc_level = '".$filter['uc_level']."' ";
        }
        
        if (@$filter['uc_function'] != NULL) {
            $sql.=" AND q.uc_function = '".$filter['uc_function']."' ";
        }

        if (@$filter['uc_competency'] != NULL) {
            $sql.=" AND q.uc_competency = '".$filter['uc_competency']."' ";
        }

        if (@$filter['key'] != NULL) {
            $sql.=" AND (q.`question_text_en` LIKE '%".$filter['key']."%' OR q.`question_text_in` LIKE '%".$filter['key']."%')";
        }


        // ANT III F1 Plan and conduct sess 1 & 2 , ATT III F1 operate main sess 1 & 2
        // $sql .= " AND uc_competency NOT IN ('14-96553-20','28-97005-10') "; 

        $sql .= " ORDER BY q.`id` DESC ";

		if ($limit != NULL) {
			$sql .= "  LIMIT ".$offset.", ".$limit." ";
		}

        return $this->exec_query($sql);
	}

    function get_count($filter = NULL,$limit = NULL, $offset = 0){
        $sql  =" SELECT COUNT(q.`id`) AS `count_total` ";
        $sql .=" FROM `tech_question` q ";
        $sql .=" WHERE q.`is_exist` = '1' " ;

        if (@$filter['uc_level'] != NULL) {
            $sql.=" AND q.uc_level = '".$filter['uc_level']."' ";
        }
        
        if (@$filter['uc_function'] != NULL) {
            $sql.=" AND q.uc_function = '".$filter['uc_function']."' ";
        }

        if (@$filter['uc_competency'] != NULL) {
            $sql.=" AND q.uc_competency = '".$filter['uc_competency']."' ";
        }

        if (@$filter['key'] != NULL) {
            $sql.=" AND (q.`question_text_en` LIKE '%".$filter['key']."%' OR q.`question_text_in` LIKE '%".$filter['key']."%')";
        }

        $sql .= " ORDER BY q.`id` DESC ";

        if ($limit != NULL) {
            $sql .= "  LIMIT ".$offset.", ".$limit." ";
        }

        return $this->exec_query($sql);
    }


    function get_with_option($unique_code = NULL){
        $sql  = " SELECT q.*,qo.`option_text_in`,qo.`option_text_en`,qo.`id` AS `option_id`, qo.`is_correct`, ";
        $sql .= " qo.`att_type` as `att_type_option`,qo.`att_file` as `att_file_option`, qo.`uc_question`, c.`label` ";
        $sql .= " FROM `tech_question` q ";
        $sql .= " LEFT JOIN `tech_question_options` qo ON q.`uc` = qo.`uc_question` ";
        $sql .= " AND qo.`uc_question` = '".$unique_code."' ";
        $sql .= " LEFT JOIN `tech_competency` c ON q.`uc_competency` = c.`uc` ";
        $sql .= " WHERE q.`uc` = '".$unique_code."' ";
        
        return $this->exec_query($sql);
    }

    function get_question_pdf($uc_level = NULL, $uc_function = NULL, $uc_competency = NULL){
        $sql  = " SELECT q.`uc`, q.`question_text_en` , q.`question_text_in` , q.`att_file` ,q.`att_type` , qo.`option_text_en`, qo.`option_text_in`, qo.`uc_question`, qo.`is_correct` " ;
        $sql .= " FROM `tech_question` q " ;
        $sql .= " LEFT JOIN `tech_question_options` qo ON q.`uc` = qo.`uc_question` " ;

        if ($uc_competency != NULL) {
            $sql .= " WHERE q.`uc_level` = '".$uc_level."'  " ;
            $sql .= " AND q.`uc_function` = '".$uc_function."' " ;
            $sql .= " AND q.`uc_competency` = '".$uc_competency."' " ; 
        }

        $sql .= " ORDER BY q.`id` DESC ";
        
        return $this->exec_query($sql);
    }

    function get_info($uc_level,$uc_function,$uc_competency){
        $sql  = " SELECT l.`label` AS `label_lev`, f.`label` AS `label_fun`, c.`label` AS `label_com` " ;
        $sql .= " FROM `tech_level` l " ;
        $sql .= " LEFT JOIN `tech_function` f ON f.`uc_level` = l.`uc` " ;
        $sql .= " LEFT JOIN `tech_competency` c ON c.`uc_function` = f.`uc` " ;
        $sql .= " WHERE l.`uc` = '".$uc_level."' " ;
        $sql .= " AND f.`uc` = '".$uc_function."' " ;
        $sql .= " AND c.`uc` = '".$uc_competency."' " ;

        return $this->exec_query($sql);

    }

    function get_filtered_question($uc_level = NULL, $uc_function = NULL, $uc_competency = NULL) {
        $sql = " SELECT * FROM `".$this->table_name."` ";
        if ($uc_level != NULL) {
            $sql .= " WHERE `uc_level` = '".$uc_level."' ";
            if ($uc_function != NULL) {
                $sql .= " AND `uc_function` = '".$uc_function."' ";
                if ($uc_competency != NULL) {
                    $sql .= " AND `uc_competency` = '".$uc_competency."' ";
                }
            }
        }

        return $this->exec_query($sql);
    }

    function get_randomize($uc_competency = NULL, $limit = 0) {
        $sql  = " SELECT * FROM `".$this->table_name."` ";
        $sql .= " WHERE `is_exist` = '1' AND `uc_competency` = '".$uc_competency."'";
        $sql .= " ORDER BY RAND() LIMIT ".$limit;
        
        return $this->exec_query($sql);
    }
    
    function get_in_question($ucs){
        $this->db->where_in('uc', $ucs);
        $this->db->order_by("id", "desc");
        return $this->db->get($this->table_name);
    }

    function get_not_in($uc_competency, $ucs_arr){
        $sql  = " SELECT * FROM `".$this->table_name."` ";
        $sql .= " WHERE `uc_competency` = '".$uc_competency."' ";
        $sql .= " AND `is_exist` = '1' ";
        if ($ucs_arr != NULL) {
            $sql .= " AND `uc` NOT IN ('" . implode( "', '" , $ucs_arr ) . "') ";
        }
        $sql .= " ORDER BY `id` DESC";


        return $this->exec_query($sql);
    }

    function get_search($key = NULL, $uc_competency = NULL, $uc_picked = NULL, $type = "bank"){
        $sql  = " SELECT * FROM `tech_question` ";

        if (($key != NULL) || ($uc_competency != NULL) || ($uc_picked != NULL) ) {
            $sql .= " WHERE ";

            $multi = FALSE;

            if ($uc_competency != NULL) {
                $sql .= " `uc_competency` = '".$uc_competency."' ";

                $multi = TRUE;
            }

            if ($key != NULL) {
                if ($multi) {
                    $sql .= " AND ";
                }

                $sql .= " (
                        `question_title_en` LIKE '%".$key."%'
                        OR `question_title_in` LIKE '%".$key."%'
                        OR `question_text_en` LIKE '%".$key."%'
                        OR `question_text_in` LIKE '%".$key."%'
                        ) ";
            }

            if ($uc_picked != NULL) {
                if ($multi) {
                    $sql .= " AND ";
                }

                if ($type == "bank") {
                    $sql .= " `uc` NOT IN ('" . implode( "', '" , $uc_picked ) . "') ";
                }
                else {
                    $sql .= " `uc` IN ('" . implode( "', '" , $uc_picked ) . "') ";
                }
            }
        }

        return $this->exec_query($sql);
    }

    function temp_not_in_real(){
        $sql  = " SELECT * FROM `tech_question_temp` ";
        $sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_question`) ";
        
        return $this->exec_query($sql);
    }

    //  EMERGENCY FUNCTION DURING DEV & DEBUGING
    function get_all_temp() {
        $sql  = " SELECT * FROM `tech_question_temp` ";
        
        return $this->exec_query($sql);   
    }

}