//$.ajax({
//    url: "data/",
//    success: function (data) {
//        var image_count = $(data).length();
//        console.log(image_count);
//    }
//});

var images = new Array();
var playTimer;
var output="";
var num_lines=0;
var submit_count=0;

$( document ).ready(function() {
  //alert(images_path);




  function preload() {
    if (assID == 'ASSIGNMENT_ID_NOT_AVAILABLE') {
      image_count = 10;
    }

    for (i = 0; i < image_count; i++) {
      images[i] = new Image()
      images[i].src = '../'+images_path+"/Kinect_" + pad(i+1,4) + ".jpg";
      //console.log(images[i].src);
    }


  }


  $(window).load(function() {
    // Animate loader off screen
    $("#div_loading").fadeOut("slow");;
  });






  function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
  }
  preload();

  $('.getFrame').click(function(){
    var c = $(this).prev().attr('class');
    $('.'+c).val(Number($('#frameNum').text()))
    if(c=="sframe") {
      $(".eframe").val("");
    } else {
      $('#play-pause').addClass('paused');
      $('#play-pause').removeClass('playing');
      $('#play-pause').css('background-position','213px -11px');
      clearInterval(playTimer);
    }
  });

  $('.addAnnon').click(function(){
    addAnnon()
  });

  /*$('.actLabel').keypress(function (e) {
  if (e.keyCode === 13 && $('.addAnnon').is(":enabled")) //enter
  addAnnon();
});*/

$('.actLabel').on('input', function() {
  text=$(this).val().trim().replace("  "," ");
  //alert(text);
  words=text.split(" ");
  if(words.length>4)
  {
    $('#length_lbl').css('visibility','visible');
    $(this).css('background-color','red');
    $('.addAnnon').prop('disabled', true);
  }
  else
  {
    $('#length_lbl').css('visibility','hidden');
    $(this).css('background-color','');
    $('.addAnnon').prop('disabled', false);
  }
});

function add_to_timeline(aL,sF,eF,num_lines) {
  var startPosLeft = $('#frameAxis > div.ticks > span:nth-child('+Math.round(sF/2)+')').position().left;
  var endPosLeft = $('#frameAxis > div.ticks > span:nth-child('+Math.round(eF/2)+')').position().left
  var wid = endPosLeft - startPosLeft;
  var row= $("<div original-title='"+aL+"' startFrame='"+sF+"' endFrame='"+eF+"' class='intervalRect itemNum-"+num_lines+"' style='width:"+wid+"px; left:"+startPosLeft+"px;'></div>");
  //Find out which row has space to append new item starting with row 1
  rowNumber = 1;
  flag = false;
  $('#mainCont').children('div').each(function () {//Row
    count = 1;
    total = $(this).children('.intervalRect').length;
    $(this).children('.intervalRect').each(function () {//rect
      answer = getOverlap(parseInt($(this).attr('startFrame')),parseInt($(this).attr('endFrame')),sF,eF);
        if ( answer == true){
          rowNumber += 1;
          return false;
        } else if (count == total) {
          flag = true;
        } // "this" is the current element in the loop
        count +=1;
    });
    if (flag == true) {
      return false;
    }

  });

  if ( !$( "#row-"+rowNumber ).length ) {
    newRow = $("<div class='intervalRow' id='row-"+rowNumber+"'><div class='dashed'></div></div>");
    $('#mainCont').append(newRow);
    bottVal = parseInt($(newRow).prev().css( "bottom" ));
    $(newRow).css( "bottom" , bottVal+25 +'px' );
  }
  $("#row-"+rowNumber).append(row);
  $(row).tipsy({gravity: 's'});
}

function remove_from_timeline(numIdSelector) {
  $("."+numIdSelector).remove();
}

function getOverlap(x1,x2,y1,y2){
  if ((x1 <= y2) && (y1 <= x2)) {
    return true;
  } else {
    return false;
  }
}

function addAnnon()
{
  var aL = $('.actLabel').val();
  var sF = $('.sframe').val();
  var eF = $('.eframe').val();

  if ((aL != '') && (sF != '') && (eF != '')) {
    if(parseInt(sF) >= parseInt(eF))   alert('Start frame cannot be after end frame!');
    else {

      num_lines+=1;
      var row = "<tr id='info' class='itemNum-"+num_lines+"'><td><div >"+aL+"</div></td><td><div>"+sF+"</div></td><td><div>"+eF+"</div></td><td><a href='#' class='delete'>delete</a></td></tr>";
      $(row).appendTo('#ann-table .row-elements');

      output+=aL+","+sF+","+eF+"\r\n";

      $(".sframe").val("");
      $(".eframe").val("");
      $(".actLabel").val("");
      add_to_timeline(aL,sF,eF,num_lines);


    }
  }else {
    alert('Some Fields are empty, Please make sure you fill in the activity label and its start and end frames before adding the annotation.');
  }

  return false;
}

$(document).on("click", ".delete", function(e) {
  deletionSelector = $(this).parent().parent().attr('class');
  $(this).parent().parent().remove();
  remove_from_timeline(deletionSelector);
  num_lines-=1;
  return false;
});

$('.submit').click(function(){

    var ans1 = $('#ans1').val();
    var ans2 = $('#ans2').val();

    if (num_lines<7) alert('You have too few annotation entries. Please identify more activities before submitting your answer.');

    else if ((ans1 == '') || (ans2 == '')) alert('You have to answer the two questions in Section 1 first.');

    else {
      if(submit_count==0)
      {
        alert("Would you please consider going back to the video and adding more labels that describe extended activities (ones that are made from a number of shorter ones)? Remember you will be rewarded based on the number of activities you identify.")
        submit_count+=1;
      }
      else if(submit_count==1)
      {
        var dur=(Math.floor(Date.now() / 1000))-start;

        $('#a').val(output);
        $('#answer1').val(ans1);
        $('#answer2').val(ans2);
        $('#dur').val(dur);

        $.post('../save_activity_annotations.php',{vid:vid,ann:output,answer1:ans1,answer2:ans2,wID:wID,dur:dur},function(a)
        {
          alert("Done. Thank you!");
          $('#sendData').submit();
        }
        );


      }
    }
});

});



