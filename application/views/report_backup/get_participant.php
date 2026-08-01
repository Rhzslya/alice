<?php if (isset($result)): ?>
	<table class="im-table" style="width: 100%">
		<?php foreach ($result as $res): ?>
			<tr>
				<td width="190"><?=$res->seafarer_code?></td>
				<td width="300"><?=$res->full_name?></td>
				<td width="220"><?=($res->born_place != "" ? $res->born_place : "-")?>, <?=time_format($res->born_date, 'd-M-Y');?></td>
				<td width="90"><?=$res->level_name?></td>
				<td>
					<a href="<?=base_url('report/detail_participant/'.$res->seafarer_code)?>"><input type="button" value="Score" class="ui-btn-default" style="margin:0;float: none;"></a>
				</td>
			</tr>
		<?php endforeach ?>
	</table>
<?php else: ?>
	<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
<?php endif ?>