<?php
namespace GWatch\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class DisplayController
{
   
    public function indexAction(Application $app, Request $request)
    {
    
   
		$module = trim($request->query->get('module'));
		$chr = trim($request->query->get('chr'));
 		$data['module'] = $module;
 		$data['chr'] = $chr;	 
 
		$data['polarized'] = ( $request->query->get('polarized') ? 'true' : 'false' );
		$data['UrlRow'] = $app['repository.display']->getCurrentRow($request->query->get('row'));
		$data['distance'] =  $app['repository.display']->getDistance($request->query->get('distance'));
		$data['anglex'] = $app['repository.display']->getAngleX($request->query->get('anglex'));
    	$data['offsetx'] = $app['repository.display']->getOffsetX($request->query->get('offsetx'));
		$data['eyepos'] = $app['repository.display']->getEyePos($request->query->get('eyepos'));
 		$data['CurrentURL'] = $app['repository.display']->getCurrentURL();
 		$data['BuildAndPlatform'] = $app['repository.display']->GetBuildAndPlatform($module);
 		 
 	
    
		return $app['twig']->render('display.html.twig', $data);

	}
    public function columnsAction(Application $app, Request $request)
    {
 
		$module = trim($request->query->get('module'));
		$chr = trim($request->query->get('chr'));
		
  
		$colInfo = $app['repository.display']->ReadColumnInfo( $module );

		$rowCount = $app['repository.display']->ReadRowCount( $module, $chr );

		// throw the row count in for good measure
		$colInfo[ "NumRows" ] = $rowCount;

		return $app->json($colInfo);
		

	}
	public function dataAction(Application $app, Request $request){
	
		$module = trim($request->query->get('module'));
		$chr = trim($request->query->get('chr'));
 		$data['module'] = $module;
 		$data['chr'] = $chr;

		$rowDataStart = $request->query->get('rowStart');
		$rowDataCount = $request->query->get('rowCount');
		$threshold = $request->query->get('threshold');



		if (!isset($rowDataStart) || !isset($rowDataCount))
		{
	
			$app['repository.display']->ReturnError("Missing Params");
	
		}

	 

		if ($app['useCacheFile'])
		{

			// if there is a cache file that matches the data we require,
			// just return that.

			$cacheFileName = "/home/newgwatch/cache/$module.$chr.$rowDataStart.$rowDataCount.cache";
			if (file_exists($cacheFileName))
			{
				echo file_get_contents($cacheFileName);
				exit(0);
			}
		
		}

 
		$colInfo = $app['repository.display']->ReadColumnInfo($module);

		// echo json_encode( $colInfo );

		$numColumns = $colInfo["NumColumns"];
		$numDataColumns = $colInfo["NumDataColumns"];
 
		$rowCount = $app['repository.display']->ReadRowCount($module, $chr);
 
		// true means return the values as heights

		$dataBlock = $app['repository.display']->ReadRowData($module, $chr, $numDataColumns, $rowCount, $rowDataStart, $rowDataCount, 1, 6.0);
 
	//print_r($dataBlock);	 
	
		return $app->json($dataBlock);

		if ($app['useCacheFile'])
		{

			// We write to a temp file and then rename it to
			// the right file, so that if two clients are trying
			// to write the same cache file, only one will
			// succeeed and we can ensure it is valid.

			$uniqer = substr(md5(uniqid(rand() , 1)) , 0, 5);
			$tmpFile = "$cacheFileName.$uniqer";

			// dump the cache to the temp file

			$cacheHandle = fopen($tmpFile, "w");
			fputs($cacheHandle, $json);
			fclose($cacheHandle);

			// rename it to the right name

			if (!rename($tmpFile, $cacheFileName))
			{

				// somebody else beat us to it?
				// just delete the temp file

				unlink($tmpFile);
			}
				
		}
		
		
	
	}
	
	public function searchAction(Application $app, Request $request){
	 
		$module = $request->query->get('module');
		$chr = $request->query->get('chr');
		if ( ! $module )
		{
			ReturnError("No module specified ");
		}
		if ( ! $chr )
		{
			ReturnError("No chr specified ");
		}
		$type = trim($request->query->get('type'));
		$searchTerm = trim($request->query->get('search'));
		$searchType = $app['repository.display']->GetSearchType($searchTerm);

		if ( !isset( $searchTerm ) || !isset( $searchType ) ) 
		{
		
			ReturnError("Missing Params");
		}



		$colInfo = $app['repository.display']->ReadColumnInfo( $module );

		$numColumns = $colInfo[ "NumColumns" ];

		$numDataColumns = $colInfo[ "NumDataColumns" ];

		$rowCount = $app['repository.display']->ReadRowCount( $module, $chr );

	

 		//$chrdescr =	$app['repository.display']->getChrDescription($module);
		
		return $app->json( $app['repository.display']->search($module, $chr, $searchTerm, $searchType, $type) );
		
 

	
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
	
	public function reportAction(Application $app, Request $request){
 	
		$reportType = trim($request->query->get('reportType'));
			if ( $reportType == "array" )
			{
			//echo 1;
 				$svgContent = $app['repository.display']->generateGWASArray($request);
				$report = '2D';
			}
			else if ( $reportType == "snapshot" )
			{
				$svgContent = $app['repository.display']->generateGWASSnapshot( $request, false );
				$report = '3D';
			}
			else if ( $reportType == "snapshot-polarized" )
			{
				$svgContent = $app['repository.display']->generateGWASSnapshot( $request, true );
				$report = '3D_pol';
			}
			else
			{
				echo "<h3>Unknown report type!</h3>";
				exit(0);
			}





			$format = trim($request->query->get('format'));
 			if ( $format == "svg")
			{
				// just dump the SVG file straight into the browser
				header('Content-type: image/svg+xml');
				echo $svgContent;
			}
			else if ( $format == "pdf" )
			{

				// write it to a temp file
				$tempFileName = "temp/" . substr(md5(uniqid(rand(),1)),0,5) . ".svg";
				$svgFile = fopen( $tempFileName, "w");
				fputs( $svgFile, $svgContent );
				fclose( $svgFile );
	
				// kick batik to convert it do a PDF
				$command = "java -Djava.awt.headless=true -Xms128m -Xmx512m -jar ../web/batik/batik-rasterizer.jar -m application/pdf $tempFileName 2>&1";
				$result = exec($command);
				if ( strpos( $result, "success" ) )
				{
					$fileName = $file; 
					list($a,$b,$c) = explode('_', $fileName);
		
					header('Content-type: application/pdf');
					header("Content-Disposition: attachment; filename=$report"."_". $module ."_". $chr ."_". $row .".pdf");
											 
					$pdfFile = str_replace( ".svg", ".pdf", $tempFileName );
		
					echo file_get_contents ( $pdfFile );
		
					unlink( $pdfFile );
		
				}
				else
				{
					echo $result;
				}

			unlink( $tempFileName );
 		}
 return false;
}
	
}