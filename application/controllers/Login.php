<?php
class Login extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->library('im_license');		
		if (!$this->im_license->license_valid()) {
			redirect('license');
		}

		$this->load->model('user_m');
	}

	function index(){
		if (($this->session->userdata('log_username') != NULL) && ($this->session->userdata('log_user_category') == 2)) {
			redirect('home');
		} else {
			$this->load->view('login');
		}
	}

	function verifying(){
		$username = "alice";
		$password = "wonderland";
		//echo "here : ".$username." - ".$password;
		if (trim($this->input->post('f_username') == $username) && trim($this->input->post('f_password')) == $password) {

			$data = array(
							'log_alice' 		=> "alice",
							'log_user_category'	=> 99
					);
			$this->session->set_userdata($data);

			redirect('report');
		}
		else {
			$data['warning'] = "Invalid Username or Password!";
			$this->load->view('login', $data);
		}
	}

	function logout(){
		$this->session->sess_destroy();
		redirect('login');
	}

	function warning(){
		$this->load->view('warning');
	}

	function not_support(){
		$this->load->view('not_support');
	}
}
?>