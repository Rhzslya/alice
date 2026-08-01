<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/contents.css')?>">
<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/immu-ui/immu-ui.css')?>">

<style type="text/css">
	th{
		font-weight: normal;
		border:1px solid white;
	}
</style>

<h2 align="center" style="font-family: Arial;">Score Recapitulation Report</h2>

<?php 
	switch ($category) {
	 	case 1:
	 		$cat = "Pra";
	 		break;
	 	case 2:
	 		$cat = "Pasca";
	 		break;
	 	case 3:
	 		$cat = "DP";
	 		break;
	 	default:
	 		$cat = "N/A";
	 		break;
	 }
?>

<?php if (isset($comp)) : ?>
	<table class="im-table">
		<tr>
			<th>Level</th>
			<td width="370"><?=$level." (".$cat.")"?></td>

			<td style="background-color: white;"></td>

			<th>Subject Title</th>
			<td width="500"><?=$period." (".time_format($start_date,'d M Y').")"?></td>

			<td style="background-color: white;"></td>

			<th>UPT</th>
			<td width="350"><?=$upt_name?></td>
		<tr>
	</table>

	<br/>

	<table class="recap-score im-table" border="1" style="">
		<thead repeat_header="50">
			<tr>
				<th width="15" rowspan="2">No.</th>
				<th width="100" rowspan="2">Seafarer Code</th>
				<th width="100" rowspan="2">Participant No.</th>
				<th width="300" rowspan="2">Name</th>
				<?php $this->load->helper('text'); ?>
				<?php foreach ($comp as $co) : ?>
					<th text-rotate="90" style="vertical-align: bottom;"><div><span><?=word_limiter($co['label'],6)?></span></div></th>
				<?php endforeach; ?>
			</tr>			
			<tr>
				<?php foreach ($comp as $co) : ?>
					<th align="center"><?=$co['sequence']?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<?php $i = 1; ?>
		<?php foreach ($part as $part) : ?>
			<tr>
				<td align="center"><?=$i?></td>
				<td align="center"><?=$part['seafarer_code']?></td>
				<td align="center"><?=$part['participant_no']?></td>
				<td><?=$part['full_name']?></td>							
				
				<?php foreach ($comp as $co) : ?>								
					<td align="right">
						<?php
							if (isset($score[$part['seafarer_code']][$co['uc_competency']])) {
								echo $score[$part['seafarer_code']][$co['uc_competency']];
							}
							else {
								echo "-";
							}
						?>
					</td>
				<?php endforeach; ?>
			</tr>

			<?php $i++;?>
		<?php endforeach; ?>
	</table>

	<br/>

	<table style="font-weight: bold; font-size: 12px;">
		<thead>	
			<tr>
				<td>Notes</td>
				<td>:</td>
				<td>UA = UnAttempt</td>
			</tr>
		</thead>
	</table>

<?php else: ?>
	<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
<?php endif ?>