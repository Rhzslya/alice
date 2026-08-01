<style type="text/css">
	body {
		font-family: Arial;
	}
</style>

<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="<?=base_url('report')?>" class="go-back">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>
<div class="main-pane">
	<div class="title-menu">Score Recapitulation Report</div>
	<div class="main-home">
		<div class="form-area">
			<div class="form-adit" style="padding: 10px;">

				<?php if (isset($result)): ?>
					
					<table style="font-size: 11pt;">
						<tr>
							<td valign="top" width="140"><b>Examination Code</b></td>
							<td width="10" align="center" valign="top">:</td>
							<td valign="top" width="300" style="border-bottom: 1px solid black;"><?=$result[0]->exam_code?></td>
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

					<div style="width: 100%; float: left; margin: 25px 0px 5px 0px;">
						<div style="float: right;padding-right: 5px;padding-left: 5px" class="ui-btn-default">
							<a href="<?=base_url('report/report_pdf/'.$result[0]->uc_exam);?>" title="Export to pdf" style="color:#fff"><img src="<?=base_url('assets/image/icon/ico-pdf.png');?>" width="15" height="15" style="margin-top: 2px;margin-right: 3px;"> Export to PDF</a>
						</div>
					</div>

					<?php if ($result[0]->full_name != NULL): ?>
						<table class="im-table">
							<thead>
								<tr>
									<th width="15" align="center">No</th>
									<th align="left" width="210">Seaferer ID</th>
									<th align="left" width="310">Participant Name</th>
									<th align="left" width="210">Participant No</th>
									<th align="left">Score</th>
									<th align="center" width="75">Detail</th>
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
										<td align="center">
											<a href="<?=base_url('report/detail/'.$res->uc_attempt);?>"><input type="button" value="Detail"></a>
										</td>
									</tr>
								<?php $no++;?>
								<?php endforeach;?>	
							</tbody>
						</table>
					<?php else: ?>
						Empty...
					<?php endif ?>

				<?php else: ?>
					<div class="ui-empty-data" style="margin-top: 15%"><?=label('empty');?></div>
				<?php endif;?>

			</div>
		</div>
	</div>
</div>