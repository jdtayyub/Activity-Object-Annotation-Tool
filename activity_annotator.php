<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="includes/player.js"></script>
<script type="text/javascript" src="includes/tableexportlib/tableExport.js"></script>
<script type="text/javascript" src="includes/tableexportlib/jquery.base64.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="includes/timeline.js"></script>
<script type='text/javascript' src='includes/jquery.tipsy.js'></script>
<link rel="stylesheet" href="includes/tipsy.css" type="text/css" />
<script>
$( function() {
    $( "#slider" ).slider();
});
</script>


<?php

if(!isset($_REQUEST['assignmentId']))
  $assignment_id="ASSIGNMENT_ID_NOT_AVAILABLE";
else
  $assignment_id = $_REQUEST['assignmentId'];


if(!isset($_REQUEST['workerId']))
  $worker_id="";
else {
  $worker_id=$_REQUEST['workerId'];
}

if(!isset($_REQUEST['vid']))
  $vid=1;
else
  $vid=$_REQUEST['vid'];


$path="vids/vid".$vid;
$images_path=$path."/images";


$fi = new FilesystemIterator($images_path, FilesystemIterator::SKIP_DOTS);
$image_count=iterator_count($fi);

if (($data = file("Qualification_Quations.csv")) !== FALSE) {
    $pieces = explode(",",$data[$vid]);
    fclose($handle);
}



$start_timestamp=time();


 ?>
<script>
image_count=<?php echo $image_count;?>;
vid=<?php echo $vid; ?>;
images_path="<?php echo $images_path;?>";
var assID = "<?php echo $assignment_id; ?>";
var wID = "<?php echo $worker_id; ?>";
var start = "<?php echo $start_timestamp; ?>";

</script>



<link rel="stylesheet" type="text/css" href="includes/style.css">
</head>
  <body>

  <div id='div_loading' style='position: absolute;
    width: 100%;
    height: 130%;
    top: 338px;
    background-color:white;
    font-size:30pt;
    text-align:center;
    z-index:999;
    vertical-align: middle;

    font-family:arial'><span style='display: inline-block;height: 100%;vertical-align: middle;'><img style='vertical-align: middle;' src='includes/media/loading.gif'></style></div>


  <div id="page-wrap">
    <!-- all websites HTML here -->


    <h1> Activity Annotation Tool </h1>
    <div id= 'instructions' style='float:left;'>
       <h2 style="margin:0;">Instructions:</h2>

<!--
       <p>You are required to use the provided annotation tool to watch the video and complete sections 1 and 2. In section 1, you simply have to answer the questions relating to the video. In section 2, you are required to identify any activities taking place and label it clearly with a short phrase (<b>2-3 words only and without punctuation marks</b>). For example, activities in a kitchen scenario might be &lsquo;cooking dinner&rsquo;,  &lsquo;dicing an onion&rsquo; or &lsquo;picking up spoon&rsquo;, etc. Note that activities may occur concurrently or in sequence, it is therefore perfectly valid to create labels that overlap or completely cover segments of other labels. You are requested to keep adding labels until you sufficiently cover all activities occurring in the scene. You will be paid <b>substantial bonuses</b> (up to $10) according to the <b>number</b> of activities you identified at different levels, the quality of the labels and the accuracy of the start/end times. Please allow upto<b> a minute </b>for the video to load.</p>
       <p>To add a label, scroll through the video frame be frame and click &lsquo;Set Start Frame&rsquo; button when you consider a new activity has started. Again, click &lsquo;Set End Frame&rsquo; button when you think the activity finishes. Finally, write the corresponding activity label in the provided field and click &lsquo;Add Annotation&rsquo;. Your annotation will appear in the annotation table on the right hand side.<div style = 'display:none;'><?php echo $vid; ?></div>
