<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();

	$Domain=$_SERVER['SERVER_NAME'];	
	$key=$_POST['key'];
	$ex=false;
	if(isset($_POST['ex']))
		$ex=$_POST['ex'];
		
	$t=0;	
	if(isset($_POST['t']))
		$t=$_POST['t'];
	$key="site:{$Domain} $key";
	
	
	$key=urlencode($key);
	$res=file_get_contents("http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=$key");
	//echo $res;
	//exit();

	$Array=Array();
	if(function_exists('json_decode'))
	{
		$res=json_decode($res);
		$res=$res->responseData->results;
		foreach($res as $cur)
		{
			$URL=$cur->unescapedUrl;
			$URL=explode('/',$URL,4);
			$URL='/'.$URL[3];
			$Array[]=$URL;
		}
	}
	else
	{
		$res=explode('"unescapedUrl":"',$res);
		unset($res[0]);
		foreach($res as $cur)
		{
			$cur=explode('"',$cur);
			$URL=$cur[0];
			$Array[]=$URL;
		}
	}
	//echo HTracer::FixURL("http://ajax.googleapis.com/ajax/servic");
	
	if(isset($Array[$t]))
	{
		$Array[$t]=str_replace('\u003d','=',$Array[$t]);
		$Array[$t]=str_replace('\u0026','&',$Array[$t]);
		$Array[$t]=str_replace('\u003f','?',$Array[$t]);
		
		if(HTracer::FixURL($Array[$t])!==HTracer::FixURL($ex))
			echo HTracer::FixURL($Array[$t]);
		elseif(isset($Array[$t+1]))
		{
			$Array[$t+1]=str_replace('\u003d','=',$Array[$t+1]);
			$Array[$t+1]=str_replace('\u0026','&',$Array[$t+1]);
			$Array[$t+1]=str_replace('\u003f','?',$Array[$t+1]);
			echo HTracer::FixURL($Array[$t+1]);
		}
	}
?>