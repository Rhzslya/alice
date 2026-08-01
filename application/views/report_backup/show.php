<link rel="stylesheet" type="text/css" href="<?=base_url('assets/tablesorter/theme.default.min.css')?>">

<script type="text/javascript" src="<?=base_url('assets/tablesorter/jquery-1.2.6.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/tablesorter/jquery.tablesorter.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/tablesorter/jquery.tablesorter.widgets.min.js');?>"></script>

<script type="text/javascript">
	$(document).ready(function() {

		var base_url = $('#base-url').html();

		$('.table-report').tablesorter({
			widgets			: ['zebra', 'columns'],
			usNumberFormat	: false,
			sortReset		: true,
			sortRestart		: true
		});

		$('.btn-edit-score').click(function() {
			var uc_eac 	= $(this).attr('uc');
			var uc_exam = $('input[name=f_uc_exam]').val();
			var uc_period = $('input[name=f_uc_period]').val();

			$('.edit-score').fadeIn();
			$('#page-blocker').fadeIn();

			$('.content-edit-score').load(base_url+'report/get_score_competency', { js_uc_eac : uc_eac, js_uc_exam : uc_exam, js_uc_period : uc_period });

			return false;
		});

		$('.edit-score-group').click(function() {
			var uc_period = $('input[name=f_uc_period]').val();
			var uc_exam = $('input[name=f_uc_exam]').val();
			var uc_competency = $('input[name=f_uc_competency]').val();

			$('.edit-score-group-form').fadeIn();
			$('#page-blocker').fadeIn();

			$('.content-edit-score-group-form').load(base_url+'report/edit_score_group_form', { js_uc_period : uc_period, js_uc_exam : uc_exam, js_uc_competency : uc_competency });

			return false;
		});

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
		
		<input type="hidden" name="f_uc_competency" value="<?=$uc_competency?>">
		<input type="hidden" name="f_uc_exam" value="<?=$uc_exam?>">
		<input type="hidden" name="f_uc_period" value="<?=$uc_period?>">
		<div class="subtile">
			<?php if (isset($result)): ?>
				<a href="<?=base_url('report/report_by_competency_pdf/'.$uc_period.'/'.$uc_exam.'/'.$uc_competency);?>" class="btn-pdf-export" title="Export PDF">
					Export PDF
				</a>
				<a href="<?=base_url('report/report_by_competency_excel/'.$uc_period.'/'.$uc_exam.'/'.$uc_competency);?>" class="btn-xls-export" title="Export Excel">
					Export Excel
				</a>		
			<?php else: ?>
				<a href="#" class="btn-pdf-export" onclick="alert('Data Empty!');" title="Export PDF">
					Export PDF
				</a>
				<a href="#" class="btn-xls-export" onclick="alert('Data Empty!');" title="Export Excel">
					Export Excel
				</a>					
			<?php endif ?>
		</div>
		
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

				<!-- Show Edit Score if Top DPKP User Login -->
				<?php if ($this->session->userdata('log_user_category') == 1): ?>
					<div align="right" style="margin: 10px 23px 50px 0px;">
						<a href="#" class="edit-score-group">
							<button class="ui-btn-default" style="background-color: #b40000;color: #fff;border: 1px solid #a20000">Edit Score</button>
						</a>					
					</div>
				<?php endif ?>
				
				<table class="im-table table-report" style="width: 1000px;">
					<thead style="text-align:center;">
						<tr>
							<th width="30" align="right">No.</th>
							<th width="100" align="">Seafarer Code</th>
							<th width="100" align="">Participant No.</th>
							<th align="left" width="400">&nbsp;&nbsp;&nbsp;Full Name</th>
							<th width="100" align="left">&nbsp;&nbsp;&nbsp;Score</th>
							<th width="30">&nbsp;&nbsp;&nbsp;Show</th>
						</tr>
					</thead>
					<?php $i = 1; ?>
					<?php foreach ($result as $res) : ?>
						<tr>
							<td align="right" width="30"><?=$i?>.</td>
							<td width="100"><?=$res->seafarer_code?></td>
							<td><?=$res->participant_no?></td>
							<td><?=$res->full_name?></td>
							<td align="left">
								<?php if ($res->is_done == NULL) : ?>
									<span class="score-red" style="color: #FF0000">Unattempt</span>									
								<?php elseif ($res->is_done == 0) : ?>
									<span class="score-red">Unfinish</span>
								<?php else : ?>								
									<!-- <span><?=$this->encrypt->decode($res->score)?></span> -->								
									<span style="width: 35px;float: left;">
										<?php if ($setting->value == 2): ?>
											<?=decryptIt($res->score_normal)?>
										<?php elseif ($setting->value == 3): ?>
											<?=decryptIt($res->score_2)?>
										<?php else: ?>
											<?=decryptIt($res->score)?>
										<?php endif ?>
									</span>
								<?php endif; ?>
								
								<!-- Show Edit Score if Top DPKP User Login -->
								<?php if ($this->session->userdata('log_user_category') == 1): ?>
									<?php if ($res->score != NULL || $res->score_2 != '' || $res->score_normal != ''): ?>
										<a href="#" class="lc-edit-btn btn-edit-score" uc="<?=$res->eac_uc?>" title="Edit Score">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
									<?php endif ?>
								<?php endif ?>
							</td>
							<td>
								<?php if ($res->score != NULL): ?>
									<a href="<?=base_url('report/report_by_answer/'.$uc_period.'/'.$uc_competency.'/'.$res->uc);?>">
										<input type="button" class="ui-btn-default" value="Answer" name="" style="margin-top: 0">
									</a>
								<?php else: ?>
									-
								<?php endif ?>
							</td>
						</tr>
						
						<?php $i++; ?>
					<?php endforeach; ?>	
				</table>

			<?php else: ?>
				<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
			<?php endif ?>
		</div>

	</div>
</div>

<div class="pop-up-form-add edit-score" style="height: 134px;width: 500px;z-index: 2">
	<div class="content-edit-score">
		
	</div>
</div>

<div class="pop-up-form-add edit-score-group-form" style="height: 525px;width: 1000px;z-index: 2">
	<div class="content-edit-score-group-form">
		
	</div>
</div>