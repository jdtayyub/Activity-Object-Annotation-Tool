<?php
$vid=$_REQUEST['vid'];
$ann=$_REQUEST['ann'];

$wID=$_REQUEST['wID'];

$ans1= $_REQUEST['answer1'];
$ans2= $_REQUEST['answer2'];

$dur=$_REQUEST['dur'];

$time=date('ymdHis');
$r=rand(1000,9999);

$path='vids/vid'.$vid.'/';
$annotation_path=$path."activity_annotations/";


file_put_contents ( $annotation_path."annotation_".$vid."_".$wID."_".$time."_"
.$r.".txt" , "#video ".$vid."\r\n".$ann);

file_put_contents ( $annotation_path."qualTestAnswers_".$vid."_".$wID."_".$time."_"
.$r.".txt" , "#video ".$vid."\r\n".$ans1.",".$ans2.",".$dur);


 ?>
