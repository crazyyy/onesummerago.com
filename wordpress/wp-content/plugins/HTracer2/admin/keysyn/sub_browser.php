<?php
	die();
	include_once("Snoopy.class.php");
	error_reporting (E_ERROR | E_PARSE| E_WARNING);
	
	set_time_limit(100);
	$snoopy = new Snoopy;
	$snoopy->agent 	 = "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13";
	$snoopy->rawheaders["Accept"] = "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
	$snoopy->rawheaders["Accept-Language"] = "ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3";
	$snoopy->rawheaders["Accept_charset"] = "windows-1251,utf-8;q=0.7,*;q=0.7";
	$snoopy->rawheaders["Accept_encoding"] = "identity";
	$snoopy->rawheaders["Connection"] = "Keep-Alive";
	if($_GET['status'])
		$snoopy->_httpmethod='HEAD';
	//$_GET['url']='http://huntress.ru/xxx';
	$snoopy->fetch($_GET['url']);
	if($_GET['status'])
	{
		echo $snoopy->status;
		exit;
	}
	//
	//$snoopy->results='';
	//foreach($snoopy->headers as $header)
	//	header($header);
	
//	$html = str_get_html($snoopy->results);
//	echo $html; 
	//$snoopy->results=mb_convert_encoding($snoopy->results,'utf-8','cp1251');
//    * Naaeaou noa?oiaie

	$snoopy->results=str_replace(
		Array('</head>','</Head>','</HEAD>'),
		'<meta http-equiv="content-type" content="text/html; charset=utf-8"></head>',
		$snoopy->results);

	$dom = new domDocument();
    @$dom->loadHTML($snoopy->results);
	$dom->preserveWhiteSpace = false;

	$Base='';
	$nodelist=$dom->getElementsByTagName('a');
	$nb = $nodelist->length;
	for($pos=0; $pos<$nb; $pos++)
		$nodelist->item($pos)->getAttribute('href');
	if(!$Base || strpos($Base,'http://')!==0)
	{
		$tmp=split('\/',$_GET['url']);
		if(count($tmp)>3)
			$tmp[count($tmp)-1]='';
		$Base=$Base.join('/',$tmp);	
	}
	if($Base{strlen($Base)-1}!='/')
		$Base.='/';
	$Base2=trim($Base,'/');
	//echo $Base;
	//exit();
	$SB="http://htracer.ru/keysyn/sub_browser.php?url=";
	
	$nodelist=$dom->getElementsByTagName('a');
	$nb = $nodelist->length;
	for($pos=0; $pos<$nb; $pos++)
	{
		$href=$nodelist->item($pos)->getAttribute('href');
		$href=str_replace($Base,'',$href);
		$href=str_replace($Base2,'',$href);
		if(strpos($href,'http:')!==0)
			$href=$SB.urlencode($Base.$href);
		else
			$href=$SB.urlencode($href);
		$nodelist->item($pos)->setAttribute('href',$href);
		$nodelist->item($pos)->setAttribute('target','_self');
	}
	//	$link->href="#######";
	//echo $dom->saveHTML();
	//echo mb_convert_encoding($dom->saveHTML(),'utf-8','cp1251');
	echo $dom->saveHTML();
	//echo $snoopy->results;
	
	//print_r($snoopy);
?>