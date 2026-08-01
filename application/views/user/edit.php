<script type="text/javascript" src="<?=base_url('assets/js/jquery.validate.min.js')?>"></script>
<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').html();

		$('#add-form input').focus(function(){
			$(this).next('span').remove();
		});


		$('#add-form').validate({
            rules   : {
                f_id_number     : { required : true },
                f_full_name  : { required : true},
                f_username  : { required : true},
                f_password  : { required : true},
                f_retype : { equalTo : "input[name=f_password]" }

            },
            messages : {
                f_id_number     : { required : "<div class='valid-error'>ID Number Harap di isi  !</div>" },
                f_full_name  : { required : "<div class='valid-error' title='diisi'>Nama Lengkap Harap di isi  !</div>" },
                f_username  : { required : "<div class='valid-error'>Username Harap di isi  !</div>"},
                f_password  : { required : "<div class='valid-error'>Password Harap di isi  !</div>"},
                f_retype : { equalTo : "<div class='valid-error'>Password Anda tidak sesuai  !</div>" }


            }
        });

        $('input[name=f_id_number]').blur(function(){
			var js_number= $('input[name=f_id_number]').val();

			$.ajax({
					type	: 'post',
					dataType: 'json',
					url		: base_url+'manage/check_available/check_available_id',
					data    : {id_number : js_number},
					success	: function(output) {
								if (output == 1) {
									$('#save').attr('disabled','disabled');

									$('input[name=f_id_number]').after('<div class="valid-error">ID Number already exist!</div>');									
								}else {
									$('#save').removeAttr('disabled');
									$('.valid-error').remove();
								};
					}
			});
		});

		$('input[name=f_username]').blur(function(){
			var js_username= $('input[name=f_username]').val();

			$.ajax({
					type	: 'post',
					dataType: 'json',
					url		: base_url+'manage/check_available/check_available_username',
					data    : {username : js_username},
					success	: function(output) {
								if (output == 1) {
									$('#save').attr('disabled','disabled');
									$('input[name=f_username]').after('<div class="valid-error">Username already taken, choose another one!</div>');
								}else {
									$('#save').removeAttr('disabled');
									$('.valid-error').remove();
								};										
								
					}
			});
		});
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
		<a href="<?=base_url('user')?>" class="go-back">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>

<div class="main-pane">
	<div class="title-menu">User Edit</div>

	<div class="main-home">
		
		<div class="form-area">
		<?=form_open_multipart('user/update', array('id' => 'add-form'))?>
			<div class="form-adit">
				<input type="hidden" name="f_id" value="<?=$row->id?>">
				<input type="hidden" name="f_old_photo" value="<?=$row->photo?>" />
				<input type="hidden" name="f_old_photo_small" value="<?=$row->photo_small?>" />		
				<div class="result-user ">				
					<table class="form-frame" width="800" height="120">
						<tr>
							<td width="15%">ID Number</td>
							<td width="50%"><input type="text" value="<?=$row->id_number?>" name="f_id_number" size="30" disabled="TRUE"></td>
						</tr>
						<tr>
							<td>Username</td>
							<td><input type="text" name="f_username" value="<?=$row->username?>" disabled size="30"></td>
						</tr>
						<tr>
							<td>Full Name</td>
							<td><input type="text" value="<?=$row->full_name?>" name="f_full_name" size="30"></td>
						</tr>
						<tr>
							<td>Photo</td>
							<td>
								<?php if($row->photo_small != NULL):?>
									<img src="<?=base_url('uploads/user/'.$row->photo)?>" width="100" height="100">
								<?php else:?>
									<img src="<?=base_url('assets/image/user-photo.jpg')?>" width="50" height="50">
								<?php endif;?>
								Replace <input type="file" name="f_photo"  />
							</td>
						</tr>
<!-- 						<tr>
							<td>Type User</td>
							<td>
								<input type="radio" name="f_user_type" class="btn-type-user" value="1" checked="checked" disabled="disabled">Administrator
							</td>
						</tr> -->
					</table>

				</div>
			</div>
			<div class="bottom-nav">
				<input type="submit" class=" ui-btn-default" value="<?=label('save');?>" name="f_save" id="save">
			</div>
		<?=form_close()?>
		</div>
	</div>
</div>