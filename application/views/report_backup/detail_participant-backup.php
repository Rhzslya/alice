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
		<div class="rep-tab" style="margin-top: 7px;">
			<a href="<?=base_url('report');?>" class="tabs">Examination</a>
			<a href="<?=base_url('report/participant');?>" class="tabs active-tab">Participant</a>

		</div>
		<div class="form-area">

			<?php if (isset($row)): ?>
				<div class="rep-part-info" style="margin-bottom: 5px;">
					<table>
						<tr>
							<td class="bold" width="100">Seafarer ID</td>
							<td width="150"><?=$row->seafarer_code?></td>

							<td class="bold" width="100">Full Name</td>
							<td width="350"><?=$row->full_name?></td>

							<td class="bold" width="70">Level</td>
							<td width="150"><?=$row->level_name?></td>

							<td width="150" align="right">
								<a href="<?=base_url('report/detail_participant_pdf/'.$row->seafarer_code);?>" title="Export to pdf" style="color:#fff">
									<div style="padding-right: 5px; padding-left: 5px; margin-top: 0px;" class="ui-btn-default">
										<img src="<?=base_url('assets/image/icon/ico-pdf.png');?>" width="15" height="15" style="margin-top: 2px;margin-right: 3px;"> Export to PDF
									</div>
								</a>
							</td>
						</tr>
					</table>
				</div>
			<?php endif ?>

			<div class="rep-part-view">
				<?php if (isset($function)): ?>
					<?php for ($i=0; $i <count($function) ; $i++) : ?>

						<div class="rep-function">
							<h2><?=$function[$i]['function_name']?></h2>
							<div class="rep-competency">
								<div class="rep-row">							
									<div class="rep-col-1" style="font-family: CGFontb">Competency</div>
									<div class="rep-col-2" style="font-family: CGFontb">Final Score</div>
								</div>

								<?php if (isset($competency)): ?>
									<?php for ($j=0; $j <count($competency[$i]) ; $j++) : ?>
										<div class="rep-row-thin">
											<div class="rep-col-1">
												<label>
													<?=$competency[$i][$j]['competency_name']?>
												</label>

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
																	<a href="<?=base_url('report/detail/'.$participant[0]['seafarer_code'].'/'.$examination[$i][$j][$k]['uc_attempt']);?>"><input type="button" value="Detail"></a>
																</td>
															</tr>
														<?php endfor; ?>
													<?php endif ?>
												</table>
											</div>
											<div class="rep-col-2 big"><?=($competency[$i][$j]['max_score'] != NULL ? $competency[$i][$j]['max_score'] : "-")?></div>
											<div class="rep-col-3">
												<input type="button" value="Show Detail" name="" class="togel" no-func="<?=$i?>" no-comp="<?=$j?>">
											</div>
										</div>
									<?php endfor; ?>
								<?php endif ?>

							</div>
						</div>

					<?php endfor; ?>
				<?php endif ?>

			</div>
		</div>
	</div>

</div>