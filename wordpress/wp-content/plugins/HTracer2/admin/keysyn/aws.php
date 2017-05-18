<?php
	if($_GET['project'])
		$_GET['project']=trim($_GET['project']);
	if($_POST['s_action']=='add_maikeys_2')
	{
		include_once("scb_funs.php");
		header ("Content-type: text/html;charset=UTF-8");
		InsertMainKeys($_GET['project'],$_POST['keys']);
		echo 'Основные ключи были добавлены';
		exit;
	}
	elseif($_POST['s_action']=='check_maikeys')
	{
		include_once("scb_funs.php");
		header ("Content-type: text/html;charset=UTF-8");
		//mkid_{$ID}_was
		$Keys=Array();
		foreach($_POST as $Key=>$Val)
		{
			$Key=split('_',$Key);
			if($Key[2]!='was')
				continue;
			$Keys[$Key[1]]=0;
		}
		foreach($_POST as $Key=>$Val)
		{
			$Key=split('_',$Key);
			if($Key[2]!='enabled')
				continue;
			$Keys[$Key[1]]=1;
		}
		foreach ($Keys as $ID=>$Val)
			mysql_query("UPDATE scb_mainkeys SET `Enable`=$Val WHERE `ID`=$ID LIMIT 1") or die ('check_maikeys :'.mysql_error());
		echo 'Основные ключи были обнолены';
		exit;
	}
	elseif($_POST['s_action']=='add_main_keys')
	{	
		include_once("scb_funs.php");
		header ("Content-type: text/html;charset=UTF-8");

		$MainKeys=Array();
		foreach($_POST as $Key=>$Val)
			if(strpos($Key,'sa_')===0)
				$MainKeys[]=$Val;
		InsertMainKeys($_POST['pid'],$MainKeys);
		echo 'Ключи были добавлены';
		exit;
	}
	elseif($_POST['s_action']=='add_keys')
	{
		include_once("scb_funs.php");
		header ("Content-type: text/html;charset=UTF-8");
		$_POST['data']=htmlspecialchars_decode($_POST['data']);	
		$Data=unserialize($_POST['data']);
		if(!$Data)	
			$Data=unserialize(stripslashes($_POST['data']));
		InsertKeys($_GET['project'],$_GET['mkid'],$Data,$_POST['selected']);
		echo 'Сохранено';
		//print_r($Data);
		exit;
	}
	elseif($_GET['project'])
	{
		$_GET['project']=trim($_GET['project']);
		include_once("scb_funs.php");
		$ProjectID=$_GET['project'];
		if($_GET['mkid'])
			$_GET['key']=GetMainKeyByID($_GET['mkid']);
		elseif($_GET['key'])
		{
			$Key  = NormalizeKey($_GET['key']);
			$mkid = InsertMainKey($ProjectID,$Key);
			header("Location: aws.php?project=$ProjectID&mkid=$mkid");
			exit;
		}
		$Was=WasMainKey($_GET['mkid']);
		if($Was)
			$SelectedKeys=GetSelectedKeys($_GET['mkid']);
		else
			$SelectedKeys=Array();
		$MainKeysCS0=GetMainKeysShort($ProjectID,true);
		$MainKeysCS=Array();
		foreach($MainKeysCS0 as $Key)
		{
			$Key=DelStopWords($Key);
			$Key=split(' ',$Key);
			sort($Key);
			$Key=join(' ',$Key);
			$MainKeysCS[$Key]=$Key;
		}
		//echo '<pre>';
		//print_r($MainKeysCS);
		//echo '</pre>';
	}
	include_once("keysyn_fun.php");
	$DspKey=$_GET['key'];
	if(!$DspKey)	
		$DspKey='ноутбук одесса';
?>
<html>
<head>
	<meta name="robots" content="noindex,nofollow">
	<title><?php 
			if(!$_GET['project']) 
				echo 'Advances WordStat';
			else
				echo 'Подбор кеев';
	?></title>

