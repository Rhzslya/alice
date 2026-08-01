<?php
Class Lic extends CI_Controller{
	function __construct(){
		parent::__construct();
	}

	function index() {
		if (preg_match('#Volume Serial Number is (.*)\n#i', shell_exec('dir c:'), $m)) {
			$volname = $m[1];
		} else {
			$volname = '';
		}

		echo $volname;
	}
}
?>	