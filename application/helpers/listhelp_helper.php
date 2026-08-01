<?php
function list_level($filter = NULL){
	$CI =& get_instance();
	$CI->load->model('level_m');
	
	if ($filter != NULL) {		
		$query = $CI->level_m->get_filtered($filter);
	}
	else {
		$query = $CI->level_m->get_all('label','ASC');	
	}
	
	if ($query->num_rows() > 0) {
		return $query->result();
	}
	else {
		return NULL;
	}
}

function list_function($filter = NULL){
	$CI =& get_instance();
	$CI->load->model('function_m');
	
	if ($filter != NULL) {		
		$query = $CI->function_m->get_filtered($filter,'label','ASC');
	}
	else {
		$query = $CI->function_m->get_all('label','ASC');	
	}
	
	if ($query->num_rows() > 0) {
		return $query->result();
	}
	else {
		return NULL;
	}
}

function list_competency($filter = NULL){
	$CI =& get_instance();
	$CI->load->model('competency_m');
	
	if ($filter != NULL) {		
		$query = $CI->competency_m->get_filtered($filter);
	}
	else {
		$query = $CI->competency_m->get_all('sequence','ASC');	
	}
	
	if ($query->num_rows() > 0) {
		return $query->result();
	}
	else {
		return NULL;
	}
}

function read_title($string){
	$string = htmlspecialchars_decode(stripslashes(mb_convert_encoding($string,"HTML-ENTITIES","UTF-8")));

	return $string;
}

function read_text($string){
	$string = htmlspecialchars_decode(stripslashes($string));

	return $string;
}


function list_pukp($filter = NULL){
	$CI =& get_instance();
	$CI->load->model('pukp_m');
	
	if ($filter != NULL) {		
		$query = $CI->pukp_m->get_filtered($filter, 'pukp_label','ASC');
	}
	else {
		$query = $CI->pukp_m->get_all('pukp_label','ASC');	
	}
	
	if ($query->num_rows() > 0) {
		return $query->result();
	}
	else {
		return NULL;
	}
}

function list_upt($filter = NULL){
	$CI =& get_instance();
	$CI->load->model('upt_m');
	
	if ($filter != NULL) {		
		$query = $CI->upt_m->get_filtered($filter, 'upt_label', 'ASC');
	}
	else {
		$query = $CI->upt_m->get_all('upt_label','ASC');	
	}
	
	if ($query->num_rows() > 0) {
		return $query->result();
	}
	else {
		return NULL;
	}
}


?>