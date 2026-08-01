<?php
	header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
	header('Pragma: no-cache'); // HTTP 1.0.
	header('Expires: 0'); // Proxies.
?>
<!DOCTYPE html>
<html>
<head>
	<title>CBA-UKP DPKP Edition</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/envi.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/home.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/contents.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/ly-tree.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/jquery-ui/jquery-ui.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/modal.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/button-animation.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/immu-ui/immu-ui.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/pop-up.css')?>">
	<link rel="stylesheet" href="<?=base_url('assets/third_party/maxazantree/css/jquery.treegrid.css')?>"> <!-- For Tree -->
	<script type="text/javascript" src="<?=base_url('assets/js/jquery-1.11.1.min.js');?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/third_party/jquery-ui/jquery-ui.js');?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/jquery.validate.min.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/ckeditor/ckeditor.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/third_party/jquery-ui/jquery-timepicker.js');?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/third_party/maxazantree/js/jquery.treegrid.js')?>"></script> <!-- For Tree -->
	<script type="text/javascript" src="<?=base_url('assets/js/action.js')?>"></script>
	<script type="text/javascript" src="<?=base_url('assets/js/loader.js');?>"></script>
	
</head>

<script type="text/javascript">
	$(document).ready(function() {

		var base_url = $("#base-url").html();

		var isFirefox = typeof InstallTrigger !== 'undefined';

		/*if (isFirefox != true){
			window.location.replace(base_url+'login/not_support');
		}*/

		$('.datepicker').datepicker({
	        changeMonth: true,
	        changeYear: true,
	        showButtonPanel: true,
	        yearRange: "-40:+0"
	        // onClose: function(dateText, inst) { 
	        //     $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 40));
	        // }
		});

		// $('.datepicker').datepicker();
		$('.timepicker').timepicker();
		$('.datetimepicker').datetimepicker();

		// For tree
		$('.tree').treegrid({
			'initialState': 'collapsed'
		});
	});
</script>

<body>
<div id="base-url" style="display: none"><?=base_url()?></div>

<div id="wrapper">
	<div class="main-header">
		<div class="emblem-top">
			<div class="proj-top" style="padding-top: 0px;height: 31px">Computer Based Assessment
				<img src="<?=base_url('assets/image/arrow-home.png')?>" width="10"> 
				<label>DPKP</label>
				 <small style="font-family: CGFont;font-size: 11px;color: #000;">Ujian Keahlian Pelaut </small>
			</div>

			<div class="select-sys">
				<!-- <span><?=label('lang')?></span>
				<div class="select-lang">

					
	
				</div> -->

				<div id="ribbon">v<?=$this->config->item('version')?></div>
			</div>			
			
		</div>
		<!-- <div class="time-clock"><span id="clock"><?php print date('H:i'); ?></span></div>
		<div class="time-date"><?php print date('d M Y')?></div> -->
		
		<a href="<?=base_url('login/logout')?>" class="menu-logout">&nbsp;</a>
		<div class="account">
			<div class="account-data-btn"><a href="<?=base_url('user/edit')?>">&nbsp;</a></div>
			<div class="account-data">
				<div class="acc-username"><?=$this->session->userdata('log_user_fullname');?></div>
				<div class="acc-id"><?=$this->session->userdata('log_user_id_number');?></div>
			</div>
			<?php if($this->session->log_user_photo != NULL):?>
				<img src="<?=base_url('uploads/user/'.$this->session->userdata('log_user_photo'))?>" width="50" height="50"  class="account-img">
			<?php else:?>
				<img src="<?=base_url('assets/image/user-photo.jpg')?>" width="50" height="50"  class="account-img">
			<?php endif;?>

				<!-- <img src="<?=base_url('assets/image/new-index/user-photo.jpg')?>" > -->
		</div>
	</div>	

	<div class="main-content">