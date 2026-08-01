<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').html();

		$('.pop-style-close').click(function(){
			$('#page-blocker').fadeOut();
			$('.export-backup').fadeOut();
		});
		
	});
</script>

<div class="pop-style">
	<div class="pop-head">
		<div class="pop-style-title">BACKUP PER DAY</div>

		<div class="close pop-style-close" title="Close"></div>
	</div>
	<div class="pop-content">
		<?=form_open('Report/backup', array('id' => 'add-form', 'autocomplete' => 'off'))?>
			<input type="hidden" name="f_uc_period" value="<?=$uc_period?>">
			<input type="hidden" name="f_uc_day" value="<?=$uc_day?>">
			<input type="hidden" name="f_hari" value="<?=$hari?>">
				<table style="font-family: CGFont; font-size: 9pt; width: 510px;">
					<tr>
						<td>Date</td>
						<td>:</td>
						<td><input type="date" name="f_date" required=""></td>
					</tr>
				</table>
			<div align="right">
				<input type="submit" name="f_save" value="Save" class="ui-btn-default">
			</div>
		<?=form_close()?>
	</div>
</div>
