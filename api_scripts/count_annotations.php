<?php

$dir="../vids";
$vids=scandir ($dir);
$c=0;
foreach( $vids as $vid )
{
  $dir2=$dir."/".$vid."/activity_annotations/";
  $files=scandir ($dir2);

  echo "<b>".$dir2."<br></b>";

  foreach($files as $file)
  {
    if(strpos($file,"annotation_")!==false)
    {
      $c++;
      echo $c." ".$file."<br>";

    }
  }
}




?>
