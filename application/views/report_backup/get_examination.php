<?php if (isset($result)): ?>
	<?php $no = 1;?>
	<?php foreach($result as $res):?>
	<div class="report-xam">
		<div class="rep-left">
			<table>
				<tr>
					<td class="bold" height="25">Exam Code</td>
				</tr>
				<tr>
					<td><?=$res->exam_code?></td>
				</tr>

				<tr>
					<td height="40">
						<a href="<?=base_url('report/browse/'.$res->uc);?>"><input type="button" name="" value="Show Report" class="rep-show-btn"></a>
						
					</td>
				</tr>
			</table>
		</div>

		<div class="rep-center">
			<table>
				<tr>
					<td width="70" class="bold">Level</td>
					<td width="150" height="25"><?=$res->level_name?></td>
					<td width="70" class="bold">Function</td>
					<td><?=$res->function_name?></td>
					<td></td>
				</tr>

				<tr>
					<td colspan="5" class="bold">Competency</td>
				</tr>
				<tr>
					<td colspan="5">
					<?=$res->competency_name?>
					</td>
				</tr>
			</table>
		</div>

		<div class="rep-right">
			<table>
				<tr>
					<td class="bold" height="25">Subject Title</td>
				</tr>

				<tr>
					<td><?=$res->periode?></td>
				</tr>

				<tr>
					<td class="bold">Date</td>
				</tr>

				<tr>
					<td>-</td>
				</tr>
			</table>
		</div>
	</div>
	<?php $no++;?>
	<?php endforeach;?>	
<?php else: ?>
	<div class="ui-empty-data" style="margin-top: 15%"><?=label('empty');?></div>
<?php endif;?>