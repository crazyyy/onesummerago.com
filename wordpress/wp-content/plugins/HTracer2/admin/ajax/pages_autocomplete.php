<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();
	
	$Array=Array(
		Array('id'=>'1','label'=>'1','value'=>'1'),
		Array('id'=>'2','label'=>'2','value'=>'2'),
		Array('id'=>'3','label'=>'3','value'=>'3'),
	);
	$term=$_GET['term'];
	$Oper='';
	if($term{0}=='='||$term{0}=='<')
	{
		$Oper=$term{0};
		$term=substr($term,1);
	}
	
	$table_prefix=HTracer::GetTablePrefix();
	$mterm=mysql_real_escape_string($term);
	
	$sQuery = "
		SELECT `URL` FROM `{$table_prefix}htracer_pages`
		WHERE  `URL` LIKE '$mterm%'			
		ORDER BY `Eva` DESC
		LIMIT 10
	";
	$rResult = htracer_mysql_query($sQuery);

	$Array=Array();
	while ($aRow = mysql_fetch_array($rResult))
	{
		$URL=$aRow['URL'];
		$Array[]=Array('id'=>$URL,'label'=>$URL,'value'=>$Oper.$URL);
	}
	echo json_encode($Array);	
?>