<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').html();

		$('body').on('click', '.import-rep', function() {

			var cat = $(this).attr('cat');

			$('.import-report').fadeIn();
			$('#page-blocker').fadeIn();

			$('.content-import-report').load(base_url+'report/form_import_report',{js_cat : cat});
			
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
							<a href="<?=base_url('report')?>" title="Score" class="tab-current">
								Score
							</a>
							|
							<a href="<?=base_url('report/report_answer')?>" title="Answer">
								Report
							</a>
						</div>
					</div>
				</div>
			</div>			
		</div>

		<div class="period-content">
			<a href="#"><input type="button" class="ui-btn-default report-sco" cat="<?=$category?>" value="Report Score" name="" style=""></a>			
			<a href="#"><input type="button" class="ui-btn-default import-rep" cat="<?=$category?>" value="Import Report" name="" style=""></a>
			
			<?=$this->session->flashdata('msg'); ?>
			
		</div>
	</div>
</div>

<div class="pop-up-form-add import-report" style="height: 110px;width: 655px;z-index: 2">
	<div class="content-import-report">
		
	</div>
</div>

<div class="pop-up-form-add report-score" style="height: 215px;width: 385px;z-index: 2">
	<div class="content-report-score">
		
	</div>
</div>

<div id="page-blocker"></div>