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
		<?=form_open_multipart('user/update_password')?>
			<div class="form-adit">
				<input type="hidden" name="f_id" value="<?=$row->id?>">
				<?php if (isset($msg)): ?>
					<div style="background-color: red; width: 300px; text-align: center; font-size: 12pt; padding: 5px; border-radius: 10px; margin-bottom: 10px;">
						<?=$msg?>
					</div>
				<?php endif ?>
				<div class="result-user">
					<table class="form-frame" width="800" height="120">
						<tr>
							<td>Old Password</td>
							<td>:</td>
							<td>
								<input type="password" name="fOldPassword" size="35" autofocus required="" />
							</td>
						</tr>
						<tr>
							<td>New Password</td>
							<td>:</td>
							<td>
								<input type="password" name="fNewPassword" size="35" required="" />
							</td>
						</tr>
						<tr>
							<td>Retype New Password</td>
							<td>:</td>
							<td>
								<input type="password" name="fRetypePassword2" size="35" />
							</td>
						</tr>
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