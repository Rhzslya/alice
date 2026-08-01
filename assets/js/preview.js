var base_url = $('#base-url').html();

function show_question(no){
	//save_answer();

	//	Set Which Question to Show
	$('.question-each').css('display','none');
	$('.question-pane').find('div.quest-'+no).css('display', 'block');

	//	Set Current Question No, for Next & Prev navigation purpose
	$('input[name=f_curr_no]').val(no);

	//	Navigation Prevention for Next or Prev
	var max_no = $('input[name=f_max_no]').val();
	///	If The End of No
	if (no >= max_no) {
	$('#next-pane').css('display','none');
	}
	else {
		$('#next-pane').css('display','block');	
	}
	///	If The Beginning of No
	if (no <= 1) {
		$('#prev-pane').css('display','none');
	}
	else {
		$('#prev-pane').css('display','block');	
	}

	return false;
}

function show_next(){
	//	Get Current No
	var curr_no = $('input[name=f_curr_no]').val();
	//	Add 1 the Current No
	var next_no = parseInt(curr_no) + 1;
	//	Get Queston to Show
	show_question(next_no);
}

function show_prev(){
	//	Get Current No
	var curr_no = $('input[name=f_curr_no]').val();
	//	Sub 1 the Current No
	var prev_no = parseInt(curr_no) - 1;
	//	Get Queston to Show
	show_question(prev_no);
}