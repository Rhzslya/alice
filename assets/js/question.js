$(document).ready(function(){
	var base_url = $('#base-url').text();

	$('.page-question a.pagination-ajax').click(function(){
		$('#page-blocker').modal('show');

		var page 			= $(this).attr('title');
		var js_level 		= $('select[name=f_level]').val();
		var js_function 	= $('select[name=f_function]').val();
		var js_competency	= $('select[name=f_competency]').val();

		$('.result-question').load(
								base_url+'question/page',
								{
									js_page 			: page,
									js_uc_level			: js_level,
									js_uc_function 		: js_function,
									js_uc_competency 	: js_competency
								}
							, function(){
								$('#page-blocker').modal('hide');
							});

		return false;
	});

	$('.peek-question').click(function(){			
		var uc_question	= $(this).attr('uc-question');

		$('#question-detail .modal-content').html('');
		$('#question-detail .modal-content').load(base_url + 'question/detail', { js_uc_question : uc_question });
		$('#question-detail').modal('show');

		return false;
	});

	$('select[name=f_level]').change(function(){
		var uc = $(this).val();
		var all = 'All';

		$('input[name=f_uc_level]').val(uc);
		$('select[name=f_function]').load(base_url+'question/get_function',{js_uc_level : uc, js_all: all});

		return false;
	});

	$('select[name=f_function]').change(function(){
		var uc = $(this).val();
		var all = 'All';

		$('input[name=f_uc_function]').val(uc);
		$('select[name=f_competency]').load(base_url+'question/get_competency',{js_uc_function : uc, js_all: all});

		return false;
		
	});

	$('select[name=f_competency]').change(function(){
		var js_level 		= $('select[name=f_level] option:selected').val();
		var js_function 	= $('select[name=f_function] option:selected').val();
		var js_competency	= $(this).val();

		//$('.load-pdf').attr('href', base_url+'question/export_question_to_pdf/'+js_level+'/'+js_function+'/'+js_competency);
		
		$('.load-pdf').load(
								base_url+'question/load_pdf',
								{
									js_uc_level			: js_level,
									js_uc_function 		: js_function,
									js_uc_competency 	: js_competency
								}
							);
		//$('.la-loader').css('display','none');
		$('input[name=f_uc_competency]').val(js_competency);
		
		return false;

	});

	$('.filter-question').click(function(){
		$('#page-blocker').modal('show');

		var page 			= 1;
		var js_level 		= $('select[name=f_level] option:selected').val();
		var js_function 	= $('select[name=f_function] option:selected').val();
		var js_competency	= $('select[name=f_competency] option:selected').val();
		var js_key			= $('input[name=f_key]').val();

		$('.result-question').load(
								base_url+'question/page',
								{
									js_page 			: page,
									js_uc_level			: js_level,
									js_uc_function 		: js_function,
									js_uc_competency 	: js_competency,
									js_key 				: js_key
								}
							, function(){
								$('#page-blocker').modal('hide');
							});
		
		return false;

	});

	$('.add-question').click(function(){
		$('#add-question-setting .modal-content').load(base_url + 'question/add_question');
		$('#add-question-setting').modal('show');

		return false;
	});
});