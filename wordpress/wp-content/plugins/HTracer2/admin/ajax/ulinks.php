<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();
	
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => 0,
		"iTotalDisplayRecords" => 0,
		"aaData" => array()
	);
	if(!$_GET['iDisplayLength'])
	{
		echo json_encode($output);	
		return;
	}
		
	$aColumns = array( 'Key', 'aURL', 'Don');
	
	$sIndexColumn = "ID";
	$sTable = "htracer_ulinks";
	
	if($_GET['iDisplayLength'])
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
				mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	if(isset($_GET['idsort']))
		$sOrder='ORDER BY `ID` DESC ';
	if(isset($_GET['iSortCol_0']))
	{
		$sOrder = "ORDER BY  ";
		for ($i=0 ; $i<intval($_GET['iSortingCols']) ; $i++ )
		{
			if($aColumns[intval($_GET['iSortCol_'.$i])])
			if ($_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				if(!$_GET['sSortDir_'.$i]
				 ||strtolower($_GET['sSortDir_'.$i])=='asc' 
				 ||strtolower($_GET['sSortDir_'.$i])=='desc')
					$sOrder .= '`'.$aColumns[intval($_GET['iSortCol_'.$i])]."`
						".mysql_real_escape_string($_GET['sSortDir_'.$i]) .", ";
			}
		}
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ($sOrder == "ORDER BY")
			$sOrder = "";
	}
	$sWhere = "";
	if ($_GET['sSearch'] != "")
	{
		$wo=" LIKE '%".mysql_real_escape_string($_GET['sSearch'])."%' "; 
		$sWhere = " 
					WHERE ( `Key` $wo 
						OR 	`aURL` $wo
						OR 	`Don`  $wo)
				";
	}
		
	$aColumns2=Array();
	foreach($aColumns as $Column)
		$aColumns2[]= "t1.`$Column` as `$Column`";
	$table_prefix=HTracer::GetTablePrefix();
	
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS t1.ID as ID, ".join(", ", $aColumns2)."
		FROM  `{$table_prefix}$sTable` as t1
		$sWhere 
		$sOrder
		$sLimit
	";

	$rResult = htracer_mysql_query($sQuery);
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = htracer_mysql_query($sQuery);
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   `{$table_prefix}$sTable`
	";
	$rResultTotal = htracer_mysql_query($sQuery);
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	while ($aRow = mysql_fetch_array($rResult))
	{
		$row = array();
		for ($i=0 ; $i<count($aColumns) ; $i++ )
		{ 
			$class='';
			if($aColumns[$i]==='Key')
				$class='u_key';
			if($aColumns[$i]==='aURL')
				$class='u_url';
			if($aColumns[$i]==='Don')
				$class='u_don';
				
			
			$row[] ="	<input type='text' 
								class='$class'
								value='{$aRow[$aColumns[$i]]}' 
								name='{$aColumns[$i]}_{$aRow['ID']}'
								id='i{$aColumns[$i]}_{$aRow['ID']}'
								onchange='ULinksChanged();'
								onkeypress='ULinksChanged();'
								spellcheck='false'
								>
							<input type='hidden' 
								value='{$aRow[$aColumns[$i]]}' 
								name='was_{$aColumns[$i]}_{$aRow['ID']}'
								>
						";
		}
		$row[] =
			"<img onclick='ulink_delete(this);' alt='D' src='images/delete.gif' style='cursor:pointer' />&nbsp;&nbsp;". 
			"<img onclick='ulink_clone(this);' alt='C'  src='images/clone.gif'  style='cursor:pointer' />";
		$output['aaData'][] = $row;
	}
	$output['iTotalRecords']=$iTotal;
	$output['iTotalDisplayRecords']=$iFilteredTotal;

	echo json_encode($output);	
?>