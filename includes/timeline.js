$(window).on("load", function() {


var c = 0;
$('.ticks').append('<span class="major">|<span class="text">0</span></span>');
if (image_count < 250) {
  scaleEndVal = 250;
} else {
  scaleEndVal = image_count;
}
for (i = 1; i <= scaleEndVal/2; i++) {
  c += 1;
  if (c % 10 == 0) {
    $('.ticks').append('<span class="major">|<span class="text">'+ i*2 +'</span></span>');
  } else {
    $('.ticks').append('<span>|</span>');
  }
}

$('#mainCont').width($('#frameAxis').width())




$('.intervalRect').tipsy({gravity: 's'});

});
