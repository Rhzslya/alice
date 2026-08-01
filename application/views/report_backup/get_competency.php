<option value="" uc-function="">-- Choose --</option>
<?php foreach($result as $row):?>
	<option value="<?=$row->uc?>" uc-function="<?=$row->uc_function?>"><?=$row->label?></option>
<?php endforeach;?>