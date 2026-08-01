<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/immu-ui/immu-ui.css')?>">

<h2 align="center" style="font-family: Arial;">Score Recapitulation Report</h2>

<?php if (isset($result)): ?>
	
	<table width="850" style="font-size: 13pt; font-family: CGFont;">
		<tr>
			<th align="left" width="150">Level</th>
			<td><?=$level?></td>
		</tr>
		<tr>
			<th align="left">Function</th>
			<td><?=$function_name?></td>
		</tr>
		<tr>
			<th align="left">Competency</th>
			<td><?=$competency_name?></td>
		</tr>
	</table>
	<hr />
	<table class="im-table" style="font-size: 12pt; font-family: CGFont;">
		<tr>
			<th width="10" align="right" style="font-size: 12pt;">No.</th>
			<th width="200" align="left" style="font-size: 12pt;">Seafarer Code</th>
			<th width="200" align="left" style="font-size: 12pt;">Participant No.</th>
			<th width="500" align="left" style="font-size: 12pt;">Full Name</th>
			<th width="50" align="right" style="font-size: 12pt;">Score</th>
		</tr>
		<?php $i = 1; ?>
		<?php foreach ($result as $res) : ?>
			<tr>
				<td align="right"><?=$i?>.</td>
				<td><?=$res->seafarer_code?></td>
				<td><?=$res->participant_no?></td>
				<td><?=$res->full_name?></td>
				<td align="right">
					<?php if ($res->score == NULL) : ?>
						<span class="score-red" style="color: #FF0000">Unattempt</span>
					<?php elseif ($res->is_done != 1) : ?>
						<span class="score-red">Unfinish</span>
					<?php else : ?>
						<!-- <span><?=$this->encrypt->decode($res->score)?></span> -->
						<span><?=$res->score?></span>
					<?php endif; ?>
				</td>
			</tr>

			<?php $i++; ?>
		<?php endforeach; ?>	
	</table>

<?php else: ?>
	Empty...
<?php endif;?>