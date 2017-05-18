<?php
	//Version 3.3.3
	$GLOBALS['htracer_curent_version_id']=13;//Порядковый номер текущей версии. Нужен для синхронизации базы и версии
	$GLOBALS['htracer_ajax_pass']=md5(dirname(__FILE__));// Пароль для функций AJAX
	$GLOBALS['htracer_is_demo']=false;
	if($_SERVER['SERVER_NAME']=='demo.htracer.ru'||$_SERVER['SERVER_NAME']=='demo.htracer.com')
		$GLOBALS['htracer_is_demo']=true;

	//TODO
//ошибка с http

	//Увеличить словарь английских и русских слов за счет Гугл Translate
	//Version 3.4 Для веб. студий
		//API для модулей
		//Мультиадминка
		//Модули
			//Модуль снятия позиций
			//Модуль для добавления произвольного кода
			//Движок для редиректов и изменения ссыллок на сайте в соотв. с ними
	//Дальняя перспектива
		//Модуль для хлебных крошек
		//Паук для индексации сайта, определения ошибок редиректов и рел канноникал
		//Привязка поисковых подсказок через селектор
	
	//TODO: для скорости
	//1. Добавить подготовку к загрузке урлов в быстрый разбор
	//2. Добавить сбалансированный разбор (simple php htm parser)
	//3. Связать все селекторы и сбалансированный разбор
	//4. Загружать словарь по требованию
	//5. htracer_start одним буфером сделать
//	[{] [|] [}]
	//TODO: Для кода
	//1. Вынести все библиотеки в папку Include
	//2. Проверить скорость выполнения унаследованных стат. методов, если разницы нет, библиотеки инклудить наследованием  
	//3. Вынести USP из page.php в отдельный файл
	
	//Кеш сделать зависимым от кодировки
	
	//CallBack Functions для подавдения специфических обработчиков ошибок движка 
	function HTracer_Error_Handler($errno=0 , $errstr='',$errfile='',$errline='',$errcontext=false){return false;}
	function HTracer_Exception_Handler($Error=false){return false;}
	
	function ht_spec_getmicrotime($tp=false) 
	{ 
		list($usec, $sec) = explode(" ", microtime()); 
		return ((float)$usec + (float)$sec); 
	} 

	function HTracer_In()
	{//Отключение нотисов
		if(!isset($GLOBALS['htracer_function_deep']))
			$GLOBALS['htracer_function_deep']=0;
		if($GLOBALS['htracer_function_deep']===0)
		{
			if(isset($_GET['htracer_show_time']))
				$GLOBALS['htracer_start_time']=ht_spec_getmicrotime(true);
			if($_SERVER['SERVER_NAME']==='htest.ru')
				$GLOBALS['htracer_old_error_reporting']=error_reporting(E_ALL);//Разработка 
			elseif(isset($GLOBALS['htracer_test']) && $GLOBALS['htracer_test'])
				$GLOBALS['htracer_old_error_reporting']=error_reporting(E_ERROR | E_PARSE| E_WARNING);//Тестовый режим
			else
				$GLOBALS['htracer_old_error_reporting']=error_reporting(E_ERROR | E_PARSE);//Нормальная работа
			//Подавляем специфические обработчики ошибок движка
			if(function_exists('set_error_handler'))		
				$GLOBALS['htracer_old_error_handler']=set_error_handler('HTracer_Error_Handler');
			if(function_exists('set_exception_handler'))		
				$GLOBALS['htracer_old_exception_handler']=set_exception_handler('HTracer_Exception_Handler');
		}
		//echo str_repeat('&nbsp;',$GLOBALS['htracer_function_deep']).'IN<br />';
		$GLOBALS['htracer_function_deep']++;
	}
	function HTracer_Refresh_Full_Time()
	{
		//echo str_repeat('&nbsp;',$GLOBALS['htracer_function_deep']).'OUT<br />';
		if(isset($GLOBALS['htracer_start_time']))
		{
			if(!isset($GLOBALS['htracer_full_time']))
				$GLOBALS['htracer_full_time']=0;
			$GLOBALS['htracer_full_time']=$GLOBALS['htracer_full_time'] + (ht_spec_getmicrotime(true) - $GLOBALS['htracer_start_time']);
			unset($GLOBALS['htracer_start_time']);
		}
		if(isset($GLOBALS['htracer_full_time']))
			return round($GLOBALS['htracer_full_time'] * 1000);
		else
			return 0;
	}
	function HTracer_Out()
	{
		$GLOBALS['htracer_function_deep']--;	
		if($GLOBALS['htracer_function_deep']===0)
		{
			HTracer_Refresh_Full_Time();
			if(isset($GLOBALS['htracer_old_error_reporting']))
				error_reporting($GLOBALS['htracer_old_error_reporting']);
			if(isset($GLOBALS['htracer_old_error_handler']) && $GLOBALS['htracer_old_error_handler'])
				set_error_handler($GLOBALS['htracer_old_error_handler']);
			if(isset($GLOBALS['htracer_old_exception_handler']) && $GLOBALS['htracer_old_exception_handler'])
				set_exception_handler($GLOBALS['htracer_old_exception_handler']);	
		}
	}
	
	HTracer_In();//Убираем Нотисы

	$htdn_x=dirname(__FILE__);
	
	if($GLOBALS['htracer_is_demo'])
	{
		if(!isset($_COOKIE['prefix'])||!$_COOKIE['prefix']
		||strpos($_COOKIE['prefix'],' ')!==false||strpos($_COOKIE['prefix'],'*')!==false
		||strpos($_COOKIE['prefix'],'"')!==false||strpos($_COOKIE['prefix'],"'")!==false)
			die('Время действия вашего демо-доступа истекло. Перейдите на сайт htracer.ru и нажмите на ссылку просмотр демо.');
		$GLOBALS['htracer_mysql_prefix'] = $_COOKIE['prefix'];
		$GLOBALS['htracer_cash_days']    = 0; 
		//echo 'x='.$GLOBALS['htracer_mysql_prefix'];
		@include($htdn_x.'/admin/auto_config'.$GLOBALS['htracer_mysql_prefix'].'.php');	
	}
	else
		@include($htdn_x.'/admin/auto_config.php');// загружаем конфигурацию
	require($htdn_x.'/HTracerTMP.php');// загружаем всякие вспомогательные функции
	require($htdn_x.'/hkey_str.php');//Загружаем функции для работы со строками
	require($htdn_x.'/Dictionary.php');//Загружаем словари
	require($htdn_x.'/HStem.php');//стеминг (обрезание окончаний слов)
	require($htdn_x.'/HTracer_Interface.php');//функции, для самостоятельного программинга
	
	$ht_have_cl_selector=  isset($GLOBALS['hkey_insert_context_links']) 
						&& $GLOBALS['hkey_insert_context_links']==='selector' 
						&& isset($GLOBALS['htracer_context_links_selector']) 
						&& $GLOBALS['htracer_context_links_selector'];
	$ht_have_cloud_selector= isset($GLOBALS['htracer_cloud_selector']) && $GLOBALS['htracer_cloud_selector']; 
						
	if(version_compare(PHP_VERSION, '5', '>') && ($ht_have_cl_selector || $ht_have_cloud_selector))
		require($htdn_x.'/simple_html_dom.php');
	if(isset($_GET['htracer_show_time']) && $_GET['htracer_show_time']==$GLOBALS['htracer_ajax_pass'])
	{
		if($GLOBALS['htracer_cash_days'])
			$GLOBALS['htracer_short_cash']=true;
		if(isset($_GET['htracer_use_php_dom']))
			$GLOBALS['htracer_use_php_dom']=!!$_GET['htracer_use_php_dom'];
		if(isset($_GET['htracer_mysql_dont_create_tables']))
			$GLOBALS['htracer_mysql_dont_create_tables']=!!$_GET['htracer_mysql_dont_create_tables'];
		if(isset($_GET['htracer_mysql_close']))
			$GLOBALS['htracer_mysql_close']=!!$_GET['htracer_mysql_close'];
		if(isset($_GET['htracer_pconnect']))
			$GLOBALS['htracer_pconnect']=!!$_GET['htracer_pconnect'];
	}
	HTracer_Load();
	htracer_save_globals();

class HTracer// класс нужен для экранирования имен функций. Он статический
{//Объекты этого класса не создаються
// В классе находиться логика и работа с бд
 	function HTracer(){}
	
//********Математика*****************
	static function LogRound($count,$Osn)
	{//логарифмическое округление
	 //$count округляеться до наибольшей снизу целой степени числа $Osn
	 //Например, если $Osn=2, то  1=>1 2=>2 3=>2 4=>4 5=>4 6=>4 7=>4 8=>8 
	 //Эта функция нужна для увеличения дискретности
	 //8             __________  График похож на всеувеличивающиеся ступени
	 //7            |            $Osn=2
	 //6            | 
	 //5	        | 
	 //4	  ______| 
	 //3	 | 	
	 //2   __|
	 //1 _|
	 //   1  2   3  4  5  6  7
		if($count==0||$count==1)
			return $count;
		$temp=$count;
		$count=floor(pow($Osn,floor(log($count,$Osn))));
		if($count>$temp||$count==0)
			$count=$temp;
		return $count;
	}
	static function myRand($key='def'){return HTracer::Rand($key);}
	static function Rand($key='def',$min=false,$max=false)
	{//Рандом не мигающий при F5
	//зависит от страницы и числа вызовов функции
	//$key нужен, чтобы разные функции не зависили от числа вызовов друг-друга
		if($min===$max && $min!==false)
			return $min;
		static $Calls=Array();
		if(!isset($Calls[$key]))
			$Calls[$key]=0;
		$Calls[$key]++;////3571 - это простое число для рандомизации
		$val=abs(round(crc32($_SERVER["REQUEST_URI"].$key.$_SERVER['SERVER_NAME']) + 3571 * $Calls[$key]));
		if($min===false || $max===false)
			return $val;
		if($min>$max)
		{
			$t=$min;
			$min=$max;
			$max=$t;
		}
		return $min + $val%($max-$min+1);
	}
	static function SelectMaxKey($Arr)
	{
		//Возвращает ключ максимального элемента массива 
		$Arr=HTracer::SelectMax($Arr);
		foreach($Arr as $Key => $Val)
			return $Key;
	}
	static function SelectMax($Arr,$Count=1)//Выбирает из массива $Count больших элементов
	{
		$Selected=Array();
		for($i=0;$i<$Count;$i++)
		{
			$Max = -100000;
			$MaxKey='';
			foreach($Arr as $Key => $Val)
			{
				if(is_array($Val))
					$Val=$Val['Count'];
				if($Max<$Val)
				{
					$Max=$Val;
					$MaxKey=$Key;
				}
			}
			if($MaxKey==='')
				break;
			$Selected[$MaxKey]=$Arr[$MaxKey];
			unset($Arr[$MaxKey]);
		}
		return $Selected;
	}
//*************Работа с ключевиком***************

	
	static function isSpecQuery($Query)
	{//Фильтр на запросы, если запрос некоректен, специальный(inurl:) или матный возвращает истину	
		$res=HTracer::isSpecQuery_explain($Query);
		if(is_array($res))
			return true;
		return $res;
	}
	static function isSpecQuery_explain($Query, $explain=false)
	{//Фильтр на запросы, возвращает код ошибки
		global $_SERVER;
		$Query0=$Query;
		
		$Query=mb_convert_case($Query,MB_CASE_LOWER,'UTF-8');
		
		$Query=trim($Query);
		if($Query==='')	
			return Array(0,1,'Пустая строка');
		if(mb_strlen($Query,'utf-8')>70)	
			return Array(0,2,'Число символов в запросе больше 70');
		if($Query=='not provided'||$Query=='not set'||$Query=='(not provided)'||$Query=='(not set)')	
			return Array(0,3,'Not provided');

		$Words=explode(' ',$Query);
		foreach($Words as $Word)
			if(HTracer::strlen($Word)>25 && !strpos($Word,'-'))
				return Array(0,4,'Длина слова больше 25 букв');
			
		if(hkey_get_numbers_count($Query)>4)
			return Array(0,10,'Число циферных последовательностей больше 4');

		$F=$Query{0};//Первый символ
		if($F==','||$F==';'||$F=='.'||$F=='!'||$F=='?')//Если начинаеться или заканчиваеться недопустимым символом
			return Array(0,20,'Запрос начинаеться с недопустимых символов');

		$L=$Query{strlen($Query)-1};//Последний символ
		if($L==';'||$L==',')
			return Array(0,21,'Запрос заканчиваеться недопустимым символом');
		
		if(isset($GLOBALS['htracer_numeric_filter']) && $GLOBALS['htracer_numeric_filter']
		&& (is_numeric(trim($Query))||is_numeric(trim(str_replace(',','.',$Query)))))
			return Array('htracer_numeric_filter',22,'Запрос состоит только из числа');
			
		if(isset($GLOBALS['htracer_not_ru_filter']) && $GLOBALS['htracer_not_ru_filter'] 
		&& $Query==str_replace(array('й','ц','у','к','е','н','г','ш','щ','з','х','ъ','ф','ы','в','а','п','р','о','л','д','ж','э','я','ч','с','м','и','т','ь','б','ю'),'',$Query))
			return Array('htracer_not_ru_filter',23,'В запросе нет русских букв');

		
		$Query=str_replace('  ',' ',$Query);
		$Query=str_replace('  ',' ',$Query);
		$Query=str_replace('  ',' ',$Query);

		
		try{
			if(preg_match('//u', "абвгдеёя АБВГДЕЁЯabczABCZ,.?") && !preg_match('//u', $Query))
				return Array(0,30,'Присутствуют не допустимые в UTF-8 символы');
		} catch (Exception $e) {}
		
		if(function_exists('utf8_encode') && function_exists('utf8_decode')
		&& utf8_encode(utf8_decode('абвгдеёя АБВГДЕЁЯabczABCZ,.?')) == 'абвгдеёя АБВГДЕЁЯabczABCZ,.?'
		&& utf8_encode(utf8_decode($Query)) != $Query)
			return Array(0,31,'Кодировка не UTF-8');
		
		if(substr_count($Query,' ')>5)//слов больше 6
			return Array(0,40,'Число слов больше 6');
		if(substr_count($Query,'.')>3)//больше 3 точек	(1.2.3.8938 проходит)
			return Array(0,41,'Число точек больше 3');
		if(substr_count($Query,'?')>1)//больше 1 вопроса	
			return Array(0,42,'Число знаков вопросов больше 1');
		if(substr_count($Query,'!')>1)//больше 1 восклицательного знака	
			return Array(0,43,'Число воскл. знаков больше 1');
		if(substr_count($Query,';')>1)//больше 1 ; 	
			return Array(0,44,'Число точек с запятой больше 1');
		if(substr_count($Query,',')>1)//больше 1 запятых	
			return Array(0,45,'Число запятых больше 1');
		if(substr_count($Query,'@')>1)//больше 1 собаки	
			return Array(0,45,'Число собак больше 1');
		if(substr_count($Query,'%')>1)	
			return Array(0,46,'Число процентов больше 1');
		if(substr_count($Query,'_')>2)	
			return Array(0,47,'Число нижних подчеркиваний больше 2');
			
		if(strpos($Query,'|')!==false)
			return Array(0,50,'Обнаружен буленов оператор |');
		if(strpos($Query,'&(')!==false || strpos($Query,'& (')!==false 
		|| strpos($Query,')&')!==false || strpos($Query,') &')!==false
		|| strpos($Query,'&&'))
			return Array(0,51,'Обнаружен буленов оператор &');
		if(strpos($Query,' - ')===false && strpos($Query,' -')!==false)
			return Array(0,52,'Минус слово');
		if(strpos($Query.' ',' ! ')===false && strpos($Query,' !')!==false)
			return Array(0,53,'Обнаружен буленов оператор !');

		// Проверяем содержит ли запрос устариваевамую информацию 	
		if(strpos($Query,'погода')      || strpos($Query,'погоды')
		 ||strpos($Query,'пагода')      || strpos($Query,'пагоды')
		 ||strpos($Query,'прогноз')     || strpos($Query,'расписание')
		 ||strpos($Query,'прагноз')     || strpos($Query,'рассписание')
		 ||strpos($Query,'россписание') || strpos($Query,'росписание'))
		{
			//Погода на август 2010
			if(strpos($Query,'.') && !strpos($Query,'. '))
				return Array(0,60,'Запрос имеет временную актуальность (1)');
			if(strpos($Query,'2009')||strpos($Query,'2010')||strpos($Query,'2011')
			 ||strpos($Query,'2012')||strpos($Query,'2013')||strpos($Query,'2014'))
				return Array(0,61,'Запрос имеет временную актуальность (2)');
			if(strpos($Query,'декабр')||strpos($Query,'январ')||strpos($Query,'феврал')
			  ||strpos($Query,'март')||strpos($Query,'апрел')
			  ||strpos($Query.' ','май ')||strpos($Query.' ','мае ')||strpos($Query.' ','мая ')
			  ||strpos($Query,'июн')||strpos($Query,'июл')||strpos($Query,'август')
			  ||strpos($Query,'сентябр')||strpos($Query,'октябр')||strpos($Query,'декабр')
			  ||strpos($Query,'сентебр')||strpos($Query,'октебр'))
				return Array(0,62,'Запрос имеет временную актуальность (3)');
		}
		
		// Создаем массив плохих слов по словарю
		static $SpecWords=false;
		if(!$SpecWords)
			$SpecWords=$GLOBALS['htracer_dict']['bad_words'];
		static $AdditionalWordsAdded=false;
		if(!$AdditionalWordsAdded)
		{//Фильтры
			$AdditionalWordsAdded=true;
			if($GLOBALS['htracer_trace_sex_filter'])
				$SpecWords=array_merge($GLOBALS['htracer_dict']['sex_words'],$SpecWords);
			if($GLOBALS['htracer_trace_free_filter'])
				$SpecWords=array_merge($GLOBALS['htracer_dict']['free_words'],$SpecWords);
			if($GLOBALS['htracer_trace_download_filter'])
				$SpecWords=array_merge($GLOBALS['htracer_dict']['download_words'],$SpecWords);
			if($GLOBALS['htracer_trace_service_filter'])
				$SpecWords=array_merge($GLOBALS['htracer_dict']['service_words'],$SpecWords);
			if($GLOBALS['htracer_mats_filter'])
				$SpecWords=array_merge($GLOBALS['htracer_dict']['mats'],$SpecWords);
				
			// Запрос содержит урл	
			if($_SERVER['SERVER_NAME'] && strlen($_SERVER['SERVER_NAME'])>4)
				$SpecWords[]=$_SERVER['SERVER_NAME'];	
		}
		$Query=' '.$Query.' ';
		$count=count($SpecWords);
		if(strpos(' '.$Query.' ',' www ')!==false && trim($Query)!='www')
			return Array(0,70,'Запрос содержит www');
		if(strpos(' '.$Query.' ',' ввв ')!==false && trim($Query)!='ввв')
			return Array(0,71,'Запрос содержит ввв');
		if(strpos(' '.$Query.' ',' www.')!==false)
			return Array(0,70,'Запрос содержит www');
		if(strpos(' '.$Query.' ',' ввв.')!==false)
			return Array(0,71,'Запрос содержит ввв');
			
		$domain=str_replace('www.','',$_SERVER['SERVER_NAME']);
		if(strpos($Query,$domain)!==false)
			return Array(0,72,'Запрос содержит домен сайта');
		$domain=str_replace('.',' ',$domain);
		if(strpos($Query,$domain)!==false)
			return Array(0,72,'Запрос содержит домен сайта');
			
		//Ищем слова, которых не должно быть
		if(str_replace($SpecWords,'',' '.$Query.' ')!=' '.$Query.' ')
		{
			if($explain)
			{
				if($GLOBALS['htracer_trace_sex_filter'] && str_replace($GLOBALS['htracer_dict']['sex_words'],'',' '.$Query.' ')!=' '.$Query.' ')
					return Array('htracer_trace_sex_filter',101,'Запрос отфильтрован по Секс-Фильтру');
				if($GLOBALS['htracer_trace_free_filter'] && str_replace($GLOBALS['htracer_dict']['free_words'],'',' '.$Query.' ')!=' '.$Query.' ')
					return Array('htracer_trace_free_filter',102,'Запрос отфильтрован по Секс-Фильтру');
				if($GLOBALS['htracer_trace_download_filter'] && str_replace($GLOBALS['htracer_dict']['download_words'],'',' '.$Query.' ')!=' '.$Query.' ')
					return Array('htracer_trace_download_filter',103,'Запрос отфильтрован по софт-фильтру');
				if($GLOBALS['htracer_trace_service_filter'] && str_replace($GLOBALS['htracer_dict']['service_words'],'',' '.$Query.' ')!=' '.$Query.' ')
					return Array('htracer_trace_service_filter',104,'Запрос отфильтрован по фильтру услуг');
				if($GLOBALS['htracer_mats_filter'] && str_replace($GLOBALS['htracer_dict']['mats'],'',' '.$Query.' ')!=' '.$Query.' ')
					return Array('htracer_mats_filter',105,'Запрос отфильтрован по фильтру на маты');
			}
			return Array(0,100,'Запрос отфильтрован по присутсвию слова из словаря (специальное слово, неправильная кодировка, антираскладка)');
		}	
		$UserMinusWords=Array();
		$arr=explode(',',$GLOBALS['htracer_user_minus_words']);
		foreach($arr as $word)
		{
			$word=trim($word);
			if(!$word)
				continue;
			$sz=false;
			$ez=false;
			if($word[0]==='*')
			{
				$word[0]=' ';
				$sz=true;
			}
			if($word[strlen($word)-1]==='*')
			{
				$word[strlen($word)-1]=' ';
				$ez=true;
			}
			$word=trim($word);
			if(!$word)
				continue;
			if(!$sz)
				$word=' '.$word;
			if(!$ez)
				$word=$word.' ';
			$UserMinusWords[]=$word;
		}
		if(str_replace($UserMinusWords,'',' '.$Query.' ')!=' '.$Query.' ')
			return Array('htracer_user_minus_words',101,'Запрос отфильтрован по пользовательским минус словам');
			
		if(strpos(' '.$Query,' хер')!==false && strpos(' '.$Query,' херcон')===false)
			return Array('htracer_mats_filter',105,'Запрос отфильтрован по фильтру на маты');
		if($GLOBALS['htracer_symb_white_list'] && function_exists('mb_strlen') && function_exists('mb_substr'))
		{
			$ulng=mb_strlen($Query,'UTF-8');
			for($i=0;$i<$ulng;++$i)
				if(!isset($GLOBALS['htracer_dict']['SymbWhiteList'][mb_substr($Query, $i, 1,'UTF-8')]))
					return Array('htracer_symb_white_list',110,'Запрос отфильтрован по белому списку символов');
		}
	
		if(function_exists('htracer_api_is_correct_query') && !htracer_api_is_correct_query(trim($Query)))
			return Array(0,200,'Запрос отфильтрован функцией API htracer_api_is_correct_query');
		return false;
	}
	static function NormalizeQuery($In) 
	{//Удаляет из строки все лишние символы, переводит в нижний регистр
		
		$strtr_s='~!@#$%^&*()_+=:,.!;?-"'."\t\n\r";	
		$In=strtr($In, $strtr_s, str_repeat(' ',strlen($strtr_s)));
		$In=str_replace(Array('- ',' -',"' "," '",'ё','Ё','  '),Array(' ',' ',' ',' ','е','Е',' '),$In);
		$In=str_replace('  ',' ',$In);
		$In=str_replace('  ',' ',$In);
		$In=str_replace('  ',' ',$In);
		//апострофы и минусы могут использоваться в словах. Например, wasn't или шалтай-болтай
		$In= mb_convert_case($In,MB_CASE_LOWER,'UTF-8');
		$In= trim($In,"'- \t\n\r");
		return $In;
	}
	
	//Приводим ключ в нормальный вид
	static function Sanitarize($Str)
	{
		if(!is_string($Str)||trim($Str)=='')
			return false;

//Графематика - буквы слитно, пробелы, кавычки
		$Str=trim($Str,",: \t\n\r");
		$Str=ltrim($Str,".!?; \t\n\r");
		
		$Str=str_replace(Array('  ','""',"'"),Array(' ','"',"`"),$Str);
		$Str=str_replace('  ',' ',$Str);
		$Str=str_replace('  ',' ',$Str);

		$arr=explode('.',$Str);
		if(sizeof($arr)===2)//Исправление слитности точки и следующего слова г.Одесса=>г. одесса
		{
			$arr=explode(' ',$arr[0]);
			$lstw=$arr[sizeof($arr)-1];
			if($lstw=='им'||HTracer::strlen($lstw) ==1)
				$Str=str_replace('.','. ',$Str); //'г.одесса'=> 'г. одесса'
			elseif($lstw=='ИМ')
				$Str=str_replace('ИМ.','им. ',$Str); 
			elseif($lstw=='И')
				$Str=str_replace('Им.','им. ',$Str); 
		}

//Кавычки
		$arr=explode('"',$Str,4);
		if(sizeof($arr)!==0 && sizeof($arr)!==1)
		{
			if(sizeof($arr)==3)//Две кавычки
			{
				$arr[1]=trim($arr[1]);
				$arr[1]=trim($arr[1],"'");
				$ws=explode(' ',$arr[1]);
				if(sizeof($ws)==1||sizeof($ws)==2)
				{	
					$arr[1]='';
					foreach($ws as $cw)
					{
						$cw=mb_convert_case(mb_convert_case($cw,MB_CASE_LOWER,'UTF-8'),MB_CASE_TITLE,'UTF-8');
						if($arr[1]!=='')
							$arr[1].=' ';
						$arr[1].=$cw;
					}
				}				
				$Str=$arr[0].' «'.$arr[1].'» '.$arr[2];
			}
			else
				$Str=str_replace('"',' ',$Str);
			$Str=str_replace('  ',' ',$Str);
		}

//Теперь по базе автоисправлений проходим
		static $SanReplaceFrom=false;
		static $SanReplaceTo=false;
		if(!$SanReplaceFrom)
		{
			$Replace=Array('  '=>' ' , ' ,' =>', ',','=>', ' ,	'!'  =>'! ','?'=>'? ' , ' .' =>'. ',' ;'=>'; ',	' :' =>': ',' !'=>'! ',	' ?' =>'? ');
			$SanReplaceFrom = array_keys($Replace);
			$SanReplaceTo   = array_values($Replace);
		}
		$Str=str_replace($SanReplaceFrom,$SanReplaceTo,$Str);
		$Str=trim($Str);
		$Str=str_replace('  ',' ',$Str);
		$Str=str_replace('  ',' ',$Str);

	//Запрос введен в ВЕРХНЕМ РЕГИСТРЕ И ДЛИННЫЙ
		if(HTracer::strlen($Str)>25 && $Str==mb_convert_case($Str,MB_CASE_UPPER,'UTF-8'))
			$Str= mb_convert_case($Str,MB_CASE_LOWER,'UTF-8');
			
		$Words = explode(' ',$Str);
		$Out='';
		$count=count($Words);
		$W1=false;
		$dict=&$GLOBALS['htracer_dict'];
		$is_last_city=NULL;
		
		for($i=0;$i<$count;++$i)
		{	
			$Word=$Words[$i];
			if($Word==='')
				continue;
			$prevW='';
			$nextW='';
			if($i!=0 && isset($Words[$i-1]))
				$prevW=$Words[$i-1];		
			if(isset($Words[$i+1]))
				$nextW=$Words[$i+1];		
			$Word0=$Word;
			$Word=HTracer::NormalizeWordCase($Word,$prevW,$nextW);
			$spacer=' ';
			if($i!==0 && $prevW)
			{
				if($W1)
					$W0=$W1;
				else
					$W0=mb_convert_case($prevW,MB_CASE_LOWER,'UTF-8');
				$W1=mb_convert_case($Word,MB_CASE_LOWER,'UTF-8');
				
				//Два города подряд москва одесса в Москва—Одесса
				if(isset($dict['UcCities'][$W1]) || HTracer::have_okon($W1,$dict['cities_ends']))
				{
					if($is_last_city)
						$spacer='—';
					if($is_last_city===NULL)
						if(isset($dict['UcCities'][$W0]) || HTracer::have_okon($W0,$dict['cities_ends']))
							$spacer='—';
					$is_last_city=true;
				}
				else	
					$is_last_city=false;
			}
			$Out.=$spacer.$Word;
		}
		$Out=str_replace(
						Array('. 1','. 2','. 3','. 4','. 5','. 6','. 7','. 8','. 9','. 0'),
						Array('.1' ,'.2' ,'.3' ,'.4' ,'.5', '.6', '.7', '.8', '.9', '.0' ),$Out);
		$Out=trim($Out);
		$Words=explode(' ',$Out,2);
		if(strlen($Out))
		{
			$lstsmb=$Out{strlen($Out)-1};
			if($lstsmb!=='.' 
			&& $lstsmb!=='!' 
			&& $lstsmb!=='?' 
			&& isset($dict['ask_words'][mb_convert_case($Words[0],MB_CASE_LOWER,'UTF-8')]))
				$Out.='?';
		}
		if(function_exists('htracer_api_query_post_filter'))
			$Out=htracer_api_query_post_filter($Out);
		$Out=str_replace("А. с Пушкин","А. С. Пушкин",$Out);
		$Out=str_replace("ул льва ","ул. Льва ",$Out);
		$Out=str_replace(" льва Толстого"," Льва Толстого",$Out);
		$Out=str_replace(" лев Толстой"," Лев Толстой",' '.$Out);
		$Out=trim($Out);
		
		return $Out;
	}
	static function strlen($Word){return mb_strlen($Word,'UTF-8');}
	static function NormalizeWordCase2($Word,$Prev='',$Next='')
	{
		$ABBR=HTracer::do_Abbr($Word);
		if($ABBR!=$Word)
			return $ABBR;
		
		if(strpos($Word,'-'))
		{//киев-одесса
			$arr=explode('-',$Word);
			$tav=true;
			foreach($arr as $el)
			{
				if(!isset($GLOBALS['htracer_dict']['UcCities'][$el]) && !HTracer::have_okon($el,$GLOBALS['htracer_dict']['cities_ends']))
				{
					$tav=true;
					break;
				}
			}
			if($tav)
			{
				$Word='';
				foreach($arr as $el)
				{
					if($Word)
						$Word.='-';
					$Word.=mb_convert_case($el,MB_CASE_TITLE,'UTF-8');
				}
			}
		}
		
		if(HTracer::is_UcWord0($Word,$Prev,$Next))
		{
			if(HTracer::strlen($Word)===1 && !is_numeric($Word))
				return mb_convert_case($Word,MB_CASE_TITLE,'UTF-8').'.';//а белоусов => A. 
			return mb_convert_case($Word,MB_CASE_TITLE,'UTF-8');
		}
		else
			return $Word;
	}
	static function NormalizeWordCase($Word,$Prev='',$Next='')
	{
		HTracer::is_UcWord_Load_Arrays();
		
		if($Word=='г' && (
			isset($GLOBALS['htracer_dict']['UcCities'][$Next])
			||HTracer::have_okon($Next,$GLOBALS['htracer_dict']['cities_ends'])
			||HTracer::have_okon($Next,$GLOBALS['htracer_dict']['cities_ends_rd'])	
		))
			return 'г.';
			
		if(is_numeric($Word))
			return $Word;
		$Prev=mb_convert_case($Prev,MB_CASE_LOWER,'UTF-8');
		$Next=mb_convert_case($Next,MB_CASE_LOWER,'UTF-8');

		if($Word===mb_convert_case($Word,MB_CASE_LOWER,'UTF-8'))//одесса
			return HTracer::NormalizeWordCase2($Word,$Prev,$Next);

		if($Word===mb_convert_case($Word,MB_CASE_UPPER,'UTF-8'))//ОДЕССА
		{
			if(HTracer::strlen($Word)<5)//наверное аббревиатрура
				return $Word;	
			$Word=mb_convert_case($Word,MB_CASE_LOWER,'UTF-8');
				return  HTracer::NormalizeWordCase2($Word,$Prev,$Next);
		}
		
		if($Word===mb_convert_case(mb_convert_case($Word,MB_CASE_LOWER,'UTF-8'),MB_CASE_TITLE,'UTF-8'))
			return $Word;//Одесса
		if(mb_convert_case($Word,MB_CASE_TITLE,'UTF-8')===mb_convert_case($Word,MB_CASE_UPPER,'UTF-8'))
		{//оДЕССА 
			$Word = mb_convert_case($Word,MB_CASE_LOWER,'UTF-8');
			return  mb_convert_case($Word,MB_CASE_TITLE,'UTF-8');
		}
		//ОдЕССа
		$Word=mb_convert_case($Word,MB_CASE_LOWER,'UTF-8');
		return  HTracer::NormalizeWordCase2($Word,$Prev,$Next);
	}
	static function do_Abbr($Word)
	{
		if(isset($GLOBALS['htracer_dict']['ABBRs'][$Word]))
			return mb_convert_case($Word,MB_CASE_UPPER,'UTF-8');
		return $Word;	
	}
	static $UcWords        = false;  //Имена собственные
	static $UcWords_Ends   = false;  //окончания фамилий и отчеств, которые почти не пересекаються с другими словами
	static $UcWords_Iscl   = false;  //исключения