</p>
 -->
    <p>Please watch the video below and then (1) answer the two questions below, and (2) use the annotation tool to identify as many activities as you can from the perspective of one of the actors.</p>
       <p style='margin-top: 0;'>You are encouraged to identify activities that overlap in time and form constituents of one another. For example, in a kitchen scenario, "dicing onion" might occur as part of "cooking dinner".</p>
       <p style='margin-top: 0; margin-bottom:0;'>To identify an activity:</p>
       <ul>
       <li>Use the controls to move to the frame at which the activity starts and press "Set Start Frame", and likewise for "Set End Frame";</li>
       <li>Enter a short label (1-3 words) describing the activity from the perspective of the chosen actor - for example, "cooking dinner", "dicing onion", "picking up spoon";</li>
       <li>Press "Add Activity" to add into the table on the right-hand side. If you wish to remove an identified activity, you can delete it from the table.</li>
       </ul>
       <p style='margin-top: 0;'>You will be paid ?? for each activity appearing in the table, and a bonus of ?? per activity if you provide concise and appropriate labels, accurate start/end times and a spread of overlapping activities that are constituents of one another.</p>
       <p style='margin-top: 0;'>Allow up to a minute for the video to load.</p>
    

 <?php if(strcmp($assignment_id,"ASSIGNMENT_ID_NOT_AVAILABLE")==0){?>
  <p> <b>You will only be able to see a small section of the video during the preview mode.</b></p>
  <?php } ?>
    </div>
  <div class = "line" style=" float:left; width:100%;"></div>

   <div id = "questions" style = "float:left;">
   <h3> Section 1 </h3>
         <div id='ques1'><?php echo $pieces[1]; ?></div><input id='ans1' style="width:25px;" maxlength="1" min="0" max="9" type="number"></input>
         <div id='ques2'><?php echo $pieces[2]; ?></div><input id='ans2' style="width:25px;" maxlength="1" type="text"></input>
   </div>

   <div class = "line" style=" float:left; width:100%;"></div>

      <div id = 'player-container'>
        <p>Current Frame Number : <p id = "frameNum">0001</p>
        <div id='player'>
          <div class = 'image'></div>
          <div id="slider"></div>

          <div class = 'buttons'>

            <div id = 'preBulk' class = 'fineSeek' title="-10 Frames" ></div>
            <div id = 'preFrame' class = 'fineSeek' title="-1 Frame"></div>

            <div id = 'play-pause' class='paused' title="Play"></div>


            <div id = 'nexFrame' class = 'fineSeek' title="+1 Frame"></div>
            <div id = 'nexBulk' class = 'fineSeek' title="+10 Frames"></div>

          </div>
        </div>

        <div class = "line" style=" float:left; width:100%;"></div>

         <div id ="buttons">
        <h3 style = "text-align: left;"> Section 2 </h3>

        <div class = "field"><p>Start Frame</p><input readonly class = "sframe" type="number" min="1" max="2000" name="sframe" ><button class="myownbutton getFrame">Set Start Frame</button></div>
        <div class = "field"><p>End Frame</p><input readonly class = "eframe" type="number" min="1" max="2000" name="eframe" ><button class="myownbutton getFrame">Set End Frame</button></div>

        <div class = "field label"><p>Activity Label</p>
          <input class = "actLabel" type="text" name="actlabel" ><span style="display:block; color:red;" id="length_lbl">
Warning: Please use 2-3 words only to describe the activity. Consider adding each activity in your description as a seperate entry rather than combining them all into a single description.</span>
          <span style="display:block; color:red;">(Short phrase only: 2-3 words)</span>

        </div>
        <button class="myownbutton addAnnon">Add Activity</button>
        </div>
        <div class = "line" style=" float:left; width:100%;"></div>
      </div>



      <div id = "controls">

        <div id = "ann-table">
          <table>

          <thead>
            <th>Activity Label</th>
            <th>Start Frame</th>
            <th>End Frame</th>
            <th>Delete</th>
          </thead>

          <tbody class="row-elements">
          </tbody>
          </table>
        </div>


          <div id='intervalCont'>
           <h3 class='heading'>Timeline</h3>
           <div id='mainWrapper'>
             <div id='mainCont'>
               <div class='intervalRow' id='row-1'>
                 <div class='dashed'></div>
               </div>
               <div class='intervalRow' id='row-2'>
                 <div class='dashed'></div>
               </div>
             </div>
             <div id='frameAxis'>
                <div class='axisLine' ></div>
                <div class='ticks'>
                </div>
             </div>
           </div>
         </div>
         <p class = 'imp'>Please complete annotating the entire video before submitting your answer.</p>

         <?php

         if(strcmp($assignment_id,"ASSIGNMENT_ID_NOT_AVAILABLE")!=0){?>
  <!-- https://workersandbox.mturk.com/mturk/externalSubmit , https://www.mturk.com/mturk/externalSubmit-->
	 <form id='sendData' method="post" action="https://www.mturk.com/mturk/externalSubmit">
	 <input type="hidden" name="assignmentId" value="<?php echo $assignment_id;?>">
   <input type="hidden" name="a" id = 'a' value="NOTHING">
   <input type="hidden" name="ans1" id = 'answer1' value="NOTHING">
   <input type="hidden" name="ans2" id = 'answer2' value="NOTHING">
   <input type="hidden" name="dur" id = 'dur' value="NOTHING">

	 </form>
         <a href="#" class="myButton submit">Submit Answer</a>

	 <?php } ?>

         <div>


      </div>

  </body>
</html>
