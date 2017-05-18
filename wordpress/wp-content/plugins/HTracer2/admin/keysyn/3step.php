<?php
	include_once('scb_funs.php');
	header ("Content-type: text/html;charset=UTF-8");
	if($_POST['waspost'])
	{
		$Keys=Array(); 
		foreach ($_POST as $Key => $Val)
		{
			$Key=split('_',$Key);
			if(count($Key)<3|| $Key[0]!='kid')
				continue;
			$Field=$Key[2];
			$Key=$Key[1];
			if(!isset($Keys[$Key]))
				$Keys[$Key]=Array('inherit'=>0);
			$Keys[$Key][$Field]=$Val;
		}
		UpdateKeys($Keys);
		echo '<br /> Результаты были сохранены <br />';
	}
	$Project=GetProject($_GET['project']);
?>
<html>
<head>
	<title>Выбор продвигаемых страниц</title>
	<meta name="robots" content="noindex,nofollow">
<!--colorbox-->
	<script type="text/javascript" src="js/tablesorter/jquery-latest.js"></script><!-- -->
	<script type="text/javascript" src="js/colorbox/jquery.colorbox-min.js"></script>
	<link media="screen" rel="stylesheet" href="js/colorbox/colorbox.css" />
<!--/colorbox-->
<!--Google Search-->
	<script src="https://www.google.com/jsapi" type="text/javascript"></script>
<!--/Google Search-->

	<link media="screen" rel="stylesheet" href="3step.css" />
	<link media="screen" rel="stylesheet" href="serp.css" />
	<script type="text/javascript" src="3step.js"></script>

	<script type="text/javascript">
		var Keys=new TKeys();
		var Domain="<?php echo $Project['Domain']; ?>";
		<?php //Запоминаем ключи
			foreach($Project['Keys'] as $Key=>$Data)
			{
				//this.Add(iID,iKey,iClear,iDirty,iURL,iSetBy,iInherit,iChildrensIDs)
				echo "\n Keys.Add(
								{$Data['ID']},
								'$Key',
								{$Data['ClearCount']},
								{$Data['DirtyCount']},
								'{$Data['URL']}',
								'{$Data['SetBy']}',
								{$Data['Inherit']},
								[".join(',',$Data['Childrens'])."]
					);";
			}
		?>
		Keys.Init();
		$(document).ready(function()
		{
			Keys.Synhr();//Синхронизируем данные
			//$("#savefrom").ajaxForm(function() {
			//	alert('saved');
			//});
			//YaBox();
			$(".x_link2").colorbox({width:"50%", inline:true, href:"#inline_example1"});
		});
	</script>
</head>
<body>
<h1 style="padding-left:25px;">Шаг 3: Выбор продвигаемых страниц</h1>
<form method="post" style="padding-left:25px;" id="savefrom">
	На этом шаге <b>вы должны выбрать страницы</b>, которые вы будете продвигать по соответсвующим запросам.<br />
	Дождитесь полной загрузки страницы и кликнете по какому-то мз запросов (они подчеркнуты чертачами)<br />
	После завершения работы кликните на кнопке сохранить<br />
	<br />
	<div id="ProjectKeys">
		<?php foreach($Project['MainKeys'] as $Key=>$Data): $Keys=$Data['Keys']; if(!count($Keys)) continue;?>
			<b class='expand' onclick="Expand(<?php echo $Data['ID'];?>)"><?php echo "$Key (".count($Keys).")";?></b><br />
			<div style="display:none" class='expand_div' id="expand_div_<?php echo $Data['ID'];?>">
				<br />
				<i>URL</i> &mdash; Адрес продвигаемой страницы по ключу<br /> 
				&nbsp;&nbsp;&nbsp;&nbsp;<i>http://<?php echo $Project['Domain']; ?></i> можно не писать<br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i>Пустая строка</i> &mdash; URL не задан<br />
				&nbsp;&nbsp;&nbsp;&nbsp;<i>Слеш (/)</i> &mdash; Главная страница<br />
				Если вы введете URL для ключа, то для всех его уточнений будет задан такой-же URL с пометкой унаследован.
				Однако если URL уточнения уже задан напрямую (без наследования), то он не наследуется. <br /><br />
				<table class="kid_table">
					<thead><tr class='trh'>
						<th class="key_th">Ключ</th>
						<th class="url_th">URL</th>
						<th class="setby_th">Определен</th>
						<th class="inherit_th">Унаследован</th>
					</tr></thead>
				<tbody>
					<?php $i=0;?>
					<?php foreach($Keys as $Key2=>$Data2): $KID=$Data2['ID'];$i++;?>
						<tr class="b<?php echo $i%2 ?>">
							<td class="key_td"><?php echo $Key2;?></td>
							<td class="url_td">
								<input 
									onchange="url_changed(<?php echo $KID;?>)";
									size="50"
									name="kid_<?php echo $KID;?>_url" 
									id="kid_<?php echo $KID;?>_url" 
									value=""
								/>
								<a  style="popup ya_link"
									onclick="YaBox('<?php echo $Key2;?>',<?php echo $KID;?>); return false"
									title="Открыть поиск ключа по сайту в Яндекс в новом окне"
									href="http://yandex.ru/yandsearch?text=<?php echo urlencode($Key2);?>&site=<?php echo $Project['Domain'];?>" 
									target="_blank">Я</a>
								<a  style="popup g_link" 
									title="Открыть поиск ключа по сайту в Google в новом окне"
									onclick="GBox('<?php echo $Key2;?>',<?php echo $KID;?>); return false"
									href="http://www.google.ru/search?ie=UTF-8&hl=ru&q=<?php echo urlencode($Key2.' site:'.$Project['Domain']);?>" 
									target="_blank">G</a>
								<a  style="popup s_link" id="kid_<?php echo $KID;?>_link"
									href="http://<?php echo $Project['Domain'];?>/" 
									title="Открыть сайт в новом окне"
									target="_blank">S</a>
							</td>
							<td class="setby_td"><input type="hidden" 
								name="kid_<?php echo $KID;?>_setby" 
								id="kid_<?php echo $KID;?>_setby" 
								value=""
							/><span id="kid_<?php echo $KID;?>_setby_span"></span>
							</td>
							<td class="inherit_td"><input type="checkbox"
								onchange="inherit_changed(<?php echo $KID;?>)";
								name="kid_<?php echo $KID;?>_inherit" 
								id="kid_<?php echo $KID;?>_inherit" 
								value="0"
							/></td>
						</tr>
					<?php endforeach//($Keys as $Key2=>$Data2);?>
				</tbody></table>
				<span class='expand' onclick="Expand(<?php echo $Data['ID'];?>)">Свернуть</span>
				<br /><br />
				<!--
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="aws.php?project=<?php echo $_GET['project'];?>&mkid=<?php echo $Data['ID'];?>">Вернутся на шаг назад</a>
				-->	
			</div>
		<?php endforeach; //($Project['Keys'] as $Key=>$Data): ?>
	</div>
	<br /><br />
	<input type="hidden" name="waspost" value="1" />
	<div id="errors"></div>
	<input type="submit" value="Сохранить" />
	<?php
		$PID=$_GET['project'];
		$MKID=GetFirstKeyID($PID);
	?>	
	<br /><br /><br />
	<a href='aws.php?<?php echo "project=$PID&mkid=$MKID"?>'>&lt;&lt;Вернуться на прошлый шаг</a>  
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='4step.php?<?php echo "project=$PID"?>'>Перейти на следующий шаг >></a>  
</form>
</body>
</html>