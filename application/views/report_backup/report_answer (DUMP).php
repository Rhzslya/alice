<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').html();

		$('body').on('click', '.page-all a.pagination-ajax', function() {
			var page 			= $(this).attr('title');

			$('.period-content').load(base_url+'report/page_report_answer', { js_page : page });
			$('.la-loader').css('display','none');
			
			return false;
		});

		$('body').on('click', '.import-rep', function() {

			var cat = $(this).attr('cat');

			$('.import-report').fadeIn();
			$('#page-blocker').fadeIn();

			$('.content-import-report').load(base_url+'report/form_import_report',{js_cat : cat});
			
			return false;
		});

		$('.rep-tab a').click(function(){
			$('.la-loader').css('display','block');
		});

		$('.rep').click(function(){
			$('.la-loader').css('display','block');
		});

	});	
</script>

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
	<div class="title-menu" style="width: auto;">Report Answer</div>
	<div class="main-home">

		<div class="subtile" style="width: 1082px">
			<div class="sub-dp">
				<div class="dp-nm">
					<div class="filter-field">
						<div class="list-tabs">
							<a href="<?=base_url('report')?>"  title="Score">
								Score
							</a>
							|
							<a href="<?=base_url('report/report_answer')?>" title="Report" class="tab-current">
								Report
							</a>
						</div>
					</div>
				</div>
			</div>
			
			<a href="#"><input type="button" class="ui-btn-default import-rep" cat="<?=$category?>" value="Import Report" name="" style=""></a>	

			<?php if ($category == 2): ?>
				<a href="<?=base_url('report/clear_report')?>" onclick="return confirm('Are you sure want to delete?')" ><input type="button" class="ui-btn-default clear-rep" value="Clear All Report" name="" style=""></a>
			<?php endif ?>
		</div>
		
		<div class="period-content">
			<table class="im-table">
				<tr>
					<th align="left" width="130">Subject Title</th>
					<th align="left" width="150">From</th>
					<th align="left" width="160">Until</th>
					<th align="left" width="450">Location</th>
					<th width="150" align="left">Action</th>
				</tr>
			</table>
			<?php if(isset($result)):?>
				<div class="result-period" style="height: 345px; overflow-y: scroll;">
					<table class="im-table">

						<?php $no = 1;?>
						<?php foreach($result as $row):?>
							<tr>
								<td align="left" width="130"><?=$row->period?></td>
								<td align="left" width="150"><?=($row->date_start != NULL ? time_format($row->date_start, 'd M Y') : "-")?></td>
								<td align="left" width="150"><?=($row->date_finish != NULL ? time_format($row->date_finish, 'd M Y') : "-")?></td>
								<td align="left" width="450"><?=$row->upt_label." - ".$row->pukp_label?></td>
								<td width="200" align="center">
									<a href="<?=base_url('report/manage/'.$row->uc);?>">
										<input type="button" value="Report" class="ui-btn-default rep" style="margin: 0">
									</a>
								</td>
								<td align="center">
									<a href="<?=base_url('report/delete/'.$row->uc)?>" onclick="return confirm('Are you sure want to delete this Subject Title and all data (examination and result)?')" class="sessdelete">
										<input type="button" title="Delete" class="lc-delete-btn rep" style="margin: 0">
									</a>									
								</td>								
							</tr>
							<?php $no++;?>
						<?php endforeach;?>
					</table>
				</div>
				<div class="bottom-pane">
					<div class="im-pagination page-all" style="float:left; margin-top: 10">
						<?php if (isset($pagination)) : ?>
							<?=$pagination?>
						<?php endif; ?>
					</div>
					<div class="total-pane" style="float:right;width: 183px">
						<div class="total-label-total">Total</div>
						<div class="total-value">
							<?php if(isset($total_record)):?>
								<?=$total_record?>
							<?php else:?>
									-
							<?php endif;?>
						</div>
						<div class="total-label-unit">Subject Title(s)</div>
					</div>
				</div>
			<?php else:?>
				<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
			<?php endif;?>
		</div>

	</div>
</div>

<div class="pop-up-form-add add-pukp" style="height: 135px;width: 400px;z-index: 2">
	<div class="content-add-pukp">
		
	</div>
</div>

<div class="pop-up-form-add import-report" style="height: 105px;width: 655px;z-index: 2">
	<div class="content-import-report">
		
	</div>
</div>

<div id="page-blocker"></div>