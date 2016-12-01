<?php
$x=json_decode($_REQUEST['x']);
$y=json_decode($_REQUEST['y']);
$w=json_decode($_REQUEST['w']);
$h=json_decode($_REQUEST['h']);
$vid=$_REQUEST['vid'];
$obj=$_REQUEST['obj'];

$time=date('ymdHis');
$r=rand(1000,9999);

$path='vids/vid'.$vid.'/';
$annotation_path=$path."object_annotations/";

$output="";
for($i=0;$i<count($x);$i++)
  $output.=$x[$i].",".$y[$i].",".$w[$i].",".$h[$i]."\r\n";

file_put_contents ( $annotation_path."annotation_".$vid."_".$obj."_".$time."_"
  .$r.".txt" , "#video: ".$vid.", obj: ".$obj."\r\n"."#x,y,w,h\r\n".$output);

//file_put_contents ( $annotation_path."annotation_".$vid."_".$obj."_".$time."_"
//.$r.".txt" , "video: ".$vid."\r\n"."obj: ".$obj."\r\n"."x: ".$x.
//"\r\n"."y: ".$y."\r\n"."w: ".$w."\r\n"."h: ".$h."\r\n");


 ?>
