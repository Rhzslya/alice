<div class="container-fluid">
	<h1 class="text-uppercase">Report</h1>
</div>

<div class="container-fluid">
	<div class="row">
		<div class="col-6 text-left">
			<h2 class="text-primary">Period Schedule</h2>
		</div>
	</div>
</div>

<div class="container-fluid" style="width: 98%">
	<div class="row" style="font-size: 0.95rem">

		<div class="load-page-subject col-11 mt-1" style="margin-left: 66px">
			

			<?php if(isset($result)): ?>
				<?php
					$schedule_label = "";

					if ($row->upt_label != NULL ) {
						$schedule_label .= $row->upt_label;
					}
					else {
						$schedule_label .= $row->pukp_label;
					}
				
				
					$row->date_start != NULL ? time_format($row->date_start, 'd M Y') : "-";
					$row->date_finish != NULL ? time_format($row->date_finish, 'd M Y') : "-";

					$schedule_label .= " [".$row->date_start." - ".$row->date_finish."]"

				?>				
				<div class="row mborder">
					<table class="table table-striped table-hover small period-list">
						<thead class="bg-dark text-white">
							<tr>
								<th>Day</th>
								<th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1; ?>
							<?php foreach($result as $res): ?>
								<tr>
									<td><?=$i?></td>
									<td><?=time_format($res->date, 'd M Y')?></td>
									<td>
										<a href="<?=base_url('schedule/export_daily/'.$uc_ukp.'/'.$res->date)?>" class="btn btn-primary btn-sm">
											<i class="fa fa-database"></i> &nbsp; Export Result
										</a>
										<!--
										<a href="<?=base_url('schedule/export_status_daily/'.$uc_ukp.'/'.$res->date)?>" class="btn btn-success btn-sm">
											<i class="fa fa-database"></i> &nbsp; Export Status
										</a>
									-->
										<a href="<?=base_url('schedule/clear_daily/'.$uc_ukp.'/'.$res->date)?>" class="btn btn-danger btn-sm ml-4" onclick="return confirm('Are you sure want to CLEAR ALL ATTEMPT DATA for\n\nSCHEDULE \n<?=$schedule_label?>\n\nDATE \n<?=time_format($res->date, 'd M Y')?>')">
											<i class="fa fa-database"></i> &nbsp; Clear Result
										</a>
										<!--
										<a href="" class="btn btn-warning btn-sm ml-4">
											<i class="fa fa-database"></i> &nbsp; Clear Package
										</a>
										-->
									</td>
								</tr>
								<?php $i++; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php else : ?>
				<h5 class="text-center mt-3"><span class="badge badge-danger">Empty</span></h5>
			<?php endif; ?>	

		</div>


	</div>	
</div>