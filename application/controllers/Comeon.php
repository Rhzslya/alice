<?php

class Comeon extends CI_Controller{	
	function __construct(){
		parent::__construct();

		$this->load->model('exam_attempt_m');
		$this->load->model('exam_attempt_competency_m');
		$this->load->model('pengajuan_ukp_m');

		$this->load->library('encrypt');
	}

	function form_edit_single($uc_competency = NULL, $uc_exam_attempt = NULL) {
		if ($uc_exam_attempt != NULL) {
			//	Get Attempt Data
			$this->load->model('exam_attempt_m');
			$query = $this->exam_attempt_m->get_filtered(array('uc' => $uc_exam_attempt));

			if ($query->num_rows() > 0) {
				$row_attempt = $query->row();

				// echo "<pre>";
				// print_r($row_attempt);
				// echo "</pre>";
			}

			//	Get Score
			$this->load->model('exam_attempt_competency_m');
			$filter = array(
							'uc_competency'		=> $uc_competency,
							'uc_exam_attempt'	=> $uc_exam_attempt
							);

			$query = $this->exam_attempt_competency_m->get_filtered($filter);
			if ($query->num_rows() > 0) {
				$row_score = $query->row();

				// echo "<pre>";
				// print_r($row_score);
				// echo "</pre>";
			}

			//echo "<br /> SCORE : ".decryptIt($row_score->score_normal);

			$data['row_score'] 		= $row_score;

			$this->im_render->main('comeon/form_edit_single', $data);
		}
		else {
			echo "missing parameter";
		}
	}

	function update_single() {
		if ($this->input->post('f_new_score') > $this->input->post('f_old_score')) {
			$uc_exam_attempt 	= $this->input->post('f_exam_attempt');
			$uc_competency 		= $this->input->post('f_competency');

			//	Get Score
			$this->load->model('exam_attempt_competency_m');
			$filter = array(
							'uc_competency'		=> $uc_competency,
							'uc_exam_attempt'	=> $uc_exam_attempt
							);

			$query = $this->exam_attempt_competency_m->get_filtered($filter);
			if ($query->num_rows() > 0) {
				$row = $query->row();
			}

			$this->do_update($row->seafarer_code, $row->uc_competency, $row->uc_exam_attempt, $this->input->post('f_old_score'), $this->input->post('f_new_score'));
		}
		else {
			echo "Nothing to change!";
		}
	}

	function do_update($seafarer_code = NULL, $uc_competency = NULL, $uc_exam_attempt = NULL, $old_score = NULL, $new_score = NULL) {
		//	Req : seafarer_code, uc_competency, uc_exam_attempt, old_score, new_score
		// echo "<br /> + ".$seafarer_code.", ".$uc_competency.", ".$uc_exam_attempt.", ".$old_score.", ".$new_score;

		// $seafarer_code = "6201014689";
		// $uc_competency = "26-56582-39";
		// $uc_exam_attempt = "380-87111-32-90";
		// $old_score = "60";
		// $new_score = "70";
		
		//	Get Attempt Data
		$this->load->model('exam_attempt_m');
		$query = $this->exam_attempt_m->get_filtered(array('uc' => $uc_exam_attempt));

		if ($query->num_rows() > 0) {
			$row = $query->row();

			// echo "<pre>";
			// print_r($row);
			// echo "</pre>";
		}

		$key = explode(",",$row->keys);
		$com = explode(",",$row->competency);
		$ans = explode(",",$row->answers);
		$res = explode(",",$row->answer_result);

		// echo "<pre>";
		// print_r($ans);
		// echo "</pre>";

		foreach ($com as $idx => $c) {		

			if ($c == $uc_competency) {
				//	Selected Competency
				$sel_com[$idx] = $c;
				// $sel_ans[$idx] = $ans[$idx];
				// $sel_res[$idx] = $res[$idx];
				
				//	False Data Only
				if ($res[$idx] == "F") {
					$fal_ans[$idx] = $ans[$idx];
					$fal_key[$idx] = $key[$idx];
					$fal_res[$idx] = $res[$idx];

				}
			}
			
		}

		// echo "<pre>";
		// print_r($fal_ans);
		// echo "</pre>";

		// echo "<pre>";
		// print_r($fal_key);
		// echo "</pre>";

		// echo "<pre>";
		// print_r($fal_res);
		// echo "</pre>";

		//	Count Needed Additional Score & True Answer
		$ads_score = $new_score - $old_score;
		$ads_true = ceil((count($sel_com) * $ads_score) / 100);

		// echo "<br /> Comp : ".count($sel_com);
		// echo "<br /> ADS : ".$ads_score." - ".$ads_true;

		//	Get Random False Result
		$rand_false = array_rand($fal_res, $ads_true);

		// echo "<pre>";
		// print_r($rand_false);
		// echo "</pre>";

		foreach ($rand_false as $rf) {
			$ans[$rf] = $fal_key[$rf];
			$res[$rf] = "T";
		}

		// echo "<pre>";
		// print_r($ans);
		// echo "</pre>";

		// echo "<pre>";
		// print_r($res);
		// echo "</pre>";

		$new_ans = implode(",",$ans);
		$new_res = implode(",",$res);

		//	Update Answer
		$data = array(
						'answers'			=> $new_ans,
						'answer_result'		=> $new_res
						);

		$where = array('uc'	=> $uc_exam_attempt);

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";

		$this->exam_attempt_m->update_data($data, $where);

		//	Update Score
		$data 	= array('score_normal' => encryptIt($new_score));
		$where 	= array(
							'uc_exam_attempt'	=> $uc_exam_attempt,
							'uc_competency'		=>  $uc_competency 
						);

		$this->exam_attempt_competency_m->update_data($data, $where);
		
	}


