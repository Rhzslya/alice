$(document).ready(function(){
	$('input, textarea, select, .button').click(
		function(){
			$('input, textarea, select, .button').removeClass('focus');
			$(this).addClass('focus');
		}
	);
	
	$('.im-tabs ul li a').click(function(){
		var tab_id = $(this).attr('id');

		//	set current tab
		$('.tab-current').removeClass('tab-current');
		$('#'+tab_id).addClass('tab-current');
		

		//	set current content
		$('.tab-content').css('display', 'none');
		$('div#'+tab_id).css('display', 'block');
		
		
	});
});