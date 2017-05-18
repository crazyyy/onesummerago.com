<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();
	error_reporting(E_ERROR | E_PARSE);

	include_once('../../keysyn/keysyn_fun.php');

	
	$q = $_GET["q"];
	if (!$q) 
		die(json_encode(Array()));			
	$Array=GetWordStat($q,4);	
	$Array=$Array['Keys'];
	$Array2=Array();
	$was=Array();
	foreach($Array as $key => $eva)
	{
		$key=trim(mb_strtolower($key,'utf-8'));
		if(!isset($was[$key]))
			$Array2[]=Array('key'=>$key,'eva'=>$eva);
		$was[$key]=1;
	}
	echo json_encode($Array2);	
?>