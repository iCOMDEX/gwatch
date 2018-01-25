<?php

namespace GWatch\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class IndexController
{
 
    public function indexAction(Application $app, Request $request)
    {
  
  		 return $app['twig']->render('index.html.twig');

	}
	
    public function contactsAction(Application $app, Request $request)
    {
  
  		 return $app['twig']->render('contacts.html.twig');

	}
	
    public function showDatasetsAction(Application $app, Request $request)
    {
  
  
  		$data['modules'] = $app['repository.index']->findModules();
  
  		return $app['twig']->render('modules.html.twig', $data);

	}
	

    public function datasetsAction(Application $app, Request $request)
    {
 
  		 return $app['twig']->render('datasets.html.twig');

	}
	

    public function descriptionAction(Application $app, Request $request)
    {
  
  		 return $app['twig']->render('description.html.twig');

	}
	

    public function featuresAction(Application $app, Request $request)
    {
  
  		 return $app['twig']->render('features.html.twig');

	}
	

    public function paperAction(Application $app, Request $request)
    {
  
  		 return $app['twig']->render('paper.html.twig');

	}
	
    public function tutorialAction(Application $app, Request $request)
    {
  
  		 return $app['twig']->render('tutorial.html.twig');

	}
	
    public function feedbackAction(Application $app, Request $request)
    {
  
  		 return $app['twig']->render('feedback.html.twig');

	}

    public function uploadAction(Application $app, Request $request)
    {
  
  		 return $app['twig']->render('upload.html.twig');

	}
	
	
		public function imgcolorAction(Application $app, Request $request){
	
		header('Content-Type: image/png');

		$w = $request->query->get('width');
		$h = $request->query->get('height');

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

		imagettftext($im, $fs_small, 0, 0, $y, $black, $font, $text2);
		imagepng($im);
		imagedestroy($im);

	
	}

	public function smallImgcolorAction(Application $app, Request $request)
	{
		header('Content-Type: image/png');

		$h = 30;
		$w = 100;

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
		$font = "/home/gwatch/htdocs/font/Artifika-Regular.ttf";

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
		 

	}
	

	
}