$(window).on("load", function() {

  //set size of div according to iamge uplaoded size (710x400)
  $('.image').width(images[0].width);
  $('.image').height(images[0].height);
  //All images loaded along with the page
  $('.image').css("background-image", "url("+images[0].src+")");
  //Set Current Frame num from current image num

  function setCurrFrame(){
    var bg = $('.image').css('background-image').replace('url(','').replace(')','').replace(/\"/gi, "");
    bg.substr(bg.length-8 , 4);
    $('#frameNum').text(bg.substr(bg.length-8 , 4));
    //alert(parseInt(bg.substr(bg.length-8 , 4) ))
  }
  setCurrFrame();



  function slide(e,ui)
  {
    newImageNum=Math.round(ui.value/100*image_count,0);
    clearInterval(playTimer);
    if ((newImageNum < image_count) && (newImageNum >= 0)) {
      $('.image').css("background-image", "url("+ images[newImageNum].src +")");
      setCurrFrame();
      if ($('#play-pause').hasClass('playing')) {
        play(getCurrentFrame());
      }
    } else {
      if ($('#play-pause').hasClass('playing')) {
        $('#play-pause').addClass('paused');
        $('#play-pause').removeClass('playing');
        $('#play-pause').css('background-position','213px -11px');
      }
    }
  }



  function play (i) {           //  create a loop function
    playTimer = setInterval(function () {    //  call a 3s setTimeout when the loop is called
      $('.image').css("background-image", "url("+images[i].src+")");
      //$('#slider').slider( "value",Math.round((parseInt(bg.substr(bg.length-8 , 4) )+0.0)/image_count*100));
      $('#slider').slider( "value",Math.round(i/image_count*100));

      setCurrFrame();          //  your code here
      i++;                     //  increment the counter
      if (i >= image_count) {            //  if the counter < 10, call the loop function
        clearInterval(playTimer);
        //--------do something here -----------//
        $('#play-pause').addClass('paused');
        $('#play-pause').removeClass('playing');
        $('#play-pause').css('background-position','213px -11px');
      }                        //  ..  setTimeout()
    }, 100)
  }

  function getCurrentFrame() {
    return parseInt($('#frameNum').text(),10);
  }

  $('.paused').click(function(){
    if ($(this).hasClass('paused')) {
      $(this).addClass('playing');
      $(this).removeClass('paused');
      $(this).css('background-position','213px -77px');
      //Check if end of video in which case start from begining again
      if (getCurrentFrame() == image_count) {
        play(1);
      } else {
        play(getCurrentFrame());
      }
    } else if ($(this).hasClass('playing')) {
      $(this).addClass('paused');
      $(this).removeClass('playing');
      $(this).css('background-position','213px -11px');
      clearInterval(playTimer);
    }
  });

  $('.buttons .fineSeek').click(function(){
    var newImageNum;
    switch(this.id) {
      case 'nexFrame':
      newImageNum = getCurrentFrame();
      break;
      case 'preFrame':
      newImageNum = getCurrentFrame()-2;
      break;
      case 'nexBulk':
      newImageNum = getCurrentFrame()+9;
      break;
      case 'preBulk':
      newImageNum = getCurrentFrame()-11;
      break;
    }

    clearInterval(playTimer);
    if ((newImageNum < image_count) && (newImageNum >= 0)) {
      $('.image').css("background-image", "url("+ images[newImageNum].src +")");
      $('#slider').slider( "value",Math.round(newImageNum/image_count*100));

      setCurrFrame();
      if ($('#play-pause').hasClass('playing')) {
        play(getCurrentFrame());
      }
    } else {
      if ($('#play-pause').hasClass('playing')) {
        $('#play-pause').addClass('paused');
        $('#play-pause').removeClass('playing');
        $('#play-pause').css('background-position','213px -11px');
      }
    }
  });



  $( "#slider" ).on( "slide", function( e, ui ) { slide(e,ui);} );
});


// <div id="player-container">
// <p>Current Frame Number : <p id = "frameNum"></p> </p>
// <!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
// <p style="clear:both;"></p>
// <div id="player"></div>
//
// <script>
//   // 2. This code loads the IFrame Player API code asynchronously.
//   var tag = document.createElement('script');
//
//   tag.src = "https://www.youtube.com/iframe_api";
//   var firstScriptTag = document.getElementsByTagName('script')[0];
//   firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
//
//   // 3. This function creates an <iframe> (and YouTube player)
//   //    after the API code downloads.
//   var player;
//   function onYouTubeIframeAPIReady() {
//     player = new YT.Player('player', {
//       height: '480',
//       width: '740',
//       videoId: 'LBtoDpMx_PA',
//       playerVars : {
//         'autoplay' : 0,
//         'rel' : 0,
//         'showinfo' : 0,
//         'egm' : 0,
//         'showsearch' : 0,
//         'modestbranding' : 1
//       },
//       events: {
//         'onReady': onPlayerReady,
//         'onStateChange': onPlayerStateChange
//       }
//     });
//   }
//
//   // 4. The API will call this function when the video player is ready.
//   function onPlayerReady(event) {
//     event.target.playVideo();
//   }
//
//   // 5. The API calls this function when the player's state changes.
//   //    The function indicates that when playing a video (state=1),
//   //    the player should play for six seconds and then stop.
//   var done = false;
//   function onPlayerStateChange(event) {
//     if (event.data == YT.PlayerState.PLAYING && !done) {
//       player.stopVideo();
//       var auto_refresh = setInterval(function () {
//
//       frameRate = 5; //SET FROM CHECKING STAT NERD
//       frameNum = Math.round(player.getCurrentTime() * frameRate);
//       $("#frameNum").html(frameNum);
//       }, 100);
//       done = true;
//     }
//   }
//
//   </script>
//   </div>
