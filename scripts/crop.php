<?php
$w=270;
$h=180;  
$x=0;    $y=0;    
$filename=$_GET['src'];
header('Content-type: image/jpg');
header('Content-Disposition: attachment; filename='.$src);
$image = imagecreatefromjpeg($filename); 
$crop = imagecreatetruecolor($w,$h);
imagecopy ( $crop, $image, 0, 0, $x, $y, $w, $h );
imagejpeg($crop);
?>