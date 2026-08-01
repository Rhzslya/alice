 $(window).load(function () {

 	$('.go-back').click(function(){
		$('.la-loader').css('display','block');
	});

	$('.go-home').click(function(){
		$('.la-loader').css('display','block');
	});

	$('.go-maspar').click(function(){
		$('.la-loader').css('display','block');
	});

	$('.go-report').click(function(){
		$('.la-loader').css('display','block');
	});

	$('.page-number a').click(function(){
		$('.la-loader').css('display','block');
		 setTimeout(function () {
			$('.la-loader').css('display','none');
	      
	    }, 150);
	});

	$('.lc-delete-btn').click(function(){
		$('.la-loader').css('display','none');
		 
	});

	$('.ui-sess-del').click(function(){
		$('.la-loader').css('display','none');

	});

	$('.manage-ul-wrap li a').click(function(){
		$('.la-loader').css('display','block');
	});
 });