<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/immu-ui/immu-ui.css')?>">

<style type="text/css">

	table {
	    border-collapse: collapse;
	}
	
	table.options-true tr {
	    border: 0;
	    display: block;
	    margin: 5px;
	}

</style>

<div class="main-pane">
	<h2>Student Examination Detail</h2>
	<div class="main-home">
		<div class="form-area">

				<div class="detail-paper">
					
					<div class="print">
						<br/>
						<div>
							<table align="center" width="100%">
								<tr>
									<td width="18%"><b>Nama Lengkap</b></td>
									<td width="2%">&nbsp;:</td>
									<td width="25%">
										<?=$row->full_name?>
									</td>

									<td width="7%">&nbsp;</td>

									<td width="22%"><b>Examination Code</b></td>
									<td width="2%">&nbsp;:</td>
									<td width="25%">
										<?=$row->exam_code?>
									</td>

								</tr>
								<tr>
									<td><b>Seaferer Code</td>
									<td>&nbsp;:</td>
									<td>
										<?=$row->seafarer_code?>
									</td>

									<td>&nbsp;</td>

									<td><b>Score</b></td>
									<td>&nbsp;:</td>
									<td>
										<?=$row->score?>
									</td>
								</tr>
							</table>

							<h4>Question List and Student Answered</h4>

							<?php for ($i=0; $i<$max_question; $i++) : ?>

								<div style="/*border: 1px solid black;*/ width: 900px; padding: 5px; margin-bottom: 25px;">
									<div style="/*border: 1px solid blue;*/ width: 30px; /*float: left;*/ font-size: 13pt; font-weight: bold;">
										<?=$i+1?>.
									</div>
									<div style="/*border: 1px solid red;*/ /*float: left;*/ width: 860px; margin: -30px 0px 0px 30px;">
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

													<?php if ($option_id[$i][$j] == $answers[$i]):?>

														<?php

															$background = "background-color: #F39494;";

															if ($resans[$i] == 1) {
																$background = "background-color: #7BEF83;";
															}

															$style = "border: 1px solid black; border-radius: 10px; margin-bottom: 5px; ".$background;
														?>

													<?php elseif ($option_id[$i][$j] == $keys[$i]):?>

														<?php
															$background = "background-color: #7BEF83;";

															$style = "border: 1px solid black; border-radius: 10px; margin-bottom: 5px; ".$background;
														?>

													<?php else: ?>

														<?php 
															$style = "margin-bottom: 5px;";
														?>

													<?php endif;?>

													<tr style="<?=$style?>">
														<td valign="top" width="20"><p><?=$abjad?>.</p></td>
														<td>
															<?php if ($row->is_language == 1): ?>
																<?php if ($option_text_in[$i][$j] != NULL): ?>
																	<?=read_text($option_text_in[$i][$j]);?>
																<?php else: ?>
																	<?=read_text($option_text_en[$i][$j]);?>
																<?php endif ?>
															<?php else: ?>
																<?php if ($option_text_en[$i][$j] != NULL): ?>
																	<?=read_text($option_text_en[$i][$j]);?>
																<?php else: ?>
																	<?=read_text($option_text_in[$i][$j]);?>
																<?php endif ?>
															<?php endif ?>
														</td>
													</tr>

													<?php $abjad++; ?>
												<?php endfor;?>

												<!-- For i dont know answer -->
												<?php 
													$style_idk = "";

													if ($answers[$i] == "0") {
														$style_idk = "border: 1px solid black; border-radius: 10px; margin-bottom: 5px; background-color: #F39494;";
													}
												?>

												<tr style="<?=$style_idk?>">
													<td valign="top" width="20"><p><?=$abjad?>.</p></td>
													<td>
														<?php if ($row->is_language == 1): ?>
															Saya tidak tahu
														<?php else: ?>
															I don't know
														<?php endif ?>
													</td>
												</tr>
											</table>

										<?php elseif ($question_type[$i] == 2) : ?>

											<?php 

												$true = "";
												$false = "";

												if ($answers[$i] == 1) {

													if ($resans[$i] == 1) {
														$background = "background-color: #7BEF83;";
													} else {
														$background = "background-color: #F39494;";
													}

													$true = "border: 1px solid black; border-radius: 10px; margin-bottom: 5px; ".$background;

												} else {

													if ($resans[$i] == 1) {
														$background = "background-color: #7BEF83;";
													} else {
														$background = "background-color: #F39494;";
													}

													$false = "border: 1px solid black; border-radius: 10px; margin-bottom: 5px; ".$background;

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

										<?php elseif ($question_type[$i] == 3) : ?>

											<div>

												<div style="float: left; width: 33%;">
													
													<table class="im-table">

														<tr>
															<th colspan="2">Question</th>
														</tr>

														<?php $abjad = 1; ?>
														<?php foreach ($match_key[$i] as $mk) : ?>

															<tr>
																<td valign="top" width="20"><p><?=$abjad?>.</p></td>
																<td>
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
																</td>
															</tr>

															<?php $abjad++; ?>
														<?php endforeach; ?>
													</table>

												</div>

												<div style="float: left; width: 33%; margin-left: 10px;">

													<table class="im-table">

														<tr>
															<th colspan="2">Key</th>
														</tr>

														<?php $abjad = 1; ?>
														<?php foreach ($match_key[$i] as $mk) : ?>

															<tr>
																<td valign="top" width="20"><p><?=$abjad?>.</p></td>
																<td>
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
																</td>
															</tr>

															<?php $abjad++; ?>
														<?php endforeach; ?>
													</table>

												</div>

												<!-- <table class="im-table" style="margin-left: 20px; float: left; margin-left: 20px;">

													<tr>
														<th colspan="2">Answered</th>
													</tr>

													<?php $abjad = 1; ?>
													<?php foreach ($match_key[$i] as $mk) : ?>

														<tr>
															<td valign="top" width="20"><p><?=$abjad?>.</p></td>
															<td>

																<?=read_text($answer_field_en[$mk])?>
															</td>
														</tr>

														<?php $abjad++; ?>
													<?php endforeach; ?>
												</table> -->

											</div>

										<?php endif?>

										<div style="margin: 10px 0px 0px 20px; font-size: 12pt; font-weight: bold;">
											Result : <?=($resans[$i] == 1 ? "Correct" : "Wrong")?>
										</div>

									</div>
								</div>

							<?php endfor;?>

						</div>
					</div>

				</div>
		</div>
	</div>
</div>