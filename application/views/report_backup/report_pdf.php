<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/immu-ui/immu-ui.css')?>">

<h2 align="center" style="font-family: Arial;">Score Recapitulation Report</h2>

<?php if (isset($result)): ?>
	<table style="font-size: 9pt; margin-bottom: 15px; font-family: Arial; margin-top: 30px">
		<tr>
			<td valign="top" width="140"><b>Examination Code</b></td>
			<td width="10" align="center" valign="top">:</td>
			<td valign="top" width="200" style="border-bottom: 1px solid black;"><?=$result[0]->exam_code?></td>
			<td width="10">&nbsp;</td>
			<td valign="top" width="100"><b>Package</b></td>
			<td width="10" align="center" valign="top">:</td>
			<td valign="top" style="border-bottom: 1px solid black;"><?=($result[0]->code_package != NULL ? $result[0]->code_package : "-")?></td>
		</tr>
		<tr>
			<td valign="top"><b>Subject Title</b></td>
			<td valign="top" align="center">:</td>
			<td valign="top" style="border-bottom: 1px solid black;"><?=$result[0]->periode?></td>
			<td width="10">&nbsp;</td>
			<td valign="top" width="140"><b>Examination Date</b></td>
			<td valign="top" align="center">:</td>
			<td valign="top" style="border-bottom: 1px solid black;"><?=time_format($result[0]->time_exam, 'd M Y')?></td>
		</tr>
		<tr>
			<td colspan="7" height="20">&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" height="20"><b>Level</b></td>
			<td align="center" valign="top">:</td>
			<td style="border-bottom: 1px solid black;"><?=$result[0]->level_name?></td>
			<td width="10">&nbsp;</td>
			<td valign="top"><b>Function</b></td>
			<td valign="top" align="center">:</td>
			<td valign="top" style="border-bottom: 1px solid black;"><?=$result[0]->function_name?></td>
		</tr>
		<tr>
			<td valign="top"><b>Competency</b></td>
			<td valign="top" align="center">:</td>
			<td colspan="5" valign="top"><u><?=$result[0]->competency_name?></u></td>
		</tr>
	</table>			
					
	<table class="im-table" style="font-family: Arial; margin-top: 30px;">
		<thead>
			<tr>
				<th width="15" align="center">No</th>
				<th align="left" width="210">Seaferer ID</th>
				<th align="left" width="310">Participant Name</th>
				<th align="left" width="210">Participant No</th>
				<th align="left">Score</th>
			</tr>
		</thead>
		<tbody>
			<?php $no = 1;?>
			<?php foreach($result as $res):?>
				<tr>
					<td align="right"><?=$no;?></td>
					<td align="left"><?=$res->seafarer_code?></td>
					<td align="left"><?=$res->full_name?></td>
					<td align="left"><?=$res->participant_no?></td>
					<td align="right"><?=($res->score != NULL ? $res->score : "-")?></td>
				</tr>
			<?php $no++;?>
			<?php endforeach;?>	
		</tbody>				
	</table>
<?php else: ?>
	Empty...
<?php endif;?>