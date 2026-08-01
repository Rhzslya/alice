<div class="container-fluid">
	<div class="row">
		<div class="col-11">
			<h1 class="text-uppercase">
				Competency Sequence
			</h1>
		</div>
		<div class="col-1">
			<a href="<?=base_url('package')?>" title="Package List"><i class="fa fa-arrow-circle-left text-danger text-right mt-2" style="font-size: 2em"></i></a>
		</div>
	</div>
</div>

<div class="container-fluid" style="width: 1280px">
	<ul class="nav nav-tabs nav-justified small">
		<li class="nav-item">
			<a class="nav-link <?=($major == 'ANT' ? "active" : "")?>" href="<?=base_url('dev/comseq/ANT')?>">ANT</a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?=($major == 'ATT' ? "active" : "")?>" href="<?=base_url('dev/comseq/ATT')?>">ATT</a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?=($major == 'ETO' ? "active" : "")?>" href="<?=base_url('dev/comseq/ETO')?>">ETO</a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?=($major == 'GMDSS' ? "active" : "")?>" href="<?=base_url('dev/comseq/GMDSS')?>">GMDSS</a>
		</li>
	</ul>
</div>

<div class="container-fluid" style="width: 1250px">
	<div class="row">
		<div class="col-12 p-3 border border-top-0" >
			<div class="col-12 list-group list-group-horizontal text-center small">
				<?php foreach ($levels as $lev) : ?>
					<a href="<?=base_url('dev/comseq/'.$major.'/'.$lev->uc."")?>" class="list-group-item list-group-item-action <?=($lev->uc == $level ? "active" : "")?> ">
						<?=$lev->label?>
					</a>
				<?php endforeach; ?>
			</div>

			<div class="col-12 list-group list-group-horizontal text-center small mt-2">
				<a href="<?=base_url('dev/comseq/'.$major.'/'.$level."/1")?>" 
					class="list-group-item list-group-item-action <?=($excat == '1' ? "active" : "")?> ">
						Pra
				</a>
				<a href="<?=base_url('dev/comseq/'.$major.'/'.$level."/2")?>"
				 	class="list-group-item list-group-item-action <?=($excat == '2' ? "active" : "")?> ">
						Pasca
				</a>
				<a href="<?=base_url('dev/comseq/'.$major.'/'.$level."/3")?>" 
					class="list-group-item list-group-item-action <?=($excat == '3' ? "active" : "")?> ">
						DP
				</a>
			</div>

			<?php if (isset($uc_competency)) : ?>
				<div id="uc-competency" uc-competeny="<?=$uc_competency?>"></div>
			<?php endif; ?>

			<div class="card my-3">
				<div class="card-body">
					<?php if (isset($comp)) : ?>
						<table class="table table-striped">
							<thead class="bg-secondary text-white">
								<tr>
									<th>No. </th>
									<th>Competency</th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 1; ?>
								<?php foreach ($comp as $com) : ?>
									<tr>
										<td><?=$i?></td>
										<td style="font-size: 0.9em">
											<?=$com->label?>
											
											<?php 
												switch ($com->category) {
													case '1'	:	$category = "[ PRA ]";
																	break;
													case '2'	:	$category = "[ PASCA ]";
																	break;
													case '4'	:	$category = "[ DP ]";
																	break;
													case '5'	:	$category = "[ PRA &amp; DP ]";
																	break;
													case '6'	:	$category = "[ PASCA &amp; DP ]";
																	break;
												}					
											?>

											<?php if (isset($category)) : ?>
												<span class="text-primary ml-2"><?=$category?></span>
											<?php endif; ?>
										</td>
									</tr>
									<?php $i++; ?>
								<?php endforeach; ?>
							</tbody>
						</table>	
					<?php else : ?>
						<div class="text-danger">Empty</div>
					<?php endif; ?>	
				</div>
			</div>


			<!--
			<?php foreach ($funcs as $fn) : ?>
				<div class="card my-3">
					<div class="card-body">
						<table class="table table-striped">
							<thead class="bg-secondary text-white">
								<tr>
									<th>Competency</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($comps[$fn['uc']] as $com) : ?>
									<tr>
										<td style="font-size: 0.9em">
											<?php 
												switch ($com['category']) {
													case '1'	:	$category = "[ PRA ]";
																	break;
													case '2'	:	$category = "[ PASCA ]";
																	break;
													case '4'	:	$category = "[ DP ]";
																	break;
													case '5'	:	$category = "[ PRA &amp; DP ]";
																	break;
													case '6'	:	$category = "[ PASCA &amp; DP ]";
																	break;
												}					
											?>

											<?=$com['label']?>
											<?php if (isset($category)) : ?>
												<span class="text-primary ml-2"><?=$category?></span>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>	
					</div>
				</div>		
			<?php endforeach; ?>
			-->
		</div>
	</div>
</div>

<div class="modal" id="page-blocker">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-body text-center">
			<i class="fas fa-circle-notch fa-spin fa-4x text-white"></i>
		</div>
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $("#base-url").html();

		$('body').on('click', '.qgen-randomly', function() {
			$('#page-blocker').modal('show');

			var uc 				= $(this).attr('uc');
			var uc_pack_comp 	= $(this).attr('uc-pack-comp');
			var uc_comp 		= $(this).attr('uc-comp');

			$.ajax({
				type		: 'post',
				dataType	: 'json',
				data 		: { js_uc : uc, js_uc_pack_comp : uc_pack_comp, js_uc_comp : uc_comp },
				url			: base_url + 'package/qgen_random',
				success		: function(output) {
								$('.last-gen-'+uc_comp).html(output.curr_time);
								if (output.success == true) {
									$('.status-'+uc_comp).html('<i class="fa fa-check-circle text-success" style="font-size: 1.2em"></i>');
								}

								$('#page-blocker').modal('hide');
				}
			});

			return false;
		});

		$('body').on('click', '.add-question',function() {
			$('#pick-question').modal('hide');
			$('#add-question').modal('show');

			return false;
		});

		$('body').on('click', '.q-delete',function() {
			var uc 				= $(this).attr('uc');
			var uc_pack_comp 	= $(this).attr('uc-pack-comp');
			var uc_comp 		= $(this).attr('uc-comp');

			$.ajax({
				type		: 'post',
				dataType	: 'json',
				data 		: { js_uc : uc, js_uc_pack_comp : uc_pack_comp },
				url			: base_url + 'package/qdelete',
				success		: function(output) {
								$('.last-gen-'+uc_comp).html('-');
								if (output.success == true) {
									$('.status-'+uc_comp).html('<i class="fa fa-ban text-danger" style="font-size: 1.2em"></i>');
								}
				}
			});

			return false;
		});
	});
</script>