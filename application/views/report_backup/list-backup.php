
<style type="text/css">
	fieldset{
		margin-bottom: 10px;
	}
</style>

<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="#" class="go-back-disable">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>
<div class="main-pane">
	<div class="title-menu">Examination Report</div>
	<div class="main-home">
		<div class="form-area">
			<div class="form-adit" style="padding: 5px;">

				<!-- <div style="width: 1000px; float: left; margin: 0px 0px 5px 0px;">
					<div style="float: right;padding-right: 5px;padding-left: 5px" class="ui-btn-default">
						<a href="#" title="Export to pdf" style="color:#fff"><img src="<?=base_url('assets/image/icon/ico-pdf.png');?>" width="15" height="15" style="margin-top: 2px;margin-right: 3px;"> Export to PDF</a>
					</div>
				</div> -->				
									
				<?php if (isset($result)): ?>
					<table class="im-table">
						<thead>
							<tr>
								<th>No</th>
								<th width="180">Exam Code</th>
								<th width="125">Level</th>
								<th width="30">Subject Title</th>
								<th width="125">Function</th>			
								<th width="125">Competency</th>
								<th width="90">Package Code</th>
								<th width="90">Report</th>
							</tr>
						</thead>
						<tbody>
							<?php $no = 1;?>
							<?php foreach($result as $res):?>
								<tr>
									<td><?=$no?></td>
									<td><?=$res->exam_code?></td>
									<td><?=$res->level_name?></td>
									<td align="center"><?=$res->periode?></td>
									<td><?=$res->function_name?></td>
									<td><?=$res->competency_name?></td>
									<td><?=($res->code_package != NULL ? $res->code_package : "-")?></td>
									<td align="center">
										<a href="<?=base_url('report/browse/'.$res->uc);?>"><input type="button" value="Rekap"></a>
									</td>
								</tr>
							<?php $no++;?>
							<?php endforeach;?>	
						</tbody>				
					</table>
				<?php else: ?>
					<div class="ui-empty-data" style="margin-top: 15%"><?=label('empty');?></div>
				<?php endif;?>

			</div>
		</div>
	</div>
</div>