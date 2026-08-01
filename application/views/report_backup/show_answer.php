<style type="text/css">
	
	table.options-true tr {
	    border: 0;
	    display: block;
	    margin: 5px;

	}

</style>

<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<?php if (isset($row)): ?>
			<a href="<?=base_url('report/report_by_competency/'.$uc_period.'/'.$row->uc_exam.'/'.$uc_competency)?>" class="go-back">&nbsp;</a>
		<?php endif ?>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>
<div class="main-pane">
	<div class="title-menu">Participant Answer</div>
	<div class="main-home">
		<div class="form-area">
			<div class="form-adit" style="padding: 10px;">

				<div class="report-nav" style="margin: 0px 45px 35px 45px;" align="right">
					<a href="<?=base_url('report/report_by_answer_pdf/'.$uc_period.'/'.$uc_competency.'/'.$uc_exam_attempt);?>" title="Export to pdf" style="color:#fff">
						<div style="padding-right: 5px; padding-left: 5px; margin-top: 0px;" class="ui-btn-default">
							<img src="<?=base_url('assets/image/icon/ico-pdf.png');?>" width="15" height="15" style="margin-top: 2px;margin-right: 3px;"> Export to PDF
						</div>
					</a>
				</div>


				<div class="detail-paper">
					
					<div class="print">
						<br/>
						<?php if (isset($row)): ?>

							<div>
								<table align="center" width="100%">
									<tr>
										<td width="100" valign="top"><b>Full Name</b></td>
										<td width="15" valign="top">&nbsp;:</td>
										<td width="270" valign="top">
											<?=$row->full_name?>
										</td>

										<td width="15">&nbsp;</td>

										<td width="130" valign="top"><b>Examination Code</b></td>
										<td width="15" valign="top">&nbsp;:</td>
										<td valign="top">
											<?=$row->exam_code?>
										</td>
									</tr>
									<tr>
										<td valign="top"><b>Seaferer Code</td>
										<td valign="top">&nbsp;:</td>
										<td valign="top">
											<?=$row->seafarer_code?>
										</td>

										<td>&nbsp;</td>

										<td valign="top"><b>Competency Name</b></td>
										<td valign="top">&nbsp;:</td>
										<td valign="top">
											<?=$row->competency_name?>
										</td>
									</tr>
									<tr>
										<td colspan="4">&nbsp;</td>
										<td><b>Score</b></td>
										<td>&nbsp;:</td>
										<td>
											<?php if ($setting->value == 2): ?>
												<?=decryptIt($row->comp_score_normal)?>
											<?php elseif ($setting->value == 3): ?>
												<?=decryptIt($row->comp_score_2)?>
											<?php else: ?>
												<?=decryptIt($row->comp_score)?>
											<?php endif ?>
										</td>
									</tr>
								</table>
								<h4>Question List and Student Answered</h4>

								<?php if ($row->is_done != 0): ?>

									<?php for ($i=0; $i<$max_question; $i++) : ?>

										<div style="/*border: 1px solid black;*/ width: 900px; padding: 5px; margin-bottom: 25px;">
											<div style="/*border: 1px solid blue;*/ width: 30px; float: left; font-size: 15pt; font-weight: bold;">
												<?=$i+1?>.
											</div>
											<div style="/*border: 1px solid red;*/ float: left; width: 860px;">
												<?php if ($row->is_language == 1): ?>
													<?php if ($question_text_in[$i] != NULL): ?>
														<?=read_text($question_text_in[$i]);?>
													<?php else: ?>
														<?=read_text($question_text_en[$i]);?>
													<?php endif ?>
												<?php else: ?>
													<?php if ($question_text_en[$i] != NULL): ?>
														<?=read_text($question_text_en[$i]);?>
													<?php else: ?>
														<?=read_text($question_text_in[$i]);?>
													<?php endif ?>
												<?php endif ?>
											</div>
											<div style="/*border: 1px solid green;*/ margin: 0px 0px 0px 26px;">

												<?php $margin = ""; ?>

												<?php if ($question_type[$i] == 1) : ?>

													<table class="options-true">
														<?php $abjad = 'a'; ?>
														<?php for ($j=0; $j<$max_option[$i]; $j++) : ?>

															<!-- <?php if ($keys[$i] == $option_uc[$i][$j]):?>

																<?php

																	$background = "background-color : #7BEF83;";
																	

																	$style = "border: 1px solid black; border-radius: 4px; margin-bottom: 5px; ".$background;
																?>

															<?php elseif ($answers[$i] == $option_uc[$i][$j]):?>

																<?php

																	$background = "background-color : #F39494;";

																	$style = "border: 1px solid black; border-radius: 4px; margin-bottom: 5px; ".$background;
																?>

															<?php else: ?>

																<?php
																	$style = "margin-bottom: 5px;";
																?>

															<?php endif;?> -->

															<?php if ($answers[$i] == $option_uc[$i][$j]):?>

																<?php if ($answers[$i] == $keys[$i]):?>
																	<?php
																		$background = "background-color : #7BEF83;";																		

																		$style = "border: 1px solid black; border-radius: 4px; margin-bottom: 5px; ".$background;
																	?>
																<?php else: ?>
																	<?php
																		$background = "background-color : #F39494;";

																		$style = "border: 1px solid black; border-radius: 4px; margin-bottom: 5px; ".$background;
																	?>
																<?php endif;?>

															<?php else: ?>

																<?php
																	$style = "margin-bottom: 5px;";
																?>

															<?php endif;?>

															<tr>
																<td valign="top" width="20"><p><?=$abjad?>.</p></td>

																<?php if ($row->is_language == 1): ?>
																	<?php if ($option_text_in[$i][$j] != NULL): ?>
																		
																		<!-- Answer true or false -->
																		<?php if ($answers[$i] == $option_uc[$i][$j]): ?>
																			<td  style="<?=$style?>">
																				<?=read_text($option_text_in[$i][$j]);?>
																			</td>
																		<?php else: ?>
																			<td><?=read_text($option_text_in[$i][$j]);?></td>
																		<?php endif ?>
																		<!-- End answer true or false -->

																	<?php else: ?>

																		<!-- Answer true or false -->
																		<?php if ($answers[$i] == $option_uc[$i][$j]): ?>
																			<td style="<?=$style?>">
																				<?=read_text($option_text_en[$i][$j]);?>
																			</td>
																		<?php else: ?>
																			<td><?=read_text($option_text_en[$i][$j]);?></td>
																		<?php endif ?>
																		<!-- End answer true or false -->
																	<?php endif ?>

																<?php else: ?>																
																	<?php if ($option_text_en[$i][$j] != NULL): ?>

																		<!-- Answer true or false -->
																		<?php if ($answers[$i] == $option_uc[$i][$j]): ?>
																			<td style="<?=$style?>">
																				<?=read_text($option_text_en[$i][$j]);?>
																			</td>
																		<?php else: ?>
																			<td><?=read_text($option_text_en[$i][$j]);?></td>
																		<?php endif ?>
																		<!-- End answer true or false -->

																	<?php else: ?>

																		<!-- Answer true or false -->
																		<?php if ($answers[$i] == $option_uc[$i][$j]): ?>
																			<td style="<?=$style?>">
																				<?=read_text($option_text_in[$i][$j]);?>
																			</td>
																		<?php else: ?>
																			<td><?=read_text($option_text_in[$i][$j]);?></td>
																		<?php endif ?>
																		<!-- End answer true or false -->

																	<?php endif ?>
																<?php endif ?>
															</tr>

															<?php $abjad++; ?>
														<?php endfor;?>

														<!-- For i dont know answer -->
														<?php 
															$style_idk = "";

															if ($answers[$i] == "0") {
																$style_idk = "border: 1px solid black; border-radius: 4px; margin-bottom: 5px; background-color : #F39494;";
															}
														?>


														<tr>
															<td valign="top" width="20"><p><?=$abjad?>.</p></td>
															<td style="<?=$style_idk?>">
																<?php if ($row->is_language == 1): ?>
																	Saya tidak tahu
																<?php else: ?>
																	I don't know
																<?php endif ?>
															</td>
														</tr>
													</table>

													<?php $margin = "margin: 10px 0px 0px 20px;"; ?>
												<?php elseif ($question_type[$i] == 2) : ?>

													<?php 

														$true = "";
														$false = "";

														if ($answers[$i] == 1) {

															if ($resans[$i] == 1) {
																$background = "background-color : #7BEF83;";
															} else {
																$background = "background-color : #F39494;";
															}

															$true = "border: 1px solid black; border-radius: 4px; margin-bottom: 5px; ".$background;

														} else {

															if ($resans[$i] == 1) {
																$background = "background-color : #7BEF83;";
															} else {
																$background = "background-color : #F39494;";
															}

															$false = "border: 1px solid black; border-radius: 4px; margin-bottom: 5px; ".$background;

														}

													?>	

													<table class="options-true">
														<tr style="<?=$true?>">
															<td valign="top" width="20"><p>a.</p></td>
															<td>
																<?php if ($row->is_language == 1): ?>
																	Benar
																<?php else: ?>
																	True
																<?php endif ?>
															</td>
														</tr>
														<tr style="<?=$false?>">
															<td valign="top" width="20"><p>a.</p></td>
															<td>
																<?php if ($row->is_language == 1): ?>
																	Salah
																<?php else: ?>
																	False
																<?php endif ?>
															</td>
														</tr>
													</table>

													<?php $margin = "margin: 10px 0px 0px 20px;"; ?>
												<?php elseif ($question_type[$i] == 3) : ?>

													<div style="/*border: 1px solid black;*/ width: 830px; height: 450px;">
														
														<table class="im-table" style="float: left;">

															<tr>
																<th colspan="2">Question</th>
															</tr>

															<?php $abjad = 1; ?>
															<?php foreach ($match_key[$i] as $mk) : ?>

																<tr>
																	<td valign="top" width="20"><p><?=$abjad?>.</p></td>
																	<td>
																		<?php if ($keys[$i] == NULL): ?>
																			<?php if ($row->is_language == 1): ?>
																				<?php if ($question_field_in[$mk] != NULL): ?>
																					<?=read_text($question_field_in[$mk]);?>
																				<?php else: ?>
																					<?=read_text($question_field_en[$mk]);?>
																				<?php endif ?>
																			<?php else: ?>
																				<?php if ($question_field_en[$mk] != NULL): ?>
																					<?=read_text($question_field_en[$mk]);?>
																				<?php else: ?>
																					<?=read_text($question_field_in[$mk]);?>
																				<?php endif ?>
																			<?php endif ?>
																		<?php endif ?>																		
																	</td>
																</tr>

																<?php $abjad++; ?>
															<?php endforeach; ?>
														</table>

														<table class="im-table" style="float: left; margin-left: 20px;">

															<tr>
																<th colspan="2">Key</th>
															</tr>

															<?php $abjad = 1; ?>
															<?php foreach ($match_key[$i] as $mk) : ?>

																<tr>
																	<td valign="top" width="20"><p><?=$abjad?>.</p></td>
																	<td>
																		<?php if ($keys[$i] == NULL): ?>
																			<?php if ($row->is_language == 1): ?>
																				<?php if ($answer_field_in[$mk] != NULL): ?>
																					<?=read_text($answer_field_in[$mk]);?>
																				<?php else: ?>
																					<?=read_text($answer_field_en[$mk]);?>
																				<?php endif ?>
																			<?php else: ?>
																				<?php if ($answer_field_en[$mk] != NULL): ?>
																					<?=read_text($answer_field_en[$mk]);?>
																				<?php else: ?>
																					<?=read_text($answer_field_in[$mk]);?>
																				<?php endif ?>
																			<?php endif ?>
																		<?php endif; ?>
																	</td>
																</tr>

																<?php $abjad++; ?>
															<?php endforeach; ?>
														</table>

													</div>

													<?php $margin = "margin: -50px 0px 0px 20px;"; ?>
												<?php endif?>

												<div style="<?=$margin?> font-size: 12pt; font-weight: bold;">
													Result : 
														<?php if (isset($resans[$i])): ?>															
															<?php if ($resans[$i] == 'T'): ?>
																<?php echo "Correct" ?>
															<?php else: ?>
																<?php echo "Wrong" ?>
															<?php endif ?>
														<?php else: ?>
															Wrong
														<?php endif ?>
												</div>

											</div>
										</div>

									<?php endfor;?>
								<?php else: ?>

									<div style="margin-top: 150px;" align="center"> 
										<h2>Participant have not completed the exam</h2>
									</div>

								<?php endif ?>
							</div>
							
						<?php else: ?>
							Empty...
						<?php endif ?>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>