<?php
	header ("Content-type: text/html;charset=utf8");
	include('HTracer.php');
	
	the_keys_cloud(Array('procent'=>20,'count'=>50,'offset'=>0));
	//exit();
	//HTracer::P404_process();
	
	//echo '<pre>';
	//HTracer::is_UcWord_Load_Arrays();
	
	//echo (int) HTracer::have_okon('крыжополь',$GLOBALS['htracer_dict']['cities_ends']);
	//echo '<hr />';
	
	//print_r($GLOBALS['htracer_dict']['cities_ends0']);
	//echo '<hr />';
		
	//print_r($GLOBALS['htracer_dict']['cities_ends']);
//	//$a=Array('й'=>1);
	
//	echo (int) HTracer::have_okon('хуй',$a);
	
	 //
	 
	 
	//print_r($GLOBALS['htracer_dict']['cities_ends']);
	
	//echo HTracer::FixURL('site.ru/sdfsdfsdf').'<br />';
	//echo HTracer::FixURL('/sdfsdfsdf').'<br />';
	//echo HTracer::FixURL('http://site.ru/sdfsdfsdf').'<br />';
	//echo HTracer::FixURL('http://www.site.ru/sdfsdfsdf').'<br />';

	//exit();
	
	//$c=file_get_contents('http://spinorum.ru/golovnye_boli/golovnaya_bol_u_rebenka/');
	//echo HTracer::Insert($c);
	
	//exit();
	
	
	
	
	
	//exit();
	/*echo '<pre>';
	$Code="
		<html>
		<head>
			<title>1 &lt;&lt; 2 &lt;&lt; 3 &lt;&lt; 4 </title>
			<meta name='keywords' content='metakeywords' />
			<meta name='description' content='metadescription' />
		</head>
		<body>
		<script type='text/javascript'>
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-26897296-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
		</body>
	</html>";
	echo '<pre>';
	echo htmlspecialchars($Code);
	echo '<hr />';

	echo htmlspecialchars(str_replace('<sssssss',"\n<",HTRacer::Insert($Code)));
	echo '<hr />';
	echo htmlspecialchars(str_replace('<sssss',"\n<",  hkey_insert_cloud_by_selector($Code,"#ddss")));
	echo '<hr />';
	print_r(HTracer::get_page_meta());
	
	//exit();
	
	//HT_FormTitle
	$res=htracer_mysql_query('SELECT * FROM `htracer_pages` ORDER by Eva DESC LIMIT 100');
	echo '<table>';
	while($cur=mysql_fetch_array($res))
	{
		$_SERVER['REQUEST_URI']= $cur['URL'];
		echo '<tr><td style="color:gray">'.$_SERVER['REQUEST_URI'].'</td><td> ';
		echo '<td>'.HT_FormTitle('base').'</td><td>';
		echo '<pre>';
		print_r($GLOBALS['htracer_curent_page_keys_in']);
		echo '</td></tr>';
		unset($GLOBALS['htracer_curent_page_keys_in']);
		
	}
	echo '</table>';
	//exit();
	
	//HTracer::AddBonus(1300);

	
	*/
	//exit();
	$_SERVER["REQUEST_URI"]='http://htest.ru/category/rent/flats/page/3/';
	
$Code='
<html>
<head></head>
<body>
	<!--ulinks-->
	<!--the_keys_cloud-->
</body>
</html>
';
//$Code=file_get_contents('test.html');

	$Code2=htracer_do_all($Code);
	echo ($Code==$Code2)."<pre><table><tr><td valign='top' width='500' style='white-space:pre; width:500px; overflow:scroll;'>".htmlspecialchars($Code) ."</td>"
										."<td valign='top' style='white-space:pre' width:500px; overflow:scroll;>".htmlspecialchars($Code2)."</td></tr></table>";
	//exit();
	
	echo HTracer::Insert('проверка кодировки 
									<a href="http://htest.ru/category/rent/flats/page/2/">234234 ывыв</a>
									<a href="http://htest.ru/category/rent/flats/">234234 ывыв</a>
									<img  />
									<a href="http://htest.ru/tag/arkadiya/?map=1" style="font-size: 147%;">Аркадия-Одесса карта</a> 
								0'
						);
	echo '<hr />';					
	the_keys_cloud();
