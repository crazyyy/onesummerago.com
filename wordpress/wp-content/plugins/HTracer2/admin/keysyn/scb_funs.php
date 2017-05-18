<?php // Функции для работы Semantic Core Builder
	setlocale(LC_ALL, "ru_RU.UTF-8");
	srand();
	set_time_limit(100);
	error_reporting (E_ERROR | E_PARSE| E_WARNING);
	//$StopWords=Array('а'=>1,'без'=>1,'более'=>1,'бы'=>1,'был'=>1,'была'=>1,'были'=>1,'было'=>1,'быть'=>1,'в'=>1,'вам'=>1,'вас'=>1,'весь'=>1,'во'=>1,'вот'=>1,'все'=>1,'всего'=>1,'всех'=>1,'вы'=>1,'где'=>1,'да'=>1,'даже'=>1,'для'=>1,'до'=>1,'его'=>1,'ее'=>1,'если'=>1,'есть'=>1,'еще'=>1,'же'=>1,'за'=>1,'здесь'=>1,'и'=>1,'из'=>1,'или'=>1,'им'=>1,'их'=>1,'к'=>1,'как'=>1,'ко'=>1,'когда'=>1,'кто'=>1,'ли'=>1,'либо'=>1,'мне'=>1,'может'=>1,'мы'=>1,'на'=>1,'надо'=>1,'наш'=>1,'не'=>1,'него'=>1,'нее'=>1,'нет'=>1,'ни'=>1,'них'=>1,'но'=>1,'ну'=>1,'о'=>1,'об'=>1,'однако'=>1,'он'=>1,'она'=>1,'они'=>1,'оно'=>1,'от'=>1,'очень'=>1,'по'=>1,'под'=>1,'при'=>1,'с'=>1,'со'=>1,'так'=>1,'также'=>1,'такой'=>1,'там'=>1,'те'=>1,'тем'=>1,'то'=>1,'того'=>1,'тоже'=>1,'той'=>1,'только'=>1,'том'=>1,'ты'=>1,'у'=>1,'уже'=>1,'хотя'=>1,'чего'=>1,'чей'=>1,'чем'=>1,'что'=>1,'чтобы'=>1,'чье'=>1,'чья'=>1,'эта'=>1,'эти'=>1,'это'=>1,'я'=>1);
	include_once("keysyn_fun.php");
	include_once("config.php");