<!--jQuery-->
	<script type="text/javascript" src="jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="jquery.form.js"></script>
<!--/jQuery-->
	
<!--TableSorter-->
	<link rel="stylesheet" href="js/tablesorter/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />
	<!--<script type="text/javascript" src="js/tablesorter/jquery-latest.js"></script> -->
	<script type="text/javascript" src="js/tablesorter/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="js/tablesorter/jquery.metadata.js"></script>
<!--/TableSorter-->

	
	
	<link rel="stylesheet" href="aws.css" type="text/css" id="" media="print, projection, screen" />
	<script type="text/javascript" src="aws.js"></script>
	<script type="text/javascript">
	var UndoBufer=new TUndoBufer();
	$(document).ready(function() 
	{
		<?php if($_GET['project']): ?>
			$("#saForm, #wnForm").ajaxForm(function() {
				alert("Ключи были добавлены!");
			});
			$("#check_maikeys").ajaxForm(function() {
				alert("Основные ключи были обнолены!");
			});
			$("#add_maikeys_2").ajaxForm(function() {
				alert("Основные ключи были добавлены!");
			});
			$("#savefrom").ajaxForm(function() {
				if(document.getElementById('savefrombtn').value!='Обновить')
					window.status='Ключевики были обновлены';
				else
					window.status='Ключевики были сохранены';
				document.getElementById('savefrombtn').value='Обновить';
				document.getElementById('savefrombtn').disabled=true;
			});/**/
		<?php endif; ?>
		<?php if($_GET['key']): ?>
			<?php if(!$Was): ?>
				var els=$(".key_cb");
				for(var i=0;i<els.length; i++)
				{
					var el=els[i];
					el.checked="checked";
				}
			<?php endif; ?>
			Filters.Load();
			refresh_selection();
		<?php endif; ?>
        $(".tablesorter").tablesorter({
			widgets: ['zebra'] 
		});
		UndoBufer.Save();
		<?php if($Was): ?>
			document.getElementById('savefrombtn').disabled=true;
		<?php endif; ?>
	});
	</script>
</head>
<body onkeydown="body_onkeypress(event)">
	<h1><?php 
		if(!$_GET['project']) 
			echo 'Hkey Advances WordStat';
		else
			echo 'Шаг 2: Подбор кеев';
	?></h1>
	
	<?php if($_GET['project']):?>
		<a href="#" onclick="document.getElementById('mainkeys1').style.display='block'; this.style.display='none'; return false;"
			>Показать основные ключи проекта >></a>
		<br /><br />
		<div class="mainkeys" id="mainkeys1" style="display:none">
			<table width="100%"><tr>
				<td valign="top" style="min-width:500px;">
					<form method='post' id="check_maikeys">
						<h3>Основные ключи проекта</h3>
						Здесь вы можете выключить часть основных ключей проекта. 
						Снимите с них галочки и нажмите "Обновить основные ключи".
						<br /><br />
						<table class='tablesorter {sortlist: [[2,1]]}' style="min-width:500px; margin-top:0">
							<thead><tr>
							<th>Ключ</th><th>Выбрано</th><th>Сумма показов</th>
							</tr></thead>
							<tbody>
								<?php PrintMainKeys($ProjectID,true);?>
							</tbody>
						</table>
						<input type='hidden' name='s_action' value='check_maikeys' />
						<input type='submit' value='Обновить основные ключи' />
					</form>	
				</td>	
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td valign="top" style="min-width:300px;">
					<form method='post' id='add_maikeys_2'>
						<h3>Добавить основные ключи</h3>
						Впишите новые основные ключи проекта, каждый в отдельной строке и нажмите "Добавить".<br />
						<br />
						<textarea name='keys' spellcheck="false" style="width:100%; height:150px; min-width:300px; white-space:nowrap;"></textarea>
						<br /><br />
						<input type='hidden' name='s_action' value='add_maikeys_2' />
						<input type='submit' value='Добавить' />
					</form>	
				</td>				
			</tr></table>
			<br /><br />
			<a onclick="return nextdialog();" href="3step.php?project=<?php echo $_GET['project'];?>">Перейти на следующий шаг >></a>
			<br /><br />
		</div> 
		
	<?php endif;//;($_GET['project']):?>	
	<form>
		<?php if($_GET['project']):?>
			<input type='hidden' name='project' value="<?php echo $_GET['project'];?> "/>
		<?php endif;//;($_GET['project']):?>
		<input name='key' size="70" value="<?php echo $DspKey;?>"/>
		<input type='submit' value="Поехали"/>
	</form>
