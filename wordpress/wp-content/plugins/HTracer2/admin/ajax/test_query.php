<?php
	$GLOBALS['ht_admin_page']='ajax_query';
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();
	
	function AcceptGlobal($Name)
	{
		$GLOBALS[$Name]=$_GET[$Name];
	}
	AcceptGlobal("htracer_trace_sex_filter");
	AcceptGlobal("htracer_trace_free_filter");
	AcceptGlobal("htracer_trace_download_filter");
	AcceptGlobal("htracer_trace_service_filter");
	AcceptGlobal("htracer_symb_white_list");
	AcceptGlobal("htracer_mats_filter");


	AcceptGlobal("htracer_numeric_filter");
	AcceptGlobal("htracer_not_ru_filter");

	AcceptGlobal("htracer_user_minus_words");

	


	include_once('../../HTracer.php');
	$res=HTracer::isSpecQuery_explain($_GET['q'],true);
	if(!$res)
		$res=Array(0,0,0);
	echo json_encode($res);
?>