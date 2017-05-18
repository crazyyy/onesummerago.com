<?php
	if(!headers_sent())
		header("Pragma: no-cache");
	$GLOBALS['ht_t_erl']=(E_ERROR | E_PARSE);
	error_reporting($GLOBALS['ht_t_erl']);
	include_once('../functions.php');
	error_reporting($GLOBALS['ht_t_erl']);
	
	if($_SERVER['SERVER_NAME']=='htest.ru'||$_SERVER['SERVER_NAME']=='projects')
		$_SERVER['SERVER_NAME']	 = 'visit.odessa.ua';
	
	
	function htracer_ajax_admin_header()
	{
		htracer_admin_header(false);
		error_reporting($GLOBALS['ht_t_erl']);
	}
?>