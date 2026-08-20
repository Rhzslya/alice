<?php
class Backup_m extends MY_Model {
	function __construct() {
		parent::__construct();

		$this->table_name = "tech_day";
	}

	/**
	 * Builds the aggregated INSERT-statement blob for one exam day (uc_day),
	 * used both by Report::backup() (single-day export form) and by
	 * Schedule::export_daily() (whole-UKP-date export, looped per day).
	 */
	function generate_query($uc_day) {
		$uc_period = NULL;
		$ex_pack = "";
		$ex_com_pack = "";

			$this->load->model('day_m');
			$query = $this->day_m->get_filtered(array('uc' => $uc_day), "id", "ASC");
			$q_day = "";
			if ($query->num_rows() > 0) {
				$value = "";
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->date."'),";

					$uc_period = $res->uc_period;
				}
				$value = substr_replace($value, '', -1);

				$q_day .= " INSERT INTO `tech_day_temp` (`uc`, `uc_period`, `date`) VALUES ";
				$q_day .= $value."; ";
			}

			// echo "<br />".$q_day;

			//	Generate Query of PERIOD Data
			$this->load->model('period_m');
			$query = $this->period_m->get_filtered(array('uc' => $uc_period));
			$q_period = "";
			if ($query->num_rows() > 0) {
				$result = $query->row();

				$value = "('".$result->uc."', '".$result->period."', '".$result->date_start."', '".$result->date_finish."' , '".$result->uc_upt."')";

				$q_period .= " INSERT INTO `tech_period_temp` (`uc`, `period`, `date_start`, `date_finish`, `uc_upt`) VALUES ";
				$q_period .= $value."; ";
			}

			// echo "<br />".$q_period;

			//	Generate Query of SESSION Data
			$this->load->model('session_m');
			$query = $this->session_m->get_session_on_day($uc_day);
			$q_sess = "";
			if ($query->num_rows() > 0) {
				$value 		= "";
				$sess_ucs 	= "";
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."', '".$res->uc_day."', '".$res->add_time."', '".$res->is_active."'),";

					$sess_ucs .= "'".$res->uc."',";
				}
				$value 		= substr_replace($value, '', -1);

				$sess_ucs 	= substr_replace($sess_ucs, '', -1);

