<?php

class video_object{
  var $name,  $x,  $y,  $w,  $h,  $sf,  $ef;

  // function init($Obj_properties)
  // {
  //   $this->name=$Obj_properties[0];
  //   $this->x=$Obj_properties[1];
  //   $this->y=$Obj_properties[2];
  //   $this->w=$Obj_properties[3];
  //   $this->h=$Obj_properties[4];
  //   $this->sf=$Obj_properties[5];
  //   $this->ef=$Obj_properties[6];
  // }

  function init($Obj_properties)
  {
    $this->name=$Obj_properties[0];
    $this->x=($Obj_properties[1]+$Obj_properties[3])/2;
    $this->y=($Obj_properties[2]+$Obj_properties[4])/2;
    $this->w=$Obj_properties[3]-$Obj_properties[1];
    $this->h=$Obj_properties[4]-$Obj_properties[2];
    $this->sf=$Obj_properties[5];
    $this->ef=$Obj_properties[6];
  }
};


?>
