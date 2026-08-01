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
    font-size: 0.75em;
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
	<div class="row bg-light pt-3 pb-2">
		<div class="col-3 text-center">
			<a href="" class="btn btn-sm btn-outline-danger"><i class="fa fa-file-pdf"></i> &nbsp; Export PDF</a>
			<a href="<?=base_url('report/recap/'.$uc_period.'/excel')?>" class="btn btn-sm btn-outline-success"><i class="fa fa-file-excel"></i> &nbsp; Export Excel</a>
		</div>
		<div class="col-5 text-center">
			<h4 class="text-uppercase">Recapitulation Report</h4>
		</div>
		<div class="col-4 text-right">
			<?php if ($this->session->userdata('log_user_category') == 1) : ?>
				<a href="" title="Update Result" class="btn btn-sm btn-warning text-white" data-toggle="modal" data-target="#form_update_result">
					<i class="fa fa-recycle"></i>
				</a>
			<?php endif; ?>	
			<a href="<?=base_url('report/recap/'.$uc_period.'/adjust')?>" title="Adjustment Mode" class="btn btn-sm btn-danger"><i class="fa fa-bars"></i></a>
			<a href="<?=base_url('report/export_result/'.$uc_period)?>" class="btn btn-sm btn-primary mx-4"><i class="fa fa-file-export"></i> &nbsp; Export Result</a>
		</div>
	</div>
	<div class="row mt-3 font-weight-bolder text-dark">
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
						<th rowspan="2" width="15">No.</th>
						<th rowspan="2" width="150">Seafarer Code</th>
						<th rowspan="2" width="200">Participant No.</th>
						<th rowspan="2">Full Name</th>
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

<div class="modal fade" id="form_update_result">
	<div class="modal-dialog">
		<div class="modal-content">

			<?=form_open_multipart('report/regenerate_result')?>
				<input type="hidden" name="f_uc_period" value="<?=$uc_period?>" />
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">Update Result</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<!-- Modal body -->
				<div class="modal-body px-4">
					<div class="form-group">
						<label for="email">File (.xls)</label>
					    <input type="file" name="f_file" width="600" class="form-control">
					</div>
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<input type="submit" name="f_update" value="Update" class="btn btn-primary">
				</div>
			<?=form_close()?>
		</div>
	</div>
</div>

</body>
</html>