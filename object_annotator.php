<?php

include('video_object.php');

$video_id=$_REQUEST['vid'];
$object_id=$_REQUEST['oid'];


$path='vids/vid'.$video_id.'/';

$image_path=$path."images/";
//$videos=scandir();

//for($i=0;$i<count($videos);$i++)

$annotation_path=$path."objects/";

$start_pos_file=fopen($annotation_path."start-positions.csv",'r');

$objs=array();
$i=0;


while(($line=fgets( $start_pos_file )) !== false )
{
  //echo $line."<br>";
  if(trim($line)=="") break;
  if($line[0]=="#") continue;

  //objName,x,y,w,h,startFrame,endFrame
  $obj_properties=explode(",",$line);

  //print_r($obj_properties);

  $obj = new video_object();
  $obj->init($obj_properties);
  $objs[$i++]=$obj;

}


// for($o=1;$o<count($objs);$o++)
// {
//   $obj=$objs[$o];
//   //$obj_>name, $obj->x, $obj_>y, $obj_>w, $obj_>h, $obj_>sf, $obj_>ef
//   echo $obj->ef;
//   //print_r($obj);

$obj=$objs[$object_id];
//print_r($obj);
  ?>

  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>Object annotator</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js">
    </script>
    <script type="text/javascript" src="includes/paper-full.js"></script>
    <script type="text/javascript" >
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext("2d");

    var object_centres_x=[];
    var object_centres_y=[];

    var bb_width=[]
    var bb_height=[]

    var image_num=<?php echo $obj->sf;?>;

    var fixed_w=<?php echo $obj->w;?>;
    var fixed_h=<?php echo $obj->h;?>;

    var W=600;
    var H=338;
    var url='<?php echo $image_path;?>'
    var first_image=<?php echo $obj->sf; ?>;
    var total_images=<?php echo $obj->ef-$obj->sf+1; ?>;
    var last_image=<?php echo $obj->ef;?>;

    var offset=0;
    var interval;





    function load_next_image()
    {
      if(paper.project.activeLayer.children.length==0)
      {
        alert('draw a box before you go to next frame.')
      }
      else {
        if($("#centre_x").val()=="")
        {
          fix_size()
          $('#fix_size').attr('checked', true);
        }
        object_centres_x.push(parseInt($("#centre_x").val()));
        object_centres_y.push(parseInt($("#centre_y").val()));
        bb_width.push(fixed_w);
        bb_height.push(fixed_h);

        if(image_num==last_image)
        {
          $.post('save_object_annotations.php',{vid:<?php echo $video_id;?>,obj:"<?php echo $obj->name;?>",x:JSON.stringify(object_centres_x),y:JSON.stringify(object_centres_y),
          w:JSON.stringify(bb_width),h:JSON.stringify(bb_height)},function(){alert("done");});

        }
        //load next picture
        else
        {
          image_num+=1;
          $('#frame_num').text(image_num);
          $("#canvas").css('background-image',"url("+url+"Kinect_"+("00000" + image_num).slice(-4)+".jpg)");
        }
      }

    }


    function load_previous_image()
    {
      object_centres_x.pop();
      object_centres_y.pop();
      bb_width.pop();
      bb_height.pop();

      if(image_num>1)
      {
        image_num-=1;
        $('#frame_num').text(image_num);
        $("#canvas").css('background-image',"url("+url+"Kinect_"+("00000" + image_num).slice(-4)+".jpg)");
      }
    }


    $(document).ready(function(){
      paper.setup(canvas);
      $("#span_total").text(total_images);
      //start_x=-100;
      start_x=<?php echo $obj->x-$obj->w/2;?>;
      end_x=<?php echo $obj->x+$obj->w/2;?>;

      start_y=<?php echo $obj->y-$obj->h/2;?>;
      end_y=<?php echo $obj->y+$obj->h/2;?>;

      $('#ul_corner_x').val(<?php echo $obj->x-$obj->w/2;?>)
      $('#ul_corner_y').val(<?php echo $obj->y-$obj->h/2;?>)

      $('#lr_corner_x').val(<?php echo $obj->y-$obj->h/2;?>)
      $('#lr_corner_y').val(<?php echo $obj->y+$obj->h/2;?>)

      new_centre_x=<?php echo $obj->x;?>;
      new_centre_y=<?php echo $obj->y;?>;

      fix_size();
      //window.globals.draw_fixed_rect(new_centre_x,new_centre_y);
      $("#fix_size").prop('checked', true);


      $("#clear_rects").click(function(){
        clear_canvas();
        fixed_w=-1;
        $('#fix_size').attr('checked', false);
      });

      $("#fix_size").click(function(){
        if($(this).is(':checked'))
        {
          fix_size();
        }
        else
        {
          fixed_w=-1
          paper.project.activeLayer.children[0].strokeColor = 'white';
          paper.project.activeLayer.children[0].strokeWidth= 2;
        }
      });

     $("#canvas").css('background-image',"url("+url+"Kinect_"+("00000" + first_image).slice(-4)+".jpg)");

      $(window).keypress(function (e) {
        if (e.keyCode === 0 || e.keyCode === 110 || e.keyCode === 78  ) { //n
          e.preventDefault()
          load_next_image()
        }
        else if (e.keyCode === 112 || e.keyCode === 80) {  //p
          e.preventDefault()
          load_previous_image()
        }
      });

      $(window).keydown(function (e) {
        if (e.keyCode === 39 ) { //right
          e.preventDefault()
          load_next_image()
        }
        else if(e.keyCode === 37 ) { //left
          e.preventDefault()
          load_previous_image()
        }
      });
    });


    function clear_canvas()
    {
      paper.project.activeLayer.removeChildren();
    };


    function fix_size()
    {
      if(paper.project.activeLayer.children.length!=0)
      {
        fixed_w=Math.abs($('#ul_corner_x').val()-$('#lr_corner_x').val());
        fixed_h=Math.abs($('#ul_corner_y').val()-$('#lr_corner_y').val());
        paper.project.activeLayer.children[0].strokeColor = 'red';
        paper.project.activeLayer.children[0].strokeWidth= 5;
        $("#centre_x").val(Math.round(Math.abs((end_x-start_x)/2)));
        $("#centre_y").val(Math.round(Math.abs((end_y-start_y)/2)));
      }
    };

    function load_next_frame()
    {
      if(image_num>=100)
      clearInterval(interval);
      image_num+=1;
      $('#frame_num').text(image_num);
      $("#canvas").css('background-image',"url("+url+"Kinect_"+("00000" + image_num).slice(-4)+".jpg)");

    }


    </script>



    <script type="text/paperscript" canvas="canvas">
    function draw_fixed_rect()
    {
      clear_canvas();

      $("#centre_x").val(new_centre_x);
      $("#centre_y").val(new_centre_y);

      var rect = new Shape.Rectangle({
        x: new_centre_x -fixed_w/2,
        y: new_centre_y - fixed_h/2,
        width: fixed_w,
        height: fixed_h,
        strokeColor: 'red',
        strokeWidth: 4,
      });
      var c1= new Shape.Circle({
        center: new Point(new_centre_x ,new_centre_y - fixed_h/2),
        radius: 4,
        strokeColor: 'red',
        strokeWidth: 1,
        fillColor: 'red',
      });
      var c2= new Shape.Circle({
        center: new Point(new_centre_x ,new_centre_y + fixed_h/2),
        radius: 4,
        strokeColor: 'red',
        strokeWidth: 1,
        fillColor: 'red',
      });
      var c3= new Shape.Circle({
        center: new Point(new_centre_x -fixed_w/2 ,new_centre_y ),
        radius: 4,
        strokeColor: 'red',
        strokeWidth: 1,
        fillColor: 'red',
      });
      var c4= new Shape.Circle({
        center: new Point(new_centre_x +fixed_w/2 ,new_centre_y),
        radius: 4,
        strokeColor: 'red',
        strokeWidth: 1,
        fillColor: 'red',
      });

    };





    $('#canvas').mousedown(function(e) {
      offset = $(this).offset();
      on_handle=0;
      if(fixed_w!=-1) //draw fixed size rec centred at the clicked point
      {
        //console.log(paper.project.activeLayer.children.length)
        //check if we clicked on a resizing handle first
        if(paper.project.activeLayer.children.length>1)
        {
          if(paper.project.activeLayer.children[1].contains(e.pageX - offset.left,e.pageY - offset.top))
          {
            //log the action
            on_handle=1;
          }
          else if(paper.project.activeLayer.children[2].contains(e.pageX - offset.left,e.pageY - offset.top))
          {
            //log the action
            on_handle=2;
          }
          else if(paper.project.activeLayer.children[3].contains(e.pageX - offset.left,e.pageY - offset.top))
          {
            //log the action
            on_handle=3;
          }
          else if(paper.project.activeLayer.children[4].contains(e.pageX - offset.left,e.pageY - offset.top))
          {
            //log the action
            on_handle=4;
          }
          else {
            new_centre_x=e.pageX - offset.left;
            new_centre_y=e.pageY - offset.top;
            draw_fixed_rect(new_centre_x,new_centre_y);
          }
        }

        else
        {
          new_centre_x=e.pageX - offset.left;
          new_centre_y=e.pageY - offset.top;
          draw_fixed_rect(new_centre_x,new_centre_y);
        }
      }
      else {
        if(start_x==-100)
        {
          start_x=Math.round(Math.min(W,Math.max(0,e.pageX - offset.left)));
          start_y=Math.round(Math.min(H,Math.max(0,e.pageY - offset.top)));
          $('#ul_corner_x').val(start_x);
          $('#ul_corner_y').val(start_y);
        }
      }

    });


    $('#canvas').mouseup(function(e){
      start_x=-100;   //reset
      if(on_handle==1)
      {
        diff=e.pageY - offset.top - (new_centre_y - fixed_h/2)
        fixed_h -= diff
        new_centre_y += diff/2
        draw_fixed_rect(new_centre_x,new_centre_y);
        on_handle=0;
      }
      else if(on_handle==2)
      {
        diff=e.pageY - offset.top - (new_centre_y + fixed_h/2)
        fixed_h += diff
        new_centre_y += diff/2
        draw_fixed_rect(new_centre_x,new_centre_y);
        on_handle=0;
      }
      else if(on_handle==3)
      {
        diff=e.pageX - offset.left - (new_centre_x - fixed_w/2)
        fixed_w -= diff
        new_centre_x += diff/2
        draw_fixed_rect(new_centre_x,new_centre_y);
        on_handle=0;
      }
      else if(on_handle==4)
      {
        diff=e.pageX - offset.left - (new_centre_x + fixed_w/2)
        fixed_w += diff
        new_centre_x += diff/2
        draw_fixed_rect(new_centre_x,new_centre_y);
        on_handle=0;
      }



    });


    function onMouseDrag(e) {
      if(start_x==-100) return;
      clear_canvas();

      end_x=Math.round(Math.min(W,Math.max(0,e.point.x)));
      end_y=Math.round(Math.min(H,Math.max(0,e.point.y)));

      $('#lr_corner_x').val(end_x);
      $('#lr_corner_y').val(end_y);

      var rect = new Shape.Rectangle({
        from: [start_x,start_y],
        to: [end_x, end_y],
        strokeColor: 'white',
        strokeWidth: 2,
      });
    }

    //alert([new_centre_x,new_centre_y])
    draw_fixed_rect()
    </script>

  </head>
  <body>
    <center>
      <div>
        Object to track: <b><?php echo $obj->name?></b><br>
        Frame <span id='frame_num'>1</span> /<span id='span_total'></span>
        <div>
          <canvas id='canvas' style='width:600px;height:338px' ></canvas>
        </div>
        <div>
          <input type='hidden' id='ul_corner_x' style='width:30px'><input type='hidden' id='ul_corner_y'  style='width:30px'>
          <input type='hidden' id='lr_corner_x'  style='width:30px'><input type='hidden' id='lr_corner_y'  style='width:30px'>
        </div>
        <div><input type="button" id='clear_rects' value='Clear'
          style="height:50px;width:100px">
          <input type="button" id='play' value='Play' onclick='interval=setInterval( function(){load_next_frame();}, 20 );'
          style="height:50px;width:100px">
          <!--input type="button" id='save_frame' value='Save'
          style="height:50px;width:100px"-->
          &nbsp;&nbsp;<input type="checkbox" id='fix_size' value='Fix size'
          style="height:20px;width:20px">Fix size


          <p>
            <input type='hidden' id='centre_x' style='width:30px'><input type='hidden' id='centre_y'  style='width:30px'>
          </p>

          <p> Press <b>N</b> or <b>&rarr;</b> for next image, <b>P</b> or <b>&larr;</b> for previous image</p>

        </div>
      </center>

    </body>
    </html>
