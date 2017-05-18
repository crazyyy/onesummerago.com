<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();
	
	$mysql_auto_config=array('engine'=>false);
	
	//В целях юезопастности, если это не первый запуск, то данные автоопределения доступов не сообщаются.. 
	if(!file_exists(str_replace('new_admin','admin',dirname(__FILE__).'/auto_config.php')))
		$mysql_auto_config=auto_detect_mysql_config();
	echo json_encode($mysql_auto_config);	
?>