//	exit();

	//echo round(1.3333,1);
//	$str='Мама 2323  мыла 2323 22323 ра2му';
//	for($i=0;$i<10000000;$i++){}

	//echo microtime(true);
	
	function getmicrotime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	$Text="
		JTESTA asdsads asd sad sa <br /> ыфваы ыва вы 
		asdsad sd asdsa dsad asd asd  JTESTB,<br /> ывавыавы а ыв
		asd sd swdsddddddddasdsadsad JTEST<br /> ыв авыавы
		asdsad<br />  фыв фы
		sadsadasds<br /> фывфыв
	";
	$Text=trim($Text);
	echo '<pre>';
	echo hkey_insert_context_links($Text);
	echo '<hr />';
	//echo insert_keywords_cb($Text);
	
//$_SERVER['REQUEST_URI']='/tag/arkadiya/';	
	ob_start();
	//echo '<!--htracer_context_links-->'.$Text.'<!--/htracer_context_links-->';
	//echo '<div id="content">'.$Text.'</div>';
//	echo $Text;
	echo "1111111111<div id='id'>222222222222</div>33333333";

	htracer_ob_end();
//echo hkey_insert_cloud_by_selector("1111111111<div id='id'>222222222222</div>33333333","_after #id ?style=100/100");
 
//echo hkey_insert_context_links($Text);



	if(isset($_POST['text']))
		$Text=$_POST['text'];
	
	echo '<head><meta name="robots" content="noindex,nofollow"></head>';	
	echo 'Этот файл предназначен для внутреннего тестирования HTracer в процессе разработки.';
