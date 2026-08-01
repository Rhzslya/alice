<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/contents.css')?>">
<link rel="stylesheet" type="text/css" href="<?=base_url('assets/third_party/immu-ui/immu-ui.css')?>">

<style type="text/css">
	th{
		font-weight: normal;
		border:1px solid white;
	}
</style>

<h2 align="center" style="font-family: Arial;">Score Recapitulation Level Report</h2>

<?php if (isset($part)): ?>
	<table class="im-table" style="width: 98%;margin: auto;">
		<tr>
			<th>PUKP</th>
			<td><?=$row_p->pukp_label?></td>
			<th>UPT</th>
			<td><?=$row_u->upt_label?></td>
			<th>Level</th>
			<td>
				<?=$row->label?>
			</td>
			<th>Category</th>
			<td>
				<?php if (isset($category)): ?>
					<?php if ($category == 1): ?>
						Pra
					<?php elseif($category == 2): ?>
						Pasca
					<?php elseif($category == 3): ?>
						DP
					<?php else: ?>
						-
					<?php endif ?>
				<?php endif ?>
			</td>
		</tr>
		<tr></tr>
	</table>		
<?php endif ?>

<br/>

<?php if (isset($comp)) : ?>	
	<table class="recap-score im-table" border="1" style="">
		<tr>
			<th width="15" rowspan="2">No.</th>
			<th width="100" rowspan="2">Seafarer Code</th>
			<th width="300" rowspan="2">Name</th>
			<?php $this->load->helper('text'); ?>
			<?php foreach ($comp as $co) : ?>
				<th text-rotate="90" style="vertical-align: bottom;"><div><span><?=word_limiter($co->label,6)?></span></div></th>
			<?php endforeach; ?>
		</tr>			
		<tr>
			<?php $no = 1; ?>
			<?php foreach ($comp as $co) : ?>
				<th align="center"><?=$no?></th>
			<?php $no++; ?>
			<?php endforeach; ?>
		</tr>
		<?php $i = 1; ?>
		<?php if (isset($part)): ?>
			<?php foreach ($part as $part) : ?>
				<tr>
					<td align="center"><?=$i?></td>
					<td align="center"><?=$part['seafarer_code']?></td>
					<td><?=$part['full_name']?></td>							
					
					<?php foreach ($comp as $co) : ?>								
						<td align="right">
							<?php
								if ($type == 1) {
									if (isset($score[$part['seafarer_code']][$co->uc])) {
										
										if ($score[$part['seafarer_code']][$co->uc] == 1){
											echo "SL";
										}
										else{
											echo "BL";
										}

									}
									else {
										echo "-";
									}
								}
								else {
									if (isset($score[$part['seafarer_code']][$co->uc])) {
										echo $score[$part['seafarer_code']][$co->uc];
									}
									else {
										echo "-";
									}
								}
							?>
						</td>
					<?php endforeach; ?>
				</tr>

				<?php $i++;?>
			<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="<?=count($comp)+4?>">Empty participant...</td>
			</tr>
		<?php endif ?>
	</table>

	<?php if ($type == 1) : ?>
		<div style="margin-top:20px;">
			<table>
				<tr>
					<td style="font-size: 11px;">Ket</td>
					<td style="font-size: 11px;">:</td>
					<td style="font-size: 11px;">BL = Belum Lulus</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td style="font-size: 11px;">SL = Sudah Lulus</td>
				</tr>
			</table>
		</div>
	<?php else: ?>
		<div style="margin-top:20px;">
			<table>
				<tr>
					<td style="font-size: 11px;">Ket</td>
					<td style="font-size: 11px;">:</td>
					<td style="font-size: 11px;">UA = UnAttempt</td>
				</tr>
			</table>
		</div>
	<?php endif; ?>
<?php else: ?>
	<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
<?php endif ?>