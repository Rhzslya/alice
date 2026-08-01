<option value="">--- Choose ---</option>
<?php if (isset($result)): ?>
	<?php foreach ($result as $res): ?>

		<option value="<?=$res->uc?>"><?=$res->upt_label?></option>
		
	<?php endforeach ?>
<?php endif ?>