				$q_sess .= " INSERT INTO `tech_session_temp` (`uc`, `uc_day`, `add_time`, `is_active`) VALUES ";
				$q_sess .= $value."; ";
			}

			// echo "<br />".$q_sess;

			//	Generate Query of EXAMINATION Data
			$q_exam = "";
			if (isset($sess_ucs)) {
				$this->load->model('examination_m');
				$query = $this->examination_m->get_exam_on_session($sess_ucs);

				if ($query->num_rows() > 0) {
					$value 		= "";
					$exam_ucs	= "";
					foreach ($query->result() as $res) {
						$value .= "('".$res->uc."', '".$res->exam_code."', '".$res->duration."', '".$res->time_create."', '".$res->is_accessed."', '".$res->is_active."', '".$res->uc_session."', '".$res->uc_level."', '".$res->uc_function."', '".$res->has_attempted."', '".$res->is_language."', '".$res->show_score."', '".$res->pra_pasca."', '".$res->is_exist."'),";

						$exam_ucs .= "'".$res->uc."',";
					}
					$value 		= substr_replace($value, '', -1);

					$exam_ucs 	= substr_replace($exam_ucs, '', -1);

					$q_exam .= " INSERT INTO `tech_examination_temp` (`uc`, `exam_code`, `duration`, `time_create`, `is_accessed`, `is_active`, `uc_session`, `uc_level`, `uc_function`, `has_attempted`, `is_language`, `show_score`, `pra_pasca`, `is_exist`) VALUES ";
					$q_exam .= $value."; ";
				}
			}

			// echo "<br />".$q_exam;

			//	Generate Query of EXAMINATION COMPETENCY Data
			$q_xcomp = "";
			if (isset($sess_ucs)) {
				$this->load->model('exam_competency_m');
				$query = $this->exam_competency_m->get_exam_competency($exam_ucs);

				if ($query->num_rows() > 0) {
					$value 		= "";
					foreach ($query->result() as $res) {
						$value .= "('".$res->uc."', '".$res->uc_exam."', '".$res->uc_competency."'),";
					}
					$value 		= substr_replace($value, '', -1);

					$q_xcomp .= " INSERT INTO `tech_exam_competency_temp` (`uc`, `uc_exam`, `uc_competency`) VALUES ";
					$q_xcomp .= $value."; ";
				}
			}

			// echo "<br />".$q_xcomp;

			//	Generate Query of PARTICIPANT PERIOD Data
			$this->load->model('period_participant_m');
			$query = $this->period_participant_m->get_filtered(array('uc_period' => $uc_period), "id", "ASC");
			$q_parper = "";

			if ($query->num_rows() > 0) {
				$value 		= "";
				$uc_per 	= "";

				$i = 1;
				foreach ($query->result() as $res) {
					$uc_per .= "'".$res->seafarer_code."',";

					$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->seafarer_code."', '".$res->participant_no."', '".$res->last_active."', '".$res->is_login."'),";

					if (($i%50) == 0) {
						$value = substr_replace($value, '', -1);
						$q_parper .= " INSERT INTO `tech_period_participant_temp` (`uc`, `uc_period`, `seafarer_code`, `participant_no`, `last_active`, `is_login`) VALUES ";
						$q_parper .= $value."; ";
						$q_parper .= "\n\r \n\r";

						$value = "";
					}

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$q_parper .= " INSERT INTO `tech_period_participant_temp` (`uc`, `uc_period`, `seafarer_code`, `participant_no`, `last_active`, `is_login`) VALUES ";
					$q_parper .= $value."; ";
				}


				$uc_per 	= substr_replace($uc_per, '', -1);
			}

			// echo "<br />".$q_parper;

			//	Generate Query of PARTICIPANT Data
			$this->load->model('participant_m');
			$query = $this->participant_m->get_filtered(array('uc_period' => $uc_period), "id", "ASC");
			$q_par = "";

			if ($query->num_rows() > 0) {
				$value 		= "";
				$uc_par 	= "";

				$i = 1;
				foreach ($query->result() as $res) {
					$uc_par .= "'".$res->uc."',";

					$value .= "('".$res->uc."', '".$res->seafarer_code."', '".str_replace("'", "''", $res->full_name)."', '".str_replace("'", "''", $res->born_place)."', '".$res->born_date."', '".$res->participant_no."', '".$res->uc_period."', '".$res->uc_level."', '".$res->uc_function."', '".$res->uc_exam."'),";

					if (($i%50) == 0) {
						$value = substr_replace($value, '', -1);
						$q_par .= " INSERT INTO `tech_participant_temp` (`uc`, `seafarer_code`, `full_name`, `born_place`, `born_date`, `participant_no`, `uc_period`, `uc_level`, `uc_function`, `uc_exam`) VALUES ";
						$q_par .= $value."; ";
						$q_par .= "\n\r \n\r";

						$value = "";
					}

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$q_par .= " INSERT INTO `tech_participant_temp` (`uc`, `seafarer_code`, `full_name`, `born_place`, `born_date`, `participant_no`, `uc_period`, `uc_level`, `uc_function`, `uc_exam`) VALUES ";
					$q_par .= $value."; ";
				}


				$uc_par 	= substr_replace($uc_par, '', -1);
			}
			// echo "<br />".$q_par;

			//	Generate Query of PARTICIPANT MASTER Data
			$q_parmas = "";
			if (isset($uc_per)) {
				$this->load->model('participant_master_m');
				$query = $this->participant_master_m->get_in_period($uc_per);

				if ($query->num_rows() > 0) {
					$value 		= "";
					$parper_ucs 	= "";

					$i = 1;
					foreach ($query->result() as $res) {
						$parper_ucs .= "'".$res->uc."',";

						$value .= "('".$res->uc."', '".$res->seafarer_code."', '".str_replace("'", "''", $res->full_name)."', '".str_replace("'", "''", $res->born_place)."', '".$res->born_date."'),";

						if (($i%50) == 0) {
							$value = substr_replace($value, '', -1);
							$q_parmas .= "INSERT INTO `tech_participant_master_temp` (`uc`, `seafarer_code`, `full_name`, `born_place`, `born_date`) VALUES ";
							$q_parmas .= $value."; ";
							$q_parmas .= "\n\r \n\r";

							$value = "";
						}

						$i++;
					}

					if ($value != "") {
						$value = substr_replace($value, '', -1);
						$q_parmas .= "INSERT INTO `tech_participant_master_temp` (`uc`, `seafarer_code`, `full_name`, `born_place`, `born_date`) VALUES ";
						$q_parmas .= $value."; ";
					}


					$parper_ucs 	= substr_replace($parper_ucs, '', -1);
				}

			}

			// echo "<br />".$q_parmas;

			//	Generate Query of EXAM ATTEMPT Data
			$q_exat = "";
			if (isset($exam_ucs)) {
				$this->load->model('exam_attempt_m');
				$query = $this->exam_attempt_m->get_attempt_for_exam_in($exam_ucs);

				if ($query->num_rows() > 0) {
					$value 		= "";
					$att_ucs	= "";

					$i = 1;
					foreach ($query->result() as $res) {
						$att_ucs .= "'".$res->uc."',";

						$value .= "('".$res->uc."', '".$res->uc_exam."', '".$res->seafarer_code."', '".$res->questions."', '".$res->competency."', '".$res->keys."', '".$res->answers."', '".$res->pairs."', '".$res->is_marks."', '".$res->answer_true."', '".$res->answer_false."', '".$res->answer_result."', '".$res->time_start."', '".$res->time_finish."', '".$res->time_running."', '".$res->time_remain."', '".$res->is_notif."', '".$res->is_done."'),";

						if (($i%50) == 0) {
							$value = substr_replace($value, '', -1);
							$q_exat .= " INSERT INTO `tech_exam_attempt_temp` (`uc`, `uc_exam`, `seafarer_code`, `questions`, `competency`, `keys`, `answers`, `pairs`, `is_marks`, `answer_true`, `answer_false`, `answer_result`, `time_start`, `time_finish`, `time_running`, `time_remain`, `is_notif`, `is_done`) VALUES ";
							$q_exat .= $value."; ";
							$q_exat .= "\n\r \n\r";

							$value = "";
						}

						$i++;
					}

					if ($value != "") {
						$value = substr_replace($value, '', -1);
						$q_exat .= " INSERT INTO `tech_exam_attempt_temp` (`uc`, `uc_exam`, `seafarer_code`, `questions`, `competency`, `keys`, `answers`, `pairs`, `is_marks`, `answer_true`, `answer_false`, `answer_result`, `time_start`, `time_finish`, `time_running`, `time_remain`, `is_notif`, `is_done`) VALUES ";
						$q_exat .= $value."; ";
					}


					$att_ucs 	= substr_replace($att_ucs, '', -1);
				}
			}

			// echo "<br />".$q_exat;

			//	Generate Query of EXAM ATTEMPT COMPETENCY Data
			$q_score = "";
			if (isset($att_ucs)) {
				$this->load->model('exam_attempt_competency_m');
				$query = $this->exam_attempt_competency_m->get_score_for_attempt_in($att_ucs);

				if ($query->num_rows() > 0) {
					$value 		= "";
					$att_ucs	= "";

					$i = 1;
					foreach ($query->result() as $res) {
						$att_ucs .= "'".$res->uc."',";

						$value .= "('".$res->uc."', '".$res->uc_exam_attempt."', '".$res->uc_competency."', '".$res->seafarer_code."', '".$res->score."', '".$res->score_2."', '".$res->score_normal."'),";

						if (($i%50) == 0) {
							$value = substr_replace($value, '', -1);
							$q_score .= " INSERT INTO `tech_exam_attempt_competency_temp` (`uc`, `uc_exam_attempt`, `uc_competency`, `seafarer_code`, `score`, `score_2`, `score_normal`) VALUES ";
							$q_score .= $value."; ";
							$q_score .= "\n\r \n\r";

							$value = "";
						}

						$i++;
					}

					if ($value != "") {
						$value = substr_replace($value, '', -1);
						$q_score .= " INSERT INTO `tech_exam_attempt_competency_temp` (`uc`, `uc_exam_attempt`, `uc_competency`, `seafarer_code`, `score`, `score_2`, `score_normal`) VALUES ";
						$q_score .= $value."; ";
					}


					$att_ucs 	= substr_replace($att_ucs, '', -1);
				}

			}

			// echo "<br />".$q_score;

			//	Generate Query of EXAM PACKAGE Data

			// $ex_pack = "";
			// if (isset($exam_ucs)) {
			// 	$this->load->model('exam_package_m');
			// 	$query = $this->exam_package_m->get_exam_package($exam_ucs);

			// 	if ($query->num_rows() > 0) {
			// 		$value 		= "";
			// 		$exam_ucp   = "";
			// 		foreach ($query->result() as $res) {
			// 			$value .= "('".$res->uc."', '".$res->uc_exam."', '".$res->package_code."'),";
			// 			$exam_ucp .= "'".$res->uc."',";
			// 		}
			// 		$value 		= substr_replace($value, '', -1);
			// 		$exam_ucp 	= substr_replace($exam_ucp, '', -1);

			// 		$ex_pack .= " INSERT INTO `tech_exam_package_temp` (`uc`, `uc_exam`, `package_code`) VALUES ";
			// 		$ex_pack .= $value."; ";
			// 	}
			// }

			// echo "<br />".$ex_pack;

			//	Generate Query of EXAM COMPETENCY PACKAGE Data

			// $ex_com_pack = "";
			// $ex_uc_pack  = "" ;
			// if (isset($exam_ucp)) {
			// 	$this->load->model('exam_comp_pack_m');
			// 	$query = $this->exam_comp_pack_m->get_exam_com_pack($exam_ucp);

			// 	if ($query->num_rows() > 0) {
			// 		$value 		= "";
			// 		foreach ($query->result() as $res) {
			// 			$value    .= "('".$res->uc."', '".$res->uc_exam_package."', '".$res->uc_competency."', '".$res->uc_package."'),";
			// 			$ex_uc_pack .= "'".$res->uc_package."',";
			// 		}

			// 		$value 		= substr_replace($value, '', -1);
			// 		$ex_uc_pack = substr_replace($ex_uc_pack, '', -1);

			// 		$ex_com_pack .= " INSERT INTO `tech_exam_competency_package_temp` (`uc`, `uc_exam_package`, `uc_competency`, `uc_package`) VALUES ";
			// 		$ex_com_pack .= $value."; ";
			// 	}
			// }

			// echo "<br />".$ex_com_pack;

			//	Generate Query of EXAM QUESTION Data

				$exam_quest = "";
				if (isset($exam_ucs)) {
				$this->load->model('exam_question_m');
				$query = $this->exam_question_m->get_ex_quest($exam_ucs);


					if ($query->num_rows() > 0) {
						$value 		= "";
						$exam_quc	= "";

						$i = 1;
						foreach ($query->result() as $res) {
							$exam_quc .= "'".$res->uc."',";
							$value .= "('".$res->uc."', '".$res->uc_question."', '".$res->question_code."', '".str_replace("'", "''", $res->question_title_in)."', '".str_replace("'", "''", $res->question_title_en)."', '".str_replace("'", "''", $res->question_text_in)."', '".str_replace("'", "''", $res->question_text_en)."', '".$res->question_att_file."', '".$res->question_att_type."', '".$res->question_type."', '".$res->answer_truefalse."', '".$res->answer_multiplechoice."', '".$res->uc_exam."', '".$res->is_exist."'),";

							if (($i%50) == 0) {
								$value = substr_replace($value, '', -1);
								$exam_quest .= " INSERT INTO `tech_exam_question_temp` (`uc`, `uc_question`, `question_code`, `question_title_in`, `question_title_en`, `question_text_in`, `question_text_en`, `question_att_type`, `question_att_file`, `question_type`, `answer_truefalse`, `answer_multiplechoice`, `uc_exam`, `is_exist`) VALUES ";
								$exam_quest .= $value."; ";
								$exam_quest .= "\n\r \n\r";

								$value = "";
							}

							$i++;
						}

						if ($value != "") {
							$value = substr_replace($value, '', -1);
							$exam_quest .= " INSERT INTO `tech_exam_question_temp` (`uc`, `uc_question`, `question_code`, `question_title_in`, `question_title_en`, `question_text_in`, `question_text_en`, `question_att_type`, `question_att_file`, `question_type`, `answer_truefalse`, `answer_multiplechoice`, `uc_exam`, `is_exist`) VALUES ";
							$exam_quest .= $value."; ";
						}

						$exam_quc 	= substr_replace($exam_quc, '', -1);

					}

				}

			// echo "<br />".$exam_quest;

			//	Generate Query of EXAM OPTIONS Data

				$exam_opti = "";
				if (isset($exam_quc)) {
				$this->load->model('exam_options_m');
				$query = $this->exam_options_m->get_ex_options($exam_quc);


					if ($query->num_rows() > 0) {
						$value 		= "";

						$i = 1;
						foreach ($query->result() as $res) {

							$value .= "('".$res->uc."', '".str_replace("'", "''", $res->option_text_in)."', '".str_replace("'", "''", $res->option_text_en)."', '".$res->option_att_type."', '".$res->option_att_file."', '".$res->is_correct."', '".$res->uc_exam_question."', '".$res->uc_exam."'),";

							if (($i%50) == 0) {
								$value = substr_replace($value, '', -1);
								$exam_opti .= " INSERT INTO `tech_exam_options_temp`(`uc`, `option_text_in`, `option_text_en`, `option_att_type`, `option_att_file`, `is_correct`, `uc_exam_question`, `uc_exam`)  VALUES  ";
								$exam_opti .= $value."; ";
								$exam_opti .= "\n\r \n\r";

								$value = "";
							}

							$i++;
						}

						if ($value != "") {
							$value = substr_replace($value, '', -1);
							$exam_opti .= " INSERT INTO `tech_exam_options_temp`(`uc`, `option_text_in`, `option_text_en`, `option_att_type`, `option_att_file`, `is_correct`, `uc_exam_question`, `uc_exam`) VALUES  ";
							$exam_opti .= $value."; ";
						}

					}

				}

			// echo "<br />".$exam_opti;

			//	Generate Query of EXAM MATCH Data

				$exam_match = "";
				if (isset($exam_quc)) {
				$this->load->model('exam_match_m');
				$query = $this->exam_match_m->get_ex_match($exam_quc);


					if ($query->num_rows() > 0) {
						$value 		= "";

						$i = 1;
						foreach ($query->result() as $res) {

							$value .= "('".$res->uc_exam_question."', '".str_replace("'", "''", $res->question_field_in)."', '".str_replace("'", "''", $res->question_field_en)."', '".$res->question_att_type."', '".$res->question_att_file."', '".str_replace("'", "''", $res->answer_field_in)."', '".str_replace("'", "''", $res->answer_field_en)."', '".$res->answer_att_type."', '".$res->answer_att_file."', '".$res->uc_exam."'),";

							if (($i%50) == 0) {
								$value = substr_replace($value, '', -1);
								$exam_match .= " INSERT INTO `tech_exam_match_temp`(`uc_exam_question`, `question_field_in`, `question_field_en`, `question_att_type`, `question_att_file`, `answer_field_in`, `answer_field_en`, `answer_att_type`, `answer_att_file`, `uc_exam`) VALUES  ";
								$exam_match .= $value."; ";
								$exam_match .= "\n\r \n\r";

								$value = "";
							}

							$i++;
						}

						if ($value != "") {
							$value = substr_replace($value, '', -1);
							$exam_match .= " INSERT INTO `tech_exam_match_temp`(`uc_exam_question`, `question_field_in`, `question_field_en`, `question_att_type`, `question_att_file`, `answer_field_in`, `answer_field_en`, `answer_att_type`, `answer_att_file`, `uc_exam`) VALUES  ";
							$exam_match .= $value."; ";
						}

					}

				}

			// echo "<br />".$exam_match;

			//	Generate Query of PACKAGE Data

				// $exam_package = "";
				// $exam_uc_pack =  "";
				// if (isset($ex_uc_pack)) {
				// $this->load->model('package_m');
				// $query = $this->package_m->get_package_backup($ex_uc_pack);


				// 	if ($query->num_rows() > 0) {
				// 		$value 		= "";

				// 		$i = 1;
				// 		foreach ($query->result() as $res) {
				// 			$exam_uc_pack .= "'".$res->uc."',";
				// 			$value .= "('".$res->code_package."', '".$res->uc."', '".$res->uc_level."', '".$res->uc_function."', '".$res->uc_competency."', '".$res->is_exist."'),";

				// 			if (($i%50) == 0) {
				// 				$value = substr_replace($value, '', -1);
				// 				$exam_package .= " INSERT INTO `tech_package_temp`(`code_package`, `uc`, `uc_level`, `uc_function`, `uc_competency`, `is_exist`) VALUES   ";
				// 				$exam_package .= $value."; ";
				// 				$exam_package .= "\n\r \n\r";

				// 				$value = "";
				// 			}

				// 			$i++;
				// 		}

				// 		if ($value != "") {
				// 			$value = substr_replace($value, '', -1);
				// 			$exam_package .= " INSERT INTO `tech_package_temp`(`code_package`, `uc`, `uc_level`, `uc_function`, `uc_competency`, `is_exist`) VALUES   ";
				// 			$exam_package .= $value."; ";
				// 		}

				// 		$exam_uc_pack 	= substr_replace($exam_uc_pack, '', -1);

				// 	}

				// }

			// echo "<br />".$exam_package;

			//	Generate Query of PACKAGE QUESTION Data

				// $exam_pack_quest = "";

				// if (isset($exam_uc_pack)) {
				// $this->load->model('package_question_m');
				// $query = $this->package_question_m->get_pack_quest($exam_uc_pack);


				// 	if ($query->num_rows() > 0) {
				// 		$value 		= "";

				// 		$i = 1;
				// 		foreach ($query->result() as $res) {

				// 			$value .= "('".$res->uc_package."', '".$res->uc_question."'),";

				// 			if (($i%50) == 0) {
				// 				$value = substr_replace($value, '', -1);
				// 				$exam_pack_quest .= " INSERT INTO `tech_package_question_temp`(`uc_package`, `uc_question`) VALUES   ";
				// 				$exam_pack_quest .= $value."; ";
				// 				$exam_pack_quest .= "\n\r \n\r";

				// 				$value = "";
				// 			}

				// 			$i++;
				// 		}

				// 		if ($value != "") {
				// 			$value = substr_replace($value, '', -1);
				// 			$exam_pack_quest .= " INSERT INTO `tech_package_question_temp`(`uc_package`, `uc_question`) VALUES  ";
				// 			$exam_pack_quest .= $value."; ";
				// 		}

				// 	}

				// }

			// echo "<br />".$exam_pack_quest;

			$all_query = $q_period."\n\r".$q_day."\n\r".$q_sess."\n\r".$q_exam."\n\r".$ex_pack."\n\r".$ex_com_pack."\n\r".$q_xcomp."\n\r".$exam_quest."\n\r".$exam_opti."\n\r".$exam_match."\n\r".$q_parper."\n\r".$q_par."\n\r".$q_parmas."\n\r".$q_exat."\n\r".$q_score."\n\r";

		return array('all_query' => $all_query, 'uc_period' => $uc_period);
	}
}
?>
