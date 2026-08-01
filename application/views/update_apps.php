<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="#" class="go-back-disable">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>

<div class="main-pane">
	<div class="title-menu" style="width: auto;">Update Applications</div>
	<?=form_open_multipart('Update_apps/update')?>
		<div class="main-home" style="background-color: #fff;border: 1px solid #222;height: 450px">	
			<input type="hidden" name="f_message" value="<?=(isset($message) ? $message : '');?>">

			<div class="update-form" align="center">
				<div>
					<table style="font-family: CGFont; font-size: 9pt">
						<tr>
							<td>File (.zip)</td>
							<td>:</td>
							<td><input type="file" name="f_file"></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div align="right" style="width: 1085px">
			<input type="submit" name="f_update" value="Update" class="ui-btn-default">
		</div>
	<?=form_close()?>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		var message = $('input[name=f_message]').val();

		if (message != '') {
			alert(message);
		}
	});
</script>