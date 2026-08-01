<script type="text/javascript">
	$(document).ready(function(){
		$('.close').click(function(){

			$('.import-report').fadeOut();
			$('#page-blocker').fadeOut();

			return false;		
		});	

	});	
</script>

<?=form_open_multipart('report/upload', array('id' => 'add-form'))?>
<!--
<input type="hidden" name="f_report_category" value="<?=$category?>">
-->
<div class="pop-style">
	<div class="pop-head">
		<div class="pop-style-title">IMPORT REPORT</div>
		<div class="close pop-style-close" title="Close"  onclick="close_pop()"></div>
	</div>

		<div class="pop-content">
			<table width="100%">
				<tr>
					<td width="15%">File (.cba)</td>
					<td>	
						<input type="file" name="f_file" required="" style="width: 460px;">
					</td>
				</tr>

				<tr>
					<td colspan="2">
						<input type="submit" name="f_save" value="Import" required="TRUE" class="ui-btn-default" onclick="$('.la-loader').css('display','block');">
					</td>
				</tr>
			</table>

		</div>
		
	<?=form_close()?>
</div>