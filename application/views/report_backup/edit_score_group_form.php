<div class="pop-style">
	<div class="pop-head">
		<div class="pop-style-title">Edit Score Examination</div>

		<div class="close pop-style-close" title="Close"  onclick="close_pop()"></div>
	</div>

	<?=form_open('report/edit_score_group');?>
		<div class="pop-content">

			<input type="hidden" name="f_uc_period" value="<?=$uc_period?>">
			<input type="hidden" name="f_uc_exam" value="<?=$uc_exam?>">
			<input type="hidden" name="f_uc_competency" value="<?=$uc_competency?>">
			
			<?php if (isset($row)): ?>
				<div style="width: 97%;margin-left: 0px;margin-top:0;height:94px;border:0px">
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
			<?php else: ?>
				Empty information...
			<?php endif ?>

			<?php if (isset($result)): ?>
				<div style="overflow-y: auto; height: 350px; margin-top: 10px;">
					<table class="im-table" width="100%">
						<thead>
							<tr>
								<th width="30">Pick</th>
								<th width="100">Seafarer Code</th>
								<th>Participant No</th>
								<th>Full Name</th>
								<th align="left" width="50">Score</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($result as $res): ?>
								<?php if ($res->is_done != NULL && $res->is_done != 0): ?>
									
									<tr>
										<td>
											<input type="checkbox" name="f_uc_ea[]" value="<?=$res->uc?>" class="check-ea">
										</td>
										<td><?=$res->seafarer_code?></td>
										<td><?=$res->participant_no?></td>
										<td><?=$res->full_name?></td>
										<td>
											<?php if ($setting->value == 2): ?>
												<?=decryptIt($res->score_normal)?>
											<?php elseif ($setting->value == 3): ?>
												<?=decryptIt($res->score_2)?>
											<?php else: ?>
												<?=decryptIt($res->score)?>
											<?php endif ?>
										</td>
									</tr>

								<?php endif ?>
							<?php endforeach ?>
						</tbody>
					</table>
				</div>
			<?php endif ?>
		</div>

		<div style="margin-top: 10px; float: left;font-family: CGFontB;font-size: 13px;color: #222">
			<!-- <input type="checkbox" name="f_pick_all" id="check-all"> Check All -->
			Limit Pick : 30
			
		</div>

		<input type="submit" name="f_process" value="Proceed" class="ui-btn-default">
	<?=form_close();?>
</div>


<script type="text/javascript">
	$(document).ready(function(){

		$('.close').click(function(){

			$('.edit-score-group-form').fadeOut();
			$('#page-blocker').fadeOut();

			return false;
		});


		var limit = 30;
		// For check all
			/*$("#check-all").click(function () {
				$('.check-ea').attr('checked', this.checked);
			});*/

		// For check if any check
			$(".check-ea").click(function(){

				if ($(".check-ea:checked").length > limit) {
					this.checked = false;

					alert('Allowed only '+limit);
				} /*else {

					if($(".check-ea:checked").length == limit) {
						$("#check-all").attr("checked", "checked");
					} else if ($(".check-ea:checked").length < limit) {
						$("#check-all").removeAttr("checked");
					}

				}*/

			});

	});	
</script>