	static $Uc_Prev        = false;  //детерминанты окочаний по прошлому слову улица Паниковского 
	static $Uc_Next        = false;  //детерминанты окочаний по следующему слову Варшавская улица  
	static $Tezarius       = false;  //тезариус
	static $Fams_Ends      = false;  //окончание фамилий
	static $Fams_Ends_Disp = false;  //окончание по родам и падежам
	static $OtNames        = false; 	
	
	
	static function is_UcWord_Load_Arrays()
	{
		//ВСЁ ДОЛЖНО БЫТЬ В НИЖНЕМ РЕГИСТРЕ 
		$Tezarius0	  = Array();
		$Tezarius0['Names_M']    = Array('адам', 'вадим', 'евгений', 'никита', 'адольф', 'валентин', 'евдоким', 'николай', 'александр', 'валерий', 'егор', 'олег', 'алексей', 'василий', 'ефим', 'павел', 'анатолий', 'виктор', 'захар', 'петр', 'андрей', 'виталий', 'иван', 'прохор', 'антон', 'владимир', 'игорь', 'роман', 'аристарх', 'владислав', 'илья', 'руслан', 'аркадий', 'всеволод', 'иннокентий', 'семен', 'арсен', 'вячеслав', 'карл', 'сергей', 'артем', 'гавриил', 'кирилл', 'станислав', 'артур', 'геннадий', 'клим', 'степан', 'афанасий', 'георгий', 'константин', 'тарас', 'богдан', 'герман', 'лев', 'тимур', 'борис', 'глеб', 'леонид', 'федор', 'бронислав', 'григорий', 'леонтий', 'филипп', 'давид', 'макар', 'эдуард', 'даниил', 'максим', 'юрий', 'денис', 'мартин', 'яков', 'дмитрий', 'михаил');
		$Tezarius0['Names_M_RD'] = Array('адама', 'вадима', 'евгения', 'никиты', 'адольфа', 'валентина', 'евдокима', 'николая', 'александра', 'валерия', 'егора', 'олега', 'алексея', 'василия', 'ефима', 'павла', 'анатолия', 'виктора', 'захара', 'петра', 'андрея', 'виталия', 'ивана', 'прохора', 'антона', 'владимира', 'игоря', 'романа', 'аристарха', 'владислава', 'ильи', 'руслана', 'аркадия', 'всеволода', 'иннокентия', 'семена', 'арсена', 'вячеслава', 'карла', 'сергея', 'артема', 'гавриила', 'кирилла', 'станислава', 'артура', 'геннадия', 'клима', 'степана', 'афанасия', 'георгия', 'константина', 'тараса', 'богдана', 'германа', 'лева', 'тимура', 'бориса', 'глеба', 'леонида', 'федора', 'бронислава', 'григория', 'леонтия', 'филиппа', 'давида', 'макара', 'эдуарда', 'даниила', 'максима', 'юрия', 'дениса', 'мартина', 'якова', 'дмитрия', 'михаила');
		$Tezarius0['Names_F']    = Array('аврора',  'валентина',  'карина',  'полина',  'агнесса',  'валерия',  'кира',  'раиса',  'агния',  'варвара',  'клавдия',  'регина',  'ада',  'венера',  'клара',  'римма',  'алевтина',  'вера',  'кристина',  'роза',  'александра',  'вероника',  'лариса',  'роксана',  'алина',  'виктория',  'лидия',  'светлана',  'алиса',  'виолетта',  'лилия',  'серафима',  'алла',  'галина',  'любовь',  'софья',  'альбина',  'гелла',  'людмила',  'стелла',  'анастасия',  'дарья',  'майя',  'сусанна',  'ангелина',  'диана',  'маргарита',  'тамара',  'анжелика',  'ева',  'мария',  'татьяна',  'анна',  'евгения',  'марина',  'фаина',  'антонина',  'екатерина',  'марта',  'элеонора',  'анфиса',  'елена',  'милена',  'эльвира',  'астра',  'елизавета',  'надежда',  'эльза',  'белла',  'жанна',  'наталья',  'эмма',  'берта',  'зинаида',  'нелли',  'юлия',  'бета',  ',иветта',  'зоя',  'нина',  'юнона',  'инна',  'оксана',  'яна',  'ирина',  'олеся',  'ольга');
		$Tezarius0['Names_F_RD'] = Array('авроры',  'валентины',  'карины',  'полины',  'агнессы',  'валерии',  'киры',  'раисы',  'агнии',  'варвары',  'клавдии',  'регины',  'ады',  'венеры',  'клары',  'риммы',  'алевтины',  'веры',  'кристины',  'розы',  'александры',  'вероникы',  'ларисы',  'роксаны',  'алины',  'виктории',  'лидии',  'светланы',  'алисы',  'виолетты',  'лилии',  'серафимы',  'аллы',  'галины',  'любовь',  'софьи',  'альбины',  'геллы',  'людмилы',  'стеллы',  'анастасии',  'дарьи',  'маи',  'сусанны',  'ангелины',  'дианы',  'маргариты',  'тамары',  'анжеликы',  'евы',  'марии',  'татьяны',  'анны',  'евгении',  'марины',  'фаины',  'антонины',  'екатерины',  'марты',  'элеоноры',  'анфисы',  'елены',  'милены',  'эльвиры',  'астры',  'елизаветы',  'надежды',  'эльзы',  'беллы',  'жанны',  'натальи',  'эммы',  'берты',  'зинаиды',  'нелли',  'юлии',  'беты',  ',иветты',  'зои',  'нины',  'юноны',  'инны',  'оксаны',  'яны',  'ирины',  'олеси',  'ольгы');
		$Tezarius0['Otch_M']  	 = Array('адамович', 'вадимович', 'евгеньевич', 'никитович', 'адольфович', 'валентинович', 'евдокимович', 'николевич', 'александрович', 'валерьевич', 'егорович', 'олегович', 'алексеевич', 'васильевич', 'ефимович', 'павлович', 'анатольевич', 'викторович', 'захарович', 'петрович', 'андреевич', 'витальевич', 'иванович', 'прохорович', 'антонович', 'владимирович', 'игоревич', 'романович', 'аристархович', 'владиславович', 'ильич', 'русланович', 'аркадьевич', 'всеволодович', 'иннокентьевич', 'семенович', 'арсенович', 'вячеславович', 'карлович', 'сергеевич', 'артемович', 'гавриилович', 'кириллович', 'станиславович', 'артурович', 'геннадьевич', 'климович', 'степанович', 'афанасьевич', 'георгивич', 'константинович', 'тарасович', 'богданович', 'германович', 'левович', 'тимурович', 'борисович', 'глебович', 'леонидович', 'федорович', 'брониславович', 'григоревич', 'леонтьевич', 'филиппович', 'давидович', 'макарович', 'эдуардович', 'даниилович', 'максимович', 'юрьевич', 'денисович', 'мартинович', 'яковович', 'дмитриич', 'михаилович');
		$Tezarius0['Otch_M_RD']  = Array('адамовича', 'вадимовича', 'евгеньевича', 'никитовича', 'адольфовича', 'валентиновича', 'евдокимовича', 'николевича', 'александровича', 'валерьевича', 'егоровича', 'олеговича', 'алексеевича', 'васильевича', 'ефимовича', 'павловича', 'анатольевича', 'викторовича', 'захаровича', 'петровича', 'андреевича', 'витальевича', 'ивановича', 'прохоровича', 'антоновича', 'владимировича', 'игоревича', 'романовича', 'аристарховича', 'владиславовича', 'ильича', 'руслановича', 'аркадьевича', 'всеволодовича', 'иннокентьевича', 'семеновича', 'арсеновича', 'вячеславовича', 'карловича', 'сергеевича', 'артемовича', 'гаврииловича', 'кирилловича', 'станиславовича', 'артуровича', 'геннадьевича', 'климовича', 'степановича', 'афанасьевича', 'георгивича', 'константиновича', 'тарасовича', 'богдановича', 'германовича', 'левовича', 'тимуровича', 'борисовича', 'глебовича', 'леонидовича', 'федоровича', 'брониславовича', 'григоревича', 'леонтьевича', 'филипповича', 'давидовича', 'макаровича', 'эдуардовича', 'данииловича', 'максимовича', 'юрьевича', 'денисовича', 'мартиновича', 'якововича', 'дмитриича', 'михаиловича');
		$Tezarius0['Otch_F'] 	 = Array('адамовна', 'вадимовна', 'евгеньевна', 'никитовна', 'адольфовна', 'валентиновна', 'евдокимовна', 'николевна', 'александровна', 'валерьевна', 'егоровна', 'олеговна', 'алексеевна', 'васильевна', 'ефимовна', 'павловна', 'анатольевна', 'викторовна', 'захаровна', 'петровна', 'андреевна', 'витальевна', 'ивановна', 'прохоровна', 'антоновна', 'владимировна', 'игоревна', 'романовна', 'аристарховна', 'владиславовна', 'ильич', 'руслановна', 'аркадьевна', 'всеволодовна', 'иннокентьевна', 'семеновна', 'арсеновна', 'вячеславовна', 'карловна', 'сергеевна', 'артемовна', 'гаврииловна', 'кирилловна', 'станиславовна', 'артуровна', 'геннадьевна', 'климовна', 'степановна', 'афанасьевна', 'георгивна', 'константиновна', 'тарасовна', 'богдановна', 'германовна', 'левовна', 'тимуровна', 'борисовна', 'глебовна', 'леонидовна', 'федоровна', 'брониславовна', 'григоревна', 'леонтьевна', 'филипповна', 'давидовна', 'макаровна', 'эдуардовна', 'данииловна', 'максимовна', 'юрьевна', 'денисовна', 'мартиновна', 'якововна', 'дмитриич', 'михаиловна');
		$Tezarius0['Otch_F_RD']  = Array('адамовны', 'вадимовны', 'евгеньевны', 'никитовны', 'адольфовны', 'валентиновны', 'евдокимовны', 'николевны', 'александровны', 'валерьевны', 'егоровны', 'олеговны', 'алексеевны', 'васильевны', 'ефимовны', 'павловны', 'анатольевны', 'викторовны', 'захаровны', 'петровны', 'андреевны', 'витальевны', 'ивановны', 'прохоровны', 'антоновны', 'владимировны', 'игоревны', 'романовны', 'аристарховны', 'владиславовны', 'ильич', 'руслановны', 'аркадьевны', 'всеволодовны', 'иннокентьевны', 'семеновны', 'арсеновны', 'вячеславовны', 'карловны', 'сергеевны', 'артемовны', 'гаврииловны', 'кирилловны', 'станиславовны', 'артуровны', 'геннадьевны', 'климовны', 'степановны', 'афанасьевны', 'георгивны', 'константиновны', 'тарасовны', 'богдановны', 'германовны', 'левовны', 'тимуровны', 'борисовны', 'глебовны', 'леонидовны', 'федоровны', 'брониславовны', 'григоревны', 'леонтьевны', 'филипповны', 'давидовны', 'макаровны', 'эдуардовны', 'данииловны', 'максимовны', 'юрьевны', 'денисовны', 'мартиновны', 'якововны', 'дмитриич', 'михаиловны');

		$Tezarius0['M']  = array_merge_recursive($Tezarius0['Otch_M'],$Tezarius0['Names_M']);
		$Tezarius0['M_RD']  = array_merge_recursive($Tezarius0['Otch_M_RD'],$Tezarius0['Names_M_RD']);
		$Tezarius0['F']  = array_merge_recursive($Tezarius0['Otch_F'],$Tezarius0['Names_F']);
		$Tezarius0['F_RD']  = array_merge_recursive($Tezarius0['Otch_F_RD'],$Tezarius0['Names_F_RD']);
		
		$StrtOc= Array('ко','ова','овой','кой','ского','кого','ина','иной','ая','ой','ий','ия','ира','ии','оды');
		$FEndsR=Array('енко','ова','ина','ого','ева','ейна','идта','мита','ая','ной');		
		$Uc_Prev0 = Array
		(
			'республика'	=> Array('ия'),		 //республика Абхазия	
			'российская'	=> Array('едерация'),//Российская Федерация
			'село'			=> Array('ое'),		 //село Нерубайское
			'села'			=> Array('ое'),		 //села Нерубайское
			'город'			=> Array('ый'),		 //город Южный
			'города'		=> Array('ый'),		 //города Южный
			'г.'			=> Array('ый'),		 //г. Южный
			'сержанта'		=> $FEndsR, 'сержант'		=> $FEndsR,
			'летентенант'	=> $FEndsR, 'летентенанта'	=> $FEndsR,
			'капитан'		=> $FEndsR, 'капитана'		=> $FEndsR,
			'полковник'		=> $FEndsR, 'полковника'	=> $FEndsR,
			'генерал'		=> $FEndsR, 'генерала'		=> $FEndsR,
			'ген'			=> $FEndsR, 'ген.'			=> $FEndsR,
			'формула'		=> $FEndsR, 'теорема'		=> $FEndsR,
			'адмирала'		=> $FEndsR,	'адмирал'		=> $FEndsR,
			'вице-адмирала'	=> $FEndsR, 'вице-адмирал'	=> $FEndsR,
			'контр-адмирала'=> $FEndsR,	'контр-адмирал'	=> $FEndsR,
			'ул' 	  		=> $StrtOc,	'ул.'   		=> $StrtOc,
			'улицы'   		=> $StrtOc,	'улице'   		=> $StrtOc,	'улица'   		=> $StrtOc,	'улицой'   		=> $StrtOc,
			'площадь'  		=> $StrtOc,	'площадь'  		=> $StrtOc,	'набережная'	=> $StrtOc,	'набережной'	=> $StrtOc,
			'набережной'	=> $StrtOc, 'проспекте'		=> $StrtOc, 'переулок'		=> $StrtOc, 'переулка'	    => $StrtOc,
			'переулке'		=> $StrtOc, 'бульваре'		=> $StrtOc, 'бульвара'		=> $StrtOc, 'бульвар'		=> $StrtOc
		);
		HTracer::$Uc_Prev=Array();		
		foreach($Uc_Prev0 as $Key=>$Arr)
		{
			foreach($Arr as $StWord)
				HTracer::$Uc_Prev[$Key][$StWord]=strlen($StWord);
		}
		$StrtOc= Array('ая','ой','ую','ий','ого','ом');
		$Uc_Next0 = Array
		(	
			'республика'	=> Array('ская'),//если прошлое слово республика и окончание этого 'ская' то это имя собственное 
			'федерация'		=> Array('ская'),
			'улицы'   		=> $StrtOc,	'улице'   		=> $StrtOc,	'улица'   		=> $StrtOc,	'улицой'   		=> $StrtOc,
			'площадь'  		=> $StrtOc,	'площадь'  		=> $StrtOc,	'пабережная'	=> $StrtOc,	'пабережной'	=> $StrtOc,
			'пабережной'	=> $StrtOc, 'проспекте'		=> $StrtOc, 'переулок'		=> $StrtOc, 'переулка'	    => $StrtOc,
			'переулке'		=> $StrtOc, 'бульваре'		=> $StrtOc, 'бульвара'		=> $StrtOc, 'бульвар'		=> $StrtOc
		);
		HTracer::$Uc_Next=Array();		
		foreach($Uc_Next0 as $Key=>$Arr)
		{
			foreach($Arr as $StWord)
				HTracer::$Uc_Next[$Key][$StWord]=strlen($StWord);
		}
			
		HTracer::$UcWords=$GLOBALS['htracer_dict']['upcase_words'];
		HTracer::$UcWords_Iscl=$GLOBALS['htracer_dict']['no_upcase_words'];
	
		$Fams_Ends0=Array('ов','ин','ий','ев','ейн','идт','мит','енко','ова','ина','ого','ева','ейна','идта','мита','ая','овой','иной','евой');
		HTracer::$Fams_Ends=Array();
		foreach($Fams_Ends0 as $StWord)
			HTracer::$Fams_Ends[$StWord]=strlen($StWord);
		
		$Fams_Ends_Disp0 = Array();  
		$Fams_Ends_Disp0['M']	 = Array('ов','ин','ский','ев','ейн','идт','мит','енко','берг');
		$Fams_Ends_Disp0['F']	 = Array('ова','ина','ская','ева','ейна','идта','мит','енко','берг');
		$Fams_Ends_Disp0['M_RD'] = Array('ова','ина','ский','ева','ейна','идта','мита','енко','берга');
		$Fams_Ends_Disp0['F_RD'] = Array('овой','иной','ской','ева','ейн','идт','мит','енко','берга');
		HTracer::$Fams_Ends_Disp= Array();  
		foreach($Fams_Ends_Disp0 as $Key=>$Arr)
			foreach($Arr as $StWord)
				HTracer::$Fams_Ends_Disp[$Key][$StWord]=strlen($StWord);
		
		HTracer::$OtNames=Array();
		HTracer::$Tezarius=Array();
		foreach($Tezarius0 as $Key=>$Arr)
			foreach($Arr as $StWord)
				HTracer::$Tezarius[$Key][$StWord]=1;
		
		$UcWords_Ends0=$GLOBALS['htracer_dict']['upcase_ends'];
		HTracer::$UcWords_Ends = Array();
		foreach($UcWords_Ends0 as $StWord)
			HTracer::$UcWords_Ends[$StWord]=strlen($StWord);
		
		$GLOBALS['htracer_dict']['cities_ends']=Array();
		foreach($GLOBALS['htracer_dict']['cities_ends0'] as $StWord)
			$GLOBALS['htracer_dict']['cities_ends'][$StWord]=mb_strlen($StWord,'cp1251');
			
		$GLOBALS['htracer_dict']['cities_ends_rd']=Array();
		foreach($GLOBALS['htracer_dict']['cities_ends_rd0'] as $StWord)
			$GLOBALS['htracer_dict']['cities_ends_rd'][$StWord]=mb_strlen($StWord,'cp1251');	
			
	}
	static function have_okon($Word,&$Okons,$len=false)
	{
		if($len===false)
			$len=mb_strlen($Word,'cp1251');
		if($len<4)
			return false;
		if(!$Okons || !is_Array($Okons))	
			return false;
		//echo $len;
		foreach($Okons as $End => $len2)
		{
			//if($len<=$len2)
			//	continue;
			//echo '<hr />'.$End.'='.$len2.'<br />';
			for($i=1;$i<=$len2;++$i)
			{
				//echo $i.'::'.ord($Word{$len-$i}).'<>'.ord($End{$len2-$i}).'<br />';
				if($i===$len2)
					return true;
				elseif(!isset($Word{$len-$i}) || !isset($End{$len2-$i}) || $Word{$len-$i}!==$End{$len2-$i})
					break;
			}
		}
		return false;
	}
	static function is_UcWord0($Word,$Prev='',$Next='')
	{ 
		
		global $UcWords0_Iscl;
		if(is_numeric($Word))
			return false;
		if(!HTracer::$UcWords)
			HTracer::is_UcWord_Load_Arrays();
		if(isset(HTracer::$UcWords_Iscl[$Word]))
			return false;
		$len=strlen($Word);
		$ulen=HTracer::strlen($Word);
		$up_len=HTracer::strlen($Prev);
		$un_len=HTracer::strlen($Next);
		
		
		
		if($ulen===2 && $Word{1}==='.' && $Word{0}!=='.' && $Word{0}!==',' && $Prev!=='в')
			return true;
		
		
		if($ulen===1)//а. в. белоусов
		{
			if(($Word==='и'||$Word==='в'||$Word==='с'||$Word==='к'||$Word==='о'||$Word==='у')
			   && $up_len && $un_len!==1)
				return false;
			elseif(!isset(HTracer::$UcWords_Iscl[$Prev]) && !isset(HTracer::$UcWords_Iscl[$Next]))
			{
				if($up_len===1 && ($Prev!=='в'||$Word!=='г') && !is_numeric($Prev)) 
					return true;
				if(($up_len===2 && $Prev[strlen($Prev)-1]==='.' && $Prev[0]!=='.'&& $Prev[0]!==','))
					return true;
				if($un_len===1 && ($Word!=='в'||$Next!=='г') && !is_numeric($Next))
					return true;
				if($un_len===2 && $Next[strlen($Next)-1]==='.' && $Next[0]!=='.' && $Next[0]!==',')
					return true;
				if(HTracer::have_okon($Next,HTracer::$Fams_Ends))
					return true;
				if(HTracer::have_okon($Prev,HTracer::$Fams_Ends) && $Prev!=='музеев' && $Word!=='а' && $Word!=='в' && $Word!=='и'&& $Word!=='к' && $Word!=='с')
					return true;
			}
		}
		if($ulen<3)
			return false;
		
		$Word=trim($Word,',.!?');
		$len=strlen($Word);
		if(isset(HTracer::$UcWords[$Word]))
			return true;

		if($Word!=='автобусов' && !isset(HTracer::$UcWords_Iscl[$Word]) && $Word!=='поездов' && HTracer::have_okon($Word,HTracer::$UcWords_Ends))
			return true; 
		//echo ' 5 ';	
			
		if((($up_len===1 && $Prev!=='.' && $Prev!==',')
		 || ($up_len===2 && $Prev[1]==='.')) 
		 && HTracer::have_okon($Word,HTracer::$Fams_Ends,$len))
			return true;
		if(($un_len===2 && $Next[strlen($Next)-1]==='.') && HTracer::have_okon($Word,HTracer::$Fams_Ends,$len))
			return true;
		$Next=rtrim($Next,' ,');	
		$Prev=ltrim($Prev,' ,');	
		if(isset(HTracer::$Uc_Next[$Next]) && HTracer::have_okon($Word,HTracer::$Uc_Next[$Next],$len))
			return true;
		if(isset(HTracer::$Uc_Prev[$Prev]) && HTracer::have_okon($Word,HTracer::$Uc_Prev[$Prev],$len))
			return true;
		foreach(HTracer::$Fams_Ends_Disp as $Key=>$Arr)	
		{
			if((isset(HTracer::$Tezarius[$Key][$Prev]) 		 && HTracer::have_okon($Word,HTracer::$Fams_Ends_Disp[$Key],$len))//андрей валентинович белоусов или андрей валентинович
			 ||(isset(HTracer::$Tezarius[$Key]['Names_'.$Next]) && HTracer::have_okon($Word,HTracer::$Fams_Ends_Disp[$Key],$len)))//белоусов андрей валентинович
				return true;
		}		
		return false;
	}

//Поисковые подсказки
	static function get_queries_like($Like=false,$Count=7,$Highlight=true,$NotThis=true)
	{
		$Queries=HTracer::select_queries_like($Like,$Count,$Highlight,$NotThis);
		$Out='';
		$SN=$_SERVER['SERVER_NAME'];
		foreach($Queries as $Query=>$URL)
			$Out.= "<a href='http://{$_SERVER['SERVER_NAME']}$URL'>$Query</a> ";
		return $Out;
	}
	static function select_queries_like($Like=false,$Count=7,$Highlight=true,$NotThis=true)
	{
		if(strtolower($GLOBALS['htracer_encoding'])!='utf-8')
			$Like=mb_convert_encoding($Like, 'utf-8',$GLOBALS['htracer_encoding']);
		$NotURL='';
		if($NotThis)
		{
			$S1=mysql_real_escape_string($_SERVER["REQUEST_URI"]);
			if($_SERVER["REQUEST_URI"]!=getenv("REQUEST_URI"))
			{
				$S2=mysql_real_escape_string(getenv("REQUEST_URI"));
				$NotURL=" AND t2.`URL`!='$S1' AND AND t2.`URL`!='$S2'";
			}
			else
				$NotURL=" AND t2.`URL`!='$S1'";
		}	
		$Like0=$Like;
		$Like=str_replace("\\","\\\\",$Like);
		$Like=str_replace("%","\\%",$Like);
		$Like=str_replace("_","\\_",$Like);
		$Like=mysql_real_escape_string($Like);

		$Count2=$Count*5;
		$table_prefix=HTracer::GetTablePrefix();
		$LikeCound=' 1=1 ';
		if($Like0)
			$LikeCound=" t1.`In` LIKE '$Like%' ";
		$query="		
					SELECT t1.`Out` as Q, t2.`URL` as U FROM `{$table_prefix}htracer_queries` as t1 
					JOIN `{$table_prefix}htracer_pages` as t2 on t1.`URL_CS`=t2.`URL_CS` 
					WHERE $LikeCound $NotURL
					ORDER By t1.`OutEva` desc
					LIMIT $Count2";
		$res=htracer_mysql_query($query);
		$n=mysql_num_rows($res);
		$Out=Array();
		$IN=Array();
		for($i=0;$i<$n;$i++)
		{
			$q=mysql_fetch_assoc($res);
			if(!isset($Out[$q['Q']]))
				$Out[$q['Q']]=$q['U'];
			$IN[$q['U']]=mysql_real_escape_string($q['U']);
		}
		$Count2=($Count - count($Out)) * 3;
		//$Count2=4;
		if($Count2>0)
		{
			if(count($IN))
			{
				$IN="'".JOIN("','",$IN)."'";
				$IN = " AND t2.`URL` NOT IN ($IN)";
			}
			else
				$IN='';
			$LikeCound=' 1=1 ';
			if($Like0)
				$LikeCound=" t1.`In` LIKE '%$Like%' ";
			$query="		
					SELECT t1.`Out` as Q, t2.`URL` as U FROM `{$table_prefix}htracer_queries` as t1 
					JOIN `{$table_prefix}htracer_pages` as t2 on t1.`URL_CS`=t2.`URL_CS` 
					WHERE ( $LikeCound $IN $NotURL )
					ORDER By t1.`OutEva` desc
					LIMIT $Count2";
			
			$res=htracer_mysql_query($query);
			$n=mysql_num_rows($res);
			//echo $n;
			for($i=0;$i<$n;$i++)
			{
				$q=mysql_fetch_assoc($res);
				//print_r($q);
				if(!isset($Out[$q['Q']]))
					$Out[$q['Q']]=$q['U'];
			}
		}
		$Out2=Array();
		$i=0;
		foreach ($Out as $Key=>$U)
		{
			if($Highlight && $Like0)
				$Key=str_replace($Like0,'<b>'.$Like0.'</b>',$Key);
			if(strtolower($GLOBALS['htracer_encoding'])!='utf-8')
				$Key=mb_convert_encoding($Key, $GLOBALS['htracer_encoding'], 'utf-8');
			$Out2[$Key]=$U;
			$i++;
			if($i>=$Count)
				break;
		}
		return $Out2;
	}
//Контекстные ссылки
	static function FormCLinksCore_Old($Pages=300)//БД для растановки контекстных ссылок
	{//Старый вариант используеться когда MySQL отключено
		$Pages=intval($Pages);
		if(!$Pages)
			$Pages=300;
		$CashCS=(string)$Pages;	
		$SW=$GLOBALS['htracer_site_stop_words'];
		if(!is_Array($SW))
			$SW=explode(',',$SW);
		foreach ($SW as $K=>$W)
			$CashCS.=trim($W).$K.',';
		$Res=htracer_read_cash_file('FormCLinksCore',$CashCS,false,false);
		if($Res)
			return $Res;
									
		$Pages=HTracer::SelectMaxPages($Pages);
		$Pages=HTracer::SelectMaxQueries($Pages,false,false,false);
		
		$Queries0=Array();
		foreach($Pages as $URL =>$Data)
		{
			foreach($Data['Q'] as $Q=>$Count)
			{
				$Q=str_replace(chr(209).chr(63),'ш',$Q);
				if(!isset($Queries0[$Q]))
					$Queries0[$Q]=Array();
				$Queries0[$Q][$URL]=$Count;
			}
		}
		$Queries=Array();
		foreach($Queries0 as $Q =>$Data)
		{
			$MaxCount=-1;
			foreach($Data as $URL => $Count)
			{
				$Count=intval($Count);
				if($MaxCount<$Count)
				{
					$MaxCount=$Count;
					$MaxURL=$URL;
				}
				$passages=hkey_split_passages($Q, 'UTF-8');
				if(sizeof($passages)!=1)
					continue;
				$passage=$passages[0];
				$Q=mb_ucfirst(sanitarize_keyword($Q),'UTF-8');
				$fWords=sizeof($passage);
				$CS=0;
				$Words=0;
				$sCS=0;
				$fCS=0;
				$wn=1;
				$isAllSiteStop=true;
				$SiteStopWords=0;
				foreach($passage as $Word)
				{
					if(!$Word->isSiteStop)
						$isAllSiteStop=false;
					else
						$SiteStopWords++;
				}
				foreach($passage as $Word)
				{
					if(!$Word->isStop)
					{
						if(!$Word->isSiteStop||$isAllSiteStop)
							$CS+=$Word->CS;
						$fCS+=$Word->CS;			
						$sCS+=$Word->CS  * $wn * 773;
						$Words++;
					}
					$wn++;
				}
				$WordsEva=$Words-$SiteStopWords/2;
				if($WordsEva<0)
					$WordsEva=0;
				$Eva=round($WordsEva*$WordsEva + $Count);
				if($Words===0 ||$passage[0]->isStop||$passage[$fWords-1]->isStop)
					continue;
				$CS2=(string)$CS;
				if(!isset($Queries[$CS2])||$Queries[$CS2]['Eva']<$Eva)
				{
					$Queries[$CS2]=Array(
						'URL'=>$MaxURL,
						'Count'=>$MaxCount,
						'Words'=>$Words,
						'Eva'=>$Eva,
						'CS'=>$CS,
						'sCS'=>$sCS,
						'fCS'=>$fCS,
						'Q'=>$Q,
						'fWords'=>$fWords
						);
				}
			}
		}		
//		print_r($Queries);
		htracer_write_cash_file($Queries,'FormCLinksCore',$CashCS,false,false);
		return $Queries;
	}
	
