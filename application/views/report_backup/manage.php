<style type="text/css">
	.titletext{
		font-family: CGFont;
		font-size: 13px;
	}
</style>

<script type="text/javascript">
	$(document).ready(function() {
		var base_url = $("#base-url").html();

		$('.btn-detail-exam-package').click(function(){
			var uc_pack 	= $(this).attr('uc-pack');
			
			$('#page-blocker').fadeIn();
			$('.mng-exam-package').fadeIn();
			$('.content-mng-exam-package').load(base_url+'exam_package/detail', {js_uc_pack : uc_pack});
			
			return false;
		});

		$('.rep').click(function(){
			$('.la-loader').css('display','block');
		});

		$('body').on('click', '.btn-backup-mng', function() {

			var js_uc_period 	= $('input[name=f_uc_period]').val();
			var js_uc_day 		= $('input[name=f_uc_day]').val();
			var js_hari 		= $('input[name=f_hari]').val();

			$('.la-loader').css('display','none');
			$('#page-blocker').fadeIn();
			$('.export-backup').fadeIn();

			$('.content-export-backup').load(base_url + 'Report/export', {uc_period : js_uc_period, uc_day : js_uc_day, hari : js_hari});
			
			return false;
		});

	});
</script>

<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="<?=base_url('report');?>" class="go-back">&nbsp;</a>
	</div>
	<?php $this->load->view('client_logo'); ?>
</div>

