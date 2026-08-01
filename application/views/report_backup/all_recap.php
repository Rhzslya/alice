<script type="text/javascript">
	$(document).ready(function() {
		var base_url = $('#base-url').html();

		$('.tbl-rotate').css('height', $('.rotate').width());

		$('body').on('click', '.f-score', function() {

			$('.type-status').css('display', 'none');
			$('.type-score').css('display', 'block');
			$('.f-status').removeClass('f-active');
			$('.f-score').addClass('f-active');			

			return false;
		});

		$('body').on('click', '.f-status', function() {

			$('.type-status').css('display', 'block');
			$('.type-score').css('display', 'none');
			$('.f-score').removeClass('f-active');
			$('.f-status').addClass('f-active');	
			
			return false;
		});

		$('body').on('click', '.report-sco', function() {

			var cat = $(this).attr('cat');

			$('.report-score').fadeIn();
			$('#page-blocker').fadeIn();

			$('.content-report-score').load(base_url+'report/form_score',{js_cat : cat});
			
			return false;
		});
	});
</script>
<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('report')?>" class="go-back">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>

<style type="text/css">
.report-value{
	text-align: center;
}

.f-score, .f-status{
	color: #bbb;
	text-transform: uppercase;
}

.f-status:hover, .f-score:hover{
	color: #F46523;
}

.f-active{
	color: red;
}
</style>


<div class="main-pane">
	<div class="title-menu" style="width: auto;">All Recapitulation Report</div>
	<div class="main-home">

		<div class="subtile">
			<?php if (isset($part)): ?>
				<a href="<?=base_url('report/recap_participant_level_pdf/'.$uc_level.'/'.$category.'/'.$uc_pukp.'/'.$uc_upt.'/'.$type)?>" class="btn-pdf-export" title="Export PDF">
					Export PDF
				</a>
				<a href="<?=base_url('report/recap_participant_level_excel/'.$uc_level.'/'.$category.'/'.$uc_pukp.'/'.$uc_upt.'/'.$type)?>" class="btn-xls-export" title="Export Excel">
					Export Excel
				</a>
				<a href="<?=base_url('report/export_result')?>" class="btn-db-export" style="width: 90px;" title="Export Result">
					Export Result
				</a>
				
			<?php else: ?>
				<a href="#" class="btn-pdf-export" onclick="alert('Data Empty!');" title="Export PDF">
					Export PDF
				</a>
				<a href="#" class="btn-xls-export" onclick="alert('Data Empty!');" title="Export Excel">
					Export Excel
				</a>
			<?php endif ?>
			
			<div class="combow" style="float: right">
				<a href="#"><input type="button" class="ui-btn-default report-sco" value="Filter" name="" style=""></a>
			</div>

		</div>

		<div class="period-content  form-adit" style="width: 1040px;height: 415px;padding-left: 15px;overflow-y: hidden;">
			<?php $this->load->helper('text'); ?>
			<div class="ui-ex-par" style="height: 30px;margin-bottom: 10px;margin-top: 0">
				<div class="ui-function" >
				<table>
					<tr>
						<td width="4%">PUKP</td>
						<td width="7%">
							<?=$row_p->pukp_label?>
						</td>
						<td width="3%">UPT</td>
						<td width="39%">
							<?=$row_u->upt_label?>
						</td>
						<td width="4%">Level</td>
						<td width="8%">
							<?=$row_l->label?>
						</td>
						<td width="7%">Category</td>
						<td width="10%">
							<?php if (isset($category)): ?>
								<?php if ($category == 1): ?>
									Pra
								<?php elseif($category == 2): ?>
									Pasca
								<?php elseif($category == 3): ?>
									DP
								<?php else: ?>
									-
								<?php endif ?>
							<?php endif ?>
						</td>
						<td width="18%">
							<a href="" class="f-status f-active"><span>Status</span></a> &nbsp; | &nbsp;
							<a href="" class="f-score"><span>Max Score</span></a>
							<!--
							<input type="radio" name="report_type" class="f-status" checked="TRUE"> Status
							<input type="radio" name="report_type" class="f-score"> Score (Max)
						-->
						</td>
					</tr>
				</table>
				</div>
			</div>
			<div class="recap-cell">
				<?php if (isset($comp)) : ?>				

					<table class="recap-score im-table" border="1" style="">
						<tr>
							<th width="15" rowspan="2">No.</th>
							<th width="100" rowspan="2">Seafarer Code</th>
							<th width="300" rowspan="2">Name</th>
							<?php foreach ($comp as $co) : ?>
								<th class="tbl-rotate" style="text-align: inherit;  font-weight: normal;"><div><span><?=word_limiter($co->label,6)?></span></div></th>
							<?php endforeach; ?>
						</tr>
						<tr>
							<?php $no = 1; ?>
							<?php foreach ($comp as $co) : ?>
								<th align="center"><?=$no?></th>
							<?php $no++; ?>
							<?php endforeach; ?>
						</tr>
						<?php $i = 1; ?>
						<?php if (isset($part)): ?>
							<?php foreach ($part as $part) : ?>
								<tr>
									<td align="center"><?=$i?></td>
									<td align="center"><?=$part['seafarer_code']?></td>
									<td>
										<a href="<?=base_url('report/report_participant/'.$uc_level.'/'.$category.'/'.$uc_pukp.'/'.$uc_upt.'/'.$type.'/'.$part['seafarer_code'])?>" target="_blank">
											<?=$part['full_name']?>
										</a>
									</td>	
									
									<?php foreach ($comp as $co) : ?>								
										<td align="right">
											<span class="type-status" style="display: block">
												<?php
													if (isset($score[$part['seafarer_code']][$co->uc]['status'])) {
														if ($score[$part['seafarer_code']][$co->uc]['status'] == 1){
															echo "SL";
														}
														else{
															echo "BL";
														}
													}
													else {
														echo "-";
													}
												?>
											</span>
											<span class="type-score" style="display: none">
												<?php
													if (isset($score[$part['seafarer_code']][$co->uc]['score'])) {
														echo $score[$part['seafarer_code']][$co->uc]['score'];
													}
													else {
														echo "-";
													}
												?>
											</span>										
										</td>
									<?php endforeach; ?>
								</tr>

								<?php $i++;?>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="<?=count($comp)+4?>">Empty participant...</td>
							</tr>
						<?php endif ?>
					</table>

				<?php else: ?>
					<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
				<?php endif ?>

			</div>
		</div>

	</div>
</div>

<div class="pop-up-form-add category-recap" style="height: 135px;width: 365px;z-index: 2">
	<div class="content-category-recap">
		
	</div>
</div>

<div class="pop-up-form-add report-score" style="height: 215px;width: 385px;z-index: 2">
	<div class="content-report-score">
		
	</div>
</div>