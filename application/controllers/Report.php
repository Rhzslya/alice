<?php
class Report extends CI_COntroller {
	function __construct() {
		parent::__construct();
		$this->load->library('im_license');		
		if (!$this->im_license->license_valid()) {
			redirect('license');
		}

		$this->each_page 	= 10;
		$this->page_int 	= 10;

		if (!$this->im_login->is_login('log_alice')) {
			redirect('login');
		}

		// Load library for dencryption score
		$this->load->library('encrypt');

		$this->load->model('schedule_m');
		$this->load->model('period_m');

		// $this->other_dbname = 'cba_ukp_recap_1_0';
	}

	function index() {
		$data = "";

		$page = 1;
		//	Pagination Initialization
		$this->load->library('im_pagination');
		///	Define Offset
		$offset = ($page - 1) * $this->each_page;
		//	Define Parameters
		$params = array(
							'page_number'	=> $page,
							'each_page'		=> $this->each_page,
							'page_int'		=> $this->page_int,	
							'segment' 		=> 'period',
							'model'			=> 'period_m'
						);

		$query = $this->schedule_m->get_list(NULL, $this->each_page, $offset);
		if ($query->num_rows() > 0) {
			$data['result'] 	= $query->result();
		}

		$query = $this->schedule_m->get_list();
		if ($query->num_rows() > 0) {
			$params['total_record'] = $query->num_rows();
			$data['pagination'] 	= $this->im_pagination->render_ajax($params);
			$data['total_record'] 	= $query->num_rows();
		}

		$data['numbering'] 	= ($this->each_page * ($page-1)) + 1;

		$this->im_render->main('report/schedule_list', $data);
	}

	function subject($uc_ukp = NULL){
		if ($uc_ukp != NULL) {
			$data['uc_ukp'] = $uc_ukp;

			$this->load->model('pengajuan_ukp_m');
			$query = $this->pengajuan_ukp_m->get_detail($uc_ukp);
			if ($query->num_rows() > 0) {
				$data['info'] = $query->row();

				//	Get All Subject in this schedule
				$this->load->model('period_m');
				$query = $this->period_m->get_by_schedule($uc_ukp);
				if ($query->num_rows() > 0) {
					$data['subject'] = $query->result();
				}
			}
			else {
				redirect('report');
			}

			$this->im_render->main('report/subject_list', $data);
		}
		else {
			redirect('report');
		}
	}

	function page(){
		$page 		= ($this->input->post('js_page') != 1 ? $this->input->post('js_page') : 1);
		$uc_pukp 	= ($this->input->post('js_uc_pukp') != 1 ? $this->input->post('js_uc_pukp') : 1);

		//	Pagination Initialization
		$this->load->library('im_pagination');
		///	Define Offset
		$offset = ($page - 1) * $this->each_page;
		//	Define Parameters
		$params = array(
							'page_number'	=> $page,
							'each_page'		=> $this->each_page,
							'page_int'		=> $this->page_int,	
							'segment' 		=> 'score',
							'model'			=> 'score_m'
						);

		$this->load->model('score_m');

		$query = $this->score_m->get_list_score($uc_pukp, $this->each_page, $offset);
		if ($query->num_rows() > 0) {
			$data['result'] 	= $query->result();
		}

		$query = $this->score_m->get_list_score($uc_pukp);
		if ($query->num_rows() > 0) {
			$params['total_record'] = $query->num_rows();
			$data['pagination'] 	= $this->im_pagination->render_ajax($params);
			$data['total_record'] 	= $query->num_rows();
		}

		$data['numbering'] 	= ($this->each_page * ($page-1)) + 1;

		// Score 1, Answer 2
		$data['category'] = 1;

		$this->load->view('report/page', $data);
	}

	function form_score(){
		$this->load->view('report/form_report_score');
	}

	// function form_report_score(){
	// 	$this->load->view('report/form_report_score');
	// }

	function score($uc_pukp = NULL, $uc_upt = NULL, $uc_level = NULL, $diklat = NULL, $type = NULL){
		if ($this->input->post('f_proccess')){
			$data = NULL;

			if ($this->input->post('f_pukp') != NULL) {
				$uc_pukp 	= $this->input->post('f_pukp');
			}

			if ($this->input->post('f_upt') != NULL) {
				$uc_upt 	= $this->input->post('f_upt');
			}

			if ($this->input->post('f_level') != NULL) {
				$uc_level 	= $this->input->post('f_level');
			}

			if ($this->input->post('f_category') != NULL) {
				$category 	= $this->input->post('f_category');
			}
			else{
				$diklat = $this->input->post('f_diklat');

				// pembentukan peningkatan
				if ($diklat == 1) {
					$category = $this->input->post('f_pra_pasca');
				}
				elseif($diklat == 2){
					$category = 3;
				}
			}

			if ($this->input->post('f_type') != NULL) {
				// 1 status, 2 score
				$type 	= $this->input->post('f_type');
			}

			// get pukp
			$this->load->model('pukp_m');
			$query = $this->pukp_m->get_filtered(array('uc' => $uc_pukp));
			if ($query->num_rows() > 0) {
				$data['row_p'] = $query->row();
			}

			// get upt
			$this->load->model('upt_m');
			$query = $this->upt_m->get_filtered(array('uc' => $uc_upt));
			if ($query->num_rows() > 0) {
				$data['row_u'] = $query->row();
			}

			// get level
			$this->load->model('level_m');
			$query = $this->level_m->get_filtered(array('uc' => $uc_level));
			if ($query->num_rows() > 0) {
				$data['row_l'] = $query->row();
			}

			if ($category == 1){
				//	Pra
				$cat = "(1,3,5,7)";
			}
			elseif($category == 2){
				//	Pasca
				$cat = "(2,3,6,7)";
			}
			elseif($category == 3){
				//	DPs
				$cat = "(4,5,6,7)";
			}

			//	Get Competency List
			$this->load->model('competency_m');
			$query = $this->competency_m->get_competency_score($uc_level,$cat,$category);
			if ($query->num_rows() > 0) {
				$data['comp'] = $query->result();
			}

			//	Get Score
			$this->load->model('score_m');
			$query = $this->score_m->get_recap($uc_level,$category,$uc_pukp,$uc_upt);
			if ($query->num_rows() > 0) {
				$result = $query->result();
			}

			$curr_comp 			= "";
			$curr_seafarer 		= "";

			$i = 0;
			$j = 0;

			if (isset($result)) {				
				foreach ($result as $res) {
					if ($res->seafarer_code != $curr_seafarer) {
						$j = 0;
						
						$part[$i]['seafarer_code'] 			= $res->seafarer_code;
						$part[$i]['full_name'] 				= $res->full_name;

						$score[$i][$j]['competency'] 			= $res->label;
						$score[$i][$j]['uc_competency']			= $res->uc_competency;
						$score[$i][$j]['sequence']				= $res->sequence;
						$score[$i][$j]['is_pass'] 				= $res->is_pass;
						$score[$i][$j]['score_max'] 			= decryptIt($res->score_max);

						$curr_seafarer = $res->seafarer_code;
						
						$i++;
						$j++;
					}
					else{
						
						$score[$i-1][$j]['competency'] 			= $res->label;
						$score[$i-1][$j]['uc_competency']		= $res->uc_competency;
						$score[$i-1][$j]['sequence'] 			= $res->sequence;
						$score[$i-1][$j]['is_pass'] 			= $res->is_pass;
						$score[$i-1][$j]['score_max'] 			= decryptIt($res->score_max);

						$j++;
					}

					$score[$res->seafarer_code][$res->uc_competency]['status'] = $res->is_pass;
					$score[$res->seafarer_code][$res->uc_competency]['score'] = decryptIt($res->score_max);


					// if ($type == 1) {					
					// 	$score[$res->seafarer_code][$res->uc_competency] = $res->is_pass;
					// }
					// else if ($type == 2) {					
					// 	$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_max);
					// }
					// else{
					// 	$score[$res->seafarer_code][$res->uc_competency] = $res->is_pass;
					// }
				}
				
				$data['part'] 		= $part;		
				$data['score'] 		= $score;		
			}

			if ($type != NULL) {
				$data['type']	 	= $type;
			}
			else {
				$data['type']	 	= 1;
			}

			$data['category'] 	= $category;
			$data['uc_pukp'] 	= $uc_pukp;
			$data['uc_upt'] 	= $uc_upt;
			$data['uc_level'] 	= $uc_level;

			$this->im_render->main('report/all_recap',$data);
		}
		else {
			redirect('report');
		}
	}

