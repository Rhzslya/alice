<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').html();

		$('body').on('click', '.page-all a.pagination-ajax', function() {
			var page 			= $(this).attr('title');
			var uc_pukp 		= $('select[namef_pukp]').val();

			$('.period-content').load(base_url+'report/page', { js_page : page, js_uc_pukp : uc_pukp });
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

		$('body').on('click', '#import-rep-score', function() {

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
	<div class="title-menu" style="width: auto;">Report Score</div>
	<div class="main-home">

		<div class="subtile" style="width: 1082px">
			<div class="sub-dp">
				<div class="dp-nm">
					<div class="filter-field">
						<div class="list-tabs">
							<a href="<?=base_url('report')?>" title="Answer">
								Answer
							</a>
							|
							<!-- <a href="#"  title="Score" id="import-rep-score" cat="2"> -->
							<a href="<?=base_url('report/report_score')?>" title="Score" class="tab-current">
								Score
							</a>
						</div>
					</div>
				</div>
			</div>
			
			<div class="combow" style="margin-left: 10px; margin-top: 7px;">
				<div class="selected" style="margin-right: -5px;">
					<select name="f_pukp" style="width:200px;">
						<option value="">--- PUKP ---</option>
						<?php $pukp = list_pukp(); ?>
						<?php if ($pukp != NULL): ?>
							<?php foreach ($pukp as $pu): ?>
								<option value="<?=$pu->uc?>" ><?=$pu->pukp_label?></option>
							<?php endforeach ?>
						<?php endif ?>
					</select>
				</div>

				<input name="btn_filter_question" class="filter-question" value="Filter" type="button" style="float: left">
			</div>
			
			<a href="#"><input type="button" class="ui-btn-default import-rep" cat="1" value="Import Report" name="" style=""></a>
			
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
								<td width="50" align="center">
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

<div class="pop-up-form-add import-report" style="height: 145px;width: 655px;z-index: 2">
	<div class="content-import-report">
		
	</div>
</div>

<div id="page-blocker"></div>