<div class="main-pane">
	<div class="title-menu" style="width: auto;">Report</div>

	<?php if (isset($row)): ?>
		<?php $ses_no = 1; ?>

		<div class="button-side-title titletext">
			Subject Title : <?=$row->period?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?=($row->date_start != NULL ? time_format($row->date_start, 'd M Y') : "-")?> - <?=($row->date_finish != NULL ? time_format($row->date_finish, 'd M Y') : "-")?>
		</div>

		<div class="main-home">


			<div class="subtile">
				<div class="sub-dp" style="width: 100%">
					<div class="rep-tab" style="margin: 0px;">

						<div class="combow" style="float: right;">
							<?=form_open('report/period')?>
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
				</div>
			</div>

			<div class="period-content" style="overflow-y: auto; height: 420px;">			

				<?php if (isset($day)): ?>
					<?php for ($i=0; $i < count($day) ; $i++) : ?>
						<?php $hari = $i + 1;?>
						<div class="ui-pack-session" style="width: 99%">
							<div class="ui-session-title" style="width: 100%;height: 29px">
								<div class="ui-day" style="height: 29px;line-height: 29px">Day <?=$hari?> : <?=($day[$i]['date'] != NULL ? time_format($day[$i]['date'], 'd M Y') : "-")?></div>

									<!-- BACKPUP PERDAY -->
									<a href="<?=base_url('report/backup/'.$uc_period.'/'.$day[$i]['uc_day'].'/'.$hari)?>" class="btn-backup-mng" title="Export" style="width: 127px">Regenerate Result</a>
									<input type="hidden" name="f_uc_day" value="<?=$day[$i]['uc_day']?>">
									<input type="hidden" name="f_hari" value="<?=$hari?>">
									<!-- END BACKPUP PERDAY -->
																	
									<a href="<?=base_url('report/download/'.$uc_period.'/'.$day[$i]['uc_day'].'/'.$hari)?>" class="btn-pack-download" style="border-top-right-radius: 6px">
											Download Report
									</a>
							</div>

							<?php if ($session[$i][0]['uc_session'] != NULL): ?>
								<?php for ($j=0; $j < count($session[$i]) ; $j++) : ?>

									<div class="ui-session-main">
										<div class="ui-sess-block">
											<div class="ui-sess-main-title">
												<div class="ui-sess-num">Session <?=$ses_no?></div>
											</div>

											<?php if ($exam[$i][$j][0]['uc_exam'] != NULL): ?>
												<table class="ui-sess-exam" width="100%">
													<tr height="40">
														<th width="250">Examination</th>
														<th width="160">level</th>
														<th width="160">Function</th>
														<th width="250">Competency</th>
														<th>Report</th>
														 <!-- <th width="87">Duration</th> 
														<th width="160">Package</th> -->
														
														<th>Report</th>
														
													</tr>
													<?php for ($k=0; $k < count($exam[$i][$j]) ; $k++) : ?>

														<tr>
															<td valign="top">
																<b><?=$exam[$i][$j][$k]['exam_code']?> </b> <br />
																<?php switch ($exam[$i][$j][$k]['pra_pasca']) {
																	case 1:
																		$cat = "<b>(Pra)</b>";
																		break;

																	case 2:
																		$cat = "<b>(Pasca)</b>";
																		break;

																	case 3:
																		$cat = "<b>(DP)</b>";
																		break;
																	
																	default:
																		$cat = "-";
																		break;
																} ?>

																<?=$cat?>
															</td>
															<td valign="top">
																<?=$exam[$i][$j][$k]['level_name']?>
															</td>
															<td valign="top">
																<?=$exam[$i][$j][$k]['function_name']?>
															</td>
															<td valign="top" colspan="2">
																<div class="sess-compets">
													
																	<?php if (isset($comp[$exam[$i][$j][$k]['uc_exam']])) : ?>
																		<ul class="manage-rep-ul">
																			<?php for ($p=0; $p<count($comp[$exam[$i][$j][$k]['uc_exam']]); $p++) : ?>
																				<li>
																					<div class="strips-comp" style="width: 594px;margin-top: 5px;padding-right: 10px">
																						<div class="comp-numb" style="text-align: center;"><?=$comp[$exam[$i][$j][$k]['uc_exam']][$p]['sequence']?>.</div>
																						<div class="comp-name" style="width: 342px"><?=$comp[$exam[$i][$j][$k]['uc_exam']][$p]['competency_name']?></div>
																						<a href="<?=base_url('report/report_by_competency_excel/'.$uc_period.'/'.$exam[$i][$j][$k]['uc_exam'].'/'.$comp[$exam[$i][$j][$k]['uc_exam']][$p]['uc_competency'].'/'.$day[$i]['uc_day'])?>">
																							<input type="button" value="Excel" class="ui-btn-default" style="margin-top: 0;  margin-left: 10px;">
																						</a>																					
																						<a href="<?=base_url('report/report_by_competency/'.$uc_period.'/'.$exam[$i][$j][$k]['uc_exam'].'/'.$comp[$exam[$i][$j][$k]['uc_exam']][$p]['uc_competency'])?>">
																							<input type="button" value="Report" class="ui-btn-default rep" style="margin-top: 0;  margin-left: 0px;">
																						</a>
																					</div>
																				</li>
																			<?php endfor; ?>
																		</ul>
																	<?php endif; ?>
																		
																	
																</div>
																
															</td>
															<td>
																<a href="<?=base_url('report/regenerate_att_exam/'.$uc_period.'/'.$exam[$i][$j][$k]['uc_exam'])?>">
																	<input type="button" value="Regenerate" name="" style="float: right;" class="btn-finish-dp">
																</a>
																<a href="<?=base_url('report/finish_all/'.$uc_period.'/'.$exam[$i][$j][$k]['uc_exam'])?>">
																	<input type="button" value="Finish All UF" name="" style="float: right; margin-bottom: 3px" class="btn-finish-dp">
																</a>																	
															</td>
																											
															
														<!-- 	<td valign="top">
																<div class="package-sess">
																	<div class="pack-name-action">
																		<?php if (isset($pack[$exam[$i][$j][$k]['uc_exam']])) : ?>
																			<?php for ($p=0; $p<count($pack[$exam[$i][$j][$k]['uc_exam']]); $p++) : ?>

																				<a href="#" class="btn-detail-exam-package" uc-pack="<?=$pack[$exam[$i][$j][$k]['uc_exam']][$p]['uc_package']?>">
																					<div class="ui-pack-name"><?=$p+1?>. <?=$pack[$exam[$i][$j][$k]['uc_exam']][$p]['package_code']?></div>
																				</a>

																			<?php endfor; ?>
																		<?php else: ?>
																			Empty...
																		<?php endif; ?>
																	</div>
																</div>
															</td> -->
															
															
															<!-- <td valign="top"> -->
															
																
															<!-- 	 <a href="<?=base_url('report/examination_quality/'.$row->uc.'/'.$exam[$i][$j][$k]['uc_exam'].'/'.$ses_no);?>">
																	<input type="button" value="Answer Statistic" class="ui-btn-default" style="margin-top: 0; float: none; margin-left: 0px;">
																</a>  -->
															<!-- </td> -->
															
														</tr>

													<?php endfor; ?>
												</table>
											<?php else: ?>
												<table class="ui-sess-exam" style="font-family: CGFont;font-size: 13px">
													<tr>
														<td>Empty exam...</td>
													</tr>
												</table>
											<?php endif ?>
										</div>
								
									</div>

									<?php $ses_no++; ?>
								<?php endfor; ?>
							<?php else: ?>
								<div class="ui-session-main" style="font-family: CGFont;font-size: 13px">
									Empty session...
								</div>
							<?php endif ?>
						</div>

					<?php endfor; ?>
				<?php endif ?>

			</div>
		</div>

	<?php endif; ?>
</div>

<div class="pop-up-form-pack mng-exam-package" style="height: 400px; width: 680px;z-index: 2;">
	<div class="content-mng-exam-package"></div>
</div>

<div class="pop-up-form-pack export-backup" style="height: 100px;width: 262px;z-index: 2">
	<div class="content-export-backup">
	</div>
</div>