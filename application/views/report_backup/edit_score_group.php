<link rel="stylesheet" type="text/css" href="<?=base_url('assets/tablesorter/theme.default.min.css')?>">

<script type="text/javascript" src="<?=base_url('assets/tablesorter/jquery-1.2.6.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/tablesorter/jquery.tablesorter.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/tablesorter/jquery.tablesorter.widgets.min.js');?>"></script>

<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="<?=base_url('report/report_by_competency/'.$uc_period.'/'.$uc_exam.'/'.$uc_competency)?>" class="go-back">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>

<div class="main-pane">
	<div class="title-menu" style="width: auto;">Report</div>
	<div class="main-home">
		
		<div class="period-content  form-adit" style="width: 1040px;height: 415px;padding-left: 15px;">
			<?php if (isset($result)): ?>
				
				<div class="ui-ex-par" style="width: 97%;margin-left: 0px;margin-top:0;height:94px;border:0px">
					<div class="ui-function" >
						<table style="width: 98%;margin: auto;">
							<tr>
								<td width="100" align="left" height="30">Level</th>
								<td width="150">
									<?=$level?>
									<?php switch ($row->diklat_type) {
										case 1:
											$cat = "(Pra)";
											break;

										case 2:
											$cat = "(Pasca)";
											break;

										case 3:
											$cat = "(DP)";
											break;
										
										default:
											$cat = "-";
											break;
									} ?>

									<?=$cat?>	
								</td>
								<td width="100" align="left">Function</th>
								<td><?=$function_name?></td>
							</tr>
							<tr></tr>
							<tr>
								<td align="left" height="40">Competency</th>
								<td colspan="3"><?=$competency_name?></td>
							</tr>

							<tr></tr>
						</table>
					</div>
				</div>

				<?=form_open('report/update_score_competency_group');?>

					<input type="hidden" name="f_uc_competency" value="<?=$uc_competency?>">
					<input type="hidden" name="f_uc_exam" value="<?=$uc_exam?>">
					<input type="hidden" name="f_uc_period" value="<?=$uc_period?>">

					<div class="pop-content" style="width: 1005px; height: 270px; overflow-y: auto;">
						<table class="im-table table-report" style="width: 100%;">
							<thead style="text-align:center;">
								<tr>
									<th width="30" align="right">No.</th>
									<th width="100" align="">Seafarer Code</th>
									<th width="100" align="">Participant No.</th>
									<th align="left" width="400">&nbsp;&nbsp;&nbsp;Full Name</th>
									<th width="100" align="left">&nbsp;&nbsp;&nbsp;Score</th>
								</tr>
							</thead>
							<?php $no = 1; ?>
							<?php foreach ($result as $res) : ?>

								<!-- Prop old value -->
								<input type="hidden" name="f_uc_attempt[]" value="<?=$res->uc?>">
								<input type="hidden" name="f_uc_att_comp[]" value="<?=$res->uc_eac?>">
								<input type="hidden" name="f_old_score[]" value="<?=decryptIt($res->score_normal)?>">

								<tr>
									<td align="right" width="30"><?=$no?>.</td>
									<td width="100"><?=$res->seafarer_code?></td>
									<td><?=$res->participant_no?></td>
									<td><?=$res->full_name?></td>
									<td align="left">							
										<select name="f_new_score[]">
											<option value="">-- Choose --</option>
											<?php $num_value = 0; ?>
											<?php for ($i=0; $i < 21 ; $i++) : ?>

												<?php if ($num_value >= decryptIt($res->score_normal)): ?>
													<option value="<?=$num_value?>" <?=select_set($num_value, decryptIt($res->score_normal));?> ><?=$num_value?></option>
												<?php endif ?>

												<?php $num_value = $num_value + 5; ?>
											<?php endfor; ?>
										</select>
									</td>
								</tr>
								
								<?php $no++; ?>
							<?php endforeach; ?>	
						</table>
					</div>

					<input type="submit" name="f_save" value="Save" class="ui-btn-default" style="margin: 10px 19px 0px 0px;">
				<?=form_close();?>

			<?php else: ?>
				<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
			<?php endif ?>
		</div>

	</div>
</div>

<div class="pop-up-form-add edit-score-form" style="height: 134px;width: 500px;z-index: 2">
	<div class="content-edit-score-form">
		
	</div>
</div>

<div class="pop-up-form-add edit-score-group-form" style="height: 525px;width: 1000px;z-index: 2">
	<div class="content-edit-score-group-form">
		
	</div>
</div>