	function recap_participant_level_pdf($uc_level = NULL, $category = NULL, $uc_pukp = NULL, $uc_upt = NULL, $type = NULL)
	{
		if ($uc_level != NULL && $category != NULL) {
			
			$data = NULL;

			// get pukp
			$this->load->model('pukp_m');
			$query = $this->pukp_m->get_filtered(array('uc' => $uc_pukp));
			if ($query->num_rows() > 0) {
				$data['row_p'] = $query->row();
			}

			// get upt
			$this->load->model('upt_m');
			$query = $this->upt_m->get_filtered(array('uc' => $uc_upt));
			if ($query->num_rows() > 0) {
				$data['row_u'] = $query->row();
			}

			// get level
			$this->load->model('level_m');
			$query = $this->level_m->get_filtered(array('uc' => $uc_level));
			if ($query->num_rows() > 0) {
				$data['row'] = $query->row();
				$row 		 = $query->row();
			}

			if ($category == 1) {
				$cat = "(1,3,5,7)";
			}
			if ($category == 2) {
				$cat = "(2,3,6,7)";
			}
			if ($category == 3) {
				$cat = "(4,5,6,7)";
			}

			$this->load->model('competency_m');
			$query = $this->competency_m->get_competency_score($uc_level,$cat,$category);
			if ($query->num_rows() > 0) {
				$data['comp'] = $query->result();
			}

			$this->load->model('score_m');
			$query = $this->score_m->get_recap($uc_level,$category,$uc_pukp,$uc_upt);
			if ($query->num_rows() > 0) {
				$result = $query->result();
			}

			$curr_comp 			= "";
			$curr_seafarer 		= "";

			$i = 0;

			if (isset($result)) {
				foreach ($result as $res) {
					if ($res->seafarer_code != $curr_seafarer) {
						$j = 0;
						
							$part[$i]['seafarer_code'] 			= $res->seafarer_code;
							$part[$i]['full_name'] 				= $res->full_name;

							$score[$i][$j]['competency'] 			= $res->label;
							$score[$i][$j]['uc_competency']			= $res->uc_competency;
							$score[$i][$j]['sequence']				= $res->sequence;
							$score[$i][$j]['is_pass'] 				= $res->is_pass;
							$score[$i][$j]['score_max'] 			= decryptIt($res->score_max);

						$curr_seafarer = $res->seafarer_code;
						
						$i++;
						$j++;
					}
					else{
						
						$score[$i-1][$j]['competency'] 			= $res->label;
						$score[$i-1][$j]['uc_competency']		= $res->uc_competency;
						$score[$i-1][$j]['sequence'] 			= $res->sequence;
						$score[$i-1][$j]['is_pass'] 			= $res->is_pass;
						$score[$i-1][$j]['score_max'] 			= decryptIt($res->score_max);

						$j++;
					}

					if ($type == 1) {					
						$score[$res->seafarer_code][$res->uc_competency] = $res->is_pass;
					}
					else if ($type == 2) {					
						$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_max);
					}
					else{
						$score[$res->seafarer_code][$res->uc_competency] = $res->is_pass;
					}
				}
				
				// echo "<pre>"; 

				$data['part'] 		= $part;		
				$data['score'] 		= $score;		
			}
			
			$data['category'] 	= $category;
			$data['uc_level'] 	= $uc_level;
			
			if ($type != NULL) {
				$data['type']	 	= $type;
			}
			else{
				$data['type']	 	= 1;
			}

			// echo "<pre>";
			// print_r($data);
			// echo "</pre>";

			/* BEGIN Of export into pdf */
				$html = $this->load->view('report/recap_participant_level_pdf', $data, TRUE);

		        //this the the PDF filename that user will get to download
				$pdfFilePath = "Score_Recapitulation_Level_[".$row->label."_".$cat_label."][".time_format(current_time(), "d-m-Y")."].pdf";

		        //load mPDF library
				$this->load->library('m_pdf');

				ob_clean(); // cleaning the buffer before Output()

		       //generate the PDF from the given html
				$this->m_pdf->pdf->WriteHTML($html);
			
		        //download it.
				$this->m_pdf->pdf->Output($pdfFilePath, "D");
			/* END Of export into pdf */
		}
	}

	function recap_participant_level_excel($uc_level = NULL, $category = NULL, $uc_pukp = NULL, $uc_upt = NULL, $type = NULL)
	{
		if ($uc_level != NULL && $category != NULL) {
			$data = "";

			$this->load->helper('text');		

			// get pukp
			$this->load->model('pukp_m');
			$query = $this->pukp_m->get_filtered(array('uc' => $uc_pukp));
			if ($query->num_rows() > 0) {
				$data['row_p'] = $query->row();
			}

			// get upt
			$this->load->model('upt_m');
			$query = $this->upt_m->get_filtered(array('uc' => $uc_upt));
			if ($query->num_rows() > 0) {
				$data['row_u'] = $query->row();
			}

			// get level
			$this->load->model('level_m');
			$query = $this->level_m->get_filtered(array('uc' => $uc_level));
			if ($query->num_rows() > 0) {
				$data['row'] = $query->row();
				$row 		 = $query->row();
			}

			if ($category == 1) {
				$cat = "(1,3,5,7)";
			}
			if ($category == 2) {
				$cat = "(2,3,6,7)";
			}
			if ($category == 3) {
				$cat = "(4,5,6,7)";
			}

			$this->load->model('competency_m');
			$query = $this->competency_m->get_competency_score($uc_level,$cat,$category);
			if ($query->num_rows() > 0) {
				$data['comp'] = $query->result();
			}

			// Score
			$this->load->model('score_m');
			$query = $this->score_m->get_recap($uc_level,$category,$uc_pukp,$uc_upt);
			if ($query->num_rows() > 0) {
				$result = $query->result();
			}

			$curr_comp 			= "";
			$curr_seafarer 		= "";

			$i = 0;

			if (isset($result)) {
				foreach ($result as $res) {
					if ($res->seafarer_code != $curr_seafarer) {
						$j = 0;
						
							$part[$i]['seafarer_code'] 			= $res->seafarer_code;
							$part[$i]['full_name'] 				= $res->full_name;

							$score[$i][$j]['competency'] 			= $res->label;
							$score[$i][$j]['uc_competency']			= $res->uc_competency;
							$score[$i][$j]['sequence']				= $res->sequence;
							$score[$i][$j]['is_pass'] 				= $res->is_pass;
							$score[$i][$j]['score_max'] 			= decryptIt($res->score_max);

						$curr_seafarer = $res->seafarer_code;
						
						$i++;
						$j++;
					}
					else{
						
						$score[$i-1][$j]['competency'] 			= $res->label;
						$score[$i-1][$j]['uc_competency']		= $res->uc_competency;
						$score[$i-1][$j]['sequence'] 			= $res->sequence;
						$score[$i-1][$j]['is_pass'] 			= $res->is_pass;
						$score[$i-1][$j]['score_max'] 			= decryptIt($res->score_max);

						$j++;
					}

					if ($type == 1) {					
						$score[$res->seafarer_code][$res->uc_competency] = $res->is_pass;
					}
					else if ($type == 2) {					
						$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_max);
					}
					else{
						$score[$res->seafarer_code][$res->uc_competency] = $res->is_pass;
					}
				}
				
				// echo "<pre>"; 

				$data['part'] 		= $part;		
				$data['score'] 		= $score;
				$data['category'] 	= $category;		
				
				if ($type != NULL) {
					$data['type']	 	= $type;
				}
				else{
					$data['type']	 	= 1;
				}		
			}
		}

		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";

		$this->load->view('report/recap_participant_level_excel', $data);
	}

	function export_result($uc = NULL){
		if ($uc != NULL) {
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

				$q_ukp .= " INSERT INTO `tech_schedule_history_temp` (`uc`, `uc_upt`, `uc_pukp`, `date_start`, `date_finish`, `create_time`, `is_approved`) VALUES ";
				$q_ukp .= $value."; ";
			}

			//echo "<br />".$q_ukp;
			// =========================================================================================


			//	Generate Query of SUBJECT Data
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

				$q_per .= " INSERT INTO `tech_period_history_temp` (`uc`, `period`, `date_start`, `date_finish`, `uc_upt`, `uc_level`, `diklat_type`, `exam_type`, `uc_ukp`) VALUES ";
				$q_per .= $value."; ";
			}

			// echo "<br />".$q_per;
			// =========================================================================================


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
						$q_parper .= " INSERT INTO `tech_period_participant_history_temp` ".$field." VALUES ";
						$q_parper .= $value."; ";
						$q_parper .= "\n\r \n\r";

						$value = "";
					}

					array_push($sea_fcs, $res->seafarer_code);

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$q_parper .= " INSERT INTO `tech_period_participant_history_temp` ".$field." VALUES ";
					$q_parper .= $value."; ";	
				}					


				$uc_per 	= substr_replace($uc_per, '', -1);					
			}			

			//echo "<br />".$q_parper;
			// ====================================================================================	


			//	BEGIN of Generate Score Query
			$this->load->model('score_m');
			$query = $this->score_m->get_in('uc_period', $per_ucs);
			if ($query->num_rows() > 0) {
				$q_score = "";
				$value = "";
				$field = "(`uc`, `uc_period`, `uc_upt` ,`uc_competency`, `uc_eac`, `diklat_type`, `uc_diklat_participant`, `seafarer_code`, `score_normal`)";
				
				$i = 1;
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."','".$res->uc_period."', '".$res->uc_upt."', '".$res->uc_competency."', '".$res->uc_eac."','".str_replace("'", "''", $res->pra_pasca)."', '".$res->uc_diklat_participant."', '".$res->seafarer_code."', '".$res->score_normal."'),";

					if (($i%50) == 0){
						$value = substr_replace($value, '', -1);
						$q_score .= "INSERT INTO `tech_score_temp` ".$field." VALUES ".$value."; ";

						$value = "";
					}

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$q_score .= "INSERT INTO `tech_score_temp` ".$field." VALUES ".$value."; ";	
				}
			}

			//echo "<br />".$q_score;
			// ====================================================================================	


			//	Generate query for Status Period
			//	WHERE $uc_period AND  IN $sta_com_ucs
					
			$this->load->model('status_period_m');
			$query = $this->status_period_m->get_in('uc_period', $per_ucs);			
			if ($query->num_rows() > 0) {
				$q_sta_per = "";	

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

			//echo "<br />".$q_sta_per;
			// ====================================================================================

			//	BEGIN of Generate Status Query
			$this->load->model('status_m');
			$query = $this->status_m->get_all('id', 'ASC');
			if ($query->num_rows() > 0) {
				$q_status = "";
				$value = "";
				$field = "(`uc`, `uc_competency`, `diklat_type`, `seafarer_code`, `is_pass`, `score_max`, `uc_score`, `status`)";
				
				$i = 1;
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."', '".$res->uc_competency."', '".$res->pra_pasca."', '".$res->seafarer_code."', '".$res->is_pass."', '".$res->score_max."', '".$res->uc_score."', '".$res->status."'),";

					if (($i%50) == 0){
						$value = substr_replace($value, '', -1);
						$q_status .= "INSERT INTO `tech_status_temp` ".$field." VALUES ".$value."; ";

						$value = "";
					}

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$q_status .= "INSERT INTO `tech_status_temp` ".$field." VALUES ".$value."; ";	
				}
			}
			//echo $q_status;
			//	END of Generate Status Query ========================================================================


			$all_query = $q_ukp."\n\r".$q_per."\n\r".$q_parper."\n\r".$q_score."\n\r".$q_sta_per."\n\r".$q_status;

			echo $all_query;

			// Generate File Name
            $this->load->helper('download');
            $this->load->library('encrypt');
            date_default_timezone_set('Etc/GMT-7');
            
            $query = $this->pengajuan_ukp_m->get_detail($uc);
            //$query = $this-
            $row = $query->row();

            $start_date  = time_format($row->date_start, "m.d");
            $finish_date = time_format($row->date_finish, "m.d");           
            $file_name   = $row->pukp_label."-".$row->upt_label."-[".$start_date."]-[".$finish_date."]";

            $en_query = $this->encrypt->encode($all_query);           

            //force_download($file_name.".res", $en_query); 
		}
		else {
			redirect('report');
		}
	}

	function export_result_subject($uc_period = NULL){
		if ($uc_period != NULL) {
			//	Generate Query of SCHEDULE Data
			//	Get UC UKP
			$this->load->model('period_m');
			$qqq = $this->period_m->get_filtered(array('uc' => $uc_period));
			if ($qqq->num_rows() > 0) {
				$row = $qqq->row();

				$uc_ukp = $row->uc_ukp;
			}

			$this->load->model('pengajuan_ukp_m');
			$query = $this->pengajuan_ukp_m->get_filtered(array('uc' => $uc_ukp), "id", "ASC");
			$q_ukp = "";
			if ($query->num_rows() > 0) {
				$value = "";
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."', '".$res->uc_upt."', '".$res->uc_pukp."', '".$res->date_start."', '".$res->date_finish."', '".$res->create_time."', '".$res->is_approved."'),";

					$uc_ukp = $res->uc;
				}
				$value = substr_replace($value, '', -1);

				$q_ukp .= " INSERT INTO `tech_schedule_history_temp` (`uc`, `uc_upt`, `uc_pukp`, `date_start`, `date_finish`, `create_time`, `is_approved`) VALUES ";
				$q_ukp .= $value."; ";
			}

			//echo "<br />".$q_ukp;
			// =========================================================================================

			//	BEGIN of Generate Period History Query
			
			$query = $this->period_m->get_filtered(array('uc' => $uc_period));
			if ($query->num_rows() > 0) {
				$q_period_his = "";
				$value = "";
				$field = "(`uc`, `period`, `date_start`, `date_finish`, `uc_upt`, `uc_level`, `diklat_type`, `exam_type`, `uc_ukp`)";

				$res = $query->row();

				$value = "('".$res->uc."', '".$res->period."', '".$res->date_start."', '".$res->date_finish."', '".$res->uc_upt."', '".$res->uc_level."', '".$res->pra_pasca."', '".$res->category."', '".$uc_ukp."')";

				$q_period_his .= " INSERT INTO `tech_period_history_temp` ".$field." VALUES ".$value."; ";
			}
			//	END of Generate Period History Query


			//	BEGIN of Generate Period Participant History Query
			$this->load->model('period_participant_m');
			$query = $this->period_participant_m->get_filtered(array('uc_period' => $uc_period));


			if ($query->num_rows() > 0) {
				$q_perpar_his = "";				
				$value = "";
				$field = "(`uc`, `uc_period`, `uc_diklat_participant`, `seafarer_code`, `participant_no`, `last_active`, `is_login`)";

				$i = 1;
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_diklat_participant."', '".$res->seafarer_code."', '".$res->participant_no."', '".$res->last_active."', '".$res->is_login."'),";

					if (($i%50) == 0){
						$value = substr_replace($value, '', -1);
						$q_perpar_his .= "INSERT INTO `tech_period_participant_history_temp` ".$field." VALUES ".$value."; ";
						$q_perpar_his .= "\n\r \n\r";
					
						$value = "";
					}

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$q_perpar_his .= "INSERT INTO `tech_period_participant_history_temp` ".$field." VALUES ".$value."; ";	
				}


				// $res = $query->row();

				// $value .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_diklat_participant."', '".$res->seafarer_code."', '".$res->participant_no."', '".$res->last_active."', '".$res->is_login."'),";

				// $q_perpar_his .= " INSERT INTO `tech_period_participant_history_temp` ".$field." VALUES ".$value."; ";
			}
			//	END of Generate Period Participant History Query


			//	BEGIN of Generate Score Query
			$this->load->model('score_m');
			$query = $this->score_m->get_filtered(array('uc_period' => $uc_period), 'id', 'ASC');
			if ($query->num_rows() > 0) {
				$q_score = "";
				$value = "";
				$field = "(`uc`, `uc_period`, `uc_upt` ,`uc_competency`, `uc_eac`, `diklat_type`, `uc_diklat_participant`, `seafarer_code`, `score_normal`)";
				
				$i = 1;
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."','".$res->uc_period."', '".$res->uc_upt."', '".$res->uc_competency."', '".$res->uc_eac."','".str_replace("'", "''", $res->pra_pasca)."', '".$res->uc_diklat_participant."', '".$res->seafarer_code."', '".$res->score_normal."'),";

					if (($i%50) == 0){
						$value = substr_replace($value, '', -1);
						$q_score .= "INSERT INTO `tech_score_temp` ".$field." VALUES ".$value."; ";
						$q_score .= "\n\r \n\r";

						$value = "";
					}

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$q_score .= "INSERT INTO `tech_score_temp` ".$field." VALUES ".$value."; ";	
				}
			}
			// echo $q_score;
			//	END of Generate Score Query ========================================================================

			//	BEGIN of Generate Status Period Query
			$this->load->model('status_period_m');

			$query = $this->status_period_m->get_all('id', 'ASC');
			
			if ($query->num_rows() > 0) {

				$field = "(`uc`, `uc_period`, `uc_competency`, `diklat_type`, `uc_diklat_participant`,`seafarer_code`, `is_pass`, `score`, `uc_score`, `status`)";
			
				
				$q_sta_per = "";
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
				}

				// $i = 1;
				// foreach ($query->result() as $res) {
				// 	if ($i <= 50) {
				// 		$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_competency."', '".$res->diklat_type."', '".$res->uc_diklat_participant."','".$res->seafarer_code."', '".$res->is_pass."', '".$res->score."', '".$res->uc_score."', '".$res->status."'),";
				// 	}

				// 	$i++;
				// }

				// $value = substr_replace($value, '', -1);

				// $q_sta_per .= "INSERT INTO `tech_status_period_temp` ".$field." VALUES ".$value."; "; 
				// $q_sta_per .= "\n\r \n\r";


			}
			//echo $q_sta_per;
			//	END of Generate Status Period Query ========================================================================

			//	BEGIN of Generate Status Query
			$this->load->model('status_m');
			$query = $this->status_m->get_all('id', 'ASC');
			if ($query->num_rows() > 0) {
				$q_status = "";
				$value = "";
				$field = "(`uc`, `uc_competency`, `diklat_type`, `seafarer_code`, `is_pass`, `score_max`, `uc_score`, `status`)";
				
				$i = 1;
				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."', '".$res->uc_competency."', '".$res->pra_pasca."', '".$res->seafarer_code."', '".$res->is_pass."', '".$res->score_max."', '".$res->uc_score."', '".$res->status."'),";

					if (($i%50) == 0){
						$value = substr_replace($value, '', -1);
						$q_status .= "INSERT INTO `tech_status_temp` ".$field." VALUES ".$value."; ";
						$q_status .= "\n\r \n\r";

						$value = "";
					}

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$q_status .= "INSERT INTO `tech_status_temp` ".$field." VALUES ".$value."; ";	
				}
			}
			// echo $q_status;
			//	END of Generate Status Query ========================================================================

			$all_query = $q_ukp."\n\r".$q_period_his."\n\r".$q_perpar_his."\n\r".$q_score."\n\r".$q_sta_per."\n\r".$q_status;

			//$all_query = $q_sta_per."\n\r".$q_status;

			//echo $all_query;

            // Generate File Name
            $this->load->helper('download');
            $this->load->library('encrypt');
            date_default_timezone_set('Etc/GMT-7');
            
            $query = $this->period_m->detail_period_upt($uc_period);
            $row = $query->row();

            $start_date  = time_format($row->date_start, "m.d");
            $finish_date = time_format($row->date_finish, "m.d");           
            $file_name   = $row->pukp_label."-".$row->upt_label."-Period[".$row->period."][".$start_date."]-[".$finish_date."]";

            $en_query = $this->encrypt->encode($all_query);           

            force_download($file_name.".res", $en_query); 
		}
	}


	// function export_result_DUMP(){
	// 	$this->load->model('score_m');
	// 	$query = $this->score_m->get_unexported();
	// 	if ($query->num_rows() > 0) {
	// 		$result = $query->result();

	// 		$value_score 	= "";
	// 		$value_history 	= "";

	// 		foreach ($result as $res) {
	// 			$value_score .= "('".$res->uc."','".$res->uc_period."','".$res->uc_competency."','".str_replace("'", "''", $res->pra_pasca)."','".$res->seafarer_code."', '".$res->score_normal."'),";

	// 			$value_history .= "('".unique_code()."','".$res->uc_period."','".$res->seafarer_code."','".$res->period."','".str_replace("'", "''", $res->pra_pasca)."','".date('Y-m-d')."'),";
	// 		}

	// 		//	Generate Query for Score
	// 		$value_score = substr_replace($value_score, '', -1);
	// 		$q_score = "INSERT INTO `tech_score_temp` (`uc`, `uc_period`, `uc_competency`, `diklat_type`, `seafarer_code`, `score`) VALUES ";
	// 		$q_score .= $value_score."; ";

	// 		//	Generate Query for Exam History
	// 		$value_history = substr_replace($value_history, '', -1);
	// 		$q_history = "INSERT INTO `tech_history_exam_participant_temp` (`uc`, `uc_period`, `seafarer_code`, `period`,`exam_type`, `date`) VALUES ";
	// 		$q_history .= $value_history."; ";

	// 		$all_query = $q_score."\n\r".$q_history;

	// 		echo $all_query;

	// 		// encrypte query
	// 		$this->load->library('encrypt');
	// 		$all_query = $this->encrypt->encode($all_query);

	// 		$file_name   = "Result-[".date('d.m.Y')."]";

	// 		$this->load->helper('download');
	// 		date_default_timezone_set('Etc/GMT-7');

	// 		force_download($file_name.".res", $all_query);
	// 	}
	// }

	function export_result_backup($uc_level = NULL, $category = NULL, $uc_pukp = NULL, $uc_upt = NULL){
		// get pukp
		$this->load->model('pukp_m');
		$query = $this->pukp_m->get_filtered(array('uc' => $uc_pukp));
		if ($query->num_rows() > 0) {
			$row_p = $query->row();
		}

		// get upt
		$this->load->model('upt_m');
		$query = $this->upt_m->get_filtered(array('uc' => $uc_upt));
		if ($query->num_rows() > 0) {
			$row_u = $query->row();
		}

		// get level
		$this->load->model('level_m');
		$query = $this->level_m->get_filtered(array('uc' => $uc_level));
		if ($query->num_rows() > 0) {
			$row_l = $query->row();
		}

		if ($category == 1) {
			$pra_pas = "Pra";
			$category_com = "(1,3,5,7)";
		}
		elseif($category == 2)
		{
			$pra_pas = "Pasca";
			$category_com = "(2,3,6,7)";
		}
		elseif($category == 3)
		{
			$pra_pas = "DP";
			$category_com = "(4,5,6,7)";
		}

		// Generate Query Data Status
		$q_status = "";

		$this->load->model('status_m');
		// $query = $this->status_m->get_status_export($uc_pukp,$uc_upt,$uc_level,$category);

		// if ($query->num_rows() > 0) {
		// 	$value_status = "";

		// 	foreach ($query->result() as $res) {
		// 		$value_status .= "('".$res->uc."','".$res->uc_period."','".$res->uc_competency."','".$res->pra_pasca."','".str_replace("'", "''", $res->seafarer_code)."','".$res->is_pass."', '".$res->score_max."', '".$res->uc_score."'),";
		// 	}

		// 	$value_status  = substr_replace($value_status, '', -1);

		// 	$q_status .= "INSERT INTO `tech_status_temp` (`uc`, `uc_period`, `uc_competency`, `diklat_type`, `seafarer_code`, `is_pass`, `score_max`, `uc_score`) VALUES ";
		// 	$q_status .= $value_status."; ";
		// }

		// Generate QUery Data Score
		$q_score 		= "";
		$q_history 	= "";

		$query = $this->status_m->get_status_export($uc_pukp,$uc_upt,$uc_level,$category);

		if ($query->num_rows() > 0) {
			$value_score = "";
			$value_history = "";

			foreach ($query->result() as $res) {
				$value_score .= "('".$res->uc."','".$res->uc_period."','".$res->uc_competency."','".str_replace("'", "''", $res->pra_pasca)."','".$res->seafarer_code."', '".$res->score_normal."'),";

				// get uc_diklat_participant
				// $q_diklat_par = $this->status_m->get_uc_diklat_par($res->uc_period, $res->seafarer_code);
				// if ($q_diklat_par->num_rows() > 0) {
					
				// 	$row = $q_diklat_par->row();

				// 	$value_history .= "('".unique_code()."', '".$row->uc_diklat_participant."','".$res->uc_period."','".$res->seafarer_code."','".$res->period."','".str_replace("'", "''", $res->pra_pasca)."','".date('Y-m-d')."'),";
				// }
			}

			$value_score  = substr_replace($value_score, '', -1);
			$value_history  = substr_replace($value_history, '', -1);

			$q_score .= "INSERT INTO `tech_score` (`uc`, `uc_period`, `uc_competency`, `diklat_type`, `seafarer_code`, `score`) VALUES ";
			//$q_history .= "INSERT INTO `tech_history_exam_participant_temp` (`uc`, `uc_diklat_participant`, `uc_period`, `seafarer_code`, `period`,`exam_type`, `date`) VALUES ";
			$q_score .= $value_score."; ";
			//$q_history .= $value_history."; ";
		}

		echo "<br /> SCORE <br /> ".$q_score;
		echo "<hr />";
		echo "<br /> STATUS <br /> ".$q_status;

		$all_query_s = $q_status."\n\r".$q_score."\n\r".$q_history;

		$all_query = $all_query_s."\n\r".$uc_level."\n\r".$category."\n\r".$category_com;

		// Generate File Name
		$this->load->helper('download');
		date_default_timezone_set('Etc/GMT-7');

		$file_name   = $row_p->pukp_label." - ".$row_u->upt_label." - ".$row_l->label."(".$pra_pas.")-[".date('d.m.Y')."]";

		// encrypte query
		$this->load->library('encrypt');
		$en_query = $this->encrypt->encode($all_query);
		$en_query = $all_query;	

		//echo "Q : ".$en_query;

		//force_download($file_name.".res", $en_query);
	}

	function load_upt()
	{
		$data = NULL;

		$uc_pukp = $this->input->post('js_uc_pukp');

		$this->load->model('upt_m');
		$query = $this->upt_m->get_filtered(array('uc_pukp' => $uc_pukp), 'upt_label', 'ASC');
		if ($query->num_rows() > 0) {
			$data['result'] = $query->result();

		}
		$this->load->view('report/load_upt',$data);
	}

	function manage($uc_period = NULL) {
		if ($uc_period != NULL) {

			$this->load->model('session_m');
			
			$data = "";
			$period = "";
			$day = "";
			$session = "";
			$exam = "";

			$query = $this->session_m->get_manage_period($uc_period);

			if ($query->num_rows() > 0) {
				$data['row'] = $query->row();
				$result = $query->result();

				$i = 0;		//	DATE/DAY
				$curr_date = 0;
				$curr_sess = 0;
				$curr_exam = 0;

				if ($result[0]->date != NULL) {
				
					foreach ($result as $res) {

						if ($curr_date != $res->date) {		// IF DIFFERENT DATE
							$j = 0;		//	SESSION
							$k = 0;		//	EXAM
							$l = 0;		//	COMPETENCY
							
							$period[0]['period']		= $res->period;
							$period[0]['date_start']	= $res->date_start;
							$period[0]['date_finish']	= $res->date_finish;

							$day[$i]['uc_day']		= $res->uc_day;
							$day[$i]['date']		= $res->date;

								$session[$i][$j]['uc_session']		= $res->uc_session;
								$session[$i][$j]['add_time']		= $res->add_time;
								$session[$i][$j]['is_active']		= $res->is_active;

									$exam[$i][$j][$k]['exam_code']			= $res->exam_code;
									$exam[$i][$j][$k]['uc_exam']			= $res->uc_exam;
									$exam[$i][$j][$k]['level_name']			= $res->level_name;
									$exam[$i][$j][$k]['function_name']		= $res->function_name;
									$exam[$i][$j][$k]['duration']			= $res->duration;
									$exam[$i][$j][$k]['show_score']			= $res->show_score;
									$exam[$i][$j][$k]['pra_pasca']			= $res->pra_pasca;
									// $exam[$i][$j][$k]['no_participant']		= $res->count_part;

										// $comp[$i][$j][$k][$l]['uc_competency'] 		= $res->uc_competency;
										// $comp[$i][$j][$k][$l]['competency_seq'] 	= $res->sequence;
										// $comp[$i][$j][$k][$l]['competency_name'] 	= $res->label;

										$curr_exam = $res->uc_exam;

								$curr_sess = $res->uc_session;

							$curr_date = $res->date;

							$i++;
							$j++;
							$k++;
							$l++;
						} 
						
						else {	// IF SAME DATE

							if ($res->uc_session != $curr_sess) {	// IF DIFFERENT SESSION
								$k = 0;
								$l = 0;

								$session[$i-1][$j]['uc_session']	= $res->uc_session;
								$session[$i-1][$j]['add_time']		= $res->add_time;
								$session[$i-1][$j]['is_active']		= $res->is_active;

									$exam[$i-1][$j][$k]['exam_code']			= $res->exam_code;
									$exam[$i-1][$j][$k]['uc_exam']				= $res->uc_exam;
									$exam[$i-1][$j][$k]['level_name']			= $res->level_name;
									$exam[$i-1][$j][$k]['function_name']		= $res->function_name;
									$exam[$i-1][$j][$k]['duration']				= $res->duration;
									$exam[$i-1][$j][$k]['show_score']			= $res->show_score;
									$exam[$i-1][$j][$k]['pra_pasca']			= $res->pra_pasca;
									// $exam[$i-1][$j][$k]['no_participant']		= $res->count_part;

										// $comp[$i-1][$j][$k][$l]['uc_competency'] 		= $res->uc_competency;
										// $comp[$i-1][$j][$k][$l]['competency_seq'] 	= $res->sequence;
										// $comp[$i-1][$j][$k][$l]['competency_name'] 	= $res->label;

									$curr_exam = $res->uc_exam;	

								$curr_sess = $res->uc_session;

								$k++;
								$j++;
							} 
							
							else {	// IF SAME SESSION
								
								if ($res->uc_exam != $curr_exam) {	//	IF DIFFERENT EXAM
									$l = 0;

									$exam[$i-1][$j-1][$k]['exam_code']			= $res->exam_code;
									$exam[$i-1][$j-1][$k]['uc_exam']			= $res->uc_exam;
									$exam[$i-1][$j-1][$k]['level_name']			= $res->level_name;
									$exam[$i-1][$j-1][$k]['function_name']		= $res->function_name;
									$exam[$i-1][$j-1][$k]['duration']			= $res->duration;
									$exam[$i-1][$j-1][$k]['show_score']			= $res->show_score;
									$exam[$i-1][$j-1][$k]['pra_pasca']			= $res->pra_pasca;
									// $exam[$i-1][$j-1][$k]['no_participant']		= $res->count_part;

										// $comp[$i-1][$j-1][$k][$l]['uc_competency'] 		= $res->uc_competency;
										// $comp[$i-1][$j-1][$k][$l]['competency_seq'] 	= $res->sequence;	
										// $comp[$i-1][$j-1][$k][$l]['competency_name'] 	= $res->label;

									$curr_exam = $res->uc_exam;

									$k++;
									$l++;																		
								}
								else {	//	IF SAME EXAM
									// $comp[$i-1][$j-1][$k-1][$l]['uc_competency'] 	= $res->uc_competency;					
									// $comp[$i-1][$j-1][$k-1][$l]['competency_seq'] 	= $res->sequence;
									// $comp[$i-1][$j-1][$k-1][$l]['competency_name'] 	= $res->label;

									$l++;
								}
							}
						}						
					}
				}

				$ex_arr = "";
				foreach ($exam as $exa) {
					foreach ($exa as $ex) {				
						foreach ($ex as $x) {
							if ($x['uc_exam'] != NULL) {
								$ex_arr .= "'".$x['uc_exam']."',";
							}							
						}
					}
				}

				$ex_arr = substr_replace($ex_arr, '', -1);				

				if ($ex_arr != "") {
					// Get Exam Competency
					$this->load->model('exam_competency_m');
					$query = $this->exam_competency_m->get_exam_competency($ex_arr);

					$comp = "";

					if ($query->num_rows() > 0) {
						$ex_curr = 0;
						$i = 0;
						foreach ($query->result() as $res) {
							if ($ex_curr != $res->uc_exam) {
								$i = 0;
								
								$ex_curr = $res->uc_exam;
							}

							$comp[$res->uc_exam][$i]['uc_competency']		= $res->uc_competency;
							$comp[$res->uc_exam][$i]['competency_name']		= $res->competency_name;
							$comp[$res->uc_exam][$i]['sequence']			= $res->sequence;

							$i++;
						}
					}
					
					$data['comp']		= $comp;
					

					// Get Exam Package
					/*$this->load->model('exam_package_m');
					$query = $this->exam_package_m->get_package_in($ex_arr);

					$pack = "";

					if ($query->num_rows() > 0) {
						$ex_curr = 0;
						$i = 0;
						foreach ($query->result() as $res) {
							if ($ex_curr != $res->uc_exam) {
								$i = 0;
								
								$ex_curr = $res->uc_exam;
							}

							$pack[$res->uc_exam][$i]['package_code'] 	= $res->package_code;
							$pack[$res->uc_exam][$i]['uc_package'] 		= $res->uc;

							$i++;
						}
					}
					
					$data['pack']		= $pack;*/
				}
				
				$data['period']		= $period;
				$data['day']		= $day;
				$data['session']	= $session;
				$data['exam']		= $exam;
				$data['uc_period']	= $uc_period;

				if (isset($comp)) {				
					$data['comp']		= $comp;
				}

				$data['uc_period']	= $uc_period;
			}

			//	Get All Level In Period
			$this->load->model('examination_m');
			$query = $this->examination_m->get_level_in_period($uc_period);
			$data['level'] = $query->result();


			
			$this->im_render->main('report/manage', $data);

		} else {
			redirect('period');
		}
	}

	function report_by_competency($uc_period = NULL, $uc_exam = NULL, $uc_competency = NULL){
		$data = "";

		if (($uc_period != NULL) && ($uc_exam != NULL) && ($uc_competency!= NULL)) {
			//	Get Competency, Function & Level
			$this->load->model('competency_m');
			$query = $this->competency_m->get_detail_competency($uc_competency);
			if ($query->num_rows() > 0) {
				$row = $query->row();

				$data['competency_name']	= $row->competency_name;
				$data['function_name']		= $row->function_name;
				$data['level']				= $row->level;
			}

			//	Get Score
			$this->load->model('participant_m');
			$query = $this->participant_m->get_all_score($uc_period, $uc_exam, $uc_competency);

			if ($query->num_rows() > 0) {
				$data['result'] = $query->result();
			}
		}

		//Get exam
		$this->load->model('examination_m');
		$query = $this->examination_m->get_filtered(array('uc' => $uc_exam));
		if ($query->num_rows() > 0) {
			$data['row'] = $query->row();
		}

		// Get setting
		$this->load->model('setting_m');
		$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
		$data['setting'] = $q_set->row();

		$data['uc_period']		= $uc_period;
		$data['uc_exam']		= $uc_exam;
		$data['uc_competency']	= $uc_competency;

		$this->im_render->main('report/show', $data);
	}

	function report_by_competency_pdf($uc_period = NULL, $uc_exam = NULL, $uc_competency = NULL){
		$data = "";

		if (($uc_period != NULL) && ($uc_exam != NULL) && ($uc_competency!= NULL)) {

			$this->load->model('examination_m');
			$data['row'] = $this->examination_m->get_filtered(array('uc' => $uc_exam))->row();

			//Get Upt
			$this->load->model('period_m');
			$query = $this->period_m->get_list($uc_period);
			if ($query->num_rows() > 0) {
				$data['upt'] = $query->row();
			}

			//	Get Competency, Function & Level
			$this->load->model('competency_m');
			$query = $this->competency_m->get_detail_competency($uc_competency);
			if ($query->num_rows() > 0) {
				$row = $query->row();

				$data['competency_name']	= $row->competency_name;
				$data['function_name']		= $row->function_name;
				$data['level']				= $row->level;
			}

			//	Get Score
			$this->load->model('participant_m');
			$query = $this->participant_m->get_all_score($uc_period, $uc_exam, $uc_competency);

			if ($query->num_rows() > 0) {
				$data['result'] = $query->result();
			}
		}

		// Get setting
		$this->load->model('setting_m');
		$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
		$data['setting'] = $q_set->row();

		/* BEGIN Of export into pdf */
			$html = $this->load->view('report/show_pdf', $data, TRUE);

			// echo $html;

	        //this the the PDF filename that user will get to download
			$pdfFilePath = "score_recapitulation(".time_format(current_time(), "d-m-Y").").pdf";

	        //load mPDF library
			$this->load->library('m_pdf');

			ob_clean(); // cleaning the buffer before Output()

	       //generate the PDF from the given html
			$this->m_pdf->pdf->WriteHTML($html);
		
	        //download it.
			$this->m_pdf->pdf->Output($pdfFilePath, "D");
		/* END Of export into pdf */
	}

	function report_by_competency_excel($uc_period = NULL, $uc_exam = NULL, $uc_competency = NULL,$uc_day = NULL){
		$data = "";

		$this->load->helper('text');
		
		if (($uc_period != NULL) && ($uc_exam != NULL) && ($uc_competency!= NULL)) {

			$this->load->model('examination_m');
			$data['row'] = $this->examination_m->get_filtered(array('uc' => $uc_exam))->row();

			//Get Upt
			$this->load->model('period_m');
			$query = $this->period_m->get_list($uc_period);
			if ($query->num_rows() > 0) {
				$data['upt'] = $query->row();
			}

			$this->load->model('day_m');
			$data['days'] = $this->day_m->get_filtered(array('uc_period' => $uc_period,'uc' => $uc_day))->row();

			//	Get Competency, Function & Level
			$this->load->model('competency_m');
			$query = $this->competency_m->get_detail_competency($uc_competency);
			if ($query->num_rows() > 0) {
				$row = $query->row();

				$data['competency_name']			= word_limiter($row->competency_name,6,'');
				$data['competency_name_title']		= $row->competency_name;
				$data['function_name']				= $row->function_name;
				$data['level']						= $row->level;
				$data['sequence']					= $row->sequence;
			}

			//	Get Score
			$this->load->model('participant_m');
			$query = $this->participant_m->get_all_score($uc_period, $uc_exam, $uc_competency);

			if ($query->num_rows() > 0) {
				$data['result'] = $query->result();
			}
		}

		// Get setting
		$this->load->model('setting_m');
		$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
		$data['setting'] = $q_set->row();

		$this->load->view('report/show_excel_com', $data);
	}

	function exam_participant($uc_exam = NULL, $session = NULL) {
		if ($uc_exam != NULL) {
			
			$data = "";

			$this->load->model('examination_m');

			$query = $this->examination_m->get_all_participant($uc_exam,"seafarer_code","ASC");
			if ($query->num_rows() > 0) {
				$data['result'] = $query->result();
				$result 		= $query->result();

				// Get Exam Course
				$this->load->model('exam_competency_m');
				$data['res_comp'] = $this->exam_competency_m->get_competency($uc_exam)->result();
				
			}

			$curr_code = "";
			$x = 0;
			$j = 0;

			foreach ($result as $res) {
				
				if ($res->seafarer_code != $curr_code) {
					$j = 0;

					$data['ea_uc'][$x] 								= $res->ea_uc;
					$data['seafarer_code'][$x] 						= $res->seafarer_code;
					$data['full_name'][$x] 							= $res->full_name;
					$data['born_place'][$x] 						= $res->born_place;
					$data['born_date'][$x] 							= $res->born_date;
					$data['score'][$x][$j] 							= decryptIt($res->score_competency);
					$data['uc_exam_attempt_competency'][$x][$j] 	= $res->uc_exam_attempt_competency;
					$data['competency_name'][$x][$j] 				= $res->competency_name;
	
					$x++;

					$curr_code = $res->seafarer_code;
				}
				else{

					$data['ea_uc'][$x-1] 								= $res->ea_uc;
					$data['seafarer_code'][$x-1] 						= $res->seafarer_code;
					$data['full_name'][$x-1] 							= $res->full_name;
					$data['born_place'][$x-1] 							= $res->born_place;
					$data['born_date'][$x-1] 							= $res->born_date;
					$data['score'][$x-1][$j] 							= decryptIt($res->score_competency);
					$data['uc_exam_attempt_competency'][$x-1][$j] 		= $res->uc_exam_attempt_competency;
					$data['competency_name'][$x-1][$j] 					= $res->competency_name;
				}

				$j++;
				
			}

			$data['max'] 	= $x;
			$data['session'] = $session;

			// echo "<pre>";
			// print_r($data);
			// echo "<pre>";

			$this->im_render->main('report/exam_participant', $data);

		} else {
			redirect('report');
		}
	}

	function exam_participant_pdf() {
		$uc_exam = $this->input->post('f_uc_exam');

		if ($uc_exam != NULL) {
			
			$session = $this->input->post('f_sessions');
			
			$data = "";

			$this->load->model('examination_m');

			$query = $this->examination_m->get_all_participant($uc_exam, $this->input->post('f_sort_by'), $this->input->post('f_order'));
			if ($query->num_rows() > 0) {
				$data['result'] = $query->result();

				// Get Exam Course
				$this->load->model('exam_competency_m');
				$data['res_comp'] = $this->exam_competency_m->get_competency($uc_exam)->result();
			}

			$data['session'] = $session;

			/* BEGIN Of export into pdf */
				$html = $this->load->view('report/exam_participant_pdf', $data, TRUE);

				// echo $html;

		        //this the the PDF filename that user will get to download
				$pdfFilePath = "score_recapitulation(".time_format(current_time(), "d-m-Y").").pdf";

		        //load mPDF library
				$this->load->library('m_pdf');

				ob_clean(); // cleaning the buffer before Output()

		       //generate the PDF from the given html
				$this->m_pdf->pdf->WriteHTML($html);
			
		        //download it.
				$this->m_pdf->pdf->Output($pdfFilePath, "D");
			/* END Of export into pdf */

		} else {
			redirect('report');
		}
	}

	function exam_participant_excel() {
		$uc_exam = $this->input->post('f_uc_exam');

		if ($uc_exam != NULL) {

			$session = $this->input->post('f_sessions');
			
			$data = "";

			$this->load->model('examination_m');

			$query = $this->examination_m->get_all_participant($uc_exam, $this->input->post('f_sort_by'), $this->input->post('f_order'));
			if ($query->num_rows() > 0) {
				$data['result'] = $query->result();

				// Get Exam Course
				$this->load->model('exam_competency_m');
				$data['res_comp'] = $this->exam_competency_m->get_competency($uc_exam)->result();
			}

			$data['session'] = $session;

			$this->load->view('report/exam_participant_excel',$data);

		} else {
			redirect('report');
		}
	}

	function get_partipant_by_name_exam() {
		$data = "";

		if ($this->input->post('js_name') != "") {
			
			$this->load->model('participant_m');

			$query = $this->participant_m->get_info_search_exam($this->input->post('js_uc_exam'), $this->input->post('js_name'));

		} else {

			$this->load->model('examination_m');
			
			$query = $this->examination_m->get_all_participant($this->input->post('js_uc_exam'), "seafarer_code", "ASC");
		}

		if ($query->num_rows() > 0) {
			$data['result'] = $query->result();
			$result 		= $query->result();
		}

		// Get Exam Course
		$this->load->model('exam_competency_m');
		$data['res_comp'] = $this->exam_competency_m->get_competency($this->input->post('js_uc_exam'))->result();

		$curr_code = 0;
		$x = 0;
		$j = 0;

		if (isset($result)) {
			foreach ($result as $res) {
				
				if ($res->seafarer_code != $curr_code) {
					$j = 0;

					$data['ea_uc'][$x] 									= $res->ea_uc;
					$data['seafarer_code'][$x] 							= $res->seafarer_code;
					$data['full_name'][$x] 								= $res->full_name;
					$data['born_place'][$x] 							= $res->born_place;
					$data['born_date'][$x] 								= $res->born_date;
					$data['score'][$x][$j] 								= decryptIt($res->score_competency);
					$data['uc_exam_attempt_competency'][$x][$j] 		= $res->uc_exam_attempt_competency;

					$x++;

					$curr_code = $res->seafarer_code;
				}
				else{

					$data['ea_uc'][$x-1] 								= $res->ea_uc;
					$data['seafarer_code'][$x-1] 						= $res->seafarer_code;
					$data['full_name'][$x-1] 							= $res->full_name;
					$data['born_place'][$x-1] 							= $res->born_place;
					$data['born_date'][$x-1] 							= $res->born_date;
					$data['score'][$x-1][$j] 							= decryptIt($res->score_competency);
					$data['uc_exam_attempt_competency'][$x-1][$j] 		= $res->uc_exam_attempt_competency;
				}

				$j++;	
			}
		}

		$data['max'] 		= $x;

		$data['session'] = $this->input->post('js_session');

		$this->load->view('report/get_participant_exam', $data);
	}

	function get_partipant_order() {
		$data = "";

		$this->load->model('examination_m');

		$uc_exam = $this->input->post('js_uc_exam');
		$order 	 = $this->input->post('js_sort_by');
		$sort 	 = $this->input->post('js_sort');

		if ($order != NULL) {
			$order = $this->input->post('js_sort_by');
		}else{
			$order = "seafarer_code";			
		}

		if ($sort != NULL) {
			$sort = $this->input->post('js_sort');
		}else{
			$sort = "ASC";			
		}

		$query = $this->examination_m->get_all_participant($uc_exam, $order, $sort);
		if ($query->num_rows() > 0) {
			$data['result'] = $query->result();
			$result 		= $query->result();

			// Get Exam Course
			$this->load->model('exam_competency_m');
			$data['res_comp'] = $this->exam_competency_m->get_competency($uc_exam)->result();
		}

		$curr_code = 0;
		$x = 0;
		$j = 0;

		foreach ($result as $res) {
			
			if ($res->seafarer_code != $curr_code) {
				$j = 0;

				$data['ea_uc'][$x] 									= $res->ea_uc;
				$data['seafarer_code'][$x] 							= $res->seafarer_code;
				$data['full_name'][$x] 								= $res->full_name;
				$data['born_place'][$x] 							= $res->born_place;
				$data['born_date'][$x] 								= $res->born_date;
				$data['score'][$x][$j] 								= decryptIt($res->score_competency);
				$data['uc_exam_attempt_competency'][$x][$j] 		= $res->uc_exam_attempt_competency;

				$x++;

				$curr_code = $res->seafarer_code;
			}
			else{

				$data['ea_uc'][$x-1] 								= $res->ea_uc;
				$data['seafarer_code'][$x-1] 						= $res->seafarer_code;
				$data['full_name'][$x-1] 							= $res->full_name;
				$data['born_place'][$x-1] 							= $res->born_place;
				$data['born_date'][$x-1] 							= $res->born_date;
				$data['score'][$x-1][$j] 							= decryptIt($res->score_competency);
				$data['uc_exam_attempt_competency'][$x-1][$j] 		= $res->uc_exam_attempt_competency;
			}

			$j++;
			
		}

		$data['max'] 		= $x;
		$data['session'] 	= $this->input->post('js_session');

		$this->load->view('report/get_participant_exam', $data);
	}

	function detail_exam_student($uc_exam = NULL, $session = NULL, $att_uc = NULL){
		if ($att_uc != NULL) {
		
			$data = "";

			$this->load->model('exam_attempt_m');
			$query = $this->exam_attempt_m->get_by_user($att_uc);

			if ($query->num_rows() > 0) {
				$data['row'] = $query->row();
				$row = $query->row();

				$data['keys']			= explode(',', $row->keys);
				$data['answers']		= explode(',', $row->answers);
				$data['pairs']			= explode(',', $row->pairs);
				$data['resans']	 		= explode(',', $row->answer_result);

				$question = explode(',', $row->questions);
				$uc_question = "";
				foreach ($question as $qy) {
					$uc_question .= "'".$qy."'".',';
				}

				$question_uc	= substr_replace($uc_question, '', -1);

				// echo $question_uc;
					
				$query = $this->exam_attempt_m->get_my_review($question_uc);
				if ($query->num_rows() > 0) {
					$result = $query->result();

					//	Break question and answer into multi demensional array
					$curr_id = 0;
					$i = 0;
					$j = 0;
					foreach($result as $res) {
						if ($res->id != $curr_id) {
							$j = 0;

							$data['question_text_en'][$i] 			= htmlspecialchars_decode(stripslashes($res->question_text_en));
							$data['question_text_in'][$i]			= htmlspecialchars_decode(stripslashes($res->question_text_in));
							$data['question_type'][$i] 				= htmlspecialchars_decode(stripslashes($res->question_type));
							$data['q_att_type'][$i] 				= $res->question_att_type;
							$data['q_att_file'][$i] 				= $res->question_att_file;
							$data['exam_code'][$i]					= $res->exam_code;

							if ($res->question_type == 1) {
								$data['option_id'][$i][$j] 		= $res->option_id;
								$data['option_uc'][$i][$j] 		= $res->option_uc;
								$data['option_text_in'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
								$data['option_text_en'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));
								$data['answer_multi'][$i][$j]	= $res->answer_multiplechoice;	
								$data['kunci'][$i]				= $data['keys'][$i];

								$data['key_text'][$res->option_id] 	= htmlspecialchars_decode(stripslashes($res->option_text_en));
							}

							if ($res->question_type == 2) {
								$data['answer_truefalse'][$i][$j] = $res->answer_truefalse;
							}
							
							if ($res->question_type == 3) {						
								$data['match_key'][$i] 	= explode('-', $data['keys'][$i]);
								$data['match_ans'][$i] 	= explode('-', $data['answers'][$i]);
								$data['match_pair'][$i] = explode('-', $data['pairs'][$i]);
							}

							$i++;
							
							$curr_id = $res->id;
						} else {
							$data['question_type'][$i-1] = $res->question_type;

							if ($res->question_type == 1) {
								$data['option_id'][$i-1][$j] 		= $res->option_id;
								$data['option_uc'][$i-1][$j] 		= $res->option_uc;
								$data['option_text_in'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
								$data['option_text_en'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));		
							 	$data['o_att_type'][$i-1][$j]  	= $res->option_att_type;
							 	$data['o_att_file'][$i-1][$j]  	= $res->option_att_file;
							 	$data['answer_multi'][$i-1][$j]	= $res->answer_multiplechoice;	
							 	// $data['bobot_option'][$i-1][$j]		= $res->bobot;							
							}

							if ($res->question_type == 2) {
								$data['answer_truefalse'][$i-1][$j] = $res->answer_truefalse;
							}
						}



						//	Assign Question, Answer & Pair Value, Indexed by pair_id
						if ($res->question_type == 3) {
							$data['question_field_in'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_in));
							$data['question_field_en'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_en));
							$data['m_q_type'][$res->pair_id] 			= $res->m_q_type;
							$data['m_q_file'][$res->pair_id] 			= $res->m_q_file;

							$data['answer_field_in'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_in));
							$data['answer_field_en'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_en));
							$data['m_a_type'][$res->pair_id]			= $res->m_a_type;
							$data['m_a_file'][$res->pair_id]			= $res->m_a_file;

						}

						$j++;
						$data['max_option'][$i-1] = $j;
					}

					$data['max_question'] 	= $i;
				}
				$data['att_code'] 		= $att_uc;
				$data['uc_exam'] 		= $row->uc_exam;
				$data['is_language']	= $row->is_language;
				//$data['session']		= $session;

			}


			//$this->im_render->main('report/detail_exam_participant', $data);

		} else {
			
			if ($uc_exam != NULL) {
				redirect('report/exam_participant/'.$uc_exam.'/'.$session);
			} else {
				redirect('report/participant');
			}

		}
	}

	function detail_exam_student_pdf($uc_exam = NULL, $session = NULL, $att_uc = NULL){
		if ($att_uc != NULL) {
		
			$data = "";

			$this->load->model('exam_attempt_m');
			$query = $this->exam_attempt_m->get_by_user($att_uc);

			if ($query->num_rows() > 0) {
				$data['row'] = $query->row();
				$row = $query->row();

				$data['keys']			= explode(',', $row->keys);
				$data['answers']		= explode(',', $row->answers);
				$data['pairs']			= explode(',', $row->pairs);
				$data['resans']	 		= explode(',', $row->answer_result);

				$question = explode(',', $row->questions);
				$uc_question = "";
				foreach ($question as $qy) {
					$uc_question .= "'".$qy."'".',';
				}

				$question_uc	= substr_replace($uc_question, '', -1);

				// echo $question_uc;
					
				$query = $this->exam_attempt_m->get_my_review($question_uc);
				if ($query->num_rows() > 0) {
					$result = $query->result();

					//	Break question and answer into multi demensional array
					$curr_id = 0;
					$i = 0;
					$j = 0;
					foreach($result as $res) {
						if ($res->id != $curr_id) {
							$j = 0;

							$data['question_text_en'][$i] 			= htmlspecialchars_decode(stripslashes($res->question_text_en));
							$data['question_text_in'][$i]			= htmlspecialchars_decode(stripslashes($res->question_text_in));
							$data['question_type'][$i] 				= htmlspecialchars_decode(stripslashes($res->question_type));
							$data['q_att_type'][$i] 				= $res->question_att_type;
							$data['q_att_file'][$i] 				= $res->question_att_file;
							$data['exam_code'][$i]					= $res->exam_code;

							if ($res->question_type == 1) {
								$data['option_id'][$i][$j] 		= $res->option_id;
								$data['option_text_in'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
								$data['option_text_en'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));
								$data['answer_multi'][$i][$j]	= $res->answer_multiplechoice;	
								$data['kunci'][$i]				= $data['keys'][$i];

								$data['key_text'][$res->option_id] 	= htmlspecialchars_decode(stripslashes($res->option_text_en));
							}

							if ($res->question_type == 2) {
								$data['answer_truefalse'][$i][$j] = $res->answer_truefalse;
							}
							
							if ($res->question_type == 3) {						
								$data['match_key'][$i] 	= explode('-', $data['keys'][$i]);
								$data['match_ans'][$i] 	= explode('-', $data['answers'][$i]);
								$data['match_pair'][$i] = explode('-', $data['pairs'][$i]);
							}

							$i++;
							
							$curr_id = $res->id;
						} else {
							$data['question_type'][$i-1] = $res->question_type;

							if ($res->question_type == 1) {
								$data['option_id'][$i-1][$j] 		= $res->option_id;
								$data['option_text_in'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
								$data['option_text_en'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));		
							 	$data['o_att_type'][$i-1][$j]  	= $res->option_att_type;
							 	$data['o_att_file'][$i-1][$j]  	= $res->option_att_file;
							 	$data['answer_multi'][$i-1][$j]	= $res->answer_multiplechoice;
							}

							if ($res->question_type == 2) {
								$data['answer_truefalse'][$i-1][$j] = $res->answer_truefalse;
							}
						}



						//	Assign Question, Answer & Pair Value, Indexed by pair_id
						if ($res->question_type == 3) {
							$data['question_field_in'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_in));
							$data['question_field_en'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_en));
							$data['m_q_type'][$res->pair_id] 			= $res->m_q_type;
							$data['m_q_file'][$res->pair_id] 			= $res->m_q_file;

							$data['answer_field_in'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_in));
							$data['answer_field_en'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_en));
							$data['m_a_type'][$res->pair_id]			= $res->m_a_type;
							$data['m_a_file'][$res->pair_id]			= $res->m_a_file;

						}

						$j++;
						$data['max_option'][$i-1] = $j;
					}

					$data['max_question'] 	= $i;
					$data['att_code'] 		= $att_uc;
					$data['uc_exam'] 		= $row->uc_exam;
					$data['is_language']	= $row->is_language;
				}

			}

			/* BEGIN Of export into pdf */
				$html = $this->load->view('report/detail_exam_student_pdf', $data, TRUE);

				// echo $html;

		        //this the the PDF filename that user will get to download
				$pdfFilePath = "participant_report(".time_format(current_time(), "d-m-Y").").pdf";

		        //load mPDF library
				$this->load->library('m_pdf');

				ob_clean(); // cleaning the buffer before Output()

		       //generate the PDF from the given html
				$this->m_pdf->pdf->WriteHTML($html);
			
		        //download it.
				$this->m_pdf->pdf->Output($pdfFilePath, "D");
			/* END Of export into pdf */

		} else {
			if ($uc_exam != NULL) {
				redirect('report/exam_participant/'.$uc_exam.'/'.$session);
			} else {
				redirect('report/participant');
			}
		}
	}

	/* BEGIN Of Participant Report */
		function participant() {
			// $data = "";

			// $this->load->model('participant_m');

			// $query = $this->participant_m->get_all_info();
			// if ($query->num_rows() > 0) {
			// 	$data['result'] = $query->result();
			// }

			// $this->im_render->main('report/list_participant');

			$this->im_render->main('under_construction');
		}

		function get_competency(){
			$data = "";
			$uc_function = $this->input->post('js_uc_function');

			$this->load->model('competency_m');
			$query = $this->competency_m->get_filtered(array('uc_function' => $uc_function));
			if ($query->num_rows() > 0 ) {
				$data['result'] = $query->result();
			}

			$this->load->view('report/get_competency',$data);
		}

		function get_examination() {
			$data = "";

			$query = $this->examination_m->get_filtered_info($this->input->post('js_uc_function'), $this->input->post('js_uc_competency'));
			if ($query->num_rows() > 0) {
				$data['result'] = $query->result();
			}

			$this->load->view('report/get_examination', $data);
		}

		function get_participant_by_level() {
			$data = "";

			$this->load->model('participant_m');

			$query = $this->participant_m->get_all_info($this->input->post('js_uc_level'));
			if ($query->num_rows() > 0) {
				$data['result'] = $query->result();
			}

			$this->load->view('report/get_participant', $data);
		}

		function get_partipant_by_name() {
			$data = "";

			$this->load->model('participant_m');

			$query = $this->participant_m->get_info_search($this->input->post('js_name'));
			if ($query->num_rows() > 0) {
				$data['result'] = $query->result();
			}

			$this->load->view('report/get_participant', $data);
		}

		/*function browse($uc_exam = NULL) {
			if ($uc_exam != NULL) {
				
				$data = "";

				$query = $this->examination_m->get_all_score($uc_exam);
				if ($query->num_rows() > 0) {
					$data['result'] = $query->result();
				}

				$this->im_render->main('report/report', $data);

			} else {
				redirect('report');
			}
		}*/

		/*function report_pdf($uc_exam = NULL) {
			if ($uc_exam != NULL) {
				
				$data = "";

				$query = $this->examination_m->get_all_score($uc_exam);
				if ($query->num_rows() > 0) {
					$data['result'] = $query->result();
				}

				 // BEGIN Of export into pdf 
					$html = $this->load->view('report/report_pdf', $data, TRUE);

					// echo $html;

			        //this the the PDF filename that user will get to download
					$pdfFilePath = "score_recapitulation_report(".time_format(current_time(), "d-m-Y").").pdf";

			        //load mPDF library
					$this->load->library('m_pdf');

					ob_clean(); // cleaning the buffer before Output()

			       //generate the PDF from the given html
					$this->m_pdf->pdf->WriteHTML($html);
				
			        //download it.
					$this->m_pdf->pdf->Output($pdfFilePath, "D");
				 // END Of export into pdf 

			} else {
				redirect('report');
			}
		}*/

		function detail($seafarer_code = NULL, $att_uc = NULL){
			if ($att_uc != NULL) {
			
				$data = "";

				$this->load->model('exam_attempt_m');
				$query = $this->exam_attempt_m->get_by_user($att_uc);

				if ($query->num_rows() > 0) {
					$data['row'] = $query->row();
					$row = $query->row();

					$data['keys']			= explode(',', $row->keys);
					$data['answers']		= explode(',', $row->answers);
					$data['pairs']			= explode(',', $row->pairs);
					$data['resans']	 		= explode(',', $row->answer_result);

					$question = explode(',', $row->questions);
					$uc_question = "";
					foreach ($question as $qy) {
						$uc_question .= "'".$qy."'".',';
					}

					$question_uc	= substr_replace($uc_question, '', -1);

					// echo $question_uc;
						
					$query = $this->exam_attempt_m->get_my_review($question_uc);
					if ($query->num_rows() > 0) {
						$result = $query->result();

						//	Break question and answer into multi demensional array
						$curr_id = 0;
						$i = 0;
						$j = 0;
						foreach($result as $res) {
							if ($res->id != $curr_id) {
								$j = 0;

								$data['question_text_en'][$i] 			= htmlspecialchars_decode(stripslashes($res->question_text_en));
								$data['question_text_in'][$i]			= htmlspecialchars_decode(stripslashes($res->question_text_in));
								$data['question_type'][$i] 				= htmlspecialchars_decode(stripslashes($res->question_type));
								$data['q_att_type'][$i] 				= $res->question_att_type;
								$data['q_att_file'][$i] 				= $res->question_att_file;
								$data['exam_code'][$i]					= $res->exam_code;

								if ($res->question_type == 1) {
									$data['option_id'][$i][$j] 		= $res->option_id;
									$data['option_text_in'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
									$data['option_text_en'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));
									$data['answer_multi'][$i][$j]	= $res->answer_multiplechoice;	
									$data['kunci'][$i]				= $data['keys'][$i];

									$data['key_text'][$res->option_id] 	= htmlspecialchars_decode(stripslashes($res->option_text_en));
								}

								if ($res->question_type == 2) {
									$data['answer_truefalse'][$i][$j] = $res->answer_truefalse;
								}
								
								if ($res->question_type == 3) {						
									$data['match_key'][$i] 	= explode('-', $data['keys'][$i]);
									$data['match_ans'][$i] 	= explode('-', $data['answers'][$i]);
									$data['match_pair'][$i] = explode('-', $data['pairs'][$i]);
								}

								$i++;
								
								$curr_id = $res->id;
							} else {
								$data['question_type'][$i-1] = $res->question_type;

								if ($res->question_type == 1) {
									$data['option_id'][$i-1][$j] 		= $res->option_id;
									$data['option_text_in'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
									$data['option_text_en'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));		
								 	$data['o_att_type'][$i-1][$j]  	= $res->option_att_type;
								 	$data['o_att_file'][$i-1][$j]  	= $res->option_att_file;
								 	$data['answer_multi'][$i-1][$j]	= $res->answer_multiplechoice;	
								 	// $data['bobot_option'][$i-1][$j]		= $res->bobot;							
								}

								if ($res->question_type == 2) {
									$data['answer_truefalse'][$i-1][$j] = $res->answer_truefalse;
								}
							}



							//	Assign Question, Answer & Pair Value, Indexed by pair_id
							if ($res->question_type == 3) {
								$data['question_field_in'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_in));
								$data['question_field_en'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_en));
								$data['m_q_type'][$res->pair_id] 			= $res->m_q_type;
								$data['m_q_file'][$res->pair_id] 			= $res->m_q_file;

								$data['answer_field_in'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_in));
								$data['answer_field_en'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_en));
								$data['m_a_type'][$res->pair_id]			= $res->m_a_type;
								$data['m_a_file'][$res->pair_id]			= $res->m_a_file;

							}

							$j++;
							$data['max_option'][$i-1] = $j;
						}

						$data['max_question'] 	= $i;
						$data['att_code'] 		= $att_uc;
						$data['uc_exam'] 		= $row->uc_exam;
						$data['is_language']	= $row->is_language;
						$data['seafarer_code']	= $seafarer_code;
					}

				}

				$this->im_render->main('report/detail_exam_student', $data);

			} else {
				
				if ($seafarer_code != NULL) {
					redirect('report/detail_participant/'.$seafarer_code);
				} else {
					redirect('report/participant');
				}

			}
		}

		function detail_pdf($seafarer_code = NULL, $att_uc = NULL){
			if ($att_uc != NULL) {
			
				$data = "";

				$this->load->model('exam_attempt_m');
				$query = $this->exam_attempt_m->get_by_user($att_uc);

				if ($query->num_rows() > 0) {
					$data['row'] = $query->row();
					$row = $query->row();

					$data['keys']			= explode(',', $row->keys);
					$data['answers']		= explode(',', $row->answers);
					$data['pairs']			= explode(',', $row->pairs);
					$data['resans']	 		= explode(',', $row->answer_result);

					$question = explode(',', $row->questions);
					$uc_question = "";
					foreach ($question as $qy) {
						$uc_question .= "'".$qy."'".',';
					}

					$question_uc	= substr_replace($uc_question, '', -1);

					// echo $question_uc;
						
					$query = $this->exam_attempt_m->get_my_review($question_uc);
					if ($query->num_rows() > 0) {
						$result = $query->result();

						//	Break question and answer into multi demensional array
						$curr_id = 0;
						$i = 0;
						$j = 0;
						foreach($result as $res) {
							if ($res->id != $curr_id) {
								$j = 0;

								$data['question_text_en'][$i] 			= htmlspecialchars_decode(stripslashes($res->question_text_en));
								$data['question_text_in'][$i]			= htmlspecialchars_decode(stripslashes($res->question_text_in));
								$data['question_type'][$i] 				= htmlspecialchars_decode(stripslashes($res->question_type));
								$data['q_att_type'][$i] 				= $res->question_att_type;
								$data['q_att_file'][$i] 				= $res->question_att_file;
								$data['exam_code'][$i]					= $res->exam_code;

								if ($res->question_type == 1) {
									$data['option_id'][$i][$j] 		= $res->option_id;
									$data['option_text_in'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
									$data['option_text_en'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));
									$data['answer_multi'][$i][$j]	= $res->answer_multiplechoice;	
									$data['kunci'][$i]				= $data['keys'][$i];

									$data['key_text'][$res->option_id] 	= htmlspecialchars_decode(stripslashes($res->option_text_en));
								}

								if ($res->question_type == 2) {
									$data['answer_truefalse'][$i][$j] = $res->answer_truefalse;
								}
								
								if ($res->question_type == 3) {						
									$data['match_key'][$i] 	= explode('-', $data['keys'][$i]);
									$data['match_ans'][$i] 	= explode('-', $data['answers'][$i]);
									$data['match_pair'][$i] = explode('-', $data['pairs'][$i]);
								}

								$i++;
								
								$curr_id = $res->id;
							} else {
								$data['question_type'][$i-1] = $res->question_type;

								if ($res->question_type == 1) {
									$data['option_id'][$i-1][$j] 		= $res->option_id;
									$data['option_text_in'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
									$data['option_text_en'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));		
								 	$data['o_att_type'][$i-1][$j]  	= $res->option_att_type;
								 	$data['o_att_file'][$i-1][$j]  	= $res->option_att_file;
								 	$data['answer_multi'][$i-1][$j]	= $res->answer_multiplechoice;
								}

								if ($res->question_type == 2) {
									$data['answer_truefalse'][$i-1][$j] = $res->answer_truefalse;
								}
							}



							//	Assign Question, Answer & Pair Value, Indexed by pair_id
							if ($res->question_type == 3) {
								$data['question_field_in'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_in));
								$data['question_field_en'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_en));
								$data['m_q_type'][$res->pair_id] 			= $res->m_q_type;
								$data['m_q_file'][$res->pair_id] 			= $res->m_q_file;

								$data['answer_field_in'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_in));
								$data['answer_field_en'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_en));
								$data['m_a_type'][$res->pair_id]			= $res->m_a_type;
								$data['m_a_file'][$res->pair_id]			= $res->m_a_file;

							}

							$j++;
							$data['max_option'][$i-1] = $j;
						}

						$data['max_question'] 	= $i;
						$data['att_code'] 		= $att_uc;
						$data['uc_exam'] 		= $row->uc_exam;
						$data['is_language']	= $row->is_language;
					}

				}

				/* BEGIN Of export into pdf */
					$html = $this->load->view('report/detail_exam_student_pdf', $data, TRUE);

					// echo $html;

			        //this the the PDF filename that user will get to download
					$pdfFilePath = "participant_report(".time_format(current_time(), "d-m-Y").").pdf";

			        //load mPDF library
					$this->load->library('m_pdf');

					ob_clean(); // cleaning the buffer before Output()

			       //generate the PDF from the given html
					$this->m_pdf->pdf->WriteHTML($html);
				
			        //download it.
					$this->m_pdf->pdf->Output($pdfFilePath, "D");
				/* END Of export into pdf */

			} else {
				if ($seafarer_code != NULL) {
					redirect('report/detail_participant/'.$seafarer_code);
				} else {
					redirect('report/participant');
				}
			}
		}

		function detail_participant($seafarer_code = NULL) {
			if ($seafarer_code != NULL) {
				$data = "";

				$this->load->model('participant_m');

				$q_filter = $this->participant_m->get_filtered_info($seafarer_code);
				if ($q_filter->num_rows() > 0) {
					$data['row'] = $q_filter->row();

					$query = $this->participant_m->get_all_report_student($seafarer_code);
					if ($query->num_rows() > 0) {
						$result = $query->result();

						$i = 0;
						$curr_func = 0;
						$curr_comp = 0;

						foreach ($result as $res) {
							if ($res->uc_function != $curr_func) {

								$j = 0;
								$k = 0;

								$participant[0]['seafarer_code'] = $res->seafarer_code;

								// Function
								$func[$i]['function_name']	= $res->function_name;
								$func[$i]['uc_function']	= $res->uc_function;
									// Competency
									$comp[$i][$j]['competency_name']	= $res->competency_name;
									$comp[$i][$j]['uc_competency']		= $res->uc_competency;
									$comp[$i][$j]['max_score']			= $res->max_score;
										// Examination
										$exam[$i][$j][$k]['exam_code']	= $res->exam_code;
										$exam[$i][$j][$k]['periode']	= $res->period;
										$exam[$i][$j][$k]['time_exam']	= $res->time_exam;
										$exam[$i][$j][$k]['score']		= $res->score;
										$exam[$i][$j][$k]['uc_attempt']		= $res->uc_attempt;

								$curr_func = $res->uc_function;

								$i++;
								$j++;
								$k++;
							} else {

								if ($res->uc_competency != $curr_comp) {

									$k = 0;

									// Competency
									$comp[$i-1][$j]['competency_name']	= $res->competency_name;
									$comp[$i-1][$j]['uc_competency']	= $res->uc_competency;
									$comp[$i-1][$j]['max_score']		= $res->max_score;
										// Examination
										$exam[$i-1][$j][$k]['exam_code']	= $res->exam_code;
										$exam[$i-1][$j][$k]['periode']		= $res->period;
										$exam[$i-1][$j][$k]['time_exam']	= $res->time_exam;
										$exam[$i-1][$j][$k]['score']		= $res->score;
										$exam[$i-1][$j][$k]['uc_attempt']		= $res->uc_attempt;

									
									$curr_comp = $res->uc_competency;

									$j++;
								} else {
									// Examination
									$exam[$i-1][$j-1][$k]['exam_code']	= $res->exam_code;
									$exam[$i-1][$j-1][$k]['periode']	= $res->period;
									$exam[$i-1][$j-1][$k]['time_exam']	= $res->time_exam;
									$exam[$i-1][$j-1][$k]['score']		= $res->score;
									$exam[$i-1][$j-1][$k]['uc_attempt']	= $res->uc_attempt;
								}
								
								$k++;

							}
						}

						$data['participant']	= $participant;
						$data['function']		= $func;
						$data['competency']		= $comp;
						$data['examination']	= $exam;
					}

				}

				$this->im_render->main('report/detail_participant', $data);
			}
		}

		function detail_participant_pdf($seafarer_code = NULL) {
			if ($seafarer_code != NULL) {
				$data = "";

				$this->load->model('participant_m');

				$q_filter = $this->participant_m->get_filtered_info($seafarer_code);
				if ($q_filter->num_rows() > 0) {
					$data['row'] = $q_filter->row();

					$query = $this->participant_m->get_all_report_student($seafarer_code);
					if ($query->num_rows() > 0) {
						$result = $query->result();

						$i = 0;
						$curr_func = 0;
						$curr_comp = 0;

						foreach ($result as $res) {
							if ($res->uc_function != $curr_func) {

								$j = 0;
								$k = 0;

								$participant[0]['seafarer_code'] = $res->seafarer_code;

								// Function
								$func[$i]['function_name']	= $res->function_name;
								$func[$i]['uc_function']	= $res->uc_function;
									// Competency
									$comp[$i][$j]['competency_name']	= $res->competency_name;
									$comp[$i][$j]['uc_competency']		= $res->uc_competency;
									$comp[$i][$j]['max_score']			= $res->max_score;
										// Examination
										$exam[$i][$j][$k]['exam_code']	= $res->exam_code;
										$exam[$i][$j][$k]['periode']	= $res->period;
										$exam[$i][$j][$k]['time_exam']	= $res->time_exam;
										$exam[$i][$j][$k]['score']		= $res->score;
										$exam[$i][$j][$k]['uc_attempt']		= $res->uc_attempt;

								$curr_func = $res->uc_function;

								$i++;
								$j++;
								$k++;
							} else {

								if ($res->uc_competency != $curr_comp) {

									$k = 0;

									// Competency
									$comp[$i-1][$j]['competency_name']	= $res->competency_name;
									$comp[$i-1][$j]['uc_competency']	= $res->uc_competency;
									$comp[$i-1][$j]['max_score']		= $res->max_score;
										// Examination
										$exam[$i-1][$j][$k]['exam_code']	= $res->exam_code;
										$exam[$i-1][$j][$k]['periode']		= $res->period;
										$exam[$i-1][$j][$k]['time_exam']	= $res->time_exam;
										$exam[$i-1][$j][$k]['score']		= $res->score;
										$exam[$i-1][$j][$k]['uc_attempt']		= $res->uc_attempt;

									
									$curr_comp = $res->uc_competency;

									$j++;
								} else {
									// Examination
									$exam[$i-1][$j-1][$k]['exam_code']	= $res->exam_code;
									$exam[$i-1][$j-1][$k]['periode']	= $res->period;
									$exam[$i-1][$j-1][$k]['time_exam']	= $res->time_exam;
									$exam[$i-1][$j-1][$k]['score']		= $res->score;
									$exam[$i-1][$j-1][$k]['uc_attempt']	= $res->uc_attempt;
								}
								
								$k++;

							}
						}

						$data['participant']	= $participant;
						$data['function']		= $func;
						$data['competency']		= $comp;
						$data['examination']	= $exam;
					}

				}

				/* BEGIN Of export into pdf */
					$html = $this->load->view('report/detail_participant_pdf', $data, TRUE);

					// echo $html;

			        //this the the PDF filename that user will get to download
					$pdfFilePath = "participant_detail(".time_format(current_time(), "d-m-Y").").pdf";

			        //load mPDF library
					$this->load->library('m_pdf');

					ob_clean(); // cleaning the buffer before Output()

			       //generate the PDF from the given html
					$this->m_pdf->pdf->WriteHTML($html);
				
			        //download it.
					$this->m_pdf->pdf->Output($pdfFilePath, "D");
				/* END Of export into pdf */
			}
		}
	/* END Of Participant Report */


	function examination_quality($uc_period = NULL, $uc_exam = NULL, $session = NULL) {
		if ($uc_exam != NULL) {
			
			$data = "";

			$this->load->model('examination_m');

			$query = $this->examination_m->show_exam_detail($uc_exam);
			if ($query->num_rows() > 0) {
				$res = $query->result();
				$data['row'] = $query->row();

				$questions = explode(",", $res[0]->questions);
				// Sort question
				asort($questions);

				$uc_question = "";
				foreach ($questions as $qs) {
					$uc_question .= "'".$qs."', ";
				}
				$uc_question = substr_replace($uc_question, "", -2);

				$ques = array();
				// $answ = array();
				$z = 0;
				foreach ($query->result() as $res) {
					$answered = array_combine(explode(",", $res->questions), explode(",", $res->answers));
					ksort($answered);
					$data['student_answered'][$z] = $answered;
					// $data['answered'] = ksort($answers);
					$z++;
				}

				$q_exam = $this->examination_m->show_exam($uc_exam, $uc_question);
				if ($q_exam->num_rows() > 0) {
					$i = 0;
					$curr_que = 0;
					foreach ($q_exam->result() as $ex) {
						if ($curr_que != $ex->uc_question) {
							$j = 0;

							$question[$i]['uc_question']		= $ex->uc_question;
							$question[$i]['question_title_in']	= read_text($ex->question_title_in);
							$question[$i]['question_title_en']	= read_text($ex->question_title_en);
							$question[$i]['question_text_in']	= read_text($ex->question_text_in);
							$question[$i]['question_text_en']	= read_text($ex->question_text_en);
							$question[$i]['question_att_type']	= $ex->question_att_type;
							$question[$i]['question_att_file']	= $ex->question_att_file;
							$question[$i]['question_type']		= $ex->question_type;
							$question[$i]['answer_tf']			= $ex->answer_truefalse;
							$question[$i]['answer_mc']			= $ex->answer_multiplechoice;

								
								$option[$i][$j]['option_id']		= $ex->option_id;
								$option[$i][$j]['option_id_ass']	= $ex->option_id;
								$option[$i][$j]['option_text_in']	= read_text($ex->option_text_in);
								$option[$i][$j]['option_text_en']	= read_text($ex->option_text_en);
								$option[$i][$j]['option_att_type']	= $ex->option_att_type;
								$option[$i][$j]['option_att_file']	= $ex->option_att_file;
								$option[$i][$j]['is_correct']		= $ex->is_correct;

								
								$match[$i][$j]['question_field_in']	= read_text($ex->question_field_in);
								$match[$i][$j]['question_field_en']	= read_text($ex->question_field_en);
								$match[$i][$j]['field_att_type']	= read_text($ex->field_att_type);
								$match[$i][$j]['field_att_file']	= read_text($ex->field_att_file);
								$match[$i][$j]['answer_field_in']	= read_text($ex->answer_field_in);
								$match[$i][$j]['answer_field_en']	= read_text($ex->answer_field_en);
								$match[$i][$j]['answer_att_type']	= $ex->answer_att_type;
								$match[$i][$j]['answer_att_file']	= $ex->answer_att_file;

							$curr_que = $ex->uc_question;
							$i++;
							$j++;

						} else {

							$option[$i-1][$j]['option_id']			= $ex->option_id;
							$option[$i-1][$j]['option_id_ass']		= $ex->option_id;
							$option[$i-1][$j]['option_text_in']		= read_text($ex->option_text_in);
							$option[$i-1][$j]['option_text_en']		= read_text($ex->option_text_en);
							$option[$i-1][$j]['option_att_type']	= $ex->option_att_type;
							$option[$i-1][$j]['option_att_file']	= $ex->option_att_file;
							$option[$i-1][$j]['is_correct']			= $ex->is_correct;

							$match[$i-1][$j]['match_id']			= $ex->match_id;
							$match[$i-1][$j]['question_field_in']	= read_text($ex->question_field_in);
							$match[$i-1][$j]['question_field_en']	= read_text($ex->question_field_en);
							$match[$i-1][$j]['field_att_type']		= read_text($ex->field_att_type);
							$match[$i-1][$j]['field_att_file']		= read_text($ex->field_att_file);
							$match[$i-1][$j]['answer_field_in']		= read_text($ex->answer_field_in);
							$match[$i-1][$j]['answer_field_en']		= read_text($ex->answer_field_en);
							$match[$i-1][$j]['answer_att_type']		= $ex->answer_att_type;
							$match[$i-1][$j]['answer_att_file']		= $ex->answer_att_file;

							$j++;
						}
					}

					$data['question'] = $question;
					$data['option'] = $option;
					$data['match'] = $match;
				}
				$data['uc_exam'] = $uc_exam;
				$data['uc_period'] = $uc_period;
				$data['session'] = $session;

			}
			$this->im_render->main("report/detail_exam_question", $data);

		} else {
			if ($uc_period != NULL) {
				redirect("report/manage/".$uc_period);
			} else {
				redirect('report');
			}
		}
	}

	function examination_quality_pdf($uc_period = NULL, $uc_exam = NULL, $session = NULL) {
		if ($uc_exam != NULL) {
			
			$data = "";

			$this->load->model('examination_m');

			$query = $this->examination_m->show_exam_detail($uc_exam);
			if ($query->num_rows() > 0) {
				$res = $query->result();
				$data['row'] = $query->row();

				$questions = explode(",", $res[0]->questions);
				// Sort question
				asort($questions);

				$uc_question = "";
				foreach ($questions as $qs) {
					$uc_question .= "'".$qs."', ";
				}
				$uc_question = substr_replace($uc_question, "", -2);

				$ques = array();
				// $answ = array();
				$z = 0;
				foreach ($query->result() as $res) {
					$answered = array_combine(explode(",", $res->questions), explode(",", $res->answers));
					ksort($answered);
					$data['student_answered'][$z] = $answered;
					// $data['answered'] = ksort($answers);
					$z++;
				}

				$q_exam = $this->examination_m->show_exam($uc_exam, $uc_question);
				if ($q_exam->num_rows() > 0) {
					$i = 0;
					$curr_que = 0;
					foreach ($q_exam->result() as $ex) {
						if ($curr_que != $ex->uc_question) {
							$j = 0;

							$question[$i]['uc_question']		= $ex->uc_question;
							$question[$i]['question_title_in']	= read_text($ex->question_title_in);
							$question[$i]['question_title_en']	= read_text($ex->question_title_en);
							$question[$i]['question_text_in']	= read_text($ex->question_text_in);
							$question[$i]['question_text_en']	= read_text($ex->question_text_en);
							$question[$i]['question_att_type']	= $ex->question_att_type;
							$question[$i]['question_att_file']	= $ex->question_att_file;
							$question[$i]['question_type']		= $ex->question_type;
							$question[$i]['answer_tf']			= $ex->answer_truefalse;
							$question[$i]['answer_mc']			= $ex->answer_multiplechoice;

								
								$option[$i][$j]['option_id']		= $ex->option_id;
								$option[$i][$j]['option_id_ass']	= $ex->option_id;
								$option[$i][$j]['option_text_in']	= read_text($ex->option_text_in);
								$option[$i][$j]['option_text_en']	= read_text($ex->option_text_en);
								$option[$i][$j]['option_att_type']	= $ex->option_att_type;
								$option[$i][$j]['option_att_file']	= $ex->option_att_file;
								$option[$i][$j]['is_correct']		= $ex->is_correct;

								
								$match[$i][$j]['question_field_in']	= read_text($ex->question_field_in);
								$match[$i][$j]['question_field_en']	= read_text($ex->question_field_en);
								$match[$i][$j]['field_att_type']	= read_text($ex->field_att_type);
								$match[$i][$j]['field_att_file']	= read_text($ex->field_att_file);
								$match[$i][$j]['answer_field_in']	= read_text($ex->answer_field_in);
								$match[$i][$j]['answer_field_en']	= read_text($ex->answer_field_en);
								$match[$i][$j]['answer_att_type']	= $ex->answer_att_type;
								$match[$i][$j]['answer_att_file']	= $ex->answer_att_file;

							$curr_que = $ex->uc_question;
							$i++;
							$j++;

						} else {

							$option[$i-1][$j]['option_id']			= $ex->option_id;
							$option[$i-1][$j]['option_id_ass']		= $ex->option_id;
							$option[$i-1][$j]['option_text_in']		= read_text($ex->option_text_in);
							$option[$i-1][$j]['option_text_en']		= read_text($ex->option_text_en);
							$option[$i-1][$j]['option_att_type']	= $ex->option_att_type;
							$option[$i-1][$j]['option_att_file']	= $ex->option_att_file;
							$option[$i-1][$j]['is_correct']			= $ex->is_correct;

							$match[$i-1][$j]['match_id']			= $ex->match_id;
							$match[$i-1][$j]['question_field_in']	= read_text($ex->question_field_in);
							$match[$i-1][$j]['question_field_en']	= read_text($ex->question_field_en);
							$match[$i-1][$j]['field_att_type']		= read_text($ex->field_att_type);
							$match[$i-1][$j]['field_att_file']		= read_text($ex->field_att_file);
							$match[$i-1][$j]['answer_field_in']		= read_text($ex->answer_field_in);
							$match[$i-1][$j]['answer_field_en']		= read_text($ex->answer_field_en);
							$match[$i-1][$j]['answer_att_type']		= $ex->answer_att_type;
							$match[$i-1][$j]['answer_att_file']		= $ex->answer_att_file;

							$j++;
						}
					}

					$data['question'] = $question;
					$data['option'] = $option;
					$data['match'] = $match;
				}
				$data['uc_exam'] = $uc_exam;
				$data['uc_period'] = $uc_period;
				$data['session'] = $session;

			}

			/* BEGIN Of export into pdf */
				$html = $this->load->view('report/detail_exam_question_pdf', $data, TRUE);

				// echo $html;

		        //this the the PDF filename that user will get to download
				$pdfFilePath = "participant_detail(".time_format(current_time(), "d-m-Y").").pdf";

		        //load mPDF library
				$this->load->library('m_pdf');

				ob_clean(); // cleaning the buffer before Output()

		       //generate the PDF from the given html
				$this->m_pdf->pdf->WriteHTML($html);
			
		        //download it.
				$this->m_pdf->pdf->Output($pdfFilePath, "D");
			/* END Of export into pdf */

		} else {
			if ($uc_period != NULL) {
				redirect("report/manage/".$uc_period);
			} else {
				redirect('report');
			}
		}
	}

	function do_update($file_name){
		$this->load->model('pengajuan_ukp_m');
		$this->load->model('day_m');
		$this->load->model('session_m');
		$this->load->model('examination_m');
		$this->load->model('exam_competency_m');
		$this->load->model('exam_package_m');
		$this->load->model('exam_comp_pack_m');
		$this->load->model('exam_match_m');
		$this->load->model('exam_question_m');
		$this->load->model('exam_options_m');
		$this->load->model('question_m');
		$this->load->model('question_option_m');
		$this->load->model('period_participant_m');
		$this->load->model('participant_m');
		$this->load->model('participant_master_m');		
		$this->load->model('exam_attempt_m');		
		$this->load->model('exam_attempt_competency_m');	
		$this->load->model('package_m');		
		$this->load->model('score_m');
		$this->load->model('status_m');
		
		
		//DELETE ALL TABLE TEMP
		$this->pengajuan_ukp_m->empty_temp();
		$this->period_m->empty_temp();
		$this->day_m->empty_temp();
		$this->session_m->empty_temp();
		$this->examination_m->empty_temp();
		$this->exam_competency_m->empty_temp();
		$this->exam_comp_pack_m->empty_temp();
		$this->exam_match_m->empty_temp();
		$this->exam_question_m->empty_temp();
		$this->exam_options_m->empty_temp();
		//$this->db->truncate('tech_question');
		//$this->db->truncate('tech_question_options');
		$this->period_participant_m->empty_temp();
		$this->participant_m->empty_temp();
		$this->participant_master_m->empty_temp();
		

		//	Insert to Temp Table
		$templine = NULL;
		// Read in entire file
		$lines = file("./exim/".$file_name);
		$dec = $this->encrypt->decode($lines[0]);

		$de_lines = explode("\n\r",$dec);

		foreach ($de_lines as $line) {
			
			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '')
			    continue;

			// Add this line to the current segment
			$templine .= $line;

			// If it has a semicolon at the end, it's the end of the query
			if (substr(trim($line), -2, 2) == ');') {

				// Perform the query
				$this->db->query($templine);
				 // echo $templine;

			    // Reset temp variable to empty
			    $templine = NULL;
			}
		}

		$query = $this->pengajuan_ukp_m->temp_not_in_real();
		$value = NULL;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $res) {
				$value .= "('".$res->uc."', '".$res->uc_upt."', '".$res->uc_pukp."', '".$res->date_start."', '".$res->date_finish."', '".$res->create_time."', '".$res->is_approved."'),";
			}
			$value = substr_replace($value, '', -1);

			$field = "(`uc`, `uc_upt`, `uc_pukp`, `date_start`, `date_finish`, `create_time`, `is_approved`)";

			//	Insert to Real Table
			$this->pengajuan_ukp_m->insert_multi_value($field, $value);
		}

		// /	Truncate Temp Table
		$this->pengajuan_ukp_m->empty_temp();
 		

		// TEMP TO REAL - PERIOD
		// Get in Temp
		$query = $this->period_m->temp_not_in_real();
		$value = NULL;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $res) {
				$value .= "('".$res->uc."', '".$res->period."', '".$res->date_start."', '".$res->date_finish."', '".$res->uc_upt."', '".$res->uc_level."', '".$res->pra_pasca."', '".$res->category."', '".$res->uc_ukp."'),";
			}
			$value = substr_replace($value, '', -1);

			$field = "(`uc`, `period`, `date_start`, `date_finish`, `uc_upt`, `uc_level`, `pra_pasca`, `category`, `uc_ukp`)";

			//	Insert to Real Table
			$this->period_m->insert_multi_value($field, $value);
		}

		///	Truncate Temp Table
		$this->period_m->empty_temp();
		// =======================================================================


		//	TEMP TO REAL - DAY
		///	Get in Temp
		$this->load->model('day_m');
		$query = $this->day_m->temp_not_in_real();
		$value = NULL;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->date."'),";
			}
			$value = substr_replace($value, '', -1);

			$field = "(`uc`, `uc_period`, `date`)";

			//	Insert to Real Table
			$this->day_m->insert_multi_value($field, $value);
		}

		///	Truncate Temp Table
		$this->day_m->empty_temp();
		//=======================================================================


		//	TEMP TO REAL - SESSION
		///	Get in Temp
		$query = $this->session_m->temp_not_in_real();
		$value = NULL;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".$res->uc_day."', '".$res->add_time."', '".$res->is_active."'),";
			}
			$value = substr_replace($value, '', -1);

			$field = "(`uc`, `uc_day`, `add_time`, `is_active`)";

			//	Insert to Real Table
			$this->session_m->insert_multi_value($field, $value);
		}

		///	Truncate Temp Table
		$this->session_m->empty_temp();
		//=======================================================================


		//	TEMP TO REAL - EXAMINATION
		///	Get in Temp
		$query = $this->examination_m->temp_not_in_real();
		$value = NULL;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".$res->exam_code."', '".$res->duration."', '".$res->time_create."', '".$res->is_accessed."', '".$res->is_active."', '".$res->uc_period."', '".$res->uc_session."', '".$res->uc_level."', '".$res->uc_function."', '".$res->has_attempted."', '".$res->is_language."', '".$res->show_score."', '".$res->diklat_type."', '".$res->is_exist."'),";
			}
			$value = substr_replace($value, '', -1);

			$field = "(`uc`, `exam_code`, `duration`, `time_create`, `is_accessed`, `is_active`, `uc_period`, `uc_session`, `uc_level`, `uc_function`, `has_attempted`, `is_language`, `show_score`, `diklat_type`, `is_exist`)";

			//	Insert to Real Table
			$this->examination_m->insert_multi_value($field, $value);
		}

		///	Truncate Temp Table
		$this->examination_m->empty_temp();
		//=======================================================================


		//	TEMP TO REAL - EXAMINATION COMPETENCY
		///	Get in Temp
		$query = $this->exam_competency_m->temp_not_in_real();
		$value = NULL;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".$res->uc_exam."', '".$res->uc_competency."'),";
			}
			$value = substr_replace($value, '', -1);

			$field = "(`uc`, `uc_exam`, `uc_competency`)";

			//	Insert to Real Table
			$this->exam_competency_m->insert_multi_value($field, $value);
		}

		///	Truncate Temp Table
		$this->exam_competency_m->empty_temp();
		//=======================================================================


		//	TEMP TO REAL - EXAMINATION QUESTION
		///	Get in Temp
		$query = $this->exam_question_m->temp_not_in_real();
		$value = NULL;

		$i = 1;	
		if ($query->num_rows() > 0){
			$field = "(`uc`, `uc_question`, `question_code`, `question_title_in`, `question_title_en`, `question_text_in`, `question_text_en`, `question_att_type`, `question_att_file`, `question_type`, `answer_truefalse`, `answer_multiplechoice`, `uc_exam`, `is_exist`)";

			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".$res->uc_question."', '".$res->question_code."', '".str_replace("'", "''", $res->question_title_in)."', '".str_replace("'", "''", $res->question_title_en)."', '".str_replace("'", "''", $res->question_text_in)."', '".str_replace("'", "''", $res->question_text_en)."', '".$res->question_att_file."', '".$res->question_att_type."', '".$res->question_type."', '".$res->answer_truefalse."', '".$res->answer_multiplechoice."', '".$res->uc_exam."', '".$res->is_exist."'),";
				
				if (($i%50) == 0){
					$value = substr_replace($value, '', -1);
					$this->exam_question_m->insert_multi_value($field, $value);
					
					$value = NULL;
				}

				$i++;
			}

			if ($value != NULL){
				$value = substr_replace($value, '', -1);
				$this->exam_question_m->insert_multi_value($field, $value);	
			}		
		}	

		///	Truncate Temp Table
		$this->exam_question_m->empty_temp();
		//=======================================================================


		//	TEMP TO REAL - EXAMINATION OPTIONS
		///	Get in Temp
		$query = $this->exam_options_m->temp_not_in_real();
		$value = NULL;

		$i = 1;	
		if ($query->num_rows() > 0){
			$field = "(`uc`, `option_text_in`, `option_text_en`, `option_att_type`, `option_att_file`, `is_correct`, `uc_exam_question`, `uc_exam`)";

			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".str_replace("'", "''", $res->option_text_in)."', '".str_replace("'", "''", $res->option_text_en)."', '".$res->option_att_type."', '".$res->option_att_file."', '".$res->is_correct."', '".$res->uc_exam_question."', '".$res->uc_exam."'),";
				
				if (($i%50) == 0){
					$value = substr_replace($value, '', -1);
					$this->exam_options_m->insert_multi_value($field, $value);
					
					$value = NULL;
				}

				$i++;
			}

			if ($value != NULL){
				$value = substr_replace($value, '', -1);
				$this->exam_options_m->insert_multi_value($field, $value);	
			}		
		}	

		///	Truncate Temp Table
		$this->exam_options_m->empty_temp();
		//=======================================================================


		//	TEMP TO REAL - EXAMINATION MATCH
		///	Get in Temp
		$query = $this->exam_match_m->temp_not_in_real();
		$value = NULL;

		$i = 1;	
		if ($query->num_rows() > 0){
			$field = "(`uc_exam_question`, `question_field_in`, `question_field_en`, `question_att_type`, `question_att_file`, `answer_field_in`, `answer_field_en`, `answer_att_type`, `answer_att_file`, `uc_exam`)";

			foreach ($query->result() as $res){
				$value .= "('".$res->uc_exam_question."', '".str_replace("'", "''", $res->question_field_in)."', '".str_replace("'", "''", $res->question_field_en)."', '".$res->question_att_type."', '".$res->question_att_file."', '".str_replace("'", "''", $res->answer_field_in)."', '".str_replace("'", "''", $res->answer_field_en)."', '".$res->answer_att_type."', '".$res->answer_att_file."', '".$res->uc_exam."'),";
				
				if (($i%50) == 0){
					$value = substr_replace($value, '', -1);
					$this->exam_match_m->insert_multi_value($field, $value);
					
					$value = NULL;
				}

				$i++;
			}

			if ($value != NULL){
				$value = substr_replace($value, '', -1);
				$this->exam_match_m->insert_multi_value($field, $value);	
			}		
		}	

		///	Truncate Temp Table
		$this->exam_match_m->empty_temp();
		//=======================================================================


		//	TEMP TO REAL - QUESTION
		///	Get in Temp
		$query = $this->question_m->temp_not_in_real();
		$value = NULL;

		$i = 1;	
		if ($query->num_rows() > 0){
			$field = "(`uc`, `question_title_in`, `question_title_en`, `question_text_in`, `question_text_en`, `question_type`, `att_type`, `att_file`, `truefalse_answer`)";

			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".str_replace("'", "''", $res->question_title_in)."', '".str_replace("'", "''", $res->question_title_en)."', '".str_replace("'", "''", $res->question_text_in)."', '".str_replace("'", "''", $res->question_text_en)."', '".$res->question_type."', '".$res->att_type."', '".$res->att_file."', '".$res->truefalse_answer."'),";
				
				if (($i%50) == 0){
					$value = substr_replace($value, '', -1);
					$this->question_m->insert_multi_value($field, $value);
					
					$value = NULL;
				}

				$i++;
			}

			if ($value != NULL){
				$value = substr_replace($value, '', -1);
				$this->question_m->insert_multi_value($field, $value);	
			}		
		}

		///	Truncate Temp Table
		//$this->db->truncate('tech_question_temp');
		//=======================================================================

		//	TEMP TO REAL - QUESTION OPTIONS
		///	Get in Temp
		$query = $this->question_option_m->temp_not_in_real();
		$value = NULL;

		$i = 1;	
		if ($query->num_rows() > 0){
			$field = "(`uc`, `option_text_in`, `option_text_en`, `is_correct`, `att_type`, `att_file`, `uc_question`)";

			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".str_replace("'", "''", $res->option_text_in)."', '".str_replace("'", "''", $res->option_text_en)."', '".$res->is_correct."', '".$res->att_type."', '".$res->att_file."', '".$res->uc_question."'),";
				
				if (($i%50) == 0){
					$value = substr_replace($value, '', -1);
					$this->question_option_m->insert_multi_value($field, $value);
					
					$value = NULL;
				}

				$i++;
			}

			if ($value != NULL){
				$value = substr_replace($value, '', -1);
				$this->question_option_m->insert_multi_value($field, $value);	
			}		
		}	

		///	Truncate Temp Table
		//$this->db->truncate('tech_question_options_temp');
		//=======================================================================


		//	TEMP TO REAL - PERIOD PARTICIPANT
		///	Get in Temp
		$query = $this->period_participant_m->temp_not_in_real();
		$value = NULL;

		$i = 1;	
		if ($query->num_rows() > 0){
			$field = "(`uc`, `uc_period`, `uc_diklat_participant`, `seafarer_code`, `participant_no`, `last_active`, `is_login`)";

			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_diklat_participant."', '".$res->seafarer_code."', '".$res->participant_no."', '".$res->last_active."', '".$res->is_login."'),";

				$nono[$res->seafarer_code]['uc_diklat_participant'] = $res->uc_diklat_participant;
				
				if (($i%50) == 0){
					$value = substr_replace($value, '', -1);
					$this->period_participant_m->insert_multi_value($field, $value);
					
					$value = NULL;
				}

				$i++;
			}

			if ($value != NULL){
				$value = substr_replace($value, '', -1);
				$this->period_participant_m->insert_multi_value($field, $value);	
			}		
		}
		else {
			//	Diperlukan utk import day berikutnya agar uc_diklat_participatn tidak kosong
			$query = $this->period_participant_m->get_in_temp();

			if ($query->num_rows() > 0){
				foreach ($query->result() as $res){
					$nono[$res->seafarer_code]['uc_diklat_participant'] = $res->uc_diklat_participant;
				}
			}
		}		

		///	Truncate Temp Table
		$this->period_participant_m->empty_temp();
		//=======================================================================


		//	TEMP TO REAL - PARTICIPANT
		///	Get in Temp
		$query = $this->participant_m->temp_not_in_real();
		
		$value 			= NULL;
		$value_part		= NULL;
		$curr_seafarer 	= NULL;

		$i = 1;	
		if ($query->num_rows() > 0){
			$field = "(`uc`, `seafarer_code`, `full_name`, `born_place`, `born_date`, `uc_diklat_participant`, `participant_no`, `uc_period`, `uc_level`, `uc_function`, `uc_exam`)";

			$field_part = "(`uc`, `seafarer_code`, `full_name`, `born_place`, `born_date`)";

			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".$res->seafarer_code."', '".str_replace("'", "''", $res->full_name)."', '".str_replace("'", "''", $res->born_place)."', '".$res->born_date."', '".$res->uc_diklat_participant."', '".$res->participant_no."', '".$res->uc_period."', '".$res->uc_level."', '".$res->uc_function."', '".$res->uc_exam."'),";

				if ($res->seafarer_code != $curr_seafarer){
					$value_part .= "('".$res->uc."', '".$res->seafarer_code."', '".str_replace("'", "''", $res->full_name)."', '".str_replace("'", "''", $res->born_place)."', ".($res->born_date != '0000-00-00' ? $res->born_date : 'NULL')."),";
					
					$curr_seafarer = $res->seafarer_code;
				}
				
				if (($i%50) == 0){
					$value = substr_replace($value, '', -1);
					$this->participant_m->insert_multi_value($field, $value);

					$value 	 = NULL;					
				}

				$i++;
			}

			if ($value != NULL)
			{
				$value = substr_replace($value, '', -1);
				$this->participant_m->insert_multi_value($field, $value);	
			}
		}			

		///	Truncate Temp Table
		$this->participant_m->empty_temp();
		//=======================================================================


		//	TEMP TO REAL - PARTICIPANT MASTER
		///	Get in Temp
		$query = $this->participant_master_m->temp_not_in_real(); 
		$value = NULL;

		$i = 1;	
		if ($query->num_rows() > 0){
			$field = "(`uc`, `seafarer_code`, `full_name`, `born_place`, `born_date`)";

			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".$res->seafarer_code."', '".str_replace("'", "''", $res->full_name)."', '".str_replace("'", "''", $res->born_place)."', '".$res->born_date."'),";
				
				if (($i%50) == 0){
					$value = substr_replace($value, '', -1);
					$this->participant_master_m->insert_multi_value($field, $value);
					
					$value = NULL;
				}

				$i++;
			}

			if ($value != NULL) {
				$value = substr_replace($value, '', -1);
				$this->participant_master_m->insert_multi_value($field, $value);	
			}		
		}				

		///	Truncate Temp Table
		$this->participant_master_m->empty_temp();
		//=======================================================================
		
		
		//	TEMP TO REAL - EXAMINATION ATTEMPT
		///	Get in Temp
		$query = $this->exam_attempt_m->temp_not_in_real();
		$value = NULL;

		$i = 1;	
		if ($query->num_rows() > 0){
			$field = "(`uc`, `uc_exam`, `seafarer_code`, `questions`, `competency`, `keys`, `answers`, `pairs`, `is_marks`, `answer_true`, `answer_false`, `answer_result`, `time_start`, `time_finish`, `time_running`, `time_remain`, `is_notif`, `is_done`)";

			foreach ($query->result() as $res){
				$value .= "('".$res->uc."', '".$res->uc_exam."', '".$res->seafarer_code."', '".$res->questions."', '".$res->competency."', '".$res->keys."', '".$res->answers."', '".$res->pairs."', '".$res->is_marks."', '".$res->answer_true."', '".$res->answer_false."', '".$res->answer_result."', '".$res->time_start."', '".$res->time_finish."', '".$res->time_running."', '".$res->time_remain."', '".$res->is_notif."', '".$res->is_done."'),";
				
				if (($i%50) == 0){					
					$value = substr_replace($value, '', -1);
					$this->exam_attempt_m->insert_multi_value($field, $value);					

					$value = NULL;
				}

				$i++;
			}

			if ($value != NULL){
				$value = substr_replace($value, '', -1);
				$this->exam_attempt_m->insert_multi_value($field, $value);
			}		
		}

		//	TEMP TO REAL - EXAMINATION ATTEMPT COMPETENCY
		///	Get in Temp
		$query = $this->exam_attempt_competency_m->temp_not_in_real();		

		$value 		 	= NULL;
		$value_score 	= NULL; 
		
		// Get data from import file
		$uc_competency = NULL;
		$seafarer_code = NULL;
		$score_normal  = NULL;

			
		if ($query->num_rows() > 0){
			
			$this->load->model('score_temp_m');

			$field 			= "(`uc`, `uc_exam_attempt`, `uc_competency`, `seafarer_code`, `score`, `score_2`, `score_normal`)";
			$field_score 	= "(`uc`, `uc_period`, `uc_upt`, `uc_competency`, `uc_eac`, `pra_pasca`, `uc_diklat_participant`,`seafarer_code`, `score_normal`)";

			// BEGIN Insert into table exam_attempt_competency & score
			$i = 1;
			foreach ($query->result() as $res){
				// generate
				$uc_competency  .= "".$res->uc_competency.",";
				$seafarer_code  .= "".$res->seafarer_code.",";
				$score_normal  	.= "".$res->score_normal.",";

				$uc_diklat_par   = $nono[$res->seafarer_code]['uc_diklat_participant'];

				$value .= "('".$res->uc."', '".$res->uc_exam_attempt."', '".$res->uc_competency."', '".$res->seafarer_code."', '".$res->score."', '".$res->score_2."', '".$res->score_normal."'),";
				
				$value_score .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_upt."', '".$res->uc_competency."', '".$res->uc."', '".$res->diklat_type."', '".$uc_diklat_par."','".$res->seafarer_code."', '".$res->score_normal."'),";

				if (($i%50) == 0){
					// CATEGORY ATTEMPT ANSWER PADA SAAT FORM IMPORT
					$value = substr_replace($value, '', -1);
					$this->exam_attempt_competency_m->insert_multi_value($field, $value);
					
					$value = NULL;

					// CATEGORY REPORT SCORE PADA SAAT FORM IMPORT
					$value_score = substr_replace($value_score, '', -1);
					$this->score_m->insert_multi_value($field_score,$value_score);
					//	Insert into table score_temp for conversion to status					
					$this->score_temp_m->insert_multi_value($field_score,$value_score);
					
					$value_score = NULL;
				}

				$i++;
			}

			if ($value != NULL){
				$value = substr_replace($value, '', -1);
				$this->exam_attempt_competency_m->insert_multi_value($field, $value);			
			}

			if ($value_score != NULL){
				// Insert To Recap Table
				$value_score = substr_replace($value_score, '', -1);
			 	$this->score_m->insert_multi_value($field_score,$value_score);
			 	//	Insert into table score_temp for conversion to status					
				$this->score_temp_m->insert_multi_value($field_score,$value_score);		
			}
			// END Insert into table exam_attempt_competency & score
			//	===================================================================================

			//	BEGIN Insert OR Update STATUS PERIOD
			$this->load->model('score_temp_m');
			$this->load->model('status_period_m');

			$field = "(`uc`, `uc_period`, `uc_competency`, `diklat_type`, `uc_diklat_participant`,`seafarer_code`, `is_pass`, `score`, `uc_score`, `status`)";
			
			//	Insert Prev Status Period
			$query = $this->status_period_m->temp_not_in_real();
			if ($query->num_rows() > 0) {
				$i = 1;
				$value = "";

				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_competency."', '".$res->diklat_type."', '".$res->uc_diklat_participant."','".$res->seafarer_code."', '".$res->is_pass."', '".$res->score."', '".$res->uc_score."', '".$res->status."'),";

					if (($i%50) == 0) {
						$value = substr_replace($value, '', -1);
						$this->status_period_m->insert_multi_value($field, $value);
						
						$value = "";
					}

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$this->status_period_m->insert_multi_value($field, $value);
				}
			}

			//	Insert New OR Update Prev Status Period
			$query = $this->status_period_m->get_compare_with_score();
			if ($query->num_rows() > 0) {
				$i = 1;
				$value = "";
				
				foreach ($query->result() as $res) {
					//echo "<br /> - ".$res->seafarer_code." - ".$res->uc_competency." - ".$res->status;
					$is_pass = (decryptIt($res->score_normal) >= 70 ? 1 : 0);
					$staper = ($is_pass == 1 ? "L" : "BL");

					if ($res->status != NULL) {
						//	Update
						$where = array('uc' => $res->uc_status_period);

						$data_update = array(
											'is_pass' 	=> $is_pass,
											'score'  	=> $res->score_normal,
											'uc_score'	=> $res->uc,
											'status' 	=> $staper
											);

						$this->status_period_m->update_data($data_update, $where);
					}
					else {
						//	Insert New
						$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_competency."', '".$res->pra_pasca."', '".$res->uc_diklat_participant."','".$res->seafarer_code."', '".$is_pass."', '".$res->score_normal."', '".$res->uc."', '".$staper."'),";

						if (($i%50) == 0) {
							$value = substr_replace($value, '', -1);
							$this->status_period_m->insert_multi_value($field, $value);
							
							$value = "";
						}	
					}

					$i++;					
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$this->status_period_m->insert_multi_value($field, $value);
				}
			}

			$this->db->truncate('tech_status_period_temp');
			//	END Insert OR Update STATUS PERIOD
			//	===================================================================================	

 
			//	BEGIN Insert OR Update STATUS
			///	Insert From Temp to Real
			$query = $this->status_m->temp_not_in_real();
			$field_status	= "(`uc`, `uc_competency`, `pra_pasca`, `seafarer_code`, `is_pass`, `score_max`, `uc_score`, `status`)";
			
			if ($query->num_rows() > 0) {				
				$i = 1;
				$value = NULL;

				foreach ($query->result() as $res) {
					$value .= "('".$res->uc."','".$res->uc_competency."','".$res->pra_pasca."','".$res->seafarer_code."','".$res->is_pass."','".$res->score_max."','".$res->uc_score."', '".$res->status."'),";

					if (($i%50) == 0) {
						$value = substr_replace($value, '', -1);
						$this->status_m->insert_multi_value($field_status, $value);
						
						$value = "";
					}

					$i++;
				}

				if ($value != "") {
					$value = substr_replace($value, '', -1);
					$this->status_m->insert_multi_value($field_status, $value);	
				}
			}
			// ============================================================================
		

			//	Insert or Update Status		
			$query = $this->score_m->get_on_status();
			
			if ($query->num_rows() > 0) {
				$i = 0;
				//	Inisialisasi variable untuk data yg belum ada di status
				$field_status	= "(`uc`, `uc_competency`, `pra_pasca`, `seafarer_code`, `is_pass`, `score_max`, `uc_score`, `status`)";
				$value_status 	= NULL;
				
				foreach ($query->result() as $sts) {
					if ($sts->score_max != NULL) {
						//	Sudah ada (Sudah pernah ujian)
						if (decryptIt($sts->score_normal) > decryptIt($sts->score_max)) {
							//	Jika nilai ujian terbaru nilainya lebih besar dr sebelumnya
							$is_pass = (decryptIt($sts->score_normal) >= 70 ? 1 : 0);
							$status = ($is_pass == 1 ? "SL" : "BL");

							$data  = array(
											'is_pass' 	=> $is_pass,
											'score_max' => $sts->score_normal,
											'uc_score'	=> $sts->uc,
											'status'	=> $status
										);

							$where = array('uc_competency' => $sts->uc_competency, 'seafarer_code' => $sts->seafarer_code);

				 			$this->status_m->update_data($data,$where);
						}
					}
					else {
						///	Genereate UC Status
						// $fro = rand(0,9)."".substr($sts->uc_competency, 0, 2);
						// $mid = substr($sts->seafarer_code, -4);
						// $end = substr($sts->uc_competency, -2)."-".substr($sts->uc_competency, 0, 2);
						// $uc = $fro."-".$sts->pra_pasca."".$mid."-".$end;

						$fro = $sts->pra_pasca.substr($sts->uc_competency, 0, 2);
						$mid = substr($sts->seafarer_code, -5);
						$end = substr($sts->uc_competency, -5);
						$uc = $fro."-".$mid."-".$end;

						//	Belum ada (Belum pernah ujian)
						$is_pass = (decryptIt($sts->score_normal) >= 70 ? 1 : 0);
						$status = ($is_pass == 1 ? "SL" : "BL");

						$value_status .= "('".$uc."','".$sts->uc_competency."','".$sts->pra_pasca."','".$sts->seafarer_code."','".$is_pass."','".$sts->score_normal."','".$sts->uc."', '".$status."'),";

						if (($i%50) == 0) {
							$value_status = substr_replace($value_status, '', -1);
							$this->status_m->insert_multi_value($field_status, $value_status);
							
							$value_status = "";
						}

						$i++;
					}
				}

				if ($value_status != "") {
					$value_status = substr_replace($value_status, '', -1);
					$this->status_m->insert_multi_value($field_status,$value_status);	
				}
			}
		}
		

		///	Truncate Temp Table
		$this->exam_attempt_m->empty_temp();
		$this->exam_attempt_competency_m->empty_temp();
		$this->score_m->empty_temp();
		$this->status_m->empty_temp();

		// END CATEFORY REPORT ANSWER

		//Delete File Update
		unlink("./exim/".$file_name);
	}

