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
		
	$aColumns = array( 'URL', 'Eva', 'FirstKey', 'SecondKey', 'ShowInCloud', 'ShowATitle');
	
	$sIndexColumn = "ID";
	$sTable = "htracer_pages";
	
	if($_GET['iDisplayLength'])
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
				mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	if(isset($_GET['idsort']))
		$sOrder='ORDER BY `ID` DESC ';
	elseif(isset($_GET['iSortCol_0']))
	{
		$sOrder = "ORDER BY  ";
		for ($i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
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
	if ( $_GET['sSearch'] != "" )
	{
		if($_GET['sSearch']=='/')
			$sWhere = " 
				WHERE `URL` = '".mysql_real_escape_string($_GET['sSearch'])."' 
			";
		elseif($_GET['sSearch']{0}=='=')
			$sWhere = " 
				WHERE `URL` = '".mysql_real_escape_string(substr($_GET['sSearch'],1))."' 
				OR 	  `FirstKey` = '".mysql_real_escape_string(substr($_GET['sSearch'],1))."' 
				OR 	  `SecondKey`  = '".mysql_real_escape_string(substr($_GET['sSearch'],1))."'
			";
		elseif($_GET['sSearch']{0}=='>')
			$sWhere = " 
				WHERE `URL` LIKE '%".mysql_real_escape_string(substr($_GET['sSearch'],1))."' 
				OR 	  `FirstKey` LIKE '%".mysql_real_escape_string(substr($_GET['sSearch'],1))."' 
				OR 	  `SecondKey`  LIKE '%".mysql_real_escape_string(substr($_GET['sSearch'],1))."'
			";
		elseif($_GET['sSearch']{0}=='<')
			$sWhere = " 
				WHERE `URL` LIKE '".mysql_real_escape_string(substr($_GET['sSearch'],1))."%' 
				OR 	  `FirstKey` LIKE '".mysql_real_escape_string(substr($_GET['sSearch'],1))."%' 
				OR 	  `SecondKey`  LIKE '".mysql_real_escape_string(substr($_GET['sSearch'],1))."%'
			";
		else
			$sWhere = " 
				WHERE `URL` LIKE '%".mysql_real_escape_string($_GET['sSearch'])."%' 
				OR 	  `FirstKey` LIKE '%".mysql_real_escape_string($_GET['sSearch'])."%' 
				OR 	  `SecondKey`  LIKE '%".mysql_real_escape_string($_GET['sSearch'])."%'
			";
	}
	$aColumns2=Array();
	foreach($aColumns as $Column)
	{
		if($Column!='URL')
			$aColumns2[]= "t1.`$Column` as `$Column`";
	}
	$table_prefix=HTracer::GetTablePrefix();
//	unset($aColumns2['URL']);
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS t1.ID as ID, ".join(", ", $aColumns2).", t1.`URL` as `URL`
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
		$aRow['Eva']=round($aRow['Eva']);
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ($aColumns[$i] == "ShowInCloud"||$aColumns[$i] == "ShowATitle")
			{
				$checked=" ";
				$val=0;
				if($aRow[$aColumns[$i]])
				{
					$checked=" checked='checked' ";
					$val=1;
				}	
				$row[] = "	<input type='checkbox' 
								name='{$aColumns[$i]}_{$aRow['ID']}' 
								value='1' 
								$checked>
							<input type='hidden'  name='was_{$aColumns[$i]}_{$aRow['ID']}' value='$val'>
				";
			}
			elseif($aColumns[$i] == "URL"||$aColumns[$i] == "FirstKey"||$aColumns[$i] == "SecondKey")
			{
				$class='';
				if($aColumns[$i] == "FirstKey"||$aColumns[$i] == "SecondKey")
					$class=" class='page_main_keys' page='".urlencode($aRow['URL'])."' ";
				$rowcode="	<input type='text' 
								value='{$aRow[$aColumns[$i]]}' 
								name='{$aColumns[$i]}_{$aRow['ID']}'
								spellcheck='false'
								$class
								>
							<input type='hidden' 
								value='{$aRow[$aColumns[$i]]}' 
								name='was_{$aColumns[$i]}_{$aRow['ID']}'
								>
						";
				if($aColumns[$i] == "URL")
					$rowcode.="<a href='page.php?url=".urlencode($aRow['URL'])."'>&raquo;</a>";
				$row[] =$rowcode;
			}
			elseif($aColumns[$i] != ' ')
				$row[] = $aRow[$aColumns[$i]];
		}
		$output['aaData'][] = $row;
	}
	$output['iTotalRecords']=$iTotal;
	$output['iTotalDisplayRecords']=$iFilteredTotal;

	echo json_encode($output);	
?>