<div class="container-fluid">
	<h1 class="text-uppercase">Report</h1>
</div>

<div class="container-fluid">
	<div class="row">
		<div class="col-6 text-left">
			<h2 class="text-primary">Period Schedule</h2>
		</div>
		<div class="col-6 text-right">
			<a href="" class="btn btn-sm btn-success" data-toggle="modal" data-target="#form_import_report">
				<i class="fa fa-file-import"></i> &nbsp; Import Report
			</a>
			<a href="<?=base_url('report/clear_report')?>" onclick="return confirm('Are you sure want to delete?')" id="btn-clear-report" class="btn btn-sm btn-danger ml-2"><i class="fa fa-trash-alt"></i> &nbsp; Clear All Report</a>
		</div>
	</div>
</div>

<div class="container-fluid" style="width: 98%">
	<div class="row" style="font-size: 0.95rem">
		<?php if(isset($result)):?>
			<table class="table table-striped">
				<thead class="thead-dark">
					<th width="45%">Location</th>
					<th width="20%">Date</th>					
					<th width="20%">Action</th>
					<th width="15%">Export</th>
				</thead>
				<tbody>

					<?php foreach($result as $row):?>
					
						<tr style="font-size: 0.9em">
							<td class="pt-3">
								<?php
									if ($row->upt_label != "") {
										echo $row->upt_label;	
									}
									else {
										echo $row->pukp_label;
									}
								?>
							</td>
							<td class="pt-3">
									<?=($row->date_start != NULL ? time_format($row->date_start, 'd M Y') : "-")?>
									- <?=($row->date_finish != NULL ? time_format($row->date_finish, 'd M Y') : "-")?>
							</td>
							<td>
								<!-- <a href="<?=base_url('report/recap/'.$row->uc);?>" class="btn btn-sm btn-primary" target="_blank">
									<i class="fa fa-table"></i> &nbsp; Recapitulation
								</a> -->								
								<a href="<?=base_url('report/subject/'.$row->uc)?>" class="btn btn-sm btn-primary text-white"><i class="fa fa-file-alt"></i> &nbsp; Subject List</a>
								<a href="" class="btn btn-sm btn-danger"><i class="fa fa-trash-alt"></i> &nbsp; Delete</a>
							</td>
							<td>
								<a href="<?=base_url('report/export_result/'.$row->uc)?>" class="btn btn-sm btn-success mx-4"><i class="fa fa-file-export"></i> &nbsp; Export Result</a>
							</td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		<?php else : ?>			
			Empty
		<?php endif; ?>	
	</div>
</div>


<!-- The Modal -->
<div class="modal fade" id="form_import_report">
	<div class="modal-dialog">
		<div class="modal-content">

			<?=form_open_multipart('report/upload')?>
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Import Report</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<!-- Modal body -->
			<div class="modal-body px-4">
				<div class="form-group">
					<label for="email">File (.cba)</label>
				    <input type="file" name="f_file" width="600" class="form-control">
				</div>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<input type="submit" name="f_save" value="Import" class="btn btn-primary">
			</div>
			<?=form_close()?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('document').ready(function(){
		$('#btn-clear-report').click(function(){
			$('#page-blocker').modal('show');
		});
	});
</script>