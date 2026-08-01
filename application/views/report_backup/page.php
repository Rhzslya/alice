<table class="im-table">
	<tr>
		<?php if ($category == 1): ?>
			<th align="left" width="130">Subject Title</th>
		<?php elseif($category == 2): ?>
			<th align="left" width="130">PUKP</th>
		<?php endif ?>

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
					<?php if ($category == 1): ?>
						<td align="left" width="130"><?=$row->period?></td>
					<?php elseif($category == 2): ?>	
						<td align="left" width="130"><?=$row->pukp_label?></td>
					<?php endif ?>
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
		<?php if ($category == 1): ?>					
			<div class="im-pagination page-all" style="float:left; margin-top: 10">
		<?php elseif ($category == 2): ?>					
			<div class="im-pagination page-all-score" style="float:left; margin-top: 10">
		<?php endif ?>
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