<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="<?=base_url('report/recap/'.$uc_period)?>" class="go-back">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>

<div class="main-pane">
	<div class="title-menu" style="width: auto;">Report Score</div>
	<div class="main-home">

		<?=form_open_multipart('report/regenerate_result')?>
			<input type="hidden" name="f_uc_period" value="<?=$uc_period?>" />
			<table border="0" style="font-family: CGFont; font-size: 9pt;">
				<tr>
					<td>File (.xls)</td>
					<td>:</td>
					<td><input type="file" name="f_file" required style="width: 285px;"></td>
				</tr>
				<tr>
					<td style="margin-left: 0">
						<input type="submit" name="f_update" value="Update" class="ui-btn-default" style="margin-left: 0" onclick="$('.la-loader').show();">
					</td>					
				</tr>
			</table>
		<?=form_close()?>

	</div>
</div>		