<?php
	include_once('scb_funs.php');
	header ("Content-type: text/html;charset=UTF-8");
	if($_POST['waspost'])
	{
		if(!$_POST['domain']||!strpos($_POST['domain'],'.'))
			$Error='Введите домен';
		elseif(!trim($_POST['keys']))
			$Error='Введите основные ключи';
		else
		{
			$PID=CreateProject($_POST['domain'],$_POST['keys']);
			$MKID=GetFirstKeyID($PID);
			header("Location: aws.php?project=$PID&mkid=$MKID");
			exit;
		}
	}
?>
<html>
<head>
	<title>Создание проекта</title>
	<meta name="robots" content="noindex,nofollow">
<!--colorbox-->
	<script type="text/javascript" src="js/tablesorter/jquery-latest.js"></script>
	<script type="text/javascript" src="js/colorbox/jquery.colorbox-min.js"></script>
	<link media="screen" rel="stylesheet" href="js/colorbox/colorbox.css" />
<!--/colorbox-->

	<link media="screen" rel="stylesheet" href="1step.css" />
	<script type="text/javascript" src="1step.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".example8").colorbox({width:"620px", inline:true, href:"#inline_example1"});
			document.getElementById('gen').value = "{слово|синоним} {слово2|синоним2}";
			document.getElementById('genkeys').value = "";
			document.getElementById('genkeys').innerHTML = "";
			document.getElementById('addbtn').disabled = true;
		})
	</script>
</head>
<body>
<h1 style="padding-left:25px;">Шаг 1: Создание проекта</h1>
<?php if($Error) echo "<div id='error'>$Error</div>"; ?>
<form method="post" style="padding-left:25px;">
	На этом шаге вы должны выбрать домен и основные запросы.<br />
	Например, "ноутбук одесса" - основной запрос. <br />
	"купить ноутбуки в одессе" и "одесса ноутбуки недорого" его уточнения.<br />
	<br />
	<b>Домен</b>:<br />
	<input type="text" name="domain" /><i>Именно домен. Без слешей, без http://. Например, site.ru </i><br />
	<br />
	<b>Основные запросы</b>:<br />
	<table id="ta_table"><tr>
		<td><textarea spellcheck="false" name="keys" id="keys"></textarea><td>
		<td id="ta_hint">	
			Только основные запросы.<br />
			Уточняющие запросы вы выберёте на следующем шаге.<br />
			На других шагах вы также можете добавлять основные запросы<br />
			Вводите запросы без стоп слов (в, на, об и др)<br />
			<br />
			Вы можете использовать <a class='example8' href="#">генератор</a>
		<td>
	</tr></table>
	<br />
	<input type="submit" value="Создать проект" />
	<input type="hidden" name="waspost" value="1" />
<?php $Projects=GetProjectsLinks(); ?>
<?php if($Projects):?>
	<br /><br />
	<h3>Проекты</h3>
	<div id='projects'>
		<?php echo $Projects;?> 
	</div>
	<br /><br />
<?php endif;?>

</form>


	<div style='display:none'><div id='inline_example1' style='padding:40px;  background:#fff;'>
		<h2>Генератор кеев</h2>
		Этот инструмент для удобной генерации кеев.<br />
		<table cellpadding="0" cellspacing="0">
			<tr><td>Синтаксис:</td><td> <i>{слово|синоним} {слово2|синоним2}</i></td></tr>
			<tr><td>Например: </td><td> <i>{привлечь|соблазнить|познакомиться} {парня|мужчину}</td></tr>
		</table>

		<br />
		<b>Выражение:</b><br />
		<input type="text" name="gen" id="gen" onkeypress="genkeypress(event)" /><br />
		<input type="button" id="genbtn" value="Сгенерировать" disabled="true" onclick="genclick()" /><br />
		<br />
		<b>Результат:</b><br />
		<textarea spellcheck="false" name="genkeys" id="genkeys"></textarea><br />
		<input type="button" id="addbtn" value="Добавить" onclick="addclick()" disabled="true" /><br />
	</div></div>
</body>
</html>