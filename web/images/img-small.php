<?php

#######################################################################
## The disclosed code and program is subject to copyright, 
## patent and other intellectual property protections.  
## This publication of the code does not represent a license 
## or permission to use it or any part of it, and is not a 
## grant of permission to modify or make derivative works of 
## the code or program.  If you would like to make 
## modifications or use it for commercial or non-commercial uses, p
## lease email Dr. Stephen J. O'Brien at lgdchief@gmail.com with your 
## request, affiliation, intended use, and contact information, 
## and Dr. O'Brien will contact you.
#######################################################################


header('Content-Type: image/png');

require_once('../../config/config.php');

$h = 30;
$w = 100;

//$h = (int)$_GET["height"];
//$w = (int)$_GET["width"];
$im = imagecreatetruecolor(180, 25);
imagesavealpha($im, true);

$t_colour = imagecolorallocatealpha($im, 0, 0, 0, 127);
imagefill($im, 0, 0, $t_colour);

 
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 0, 0, 0);
$colors = array(imagecolorallocate($im,245,185,15), imagecolorallocate($im,65,107,236), imagecolorallocate($im,48,149,84),imagecolorallocate($im,208,79,53));
shuffle($colors);


$text = 'GWATCH';
$arr1 = str_split($text); 
$font = $rootpath . "/htdocs/font/Artifika-Regular.ttf";

$temp_x = 0;
for($i = 0; $i < count($arr1); ++$i) {
	if ($i==1 or $i==5) {
		$box = imagettftext($im, 25, 0, $temp_x, 25, $grey, $font, $arr1[$i]);
		$temp_x += $box[2] - $box[0];
	} elseif ($i==4) {
		$box = imagettftext($im, 25, 0, $temp_x, 25, $colors[1], $font, $arr1[$i]);
		$temp_x += $box[2] - $box[0];
	} else {
		$box = imagettftext($im, 25, 0, $temp_x, 25, $colors[$i], $font, $arr1[$i]);
		$temp_x += $box[2] - $box[0];
	}
}



imagepng($im);
imagedestroy($im);







?>