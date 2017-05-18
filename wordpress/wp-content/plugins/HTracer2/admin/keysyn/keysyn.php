<?php
	include_once("keysyn_fun.php");
	$DspKey=$_GET['key'];
	if(!$DspKey)	
		$DspKey='ноутбук одесса';
	if(!$_GET['count'])
		$_GET['count']=50;
	if($_GET['ajax'])	
	{	
		$Res=SynKeyFull($_GET['key'], $_GET['count']);	
		if($_GET['what']=='clear')
			foreach($Res['Clear'] as $Key) 
				echo $Key."\n";
		else
			foreach($Res['Sape'] as $Key) 
				echo $Key."\n";
		exit;
	}
?>
<html>
<head>
	<title>Синомайзер ключевиков</title>
	<meta name="robots" content="noindex,nofollow">
</head>
<body>
<br />
<table style="width:100%"><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td style="width:100%">
	<h1>Синомайзер ключевиков</h1>
	<p>
		Введите сюда ключевик (например "ноутбук одесса") и нажмите поехали. 
		Скрипт выводит ключи пропорционально вордстату.
		Ключ лучше вводить в единственном числе.
		Хорошо работает с рагиональными и коммерческими запросами.
		Скрипт использует данные вордстата и свои алгоримы для синомизации ключей.
	</p>
	<br />
	<form>
		<input name='key' size="70" value="<?php echo $DspKey;?>"/>
		<input name='count' size="3" value="<?php echo $_GET['count'];?>"/><br />
		<input type='submit' value="Поехали"/>
	</form>
<?php 	
	if($_GET['key']):	
		$Res=SynKeyFull($_GET['key'],$_GET['count']);
?>
		<table style="width:100%"><tr>
			<td valign="top" style="white-space:nowrap">
				<h2>Чистые</h2>
				<?php foreach($Res['Clear'] as $Key) echo $Key.'<br />' ?>
			</td>
			<td style="width:100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td valign="top" style="white-space:nowrap">
				<h2>Sape</h2>
				<?php foreach($Res['Sape'] as $Key) echo $Key.'<br />' ?>
			</td>
		</tr></table>
<?php endif;?>		
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></td></tr></table>
</body>
</html>