	function magic(){
		if ($this->input->post('f_magic')) {
			$uc_period = $this->input->post('f_uc_period');
			$data = "";

			$this->load->model('period_m');
			$q_period = $this->period_m->get_exams($uc_period);

			if ($q_period->num_rows() > 0 ){
				//	BEGIN of Get DB Score
				//	BEGIN of Get Detail Periode & Exam List
				$result 	= $q_period->result();
				$period  	= $result[0]->period;
				$diklat 	= $result[0]->pra_pasca;
				$ex_status 	= $result[0]->category;
				$uc_level 	= $result[0]->uc_level;
				$uc_upt		= $result[0]->uc_upt;
				$upt_label	= $result[0]->upt_label;
				$uc_pukp 	= $result[0]->uc_pukp;
				$pukp_label = $result[0]->pukp_label;
				$level 		= $result[0]->label;

				$exam_arr = array();
				$exam_ucs = NULL;
				foreach ($result as $res) {
					if(!in_array($res->uc_exam, $exam_arr, true)){
						array_push($exam_arr, $res->uc_exam);
						$exam_ucs .= "'".$res->uc_exam."',";
					}
				}
				$exam_ucs = substr_replace($exam_ucs, '', -1);

				//	END of Get Detail Periode & Exam List

				//	BEGIN of Get Competencies of This Period
				if ($diklat == 1){
					$coms = "(1,3,5,7)";
				}
				elseif($diklat == 2){
					$coms = "(2,3,6,7)";
				}
				elseif($diklat == 3){
					$coms = "(4,5,6,7)";
				}

				//	Get Competency List
				$this->load->model('competency_m');
				$query = $this->competency_m->get_competency_score($uc_level,$coms,$diklat);
				if ($query->num_rows() > 0) {
					$result = $query->result();

					// echo "<pre>";
					// print_r($result);
					// echo "</pre>";

					$comp = $result;

					$comp_ucs = NULL; 
					foreach ($result as $res) {
						$comp_ucs .= "'".$res->uc."',";
					}
					$comp_ucs = substr_replace($comp_ucs, '', -1);

				}
				//	END of Get Competencies of This Period

				// if ($show == 'adjust') {
				// 	//	Get Exam of Competency
				// 	$this->load->model('exam_competency_m');
				// 	$query = $this->exam_competency_m->get_exams($comp_ucs, $uc_period);
				// 	if ($query->num_rows() > 0) {
				// 		foreach ($query->result() as $ex) {
				// 			$exam[$ex->uc_competency] = $ex->uc_exam;
				// 		}
				// 	}

				// 	$data['exam'] = $exam;
				// }

				//	BEGIN of Get Scores
				if ($exam_ucs != NULL) {
					$this->load->model('exam_attempt_m');
					$query = $this->exam_attempt_m->get_score_of_period($exam_ucs);
					
					if ($query->num_rows() > 0) {

						$i = 0; // Seafarer Code

						$curr_seafarer = NULL;

						//$seafcs = NULL;

						// echo "<pre>";
						// print_r($query->result());
						// echo "</pre>";

						foreach ($query->result() as $res) {
							if ($res->seafarer_code != $curr_seafarer) {
								$part[$i]['seafarer_code']  = $res->seafarer_code;

								$curr_seafarer = $res->seafarer_code;

								$i++;						
							}
							else {
								$part[$i-1]['seafarer_code']  = $res->seafarer_code;
							}


							if ($res->uc != NULL) {
								//	If Attempt
								if ($res->is_done == 0) {
									//	If Unfinish
									$score[$res->seafarer_code][$res->uc_competency] = "UF";
								}
								else {

									//	If Finish
									if ($res->score_normal != NULL) {
										$score[$res->seafarer_code][$res->uc_competency]['score'] 			= decryptIt($res->score_normal);
										$score[$res->seafarer_code][$res->uc_competency]['uc_exam_attempt'] = $res->uc_exam_attempt;
									}
									else {
										//	If Score Not Exist
										$score[$res->seafarer_code][$res->uc_competency]['score'] 			= "NA";
										$score[$res->seafarer_code][$res->uc_competency]['uc_exam_attempt'] = $res->uc_exam_attempt;
									}
								}

							}
							else {
								//	If Not Attempt
								$score[$res->seafarer_code][$res->uc_competency]['score']			 	= "UA";
								$score[$res->seafarer_code][$res->uc_competency]['uc_exam_attempt'] 	= $res->uc_exam_attempt;
							}
						}

						// echo "<pre>";
						// print_r($score);
						// echo "</pre>";

						
						//	Set Participant & Score to Array[$i][$j]
						$i = 0;
						foreach ($part as $p) {
							$j = 0;

							foreach ($comp as $c) {
								if (isset($score[$p['seafarer_code']][$c->uc])) {
									$all_seaf[$i] = $p['seafarer_code'];

									$ss[$i][$j]['score'] 			= $score[$p['seafarer_code']][$c->uc]['score'];
									$ss[$i][$j]['uc_exam_attempt']  = $score[$p['seafarer_code']][$c->uc]['uc_exam_attempt'];
									$ss[$i][$j]['seafarer_code'] 	= $p['seafarer_code'];
									$ss[$i][$j]['uc_competency'] 	= $c->uc;
								}
								$j++;
							}
							$i++;
						}

						// echo "<pre>";
						// print_r($ss);
						// echo "</pre>";
					}	
				}
				//	END of Get DB Score

				//	BEGIN of PROCESS EXCEL

				$config['upload_path']			= './excel/';
				$config['allowed_types'] 		= 'xls|xlsx|csv';
				$config['max_size']				= 50000;
				$config['overwrite']			= TRUE;

				$this->load->library('upload', $config);			
				$this->upload->initialize($config);

				if ( ! $this->upload->do_upload('f_file')) {
					$this->upload->display_errors(); 	
				}
				else {
					$upload_data	= $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
					// $file_name		= $upload_data['file_name'];

					$inputFileName 	= 'excel/'.$upload_data['file_name'];

					$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));

					
					try {
						$inputFileType 	= IOFactory::identify($inputFileName);
						$objReader 		= IOFactory::createReader($inputFileType);
						$objPHPExcel 	= $objReader->load($inputFileName);
					} catch(Exception $e) {
						die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
					}

					$sheet 			= $objPHPExcel->getSheet(0);
					$highestRow 	= $sheet->getHighestRow();
					$highestColumn 	= $sheet->getHighestColumn();

					$validData = $sheet->rangeToArray('B11:'.'B'.$highestRow,
															NULL,
															TRUE,
															FALSE);

					$rowData = $sheet->rangeToArray('E11:'.$highestColumn.$highestRow,
															NULL,
															TRUE,
															FALSE);
					
					

					//	END of PROCESS EXCEL

				}

				//	Compare DB Score VS Excel File
				if ($this->is_data_match($all_seaf, $validData)) {
					$z = 0;

					for ($r=0; $r<$i; $r++) {
						for ($c=0; $c<$j; $c++) {
							if (  isset($ss[$r][$c]['score']) && $ss[$r][$c]['score'] != "UA"   ) {
								//echo "<br /> - ".$ss[$r][$c]['score'];

								if ($rowData[$r][$c] > $ss[$r][$c]['score']) {
									//	Required : seafarer_code, uc_competency, uc_exam_attempt, old_score, new_score
									$this->do_update($ss[$r][$c]['seafarer_code'], $ss[$r][$c]['uc_competency'], $ss[$r][$c]['uc_exam_attempt'], $ss[$r][$c]['score'], $rowData[$r][$c]);
								}
							}
						}
					}
					
					if ($this->input->post('f_redirect') == "recap") {
						redirect('report/recap/'.$uc_period);
					}
					else {
						redirect('report/subject/'.$this->input->post('f_redirect'));
					}
				}
				else {
					echo "Invalid File, Unmatch excel file!!!";
				}
				
			}
			
		}
		else {
			redirect('report');
		}
	}

	function is_data_match($all_seaf, $validData) {
		$ss_last_data = count($all_seaf);
		$excel_last_data = count($validData);
		
		//	Check amount of Participant Data
		$valid_amount_data = FALSE;
		if ($ss_last_data == $excel_last_data) {
			$valid_amount_data = TRUE;
		}

		$valid_participant_data = FALSE;
		if (($all_seaf[0] == $validData[0][0]) && ($all_seaf[$ss_last_data-1] == $validData[$excel_last_data-1][0])) {
			$valid_participant_data = TRUE;
		}

		if ($valid_amount_data && $valid_participant_data) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	function daily_manage($uc_ukp = NULL) {
		if ($uc_ukp != NULL) {
			$data = "";

			$data['uc_ukp'] = $uc_ukp;

			//	Get All Day
			$query = $this->pengajuan_ukp_m->get_days($uc_ukp);
			if ($query->num_rows() > 0) {
				$data['result'] = $query->result();
			}

			//	Get Schedule Detail
			$query = $this->pengajuan_ukp_m->get_detail($uc_ukp);
			$data['row'] = $query->row();

			$this->im_render->main('report/daily_result', $data);
		}
		else {
			redirect('schedule');
		}
	}

	function rebackup($uc = NULL) {
		if ($uc != NULL) {
			$data = NULL;

			//	Generate Query of PERIOD Data
			$this->load->model('pengajuan_ukp_m');
			$query = $this->pengajuan_ukp_m->get_filtered(array('uc' => $uc), "id", "ASC");
			$q_ukp = "";
			if ($query->num_rows() > 0) {
				$value = "";
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."', '".$res->uc_upt."', '".$res->uc_pukp."', '".$res->date_start."', '".$res->date_finish."', '".$res->create_time."', '".$res->is_approved."'),";

					$uc_ukp = $res->uc;
				}
				$value = substr_replace($value, '', -1);

				$q_ukp .= " INSERT INTO `tech_pengajuan_ukp_temp` (`uc`, `uc_upt`, `uc_pukp`, `date_start`, `date_finish`, `create_time`, `is_approved`) VALUES ";
				$q_ukp .= $value."; ";
			}

			//echo "<br />".$q_ukp;
			// ====================================================================================

			//	Generate Query of PERIOD Data
			$this->load->model('period_m');
			$query = $this->period_m->get_filtered(array('uc_ukp' => $uc), "id", "ASC");
			$q_per = "";
			if ($query->num_rows() > 0) {
				$value = "";
				$per_ucs = "";
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."', '".$res->period."', '".$res->date_start."', '".$res->date_finish."', '".$res->uc_upt."', '".$res->uc_level."', '".$res->pra_pasca."', '".$res->category."', '".$res->uc_ukp."'),";

					//$uc_period = $res->uc;
					$per_ucs .= "'".$res->uc."',";
				}
				$value = substr_replace($value, '', -1);
				$per_ucs = substr_replace($per_ucs, '', -1);

				$q_per .= " INSERT INTO `tech_period_temp` (`uc`, `period`, `date_start`, `date_finish`, `uc_upt`, `uc_level`, `pra_pasca`, `category`, `uc_ukp`) VALUES ";
				$q_per .= $value."; ";
			}

			//echo "<br />".$q_per;
			// // ====================================================================================

			//	Generate Query of DAY Data
			$this->load->model('day_m');
			$query = $this->day_m->get_in('uc_period', $per_ucs);
			$q_day = "";
			if ($query->num_rows() > 0) {
				$value = "";
				$uc_day	= "";
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->date."'),";

					$uc_day .= "'".$res->uc."',";
				}
				$value = substr_replace($value, '', -1);

				$uc_day 	= substr_replace($uc_day, '', -1);

				$q_day .= " INSERT INTO `tech_day_temp` (`uc`, `uc_period`, `date`) VALUES ";
				$q_day .= $value."; ";
			}

			//echo "<br />".$q_day;
			// ====================================================================================


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

			//echo "<br />".$q_sess;

			//	Generate Query of EXAMINATION Data
			$q_exam = "";
			if (isset($sess_ucs)) {							
				$this->load->model('examination_m');
				
				$query = $this->examination_m->get_exam_on_session($sess_ucs);
				
				if ($query->num_rows() > 0) {
					$value 		= "";
					$exam_ucs	= "";
					foreach ($query->result() as $res) {
						$value .= "('".$res->uc."', '".$res->exam_code."', '".$res->duration."', '".$res->time_create."', '".$res->is_accessed."', '".$res->is_active."', '".$res->uc_period."', '".$res->uc_session."', '".$res->uc_level."', '".$res->uc_function."', '".$res->has_attempted."', '".$res->is_language."', '".$res->show_score."', '".$res->diklat_type."', '".$res->is_exist."'),";

						$exam_ucs .= "'".$res->uc."',";
					}
					$value 		= substr_replace($value, '', -1);

					$exam_ucs 	= substr_replace($exam_ucs, '', -1);					

					$q_exam .= " INSERT INTO `tech_examination_temp` (`uc`, `exam_code`, `duration`, `time_create`, `is_accessed`, `is_active`, `uc_period`, `uc_session`, `uc_level`, `uc_function`, `has_attempted`, `is_language`, `show_score`, `diklat_type`, `is_exist`) VALUES ";
					$q_exam .= $value."; ";
				}	
			}

			// echo "<br />".$q_exam;
			// ====================================================================================

			//	Generate Query of EXAMINATION COMPETENCY Data
			$q_xcomp = "";
			if (isset($sess_ucs)) {							
				$this->load->model('exam_competency_m');
				$query = $this->exam_competency_m->get_exam_competency($exam_ucs);
				
				if ($query->num_rows() > 0) {
					$value 		= "";

					$sta_com_ucs 	= "";
					foreach ($query->result() as $res) {
						$value .= "('".$res->uc."', '".$res->uc_exam."', '".$res->uc_competency."'),";
						
						$sta_com_ucs .= "'".$res->uc_competency."',";
					}
					$value 		= substr_replace($value, '', -1);

					$q_xcomp .= " INSERT INTO `tech_exam_competency_temp` (`uc`, `uc_exam`, `uc_competency`) VALUES ";
					$q_xcomp .= $value."; ";

					$sta_com_ucs 		= substr_replace($sta_com_ucs, '', -1);
				}	
			}

			
			// echo "<br />".$q_xcomp;
			// ====================================================================================

			//	Generate Query of PARTICIPANT PERIOD Data
			$this->load->model('period_participant_m');
			$query = $this->period_participant_m->get_in('uc_period', $per_ucs);
			$q_parper = "";

			if ($query->num_rows() > 0) {
				$value 		= "";
				$uc_per 	= "";

				$sea_fcs	= array();

				$field = "(`uc`, `uc_period`, `uc_diklat_participant`, `seafarer_code`, `participant_no`, `last_active`, `is_login`)";

				$i = 1;	
				foreach ($query->result() as $res) {
					$uc_per .= "'".$res->seafarer_code."',";
					
					$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_diklat_participant."', '".$res->seafarer_code."', '".$res->participant_no."', '".$res->last_active."', '".$res->is_login."'),";

					if (($i%50) == 0) {
						$value = substr_replace($value, '', -1);
						$q_parper .= " INSERT INTO `tech_period_participant_temp` ".$field." VALUES ";
						$q_parper .= $value."; ";
						$q_parper .= "\n\r \n\r";

						$value = "";
					}

					array_push($sea_fcs, $res->seafarer_code);

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$q_parper .= " INSERT INTO `tech_period_participant_temp` ".$field." VALUES ";
					$q_parper .= $value."; ";	
				}					


				$uc_per 	= substr_replace($uc_per, '', -1);					
			}			

			// echo "<br />".$q_parper;
			// ====================================================================================			

			//	Generate Query of PARTICIPANT Data
			$this->load->model('participant_m');
			$query = $this->participant_m->get_in('uc_period', $per_ucs);
			$q_par = "";
			
			if ($query->num_rows() > 0) {
				$value 		= "";
				$uc_par 	= "";

				$field = "(`uc`, `seafarer_code`, `full_name`, `born_place`, `born_date`, `uc_diklat_participant`, `participant_no`, `uc_period`, `uc_level`, `uc_function`, `uc_exam`)";

				$i = 1;	
				foreach ($query->result() as $res) {
					$uc_par .= "'".$res->uc."',";
					
					$value .= "('".$res->uc."', '".$res->seafarer_code."', '".str_replace("'", "''", $res->full_name)."', '".str_replace("'", "''", $res->born_place)."', '".$res->born_date."', '".$res->uc_diklat_participant."', '".$res->participant_no."', '".$res->uc_period."', '".$res->uc_level."', '".$res->uc_function."', '".$res->uc_exam."'),";

					if (($i%50) == 0) {
						$value = substr_replace($value, '', -1);
						$q_par .= " INSERT INTO `tech_participant_temp` ".$field." VALUES ";
						$q_par .= $value."; ";
						$q_par .= "\n\r \n\r";

						$value = "";
					}

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$q_par .= " INSERT INTO `tech_participant_temp` ".$field." VALUES ";
					$q_par .= $value."; ";	
				}					


				$uc_par 	= substr_replace($uc_par, '', -1);					
			}				
			// echo "<br />".$q_par;
			// ====================================================================================

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
			// ====================================================================================
			
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
			// ====================================================================================

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
			// ====================================================================================		

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
			// ====================================================================================

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
			// ====================================================================================

			//	Generate Query of EXAM ATTEMPT COMPETENCY Data
			$q_score = "";
			if (isset($att_ucs)) {
				$this->load->model('exam_attempt_competency_m');
				$query = $this->exam_attempt_competency_m->get_score_for_attempt_in($att_ucs);
				

				if ($query->num_rows() > 0) {
					$value 		= "";
					$att_ucs	= "";
					$com_ucs 	= "";
					$seafcs		= "";

					$i = 1;	
					foreach ($query->result() as $res) {
						$att_ucs .= "'".$res->uc."',";	// <--- DUNO 4 WHAT???

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
			// ====================================================================================


			//	Generate query for Status Period
			//	WHERE $uc_period AND  IN $sta_com_ucs
			$q_sta_per = "";
			if (isset($sta_com_ucs)) {
				$this->load->model('status_period_m');
				$query = $this->status_period_m->get_in('uc_period', $per_ucs);
				//$query = $this->status_period_m->get_status_competency($uc_period, $sta_com_ucs);
				if ($query->num_rows() > 0) {
					$field = "(`uc`, `uc_period`, `uc_competency`, `diklat_type`, `uc_diklat_participant`,`seafarer_code`, `is_pass`, `score`, `uc_score`, `status`)";

					$value = "";

					$i = 1;	
					foreach ($query->result() as $res) {
						$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_competency."', '".$res->diklat_type."', '".$res->uc_diklat_participant."','".$res->seafarer_code."', '".$res->is_pass."', '".$res->score."', '".$res->uc_score."', '".$res->status."'),";

						if (($i%50) == 0) {
							$value = substr_replace($value, '', -1);
							$q_sta_per .= "INSERT INTO `tech_status_period_temp` ".$field." VALUES ".$value."; ";
							$q_sta_per .= "\n\r \n\r";
							
							$value = "";
						}

						$i++;
					}

					if ($value != "") {
						$value = substr_replace($value, '', -1);
						$q_sta_per .= "INSERT INTO `tech_status_period_temp` ".$field." VALUES ".$value."; ";
						$q_sta_per .= "\n\r \n\r";	
					}
				}	
			}

			//echo "<br />".$q_sta_per;
			// ====================================================================================


			//	Generate query for status
			//	Get All Competency from table status WHERE competency in Period
			$q_status = "";
			if (isset($sta_com_ucs)) {
				$this->load->model('status_m');

				$query = $this->status_m->get_participant_competency($per_ucs, $uc_per);	// $uc_per = (all seafarer in this period in come separtation)


				// $query = $this->status_m->get_competency_in_period($sta_com_ucs);
				if ($query->num_rows() > 0) {
					$value = "";

					$i = 1;	
					foreach ($query->result() as $res) {
						foreach ($sea_fcs as $sfc) {
							if ($res->seafarer_code == $sfc) {
								//	If Seafarer in this period
								$value .= "('".$res->uc."', '".$res->uc_competency."', '".$res->pra_pasca."', '".$res->seafarer_code."', '".$res->is_pass."', '".$res->score_max."', '".$res->uc_score."', '".$res->status."'),";
								
								//echo "<br /> i = ".$i;
								if (($i%50) == 0) {
									$value = substr_replace($value, '', -1);
									$q_status .= " INSERT INTO `tech_status_temp` (`uc`, `uc_competency`, `pra_pasca`, `seafarer_code`, `is_pass`, `score_max`, `uc_score`, `status`) VALUES ";
									$q_status .= $value."; ";
									$q_status .= "\n\r \n\r";

									$value = "";
								}

								$i++;
							}
						}
					}

					if ($value != "") {
						$value = substr_replace($value, '', -1);
						$q_status .= " INSERT INTO `tech_status_temp` (`uc`, `uc_competency`, `pra_pasca`, `seafarer_code`, `is_pass`, `score_max`, `uc_score`, `status`) VALUES ";
						$q_status .= $value."; ";
						$q_status .= "\n\r \n\r";

						$value = "";	
					}
				}
			}

			//echo "<br />".$q_status;
			// ====================================================================================	
			

			$all_query = $q_ukp."\n\r".$q_per."\n\r".$q_day."\n\r".$q_sess."\n\r".$q_exam."\n\r".$q_xcomp."\n\r".$exam_quest."\n\r".$exam_opti."\n\r".$exam_match."\n\r".$q_parper."\n\r".$q_par."\n\r".$q_parmas."\n\r".$q_exat."\n\r".$q_score."\n\r".$q_sta_per."\n\r".$q_status;
			
			// echo "<br />".$all_query;

			// Generate File Name
			$this->load->helper('download');
			date_default_timezone_set('Etc/GMT-7');
			
			$query = $this->pengajuan_ukp_m->get_detail_info($uc);
			$row = $query->row();

			$start_date = time_format($row->date_start, "m.d");
			$finish_date = time_format($row->date_finish, "m.d");			
			$file_name = $row->pukp_label." - [".$start_date."][".$finish_date."]";			
			$en_query = $this->encrypt->encode($all_query);

			force_download($file_name.".cba", $en_query);
		}
	}

	/*
	function magic() {
		//	Uplaod Excel
		if ($this->input->post('f_magic')) {
			$config['upload_path']			= './excel/';
			$config['allowed_types'] 		= 'xls|xlsx|csv';
			$config['max_size']				= 50000;
			$config['overwrite']			= TRUE;

			$this->load->library('upload', $config);			
			$this->upload->initialize($config);

			if ( ! $this->upload->do_upload('f_file')) {
				$this->upload->display_errors(); 	
			}
			else {
				$upload_data	= $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
				// $file_name		= $upload_data['file_name'];

				$inputFileName 	= 'excel/'.$upload_data['file_name'];

				$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));

				try {
					$inputFileType 	= IOFactory::identify($inputFileName);
					$objReader 		= IOFactory::createReader($inputFileType);
					$objPHPExcel 	= $objReader->load($inputFileName);
				} catch(Exception $e) {
					die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}

				echo "FILE ; ".$inputFileName ;

			}	
		}

	}
	*/
}