function upload(){
		error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if ($this->input->post('f_save')){
            $data = NULL;

            $config['upload_path']          = './exim/';
			if (!is_dir($upload_path)) {
    		mkdir($upload_path, 0777, TRUE);
}
            $config['allowed_types']        = 'cba|sql';
            $config['max_size']             = 150000;
            $config['overwrite']            = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ( ! $this->upload->do_upload('f_file')){
                $this->upload->display_errors();    
            }
            else {
                $upload_data    = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
                $file_name      = $upload_data['file_name'];

                //$this->decrypt_report($file_name);
                $this->do_update($file_name);

                $this->session->set_flashdata('msg','Success upload data!'); 

                // Redirect to report (Catatan: redirect harus ditaruh setelah set_flashdata agar pesannya terbaca)
                redirect('report');
            }
        }
    }

	function upload_bulk() {
		if ($this->input->post('f_save')){
			$data = NULL;

			$config['upload_path']			= './exim/';
	        $config['allowed_types']		= 'zip';
	        $config['max_size']				= 80000;
	        $config['overwrite']			= TRUE;

	        $extract_dir = $config['upload_path'];

	        $this->load->library('upload', $config);
	        $this->upload->initialize($config);
	        if ( ! $this->upload->do_upload('f_file')){
	        	// Show alert
				$message = $this->upload->display_errors();

				$this->session->set_flashdata('msg', $message);
	        }
	        else {
	        	$this->load->library('zip');

				$zip = new ZipArchive;

	        	$upload_data	= $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
				$file_name		= $upload_data['file_name'];

				
				$res = $zip->open($extract_dir.$file_name);

				// Extract Proccess
				$zip->extractTo($extract_dir);

				$file_package = glob($extract_dir."*.cba");

				foreach ($file_package as $fp) {
					//echo "<br /> up : ".substr($fp, 7);
					$this->do_update(substr($fp, 7));
				}
	        }

			// Redirect to report
			redirect('report');
	    }
	}

	function edit_score_group_form() {
		if ($this->input->post('js_uc_competency')) {
			
			$data = NULL;

			$uc_period = $this->input->post('js_uc_period');
			$uc_exam = $this->input->post('js_uc_exam');
			$uc_competency = $this->input->post('js_uc_competency');


			//	Get Competency, Function & Level
				$this->load->model('competency_m');
				$query = $this->competency_m->get_detail_competency($uc_competency);
				if ($query->num_rows() > 0) {
					$row = $query->row();

					$data['competency_name']	= $row->competency_name;
					$data['function_name']		= $row->function_name;
					$data['level']				= $row->level;
				}

			//Get exam
				$this->load->model('examination_m');
				$query = $this->examination_m->get_filtered(array('uc' => $uc_exam));
				if ($query->num_rows() > 0) {
					$data['row'] = $query->row();
				}

			// Get setting
				$this->load->model('setting_m');
				$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
				$data['setting'] = $q_set->row();


			//	Get Score
				$this->load->model('participant_m');
				$query = $this->participant_m->get_all_score($uc_period, $uc_exam, $uc_competency);

				if ($query->num_rows() > 0) {
					$data['result'] = $query->result();
				}

			$data['uc_period'] = $uc_period;
			$data['uc_exam'] = $uc_exam;
			$data['uc_competency'] = $uc_competency;

			$this->load->view('report/edit_score_group_form', $data);

		} else {
			echo "Empty...";
		}
	}

	function edit_score_group() {
		if ($this->input->post('f_process')) {
			
			$data = NULL;

			$data['uc_period'] = $this->input->post('f_uc_period');
			$data['uc_exam'] = $this->input->post('f_uc_exam');
			$data['uc_competency'] = $this->input->post('f_uc_competency');


			$uc_ea = "";
			if ($this->input->post('f_uc_ea')) {
				foreach ($this->input->post('f_uc_ea') as $ea) {
					$uc_ea .= "'".$ea."', ";
				}
				$uc_ea = substr_replace($uc_ea, '', -2);
			}


			//	Get Competency, Function & Level
				$this->load->model('competency_m');
				$query = $this->competency_m->get_detail_competency($data['uc_competency']);
				if ($query->num_rows() > 0) {
					$row = $query->row();

					$data['competency_name']	= $row->competency_name;
					$data['function_name']		= $row->function_name;
					$data['level']				= $row->level;
				}


			// Get setting
				$this->load->model('setting_m');
				$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
				$data['setting'] = $q_set->row();

			//Get exam
				$this->load->model('examination_m');
				$query = $this->examination_m->get_filtered(array('uc' => $data['uc_exam']));
				if ($query->num_rows() > 0) {
					$data['row'] = $query->row();
				}


			// Get student attempt and detail
				$this->load->model('exam_attempt_m');

				$query = $this->exam_attempt_m->get_detail_att_in($uc_ea, $data['uc_competency']);
				if ($query->num_rows() > 0) {
					$data['result'] = $query->result();
				}


			/*echo "<pre>";
			print_r($data);
			echo "</pre>";*/

			$this->im_render->main('report/edit_score_group', $data);
		}
	}

	function update_score_competency_group() {
		if ($this->input->post('f_save')) {
			
			$uc_period = $this->input->post('f_uc_period');
			$uc_exam = $this->input->post('f_uc_exam');
			$uc_competency = $this->input->post('f_uc_competency');

			$uc_attempt = $this->input->post('f_uc_attempt');
			$uc_att_comp = $this->input->post('f_uc_att_comp');

			$old_score = $this->input->post('f_old_score');
			$new_score = $this->input->post('f_new_score');

			$this->load->model('exam_attempt_m');
			$this->load->model('exam_attempt_competency_m');

			for ($i=0; $i < count($uc_attempt) ; $i++) {
				if ($new_score[$i] != $old_score[$i]) {
				
					// Get attempt
						$r_att = $this->exam_attempt_m->get_filtered(array('uc' => $uc_attempt[$i]))->row();
					// Get value competency
						$key_comp = explode(',', $r_att->competency);
					// Get the index off competency for get answer and ket
						$key_selected = array_keys($key_comp, $uc_competency);
					// Get value keys & answer student
						$keys = explode(',', $r_att->keys);

						$answers = explode(',', $r_att->answers);



					// Compare keys & answers student
						// Prepare by combining keys and answers
							$compare = array();
							foreach ($key_selected as $ks) {
								
								$compare[$ks]['keys'] = $keys[$ks];
								$compare[$ks]['answers'] = $answers[$ks];

							}

						// Start compare key and answer
							$ans_true = "";
							$count_true = 0;
							$ans_false = "";
							$count_false = 0;
							foreach ($compare as $ke => $an) {
								
								if ($an['keys'] == $an['answers']) {
									$ans_true .= $ke.",";
									$count_true++;
								} else {
									$ans_false .= $ke.",";
									$count_false++;
								}

							}
							$ans_true = substr_replace($ans_true, '', -1);
							$ans_false = substr_replace($ans_false, '', -1);



					// BEGIN Updating score proccess
						$true_filter = explode(',', $ans_true);
						$false_filter = explode(',', $ans_false);

						// Required how many answers must be correct
						$req_true = floor(($new_score[$i] * count($key_selected)) / 100);

						// Required total answers to meet the number off answered
						$req_ans = $req_true - $count_true;

						// Random keys for completely answers requirement
						$rand_ans = array_rand(explode(',', $ans_false), $req_ans);


						// Updating new answered
							$new_answers = explode(',', $r_att->answers);
							$new_ans_res = explode(',', $r_att->answer_result);
							foreach ($rand_ans as $ra) {

								$rand_key = $false_filter[$ra];
								
								// Update New Answers
									$new_answers[$rand_key] = $keys[$rand_key];

								// Update New Answers Result
									$new_ans_res[$rand_key] = "T";

							}

							// Update exam_attempt proccess
								$data_ea = array('answers' => implode(',', $new_answers), 'answer_result' => implode(',', $new_ans_res));
								$filter_ea = array('uc' => $uc_attempt[$i]);

								$this->exam_attempt_m->update_data($data_ea, $filter_ea);

							// Update Score comptency
								// New Counting	MODE : True (+2), False (-1), Empty/IDK (0)
									$point = (($req_true*2)-(count($key_selected)-$req_true));
									$score = value_format(($point / (count($key_selected)*2) * 100), ',', '.', 2);

								// New Counting	MODE : True (+4), False (-1), Empty/IDK (0)
									$point = (($req_true*4)-(count($key_selected)-$req_true));
									$score_2 = value_format(($point / (count($key_selected)*4) * 100), ',', '.', 2);
								
								$data_comp = array(
													'score'			=> encryptIt($score),
													'score_2'		=> encryptIt($score_2),
													'score_normal'	=> encryptIt($new_score[$i])
								);
								$filter_comp = array('uc' => $uc_att_comp[$i]);

								$this->exam_attempt_competency_m->update_data($data_comp, $filter_comp);
					// END Updating score proccess
				}
			}

			redirect('report/report_by_competency/'.$uc_period.'/'.$uc_exam.'/'.$uc_competency);

		} else {
			echo "404 Not Found";
		}
	}

	function get_score_competency(){
		$data = "";

		$uc = $this->input->post('js_uc_eac');

		$this->load->model('exam_attempt_competency_m');
		$query = $this->exam_attempt_competency_m->get_competency($uc);
		if ($query->num_rows() > 0) {
			$data['row'] = $query->row();
		}

		// Get setting
			$this->load->model('setting_m');
			$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
			$data['setting'] = $q_set->row();

		$data['uc_exam'] = $this->input->post('js_uc_exam');
		$data['uc_period'] = $this->input->post('js_uc_period');

		$this->load->view('report/edit_score',$data);

	}

	function update_score_competency(){
		if ($this->input->post('f_save')) {
		
			$this->load->model('exam_attempt_competency_m');

			$uc_period = $this->input->post('f_uc_period');
			$uc_exam = $this->input->post('f_uc_exam');
			$uc_competency = $this->input->post('f_uc_competency');

			$uc_attempt = $this->input->post('f_uc_attempt');
			$uc_att_comp = $this->input->post('f_uc_att_comp');
			$new_score = $this->input->post('f_score_comp');

			
			// Get attempt
				$this->load->model('exam_attempt_m');

				$r_att = $this->exam_attempt_m->get_filtered(array('uc' => $uc_attempt))->row();

			// Get competency
				$key_comp = explode(',', $r_att->competency);
			// Get the index off competency for get answer and ket
				$key_selected = array_keys($key_comp, $uc_competency);

			// Get value keys & answer student
				$keys = explode(',', $r_att->keys);
				$answers = explode(',', $r_att->answers);


			// Compare keys & answers student
				// Prepare by combining keys and answers
					$compare = array();
					foreach ($key_selected as $ks) {
						
						$compare[$ks]['keys'] = $keys[$ks];
						$compare[$ks]['answers'] = $answers[$ks];

					}

				// Start compare key and answer
					$ans_true = "";
					$count_true = 0;
					$ans_false = "";
					$count_false = 0;
					foreach ($compare as $ke => $an) {
						
						if ($an['keys'] == $an['answers']) {
							$ans_true .= $ke.",";
							$count_true++;
						} else {
							$ans_false .= $ke.",";
							$count_false++;
						}

					}
					$ans_true = substr_replace($ans_true, '', -1);
					$ans_false = substr_replace($ans_false, '', -1);


			// BEGIN Updating score proccess
				$true_filter = explode(',', $ans_true);
				$false_filter = explode(',', $ans_false);

				// Required how many answers must be correct
				$req_true = floor(($new_score * count($key_selected)) / 100);

				// Required total answers to meet the number off answered
				$req_ans = $req_true - $count_true;

				// Random keys for completely answers requirement
				$rand_ans = array_rand(explode(',', $ans_false), $req_ans);


				// Updating new answered
					$new_answers = explode(',', $r_att->answers);
					$new_ans_res = explode(',', $r_att->answer_result);
					foreach ($rand_ans as $ra) {

						$rand_key = $false_filter[$ra];
						
						// Update New Answer
							$new_answers[$rand_key] = $keys[$rand_key];

						// Update New Answers Result
							$new_ans_res[$rand_key] = "T";


					}

					// Update exam_attempt proccess
						$data_ea = array('answers' => implode(',', $new_answers), 'answer_result' => implode(',', $new_ans_res));
						$filter_ea = array('uc' => $uc_attempt);

						$this->exam_attempt_m->update_data($data_ea, $filter_ea);

					// Update Score comptency
						// New Counting	MODE : True (+2), False (-1), Empty/IDK (0)
							$point = (($req_true*2)-(count($key_selected)-$req_true));
							$score = value_format(($point / (count($key_selected)*2) * 100), ',', '.', 2);

						// New Counting	MODE : True (+4), False (-1), Empty/IDK (0)
							$point = (($req_true*4)-(count($key_selected)-$req_true));
							$score_2 = value_format(($point / (count($key_selected)*4) * 100), ',', '.', 2);
						
						$data_comp = array(
											'score'			=> encryptIt($score),
											'score_2'		=> encryptIt($score_2),
											'score_normal'	=> encryptIt($new_score)
						);
						$filter_comp = array('uc' => $uc_att_comp);

						$this->exam_attempt_competency_m->update_data($data_comp, $filter_comp);
			// END Updating score proccess

			redirect('report/report_by_competency/'.$uc_period.'/'.$uc_exam.'/'.$uc_competency);
		} else {
			echo "404 Not Found";
		}
	}

	function form_import_report(){
		// $data = "";

		// $category = $this->input->post('js_cat');

		// $data['category'] = $category;

		// $this->load->view('report/form_import_report', $data);

		$this->load->view('report/form_import_report');
	}

	function download($uc_period,$uc_day,$hari){

		$this->load->model('examination_m');

		$query = $this->examination_m->get_period_exam($uc_period,$uc_day);
		if ($query->num_rows() > 0) {
			$result_period = $query->result();

			$replace_periode = str_replace('/', '-', $result_period[0]->period);

			//echo $replace_periode;

			$idx_folder = $result_period[0]->pukp_label."-".$result_period[0]->upt_label."-Period[".$replace_periode."] - Day[".$hari."]";

			@mkdir($idx_folder);

			$path_sub = './'.$idx_folder.'/';

			$name_folder = "";
			$number = 1;
			foreach ($result_period as $rp) {
				@mkdir($path_sub.'/'.$number.". ".$rp->exam_code);

				$this->load->model('exam_competency_m');
				$query = $this->exam_competency_m->get_filtered(array('uc_exam' => $rp->uc_exam));
				if ($query->num_rows()) {
					$result = $query->result();

					foreach ($result as $res) {
						$this->load->helper('text');

						$name_folder = $path_sub.'/'.$number.". ".$rp->exam_code;


						if (($uc_period != NULL) && ($res->uc_exam != NULL) && ($res->uc_competency != NULL)) {

							$this->load->model('examination_m');
							$data['row'] = $this->examination_m->get_filtered(array('uc' => $rp->uc_exam))->row();

							//Get Upt
							$this->load->model('period_m');
							$query = $this->period_m->get_list($uc_period);
							if ($query->num_rows() > 0) {
								$data['upt'] = $query->row();
							}

							//	Get Competency, Function & Level
							$this->load->model('competency_m');
							$query = $this->competency_m->get_detail_competency($res->uc_competency);
							if ($query->num_rows() > 0) {
								$row = $query->row();

								$data['competency_name']			= word_limiter($row->competency_name,6,'');
								$data['competency_name_title']		= $row->competency_name;
								$data['function_name']				= $row->function_name;
								$data['level']						= $row->level;
								$data['sequence']					= $row->sequence;
							}

							//	Get Score
							$this->load->model('participant_m');
							$query = $this->participant_m->get_all_score($uc_period, $res->uc_exam, $res->uc_competency);
							if ($query->num_rows() > 0) {
								$data['result'] = $query->result();
							}
						}

						// Get setting
						$this->load->model('setting_m');
						$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
						$data['setting'] = $q_set->row();

						$data['name_folder'] = $name_folder;

						$this->load->view('report/show_excel', $data);
					}
				}
				$number++;
			}

			$this->load->library('zip');
			$this->load->helper('file');

			//DOWNLOAD ZIP
			$path = $idx_folder.'/';
			
	 		$this->zip->read_dir($path,FALSE);
		    
		    $path_delete = $idx_folder; 
			//delete_files($path_delete, true);
			deleteDirectory($path_delete);

			$this->zip->download($idx_folder.'.zip');

		}

		// redirect('report/manage/'.$uc_period);

	}

	function download_examination_excel($uc_period = NULL, $uc_exam = NULL){
		$this->load->model('examination_m');

		$query = $this->examination_m->get_period_exam($uc_period,NULL, $uc_exam);
		if ($query->num_rows() > 0) {
			$result_period = $query->result();

			$idx_folder = $result_period[0]->pukp_label."-".$result_period[0]->upt_label. "-".$result_period[0]->level.'-' .$result_period[0]->exam_code;

			mkdir($idx_folder);


			$data['days'] = $result_period[0]->date;

			$name_folder = $idx_folder;

			// Get setting
			$this->load->model('setting_m');
			$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
			$data['setting'] = $q_set->row();

			$this->load->model('exam_competency_m');
				$query = $this->exam_competency_m->get_filtered(array('uc_exam' => $uc_exam));
				if ($query->num_rows()) {
					$result = $query->result();

					foreach ($result as $res) {
						$this->load->helper('text');

						if (($uc_period != NULL) && ($res->uc_exam != NULL) && ($res->uc_competency != NULL)) {

							$this->load->model('examination_m');
							$data['row'] = $this->examination_m->get_filtered(array('uc' => $uc_exam))->row();

							//Get Upt
							$this->load->model('period_m');
							$query = $this->period_m->get_list($uc_period);
							if ($query->num_rows() > 0) {
								$data['upt'] = $query->row();
							}

							//	Get Competency, Function & Level
							$this->load->model('competency_m');
							$query = $this->competency_m->get_detail_competency($res->uc_competency);
							if ($query->num_rows() > 0) {
								$row = $query->row();

								$data['competency_name']			= word_limiter($row->competency_name,6,'');
								$data['competency_name_title']		= $row->competency_name;
								$data['function_name']				= $row->function_name;
								$data['level']						= $row->level;
								$data['sequence']					= $row->sequence;
							}

							//	Get Score
							$this->load->model('participant_m');
							$query = $this->participant_m->get_all_score($uc_period, $res->uc_exam, $res->uc_competency);
							if ($query->num_rows() > 0) {
								$data['result'] = $query->result();
							}
						}

						$data['name_folder'] = $name_folder;

						$this->load->view('report/show_excel', $data);
					}
				}

				$this->load->library('zip');
				$this->load->helper('file');
		
				$path = $idx_folder.'/';
				$files = get_filenames($path);

				foreach ($files as $f) {	
					$this->zip->read_file($path.$f, true);
				}

				$path_delete = $idx_folder.'/'; 
				delete_files($path_delete, true);
				rmdir($path_delete);


				$this->zip->download($idx_folder.".zip");
		}
	}
	
	function clear_report(){
		$table = array(
						'tech_pengajuan_ukp','tech_pengajuan_ukp_temp',
						'tech_day','tech_day_temp',
						'tech_examination','tech_examination_temp',
						'tech_exam_attempt','tech_exam_attempt_temp',
						'tech_exam_attempt_competency','tech_exam_attempt_competency_temp',
						'tech_exam_competency', 'tech_exam_competency_temp',
						//'tech_exam_competency_package', 'tech_exam_competency_package_temp', 
						'tech_exam_match', 'tech_exam_match_temp',
						'tech_exam_options', 'tech_exam_options_temp',
						'tech_exam_question','tech_exam_question_temp',
						'tech_participant', 'tech_participant_temp',
						'tech_participant_master_temp',
						'tech_period', 'tech_period_temp',
						'tech_period_participant','tech_period_participant_temp',
						'tech_session','tech_session_temp',
						'tech_score', 'tech_score_temp',
						'tech_status_period', 'tech_status_period_temp',
						'tech_status', 'tech_status_temp'
					);

		foreach ($table as $tb) {
			$this->db->query(" TRUNCATE TABLE $tb;");
		}

		redirect('report');		
	}

	function period(){
		if (($this->input->post('f_level') != NULL) && ($this->input->post('f_category') != NULL)) {
			//	Get All Competency In This Period for certain Level & Category
			$this->load->model('exam_competency_m');
			$query = $this->exam_competency_m->get_competency_of_period($this->input->post('f_uc_period'), $this->input->post('f_level'), $this->input->post('f_category'));
			
			$comp_arr = array();
			
			$exam_arr = array();
			$exam_ucs = "";

			if ($query->num_rows() > 0) {
				$i = 0 ;
				foreach ($query->result() as $res) {
					//	Sorting Out Period
					if(!in_array($res->uc_competency, $comp_arr, true)){
						array_push($comp_arr, $res->uc_competency);
						$comp[$i]['uc_competency'] 	= $res->uc_competency;
						$comp[$i]['sequence'] 		= $res->sequence;
						$comp[$i]['label'] 			= $res->label;

						$i++;
					}

					//	Acquiring UC Exam
					if(!in_array($res->uc_exam, $exam_arr, true)){
						array_push($exam_arr, $res->uc_exam);
						$exam_ucs .= "'".$res->uc_exam."',";
					}
				}
				$exam_ucs = substr_replace($exam_ucs, '', -1);

				$data['comp'] = $comp;
			}

			//	Get All Result of Exam of This Period for certain Level & Category
			if ($exam_ucs != NULL) {
				$this->load->model('exam_attempt_m');
				$query = $this->exam_attempt_m->get_score_of_period($exam_ucs);
				
				if ($query->num_rows() > 0) {

					// Get setting for scoring options
					$this->load->model('setting_m');
					$row_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter')->row();

					$i =0; // Seafarer Code

					$curr_seafarer = "";

					foreach ($query->result() as $res) {
						//echo "<br /> - ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
						
						if ($res->seafarer_code != $curr_seafarer) {
							
							//echo "<br /> ---- [".$i."] ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
							$part[$i]['seafarer_code']  = $res->seafarer_code;
							$part[$i]['participant_no'] = $res->participant_no;
							$part[$i]['full_name'] 		= $res->full_name;

							$curr_seafarer = $res->seafarer_code;

							$i++;						
						}
						else {
							//echo "<br /> -- [".($i-1)."] ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
							$part[$i-1]['seafarer_code']  = $res->seafarer_code;
							$part[$i-1]['participant_no'] = $res->participant_no;
							$part[$i-1]['full_name'] 		= $res->full_name;
						}

						if ($res->uc != NULL) {
							if ($res->is_done == 0) {
								$score[$res->seafarer_code][$res->uc_competency] = "UF";
							}
							else {
								if ($res->score != NULL) {
									if ($row_set->value == 2) {
										if (isset($score[$res->seafarer_code][$res->uc_competency])) {
											if ($score[$res->seafarer_code][$res->uc_competency] < decryptIt($res->score_normal)) {
												$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_normal);
											}											
										}
										else {
											$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_normal);
										}
									} elseif ($row_set->value == 3) {
										$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_2);
									} else {
										$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score);
									}
								}
								else {
									$score[$res->seafarer_code][$res->uc_competency] = "NA";
								}
							}
						}
						else {
							$score[$res->seafarer_code][$res->uc_competency] = "UA";
						}						
					}

					$data['part'] = $part;
					$data['score'] = $score;
				}	
			}

			// Get Detail Level
			$this->load->model('level_m');
			$query = $this->level_m->get_filtered(array('uc' => $this->input->post('f_level')));
			$row = $query->row();
			$data['uc_level'] 		= $this->input->post('f_level');
			$data['level_label'] 	= $row->label;

			// Get Detail Period & UPT
			$this->load->model('period_m');
			$query = $this->period_m->detail_period_upt($this->input->post('f_uc_period'));
			$row = $query->row();
			$data['upt_name'] 	= $row->upt_label;
			$data['period']		= $row->period;
			$data['start_date']	= $row->date_start;
			$data['uc_pukp'] 	= $row->uc_pukp;
			$data['uc_upt'] 	= $row->uc_upt;

			$data['category'] 	= $this->input->post('f_category');
			$data['uc_period'] 	= $this->input->post('f_uc_period');

			//	Get All Level In Period
			$this->load->model('examination_m');
			$query = $this->examination_m->get_level_in_period($this->input->post('f_uc_period'));
			$data['level'] = $query->result();

			$this->im_render->main('report/period_recap', $data);
		}
		else {
			redirect('report/manage/'.$this->input->post('f_uc_period'));
		}
	}

	function recap_excel_DUMP($uc_period = NULL, $uc_level = NULL, $category = NULL){
		if (($uc_level != NULL) && ($category != NULL)) {
			//	Get All Competency In This Period for certain Level & Category
			$this->load->model('exam_competency_m');
			$query = $this->exam_competency_m->get_competency_of_period($uc_period, $uc_level, $category);
			
			$comp_arr = array();
			
			$exam_arr = array();
			$exam_ucs = "";

			if ($query->num_rows() > 0) {
				$i = 0 ;
				foreach ($query->result() as $res) {
					//	Sorting Out Period
					if(!in_array($res->uc_competency, $comp_arr, true)){
						array_push($comp_arr, $res->uc_competency);
						$comp[$i]['uc_competency'] 	= $res->uc_competency;
						$comp[$i]['sequence'] 		= $res->sequence;
						$comp[$i]['label'] 			= $res->label;

						$i++;
					}

					//	Acquiring UC Exam
					if(!in_array($res->uc_exam, $exam_arr, true)){
						array_push($exam_arr, $res->uc_exam);
						$exam_ucs .= "'".$res->uc_exam."',";
					}
				}
				$exam_ucs = substr_replace($exam_ucs, '', -1);

				$data['comp'] = $comp;
			}

			//	Get All Result of Exam of This Period for certain Level & Category
			if ($exam_ucs != NULL) {
				$this->load->model('exam_attempt_m');
				$query = $this->exam_attempt_m->get_score_of_period($exam_ucs);
				
				if ($query->num_rows() > 0) {

					// Get setting for scoring options
					$this->load->model('setting_m');
					$row_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter')->row();

					$i = 0; // Seafarer Code

					$curr_seafarer = "";

					foreach ($query->result() as $res) {
						//echo "<br /> - ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
						if ($res->seafarer_code != $curr_seafarer) {
							//echo "<br /> ---- [".$i."] ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
							$part[$i]['seafarer_code']  = $res->seafarer_code;
							$part[$i]['participant_no'] = $res->participant_no;
							$part[$i]['full_name'] 		= $res->full_name;

							$curr_seafarer = $res->seafarer_code;

							$i++;						
						}
						else {
							//echo "<br /> ---- [".($i-1)."] ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
							$part[$i-1]['seafarer_code']  	= $res->seafarer_code;
							$part[$i-1]['participant_no'] 	= $res->participant_no;
							$part[$i-1]['full_name'] 		= $res->full_name;
						}

						if ($res->uc != NULL) {
							if ($res->is_done == 0) {
								$score[$res->seafarer_code][$res->uc_competency] = "UF";
							}
							else {
								if ($res->score != NULL) {
									if ($row_set->value == 2) {
										if (isset($score[$res->seafarer_code][$res->uc_competency])) {
											if ($score[$res->seafarer_code][$res->uc_competency] < decryptIt($res->score_normal)) {
												$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_normal);
											}											
										}
										else {
											$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_normal);
										}
									} elseif ($row_set->value == 3) {
										$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_2);
									} else {
										$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score);
									}
								}
								else {
									$score[$res->seafarer_code][$res->uc_competency] = "NA";
								}
							}
						}
						else {
							$score[$res->seafarer_code][$res->uc_competency] = "UA";
						}
					}

					$data['part'] 	= $part;
					$data['score'] 	= $score;
				}	
			}

			// Get Detail Level
			$this->load->model('level_m');
			$query = $this->level_m->get_filtered(array('uc' => $uc_level));
			$row = $query->row();
			$data['uc_level'] 	= $this->input->post('f_level');
			$data['level'] 		= $row->label;

			// Get Detail Period & UPT
			$this->load->model('period_m');
			$query = $this->period_m->detail_period_upt($uc_period);
			$row = $query->row();
			$data['upt_name'] 	= $row->upt_label;
			$data['period']		= $row->period;
			$data['start_date']	= $row->date_start;

			$data['category'] 	= $category;
			$data['uc_period'] 	= $uc_period;
			
			$this->load->view('report/recapitulation_excel', $data);	
		}
		else {
			redirect('report/manage/'.$uc_period);
		}
	}

	function recap_pdf_DUMP($uc_period = NULL, $uc_level = NULL, $category = NULL){
		if (($uc_level != NULL) && ($category != NULL)) {
			//	Get All Competency In This Period for certain Level & Category
			$this->load->model('exam_competency_m');
			$query = $this->exam_competency_m->get_competency_of_period($uc_period, $uc_level, $category);
			
			$comp_arr = array();
			
			$exam_arr = array();
			$exam_ucs = "";

			if ($query->num_rows() > 0) {
				$i = 0 ;
				foreach ($query->result() as $res) {
					//	Sorting Out Period
					if(!in_array($res->uc_competency, $comp_arr, true)){
						array_push($comp_arr, $res->uc_competency);
						$comp[$i]['uc_competency'] 	= $res->uc_competency;
						$comp[$i]['sequence'] 		= $res->sequence;
						$comp[$i]['label'] 			= $res->label;

						$i++;
					}

					//	Acquiring UC Exam
					if(!in_array($res->uc_exam, $exam_arr, true)){
						array_push($exam_arr, $res->uc_exam);
						$exam_ucs .= "'".$res->uc_exam."',";
					}
				}
				$exam_ucs = substr_replace($exam_ucs, '', -1);

				$data['comp'] = $comp;
			}

			//	Get All Result of Exam of This Period for certain Level & Category
			if ($exam_ucs != NULL) {
				$this->load->model('exam_attempt_m');
				$query = $this->exam_attempt_m->get_score_of_period($exam_ucs);
				
				if ($query->num_rows() > 0) {

					// Get setting for scoring options
					$this->load->model('setting_m');
					$row_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter')->row();

					$i =0; // Seafarer Code

					$curr_seafarer = "";

					foreach ($query->result() as $res) {
						//echo "<br /> - ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
						if ($res->seafarer_code != $curr_seafarer) {
							//echo "<br /> ---- [".$i."] ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
							$part[$i]['seafarer_code']  = $res->seafarer_code;
							$part[$i]['participant_no'] = $res->participant_no;
							$part[$i]['full_name'] 		= $res->full_name;

							$curr_seafarer = $res->seafarer_code;

							$i++;						
						}
						else {
							//echo "<br /> ---- [".($i-1)."] ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
							$part[$i-1]['seafarer_code']  	= $res->seafarer_code;
							$part[$i-1]['participant_no'] 	= $res->participant_no;
							$part[$i-1]['full_name'] 		= $res->full_name;
						}

						if ($res->uc != NULL) {
							if ($res->is_done == 0) {
								$score[$res->seafarer_code][$res->uc_competency] = "UF";
							}
							else {
								if ($res->score != NULL) {
									if ($row_set->value == 2) {
										if (isset($score[$res->seafarer_code][$res->uc_competency])) {
											if ($score[$res->seafarer_code][$res->uc_competency] < decryptIt($res->score_normal)) {
												$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_normal);
											}											
										}
										else {
											$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_normal);
										}
									} elseif ($row_set->value == 3) {
										$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_2);
									} else {
										$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score);
									}
								}
								else {
									$score[$res->seafarer_code][$res->uc_competency] = "NA";
								}
							}
						}
						else {
							$score[$res->seafarer_code][$res->uc_competency] = "UA";
						}
					}

					$data['part'] 	= $part;
					$data['score'] 	= $score;
				}	
			}

			// Get Detail Level
			$this->load->model('level_m');
			$query = $this->level_m->get_filtered(array('uc' => $uc_level));
			$row = $query->row();
			$data['uc_level'] 	= $this->input->post('f_level');
			$data['level'] 		= $row->label;

			// Get Detail Period & UPT
			$this->load->model('period_m');
			$query = $this->period_m->detail_period_upt($uc_period);
			$row = $query->row();

			$data['upt_name'] 	= $row->upt_label;
			$data['period']		= $row->period;
			$data['start_date']	= $row->date_start;

			$data['category'] 	= $category;
			$data['uc_period'] 	= $uc_period;

			switch ($category) {
				case 1:
					$cat = "Pra";
					break;
				case 2:
					$cat = "Pasca";
					break;
				case 3:
					$cat = "DP";
					break;
			default:
					$cat = "N/A";
					break;
			}

			/* BEGIN Of export into pdf */
				$html = $this->load->view('report/recapitulation_pdf', $data, TRUE);

				// echo $html;

		        //this the the PDF filename that user will get to download
				$pdfFilePath = $row->upt_label."-".$row->period."(".time_format($row->date_start,'dMy').")-".$row->label."(".$cat.").pdf";

		        //load mPDF library
				$this->load->library('m_pdf');

				ob_clean(); // cleaning the buffer before Output()

		       //generate the PDF from the given html
				$this->m_pdf->pdf->WriteHTML($html);
			
		        //download it.
				$this->m_pdf->pdf->Output($pdfFilePath, "D");
			/* END Of export into pdf */
		}
		else {
			redirect('report/manage/'.$uc_period);
		}
	}

	function delete($uc = NULL){
		if ($uc != NULL) {
		 	/*DELETE ALL PERIOD*/

				// DELETE ALL ABOUT EXAM
				$query = $this->period_m->get_delete_period($uc);
					if ($query->num_rows() > 0) {
						$result_exam = $query->result();

						foreach ($result_exam as $re) {

							//DELETE EXAM ATTEMPT & EXAM ATTEMPT COMPETENCY
							$this->load->model('exam_attempt_m');
						 	$query = $this->exam_attempt_m->get_filtered(array('uc_exam' => $re->uc_exam));
						 	if ($query->num_rows() > 0) {
						 		$result = $query->result();

						 		$uc_exam_attempt = "";

						 		foreach ($result as $rs) {
						 			$uc_exam_attempt .= "'".$rs->uc."',";
						 		}

						 		$uc_exam_attempt = substr_replace($uc_exam_attempt, '', -1);

						 		// DELETE EXAM ATTEMPT COMPETENCY
						 		$this->load->model('exam_attempt_competency_m');
								$this->exam_attempt_competency_m->delete_attempt_competency($uc_exam_attempt);	

						 		// DELETE EXAM ATTEMPT
						 		$this->exam_attempt_m->delete_attempt($uc_exam_attempt);

						 	}					

						 	$this->load->model('exam_competency_m');
						 	$query = $this->exam_competency_m->get_filtered(array('uc_exam' => $re->uc_exam));
						 	if ($query->num_rows() > 0) {
						 		$result_package = $query->result();

						 		$uc_exam_package = "";

						 		foreach ($result_package as $rp) {
						 			$uc_exam_package .= "'".$rp->uc."',";
						 		}

						 		$uc_exam_package = substr_replace($uc_exam_package, '', -1);

						 		// DELETE EXAM PACKAGE
						 		$this->exam_competency_m->delete_exam_competency($uc_exam_package);			 		

						 	}						

							// DELETE EXAM PACKAGE
							$this->load->model('exam_package_m');
							$this->exam_package_m->delete_data(array('uc_exam' => $re->uc_exam));

							// DELETE EXAM MATCH
							$this->load->model('exam_match_m');
							$this->exam_match_m->delete_data(array('uc_exam' => $re->uc_exam));

							// DELETE EXAM OPTIONS
							$this->load->model('exam_options_m');
							$this->exam_options_m->delete_data(array('uc_exam' => $re->uc_exam));					

							// DELETE EXAM QUESTION
							$this->load->model('exam_question_m');
							$this->exam_question_m->delete_data(array('uc_exam' => $re->uc_exam));					

							// DELETE EXAM
							$this->load->model('examination_m');
							$this->examination_m->delete_data(array('uc' => $re->uc_exam));
						}

					}

					// DELETE PARTCIPANT
					$this->load->model('participant_m');
					$this->participant_m->delete_data(array('uc_period' => $uc));

					// DELETE PERIOD PARTICIPANT
					$this->load->model('period_participant_m');					
					$this->period_participant_m->delete_data(array('uc_period' => $uc));	

					//DELETE SESSION
					$this->load->model('day_m');					
				 	$query = $this->day_m->get_filtered(array('uc_period' => $uc));
				 	if ($query->num_rows() > 0) {
				 		$result_period = $query->result();

				 		$uc_day = "";

				 		foreach ($result_period as $rpe) {
				 			$uc_day .= "'".$rpe->uc."',";
				 		}

				 		$uc_day = substr_replace($uc_day, '', -1);


						//DELETE SESSION
						$this->load->model('session_m');	
						$this->session_m->delete_session($uc_day);				 		
				 	}

					//DELETE DAY
					$this->day_m->delete_data(array('uc_period' => $uc));
			
					// DELETE PERIOD
					$this->load->model('period_m');										
					$this->period_m->delete_data(array('uc' => $uc));
		 	/*END DELETE ALL PERIOD*/	
		}

		redirect('report/');

	}

	function show_answer($uc_period = NULL , $uc_competency = NULL , $uc_exam_attempt =  NULL){
		if ($uc_exam_attempt != NULL) {
			$data = "" ;
			$competency = "" ;
			$quest = "" ;

			$this->load->model('exam_attempt_m');
			$this->load->model('exam_question_m');
			$this->load->model('period_m');

			// Query for get detail answer student
			$query = $this->exam_attempt_m->get_info_user($uc_exam_attempt,$uc_competency);
			if ($query->num_rows() > 0) {

				// Get setting
				$this->load->model('setting_m');
				$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
				$data['setting'] = $q_set->row();

				$data['row'] = $query->row();
				
				$row = $query->row();

				$keys = explode(',', $row->keys);
				$answer = explode(',', $row->answers);
				$resans = explode(',', $row->answer_result);

				// Combine array between question and competency
				$question = array_combine(explode(',', $row->questions), explode(',', $row->competency));

				$q = 0;
				$uc_question = "";
				$rea_key = "";
				$rea_ans = "";
				$rea_resans = "";
				
				foreach ($question as $quest => $comp) {
					// condition for separate the question to per competency
					if ($uc_competency == $comp) {
						// get question
						$uc_question .= "'".$quest."'".',';
						// get key
						$rea_key .= $keys[$q].",";
						// get ans
						$rea_ans .= $answer[$q].",";
						// get ans
						$rea_resans .= $resans[$q].",";
					}
					$q++;
				}
				$question_uc	= substr_replace($uc_question, '', -1);
				$rea_key		= substr_replace($rea_key, '', -1);
				$rea_ans		= substr_replace($rea_ans, '', -1);
				$rea_resans		= substr_replace($rea_resans, '', -1);


				$data['keys']		= explode(',', $rea_key);
				$data['answers']	= explode(',', $rea_ans);
				$data['resans']	 	= explode(',', $rea_resans);

				// echo "<pre>";
				// print_r($answer);
				// echo "</pre>";

				// Separate question and options

				// Query for get the question, option, and match
				$query_ex = $this->exam_attempt_m->get_my_review($question_uc, $row->uc_exam);
				
				if ($query_ex->num_rows() > 0) {
					$result = $query_ex->result();

					//	Break question and answer into multi demensional array
					$curr_id = 0;
					$i = 0;
					$j = 0;
					foreach($result as $res) {
						if ($res->id != $curr_id) {
							$j = 0;

							$data['question_text_en'][$i] 			= htmlspecialchars_decode(stripslashes($res->question_text_en));
							$data['question_text_in'][$i]			= htmlspecialchars_decode(stripslashes($res->question_text_in));
							$data['question_type'][$i] 				= htmlspecialchars_decode(stripslashes($res->question_type));
							$data['q_att_type'][$i] 				= $res->question_att_type;
							$data['q_att_file'][$i] 				= $res->question_att_file;
							$data['exam_code'][$i]					= $res->exam_code;

							if ($res->question_type == 1) {
								$data['option_id'][$i][$j] 		= $res->option_id;
								$data['option_uc'][$i][$j] 		= $res->option_uc;
								$data['option_text_in'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
								$data['option_text_en'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));
								$data['o_att_type'][$i][$j]  	= $res->option_att_type;
							 	$data['o_att_file'][$i][$j]  	= $res->option_att_file;
								$data['answer_multi'][$i][$j]	= $res->answer_multiplechoice;	
								$data['is_correct'][$i][$j]		= $res->is_correct;
								//$data['kunci'][$i]				= $data['keys'][$i];

								$data['key_text'][$res->option_id] 	= htmlspecialchars_decode(stripslashes($res->option_text_en));
							}

							if ($res->question_type == 2) {
								$data['answer_truefalse'][$i][$j] = $res->answer_truefalse;
							}
							
							if ($res->question_type == 3) {						
								$data['match_key'][$i] 	= explode('-', $data['keys'][$i]);
								$data['match_ans'][$i] 	= explode('-', $data['answers'][$i]);
								$data['match_pair'][$i] = explode('-', $data['pairs'][$i]);
							}

							$i++;
							
							$curr_id = $res->id;
						} else {
							$data['question_type'][$i-1] = $res->question_type;

							if ($res->question_type == 1) {
								$data['option_id'][$i-1][$j] 		= $res->option_id;
								$data['option_uc'][$i-1][$j] 		= $res->option_uc;
								$data['option_text_in'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
								$data['option_text_en'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));		
							 	$data['o_att_type'][$i-1][$j]  	= $res->option_att_type;
							 	$data['o_att_file'][$i-1][$j]  	= $res->option_att_file;
							 	$data['answer_multi'][$i-1][$j]	= $res->answer_multiplechoice;	
							 	$data['is_correct'][$i-1][$j]		= $res->is_correct;
							 	// $data['bobot_option'][$i-1][$j]		= $res->bobot;							
							}

							if ($res->question_type == 2) {
								$data['answer_truefalse'][$i-1][$j] = $res->answer_truefalse;
							}
						}



						//	Assign Question, Answer & Pair Value, Indexed by pair_id
						if ($res->question_type == 3) {
							$data['question_field_in'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_in));
							$data['question_field_en'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_en));
							$data['m_q_type'][$res->pair_id] 			= $res->m_q_type;
							$data['m_q_file'][$res->pair_id] 			= $res->m_q_file;

							$data['answer_field_in'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_in));
							$data['answer_field_en'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_en));
							$data['m_a_type'][$res->pair_id]			= $res->m_a_type;
							$data['m_a_file'][$res->pair_id]			= $res->m_a_file;

						}

						$j++;
						$data['max_option'][$i-1] = $j;
					}

					$data['max_question'] 	= $i;
				}

			}

			$data['uc_period']			= $uc_period;
			$data['uc_competency']		= $uc_competency;
			$data['uc_exam_attempt']	= $uc_exam_attempt;

			// echo "<pre>";
			// print_r($data);
			// echo "</pre>";

			$this->load->view('report/show_answer', $data);
		}		
		
	}

	function report_by_answer_BACKUP($uc_period = NULL , $uc_competency = NULL , $uc_exam_attempt =  NULL){
		if ($uc_exam_attempt != NULL) {
			$data = "" ;
			$competency = "" ;
			$quest = "" ;

			$this->load->model('exam_attempt_m');
			$this->load->model('exam_question_m');
			$this->load->model('period_m');

			// Query for get detail answer student
			$query = $this->exam_attempt_m->get_info_user($uc_exam_attempt,$uc_competency);
			if ($query->num_rows() > 0) {

				// Get setting
				$this->load->model('setting_m');
				$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
				$data['setting'] = $q_set->row();

				$data['row'] = $query->row();
				
				$row = $query->row();

				$keys = explode(',', $row->keys);
				$answer = explode(',', $row->answers);
				$resans = explode(',', $row->answer_result);

				// Combine array between question and competency
				$question = array_combine(explode(',', $row->questions), explode(',', $row->competency));

				$q = 0;
				$uc_question = "";
				$rea_key = "";
				$rea_ans = "";
				$rea_resans = "";
				
				foreach ($question as $quest => $comp) {
					// condition for separate the question to per competency
					if ($uc_competency == $comp) {
						// get question
						$uc_question .= "'".$quest."'".',';
						// get key
						$rea_key .= $keys[$q].",";
						// get ans
						$rea_ans .= $answer[$q].",";
						// get ans
						$rea_resans .= $resans[$q].",";
					}
					$q++;
				}
				$question_uc	= substr_replace($uc_question, '', -1);
				$rea_key		= substr_replace($rea_key, '', -1);
				$rea_ans		= substr_replace($rea_ans, '', -1);
				$rea_resans		= substr_replace($rea_resans, '', -1);


				$data['keys']		= explode(',', $rea_key);
				$data['answers']	= explode(',', $rea_ans);
				$data['resans']	 	= explode(',', $rea_resans);



				// Separate question and options

				// Query for get the question, option, and match
				$query_ex = $this->exam_attempt_m->get_my_review($question_uc);
				if ($query_ex->num_rows() > 0) {
					$result = $query_ex->result();

					//	Break question and answer into multi demensional array
					$curr_id = 0;
					$i = 0;
					$j = 0;
					foreach($result as $res) {
						if ($res->id != $curr_id) {
							$j = 0;

							$data['question_text_en'][$i] 			= htmlspecialchars_decode(stripslashes($res->question_text_en));
							$data['question_text_in'][$i]			= htmlspecialchars_decode(stripslashes($res->question_text_in));
							$data['question_type'][$i] 				= htmlspecialchars_decode(stripslashes($res->question_type));
							$data['q_att_type'][$i] 				= $res->question_att_type;
							$data['q_att_file'][$i] 				= $res->question_att_file;
							$data['exam_code'][$i]					= $res->exam_code;

							if ($res->question_type == 1) {
								$data['option_id'][$i][$j] 		= $res->option_id;
								$data['option_uc'][$i][$j] 		= $res->option_uc;
								$data['option_text_in'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
								$data['option_text_en'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));
								$data['answer_multi'][$i][$j]	= $res->answer_multiplechoice;	
								$data['kunci'][$i]				= $data['keys'][$i];

								$data['key_text'][$res->option_id] 	= htmlspecialchars_decode(stripslashes($res->option_text_en));
							}

							if ($res->question_type == 2) {
								$data['answer_truefalse'][$i][$j] = $res->answer_truefalse;
							}
							
							if ($res->question_type == 3) {						
								$data['match_key'][$i] 	= explode('-', $data['keys'][$i]);
								$data['match_ans'][$i] 	= explode('-', $data['answers'][$i]);
								$data['match_pair'][$i] = explode('-', $data['pairs'][$i]);
							}

							$i++;
							
							$curr_id = $res->id;
						} else {
							$data['question_type'][$i-1] = $res->question_type;

							if ($res->question_type == 1) {
								$data['option_id'][$i-1][$j] 		= $res->option_id;
								$data['option_uc'][$i-1][$j] 		= $res->option_uc;
								$data['option_text_in'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
								$data['option_text_en'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));		
							 	$data['o_att_type'][$i-1][$j]  	= $res->option_att_type;
							 	$data['o_att_file'][$i-1][$j]  	= $res->option_att_file;
							 	$data['answer_multi'][$i-1][$j]	= $res->answer_multiplechoice;	
							 	// $data['bobot_option'][$i-1][$j]		= $res->bobot;							
							}

							if ($res->question_type == 2) {
								$data['answer_truefalse'][$i-1][$j] = $res->answer_truefalse;
							}
						}



						//	Assign Question, Answer & Pair Value, Indexed by pair_id
						if ($res->question_type == 3) {
							$data['question_field_in'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_in));
							$data['question_field_en'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_en));
							$data['m_q_type'][$res->pair_id] 			= $res->m_q_type;
							$data['m_q_file'][$res->pair_id] 			= $res->m_q_file;

							$data['answer_field_in'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_in));
							$data['answer_field_en'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_en));
							$data['m_a_type'][$res->pair_id]			= $res->m_a_type;
							$data['m_a_file'][$res->pair_id]			= $res->m_a_file;

						}

						$j++;
						$data['max_option'][$i-1] = $j;
					}

					$data['max_question'] 	= $i;
				}

			}
			$data['uc_period']			= $uc_period;
			$data['uc_competency']		= $uc_competency;
			$data['uc_exam_attempt']	= $uc_exam_attempt;

			$this->im_render->main('report/show_answer', $data);

		}		
		
	}

	function report_by_answer_pdf($uc_period = NULL , $uc_competency = NULL , $uc_exam_attempt =  NULL){
		if ($uc_exam_attempt != NULL) {
			$data = "" ;
			$competency = "" ;
			$quest = "" ;

			$this->load->model('exam_attempt_m');
			$this->load->model('exam_question_m');
			$this->load->model('period_m');

			// Query for get detail answer student
			$query = $this->exam_attempt_m->get_info_user($uc_exam_attempt,$uc_competency);
			if ($query->num_rows() > 0) {

				// Get setting
				$this->load->model('setting_m');
				$q_set = $this->setting_m->get_filtered(array('parameter' => 'scoring'), 'parameter');
				$data['setting'] = $q_set->row();

				$data['row'] = $query->row();
				
				$row = $query->row();

				$keys = explode(',', $row->keys);
				$answer = explode(',', $row->answers);
				$resans = explode(',', $row->answer_result);

				// Combine array between question and competency
				$question = array_combine(explode(',', $row->questions), explode(',', $row->competency));

				$q = 0;
				$uc_question = "";
				$rea_key = "";
				$rea_ans = "";
				$rea_resans = "";
				
				foreach ($question as $quest => $comp) {
					// condition for separate the question to per competency
					if ($uc_competency == $comp) {
						// get question
						$uc_question .= "'".$quest."'".',';
						// get key
						$rea_key .= $keys[$q].",";
						// get ans
						$rea_ans .= $answer[$q].",";
						// get ans
						$rea_resans .= $resans[$q].",";
					}
					$q++;
				}
				$question_uc	= substr_replace($uc_question, '', -1);
				$rea_key		= substr_replace($rea_key, '', -1);
				$rea_ans		= substr_replace($rea_ans, '', -1);
				$rea_resans		= substr_replace($rea_resans, '', -1);


				$data['keys']		= explode(',', $rea_key);
				$data['answers']	= explode(',', $rea_ans);
				$data['resans']	 	= explode(',', $rea_resans);



				// Separate question and options

				// Query for get the question, option, and match
				$query_ex = $this->exam_attempt_m->get_my_review($question_uc);
				if ($query_ex->num_rows() > 0) {
					$result = $query_ex->result();

					//	Break question and answer into multi demensional array
					$curr_id = 0;
					$i = 0;
					$j = 0;
					foreach($result as $res) {
						if ($res->id != $curr_id) {
							$j = 0;

							$data['question_text_en'][$i] 			= htmlspecialchars_decode(stripslashes($res->question_text_en));
							$data['question_text_in'][$i]			= htmlspecialchars_decode(stripslashes($res->question_text_in));
							$data['question_type'][$i] 				= htmlspecialchars_decode(stripslashes($res->question_type));
							$data['q_att_type'][$i] 				= $res->question_att_type;
							$data['q_att_file'][$i] 				= $res->question_att_file;
							$data['exam_code'][$i]					= $res->exam_code;

							if ($res->question_type == 1) {
								$data['option_id'][$i][$j] 		= $res->option_id;
								$data['option_uc'][$i][$j] 		= $res->option_uc;
								$data['option_text_in'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
								$data['option_text_en'][$i][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));
								$data['answer_multi'][$i][$j]	= $res->answer_multiplechoice;	
								$data['kunci'][$i]				= $data['keys'][$i];

								$data['key_text'][$res->option_id] 	= htmlspecialchars_decode(stripslashes($res->option_text_en));
							}

							if ($res->question_type == 2) {
								$data['answer_truefalse'][$i][$j] = $res->answer_truefalse;
							}
							
							if ($res->question_type == 3) {						
								$data['match_key'][$i] 	= explode('-', $data['keys'][$i]);
								$data['match_ans'][$i] 	= explode('-', $data['answers'][$i]);
								$data['match_pair'][$i] = explode('-', $data['pairs'][$i]);
							}

							$i++;
							
							$curr_id = $res->id;
						} else {
							$data['question_type'][$i-1] = $res->question_type;

							if ($res->question_type == 1) {
								$data['option_id'][$i-1][$j] 		= $res->option_id;
								$data['option_uc'][$i-1][$j] 		= $res->option_uc;
								$data['option_text_in'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_in));
								$data['option_text_en'][$i-1][$j] = htmlspecialchars_decode(stripslashes($res->option_text_en));		
							 	$data['o_att_type'][$i-1][$j]  	= $res->option_att_type;
							 	$data['o_att_file'][$i-1][$j]  	= $res->option_att_file;
							 	$data['answer_multi'][$i-1][$j]	= $res->answer_multiplechoice;	
							 	// $data['bobot_option'][$i-1][$j]		= $res->bobot;							
							}

							if ($res->question_type == 2) {
								$data['answer_truefalse'][$i-1][$j] = $res->answer_truefalse;
							}
						}



						//	Assign Question, Answer & Pair Value, Indexed by pair_id
						if ($res->question_type == 3) {
							$data['question_field_in'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_in));
							$data['question_field_en'][$res->pair_id] 	= htmlspecialchars_decode(stripslashes($res->question_field_en));
							$data['m_q_type'][$res->pair_id] 			= $res->m_q_type;
							$data['m_q_file'][$res->pair_id] 			= $res->m_q_file;

							$data['answer_field_in'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_in));
							$data['answer_field_en'][$res->pair_id]		= htmlspecialchars_decode(stripslashes($res->answer_field_en));
							$data['m_a_type'][$res->pair_id]			= $res->m_a_type;
							$data['m_a_file'][$res->pair_id]			= $res->m_a_file;

						}

						$j++;
						$data['max_option'][$i-1] = $j;
					}

					$data['max_question'] 	= $i;
				}

			}

			/* BEGIN Of export into pdf */
			$html = $this->load->view('report/show_answer_pdf', $data, TRUE);

			// echo $html;

	        //this the the PDF filename that user will get to download
			$pdfFilePath = $row->seafarer_code."(".$row->exam_code.")_Result.pdf";

	        //load mPDF library
			$this->load->library('m_pdf');

			ob_clean(); // cleaning the buffer before Output()

	       //generate the PDF from the given html
			$this->m_pdf->pdf->WriteHTML($html);
		
	        //download it.
			$this->m_pdf->pdf->Output($pdfFilePath, "D");
		/* END Of export into pdf */

		}		
		
	}

	function export(){
		$data = "";
		
		$data['uc_period']  = $this->input->post('uc_period');
		$data['uc_day'] 	= $this->input->post('uc_day');
		$data['hari'] 		= $this->input->post('hari');

		$this->load->view('report/export_per_day', $data);
	}

	function backup(){
		if($this->input->post('f_save')){

			$uc_period  = $this->input->post('f_uc_period');
			$uc_day		= $this->input->post('f_uc_day');
			$hari		= $this->input->post('f_hari');
			$date 		= $this->input->post('f_date');

			//	Generate Query of DAY Data
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
								$exam_quest .= " INSERT INTO `tech_exam_question_temp` (`uc`, `uc_question`, `question_code`, `question_title_in`, `question_title_en`, `question_text_in`, `question_text_en`,n requestion_att_type`, `question_att_file`, `question_type`, `answer_truefalse`, `answer_multiplechoice`, `uc_exam`, `is_exist`) VALUES ";
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
			
			$query = $this->examination_m->get_period_exam($uc_period,$uc_day);
			if($query->num_rows() > 0){
				$result_period = $query->result();
				$replace_periode = str_replace('/', '-', $result_period[0]->period);		
				$namefile = $result_period[0]->pukp_label."-".$result_period[0]->upt_label."-Period[".$replace_periode."] - Day[".$hari."]";

			}

			$this->load->library('zip');
			$this->load->helper('file');

			//CREATE FILE
			$dir = $namefile.".cba";
			$timestamp = strtotime($date);
			touch($dir,$timestamp);	

            // Encrypt query
            $en_query = $this->encrypt->encode($all_query);

			//CREAT ZIP
			$this->zip->add_data($dir, $en_query);
			unlink($dir);
			$this->zip->download($namefile.'.zip');	 
		}
	}

	function finish_all($uc_period = NULL, $uc_exam = NULL){
		if($uc_exam != NULL){

			$this->load->model('exam_attempt_m');
			$query = $this->exam_attempt_m->get_unfinish($uc_exam);
			if ($query->num_rows() > 0) {
				$result 	= $query->result();
				$num_result = $query->num_rows();

				$uc_att = array();
				foreach ($result as $res) {
					array_push($uc_att, $res->uc);
				}

				for ($i=0; $i < $num_result ; $i++) { 
					if ($uc_att[$i] != NULL) {	

						//	Calculate Score
						$this->scoring($uc_att[$i]);

						/* BEGIN Of Check attempt competency */
							$this->load->model('exam_attempt_competency_m');
							$query = $this->exam_attempt_competency_m->get_filtered(array('uc_exam_attempt' => $uc_att[$i]));	
							if ($query->num_rows() > 0) {
								
								//	Update Finish Time
								$this->load->model('exam_attempt_m');
								$data = array(
												'time_finish' 		=> current_time(),
												'is_done'			=> 1
											);
								$filter = array('uc'	=> $uc_att[$i]);
								$this->exam_attempt_m->update_data($data, $filter);
															
							}
						 // END Of Check attempt competency 
					}				
				}				
			}
			
			redirect('report/manage/'.$uc_period);
		}
	}

	function scoring($att_code = NULL, $i = 1){
		if ($att_code != NULL) {

			//	Get keys and answers
			$query = $this->exam_attempt_m->get_filtered(array('uc' => $att_code));
			if ($query->num_rows() > 0) {
				$row = $query->row();

				$answer_arr = explode(',', $row->answers);
				$key_arr	= explode(',', $row->keys);
				$competency	= explode(',', $row->competency);
			}

			// Prop for insert exam_attempt
			$z = 0;
			$sc 				= array();
			$result_arr			= array();

			// Prop for insert exam_attempt_comp
			$curr_comp = 0;
			$uc_competency = "";

			foreach ($answer_arr as $aa ){

				/* Count the score and result answer */
					if ($answer_arr[$z] == $key_arr[$z]) {
						array_push($result_arr, "T");
					}
					elseif ($answer_arr[$z] == "0") {
						array_push($result_arr, "-");
					}
					else {
						array_push($result_arr, "F");
					}
				/* Count the score and result answer */


				/* BEGIN Of Get for insert_exam_attempt_competency */
					// Get all competency
					if ($curr_comp != $competency[$z]) {
						$i = 0;

						$uc_competency .= "".$competency[$z].",";

						$curr_comp = $competency[$z];

						$i++;
					} else {
						$i++;

						// count quest per competency
						$num_quest_comp[$competency[$z]] = $i;
					}
				/* END Of Get for insert_exam_attempt_competency */

				$z++;
			}
			// Finishing get all uc competency
			$uc_competency = substr_replace($uc_competency, "", -1);


			/* BEGIN Of Update exam_attempt */
			$a_result = implode(',', $result_arr);

			///	updating score
			$data = array(
							'answer_result'	=> $a_result,
							'time_finish'	=> current_time()
						);

			$filter = array('uc' => $att_code);
			$this->exam_attempt_m->update_data($data, $filter);
			/* END Of Update exam_attempt */

			// Prop for get answer and key the attempted
			$res_ans = array();
			$res_key = array();
			$z = 0;
			foreach ($answer_arr as $ra) {
				// Get answer
				$res_ans[$competency[$z]][$z] = $ra;

				// Get key
				$res_key[$competency[$z]][$z] = $key_arr[$z];

				$z++;
			}

			/* BEGIN Of inserting score per competency */
				foreach (explode(',', $uc_competency) as $comp) {

					/* BEGIN Of Counting per competency */
						$comp_true = 0;
						$comp_false = 0;
						foreach ($res_ans[$comp] as $k_ra => $ra) {
							if ($ra == $res_key[$comp][$k_ra]) {
								$comp_true++;
							}
							else if ($ra == "0") {
								// Condition when answer is IDK, and do nothing
							}
							else {
								$comp_false++;
							}
						}
						//	FINAL SCORING
						///	MODE : True (+1), False (0)
						$total_score_comp_normal = value_format((($comp_true / $num_quest_comp[$comp]) * 100), ',', '.', 2);

							// Condition make Natural number
							if ($total_score_comp_normal >= 95) {
								$total_score_comp_normal = 100;
							}
							elseif ($total_score_comp_normal >= 85) {
								$total_score_comp_normal = 90;
							}
							elseif ($total_score_comp_normal >= 75) {
								$total_score_comp_normal = 80;
							}
							elseif ($total_score_comp_normal >= 65) {
								$total_score_comp_normal = 70;
							}
							elseif ($total_score_comp_normal >= 55) {
								$total_score_comp_normal = 60;
							}
							elseif ($total_score_comp_normal >= 45) {
								$total_score_comp_normal = 50;
							}
							elseif ($total_score_comp_normal >= 35) {
								$total_score_comp_normal = 40;
							}
							elseif ($total_score_comp_normal >= 25) {
								$total_score_comp_normal = 30;
							}
							elseif ($total_score_comp_normal >= 15) {
								$total_score_comp_normal = 20;
							}
							elseif ($total_score_comp_normal >= 10) {
								$total_score_comp_normal = 10;
							}
							elseif ($total_score_comp_normal >= 5) {
								$total_score_comp_normal = 10;
							}
							elseif ($total_score_comp_normal < 5) {
								$total_score_comp_normal = 0;
							}

						///	MODE : True (+2), False (-1), Empty/IDK (0)
						$point = (($comp_true*2)-$comp_false);
						$total_score_comp = value_format(($point / ($num_quest_comp[$comp]*2) * 100), ',', '.', 2);

						///	MODE : True (+4), False (-1), Empty/IDK (0)
						$point = (($comp_true*4)-$comp_false);
						$total_score_comp_2 = value_format(($point / ($num_quest_comp[$comp]*4) * 100), ',', '.', 2);
						
					/* END Of Counting per competency */

					// Encrypt Score
					$score = encryptIt($total_score_comp);
					$score_2 = encryptIt($total_score_comp_2);
					$score_normal = encryptIt($total_score_comp_normal);
					
					/*CHECK INSERT DOUBLE*/
					$this->load->model('exam_attempt_competency_m');

					
					$data = array(
								'uc'				=> unique_code(),
								'uc_exam_attempt'	=> $att_code,
								'uc_competency'		=> $comp,
								'seafarer_code'		=> $row->seafarer_code,
								'score'				=> $score,
								'score_2'			=> $score_2,
								'score_normal'		=> $score_normal
					);
					
					/*BEGIN Of INSERT per competency proccesss*/
						// Start check state INSERT
						$this->db->trans_start();
						// Start INSERT data
						$this->exam_attempt_competency_m->insert_data($data);
						// Finishing check state INSERT
						$this->db->trans_complete();

						// Condition when data failed to insert
						if ($this->db->trans_status() == FALSE) {
							$i++;

							if ($i < 4) {
								$this->scoring($att_code, $i);
							}
						}
					/*END Of INSERT per competency proccesss*/
				}
			/* END Of inserting score per competency */
		}
	}	

	function report_answer(){
		$data = "";

		$page = 1;
		//	Pagination Initialization
		$this->load->library('im_pagination');
		///	Define Offset
		$offset = ($page - 1) * $this->each_page;
		//	Define Parameters
		$params = array(
							'page_number'	=> $page,
							'each_page'		=> $this->each_page,
							'page_int'		=> $this->page_int,	
							'segment' 		=> 'period',
							'model'			=> 'period_m'
						);


		$query = $this->period_m->get_list(NULL, $this->each_page, $offset);
		if ($query->num_rows() > 0) {
			$data['result'] 	= $query->result();
		}

		$query = $this->period_m->get_list();
		if ($query->num_rows() > 0) {
			$params['total_record'] = $query->num_rows();
			$data['pagination'] 	= $this->im_pagination->render_ajax($params);
			$data['total_record'] 	= $query->num_rows();
		}

		$data['numbering'] 	= ($this->each_page * ($page-1)) + 1;

		$query = $this->period_m->get_list(NULL, $this->each_page, $offset);
		if ($query->num_rows() > 0) {
			$data['result'] 	= $query->result();
		}

		// Score 1, Answer 2
		$data['category'] = 2;

		$this->im_render->main('report/report_answer', $data);
	}

	function page_report_answer(){
		$data = "";

		$page 		= ($this->input->post('js_page') != 1 ? $this->input->post('js_page') : 1);
		//	Pagination Initialization
		$this->load->library('im_pagination');
		///	Define Offset
		$offset = ($page - 1) * $this->each_page;
		//	Define Parameters
		$params = array(
							'page_number'	=> $page,
							'each_page'		=> $this->each_page,
							'page_int'		=> $this->page_int,	
							'segment' 		=> 'period',
							'model'			=> 'period_m'
						);


		$query = $this->period_m->get_list(NULL, $this->each_page, $offset);
		if ($query->num_rows() > 0) {
			$data['result'] 	= $query->result();
		}

		$query = $this->period_m->get_list();
		if ($query->num_rows() > 0) {
			$params['total_record'] = $query->num_rows();
			$data['pagination'] 	= $this->im_pagination->render_ajax($params);
			$data['total_record'] 	= $query->num_rows();
		}

		$data['numbering'] 	= ($this->each_page * ($page-1)) + 1;

		$query = $this->period_m->get_list(NULL, $this->each_page, $offset);
		if ($query->num_rows() > 0) {
			$data['result'] 	= $query->result();
		}

		// Score 1, Answer 2
		$data['category'] = 2;

		$this->load->view('report/page_report_answer',$data);
	}

	function recap($uc_period = NULL, $show = NULL){
		$data = "";
		if ($uc_period != NULL) {
			//	BEGIN of Get Detail Periode & Exam List
			$this->load->model('period_m');
			$q_period = $this->period_m->get_exams($uc_period);

			if ($q_period->num_rows() > 0 ){
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

					$data['comp'] = $result;

					$comp_ucs = NULL; 
					foreach ($result as $res) {
						$comp_ucs .= "'".$res->uc."',";
					}
					$comp_ucs = substr_replace($comp_ucs, '', -1);

				}
				//	END of Get Competencies of This Period

				if ($show == 'adjust') {
					//	Get Exam of Competency
					$this->load->model('exam_competency_m');
					$query = $this->exam_competency_m->get_exams($comp_ucs, $uc_period);
					if ($query->num_rows() > 0) {
						foreach ($query->result() as $ex) {
							$exam[$ex->uc_competency] = $ex->uc_exam;
						}
					}

					$data['exam'] = $exam;
				}

				//	BEGIN of Get Scores
				if ($exam_ucs != NULL) {
					$this->load->model('exam_attempt_m');
					$query = $this->exam_attempt_m->get_score_of_period($exam_ucs);
					
					if ($query->num_rows() > 0) {

						$i =0; // Seafarer Code

						$curr_seafarer = "";

						//$seafcs = NULL;
						foreach ($query->result() as $res) {
							//echo "<br /> - ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score_normal;
							
							if ($res->seafarer_code != $curr_seafarer) {
								
								//echo "<br /> ---- [".$i."] ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
								$part[$i]['seafarer_code']  = $res->seafarer_code;
								$part[$i]['participant_no'] = $res->participant_no;
								$part[$i]['full_name'] 		= $res->full_name;


								//$seafcs .= "'".$res->seafarer_code."',";

								$curr_seafarer = $res->seafarer_code;

								$i++;						
							}
							else {
								//echo "<br /> -- [".($i-1)."] ".$res->seafarer_code." - ".$res->full_name." - ".$res->uc_competency." - ".$res->score;
								$part[$i-1]['seafarer_code']  = $res->seafarer_code;
								$part[$i-1]['participant_no'] = $res->participant_no;
								$part[$i-1]['full_name'] 	  = $res->full_name;
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
										$score[$res->seafarer_code][$res->uc_competency] = decryptIt($res->score_normal);
										$att[$res->seafarer_code][$res->uc_competency] = $res->uc;

										$data['att']	= $att;	
										
									}
									else {
										//	If Score Not Exist
										$score[$res->seafarer_code][$res->uc_competency] = "NA";
									}
								}

							}
							else {
								//	If Not Attempt
								$score[$res->seafarer_code][$res->uc_competency] = "UA";
							}
						}

						//$seafcs = substr_replace($seafcs, '', -1);

						$data['part'] 	= $part;
						$data['score'] 	= $score;
					}	
				}
				//	END of Get Scores

				//	BEGIN of GET Status				
				$this->load->model('status_period_m');
                $query = $this->status_period_m->get_status_period($uc_period);
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $res) {
                        if ($res->is_pass != NULL) {
                        	$status[$res->seafarer_code][$res->uc_competency] = $res->is_pass;
                        }
                    }

                    if (isset($status)) {
                    	$data['status'] = $status;
                    }
                }
				//	END of GET Status

				$data['category'] 	= $diklat;
				$data['ex_status'] 	= $ex_status;
				$data['uc_period']	= $uc_period;
				$data['period']		= $period;
				$data['uc_pukp'] 	= $uc_pukp;
				$data['pukp_label']	= $pukp_label;
				$data['uc_upt'] 	= $uc_upt;
				$data['upt_label'] 	= $upt_label;
				$data['uc_level'] 	= $uc_level;
				$data['level']		= $level;
				$data['type']	 	= 1;
				
			}

			if ($show == NULL) {
				$this->load->view('report/recapitulation', $data);
			}
			else if ($show == "adjust") {
				$this->load->view('report/adjust', $data);
			}
			else if ($show == "excel") {
				$this->load->view('report/recap_excel', $data);
			}
		}
		else {
			redirect('report');
		}
	}

	function new_result($uc_period = NULL){
		if ($uc_period != NULL) {
			$data['uc_period'] = $uc_period;
			$this->im_render->main('report/form_new_result', $data);			
		}
		else {
			redirect('report');
		}
	}

	function regenerate_result(){
		if ($this->input->post('f_update')) {
			$uc_period = $this->input->post('f_uc_period');

			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));

	        $config['upload_path']      = './excel/'; //buat folder dengan nama assets di root folder
	        $config['allowed_types']    = 'xls|xlsx|csv';
	        $config['max_size']         = 10000;

	        $this->load->library('upload');
	        $this->upload->initialize($config);

	        if (! $this->upload->do_upload('f_file')){
	            $msg = $this->upload->display_errors();

	            $this->session->set_flashdata('msg', $msg);

	            redirect('period_schedule/manage_participant/'.$uc_period);
	        }
	        else{
	        	//  BEGIN of GET DATA FORM EXCEL
	            $upload_data    = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
	            $inputFileName  = 'excel/'.$upload_data['file_name'];

	            try
	            {
	                $inputFileType  = IOFactory::identify($inputFileName);
	                $objReader      = IOFactory::createReader($inputFileType);
	                $objPHPExcel    = $objReader->load($inputFileName);
	            } 
	            catch(Exception $e)
	            {
	                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
	            }

	            $sheet          = $objPHPExcel->getSheet(0);
	            $highestRow     = $sheet->getHighestRow();
	            $highestColumn  = $sheet->getHighestColumn();
	            //  END of GET DATA FORM EXCEL

	            //	BEGIN of Get Period List
	        	$this->load->model('period_m');
				$q_period = $this->period_m->get_exams($uc_period);

				if ($q_period->num_rows() > 0 ){
					$result 	= $q_period->result();
					$diklat 	= $result[0]->pra_pasca;
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
						//$query = $query->result();

						$i = 1;
						foreach ($query->result() as $res) {
							$comp[$i] = $res->uc;

							$i++;
						}
					}

					// echo "<br /> COMPETENCY";
					// echo "<pre>";
					// print_r($comp);
					// echo "</pre>";					
				}
				//	END of Get Competencies of This Period

	            $i = 0;
	            $sc_arr = array();

	            for ($row = 11; $row <= $highestRow; $row++){
	            	$rowData = $sheet->rangeToArray('B' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

	                //  COLLECT DATA Seafarer Code & Scores

	            	$seafarer_code = strval($rowData[0][0]);
	            	array_push($sc_arr, $seafarer_code);

	            	for ($col = 2; $col < count($rowData[0]) - 1; $col++) {
	            		$idx = $col-1;
	                	
	                	echo "<br /> - ".$idx."-".$comp[$idx]." -> ".$rowData[0][$col+1];
	                	$ex_sco[$seafarer_code][$comp[$idx]] = $rowData[0][$col+1];	                	
	                }
	            }

	            // echo "<br /> NEW SCORE";
	            // echo "<pre>";
	            // print_r($ex_sco);
	            // echo "</pre>";

	            //DELETE FILE AFTER INSERT
	        	unlink("./".$inputFileName);

				//	BEGIN of Get Scores
				if ($exam_ucs != NULL) {
					$this->load->model('exam_attempt_m');
					$query = $this->exam_attempt_m->get_score_of_period($exam_ucs);
					
					if ($query->num_rows() > 0) {

						// $i = 0; // Seafarer Code
						// $curr_seafarer = "";

						foreach ($query->result() as $res) {
							//	Assign UDP (UC Diklat Participant)
							$udp[strval($res->seafarer_code)] = $res->uc_diklat_participant;

							echo "<br /> curr_seafarer <br /> - ".$res->seafarer_code." - ";
							if ($res->uc != NULL) {
								//	If Attempt
								if ($res->is_done == 0) {
									//	If Unfinish
									$the_score = "UF";
								}
								else {
									//	If Finish
									if ($res->score != NULL) {
										//echo "<br /> --- ".$res->seafarer_code." - ".$res->uc_competency." - ".$res->score;
										$the_score = decryptIt($res->score_normal);
										$eac[strval($res->seafarer_code)][strval($res->uc_competency)] = $res->uc_eac;
									}
									else {
										//	If Score Not Exist
										$the_score = "NA";
									}
								}
							}
							else {
								//	If Not Attempt
								$the_score = "UA";

							}

							$score[strval($res->seafarer_code)][strval($res->uc_competency)] = $the_score;						
						}

						echo "<pre>";
						print_r($eac);
						echo "</pre>";
					}
					//	END of Get Scores

					//	BEGIN of GET Status				
					$this->load->model('status_period_m');
	                $query = $this->status_period_m->get_status_period($uc_period);
	                if ($query->num_rows() > 0) {
	                    foreach ($query->result() as $res) {
	                        if ($res->is_pass != NULL) {
	                            $status[strval($res->seafarer_code)][strval($res->uc_competency)] = $res->status;
	                        }
	                    }

	                    //$data['status'] = $status;
	                }

	    			// echo "<br /> PREV STATUS";
					// echo "<pre>";
					// print_r($status);
					// echo "</pre>";
					//	END of GET Status
				}

				//	BEGIN of UPDATE SCORE & STATUS
				$this->load->model('exam_attempt_competency_m');
				$this->load->model('score_m');
				$this->load->model('status_m');

				$field_sta_per 	= "(`uc`, `uc_period`, `uc_competency`, `diklat_type`, `uc_diklat_participant`,`seafarer_code`, `is_pass`, `score`, `uc_score`, `status`)";
				$field_status	= "(`uc`, `uc_competency`, `pra_pasca`, `seafarer_code`, `is_pass`, `score_max`, `uc_score`, `status`)";

				$new_sta_per_value 	= "";		//	New Record for Status Period, for data that never exist before
				$new_status_value 	= "";

				foreach ($sc_arr as $sc) {
					foreach ($comp as $co) {
						$where = array(
											'uc_period' 	=> $uc_period,
											'uc_competency'	=> $co,
											'seafarer_code'	=> $sc
										);

						//echo "<br /> SCO : ".$score[$sc][$co]." - ".$eac[$sc][$co];

						if (isset($score[$sc][$co])) {
							//	Jika yg diubah score yg sudah ada
							// echo "<br /> - SCO - ".$ex_sco[$sc][$co]." == ".$score[$sc][$co];

							if ($ex_sco[$sc][$co] != $score[$sc][$co]) {
								$is_pass 		= ($ex_sco[$sc][$co] >= 70 ? 1 : 0);
								$final_status 	= ($is_pass == 1 ? "SL" : "BL");
								$per_status 	= ($is_pass == 1 ? "L" : "BL");

								//	Update Exam Attempt Competency
								$data_eac		= array('score_normal' => encryptIt($ex_sco[$sc][$co]));
								$where_eac		= array('uc' => $eac[$sc][$co]);
								$this->exam_attempt_competency_m->update_data($data_eac, $where_eac);
								echo "<br > --- UPDATE : ".$ex_sco[$sc][$co]." => ".$eac[$sc][$co];
								
								 //	Update Score & Status Period								
								$data_score 	= array('score_normal' => encryptIt($ex_sco[$sc][$co]));
								$data_staper	= array(
														'is_pass' 	=> $is_pass,
														'score'		=> encryptIt($ex_sco[$sc][$co]),
														'status'	=> $per_status
														);
								$this->score_m->update_data($data_score, $where);
								$this->status_period_m->update_data($data_staper, $where);


								//	Update Score & Status Period
								$where_status  = array(
														'uc_competency'	=> $co,
														'seafarer_code'	=> $sc,
														'pra_pasca'		=> $diklat
													);
								$data_status	= array(
														'is_pass' 	=> $is_pass,
														'score_max'	=> encryptIt($ex_sco[$sc][$co]),
														'status'	=> $final_status                                                                              
														);
								$this->status_m->update_data($data_status, $where_status);
							}
						}
						else if (isset($status[$sc][$co])) {
							//echo "<br /> - STA - ".$ex_sco[$sc][$co]." == ".$status[$sc][$co];
							// Update Status yang sudah ada
							if ($ex_sco[$sc][$co] != $status[$sc][$co]) {

								if ($ex_sco[$sc][$co] == "SL") {
									$is_pass = 1;
									$final_status = "SL";
								}
								else {
									$is_pass = 0;
									$final_status = "BL";
								}
								
								// Update Status Period
								$data_staper = array(
														'is_pass' 	=> $is_pass,
														'score'		=> NULL,
														'status'	=> $ex_sco[$sc][$co]
														);
								$this->status_period_m->update_data($data_staper, $where);

								// Update Status
								$data_status	= array(
													'is_pass' 	=> $is_pass,
													'score_max'	=> NULL,
													'status'	=> $final_status                                                                              
													);
								$this->status_m->update_data($data_status, $where_status);
							}

						}
						else {
							// Update (Insert) Status tidak ada
							// $field_sta_per = "(`uc`, `uc_period`, `uc_competency`, `diklat_type`, `uc_diklat_participant`,`seafarer_code`, `is_pass`, `score`, `uc_score`, `status`)";

							if (($ex_sco[$sc][$co] == "SL") || ($ex_sco[$sc][$co] == "BL")) {
								// echo "<br /> + ".$sc." - ".$co." => ".$ex_sco[$sc][$co];
								// echo "<br /> ----- ".$diklat." - ".$udp[$sc];

								if ($ex_sco[$sc][$co] == "SL") {
									$is_pass 	= 1;
									$per_status = "SL"; 
								}
								else if ($ex_sco[$sc][$co] == "BL") {
									$is_pass 	= 0;
									$per_status = "BL"; 	
								}
								else {
									$is_pass 	= NULL;
									$per_status = NULL;
								}

								$new_sta_per_value .= "('".unique_code()."', '".$uc_period."', '".$co."', '".$diklat."', '".$udp[$sc]."', '".$sc."', '".$is_pass."', 'NULL', 'NULL', '".$per_status."'),";

								$new_status_value .= "('".unique_code()."', '".$co."', '".$diklat."', '".$sc."', '".$is_pass."', 'NULL', 'NULL', '".$per_status."'),"; 

								
							}
						}
					}
				}

				if ($new_sta_per_value != "") {
					$new_sta_per_value = substr_replace($new_sta_per_value, '', -1);
					$this->status_period_m->insert_multi_value($field_sta_per, $new_sta_per_value);
				}

				if ($new_status_value != "") {
					$new_status_value = substr_replace($new_status_value, '', -1);
					$this->status_m->insert_multi_value($field_status, $new_status_value);
				}				


				//	END of UPDATE SCORE

				//redirect('report/recap/'.$uc_period);			
			}
        }
	}

	function regenerate_finish($uc_period = NULL, $uc_exam = NULL){
		if($uc_exam != NULL){

			$this->load->model('exam_attempt_m');
			$query = $this->exam_attempt_m->get_unfinish($uc_exam);
			if ($query->num_rows() > 0) {
				$result 	= $query->result();
				$num_result = $query->num_rows();

				$uc_att = array();
				foreach ($result as $res) {
					array_push($uc_att, $res->uc);
				}

				for ($i=0; $i < $num_result ; $i++) { 
					if ($uc_att[$i] != NULL) {	

						//	Calculate Score
						$this->scoring($uc_att[$i]);

						/* BEGIN Of Check attempt competency */
							$this->load->model('exam_attempt_competency_m');
							$query = $this->exam_attempt_competency_m->get_filtered(array('uc_exam_attempt' => $uc_att[$i]));	
							if ($query->num_rows() > 0) {
								
								//	Update Finish Time
								$this->load->model('exam_attempt_m');
								$data = array(
												'time_finish' 		=> current_time(),
												'is_done'			=> 1
											);
								$filter = array('uc'	=> $uc_att[$i]);
								$this->exam_attempt_m->update_data($data, $filter);
															
							}
						 // END Of Check attempt competency 
					}				
				}				
			}
			
			redirect('report/recap/'.$uc_period.'/adjust');
		}
	}

	function decrypt_report($file_name = NULL){
		$file_name = "PUKP 07 - [06.21 - 06.26] - 26 Jun.cba";
		//	Insert to Temp Table
		$templine = NULL;
		// Read in entire file
		$lines = file("./exim/".$file_name);
		$dec = $this->encrypt->decode($lines[0]);

		$de_lines = explode("\n\r",$dec);

		foreach ($de_lines as $line) {
			
			// Skip it if it's a comment
			if (substr($line, 0, 2) == '--' || $line == '')
			    continue;

			echo $line;
			echo "<hr />";

			// // Add this line to the current segment
			// $templine .= $line;
			

			// // If it has a semicolon at the end, it's the end of the query
			// if (substr(trim($line), -2, 2) == ');') {

			// 	// Perform the query
			// 	$this->db->query($templine);
			// 	 // echo $templine;

			//     // Reset temp variable to empty
			//     $templine = NULL;
			// }
		}
	}

	function regenerate_attempt_INDEV($uc_period = 0, $uc_exam = 0){
		//echo "<br /> + ".$uc_period." - ".$uc_exam;
		
		$this->load->model('exam_question_m');
        $this->load->model('exam_attempt_m');
        $this->load->model('exam_attempt_competency_m');
        $this->load->model('score_m');

        // Get all exam of period
        $q_att_xam = $this->exam_attempt_m->get_filtered(array('uc_exam' => $uc_exam));
        if ($q_att_xam->num_rows() > 0) {
            
            foreach ($q_att_xam->result() as $rax) {
                
                $uc_att = $rax->uc;

                // Get detail of attempt
                //$q_att = $this->exam_attempt_m->get_filtered(array('uc' => $uc_att));
                $q_att = $this->exam_attempt_m->get_with_uc_diklat($uc_att, $uc_period);
             	
            	if ($q_att->num_rows() > 0) {                    
                    $att = $q_att->row();

     //                //	DELETE old score for this attempt
     //                ///	Get Attempt Competency                    
     //                $qattc = $this->exam_attempt_competency_m->get_filtered(array('uc_exam_attempt' => $uc_att));
					// if ($qattc->num_rows() > 0) {
					// 	$arr_att_comp = array();	// For Deleting Existing Score Purpose
					// 	foreach ($qattc->result() as $rac) {
					// 		array_push($arr_att_comp, $rac->uc);			
					// 	}

					// 	$this->score_m->delete_where_in($arr_att_comp);
					// }

     //                // Delete old attempt comp
     //                $this->exam_attempt_competency_m->delete_data(array('uc_exam_attempt' => $uc_att));


                    // Regenerate Key
                    $keys = "";

                    // Get array question
                    $arr_quest = explode(',', $att->questions);
                    // echo "<br /> -----------";
                    // echo "<br /> QUE : ".count($arr_quest);

                    $question = "";
                    foreach ($arr_quest as $rq) {
                    	$question .= "'".$rq."',";
                    }
                    $question = substr_replace($question, '', -1);

                    // Generate key
					//echo "<br /> Q : ".$question;
					$q_quest = $this->exam_question_m->get_question_in_emer($question, $uc_exam);
					// echo "<br /> N U : ".$q_quest->num_rows();


					if ($q_quest->num_rows() > 0) {
						foreach ($q_quest->result() as $rk) {
                        	if ($rk->question_type == 1) {
                                $dkey[$rk->uc] = $rk->eo_uc;
                            }
                            else if ($rk->question_type == 2) {
                            	$dkey[$rk->uc] = $rk->truefalse_answer;
                            }
                            else {
                                $dkey[$rk->uc] = NULL;
                            }
						}

                        
						$n = 1;
                        foreach ($arr_quest as $rq) {
                        	echo "<br /> ".$n.". ".$rq;

                        	if (isset($dkey[$rq])) {
                        		echo " -> ".$dkey[$rq];
                        	}
                        	else {
                        		echo " -> NULL";
                        	}


                        	$n++;
                        }
                    }    	



					/*
                    if ($q_quest->num_rows() > 0) {

                        foreach ($q_quest->result() as $rk) {
                        	if ($rk->question_type == 1) {
                                $keys .= $rk->eo_uc.",";
                            }
                            else if ($rk->question_type == 2) {
                                $keys .= $rk->truefalse_answer.",";
                            }
                            else {
                                $keys .= NULL.",";
                            }
                        }
                        $keys = substr_replace($keys, '', -1);
                    }

                    // Update key proccess
                    $this->exam_attempt_m->update_data(array('keys' => $keys), array('uc' => $uc_att));

                    // ReInsert Attempt comp (score)
                    $answer_arr = explode(',', $att->answers);
                    $key_arr    = explode(',', $keys);
                    $competency = explode(',', $att->competency);

                    // Prop for insert exam_attempt
                    $z = 0;
                    $sc                 = array();
                    $result_arr         = array();

                    // Prop for insert exam_attempt_comp
                    $curr_comp = 0;
                    $uc_competency = "";

                    echo "<br /> ANS : ".count($answer_arr);
                    echo "<br /> KEY : ".count($key_arr);
                    echo "<br /> COM : ".count($competency);


					$c_true = 0;
                    foreach ($answer_arr as $aa){
                    	//echo "<br /> - ".$answer_arr[$z]." == ".$key_arr[$z];
                        // BEGIN Count the score and result answer
                        if ($answer_arr[$z] == $key_arr[$z]) {
                        	// echo " --> T";
                            array_push($result_arr, "T");
                            $c_true ++;
                        }
                        elseif ($answer_arr[$z] == "0") {
                            array_push($result_arr, "-");
                        }
                        else {
                            array_push($result_arr, "F");
                        }
                        // END Count the score and result answer

                        // BEGIN Of Get for insert_exam_attempt_competency
                        // Get all competency
                        if ($curr_comp != $competency[$z]) {
                            $i = 0;

                            $uc_competency .= "".$competency[$z].",";

                            $curr_comp = $competency[$z];

                            $i++;
                        } else {
                            $i++;

                            // count quest per competency
                            $num_quest_comp[$competency[$z]] = $i;
                        }
                        // END Of Get for insert_exam_attempt_competency

                        $z++;
                    }
                    //echo "<br /> --------> ".count($arr_quest)."TRUE : ".$c_true;

                    // Finishing get all uc competency
                    $uc_competency = substr_replace($uc_competency, "", -1);

                    // BEGIN Of Update exam_attempt
                    $a_result = implode(',', $result_arr);

                    /// updating answer result & is_done status 
                    $data = array(
                                    'answer_result' => $a_result,
                                    'is_done' => 1
                                );
                    $filter = array('uc' => $uc_att);
                    $this->exam_attempt_m->update_data($data, $filter);
                    // END Of Update exam_attempt

                    // Prop for get answer and key the attempted
                    $res_ans = array();
                    $res_key = array();
                    $z = 0;
                    foreach ($answer_arr as $ra){
                        // Get answer
                        $res_ans[$competency[$z]][$z] = $ra;

                        // Get key
                        $res_key[$competency[$z]][$z] = $key_arr[$z];

                        $z++;
                    }

                    // BEGIN Of inserting score per competency AND score to temp
                    $this->reg_attempt_competency($uc_period, $att, $uc_att, $uc_competency, $num_quest_comp, $res_ans, $res_key);

                    //	INSERT Score from Temp to Real
                    $this->reg_score_temp_to_real();

                    // UPDATE Status Period
                    $this->reg_status_period();

                    // UPDATE Status
                    $this->reg_status();
                    */
                }
            }
        }
        // $this->db->truncate('tech_score_temp');

        // redirect('report/recap/'.$uc_period.'/adjust');
	}

	function regattemer_DEBUG($uc_period, $uc_competency){
		echo "<br /> + ".$uc_period." --> ".$uc_competency;
	}

	function regattemer($uc_period, $uc_competency){
		//echo "<br /> + ".$uc_period." --> ".$uc_competency;
		
        $this->load->model('exam_question_m');
        $this->load->model('exam_attempt_m');
        $this->load->model('exam_attempt_competency_m');
        $this->load->model('score_m');
		//	Get Exam Attempt
		$query = $this->exam_attempt_competency_m->get_my_attempt($uc_period, $uc_competency);
		//echo "<br /> NUMPAR : ".$query->num_rows();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $res) {
				$uc_att = $res->uc_exam_attempt;
				$uc_exam = $res->uc_exam;

				//echo "<br /> ATT : ".$uc_att."  >  EX : ".$uc_exam." ---> ".$res->seafarer_code;


				// Get detail of attempt
                $q_att = $this->exam_attempt_m->get_filtered(array('uc' => $uc_att));
                //$q_att = $this->exam_attempt_m->get_with_uc_diklat($uc_att, $uc_period);
             	
             	//echo "<br /> NR ".$q_att->num_rows();

            	if ($q_att->num_rows() > 0) {
            		$att = $q_att->row();

                    //	DELETE old score for this attempt
                    ///	Get Attempt Competency                    
     //                $qattc = $this->exam_attempt_competency_m->get_filtered(array('uc_exam_attempt' => $uc_att));
					// if ($qattc->num_rows() > 0) {
					// 	$res_eac = $qattc->result();

					// 	$arr_att_comp = array();	// For Deleting Existing Score Purpose

					// 	foreach ($res_eac as $rac) {
					// 		array_push($arr_att_comp, $rac->uc);			
					// 	}

					// 	$this->score_m->delete_where_in($arr_att_comp);
					// }

            		// Delete old attempt comp
                    $this->exam_attempt_competency_m->delete_data(array('uc_exam_attempt' => $uc_att));



					// Regenerate Key
                    $keys = "";

                    // Get array question
                    $arr_quest = explode(',', $att->questions);
                    // echo "<br /> -----------";
                    // echo "<br /> QUE : ".count($arr_quest);

                    
                    $question = "";
                    foreach ($arr_quest as $rq) {
                    	$question .= "'".$rq."',";
                    }
                    $question = substr_replace($question, '', -1);

                    // Generate key
					//$q_quest = $this->exam_question_m->get_question_in_emer($question, $uc_exam);
					//$q_quest = $this->exam_question_m->get_question_in($question, $uc_exam);
					$q_quest = $this->exam_question_m->get_question_in($question);
					// echo "<br /> Q : ".$question;
					// echo "<br /> N U : ".$q_quest->num_rows();
                    if ($q_quest->num_rows() > 0) {
//
                        foreach ($q_quest->result() as $rk) {
                        	if ($rk->question_type == 1) {
                                //$keys .= $rk->eo_uc.",";
                                $keys .= $rk->eo_uc.",";
                            }
                            else if ($rk->question_type == 2) {
                                //$keys .= $rk->truefalse_answer.",";
                                $keys .= $rk->answer_truefalse.",";
                            }
                            else {
                                $keys .= NULL.",";
                            }
                        }
                        $keys = substr_replace($keys, '', -1);
                    }

                    // echo "<br /> KEYS : ".$keys;
                   
                    // Update key proccess
                    $this->exam_attempt_m->update_data(array('keys' => $keys), array('uc' => $uc_att));
                    // ReInsert Attempt comp (score)
                    $answer_arr = explode(',', $att->answers);
                    $key_arr    = explode(',', $keys);
                    $competency = explode(',', $att->competency);

                    // Prop for insert exam_attempt
                    $z = 0;
                    $sc                 = array();
                    $result_arr         = array();

                    // Prop for insert exam_attempt_comp
                    $curr_comp = 0;
                    $uc_competency = "";

                    // echo "<br /> ANS : ".count($answer_arr);
                    // echo "<br /> KEY : ".count($key_arr);
                    // echo "<br /> COM : ".count($competency);
                    

					$c_true = 0;
                    foreach ($answer_arr as $aa){
                    	//echo "<br /> - ".$answer_arr[$z]." == ".$key_arr[$z];
                        // BEGIN Count the score and result answer
                        if ($answer_arr[$z] == $key_arr[$z]) {
                        	// echo " --> T";
                            array_push($result_arr, "T");
                            $c_true ++;
                        }
                        elseif ($answer_arr[$z] == "0") {
                            array_push($result_arr, "-");
                        }
                        else {
                            array_push($result_arr, "F");
                        }
                        // END Count the score and result answer

                        // BEGIN Of Get for insert_exam_attempt_competency
                        // Get all competency
                        if ($curr_comp != $competency[$z]) {
                            $i = 0;

                            $uc_competency .= "".$competency[$z].",";

                            $curr_comp = $competency[$z];

                            $i++;
                        } else {
                            $i++;

                            // count quest per competency
                            $num_quest_comp[$competency[$z]] = $i;
                        }
                        // END Of Get for insert_exam_attempt_competency

                        $z++;
                    }
                    //echo "<br /> --------> ".count($arr_quest)."TRUE : ".$c_true;

                    // Finishing get all uc competency
                    $uc_competency = substr_replace($uc_competency, "", -1);

                    // BEGIN Of Update exam_attempt
                    $a_result = implode(',', $result_arr);

                    /// updating answer result & is_done status 
                    $data = array(
                                    'answer_result' => $a_result,
                                    'is_done' => 1
                                );
                    $filter = array('uc' => $uc_att);
                    $this->exam_attempt_m->update_data($data, $filter);
                    // END Of Update exam_attempt

                    // Prop for get answer and key the attempted
                    $res_ans = array();
                    $res_key = array();
                    $z = 0;
                    foreach ($answer_arr as $ra){
                        // Get answer
                        $res_ans[$competency[$z]][$z] = $ra;

                        // Get key
                        $res_key[$competency[$z]][$z] = $key_arr[$z];

                        $z++;
                    }

                    // // BEGIN Of inserting score per competency AND score to temp
                    $this->reg_attempt_competency($uc_period, $att, $uc_att, $uc_competency, $num_quest_comp, $res_ans, $res_key);
                  
                    
                    // //	INSERT Score from Temp to Real
                    // $this->reg_score_temp_to_real();

                    // // UPDATE Status Period
                    // $this->reg_status_period();

                    // // UPDATE Status
                    // $this->reg_status();                    
                }

			}
		}

		$this->db->truncate('tech_score_temp');

        redirect('report/recap/'.$uc_period.'/adjust');
	}

	function regattemer_regex($uc_period, $uc_competency){
		//echo "<br /> + ".$uc_period." --> ".$uc_competency;
		
        $this->load->model('exam_question_m');
        $this->load->model('exam_attempt_m');
        $this->load->model('exam_attempt_competency_m');
        $this->load->model('score_m');
		//	Get Exam Attempt
		$query = $this->exam_attempt_competency_m->get_my_attempt($uc_period, $uc_competency);
		//echo "<br /> NUMPAR : ".$query->num_rows();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $res) {
				$uc_att = $res->uc_exam_attempt;
				$uc_exam = $res->uc_exam;

				echo "<br /> ATT : ".$uc_att."  >  EX : ".$uc_exam." ---> ".$res->seafarer_code;

				// Get detail of attempt
                $q_att = $this->exam_attempt_m->get_filtered(array('uc' => $uc_att));
                //$q_att = $this->exam_attempt_m->get_with_uc_diklat($uc_att, $uc_period);
             	
             	//echo "<br /> NR ".$q_att->num_rows();

            	if ($q_att->num_rows() > 0) {
            		$att = $q_att->row();

                    //	DELETE old score for this attempt
                    ///	Get Attempt Competency                    
     //                $qattc = $this->exam_attempt_competency_m->get_filtered(array('uc_exam_attempt' => $uc_att));
					// if ($qattc->num_rows() > 0) {
					// 	$res_eac = $qattc->result();

					// 	$arr_att_comp = array();	// For Deleting Existing Score Purpose

					// 	foreach ($res_eac as $rac) {
					// 		array_push($arr_att_comp, $rac->uc);			
					// 	}

					// 	$this->score_m->delete_where_in($arr_att_comp);
					// }

            		// Delete old attempt comp
                    //$this->exam_attempt_competency_m->delete_data(array('uc_exam_attempt' => $uc_att));



					// Regenerate Key
                    $keys = "";

                    // Get array question
                    $arr_quest = explode(',', $att->questions);
                    // echo "<br /> -----------";
                    // echo "<br /> QUE : ".count($arr_quest);

                    
                    $question_regex = "'";
                    foreach ($arr_quest as $rq) {
                    	//$question .= "'".$rq."',";

                    	$question_regex .= $rq."|";
                    }
                    $question_regex = substr_replace($question_regex, '', -1)."'";

                    echo "<br/> QREG : ".$question_regex;

                    
                    // Generate key
					//$q_quest = $this->exam_question_m->get_question_in_emer($question, $uc_exam);
					//$q_quest = $this->exam_question_m->get_question_in($question, $uc_exam);
					$q_quest = $this->exam_question_m->get_question_in_regex($question_regex);
					// echo "<br /> Q : ".$question;
					// echo "<br /> N U : ".$q_quest->num_rows();
                    if ($q_quest->num_rows() > 0) {
//
                        foreach ($q_quest->result() as $rk) {
                        	if ($rk->question_type == 1) {
                                //$keys .= $rk->eo_uc.",";
                                $keys .= $rk->eo_uc.",";
                            }
                            else if ($rk->question_type == 2) {
                                //$keys .= $rk->truefalse_answer.",";
                                $keys .= $rk->answer_truefalse.",";
                            }
                            else {
                                $keys .= NULL.",";
                            }
                        }
                        $keys = substr_replace($keys, '', -1);
                    }

                    // echo "<br /> KEYS : ".$keys;
                   
                    // Update key proccess
                    $this->exam_attempt_m->update_data(array('keys' => $keys), array('uc' => $uc_att));
                    // ReInsert Attempt comp (score)
                    $answer_arr = explode(',', $att->answers);
                    $key_arr    = explode(',', $keys);
                    $competency = explode(',', $att->competency);

                    // Prop for insert exam_attempt
                    $z = 0;
                    $sc                 = array();
                    $result_arr         = array();

                    // Prop for insert exam_attempt_comp
                    $curr_comp = 0;
                    $uc_competency = "";

                    echo "<br /> ANS : ".count($answer_arr);
                    echo "<br /> KEY : ".count($key_arr);
                    echo "<br /> COM : ".count($competency);
                    

					$c_true = 0;
                    foreach ($answer_arr as $aa){
                    	echo "<br /> - ".$answer_arr[$z]." == ".$key_arr[$z];
                        // BEGIN Count the score and result answer
                        if ($answer_arr[$z] == $key_arr[$z]) {
                        	// echo " --> T";
                            array_push($result_arr, "T");
                            $c_true ++;
                        }
                        elseif ($answer_arr[$z] == "0") {
                            array_push($result_arr, "-");
                        }
                        else {
                            array_push($result_arr, "F");
                        }
                        // END Count the score and result answer

                        // BEGIN Of Get for insert_exam_attempt_competency
                        // Get all competency
                        if ($curr_comp != $competency[$z]) {
                            $i = 0;

                            $uc_competency .= "".$competency[$z].",";

                            $curr_comp = $competency[$z];

                            $i++;
                        } else {
                            $i++;

                            // count quest per competency
                            $num_quest_comp[$competency[$z]] = $i;
                        }
                        // END Of Get for insert_exam_attempt_competency

                        $z++;
                    }
                    //echo "<br /> --------> ".count($arr_quest)."TRUE : ".$c_true;
                    /*
                    // Finishing get all uc competency
                    $uc_competency = substr_replace($uc_competency, "", -1);

                    // BEGIN Of Update exam_attempt
                    $a_result = implode(',', $result_arr);

                    /// updating answer result & is_done status 
                    $data = array(
                                    'answer_result' => $a_result,
                                    'is_done' => 1
                                );
                    $filter = array('uc' => $uc_att);
                    $this->exam_attempt_m->update_data($data, $filter);
                    // END Of Update exam_attempt

                    // Prop for get answer and key the attempted
                    $res_ans = array();
                    $res_key = array();
                    $z = 0;
                    foreach ($answer_arr as $ra){
                        // Get answer
                        $res_ans[$competency[$z]][$z] = $ra;

                        // Get key
                        $res_key[$competency[$z]][$z] = $key_arr[$z];

                        $z++;
                    }

                    // // BEGIN Of inserting score per competency AND score to temp
                    $this->reg_attempt_competency($uc_period, $att, $uc_att, $uc_competency, $num_quest_comp, $res_ans, $res_key);
                  
                    
                    // //	INSERT Score from Temp to Real
                    // $this->reg_score_temp_to_real();

                    // // UPDATE Status Period
                    // $this->reg_status_period();

                    // // UPDATE Status
                    // $this->reg_status();  
                    */                  
                }
			}
		}

		//$this->db->truncate('tech_score_temp');

        //redirect('report/recap/'.$uc_period.'/adjust');
	}

	function regatt_single($uc_period, $seafarer_code, $uc_competency) {
		echo "<br /> : uc_period - seafarer_code - uc_competency";
		echo "<br /> + ".$uc_period." - ".$seafarer_code." : --> ".$uc_competency;
		
        $this->load->model('exam_question_m');
        $this->load->model('exam_attempt_m');
        $this->load->model('exam_attempt_competency_m');
        $this->load->model('score_m');
		//	Get Exam Attempt
		$query = $this->exam_attempt_competency_m->get_my_attempt_only($uc_period, $uc_competency, $seafarer_code);
		//echo "<br /> NUMPAR : ".$query->num_rows();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $res) {
				$uc_att = $res->uc_exam_attempt;
				$uc_exam = $res->uc_exam;

				echo "<br /> ATT : ".$uc_att."  >  EX : ".$uc_exam." ---> ".$res->seafarer_code;

				// Get detail of attempt
                $q_att = $this->exam_attempt_m->get_filtered(array('uc' => $uc_att));
                //$q_att = $this->exam_attempt_m->get_with_uc_diklat($uc_att, $uc_period);
             	
             	//echo "<br /> NR ".$q_att->num_rows();

            	if ($q_att->num_rows() > 0) {
            		$att = $q_att->row();

//                    $this->exam_attempt_competency_m->delete_data(array('uc_exam_attempt' => $uc_att));

					// Regenerate Key
                    $keys = "";

                    // Get array question
                    $arr_quest = explode(',', $att->questions);
                    // echo "<br /> -----------";
                    // echo "<br /> QUE : ".count($arr_quest);

                    
                    $question = "";
                    foreach ($arr_quest as $rq) {
                    	$question .= "'".$rq."',";
                    }
                    $question = substr_replace($question, '', -1);

                    // Generate key
					$q_quest = $this->exam_question_m->get_question_in($question);
					//echo "<br /> Q : ".$question;
					//echo "<br /> N U : ".$q_quest->num_rows();
                    if ($q_quest->num_rows() > 0) {

                        foreach ($q_quest->result() as $rk) {
                        	if ($rk->question_type == 1) {
                                //$keys .= $rk->eo_uc.",";
                                $keys .= $rk->eo_uc.",";
                            }
                            else if ($rk->question_type == 2) {
                                //$keys .= $rk->truefalse_answer.",";
                                $keys .= $rk->answer_truefalse.",";
                            }
                            else {
                                $keys .= NULL.",";
                            }
                        }
                        $keys = substr_replace($keys, '', -1);
                    }

                    //echo "<br /> KEYS : ".$keys;
                   
                    // Update key proccess
//                    $this->exam_attempt_m->update_data(array('keys' => $keys), array('uc' => $uc_att));
                    // ReInsert Attempt comp (score)
                    $answer_arr = explode(',', $att->answers);
                    $key_arr    = explode(',', $keys);
                    $competency = explode(',', $att->competency);

                    // Prop for insert exam_attempt
                    $z = 0;
                    $sc                 = array();
                    $result_arr         = array();

                    // Prop for insert exam_attempt_comp
                    $curr_comp = 0;
                    $uc_competency = "";

                    echo "<br /> ANS : ".count($answer_arr);
                    echo "<br /> KEY : ".count($key_arr);
                    echo "<br /> COM : ".count($competency);
                    
                    echo "<br /> - Competency : Answer == Key";
					$c_true = 0;
                    foreach ($answer_arr as $aa){
                    	echo "<br /> - ".$competency[$z]." : ".$answer_arr[$z]." == ".$key_arr[$z];
                        // BEGIN Count the score and result answer
                        if ($answer_arr[$z] == $key_arr[$z]) {
                        	echo " --> T";
                            array_push($result_arr, "T");
                            $c_true ++;
                        }
                        elseif ($answer_arr[$z] == "0") {
                            array_push($result_arr, "-");
                        }
                        else {
                            array_push($result_arr, "F");
                        }
                        // END Count the score and result answer

                        // BEGIN Of Get for insert_exam_attempt_competency
                        // Get all competency
                        if ($curr_comp != $competency[$z]) {
                            $i = 0;

                            $uc_competency .= "".$competency[$z].",";

                            $curr_comp = $competency[$z];

                            $i++;
                        } else {
                            $i++;

                            // count quest per competency
                            $num_quest_comp[$competency[$z]] = $i;
                        }
                        // END Of Get for insert_exam_attempt_competency

                        $z++;
                    }
                    
                    //echo "<br /> --------> TRUE : ".$c_true." OF ".count($arr_quest);

                    // Finishing get all uc competency
                    $uc_competency = substr_replace($uc_competency, "", -1);

                    // BEGIN Of Update exam_attempt
                    $a_result = implode(',', $result_arr);

                    /// updating answer result & is_done status 
                    $data = array(
                                    'answer_result' => $a_result,
                                    'is_done' => 1
                                );
                    $filter = array('uc' => $uc_att);
                    $this->exam_attempt_m->update_data($data, $filter);
                    // END Of Update exam_attempt

                    // Prop for get answer and key the attempted
                    $res_ans = array();
                    $res_key = array();
                    $z = 0;
                    foreach ($answer_arr as $ra){
                        // Get answer
                        $res_ans[$competency[$z]][$z] = $ra;

                        // Get key
                        $res_key[$competency[$z]][$z] = $key_arr[$z];

                        $z++;
                    }

                    // // BEGIN Of inserting score per competency AND score to temp
                    $this->reg_attempt_competency($uc_period, $att, $uc_att, $uc_competency, $num_quest_comp, $res_ans, $res_key);
                                     
                    // //	INSERT Score from Temp to Real
                    // $this->reg_score_temp_to_real();

                    // // UPDATE Status Period
                    // $this->reg_status_period();

                    // // UPDATE Status
                    // $this->reg_status();s                                    
                }
			}
		}

		//redirect('report/recap/'.$uc_period.'/adjust');
	}

	function regenerate_attempt($uc_period = 0, $uc_exam = 0){
		//echo "<br /> + ".$uc_period." - ".$uc_exam;
		
		$this->load->model('exam_question_m');
        $this->load->model('exam_attempt_m');
        $this->load->model('exam_attempt_competency_m');
        $this->load->model('score_m');

        // Get all exam of period
        $q_att_xam = $this->exam_attempt_m->get_filtered(array('uc_exam' => $uc_exam));
        if ($q_att_xam->num_rows() > 0) {
            
            foreach ($q_att_xam->result() as $rax) {
                
                $uc_att = $rax->uc;

                // Get detail of attempt
                //$q_att = $this->exam_attempt_m->get_filtered(array('uc' => $uc_att));
                $q_att = $this->exam_attempt_m->get_with_uc_diklat($uc_att, $uc_period);
             	
            	if ($q_att->num_rows() > 0) {                    
                    $att = $q_att->row();

                    //	DELETE old score for this attempt
                    ///	Get Attempt Competency                    
                    $qattc = $this->exam_attempt_competency_m->get_filtered(array('uc_exam_attempt' => $uc_att));
					if ($qattc->num_rows() > 0) {
						$arr_att_comp = array();	// For Deleting Existing Score Purpose
						foreach ($qattc->result() as $rac) {
							array_push($arr_att_comp, $rac->uc);			
						}

						$this->score_m->delete_where_in($arr_att_comp);
					}

                    // Delete old attempt comp
                    $this->exam_attempt_competency_m->delete_data(array('uc_exam_attempt' => $uc_att));


                    // Regenerate Key
                    $keys = "";

                    // Get array question
                    $arr_quest = explode(',', $att->questions);
                    // echo "<br /> -----------";
                    // echo "<br /> QUE : ".count($arr_quest);

                    $question = "";
                    foreach ($arr_quest as $rq) {
                    	$question .= "'".$rq."',";
                    }
                    $question = substr_replace($question, '', -1);

                    // Generate key
					//$q_quest = $this->exam_question_m->get_question_in_emer($question, $uc_exam);
					$q_quest = $this->exam_question_m->get_question_in($question, $uc_exam);
					// echo "<br /> Q : ".$question;
					//echo "<br /> N U : ".$q_quest->num_rows();
                    if ($q_quest->num_rows() > 0) {

                        foreach ($q_quest->result() as $rk) {
                        	if ($rk->question_type == 1) {
                                //$keys .= $rk->eo_uc.",";
                                $keys .= $rk->eo_uc.",";
                            }
                            else if ($rk->question_type == 2) {
                                //$keys .= $rk->truefalse_answer.",";
                                $keys .= $rk->answer_truefalse.",";
                            }
                            else {
                                $keys .= NULL.",";
                            }
                        }
                        $keys = substr_replace($keys, '', -1);
                    }

                    //echo "<br /> KEYS : ".$keys;
                    // Update key proccess
                    $this->exam_attempt_m->update_data(array('keys' => $keys), array('uc' => $uc_att));

                    // ReInsert Attempt comp (score)
                    $answer_arr = explode(',', $att->answers);
                    $key_arr    = explode(',', $keys);
                    $competency = explode(',', $att->competency);

                    // Prop for insert exam_attempt
                    $z = 0;
                    $sc                 = array();
                    $result_arr         = array();

                    // Prop for insert exam_attempt_comp
                    $curr_comp = 0;
                    $uc_competency = "";

                    // echo "<br /> ANS : ".count($answer_arr);
                    // echo "<br /> KEY : ".count($key_arr);
                    // echo "<br /> COM : ".count($competency);

					$c_true = 0;
                    foreach ($answer_arr as $aa){
                    	//echo "<br /> - ".$answer_arr[$z]." == ".$key_arr[$z];
                        // BEGIN Count the score and result answer
                        if ($answer_arr[$z] == $key_arr[$z]) {
                        	// echo " --> T";
                            array_push($result_arr, "T");
                            $c_true ++;
                        }
                        elseif ($answer_arr[$z] == "0") {
                            array_push($result_arr, "-");
                        }
                        else {
                            array_push($result_arr, "F");
                        }
                        // END Count the score and result answer

                        // BEGIN Of Get for insert_exam_attempt_competency
                        // Get all competency
                        if ($curr_comp != $competency[$z]) {
                            $i = 0;

                            $uc_competency .= "".$competency[$z].",";

                            $curr_comp = $competency[$z];

                            $i++;
                        } else {
                            $i++;

                            // count quest per competency
                            $num_quest_comp[$competency[$z]] = $i;
                        }
                        // END Of Get for insert_exam_attempt_competency

                        $z++;
                    }
                    //echo "<br /> --------> ".count($arr_quest)."TRUE : ".$c_true;

                    // Finishing get all uc competency
                    $uc_competency = substr_replace($uc_competency, "", -1);

                    // BEGIN Of Update exam_attempt
                    $a_result = implode(',', $result_arr);

                    /// updating answer result & is_done status 
                    $data = array(
                                    'answer_result' => $a_result,
                                    'is_done' => 1
                                );
                    $filter = array('uc' => $uc_att);
                    $this->exam_attempt_m->update_data($data, $filter);
                    // END Of Update exam_attempt

                    // Prop for get answer and key the attempted
                    $res_ans = array();
                    $res_key = array();
                    $z = 0;
                    foreach ($answer_arr as $ra){
                        // Get answer
                        $res_ans[$competency[$z]][$z] = $ra;

                        // Get key
                        $res_key[$competency[$z]][$z] = $key_arr[$z];

                        $z++;
                    }

                    // BEGIN Of inserting score per competency AND score to temp
                    $this->reg_attempt_competency($uc_period, $att, $uc_att, $uc_competency, $num_quest_comp, $res_ans, $res_key);

                    //	INSERT Score from Temp to Real
                    $this->reg_score_temp_to_real();

                    // UPDATE Status Period
                    $this->reg_status_period();

                    // UPDATE Status
                    $this->reg_status();
                }
            }
        }
        $this->db->truncate('tech_score_temp');

        redirect('report/recap/'.$uc_period.'/adjust');
	}

	function regenerate_attempt_BACKUP($uc_period = 0, $uc_exam = 0){
		// echo "<br /> + ".$uc_period." - ".$uc_exam;
		$this->load->model('exam_question_m');
        $this->load->model('exam_attempt_m');
        $this->load->model('exam_attempt_competency_m');
        $this->load->model('score_m');

        // Get all exam of period
        $q_att_xam = $this->exam_attempt_m->get_filtered(array('uc_exam' => $uc_exam));
        if ($q_att_xam->num_rows() > 0) {
            
            foreach ($q_att_xam->result() as $rax) {
                
                $uc_att = $rax->uc;

                // Get detail of attempt
                //$q_att = $this->exam_attempt_m->get_filtered(array('uc' => $uc_att));
                $q_att = $this->exam_attempt_m->get_with_uc_diklat($uc_att, $uc_period);
             	
            	if ($q_att->num_rows() > 0) {                    
                    $att = $q_att->row();

                    //	DELETE old score for this attempt
                    ///	Get Attempt Competency                    
                    $qattc = $this->exam_attempt_competency_m->get_filtered(array('uc_exam_attempt' => $uc_att));
					if ($qattc->num_rows() > 0) {
						$arr_att_comp = array();	// For Deleting Existing Score Purpose
						foreach ($qattc->result() as $rac) {
							array_push($arr_att_comp, $rac->uc);			
						}

						$this->score_m->delete_where_in($arr_att_comp);
					}

                    // Delete old attempt comp
                    $this->exam_attempt_competency_m->delete_data(array('uc_exam_attempt' => $uc_att));


                    // Regenerate Key
                    $keys = "";

                    // Get array question
                    $arr_quest = explode(',', $att->questions);
                    // echo "<br /> -----------";
                    // echo "<br /> QUE : ".count($arr_quest);

                    $question = "";
                    foreach ($arr_quest as $rq) {
                    	$question .= "'".$rq."',";
                    }
                    $question = substr_replace($question, '', -1);

                    // Generate key
					$q_quest = $this->exam_question_m->get_question_in($question, $uc_exam);
					// echo "<br /> NU : ".$q_quest->num_rows();
                    if ($q_quest->num_rows() > 0) {

                        foreach ($q_quest->result() as $rk) {
                        	if ($rk->question_type == 1) {
                                $keys .= $rk->eo_uc.",";
                            }
                            else if ($rk->question_type == 2) {
                                $keys .= $rk->answer_truefalse.",";
                            }
                            else {
                                $keys .= NULL.",";
                            }
                        }
                        $keys = substr_replace($keys, '', -1);
                    }

                    // Update key proccess
                    $this->exam_attempt_m->update_data(array('keys' => $keys), array('uc' => $uc_att));

                    // ReInsert Attempt comp (score)
                    $answer_arr = explode(',', $att->answers);
                    $key_arr    = explode(',', $keys);
                    $competency = explode(',', $att->competency);

                    // Prop for insert exam_attempt
                    $z = 0;
                    $sc                 = array();
                    $result_arr         = array();

                    // Prop for insert exam_attempt_comp
                    $curr_comp = 0;
                    $uc_competency = "";

                    // echo "<br /> ANS : ".count($answer_arr);
                    // echo "<br /> KEY : ".count($key_arr);
                    // echo "<br /> COM : ".count($competency);

					$c_true = 0;
                    foreach ($answer_arr as $aa){
                    	// echo "<br /> - ".$answer_arr[$z]." == ".$key_arr[$z];
                        // BEGIN Count the score and result answer
                        if ($answer_arr[$z] == $key_arr[$z]) {
                        	// echo " --> T";
                            array_push($result_arr, "T");
                            $c_true ++;
                        }
                        elseif ($answer_arr[$z] == "0") {
                            array_push($result_arr, "-");
                        }
                        else {
                            array_push($result_arr, "F");
                        }
                        // END Count the score and result answer

                        // BEGIN Of Get for insert_exam_attempt_competency
                        // Get all competency
                        if ($curr_comp != $competency[$z]) {
                            $i = 0;

                            $uc_competency .= "".$competency[$z].",";

                            $curr_comp = $competency[$z];

                            $i++;
                        } else {
                            $i++;

                            // count quest per competency
                            $num_quest_comp[$competency[$z]] = $i;
                        }
                        // END Of Get for insert_exam_attempt_competency

                        $z++;
                    }
                    //echo "<br /> --------> ".count($arr_quest)."TRUE : ".$c_true;

                    // Finishing get all uc competency
                    $uc_competency = substr_replace($uc_competency, "", -1);

                    // BEGIN Of Update exam_attempt
                    $a_result = implode(',', $result_arr);

                    /// updating answer result & is_done status 
                    $data = array(
                                    'answer_result' => $a_result,
                                    'is_done' => 1
                                );
                    $filter = array('uc' => $uc_att);
                    $this->exam_attempt_m->update_data($data, $filter);
                    // END Of Update exam_attempt

                    // Prop for get answer and key the attempted
                    $res_ans = array();
                    $res_key = array();
                    $z = 0;
                    foreach ($answer_arr as $ra){
                        // Get answer
                        $res_ans[$competency[$z]][$z] = $ra;

                        // Get key
                        $res_key[$competency[$z]][$z] = $key_arr[$z];

                        $z++;
                    }

                    // BEGIN Of inserting score per competency AND score to temp
                    $this->reg_attempt_competency($uc_period, $att, $uc_att, $uc_competency, $num_quest_comp, $res_ans, $res_key);

                    //	INSERT Score from Temp to Real
                    $this->reg_score_temp_to_real();

                    // UPDATE Status Period
                    $this->reg_status_period();

                    // UPDATE Status
                    $this->reg_status();
                }
            }
        }

        $this->db->truncate('tech_score_temp');

        redirect('report/recap/'.$uc_period.'/adjust');  
	}

	function reg_status(){
		//	Insert or Update Status		
		$query = $this->score_m->get_on_status();

		if ($query->num_rows() > 0) {
			$this->load->model('status_m');

			$i = 0;
			//	Inisialisasi variable untuk data yg belum ada di status
			$field_status	= "(`uc`, `uc_competency`, `pra_pasca`, `seafarer_code`, `is_pass`, `score_max`, `uc_score`, `status`)";
			$value_status 	= NULL;
			
			foreach ($query->result() as $sts) {
				if ($sts->score_max != NULL) {
					//	Sudah ada (Sudah pernah ujian)
					if (decryptIt($sts->score_normal) > decryptIt($sts->score_max)) {
						//	Jika nilai ujian terbaru nilainya lebih besar dr sebelumnya
						$is_pass = (decryptIt($sts->score_normal) >= 70 ? 1 : 0);
						$status = ($is_pass == 1 ? "SL" : "BL");

						$data  = array(
										'is_pass' 	=> $is_pass,
										'score_max' => $sts->score_normal,
										'uc_score'	=> $sts->uc,
										'status'	=> $status
									);

						$where = array('uc_competency' => $sts->uc_competency, 'seafarer_code' => $sts->seafarer_code);

			 			$this->status_m->update_data($data,$where);
					}
				}
				else {
					///	Genereate UC Status
					$fro = $sts->pra_pasca.substr($sts->uc_competency, 0, 2);
					$mid = substr($sts->seafarer_code, -5);
					$end = substr($sts->uc_competency, -5);
					$uc = $fro."-".$mid."-".$end;

					//	Belum ada (Belum pernah ujian)
					$is_pass = (decryptIt($sts->score_normal) >= 70 ? 1 : 0);
					$status = ($is_pass == 1 ? "SL" : "BL");

					$value_status .= "('".$uc."','".$sts->uc_competency."','".$sts->pra_pasca."','".$sts->seafarer_code."','".$is_pass."','".$sts->score_normal."','".$sts->uc."', '".$status."'),";

					if (($i%50) == 0) {
						$value_status = substr_replace($value_status, '', -1);
						$this->status_m->insert_multi_value($field_status, $value_status);
						
						$value_status = "";
					}

					$i++;
				}
			}

			if ($value_status != "") {
				$value_status = substr_replace($value_status, '', -1);
				$this->status_m->insert_multi_value($field_status,$value_status);	
			}
		}
	}

	function reg_status_period(){
		$this->load->model('status_period_m');

		//	Insert New OR Update Prev Status Period
		$query = $this->status_period_m->get_compare_with_score();
		if ($query->num_rows() > 0) {
			$i = 1;
			$value = "";
			$field = "(`uc`, `uc_period`, `uc_competency`, `diklat_type`, `uc_diklat_participant`,`seafarer_code`, `is_pass`, `score`, `uc_score`, `status`)";
			
			foreach ($query->result() as $res) {
				//echo "<br /> - ".$res->seafarer_code." - ".$res->uc_competency." - ".$res->status;
				$is_pass = (decryptIt($res->score_normal) >= 70 ? 1 : 0);
				$staper = ($is_pass == 1 ? "L" : "BL");

				if ($res->status != NULL) {
					//	Update
					$where = array('uc' => $res->uc_status_period);

					$data_update = array(
										'is_pass' 	=> $is_pass,
										'score'  	=> $res->score_normal,
										'uc_score'	=> $res->uc,
										'status' 	=> $staper
										);

					$this->status_period_m->update_data($data_update, $where);
				}
				else {
					//	Insert New
					$value .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_competency."', '".$res->pra_pasca."', '".$res->uc_diklat_participant."','".$res->seafarer_code."', '".$is_pass."', '".$res->score_normal."', '".$res->uc."', '".$staper."'),";

					if (($i%50) == 0) {
						$value = substr_replace($value, '', -1);
						$this->status_period_m->insert_multi_value($field, $value);
						
						$value = "";
					}	
				}

				$i++;					
			}

			if ($value != "") {
				$value = substr_replace($value, '', -1);
				$this->status_period_m->insert_multi_value($field, $value);
			}
		}

		$this->db->truncate('tech_status_period_temp');
		//	END Insert OR Update STATUS PERIOD
	}

	function reg_attempt_competency($uc_period, $att, $uc_att, $uc_competency, $num_quest_comp, $res_ans, $res_key) {
		$this->db->truncate('tech_score_temp');

		$this->load->model('exam_attempt_competency_m');
		$this->load->model('score_temp_m');

		foreach (explode(',', $uc_competency) as $comp) {
            /* BEGIN Of Counting per competency */
            $comp_true = 0;
            $comp_false = 0;
           
            foreach ($res_ans[$comp] as $k_ra => $ra) {
                if ($ra == $res_key[$comp][$k_ra]) {
                    $comp_true++;
                }
                else if ($ra == "0") {
                    // Condition when answer is IDK, and do nothing
                }
                else {
                    $comp_false++;
                }
            }
            //  FINAL SCORING
            /// MODE : True (+1), False (0)
            $total_score_comp_normal = value_format((($comp_true / $num_quest_comp[$comp]) * 100), ',', '.', 2);

            // ROUNDING SCORE VALUE
            $total_score_comp_normal = $this->score_round($total_score_comp_normal);
            //echo "<br /> - SCORE : ".$total_score_comp_normal;

            // Encrypt score
            $score_normal = encryptIt($total_score_comp_normal);

            // CHECK INSERT DOUBLE
            $uc_att_comp =  unique_code();                            
            $data = array(
                        'uc'                => $uc_att_comp,
                        'uc_exam_attempt'   => $uc_att,
                        'uc_competency'     => $comp,
                        'seafarer_code'     => $att->seafarer_code,
                        'score_normal'      => $score_normal
            );
            
            // BEGIN Of INSERT per competency proccesss
            // Start check state INSERT
            $this->db->trans_start();
            // Start INSERT data
            $this->exam_attempt_competency_m->insert_data($data);
            // Finishing check state INSERT
            $this->db->trans_complete();

            // Condition when data failed to insert                   
            if ($this->db->trans_status() == FALSE) {
                $i++;

                if ($i < 2) {
                    $this->scoring($uc_att, $i);
                }
            }
            // END Of INSERT per competency proccesss

            //	INSERT SCORE TEMP
            //$this->reg_score_temp($uc_att_comp, $att, $uc_period, $comp, $score_normal);
        }
	}

	function reg_score_temp_to_real() {
		//	Temp to Real
		$query = $this->score_m->temp_not_in_real();

		if ($query->num_rows() > 0) {

			$i = 1;
			$value_score = "";
			$field_score 	= "(`uc`, `uc_period`, `uc_upt`, `uc_competency`, `uc_eac`, `pra_pasca`, `uc_diklat_participant`,`seafarer_code`, `score_normal`)";

			foreach ($query->result() as $res) {
				$value_score .= "('".$res->uc."', '".$res->uc_period."', '".$res->uc_upt."', '".$res->uc_competency."', '".$res->uc_eac."', '".$res->pra_pasca."', '".$res->uc_diklat_participant."','".$res->seafarer_code."', '".$res->score_normal."'),";

				if (($i%50) == 0) {
					$value_score = substr_replace($value, '', -1);
					$this->score_m->insert_multi_value($field_score, $value);
					
					$value = "";
				}

				$i++;
			}

			if ($value_score != "") {
				$value_score = substr_replace($value_score, '', -1);
				$this->score_m->insert_multi_value($field_score, $value_score);
			}
		}		
	}
	
	function reg_score_temp($uc_att_comp, $att, $uc_period, $comp, $score_normal){	
		//	Delete Score by uc_attempt			
		$value = array(

						'uc'					=> $uc_att_comp,
						'uc_period'				=> $uc_period,
						'uc_upt'				=> $att->uc_upt,
						'uc_competency'			=> $comp,	
						'uc_eac'				=> $uc_att_comp,
						'pra_pasca'				=> $att->pra_pasca,
						'uc_diklat_participant'	=> $att->uc_diklat_participant,
						'seafarer_code'			=> $att->seafarer_code,
						'score_normal'			=> $score_normal
					);
		
		$this->score_temp_m->insert_data($value);
	}

	function score_round($total_score_comp_normal){		
        if ($total_score_comp_normal >= 96) {
            $total_score_comp_normal = 100;
        }
        elseif ($total_score_comp_normal >= 91) {
            $total_score_comp_normal = 95;
        }
        elseif ($total_score_comp_normal >= 86) {
            $total_score_comp_normal = 90;
        }
        elseif ($total_score_comp_normal >= 81) {
            $total_score_comp_normal = 85;
        }
        elseif ($total_score_comp_normal >= 76) {
            $total_score_comp_normal = 80;
        }
        elseif ($total_score_comp_normal >= 71) {
            $total_score_comp_normal = 75;
        }
        elseif ($total_score_comp_normal >= 66) {
            $total_score_comp_normal = 70;
        }
        elseif ($total_score_comp_normal >= 61) {
            $total_score_comp_normal = 65;
        }
        elseif ($total_score_comp_normal >= 56) {
            $total_score_comp_normal = 60;
        }
        elseif ($total_score_comp_normal >= 51) {
            $total_score_comp_normal = 55;
        }
        elseif ($total_score_comp_normal >= 46) {
            $total_score_comp_normal = 50;
        }
        elseif ($total_score_comp_normal >= 41) {
            $total_score_comp_normal = 45;
        }
        elseif ($total_score_comp_normal >= 36) {
            $total_score_comp_normal = 40;
        }
        elseif ($total_score_comp_normal >= 31) {
            $total_score_comp_normal = 35;
        }
        elseif ($total_score_comp_normal >= 26) {
            $total_score_comp_normal = 30;
        }
        elseif ($total_score_comp_normal >= 21) {
            $total_score_comp_normal = 25;
        }
        elseif ($total_score_comp_normal >= 16) {
            $total_score_comp_normal = 20;
        }
        elseif ($total_score_comp_normal >= 11) {
            $total_score_comp_normal = 15;
        }
        elseif ($total_score_comp_normal >= 6) {
            $total_score_comp_normal = 10;
        }
        elseif ($total_score_comp_normal >= 1) {
            $total_score_comp_normal = 5;
        }
        elseif ($total_score_comp_normal < 1) {
            $total_score_comp_normal = 0;
        }

        return $total_score_comp_normal;
	}

	function clear_participant() {
		if ($this->input->post('f_clear')) {
			if(!is_null($this->input->post('f_seaf_del'))) {
				//	Collect Seafarers
				$seafs = NULL;
				foreach ($this->input->post('f_seaf_del') as $sd) {
					$seafs .= "'".$sd."',";
				}

				if ($seafs != NULL) {
					$seafs = substr_replace($seafs, "", -1);
				}

				//	Delete Attempt
				$this->load->model('exam_attempt_competency_m');
				$this->exam_attempt_competency_m->delete_in('seafarer_code', $seafs);

				//	Delete Attempt Score
				$this->load->model('exam_attempt_m');
				$this->exam_attempt_m->delete_in('seafarer_code', $seafs);

				//	Delete (Exam) Participant
				$this->load->model('participant_m');
				$this->participant_m->delete_in('seafarer_code', $seafs);

				//	Delete Period Participant
				$this->load->model('period_participant_m');
				$this->period_participant_m->delete_in('seafarer_code', $seafs);

			}
			
		}

		redirect('report/recap/'.$this->input->post('f_uc_period').'/adjust');
	}
}
?>