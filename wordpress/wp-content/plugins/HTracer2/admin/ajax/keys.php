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
		
	$aColumns = array( 'Status', 'In', 'Out', 'Eva', 'ShowInCLinks', 'URL');
	
	$sIndexColumn = "ID";
	$sTable = "htracer_queries";
	
	if($_GET['iDisplayLength'])
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
				mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	if(isset($_GET['idsort']))
		$sOrder='ORDER BY `ID` DESC ';
	if(isset($_GET['iSortCol_0']) && intval($_GET['iSortCol_0'])===0 && intval($_GET['iSortingCols'])==1)
	{	
		if(!$_GET['sSortDir_0']
		 ||strtolower($_GET['sSortDir_0'])=='asc' 
		 ||strtolower($_GET['sSortDir_0'])=='desc')
			$sOrder=" ORDER BY `Status` {$_GET['sSortDir_0']}, `ID` {$_GET['sSortDir_0']}    ";
	}
	elseif(isset($_GET['iSortCol_0']))
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
	if ( $_GET['sSearch'] != "" )
	{
		$wo=false;
		if($_GET['sSearch']=='/' && !isset($_GET['url']))
		{
			$sWhere = " 
				WHERE `URL` = '".mysql_real_escape_string($_GET['sSearch'])." ' 
			";
		}
		elseif($_GET['sSearch']{0}=='=')
			$wo=" = '".mysql_real_escape_string(substr($_GET['sSearch'],1))."' "; 
		elseif($_GET['sSearch']{0}=='>')
			$wo=" LIKE '%".mysql_real_escape_string(substr($_GET['sSearch'],1))."' "; 
		elseif($_GET['sSearch']{0}=='<')
			$wo=" LIKE '".mysql_real_escape_string(substr($_GET['sSearch'],1))."%' "; 
		else
			$wo=" LIKE '%".mysql_real_escape_string($_GET['sSearch'])."%' "; 
		if($wo)
		{
			if(!isset($_GET['url']))
				$sWhere = " 
					WHERE ( `URL` $wo 
					OR 	`Out` $wo
					OR 	`In`  $wo)
				";
			else
				$sWhere = " 
					WHERE (`Out` $wo
					OR 	`In`  $wo)
				";
		}
	}
	else
		$sWhere = " WHERE 1=1 ";
	if(isset($_GET['url']))
		$sWhere.= " AND `URL` = '".mysql_real_escape_string($_GET['url'])."' ";
	if(!trim($sWhere))
		$sWhere = " WHERE 1=1 ";
		
	$aColumns2=Array();
	foreach($aColumns as $Column)
	{
		if($Column!='URL')
			$aColumns2[]= "t1.`$Column` as `$Column`";
	}
	$table_prefix=HTracer::GetTablePrefix();
	$WhereFilter=" ((t1.`Eva`>0 AND CHAR_LENGTH(t1.`Out`)<70)||t1.`Status`!=0) ";
	
	
//	unset($aColumns2['URL']);
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS t1.ID as ID, ".join(", ", $aColumns2).", t2.URL as URL 
		FROM  `{$table_prefix}$sTable` as t1
		INNER JOIN {$table_prefix}htracer_pages as t2 on t1.URL_CS=t2.URL_CS
		$sWhere AND $WhereFilter
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
		$aRow['Eva']=round($aRow['Eva'],1);
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ($aColumns[$i] == "Status"||$aColumns[$i] == "ShowInCLinks")
			{
				$checked=" ";
				$val=0;
				if($aRow[$aColumns[$i]])
				{
					$checked=" checked='checked' ";
					$val=1;
				}	
				if($aColumns[$i] == "Status")
					$checked.=" onchange='ReloadKeyColor(this,\"{$aRow['ID']}\"); was_keys_chahged=true;' ";
				else
					$checked.=" onchange='was_keys_chahged=true;' ";
			
				$row[] = "	<input type='checkbox' 
								name='{$aColumns[$i]}_{$aRow['ID']}' 
								id='i{$aColumns[$i]}_{$aRow['ID']}' 
								value='1' 
								$checked>
							<input type='hidden'  name='was_{$aColumns[$i]}_{$aRow['ID']}' value='$val'>
								 ";
			}
			elseif ($aColumns[$i] == "Out"||$aColumns[$i] == "Eva")
			{
				$row[] ="	<input type='text' 
								value='{$aRow[$aColumns[$i]]}' 
								name='{$aColumns[$i]}_{$aRow['ID']}'
								id='i{$aColumns[$i]}_{$aRow['ID']}'
								onchange='was_keys_chahged=true;'
								onkeypress='was_keys_chahged=true;'
								spellcheck='false'
								>
							<input type='hidden' 
								value='{$aRow[$aColumns[$i]]}' 
								name='was_{$aColumns[$i]}_{$aRow['ID']}'
								>
						";
			}
			elseif ($aColumns[$i] == "In")
			{
				$cl='';
				if(!$aRow['Status'])
					$cl=" style='color:gray;text-decoration:line-through' ";
				$row[] = "<span id='In_{$aRow['ID']}' $cl >"
							.str_replace($_GET['sSearch'],'<b>'.$_GET['sSearch'].'</b>',$aRow[$aColumns[$i]])
						."</span><input type='hidden' class='key_in' value='{$aRow[$aColumns[$i]]}' />";
			}
			elseif ($aColumns[$i] == "URL")
			{
				if(!isset($_GET['url']))
				{
					$row[]= "<a href='page.php?url=".urlencode($aRow[$aColumns[$i]])."' title='Перейти на редактирование страницы {$aRow[$aColumns[$i]]}'>"
							.str_replace($_GET['sSearch'],'<b>'.$_GET['sSearch'].'</b>',$aRow[$aColumns[$i]])
						."</a>
						<input type='hidden' 
							value='{$aRow[$aColumns[$i]]}' 
							name='was_URL_{$aRow['ID']}'
						>";
				}
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