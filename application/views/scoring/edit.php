<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>

<div class="main-pane">
	<div class="title-menu">Scoring</div>

		<div class="main-home">
		
			<div class="form-area" >
				<?=form_open('scoring/update')?>
					<div class="form-adit">
						<table class="form-frame">
							<tr>
								<td width="50">Mode</td>
								<td>:</td>
								<td width="100">
									<input type="radio" name="f_mode" value="3" <?=radio_set(3, $row->value);?> /> +4 & -1
								</td>
								<td width="100">
									<input type="radio" name="f_mode" value="1" <?=radio_set(1, $row->value);?> /> +2 & -1
								</td>
								<td width="100">
									<input type="radio" name="f_mode" value="2" <?=radio_set(2, $row->value);?> /> +1 & 0
								</td>
							</tr>
						</table>
					</div>
					<div align="right">
						<input type="submit" name="f_save" class="ui-btn-default" value="Save">
					</div>
				<?=form_close()?>
			</div>
		</div>
</div>