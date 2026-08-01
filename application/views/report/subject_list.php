<div class="container-fluid">
	<div class="row">
		<div class="col-11 text-left">
			<h1 class="text-uppercase">Report</h1>
		</div>
		<div class="col-1">
			<a href="<?=base_url('report')?>" title="Package List"><i class="fa fa-arrow-circle-left text-danger text-right mt-2" style="font-size: 2em"></i></a>
		</div>
	</div>			
</div>

<div class="container-fluid">
	<div class="row">
		<div class="col-6 text-left">
			<h2 class="text-primary">Subject List</h2>
		</div>
		<div class="col-6 text-right">
			<h5 class="text-right text-secondary">
				<?=($info->date_start != NULL ? time_format($info->date_start, 'd M Y') : "-")?>
				- <?=($info->date_finish != NULL ? time_format($info->date_finish, 'd M Y') : "-")?>
			</h5>
		</div>
	</div>
</div>

<div class="container-fluid mt-4" style="width: 98%">
	<div class="row">
		<div class="col">
			<?php if (isset($subject)) : ?>
				<table class="table table-striped">
					<thead class="thead-dark">
						<th width="3%">No.</th>
						<th width="42%">Label (UPT)</th>
						<th width="20%">Level - Category - Status</th>
						<th width="20%">Date</th>					
						<th width="15%">Action</th>
					</thead>
					<tbody>
						<?php $i = 1; ?>
						<?php foreach ($subject as $sub) : ?>
						
							<tr style="font-size: 0.9em">
								<td class="pt-2 text-right">
									<?=$i?>.
								</td>
								<td class="pt-2">
									<?=$sub->period?> <br />
									<span class="text-success ml-3" style="font-size: 0.9em"><b><?=$sub->upt_label?> </b></span>
								</td>
								<td class="pt-2">
									<?php 
										switch ($sub->pra_pasca) {
											case 1 :
												$exam_type = "Pra";
												break;
											case 2 :
												$exam_type = "Pasca";
												break;
											case 3 :
												$exam_type = "DP";
												break;	
											default:
												$exam_type = "-";
												break;
										}
										$exam_status 	= ($sub->category == 1 ? "Perdana" : "Mengulang");
									?>
									<?=$sub->level." - ".$exam_type." [".$exam_status."]"?>
								</td>
								<td class="pt-2">
										<?=($sub->date_start != NULL ? time_format($sub->date_start, 'd M Y') : "-")?> - 
										<?=($sub->date_finish != NULL ? time_format($sub->date_finish, 'd M Y') : "-")?>
								</td>
								<td class="pt-3">	
									<a href="<?=base_url('report/recap/'.$sub->uc);?>" class="btn btn-sm btn-primary" target="_blank" title="Recapitulation">
										<i class="fa fa-table"></i> &nbsp; Recapitulation
									</a>
									<a href="#" title="Update Result" class="btn btn-sm btn-warning text-white btn-update-result" data-uc-period="<?=$sub->uc?>">
										<i class="fa fa-recycle"></i>
									</a>
								</td>
							</tr>
							<?php $i++; ?>
						<?php endforeach;?>
					</tbody>
				</table>
			<?php else : ?>
				Empty
			<?php endif; ?>
		</div>
	</div>
</div>

<div class="modal fade" id="form-update-result">
	<div class="modal-dialog">
		<div class="modal-content">

			<?=form_open_multipart('comeon/magic')?>
				<input type="hidden" name="f_uc_period"/>
				<input type="hidden" name="f_redirect" value="<?=$uc_ukp?>" />
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
					<input type="submit" name="f_magic" value="Update" class="btn btn-primary">
				</div>
			<?=form_close()?>
		</div>
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function(){
		$('.btn-update-result').click(function(){
			$('input[name=f_uc_period]').val($(this).attr('data-uc-period'));
			$('#form-update-result').modal('show');
		});
	});
</script>