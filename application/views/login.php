<!DOCTYPE html>
<html lang="en">
<head>
	<title>CBA-UKP - Alice</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?=base_url('assets/third_party/bootstrap-4.5.0/css/bootstrap.min.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/fontawesome-5.13.0/css/all.css')?>">
	<link rel="stylesheet" href="<?=base_url('assets/css/allnew.css');?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/animate.css')?>">
	<script src="<?=base_url('assets/js/jquery-3.5.1.min.js')?>"></script>
	<script src="<?=base_url('assets/js/popper.min.js')?>"></script>
	<script src="<?=base_url('assets/third_party/bootstrap-4.5.0/js/bootstrap.min.js')?>"></script>
</head>

<body class="bg-light">

<div id="base-url" style="display: none"><?=base_url()?></div>


<div class="jumbotron jumbotron-fluid bg-dark text-white">
  <div class="container">
    <h1>Welcome</h1>      
    <h3>CBA UKP - Alice <span class="badge badge-danger ml-3"><?=$this->config->item('version')?></span></h3>
  </div>
</div>

<div class="container-fluid mb-3">	
	<div class="col-12 text-center">
		<?php if (isset($warning)): ?>
			<div class="badge badge-danger invalid-warning px-3 py-2"><?=$warning?></div>
		<?php else : ?>
			<div class="badge px-3 py-2">&nbsp;</div>
		<?php endif ?>
	</div>
</div>		

<div class="container-fluid">
	
	<div class="d-flex justify-content-center mb-3">		
		
		<?=form_open('login/verifying', array('autocomplete' => 'off'));?>
			<div class="input-group mb-2 ">
				<div class="input-group-prepend">
					<span class="input-group-text bg-secondary">
						<i class="fa fa-user-alt text-white small"></i>
					</span>
				</div>
				<input type="text" name="f_username" class="form-control small" placeholder="Username">
			</div>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text bg-secondary">
						<i class="fa fa-lock text-white small"></i>
					</span>
				</div>
				<input type="password" name="f_password" class="form-control" placeholder="Password">
			</div>
			<div class="input-group mb-2">
				<input type="submit" class="form-control btn-warning" name="f_login" value="Login" >
			</div>	
		<?=form_close()?>
	
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('input[name=f_username]').focus();

		$('.invalid-warning').addClass('animated shake');
	});

</script>
	
</body>
</html>