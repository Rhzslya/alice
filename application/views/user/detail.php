<script type="text/javascript">
	$(document).ready(function(){
		$('.ico-close-user').click(function(){
			$('.profile-pane').fadeOut();
			$('#page-blocker').fadeOut();

			return false;
		});
	});
</script>
	
	
<div class="profile-view">
	<a href="#" class="ico-close-user"><img src="<?=base_url('assets/image/ico-close.png')?>" /></a>
	<div align="right">			
		<h3>User</h3>
	</div>

	<div class="profile-detail-ring">
		<div class="profile-detail-photo">
			<img src="<?=base_url('uploads/user/'.$row->photo)?>">
		</div>

		<div class="profile-category">
			Administrator
		</div>		
	</div>
</div>
<div class="profile-detail">
	<h3>Profile</h3>

	<div class="profile-detail-pane">
		<h4><?=label('id_number');?></h4>
		<div><?=$row->id_number?></div>

		<h4><?=label('full_name');?></h4>
		<div><?=$row->full_name?></div>

		<?php if ($row->username != NULL): ?>
			<h4><?=label('username');?></h4>
			<div><?=$row->username?></div>
		<?php else: ?>
			<h4><?=label('Seafarer Code');?></h4>
			<div><?=$row->seaferer_code?></div>	
		<?php endif ?>
	</div>
</div>