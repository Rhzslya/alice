<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').html();

		$('body').on('click', '.page-all a.pagination-ajax', function() {
			var page 			= $(this).attr('title');

			$('.period-content').load(base_url+'report/page_report_answer', { js_page : page });
			$('.la-loader').css('display','none');
			
			return false;
		});

		$('body').on('click', '.import-rep', function() {

			var cat = $(this).attr('cat');

			$('.import-report').fadeIn();
			$('#page-blocker').fadeIn();

			$('.content-import-report').load(base_url+'report/form_import_report',{js_cat : cat});
			
			return false;
		});

		$('.rep-tab a').click(function(){
			$('.la-loader').css('display','block');
		});

		$('.rep').click(function(){
			$('.la-loader').css('display','block');
		});

	});	
</script>

<table class="im-table">
	<tr>
		<th align="left" width="130">Subject Title</th>
		<th align="left" width="150">From</th>
		<th align="left" width="160">Until</th>
		<th align="left" width="450">Location</th>
		<th width="150" align="left">Action</th>
	</tr>
</table>
<?php if(isset($result)):?>
	<div class="result-period" style="height: 345px; overflow-y: scroll;">
		<table class="im-table">

			<?php $no = 1;?>
			<?php foreach($result as $row):?>
				<tr>
					<td align="left" width="130"><?=$row->period?></td>
					<td align="left" width="150"><?=($row->date_start != NULL ? time_format($row->date_start, 'd M Y') : "-")?></td>
					<td align="left" width="150"><?=($row->date_finish != NULL ? time_format($row->date_finish, 'd M Y') : "-")?></td>
					<td align="left" width="450"><?=$row->upt_label." - ".$row->pukp_label?></td>
					<td width="50" align="center">
						<a href="<?=base_url('report/manage/'.$row->uc);?>">
							<input type="button" value="Report" class="ui-btn-default rep" style="margin: 0">
						</a>
					</td>
					<td align="center">
						<a href="<?=base_url('report/delete/'.$row->uc)?>" onclick="return confirm('Are you sure want to delete this Subject Title and all data (examination and result)?')" class="sessdelete">
							<input type="button" title="Delete" class="lc-delete-btn rep" style="margin: 0">
						</a>									
					</td>								
				</tr>
				<?php $no++;?>
			<?php endforeach;?>
		</table>
	</div>
	<div class="bottom-pane">
		<div class="im-pagination page-all" style="float:left; margin-top: 10">
			<?php if (isset($pagination)) : ?>
				<?=$pagination?>
			<?php endif; ?>
		</div>
		<div class="total-pane" style="float:right;width: 183px">
			<div class="total-label-total">Total</div>
			<div class="total-value">
				<?php if(isset($total_record)):?>
					<?=$total_record?>
				<?php else:?>
						-
				<?php endif;?>
			</div>
			<div class="total-label-unit">Subject Title(s)</div>
		</div>
	</div>
<?php else:?>
	<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
<?php endif;?>