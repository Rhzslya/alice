<!DOCTYPE html>
<html lang="en">
<head>
	<title>CBA-UKP - Alice</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?=base_url('assets/third_party/bootstrap-4.5.0/css/bootstrap.min.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/fontawesome-5.13.0/css/all.css')?>">
	<link rel="stylesheet" href="<?=base_url('assets/css/allnew.css');?>">
	<script src="<?=base_url('assets/js/jquery-3.5.1.min.js')?>"></script>
	<script src="<?=base_url('assets/js/popper.min.js')?>"></script>
	<script src="<?=base_url('assets/third_party/bootstrap-4.5.0/js/bootstrap.min.js')?>"></script>
</head>

<style type="text/css">
	.list-group-item{
		list-style-type: lower-latin;
	}
</style>

<body>

<div class="container-fluid">
	<nav class="navbar navbar-expand-sm navbar-dark justify-content-center nav-allnew mb-3" style="background-color: #ff7019">
		<h4>PARTICIPANT ANSWER</h4>
	</nav>

	<div class="row">
		<div class="col-12">
			<table class="table table-borderless">
				<tr>
					<td>
						<b>Seafarer Code </b> &nbsp; &nbsp;
						<?=$row->seafarer_code?>						
					</td>
					<td>
						<b>Participant No. </b>	&nbsp; &nbsp;
						<?=$row->participant_no?>			
					</td>
					<td>
						<b>Full Name </b> &nbsp; &nbsp;
						<?=$row->full_name?>
					</td>					
				</tr>
			</table>
		</div>

		<div class="col-12">
			<table class="table table-bordered">
				<tr>
					<th width="200" class="bg-secondary text-white">Examination Code</th>
					<td><?=$row->exam_code?></td>
					<td rowspan="2" width="100" class="text-center">
						<b>Benar </b> <br /> <h4 class="text-success answer-true"> </h4> 
						 
						<b>Salah </b> <br /> <h4 class="text-danger answer-false"> </h4>
					</td>
					<th class="text-center bg-secondary text-white">
						Score
						<a href="<?=base_url('comeon/form_edit_single/'.$uc_competency.'/'.$uc_exam_attempt)?>" class="ml-3 text-warning"><i class="fa fa-pencil-alt"></i></a>
					</th>
				</tr>
				<tr>
					<th class="bg-secondary text-white">Competency</th>
					<td><?=$row->competency_name?></td>
					<td width="150" class="text-center"><span class="display-4 text-primary font-weight-bold"><?=decryptIt($row->comp_score_normal)?></span></td>
				</tr>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<h3>Questions &amp; Answers</h3>
			
			<?php if ($row->is_done != 0): ?>

				<table cellpadding="20" cellspacing="10">
					<?php
						$jumlah_benar = 0;
						$jumlah_salah = 0;
					?>

					<?php for ($i=0; $i<$max_question; $i++) : ?>

						<tr>
							<td>
								<div class="row mb-4">
									
									<h5 class="col-1 text-right pr-4 pt-3"><?=$i+1?>.</h5>
									<div class="col-11">
										<p>	
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
												
												<?php if ($q_att_type[$i] == "image") : ?>
													<img src="<?=base_url('uploads/question/'.$q_att_file[$i])?>" class="img-thumbnail" alt="">
												<?php endif; ?>	
										</p>
										<?php if ($question_type[$i] == 1) : ?>
											<ol class="list-group">
												<?php

													if ($answers[$i] == "NULL" || $answers[$i] == "" || $answers[$i] == 0) {
														$jumlah_salah++;
													}
												?>
												<?php for ($j=0; $j<$max_option[$i]; $j++) : ?>
													<?php
														//echo "<br /> - O : ".$option_uc[$i][$j]." - A : ".$answers[$i]." - K :	 ".$keys[$i];

														if ($answers[$i] == $option_uc[$i][$j]) {
															if ($answers[$i] == $keys[$i]) {
																//	BENAR
																$answer_color = "bg-success";
																$jumlah_benar++;
															}
															else {
																//	SALAH
																$answer_color = "bg-danger text-white";
																$jumlah_salah++;
															}
														}
														else {
															$answer_color = "";
														}
													?>

													<li class="list-group-item <?=$answer_color?>">
														<?php if ($row->is_language == 1): ?>
															<?php if ($option_text_in[$i][$j] != NULL): ?>
																<?=read_text($option_text_in[$i][$j]);?>
															<?php else: ?>
																<?=read_text($option_text_en[$i][$j]);?>
															<?php endif; ?>
														<?php endif; ?>

														<?php if ($o_att_type[$i][$j] == "image") : ?>
															<img src="<?=base_url('uploads/question/'.$o_att_file[$i][$j])?>" class="img-thumbnail" alt="">
														<?php endif; ?>
														<?php if ($is_correct[$i][$j] == 1) : ?>
															<small><i class="fa fa-key"></i></small>
														<?php endif; ?>	
													</li>
												<?php endfor; ?>
											</ol>
										<?php endif; ?>
									</div
								</div>
							</td>
						</tr>
							
					<?php endfor;  ?>

					<div class="jbenar" style="display: none;"><?=$jumlah_benar?></div>
					<div class="jsalah" style="display: none;"><?=$jumlah_salah?></div>

				</table>

			<?php endif; ?>	
		</div>
		<div class="col-1"></div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		var benar = $('.jbenar').text();
		var salah = $('.jsalah').text();

		$('.answer-true').html(benar);
		$('.answer-false').html(salah);
	});
</script>

</body>
</html>