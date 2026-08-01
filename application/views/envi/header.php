<!DOCTYPE html>
<html lang="en">
<head>
	<title>CBA-UKP - Alice</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?=base_url('assets/third_party/bootstrap-4.5.0/css/bootstrap.min.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/fontawesome-5.13.0/css/all.css')?>">
	<link rel="stylesheet" href="<?=base_url('assets/css/allnew.css');?>">
	<script src="<?=base_url('assets/js/jquery-3.5.1.min.js')?>"></script>
	<script src="<?=base_url('assets/js/popper.min.js')?>"></script>
	<script src="<?=base_url('assets/js/jquery.validate.min.js')?>"></script>
	<script src="<?=base_url('assets/third_party/bootstrap-4.5.0/js/bootstrap.min.js')?>"></script>
	<script src="<?=base_url('assets/ckeditor/ckeditor.js')?>"></script>

</head>
<body>
<div id="base-url" style="display: none"><?=base_url()?></div>	

<nav class="navbar navbar-expand-sm navbar-dark nav-allnew" style="background-color: #ff7019">

	<a class="navbar-brand mr-auto" href="#">
		<h4>Alice <span class="badge badge-primary ml-3">1.0</span></h4>
	</a>
	
	<ul class="navbar-nav">
		<li class="nav-item">
			<a class="nav-link" href="<?=base_url('home')?>"><i class="fa fa-home"></i> Home</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="<?=base_url('report')?>"><i class="fa fa-clipboard"></i> Report</a>
		</li>
		<li class="nav-item ml-4">
			<a class="nav-link" href="<?=base_url('login/logout')?>"><i class="fa fa-power-off text-warning"></i></a>
		</li>
	</ul>
</nav>