<?php
	$GLOBALS['ht_admin_page']='ajax_get_template_path';
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();
	
	if(isset($_ENV))
		$_ENV['REQUEST_URI']='/'.rand().microtime();
	if(isset($HTTP_ENV_VAR))
		$HTTP_ENV_VAR['REQUEST_URI']='/'.rand().microtime();
	if(isset($_SERVER))
		$_SERVER['REQUEST_URI']='/'.rand().microtime();
	$GLOBALS['htracer_cash_days']=0;

	function AcceptGlobal($Name)
	{
		if(isset($_GET[$Name]))
			$GLOBALS[$Name]=$_GET[$Name];
	}
	if(!isset($GLOBALS['htracer_cloud_randomize']))
		$GLOBALS['htracer_cloud_randomize']=1;
	if(!isset($GLOBALS['htracer_cloud_links']))
		$GLOBALS['htracer_cloud_links']=20;
	if(($GLOBALS['htracer_cloud_randomize'] * $GLOBALS['htracer_cloud_links'])>1000)
		die("Too musch links or randome");
		
	AcceptGlobal("htracer_cloud_links");
	AcceptGlobal("htracer_cloud_randomize");
	AcceptGlobal("htracer_cloud_min_size");
	AcceptGlobal("htracer_cloud_max_size");
	AcceptGlobal("htracer_cloud_style");
	$GLOBALS['htracer_encoding']='utf-8';
	include_once('../../HTracer.php');
	$GLOBALS['htracer_encoding']='utf-8';
	$k=get_keys_cloud();
	if(stripos($k,'<a ')===false)
		echo 'Probably in DB no keys.';
	echo $k;
?>