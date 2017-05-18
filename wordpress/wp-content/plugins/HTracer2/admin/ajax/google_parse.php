<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();
	$q = $_GET["q"];
	if (!$q)
		$q='test';
	//	return;
//	$text = file_get_contents("http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=".urlencode($q).'&callback=mfn&context=?'); //Get content from Google suggest
//	$text0=$text = file_get_contents(); //Get content from Google suggest

	include_once('../../keysyn/Snoopy.class.php');
	$snoopy = new Snoopy;
	$snoopy->agent 	 = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
	$snoopy->rawheaders["Accept"] = "text/html";
	$snoopy->rawheaders["Connection"] = "Keep-Alive";
	$snoopy->fetch("http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=".urlencode($q).'&callback=mfn&context=mvc');
	

	$text0=$text =  $snoopy->results;



	$text=explode('[',$text,2);
	if(count($text)!=2)
		die('x1::'.$text0);
	$text=$text[1];
	$text=explode(']',$text,2);
	if(count($text)!=2)
		die('x2::'.$text0);
	$text=$text[0];
	echo '{"results":['.$text.']}';
?>