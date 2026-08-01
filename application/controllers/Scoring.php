<?php
class Scoring extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->library('im_license');		
		$this->load->model('setting_m');
		if (!$this->im_license->license_valid()) {
			redirect('license');
		}
		
		if ((!$this->im_login->is_login('log_username')) || ($this->session->userdata('log_user_category') == 3)) {
			redirect('login');
		}
	}

	/*function index() {
		$data="";

		$query = $this->setting_m->get_filter();
		if ($query->num_rows() > 0) {
			$data['row'] = $query->row();
		}

		$this->im_render->main('scoring/edit',$data);
	}


	function update(){
		if ($this->input->post('f_save')) {		
			$this->setting_m->update_mode($this->input->post('f_mode'),'scoring');

			redirect('home');			
		}
	}*/


}
?>