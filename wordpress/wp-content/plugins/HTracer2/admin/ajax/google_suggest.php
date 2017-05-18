<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();
	
	$q = $_GET["term"];
	if (!$q) 
		return;
	function HT_GetSuggestItemsArray($q)
	{
		$url="http://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=ru&q=".urlencode($q);		$text = file_get_contents($url); //Get content from Google suggest
		$text = file_get_contents($url); //Get content from Google suggest
		$text=str_replace(Array('"]]',']]'), '', $text); 
		$text = explode('",["', $text);
		return explode('","', $text[1]);
	}
	$arr_items=HT_GetSuggestItemsArray($q);
	if(isset($_GET["stem"]))
	{
		$q2 = trim(str_replace('  ',' ',str_replace('  ',' ',$_GET["term"])));
		$q2 = explode(' ',$q2);
		$l  = $q2[count($q2)-1];
		$l  = HkStrem($l);
		if($l && $l!= $q2[count($q2)-1])
		{
			$arr_items2=HT_GetSuggestItemsArray($q2);
			foreach($arr_items2 as $item)
				$arr_items[]=$item;
		}
	}
	$Array=Array();
	$was=Array();
	if(isset($_GET['showfirstkey']))
	{
		$Array[]=Array('id'=>trim($q),'label'=>trim($q),'value'=>trim($q));
		$was[$q]=$q;
		$was[trim($q)]=trim($q);
	}
	foreach($arr_items as $items)
	{        
		$arr_item=explode(",",$items);
        $key=$arr_item[0]; //Get the keyword, the arrary will have other details such as no.of resutls also.
        $key=trim($key,"\""); //Use to remove quotes
		$key2=$key;
		if(function_exists('mb_detect_encoding'))
		{
			$encoding=mb_detect_encoding($key,Array('cp1251','utf-8'));
			$key2=mb_convert_encoding($key,'utf-8',$encoding);
		}
		if(isset($_GET['san_sugest']) && $_GET['san_sugest'])
			$key2=HTracer::Sanitarize($key2);
		if(strlen($key2) && $key2[strlen($key2)-1]==' ')
		{
			$key2=trim($key2);
			$key2.=' ';
		}
		else
			$key2=trim($key2);
			
		if(!$key2 ||!trim($key2) || isset($was[$key2])||strlen(trim($key2))<2)
			continue;
		if(isset($_GET['showfirstkey']))
		{
			if(isset($was[trim($key2)]))
				continue;
			$was[trim($key2)]=$key2;
		}
		else
			$was[$key2]=$key2;
		$Array[]=Array('id'=>$key2,'label'=>$key2,'value'=>$key2);
	}
	if(isset($_GET['showfirstkey']) && count($Array)===1)
		$Array=Array();
	echo json_encode($Array);	
?>