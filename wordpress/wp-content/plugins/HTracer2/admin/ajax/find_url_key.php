<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();
	$Domain=$_SERVER['SERVER_NAME'];
	$URL=HTracer::FixURL($_POST['url']);
	$Key=false;


	$table_prefix=HTracer::GetTablePrefix();
	$CS=MD5($URL);
	
	$sQuery = "
		SELECT *
		FROM `{$table_prefix}htracer_pages`
		WHERE `URL_CS`='$CS'
	";
	$rResult = htracer_mysql_query($sQuery);
	if($aRow = mysql_fetch_assoc($rResult))
	{
		$Key=$aRow['SecondKey'];
		if(!$Key)
			$Key=$aRow['FirstKey'];
	}
	echo $Key;	
?>