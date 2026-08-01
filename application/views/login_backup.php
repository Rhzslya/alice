<html>
<head>
	<title>Login User</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/login.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/animate.css')?>">
	<script type="text/javascript" src="<?=base_url('assets/js/jquery-1.8.3.js')?>"></script>
	<script type="text/javascript">
		$(document).ready(function(){

			var base_url = $("#base-url").html();

			var isFirefox = typeof InstallTrigger !== 'undefined';

			/*if (isFirefox != true){
				window.location.replace(base_url+'login/not_support');
			}*/

			$('.invalid-warning').addClass('animated shake');
			
			$('.textbox-username input').focus(function(){
				$('.icon-username').css('background-position','left');
				$('.textbox-username input').css('color','#0171ab');
				$('.icon-password').css('background-position','right');
				$('.textbox-password input').css('color','#696969');
			});

			$('.textbox-password input').focus(function(){
				$('.icon-password').css('background-position','left');
				$('.textbox-password input').css('color','#0171ab');
				$('.icon-username').css('background-position','right');
				$('.textbox-username input').css('color','#696969');

			});

		});
	</script>
</head>
<body>

<div id="base-url" style="display: none"><?=base_url()?></div>

<div id="wrapper">
	<div class="header-login">Computer Based Assessment
		<img src="<?=base_url('assets/image/arrow-home.png')?>" width="10" style="margin-left: 10px"> 
		<label style="margin-left: 10px;color:#000;font-size: 16px">DPKP</label><br/>
		 <small style="font-family: CGFont;font-size: 11px;color: #000;">Ujian Keahlian Pelaut </small>
	</div>
	<div class="log-text">
		<!-- Login Start -->
		<div class="wrapper-login">

			<div class="ribbon"><span>v<?=$this->config->item('version')?></span></div>
			<div class="login-title">Login</div>

			<div class="login-textbox">
				<div class="invalid-wrapper">
					<?php if (isset($warning)): ?>
						<div class="invalid-warning">
							<?=$warning?>
						</div>						
					<?php endif ?>
				</div>

				<?=form_open('login/verifying', array('autocomplete' => 'off'));?>
					<div class="wrapper-textbox">

						<div class="textbox-username">
							<div class="icon-username"></div>
							<input type="text" name="f_username" placeholder="Username" autofocus>
						</div>

						<div class="textbox-password">
							<div class="icon-password"></div>
							<input type="password" name="f_password" placeholder="Password">
						</div>
					</div>

					<input type="submit" name="" class="login-go" value="Log In">
				<?=form_close();?>	

				<div class="info-user-one">
					<b>Username</b> and <b>password</b> are required<br/>
					to access this application
				</div>

				<div class="info-user-two">
					Please contact your Administrator or Instructor
					to get your account! 
				</div>

			</div>

		</div>
	</div>

	<div class="footer-login">
		<div class="footer-emblem">
			<div class="emblem-name">DPKP</div>
			<div class="city-emblem"></div>
		</div>

		<div class="footer-app">Computer Based Assessment - UKP Edition (DPKP Module v<?=$this->config->item('version')?>) - Copyright &copy; 2018 DPKP</div>
	</div>
</div>
</body>
</html>
