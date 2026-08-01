<script type="text/javascript">
	$(document).ready(function() {

		var base_url = $('#base-url').html();

		// For Report Paricipant
		$('body').on('click', 'input[name=f_search_level]', function() {
			var uc_level = $('select[name=f_level] option:selected').val();

			$('body').addClass('loading');

			$('.rep-part-wrapper').load(base_url + 'report/get_participant_by_level', { js_uc_level : uc_level }, function() {
				$('body').removeClass('loading');
			});
		});

		$('body').on('click', 'input[name=f_search_name]', function() {
			var name = $('input[name=f_name]').val();

			$('body').addClass('loading');

			$('.rep-part-wrapper').load(base_url + 'report/get_partipant_by_name', { js_name : name }, function() {
				$('body').removeClass('loading');
			});
		});

		$('input[name=f_name]').keyup(function(e){
		    if(e.keyCode == 13) {
		        $('input[name=f_search_name]').click();
		    }
		});

	});
</script>

<style type="text/css">
	/* BEGIN Of loading animation */
		.modal {
		    background: url('assets/image/loading.gif') 50% 50% no-repeat;
		    width: 100%;
			height: 100%;
			position: fixed;
			top: 0;
			left: 0;
			opacity: 0.7;
			display: none;
			z-index: 700;
		}

		.loading {
		    overflow: hidden;  
		}

		.loading .modal {
		    display: block;
		}

		.txtsc{
			border: 1px solid #dadada;
			padding: 2px;
		}
	/* BEGIN Of loading animation */
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
	<div class="title-menu">Report</div>
	<div class="main-home">
		<div class="rep-tab" style="margin-top: 7px;">
			<a href="<?=base_url('report');?>" class="tabs">Examination</a>
			<a href="<?=base_url('report/participant');?>" class="tabs active-tab" type="2">Participant</a>
		</div>

		<div class="form-area">
			<div class="form-area">
				<div class="rep-part-filter">
					<table>
						<tr>
							<td width="50">							
								<label>Level</label>
							</td>

							<td>
								<div class="combow" style="margin-top: 0px">
									<div class="selected">							
										<select name="f_level">
											<?php $level = list_level(); ?>
											<option value="">- All Level -</option>
											<?php if (isset($level)): ?>
												<?php foreach ($level as $res): ?>
													<option value="<?=$res->uc?>"><?=$res->label?></option>
												<?php endforeach ?>
											<?php endif ?>
										</select>
									</div>
								</div>
							</td>

							<td>
								<div class="combow">							
									<input type="button" value="OK" name="f_search_level">
								</div>
							</td>

							<td width="600"></td>

							<td width="50">
								<label>Search</label>							
							</td>

							<td>							
								<input type="text" placeholder="Seafarer ID / Name" name="f_name" size="40" class="txtsc">
							</td>

							<td>							
								<input type="button" value="OK" name="f_search_name" class="ui-btn-default" style="margin-top: 0" >
							</td>
						</tr>
					</table>
					

				</div>

				<table class="im-table" width="100%">
					<tr align="left">
						<th width="190">Seafarer ID</th>
						<th width="300">Full Name</th>
						<th width="220">Tempat Tanggal Lahir</th>
						<th width="100">Level</th>
						<th >Detail</th>
					</tr>
				</table>
				<div class="rep-part-wrapper" style="overflow-y: auto;height: 342px;margin-top: -2px">
					<?php if (isset($result)): ?>
						<table class="im-table" width="100%">
							<?php foreach ($result as $res): ?>
								<tr>
									<td width="190"><?=$res->seafarer_code?></td>
									<td width="300"><?=$res->full_name?></td>
									<td width="220"><?=($res->born_place != "" ? $res->born_place : "-")?>, <?=time_format($res->born_date, 'd-M-Y');?></td>
									<td width="90"><?=$res->level_name?></td>
									<td>
										<a href="<?=base_url('report/detail_participant/'.$res->seafarer_code)?>"><input type="button" value="Score" class="ui-btn-default" style="margin-top: 0;float: none;"></a>
									</td>
								</tr>
							<?php endforeach ?>
						</table>
					<?php else: ?>
						<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
</div>