<?php 	
if($_GET['key']):
	if($_GET['project'])
		echo '<br />Выберите необходимые ключи и нажмите кнопку "Сохранить"<br />';
	$Res=AdvancesWordStat($_GET['key']);
	$i1=1;
	$i2=1;
		echo '<pre>';
		//	print_r($Res['TreeN']);
		//	print_r($Res['Tree']);
		echo '</pre>';
		//addslashes
	$SData=htmlspecialchars(serialize($Res['Data']));
?>
<!-- 1 -->
	<?php if($_GET['project']):?>
		<form id="savefrom" method="post">
			<input type="hidden" name="s_action" value="add_keys" />
			<input type="hidden" name="pid" value="<?php echo $_GET['project']; ?>" />
			<input type="hidden" name="mkid" value="<?php echo $_GET['mkid']; ?>" />
			<input type="hidden" name="selected" id="s2_selected" value="" />
			<input type="hidden" name="data" value='<?php echo $SData; ?>' />
			<input type="submit" id="savefrombtn" <?php if($Was) echo 'disabled="disabled"'?> value="Сохранить" />
		</form>
	<?php endif; ?>
	<script type="text/javascript">
		var KeyTree = new Array();
		<?php foreach($Res['TreeN'] as $Parent =>$Childrens): ?>
			KeyTree[<?php echo $Parent; ?>] = [<?php echo JOIN(',', $Childrens); ?>];
		<?php endforeach; ?>	
		var tFilters = new Array();
		var mainkey='<?php echo $_GET['key'];?>';
	</script>	
	<?php if(count($Res['Keys'])>1 && !$_GET['project']):?>
		<a href="#" title="Позать частотность употребления словоформ" onclick="this.style.display='none'; document.getElementById('wordforms').style.display='block'; return false;" 
		/>Cловоформы >></a>
		<div id='wordforms'>
			<h2>Анализ частотности употреблений словоформ</h2>
			Анализ частотности употребления словоформ в чистых показах введеного вами ключа и его уточнений.
			<?php foreach($Res['Forms'] as $Word =>$WordForms):?>
				<?php if(strpos($Word,' ')) echo '<br style="clear:both" />';?>
				<div class='wordform'>
					<h3><?php echo $Word;?></h3>
					<table class='wordformtable tablesorter {sortlist: [[2,1]]}'>
						<thead>
							<tr>
								<th>Словоформа</th><th>Показы</th><th>%</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($WordForms as $Form =>$Count): ?>
							<tr>
								<td><?php echo $Form;?></td>
								<td><?php echo $Count;?></td>
								<td><?php echo round(($Count/$Res['Summ'])*100.0, 1)?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class='wordformpie'>
						<?php 
							if(!strpos($Word,' '))
								the_google_pie($WordForms,'',200, 150);
							else
								the_google_pie($WordForms,'',300, 200);
						?>
					</div>	
				</div>
			<?php endforeach; ?>
			<br style="clear:both" /><br /><br /><br />
		</div>
	<?php endif;?>
	<table style="width:100%">
		<tr>
			<td valign="top" style="white-space:nowrap">
				<h2 style="margin-top:0;">Запросы</h2>
				<?php if(!$_GET['project']):?>
					<i>Все</i>    &mdash; показы ключа и его уточнений, по WordStat<br /> 
					<i>Чистые</i> &mdash; оценка числа показов ключа без уточнений.<br />
					<i>Чистые</i> &mdash; оценка выдачи WordStat, если запрос взять в кавычки.<br />
					<br />
				<?php endif;?>
				Клик по ключевику отменяет или выделяет запрос<br />
				Клик по чекбоксу отменяет или выделяет запрос и все его подзапросы<br />
				Ctrl+Z отменить действие. Ctrl+Y вернутся.<br />
				Чтобы упорядочить по столбцу щелкните по его названию<br />
				<br />
				<table id="keytable" class='ztable tablesorter {sortlist: [[1,1]]}'>
					<thead>
						<tr>
							<!--<th style="padding-right:10px;"></th>-->
							<th>Запрос</th>
							<th>Все</th>
							<th>Чистые</th>
						</tr>
					</thead>
					<tbody>
						<?php $i=0; ?>
						<?php foreach($Res['Keys'] as $Key =>$Data): ?>
						<tr>
							<td class='key_td'><!--<?php echo $Key;?>-->
								<input
									onclick="key_cb_click(event,<?php echo $i;?>);"
									<?php if(!$Was || $SelectedKeys[$Key]): ?>
										checked="checked" 
									<?php endif; ?>
									type="checkbox" class="key_cb"
									name="key_<?php echo $i;?>" 
									id="key_cb_<?php echo $i;?>"
									clearcount="<?php echo $Data['ClearCount'];?>" 
									value="<?php echo $Key;?>" 
									ival="<?php echo $i;?>"
									addon="<?php echo $Data['Addon'];?>"
								/>
								<span onclick="key_span_click(event,<?php echo $i;?>);"><?php 
										echo $Key; if($Data['Virtual']) echo '(*)';
								?></span>
								</label>
							</td>
							<td><?php echo $Data['DirtyCount'];?></td>
							<td><?php echo $Data['ClearCount'];?></td>
						</tr>
						<?php $i++; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
				<div id="oselkeys1">
					<h2 <?php if($_GET['project']) echo "style='display:none'";?>>Выбранные ключи</h2>
					<textarea spellcheck="false" id="fselkeys1" onfocus="this.select()" wrap="on" rows="1"></textarea><br />
					<textarea <?php if($_GET['project']) echo "style='display:none'";?> spellcheck="false" id="selkeys1"  onfocus="this.select()" wrap="off"></textarea>
					<div id="selkeys1info"></div>
				</div>
			</td>
			<td style="width:100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td valign="top" style="white-space:nowrap">
			<div id="oselkeys2">					
				<h2 <?php if($_GET['project']) echo "style='display:none'";?>>Выбранные ключи</h2>
				<textarea spellcheck="false" id="fselkeys2" onfocus="this.select()" wrap="on" rows="1"></textarea><br />
				<textarea <?php if($_GET['project']) echo "style='display:none'";?> spellcheck="false" id="selkeys2"  onfocus="this.select()" wrap="off"></textarea>
				<div id="selkeys2info"></div>
			</div>
			<?php 
				$DirtyDataSet=Array();
				$ClearDataSet=Array();
				foreach($Res['Keys'] as $Key =>$Data)
				{
					$DirtyDataSet[$Key]=$Data['DirtyCount'];
					$ClearDataSet[$Key]=$Data['ClearCount'];
				}
			?>
			<br /><br />
			<?php if(count($Res['Keys'])>1 && !$_GET['project']):?>
				<?php the_google_pie($Res['SingleСlarify'],'Уточняющие слова',450, 300,true);?>
				<br /><br />
				<?php the_google_pie($Res['CountСlarify'],'Число уточняющих слов',450, 300,true);?>
				<br /><small>При расчете числа уточняющих слов игнорируются предлоги: «в», «из» и другие</small><br />
				<?php// the_google_pie($DirtyDataSet,'Все показы',450, 300);?>
				<br /><br />
				<?php the_google_pie($ClearDataSet,'Показы чистого ключа',450, 300);?>
				<br /><br />
			<?php endif;?>
			<?php 	
				if($_GET['project'])
					$Res['SeeAlso']=DelDoublesMainKeys($Res['SeeAlso'],$MainKeysCS)
			?>	
			<?php if(count($Res['SeeAlso'])):?>
				<form method="post" id="saForm">
					<h2>Смотрите также</h2>
					<?php if($_GET['project']):?>
						<small>Стоп-слова (они подсвечены <span color="gray">серым</span>) будут верезаны из основного ключа</small> 
					<?php endif;?>
					<table id="seealsotable"
						   class='ztable tablesorter {sortlist: [[<?php echo 1 + (bool) $_GET['project'];?>,1]]}'>
						<thead>
							<tr>
								<?php if($_GET['project']) echo "<th></th>";?>
								<th>Запрос</th><th>Показы <?php if(!$Res['PT']) echo '(ориентировочно)';?></th>
							</tr>
						</thead>
						<tbody>
							<?php $i=0; ?>
							<?php foreach($Res['SeeAlso'] as $Key =>$Count):  ?>
							<tr>
								<?php if($_GET['project']):?>
									<td>
										<input type="checkbox" 
											checked="checked"
											name="sa_$i" 
											value="<?php echo DelStopWords($Key);?>" 
										/>
									</td>
								<?php endif;?>
								<td><?php echo AWSLink($Key);?></td>
								<td><?php echo $Count;?></td>
							</tr>
							<?php $i++; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php if($_GET['project']):?>
						<input type="hidden" name="pid" value="<?php echo $_GET['project'];?>" />
						<input type="hidden" name="s_action" value="add_main_keys" />
						<input type="submit" value="Добавить эти ключи в проект" />
					<?php endif; ?>
				</form>	
				<br /><br />
			<?php endif;?>
			<?php 	
				if($_GET['project'])
					$Res['Near']=DelDoublesMainKeys($Res['Near'],$MainKeysCS)
			?>	
			<?php if(count($Res['Near'])):?>
				<form method="post" id="wnForm">
					<h2>Похожие по WordStat</h2>
					<?php if(!count($Res['SeeAlso']) && $_GET['project']):?>
						<small>Стоп-слова (они подсвечены <span color="gray">серым</span>) будут верезаны из основного ключа</small> 
					<?php endif;?>
					<table id="neartable" class='ztable tablesorter {sortlist: [[<?php echo 0 + (bool) $_GET['project'];?>,0]]}'>
						<thead>
							<tr>
								<?php if($_GET['project']) echo "<th></th>";?>
								<th>№</th><th>Запрос</th><th>Показы</th>
							</tr>
						</thead>
						<tbody>
							<?php $i=0; ?>
							<?php foreach($Res['Near'] as $Key =>$Count):  ?>
							<tr>
								<?php if($_GET['project']):?>
									<td>
										<input type="checkbox" 
											   name="sa_$i" 
											   value="<?php echo DelStopWords($Key);?>" 
										/>
									</td>
								<?php endif;?>
								<td><?php echo $i+1;?></td>
								<td>
									<?php echo AWSLink($Key);?>
								</td>
								<td><?php echo $Count;?></td>
							</tr>
							<?php $i++; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php if($_GET['project']):?>
						<input type="hidden" name="pid" value="<?php echo $_GET['project'];?>" />
						<input type="hidden" name="s_action" value="add_main_keys" />
						<input type="submit" value="Добавить эти ключи в проект" />
					<?php endif; ?>
				</form>					
			<?php endif;?>
			</td>
		</tr>
	</table>
<?php endif;?>
	<?php if($_GET['project']):?>
	<br /><br />
		<div class="mainkeys">
			<?php PrintMainKeysShort($ProjectID,true);?>
			<br /><br />
			<a onclick="return nextdialog();" href="3step.php?project=<?php echo $_GET['project'];?>">Перейти на следующий шаг >></a>
		</div> 
	<br /><br />	
	<?php endif;//;($_GET['project']):?>	
</body>
</html>