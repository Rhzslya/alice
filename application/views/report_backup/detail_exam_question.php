<style type="text/css">
	
	table.options-true tr {
	    border: 0;
	    display: block;
	    margin: 5px;

	}
	/*.solid {
	    border: 2px red solid;
	    border-radius: 10px;
	}
	.dotted {
	    border: 2px green dotted;
	    border-radius: 10px;
	}

	.dashed {
	    border: 2px blue dashed;
	    border-radius: 10px;
	}

	td {
	    padding: 5px;
	}*/

</style>

<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="<?=base_url('report/manage/'.$uc_period);?>" class="go-back">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>
<div class="main-pane">
	<div class="title-menu">Answer Statistic</div>
	<div class="main-home">
		<div class="form-area">
			<div class="form-adit" style="padding: 10px;">

				<div class="report-nav" style="margin: 0px 45px 35px 45px;" align="right">
					<a href="<?=base_url('report/examination_quality_pdf/'.$uc_period.'/'.$uc_exam.'/'.$session);?>" title="Export to pdf" style="color:#fff">
						<div style="padding-right: 5px; padding-left: 5px; margin-top: 0px;" class="ui-btn-default">
							<img src="<?=base_url('assets/image/icon/ico-pdf.png');?>" width="15" height="15" style="margin-top: 2px;margin-right: 3px;"> Export to PDF
						</div>
					</a>
				</div>

				<div class="detail-paper">
					
					<div class="print">

						<div>

							<table style="margin-bottom: 70px;">
								<tr>
									<td width="130">Examination Code</td>
									<td width="160" style="border-bottom: 1px solid black"><?=$row->exam_code?></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td width="70" align="center">Subject Title</td>
									<td width="80" style="border-bottom: 1px solid black"><?=$row->period?></td>
									<td width="60" align="center">Date</td>
									<td width="110" style="border-bottom: 1px solid black"><?=($row->date != NULL ? time_format($row->date, 'd M Y') : "-")?></td>
									<td width="70" align="center">Session</td>
									<td width="80" style="border-bottom: 1px solid black"><?=(isset($session) ? $session : "-")?></td>
								</tr>
								<tr>
									<td height="40" valign="bottom">Level</td>
									<td valign="bottom" style="border-bottom: 1px solid black"><?=$row->level_name?></td>
									<td width="70" valign="bottom" align="center">Function</td>
									<td width="120" valign="bottom" style="border-bottom: 1px solid black"><?=$row->function_name?></td>
								</tr>
								<tr>
									<td height="65">Competency</td>
									<td colspan="9"><u><?=$row->competency_name?></u></td>
								</tr>
							</table>

							<h4>Question List and Student Answered</h4>
							<?php if (isset($question)): ?>
								<?php for ($i=0; $i<count($question); $i++) : ?>

									<div style="/*border: 1px solid black;*/ width: 900px; padding: 5px; margin-bottom: 25px;">
										<div style="/*border: 1px solid blue;*/ width: 30px; float: left; font-size: 15pt; font-weight: bold;">
											<?=$i+1?>.
										</div>
										<div style="/*border: 1px solid red;*/ float: left; width: 860px;">
											<?php if ($row->is_language == 1): ?>
												<?php if ($question[$i]['question_text_in'] != NULL): ?>
													<?=read_text($question[$i]['question_text_in']);?>
												<?php else: ?>
													<?=read_text($question[$i]['question_text_en']);?>
												<?php endif ?>
											<?php else: ?>
												<?php if ($question[$i]['question_text_en'] != NULL): ?>
													<?=read_text($question[$i]['question_text_en']);?>
												<?php else: ?>
													<?=read_text($question[$i]['question_text_in']);?>
												<?php endif ?>
											<?php endif ?>
										</div>
										<div style="/*border: 1px solid green;*/ margin: 0px 0px 0px 26px;">
											<?php if ($question[$i]['question_type'] == 1) : ?>

												<table class="options-true">
													<?php $abjad = 'a'; ?>
													<?php if (isset($option)): ?>
														<?php for ($j=0; $j<count($option[$i]); $j++) : ?>

															<tr>
																<td valign="top" width="20"><p><?=$abjad?>.</p></td>
																<td>
																	<!-- Default variable for num_rows options -->
																	<?php $option[$i][$j]['option_id_ass'] = 0;?>

																	<!-- Count the option if same -->
																	<?php for ($k=0; $k<count($student_answered); $k++) : ?>
																		<?php if (isset($student_answered[$k][$question[$i]['uc_question']])): ?>
																			<?php if ($option[$i][$j]['option_id'] == $student_answered[$k][$question[$i]['uc_question']]): ?>
																				
																				<!-- Increase the count options -->
																				<?php $option[$i][$j]['option_id_ass']++;?>

																			<?php endif ?>
																		<?php endif ?>
																	<?php endfor; ?>

																	<?php if ($row->is_language == 1): ?>
																		<?php if ($option[$i][$j]['option_text_in'] != NULL): ?>
																			<?=read_text($option[$i][$j]['option_text_in'])?>

																			<!-- Print the count of student answer for this options -->
																			<div style="color: #FF8A00; font-weight: bold;">
																				<?=$option[$i][$j]['option_id_ass']?> Participant(s)
																			</div>
																		<?php else: ?>
																			<?=read_text($option[$i][$j]['option_text_en'])?>

																			<!-- Print the count of student answer for this options -->
																			<div style="color: #FF8A00; font-weight: bold;">
																				<?=$option[$i][$j]['option_id_ass']?> Participant(s)
																			</div>
																		<?php endif ?>
																	<?php else: ?>
																		<?php if ($option[$i][$j]['option_text_en'] != NULL): ?>
																			<?=read_text($option[$i][$j]['option_text_en'])?>

																			<!-- Print the count of student answer for this options -->
																			<div style="color: #FF8A00; font-weight: bold;">
																				<?=$option[$i][$j]['option_id_ass']?> Participant(s)
																			</div>
																		<?php else: ?>
																			<?=read_text($option[$i][$j]['option_text_in'])?>

																			<!-- Print the count of student answer for this options -->
																			<div style="color: #FF8A00; font-weight: bold;">
																				<?=$option[$i][$j]['option_id_ass']?> Participant(s)
																			</div>
																		<?php endif ?>
																	<?php endif ?>
																</td>
															</tr>

															<?php $abjad++; ?>
														<?php endfor; ?>

														<!-- BEGIN Counting the IDK answer -->
															<!-- Default variable for num_rows options -->
															<?php $count_idk = 0;?>

															<!-- Count the option if same -->
															<?php for ($k=0; $k<count($student_answered); $k++) : ?>
																<?php if (isset($student_answered[$k][$question[$i]['uc_question']])): ?>
																	<?php if (0 == $student_answered[$k][$question[$i]['uc_question']]): ?>
																		
																		<!-- Increase the count options -->
																		<?php $count_idk++;?>

																	<?php endif ?>
																<?php endif ?>
															<?php endfor; ?>
															<tr>
																<td valign="top" width="20"><p><?=$abjad?>.</p></td>
																<td>
																	<p>I don't know</p>
																	<div style="color: #FF8A00; font-weight: bold;">
																		<?=$count_idk?> Participant(s)
																	</div>
																</td>
															</tr>
														<!-- BEGIN Counting the IDK answer -->

													<?php endif ?>
												</table>

											<?php elseif ($question[$i]['question_type'] == 2) : ?>



											<?php elseif ($question[$i]['question_type'] == 3) : ?>

												<div style="border: 1px solid black; margin: 10px 0px 0px 10px; font-size: 12pt; color: red; font-style: italic;">
													Matching
												</div>

											<?php endif?>
										</div>
									</div>

								<?php endfor;?>
							<?php else: ?>
								<div style="font-style: italic; margin: 50px 0px 0px 10px; font-size: 12pt; color: red;">
									No one student has attempted this examination...
								</div>
							<?php endif ?>
						</div>
						
					</div>
				</div>

			</div>
		</div>
	</div>
</div>