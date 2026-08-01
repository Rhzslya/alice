<?php if (isset($result)): ?>
	<table class="im-table" style="border-collapse: collapse;">
		<tr>
			<th align="center" rowspan="2" width="20">No</th>
			<th align="left" width="80" rowspan="2">Seafarer ID</th>
			<th align="left" width="220" rowspan="2">Full Name</th>
			<th align="left" width="180" rowspan="2">Date Of Birth</th>
			<th align="center" width="70" colspan="2">Score</th>

			<?php if ($this->session->userdata('log_user_category') == 1): ?>
				<th align="center" width="70" rowspan="2">Edit Score</th>
			<?php endif ?>
		</tr>
		
		<tr>
			<th width="250">Competency</th>
			<th>Score</th>
		</tr>	

		<?php $nom = 1; ?>
			<?php for($i = 0; $i < $max; $i++): ?>
				<td align="center"><?=$nom?></td>
					<td align="left"><?=$seafarer_code[$i]?></td>
					<td align="left"><?=$full_name[$i]?></td>
					<td align="left"><?=$born_place[$i]?>, <?=($born_date[$i] != NULL ? time_format($born_date[$i], 'd-M-Y') : "-")?></td>
					<td>
						<ul style="list-style: none;padding: 0">
							<?php $no = 1; ?>
							<?php foreach ($res_comp as $rc): ?>													
							<li>
						
								<label class="label-folder" title="<?=$rc->competency_name?>" style="float: left;"><?=$no?>.<?=$rc->competency_name?></label>
							</li>
								<?php $no++; ?>
							<?php endforeach ?>
						</ul>
								
					</td>
					<td>
						<ul style="list-style: none;padding: 0">
							<?php for($j = 0; $j < count($score[$i]); $j++): ?>													
								<li style="height: 20px;line-height: 30px;">
									<?=$score[$i][$j] ?>
								</li>	
								<?php $no++; ?>
							<?php endfor;?>
						</ul>
					</td>

					<?php if ($this->session->userdata('log_user_category') == 1): ?>
						<td>
							<ul style="list-style: none;padding: 0">
								<?php for($k = 0; $k < count($score[$i]); $k++): ?>
									<li style="height: 20px;line-height: 30px;">
										<?php if ($uc_exam_attempt_competency[$i][$k] != NULL): ?>
											<a href="#" class="btn-edit-score" uc="<?=$uc_exam_attempt_competency[$i][$k]?>">
												Edit Score															
											</a>
										<?php endif ?>
										<?php $no++; ?>
									</li>
								<?php endfor;?>
							</ul>
						</td>
					<?php endif ?>

					<!-- <td align="center">
						<a href="<?=base_url('report/detail_exam_student/'.$res->uc.'/'.$session.'/'.$ea_uc[$i]);?>">
							<input type="button" value="Answer Detail">
						</a>
					</td> -->
				</tbody>
			<?php $nom++ ?>
			<?php endfor;?>
	</table>
<?php else: ?>
	<div class="ui-empty-data"  style="margin-top: 90px;margin-left: 40%">Empty</div>
<?php endif ?>