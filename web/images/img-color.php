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

$w = (int)$_GET["width"];
$h = (int)$_GET["height"];

if ($w<825) {
$width = 400;
$fs = 50;
$fs_small=10;
$temp_x = 30;
$y = 90;
} else {
$width = 800;
$fs = 80;
$fs_small=20;
$temp_x = 140;
$y = 110;

}

$im = imagecreatetruecolor($width, 120);
imagesavealpha($im, true);
$black = imagecolorallocate($im, 0,0,0);
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);

$t_colour = imagecolorallocatealpha($im, 0, 0, 0, 127);
imagefill($im, 0, 0, $t_colour);

$colors = array(imagecolorallocate($im,245,185,15), imagecolorallocate($im,65,107,236), imagecolorallocate($im,48,149,84),imagecolorallocate($im,208,79,53));
shuffle($colors);

$text = 'GWATCH';
$text2 = 'Genome-Wide Association Tracks Chromosome Highway';
$arr1 = str_split($text); 
$font = "/home/gwatch/htdocs/font/Artifika-Regular.ttf";
#$temp_x = 140;
for($i = 0; $i < count($arr1); ++$i) {
	if ($i==1 or $i==5) {
		$box = imagettftext($im, $fs, 0, $temp_x, 75, $grey, $font, $arr1[$i]);
		$temp_x += $box[2] - $box[0];
	} elseif ($i==4) {
		$box = imagettftext($im, $fs, 0, $temp_x, 75, $colors[1], $font, $arr1[$i]);
		$temp_x += $box[2] - $box[0];
	} else {
		$box = imagettftext($im, $fs, 0, $temp_x, 75, $colors[$i], $font, $arr1[$i]);
		$temp_x += $box[2] - $box[0];
	}
}

/*$temp_smallx = 0;
for($j = 0; $j < count($words); ++$j) {
	$arr = str_split($words[$j], 1); 
	$box = imagettftext($im, 20, 0, $temp_smallx, 110, $colors[$j], $font, $arr[0]);
	$temp_smallx += $box[2] - $box[0];
	for($i = 1; $i < count($arr); ++$i) {
			$box = imagettftext($im, 20, 0, $temp_smallx, 110, $mygrey, $font, $arr[$i]);
			$temp_smallx += $box[2] - $box[0];
		}
	$temp_smallx += 20;
	}*/
imagettftext($im, $fs_small, 0, 0, $y, $black, $font, $text2);
imagepng($im);
imagedestroy($im);







?>