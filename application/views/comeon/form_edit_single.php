<div class="container-fluid">
	
<div class="row">
	<div class="col">
		
		<h1>FORM EDIT SINGLE</h1>

		<?=form_open('comeon/update_single')?>
			<input type="hidden" name="f_exam_attempt" value="<?=$row_score->uc_exam_attempt?>">
			<input type="hidden" name="f_competency" value="<?=$row_score->uc_competency?>">
			<input type="hidden" name="f_old_score" value="<?=decryptIt($row_score->score_normal)?>">
			<input type="text" name="f_new_score" value="<?=decryptIt($row_score->score_normal)?>" size="3" >
			<input type="submit" value="Update">
		<?=form_close()?>

	</div>	
</div>

</div>