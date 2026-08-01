<!DOCTYPE html>
<html lang="en">
<head>
	<title>CBA-UKP DPKP Module</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?=base_url('assets/third_party/bootstrap-4.5.0/css/bootstrap.min.css')?>">
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/fontawesome-5.13.0/css/all.css')?>">
	<link rel="stylesheet" href="<?=base_url('assets/css/allnew.css');?>">
	<script src="<?=base_url('assets/js/jquery-3.5.1.min.js')?>"></script>
	<script src="<?=base_url('assets/js/popper.min.js')?>"></script>
	<script src="<?=base_url('assets/third_party/bootstrap-4.5.0/js/bootstrap.min.js')?>"></script>
</head>

<style type="text/css">
th.rotated-text {
    height: 275px;
    white-space: nowrap;
    padding: 0 !important;
    font-size: 0.85em;
    font-weight: normal;
}

th.rotated-text > div {
    transform:
        translate(8px, -10px)
        rotate(270deg);
    width: 30px;
}

th.rotated-text > div > span {
    padding: 5px 10px;
}
</style>

<body>

<?php $this->load->helper('text'); ?>

<div class="container-fluid">
	<div class="row bg-danger pt-3 pb-2">
		<div class="col-3 text-center">
		</div>
		<div class="col-5 text-center">
			<h4 class="text-uppercase text-white">Report Adjustment Mode</h4>
		</div>
		<div class="col-4 text-right">
			<a href="<?=base_url('report/recap/'.$uc_period)?>" class="text-white" title="Exit Adjustment Mode"><i class="fa fa-window-close" style="font-size: 1.5em"></i></a>
		</div>
	</div>
	<div class="row mt-3 font-weight-bolder text-primary">
		<div class="col-4"><?=$period?></div>
		<div class="col-6">[<?=$pukp_label?>] <?=$upt_label?></div>
		<div class="col-2">
			<?php
				switch ($category) {
					case '1':
						$cat = "Pra";
						break;
					case '2' :
						$cat = "Pasca";
						break;
					case '3' :
						$cat = "DP";
						break;
					default :
						$cat = "-";			
						break;
				}
			?>
			<?=$level?> (<?=$cat?>)
		</div>
	</div>
	<div class="row mt-2">
		<?php if (isset($comp)) : ?>

			<table class="table table-bordered table-striped" style="font-size: 0.9em">
				<thead>
					<tr class="thead-dark">
						<th rowspan="3" width="15">No.</th>
						<th rowspan="3" width="150">Seafarer Code</th>
						<th rowspan="3" width="200">Participant No.</th>
						<th rowspan="3">Full Name</th>
						<?php foreach ($comp as $co) : ?>
							<th class="rotated-text" width="50"><div><?=word_limiter($co->label,6)?></div></th>
						<?php endforeach; ?>	
					</tr>
					<tr class="thead-dark text-center">
						<?php $no = 1; ?>
						<?php foreach ($comp as $co) : ?>
							<th><?=$no?></th>
							<?php $no++; ?>
						<?php endforeach; ?>
					</tr>
					<tr class="thead-dark text-center">
						<?php foreach ($comp as $co) : ?>
							<th>
								<?php if (isset($exam[$co->uc])) : ?>
									<a href="<?=base_url('report/regenerate_attempt/'.$uc_period."/".$exam[$co->uc])?>" title="Regenerate">
										<i class="fa fa-sync-alt text-warning"></i>
									</a>
									<a href="<?=base_url('report/regenerate_finish/'.$uc_period."/".$exam[$co->uc])?>" title="Finish UF">
										<i class="fa fa-check-circle text-success"></i>
									</a>
								<?php endif; ?>
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody style="font-size: 0.9em">
					<?php $i = 1; ?>
					<?php if (isset($part)): ?>
						<?php foreach ($part as $part) : ?>

							<tr>
								<td class="text-right"><?=$i?></td>
								<td><?=$part['seafarer_code']?></td>
								<td><?=$part['participant_no']?></td>
								<td><?=$part['full_name']?></td>

								<?php foreach ($comp as $co) : ?>								
									<td class="text-center">

										<?php 
											if (isset($score[$part['seafarer_code']][$co->uc])) {
												if (($score[$part['seafarer_code']][$co->uc] == "NA") ||
													($score[$part['seafarer_code']][$co->uc] == "UF") ||
													($score[$part['seafarer_code']][$co->uc] == "UA")
													) {
													$the_score = $score[$part['seafarer_code']][$co->uc];
													$sco_class = "sco-info";
												}
												else {
													$the_score = $score[$part['seafarer_code']][$co->uc];
													$sco_class = "sco-value";
												} 
											}
											elseif (isset($status[$part['seafarer_code']][$co->uc])) {
												if ($status[$part['seafarer_code']][$co->uc] == 1) {
													$the_score = "SL";
													$sco_class = "sco-sl";
												}
												else {
													$the_score = "BL";
													$sco_class = "sco-bl";
												}
											}
											else {
												$the_score = "-";
												$sco_class = "sco-nope";
											}
										?>

										<a href="<?=base_url('allnew/show_answer')?>" target="_blank"><?=$the_score?></a>
									</td>
								<?php endforeach; ?>

								<?php $i++;?>

							</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="<?=count($comp)+4?>">Empty participant...</td>
						</tr>
					<?php endif ?>	
				</tbody>
			</table>

		<?php else: ?>
			Empty
		<?php endif ?>
	</div>

</div>

</body>
</html>