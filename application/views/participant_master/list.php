<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/user_detail.css')?>">
<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').text();

		$('body').on('click', '.search-participant', function() {			
			var page = 1;
			var search_val = $('input[name=f_search]').val();

			$('.load-participant').load(base_url + 'participant_master/page', { js_page : page, js_search : search_val });
		});

		$('input[name=f_search]').keypress(function (e) {
		  	if (e.which == 13) {
		  		$('.search-participant').click();

		  		return false;
		  	}
		});

		$('body').on('click', '.page-all a.pagination-ajax', function() {
			var page = $(this).attr('title');
			var search_val = $('input[name=f_search]').val();

			$('.load-participant').load(base_url + 'participant_master/page', { js_page : page, js_search : search_val });

			return false;
		});

		$('body').on('click', '.btn-import-participant', function() {

			$('.import-participant').fadeIn();
			$('#page-blocker').fadeIn();

			$('.content-import-participant').load(base_url+'participant_master/form_import');
			
			return false;
		});

		$('body').on('click', '.btn-import-excel', function() {

			$('.import-excel').fadeIn();
			$('#page-blocker').fadeIn();

			$('.content-import-excel').load(base_url+'participant_master/form_import_excel');
			
			return false;
		});

		 $('.ui-button-add').click(function(){
		 	$('.la-loader').css('display','block');
		 });

		 $('.lc-edit-btn').click(function(){
		 	$('.la-loader').css('display','block');

		 });
	});
</script>
<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="#" class="go-back-disable">&nbsp;</a>
		<a href="<?=base_url('participant_master')?>" class="go-maspar">&nbsp;</a>
		<a href="<?=base_url('report')?>" class="go-report">&nbsp;</a>
		<!-- <a href="<?=base_url('error')?>" class="go-setting">&nbsp;</a>
		<a href="<?=base_url('error')?>" class="go-guide">&nbsp;</a> -->
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>
<div class="main-pane">
	<div class="title-menu" style="width: auto;">Participant Master List</div>
	<div class="button-side-title">
		<a href="<?=base_url('participant_master/add');?>">
			<input type="button" value="Add" name="" class="ui-button-add">
		</a>
	</div>

	<div class="main-home">
		<div class="subtile" style="width: 1090px">
			<?php if (isset($msg)): ?>
				<div style="float: left; border: 1px solid black; border-radius: 5px; width: 500px; font-size: 14pt; padding: 3px; background-color: red;" align="center">
					Seafarer ID has exist for this Participant.
				</div>
			<?php endif ?>
			<div style="float: left;font-family: CGFont;font-size: 13px; margin-left: -10px;">
				<table>
					<tr>
						<td>
							<a href="#" class="btn-import-participant">
								<input class="import-part" title="Import Data" type="button">
							</a>
						</td>
						<td>
							<a href="<?=base_url('participant_master/backup')?>">
								<input class="export-source" title="Export Data" type="button">
							</a>
						</td>
						<td>
							<a href="#" class="btn-import-excel">
								<input class="import-reps" title="Import Excel" type="button" style="width: 124px;margin-left: 0">
							</a>
						</td>
				</table>
			</div>
			<div style="float: right;font-family: CGFont;font-size: 13px">
				<table>
					<tr>
						<td>Search</td>
						<td>:</td>
						<td><input type="text" name="f_search" placeholder="Seafarer ID / Name" size="50" class="search-box"></td>
						<td><input type="button" value="OK" class="search-participant ui-btn-default" style="margin: 0;float: none;"></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="load-participant">
			<table class="im-table" width="1085">
				<tr>
					<th width="20" align="center">No.</th>
					<th width="300">Seafarer Code</th>
					<th width="300">Full Name</th>
					<th width="200">Birthday</th>
					<th width="90">Action</th>
				</tr>
			</table>
			<?php if (isset($result)): ?>
				<div class="result-user" style="height: 350px; overflow-y: auto;">	
				<table class="im-table">					
						<?php $no = 1;?>
						<?php foreach($result as $res):?>
							<tr>
								<td width="20"><?=$no?></td>
								<td width="300"><?=$res->seafarer_code?></td>
								<td width="300"><?=$res->full_name?></td>
								<td align="center" width="200"><?=($res->born_place != "" ? $res->born_place : "-")?>,<?=($res->born_date != NULL ? time_format($res->born_date, 'd-m-Y') : "-")?></td>
								<td align="center" width="90">
									<a href="<?=base_url('participant_master/edit/'.$res->uc);?>"><input class="lc-edit-btn" type="button"></a>
									<a href="<?=base_url('participant_master/delete/'.$res->uc);?>" onclick="return confirm('Are you sure want to delete?');"><input class="lc-delete-btn" type="button"></a>
								</td>
							</tr>
						<?php $no++;?>
						<?php endforeach;?>
					</table>				
				</div>
				<div class="total-pane" style="float:right;width: 190px">
					<div class="total-label-total">Total</div>
					<div class="total-value">
						<?php if(isset($total_record)):?>
							<?=$total_record?>
						<?php else:?>
								-
						<?php endif;?>
					</div>
					<div class="total-label-unit">User(s)</div>
				</div>

				<div class="im-pagination page-all">
					<?php if (isset($pagination)) : ?>
						<?=$pagination?>
					<?php endif; ?>
				</div>				
			<?php else: ?>
				<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
				
			<?php endif ?>
		</div>
	</div>
</div>

<div class="pop-up-form-add import-participant" style="height: 165px;width: 400px;z-index: 2">
	<div class="content-import-participant">
		
	</div>
</div>
<div class="pop-up-form-add import-excel" style="height: 165px;width: 400px;z-index: 2">
	<div class="content-import-excel">
		
	</div>
</div>

<div id="page-blocker"></div>