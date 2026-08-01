<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').text();

		$('.page-all a.pagination-ajax').click(function(){
			var page 			= $(this).attr('title');

			$('.load-user').load(
									base_url+'user/page',
									{
										js_page 			: page
									}
								);
			$('.la-loader').css('display','none');
			
			return false;
		});


	});
</script>
	<?php if (isset($result)): ?>
				<div class="result-user" style="height: 390px">	
					<table class="im-table">
						<tr>
							<th width="20" align="center">No.</th>
							<th width="300">Full Name</th>
							<th width="300">Username</th>
							<th width="200">Photo</th>
							<th width="100">Action</th>
						</tr>
						<?php $no = 1;?>
						<?php foreach($result as $row):?>
							<tr>
								<td><?=$no?></td>
								<td><?=($row->full_name != NULL ? $row->full_name : "-" )?></td>
								<td>
									<?=($row->username != NULL ? $row->username : "-" )?>
								</td>
								<td align="center">adas</td>
								<td align="center">
									<div style="float: left; margin-left: 20px">
										<a href="#" class="detail-user" id="<?=$row->id?>"><input type="button"  class="lc-detail-btn" name="" title="Detail"></a>	
									</div>
									<div style="float: left;">
										<a href="<?=base_url('user/edit/'.$row->id);?>"><input class="lc-edit-btn" type="button" name=""></a>
									</div>
									<div style="float: left;">
										<a href="<?=base_url('user/change_password/'.$row->id);?>">
											<input type="button" class="lc-key-btn" name="" title="Change Password">
										</a>
									</div>
									<div style="float: left;">
										<a href="<?=base_url('user/delete/'.$row->id);?>"><input class="lc-delete-btn" type="button" name="" onclick="return confirm('<?=label('Are you sure want to delete?')?>"></a>
									</div>
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
				Empty ...
			<?php endif ?>