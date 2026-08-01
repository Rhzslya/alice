<table class="im-table" width="1085">
	<tr>
		<th width="20" align="center">No.</th>
		<th width="300">Seafarer_code</th>
		<th width="300">Full Name</th>
		<th width="200">Birthday</th>
		<th width="90">Action</th>
	</tr>
</table>
<?php if (isset($result)): ?>
	<div class="result-user" style="height: 350px; overflow-y: auto;">	
		<table class="im-table">
			<?php $no = $numbering;?>
			<?php foreach($result as $res):?>
				<tr>
					<td width="20"><?=$no?></td>
					<td width="300"><?=$res->seafarer_code?></td>
					<td width="300"><?=$res->full_name?></td>
					<td align="center" width="200"><?=($res->born_place != "" ? $res->born_place : "-")?>, <?=($res->born_date != NULL ? time_format($res->born_date, 'd-m-Y') : "-")?></td>
					<td align="center" width="90">
						<a href="<?=base_url('participant_master/edit/'.$res->uc);?>"><input class="lc-edit-btn" type="button"></a>
						<a href="<?=base_url('participant_master/delete/'.$res->uc);?>" onclick="return confirm('Are you sure want to delete?');"><input class="lc-delete-btn" type="button"></a>
					</td>
				</tr>
			<?php $no++;?>
			<?php endforeach;?>
		</table>				
	</div>
	<div class="total-pane" style="float:right;width: 190px">
		<div class="total-label-total">Total</div>
		<div class="total-value">
			<?php if(isset($total_record)):?>
				<?=$total_record?>
			<?php else:?>
					-
			<?php endif;?>
		</div>
		<div class="total-label-unit">User(s)</div>
	</div>

	<div class="im-pagination page-all">
		<?php if (isset($pagination)) : ?>
			<?=$pagination?>
		<?php endif; ?>
	</div>				
<?php else: ?>
				<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
	
<?php endif ?>