<style type="text/css">
	.label-folder {
		overflow: hidden;
	  	white-space: nowrap;
	  	text-overflow: ellipsis;
		width: 302px;
		display: inline-block;
		height: 20px;
	}
</style>

<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').html();
		var session = $('input[name=f_sessions]').val();

		$('body').on('click', 'input[name=btn_search]', function() {
			var name = $('input[name=f_search]').val();
			var uc_exam = $('input[name=f_uc_exam]').val();

			$('body').addClass('loading');

			$('.content-participant').load(base_url + 'report/get_partipant_by_name_exam', { js_name : name, js_uc_exam : uc_exam, js_session : session }, function() {
				$('body').removeClass('loading');
			});
		});

		$('input[name=f_search]').keyup(function(e){
		    if(e.keyCode == 13) {
		        $('input[name=btn_search]').click();
		    }
		});

		$('body').on('click', 'input[name=btn_order]', function() {
			var name 	= $('input[name=f_search]').val();
			var uc_exam = $('input[name=f_uc_exam]').val();
			var sort_by = $('select[name=f_sort_by] :selected').val();
			var sort 	= "ASC";

			// $('body').addClass('loading');

			if (sort_by == "full_name") {
				var sort = $('select[name=f_sort_name] :selected').val();
			} else if (sort_by == "seafarer_code" || sort_by == "score_competency") {
				var sort = $('select[name=f_sort_no] :selected').val();
			}

			$('.content-participant').load(base_url + 'report/get_partipant_order', { js_uc_exam : uc_exam, js_sort_by : sort_by, js_sort : sort, js_session : session }, function() {
				$('input[name=f_sort_by]').val(sort_by);
				$('input[name=f_order]').val(sort);
				$('body').removeClass('loading');
			});
		});

		$('body').on('change', 'select[name=f_sort_by]', function() {
			var sort = $(this).val();

			if (sort == "full_name") {
				$('.sort').attr('hidden', true);
				$('select[name=f_sort_name]').removeAttr('hidden');
			} else if (sort == "seafarer_code" || sort == "score_competency") {
				$('.sort').attr('hidden', true);
				$('select[name=f_sort_no]').removeAttr('hidden')
			} else {
				$('.sort').attr('hidden', true);
				$('select[name=f_empty]').show();
			}
		});

		// Score
		$('body').on('click', '.btn-edit-score', function() {
			var uc_eac 	= $(this).attr('uc');
			var uc_exam = $('input[name=f_uc_exam]').val();
			var session = $('input[name=f_sessions]').val();

			$('.edit-score').fadeIn();
			$('#page-blocker').fadeIn();

			$('.content-edit-score').load(base_url+'report/get_score_competency', { js_uc_eac : uc_eac, js_uc_exam : uc_exam, js_session : session });
		});
	});	
</script>

<div class="side-menu">
	<div class="btn-back-home">
		<a href="<?=base_url('home')?>" class="go-home">&nbsp;</a>
		<a href="<?=base_url('report/manage/'.$result[0]->uc_period);?>" class="go-back">&nbsp;</a>
	</div>
	<?php 
		$this->CI =& get_instance();
		$this->CI->load->view('client_logo');
	?>
</div>

