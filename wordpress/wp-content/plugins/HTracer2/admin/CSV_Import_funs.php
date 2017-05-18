<?php //Функции для импорта из CSV
	function print_step3_csv_table()
	{
		$Roles=Array();
		foreach($_POST as $Key => $Value)
		{
			if($Value!='none' && strpos($Key,'col_role_')===0)
			{	
				
				$Arr=explode('_',$Key);
				$Roles[$Arr[2]]=$Value;
			}
		}
		//print_r($_POST);
		//print_r($Roles);
		//row_{$i}_enabled
		//cell_{$i}_{$j}
		$Table=Array();
		$HaveCount=false;
		$HaveURL=false;
		foreach($_POST as $Key => $Value)
		{	
			$i=false;
			if(strpos($Key,'cell_')===0)
			{
				$Key=explode('_',$Key);
				$j=$Key[2];
				if(isset($Roles[$j]))
				{
					$i=$Key[1];
					$Role=$Roles[$j];
					if($Role=='Count')
						$HaveCount=true;
					elseif($Role=='URL')
						$HaveURL=true;
				}
			}
			if($i!==false)
			{
				if($Role=='Count')
					$Value=str_replace(',','.',$Value);
				elseif(get_magic_quotes_gpc())
					$Value = stripcslashes($Value);
					
				$Value = str_replace('"',' ',$Value);
				$Value = str_replace("\\",' ',$Value);
				
				$Value = str_replace('  ',' ',$Value);
				$Value = str_replace('  ',' ',$Value);
				$Value = str_replace('  ',' ',$Value);
				$Value = trim($Value);
				if(!isset($Table[$i]))
					$Table[$i]=Array('Count'=>'','Query'=>'','URL'=>'');
				if($Role=='Count' && isset($Table[$i]['Count']))
					$Table[$i]['Count']=$Table[$i]['Count'] * 1 + $Value * 1;
				else				
					$Table[$i][$Role]=$Value;
			}
		}
		echo '<script>var ht_table_rows='.count($Table).';</script>';
		if(!$HaveCount)
		{
			echo '<h3 style="margin-bottom:0">'.ht_trans('Определение веса').'</h4>';
			echo '	<select name="count_source" id="count_source">
						<option value="wordstat_k">"WordStat" ('.ht_trans('без  уточнений').')</option>
						<option value="wordstat">WordStat ('.ht_trans('во всех словоформах с уточнениями').')</option>
						<option value="wordstat_kv">"!WordStat" ('.ht_trans('без уточнений и словоформ').')</option>
						<option value="wordstat_v">!WordStat ('.ht_trans('без учета словоформ').')</option>
						<option value="const_1">'.ht_trans('Все').' = 1</option>
						<option value="const_10">'.ht_trans('Все').' = 10</option>
						<option value="const_20">'.ht_trans('Все').' = 20</option>
						<option value="const_50">'.ht_trans('Все').' = 50</option>
						<option value="const_100">'.ht_trans('Все').' = 100</option>
						<option value="const_500">'.ht_trans('Все').' = 500</option>
						<option value="const_1000">'.ht_trans('Все').' = 1000</option>
					</select>
						<input type="button" value="'.ht_trans('Поехали').'!" id="ht_load_eva_btn" onclick="ht_load_eva()" />
						<b id="ht_load_eva_info"></b>
					<br />
				<br /><br />
			';
		}
		if(!$HaveURL)
		{
			echo '<h3 style="margin-bottom:0">URL</h4>';
			echo   ht_trans('В ваших данных не было колонки URL').'.<br />
					'.ht_trans('Адреса страниц будут уточнены благодаря Google').'.<br />
					<input type="button" id="ht_load_url_btn" onclick="ht_parse_se()" value="'.ht_trans('Поехали').'!"/>
					<b id="ht_load_url_info"></b>
					<br />
				<br /><br />';
		}
		echo '
			<table>
			<tr>
				<th>'.ht_trans('Ключевое слово').'</th>
				<th>'.ht_trans('Веc').'</th>
				<th>'.ht_trans('URL').'</th>
			</tr>
		';
		
		//убираем пропуским
		$Table0=$Table;
		$Table=Array();
		foreach ($Table0 as $Row)
			$Table[]=$Row;
		//echo '<pre>';
		//print_r($Table);
		
		foreach ($Table as $i=>$Row)
		{
			echo "
				<tr>
					<td><input id='Q_{$i}_Query' name='Q_{$i}_Query' value='{$Row['Query']}' size='40' /></td>
					<td><input id='Q_{$i}_Count' name='Q_{$i}_Count' value='{$Row['Count']}' size='4' /></td>
					<td>
						<input id='Q_{$i}_URL'  name='Q_{$i}_URL' value='{$Row['URL']}' size='50' onchange='s_link_click($i)'/>
						<a  href='#' 
							id='s_link_{$i}' 
							
							title='".ht_trans('Перейти на эту страницу')."' 
							onclick='s_link_click($i)'
							target='_blank'>S</a>
						<a  style='popup g_link' 
							title='".ht_trans('Открыть поиск ключа по сайту в Google в новом окне')."'
							onclick='GBox(\"{$Row['Query']}\",$i); return false;'
							href='http://www.google.ru/search?ie=UTF-8&hl=ru&q=".urlencode($Row['Query'].' site:'.$_SERVER['HTTP_HOST'])."'
							target='_blank'>G</a>							
					</td>
				</tr>";
		}
		echo '</table>';
	}
	function print_comma_select($Comma,$Data)
	{
		$is_other=($Comma!='none' && $Comma!=';' && $Comma!='#' && $Comma!=',' 
				&& $Comma!="\t" && $Comma!='tab' && $Comma!='lsp' && $Comma!='fsp'&& $Comma!='HTracerSpecialComma');
	?>
		<br />
		<form method='post'>
			<input type="hidden" name="ht_in_csv_import" value="1" />
			<?php echo ht_trans('Ecли в приведенной ниже таблице неправильно разделены строки &mdash; настройте разделитель столбцов вручную');?>.<br />
			<select name='Comma' id='Comma' onchange='comma_changed()'>
				<option value='none'<?php if($Comma=='none') echo ' selected="selected"'?>><?php echo ht_trans('Нет');?></option>
				<option value=';'   <?php if($Comma==';')    echo ' selected="selected"'?>>;</option>
				<option value=','   <?php if($Comma==',')    echo ' selected="selected"'?>>,</option>
				<option value='#'   <?php if($Comma=='#')    echo ' selected="selected"'?>>#</option>
				<option value='tab' <?php if($Comma=='tab'||$Comma=="\t")  echo ' selected="selected"'?>><?php echo ht_trans('Символ табуляции');?></option>
				<option value='lsp' <?php if($Comma=='lsp')  echo ' selected="selected"'?>><?php echo ht_trans('последний пробел');?></option>
				<option value='fsp' <?php if($Comma=='fsp')  echo ' selected="selected"'?>><?php echo ht_trans('первый пробел');?></option>
				<option value='HTracerSpecialComma'<?php if($Comma=='HTracerSpecialComma')  echo ' selected="selected"'?>><?php echo ht_trans('Системный');?></option>
				<option value='other'<?php if($is_other) echo ' selected="selected"'?>><?php echo ht_trans('Другой');?></option>
			</select>&nbsp;&nbsp;&nbsp;<input name='Comma2' size='1' id='Comma2' value='<?php echo $Comma; ?>' <?php if(!$is_other) echo ' style="display:none;"'?> /><br />
			<input type="hidden" name="step" value="2" />
			<input type="hidden" name="CSVData" value="<?php echo htmlspecialchars($Data); ?>" />
			<br />
			<input type='submit'  value='<?php echo ht_trans('Изменить');?>' /> 
			<br />
		</form>	<br />
	<?php	
	}
	function print_csv_table_select($Role,$i)
	{	
		$Size='';
		if(!$Role)
			$Size=' style="width:150px; color:gray;" ';
		elseif($Role=='Query')
			$Size=' style="width:300px" ';
		elseif($Role=='URL')
			$Size=' style="width:200px" ';

		else
			$Size=' style="width:90px" ';
	?>
		<select <?php echo $Size;?> name='col_role_<?php echo $i;?>' class='csv_role_select' id='col_role_<?php echo $i;?>'>
			<option value='none'><?php echo ht_trans('Не имеет значения');?></option> 
			<option value='Query' <?php if($Role=='Query') echo 'selected="selected"';?>><?php echo ht_trans('Ключевое слово');?></option> 
			<option value='Count' <?php if($Role=='Count') echo 'selected="selected"';?>><?php echo ht_trans('Вес (число переходов)');?></option> 
			<option value='URL'   <?php if($Role=='URL')   echo 'selected="selected"';?>>URL</option> 
		</select>
	<?php			
	}
	function print_csv_table($Data, $DetectData=false)
	{
		if(!$DetectData)
			$DetectData=AutoDetectFormat($Data);
		elseif(!is_array($DetectData))
			$DetectData=AutoDetectFormat($Data,$DetectData);
		$Table=explode_csv_table($Data, $DetectData);
		
		echo '<table>';
		$MaxColCount=0;
		foreach($Table as $i=>$Row)
			if($MaxColCount<count($Row))
				$MaxColCount=count($Row);
		$MaxColCount--;
		echo "
		<script>
			var MaxColCount={$MaxColCount};
			var RowCount=".count($Table).";
		</script>";
//Заголовки фигачим
		//print_r($DetectData);
		echo '<tr><th></th>';
		for($i=0;$i<$MaxColCount;$i++)
		{
			echo '<th>';
			if(isset($DetectData[$i]))
				print_csv_table_select($DetectData[$i],$i);
			else
				print_csv_table_select(false,$i);
			echo '</th>';
		}
		echo '</tr>';
		foreach($Table as $i=>$Row)
		{
			$Enabled=$Row['Enabled'];
			unset($Row['Enabled']);
			echo "<tr><td><input type='checkbox' name='row_{$i}_enabled' id='row_{$i}_enabled' onchange='row_checked($i)' value='1'";
			if($Enabled)
				echo " checked='checked'";
			echo " /></td>";
			foreach ($Row as $j=>$Cell)
			{	
				$Cell=htmlspecialchars($Cell);
				$Size='';
				if(!isset($DetectData[$j]))
					$Size=' style="width:150px" ';
				elseif($DetectData[$j]=='Query')
					$Size=' style="width:300px" ';
				elseif($DetectData[$j]=='URL')
					$Size=' style="width:200px" ';
				else
					$Size=' style="width:90px" ';	
				if(!$Enabled)
					$Size.=' disabled="disabled" ';
				if($Cell[0]=="\\" && $Cell[strlen($Cell)-1]=="\\")
					$Cell=str_replace("\\",'',$Cell);
				echo "<td><input name='cell_{$i}_{$j}' id='cell_{$i}_{$j}' value='$Cell' $Size /></td>";
			}
			echo '</tr>';
		}
		echo '</table>';
	}
	function explode_csv_table($Data, $DetectData=false)
	{ //Превращаеи данные в массив строк и столбцов
		if(!$DetectData)
			$DetectData=AutoDetectFormat($Data);
		elseif(!is_array($DetectData))
			$DetectData=AutoDetectFormat($Data,$DetectData);
		$Comma=$DetectData['Comma'];
		unset($DetectData['Comma']);
		$Strs=explode("\n", $Data);
		$Max=0;
		$QCol=false;
		$NCol=false;
		foreach($DetectData as $i=>$Col)
		{
			if($Col=='Query')
				$QCol=$i;
			elseif($Col=='Count')
				$NCol=$i;
			if($Max<$i)
				$Max=$i;
		}
		$Max++;
		$Table=Array();
		foreach($Strs as $Str)
		{	
			if(trim($Str)==='')
				continue;
			$Str=explode_cvs($Comma,$Str);
			$Str['Enabled'] 
				= count($Str)>=$Max //Достаточное число колонок 
					&& $Str[$QCol]!==''//запрос должен быть не пустой 
					&& !HTracer::isSpecQuery($Str[$QCol])
					// Отфильтровываем некоторые ключи
					&& $Str[$QCol]!=='Запрос'  			&& $Str[$QCol]!=='запрос'
					&& $Str[$QCol]!=='Запрос:' 			&& $Str[$QCol]!=='запрос:'
					&& $Str[$QCol]!=='Запрос...' 		&& $Str[$QCol]!=='запрос...'
					&& $Str[$QCol]!=='Запросы'  		&& $Str[$QCol]!=='запросы'
					&& $Str[$QCol]!=='Запросы:' 		&& $Str[$QCol]!=='запросы:'
					&& $Str[$QCol]!=='Запросы...' 		&& $Str[$QCol]!=='запросы...'
					&& $Str[$QCol]!=='Ключевое слово'  	&& $Str[$QCol]!=='ключевое слово'
					&& $Str[$QCol]!=='Ключевое слово:' 	&& $Str[$QCol]!=='ключевое слово:'
					&& $Str[$QCol]!=='Ключевое слово...'&& $Str[$QCol]!=='ключевое слово...'
					&& $Str[$QCol]!=='Ключевые слова'  	&& $Str[$QCol]!=='ключевые слова'
					&& $Str[$QCol]!=='Ключевые слова:' 	&& $Str[$QCol]!=='ключевые слова:'
					&& $Str[$QCol]!=='Ключевые слова...'&& $Str[$QCol]!=='ключевые слова...'
					&& $Str[$QCol]!=='Всего'  			&& $Str[$QCol]!=='всего'
					&& $Str[$QCol]!=='Всего:' 			&& $Str[$QCol]!=='всего:'
					&& $Str[$QCol]!=='Всего...' 		&& $Str[$QCol]!=='всего...'
					&& $Str[$QCol]!=='Сумма'  			&& $Str[$QCol]!=='сумма'
					&& $Str[$QCol]!=='Сумма:' 			&& $Str[$QCol]!=='сумма:'
					&& $Str[$QCol]!=='Сумма...' 		&& $Str[$QCol]!=='сумма...'
					&& $Str[$QCol]!=='В сумме'  		&& $Str[$QCol]!=='в сумме'
					&& $Str[$QCol]!=='В сумме:' 		&& $Str[$QCol]!=='в сумме:'
					&& $Str[$QCol]!=='В сумме...' 		&& $Str[$QCol]!=='в сумме...'
					&& $Str[$QCol]!=='Итого'  			&& $Str[$QCol]!=='итого'
					&& $Str[$QCol]!=='Итого:'			&& $Str[$QCol]!=='итого:'
					&& $Str[$QCol]!=='Итого...' 		&& $Str[$QCol]!=='итого...'
					&& $Str[$QCol]!=='Общее'  			&& $Str[$QCol]!=='общее'
					&& $Str[$QCol]!=='Общее:' 			&& $Str[$QCol]!=='общее:'
					&& $Str[$QCol]!=='Общее...' 		&& $Str[$QCol]!=='общее...'
					&& $Str[$QCol]!=='В общем'  		&& $Str[$QCol]!=='в общем'
					&& $Str[$QCol]!=='В общем:' 		&& $Str[$QCol]!=='в общем:'
					&& $Str[$QCol]!=='В общем...' 		&& $Str[$QCol]!=='в общем...'
					&& $Str[$QCol]!=='В общей сложности'&& $Str[$QCol]!=='в общей сложности'
					&& $Str[$QCol]!=='В общей сложности:'	&& $Str[$QCol]!=='в общей сложности:'
					&& $Str[$QCol]!=='В общей сложности...'	&& $Str[$QCol]!=='в общей сложности...'
					&& $Str[$QCol]!=='Среднее'  		&& $Str[$QCol]!=='среднее'
					&& $Str[$QCol]!=='Среднее:' 		&& $Str[$QCol]!=='среднее:'
					&& $Str[$QCol]!=='Среднее...' 		&& $Str[$QCol]!=='среднее...'
					&& $Str[$QCol]!=='В среднем'  		&& $Str[$QCol]!=='в среднем'
					&& $Str[$QCol]!=='В среднем:' 		&& $Str[$QCol]!=='в среднем:'
					&& $Str[$QCol]!=='В среднем...' 	&& $Str[$QCol]!=='в среднем...'
					&& $Str[$QCol]!=='Сумма выбранных'  && $Str[$QCol]!=='сумма выбранных'
					&& $Str[$QCol]!=='Сумма выбранных:' && $Str[$QCol]!=='сумма выбранных:'
					&& $Str[$QCol]!=='Быстрый поиск'  	&& $Str[$QCol]!=='быстрый поиск'
					&& $Str[$QCol]!=='Summ'  			&& $Str[$QCol]!=='summ'
					&& $Str[$QCol]!=='Summ:'  			&& $Str[$QCol]!=='summ:'
					&& $Str[$QCol]!=='Average'  		&& $Str[$QCol]!=='average'
					&& $Str[$QCol]!=='Average:'  		&& $Str[$QCol]!=='average:'
					&& $Str[$QCol]!=='Keyword'  		&& $Str[$QCol]!=='keyword'
					&& $Str[$QCol]!=='Keyword:'  		&& $Str[$QCol]!=='keyword:'
					&& $Str[$QCol]!=='Keywords'  		&& $Str[$QCol]!=='keywords'
					&& $Str[$QCol]!=='Keywords:'  		&& $Str[$QCol]!=='keywords:'
					&& $Str[$QCol]!=='KeyWords'  		&& $Str[$QCol]!=='KeyWords:'
					&& $Str[$QCol]!=='Query'  			&& $Str[$QCol]!=='query'
					&& $Str[$QCol]!=='Query:'  			&& $Str[$QCol]!=='query:'
					&& $Str[$QCol]!=='Queries'  		&& $Str[$QCol]!=='queries'
					&& $Str[$QCol]!=='Queries:'  		&& $Str[$QCol]!=='queries:'
					&& $Str[$QCol]!=='Request'  		&& $Str[$QCol]!=='request'
					&& $Str[$QCol]!=='Request:'  		&& $Str[$QCol]!=='request:'
					&& $Str[$QCol]!=='Requests'  		&& $Str[$QCol]!=='requests'
					&& $Str[$QCol]!=='Requests:'  		&& $Str[$QCol]!=='requests:'
					&& ($NCol===false // либо веса нет вообще, либо он число > 0
					|| (is_numeric($Str[$NCol]) && $Str[$NCol] * 1 >0 ));
			$Table[]=$Str;
		}
		return $Table;
	}
	function AutoDetectFormat(&$Data,$Comma=false)
	{// Автоопределяет формат
		//echo '<br />Data0='.$Data;
		if (get_magic_quotes_gpc())
            $Data = stripcslashes($Data); 
		$Strs=explode("\n",$Data);
		foreach($Strs as $i=>$Str)
		{
			$Strs[$i]=trim($Str);
			if($Strs[$i]===''||trim($Str)=='Слова'||trim($Str)=='Показов в месяц')
				unset($Strs[$i]);//Это для приямого копирования из WordStat
		}
		if($Comma==='none')
			return Array
			(
				'Comma'=>'none',
				0=>'Query'
			);
		elseif($Comma==='fsp')
			return Array(
				'Comma'=>'fsp',
				0=>'Count',
				1=>'Query'
			);
		elseif($Comma==='lsp')
			return Array(
				'Comma'=>'lsp',
				0=>'Query',
				1=>'Count'
			);
		elseif(!$Comma)//Определяем разделитель столбцов
		{
			$IsWordStat=false;
			if(count($Strs)>3 && count($Strs)%2==0)
			{
				// Пытаемся отпределить прямое копирование с WordStat
				$IsWordStat=true;
				$R=false;
				$i2=0;
				$HaveNotNumeric=false;
				foreach($Strs as $i=>$Str)
				{	
					$Str=trim($Str);
					if(is_numeric($Str) && strpos($Str,'.')===false && strpos($Str,',')===false)
					{
						if($R===false)
							$R=$i2%2;
						elseif($R!=$i2%2)	
						{
							$IsWordStat=false;
							break;
						}
					}
					else
						$HaveNotNumeric=true;
					$i2++;
					if($R===false && $i2>2)
					{
						$IsWordStat=false;
						break;
					}
				}
				$IsWordStat= ($IsWordStat&& $HaveNotNumeric && $R!==false);
				//echo "<h2>IsWordStat=$IsWordStat</h2>";
				if($IsWordStat)
				{
					$Strs2=Array();
					$Cur='';
					$i2=0;
					foreach($Strs as $i=>$Str)
					{
						if($i2%2===0)
							$Cur=str_replace('+','',$Str).'HTracerSpecialComma';
						else
							$Strs2[]=$Cur.$Str;
						$i2++;
					}
					$Strs=$Strs2;
				}
			}
			$Data=JOIN("\n",$Strs);
			if(count($Strs)>2 && !$IsWordStat)
			{	
			// пытаемся определить формат "10 купить ноутбук"	
				$is_fsp=true;
				$i2=0;
				foreach($Strs as $Str)
				{
					if(trim($Str)==='')
						continue;
					$Str=explode_cvs('fsp',$Str);
					if((count($Str)<2|| !is_numeric($Str[0]) || strpos($Str[0],'.')!==false ||trim($Str[1])==='')
					&&( $i2 || count($Strs)==3))
					{
						$is_fsp=false;
						break;
					}
					$i2++;
				}
				if($is_fsp)
					return Array(
						'Comma'=>'fsp',
						0=>'Count',
						1=>'Query'
					);
				// пытаемся определить формат "купить ноутбук 10"	
				$is_lsp=true;
				$i2=0;
				foreach($Strs as $Str)
				{
					if(trim($Str)==='')
						continue;
					$Str=explode_cvs('lsp',$Str);
					if((count($Str)<2||!is_numeric($Str[1])|| strpos($Str[1],'.')!==false||trim($Str[0])==='')
					&&( $i2 || count($Strs)==3))
					{
						$is_lsp=false;
						break;
					}
					$i2++;
				}	
				if($is_lsp)
					return Array(
						'Comma'=>'lsp',
						0=>'Query',
						1=>'Count'
					);
			}
			$Commas=Array("#",";",",","\t","|",'HTracerSpecialComma');
			$CommasSKO=Array();//Среднеквадрачтничные отклонения
			foreach($Commas as $Comma)
			{
				$count=count(explode($Comma,$Data))-1;
				if(!count($Strs))
					$Average=0;
				else
					$Average=$count/count($Strs);
				if(!$count)
					continue;
				//echo $Average;
				$SKO=0;	
				foreach($Strs as $Str)
				{
					$delta=$Average-count(explode($Comma,$Str));
					$SKO+=$delta*$delta;
				}
				if($Average>1)
					$SKO=$SKO/($Average * $Average);
				$CommasSKO[$Comma]=$SKO;
			}	
			asort($CommasSKO);
			if(!count($CommasSKO))
			{	
				return Array
				(
					'Comma'=>'none',
					0=>'Query'
				);
			}
			$CommasSKO=array_keys($CommasSKO);
			$Comma=$CommasSKO[0];
			if(3 * count(explode($Comma,$Data))< count($Strs))
			{
				return Array
				(
					'Comma'=>'none',
					0=>'Query'
				);
			}
		}
		$Detect=Array();
	
		foreach($Strs as $Str)
		{
			$Cells=explode_cvs($Comma,$Str);
			foreach($Cells as $i=>$Cell)
			{	
				//echo ' '.$i.' ';
				if(!isset($Detect[$i]))
					$Detect[$i]=Array('Count'=>false,'Query'=>false,'URL'=>false);
				$Cell=trim($Cell);
				if(stripos($Cell,' купит')!==false
					 ||stripos($Cell,' скачат')!==false
					 ||stripos($Cell,' бесплатн')!==false
					 ||stripos($Cell,' недорог')!==false
					 ||stripos($Cell,' дешев')!==false
					 ||stripos($Cell,'как ')!==false
					 ||stripos($Cell,' как')!==false
					 ||stripos($Cell,'где ')!==false
					 ||stripos($Cell,' цен')!==false
					 ||stripos($Cell,' price ')!==false
					 ||stripos($Cell,' cheap ')!==false
					 ||stripos($Cell,' free ')!==false
					 ||stripos($Cell,' online ')!==false
					 ||stripos($Cell,' download ')!==false)
				{
					$Detect[$i]['Query']+=2;
				}				
				if(is_numeric($Cell) && stripos($Cell,',')===false && stripos($Cell,'.')===false)
				{
					if($Cell*1>0)
						$Detect[$i]['Count']+=3;
					$Detect[$i]['URL']-=2;
					$Detect[$i]['Query']-=1;
				}
				elseif(stripos($Cell,' ')!==false)
				{
					$Detect[$i]['URL']-=1;
					$Detect[$i]['Query']+=1;
				}
				elseif(stripos($Cell,'?')===false && stripos($Cell,'&')!==false)
					$Detect[$i]['URL']-=3;
				elseif(stripos($Cell,'http://')!==false||stripos($Cell,'www.')!==false
				||stripos($Cell,'.com')!==false||stripos($Cell,'.ua')!==false||stripos($Cell,'.net')!==false||stripos($Cell,'.ru')!==false||stripos($Cell,'.org')!==false
				||stripos($Cell,'.htm')!==false||stripos($Cell,'.php')!==false
				||(stripos($Cell,'?')!==false&&stripos($Cell,'&')!==false && stripos($Cell,'?')<stripos($Cell,'&'))
				||stripos($Cell,'/')===0||stripos($Cell,'?')===0||count(explode('/',$Cell))>2
				||stripos($Cell,'=')!==false)
					$Detect[$i]['URL']+=3;
				elseif(count(explode('/',$Cell))>1||count(explode('+',$Cell))>1)
					$Detect[$i]['URL']+=1;
			}
		}

		
		$DetectEva=Array();
		$DetectMax=Array();
		
		foreach ($Detect as $i=>$DetectColl)
		{
			//arsort($Detect[$i]);
			arsort($DetectColl);
			$keys=array_keys($DetectColl);
			$key=$keys[0];
			if($DetectColl[$key]<=0)
				unset($Detect[$i]);
			else
			{
				$DetectEva[$i]=$DetectColl[$key];
				if(!isset($DetectMax[$key])||$DetectMax[$key]<$DetectColl[$key])
					$DetectMax[$key]=$DetectColl[$key];
				$Detect[$i]=$key;
			}
		}
		$DetectWas=Array();
		$Detect0=$Detect;
		foreach ($Detect as $i=>$DetectColl)
		{	
			if($DetectEva[$i]<$DetectMax[$DetectColl] || isset($DetectWas[$DetectColl]))
				unset($Detect[$i]);
			else
				$DetectWas[$DetectColl]=true;
		}
		//echo '<br />Data2='.$Data;
		if(!isset($DetectWas['Query']) && !isset($Detect[0]))
			$Detect[0]='Query';

		ksort($Detect);
		if($Comma=="\t")
			$Comma="tab";

//		echo '<pre>';
//		print_r($Detect);
//		echo '</pre>';

		$Detect['Comma']=$Comma;
		return $Detect;
	}
	function explode_cvs($Comma,$Str)
	{//Обертка для функциии разбивки строк
		$Res= explode_cvs0($Comma,$Str);
		foreach($Res as $i=>$Coll)
		{	
			$Coll=str_replace('HTracerDableDableKav','"',$Coll);
			$Coll=trim($Coll);
			$Res[$i]=$Coll;
		}
		return $Res;
	}
	function explode_cvs0($Comma,$Str)
	{
	// Разбивает строку CSV на ячейки
	// $Comma разделитель произвольного типа может быть строкой любой длины
	// или константами:
	//	none - нет, простой список 
	//	tab -  символ табуляции
	//	fsp -  first space первый пробел "10 купить ноутбуки"
	//	lsp -  last space первый пробел "купить ноутбуки 10"
		$Str=str_replace('""','HTracerDableDableKav',$Str);
		if($Comma==="tab"||$Comma=="\t")
		{	
			$tarr=explode("\t",$Str);
			$wasdblkav=false;
			foreach($tarr as $curel)
			{//Пытаемся определить являеться ли строка CSV с разделителем таб или это нормальный CSV
				if($curel{0}=='"' && $curel{strlen($curel)-1}=='"')
				{
					$tcurel=$curel;
					$tcurel{0}=' ';
					$tcurel{strlen($tcurel)-1}=' ';
					if(strpos($tcurel,'""')!==false||strpos($tcurel,'"')===false)
					{
						$wasdblkav=true;
						break;
					}
				}	
			}
			if(!$wasdblkav)
				return $tarr;
			$Comma="\t";	
		}
		elseif($Comma==="none")
			return Array($Str);
		elseif($Comma==="fsp")
			return explode(' ',$Str,2);
		elseif($Comma==="lsp")
		{
			$pos=strrpos($Str,' ');
			$Res=Array();
			$Res[]=substr($Str,0,$pos);
			$Res[]=substr($Str,$pos);
			return $Res;
		}	
		$Out=Array();
		$in_sk=false;
		$len=strlen($Str);
		$Part='';
		$cur='';
		$CommaLen=strlen($Comma);
		$InComma=0;
		for($i=0;$i<$len;$i++)
		{
			$prev=$cur;
			$cur=$Str{$i};
			if(!$in_sk && $cur==$Comma{0})
			{
				$InComma=$CommaLen;
				if($len-$i<$CommaLen)
					$InComma=0;
				elseif($CommaLen>1)
				{
					for($j=1;$j<$CommaLen;$j++)
					{
						if($Str{$j+$i}!==$Comma{$j})
						{
							$InComma=0;
							break;
						}
					}
				}
			}
			if($cur=='"' && !$InComma)
				$in_sk=!$in_sk;
			elseif($InComma)
			{	
				if($InComma==$CommaLen)
					$Out[]=$Part;
				$Part='';
				$InComma--;
			}
			else
				$Part.=$cur;
		}
		if($Part!=='')
			$Out[]=$Part;
		return $Out;
	}
?>