<script type="text/javascript">
	$(document).ready(function(){

		$('.close').click(function(){

			$('.edit-score').fadeOut();
			$('#page-blocker').fadeOut();

			return false;		
		});

		$('input[name=f_save]').click(function() {
			var old_score = parseInt($('input[name=f_old_score]').val());
			var new_score = parseInt($('select[name=f_score_comp] :selected').val());

			if (new_score == old_score) {
				alert("Sorry can't process a same value.");

				return false;
			} else {
				if (confirm('Are you sure want to change scoring for this student?')) {
					return true;
				} else {
					return false;
				}
			}
		});

	});	
</script>
<div class="pop-style">
	<div class="pop-head">
		<div class="pop-style-title">Edit Score</div>

		<div class="close pop-style-close" title="Close"  onclick="close_pop()"></div>
	</div>

	<?php if (isset($row)): ?>
		<?=form_open('report/update_score_competency')?>
			<input type="hidden" name="f_old_score" value="<?=decryptIt($row->score_normal)?>">
			<input type="hidden" name="f_uc_attempt" value="<?=$row->uc_exam_attempt?>">
			<input type="hidden" name="f_uc_att_comp" value="<?=$row->uc?>">
			<input type="hidden" name="f_uc_exam" value="<?=$uc_exam?>">
			<input type="hidden" name="f_uc_period" value="<?=$uc_period?>">
			<input type="hidden" name="f_uc_competency" value="<?=$row->uc_competency?>">

			<div class="pop-content">
				<table>
					<tr>
						<td>Competency</td>
						<td>:</td>
						<td title="<?=$row->label?>">
							<input type="text" name="f_com" value="<?=$row->label?>" size="49" disabled>
						</td>
					</tr>
					<tr>
						<td>Score</td>
						<td>:</td>
						<td>
							<select name="f_score_comp">
								<option value="">-- Choose --</option>
								<?php $num_value = 0; ?>
								<?php for ($i=0; $i < 21 ; $i++) : ?>

									<?php if ($num_value >= decryptIt($row->score_normal)): ?>
										<option value="<?=$num_value?>" <?=select_set($num_value, decryptIt($row->score_normal));?> ><?=$num_value?></option>
									<?php endif ?>

									<?php $num_value = $num_value + 5; ?>
								<?php endfor; ?>
							</select>
						</td>
					</tr>
				</table>
			</div>

			<input type="submit" name="f_save" value="Update" class="ui-btn-default">
			
		<?=form_close()?>
	<?php else: ?>
		Empty...
	<?php endif ?>
</div>