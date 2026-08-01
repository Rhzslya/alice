$(document).ready(function(){
  var base_url = $('#base-url').html();

    // BUTTON PLAY
  $('.play-icon').click(function(){
    var name_swf = $(this).attr('name-swf');

    try{document.getElementsByName(name_swf)[0].Play();}
    catch(e){document.getElementById(name_swf).Play();}
    
  }); 
  // END BUTTON PLAY

  // BUTTON STOP
  $('.stop-icon').click(function(){
    var name_swf = $(this).attr('name-swf');

    try{document.getElementsByName(name_swf)[0].StopPlay();}
    catch(e){document.getElementById(name_swf).StopPlay();}

  }); 
  // END BUTTON STOP

  // BUTTON REWIND
  $('.replay-icon').click(function(){
    var name_swf = $(this).attr('name-swf');

    try{document.getElementsByName(name_swf)[0].Rewind();}
    catch(e){document.getElementById(name_swf).Rewind();} 
  }); 
  // END BUTTON REWIND
});