/*
	$CSS='
.ht_cloud a{white-space:nowrap;}
.ht_cloud {text-align:center;}
	';
	echo "<style>$CSS</style>";
	echo '<div class="ht_cloud" style="max-width:400px;padding:100px;">';
//	$Params="sort=0&upcase=0&sanitarize=0";	
	echo "<i><pre>$CSS</pre></i>";
		the_keys_cloud();
	echo '</div>';
	print_r(explode('\&q\=','sadasdasd&q=asdasdasd'));
	print_r(split('\&q\=','sadasdasd&q=asdasdasd'));
	*/
	
	echo '<h2>Вставка</h2>';	
	
	echo '<h2>Облако</h2>';	
	$time_start = getmicrotime();
	for($i=0;$i<1;$i++)	
		get_keys_cloud(); 
	echo '<hr /> Z:Fy() '.(getmicrotime()-$time_start).'<br />';
	//exit();

	
	echo '<h2>Облако 40/100</h2>';	
	the_keys_cloud("style=40/100"); 
	
	echo '<h2>Облако на d</h2>';	
	the_keys_cloud("style=40/100&urlstart=/d");

	//echo count($HTracer->SelectMaxPages($MinLinks+1,'d'));
	echo '<h2>Облако the_keys_cloud_subdir</h2>';	
	$_SERVER["REQUEST_URI"]='/domashnjaja-bytovaja-tehnika/massazhnye-nakidki';
	the_keys_cloud_subdir("style=40/100&urlstart=/c");
	
	echo '<h2>Титлы</h2>';	
	echo insert_keywords_cb('
		<a href="http://htest.ru/ingaljatory">Nebulflaem super купить</a> 
		<a href="http://htest.ru/domashnjaja-bytovaja-tehnika/massazhnye-kresla/rongtai_rt-y019"</a>
		<a href="http://htest.ru/tonometry/rossmax/ehlektronnyjj-avtomaticheskijj-tonometr-na-zapjaste-rossmax-p400" style="font-size: 74%;" title="Rossmax p400">Rossmax p400</a> <a href="http://htest.ru/shipping" style="font-size: 89%;" title="Aquajet Ld-A7">Sentech Al-2500</a> <a href="http://htest.ru/medicinskoe-oborudovanie" style="font-size: 98%;" title="Ves dh 69l">Ves dh 69l</a> <a href="http://htest.ru/medicinskoe-oborudovanie/kresla_kolyaski/100_632" style="font-size: 73%;" title="Армед 250 lcpq">Армед 250 lcpq</a> <a href="http://htest.ru/domashnjaja-bytovaja-tehnika/applikatory-ljapko/100_878">Валик Ляпко цена</a>
	');
	/*
	
	htracer_write_cash_file(Array('x'=>1,2,3,'var'=>'value'),'Array');
	htracer_write_cash_file('content1.1','function1');
	htracer_write_cash_file('content1.2','function1');
	htracer_write_cash_file('content1.3','function1');
	
	htracer_write_cash_file('content2.1','function2','fdsdfdsfsd');
	htracer_write_cash_file('content2.2','function2','fdsdfdsfsd');
	htracer_write_cash_file('content2.3','function2','fdsdfdsfsd');
	
	echo '<br />1.1:::'.htracer_read_cash_file('function1',false,true,false);
	echo '<br />1.2:::'.htracer_read_cash_file('function1');
	echo '<br />1.3:::'.htracer_read_cash_file('function1');

	echo '<br />2.0:::'.htracer_read_cash_file('function2','4444444444444');
	echo '<br />2.1:::'.htracer_read_cash_file('function2','fdsdfdsfsd');
	echo '<br />2.2:::'.htracer_read_cash_file('function2','fdsdfdsfsd');
	echo '<br />2.3:::'.htracer_read_cash_file('function2','fdsdfdsfsd');
	
	echo '<br />3.4:::'.htracer_read_cash_file('function3','fdsdfdsasdasdfsd');
	echo '<br />3.4:::'.htracer_read_cash_file('function3','fdsdfdsasdasdfsd');
	echo '<br />3.4:::'.htracer_read_cash_file('function3','fdsdfdsasdasdfsd');
	echo '<br />Array:::';
	print_r(htracer_read_cash_file('Array'));
*/
	echo '<hr />';

	
	
	
	echo '<br /><br /><br />';
	//$GLOBALS['htracer_site_stop_words']=mb_convert_encoding($GLOBALS['htracer_site_stop_words'],'cp1251','utf8');
	//echo $GLOBALS['htracer_site_stop_words'];
	echo "
		<h2>Контекстные ссылки</h2>
		<form method='post'>
			Введите сюда текст для тестирования растановки ссылок.<br />
			<textarea name='text' rows='15' cols='100'>$Text</textarea><br />
			<input type='submit' />
		<br />
		</form>
		";
	

	echo '<h2>Контекстные ссылки CP1251</h2>';	
	$GLOBALS['htracer_encoding']='cp1251';
	echo hkey_insert_context_links($Text);  	

	echo '<br /><br /><br />';
	echo '<h2>Контекстные ссылки UTF-8</h2>';	
	$Text2=mb_convert_encoding($Text,'utf8','cp1251');
	$GLOBALS['htracer_encoding']='utf8';
	echo hkey_insert_context_links($Text2);  	
	
	$GLOBALS['hkey_insert_context_links']='ranges';
	$GLOBALS['htracer_encoding']='cp1251';
	echo '<br /><br /><br />';
	echo '<h2>Контекстные ссылки (htracer_start)</h2>';	
	echo "\r\n\r\n\r\n\r\n\r\n\r\n";
	htracer_start();
	echo "<b>Здесь ссылок не должно быть:</b> 
			<br /><br />$Text<br /><br />
		<b>А здесь должны:</b>
		<br />
			\r\n\r\n\r\n\r\n\r\n\r\n<!--htracer_context_links-->";
	echo $Text;
	echo '<!--/htracer_context_links-->';


	echo '<h2>Контекстные ссылки (htracer_ob_end)</h2>';	
	echo "\r\n\r\n\r\n\r\n\r\n\r\n";
	ob_start();
	echo "<b>Здесь ссылок не должно быть:</b> 
			<br /><br />$Text<br /><br />
		<b>А здесь должны:</b>
		<br />
			\r\n\r\n\r\n\r\n\r\n\r\n<!--htracer_context_links-->";
	echo $Text;
	echo '<!--/htracer_context_links-->';
	htracer_ob_end(true);
	HTracer_Refresh_Full_Time();
	echo 'htracer_full_time='.$GLOBALS['htracer_full_time'];
?>