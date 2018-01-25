<?php

 

$dom = new DOMDocument('1.0', 'utf-8');


$box = $dom->createElement("use", null);
	
 	$box->setAttribute("xlink:href", "#222");
	$box->setAttribute("x",  'ksksk');
	 
	$dom->appendChild($box);

 echo $dom->saveXML();