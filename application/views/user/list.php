<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/user_detail.css')?>">
<script type="text/javascript">
	$(document).ready(function(){
		var base_url = $('#base-url').text();

		$('body').on('click', '.detail-user', function() {
			var id = $(this).attr('id');

			$('#page-blocker').css('display', 'block');
			$('.profile-user').css({'display':'block'});
				
			$('.profile-user').empty();
			$('.profile-user').load(base_url+'user/detail', {js_id : id});
			$('.la-loader').css('display','none');
			return false;
		});

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

		$('.lc-edit-btn').click(function(){
			$('.la-loader').css('display','block');

		});

		$('.lc-key-btn').click(function(){
			$('.la-loader').css('display','block');

		});

		$('.btn-add').click(function(){
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
	<div class="title-menu" style="width: auto;">User List</div>
	<div class="button-side-title">
		<a href="<?=base_url('user/add');?>"  id="add-parent" class="btn-add">
			<input type="button" value="Add" name="" class="ui-button-add">
		</a>
	</div>

	<div class="main-home">
		<div class="subtile" style="width: 1090px">
			<?php if (isset($msg)): ?>
				<div style="float: left; border: 1px solid black; border-radius: 7px; width: 500px; font-size: 14pt; padding: 3px; background-color: red;" align="center">
					<?=$msg?>
				</div>
			<?php endif ?>
		</div>
		<div class="load-user">
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
								<td align="center">
									<?php if($row->photo_small != NULL):?>
										<img src="<?=base_url('uploads/user/'.$row->photo)?>" width="50" height="50">
									<?php else:?>
										<img src="<?=base_url('assets/image/user-photo.jpg')?>" width="50" height="50">
									<?php endif;?>

								</td>
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
		</div>
	</div>
</div>

<div class="profile-pane profile-user" style="z-index: 2;"></div>