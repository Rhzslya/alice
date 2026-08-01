<!-- <link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/immu-ui/immu-ui.css')?>"> -->

<style>
	.report-table th, .report-table td {
	    border: 1px solid black;
	    padding: 5px;
	}
</style>

<h2 align="center" style="font-family: Arial;">Score Recapitulation Report</h2>

<?php if (isset($result)): ?>
	<table style="margin: 30px 0px 20px 0px; font-family: Arial;">
		<tr>
			<td width="130">Examination Code</td>
			<td width="160" style="border-bottom: 1px solid black"><?=$result[0]->exam_code?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td width="70" align="center">Subject Title</td>
			<td width="80" style="border-bottom: 1px solid black"><?=$result[0]->period?></td>
			<td width="60" align="center">Date</td>
			<td width="110" style="border-bottom: 1px solid black"><?=($result[0]->date != NULL ? time_format($result[0]->date, 'd M Y') : "-")?></td>
			<td width="70" align="center">Session</td>
			<td width="80" style="border-bottom: 1px solid black"><?=(isset($session) ? $session : "-")?></td>
		</tr>
		<tr>
			<td height="40" valign="bottom">Level</td>
			<td valign="bottom" style="border-bottom: 1px solid black"><?=$result[0]->level_name?></td>
			<td width="100" valign="bottom" align="center">Function</td>
			<td width="120" valign="bottom" style="border-bottom: 1px solid black"><?=$result[0]->function_name?></td>
		</tr>
		<tr>
			<td height="65" valign="top"><div style="margin-top: 12px">Competency</div></td>
			<td colspan="9">
				<?php if (isset($res_comp)): ?>
					<?php foreach ($res_comp as $res): ?>

						<div style="margin-top: 12px;"><?=$res->sequence?>. <?=$res->competency_name?></div>

					<?php endforeach ?>
				<?php endif ?>
			</td>
		</tr>
	</table>

	<table class="report-table" style="border-collapse: collapse;">
		<tr>
			<th align="center" rowspan="2" width="20">No</th>
			<th align="left" width="80" rowspan="2">Seafarer ID</th>
			<th align="left" width="220" rowspan="2">Full Name</th>
			<th align="left" width="180" rowspan="2">Date Of Birth</th>
			<th align="center" width="70" colspan="2">Score</th>
		</tr>		
		<tr>
			<th width="250">Competency</th>
			<th>Score</th>
		</tr>
		<?php if (isset($max)): ?>
			<?php for ($i=0; $i < $max ; $i++) : ?>

				<?php if (count($competency_name[$i]) > 1): ?>
					
					<tr>
						<td align="center" valign="top" rowspan="<?=count($competency_name[$i])?>"><?=$i+1?></td>
						<td align="left" valign="top" rowspan="<?=count($competency_name[$i])?>"><?=$seafarer_code[$i]?></td>
						<td align="left" valign="top" rowspan="<?=count($competency_name[$i])?>"><?=$full_name[$i]?></td>
						<td align="left" valign="top" rowspan="<?=count($competency_name[$i])?>"><?=$born_place[$i]?>, <?=($born_date[$i] != NULL ? time_format($born_date[$i], 'd-M-Y') : "-")?></td>
						<td>
							<?php if ($competency_name[$i][0] != NULL): ?>
								<label>1. <?=$competency_name[$i][0]?></label>
							<?php else: ?>
								<div align="center">
									-
								</div>
							<?php endif ?>
						</td>
						<td>
							<?php if ($competency_name[$i][0] != NULL): ?>
								<label><?=$score[$i][0]?></label>
							<?php else: ?>
								<div align="center">
									-
								</div>
							<?php endif ?>
						</td>
					</tr>
					<?php for($k = 1; $k < count($score[$i]); $k++): ?>
						<tr>
							<td>
								<?php if ($competency_name[$i][$k] != NULL): ?>
									<label><?=$k+1?>. <?=$competency_name[$i][$k]?></label>
								<?php else: ?>
									<div align="center">
										-
									</div>
								<?php endif ?>
								</ul>
							</td>
							<td>
								<?php if ($competency_name[$i][$k] != NULL): ?>
									<label><?=$score[$i][$k]?></label>
								<?php else: ?>
									<div align="center">
										-
									</div>
								<?php endif ?>
							</td>
						</tr>
					<?php endfor;?>

				<?php else: ?>

					<tr>
						<td align="center"><?=$i+1?></td>
						<td align="left"><?=$seafarer_code[$i]?></td>
						<td align="left"><?=$full_name[$i]?></td>
						<td align="left"><?=$born_place[$i]?>, <?=($born_date[$i] != NULL ? time_format($born_date[$i], 'd-M-Y') : "-")?></td>
						<td>
							<ul style="list-style: none;padding: 0">
								<?php for($k = 0; $k < count($score[$i]); $k++): ?>
									<li style="height: 20px;line-height: 30px;">
										<?php if ($competency_name[$i][$k] != NULL): ?>
											<label><?=$k+1?>. <?=$competency_name[$i][$k]?></label>
										<?php else: ?>
											<div align="center">
												-
											</div>
										<?php endif ?>
									</li>
								<?php endfor;?>
							</ul>
						</td>
						<td>
							<ul style="list-style: none;padding: 0">
								<?php for($j = 0; $j < count($score[$i]); $j++): ?>
									<li style="height: 20px;line-height: 30px;">
										<?=$score[$i][$j] ?>
									</li>
								<?php endfor;?>
							</ul>
						</td>
					</tr>

				<?php endif ?>

			<?php endfor; ?>
		<?php endif ?>
	</table>
<?php else: ?>
	Empty...
<?php endif;?>