<?php 
	include_once('HTracer.php'); 
	header("Content-type: text/html;charset=UTF-8");
?>
<!--Смотрите текст от CODE до /CODE-->	


<html>	
<head>
	<title>Пример поисковых подсказок</title>
	<meta name="robots" content="noindex,nofollow" />
</head>
<body>
	<ol>
		<li>Перекопируйте 'ajax_hints.js', 'ajax_hints.php', 'ajax_hints.css' из папки HTracer в корень вашего сайта</li>
		<li>Откройте этот файл в текстовом редакторе</li>
		<li>Измените в этом файле htest.ru на домен вашего сайта</li>
		<li>См. код от комментария CODE до /CODE и по аналогии встате код вашей формы</li>
		<li>По аналогии вставьте код вашей формы поиска</li>
	</ol>
	<br />	
	Если у подсказок на этой странице неправильная кодировка, то это не глюк. 
	На сайте они будут выводиться в правильной кодировке.	
	<br />	

<!--CODE-->	
<script type="text/javascript" src='http://htest.ru/ajax_hints.js'></script>
<link rel="stylesheet" href="http://htest.ru/ajax_hints.css" type="text/css" media="all" />
<form>	
	<table id='search_hints_table' cellpadding='0' cellspacing='0'><tbody>
		<tr><td>
			<!--поле ввода -->	
			<!--мы оборачиваем всю строчку в которой находиться input-->
			Поиск: <input id='search_input' autocomplete='off' /> 
			<!-- autocomplete='off' отключение автозаполнения -->	
			<!-- если вы поменяете id, то нужно поменять его и при вызове initSearchHints('search_input')-->	
		</td></tr>
		<tr><td>
			<div id='search_hints_outer'>
				<div id='search_hints'>
					<div id='search_hints_inner'>
						<?php htracer_print_queries_like(); ?>
					</div>
				<div>	
			</div>
		</td></tr>
	</tbody></table> 
	<!--кнопка-->
	<!--поскольку кнопка в новой строке под полем ввода (из-за <br />) мы ее не оборачиваем-->
	<br /> <input type='submit' value='Поиск' /> 	
</form>	
<script type="text/javascript">	
	var searchHintsURL='http://htest.ru/ajax_hints.php';
	initSearchHints('search_input');
</script>
<!--/CODE-->	
</body>
</html>