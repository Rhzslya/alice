<?php
Class Exam_question_m extends MY_Model{
	function __construct(){
		parent::__construct();
		$this->table_name = 'tech_exam_question';
	}

	function empty_temp(){
		$this->db->empty_table('tech_exam_question_temp');
	}

	function temp_not_in_real(){
		$sql  = " SELECT * FROM `tech_exam_question_temp` ";
		$sql .= " WHERE `uc` NOT IN (SELECT `uc` FROM `tech_exam_question`) ";
		
		return $this->exec_query($sql);
	}

    function get_all_temp() {
        $sql  = " SELECT * FROM `tech_exam_question_temp` ";
        
        return $this->exec_query($sql);   
    }	

    // function get_exam_question($uc_question = NULL, $uc_exam_package = NULL, $uc_competency = NULL){
    //     $sql  = " SELECT eq.`uc_exam_package`, eq.`uc`, q.`question_title_en` , q.`question_text_en`, q.`question_title_in` , q.`question_text_in` , e.`exam_code`, e.`pra_pasca` , q.`att_file` , q.`att_type` , c.`label` AS `label_com` , c.`sequence` , q.`uc_competency` ,  p.`code_package` , p.`uc` AS `uc_package`  "  ;
    //     $sql .= " , p.`uc` AS `uc_package`, eo.`uc` AS `ucop`, ec.`uc_competency` AS `ucco`  " ;
    //     $sql .= " , eo.`id` AS `qo_uc`, eo.`option_text_en`, eo.`option_text_in`, eo.`uc_exam_question`, eo.`is_correct` " ;
    //     $sql .= " FROM `tech_exam_question` eq "  ;
    //     $sql .= " LEFT JOIN `tech_examination` e ON e.`uc` = eq.`uc_exam` "  ;
    //     $sql .= " LEFT JOIN `tech_question` q ON q.`uc` = eq.`uc_question` "  ;
    //     $sql .= " LEFT JOIN `tech_competency` c ON c.`uc` = q.`uc_competency` "  ;
    //     $sql .= " LEFT JOIN `tech_exam_competency_package` ec ON ec.`uc_exam_package` = eq.`uc_exam_package` " ;
    //     $sql .= " AND ec.`uc_competency` = q.`uc_competency` " ;
    //     $sql .= " LEFT JOIN `tech_package` p ON p.`uc` = ec.`uc_package`" ;
    //     $sql .= " LEFT JOIN `tech_exam_options` eo ON eo.`uc_exam_question` = eq.`uc` " ;

    //     if ($uc_question != NULL) {
    //         $sql .= " WHERE eq.`uc` IN (".$uc_question.") " ;
    //         $sql .= " AND eq.`uc_exam_package` = '".$uc_exam_package."' " ;
    //         $sql .= " AND ec.`uc_competency` = '".$uc_competency."' " ; 
    //     }

    //     $sql .= " ORDER BY eq.`uc` ASC " ;

    //     echo $sql;

    //     return $this->exec_query($sql);
    // }	

    /*function get_exam_question($uc_exam = 0, $uc_question = 0){
        $sql  = " SELECT e.`exam_code`, eq.* ";
        $sql .= " FROM `tech_examination` e ";
        $sql .= " LEFT JOIN `tech_exam_question` eq ";
        $sql .= " ON eq.`uc_exam` = e.`uc` ";
        $sql .= " LEFT JOIN `tech_exam_options` eo ";
        $sql .= " ON eo.`uc_exam_question` = eq.`uc` ";
        $sql .= " WHERE e.`uc` = '".$uc_exam."' ";
        $sql .= " AND eq.`uc` IN (".$uc_question.") ";
        
        return $this->exec_query($sql);  
    }*/

    //backup per day
    function get_ex_quest($exam_ucs){
        $sql = " SELECT * FROM `tech_exam_question` WHERE `uc_exam` IN (".$exam_ucs.") " ;

        return $this->exec_query($sql);
    }      

    public function get_question_in($uc_questions = 0, $uc_exam = NULL) {
        $sql  = " SELECT q.* ";
        $sql .= " FROM (SELECT q.*, eo.`uc` AS `eo_uc`, eo.`is_correct` ";
            $sql .= " FROM `tech_exam_question` q ";
            $sql .= " LEFT JOIN `tech_exam_options` eo  ";
            $sql .= " ON eo.`uc_exam_question` = q.`uc` ";
            $sql .= " AND eo.`is_correct` = '1' ";
            if ($uc_exam != NULL) {
                $sql .= " AND eo.`uc_exam` = '".$uc_exam."' ";
            }
            $sql .= " WHERE q.`uc` IN (".$uc_questions.")  ";
        $sql .= " ) q ";
        
        $sql .= " GROUP BY q.`uc` ";
        $sql .= " ORDER BY FIELD(q.`uc`, ".$uc_questions.") ";
        //echo $sql;  
        return $this->exec_query($sql);
    }

    public function get_question_in_regex($uc_questions = 0, $uc_exam = NULL) {
        $sql  = " SELECT q.* ";
        $sql .= " FROM (SELECT q.*, eo.`uc` AS `eo_uc`, eo.`is_correct` ";
            $sql .= " FROM `tech_exam_question` q ";
            $sql .= " LEFT JOIN `tech_exam_options` eo  ";
            $sql .= " ON eo.`uc_exam_question` = q.`uc` ";
            $sql .= " AND eo.`is_correct` = '1' ";
            if ($uc_exam != NULL) {
                $sql .= " AND eo.`uc_exam` = '".$uc_exam."' ";
            }
            $sql .= " WHERE q.`uc` REGEXP ".$uc_questions."  ";
        $sql .= " ) q ";
        
        $sql .= " GROUP BY q.`uc` ";
        $sql .= " ORDER BY FIELD(q.`uc`, ".$uc_questions.") ";
        //echo $sql;  
        return $this->exec_query($sql);
    }

    public function get_question_in_emer($uc_questions = 0, $uc_exam) {
        $sql  = " SELECT q.* ";
        $sql .= " FROM (SELECT q.*, eo.`uc` AS `eo_uc`, eo.`is_correct` ";
            $sql .= " FROM `tech_question` q ";
            $sql .= " LEFT JOIN `tech_question_options` eo  ";
            $sql .= " ON eo.`uc_question` = q.`uc` ";
            $sql .= " AND eo.`is_correct` = '1' ";
            $sql .= " WHERE q.`uc` IN (".$uc_questions.")  ";
        $sql .= " ) q ";
        
        $sql .= " GROUP BY q.`uc` ";
        $sql .= " ORDER BY FIELD(q.`uc`, ".$uc_questions.") ";
        //echo $sql;
        return $this->exec_query($sql);
    }

    public function get_question_in_for_regen($uc_questions = 0) {
        $sql  = " SELECT q.* ";
        $sql .= " FROM (SELECT q.*, eo.`is_correct` ";
            $sql .= " FROM `tech_question` q ";
            $sql .= " LEFT JOIN `tech_question_options` eo  ";
            $sql .= " ON eo.`uc_question` = q.`uc` ";
            $sql .= " AND eo.`is_correct` = '1' ";
            $sql .= " WHERE q.`uc` IN (".$uc_questions.")  ";
            //$sql .= " ORDER BY eo.`is_correct` DESC ";
        $sql .= " ) q ";
        $sql .= " GROUP BY q.`uc` ";
        $sql .= " ORDER BY FIELD(q.`uc`, ".$uc_questions.") ";
        echo "<br /> ".$sql;
        return $this->exec_query($sql);
    }

}