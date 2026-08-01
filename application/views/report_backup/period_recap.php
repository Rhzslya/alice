<script type="text/javascript">
	$(document).ready(function() {
	  $('.tbl-rotate').css('height', $('.rotate').width());
	});
</script>
<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="<?=base_url('report/manage/'.$uc_period)?>" class="go-back">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>

<div class="main-pane">
	<div class="title-menu" style="width: auto;">Report</div>
	<div class="main-home">

		<div class="subtile">
			<?php if (isset($comp)): ?>
				<a href="<?=base_url('report/recap_pdf/'.$uc_period.'/'.$uc_level.'/'.$category);?>" class="btn-pdf-export" title="Export PDF">
					Export PDF
				</a>
				<a href="<?=base_url('report/recap_excel/'.$uc_period.'/'.$uc_level.'/'.$category);?>" class="btn-xls-export" title="Export Excel">
					Export Excel
				</a>
				<a href="<?=base_url('report/export_result/'.$uc_period)?>" class="btn-db-export" style="width: 90px;" title="Export Result">
					Export Result
				</a>
			<?php else: ?>
				<a href="#" class="btn-pdf-export" onclick="alert('Data Empty!');" title="Export PDF">
					Export PDF
				</a>
				<a href="#" class="btn-xls-export" onclick="alert('Data Empty!');" title="Export Excel">
					Export Excel
				</a>					
			<?php endif ?>



				<div class="combow" style="float: right;">
					<?=form_open('report/recap')?>
						<input type="hidden" name="f_uc_period" value="<?=$uc_period?>" />						
						<div class="selected">									
							<select name="f_level">
								<option value="">-- Level --</option>
								<?php foreach ($level as $lev) : ?>
									<option value="<?=$lev->uc_level?>"><?=$lev->label?></option>
								<?php endforeach; ?>	
							</select>
						</div>
						
						<div class="selected">	
							<select name="f_category">
								<option value="">-- Category --</option>
								<option value="1">Pra</option>
								<option value="2">Pasca</option>
								<option value="3">DP</option>
							</select>
						</div>
						<input type="submit" value="Recapitulation" class="ui-btn-default" style="margin-top: 0px">
					<?=form_close()?>
				</div>


		</div>
		
		<div class="period-content  form-adit" style="width: 1040px;height: 415px;padding-left: 15px;overflow-y: hidden;">
			<?php $this->load->helper('text'); ?>
			<div class="ui-ex-par" style="height: 30px;margin-bottom: 10px;margin-top: 0">
				<div class="ui-function" >
				<table>
					<tr>
						<td>Level</td>
						<td>
							<?php
								switch ($category) {
								 	case 1:
								 		$cat = "Pra";
								 		break;
								 	case 2:
								 		$cat = "Pasca";
								 		break;
								 	case 3:
								 		$cat = "DP";
								 		break;
								 	default:
								 		$cat = "N/A";
								 		break;
								 } ($category)
							?>
							<?=$level_label." (".$cat.")"?>
						</td>
						<td>Subject Title</td>
						<td><?=$period." (".time_format($start_date,'d M Y').")"?></td>
						<td>UPT</td>
						<td><?=$upt_name?></td>
					</tr>
				</table>
				</div>
			</div>
			<div class="recap-cell">
			<?php if (isset($comp)) : ?>				

				<table class="recap-score im-table" border="1" style="">
					<tr>
						<th width="15" rowspan="2">No.</th>
						<th width="100" rowspan="2">Seafarer Code</th>
						<th width="100" rowspan="2">Participant No.</th>
						<th width="300" rowspan="2">Name</th>
						<?php foreach ($comp as $co) : ?>
							<th class="tbl-rotate" style="text-align: inherit;  font-weight: normal;"><div><span><?=word_limiter($co['label'],6)?></span></div></th>
						<?php endforeach; ?>
					</tr>
					<tr>
						<?php foreach ($comp as $co) : ?>
							<th align="center"><?=$co['sequence']?></th>
						<?php endforeach; ?>
					</tr>
					<?php $i = 1; ?>
					<?php if (isset($part)): ?>
						<?php foreach ($part as $part) : ?>
							<tr>
								<td align="center"><?=$i?></td>
								<td align="center"><?=$part['seafarer_code']?></td>
								<td align="center"><?=$part['participant_no']?></td>
								<td><?=$part['full_name']?></td>							
								
								<?php foreach ($comp as $co) : ?>								
									<td align="right">
										<?php
											if (isset($score[$part['seafarer_code']][$co['uc_competency']])) {
												echo $score[$part['seafarer_code']][$co['uc_competency']];
											}
											else {
												echo "-";
											}
										?>
									</td>
								<?php endforeach; ?>
							</tr>

							<?php $i++;?>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="<?=count($comp)+4?>">Empty participant...</td>
						</tr>
					<?php endif ?>
				</table>

			<?php else: ?>
				<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
			<?php endif ?>

			</div>
		</div>

	</div>
</div>