<?php
	include_once('scb_funs.php');
	header ("Content-type: text/html;charset=UTF-8");
?>
<html>
<head>
	<title>Шаг 4: Экспорт</title>
	<meta name="robots" content="noindex,nofollow">
	<link media="screen" rel="stylesheet" href="4step.css" />
</head>
<body>
	<h1>Шаг 4: Экспорт</h1>
	Пока можно экспортировать только в HTracer<br />
	<br />
	<form method='post'>
		<b>Число переходов</b>:
		<input name='per_count' value='10000' size="7" />&nbsp;&nbsp;
		<input type='submit' value='Поехали' />  <br />
	</form>
<?php
	if($_POST['per_count']):
		$Keys=GetProjectKeys($_GET['project']);
		$Summ=0;
		foreach ($Keys as $Key =>$Data)
			$Summ+=(int) $Data['Count'];
		$k=$_POST['per_count']/$Summ;
		if(!count($Keys))
			echo "<b>Нет ни одного ключа с прописанным урлом!</b>";
?>
		Экспорт осуществляеться путем имитации Google Analitics <br /> 
		Откройте в браузере "HTracer/admin/import.php" и встатьте в поле ввода слудющий текст:
		<br />
		<textarea spellcheck="false" style="width:100%; height:500px; min-width:300px; white-space:nowrap;" onfocus="this.select()"
		><?php
			foreach ($Keys as $Key =>$Data)
			{
				$Count=round($Data['Count'] * $k);
				if($Count==0)	
					continue;
				$Key=str_replace(',',' ',$Key);
				$Key=str_replace('  ',' ',$Key);
				$Key=str_replace('  ',' ',$Key);
				echo "{$Data['URL']},$Key,0,$Count,0,0,0\n";
			}
		?></textarea>
	<?php endif;?>
</body>
</html>