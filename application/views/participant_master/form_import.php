<script type="text/javascript">
	$(document).ready(function(){
		$('.close').click(function(){

			$('.import-participant').fadeOut();
			$('#page-blocker').fadeOut();

			return false;		
		});

		$('#add-form').validate({
		rules   : {
			f_file    	 		: { required : true }
		
		},
			messages : {
			f_file     		: { required : "<div class='valid-error' style='margin-top:3px;'>Harap attach file !</div>" }
		}
		});

	});	
</script>

<?=form_open_multipart('participant_master/import', array('id' => 'add-form'))?>
<div class="pop-style">
	<div class="pop-head">
		<div class="pop-style-title">IMPORT PARTICIPANT</div>

		<div class="close pop-style-close" title="Close"  onclick="close_pop()"></div>
	</div>

		<div class="pop-content">
			<table>
				<tr>
					<td style="width:250px;">Select File</td>
				</tr>

				<tr>
					<td>
						<input type="file" name="f_file"/>
					</td>
				</tr>
			</table>
		</div>
		<div style="margin-top: 135px; position: fixed; margin-left: 315px;">
			<input type="submit" name="f_save" value="Import" required="TRUE" class="ui-btn-default">
		</div>
	<?=form_close()?>
</div>