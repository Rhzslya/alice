<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/contents.css')?>">
<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/immu-ui/immu-ui.css')?>">

<div class="main-pane">
	<div class="main-home">
		<div class="form-area">

			<?php if (isset($row)): ?>
				<div class="rep-part-info" style="margin-bottom: 25px;">
					<table>
						<tr>
							<td style="font-size: 17px;"><b>Seafarer ID</b></td>
							<td width="15">:</td>
							<td style="font-size: 17px;"><?=$row->seafarer_code?></td>
						</tr>
						<tr>
							<td style="font-size: 17px;"><b>Full Name</b></td>
							<td width="15">:</td>
							<td style="font-size: 17px;"><?=$row->full_name?></td>
						</tr>
						<tr>
							<td style="font-size: 17px;"><b>Level</b></td>
							<td width="15">:</td>
							<td style="font-size: 17px;"><?=$row->level_name?></td>
						</tr>
					</table>
				</div>
			<?php endif ?>

			<!-- <div class="rep-part-view" style="height: auto;"> -->
				<?php if (isset($function)): ?>
					<?php for ($i=0; $i <count($function) ; $i++) : ?>

						<!-- <div class="rep-function"> -->
							<h3><?=$function[$i]['function_name']?></h3>
							<div class="rep-competency">
								<div class="rep-row" style="font-size: 12pt;">							
									<div class="rep-col-1" style="font-family: CGFontb">Competency</div>
									<div class="rep-col-2" style="font-family: CGFontb">Final Score</div>
								</div>

								<?php if (isset($competency)): ?>
									<?php for ($j=0; $j <count($competency[$i]) ; $j++) : ?>
										<div class="rep-row-thin">
											<div class="rep-col-1">
												<label style="font-size: 11pt;">
													<?=$competency[$i][$j]['competency_name']?>
												</label>

												<table class="im-table tab-<?=$i?>-<?=$j?>">
													<tr>
														<th>Exam Code</th>
														<th>Subject Title</th>
														<th>Date</th>
														<th>Score</th>
													</tr>
													<?php if (isset($examination)): ?>
														<?php for ($k=0; $k <count($examination[$i][$j]) ; $k++) : ?>
															<tr>
																<td><?=$examination[$i][$j][$k]['exam_code']?></td>
																<td><?=$examination[$i][$j][$k]['periode']?></td>
																<td><?=($examination[$i][$j][$k]['time_exam'] != NULL ? time_format($examination[$i][$j][$k]['time_exam'], 'd-M-Y') : "-")?></td>
																<td><?=$examination[$i][$j][$k]['score']?></td>
															</tr>
														<?php endfor; ?>
													<?php endif ?>
												</table>
											</div>
											<div class="rep-col-2" style="font-size: 17px;"><?=$competency[$i][$j]['max_score']?></div>
										</div>
									<?php endfor; ?>
								<?php endif ?>

							</div>
						<!-- </div> -->

					<?php endfor; ?>
				<?php endif ?>

			<!-- </div> -->
		</div>
	</div>

</div>