//Создаем таблицы
	mysql_query(
	"
		CREATE TABLE if not exists `scb_projects` 
		(
			`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`Domain` VARCHAR( 255 ) NOT NULL ,
			UNIQUE (`Domain`)
		) ENGINE = MYISAM ;
	") or die ('_Create Tables 1 :'.mysql_error());	
	mysql_query("
		CREATE TABLE if not exists `scb_mainkeys` 
		(
			`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`PID` INT NOT NULL ,
			`Key` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
			`CS` VARCHAR( 40 ),
			`Data` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			`Enable` INT NOT NULL DEFAULT '1',
			`Was` BOOL NOT NULL,
			INDEX (`CS`, `PID` , `Key` )
		) ENGINE = MYISAM ;
	") or die ('_Create Tables 2:'.mysql_error());
	mysql_query("
		CREATE TABLE if not exists `scb_keys` (
			`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`MKID` INT NOT NULL ,
			`Key` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
			`ClearCount` INT NOT NULL ,
			`DirtyCount` INT NOT NULL ,
			`URL` TEXT NOT NULL ,
			`SetBy` ENUM( 'none', 'user', 'script') NOT NULL DEFAULT 'none',
			`Inherit` INT NOT NULL DEFAULT '0',
			INDEX (`MKID` , `Key` , `ClearCount` , `DirtyCount`)
		) ENGINE = MYISAM ;
	") or die ('_Create Tables 3:'.mysql_error());
	mysql_query("	
		CREATE TABLE if not exists `scb_relations` (
			`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`KID1` INT NOT NULL ,
			`Relations` ENUM( 'parent', 'child' ) NOT NULL ,
			`KID2` INT NOT NULL ,
			INDEX ( `KID1` , `Relations` , `KID2` )
		) ENGINE = MYISAM
	") or die ('_Create Tables 4:'.mysql_error());	
	
//Теперь сами функции
	//
	//http://
	function NormalizeDomain($Domain)
	{
		$Domain=strtolower(trim($Domain));
		$Domain=str_replace('http://','',$Domain);
		$Domain=str_replace('/','',$Domain);
		$Domain=str_replace("\\",'',$Domain);
		return trim($Domain);
	}
	function NormalizeKey($Key)
	{
		$Key=mb_strtolower($Key,'utf-8');
		$Key=str_replace(Array('`','.',',','-','+','!','?',"\r","\n","\t","'",'"','(',')','}','}','|',':')
						,' ',$Key);
		$Key=str_replace('ё','е',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		
		return trim($Key);		
	}
	
	function GetProjectsLinks()
	{
		$Res=mysql_query("select * from `scb_projects`") or die ('CreateProject_Select :'.mysql_error());
		$n=mysql_num_rows($Res);
		$OUT='';
		for($i=0;$i<$n;$i++)
		{
			$Cur=mysql_fetch_array($Res);
			$MKID=GetFirstKeyID($Cur['ID']);
			$URL="aws.php?project={$Cur['ID']}&mkid=$MKID";
			$OUT.="<a href='$URL'>{$Cur['Domain']}</a> ";
		}
		return $OUT;
	}
	function CreateProject($Domain,$MainKeys=false)
	{
		$Domain=NormalizeDomain($Domain);
		$Domain=mysql_real_escape_string($Domain);
		$ID=mysql_query("select * from `scb_projects` where (`Domain`='$Domain') LIMIT 1") or die ('CreateProject_Select :'.mysql_error());
		if(mysql_num_rows($ID)==1)
		{
			$ID=mysql_fetch_array($ID);
			$ID=$ID['ID'];
		}
		else
		{
			mysql_query("insert into `scb_projects` SET `Domain`='$Domain'") or die ('CreateProject_Insert :'.mysql_error());
			$ID=mysql_insert_id();
		}
		if($MainKeys!==false)
			InsertMainKeys($ID,$MainKeys);
		return $ID;
	}
	function ClearDoubleKeys($Arr)
	{
		$Res=Array();
		foreach($Arr as $Cur)
		{
			$Cur=NormalizeKey($Cur);
			if($Cur!=='')
				$Res[GetKeyCS($Cur)]=$Cur;
		}
		return $Res;
	}
	function GetKeyCS($Key)
	{
		$Key=NormalizeKey($Key);
		$Key=split(' ',$Key);
		sort($Key);
		return md5(join(' ',$Key));
	}
	function InsertMainKeys($ProjectID,$MainKeys)
	{
		if(!is_numeric($ProjectID))
			$ProjectID=CreateProject($ProjectID);
		if(is_string($MainKeys))
			$MainKeys=split("\n",$MainKeys);
		if(!is_array($MainKeys))
			return false;
		$MainKeys=ClearDoubleKeys($MainKeys);
		if(!count($MainKeys))
			return false;
		$MainKeys0=GetMainKeysShort($ProjectID);
		$MainKeys0Tmp=Array();
		foreach ($MainKeys0 as $Key)
		{	
			$Key=split(' ',$Key);
			sort($Key);
			$Key=join(' ',$Key);
			$MainKeys0Tmp[]=$Key;
		}
		$MainKeys0=$MainKeys0Tmp;
		$MainKeysTmp=Array();
		foreach($MainKeys as $Key)
		{
			$KeyTmp=$Key;
			$Key=split(' ',$Key);
			sort($Key);
			$Key=join(' ',$Key);
			foreach($MainKeys0 as $Key0)
				if($Key0==$Key)
					continue 2;
			$MainKeysTmp[]=$KeyTmp;
		}	
		$MainKeys=$MainKeysTmp;
		foreach($MainKeys as $CS=>$MainKey)
			InsertMainKey($ProjectID,$MainKey);
		return true;	
	}
	function InsertMainKey($ProjectID,$MainKey)
	{
		if(!is_numeric($ProjectID))
			$ProjectID=CreateProject($ProjectID);
		$MainKey=NormalizeKey($MainKey);
		$MainKey=mysql_real_escape_string($MainKey);
		$CS=GetKeyCS($MainKey);
		
		$ID=mysql_query("select * from `scb_mainkeys` where (`PID`='$ProjectID' AND `CS`='$CS') LIMIT 1") or die ('InsertMainKey_Select :'.mysql_error());
		if(mysql_num_rows($ID)==1)
		{
			$ID=mysql_fetch_array($ID);
			$ID=$ID['ID'];
		}
		else
		{
			mysql_query("insert into `scb_mainkeys` SET `PID`='$ProjectID', `CS`='$CS',`Key`='$MainKey'") or die ('InsertMainKey_Insert :'.mysql_error());
			$ID=mysql_insert_id();
		}
		return $ID;
	}
	function GetFirstKeyID($ProjectID)
	{
		$Res=mysql_query("select * from `scb_mainkeys` where (`PID`='$ProjectID' AND `Enable`=1) LIMIT 1") or die ('GetMainKeyID_Select :'.mysql_error());
		$Cur=mysql_fetch_array(	$Res);
		return $Cur['ID'];
	}
	function GetMainKeyByID($ID)
	{
		$Res=mysql_query("select * from `scb_mainkeys` where (`ID`='$ID') LIMIT 1") or die ('GetMainKeyByID_Select :'.mysql_error());
		$Cur=mysql_fetch_array($Res);
		return $Cur['Key'];
	}
	function DelDoublesMainKeys($Add,$Old)
	{
		foreach($Add as $Key =>$Count)
		{
			$Key0=$Key;
			$Key=DelStopWords($Key);
			$Key=split(' ',$Key);
			sort($Key);
			$Key=join(' ',$Key);
			if(isset($Old[$Key]))
				unset($Add[$Key0]);
		}
		return $Add;
	}
	function GetMainKeysShort($ProjectID,$All=false)
	{
		if(!$All)
			$Res=mysql_query("select * from `scb_mainkeys` where (`PID`='$ProjectID' AND `Enable`=1)") or die ('GetMainKeys_Select :'.mysql_error());
		else
			$Res=mysql_query("select * from `scb_mainkeys` where (`PID`='$ProjectID')") or die ('GetMainKeys_Select :'.mysql_error());
		$Out=Array();
		$n=mysql_num_rows($Res);
		for($i=0;$i<$n;$i++)
		{
			$Cur=mysql_fetch_array($Res);
			if(!$Cur['ID'])
				continue;
			$Out[$Cur['ID']]=$Cur['Key'];
		}
		return $Out;
	}
	function PrintMainKeysShort($ProjectID,$Dialog=false)
	{
		$Keys=GetMainKeysShort($ProjectID);
		foreach($Keys as $ID => $Key)
		{
			if($_GET['mkid']==$ID)
				echo "<span class='curent'>$Key</span> ";
			elseif($Dialog)
				echo "<a onclick='return nextdialog();' href='aws.php?project=$ProjectID&mkid=$ID'>$Key</a> ";
			else
				echo "<a href='aws.php?project=$ProjectID&mkid=$ID'>$Key</a> ";
		}
	}
	function GetMainKeys($ProjectID)
	{
		$Out=Array();
		$IDs=Array();

		$Res=mysql_query("select * from `scb_mainkeys` where (`PID`='$ProjectID')") or die ('GetMainKeys_Select :'.mysql_error());
		$n=mysql_num_rows($Res);
		for($i=0;$i<$n;$i++)
		{
			$Cur=mysql_fetch_array($Res);
			$ID=$Cur['ID'];
			if(!$ID)
				continue;
			$IDs[]=$ID;
			$Out[$ID]=$Cur;
			$Out[$ID]['Keys']=0;
			$Out[$ID]['Sum']=0;
		}
		if(!$IDs)
			return Array(); 
		$IDs=join(',',$IDs);
		$Res=mysql_query("SELECT MKID,
								 SUM(`ClearCount`) as `Sum`, 
								 COUNT(ClearCount) as 'Keys' 
						  FROM `scb_keys` 
						  WHERE `MKID` IN ($IDs)
						  GROUP BY `MKID`
						  
						  ") 
		or die ('GetMainKeys_Select_2 :'.mysql_error());
		for($i=0;$i<$n;$i++)
		{
			$Cur=mysql_fetch_array($Res);
			$ID=$Cur['MKID'];
			if(!$ID)
				continue;
			$Out[$ID]['Keys']=$Cur['Keys'];
			$Out[$ID]['Sum']=$Cur['Sum'];
		}
		
		$Res=mysql_query("select * from `scb_mainkeys` where (`PID`='$ProjectID')") or die ('GetMainKeys_Select :'.mysql_error());
		$n=mysql_num_rows($Res);

		return $Out;
	}
	

	function PrintMainKeys($ProjectID,$Dialog=false)
	{
		$Keys=GetMainKeys($ProjectID);
		foreach($Keys as $ID => $Data)
		{
			$Key=$Data['Key'];
			echo '<tr>';
			echo '<td style="white-space:nowrap">';
			echo "<!--$Key-->";
			echo "<input type='hidden' name='mkid_{$ID}_was' value='1' />";
			if($Data['Enable'])
				echo "<input checked='checked' type='checkbox' name='mkid_{$ID}_enabled' value='1' />";
			else
				echo "<input type='checkbox' name='mkid_{$ID}_enabled' value='1' />";
			echo " ";	
			if($_GET['mkid']==$ID)
				echo "<span class='curent'>$Key</span>";
			elseif($Dialog)
				echo "<a onclick='return nextdialog();' href='aws.php?project=$ProjectID&mkid=$ID'>$Key</a>";
			else
				echo "<a href='aws.php?project=$ProjectID&mkid=$ID'>$Key</a>";
			echo '</td>';
			echo "<td>{$Data['Keys']}</td>";
			echo "<td>{$Data['Sum']}</td>";
			echo '</tr>';
		}
	}
	function GetSelectedKeys($MKID)
	{
		$Res=mysql_query("SELECT * FROM `scb_keys` WHERE (`MKID`= $MKID)") or die ('GetSelectedKeys :'.mysql_error());
		$n=mysql_num_rows($Res);
		$Out=Array();
		for($i=0;$i<$n;$i++)
		{
			$Cur=mysql_fetch_array($Res);
			$Out[$Cur['Key']]=$Cur['Key'];
		}
		return $Out;
	}
	function WasMainKey($MKID)
	{
		$Res=mysql_query("SELECT * FROM `scb_mainkeys` WHERE `ID`= $MKID LIMIT 1") or die ('WasMainKey :'.mysql_error());
		$Res=mysql_fetch_array($Res);
		return $Res['Was'];
	}
	function InsertKeys($PID,$MKID,$Data,$Selected)
	{
		if(!is_array($Selected))
			$Selected=split('\#',$Selected);
		$SelectedA=Array();
		foreach($Selected as $Cur)	
			$SelectedA[$Cur]=$Cur;
		$sData=serialize($Data);
		$sData=mysql_real_escape_string($sData);
		mysql_query("UPDATE `scb_mainkeys` SET `Data`='$sData', `Was`=1  WHERE `ID`= $MKID LIMIT 1") or die ('InsertKeys_Update :'.mysql_error());

		//Запоминаем уже выбранные URL
		$Res=mysql_query("SELECT * FROM `scb_keys` WHERE (`MKID`= $MKID)") or die ('InsertKeys_SELECT_0 :'.mysql_error());
		$n=mysql_num_rows($Res);
		$TmpURLs=Array();
		for($i=0;$i<$n;$i++)
		{
			$Cur=mysql_fetch_array($Res);
			$TmpURLs[$Cur['Key']]=Array(
				'URL'=>$Cur['URL'],
				'SetBy'=>$Cur['SetBy'],
				'Inherit'=>$Cur['Inherit']
			);
		}		

		//Удаляем URL
		mysql_query("DELETE FROM `scb_keys` WHERE `MKID`= $MKID") or die ('InsertKeys_DELETE :'.mysql_error());
		
		$Data2=Array();
		foreach($Data as $Key=>$Cur)		
			if(isset($SelectedA[$Key]))
				$Data2[$Key]=$Cur;
		$Data=$Data2;
		$Values='';
		foreach($Data as $Key=>$Cur)		
		{
			if($Values)
				$Values.=',';
			$Key=mysql_real_escape_string($Key);
			$Clear=$Cur['ClearCount'];
			$Dirty=$Cur['DirtyCount'];
			
			$URL='';
			$SetBy='none';
			$Inherit=0;
			if(isset($TmpURLs[$Key]))
			{
				$URL=$TmpURLs[$Key]['URL'];
				$SetBy=$TmpURLs[$Key]['SetBy'];
				$Inherit=$TmpURLs[$Key]['Inherit'];
			}
			$Values.="($MKID,'$Key',$Clear,$Dirty,'$URL','$SetBy',$Inherit)";	
		}
		mysql_query("INSERT INTO `scb_keys` (`MKID`,`Key`,`ClearCount`,`DirtyCount`,`URL`,`SetBy`,`Inherit`) VALUES $Values") or die ('InsertKeys_INSERT_1 :'.mysql_error());
		$Res=mysql_query("SELECT * FROM `scb_keys` WHERE (`MKID`=$MKID)") or die ('InsertKeys_SELECT :'.mysql_error());
		$n=mysql_num_rows($Res);
		$KeyToID=Array();
		for($i=0;$i<$n;$i++)
		{
			$Cur=mysql_fetch_array($Res);
			$KeyToID[$Cur['Key']]=$Cur['ID'];
		}
		$Values='';
		foreach($Data as $Key=>$Cur)		
		{
			$ParentID=$KeyToID[$Key];
			if(!$ParentID)
				continue;
			foreach($Cur['Childrens'] as $Child)
			{
				$ChildID =$KeyToID[$Child];
				if(!$ChildID)
					continue;	
				if($Values)
					$Values.=',';
				$Values.="($ParentID,'parent',$ChildID)";	
				$Values.=',';
				$Values.="($ChildID,'child',$ParentID)";	
			}
		}
		mysql_query("INSERT INTO `scb_relations` (`KID1`,`Relations`,`KID2`) VALUES $Values") or die ('InsertKeys_INSERT_2 :'.mysql_error());
	}
	function GetProjectKeys($PID)
	{
		$MainKeys=Array();
		$Res=mysql_query("SELECT * FROM `scb_mainkeys` WHERE (`PID`=$PID AND `Enable`=1)") or die ('GetProjectKeys_SELECT_1 :'.mysql_error());
		$n=mysql_num_rows($Res);
		for($i=0;$i<$n;$i++)
		{
			$Cur=mysql_fetch_assoc($Res);
			$MainKeys[$Cur['ID']]=$Cur['ID'];
		}
		$MainKeys=join(',',$MainKeys);
		
		$Res=mysql_query("SELECT * FROM `scb_keys` WHERE (`URL`!='' && `MKID` IN ($MainKeys))") or die ('GetProjectKeyst_SELECT_2 :'.mysql_error());
		$n=mysql_num_rows($Res);
		
		$Out=Array();
		for($i=0;$i<$n;$i++)
		{
			$Cur=mysql_fetch_assoc($Res);
			$Key=$Cur['Key'];
			$URL=$Cur['URL'];
			$Count=$Cur['DirtyCount'];
			if(isset($Out[$Key]) && $Out[$Key]['Count']>$Count)
				continue;
			$Out[$Key]=Array('Count'=>$Count,'URL'=>$URL);
		}
		return $Out;
	}
	function GetProject($PID)
	{
		$Res=mysql_query("SELECT * FROM `scb_projects` WHERE (`ID`=$PID) LIMIT 1") or die ('GetProject_SELECT_0 :'.mysql_error());
		$Project=mysql_fetch_assoc($Res);//'ID','Domain'
		$Project['MainKeys']=Array();
		$Res=mysql_query("SELECT * FROM `scb_mainkeys` WHERE (`PID`=$PID AND `Enable`=1) LIMIT 100") or die ('GetProject_SELECT_1 :'.mysql_error());
		$n=mysql_num_rows($Res);
		for($i=0;$i<$n;$i++)
		{
			$Cur=mysql_fetch_assoc($Res);
			unset($Cur['PID']);
			unset($Cur['CS']);
			unset($Cur['Data']);
			
			$Project['MainKeys'][$Cur['Key']]=$Cur;
			$Project['MainKeys'][$Cur['Key']]['Keys']=Array();
		}
		$KeyIDs=Array();
		foreach($Project['MainKeys'] as $Key => $Data)
		{
			$MKID=$Data['ID'];
			$Res=mysql_query("SELECT * FROM `scb_keys` WHERE (`MKID`=$MKID) ORDER BY `DirtyCount` DESC") or die ('GetProject_SELECT_2 :'.mysql_error());
			
			$n=mysql_num_rows($Res);
			for($i=0;$i<$n;$i++)
			{
				$Cur=mysql_fetch_assoc($Res);
				unset($Cur['MKID']);
				$KeyIDs[]=$Cur['ID'];
				$Project['MainKeys'][$Key]['Keys'][$Cur['Key']]=$Cur;
				//$Project['MainKeys'][$Key]['Keys'][$Cur['Key']]['Parents']=Array();
				//$Project['MainKeys'][$Key]['Keys'][$Cur['Key']]['Childrens']=Array();
			}
		}
		$Parents=Array();
		$Childrens=Array();
		$KeyIDs2=Array();
		foreach($KeyIDs as $ID)
		{
			if(!$ID && $ID!==0 && $ID!=='0')
				continue;
			$Parents[(int)$ID]=Array();
			$Childrens[(int)$ID]=Array();
			$KeyIDs2[]=$ID;
		}	
		if(count($KeyIDs2))
		{
			$Res=mysql_query("SELECT * FROM `scb_relations` WHERE (`KID1` in (".join(',',$KeyIDs2)."))") or die ('GetProject_SELECT_3 :'.mysql_error());
			$n=mysql_num_rows($Res);
			for($i=0;$i<$n;$i++)
			{
				$Cur=mysql_fetch_assoc($Res);
				if($Cur['Relations']=='parent')
					$Childrens[(int)$Cur['KID1']][]=(int)$Cur['KID2'];
				else
					$Parents[(int)$Cur['KID1']][]=(int)$Cur['KID2'];
			}
		}
		else 
			echo '<b sryle="color:red">В проекте нет ключей. Возможно, вы не нажимали кнопку Сохранить.</b>';
		$Project['Keys']=Array();
		foreach($Project['MainKeys'] as $Key1 => $Data1)
		{
			foreach($Data1['Keys'] as $Key2 => $Data2)
			{
				$ID=(int) $Data2['ID'];
				$Project['MainKeys'][$Key1]['Keys'][$Key2]['Parents']=$Parents[(int)$ID];
				$Project['MainKeys'][$Key1]['Keys'][$Key2]['Childrens']=$Childrens[(int)$ID];
				$Project['Keys'][]=$Project['MainKeys'][$Key1]['Keys'][$Key2];
			}
		}
		return $Project;
	}
	function UpdateKeys($Keys)
	{
		foreach($Keys as $ID => $Data)
		{
			$url= mysql_real_escape_string($Data['url']);
			if(!$Data['setby']||$Data['setby']!=='none'||$Data['setby']!=='script')
				$Data['setby']='user';
			$setby= mysql_real_escape_string($Data['setby']);
			$inherit=(int) $Data['inherit'];
			mysql_query("UPDATE scb_keys 
							SET `URL`='$url', 
								`SetBy`='$setby',
								`Inherit`='$inherit'
							WHERE `ID`=$ID LIMIT 1") 
			or die ('UpdateKeys_Update :'.mysql_error());
		}
	}
	function PodsvStopWords($Key)
	{
		global $StopWords;
		$Key=split(' ',$Key);
		foreach($Key as $i => $Word)
		{
			if(isset($StopWords[mb_strtolower($Word,'utf-8')]))
				$Key[$i]='<span style="color:gray">'.$Word.'</span>';
		}
		return join(' ',$Key);
	}
	function DelStopWords($Key)
	{
		global $StopWords;
		$Key=split(' ',$Key);
		foreach($Key as $i => $Word)
		{
			if(isset($StopWords[mb_strtolower($Word,'utf-8')]))
				unset($Key[$i]);
		}
		return join(' ',$Key);
	}
	DelStopWords
?>