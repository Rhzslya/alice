<script type="text/javascript">
	$(document).ready(function() {

		var base_url = $('#base-url').html();

		/*$('body').on('click', '.tabs', function() {
			var type = $(this).attr('type');

			$('.tabs').removeClass('active-tab'); // To remove active tabs

			$('.tabs[type='+type+']').addClass('active-tab'); // To add new active tabs

			$('.form-area').load(base_url + "report/get_menu_ajax", { js_type : type });
		});*/


		// For Report Examination
		$('body').on('click', '.function-label', function() {
			var uc = $(this).attr('uc');

			$('body').addClass('loading');

			$('select[name=f_competency]').load(base_url + 'report/get_competency', { js_uc_function : uc }, function() {
				$('body').removeClass('loading');
			});

			return false;
		});

		$('body').on('change', 'select[name=f_competency]', function() {
			var uc_function = $('select[name=f_competency] option:selected').attr('uc-function');
			var uc_competency = $('select[name=f_competency] option:selected').val();

			$('body').addClass('loading');

			$('.form-adit').load(base_url + 'report/get_examination', { js_uc_function : uc_function, js_uc_competency : uc_competency }, function() {
				$('body').removeClass('loading');
			});
		});

	});
</script>

<style type="text/css">
	/* BEGIN Of loading animation */
		.modal {
		    background: url('assets/image/loading.gif') 50% 50% no-repeat;
		    width: 100%;
			height: 100%;
			position: fixed;
			top: 0;
			left: 0;
			opacity: 0.7;
			display: none;
			z-index: 700;
		}

		.loading {
		    overflow: hidden;  
		}

		.loading .modal {
		    display: block;
		}
	/* BEGIN Of loading animation */

	.label-tree {
		overflow: hidden;
		font-family: Calibri;
		font-size: 11pt;
	  	/*white-space: nowrap;*/
	  	/*text-overflow: ellipsis;*/
		/*width: 120px;*/
		margin-left: 5px;
		display: inline-block;
	}
</style>

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
	<div class="title-menu"> Report</div>
	<div class="main-home">
		<div class="rep-tab">
			<a href="<?=base_url('report');?>" class="tabs active-tab">Examination</a>
			<a href="<?=base_url('report/participant');?>" class="tabs" type="2">Participant</a>
		</div>

		<div class="form-area">
			<div class="tree-report">
				<table class="tree" style="margin:5px; width: 750px;">
					<?php if (isset($tree)): ?>
						<?php tree_browse($tree, 0); ?>
					<?php else: ?>
						<div class="ui-empty-data"  style="margin-top: 15%;margin-left: 40%">Empty</div>
						
					<?php endif ?>
				</table>
			</div>

			<div class="combo-report" style="width: 805px;">
				<div class="combow" style="margin-top: 0px">
					<div class="selected" style="">
						<select name="f_competency" style="width: 703px;margin-left: 0px">
							<option value="" uc-function="">-- Choose --</option>
						</select>
					</div>
				</div>
				<label>Competency</label>
			</div>
			<div class="form-adit" style="padding: 5px;width: 73%;height: 354px;margin-top: 10px;">
				<div class="ui-empty-data" style="margin-top: 15%"><?=label('empty');?></div>
			</div>
		</div>
	</div>
</div>