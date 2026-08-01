<div class="load-range">
	<table class="im-table">
		<tr>
			<th width="5%">No</th>
			<th width="30%">Pertanyaan</th>
			<th width="10%">Pilihan</th>
			<th width="10%">Jawaban</th>
			<th width="10%">Kunci Jawaban</th>
			<th width="10%">Hasil</th>
		</tr>
		<?php for ($i=0; $i<$max_question; $i++) : ?>		
			<tr>
				<td width="5%"><?=$i+1?></td>
				<td width="10%"><?=read_text($question_text_en[$i]);?></td>
				<td width="10%">
					<?php if ($question_type[$i] == 1) : ?>
					<ol type="lower-alpha" style="width:auto">	
						<?php for ($j=0; $j<$max_option[$i]; $j++) : ?>
							
								<li><?=read_text($option_text_en[$i][$j])?></li>
							
						<?php endfor;?>	
					</ol>			
					<?php endif?>

					<?php if ($question_type[$i] == 2) :?>
						Correct | Wrong
					<?php endif;?>
					
					<?php if ($question_type[$i] == 3) :?>
						<?php foreach ($match_key[$i] as $mk) : ?>
								
							<?=read_text($question_field_en[$i][$j])?>
																							
						<?php endforeach; ?>
					<?php endif;?>	
				</td>
				<td width="10%"> 
					<?php if ($question_type[$i] == 1) : ?>	
						
						<?php for ($j=0; $j<$max_option[$i]; $j++) : ?>
							<?php if ($option_id[$i][$j] == $answers[$i]):?>
								<?=read_text($option_text_en[$i][$j])?>
							<?php endif;?>
						<?php endfor;?>
						
					<?php endif;?>
					<?php if ($question_type[$i] == 2) :?>	
						<?php if($answers[$i] == 1) :?>
							<label>Correct</label>
						<?PHP else:?>
							<label>Wrong</label>
						<?php endif;?>
						
					<?php endif;?>
					<?php if ($question_type[$i] == 3) :?>
						<?php foreach ($match_ans[$i] as $ma) : ?>
								
							<?php
								if ($ma != "NULL") {
									read_text($answer_field_en[$i][$j]);
								}
							?>
																						
						<?php endforeach; ?>
					<?php endif;?>
				</td>
				<td width="10%">

					<?php if ($question_type[$i] == 1) : ?>	
						<label><?php echo htmlspecialchars_decode(stripslashes($key_text[$kunci[$i]]))?></label>
					<?php endif;?>

					<?php if ($question_type[$i] == 2) :?>
						<?php if($keys[$i] == 1):?>
							<label>Correct</label>
						<?php else:?>
							<label>Wrong</label>
						<?php endif;?>
					<?php endif;?>

					<?php if($question_type[$i] == 3):?>
						<?php foreach ($match_key[$i] as $ma) : ?>
							
							<?php if ($ma != "NULL") :?>
								<label><?php echo htmlspecialchars_decode(stripslashes($answer_field_en[$ma]))?></label>
							<?php endif;?>
																					
						<?php endforeach; ?>
					<?php endif;?>

				</td>
				<td width="10%">
					<?php if ($resans[$i] == 1) : ?>
						Correct
					<?php else : ?>
						Wrong
					<?php endif; ?>	

				</td>
			</tr>

		<?php endfor;?>
	</table>
</div>