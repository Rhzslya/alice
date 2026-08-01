<script type="text/javascript" src="<?=base_url('assets/js/jquery.validate.min.js')?>"></script>
<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').html();

		$('#add-form input').focus(function(){
			$(this).next('span').remove();
		});


		// $('#add-form').validate({
  //           rules   : {
  //               f_seafarer_code     : { required : true },
  //           },
  //           messages : {
  //               f_seafarer_code     : { required : "<div class='valid-error'>Please input the Seafarer ID!!</div>" },
  //           }
  //       });

	});


</script>
<style type="text/css">
	.valid-error{
		background-color: #267fd9;
		color: #ffffff;
		padding: 5px 10px;
		border-radius: 4px;
		font-size: 8pt;		
		width: 210px;
		float: right;
		position: relative;
		right: 120px
	}

	

</style>
<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="<?=base_url('participant_master')?>" class="go-back">&nbsp;</a>
		<a href="<?=base_url('participant_master')?>" class="go-maspar">&nbsp;</a>
		<a href="<?=base_url('report')?>" class="go-report">&nbsp;</a>
		<!-- <a href="<?=base_url('error')?>" class="go-setting">&nbsp;</a>
		<a href="<?=base_url('error')?>" class="go-guide">&nbsp;</a> -->
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>

<div class="main-pane">
	<div class="title-menu">Participant Master Add</div>

	<div class="main-home">
		
		<div class="form-area">
		<?=form_open_multipart('participant_master/insert', array('id' => 'add-form'))?>
			<div class="form-adit">
				<div class="result-user ">
				
					<table class="form-frame" width="800" height="120">
						<tr>
							<td width="15%">Seafarer ID</td>							
							<td><input type="text" name="f_seafarer_code" required="true"></td>
						</tr>
						<tr>
							<td>Full Name</td>							
							<td><input type="text" name="f_full_name" required="true"></td>
						</tr>
						<tr>
							<td>Born Place</td>							
							<td><input type="text" name="f_born_place" required="true"></td>
						</tr>
						<tr>
							<td>Born Date</td>							
							<td><input type="text" name="f_born_date" class="datepicker" required="true"></td>
						</tr>
					</table>

				</div>
			</div>
			<div class="bottom-nav">
				<input type="submit" class=" ui-btn-default" value="Save" name="f_save">
			</div>
		<?=form_close()?>
		</div>
	</div>
</div>