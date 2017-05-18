<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();

	$term=$_GET['term'];
	$page=$_GET['page'];
	$CS=md5($page);

	$table_prefix=HTracer::GetTablePrefix();
	$mterm=mysql_real_escape_string($term);
	
	$sQuery = "
		SELECT `In`,`Out` FROM `{$table_prefix}htracer_queries`
		WHERE `URL_CS`='$CS'
			AND (`Out` LIKE '$mterm%'
			OR   `In`  LIKE '$mterm%')
		ORDER BY `Eva` DESC
		LIMIT 5
	";
	
	$rResult = htracer_mysql_query($sQuery);
	
	if(mysql_num_rows($rResult))
	{
		$Array=Array();
		while ($aRow = mysql_fetch_array($rResult))
		{
			if(isset($_GET['san_sugest']) && $_GET['san_sugest'])
				$Key=$aRow['Out'];
			else
				$Key=$aRow['In'];
			$Array[]=Array('id'=>$Key,'label'=>$Key,'value'=>$Key);
		}
		echo json_encode($Array);	
	}
	else
		include('google_suggest.php');
?>