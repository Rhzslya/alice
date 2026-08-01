<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/detail-participant.css')?>">
<script>
	$(document).ready(function(){

		$( ".togel" ).click(function() {
			var link = $(this);
			var no_func = $(this).attr('no-func');
			var no_comp = $(this).attr('no-comp');

			$(".tab-"+no_func+"-"+no_comp).slideToggle(200, function() {
		        if ($(this).is(':visible')) {
		        	link.val('Hide Detail');
		        } else {
		        	link.val('Show Detail');
		        }
		    });
		});

	});
</script>

<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="<?=base_url('report/participant');?>" class="go-back">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>

<div class="main-pane">
	<div class="title-menu">Report</div>
	<div class="main-home">
		<div class="rep-tab" style="margin-top: 7px">
			<a href="<?=base_url('report');?>" class="tabs">Examination</a>
			<a href="<?=base_url('report/participant');?>" class="tabs active-tab">Participant</a>
			<a href="<?=base_url('report/detail_participant_pdf/'.$row->seafarer_code);?>" title="Export to pdf" style="color:#fff">
				<div style="padding-right: 5px; padding-left: 5px; margin-top: 0px;" class="ui-btn-default">
					<img src="<?=base_url('assets/image/icon/ico-pdf.png');?>" width="15" height="15" style="margin-top: 2px;margin-right: 3px;"> Export to PDF
				</div>
			</a>
		</div>
		<div class="form-area">
			<div class="ui-rep-main">
			<?php if (isset($row)): ?>
				<div class="ui-main-head">
					<div class="ui-col-1">Seafarer ID</div>
					<div class="ui-col-2"><?=$row->seafarer_code?></div>
					<div class="ui-col-1">Full Name</div>
					<div class="ui-col-2" style="width: 350px"><?=$row->full_name?></div>
					<div class="ui-col-1">Level</div>
					<div class="ui-col-2" style="width: 150px"><?=$row->level_name?></div>
				</div>
			<?php endif ?>
			<?php if (isset($function)): ?>
				<div class="ui-rep-content">
					<?php for ($i=0; $i <count($function) ; $i++) : ?>
						<div class="ui-row-fx"><?=$function[$i]['function_name']?></div>
						<div class="ui-row-comp">
							<div class="ui-col-comp">Competency</div>
							<div class="ui-col-score">Final Score</div>
						</div>
						<?php if (isset($competency)): ?>
							<?php for ($j=0; $j <count($competency[$i]) ; $j++) : ?>
								<div class="ui-row-comain">
									<div class="ui-comain-strip">
										<div class="ui-col-com-1">
											<?=$competency[$i][$j]['competency_name']?>
										</div>
										<div class="ui-col-com-2">
											<!-- <?=($competency[$i][$j]['max_score'] != NULL ? $competency[$i][$j]['max_score'] : "-")?> -->

											<?php $score = $competency[$i][$j]['max_score'];?>
											<?php if ($score < 70):?>
												<span style="color: #c10000"><?=$score?></span>
											<?php elseif ($score > 85):?>
												<span style="color: green"><?=$score?></span>
											<?php elseif  ($score != NULL):?>
												-
											<?php endif;?>
										</div>
										<div class="ui-col-com-3">
											<input type="button" class="ui-btn-default togel" name="" value="Show Detail" style="float: none;" no-func="<?=$i?>" no-comp="<?=$j?>">
										</div>
									</div>

									<div class="ui-for-table">										
										<table class="im-table tab-<?=$i?>-<?=$j?>" hidden>
											<tr>
												<th align="left" width="250">Exam Code</th>
												<th align="left" width="200">Subject Title</th>
												<th align="left" width="160">Date</th>
												<th align="left" width="100">Score</th>
												<th align="left" width="70">Action</th>
											</tr>
											<?php if (isset($examination)): ?>
												<?php for ($k=0; $k <count($examination[$i][$j]) ; $k++) : ?>
													<tr>
														<td align="left"><?=$examination[$i][$j][$k]['exam_code']?></td>
														<td align="left"><?=$examination[$i][$j][$k]['periode']?></td>
														<td align="left"><?=($examination[$i][$j][$k]['time_exam'] != NULL ? time_format($examination[$i][$j][$k]['time_exam'], 'd-M-Y') : "-")?></td>
														<td align="right"><?=($examination[$i][$j][$k]['score'] != NULL ? $examination[$i][$j][$k]['score'] : "-")?></td>
														<td align="center">
															<a href="<?=base_url('report/detail/'.$participant[0]['seafarer_code'].'/'.$examination[$i][$j][$k]['uc_attempt']);?>"><input type="button" value="Detail" class="ui-btn-default" style="margin-top: 0"></a>
														</td>
													</tr>
												<?php endfor; ?>
											<?php endif ?>
										</table>
									</div>
								</div>
							<?php endfor; ?>
						<?php endif ?>
					<?php endfor; ?>
				</div>
			<?php endif ?>
			</div>
		</div>
	</div>

</div>