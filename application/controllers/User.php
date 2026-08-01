<?php
Class User extends CI_Controller{
	function __construct(){
		parent::__construct();

		$this->load->library('im_license');		
		if (!$this->im_license->license_valid()) {
			redirect('license');
		}
		
		if ((!$this->im_login->is_login('log_username')) || ($this->session->userdata('log_user_category') == 3)) {
			redirect('login');
		}

		$this->each_page 	= 2;
		$this->page_int 	= 10;

		$this->load->model('user_m');
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
							'segment' 		=> 'user',						
							'model'			=> 'user_m'
						);


		$query = $this->user_m->get_list($this->each_page,$offset);
		if ($query->num_rows() > 0) {
			$data['result'] = $query->result();
		}

		$query = $this->user_m->get_list();
		if ($query->num_rows() > 0) {
			$params['total_record'] = $query->num_rows();
			$data['pagination'] 	= $this->im_pagination->render_ajax($params);
			$data['total_record'] 	= $query->num_rows();
		}

		$data['msg'] = $this->session->flashdata('msg');

		$data['numbering'] 	= ($this->each_page * ($page-1)) + 1;

		$this->im_render->main('user/list',$data);
	}

	function page(){
		$data = "";
		$page 	= ($this->input->post('js_page') != 1 ? $this->input->post('js_page') : 1);
		
		//	Pagination Initialization
		$this->load->library('im_pagination');
		///	Define Offset
		$offset = ($page - 1) * $this->each_page;
		//	Define Parameters
		$params = array(
							'page_number'	=> $page,
							'each_page'		=> $this->each_page,
							'page_int'		=> $this->page_int,	
							'segment' 		=> 'user',						
							'model'			=> 'user_m'
						);


		$query = $this->user_m->get_list($this->each_page,$offset);
		if ($query->num_rows() > 0) {
			$data['result'] = $query->result();
		}

		$query = $this->user_m->get_list();
		if ($query->num_rows() > 0) {
			$params['total_record'] = $query->num_rows();
			$data['pagination'] 	= $this->im_pagination->render_ajax($params);
			$data['total_record'] 	= $query->num_rows();
		}

		$data['numbering'] 	= ($this->each_page * ($page-1)) + 1;

		$this->load->view('user/page',$data);
	}

	function add(){
		$this->im_render->main('user/add');
	}

	function insert(){
		if ($this->input->post('f_save')) {
			$unique_code = unique_code();

			$username = $this->input->post('f_username');

			$this->load->model('user_m');
			$query = $this->user_m->get_filtered(array('username' => $username));
			if ($query->num_rows() > 0) {
				
				if ($this->session->userdata('session_lang') == 'english') {
					$msg = "Username is already exist!";
				}else{
					$msg = "Nama Pengguna sudah ada!";
				}

				$this->session->set_flashdata('msg', $msg);		
				
			}
			else{
				//	Upload photo
				$image_file = "";	
				$thumb_file = "";	
				if(preg_match('/image/', $_FILES['f_photo']['type'])){
					$this->load->library('im_upload');
					
					$image_file = $this->im_upload->uploading('f_photo', 'user');

					///	create thumbnail if upload succeed
					$this->load->helper('thumbnail');		
					image_thumb('uploads/user/', $image_file, 85, 85);
					
					$path_parts = pathinfo($image_file);
					    
				    $thumb_file = $path_parts['filename'].'_thumb'.'.'.$path_parts['extension'];
				}		
				
				//	Set value
				$data = array(
								'id_number'			=> $this->input->post('f_id_number'),
								'username'			=> $username,
								'password'			=> md5($this->input->post('f_password')),
								'full_name'			=> $this->input->post('f_full_name'),
								'category'			=> $this->input->post('f_user_type'),
								'photo_small'		=> $thumb_file,
								'photo'				=> $image_file
							);
							
				$this->user_m->insert_data($data);
			}
		}

		redirect('user');			
	}

	function edit($id = 0){
		if ($id != 0) {
			$query = $this->user_m->get_filtered(array('id' => $id));
			if ($query->num_rows() > 0) {
				$data['row'] = $query->row();
			}
			
			$this->im_render->main('user/edit',$data);
			
		}else{
			redirect('user');
		}
	}

	function update(){
		if ($this->input->post('f_save')) {
			$unique_code = unique_code();

			$image_file = $this->input->post('f_old_photo');		
			$thumb_file = $this->input->post('f_old_photo_small');
					
			if(preg_match('/image/', $_FILES['f_photo']['type'])){
				$this->load->library('im_upload');
				
				if (isset($_POST['f_old_photo'])) {
					//	replace old file if 'new image' not empty					
					if($_FILES['f_photo']['type'] != NULL){
						$image_file = $this->im_upload->replacing($this->input->post('f_old_photo'), 'f_photo', 'user');

						///	create thumbnail if upload succeed
						$this->load->helper('thumbnail');		
						image_thumb('uploads/user/', $image_file, 85, 85);
						
						$path_parts = pathinfo($image_file);
						    
					    $thumb_file = $path_parts['filename'].'_thumb'.'.'.$path_parts['extension'];	
					}			
				}
				else {
					//	if no old file, just upload it
					$image_file = $this->im_upload->uploading('f_photo', 'user');
					///	create thumbnail if upload succeed
					$this->load->helper('thumbnail');		
					image_thumb('uploads/user/', $image_file, 85, 85);
					
					$path_parts = pathinfo($image_file);
					    
				    $thumb_file = $path_parts['filename'].'_thumb'.'.'.$path_parts['extension'];				
				}
			}

			$data = array(
							// 'id_number'			=> $this->input->post('f_id_number'),
							'full_name'			=> $this->input->post('f_full_name'),
							'photo_small'		=> $thumb_file,
							'photo'				=> $image_file
						);

			$where = array('id' => $this->input->post('f_id'));


			$this->user_m->update_data($data, $where);
		}

		redirect('user');
	}	

	function detail(){
		$id = $this->input->post('js_id');

		if ($id != NULL) {
			$data = "";

			$query = $this->user_m->get_filtered(array('id' => $id));
			if ($query->num_rows() > 0) {
				$data['row'] = $query->row();
			}

			$this->load->view('user/detail',$data);
		}
	}

	function delete($id = 0){
		if ($id != 0) {
			$query = $this->user_m->get_filtered(array('id' => $id));
			if ($query->num_rows() > 0) {
				$row = $query->row();
				
			}

			$this->load->library('im_upload');
			
			$this->im_upload->deleting($row->photo,'user');
			$this->im_upload->deleting($row->photo_small,'user');

			$this->user_m->delete_data(array('id' => $id));
			
			redirect('user');
		}else{
			redirect('user');
		}
	}

	function change_password($id = NULL) {
		if ($id != NULL) {
			
			$data = "";

			$query = $this->user_m->get_filtered(array('id' => $id));
			if ($query->num_rows() > 0) {
				$data['row'] = $query->row();
			}

			$data['msg'] = $this->session->flashdata('msg');

			$this->im_render->main('user/change_password', $data);

		} else {
			redirect('user');
		}
	}

	function update_password(){
		if ($this->input->post('f_save')) {

			$id = $this->input->post('f_id');

			$row = $this->user_m->get_filtered(array('id' => $id))->row();
			$data['row'] = $row;
			
			///	compore old password
			if (md5($this->input->post('fOldPassword')) == $row->password ) {

				if ($this->input->post('fNewPassword') == $this->input->post('fRetypePassword2')) {

					$data = array('password' => md5($this->input->post('fNewPassword')));
					$filter = array('id' => $this->session->userdata('log_user_id'));
					
					// Update Proccess
					$this->user_m->update_data($data, $filter);

					// Give alert
					$this->session->set_flashdata("msg", "Change password success...");

					redirect('user');

				} else {

					// Give alert
					$this->session->set_flashdata("msg", "New password doesn't match!");

					redirect('user/change_password/'.$row->id);

				}

			} else {

				// Give alert
				$this->session->set_flashdata("msg", "Old password is missmatch with existing!");

				redirect('user/change_password/'.$row->id);

			}

		} else {
			redirect('user');
		}
	}


}