	static function FormCLinksCore($Pages=300)//Для контекстных ссылок
	{
		if(!$GLOBALS['htracer_mysql'])
			return HTracer::FormCLinksCore_Old($Pages);
		$CashCS='none';	
		$SW=$GLOBALS['htracer_site_stop_words'];
		if(!is_Array($SW))
			$SW=explode(',',$SW);
		foreach ($SW as $K=>$W)
			$CashCS.=trim($W).$K.',';
		$CashCS.=$GLOBALS['htracer_context_links_b'].$GLOBALS['htracer_clcore_size'];
		$Res=htracer_read_cash_file('FormCLinksCore',$CashCS,false,false);
		if($Res)
		{
			if(!$GLOBALS['htracer_context_links_b'])
				return HTracer::DelCurPageLinks($Res);
			else
				return $Res;
		}
		$table_prefix=HTracer::GetTablePrefix();	
		$Limit=1000;
		if(isset($GLOBALS['htracer_clcore_size'])
		&& $GLOBALS['htracer_clcore_size']
		&& is_numeric(trim($GLOBALS['htracer_clcore_size']))
		&& intval(trim($GLOBALS['htracer_clcore_size']))>1)
			$Limit=trim($GLOBALS['htracer_clcore_size']);
		
		$Qs=htracer_mysql_query("
				SELECT t1.`In` as QIn, t1.`Out` as QOut, t1.`OutEva` as N, t2.URL as `URL` 
					  FROM `{$table_prefix}htracer_queries` as t1
				INNER JOIN `{$table_prefix}htracer_pages`   as t2 
					  ON t1.URL_CS=t2.URL_CS  
				Where t1.`Status`=1 AND t1.`ShowInCLinks`=1 
				Order by `OutEva` desc LIMIT $Limit");
		$N=mysql_num_rows($Qs);	
		$Queries0=Array();
		$SanCash=Array();
		
		for($i=0;$i<$N;$i++)
		{
			$cur=mysql_fetch_assoc($Qs);
			$Q=$cur['QIn'];
			if(!isset($Queries0[$Q]))
				$Queries0[$Q]=Array();
			$Queries0[$Q][$cur['URL']]=$cur['N'];
			$SanCash[$Q]=$cur['QOut'];
		}

		
		$Queries=Array();
		foreach($Queries0 as $Q =>$Data)
		{
			$MaxCount=-1;
			foreach($Data as $URL => $Count)
			{
				$Count=intval($Count);
				if($MaxCount<$Count)
				{
					$MaxCount=$Count;
					$MaxURL=$URL;
				}
				$passages=hkey_split_passages($Q, 'UTF-8');
				if(sizeof($passages)!=1 ||!isset($SanCash[$Q]))
					continue;
				$passage=$passages[0];
				$Q=mb_ucfirst($SanCash[$Q],'UTF-8');
				$fWords=sizeof($passage);
				$CS=0;
				$Words=0;
				$sCS=0;
				$fCS=0;
				$wn=1;
				$isAllSiteStop=true;
				$SiteStopWords=0;
				$HaveNormalWord=false;
				foreach($passage as $Word)
				{
					if(!$Word->isSiteStop)
						$isAllSiteStop=false;
					else
						$SiteStopWords++;
					if(!$Word->isStop && !htracer_isPopWord($Word->Str))
						$HaveNormalWord=true;
				}
				if(!$HaveNormalWord)
					continue;//все слова либо стоповые либо не несут смысла типа бесплатно 
				foreach($passage as $Word)
				{
					if(!$Word->isStop)
					{
						if(!$Word->isSiteStop||$isAllSiteStop)
							$CS+=$Word->CS;
						$fCS+=$Word->CS;			
						$sCS+=$Word->CS  * $wn * 773;
						$Words++;
					}
					$wn++;
				}
				$WordsEva=$Words-$SiteStopWords/2;
				if($WordsEva<0)
					$WordsEva=0;
				$Eva=round($WordsEva*$WordsEva + $Count);
				if($Words===0 ||$passage[0]->isStop||$passage[$fWords-1]->isStop)
					continue;
				$CS2=(string)$CS;
				if(!isset($Queries[$CS2])||$Queries[$CS2]['Eva']<$Eva)
				{
					$Queries[$CS2]=Array(
						'URL'=>$MaxURL,
						'Count'=>$MaxCount,
						'Words'=>$Words,
						'Eva'=>$Eva,
						'CS'=>$CS,
						'sCS'=>$sCS,
						'fCS'=>$fCS,
						'Q'=>$Q,
						'fWords'=>$fWords
						);
				}
			}
		}		
//		print_r($Queries);
		htracer_write_cash_file($Queries,'FormCLinksCore',$CashCS,false,false);
		if(!$GLOBALS['htracer_context_links_b'])
			$Queries=HTracer::DelCurPageLinks($Queries);
		return $Queries;
	}
	static function DelDomain($URL)
	{
		if(strpos($URL,'http://')===0)
			$URL=str_replace('http://'.$_SERVER["SERVER_NAME"],'',$URL);
		if(!isset($URL{0}) || $URL{0}!='/')
			$URL='/'.$URL;
		return $URL;
	}
	
	static function isCurPage($URL)
	{//Если возможно, что URL равен текущему возвращает true
		$URL=HTracer::DelDomain($URL);
		static $FArray= Array('REQUEST_URI','REDIRECT_URL','REQUEST_URI');
		foreach($FArray as $Key)
		{
			if(!isset($_SERVER[$Key]))
				continue;
			$URL0=HTracer::DelDomain($_SERVER[$Key]);
			if($URL0==$URL||$URL0==$URL.'/'||$URL0.'/'==$URL)
				return true;
		}
		if($URL==getenv("REQUEST_URI"))
			return true;
		return false;
	}
	/*
	static function isCurPage($URL)
	{
		return ($URL==$_SERVER["REQUEST_URI"]
			|| (isset($_SERVER["REDIRECT_URL"]) && $_SERVER["REDIRECT_URL"] && $URL==$_SERVER["REDIRECT_URL"])
 			|| (isset($_SERVER["REDIRECT_URI"]) && $_SERVER["REDIRECT_URL"] && $URL==$_SERVER["REDIRECT_URI"])
			|| $URL==getenv("REQUEST_URI"));
	}*/
	static function DelCurPageLinks($Queries)
	{
		foreach($Queries as $K => $Q)
		{
			if(HTracer::isCurPage($Q['URL']))
				unset($Queries[$K]);
		}
		return $Queries;
	}
	
//Работа с БД на низком уровне
	static function GetTablePrefix()
	{
		$table_prefix=$GLOBALS['htracer_mysql_prefix'];
		if(!$table_prefix && isset($GLOBALS['htracer_is_wp_plugin']))
			$table_prefix=$GLOBALS['table_prefix'];
		return ($table_prefix);
	}
	static function GetCurentURL()
	{	
		$URL=false;
		if(function_exists('getenv'))
			$URL=getenv('REQUEST_URI');
		if(!$URL)
			$URL=$_SERVER["REQUEST_URI"];
		return $URL;
	}
	static function P404_process()
	{
		if(!isset($GLOBALS['htracer_404_plugin'])||!$GLOBALS['htracer_404_plugin'])
			return;
		
		if(isset($GLOBALS['htracer_404']) && $GLOBALS['htracer_404'] && HTracer::HaveResponseCode('404'))
			HTracer::DeletePage(false,true);
		if(isset($GLOBALS['htracer_301']) && $GLOBALS['htracer_301'] && HTracer::HaveResponseCode('301'))
		{	
			$headers=headers_list();
			$URL=false;
			foreach($headers as $header)
			{
				if(strpos($header,"Location:")!==false||strpos($header,"location:")!==false 
				|| (function_exists('stripos') && stripos($header,"Location:")!==false))
				{
					$URL=explode(':',$header);
					$URL=HTracer::FixURL(trim($URL[1]));
				}
			}
			//$URL='/test3.php'; 
			if($URL)
				HTracer::ChangePageURL(false,$URL);
		}	
	}
	
	static function DeletePage($URL=false, $OnlyKeys=false)
	{
		if($URL===false)
			$URL=HTracer::GetCurentURL();
		$Prefix=HTracer::GetTablePrefix();	
		$CS=MD5($URL);

		//Удаляем ключи
		htracer_mysql_query("
				DELETE FROM `{$Prefix}htracer_queries`
					WHERE `URL_CS`='$CS'
		",'DeletePageData_1');

		if($OnlyKeys)
		{
			//Eva Weigth of page (summ keys weigth) || вес страницы (суммарный вес ключей)	Eva15
			//FirstKey Source of key with maximum weigth || содержимое максимального кея этой страницы	SecondKey
			htracer_mysql_query("
				UPDATE `{$Prefix}htracer_pages`
					SET 
						`Eva`=0, 
						`Eva15`=0
					WHERE `URL_CS`='$CS'
					LIMIT 1
			",'DeletePageData_2');			

			// Если Первый и второй ключ страницы заданы не пользователем, то удаляем их
			$res=htracer_mysql_query("
				SELECT * FROM `{$Prefix}htracer_pages`
					WHERE `URL_CS`='$CS'
					LIMIT 1
			",'DeletePageData_3');			
			
			$res=mysql_fetch_array($res);
			$res=$res['isFirstKeysSetByUser'];//в базе ошибка данные верны с точностью до наоборот
			if($res)
			{			
				htracer_mysql_query("
					UPDATE `{$Prefix}htracer_pages`
						SET 
							`FirstKey`='', 
							`SecondKey`=''
						WHERE `URL_CS`='$CS'
					LIMIT 1
				",'DeletePageData_4');	
			}
			return;
		}
		//Удаляем страницу
		htracer_mysql_query("
			DELETE FROM `{$Prefix}htracer_pages`
				WHERE `URL_CS`='$CS'
				LIMIT 1
		",'DeletePageData_5');

		//Удаляем USP
		if(isset($GLOBALS['htracer_usp']) && $GLOBALS['htracer_usp'])
		{
			htracer_mysql_query("
				DELETE FROM `{$Prefix}htracer_page_meta`
					WHERE `URL_CS`='$CS'
			",'DeletePageData_6');
		}		
	}
	static function ChangePageURL($URL=false,$New)
	{
		if($URL===false)
			$URL=HTracer::GetCurentURL();
		$Prefix=HTracer::GetTablePrefix();	
		$CS=MD5($URL);
		$newCS=MD5($New);
		
		$New=mysql_real_escape_string($New);
		
		//echo '"'.$URL.'"'.'=>'.$New;
		//echo  "<br />";
		//echo $CS.'=>'.$newCS;
		//echo  "<br />";

		if(true)//!mysql_num_rows(htracer_mysql_query("SELECT * FROM `{$Prefix}htracer_pages` WHERE `URL_CS`='$newCS'")))
		{
			//echo 'x2';
			htracer_mysql_query("
				UPDATE `{$Prefix}htracer_pages`
					SET 
						`URL_CS`='$newCS',
						`URL`='$New'
					WHERE `URL_CS`='$CS'
				",'ChangePageURL_1',false);
			htracer_mysql_query("
				UPDATE `{$Prefix}htracer_queries`
					SET `URL_CS`='$newCS'
					WHERE `URL_CS`='$CS'
				",'ChangePageURL_2',false);			

			if(isset($GLOBALS['htracer_usp']) && $GLOBALS['htracer_usp'])
			{
				htracer_mysql_query("
					UPDATE `{$Prefix}htracer_page_meta`
						SET `URL_CS`='$newCS'
					WHERE `URL_CS`='$CS'	
					",'ChangePageURL_3',false);
			}
		}
	}
	static function UpdateTableData($TableName,$Data)
	{
		if(strtolower($TableName)=='pages')
			$TableName='htracer_pages';
		elseif(strtolower($TableName)=='keys'||strtolower($TableName)=='queries')
			$TableName='htracer_queries';
		$Prefix=HTracer::GetTablePrefix();	
		$TableName=$Prefix.$TableName;
		
		foreach($Data as $ID => $Params)
		{
			$ID=mysql_real_escape_string($ID);
			if(isset($Params['wasURL']))
			{
				if(isset($Params['URL']) && $Params['URL']=='')
				{
					HTracer::DeletePage($Params['wasURL'],false);
					continue;
				}
				elseif(isset($Params['URL']) && $Params['URL']!=$Params['wasURL'])
					HTracer::ChangePageURL($Params['wasURL'],$Params['URL']);
				unset($Params['wasURL']);
			}
			
			if(isset($Params['URL']))
				$Params['URL_CS']=MD5($Params['URL']);
				
			if(isset($Params['Don']))
				$Params['DON_CS']=MD5($Params['Don']);	
				
			if(!count($Params)||!is_numeric($ID))
				continue;
			$Set=Array();
			foreach($Params as $Param => $Value)
			{
				if(stripos($Param,'`')!==false)
					continue;
				if($Value===false)
					$Value=0;
				$Set[]="`$Param` = '".mysql_real_escape_string($Value)."'";
			}
			$Set=JOIN(',',$Set);
			htracer_mysql_query("
				UPDATE `$TableName`
				SET $Set
				WHERE `ID`='$ID'
				LIMIT 1
			",'UpdateTableData_100');
		}
	}
	static function SynhrFilters()
	{
		set_time_limit(60 * 15);
		$prefix=HTracer::GetTablePrefix();
		$Version=$GLOBALS['htracer_curent_version_id'];
		$Res=htracer_mysql_query("SELECT * FROM `{$prefix}htracer_queries` WHERE `Version`!=10000",'SynhrFilters_1');
		$URLs_CS=Array();
		$count=0;
		while ($Key = mysql_fetch_assoc($Res))
		{
			$Out=sanitarize_keyword($Key['In']);
			$Out=mysql_real_escape_string($Out);
			$Status=intval(!HTracer::isSpecQuery($Key['In']));
			if($Out!=$Key['Out']||$Status!=$Key['Status'])
			{
				$URLs_CS[$Key['URL_CS']]=$Key['URL_CS'];
				htracer_mysql_query("
					UPDATE `{$prefix}htracer_queries` 
					SET 
						`Out`='$Out',
						`Status`='$Status',
						`Version`='$Version'
					WHERE `ID`={$Key['ID']}"
				,'SynhrFilters_2');
				$count++;
			}
		}
		foreach ($URLs_CS as $URL_CS)
			HTracer::RefreshPage($URL_CS,true,true);
		return $count;
	}
	static function AddUserKeys($Data0)
	{
		$Data=Array();
		$In=Array();	

		foreach($Data0 as $Key)
		{
			if(!isset($Key['In']))
				$Key['In']=false;
			else
				$Key['In']=trim($Key['In']);
				
			if(!isset($Key['Out']))
				$Key['Out']=false;
			else
				$Key['Out']=trim($Key['Out']);
				
			if((!$Key['In']  && !$Key['Out'])
			  ||!$Key['URL'] || !isset($Key['URL']))
				continue;
			
			if(!$Key['In'])
				$Key['In']=strtolower($Key['Out']);
			if(!$Key['Out'])
				$Key['Out']=HTracer::Sanitarize($Key['In']);
			if(!isset($Key['Eva']))
				$Key['Eva']=1;
			if(!isset($Key['OutEva']))
				$Key['OutEva']=$Key['Eva'];
			if(!isset($Key['Status']))
				$Key['Status']=1;
			if(!isset($Key['ShowInCLinks']))
				$Key['ShowInCLinks']=true;
			if(!isset($Key['Version']))
				$Key['Version']=10000;
			if(!$Key['Status'])
				$Key['Status']=0;
			if(!$Key['ShowInCLinks'])
				$Key['ShowInCLinks']=0;
			$Key['In']=trim($Key['In']);
			$Key['Out']=trim($Key['Out']);
			$Key['URL']=trim($Key['URL']);
			$Key['URL_CS']=MD5($Key['URL']);
			
			$Data[mb_strtolower($Key['In'].'::'.$Key['URL_CS'],'utf-8')]=$Key;
			$WHERE[]="(`In` = '".mysql_real_escape_string($Key['In'])."' 
					AND `URL_CS` = '".mysql_real_escape_string($Key['URL_CS'])."')";
		}
		if(!count($Data))
			return 0;
		$WHERE=JOIN(' OR ',$WHERE);
		$TableName='htracer_queries';
		$TableName=HTracer::GetTablePrefix().$TableName;
		$res=htracer_mysql_query("SELECT `In`,`URL_CS` FROM `$TableName` WHERE ($WHERE)");
		while ($Key = mysql_fetch_assoc($res))
			unset($Data[mb_strtolower($Key['In'].'::'.$Key['URL_CS'],'utf-8')]);
		if(!count($Data))
			return 0;
		$Params=Array('In', 'Out', 'Eva', 'URL_CS','OutEva','Version','Status','ShowInCLinks');
		$Values=Array();
		
		$Data=array_reverse($Data);
		foreach($Data as $Key)
		{
			$CValues=Array();
			foreach($Params as $Param)
				$CValues[]="'".mysql_real_escape_string($Key[$Param])."'";
			$CValues=JOIN(',',$CValues);
			$Values[]='('.$CValues.')';
		}
		$Values=JOIN(',',$Values);

		htracer_mysql_query
		("
			INSERT INTO `$TableName`
			(`".JOIN('`, `',$Params)."`)
			VALUES  $Values
			ON DUPLICATE KEY UPDATE `ID`=`ID`
		");
		
		return count($Data);
	}
	static function SelectMaxPages($Count,$urlstart=false)
	{		
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		
		$table_prefix=HTracer::GetTablePrefix();
		$tablename3=$table_prefix."htracer_pages";
		$Out=Array();
		if($GLOBALS['htracer_mysql'])
		{
			if(!$urlstart)
				$query="SELECT *, Eva15 as Num FROM `$tablename3` order by `Eva15` DESC LIMIT $Count";
			else
			{
				$urlstart=HTracer::correct_urlstart($urlstart);
				$urlstart=str_replace("\\","\\\\",$urlstart);
				$urlstart=str_replace("%","\\%",$urlstart);
				$urlstart=str_replace("_","\\_",$urlstart);
				$urlstart.='%';
				$urlstart=mysql_real_escape_string($urlstart);
				$query="SELECT *, Eva15 as Num FROM 
						`$tablename3` 
						WHERE `URL` LIKE '$urlstart' 
						order by `Eva15` DESC LIMIT $Count";
				//echo $query; 
			}
			$res=htracer_mysql_query($query,'SelectMaxPages');//mysql_query($query) or die('203 '.mysql_error().'<br>'.$query);
			if(!$res)
				$N=0;
			else
				$N=mysql_num_rows($res);
			for($i=0;$i<$N;++$i)	
			{
				$cur= mysql_fetch_array($res);
				$Out[$cur['URL']]=intval($cur['Num']);
			}
		}
		else
		{	
			for($i=0;$i<128;$i++)	
			{
				$filename=dirname(__FILE__).'/query/all'.$i.'.txt';
				if(file_exists($filename))
				{
					$strs=file($filename);
					foreach($strs as $str)
					{
						$arr=explode('|#;#|',$str);
						if(sizeof($arr)!=3)
							continue;
						$num=intval(trim($arr[0]));
						$URL=trim($arr[2]);
						$Out[$URL]=$num;
					}
				}
			}
			arsort($Out,SORT_NUMERIC);
			//HPROTECTION
			$i=0;
			foreach($Out as $key => $val)
			{
				if($i>$Count)
					unset($Out[$key]);
				$i++;
			}
		}
		HTracer_Out();
		return $Out;
	}
	static function ClearPageData($Page)
	{//Удаляет всю инфу о странице
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		$CS=MD5($Page);
		global $HTracer_Files_Count;
		if(!$HTracer_Files_Count)
			$HTracer_Files_Count=0;
		
		if($GLOBALS['htracer_mysql'])
		{
			$table_prefix=HTracer::GetTablePrefix();
			$tablename=$table_prefix."htracer_pages";
			$tablename2=$table_prefix."htracer_queries";
			//$tablename3=$table_prefix."ht_search_query_n";
			
			$query="DELETE FROM `$tablename` WHERE (`URL_CS`= '$CS')";
			$res=htracer_mysql_query($query,'ClearPageData_1');

			$query="DELETE FROM `$tablename2` WHERE (`URL_CS`= '$CS')";
			$res=htracer_mysql_query($query,'ClearPageData_2');

			//$query="DELETE FROM `$tablename3` WHERE (`URL_CS`= '$CS')";
			//$res=htracer_mysql_query($query,'ClearPageData_3');
		}
		else
		{
			for($i=0;$i<$HTracer_Files_Count;$i++)
			{
				$filename=dirname(__FILE__).'/query/'.$CS.'_'.$i.'.txt';
				@unlink($filename);
			}
			for($i=0;$i<128;$i++)	
			{
				$filename=dirname(__FILE__).'/query/all'.$i.'.txt';
				if(file_exists($filename))
				{
					$strs=file($filename);
					$Data='';
					foreach($strs as $str)
					{
						$arr=explode('|#;#|',$str);
						if(sizeof($arr)!=3||$arr[2]==$Page)
							continue;
						if($Data)
							$Data.='\n';
						$Data.=$str;	
					}
					file_put_contents($filename,$Data);
				}
			}
		}
		HTracer_Out();
	}
	static function SelectMaxQueries($Pages,$Sanitarize=true,$UpCaseFirst=true,$selMax=true,$selMinus=true)
	{
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		global $HTracer_Files_Count;
		if(!$HTracer_Files_Count)
			$HTracer_Files_Count=64;
				
		if(!$Pages||!count($Pages))
		{
			HTracer_Out();
			return Array();
		}
		$table_prefix=HTracer::GetTablePrefix();
		$tablename2=$table_prefix."htracer_queries";
		$Out=Array();
		foreach($Pages as $Page=>$Count)
		{
			$Out[$Page]=Array();
			$Out[$Page]['N']=$Count;
			$Out[$Page]['Q']=Array();
		}
		if($GLOBALS['htracer_mysql'])
		{
			$CS_IN='';
			$CSToURL=Array();		
			foreach($Pages as $Page=>$Count)
			{
				if($CS_IN)
					$CS_IN.=' , ';
				$CS=MD5($Page);
				$CS_IN.=" '".$CS."' ";
				$CSToURL[$CS]=$Page;
			}
			// 
			if($selMinus)
				$query="SELECT * FROM `$tablename2` WHERE (`URL_CS` IN ($CS_IN))";
			else
				$query="SELECT * FROM `$tablename2` WHERE (`URL_CS` IN ($CS_IN) AND `Status`=1)";
			$res=htracer_mysql_query($query,'SelectMaxQueries');//mysql_query($query) or die('252 '.mysql_error().'<br />'.$query);
			$N=mysql_num_rows($res);
			//die("<h1>$N</h1>");
			for($i=0;$i<$N;$i++)
			{
				$cur=mysql_fetch_array($res);
				$EVA=$cur['Eva'];
				if(!$EVA)
					$EVA=1;
				if($Sanitarize)
					$Query=$cur['Out'];
				else
					$Query=$cur['In'];
				if(trim($Query)==''||is_Array($Query))
					continue;
				$Page=$CSToURL[$cur['URL_CS']];
				if(isset($Out[$Page]['Q'][$Query]))
					$Out[$Page]['Q'][$Query]+=$EVA;
				else
					$Out[$Page]['Q'][$Query]=$EVA;
			}
		}
		else
		{
			//HPROTECTION
			foreach($Pages as $Page=>$Count)
			{
				$URL_CS=MD5($Page);
				for($i=0;$i<$HTracer_Files_Count;$i++)
				{
					$filename=dirname(__FILE__).'/query/'.$URL_CS.'_'.$i.'.txt';
					if(file_exists($filename))
					{
						$strs=file($filename);
						foreach($strs as $str)
						{
							$Query=trim(HTracer::NormalizeQuery($str));
							if(trim($Query)==''||is_Array($Query))
								continue;
							if(isset($Out[$Page]['Q'][$Query]))
								$Out[$Page]['Q'][$Query]+=1;
							else
								$Out[$Page]['Q'][$Query]=1;
						}
					}
					else if($Count>$HTracer_Files_Count)
						break;
				}
			}
		}
		$Ns=0;
		$Ms=0;
		if(!$selMax)
		{
			HTracer_Out();
			return $Out;
		}
		foreach($Out as $Page=>$Data)
		{
			$Max=HTracer::SelectMax($Data['Q'],2);
			$ti=0;
			foreach($Max as $Key=>$Val)
			{
				$ti++;
				if($ti===1)
				{
					$Out[$Page]['Q']=$Key;	
					$Out[$Page]['M']=$Val;	
				}
				else
					$Out[$Page]['Key2']=$Key;
			}
			$Ns+=$Out[$Page]['N'];
			$Ms+=$Out[$Page]['M'];
		}
		$K=1;
		if($Ms!=0)
			$K=(double)((double)$Ns/(double)$Ms);
		foreach($Out as $Page=>$Data)//Увеличиваем оценки в среднем вдвое
			$Out[$Page]['E']=$Out[$Page]['N']+($K * $Out[$Page]['M']);
		$Out2=Array();
		foreach($Out as $Page=>$Data)	
		{
			$Q=str_replace(chr(209).chr(63),'ш',$Data['Q']); 
			$Q2=str_replace(chr(209).chr(63),'ш',$Data['Key2']); 
			if($Sanitarize && !$GLOBALS['htracer_mysql'])
			{
				$Q =sanitarize_keyword($Q);
				$Q2=sanitarize_keyword($Q2);
			}
			if($UpCaseFirst)
			{
				$Q =mb_ucfirst($Q);
				$Q2=mb_ucfirst($Q2);
			}	
			$Out2[$Q]=$Data;
			$Out2[$Q]['U']=$Page;
			$Out2[$Q]['Q']=$Q;
			$Out2[$Q]['Key2']=$Q2;
		}
		HTracer_Out();
		return $Out2;
	}
//ОБЛАКО	
	static function correct_urlstart($urlstart)
	{
		$urlstart=str_replace('_AMP_','&',$urlstart);
		if(stripos($urlstart,'http://'.$_SERVER['SERVER_NAME'])===0)
			$urlstart=substr(strlen('http://'.$_SERVER['SERVER_NAME']));
		if(stripos($urlstart,'http://www.'.$_SERVER['SERVER_NAME'])===0)
			$urlstart=substr(strlen('http://www.'.$_SERVER['SERVER_NAME']));
		if($urlstart{0}!='/')
			$urlstart='/'.$urlstart;
		return $urlstart;
	}
	static function FormCloud($Pages,$minsize,$maxsize,$mode,$shablon,$spacer,$HK=3)
	{
		if($minsize>$maxsize)
		{	
			$temp=$minsize;
			$minsize=$maxsize;
			$maxsize=$temp;
		}
		$max = -1999;
		$min = 999999;
		foreach ($Pages as $Page)
		{
			$val=$Page[$mode];
			if($max<$val)
				$max=$val;
			if($min>$val)
				$min=$val;
		}
		$delta 		= (double) $max - $min;
		$deltasize  = (double) $maxsize - $minsize;
		$Res='';
		//HPROTECTION
		$oPages=Array();
		foreach ($Pages as $Page)
			$oPages[$Page['U']]=$Page[$mode];
		arsort($oPages);
		if($HK)
		{
			if($HK===1||$HK==='1'||$HK===true)
				$HK=3;
			$i=0;
			$n=count($oPages);
			foreach ($oPages as $URL => $val)
			{
				if($val!=$min)
				{
					$rHK=1.0/$HK;
					$x=  $rHK + (($HK-$rHK) * $i)/($n-1);
					$y=  ($deltasize/($HK * $x)) + $minsize;
				}
				else
					$y= $minsize;
				$oPages[$URL]=$y;
				$i++;
			}
		}
		foreach ($Pages as $Page)
		{
			$val=$Page[$mode];
			if($delta==0)
				$val=round($minsize+($deltasize/2.0));
			else
				$val=$minsize + round(((double)($val - $min)/$delta) * $deltasize);
			if($HK)
				$val=($val+ 3 * $oPages[$Page['U']])/4;
			//$val=$oPages[$Page['U']];
				
			$val=round($val);
				
			
			if($Page['Q']===''||is_Array($Page['Q'])|| strtolower($Page['Q'])=='array')
				continue;	
			if($Page['Key2']===''||is_Array($Page['Key2']) || strtolower($Page['Key2'])=='array')
				$Page['Key2']=$Page['Q'];
			if(strtolower($GLOBALS['htracer_encoding'])!='utf-8')
			{
				$Page['Q']=mb_convert_encoding($Page['Q'], $GLOBALS['htracer_encoding'], 'utf-8');
				$Page['Key2']=mb_convert_encoding($Page['Key2'], $GLOBALS['htracer_encoding'], 'utf-8');
			}
			$U=$Page['U'];
			if(strpos($U,'&')!==false && strpos($U,';')===false)
				$U=str_replace('&','&amp;',$U);
			$U=str_replace("'" ,'&#39;',$U);
			$U=str_replace("\"",'&#34;',$U);
			$Q=explode('|',$Page['Q']);
			$Q=$Q[hkey_rand('key_n')%count($Q)];
			
			if(strpos($U,'#test')!==0)
				$U='http://'.$_SERVER['SERVER_NAME'].$U;
			$Replace= Array(
				'_HREF_'=>$U,
				'_SIZE_'=>$val,
				'_KEY_'=>$Q,
				'_KEY2_'=>$Page['Key2'],
				'_AMP_'=>'&'
			);
			if($Res)
				$Res.=$spacer;
			$Res.=str_replace(array_keys($Replace),array_values($Replace),$shablon);
		}
		return $Res;
	}
	static function TestCloud($Res,$params)
	{
		if(!$GLOBALS['htracer_test'])
			return $Res;
		$count =round(($params['count'] * $params['procent'])/100);
		
		
		if(count(explode('<a',$Res))>$count-3)
			return $Res;

		$Keys=Array('Sample','Test','HTracer','Cloud','HTracer cloud','HTracer cloud test','HTracer sample cloud','Sample cloud','Cloud test','HTracer test mode cloud');
		$Pages=Array();

		for($i=0; $i<$count; $i++)
		{
			$N=hkey_rand('tcloud')%100+100;
			$Key=$Keys[hkey_rand('tcloud')%count($Keys)];
			$Pages[$Key.$i]=Array(
				'N'=>$N,'M'=>$N,'E'=>$N,
				'Q'=>$Key,'Key2'=>$Key,
				'U'=>'#test'.$i
			);
		}
		//echo $params['count'].' ';
		//echo $count.' ';
		//echo count($Pages);
		$Res=HTracer::FormCloud($Pages,$params['minsize'],$params['maxsize'],$params['mode']
							 ,$params['shablon'],$params['spacer']);
							 
		$Res="<!--noindex-->Cloud Sample:<br /><br />$Res<br /><br /><small>If you dont want to see this -- disable test mode</small><!--/noindex-->";					 
		return $Res;
	}
	static function Cloud($params=false)
	{
		HTracer_In();
		$dcount=false;
	//Считаем параметры
		$paramsi0=htracer_parse_params($params);
		if(isset($paramsi0['ofset']) && !isset($paramsi0['offset']))
			$paramsi0['offset']=$paramsi0['ofset'];
		$params=$paramsi0;
		
		$defparams=Array
		(
			'upcase'		=> true,
			'sanitarize'	=> true,
			'count'			=> 20,
			'mode'			=> 'E', // E N M
			'minsize'		=> 70, 
			'maxsize'		=> 180,
			'shablon'		=> '<a href="_HREF_" style="font-size:_SIZE_%" title="_KEY2_">_KEY_</a>',
			'spacer'		=> ' ',
			'sort'			=> true,
			'replace'		=> '',
			'procent'		=> 100,
			'title'			=> '',
			'pre'			=> '',
			'post'			=> '',
			'style'			=> '',
			'this'			=> false,
			'urlstart'		=> false,
			'offset'		=> 0,
			
		);
	//Загружаем глобальные параметры по умолчанию
		$ArrGlobalNames=Array('htracer_cloud_links','htracer_cloud_randomize'
							 ,'htracer_cloud_min_size','htracer_cloud_max_size'
							 ,'htracer_cloud_style');
		foreach ($ArrGlobalNames as $GName)
			if(isset($GLOBALS[$GName]) && is_string($GLOBALS[$GName]))
				$GLOBALS[$GName]=trim($GLOBALS[$GName]);

		if(isset($GLOBALS['htracer_cloud_min_size']) && $GLOBALS['htracer_cloud_min_size'])
			$defparams['minsize']=intval($GLOBALS['htracer_cloud_min_size']);
		if(isset($GLOBALS['htracer_cloud_max_size']) && $GLOBALS['htracer_cloud_max_size'])
			$defparams['maxsize']=intval($GLOBALS['htracer_cloud_max_size']);
		if(isset($GLOBALS['htracer_cloud_pre']) && $GLOBALS['htracer_cloud_pre'])
			$defparams['pre']=($GLOBALS['htracer_cloud_pre']);
		if(isset($GLOBALS['htracer_cloud_post']) && $GLOBALS['htracer_cloud_post'])
			$defparams['post']=($GLOBALS['htracer_cloud_post']);
			
		if(isset($GLOBALS['htracer_cloud_links']) && $GLOBALS['htracer_cloud_links'])
			$GLOBALS['htracer_cloud_links']=intval($GLOBALS['htracer_cloud_links']);
		else
			$GLOBALS['htracer_cloud_links']=20;
		if(isset($GLOBALS['htracer_cloud_randomize']) && $GLOBALS['htracer_cloud_randomize'])
			$GLOBALS['htracer_cloud_randomize']=$GLOBALS['htracer_cloud_randomize'] * 1.0;
		else
			$GLOBALS['htracer_cloud_randomize']=1;
		if($GLOBALS['htracer_cloud_randomize']<1)
			$GLOBALS['htracer_cloud_randomize']=1;
		$defparams['count']   = round($GLOBALS['htracer_cloud_randomize'] * $GLOBALS['htracer_cloud_links']);
		$defparams['procent'] = round(100 / $GLOBALS['htracer_cloud_randomize']);
		
		$styles=Array(
			'cloud'=>Array(),
			
			'ul_list'=>Array(
				'shablon'=>'<li><a href="_HREF_" title="_KEY2_">_KEY_</a></li>',
				'sort'=>false,
				'pre'=>'<div class="clearfix"></div><ul class="seealso" style="margin-top:0">',
				'post'=>'</ul>'
			),
			'ol_list'=>Array(
				'shablon'=>'<li><a href="_HREF_" title="_KEY2_">_KEY_</a></li>',
				'sort'=>false,
				'pre'=>'<div class="clearfix"></div><ol class="seealso" style="margin-top:0">',
				'post'=>'</ol>'
			),
			'br_list'=>Array(
				'shablon'=>'<a href="_HREF_" title="_KEY2_">_KEY_</a>',
				'sort'=>false,
				'spacer'=>'<br />',
				'post'=>'<br />'
			),
			'space_list'=>Array(
				'shablon'=>'<a href="_HREF_" title="_KEY2_">_KEY_</a>',
				'sort'=>false,
				'spacer'=>' '
			),
			'comma_list'=>Array(
				'shablon'=>'<a href="_HREF_" title="_KEY2_">_KEY_</a>',
				'sort'=>false,
				'spacer'=>', '
			)
		);
		if(isset($GLOBALS['htracer_cloud_style']) && $GLOBALS['htracer_cloud_style']
		&& isset($styles[$GLOBALS['htracer_cloud_style']]))
			$defparams['style']=$GLOBALS['htracer_cloud_style'];
		
		foreach($defparams as $key => $value)
		{
			if(!isset($params[$key]))
				$params[$key]=$value;
		}
		if($params['urlstart'])
		{
			$params['urlstart']=HTracer::correct_urlstart($params['urlstart']);
			$urlstartCS='_us'.CRC32($params['urlstart']).'_';
		}
		$params['count']=intval($params['count']);
		$params['minsize']=intval($params['minsize']);
		$params['maxsize']=intval($params['maxsize']);
		$params['procent']=intval($params['procent']);

		if($params['style'])	
		{
			$sarr=explode(' ',trim($params['style']));
			foreach($sarr as $csname)
			{
				if($csname && isset($styles[$csname]))
				{
					$curstyle=$styles[$csname];
					foreach($curstyle as $Key=>$Val)
					{
						if(!isset($paramsi0[$Key]))	
							$params[$Key]=$Val;
					}
				}
				else if($csname)
				{
					$narr= explode('/',$csname);
					if(sizeof($narr)==2)
					{
						$narr[0]=trim($narr[0]);
						$narr[1]=trim($narr[1]);
						if(!isset($paramsi0['count']))	
							$params['count']=intval($narr[1]);
						if(!isset($paramsi0['procent']))
							$params['procent']=round((intval($narr[0]) * 100.0)/intval($narr[1]));
						if(!isset($paramsi0['procent']) && !isset($paramsi0['count']))
							$dcount= intval($narr[1])-intval($narr[0]);
						$rcount=intval($narr[0]);
					}
				}
			}
		}
		
		//Теперь пытаемся загрузить облако целиком из кеша (Кеш первого уровня)
		$CashCS=htracer_pars_to_str($params);
		$Res=htracer_read_cash_file('Cloud',$CashCS);
		if($Res)
		{
			if(function_exists('htracer_api_cloud_filter'))
				$Res=htracer_api_cloud_filter($Res,$params);
			$Res=HTracer::TestCloud($Res,$params);	
			HTracer_Out();	
			return $Res;
		}		
		//Отдельный кеш для страниц облака (второй уровень) (уникален для каждой страницы, если испозован стиль вроде 5/15)
		$Pages=htracer_read_cash_file('CloudCashedPages',$CashCS,false,(($params['procent'] && $params['procent']!=100) || $params['urlstart'] || $params['offset']));
		if(!$Pages)
		{ 
			if(is_string($params['replace']))
				$params['replace']=str_replace('+','&',$params['replace']);
			$params['replace']=htracer_parse_params($params['replace'],false);
			$SelectMaxPagesCount=$params['count']+$params['offset'];
			if(!$params['this'])
				$SelectMaxPagesCount++;
				
				
			if($GLOBALS['htracer_mysql'])
			{
				//Кеш третьего уровня - для всех страниц одинаковый при любых параметрах
				$Pages=htracer_read_cash_file('CloudCashedPages0M'.$urlstartCS,$SelectMaxPagesCount,false,false);
				if(!$Pages)
				{
					$WhereURL=" 1=1 ";
					$Pages=Array();
					$table_prefix=HTracer::GetTablePrefix();
					if(!$params['urlstart'])
						$WhereURL=" 1=1 ";
					else
					{
						$US=mysql_real_escape_string($params['urlstart']);
						$WhereURL=" URL LIKE '$US%' ";
					}
					$mr=htracer_mysql_query("
						SELECT `URL` as U, `Eva15` as N, `FirstKey` as Q, `SecondKey` as Key2  
						FROM `{$table_prefix}htracer_pages` 
						WHERE `ShowInCloud`=1 AND $WhereURL
						ORDER BY `Eva15` DESC 
						LIMIT $SelectMaxPagesCount
					");
					$pc=mysql_num_rows($mr);
					for($i=0;$i<$pc;$i++)
					{
						$cur=mysql_fetch_assoc($mr);
						$N=intval($cur['N']);
						if($N && $N>4)
							$N-=crc32($cur['U'])%round($N/4);
						$cur['N']=$N;	
						$cur['M']=$cur['N'];
						$cur['E']=$cur['N'];
						if($params['upcase'])
						{
							$cur['Q']=mb_ucfirst($cur['Q']);
							$cur['Key2']=mb_ucfirst($cur['Key2']);
						}
						$Pages[$cur['Q']]=$cur;
					}
					htracer_write_cash_file($Pages,'CloudCashedPages0M'.$urlstartCS,$SelectMaxPagesCount,false,false);
				}

				if($params['offset'])
				{			
					foreach($Pages as $key =>$Blablabla)
					{
						unset($Pages[$key]);
						$ntdc--;
						if($ntdc<=0)
							break;
					}
				}
				
				$keys=array_keys($Pages);
				$count0=sizeof($Pages);
				
				if(($params['procent'] && $params['procent']!=100 && $count0)||$dcount)
				{
					$count =round(($count0 * $params['procent'])/100);
					if($dcount===false)
						$dcount=$count0-$count;
					else
						$dcount=$count0-$rcount;
						
					if(!$params['this'])
						$dcount--;

					for($i=0;$i<$dcount;++$i)
					{	
						$rand=hkey_rand('cloud')%$count0;
						for($j=0;$j<100;++$j)
						{
							$j_rand=($rand+$j)%$count0;
							if($keys[$j_rand]!==false)
								break;
						}
						$key=$keys[$j_rand];
						$keys[$j_rand]=false;
						unset($Pages[$key]);
					}
				}
			}
			else //Не MySQL
			{
				//Кеш третьего уровня - для всех страниц одинаковый при любых параметрах
				$Pages=htracer_read_cash_file('CloudCashedPages0'.$urlstartCS,$SelectMaxPagesCount,false,false);
				if(!$Pages)
				{			
					$Pages=HTracer::SelectMaxPages($SelectMaxPagesCount,$params['urlstart']);
					htracer_write_cash_file($Pages,'CloudCashedPages0'.$urlstartCS,$SelectMaxPagesCount,false,false);
				}	
				$keys=array_keys($Pages);
				$count0=sizeof($Pages);

				if(($params['procent'] && $params['procent']!=100 && $count0)||$dcount)
				{
					$count =round(($count0 * $params['procent'])/100);
					if($dcount===false)
						$dcount=$count0-$count;
					else
						$dcount=$count0-$rcount;
					
					if(!$params['this'])
						$dcount--;
							
					for($i=0;$i<$dcount;++$i)
					{	
						$rand=hkey_rand('cloud')%$count0;
						for($j=0;$j<100;++$j)
						{
							$j_rand=($rand+$j)%$count0;
							if($keys[$j_rand]!==false)
								break;
						}
						$key=$keys[$j_rand];
						$keys[$j_rand]=false;
						unset($Pages[$key]);
					}
				}
					
				$Pages=HTracer::SelectMaxQueries($Pages,$params['sanitarize'],$params['upcase']);
			}	
			//print_R($Pages);

			if($params['sort'])
				ksort($Pages,SORT_STRING);
		
			if($params['replace'] && count($params['replace']))
			{
				$from=array_keys($params['replace']);
				$to  =array_values($params['replace']);
				foreach($Pages as $Key=>$Data)
				{
					$Pages[$Key]['Q']=trim(str_replace($from,$to,$Data['Q']));
					if(!isset($Data['Key2']) || $Data['Key2']==='' ||$Data['Key2']===false)
						$Pages[$Key]['Key2']=$Data['Q'];
				}
			}
			htracer_write_cash_file($Pages,'CloudCashedPages',$CashCS,false,(($params['procent'] && $params['procent']!=100) || $params['urlstart']));
		}
		if(!$params['this'])
		{
			//Удаляем ссылку на текущую страницу
			foreach($Pages as $CKey =>$CData)
			{
				$LastKey=$CKey;
				if($CData['U']==$_SERVER["REQUEST_URI"]
				|| $CData['U']==$_SERVER["REDIRECT_URL"]
 				|| $CData['U']==$_SERVER["REDIRECT_URI"] 
				|| $CData['U']==getenv("REQUEST_URI"))
				{
					unset($Pages[$CKey]);
					$LastKey=false;
					break;
				}
			}
			if($LastKey)
				unset($Pages[$LastKey]);
		}
		$Res=HTracer::FormCloud($Pages,$params['minsize'],$params['maxsize'],$params['mode']
							 ,$params['shablon'],$params['spacer']);
		if(trim($Res)!='')
			$Res=$params['title'].$params['pre'].$Res.$params['post'];
		htracer_write_cash_file($Res,'Cloud',$CashCS);
		if(function_exists('htracer_api_cloud_filter'))
			$Res=htracer_api_cloud_filter($Res,$params);
		$Res=HTracer::TestCloud($Res,$params);	
		HTracer_Out();	
		return $Res;
	}
	
//Импорт/Экспорт	
	static function ImportFromGA($Data,$echo=false)
	{
		$optimize_tables=$GLOBALS['htracer_mysql_optimize_tables'];
		$GLOBALS['htracer_mysql_optimize_tables']=false;
		
		@set_time_limit(600);
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		$GLOBALS['ht_in_ga_import']=true;
		if(!is_array($Data))
			$Data=explode("\n",$Data);
		$Keys=Array();
	
		$rn=0;
		foreach($Data as $Str)
		{
			$rn++;
			$Str=trim($Str);
//			$Str=str_replace(array('\"','\\"'),'"',$Str);
//			$arr=explode('"',$Str);
			
//			if(count($arr)>3)
//				continue;
//			if(strlen($Str)<5||$Str{0}==='#'||$Str{0}==='"')
//				continue;
//			if(count($arr)==3)
//			{
//				$arr[1]=str_replace(',','*zpt*',$arr[1]);
///				$Str=join('"',$arr);
//			}
			$arr=explode("\t",$Str);
			if(count($arr)<5)
				continue;
			/*if($rn==17)
			{
				echo '<br /><br /><br /><br /><pre>';
				print_r($arr);
				exit();
			}*/
			

			$URL=trim($arr[1]);//URL
			$Key=trim($arr[0]);//Запрос
			$Key=str_replace('*zpt*',',',$Key);
			$Key=trim($Key,'", ');//Запрос
			$Key=trim($Key);//Запрос
			$Num=trim($arr[2]);//Число
			//if(strpos($Key,'*zpt*')||strpos($Num,'*zpt*'))
			//	continue;
			//echo '22222222';	
			if(!$Key || stripos($Key,'no set')!==false|| stripos($Key,'not set')!==false ||stripos($Key,'not provided')!==false)
				continue;
			//echo '333333';	

			if(!is_numeric($Num))
				continue;
			//echo '444444444444';	

			$Num=intval($Num);
			if(!$Num)
				continue;
			if(strpos($URL,'&q=cache:')!==false||strpos($URL,'yandbtm?text=')!==false)
				continue;
				
				
			if(!isset($Keys[$Key]))
			{
				$Keys[$Key]=Array();
				$Keys[$Key]['Count']=$Num;
				$Keys[$Key]['URL']	=$URL;
			}
		}
		$AllCount=0;
		foreach($Keys as $Key => $Data)
		{
			$Num=$Data['Count'];
			$URL=$Data['URL'];
			$AllCount+=$Num;
			if($echo)
				echo "$Key($Num) $URL<br />";
			HTracer::AddQueryToDB($Key,'analitics','',$Num,$URL);
		}
		$GLOBALS['htracer_mysql_optimize_tables']=$optimize_tables;
		if($GLOBALS['htracer_mysql_optimize_tables'])
			HTracer::OptimizeTables();
		HTracer_Out();
		$GLOBALS['ht_in_ga_import']=false;
		return $AllCount;
	}
//Вставка
	static function Insert_Load_Params($html,&$what,&$params)
	{	
		if($what)
		{
			if($what{0}==='+')
				$what='img_alt+a_title'.$what;
			$what=$what;
		}
		else
			$what='img_alt+a_title';
		$params=htracer_parse_params($params);
		
		if(!is_array($what))
		{
			if(strpos($what,'+')!==false)
			{
				$arr=explode('+',$what);
				$what=Array();
				foreach($arr as $el)
				{
					$el=strtolower(trim($el));
					if(!$el || isset($what[$el]))
						continue;
					$what[$el]=$el;
				}
			}
			else
				$what=Array($what=>$what);	
		}

		$defParams=Array(
			'encoding' 		=> 'global',
			'delcomments' 	=> true,
			'procent'  		=> 100, 	//вероятность 
			'upcase' 		=> true,	//в верхний регистр первую букву
			'sanitarize'	=> true,
			'rewrite'	 	=> false, 	//перезаписывать даже если этот параметр есть
			'rewrite0'		=> true  	//перезаписывать даже если параметр есть, но пустой
		);
		if(!$params)
			$params=$defParams;
		else
		{	
			$params0=$params;
			$params=Array();
			foreach($params0 as $param => $value)
				$params[strtolower(trim($param))] = $value;
			foreach($defParams as $param => $value)
			{
				if(!isset($params[$param]))
					$params[$param]=$value;
			}
		}
		if(strtolower($params['encoding'])==='global'||strtolower($params['encoding'])==='globals'||strtolower($params['encoding'])==='from_globals')
			$params['encoding']=$GLOBALS['htracer_encoding'];
		else if(strtolower($params['encoding'])==='auto'||$params['encoding']==='true'||$params['encoding']==='1'||$params['encoding']===true||$params['encoding']===1||$params['encoding']==='')
		{
			if(function_exists('mb_detect_encoding'))	
				$params['encoding']=mb_detect_encoding($html, "windows-1251,UTF-8,koi-8r");
			else
				$params['encoding']='UTF-8';
		}
		$params['procent']=intval($params['procent']);
		foreach($params as $param => $value)
		{
			$fcontinue=false;
			if(strpos($param,'_'))
				continue;
			foreach($what as $key)
			{
				if(!isset($params[$key.'_'.$param]))
					$params[$key.'_'.$param]=$value;
				else if($param=='procent')
					$params[$key.'_'.$param]=intval($params[$key.'_'.$param]);
			}
		}
		
		$RParams=$_SERVER["REQUEST_URI"];
		$arr=explode('?',$RParams,2);
		$RParams=$arr[0];
		$arr=explode('/',$RParams);
		if(count($arr)>1)
		{
			$RParams='';
			for($i=0;$i<count($arr)-1;$i++)
			{
				if($RParams)
					$RParams.='/';
				$RParams.=$arr[$i];
			}
		}
		//HPROTECTION
		if($RParams{0}!='/' && $_SERVER['SERVER_NAME']{strlen($_SERVER['SERVER_NAME'])-1}!='/')
			$Base		   		= "http://".$_SERVER['SERVER_NAME'].'/'.$RParams;
		else
			$Base		   		= "http://".$_SERVER['SERVER_NAME'].$RParams;
		return $Base;	
	}
	static function Insert($html,$where=false,$params=false)
	{//Вставка альтов картинок, титлов ссылок и прочего		
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		//error_reporting (E_ALL);
		
		if(!isset($html{0})||$html{0}==='{')
			return $html;
		
		$UsePhpDom=!!$GLOBALS['htracer_use_php_dom'];
		if(!$where)
		{
			if(isset($GLOBALS['insert_keywords_where']))
				$where=$GLOBALS['insert_keywords_where'];
			else
				$where='img_alt+a_title+meta_keys';
		}
		if(!$params && isset($GLOBALS['insert_keywords_params']))
			$params=$GLOBALS['insert_keywords_params'];
		if(!$params)
			$params='';
		
		$stat=0;
		$stat2=0;
		
		$what=$where;
		$was_meta_keys 		= false;	
		$in_tag        		= false;
		$cur_tag_name  		= '';
		$in_quotes1    		= false;
		$in_quotes2    		= false;
		$in_comment    		= false;
		$tag_ranges	     	= Array();
		//$in_range 	   		= true;
		$in_range_a_title	= true;
		$in_range_img_alt	= true;
		$white_c_val		= true;
		$in_meta_keys		= false;
		$write				= true;
		$writecur			= true;
		
		$Base				= HTracer::Insert_Load_Params($html,$what,$params);
		$affix				= '';
		
		$is_c_tag_auto_close=false;
		$is_c_tag_auto_close=false;
		$TestNullStr='';
		if($GLOBALS['htracer_test'])
		{
			if(!$UsePhpDom)
				$TestNullStr=get_rand_key('htracer_void_url');
			else
				$TestNullStr=get_rand_key('htracer_void_url',true,true,'utf-8');
		}	
		$CashCS=htracer_pars_to_str($params).htracer_pars_to_str($where).$html;
		if($GLOBALS['htracer_cash_save_full_pages'] && !$GLOBALS['htracer_test'])
		{
			$Res=htracer_read_cash_file('Insert',$CashCS);
			if($Res)
			{
				HTracer_Out();
				return $Res;
			}
		}
		$res='';
		$cur_atrib			= ''; 
		$cur_atrib_name		= ''; 
		$atrib				= Array();
		$addon				='';
		if(!$params['encoding'])
			$params['encoding']='UTF-8';
		
		$len=strlen($html);
		$prev='';
		$prevprev='';

		//Кеширование альтов
		$AltCashCS=$params['img_alt_sanitarize']
				  .'*uc*'.$params['img_alt_upcase']
				  .'*en*'.$params['encoding']
				  .$UsePhpDom;
		$AltCash0=htracer_read_cash_file('InsertAltCash',$AltCashCS);
		$AltCash=Array();
		if($AltCash0 && is_array($AltCash0))
		{
			foreach ($AltCash0 as $i=>$Value)
				$AltCash[intval($i)]=$Value;
			$AltCashCount=count($AltCash);
		}
		else
			$AltCashCount=0;
		//print_r($AltCash);
			
		$AltCashPos=0;
		
		//Кеширование титлов
		$TitleCashCS=$params['a_title_sanitarize']
					.'*uc*'.$params['a_title_upcase']
					.'*en*'.$params['encoding']
					.$UsePhpDom;
		$TitleCash=htracer_read_cash_file('InsertTitleCash',$TitleCashCS);
		if(!$TitleCash)
		{
			$TitleCash=Array();
			$TitleCashCount=0;
		}
		else
			$TitleCashCount=count($TitleCash);
		
		$meta_keys_rewrite=$params['meta_keys_rewrite'];
		$meta_keys_rewrite0=$params['meta_keys_rewrite0'];

		$img_alt_rewrite=$params['img_alt_rewrite'];
		$img_alt_rewrite0=$params['img_alt_rewrite0'];
		$img_alt_procent=$params['img_alt_procent'];
		$img_alt_sanitarize=$params['img_alt_sanitarize'];
		$img_alt_upcase=$params['img_alt_upcase'];

		$a_title_rewrite=$params['a_title_rewrite'];
		$a_title_rewrite0=$params['a_title_rewrite0'];
		$a_title_procent=$params['a_title_procent'];
		$a_title_sanitarize=$params['a_title_sanitarize'];
		$a_title_upcase=$params['a_title_upcase'];
		
		//echo '$a_title_procent='.$a_title_procent;
		if(!$a_title_procent||$a_title_procent==='100')
			$a_title_procent=100;	
		if(!$img_alt_procent||$img_alt_procent==='100')
			$img_alt_procent=100;
		
		$meta_keys=isset($what['meta_keys']);
		$img_alt=isset($what['img_alt']);
		$a_title=isset($what['a_title']);
		$encoding=$params['encoding'];
		
		$URL=false;
		
		if(!$meta_keys||!$meta_keys_rewrite||!$meta_keys_rewrite0)
			$meta_keys=$meta_keys_rewrite=$meta_keys_rewrite0=HTracer::get_page_meta($URL,'meta_keywords_cb');
		
		$meta_desc=false;
		if(HTracer::get_page_meta($URL,'meta_description_cb'))
			$meta_desc = HTracer::get_page_meta($URL,'meta_description');
		$meta_title=false;	
		if(HTracer::get_page_meta($URL,'page_title_cb'))
			$meta_title = HTracer::get_page_meta($URL,'page_title');
		
		//для остальных тегов атрибуты мы не считываем
		$need_tags=Array('img'=>1,'a'=>1,'style'=>1,'meta'=>1,'script'=>1,'base'=>1,'head'=>1,'body'=>1,'title'=>1);
		if($UsePhpDom && class_exists('DOMDocument'))
		{//Быстрый разбор
			$EncodingAdded=false;
			if(stripos($html,'<meta http-equiv="content-type" content="text/html; charset=')===false
			&& stripos($html,"<meta http-equiv='content-type' content='text/html; charset=")===false)
			{
				$html='<meta http-equiv="content-type" content="text/html; charset='.$encoding.'" />'.$html;
				$EncodingAdded=true;
			}
			//echo $encoding;
			if(function_exists('str_replace'))
			{// Исправляем ошибку с комментариями в скрипте
				$PR_Array=Array();
				$PR_Array["<script type=\"text/javascript\">//<!--\n"]="<script type=\"text/javascript\">/*<!--*/\n";
				$PR_Array["<script type='text/javascript'>//<!--\n"]="<script type='text/javascript'>/*<!--*/\n";
				$PR_Array["<script type=\"text/javascript\">\n//<!--\n"]="<script type=\"text/javascript\">/*<!--*/\n";
				$PR_Array["<script type='text/javascript'>\n//<!--\n"]="<script type='text/javascript'>/*<!--*/\n";
				$PR_Array["<Script type=\"text/javascript\">//<!--\n"]="<script type=\"text/javascript\">/*<!--*/\n";
				$PR_Array["<Script type='text/javascript'>//<!--\n"]="<script type='text/javascript'>/*<!--*/\n";
				$PR_Array["<Script type=\"text/javascript\">\n//<!--\n"]="<script type=\"text/javascript\">/*<!--*/\n";
				$PR_Array["<Script type='text/javascript'>\n//<!--\n"]="<script type='text/javascript'>/*<!--*/\n";
				$PR_Array["<SCRIPT type=\"text/javascript\">//<!--\n"]="<script type=\"text/javascript\">/*<!--*/\n";
				$PR_Array["<SCRIPT type='text/javascript'>//<!--\n"]="<script type='text/javascript'>/*<!--*/\n";
				$PR_Array["<SCRIPT type=\"text/javascript\">\n//<!--\n"]="<script type=\"text/javascript\">/*<!--*/\n";
				$PR_Array["<SCRIPT type='text/javascript'>\n//<!--\n"]="<script type='text/javascript'>/*<!--*/\n";
				$PR_Array["<script>\n//<!--\n"]="<script>/*<!--*/\n";
				$PR_Array["<script>//<!--\n"]="<script>/*<!--*/\n";
				$PR_Array["<Script>\n//<!--\n"]="<Script>/*<!--*/\n";
				$PR_Array["<Script>//<!--\n"]="<Script>/*<!--*/\n";
				$PR_Array["<SCRIPT>\n//<!--\n"]="<SCRIPT>/*<!--*/\n";
				$PR_Array["<SCRIPT>//<!--\n"]="<SCRIPT>/*<!--*/\n";
				$PR_Array["<script type=\"text/javascript\">//<!--\r\n"]="<script type=\"text/javascript\">/*<!--*/\r\n";
				$PR_Array["<script type='text/javascript'>//<!--\r\n"]="<script type='text/javascript'>/*<!--*/\r\n";
				$PR_Array["<script type=\"text/javascript\">\r\n//<!--\r\n"]="<script type=\"text/javascript\">/*<!--*/\r\n";
				$PR_Array["<script type='text/javascript'>\r\n//<!--\r\n"]="<script type='text/javascript'>/*<!--*/\r\n";
				$PR_Array["<Script type=\"text/javascript\">//<!--\r\n"]="<script type=\"text/javascript\">/*<!--*/\r\n";
				$PR_Array["<Script type='text/javascript'>//<!--\r\n"]="<script type='text/javascript'>/*<!--*/\r\n";
				$PR_Array["<Script type=\"text/javascript\">\r\n//<!--\r\n"]="<script type=\"text/javascript\">/*<!--*/\r\n";
				$PR_Array["<Script type='text/javascript'>\r\n//<!--\r\n"]="<script type='text/javascript'>/*<!--*/\r\n";
				$PR_Array["<SCRIPT type=\"text/javascript\">//<!--\r\n"]="<script type=\"text/javascript\">/*<!--*/\r\n";
				$PR_Array["<SCRIPT type='text/javascript'>//<!--\r\n"]="<script type='text/javascript'>/*<!--*/\r\n";
				$PR_Array["<SCRIPT type=\"text/javascript\">\r\n//<!--\r\n"]="<script type=\"text/javascript\">/*<!--*/\r\n";
				$PR_Array["<SCRIPT type='text/javascript'>\r\n//<!--\r\n"]="<script type='text/javascript'>/*<!--*/\r\n";
				$PR_Array["<script>\r\n//<!--\r\n"]="<script>/*<!--*/\r\n";
				$PR_Array["<script>//<!--\r\n"]="<script>/*<!--*/\r\n";
				$PR_Array["<Script>\r\n//<!--\r\n"]="<Script>/*<!--*/\r\n";
				$PR_Array["<Script>//<!--\r\n"]="<Script>/*<!--*/\r\n";
				$PR_Array["<SCRIPT>\r\n//<!--\r\n"]="<SCRIPT>/*<!--*/\r\n";
				$PR_Array["<SCRIPT>//<!--\r\n"]="<SCRIPT>/*<!--*/\r\n";
				
				$PR_Array["<script>\n<!--\n"]="<script>\n<!-- \n";
				$PR_Array["<script><!--\n"]="<script><!-- \n";
				$PR_Array["<Script>\n<!--\n"]="<Script>\n<!-- \n";
				$PR_Array["<Script><!--\n"]="<Script><!-- \n";
				$PR_Array["<SCRIPT>\n<!--\n"]="<SCRIPT>\n<!-- \n";
				$PR_Array["<SCRIPT><!--\n"]="<SCRIPT><!-- \n";
				$PR_Array["<script type='text/javascript'>\n<!--\n"]="<script type='text/javascript'>\n<!-- \n";
				$PR_Array["<script type='text/javascript'><!--\n"]="<script type='text/javascript'><!-- \n";
				$PR_Array["<Script type='text/javascript'>\n<!--\n"]="<Script type='text/javascript'>\n<!-- \n";
				$PR_Array["<Script type='text/javascript'><!--\n"]="<Script type='text/javascript'><!-- \n";
				$PR_Array["<SCRIPT type='text/javascript'>\n<!--\n"]="<SCRIPT type='text/javascript'>\n<!-- \n";
				$PR_Array["<SCRIPT type='text/javascript'><!--\n"]="<SCRIPT type='text/javascript'><!-- \n";
				$PR_Array["<script type=\"text/javascript\">\n<!--\n"]="<script type=\"text/javascript\">\n<!-- \n";
				$PR_Array["<script type=\"text/javascript\"><!--\n"]="<script type=\"text/javascript\"><!-- \n";
				$PR_Array["<Script type=\"text/javascript\">\n<!--\n"]="<Script type=\"text/javascript\">\n<!-- \n";
				$PR_Array["<Script type=\"text/javascript\"><!--\n"]="<Script type=\"text/javascript\"><!-- \n";
				$PR_Array["<SCRIPT type=\"text/javascript\">\n<!--\n"]="<SCRIPT type=\"text/javascript\">\n<!-- \n";
				$PR_Array["<SCRIPT type=\"text/javascript\"><!--\n"]="<SCRIPT type=\"text/javascript\"><!-- \n";
				$PR_Array["<script>\r\n<!--\r\n"]="<script>\r\n<!-- \r\n";
				$PR_Array["<script><!--\r\n"]="<script><!-- \r\n";
				$PR_Array["<Script>\r\n<!--\r\n"]="<Script>\r\n<!-- \r\n";
				$PR_Array["<Script><!--\r\n"]="<Script><!-- \r\n";
				$PR_Array["<SCRIPT>\r\n<!--\r\n"]="<SCRIPT>\r\n<!-- \r\n";
				$PR_Array["<SCRIPT><!--\r\n"]="<SCRIPT><!-- \r\n";
				$PR_Array["<script type='text/javascript'>\r\n<!--\r\n"]="<script type='text/javascript'>\r\n<!-- \r\n";
				$PR_Array["<script type='text/javascript'><!--\r\n"]="<script type='text/javascript'><!-- \r\n";
				$PR_Array["<Script type='text/javascript'>\r\n<!--\r\n"]="<Script type='text/javascript'>\r\n<!-- \r\n";
				$PR_Array["<Script type='text/javascript'><!--\r\n"]="<Script type='text/javascript'><!-- \r\n";
				$PR_Array["<SCRIPT type='text/javascript'>\r\n<!--\r\n"]="<SCRIPT type='text/javascript'>\r\n<!-- \r\n";
				$PR_Array["<SCRIPT type='text/javascript'><!--\r\n"]="<SCRIPT type='text/javascript'><!-- \r\n";
				$PR_Array["<script type=\"text/javascript\">\r\n<!--\r\n"]="<script type=\"text/javascript\">\r\n<!-- \r\n";
				$PR_Array["<script type=\"text/javascript\"><!--\r\n"]="<script type=\"text/javascript\"><!-- \r\n";
				$PR_Array["<Script type=\"text/javascript\">\r\n<!--\r\n"]="<Script type=\"text/javascript\">\r\n<!-- \r\n";
				$PR_Array["<Script type=\"text/javascript\"><!--\r\n"]="<Script type=\"text/javascript\"><!-- \r\n";
				$PR_Array["<SCRIPT type=\"text/javascript\">\r\n<!--\r\n"]="<SCRIPT type=\"text/javascript\">\r\n<!-- \r\n";
				$PR_Array["<SCRIPT type=\"text/javascript\"><!--\r\n"]="<SCRIPT type=\"text/javascript\"><!-- \r\n";
				
				$html=str_replace(array_keys($PR_Array),array_values($PR_Array),$html);
			}
			if(function_exists('libxml_use_internal_errors'))
			{
				libxml_use_internal_errors(true);
				$domDoc = new DOMDocument;
				@$domDoc->loadHTML($html);
				$errors=Array();
				if(function_exists('libxml_get_errors'))
					$errors=libxml_get_errors();	
				if (!empty($errors) && function_exists('libxml_clear_errors')) 
					libxml_clear_errors();
				libxml_use_internal_errors(false);
			}
			else
			{
				$domDoc = new DOMDocument;
				@$domDoc->loadHTML($html);
			}
				
			if($meta_title)
			{
				$heads=$domDoc->getElementsByTagName('head');
				foreach ($heads as $head)
				{
					$titles=$head->getElementsByTagName('title');
					foreach ($titles as $title)
					{
						$head->removeChild($title);
						break;
					}
					$title2=new DOMElement('title',$meta_title);
					$head->appendChild($title2);
					break;
				}
			}
			//Титлы ссылок	
			if($a_title)
			{
				//ищем начало урлов в теге Base	
				$Base='';
				$bases=$domDoc->getElementsByTagName('base');
				foreach ($bases as $cbase)
				{
					if($cbase->hasAttribute('href'))
					{
						$Base=$cbase->getAttribute('href');
						break;
					}
				}
				//Подготавливаем загрузку ключей
				$links=$domDoc->getElementsByTagName('a');
				foreach ($links as $link)
				{
					if($a_title_rewrite  || !$link->hasAttribute('title')
					||($a_title_rewrite0 &&  $link->getAttribute('title')===''))
					{
						$href=$link->getAttribute('href');
						if(!$href||$href{0}==='#')
							continue;
						$href=htracer_form_href($href,$Base);
						HTracer::GetRandKey_Prepare($href);
					}
				}
				//Теперь раставляем ссылки
				foreach ($links as $link)
				{
					if(($a_title_rewrite  || !$link->hasAttribute('title')
					||($a_title_rewrite0 &&  $link->getAttribute('title')===''))
					&&($a_title_procent===100 ||(HTracer::myRand('a_title')%100)<$a_title_procent))
					{
						$href=$link->getAttribute('href');
						if(!$href||$href{0}==='#')
							continue;
						$href=htracer_form_href($href,$Base);
						if($href)
						{
							$stat++;
							if(isset($TitleCash[$href]))
								$title=$TitleCash[$href];
							else
							{
								$title=get_rand_key($href,$a_title_sanitarize,$a_title_upcase,'utf-8');
								$TitleCash[$href]=$title;
							}
							if($title)
							{
								$link->setAttribute('title',$title);
								if($title!==$TestNullStr)
									$stat2++;
							}
						}
					}
				}
			}
			if($img_alt)
			{
				$images=$domDoc->getElementsByTagName('img');
				foreach ($images as $image)
				{
					if(($a_title_rewrite  || !$image->hasAttribute('alt')
					||($a_title_rewrite0 &&  $image->getAttribute('alt')===''))
					&& ($img_alt_procent===100||(HTracer::myRand('img_alt')%100)<$img_alt_procent))
					{
						$stat++;
						if($AltCashPos<$AltCashCount)
							$alt=$AltCash[$AltCashPos];
						else
						{					
							$alt=get_rand_key(false,$img_alt_sanitarize,$img_alt_upcase,'utf-8');
							$AltCash[$AltCashPos]=$alt;
						}	
						if($alt)
						{
							$image->setAttribute('alt',$alt);
							if($alt!==$TestNullStr)
								$stat2++;
						}
						$AltCashPos++;						
					}
				}
			}
			if($meta_keys || $meta_desc)
			{
				$was_meta_keys=false;
				$was_meta_desc=false;
				$metas=$domDoc->getElementsByTagName('meta');
				//$meta_desc
				foreach ($metas as $meta)
				{
					if(!$meta->hasAttribute('name'))
						continue;
					$meta_name=trim(strtolower($meta->getAttribute('name')));
					if($meta_name=='keywords' && $meta_keys)
					{
						$was_meta_keys=true;		
						if($meta_keys_rewrite  ||!$meta->hasAttribute('content')
						||($meta_keys_rewrite0 && $meta->getAttribute('content')===''))
						{
							$stat++;
							if(HTracer::get_page_meta($URL,'meta_keywords_cb'))
								$keys=HTracer::get_page_meta($URL,'meta_keywords');
							else
								$keys=get_meta_keywords(', ','utf-8');
							if($keys)
							{
								$meta->setAttribute('content',$keys);
								if($keys!==$TestNullStr)
									$stat2++;
							}
						}
					}
					elseif($meta_name=='description' && $meta_desc)
					{
						$was_meta_desc=true;
						$stat++; 
						$stat2++;
						$meta->setAttribute('content',$meta_desc);
					}
				}
				if((!$was_meta_desc && $meta_desc)||(!$was_meta_keys && $meta_keys))
				{
					$heads=$domDoc->getElementsByTagName('head');
					if(!$was_meta_desc && $meta_desc)
					{
						foreach ($heads as $head)
						{
							$stat++; 
							$stat2++;
							$meta=new DOMElement('meta');
							$head->appendChild($meta);
							$meta->setAttribute('name','description');
							$meta->setAttribute('content',$meta_desc);
							break;
						}
					}
					if(!$was_meta_keys && $meta_keys)
					{
						foreach ($heads as $head)
						{
							$stat++;
							$keys=get_meta_keywords(', ','utf-8');
							if($keys)
							{
								$meta=new DOMElement('meta');
								$head->appendChild($meta);
							
								$meta->setAttribute('name','keywords');
								$meta->setAttribute('content',$keys);
								if($keys!==$TestNullStr)
									$stat2++;
							}
							break;
						}
					}
				}
			}
			$res=$domDoc->saveHTML();
			//удаляем кодировку, если мы ее добавили
			if($EncodingAdded && stripos($res,'<meta http-equiv="content-type" content="text/html; charset=')===0)
				$res=substr($res,strpos($res,'>')+1);
			//Если код изначально не имел BODY и прочего (был не страницей а ее куском), то удаляем нах все кроме содержимого Body	
			if(strpos($html,'<head')===false && strpos($html,'<Head')===false && strpos($html,'<HEAD')===false 
			&& strpos($html,'</head')===false && strpos($html,'</Head')===false && strpos($html,'</HEAD')===false
			&& strpos($html,'<body')===false && strpos($html,'<Body')===false && strpos($html,'<BODY')===false 
			&& strpos($html,'</body')===false && strpos($html,'</Body')===false && strpos($html,'</BODY')===false
			&& strpos($html,'<html')===false && strpos($html,'<Html')===false && strpos($html,'<HTML')===false 
			&& strpos($html,'</html')===false && strpos($html,'</Html')===false && strpos($html,'</HTML')===false
			&& strpos($html,'<!doctype')===false && strpos($html,'<!Doctype')===false && strpos($html,'<!DOCTYPE')===false)
			{
				$start=strpos($res,'<body');
				$end=strpos($res,'</body>');
				if($start!==false && $end!==false && $start<$end && false)
				{
					$start=strpos($res,'>',$start);
					$res=substr($res,$start + 1,  $end - $start -1);
				}
			}
		}
		else
		{//Надежный разбор
			
			//TODO: ПОДГОТОВКА К ЗАБОРУ КЛЮЧЕЙ ВНЕШНИХ СТРАНИЦ
			/*
			if($a_title)
			{
				$pa=split('<a ',$html);
				$acount=count($pa);
				for($i=1;$i<$acount;++$i)
				{
					$ca=$pa[$i];
					if(!$a_title_rewrite0 && strpos($ca,'title=')!==false)
						continue;
					if(!$a_title_rewrite && strpos($ca,'title=')!==false && strpos($ca,'title=""')===false && strpos($ca,'title=""')===false)
						continue;
				}
			}
			*/
			
			//USP 
			if(!$Encoding)
				$Encoding=$GLOBALS['htracer_encoding'];
			if(!$Encoding||strtolower($Encoding)==='auto'||strtolower($Encoding)==='global')
				$Encoding='UTF-8';
			if($Encoding!=='UTF-8'&& strtolower($Encoding)!=='utf-8'||strtolower($Encoding)!=='utf8')
			{
				if($meta_desc)
					$meta_desc  = mb_convert_encoding($meta_desc, $Encoding, 'UTF-8');
				if($meta_title)
					$meta_title = mb_convert_encoding($meta_title, $Encoding, 'UTF-8');
			}
			$in_meta_desc=false;
			$was_meta_desc=false;
			
			$in_meta_title=false;
			$was_meta_title=false;
			
			$prev='';
			$cur='';
			//Перебор
			for($i=0;$i<$len;++$i)
			{
				$addon='';
				$prevprev=$prev;
				$prev=$cur;
				$next='';
				$cur=$html{$i};
				if($cur==='<' && !$in_quotes1 && !$in_quotes2)
				{
					//Оптимизация по скорости
					if($html{$i+2}===' '||$html{$i+2}==='>')
						$get_tag_name=$html{$i+1};
					elseif($html{$i+3}===' '||$html{$i+3}==='>')
						$get_tag_name=$html{$i+1}.$html{$i+2};
					else
						$get_tag_name=htracer_get_tag_name($html,$i);
					
					$in_meta_title=false;
				
					if(isset($tag_ranges['script'])    && $get_tag_name==='/script')
						unset($tag_ranges['script']);
					elseif(isset($tag_ranges['style']) && $get_tag_name==='/style')
						unset($tag_ranges['style']);
					elseif(!$is_c_tag_auto_close  && $in_tag && $cur_tag_name && $i!==0 && $cur_tag_name{0}!=='/' 
						&& ($prev==='/' || ($i!==1 && ($prev===' '||$prev==="\n"||$prev==="\t") && $prevprev==='/')))
					{
						if(isset($tag_ranges[$cur_tag_name]))
						{
							if($tag_ranges[$cur_tag_name]==1||$cur_tag_name==='style'||$cur_tag_name==='script')
								unset($tag_ranges[$cur_tag_name]);
							else
								$tag_ranges[$cur_tag_name]--;
						}
					}
				}	
				if($cur==="'" && $in_tag && !$in_quotes2 && !isset($tag_ranges['script']) && !isset($tag_ranges['style']))
				{	
					if($i+1<$len)
						$next=$html{$i+1};
					$write=true;
					if(isset($need_tags[$cur_tag_name]))
					{
						if(!$in_quotes1)
							$cur_atrib_name=htracer_get_atrib_name($html,$i);
						else
							$atrib[$cur_atrib_name]=$cur_atrib;
						if($meta_keys
						&& $cur_tag_name==='meta' 
						&& $cur_atrib_name==='name' 
						&& strtolower($cur_atrib)==='keywords')
						{
							$was_meta_keys = true;
							$in_meta_keys  = true;
						}
						elseif($meta_desc
						&& $cur_tag_name==='meta' 
						&& $cur_atrib_name==='name' 
						&& strtolower($cur_atrib)==='description')
						{
							$was_meta_desc = true;
							$in_meta_desc  = true;
						}
						elseif($meta_keys && !$in_quotes1 && ($cur_tag_name==='meta' && $cur_atrib_name==='content' && $in_meta_keys)
						&&($meta_keys_rewrite||($next==="'" && $meta_keys_rewrite0)))
						{	
							$stat++;
							$addon=get_meta_keywords();
							if($addon && $addon!==$TestNullStr)
								$stat2++;
						}
						elseif($meta_desc && !$in_quotes1 && ($cur_tag_name==='meta' && $cur_atrib_name==='content' && $in_meta_desc))
						{	
							$stat++;
							$stat2++;
							$addon=$meta_desc;
						}
						else if(!$in_quotes1 && $cur_tag_name==='img' && $cur_atrib_name==='alt'
						&&($img_alt_rewrite || ($next==="'" && $img_alt_rewrite0))
						&& $img_alt 
						&& ($img_alt_procent===100||(HTracer::myRand('img_alt')%100)<$img_alt_procent))
						{
							$stat++;
							if($AltCashPos<$AltCashCount)
								$addon=$AltCash[$AltCashPos];
							else
							{					
								$addon=get_rand_key(false,$img_alt_sanitarize,$img_alt_upcase,$encoding);
								$AltCash[$AltCashPos]=$addon;
							}	
							if($addon && $addon!==$TestNullStr)
								$stat2++;
							$AltCashPos++;
						}
						else if(!$in_quotes1 && $cur_tag_name==='a' && $cur_atrib_name==='title'
						&&($a_title_rewrite || ($next==="'" && $a_title_rewrite0))
						&& $a_title 
						&& ($a_title_procent===100 ||(HTracer::myRand('a_title')%100)<$a_title_procent))
						{
							$href=$atrib['href'];
							if(!$href && $href!=='0')
								$href=htracer_find_attrib($html,$i,'href',$encoding,$len);
							$href=htracer_form_href($href,$Base);
							if($href)
							{
								$stat++;
								if(isset($TitleCash[$href]))
									$addon=$TitleCash[$href];
								else
								{
									$addon=get_rand_key($href,$a_title_sanitarize,$a_title_upcase,$encoding);
									$TitleCash[$href]=$addon;
								}	
								if($addon && $addon!==$TestNullStr)
									$stat2++;
							}	
						}	
					}
					$in_quotes1=!$in_quotes1;
					$cur_atrib='';
				}
				elseif($cur==='"' && $in_tag && !$in_quotes1 && !isset($tag_ranges['script']) && !isset($tag_ranges['style']))
				{
					if($i+1<$len)
						$next=$html{$i+1};
					$write=true;
					if(isset($need_tags[$cur_tag_name]))
					{
						if(!$in_quotes2)
							$cur_atrib_name=htracer_get_atrib_name($html,$i);
						else
							$atrib[$cur_atrib_name]=$cur_atrib;
				
						if($meta_keys
						&& $cur_tag_name==='meta' 
						&& $cur_atrib_name==='name' 
						&& strtolower($cur_atrib)==='keywords')
						{
							$was_meta_keys = true;
							$in_meta_keys  = true;
						}
						elseif($meta_desc
						&& $cur_tag_name==='meta' 
						&& $cur_atrib_name==='name' 
						&& strtolower($cur_atrib)==='description')
						{
							$was_meta_desc = true;
							$in_meta_desc  = true;
						}
						elseif(!$in_quotes2 
						&& $meta_keys
						&& $cur_tag_name==='meta' 
						&& $cur_atrib_name==='content' 
						&& $in_meta_keys
						&& ($meta_keys_rewrite||($next==='"' && $meta_keys_rewrite0)))
						{	
							$stat++;
							$addon=get_meta_keywords();
							if($addon && $addon!==$TestNullStr)
								$stat2++;
						}
						elseif(!$in_quotes2 
						&& $meta_desc
						&& $cur_tag_name==='meta' 
						&& $cur_atrib_name==='content' 
						&& $in_meta_desc)
						{	
							$stat++;
							$stat2++;
							$addon=$meta_desc;
						}
						elseif(!$in_quotes2 && $cur_tag_name==='img' && $cur_atrib_name==='alt'
						&&($img_alt_rewrite || ($next==='"' && $img_alt_rewrite0))
						&& $img_alt 
						&& ($img_alt_procent===100||(HTracer::myRand('img_alt')%100)<$img_alt_procent))
						{
							$stat++;
							if($AltCashPos<$AltCashCount)
								$addon=$AltCash[$AltCashPos];
							else
							{					
								$addon=get_rand_key(false,$img_alt_sanitarize,$img_alt_upcase,$encoding);
								$AltCash[$AltCashPos]=$addon;
							}
							if($addon && $addon!==$TestNullStr)
								$stat2++;
							$AltCashPos++;
						}
						elseif(!$in_quotes2 && $cur_tag_name==='a' && $cur_atrib_name==='title'
						&&($a_title_rewrite || ($next==='"' && $a_title_rewrite0))
						&& $a_title 
						&& ($a_title_procent===100||(HTracer::myRand('a_title')%100)<$a_title_procent))
						{
							$href=$atrib['href'];
							if(!$href && $href!=='0')
								$href=htracer_find_attrib($html,$i,'href',$encoding,$len);
							$href=htracer_form_href($href,$Base);
							if($href)
							{
								$stat++;
								if(isset($TitleCash[$href]))
									$addon=$TitleCash[$href];
								else
								{
									$addon=get_rand_key($href,$a_title_sanitarize,$a_title_upcase,$encoding);
									$TitleCash[$href]=$addon;
								}	
								if($addon && $addon!==$TestNullStr)
									$stat2++;
							}
						}
					}
					$in_quotes2=!$in_quotes2;
					$cur_atrib='';
				}
				elseif($in_tag && ($in_quotes1||$in_quotes2))
					$cur_atrib.=$cur;
				elseif(!$in_quotes1 && !$in_quotes2 && !isset($tag_ranges['script']) && !isset($tag_ranges['style']))
				{
					if($cur==='<')
					{
						if($html{$i+1}==='!' && $html{$i+2}==='-' && $html{$i+3}==='-')//HTracer::IsStarts($html,$i,'<!--'))
							$in_comment = true;
						else if(!$in_comment)
						{
							$in_tag = true;
							$cur_tag_name=$get_tag_name;
							if($cur_tag_name==='/head')
							{							
								if(!$was_meta_keys && $meta_keys)
									$res.=' <meta name="keywords" content="'.get_meta_keywords().'" /> ';
								if(!$was_meta_desc && $meta_desc)
									$res.=' <meta name="description" content="'.$meta_desc.'" /> ';
								if(!$was_meta_title && $meta_title)
									$res.='<title>'.$meta_title.'</title>';
							}
							if($cur_tag_name{0}==='/')
							{
								$is_c_tag_auto_close=true;
								$tname=	substr($cur_tag_name,1);
								if(isset($tag_ranges[$tname]))
								{
									if($tag_ranges[$tname]==1||$tname==='style'||$tname==='script')
										unset($tag_ranges[$tname]);
									else
										$tag_ranges[$tname]--;
								}
							}
							elseif(isset($need_tags[$cur_tag_name]))
							{	
								$is_c_tag_auto_close=HTracer::is_autoends_tag($html,$i);
								if(!$is_c_tag_auto_close)
								{
									if(isset($tag_ranges[$cur_tag_name]))
										$tag_ranges[$cur_tag_name]++;
									else
										$tag_ranges[$cur_tag_name]=1;
								}
							}
						}
					}
					elseif($cur==='>')
					{
						if($meta_title)
						{
							if($cur_tag_name=='title')
							{
								//$addon=$meta_title;
								$in_meta_title=true;
								$was_meta_title=true;
								$stat++;
								$stat2++;
								$res.='>'.$meta_title;
							}		
						}
						if($html{$i-1}==='-' && $html{$i-2}==='-')//HTracer::IsEnds($html,$i,'-->'))
							$in_comment = false;
						else if($in_tag && !$in_comment)		
						{
							$in_tag = false;
							$AvAdd= !isset($tag_ranges['head']) && !$in_comment; 
							if(isset($tag_ranges['head']) && $cur_tag_name==='base' && isset($atrib['href']))
								$Base=$atrib['href'];
							else if($AvAdd && $cur_tag_name==='img' && $img_alt
								&&!isset($atrib['alt'])
								&&($img_alt_procent===100 || (HTracer::myRand('img_alt')%100)<$img_alt_procent))
							{
								$stat++;
								if($AltCashPos<$AltCashCount)
									$rand_key=$AltCash[$AltCashPos];
								else
								{					
									$rand_key=get_rand_key(false,$img_alt_sanitarize,$img_alt_upcase,$encoding);
									$AltCash[$AltCashPos]=$rand_key;
								}	
								$AltCashPos++;

								if($rand_key)
								{
									if($rand_key!==$TestNullStr)
										$stat2++;
									if($res[strlen($res)-1]==='/')
									{
										$res[strlen($res)-1]=' ';
										$res.='alt="'.$rand_key.'" /';
									}
									else
										$res.=' alt="'.$rand_key.'"';
								}
								$rand_key='';
							}
							else if($AvAdd && $cur_tag_name==='a' && $a_title 
									&& !isset($atrib['title'])
									&&($a_title_procent===100||(HTracer::myRand('a_title')%100)<$a_title_procent))
							{
								$href=htracer_form_href($atrib['href'],$Base);
								if($href)
								{
									$stat++;
									if(isset($TitleCash[$href]))
										$rand_key=$TitleCash[$href];
									else
									{
										$rand_key=get_rand_key($href,$a_title_sanitarize,$a_title_upcase,$encoding);
										$TitleCash[$href]=$rand_key;
									}	
									if($rand_key)
									{	
										if($rand_key!==$TestNullStr)
											$stat2++;
										$res.=' title="'.$rand_key.'"';
									}
									$rand_key='';
								}
							}	
							$atrib=Array();
						}
						$in_meta_keys  = false;
						$in_meta_desc  = false;
						
					}
				}
				if($addon)
				{
					if($i+1<$len)
						$next=$html{$i+1};//mb_substr($html,$i+1,1,$params['encoding']);
					if($cur==="'")
						$res.="'".$addon;
					elseif($cur===">")
						$res.='>'.$addon;
					elseif($cur==="<")
						$res.='<'.$addon;
					else
						$res.='"'.$addon;
					if($next!=="'"||$next!=='"')
						$write=false;
					else
						$writecur=false;
					$addon='';		
				}	
				if($write && $writecur && !$in_meta_title)
					$res.=$cur;
						
				$writecur=true;
				
				//оптимизация по скорости	
				if($cur==='>' && !$in_tag && !$in_meta_title
				&& !$in_quotes1 && !$in_quotes2 
				&& !isset($tag_ranges['script']) && !isset($tag_ranges['style'])
				&& !$in_comment)
				{
					$tspos=strpos($html,'<',$i);
					if($tspos && $tspos-$i>10)
					{
						$res.=substr($html,$i+1,$tspos - $i -1);
						$i=$tspos-1;
					}	
				}
				/**/
			}
		}
		if($AltCashCount<$AltCashPos && !$GLOBALS['htracer_test'])
			htracer_write_cash_file($AltCash,'InsertAltCash',$AltCashCS);
		if($TitleCashCount<count($TitleCash) && !$GLOBALS['htracer_test'])	
			htracer_write_cash_file($TitleCash,'InsertTitleCash',$TitleCashCS);	
		if($GLOBALS['htracer_cash_save_full_pages'] && !$GLOBALS['htracer_test'])
			htracer_write_cash_file($res,'Insert',$CashCS);
		if($GLOBALS['htracer_test'])
		{
			if($stat==0 && $stat2==0)
			{
				if(!trim($html))
					$res='Range is empty<br />'.$res;
				elseif(strlen(trim($html))<100)
					$res='Range is too short<br />'.$res;
				else
					$res='strlen='.strlen(trim($html)).'<br />'.$res;
			}
			elseif($stat2==0)
				$res=' <!--noindex-->Probably in DB no keys, import or wait few days<br /> <!--/noindex-->'.$res;
			$res=" <!--noindex-->OK<br /><br />HTracer:: Number of insertions/Avalible Places= $stat2/$stat 
			<br /><b>If you dont want to see this text - disable Test mode in HTracer options</b>
			<br /> <!--/noindex-->".$res.' <!--noindex--><br />HT_END_OF_INSERT_RANGE<!--/noindex-->';
		}
		HTracer_Out();
		return $res;
	}
	
//Автовалидация html	
	static function is_autoends_tag(&$html,$pos)
	{
		$len=strlen($html);
		$in_quotes1    = false;
		$in_quotes2    = false;
		for($i=$pos;$i<$len;$i++)
		{
			$cur=$html{$i};
			if($cur=='"' && !$in_quotes1)
				$in_quotes2=!$in_quotes2;
			if($cur=="'" && !$in_quotes2)
				$in_quotes1=!$in_quotes1;
			if($cur=='/' && !$in_quotes1 && !$in_quotes2)
				return true;
			if($cur=='>' && !$in_quotes1 && !$in_quotes2)
				break;
		}
		return false;
	}
	static function Validate($html) 
	{
		if(!$GLOBALS['htracer_validate'])	
			return $html;
		if(!isset($html{0})||$html{0}==='{')//защита от JSON
			return $html;

		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		
		$SingleTags=Array('link'=>1,'base'=>1,'rel'=>1,'br'=>1,'hr'=>1,'meta'=>1,'area'=>1,'img'=>1,'param'=>1,'input'=>1);	
		$RenameTags=Array('i'=>'em','b'=>'strong','s'=>'strike');
		if($GLOBALS['htracer_cash_save_full_pages'])
		{
			$Res=htracer_read_cash_file('Validate',$html);
			if($Res)
			{
				HTracer_Out();
				return $Res;
			}
		}
		$was_meta_keys 		= false;	
		$in_tag        		= false;
		$cur_tag_name  		= '';
		$in_quotes1    		= false;
		$in_quotes2    		= false;
		$in_comment    		= false;
		$tag_ranges	     	= Array();
		$affix				= '';
		$lastnobr 			= false;
		$cur_atrib_name 	='';
		
		$is_c_tag_auto_close=false;
		$prevprev='';
		$prev='';
		$tr_td_opened=0;
		$Res='';	
		$len=strlen($html);
		for($i=0;$i<$len;$i++)
		{
			$prevprev=$prev;
			$prev=$cur;
			$cur=$html{$i};	
			if($cur==='<')
			{
				$get_tag_name=htracer_get_tag_name($html,$i);
				if(isset($tag_ranges['script']) && $get_tag_name==='/script')
					unset($tag_ranges['script']);
				elseif(isset($tag_ranges['style']) && $get_tag_name==='/style')
					unset($tag_ranges['style']);
			}
			if($cur==='>' && !$is_c_tag_auto_close && $in_tag && $cur_tag_name[0]!='/' && $cur_tag_name && $i!=0 && ($prev==='/' || ($i!=1 && ($prev===' '||$prev==="\n"||$prev==="\t") && $prevprev==='/')))
			{
				if(isset($tag_ranges[$cur_tag_name]))
				{
					if($tag_ranges[$cur_tag_name]===1||$cur_tag_name==='style'||$cur_tag_name==='script')
						unset($tag_ranges[$cur_tag_name]);
					else
						$tag_ranges[$cur_tag_name]--;
				}
			}	
			else if($cur==='>' && !$is_c_tag_auto_close && $in_tag && $cur_tag_name && $cur_tag_name[0]!='/' && $i!=0 && ($prev!='/' && (($prev!=' '&&$prev!="\n"&&$prev!="\t") || $prevprev!='/')))
			{	
				if(isset($SingleTags[$cur_tag_name]))
				{
					$HaveTE_Tag=false;
					for($j=$i;$j<$len;$j++)
					{
						$curj=$html{$j};	
						if($curj==='<')
						{
							$ttn=$get_tag_name;
							if($ttn==='/'.$cur_tag_name||$ttn==='/ '.$cur_tag_name)
								$HaveTE_Tag=true;
							break;
						}
						if($j-$i>100)
							break;
					}
					if(!$HaveTE_Tag)
						$Res.=' /';
				}
			}
			$is_in_normal_html= !$in_quotes1 && !$in_quotes2 && !isset($tag_ranges['script']) && !isset($tag_ranges['style']);
			if($cur==="'" && $in_tag && !$in_quotes2 && !isset($tag_ranges['script']) && !isset($tag_ranges['style']))
			{	
				$write=true;
				if(!$in_quotes1)
				{
					$cur_atrib_name=htracer_get_atrib_name($html,$i);
					if(strtolower($cur_atrib_name)==='href')
					{
						$cattr='';
						for($j=$i+1;$j<$len;$j++)
						{
							$curj=$html{$j};	
							if($curj==="'")
								break;
							$cattr.=$curj;
						}
						$cattr0=$cattr;
						if(strpos($cattr,';')===false)
							$cattr=str_replace('&','&amp;',$cattr);
						if($cattr0!=$cattr)
						{
							$Res.="'".$cattr;	
							$i+=strlen($cattr0);			
							continue;
						}
					}
				}
				else
					$atrib[$cur_atrib_name]=$cur_atrib;
				$in_quotes1=!$in_quotes1;
				$cur_atrib='';
			}
			else if($cur==='"' && $in_tag && !$in_quotes1 && !isset($tag_ranges['script']) && !isset($tag_ranges['style']))
			{
				$write=true;
				if(!$in_quotes2)
				{
					$cur_atrib_name=htracer_get_atrib_name($html,$i);
					if(strtolower($cur_atrib_name)==='href')
					{
						$cattr='';
						for($j=$i+1;$j<$len;$j++)
						{
							$curj=$html{$j};	
							if($curj==='"')
								break;
							$cattr.=$curj;
						}
						$cattr0=$cattr;
						if(strpos($cattr,';')===false)
							$cattr=str_replace('&','&amp;',$cattr);
						if($cattr0!=$cattr)
						{
							$Res.='"'.$cattr;	
							$i+=strlen($cattr0);			
							continue;
						}
					}
				}
				else
					$atrib[$cur_atrib_name]=$cur_atrib;
				$in_quotes2=!$in_quotes2;
				$cur_atrib='';
			}
			else if($in_tag && ($in_quotes1||$in_quotes2))
				$cur_atrib.=$cur;
			else if($cur==='<' && $is_in_normal_html)
			{
				if($html{$i+1}==='!' && $html{$i+2}==='-' && $html{$i+3}==='-')//HTracer::IsStarts($html,$i,'<!--'))
					$in_comment = true;
				else if(!$in_comment)
				{
					$in_tag = true;
					$cur_tag_name =$get_tag_name;
					$cur_tag_name0=$cur_tag_name;
					if(isset($RenameTags[$cur_tag_name]))
						$cur_tag_name=$RenameTags[$cur_tag_name];
					
					if($cur_tag_name{0}==='/')
					{
						$is_c_tag_auto_close=true;
						$tname=	substr($cur_tag_name,1);
						if(isset($tag_ranges[$tname]))
						{
							if($tag_ranges[$tname]===1||$tname==='style'||$tname==='script')
								unset($tag_ranges[$tname]);
							else if(isset($tag_ranges[$tname]))
								$tag_ranges[$tname]--;//удаляем закрывающиеся теги, которые не открываются
							else if($html{strlen($cur_tag_name0)+1}==='>')
							{
								$i+=strlen($cur_tag_name0)+1;
								continue;
							}
							else if($html{strlen($cur_tag_name0)+2}==='>')
							{
								$i+=strlen($cur_tag_name0)+2;
								continue;
							}
						}
					}
					else 
					{
						$is_c_tag_auto_close=HTracer::is_autoends_tag($html,$i);
						if (!$is_c_tag_auto_close)
						{
							if(isset($tag_ranges[$cur_tag_name]))
								$tag_ranges[$cur_tag_name]++;
							else
								$tag_ranges[$cur_tag_name]=1;
						}
					}
					// Закрываем TD
					if($cur_tag_name==='tr')
						$tr_td_opened=$tag_ranges['td'];
					if($cur_tag_name==='/tr')
					{
						if(($tag_ranges['td']-$tr_td_opened)===1)
							$Res.='</td>';
						$tr_td_opened=0;
					}
					//<nobr> на <span style="white-space:nowrap;">
					if($cur_tag_name==='nobr' && ($html{$i+5}==='>'||$html{$i+6}==='>'))
					{
						$lastnobr=true;
						$Res.='<span style="white-space:nowrap;"';
					}
					else if($lastnobr && (($cur_tag_name==='/nobr' && ($html{$i+6}==='>'||$html{$i+7}==='>')) 
					||($cur_tag_name==='/ nobr' && ($html{$i+7}==='>'||$html{$i+8}==='>'))))
					{
						$lastnobr=false;
						$Res.='</span'; 
					}
					else if($cur_tag_name==='img' && (stripos($html,'alt',$i+1)===false||stripos($html,'alt',$i+1)>strpos($html,'>',$i+1)))
						$Res.='<img alt="" ';//дописываем альты к картинкам
					else if($cur_tag_name==='style' && (stripos($html,'type',$i+1)===false||stripos($html,'type',$i+1)>strpos($html,'>',$i+1)))
						$Res.='<style type="text/css" ';//дописываем type у style и script
					else if($cur_tag_name==='script' && (stripos($html,'type',$i+1)===false||stripos($html,'type',$i+1)>strpos($html,'>',$i+1)))
						$Res.='<script type="text/javascript" ';
					else
						$Res.='<'.$cur_tag_name;	
					$i+=strlen($cur_tag_name0);
					continue;
				}
			}
			$Res.=$cur;
		}
		$arr=explode('</head>',$Res,2);
		if(count($arr)===2)
		{
			$arr[0]=str_replace('&nbsp;',' ',$arr[0]);
			$Res=$arr[0].'<head>'.$arr[1];
		}
		if($GLOBALS['htracer_cash_save_full_pages'])
			htracer_write_cash_file($Res,'Validate',$CashCS);
		HTracer_Out();	
		return $Res;
	}	
//МЕТА КЕЙС	
	static function get_meta_keys($Razd=', ',$URL=false)
	{
		HTracer_In();
		$Res=htracer_read_cash_file('get_meta_keys_2');
		if($Res)
		{
			HTracer_Out();
			return $Res;
		}	
		if(HTracer::get_page_meta($URL,'meta_keywords_cb') && HTracer::get_page_meta($URL,'meta_keywords'))
		{
			HTracer_Out();
			return HTracer::get_page_meta($URL,'meta_keywords');
		}
		global $HTracer_Files_Count;
		if(!$HTracer_Files_Count)
			$HTracer_Files_Count=64;
		$table_prefix=HTracer::GetTablePrefix();
		if($URL===false)
		{
			$URL=$_SERVER["REQUEST_URI"];
			if(getenv("REQUEST_URI"))
				$URL=getenv("REQUEST_URI");
		}
		$URL_CS=MD5($URL);
		
		$tablename2=$table_prefix."htracer_queries";
		$Keys= Array();
		if($GLOBALS['htracer_mysql'])
		{
			if(!isset($GLOBALS['htracer_curent_page_keys_in'])
			 ||!$GLOBALS['htracer_curent_page_keys_in'])
				HTracer::FormCurentPageKeysArrays();
			$Keys=$GLOBALS['htracer_curent_page_keys_in'];
		}
		else
		{	
			for($i=0;$i<$HTracer_Files_Count;++$i)
			{	
				$filename = dirname(__FILE__).'/query/'.$URL_CS.'_'.$i.'.txt';
				if(!file_exists($filename))
					break;
				$strings  = file($filename);
				for($j=0;$j<count($strings);$j++)
				{
					$Q = HTracer::NormalizeQuery($strings[$j]);
					if(!isset($Keys[$Q]))
						$Keys[$Q]=0;
					$Keys[$Q]+=1;					
				}
			}
		}
		//print_r($Keys);
		if(count($Keys))
			$res= HTracer::get_meta_keys_from_array($Keys,$Razd);
		else
			$res= '';
		htracer_write_cash_file($res,'get_meta_keys');
		HTracer_Out();
		return $res; 
	}
	static function get_meta_keys_from_array($Keys=false,$Razd=', ')
	{
		//0. Сначала выводим наиболее часточные 3ухсловные сочетания в наиболее частотной словоформе
		//1. Затем выводит наиболее часточные 2ухсловные сочетания в наиболее частотной словоформе
		//2. Затем выводит наиболее часточные слова в наиболее частотной словоформе, если это словоформа не была использована в 1.
		//3. Затем выводим наиболее часточные словоформы, которые не были использованы в 1. и 3.
		//4. Выводим остаток 2. Те слова, словоформы которых были в 1.
		//5. Записывая наиболее частотный запрос, состоящий из более чем 2 слов 
		// при рассчете частотности популярые слова в Интернете типа купить скачать обладают меньшим весом
		// При равенстве частотности приоритет отдаеться более поздним записям	

		
		$N = count($Keys);
		
		$All_Forms  = Array(); //Все словоформы
		$All_Words  = Array(); //Все слова 
		$All_Words2 = Array(); //Словосочетания из двух слов
		$All_Words3 = Array(); //Словосочетания из трех слов
		
		
		$MAXEVA=-1;
		$MAXKEY='';
		
		//Формируем эти 4 массива
		$is_numeric_array=true;
		$i=0;
		foreach($Keys as $Key => $EVA)
		{//проверка на ассоциативность
			if(!is_numeric($Key))
				$is_numeric_array=false;
			$i++;
			if($i==5)
				break;
		}
		$KeysCount=0;
		foreach($Keys as $Key =>$EVA)
		{	
			++$KeysCount;
			if($KeysCount===100)
				break;
				
			if($is_numeric_array)//если это не ассоциативный массив
			{
				$Key = $EVA;
				$EVA = 1;
			}
			
			if(!$EVA)
				$EVA=1;
			else if($EVA!=1)
				$EVA + log($EVA,2);//если запрос был несколько раз, то добавляем ему доверия 
			$Words=htracer_split_words($Key);
			if(count($Words)>2 && $MAXEVA<=$EVA)
			{
				$MAXEVA  = $EVA;
				$MAXKEY  = $Key;
			}
			foreach($Words as $j => $Word)
			{	
				if(htracer_isStopWord($Word))
					continue;
				$cEVA=$EVA;
				if(htracer_isPopWord($Word))
					$cEVA=$cEVA * 0.7;	
				if($j>=1 && $Words[$j-1] && htracer_isStopWord($Words[$j-1]))
					$Word=$Words[$j-1].' '.$Word;//добавляем переднее стоповое слово
				if(!isset($All_Forms[$Word]))
					$All_Forms[$Word]=0;
				$All_Forms[$Word]+=$EVA;
				$Base=HkStrem($Word);
				if(!$Base)
					continue;
				if(!isset($All_Words[$Base]))
				{
					$All_Words[$Base]=Array();
					$All_Words[$Base]['Forms']=Array();
					$All_Words[$Base]['Count']=0;
				}
				$All_Words[$Base]['Count']+=$cEVA;
				if(!isset($All_Words[$Base]['Forms'][$Word]))
					$All_Words[$Base]['Forms'][$Word]=0;
				$All_Words[$Base]['Forms'][$Word]+=$cEVA;
				if(isset ($Words[$j+1]))
				{
					$spacer=' ';
					$nWord=$Words[$j+1];//следующее слово
					$sdvig=2;
					if(htracer_isStopWord($nWord))
					{//если следующее слово стоп-слово, мы его пропуснаем но запоминаем в $spacer
						if(!isset($Words[$j+2]))
							continue;
						if(htracer_isStopWord($Words[$j+2]))
						{// два стоп слова подряд. Актуально для английского, Например Lord of the Rings 
							if(!isset($Words[$j+3]))
								continue;
							$spacer=' '.$nWord.' '.$Words[$j+2].' ';
							$nWord=$Words[$j+3];
							$sdvig=4;
						}
						else
						{						
							$spacer=' '.$nWord.' ';
							$nWord=$Words[$j+2];
							$sdvig=5;
						}
						if(htracer_isPopWord($nWord))
							$cEVA=$cEVA * 0.7;	
					}
					$nBase=HkStrem($nWord);
					if(!$nBase)
						continue;
					if(!isset($All_Words2[$Base.$nBase]))
					{
						$All_Words2[$Base.$nBase]=Array();
						$All_Words2[$Base.$nBase]['Forms']=Array();
						$All_Words2[$Base.$nBase]['Count']=0;
					}
					$All_Words2[$Base.$nBase]['Count']+=$cEVA;
					if(!isset($All_Words2[$Base.$nBase]['Forms'][$Word.$spacer.$nWord]))
						$All_Words2[$Base.$nBase]['Forms'][$Word.$spacer.$nWord]=0;
					$All_Words2[$Base.$nBase]['Forms'][$Word.$spacer.$nWord]+=$cEVA;
					if(!isset($Words[$j+$sdvig]))
						continue;
					$nWord2=$Words[$j+$sdvig];
					$spacer2=' ';
					if(htracer_isStopWord($nWord2))
					{
						if(htracer_isStopWord($Words[$j+$sdvig+1]))
							continue;
						$spacer2=' '.$nWord2.' ';
						$nWord2=$Words[$j+$sdvig+1];
					}
					if(htracer_isPopWord($nWord2))
						$cEVA=$cEVA * 0.7;
					$nBase2=HkStrem($nWord2);
					if(!isset($All_Words3[$Base.$nBase.$nBase2]))
					{
						$All_Words3[$Base.$nBase.$nBase2]=Array();
						$All_Words3[$Base.$nBase.$nBase2]['Forms']=Array();
						$All_Words3[$Base.$nBase.$nBase2]['Count']=0;					
					}
					$All_Words3[$Base.$nBase.$nBase2]['Count']+=$cEVA;
					if(!isset($All_Words3[$Base.$nBase.$nBase2][$Word.$spacer.$nWord.$spacer2.$nWord2]))
						$All_Words3[$Base.$nBase.$nBase2][$Word.$spacer.$nWord.$spacer2.$nWord2]=0;
					$All_Words3[$Base.$nBase.$nBase2][$Word.$spacer.$nWord.$spacer2.$nWord2]+=$cEVA;
				}
			}
		}
//		print_r($All_Words);
		$All_Words  = HTracer::SelectMax($All_Words, $GLOBALS['htracer_metakeys_max_words1']);
//		print_r($All_Words);

		foreach($All_Words as $k => $Word)
		{
			$Str=HTracer::SelectMaxKey($Word['Forms']);
			unset($All_Forms[$Str]);
		}	
		$All_Words2 = HTracer::SelectMax($All_Words2, $GLOBALS['htracer_metakeys_max_words2']);
		$All_Words3 = HTracer::SelectMax($All_Words3, $GLOBALS['htracer_metakeys_max_words3']);
		$All_Forms  = HTracer::SelectMax($All_Forms,  $GLOBALS['htracer_metakeys_max_forms']);
		$TempStr='';
		$Res = Array();
		foreach($All_Words3 as $k => $Word)
		{
			if($Word['Count']<=$MAXEVA)
				continue;
			$Str=HTracer::SelectMaxKey($Word['Forms']);
			if(strpos($TempStr,'!'.$Str.'!')===false)
			{
				$TempStr.='!'.$Str.'!';
				$Res[]=$Str;
			}
		}
		foreach($All_Words2 as $k => $Word)
		{
			if($Word['Count']<=$MAXEVA)
				continue;
			$Str=HTracer::SelectMaxKey($Word['Forms']);
			if(strpos($TempStr,'!'.$Str.'!')===false)
			{
				$Res[]=$Str;
				$TempStr.='!'.$Str.'!';
			}
		}
		foreach($All_Words as $k => $Word)
		{
			if($Word['Count']<=$MAXEVA)
				continue;
			$Str=HTracer::SelectMaxKey($Word['Forms']);
			if(strpos($TempStr,'!'.$Str.'!')===false)
			{
				$Res[]=$Str;
				$TempStr.='!'.$Str.'!';
			}
		}
		foreach($All_Forms as $Str => $cnt)
		{
			//if($count<=$MAXEVA && count($Res))
			//	continue;
			if(strpos($TempStr,'!'.$Str.'!')===false)
			{
				$Res[]=$Str;
				$TempStr.='!'.$Str.'!';
			}
		}		
		$Res[]=$MAXKEY;
		$Out='';
//	print_r($Res);

		if(!$GLOBALS['htracer_metakeys_max_len'] || $GLOBALS['htracer_metakeys_max_len']<50)
			$GLOBALS['htracer_metakeys_max_len']=200;
		foreach ($Res as $Cur)
		{
			if(!$Cur)	
				continue;
			//echo $Cur;
			if(mb_strlen($Out,'utf-8')>$GLOBALS['htracer_metakeys_max_len'])
				break;
			if($Out!=='')
				$Out.=$Razd;
			$Out.=$Cur;
		}
		return $Out;
	}
	static function GetRealIp()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			$ip=$_SERVER['REMOTE_ADDR'];
		return $ip;
	}
	static function SiteUrl()
	{
		return "http://".$_SERVER['SERVER_NAME'];
	}
	static function SelectCountOfKeys($URL_CS=false)
	{
		static $Cash=Array();
		if(isset($Cash[$URL_CS]))
			return $Cash[$URL_CS];
		if(!$GLOBALS['htracer_mysql'])
			$val=trim(@file_get_contents(dirname(__FILE__).'/query/'.$URL_CS.'_count'));
		if(!$val)
			$val=0;
		$val=intval($val);
		$Cash[$URL_CS]=$val;
		return $val;
	}
// ВЫБОР СЛУЧАЙНОГО КЛЮЧЕВИКА	
	static function GetRandKey($URL=false)
	{//Возвращает случайный ключевик
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		if($URL===false)
			$URL=$_SERVER["REQUEST_URI"];
		else if(stripos($URL,'http://')===0)
			$URL=str_replace(HTracer::SiteUrl(),'',$URL);
		if($GLOBALS['htracer_mysql'])
		{
			if($URL==$_SERVER["REQUEST_URI"])
				$Out=HTracer::GetRandKeyOfCurentPage();
			else
			{
				if(!isset($GLOBALS['htracer_pages_cash'][$URL]))
				{
					HTracer::GetRandKey_Prepare($URL);
					$Arr=Array();
					foreach($GLOBALS['htracer_pages_prepare'] as $URL)
						$Arr[]="'".MD5($URL)."'";
					$IN=JOIN(',',$Arr);
					$table_prefix=HTracer::GetTablePrefix();
					if(count($Arr)>1)
						$res=htracer_mysql_query("SELECT * FROM `{$table_prefix}htracer_pages` WHERE `URL_CS` IN($IN) AND ShowATitle=1",'GetRandKey');
					else
						$res=htracer_mysql_query("SELECT * FROM `{$table_prefix}htracer_pages` WHERE `URL_CS` = $IN AND ShowATitle=1",'GetRandKey');
						
					$n=mysql_num_rows($res);
					for($i=0;$i<$n;$i++)
					{
						$cur=mysql_fetch_assoc($res);
						$GLOBALS['htracer_pages_cash'][$cur['URL']]=$cur;
					}
					//Теперь запоминаем не найденные страницы
					foreach($GLOBALS['htracer_pages_prepare'] as $URL)
						if(!isset($GLOBALS['htracer_pages_cash'][$URL]))
							$GLOBALS['htracer_pages_cash'][$URL]=Array();
					$GLOBALS['htracer_pages_prepare']=Array();
					//echo '<pre><br>';
					//print_r($GLOBALS['htracer_pages_cash']);
				}
				$page=$GLOBALS['htracer_pages_cash'][$URL];
				if(strpos($page['FirstKey'],'|')!==false)
				{
					$Rt=explode('|',$page['FirstKey']);
					$Out=$Rt[hkey_rand('sdsds')%count($Rt)];
				}
				elseif(HTracer::Rand('GetRandKey')%100<33 && $page['SecondKey'])
					$Out=$page['SecondKey'];
				else
					$Out=$page['FirstKey'];
			}
		}
		else
		{// работа с файлами
			$URL_CS=MD5($URL);
			$count=HTracer::SelectCountOfKeys($URL_CS);
			$count=HTracer::LogRound($count,1.5);
			if(!$count)
				$offset=0;
			else
				$offset=HTracer::myRand('23dfs3') % $count;
			global $HTracer_Files_Count;
			if(!$HTracer_Files_Count)
				$HTracer_Files_Count=64;
			$file=$offset%$HTracer_Files_Count;
			$line=floor((double)$offset/(double)$HTracer_Files_Count);
			$file = file(dirname(__FILE__).'/query/'.$URL_CS.'_'.$file.'.txt');
			$Out=$file[$line];
			$Out=HTracer::Sanitarize($Out);
		}
		HTracer_Out();
		return trim($Out);
	}
	static function FormCurentPageKeysArrays()
	{//Формирует массивы для быстрого выбора случайного ключа текущей страницы
		if(isset($_SERVER["REQUEST_URI"]) && $_SERVER["REQUEST_URI"])
			$URL_CS=MD5($_SERVER["REQUEST_URI"]);
		else
			$URL_CS=MD5(getenv("REQUEST_URI"));
		$table_prefix=HTracer::GetTablePrefix();
		$res=htracer_mysql_query(
			"SELECT `Out` as Q,`In` as Q0,`OutEva` as N  FROM `{$table_prefix}htracer_queries` 
				WHERE URL_CS='$URL_CS' AND `Status`=1
				ORDER BY `OutEva` DESC
				LIMIT 1000",
			'FormCurentPageKeysArrays');
		$n=mysql_num_rows($res);
		$Out0=Array();
		$Max=0;
		$In0=Array();
		for($i=0;$i<$n;$i++)
		{
			$cur=mysql_fetch_assoc($res);
			$Out0[$cur['Q']]=$cur['N'];
			$In0[$cur['Q0']]=$cur['N'];
			if($Max<$cur['N'])
				$Max=$cur['N'];
		}
		//Теперь удаляем случайные ключи
		$Precession=0;
		if($Max>10000)
			$Precession=9;
		elseif($Max>7000)
			$Precession=8;
		elseif($Max>3000)
			$Precession=7;
		elseif($Max>1000)
			$Precession=6;
		elseif($Max>700)
			$Precession=5;
		elseif($Max>300)
			$Precession=4;
		elseif($Max>100)
			$Precession=3;
		elseif($Max>25)
			$Precession=2;
		elseif($Max>10)
			$Precession=1;
		if($Precession)
		{
			foreach($Out0 as $cur=>$N)
				if($Precession>=$N)
					unset($Out0[$cur]);
			foreach($In0 as $cur=>$N)
				if($Precession>=$N)
					unset($In0[$cur]);
		}			
		$GLOBALS['htracer_curent_page_keys']=$Out0;//Массив для построения метакеев
		$GLOBALS['htracer_curent_page_keys_in']=$In0;//Массив для построения метакеев

		//Теперь создаем массив для быстрого поиска вариантов
		$Out=Array();
		$pos=0;
		foreach($Out0 as $Key => $Count)
		{		
			if(!$Count)	
				$Count=1;
			$Out[] =Array
			(
				'Key'=>$Key,
				'From'=>$pos,
				'To'=>$pos+$Count,
			);			
			$pos+=$Count;
		}
		$GLOBALS['htracer_curent_page_keys_fast']=$Out;	
		return $Out;
	}
	static function GetRandKeyOfCurentPage()
	{//Быстро возвращает ключ для текущей страницы	
		if(!isset($GLOBALS['htracer_curent_page_keys']))
			HTracer::FormCurentPageKeysArrays();
		$Array=$GLOBALS['htracer_curent_page_keys_fast'];
		$count=sizeof($Array);
		if($count===0||$Array[$count-1]['To']==0)
			return '';
		$Rand=HTracer::Rand('GetRandKeyOfCurentPage') % $Array[$count-1]['To'];
		$first=0;
		$last=$count;

		//быстрый поиск
		while ($first < $last)
		{
			$mid = $first + ($last - $first) / 2;
			if ($Array[$mid]['From'] <= $Rand)
			{
				if($Rand <= $Array[$mid]['To'])
					return $Array[$mid]['Key'];
				$first = $mid + 1;
			}
			else
				$last = $mid;
		}
		return '';
	}
	static function GetRandKey_Prepare($URL)
	{//Подготовиться сгенерировать ключ для другой страницы
		if(!isset($GLOBALS['htracer_pages_prepare']))
			$GLOBALS['htracer_pages_prepare']=Array();
		if(stripos($URL,'http://')===0)
			$URL=str_replace(HTracer::SiteUrl(),'',$URL);
		if($URL!=$_SERVER["REQUEST_URI"] && !isset($GLOBALS['htracer_pages_cash'][$URL]))
			$GLOBALS['htracer_pages_prepare'][$URL]=$URL;
		return '';	
	}

//Общая работа с БД	
	static function DelAllFiles($dirname)
	{
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		$ddirname=dirname(__FILE__).'/'.$dirname;
		$d=opendir($ddirname);
		$df_count=0;
		while(true) 
		{
			$f=readdir($d);
			if($f===false)
			{
				for($i=0;$i<7;$i++)
				{
					closedir($d);
					$d=opendir($ddirname);
					$f=readdir($d);
					if($f!==false && $f!="." && $f!=".." && $f!="")
						break;
				}
				if($f===false|| $f=="." || $f==".." || $f=="")
					break;
			}
			if($f!="."&&$f!=".."&&$f!="") 
				unlink($ddirname.'/'.$f);
			$df_count++;
		}
		closedir($d);		
		HTracer_Out();
	}	
	static function TrancateTablesOld()
	{
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		HTracer::DelAllFiles('cash');
		HTracer::DelAllFiles('query');
		
		if(!$GLOBALS['htracer_mysql'])
		{
			HTracer_Out();
			return;
		}
		$table_prefix=HTracer::GetTablePrefix();
		$tablename=$table_prefix."ht_search_query";
		$tablename2=$table_prefix."ht_search_query_s";
		$tablename3=$table_prefix."ht_search_query_n";
		$query="TRUNCATE TABLE `$tablename`";
		htracer_mysql_query($query,'TrancateTables_1');//mysql_query($query) or die ('2111 :'.mysql_error());	
		$query="TRUNCATE TABLE `$tablename2`"; 
		htracer_mysql_query($query,'TrancateTables_2');//mysql_query($query) or die ('2112 :'.mysql_error());	
		$query="TRUNCATE TABLE `$tablename3`"; 
		htracer_mysql_query($query,'TrancateTables_3');//mysql_query($query) or die ('2113 :'.mysql_error());	
		HTracer_Out();
	}
	static function TrancateTables()
	{
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		HTracer::DelAllFiles('cash');
		HTracer::DelAllFiles('query');
		
		if(!$GLOBALS['htracer_mysql'])
		{
			HTracer_Out();
			return;
		}
		$table_prefix=HTracer::GetTablePrefix();
		$tablename1=$table_prefix."htracer_pages";
		$tablename2=$table_prefix."htracer_queries";
		
		$query="TRUNCATE TABLE `$tablename1`";
		htracer_mysql_query($query,'TrancateTables_1');
		$query="TRUNCATE TABLE `$tablename2`"; 
		htracer_mysql_query($query,'TrancateTables_2');
		HTracer_Out();		
	}
	static function IsNeedToConvertTables()
	{
		if(!isset($GLOBALS['htracer_mysql'])||!$GLOBALS['htracer_mysql'])
			return false;
			
		$table_prefix=HTracer::GetTablePrefix();
		$res1=htracer_mysql_query("select count(*) from `{$table_prefix}htracer_pages`",'IsNeedToConvertTables_1',false);
		if(!$res1)
			return true;

		$res1=mysql_fetch_array($res1);
		$res1=$res1[0];
		$res2=htracer_mysql_query("select count(*) from `{$table_prefix}ht_search_query_n`",'IsNeedToConvertTables_1',false);
		if(!$res2)
			return false;

		$res2=mysql_fetch_array($res2);
		$res2=$res2[0];
		return $res2>($res1 * 10);
	}
	static function ConvertTables()
	{
		//echo '1111';
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		
		set_time_limit(2000);
		HTracer::CreateTables();
		HTracer::TrancateTables();
		HTracer::CreateTables();

		$table_prefix=HTracer::GetTablePrefix();
		$tablename=$table_prefix."ht_search_query_s";
		$Res=htracer_mysql_query("SELECT URL_CS, Query, EVA FROM `$tablename`",'ConvertTables_1');
		$nr=mysql_num_rows($Res);
		$Keys=Array();
		for($i=0;$i<$nr;++$i)
		{
			$Cur=mysql_fetch_assoc($Res);
			$Cur['Query']=str_replace(chr(209).chr(63),'ш',$Cur['Query']);
			$Cur['Query']=HTracer::NormalizeQuery($Cur['Query']);
			$key=$Cur['URL_CS'].'#k=#'.$Cur['Query'];
			if(!isset($Keys[$key]))
				$Keys[$key]=$Cur;
			else
				$Keys[$key]['EVA']+=$Cur['EVA'];
		}
		
		$Keys2=Array();
		foreach($Keys as $Q)
		{
			$key=$Q['Query'];
			if(!isset($Keys2[$key]) || $Keys2[$key]['EVA']<$Q['EVA'])
				$Keys2[$key]=$Q;
		}
		$VALUES=Array();
		unset($Keys);
		$PagesWeigth=Array();
		
		$PagesMax1Q=Array();
		$PagesMax2Q=Array();
		
		$PagesMax1Val=Array();
		$PagesMax2Val=Array();

		$PagesMax1ID=Array();
		$PagesMax2ID=Array();
		$ID=0;
		$tablename=$table_prefix."htracer_queries";
		foreach($Keys2 as $Q)
		{
			$ID++;
			$Q['Query']=str_replace(chr(209).chr(63),'ш',$Q['Query']);
			$Q['Query']=HTracer::NormalizeQuery($Q['Query']);
			$In  	= mysql_real_escape_string($Q['Query']);
			$Out 	= HTracer::Sanitarize($Q['Query']);
			$Out 	= mysql_real_escape_string($Out);
			$Eva 	= $Q['EVA'];
			$CS  	= $Q['URL_CS'];
			$Ver    = $GLOBALS['htracer_curent_version_id']; 
			$Status = !HTracer::isSpecQuery($Q['Query']);
			if(!isset($PagesWeigth[$CS]))
				$PagesWeigth[$CS]=0;
			if($Status)
			{
				$PagesWeigth[$CS]+=$Eva;
				if(!isset($PagesMax1Val[$CS]))
				{
					$PagesMax1Val[$CS]=$Eva;
					$PagesMax1Q[$CS]=$Out;
					$PagesMax1ID[$CS]=$ID;
				}
				elseif($PagesMax1Val[$CS]<=$Eva)
				{
					$PagesMax2Val[$CS]=$PagesMax1Val[$CS];
					$PagesMax2Q[$CS]=$PagesMax1Q[$CS];
					$PagesMax2ID[$CS]=$PagesMax1ID[$CS];

					$PagesMax1Val[$CS]=$Eva;
					$PagesMax1Q[$CS]=$Out;
					$PagesMax1ID[$CS]=$ID;
				}
				elseif($PagesMax2Val[$CS]<$Eva)
				{
					$PagesMax2Val[$CS]=$Eva;
					$PagesMax2Q[$CS]=$Out;
					$PagesMax2ID[$CS]=$ID;
				}
				$Status=1;
			}
			else
				$Status=0;
			$VALUES[]="('$In','$Out',$Eva,$Eva,'$CS',$Ver,$Status)";
			if($ID%1000===0)
			{
				$VALUES=JOIN(" , \n",$VALUES);
				htracer_mysql_query("INSERT INTO `$tablename` (`In`,`Out`,`Eva`,`OutEva`,`URL_CS`,`Version`,`Status`) VALUES $VALUES 
										ON DUPLICATE KEY UPDATE `Version`=`Version`");
				$VALUES=Array();
			}
		}	
		unset($Keys2);
		if(count($VALUES))
		{
			$VALUES=JOIN(" , \n",$VALUES);
			htracer_mysql_query("INSERT INTO `$tablename` (`In`,`Out`,`Eva`,`OutEva`,`URL_CS`,`Version`,`Status`) VALUES $VALUES 
									ON DUPLICATE KEY UPDATE `Version`=`Version`");
		}
		
		$tablename=$table_prefix."ht_search_query_n";
		$Res=htracer_mysql_query("SELECT URL_CS, URL FROM `$tablename`");
		$Pages=Array();
		$nr=mysql_num_rows($Res);
		for($i=0;$i<$nr;++$i)
		{
			$Cur=mysql_fetch_assoc($Res);
			$Cur['Eva']=0;
			if(isset($PagesWeigth[$Cur['URL_CS']]))
				$Cur['Eva']=$PagesWeigth[$Cur['URL_CS']];
			$Pages[]=$Cur;
		}
		$VALUES=Array();
		$tablename=$table_prefix."htracer_pages";
		$i=0;
		foreach($Pages as $P)
		{
			$i++;
			$URL=mysql_real_escape_string($P['URL']);
			$CS=$P['URL_CS'];
			$Eva=$P['Eva'];
			if(!$Eva)
				$Eva=0;
			$Eva15=HTracer::LogRound($Eva,1.5);
			$Max1ID=$PagesMax1ID[$CS];
			if(!$Max1ID)
				$Max1ID=0;

			$Max2ID=$PagesMax2ID[$CS];
			if(!$Max2ID)
				$Max2ID=0;

			$Max1Q=$PagesMax1Q[$CS];
			$Max2Q=$PagesMax2Q[$CS];
			$VALUES[]="('$URL','$CS',$Eva,$Eva15,'$Max1Q','$Max2Q')";
			if($i%1000===0)
			{
				$VALUES=JOIN(" , \n",$VALUES);
				htracer_mysql_query("INSERT INTO `$tablename` (`URL`,`URL_CS`,`Eva`,`Eva15`,`FirstKey`,`SecondKey`) VALUES $VALUES
										ON DUPLICATE KEY UPDATE `Eva`=`Eva`");
				$VALUES=Array();
			}
		}
		unset($Pages);
		if(count($VALUES))
		{
			$VALUES=JOIN(" , \n",$VALUES);
			htracer_mysql_query("INSERT INTO `$tablename` (`URL`,`URL_CS`,`Eva`,`Eva15`,`FirstKey`,`SecondKey`) VALUES $VALUES 
									ON DUPLICATE KEY UPDATE `Eva`=`Eva`");
		}
		//echo '<pre>'; 
		HTracer::OptimizeTables();
		echo '<h1>HTracer :: Data Base was converted from 2.x version to 3.x version</h1>';
		HTracer_Out();
	}
	static function OptimizeTables()
	{
		set_time_limit(1000);
		if(!$GLOBALS['htracer_mysql'])
			return;
		$table_prefix=HTracer::GetTablePrefix();
		$tablename1=$table_prefix."htracer_pages";
		$tablename2=$table_prefix."htracer_queries";
		$query="LOCK TABLES `$tablename1` WRITE,`$tablename2` WRITE";
		if(!htracer_mysql_query($query,'LOCK_TABLES',false))
			return false;
		$res1=htracer_mysql_query("OPTIMIZE TABLE `$tablename1`",'OPTIMIZE_TABLE_1',false);
		$res2=htracer_mysql_query("OPTIMIZE TABLE `$tablename2`",'OPTIMIZE_TABLE_2',false);
		
		$res4=htracer_mysql_query('UNLOCK TABLES','UNLOCK_TABLES',false);
		
		return $res1 && $res2 && $res4;
	}
	static function get_page_meta($URL=false,$Field=false)
	{
		if(!isset($GLOBALS['htracer_usp']) || !$GLOBALS['htracer_usp'])
			return NULL;
		if(!isset($GLOBALS['pages_meta']))
			$GLOBALS['pages_meta']=Array();
		if($URL===false)
		{
			$URL=$_SERVER["REQUEST_URI"];
			if(getenv("REQUEST_URI"))
				$URL=getenv("REQUEST_URI");
		}
		if(!isset($GLOBALS['pages_meta'][$URL]))
		{
			$GLOBALS['pages_meta'][$URL]=Array();
			$URL_CS=MD5($URL);
			$table_prefix=HTracer::GetTablePrefix();
			//$table_prefix."htracer_page_meta
			if(isset($GLOBALS['htracer_usp']) && $GLOBALS['htracer_usp'])
			{
				$res=htracer_mysql_query("	
						SELECT `Name`,`Value` 
						FROM `{$table_prefix}htracer_page_meta`
						WHERE `URL_CS`='$URL_CS'",'get_page_meta');
			}
			while($cur=mysql_fetch_array($res))
				$GLOBALS['pages_meta'][$URL][$cur['Name']]=unserialize($cur['Value']);
		}
		if($Field===false)
			return $GLOBALS['pages_meta'][$URL];
		if(isset($GLOBALS['pages_meta'][$URL][$Field]))
			return $GLOBALS['pages_meta'][$URL][$Field];
		return NULL;
	}
	static function add_page_meta($URL,$Data,$Value='ht_no_val')
	{
		if(!isset($GLOBALS['htracer_usp']) || !$GLOBALS['htracer_usp'])
			return NULL;
		if($Value!=='ht_no_val')
			$Data=Array($Data => $Value);
		if($URL===false)
		{
			$URL=$_SERVER["REQUEST_URI"];
			if(getenv("REQUEST_URI"))
				$URL=getenv("REQUEST_URI");
		}
		$Changed=$Data;
		$Was=$Data;
		if(isset($GLOBALS['pages_meta']) && isset($GLOBALS['pages_meta'][$URL]))
		{
			foreach($Data as $Key => $Value)
			{
				if(isset($GLOBALS['pages_meta'][$URL][$Key]) && $GLOBALS['pages_meta'][$URL][$Key]===$Value)
				{
					unset($Changed[$Key]);
					unset($Was[$Key]);
				}
				else
				{
					$GLOBALS['pages_meta'][$URL][$Key]=$Value;
					if(!isset($GLOBALS['pages_meta'][$URL][$Key]))
						unset($Was[$Key]);
				}
			}
		}
		if(count($Changed))
		{
			$URL_CS=MD5($URL);
			$table_prefix=HTracer::GetTablePrefix();
			$Keys=array_keys($Was);
			foreach($Keys as $i => $Key)
				$Keys[$i]=mysql_real_escape_string($Key);
			$Keys="'".JOIN("', '",$Keys)."'";
			if(isset($GLOBALS['htracer_usp']) && $GLOBALS['htracer_usp'])
			{
				htracer_mysql_query("	
					DELETE FROM `{$table_prefix}htracer_page_meta`
					WHERE `URL_CS`='$URL_CS'
					 AND `Name` IN ($Keys) ",'add_page_meta_1');
			}
			$Values=Array();
			foreach($Changed as $Key => $Value)
			{
				$Key=mysql_real_escape_string($Key);
				$Value=mysql_real_escape_string(serialize($Value));
				$Values[] = " ('$URL_CS', '$Key', '$Value') ";
			}
			$Values=join(',',$Values);
			if(isset($GLOBALS['htracer_usp']) && $GLOBALS['htracer_usp'])
			{
				htracer_mysql_query("	
						INSERT INTO `{$table_prefix}htracer_page_meta` 
						(`URL_CS`,`Name`,`Value`)
						VALUES $Values",'add_page_meta_2');
			}
		}	
	}
	static function CreateTables()
	{
		if(!isset($GLOBALS['htracer_mysql'])||!$GLOBALS['htracer_mysql'])
			return;
		HTracer_In();
		$table_prefix=HTracer::GetTablePrefix();
		$tablename1=$table_prefix."htracer_pages";
		$query="
			CREATE TABLE IF NOT EXISTS `$tablename1` (
				`ID` mediumint(9) unsigned 		NOT NULL AUTO_INCREMENT,
				`URL` text COLLATE utf8_bin 	NOT NULL 			 	COMMENT 'PAGE URI (/page1.html)',
				`URL_CS` varbinary(32) 			NOT NULL 			 	COMMENT 'MD5 of URI',
				`Eva` float unsigned 			NOT NULL DEFAULT '0' 	COMMENT 'Weigth of page (summ keys weigth)',
				`Eva15` mediumint(9) unsigned 	NOT NULL DEFAULT '0' 	COMMENT 'LogRound(1.5, Eva)',
				`FirstKey` varbinary(255) 		NOT NULL 				COMMENT 'Source of key with maximum weigth',
				`SecondKey` varbinary(255) 		NOT NULL 				COMMENT 'Source of second key with max weigth',
				`ShowInCloud` tinyint(1) 		NOT NULL DEFAULT '1' 	COMMENT 'show this page in cloud',
				`ShowATitle` tinyint(1) 		NOT NULL DEFAULT '1' 	COMMENT 'add alts to this page',
				`isFirstKeysSetByUser` tinyint(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`ID`),
				#UNIQUE KEY `URL_CS` 	(`URL_CS`),
				UNIQUE KEY `A_Title` 	(`URL_CS`,`ShowATitle`,`FirstKey`,`SecondKey`),
				KEY `Cloud` 			(`ShowInCloud`,`Eva15`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
		htracer_mysql_query($query,'CreateTables_1');
		
		$tablename2=$table_prefix."htracer_queries";
		$query="
			CREATE TABLE IF NOT EXISTS `$tablename2` (
				`ID` int(11) unsigned 			NOT NULL AUTO_INCREMENT,
				`In` varbinary(255) 			NOT NULL 				COMMENT 'Key String',
				`Out` varbinary(255) 			NOT NULL 				COMMENT 'Sanitarized Key String',
				`Eva` float 					NOT NULL DEFAULT '0' 	COMMENT 'Weigth',
				`URL_CS` varbinary(32) 			NOT NULL 				COMMENT 'Link to htracer_pages',
				`OutEva` float 					NOT NULL DEFAULT '0' 	COMMENT 'Current weigth of Key	(updated when htracer_pages.Eva15 changed)',
				`Version` smallint(6) unsigned 	NOT NULL DEFAULT '0' 	COMMENT 'ID of HTracer version that change Out or Status. User=10000',
				`Status`  tinyint(4) unsigned 	NOT NULL DEFAULT '1' 	COMMENT '0 - disabled, 1 - enabled, 2 - moved to another page',
				`ShowInCLinks`  tinyint(1) 		NOT NULL DEFAULT '1' 	COMMENT 'Is this key Enabled in forming contex links',
				
				PRIMARY KEY (`ID`),
				UNIQUE KEY `KeyAndURL` 	(`In`,`URL_CS`),
				#KEY `In` 				(`In`),
				KEY `OutEva` 			(`OutEva`),
				KEY `Status` 			(`Status`),
				KEY `CurentPage` 		(`URL_CS`,`Status`,`OutEva`,`Out`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
		htracer_mysql_query($query,'CreateTables_2');
		
		if(isset($GLOBALS['htracer_usp']) && $GLOBALS['htracer_usp'])
		{
			$tablename3=$table_prefix."htracer_page_meta";
			$query="
				CREATE TABLE IF NOT EXISTS `$tablename3` (
					`ID` int(11) unsigned 					NOT NULL AUTO_INCREMENT,
					`URL_CS` varbinary(32) 					NOT NULL 				COMMENT 'Link to htracer_pages',
					`Name`   varbinary(32) 					NOT NULL 				COMMENT 'Parameter Name',
					`Value`  varbinary(512) 				NOT NULL,
					
					PRIMARY KEY (`ID`),
					UNIQUE KEY `PageAndPar`	(`URL_CS`,`Name`),
					KEY `URL_CS` 			(`URL_CS`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
			htracer_mysql_query($query,'CreateTables_meta');
		}
		if(isset($GLOBALS['htracer_ulink_plugin']) && $GLOBALS['htracer_ulink_plugin'])
		{
			$tablename3=$table_prefix."htracer_ulinks";
			$query="
				CREATE TABLE IF NOT EXISTS `$tablename3` (
					`ID` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`Key` varbinary(255) NOT NULL ,
					`aURL` TEXT NOT NULL ,
					`Don` TEXT NOT NULL ,
					`DON_CS` VARCHAR( 32 ) NOT NULL ,
					INDEX ( `DON_CS` )
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
			htracer_mysql_query($query,'CreateTables_meta');
		}		
		HTracer_Out();
	}
//Добавление в БД Ключей
	static function AddQueriesToDB($Queries0)
	{//Добавление большого числа ключей (группировка)
		set_time_limit(600);
		$optimize_tables=$GLOBALS['htracer_mysql_optimize_tables'];
		$GLOBALS['htracer_mysql_optimize_tables']=false;

		$Queries=Array();
		//echo 'Queries0=<pre>';
		//print_r($Queries0);

		foreach($Queries0 as $Q)
		{
			$key=$Q['key'].'#url#'.$Q['URL'];
			if(!isset($Queries[$key]))
				$Queries[$key]=$Q;
			else
				$Queries[$key]['count']+=$Q['count'];
		}
		//echo 'Queries=<pre>';
		//print_r($Queries);
		foreach($Queries0 as $Q)
			HTracer::AddQueryToDB($Q['key'],'','',$Q['count'],$Q['URL']);
		$GLOBALS['htracer_mysql_optimize_tables']=$optimize_tables;
		if($GLOBALS['htracer_mysql_optimize_tables'])
			HTracer::OptimizeTables();	
	}	
	static function GetCookieName()
	{
		return 'a'.md5(dirname(__FILE__) . $_SERVER['SERVER_NAME']);
	}
	static function HaveCookie()
	{
		return (isset($_COOKIE[HTracer::GetCookieName()]) && $_COOKIE[HTracer::GetCookieName()]);
	}
	static function RemoveCookie()
	{
		if(HTracer::HaveCookie())
		{
			if(headers_sent())
			{
				if(isset($GLOBALS['htracer_test']) && $GLOBALS['htracer_test'])
					echo ' <!--noindex--><Br /><B>HTracer:: Cant remove coockie. Headers was already sent. Please check place of HTracer install code</B><Br /><!--/noindex-->';
			}
			else
				setcookie(HTracer::GetCookieName(),"0",time() - 60*60,'/');
		}
	}
	static function AddCookie($Value)
	{
		if(isset($GLOBALS['ht_admin_page']) && $GLOBALS['ht_admin_page'])
			return;
		if(headers_sent())
		{
			if(isset($GLOBALS['htracer_test']) && $GLOBALS['htracer_test'])
				echo '<!--noindex--><Br /><B>HTracer:: Cant add coockie. Headers was already sent. Please check place of HTracer install code</B><Br /> <!--/noindex-->';
		}
		else
			setcookie(HTracer::GetCookieName(),$Value,time() + 24 * 60 * 60,'/');
	}
	static function AddBonus($Value)
	{
		$Value*=HTracer::CalcMounthK();
		$NeedCookies=((isset($GLOBALS['htracer_trace_view_depth'])   && $GLOBALS['htracer_trace_view_depth'])
				 	 ||(isset($GLOBALS['htracer_trace_use_targets']) && $GLOBALS['htracer_trace_use_targets']));
		if(!$NeedCookies)
		{
			if(isset($GLOBALS['htracer_test']) && $GLOBALS['htracer_test'])
				echo '<!--noindex--><Br /><B>HTracer::AddBonus() not avalible when option "Bonus for target pages" is switched off</B><Br /><!--/noindex-->';
			return;
		}
		if(!$Value || !HTracer::HaveCookie())
			return;
		$table_prefix=HTracer::GetTablePrefix();
		$ID=$_COOKIE[HTracer::GetCookieName()];
		if(!is_numeric($ID))
			return;
		$Key=htracer_mysql_query("SELECT * from `{$table_prefix}htracer_queries` WHERE `ID`='$ID'",'AddBonus_1');
		if(!mysql_num_rows($Key))
			return;
		$Key=mysql_fetch_array($Key);	
		$URL_CS=$Key['URL_CS'];
		
		$Value = str_replace(',','.',$Value.'');
		
		$Key=htracer_mysql_query("
			UPDATE `{$table_prefix}htracer_queries`
				SET `Eva`=`Eva` + $Value
			WHERE `ID`='$ID'"
		,'AddBonus_2');
		HTracer::RefreshPage($URL_CS,true);				
	}
	static function CalcMounthK()
	{
		if(isset($GLOBALS['htracer_trace_runaway']) && $GLOBALS['htracer_trace_runaway'] && $GLOBALS['htracer_trace_runaway']!=1
		&& isset($GLOBALS['htracer_trace_runaway_start_time']) && $GLOBALS['htracer_trace_runaway_start_time'] && $GLOBALS['htracer_trace_runaway_start_time']!=0)
		{
			$delta=$GLOBALS['htracer_trace_runaway'] * ((time() - $GLOBALS['htracer_trace_runaway_start_time']) / (3600 * 24 * 30));
			if($delta && $delta>1)
				return $delta;
		}
		return 1;
	}
	static function AddQuery()
	{//Добавление текущего ключа (ключа по которому перешел пользователь на страницу)
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		$refer=false;
		if(isset($_SERVER['HTTP_REFERER']))
			$refer=$_SERVER['HTTP_REFERER'];
		$NeedCookies=((isset($GLOBALS['htracer_trace_view_depth'])   && $GLOBALS['htracer_trace_view_depth'])
				 	 ||(isset($GLOBALS['htracer_trace_use_targets']) && $GLOBALS['htracer_trace_use_targets']));
		if(!$refer)
		{
			if($NeedCookies)
				HTracer::RemoveCookie();
			HTracer_Out();
			return;
		}
		if(strpos($refer,'http://'.$_SERVER['SERVER_NAME'])===0     || strpos($refer,'https://'.$_SERVER['SERVER_NAME'])===0
		|| strpos($refer,'http://www.'.$_SERVER['SERVER_NAME'])===0 || strpos($refer,'https://www.'.$_SERVER['SERVER_NAME'])===0)
		{
			if($NeedCookies && HTracer::HaveCookie())
			{
				$Bonus=0;
				if(isset($GLOBALS['htracer_trace_view_depth']))
					$Bonus=$GLOBALS['htracer_trace_view_depth'];
				for($i=1;$i<=5;$i++)
				{
					if(!isset($GLOBALS["htracer_trace_p{$i}_url"]) || !isset($GLOBALS["htracer_trace_p{$i}_bonus"]))
						continue;
					$cu=$GLOBALS["htracer_trace_p{$i}_url"];
					$cb=$GLOBALS["htracer_trace_p{$i}_bonus"];
					if(!$cb || !$cu || $cu==='/')
						continue;
					if($_SERVER["REQUEST_URI"]===$cu 
					||($cb{strlen($cb)-1}==='*' 
						&& strpos($_SERVER["REQUEST_URI"],substr($cb,0,strlen($cb)-1))===0))
					{
						$Bonus+=$cb;
						break;
					}
				}
				HTracer::AddBonus($Bonus);
			}
			HTracer_Out();
			return;
		}
		$SE='';	
		//если пользователь нажмет F5, то запрос повторно не запишеться	
		$filename=dirname(__FILE__).'/last_refer.txt';
		if(file_exists($filename) && trim(file_get_contents($filename))==trim($refer).HTracer::GetRealIp())
		{
			HTracer_Out();
			return;
		}
		$f=fopen($filename,'w');
		fputs($f,trim($refer).HTracer::GetRealIp());
		fclose($f);
		
		$refQuery='';
		//$GLOBALS['htracer_doubles_not_first_serp_page']
		$is_first_serp_page=true;
		if(stripos($refer,'http://www.google')!==false||stripos($refer,'http://google')!==false)
		{
			$arr=explode('&q=',$refer);
			if(count($arr)<2)
			$arr=explode('?q=',$refer);
			$arr=explode('&',$arr[1]);
			if($arr[0])		
				$refQuery=urldecode($arr[0]);
			$SE='Google';
			if(stripos($refer,'&start=')!==false && stripos($refer,'&start=0')===false)
				$is_first_serp_page=false;
		}
		else if(stripos($refer,'http://yandex')!==false||stripos($refer,'http://www.yandex')!==false)
		{
			$arr=explode('&text=',$refer);
			if(count($arr)<2)
				$arr=explode('?text=',$refer);
			$arr=explode('&',$arr[1]);
			if($arr[0])		
				$refQuery=urldecode($arr[0]);
			//$refQuery
			$enc=mb_detect_encoding($refQuery,Array('utf-8','cp1251'));
			if(strtolower($enc)!='utf-8' && strtolower($enc)!='utf8')
			{
				$refQuery2=mb_convert_encoding($refQuery,'utf-8',$enc);
				if(!HTracer::isSpecQuery($refQuery2) || HTracer::isSpecQuery($refQuery))
					$refQuery=$refQuery2;
			}
			$SE='Yandex';
			if((stripos($refer,'yandsearch?p=')!==false  || stripos($refer,'&p=')!==false)
			 &&(stripos($refer,'yandsearch?p=0')===false && stripos($refer,'&p=0')===false))
				$is_first_serp_page=false;
		}
		else if(stripos($refer,'http://rambler')!==false||stripos($refer,'http://www.rambler')!==false||stripos($refer,'http://nova.rambler')!==false)
		{
			$arr=explode('&query=',$refer);
			if(count($arr)<2)
				$arr=explode('?query=',$refer);
			$arr=explode('&',$arr[1]);
			if($arr[0])		
				$refQuery=urldecode($arr[0]);
			$SE='Rambler';
			if(stripos($refer,'&page=')!==false && stripos($refer,'&page=1&')===false)
				$is_first_serp_page=false;
		}
		else if(stripos($refer,'http://mail.ru')!==false||stripos($refer,'http://www.mail.ru')!==false||stripos($refer,'http://go.mail.ru')!==false)
		{
			$arr=explode('&q=',$refer);
			if(count($arr)<2)
				$arr=explode('?q=',$refer);
			$arr=explode('&',$arr[1]);
			if($arr[0])		
			{
				$refQuery0=urldecode($arr[0]);
				if(function_exists('iconv'))
					$refQuery=iconv('windows-1251','utf-8', $refQuery0);
				else
					$refQuery=mb_convert_encoding($refQuery0, 'utf-8','windows-1251');
				if(strpos($refQuery,'????')!==false  || strpos($refQuery,'?? ??')!==false
 				 ||strpos($refQuery,'??? ?')!==false || strpos($refQuery,'? ???')!==false)
					$refQuery=$refQuery0;
			}
			$SE='Mail.ru';
			if(stripos($refer,'&sf=')!==false && stripos($refer,'&sf=0')===false)
				$is_first_serp_page=false;
		}
		else if(stripos($refer,'http://search.yahoo')!==false)
		{
			$arr=explode('?p=',$refer);
			if(count($arr)<2)
				$arr=explode('&p=',$refer);
			$arr=explode('&',$arr[1]);
			if($arr[0])
				$refQuery=urldecode($arr[0]);
			$SE='yahoo.com';
			if((stripos($refer,'&b='      )!==false || stripos($refer,    '&b=')!==false) 
			 &&(stripos($refer.'&','&b=1&')===false && stripos($refer.'&','?b=1&')===false))
				$is_first_serp_page=false;
		}
		$k=1;
		if(!$is_first_serp_page && $GLOBALS['htracer_trace_double_not_first_page'])
			$k*=2;	
		if(function_exists('htracer_api_weight_filter'))
			$k=htracer_api_weight_filter($k,$refQuery,$is_first_serp_page,$SE,$refer);
		
		if((isset($_GET['utm_source']) && $_GET['utm_source'])||// Маркеры гугл аналитикс
			isset($_GET['_openstat'])||// Маркер Яндекс-директа
			isset($_GET['gclid']))// Маркер Google adwords
		{
			HTracer_Out();
			return;
		}
		if($refQuery && $k!==0 && $k!=='0' && mb_strlen($refQuery,'utf-8')<70
		&& hkey_get_numbers_count($Query)<=5 && count(explode(' ',$Query))<=7)
		{
			$k*=HTracer::CalcMounthK();
			$GLOBALS['htracer_remember_last_id_in_cookie']=true;
			HTracer::AddQueryToDB($refQuery,$SE,$refer,$k,false,true,true);
			unset($GLOBALS['htracer_remember_last_id_in_cookie']);
		}
		HTracer_Out();	
	}
	static function FixURL($URL,$OnlyCurDomain=false)
	{
		$URL=trim($URL);
		if($OnlyCurDomain)
		{
			$lURL=strtolower($URL);
			$Domain=strtolower($_SERVER['SERVER_NAME']);
			if(strpos($Domain,'www.')===0)
				$Domain=substr($Domain,4);
			
			if(strpos($lURL,$Domain.'/')===0)
				return substr($URL,strlen($Domain));
				
			elseif(strpos($lURL,'www.'.$Domain.'/')===0)
				return substr($URL,strlen('www.'.$Domain));
				
			elseif(strpos($lURL,'http://'.$Domain.'/')===0)
				return substr($URL,strlen('http://'.$Domain));
			
			elseif(strpos($lURL,'http://www.'.$Domain.'/')===0)
				return substr($URL,strlen('http://www.'.$Domain));
				
			elseif(strpos($lURL,'https://'.$Domain.'/')===0)
				return substr($URL,strlen('https://'.$Domain));
				
			elseif(strpos($lURL,'https://www.'.$Domain.'/')===0)
				return substr($URL,strlen('https://www.'.$Domain));
				
			return $URL;
		}
		if(strpos($URL,'http://')===0)
		{
			$URL=str_replace('http://','',$URL);
			$URL=explode('/',$URL,2);
			if(isset($URL[1]))
				$URL='/'.$URL[1];
			else
				$URL='/';
		}
		elseif(is_string($URL) && isset($URL{0}))
		{
			if($URL{0}!='/')
			{
				$parts=explode('/',$URL,2);
				if(isset($parts[1]))
				{
					if(isset($_SERVER) && isset($_SERVER['SERVER_NAME']) && $parts[0]===$_SERVER['SERVER_NAME'])
						$URL='/'.$parts[1];
					else
					{
						$parts2=explode('.',$parts[0]);
						$end=$parts2[count($parts2)-1];
						if($end==='ru'||$end==='com'||$end==='net'||$end==='ua'||$end==='org'||$end==='su')
							$URL='/'.$parts[1];
					}
				}
			}
		}
		return $URL;
	}
	static function HaveResponseCode($Code='404')
	{
		$headers=headers_list();
		if(!$headers)
			return false;
		
		if(function_exists('stripos'))
		{
			foreach($headers as $header)
			{
				if(stripos($header,"Status: $Code")!==false
				|| stripos($header,"Status:$Code")!==false)
					return true;
				elseif($Code!='301') 
				{
					if(stripos($header,"Status:")!==false)
						return false;
				}
				elseif(stripos($header,"Location:")!==false)
					return true;
			}		
		}
		else
		{
			foreach($headers as $header)
			{
				if(strpos($header,"Status: $Code")!==false
				|| strpos($header,"Status:$Code")!==false
				|| strpos($header,"status: $Code")!==false
				|| strpos($header,"status:$Code")!==false
				|| strpos($header,"STATUS: $Code")!==false
				|| strpos($header,"STATUS:$Code")!==false)
					return true;
				elseif($Code!='301') 
				{
					if(strpos($header,"Status:")!==false)
						return false;
				}
				elseif(strpos($header,"Location:")!==false||strpos($header,"location:")!==false)
					return true;
			}		
		}
		return false;
	}
	static function AddQueryToDB($refQuery,$SE='ga',$SE_URL='',$Count=1,$URL=false,$CheckQuery=true
	,$AllowGrooping=false)
	{
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		
		if(strpos($refQuery,'<')!==false)
			return false;
		
		if($URL===false)
		{
			$URL=getenv("REQUEST_URI");
			if(!$URL)
				$URL=$_SERVER["REQUEST_URI"];
			if(HTracer::HaveResponseCode('404')||HTracer::HaveResponseCode('301'))
			{
				HTracer_Out();
				return;
			}
		}
		else
			$URL=HTracer::FixURL($URL);

		if(strpos($URL,'/q=cache:')!==false //кеш Google
		 ||strpos($URL,'yandbtm?fmode=inject&url=')!==false//кеш Яндекса 
		 ||strpos($URL,'search?DocId=')!==false//кеш Апорта 
		 ||strpos($URL,'cache?hilite=')!==false//кеш Рамблера 
		 ||strpos($URL,'/search/srpcache?')!==false//кеш Яху  
         ||strpos($URL,'/r/_ylt=')!==false//кеш Яху 
		 ||strpos($URL,'/favicon.ico')!==false
		//Поиск	
		 ||strpos($URL,'/search')===0
		 ||strpos($URL,'/?search=')===0
		 ||strpos($URL,'?search=')===0
		 ||strpos($URL,'?s=')===0
		 ||strpos($URL,'/?s=')===0

		 ||$URL==='/404.php'||$URL==='/403.php'||$URL==='/404'
		 ||$URL==='/404.htm'||$URL==='/403.htm'||$URL==='/403'
		 ||$URL==='/404.html'||$URL==='/403.html')
		 {
			HTracer_Out();
			return;
		 }
			
		if($AllowGrooping && $GLOBALS['htracer_mysql']
		&& isset($GLOBALS['htracer_trace_grooping'])
		&& $GLOBALS['htracer_trace_grooping']
		&& is_numeric($GLOBALS['htracer_trace_grooping'])		
		&& intval($GLOBALS['htracer_trace_grooping'])>1
		&& $GLOBALS['htracer_trace_grooping']!==1
		&& $GLOBALS['htracer_trace_grooping']!=='1')
		{
			$Queries=Array();
			if(file_exists(dirname(__FILE__).'/query/grouped_queries.txt'))
				$Queries=unserialize(file_get_contents(dirname(__FILE__).'/query/grouped_queries.txt'));
			$Queries[]=Array(
				'key'=>$refQuery,
				'count'=>$Count,
				'URL'=>$URL
			);
			$GLOBALS['htracer_trace_grooping']=intval($GLOBALS['htracer_trace_grooping']);
			$rand=abs(rand() + crc32($_SERVER["REQUEST_URI"].$refQuery.$SE_URL));
			if(!$GLOBALS['htracer_only_night_update'])
				$rand=$rand%(round($GLOBALS['htracer_trace_grooping']/10 + 10));
			else
				$rand=$rand%(round($GLOBALS['htracer_trace_grooping']/70 + 2));
			
			//echo count($Queries).'<br />';
			$H=intval(date('G'));
			if($rand===1 && count($Queries)>=$GLOBALS['htracer_trace_grooping']
			&&(!$GLOBALS['htracer_only_night_update']|| ($H>=1 && $H<6)))
			{
				file_put_contents(dirname(__FILE__).'/query/grouped_queries.txt',serialize(Array()));
				HTracer::AddQueriesToDB($Queries);	
			}
			else			
				file_put_contents(dirname(__FILE__).'/query/grouped_queries.txt',serialize($Queries));
			HTracer_Out();	
			return;	
		}
		$refQuery=trim($refQuery);
		if(function_exists('htracer_api_query_pre_filter'))
			$refQuery=htracer_api_query_pre_filter($refQuery);
		if($CheckQuery && !$GLOBALS['htracer_mysql'] && HTracer::isSpecQuery($refQuery))
		{
			HTracer_Out();
			return;
		}
		if(!$refQuery||!trim($refQuery,'? '))
		{
			HTracer_Out();
			return;
		}
		$Count=intval($Count);
		static $ComWords=Array
		(
			' купить ',' продаж',
			' магазин',' интернет-магазин',' интернет магазин',
			' цена',' цене',' цены','стоимост',
			' кредит ',' расрочк',' рассрочк',
			' со склада ',' опт',' розниц',
			' заказать',' заказ ',' доставк',
			' скидк',' недорог',
			'бронировать ',' бронирование '
		);
		//http://www.google.com/complete/search?hl=en&js=true&qu=fast%20bug
	
		$CheckQuery=' '.$refQuery.' ';

		
		if($Count>0 && $GLOBALS['htracer_trace_double_comercial_query']
		&& str_replace($ComWords,'',$CheckQuery)!=$CheckQuery)
			$Count*=2;
		if($GLOBALS['htracer_mysql'])
			HTracer::AddQueryToDB_MySQL($refQuery,$Count,$URL);
		else
			HTracer::AddQueryToDB_Files($refQuery,$SE,$SE_URL,$Count,$URL);
		HTracer_Out();	
	}
	static function RefreshPage($URL=false,$is_url_cs=false,$forced=false)
	{//Обновляет данные страницы, исходя из ее ключей
		if(!$is_url_cs)
		{
			if($URL===false)
				$URL=$_SERVER["REQUEST_URI"];
			$URL_CS=MD5($URL);
		}
		else
			$URL_CS=$URL;
		$table_prefix=HTracer::GetTablePrefix();
		$Page=htracer_mysql_query("SELECT * from `{$table_prefix}htracer_pages` WHERE `URL_CS`='$URL_CS' LIMIT 1",'RefreshPage_1');
		if(mysql_num_rows($Page))
			$Page=mysql_fetch_assoc($Page);
		else
			$Page=false;
		if(!mysql_num_rows(htracer_mysql_query(
			"SELECT * from `{$table_prefix}htracer_queries` WHERE `URL_CS`='$URL_CS' LIMIT 1"
		,'RefreshPage_2')))
		{
			if($Page)
			{
				//htracer_mysql_query("DELETE FROM {$table_prefix}htracer_pages WHERE `URL_CS`='$URL_CS' LIMIT 1",'RefreshPage_3');
				htracer_mysql_query("
					UPDATE `{$table_prefix}htracer_pages`
					SET 
						`Eva`			= '0'
						`Eva15`			= '0'
					WHERE
						`URL_CS`			= '$URL_CS'"
					,'RefreshPage_3');
			}
			return;
		}
		
		//Выбираем ключи страницы чтобы посчитать сумму весов и найти два максимальный ключа
		$Queries=htracer_mysql_query("SELECT * from `{$table_prefix}htracer_queries` 
									  WHERE `URL_CS`='$URL_CS' 
									    AND `Status`=1 
										AND `Eva`!=0
									  ORDER BY `Eva` DESC 
									  LIMIT 1000",'RefreshPage_4');
		$Eva=0;
		$i=0;
		$Key1='';
		$Key2='';
		
		while($Key = mysql_fetch_array($Queries))
		{
			$Eva+=intval($Key['Eva']);
			if($i==0)
				$Key1=mysql_real_escape_string($Key['Out']);
			if($i==1)
				$Key2=mysql_real_escape_string($Key['Out']);
			$i++;
		}
		$Eva15=HTracer::LogRound($Eva,1.5);
		if(!$Page || $Eva15!=HTracer::LogRound(intval($Page['Eva15']),1.5)||$forced)
		{
			//Порог пройден, необходим полный апдейт
			
			//Обновляем ключи страницы
			htracer_mysql_query("
				UPDATE `{$table_prefix}htracer_queries`
				SET 
					`OutEva`			= `Eva`
				WHERE
					`URL_CS`			= '$URL_CS'"
			,'RefreshPage_5');

			//Обновляем страницу
			if($Page)
			{
				if($Page['isFirstKeysSetByUser']
				|| !isset($Page['FirstKey'])
				|| !isset($Page['SecondKey'])
				|| !($Page['FirstKey'])
				|| !($Page['SecondKey'])
				|| HTracer::isSpecQuery($Page['FirstKey'])
				|| HTracer::isSpecQuery($Page['SecondKey']))
				{
					htracer_mysql_query(
						"UPDATE `{$table_prefix}htracer_pages`
							SET 
								`Eva`			= $Eva,
								`Eva15`			= $Eva15,
								`FirstKey`		= '$Key1',
								`SecondKey`		= '$Key2'
							WHERE
								`URL_CS`		= '$URL_CS'
							LIMIT 1
						",'RefreshPage_6_1');
				}
				else
				{
					htracer_mysql_query(
						"UPDATE `{$table_prefix}htracer_pages`
							SET 
								`Eva`			= $Eva,
								`Eva15`			= $Eva15
							WHERE
								`URL_CS`		= '$URL_CS'
							LIMIT 1
						",'RefreshPage_6_2');
				}
			}
		}
		elseif($Page)
		{
			htracer_mysql_query(
				"UPDATE `{$table_prefix}htracer_pages`
					SET 
						`Eva`			=  $Eva
					WHERE
						`URL_CS`		= '$URL_CS'
					LIMIT 1
			",'RefreshPage_7');
		}
		
		if(!$Page && !$is_url_cs)
		{
			$URL=mysql_real_escape_string($URL);
			htracer_mysql_query(
				"INSERT INTO `{$table_prefix}htracer_pages`
					SET 
						`Eva`			= $Eva,
						`Eva15`			= $Eva,
						`URL`			= '$URL',
						`URL_CS`		= '$URL_CS',
						`FirstKey`		= '$Key1',
						`SecondKey`		= '$Key2'
			",'RefreshPage_8');
		}
	}


	static function AddQueryToDB_MySQL($Key,$Eva=1,$URL=false,$IsUser=false,$Out=false,$Status=false,$ShowInCLinks=1)
	{	
		$ShowInCLinks=intval($ShowInCLinks);
		if($URL===false)
			$URL=$_SERVER["REQUEST_URI"];
		elseif(stripos($URL,'http://')===0)
			$URL=str_replace(HTracer::SiteUrl(),'',$URL);
		$URL_CS=MD5($URL);
		$table_prefix=HTracer::GetTablePrefix();
		$Key=HTracer::NormalizeQuery($Key);
		if(!$Key)
			return false;
		$In=mysql_real_escape_string($Key);
		
		$Page=htracer_mysql_query("SELECT * from `{$table_prefix}htracer_pages` WHERE `URL_CS`='$URL_CS' LIMIT 1",'AddQueryToDB_MySQL_4');
		
		if(mysql_num_rows($Page))
			$Page=mysql_fetch_assoc($Page);
		else
			$Page=false;
		
		if($Page)
		{
			$Was=htracer_mysql_query("SELECT * from `{$table_prefix}htracer_queries` WHERE `In`='$In' AND `URL_CS`='$URL_CS' LIMIT 1",'AddQueryToDB_MySQL_1');
			if(mysql_num_rows($Was))
				$Was=mysql_fetch_assoc($Was);
			else
				$Was=false;
		}
		else
			$Was=false;
		if($Was && ($Was['Eva']===0||$Was['Eva']==='0'))
			return false;

		$Version=$GLOBALS['htracer_curent_version_id'];
		if($IsUser)
			$Version=10000;
		if(!$Was || $Was['Version']<$Version)
		{//Либо нового ключа либо он в последний раз обновлялся старой версией 
			if($Out)
				$Out=mysql_real_escape_string($Out);
			else
				$Out=mysql_real_escape_string(sanitarize_keyword($Key));
			if($Status===false)
			{
				if((!$Was||$Was['Status']==0) && isset($GLOBALS['htracer_premoderation']) && $GLOBALS['htracer_premoderation'])
					$Status=false;
				elseif(!$Was || $Was['Status']==0 || $Was['Status']==1)
					$Status=intval(!HTracer::isSpecQuery($Key));
				else
					$Status=$Was['Status'];
			}
		}
		else
		{
			if($Was['Out'])
				$Out=mysql_real_escape_string($Was['Out']);
			else
				$Out=mysql_real_escape_string(sanitarize_keyword($Key));
			if($Status===false)
				$Status=$Was['Status'];	
		}
		
		$NeedCookie=(isset($GLOBALS['htracer_remember_last_id_in_cookie']) && $GLOBALS['htracer_remember_last_id_in_cookie']);
		if(!$Was)//Добавляем новый ключ
		{
			if(!$Page)
				$OutEva=0;
			else
				$OutEva=$Eva;
			if($Eva>0)
				htracer_mysql_query(
					"INSERT `{$table_prefix}htracer_queries`
						SET `In`			= '$In',
							`Out`			= '$Out',
							`Eva`			= '$Eva',
							`URL_CS`		= '$URL_CS',
							`OutEva`		=  $OutEva,
							`Status`		= '$Status',
							`Version`		= '$Version',
							`ShowInCLinks` 	= '$ShowInCLinks'
					",'AddQueryToDB_MySQL_2');
			if($NeedCookie)
				HTracer::AddCookie(mysql_insert_id($GLOBALS['htracer_mysql_link']));
		}
		else
		{
			$ID=$Was['ID'];
			htracer_mysql_query(
				"UPDATE `{$table_prefix}htracer_queries`
					SET 
						`Out`			= '$Out',
						`Eva`			= `Eva`+$Eva,
						`Status`		= '$Status',
						`Version`		= '$Version',
						`ShowInCLinks` 	= '$ShowInCLinks'
					WHERE
						`ID`			= $ID
				",'AddQueryToDB_MySQL_3');
			if($NeedCookie)
				HTracer::AddCookie($ID);
		}
		$PageEva=0;
		if($Page)
			$PageEva=$Page['Eva'];
		if($Status)
			$PageEva+=$Eva;
		$Eva15=HTracer::LogRound($PageEva,1.5);
		if(!isset($GLOBALS['ht_in_ga_import']))
			$GLOBALS['ht_in_ga_import']=false;
		if($Page && ($Eva15!=HTracer::LogRound(intval($Page['Eva15']),1.5)||$GLOBALS['ht_in_ga_import']))
		{
			$PID=$Page['ID'];
		
			//ОБНОВЛЯЕМ КЛЮЧИ СТРАНИЦЫ 
			htracer_mysql_query(
				"UPDATE `{$table_prefix}htracer_queries`
					SET 
						`OutEva`			= `Eva`
					WHERE
						`URL_CS`			= '$URL_CS'
				",'AddQueryToDB_MySQL_5');
			
			//Обновляем главные ключи страницы
			$Keys=htracer_mysql_query(
				"SELECT * FROM `{$table_prefix}htracer_queries`
				WHERE
					`URL_CS`			= '$URL_CS'
					 AND `Status`=1 
					 AND `Eva`!=0
				ORDER BY `Eva` DESC
				LIMIT 2
				",'AddQueryToDB_MySQL_5');
				
			//Считаем главные ключи страницы	
			if($Page['isFirstKeysSetByUser']
			|| !isset($Page['FirstKey'])
			|| !isset($Page['SecondKey'])
			|| !($Page['FirstKey'])
			|| !($Page['SecondKey'])
			|| HTracer::isSpecQuery($Page['FirstKey'])
			|| HTracer::isSpecQuery($Page['SecondKey']))
			{
				$n=mysql_num_rows($Keys);
				$First='';
				$Second='';
				if($n)
				{
					$First=mysql_fetch_assoc($Keys);
					$FirstEva=$First['Eva'];
					
					$First=$First['Out'];
					if($n>1)
					{
						$Second=mysql_fetch_assoc($Keys);
						if($Second['Eva']>$FirstEva/4)
							$Second=$Second['Out'];
						else
							$Second='';
					}
				}
			}
			else
			{
				$First =$Page['FirstKey'];
				$Second=$Page['SecondKey'];
			}
			$First =mysql_real_escape_string($First);
			$Second=mysql_real_escape_string($Second);


			htracer_mysql_query(
				"UPDATE `{$table_prefix}htracer_pages`
					SET 
						`Eva`			= $PageEva,
						`Eva15`			= $PageEva,
						`FirstKey`		= '$First',
						`SecondKey`		= '$Second'
					WHERE
						`ID`			= $PID
				",'AddQueryToDB_MySQL_6');

		}
		elseif($Page)
		{
			$PID=$Page['ID'];
			htracer_mysql_query(
				"UPDATE `{$table_prefix}htracer_pages`
					SET 
						`Eva`			= `Eva` + $Eva
					WHERE
						`ID`			= $PID
				",'AddQueryToDB_MySQL_6');
		}
		else
		{
			$URL=mysql_real_escape_string($URL);
			htracer_mysql_query(
				"INSERT `{$table_prefix}htracer_pages`
					SET 
						`Eva`			= $Eva,
						`Eva15`			= $Eva15,
						`URL`			= '$URL',
						`URL_CS`		= '$URL_CS',
						`FirstKey`		= '$Out'
				",'AddQueryToDB_MySQL_7');
		}
	}
	
	
	static function AddQueryToDB_Files($refQuery,$SE,$SE_URL,$QCount=1,$URL=false)
	{
		global $HTracer_Files_Count;
		if(!$HTracer_Files_Count)
			$HTracer_Files_Count=64;
		$refQuery=str_replace(Array("\r","\n"),Array(" "," "),$refQuery);
		if($URL===false)
			$URL=$_SERVER["REQUEST_URI"];
		$URL_CS=MD5($URL);
		$count=HTracer::SelectCountOfKeys($URL_CS);
		
		$ccount=$count+$QCount;
		if($count>7561 && $QCount=1)//7561 простое число  максимум в файлах по 60 строк
		{
			$ccount-= 9 * $HTracer_Files_Count;//9 это тоже простое число
			for($i=0;$i<$HTracer_Files_Count;$i++)
			{	
				$filename = dirname(__FILE__).'/query/'.$URL_CS.'_'.$i.'.txt';
				$strings  = file($filename);
				$f=fopen($filename,'w');
				for($j=9;$j<count($strings);$j++)
					fputs($f,$strings[$j]."\r\n");
				fclose($f);
			}
		}
		$fs=Array();//оптимизация на случай, если файлов $QCount>200
		for($i=0;$i<$QCount;$i++)
		{
			$index=($count+$i)%$HTracer_Files_Count;
			if(isset($fs[$index]))
				$f=$fs[$index];
			else
			{
				$f=fopen(dirname(__FILE__).'/query/'.$URL_CS.'_'.$index.'.txt', 'a');
				$fs[$index]=$f;
			}
			fputs($f,$refQuery."\r\n");
		}
		foreach($fs as $f)	
			fclose($f);
		$f=fopen(dirname(__FILE__).'/query/'.$URL_CS.'_count','w');
		fputs($f,$ccount);
		fclose($f);
		
		
		//foreach()
		$filename=dirname(__FILE__).'/query/all'.abs(crc32($URL_CS)%128).'.txt';
		$all_pages=Array();
		$cpage="$URL_CS|#;#|$URL";
		srand();
		if(rand()%99===1)//чистка файла
		{
			$strings  = @file($filename);
			if($strings && $f=fopen($filename, 'w'))
			{
				for($i=count($strings);$i>0;$i--)
				{
					$cstr=$strings[$i];
					$arr=explode('|#;#|',$cstr,2);
					$page=$arr[1];
					if($cpage==$page||isset($all_pages[$page]))
						continue;
					$all_pages[$page]=1;
					if($cstr[strlen($cstr)]=='\n')
						fputs($f,$cstr."\n");
					else if($cstr[strlen($cstr)]=='\r')
						fputs($f,$cstr."\n");
					else
						fputs($f,$cstr."\r\n");
				}
				fputs($f,"$ccount|#;#|$URL_CS|#;#|$URL"."\r\n");
				fclose($f);
			}
			else if($f=fopen($filename, 'a'))
			{
				fputs($f,"$ccount|#;#|$URL_CS|#;#|$URL"."\r\n");
				fclose($f);
			}
		}
		else if($f=fopen($filename, 'a'))
		{
			fputs($f,"$ccount|#;#|$URL_CS|#;#|$URL"."\r\n");
			fclose($f);
		}
		//$refQuery=HTracer::NormalizeQuery($refQuery);
	}
};
HTracer_Out();
?>