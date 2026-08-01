<?php
Class Home extends CI_Controller{
	function __construct(){
		parent::__construct();

		$this->load->library('im_license');		
		if (!$this->im_license->license_valid()) {
			redirect('license');
		}
		
		if ((!$this->im_login->is_login('log_username')) || ($this->session->userdata('log_user_category') == 3)) {
			redirect('login');
		}

		// $this->load->helper('license');
		// if (lic_verification()) {
		// 	if ((!$this->im_login->is_login('log_username')) || ($this->session->userdata('log_user_category') != 2)) {
		// 		redirect('login');
		// 	}			
		// }
		// else {
		// 	redirect('license/non_license');
		// }
	}

	function index(){
		$this->im_render->main('home');		
	}

	function error404(){
		$this->im_render->main('under_construction');
	}
}