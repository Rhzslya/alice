<?=form_open_multipart('report/score')?>
	<div class="pop-style">
		<div class="pop-head">
			<div class="pop-style-title">Filter Tingkat</div>

			<div class="close pop-style-close" title="Close"  onclick="close_pop()"></div>
		</div>

		<div class="pop-content">		
			<table>
				<tr>
					<td width="100">PUKP</td>
					<td>
						<select name="f_pukp" style="width:250px;" required="TRUE">
							<option value="">--- Choose ---</option>
							<?php $list_pukp = list_pukp(); ?>
							<?php if (isset($list_pukp)): ?>
								<?php foreach ($list_pukp as $lp) : ?>

									<option value="<?=$lp->uc?>"><?=$lp->pukp_label?></option>

								<?php endforeach; ?>	
							<?php endif ?>
						</select>						
					</td>
				</tr>
				<tr>
					<td width="100">UPT</td>
					<td>
						<select name="f_upt" style="width:250px;" required="TRUE">
							<option value=""->--- Choose ---</option>
						</select>						
					</td>
				</tr>
				<tr>
					<td width="100">Level</td>
					<td>
						<select name="f_level" style="width:250px;" required="TRUE">
							<option value="">--- Choose ---</option>
							<?php $list_level = list_level(); ?>
							<?php if (isset($list_level)): ?>
								<?php foreach ($list_level as $lv) : ?>

									<option value="<?=$lv->uc?>"><?=$lv->label?></option>

								<?php endforeach; ?>
							<?php endif ?>
						</select>	
					</td>
				</tr>
				<tr>
					<td width="100">Diklat</td>
					<td>
						<input type="radio" name="f_diklat" value="1" checked="TRUE"> Pembentukan
						<input type="radio" name="f_diklat" value="2"> Peningkatan
					</td>
				</tr>
				<tr>
					<td width="100">&nbsp;</td>
					<td class="cat-pembentukan">
						<input type="radio" name="f_pra_pasca" value="1" checked="true"> Pra
						<input type="radio" name="f_pra_pasca" value="2"> Pasca
					</td>
				</tr>
			</table>
		</div>

		<div style="margin-top: 185px; position: fixed; margin-left: 278px;">
			<input type="submit" name="f_proccess" value="Proccess" required="TRUE" class="ui-btn-default">
		</div>
	</div>
<?=form_close()?>

<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').html();

		// Close popup filter participant
			$('.close').click(function(){
				$('.report-score').fadeOut();
				$('#page-blocker').fadeOut();
			});

		$('input[name=f_diklat]').change(function(){
			var val_diklat = $(this).val();

			if (val_diklat != 1){

				$('.cat-pembentukan').hide();
			}
			else{
				$('.cat-pembentukan').show();
			}
		});

		$('select[name=f_pukp]').change(function(){
			var uc_pukp = $(this).val();

			$('select[name=f_upt]').load(base_url+'report/load_upt',{ js_uc_pukp : uc_pukp });

			return false;
		});
	});	
</script>