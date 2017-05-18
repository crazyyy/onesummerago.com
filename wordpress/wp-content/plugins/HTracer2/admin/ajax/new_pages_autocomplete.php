<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();

	$Domain=$_SERVER['SERVER_NAME'];	
	$term=$_GET['term'];
	if($term && $term!='/')
	{
		if($term[0]=='/')
		{
			$term='"'.trim(str_replace('/',' ',$term)).'"';
			$key="site:{$Domain} inurl:$term";
		}
		else
			$key="site:{$Domain} $term";
	}
	else
		$key="site:{$Domain}";
	$key=urlencode($key);
	$res=file_get_contents("http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=$key");

	$Array=Array();
	if(function_exists('json_decode'))
	{
		$res=json_decode($res);
		$res=$res->responseData->results;
	
		foreach($res as $cur)
		{
			$URL=$cur->unescapedUrl;
			$URL=str_replace(Array('\u003d','\u0026','\u003f'),Array('=','&','?'),$URL);
			$URL=explode('/',$URL,4);
			$URL='/'.$URL[3];
			$Array[]=Array('id'=>$URL,'label'=>$URL,'value'=>$URL);
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
			$URL=str_replace(Array('\u003d','\u0026','\u003f'),Array('=','&','?'),$URL);
			$Array[]=Array('id'=>$URL,'label'=>$URL,'value'=>$URL);
		}
	}
	echo json_encode($Array);	
?>