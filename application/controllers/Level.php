<?php
Class Level extends CI_Controller{
	function __construct(){
		parent::__construct();

		$this->load->library('im_license');		
		if (!$this->im_license->license_valid()) {
			redirect('license');
		}
		
		if ((!$this->im_login->is_login('log_username')) || ($this->session->userdata('log_user_category') == 3)) {
			redirect('login');
		}

		$this->each_page 	= 10;
		$this->page_int 	= 10;

		$this->load->model('level_m');
	}


	function index(){
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
							'segment' 		=> 'Level',						
							'model'			=> 'level_m'
						);


		$query = $this->level_m->get_all('label','ASC',$this->each_page, $offset);
		if ($query->num_rows() > 0) {
			$data['result'] 	= $query->result();
		}

		$query = $this->level_m->get_all();
		if ($query->num_rows() > 0) {
			$params['total_record'] = $query->num_rows();
			$data['pagination'] 	= $this->im_pagination->render_ajax($params);
			$data['total_record'] 	= $query->num_rows();
		}

		$data['numbering'] 	= ($this->each_page * ($page-1)) + 1;
        $data['msg'] = $this->session->flashdata('msg');

		$this->im_render->main('level/list',$data);
	}

	function page(){
		$data = "";
		$page = ($this->input->post('js_page') != 1 ? $this->input->post('js_page') : 1);
		//	Pagination Initialization
		$this->load->library('im_pagination');
		///	Define Offset
		$offset = ($page - 1) * $this->each_page;
		//	Define Parameters
		$params = array(
							'page_number'	=> $page,
							'each_page'		=> $this->each_page,
							'page_int'		=> $this->page_int,	
							'segment' 		=> 'Level',						
							'model'			=> 'level_m'
						);


		$query = $this->level_m->get_all('label','ASC',$this->each_page, $offset);
		if ($query->num_rows() > 0) {
			$data['result'] 	= $query->result();
		}

		$query = $this->level_m->get_all();
		if ($query->num_rows() > 0) {
			$params['total_record'] = $query->num_rows();
			$data['pagination'] 	= $this->im_pagination->render_ajax($params);
			$data['total_record'] 	= $query->num_rows();
		}

		$data['numbering'] 	= ($this->each_page * ($page-1)) + 1;
		$this->load->view('level/page',$data);
	}

	/*function add(){
		$this->load->view('level/add');
	}*/

	/*function insert(){
		if ($this->input->post('f_save')) {
			$data = array(
						'label'		=> $this->input->post('f_label'),
						'uc'		=> unique_code()
					);

			$this->level_m->insert_data($data);
		}

		redirect('level');
	
	}*/

	/*function edit(){
		$uc = $this->input->post('js_uc');

		$query = $this->level_m->get_filtered(array('uc' => $uc));
		if ($query->num_rows() > 0) {
			$data['row'] = $query->row();
		}

		$this->load->view('level/edit',$data);
	}*/

	/*function update(){
		if ($this->input->post('f_save')) {
			$data = array(
						'label'		=> $this->input->post('f_label')
					);
			$where = array('uc' => $this->input->post('f_uc'));
			$this->level_m->update_data($data,$where);
		}

		redirect('level');
	}*/

	/*function delete($uc = NULL){
		if ($uc != NULL) {
			$this->level_m->delete_data(array('uc' => $uc));
			redirect('level');
		}else{
			redirect('level');
		}
	}*/

    function form_import()
    {
        $this->load->view('level/form_import');
    }

    function import_process()
    {
        if ($this->input->post('f_upload'))
        {
            $config['upload_path']          = './exim/';
            $config['allowed_types']        = 'lfc';
            $config['max_size']             = 20000;
            $config['overwrite']            = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ( ! $this->upload->do_upload('f_file'))
            {
                $msg = $this->upload->display_errors();

                $this->session->set_flashdata('msg', $msg);
            }
            else
            {
                $upload_data    = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
                $file_name      = $upload_data['file_name'];

                $this->do_upload($file_name);

                $msg = "Import Level success.";

                $this->session->set_flashdata('msg', $msg);
            }
        }

       redirect('level');
    }

    function do_upload($file_name)
    {
        $this->load->library('encrypt');

        //  Insert to Temp Table
        $templine = '';
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
                $templine = '';
            }
        }

        // TEMP TO REAL - Level
            // Get in Temp
            $query = $this->level_m->temp_not_in_real();
            if ($query->num_rows() > 0)
            {
                $value = NULL;
                $field = "(`label`, `uc`)";

                foreach ($query->result() as $res)
                {
                    $value .= "('".$res->label."', '".$res->uc."'), ";
                }
                $value = substr_replace($value, '', -2);

                //  Insert to Real Table
                $this->level_m->insert_multi_value($field, $value);
            }

            /// Truncate Temp Table
            $this->db->truncate('tech_level_temp');

        //Delete File Update
        unlink("./exim/".$file_name);
    }


}