<div class="main-pane">
	<div class="title-menu" style="width: auto;">Report</div>
	<div class="main-home">

		<div class="subtile">
			<div class="sub-dp">

				<div class="rep-tab" style="margin: 0px;">
					<a href="<?=base_url('report');?>" class="tabs active-tab">Examination</a>
					<a href="<?=base_url('report/participant');?>" class="tabs" type="2">Participant</a>
				</div>

			
			</div>

			<div class="report-nav" style="margin-right: -28px;" align="right">
				<?=form_open("report/exam_participant_pdf");?>
					<input type="hidden" name="f_uc_exam" value="<?=$result[0]->uc?>">
					<input type="hidden" name="f_sessions" value="<?=$session?>">
					<!-- <a href="#" title="Export to pdf" style="color:#fff">
						<div style="padding-right: 5px; padding-left: 5px; margin-top: 0px;" class="ui-btn-default">
							<img src="<?=base_url('assets/image/icon/ico-pdf.png');?>" width="15" height="15" style="margin-top: 2px;margin-right: 3px;"> Export to PDF
						</div>
					</a> -->
					<!-- <input type="submit" name="f_export" title="Export to pdf" style="cursor: pointer; padding-right: 5px; padding-left: 5px; margin-top: 0px;" class="ui-btn-default" value="Export to PDF"> -->

						<!-- BEGIN For Sort by -->
							<input type="hidden" name="f_sort_by">
							<input type="hidden" name="f_order">
						<!-- END For Sort by -->
					<input type="image" src="<?=base_url('assets/image/icon/ico-pdf.png');?>" title="Export to PDF" style="cursor: pointer; padding-right: 5px; padding-left: 5px; margin-top: 0px;" class="ui-btn-default" />
				<?=form_close();?>

				<?=form_open("report/exam_participant_excel");?>
					<input type="hidden" name="f_uc_exam" value="<?=$result[0]->uc?>">
					<input type="hidden" name="f_sessions" value="<?=$session?>">
					<!-- <a href="<?=base_url('report/exam_participant_excel/'.$result[0]->uc.'/'.$session);?>" title="Export to pdf" style="color:#fff">
						<div style="padding-right: 5px; padding-left: 5px; margin-top: 0px;" class="ui-btn-default">
							<img src="<?=base_url('assets/image/icon/ico-excel.jpg');?>" width="15" height="15" style="margin-top: 2px;margin-right: 3px;"> Export to Excel
						</div>
					</a> -->

						<!-- BEGIN For Sort by -->
							<input type="hidden" name="f_sort_by">
							<input type="hidden" name="f_order">
						<!-- END For Sort by -->
					<input type="image" src="<?=base_url('assets/image/icon/ico-excel.jpg');?>" title="Export to Excel" style="cursor: pointer; padding-right: 5px; padding-left: 5px; margin-top: 0px;" class="ui-btn-default" />
				<?=form_close();?>
			</div>
		</div>
		
		<div class="period-content main-home" style="background-color: #fff;border: 1px solid #a4a4a4;height: 414px">
			<?php if (isset($result)): ?>
			<input type="hidden" name="f_uc_exam" value="<?=$result[0]->uc?>">

			<div class="ui-ex-par" style="width: 100%;margin: 0px;height:144px;border:0px">
				<div class="ui-function" >
				<table style="width: 98%;margin: auto;">
					<tr>
						<td width="100">Exam Code</td>
						<td width="200"><?=$result[0]->exam_code?></td>
						<td width="70">Subject Title</td>
						<td width="150"><?=$result[0]->period?></td>
						<td width="60">Date</td>
						<td width="200"><?=($result[0]->date != NULL ? time_format($result[0]->date, 'd M Y') : "-")?></td>
						<td width="150">Session</td>
						<td width="50"><?=(isset($session) ? $session : "-")?></td>
					</tr>
					<tr></tr>
					<tr>
						<td>Level</td>
						<td><?=$result[0]->level_name?></td>
						<td >Function</td>
						<td><?=$result[0]->function_name?></td>
					</tr>
					<tr></tr>
					<tr valign="top">
						
						<td colspan="8">
							<div style="float: left;">Competency</div>
							<div class="exam-competence">

							<?php if (isset($res_comp)): ?>
								
									<ul>
										<?php foreach ($res_comp as $res): ?>
											<li>
												<?=$res->sequence?>.<?=$res->competency_name?>											
											</li>
										<?php endforeach ?>											

									</ul>
								
							<?php endif ?>
							</div>
						</td>
					</tr>
				</table>
				</div>
			</div>
			<div class="ui-assigned-participant">
				<div class="ui-part" style="width: 100%">
					<?php if ($result[0]->seafarer_code != NULL): ?>
						<div class="ui-boxtile" style=" width: 1062px; margin-bottom:5px ;">
							<table style="float: right;font-size: 13px">
								<tr>
									<td width="50">Search</td>
									<td width="250"><input type="text" name="f_search" size="40" placeholder="Seafarer ID / Name"></td>
									<td><input type="button" name="btn_search" value="OK" class="ui-btn-default" style="margin:0"></td>
								</tr>
							</table>

							<table style="float: left;font-size: 13px">
								<tr>
									<td width="70">Order By</td>
									<td width="130">
										<select name="f_sort_by" style="width: 130px;">
											<option value="">-- Choose --</option>
											<option value="seafarer_code">Seafare ID</option>
											<option value="full_name">Full Name</option>
											<option value="score_competency">Score</option>
										</select>
									</td>
									<td>&rarr;</td>
									<td width="135">
										<select name="f_empty" class="sort" style="width: 130px;">
											<option>-- Choose --</option>
										</select>
										<select name="f_sort_no" class="sort" style="width: 130px;" hidden>
											<option value="">-- Choose --</option>
											<option value="ASC">Lowest to Highest</option>
											<option value="DESC">Highest to Lowest</option>
										</select>
										<select name="f_sort_name" class="sort" style="width: 130px;" hidden>
											<option value="">-- Choose --</option>
											<option value="ASC">A to Z</option>
											<option value="DESC">Z to A</option>
										</select>
									</td>
									<td><input type="button" name="btn_order" value="OK" class="ui-btn-default" style="margin:0"></td>
								</tr>
							</table>
						</div>

						<div class="content-participant" style="height: 214px; overflow-y: scroll; border: 1px solid black;">
							<table class="im-table" style="border-collapse: collapse;">
								<tr>
									<th align="center" rowspan="2" width="20">No</th>
									<th align="left" width="80" rowspan="2">Seafarer ID</th>
									<th align="left" width="220" rowspan="2">Full Name</th>
									<th align="left" width="180" rowspan="2">Date Of Birth</th>
									<th align="center" width="70" colspan="2">Score</th>
									<th align="center" rowspan="2">answer Detai</th>

									<?php if ($this->session->userdata('log_user_category') == 1): ?>
										<th align="center" width="70" rowspan="2">Edit Score</th>
									<?php endif ?>
								</tr>
								
								<tr>
									
									<th width="250">Competency</th>
									<th>Score</th>
								</tr>								
								
								<?php $nom = 1; ?>
								<?php for($i = 0; $i < $max; $i++): ?>
									<td align="center"><?=$nom?></td>
										<td align="left"><?=$seafarer_code[$i]?></td>
										<td align="left"><?=$full_name[$i]?></td>
										<td align="left"><?=$born_place[$i]?>, <?=($born_date[$i] != NULL ? time_format($born_date[$i], 'd-M-Y') : "-")?></td>
										<td>
											<ul style="list-style: none;padding: 0">
												<?php $k_no = 1; ?>
												<?php for($k = 0; $k < count($score[$i]); $k++): ?>													
													<li style="height: 20px;line-height: 30px;">													
														<?php if ($competency_name[$i][$k] != NULL): ?>															
															<label class="label-folder" title="<?=$competency_name[$i][$k]?>" style="float: left;"><?=$k_no?>.<?=$competency_name[$i][$k]?></label>
														<?php else: ?>
															<div align="center">
																-
															</div>
														<?php endif ?>
													</li>														
													<?php $k_no++; ?>
												<?php endfor;?>
											</ul>


										</td>
										<td>
											<ul style="list-style: none;padding: 0">
												<?php for($j = 0; $j < count($score[$i]); $j++): ?>													
													<li style="height: 20px;line-height: 30px;">													
														<?=$score[$i][$j] ?>
													</li>
												<?php endfor;?>
											
											</ul>
										</td>
										<?php if ($this->session->userdata('log_user_category') == 1): ?>
											<td>
												<ul  style="list-style: none;padding: 0">
													<?php for($k = 0; $k < count($score[$i]); $k++): ?>												
														<li style="height: 20px;line-height: 30px;">
															
																<?php if ($uc_exam_attempt_competency[$i][$k] != NULL): ?>
																	<a href="#" class="btn-edit-score" uc="<?=$uc_exam_attempt_competency[$i][$k]?>">
																		Edit Score															
																	</a>
																<?php endif ?>
															
														</li>
														<?php $no++; ?>
													<?php endfor;?>
												</ul>
											</td>
										<?php endif ?>
											<td align="center">
											<a href="<?=base_url('report/detail_exam_student/'.$res->uc.'/'.$session.'/'.$ea_uc[$i]);?>">
												<input type="button" value="Answer Detail">
											</a>
										</td>
									</tbody>
								<?php $nom++ ?>
								<?php endfor;?>
							</table>
						</div>
					<?php else: ?>
						<div class="ui-empty-data"  style="margin-top: 8%;margin-left: 40%">Empty</div>
					<?php endif ?>
				</div>
			<?php else: ?>
				<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
			<?php endif ?>
			</div>
		</div>

	</div>
</div>

<div class="pop-up-form-add add-pukp" style="height: 135px;width: 400px;z-index: 2">
	<div class="content-add-pukp">
		
	</div>
</div>

<div class="pop-up-form-add edit-score" style="height: 134px;width: 500px;z-index: 2">
	<div class="content-edit-score">
		
	</div>
</div>

<div id="page-blocker"></div>