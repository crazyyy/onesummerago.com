<?php
	//библиотека для работы со строками и разбивка строк по пассажам и словам
	//нужна для вставки контекстных ссылок
	// Также здесь функции для работы с контекстными ссылками
	if(!isset($GLOBALS['htracer_site_stop_words']))
		$GLOBALS['htracer_site_stop_words']='';
	if(!isset($GLOBALS['htracer_context_links_class']))
		$GLOBALS['htracer_context_links_class']='alink';
	if(!isset($GLOBALS['htracer_context_links_acceptor_pages'])||!$GLOBALS['htracer_context_links_acceptor_pages']||$GLOBALS['htracer_context_links_acceptor_pages']<=0)
		$GLOBALS['htracer_context_links_acceptor_pages']=300;
	else
		$GLOBALS['htracer_context_links_acceptor_pages']*=1;
	if(!isset($GLOBALS['htracer_context_links_b']))
		$GLOBALS['htracer_context_links_b']=false;
	
	function htracer_get_curent_url()
	{
		$res=getenv("REQUEST_URI");
		if($res)	
			return $res;
		return $_SERVER["REQUEST_URI"];
	}
	
	function htracer_split_words($In) //разбивает строку на слова
	{
		$Arr=explode(' ',$In);
		$Out= Array();
		foreach($Arr as $Word)
		{	
			$Word=trim($Word);
			if($Word && $Word!=='')
				$Out[]=$Word;
		}
		return $Out;
	}
	function htracer_parse_params($In,$Lower=true)
	{	
		error_reporting (E_ERROR | E_PARSE| E_WARNING);
		if(is_array($In))
			return $In;
		if(!is_string($In))
			return Array();
		$In=ltrim($In);
		if($In{0}=='?'||$In{0}=='&')
		{
			$In=substr($In,1);
			$In=ltrim($In);
		}
		$In=explode('&',$In);
		$Res=Array();
		foreach($In as $Cur)
		{
			$Cur=explode('=',$Cur,2);
			if($Lower)
				$Cur[0]=strtolower(trim($Cur[0]));
			$Res[$Cur[0]]=$Cur[1];
		}
		return $Res;
	}
	
	function htracer_form_href($href,$Base)
	{
		$href=trim($href);
		if($href===''||$href===false||$href===0||$href{0}==='#')
			return false;
		$pos=strpos($href,'#');
		if($pos===0)
			return false;
			
		if($pos)//вырезаем символы после # 
			$href=substr($href,0,$pos);
		
		if(stripos($href,'http://')===0)
		{	//нормализуем регистр домена
			$href=substr($href, 7 /*strlen('http://')*/);
			$arr=explode('/',$href,2);
			if(count($arr)==2)
				$href=strtolower($arr[0]).'/'.$arr[1];
			$href='http://'.$href;
		}
		else
		{
			if(strpos($href,':'))
				return false;
			if($href{0}=='/' || $Base{strlen($Base)-1}=='/')
				$href=$Base.$href;
			else
				$href=$Base.'/'.$href;
		}
		return $href; 
	}
	function htracer_find_attrib($html,$pos,$name,$encoding,$len=false)
	{	
		$in_quotes1    		= false;
		$in_quotes2    		= false;
		$in_cur				= false;
		$res				= '';
		//get_atrib_name
		if(!$len)
			$len=strlen($html);//mb_strlen($html,$encoding);
		for($i=$pos;$i<$len;$i++)
		{
			$cur=$html{$i};//mb_substr($html,$i,1,$encoding);
			if(!$in_quotes1 && !$in_quotes2 && ($cur=='>'||$cur=='<'))
				return false;
			if($cur=='"' && !$in_quotes1)
			{
				if(!$in_quotes2)
				{
					if($name==htracer_get_atrib_name($html,$i))
						$in_cur=true;
				}
				else if($in_cur)
					return $res;
				$in_quotes2=!$in_quotes2;
			}
			if($cur=="'" && !$in_quotes2)
			{
				if(!$in_quotes1)
				{
					if($name==htracer_get_atrib_name($html,$i))
						$in_cur=true;
				}
				else if($in_cur)
					return $res;
				$in_quotes1=!$in_quotes1;
			}
			if(($cur!='"'&& $cur!="'") || $res!=='')
				$res.=$cur;
		}	
		return false;
	}

	
	function htracer_pars_to_str($params)
	{//преобразует массив в строку для рассчета CS	
		error_reporting (E_ERROR | E_PARSE| E_WARNING);
		$Res='';
		if(is_string($params))
			return $params;
		foreach($params as $key => $Data)
		{
			$Res.='&'.$key.'=';
			if(is_array($Data))
			{
				foreach($Data as $key2 => $Data2)
					$Res.='&'.$key2.'='.$Data2;
			}
			else
				$Res.=$Data;
		}	
		return $Res;
	}
	function htracer_get_tag_name(&$html,$pos)//токо для английского и спец. символов
	{
		$len=strlen($html);
		$res='';
		for($i=$pos;$i<$len;$i++)
		{
			$cur=$html{$i};
			if($cur===' '||$cur==='>'||$cur==="\n"||$cur==="\t"||($cur==='/' && $res))
				break;
			if($cur!='<')
				$res.=$cur;
		}
		return strtolower(trim($res));
	}
	function htracer_get_atrib_name(&$html,$pos)
	{
		$res='';
		for($i=$pos;$i>=0;$i--)
		{
			$cur=$html{$i};
			if($cur==='>'||$cur==='<')
				break;
			if($cur===' '||$cur==="\r"||$cur==="\n"||$cur==="\t"||$cur==="'"||$cur==='"'||$cur==='=')
			{
				if($res!=='')
					break;
			}
			else
				$res.=$cur;
		}
		$res2=strrev($res);
		return strtolower(trim($res2));
	}
	function htracer_autoends_tag($html,$pos)
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
				return false;
		}
		return false;
	}
	//Проверка доступности кеша по параметрам
	function htracer_is_cash_awalible($URLSensivity)
	{
		if(!$GLOBALS['htracer_cash_days']||$GLOBALS['htracer_cash_days']==-1
		||$GLOBALS['htracer_test'])
			return false;//Кеш выключен или мы находимся в тестовом режиме
		
		if($GLOBALS['htracer_short_cash'] && $URLSensivity)
		{
			$CurURL=$_SERVER['REQUEST_URI'];
			if($CurURL!=='/' && $CurURL!=='/index.php' && $CurURL!=='/index.html' && $CurURL!=='/index.htm')
				return false;
		}
		return true;
	}

	//Чтение кеш файла 
	//Имя файла чек сумм имени функции, URL и числа ее вызовов
	//Первая строка чексумм параметров функции
	//Вторая строка наличие GZip сжатия
	//Третья строка наличие сериализация
	//Четвертая и далее само запомненое значение
	function htracer_read_cash_file($FunName,$ParamsCS=false
									,$CallsSensivity=true,$URLSensivity=true)
	{
		if(!htracer_is_cash_awalible($URLSensivity))
			return NULL;
			
		if($ParamsCS!==false)
			$ParamsCS=MD5($ParamsCS);
		static $FunNames=Array();
		$PlaceCS=$FunName.$_SERVER['SERVER_NAME'];
		if(isset($FunNames[$FunName]) && $CallsSensivity)
		{
			$PlaceCS.='_fun_call_num_'.$FunNames[$FunName];
			$FunNames[$FunName]++;
		}
		else
			$FunNames[$FunName]=1;
		if($URLSensivity)	
			$PlaceCS.='_page_url_is_'.htracer_get_curent_url();
		else
			$PlaceCS.='_no_url_sensivity';
			
		$PlaceCS=MD5($PlaceCS);
		$FunName=strtolower(trim($FunName));
		$CashFile=dirname(__FILE__).'/cash/htc_v2_'.$FunName.'_'.$PlaceCS.'.txt';
		$modif=time()-@filemtime($CashFile);
	
		if($modif<(86400 * $GLOBALS['htracer_cash_days']) && @filesize($CashFile)>5)//Обновляет после 5 Дней
		{
			$content=trim(file_get_contents($CashFile));
			if($content)
			{
				$content=explode("\n",$content,4);
				if($ParamsCS==$content[0] || ($ParamsCS===false && $content[0]==='0'))
				{
					if($content[1])	
					{
						if(!function_exists('gzuncompress'))
							return NULL;
						try{$content[3]=gzuncompress($content[3]);}
						catch (Exception $e){return NULL;}
					}
					if($content[2])	
						$content[3]=unserialize($content[3]);
					return $content[3];
				}
			}
		}
		return NULL;
	}
	function htracer_write_cash_file($Content,$FunName,$ParamsCS=false
									,$CallsSensivity=true,$URLSensivity=true)
	{
		if(!htracer_is_cash_awalible($URLSensivity))
			return;
		if($GLOBALS['htracer_short_cash'] && $URLSensivity)
		{
			$CurURL=$_SERVER['REQUEST_URI'];
			if($CurURL!=='/' && $CurURL!=='/index.php' && $CurURL!=='/index.html' && $CurURL!=='/index.htm')
				return;
		}
		if(is_array($Content) && !count($Content))
			return;
		if(is_string($Content) && ($Content===''||trim($Content==='')))
			return;
			
		if($ParamsCS!==false)
			$ParamsCS=MD5($ParamsCS);
		static $FunNames=Array();
		$PlaceCS=$FunName.$_SERVER['SERVER_NAME'];
		if(isset($FunNames[$FunName]) && $CallsSensivity)
		{
			$PlaceCS.='_fun_call_num_'.$FunNames[$FunName];
			$FunNames[$FunName]++;
		}
		else
			$FunNames[$FunName]=1;
		if($URLSensivity)	
			$PlaceCS.='_page_url_is_'.htracer_get_curent_url();
		else
			$PlaceCS.='_no_url_sensivity';
		$PlaceCS=MD5($PlaceCS);
		$FunName=strtolower(trim($FunName));
		$CashFile=dirname(__FILE__).'/cash/htc_v2_'.$FunName.'_'.$PlaceCS.'.txt';

		if($Content && !$GLOBALS['htracer_test'] && $GLOBALS['htracer_cash_days']>0 && !$GLOBALS['htracer_test'] 
		&& $f=fopen($CashFile,'w'))
		{
			$Serialize='0';
			if(is_array($Content))
			{
				$Content=serialize($Content);
				$Serialize='1';
			}			
			$gziped = 
				$GLOBALS['htracer_cash_use_gzip'] &&
				function_exists('gzcompress')     && 
				function_exists('gzuncompress')   && 
				strlen($Content)>4000;
			if($gziped)
			{	
				$Content0=$Content;
				$Content=gzcompress($Content);
				if(strlen($Content)>=strlen($Content0))
				{
					$Content=$Content0;
					$gziped='0';
				}
				else
					$gziped='1';
			}
			else
				$gziped='0';
			if($ParamsCS===false)
				$ParamsCS='0';
			fputs($f,$ParamsCS."\n".$gziped."\n".$Serialize."\n".$Content);
			fclose($f);
		}
	}
	
// Теперь делаем так чтобы наши функции работали и в PHP 4	


	if(!function_exists('file_get_contents'))
	{
		function file_get_contents($FileName)
		{
			return JOIN("\n",file($FileName));
		}
	}
	if (!function_exists('file_put_contents')) 
	{
		function file_put_contents($filename, $data) 
		{
			$f = @fopen($filename, 'w');
			if(!$f) 
				return false;
			$bytes = fwrite($f, $data);
			fclose($f);
			return $bytes;
		}
	}
	if(!function_exists('stripos'))
	{
		function stripos($haystack, $needle, $offset=0)
		{
			$haystack=strtolower($haystack);
			$needle=strtolower($needle);
			return strpos($haystack, $needle, $offset);
		}
	}
	if(!function_exists('mb_strpos'))
	{
		function mb_strpos($haystack, $needle, $offset=0,$Encoding='utf-8')
		{
			return strpos($haystack, $needle, $offset);
		}
	}
	
	if(!function_exists('mb_strtolower'))
	{
		function mb_strtolower($In,$encoding='utf-8')
		{
			return strtolower($In);
		}
	}
	if(!function_exists('mb_stripos'))
	{
		function mb_stripos($haystack, $needle, $offset=0,$Encoding='utf-8')
		{
			if(function_exists('mb_stripos'))
			{
				$haystack=mb_strtolower($haystack,$Encoding);
				$needle=mb_strtolower($needle,$Encoding);
			}
			return mb_strpos($haystack, $needle, $offset,$Encoding);
		}
	}
	if(!function_exists('mysql_fetch_assoc'))
	{
		function mysql_fetch_assoc($result)
		{
			return mysql_fetch_array($result);
		}
	}
	if(!function_exists('mb_ucfirst'))
	{
		function mb_ucfirst($In, $encoding='UTF-8')
		{
			/*
			static $Leters=Array
			(
				'А'=>'а', 'Б'=>'б', 'В'=>'в', 'Г'=>'г', 'Д'=>'д', 'Е'=>'е', 'Ё'=>'ё',
				'Ж'=>'ж', 'З'=>'з', 'И'=>'и', 'Й'=>'й', 'К'=>'к', 'Л'=>'л', 'М'=>'м',
				'Н'=>'н', 'О'=>'о', 'П'=>'п', 'Р'=>'р', 'С'=>'с', 'Т'=>'т', 'У'=>'у',
				'Ф'=>'ф', 'Х'=>'х', 'Ц'=>'ц', 'Ч'=>'ч', 'Ш'=>'ш', 'Щ'=>'щ', 'Ъ'=>'ъ',
				'Ы'=>'ы', 'Ь'=>'ь', 'Э'=>'э', 'Ю'=>'ю', 'Я'=>'я'
			);
			*/
			
			
			$first=mb_substr($In,0, 1, $encoding);
			$In=mb_substr($In,1, mb_strlen($In,$encoding)-1, $encoding);
			$first0=$first;
			$first=mb_convert_case($first,MB_CASE_TITLE,$encoding);
			if(trim($first)==='' && trim($first0)!=='')
			{
				$first=$first0;
				/*
				if(strlen($first)>=1)
				foreach($Leters as $Up => $Low)
				{
					if(stripos($first,$Low)===0)
					{
						$first=$Up.substr($first,strlen($Low));
						break;
					}
				}*/
			}
			return $first.$In;
		}
	}
	if(!function_exists('spliti'))
	{
		function spliti($S1,$S2,$limit=-1)
		{	
			return split($S1,$S2,$limit);
		}
	}
	function HT_GetZnWordsCount($Str)
	{
		$Words=explode(' ',trim($Str));
		$count=0;
		foreach ($Words as $Word)
			if(!htracer_isStopWord($Word))
				$count++;
		return $count;		
	}		
	function HT_GetStartSymbCount($Word1,$Word2)
	{
		$Word1=hkey_str_split($Word1);
		$Word2=hkey_str_split($Word2);
		$mincount=count($Word1);
		if($mincount>count($Word2))
			$mincount=count($Word2);
		$same=0;
		for($i=0;$i<$mincount;$i++)
		{
			if($Word1[$i]===$Word2[$i])
				$same++;
			else
				break;
		}
		return $same;
	}
	function HT_FormTitle($Base,$MaxWords=7,$Queries=NULL)
	{
		if($Queries===NULL)
			$Queries=$_SERVER["REQUEST_URI"];
		if(!is_array($Queries))
		{		
			if(!isset($GLOBALS['htracer_curent_page_keys_in'])
			||!$GLOBALS['htracer_curent_page_keys_in'])
				HTracer::FormCurentPageKeysArrays();
			$Queries=$GLOBALS['htracer_curent_page_keys_in'];
		}
		$New=HT_FormTitle0($Base,$MaxWords,$Queries);
		if(!$New||HT_GetZnWordsCount($New)<HT_GetZnWordsCount($Base))
			return $Base;
		$New=mb_ucfirst(sanitarize_keyword($New));
		if(strtolower($GLOBALS['htracer_encoding'])!='utf-8' && $Base!=='none')
			$New=mb_convert_encoding($New, $GLOBALS['htracer_encoding'], 'utf-8');
		if(strlen(trim($New,"\r\n\t ,;.!?:"))<4)
			return $Base;
		return $New;
	}
	function HT_FormTitle0($Base,$MaxWords=7,$Queries=NULL)
	{
		arsort($Queries);
		if(count($Queries)==0)
			return $Base;
		if(count($Queries)<3)
		{
			$Queries=array_keys($Queries);
			if(count($Queries)==1||strpos(' '.$Queries[1].' ',' '.$Queries[0].' ')===false)
				return $Queries[0];
			return $Queries[1];	
		}
		$Max=-1;
		foreach($Queries as $key=>$count)
			if($Max<$count)
				$Max=$count;
		$MinLen=1000;
		$MaxLen=-1;
		$MinKey='';
		$MaxKey='';
		
		
		//Выбираем минимальный и максимальный по числу cлов ключевик.
		//Такие что число переходо по ним не намного меньше чем переходом по основному ключу
		//Минимальный мы будем стараться уточнить. Если у нас это не получиться, мы выберим максимальный
		foreach($Queries as $key=>$count)
		{
			$key=trim($key);
			//echo $key.'='.count(split(' ',$key)).'<br />';
			if($count>$Max/2) 
			{
				if($MaxWords==HT_GetZnWordsCount($key))
					return $key;
				if($MinLen>HT_GetZnWordsCount($key))
				{
					$MinLen==HT_GetZnWordsCount($key);
					$MinKey=$key;
				}
				if($MaxLen<HT_GetZnWordsCount($key))
				{
					$MaxLen==HT_GetZnWordsCount($key);
					$MaxKey=$key;
				}
			}
		}

		
		//Вырезаем стоп-слова сайта из начала и конца запроса, который мы будем уточнять 
		$MinKey2=explode(' ',$MinKey);
		if(hkey_is_site_stop_word($MinKey2[count($MinKey2)-1]) 
		&& $MinKey2[count($MinKey2)-2]!='в' && $MinKey2[count($MinKey2)-2]!='во')
			unset($MinKey2[count($MinKey2)-1]);
		if(hkey_is_site_stop_word($MinKey2[0]))
			unset($MinKey2[0]);
		$MinKey2=join(' ',$MinKey2);

		//Запоминаем слова уточняемого запроса
		$BaseWords0=explode(' ',$MinKey);
		$BaseWords=Array();
		$WasWords=Array();
		$WasWords2=Array();
		
		foreach($BaseWords0 as $i=>$Word)
		{
			$WasWords[$Word]=1;
			$WasWords[HkStrem($Word)]=1;
			if(!htracer_isStopWord($Word))
			{
				$BaseWords[HkStrem($Word)]=$Word;
				$WasWords2[$Word]=1;
			}
		}
		$Was=Array();
		
		$StartClarifies=Array();
		$EndClarifies=Array();
			
		foreach($Queries as $key=>$count)
		{	
			if(!$key||$MinKey==$key||$MinKey2==$key
			|| $count<$Max/100 //Фильтруем от статистических погрешностей 
			||$count<3
			||($Max>10 && $count<5)
			||($Max>100 && $count<7)
			||($Max>1000 && $count<10))
				continue;
			$Clarify=false;	
			$OtherForm=false;	
			if(strpos($key,$MinKey.' ')===0)
				$Clarify=trim(substr($key,strlen($MinKey.' ')));
			elseif(strpos($key,$MinKey2.' ')===0)
				$Clarify=trim(substr($key,strlen($MinKey2.' ')));
			else
			{
				if(strpos($key,' '.$MinKey2))
				{
					$Clarify=explode(' '.$MinKey2,$key);
					$Clarify=trim($Clarify[0]);
				}
				if(!$Clarify)
					continue;
				if(!$BaseWords[HkStrem($Clarify)])
				{//Уточнения вначале							
					if(HT_GetZnWordsCount($Clarify)==1
					&& str_replace(Array('ая*','оя*','ий*','ой*','ое*','ие*','ые*'),'',$Clarify.'*')!==$Clarify.'*'
					&& str_replace(Array('сание','ведение'),'',$Clarify)===$Clarify)
					{//Прилагательные
					//
						if(
						
						!$WasWords2[$Clarify] && !$WasWords2[HkStrem($Clarify)] 
						&& !$BaseWords[HkStrem($Clarify)] 
						&& !$Was[$Clarify] 		 && !$Was[HkStrem($Clarify)]
						)
						{	
							//Пытаемся определить есть ли однокоренное слово
							$HaveSameKoren=false;
							foreach($BaseWords as $Word)
							{
								if($Word==$Clarify
								 ||HT_GetStartSymbCount($Word,$Clarify)>=4
								 ||(
									function_exists('mb_strlen') 
									&& HT_GetStartSymbCount($Word,$Clarify)>=3
									&& mb_strlen($Word,'utf-8')<=5
								))
								{
									$HaveSameKoren=true;
									break;
								}	
							}
							if(!$HaveSameKoren)
								$StartClarifies[]=$Clarify;
						}			
						$Was[$Clarify]=1;
						$Was[HkStrem($Clarify)]=1;
					}
					elseif(!$StartClarify && !$OtherForm && HT_GetZnWordsCount($Clarify)==1 && (!$Was[$Clarify] && !$Was[HkStrem($Clarify)]))
					{
						$StartClarifyAdd=($Max/10<$count);
						$StartClarify=$Clarify;
					}
					continue;
				}
				else if(!$BaseWords[HkStrem($Clarify)])
				{//Пытаемся проигнорировать порядок слов
					$Words=explode(' ',$key);
					$Same=0;
					$AddWords=Array();
					foreach($Words as $i=>$Word)
					{
						if($Same==count($BaseWords))
							$AddWords[]=$Word;
						else
						{
							if(!htracer_isStopWord($Word))
							{
								if($BaseWords[HkStrem($Word)])
									$Same++;
								else
									break;
							}
						}
					}
					if(!count($AddWords))
						continue;
					else
						$Clarify=join(' ',$AddWords);
				}
				else
					continue;
			}
			if(!$Clarify||!isset($Was[$Clarify])||$Was[$Clarify])
				continue;
			$Was[$Clarify]=1;
			$Lowered=mb_strtolower($Clarify,'utf-8');
			if($Clarify!=$Lowered && $Was[$Lowered])
				continue;
			$Spaced=str_replace(array(' ','-'),'',$Clarify);
			if($Clarify!=$Spaced && $Was[$Spaced])
				continue;
			$Was[$Spaced]=1;
			$Words=explode(' ',$Lowered);
			foreach($Words as $Word)
			{
				if(htracer_isStopWord($Word))
					continue;
				if(isset($WasWords[$Word]) && $WasWords[$Word])
					continue 2;
				$WasWords[$Word]=1;	
				$Word2=HkStrem($Word);
				if(isset($WasWords[$Word2]) && $WasWords[$Word2] && $Word2 && $Word2!=$Word)
					continue 2;
				$WasWords[$Word2]=1;	
				if(str_replace(Array('ая*','оя*','ий*','ой*','ое*','ие*','ые*'),'',$Word.'*')!==$Word.'*'
					&& str_replace(Array('ание','ение'),'',$Word)===$Word)
						continue 2;
				if(mb_strlen($Word,'utf-8')>3)
				{
					foreach ($WasWords2 as $wWord =>$ti)
						if(levenshtein($wWord,$Word)<3)
							continue 3;
					$WasWords2[$Word]=1;
				}
			}
			$EndClarifies[]=$Clarify;
		}
	//недорогие гостиницы 
	//лучшие отели, рестораны
	//все санатории 
	//все основные достопримечательности,достопремечательности
	//"Украина," одесса,киев,львов,луганск,донецк,харьков,днепропетровск,херсон
	// одесса достопимечат, клубы... города   одесса,киев,москва
	//"Крым," симферополь,севастополь,форос,феодосия,ялка,алупка,алушта
	//популярные клубы, ночные клубы, дискотеки, форумы
	// купить 'мобильные телефоны,ноутбуки,телевизоры,нетбуки,компьютеры,пластиковые,стиральные,швейные,посудомоечные,холодильники,телефоны'
	//купить  'ноутбук,телевизор,нетбук,компьютер,холодильник,телефон'=>
	//'купить,снять'=> недорого
	//'скачать'=>бесплатно
	//
	
		//Оставляем только 2 прилагательных
		foreach($StartClarifies as $i=>$c)
			if($i>1)
				unset($StartClarifies[$i]);

		$Count=2+HT_GetZnWordsCount($MinKey);
		if(count($EndClarifies))
			$Count+=HT_GetZnWordsCount($EndClarifies[0]);
		if($Count-1>$MaxWords)
			$StartClarifies=Array();
		elseif($Count>$MaxWords && count($StartClarifies))
			unset($StartClarifies[1]);
			
		//echo ("$Count>$MaxWords"); 	
		if(count($StartClarifies)==2)
		{//Если одно из прилагательных синоним другого, убираем его
			if((strpos($StartClarifies[0],'лучш')===0 && strpos($StartClarifies[1],'хорош')===0)
		 	 ||(strpos($StartClarifies[1],'лучш')===0 && strpos($StartClarifies[0],'хорош')===0))
				unset($StartClarifies[1]);//лучшие хорошие
			if(((strpos($StartClarifies[0],'дешев')===0||strpos($StartClarifies[0],'дёшев')===0||strpos($StartClarifies[0],'дешов')===0) && (strpos($StartClarifies[1],'недорог')===0||strpos($StartClarifies[1],'дорог')===0))
			 ||((strpos($StartClarifies[1],'дешев')===0||strpos($StartClarifies[1],'дёшев')===0||strpos($StartClarifies[1],'дешов')===0) && (strpos($StartClarifies[0],'недорог')===0||strpos($StartClarifies[0],'дорог')===0)))
				unset($StartClarifies[1]);//недорогие дешевые
			if((strpos($StartClarifies[0],'главн')===0 && strpos($StartClarifies[1],'основн')===0)
		 	 ||(strpos($StartClarifies[1],'главн')===0 && strpos($StartClarifies[0],'основн')===0))
				unset($StartClarifies[1]);//главн основн
			if(levenshtein($StartClarifies[0],$StartClarifies[1])<3)
				unset($StartClarifies[1]);
			// Теперь пытаемся разобраться с длиной
		}
		//Сортируем прилагательные по алфавиту (правило русского языка такое)	
		sort($StartClarifies);
		
		if(count($StartClarifies)==2)
		{
			if($StartClarifies[0]=='ночные')
				$rokirovka=true;
			else
			{
				$normal=0;
				$rock=0;
				foreach($Queries as $key=>$count)
				{
					if(strpos($key,$StartClarifies[0])!==false
					&&strpos($key,$StartClarifies[1])!==false)
					{
						if(strpos($key,$StartClarifies[0])>strpos($key,$StartClarifies[1]))
							$rock+=$count;
						else	
							$normal+=$count;
					}
				}
				$rokirovka=($rock>$normal);
			}
			if($rokirovka)
			{
				$t=$StartClarifies[1];
				$StartClarifies[1]=$StartClarifies[0];
				$StartClarifies[0]=$t;
			}
		}
		$Count=1+HT_GetZnWordsCount($MinKey);
		if(count($EndClarifies))
			$Count+=HT_GetZnWordsCount($EndClarifies[0]);
		if(count($StartClarifies)==1 && $Count<$MaxWords && $StartClarifies[0])
		{
			if($StartClarifies[0]=='лучшие'||$StartClarifies[0]=='дорогие'||$StartClarifies[0]=='дешевые'||$StartClarifies[0]=='дешовые')
			{
				$StartClarifies[1]=$StartClarifies[0];
				$StartClarifies[0]='самые';
			}
		}
		

		//
		
		//Теперь мы добавляем уточнение вначало не являющеся прилагательыным например, клуб
		if($StartClarifies
		&& $StartClarify 
		&&(!count($StartClarifies)
		||($StartClarifyAdd && count($StartClarifies)<3)))
		{
			$StartClarifies[]=$StartClarify;
			//echo " StartClarify=$StartClarify; ";
			
			//Теперь чистим дубли
			foreach($EndClarifies as $i=>$Clarify)
				if(stripos(' '.$Clarify.' ',' '.$StartClarify.' ')!==false
				||stripos(' '.$StartClarify.' ',' '.$Clarify.' ')!==false)
					unset($EndClarifies[$i]);
		}
		

		$Count=count($StartClarifies)+ HT_GetZnWordsCount($MinKey);
		if(count($EndClarifies))
			$Count+=HT_GetZnWordsCount($EndClarifies[0]);
		$S_Arr=Array();
		$S_Str_IsFirst=false;
		//echo "$Count>$MaxWords";
		foreach($EndClarifies as $i=>$Clarify)
		{
			if(strpos($Clarify,'с ')===0)
			{
				if($i==0)
				{
					$S_Str_IsFirst=true;
					$Count-=HT_GetZnWordsCount($EndClarifies[0]);
				}
				$Count+=HT_GetZnWordsCount($Clarify)-1;
				if($Count>$MaxWords && $i)
					break;
				$S_Arr[]=$Clarify;
				if(count($S_Arr)>3)
					break;
			}
		}
		$S_Str='';
		foreach($S_Arr as $i=>$El)
		{
			$El=trim(substr($El,strlen('с ')));
			if(!$i)
				$S_Str='с ';
			elseif($i==count($S_Arr)-1)
				$S_Str.=' и ';
			else
				$S_Str.=', ';
			$S_Str.=$El;	
		}
		
		$Out='';
		//echo '<pre>';
//		echo 'StartClarifies=';print_r($StartClarifies);
		//echo "<br>$MinKey<br>";
		//echo 'EndClarifies=';print_r($EndClarifies);
		
		if(count($StartClarifies))
			$Out=join(' ',$StartClarifies).' ';
		$Out.=$MinKey;
		if(count($EndClarifies) && !$S_Str_IsFirst 
		&& HT_GetZnWordsCount($EndClarifies[0]) + HT_GetZnWordsCount($Out)<=$MaxWords
		&& HT_GetStartSymbCount($Word,$EndClarifies[0])<4)
		{
			$Out.=' ';
			$Out.= $EndClarifies[0];
		}
		if($S_Str)
			$Out.=' '.$S_Str;
		
		//Теперь пытаемся добавить слово, которое в большинстве случаев идет после последнего в титле
		
		
		//Пытаемся тупо расширить ключ
		$MaxLen=-1;
		static $AskWords=Array('как'=>1,'cколько'=>1,'где'=>1,'почему'=>1,'какой'=>1,'какая'=>1,'какие'=>1);
		$AskWord=false;
		$Out2=explode(' ',$Out);
		if(isset($AskWords[$Out2[0]]))
		{
			$AskWord=$Out2[0];
			unset($Out2[0]);
		}
		$Out=join(' ',$Out2);
		$MaxLenKey=$Out;
		$MaxLen=-1;
		foreach($Queries as $key=>$count)
		{
			if(strpos($key,$Out)!==false 
			&& $MaxLen<HT_GetZnWordsCount($Out)
			&& $AskWord.' '.$Out!=$key)
			{
				$MaxLen=HT_GetZnWordsCount($Out);
				$MaxLenKey=$key;
			}
		}
		$Out=$MaxLenKey;
//		echo "<br />x_100_<b>$Out</b>";
		if($AskWord)
			$Out=$AskWord.' '.$Out;	
		//Если полученный ключ меньше ключа полученого раньше
		if(HT_GetZnWordsCount($Out)<=HT_GetZnWordsCount($MaxKey))
		{		
			$Out=$MaxKey;
			foreach($Queries as $key=>$count)
			{
				if(strpos($key,$Out)!==false 
				&& $MaxLen<HT_GetZnWordsCount($Out))
				{
					$MaxLen<HT_GetZnWordsCount($Out);
					$MaxLenKey=$key;
				}
			}
		}
		//echo '<b>'.$Out.'</b>';	
		
		//echo "<br /><b>$Out</b><br />";
		$OutA=explode(' ',$Out);
		$Last=$OutA[count($OutA)-1];
		$First=$OutA[0];
		$Clarifies1=Array();
		$Clarifies2=Array();
		
		if(count($OutA)<$MaxWords && count($Queries)>2)
		{
			$isfirst=true;
			foreach($Queries as $key=>$count)
			{
				if($isfirst && $count<15)
					break;
				$isfirst=false;
				$key=explode(' ',$key);
				foreach($key as $i=>$Word)
				{	
					if(true||mb_strlen($Word,'utf-8')>4)
					{
						if($First==$Word  && isset($key[$i-1]))
						{
							if(!isset($Clarifies1[$key[$i-1]]))
								$Clarifies1[$key[$i-1]]=0;
							$Clarifies1[$key[$i-1]]+=$count;
						}
						if($Last==$Word && isset($key[$i+1]))
						{
							if(!isset($Clarifies2[$key[$i+1]]))
								$Clarifies2[$key[$i+1]]=0;
							$Clarifies2[$key[$i+1]]+=$count;
						}
					}
				}
			}
		}
	
		//echo '<pre>'.$First;print_r($Clarifies1);
		//echo '<pre>'.$Last;print_r($Clarifies2);
		
		arsort($Clarifies1);
		arsort($Clarifies2);
		
		$Clarifies1=array_keys($Clarifies1);
		$Clarifies2=array_keys($Clarifies2);
		

		
		if(isset($Clarifies2[0]) && $Clarifies2[0] && stripos($Out,$Clarifies2[0])===false)
			$Out.=' '.$Clarifies2[0];
		if(HT_GetZnWordsCount($Out)<$MaxWords && isset($Clarifies1[0]) && $Clarifies1[0] && stripos($Out,$Clarifies1[0])===false)
			$Out=$Clarifies1[0].' '.$Out;
		$Out=str_replace(' официальный сайт ',' сайт ',' '.$Out.' ');
		$arr=array_keys($Queries);
		if(HT_GetZnWordsCount($arr[0])>=HT_GetZnWordsCount($Out))
			$Out=$arr[0];
		return trim($Out);
	}
	function hkey_get_numbers_count($str)
	{//Возвращат число чисел в строке
		$NumberCount=0;
		$inNumber=false;
		$Digits=Array('0'=>1,'1'=>1,'2'=>1,'3'=>1,'4'=>1,'5'=>1,'6'=>1,'7'=>1,'8'=>1,'9'=>1);
		$astr=hkey_str_split($str, 'utf-8');
		foreach($astr as $i=>$cur)
		{
			if(isset($Digits[$cur]))
				$inNumber=true;
			else
			{
				if($inNumber)
					$NumberCount++;
				$inNumber=false;
			}
		}
		return $NumberCount;
	}
	function hkey_str_split($str, $encoding=false,$Keys=false) 
	{//разбивает строку на массив символов
		if(!$encoding)
			$encoding=$GLOBALS['htracer_encoding'];
		$split=1;
		$array = array();
		$encoding=strtoupper($encoding);
		for ($i=0; $i<strlen($str);)
		{
			if($encoding=='UTF-8')
			{
				$value = ord($str[$i]);
				if($value > 127)
				{
					if($value >= 192 && $value <= 223)
						$split=2;
					elseif($value >= 224 && $value <= 239)
						$split=3;
					elseif($value >= 240 && $value <= 247)
						$split=4;
				}
				else
					$split=1;
				$key = NULL;
				for ($j = 0; $j < $split; $j++, $i++ ) 
					$key .= $str[$i];
			}
			else
			{
				$key=$str{$i};
				$i++;
			}
			if(!$Keys)
				array_push( $array, $key );
			else
				$array[$key]=1;
		}
		return $array;
	}
	function hkey_join($Str_Array,$from=0,$len=false)
	{
		if($len===false)
			$len=count($Str_Array)-$from;
		if($len>=0)
			$to=$from+$len;
		else
			$to= -1 * $len;
		$Res='';
		for($i=$from;$i<$to;$i++)
			$Res.=$Str_Array[$i];
		return $Res;
	}
	$hkey_is_letter_arr			  = false;
	$hkey_is_letter_last_encoding = false;
	
	function hkey_is_letter($Symb,$encoding=false)
	{//буква ли это 
		if(!$encoding)
			$encoding=$GLOBALS['htracer_encoding'];
		global $hkey_is_letter_arr,$hkey_is_letter_last_encoding;
		if($hkey_is_letter_arr===false||$hkey_is_letter_last_encoding!=$encoding)
		{
			$Alfabit_Str="АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя"
						."AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz"
						."-1234567890";
			if($encoding!='UTF-8')	
				$Alfabit_Str=mb_convert_encoding($Alfabit_Str ,  $encoding, 'UTF-8');
			$hkey_is_letter_arr=hkey_str_split($Alfabit_Str, $encoding,true);
			$hkey_is_letter_last_encoding=$encoding;
		}
		return (isset($hkey_is_letter_arr[$Symb]));
	}
	function htracer_isStopWord($Str)
	{
		return (!$Str||!isset($Str{3})||isset($GLOBALS['htracer_dict']['stop_words'][$Str]));
	}
	function htracer_isPopWord($Str)
	{
		return (isset($GLOBALS['htracer_dict']['pop_words'][$Str]));
	}
	class hkey_word
	{
		var $Str='';
		var $Stem='';
		var $CS=0;
		var $Start=-1;
		var $End=-1;
		var $isStop 	= false; 
		var $isSiteStop = false;
		function hkey_word($Str,$Start,$End,$encoding)
		{
			global $HTracer; 
			if(!$encoding)
				$encoding=$GLOBALS['htracer_encoding'];
			$Str 				= mb_convert_encoding($Str,'UTF-8',$encoding);
			$Str 				= mb_convert_case($Str,MB_CASE_LOWER,'UTF-8');
			$this->isStop 		= htracer_isStopWord($Str);
			$this->isSiteStop 	= hkey_is_site_stop_word($Str);
			$this->Start  		= $Start;
			$this->End	  		= $End;
			$this->Str	  		= $Str;
			$this->Stem	  		= HkStrem($Str);	
			if(!$this->isStop)
				$this->CS 	  = crc32($this->Stem);
			else
				$this->CS     = 0;  
		}	
	}
	//HPROTECTION
	function hkey_is_starts($Str_Array,$Needle,$Pos=0, $encoding=false)
	{
		if(!$encoding)
			$encoding=$GLOBALS['htracer_encoding'];
		$Needle=mb_convert_case($Needle,MB_CASE_LOWER,$encoding);
		$len=strlen($Needle);
		for($i=0;$i<$len;$i++)
		{
			if(mb_convert_case($Str_Array[$Pos+$i],MB_CASE_LOWER,$encoding)!=$Needle[$i])
				return false;
		}
		return true;
	}
	function hkey_html_tag_name($Str_Array,$Pos=0, $encoding=false)
	{
		if(!$encoding)
			$encoding=$GLOBALS['htracer_encoding'];
		$Res='';
		$len=count($Str_Array);
		for($i=$Pos;$i<$len;$i++)
		{
			$cur=$Str_Array[$i];
			if($cur==' '||$cur=='>'||$cur=='\r'||$cur=='\n'||$cur=='\t'||($cur=='/' && $Res))
				break;
			$Res.=$cur;	
		}
		return mb_convert_case($Res,MB_CASE_LOWER,$encoding); 
	}
	//if(!isset($GLOBALS['hkey_in_plain_text_range_array']))
	//	$hkey_in_plain_text_range_array=false;
	
	$GLOBALS['htracer_not_plain_tags']=Array('head'=>1,'script'=>1,'style'=>1,'a'=>1,'b'=>1,'i'=>1,'strong'=>1,'title'=>1,'h1'=>1,'h2'=>1,'h3'=>1,'h4'=>1,'h5'=>1,'h6'=>1,'u'=>1,'em'=>1,'s'=>1,'strice'=>1,'sub'=>1,'sup'=>1,'li'=>1,'tt'=>1,'kbd'=>1,'var'=>1,'cite'=>1,'var'=>1,'code'=>1,'pre'=>1);
	function hkey_in_plain_text_range($tag_ranges)
	{
		global $htracer_not_plain_tags;
		foreach($htracer_not_plain_tags as $tag => $tv)
		{
			if(isset($tag_ranges[$tag]))
				return false;
		}
		return true;
	}
	function hkey_split_passages($Str, $encoding=false, $OnePassage=false)
	{//разбивает текст на пассажи, которые в свою очередь разбивает на слова
		if(!is_array($Str))
			$Str=hkey_str_split($Str,$encoding);
		
		if(!$encoding)
			$encoding=$GLOBALS['htracer_encoding'];
		$len=count($Str);
		$Passages=Array();
		$CurPassage=Array();
		$CurWord='';
		$WordStart=-1;
		$intag	    = false;
		$incomment  = false;
		$tag_ranges = Array();
		
		$inkav1=false;
		$inkav2=false;
		
		//HPROTECTION
		
		for($i=0;$i<=$len;$i++)
		{
			$inscript=isset($tag_ranges['script']);
			if($i!=$len)
				$cur=$Str[$i];
			else
				$cur='*';
			if($cur=='<' && !$intag && !$incomment && $inscript 
			&&(hkey_is_starts($Str,"</script",$i,$encoding)||hkey_is_starts($Str,"</ script",$i,$encoding)))
				$inscript=false;
			if($cur=='<' && !$intag && !$incomment && !$inscript)
			{
				if($CurWord!=='')
					$CurPassage[]=new hkey_word($CurWord,$WordStart,$i-1,$encoding);
				$CurWord='';
				if(count($CurPassage))
					$Passages[]=$CurPassage;
				$CurPassage=Array();
					
				if(hkey_is_starts($Str,"<!--",$i,$encoding))
					$incomment=true;
				else
				{	
					$intag = true;
					$ctag=hkey_html_tag_name($Str,$i+1,$encoding);
					if($ctag[0]=='/')
					{
						$ctag=substr($ctag,1);
						if($tag_ranges[$ctag]==1)
							unset($tag_ranges[$ctag]);
						else
							$tag_ranges--;
					}
					else
					{
						if(isset($tag_ranges[$ctag]))
							$tag_ranges[$ctag]++;
						else
							$tag_ranges[$ctag]=1;
					}
				}
			}
			elseif($cur=='>' && $i>2 && $Str[$i-1]=='-' && $Str[$i-2]=='-' && $incomment)
				$incomment=false;
			elseif($cur=="'" && $intag)
				$inkav1=!$inkav1;
			elseif($cur=='"' && $intag)
				$inkav2=!$inkav2;
			elseif($cur=='>' && $intag && !$incomment && !$inkav1 && !$inkav2)	
			{
				$intag = false;
				$isaetag=false;
				for($j=$i-1;$j>=0;$j--)
				{
					$jcur=$Str[$j];
					if($jcur!=' '&&$jcur!='\n'&&$jcur!='\t'&&$jcur!='\r')
					{
						$isaetag=($jcur=='/');
						break;
					}
				}
				if($isaetag)
				{
					if($tag_ranges[$ctag]==1)
						unset($tag_ranges[$ctag]);
					else
						$tag_ranges[$ctag]--;
				}
			}
			if(!$intag && !$incomment && !$inscript && hkey_in_plain_text_range($tag_ranges))
			{	
				if(hkey_is_letter($cur,$encoding))
				{
					if($CurWord==='')
						$WordStart=$i;
					$CurWord.=$cur;
				}
				else 
				{
					if($CurWord!=='')
						$CurPassage[]=new hkey_word($CurWord,$WordStart,$i-1,$encoding);
					$CurWord='';
					if($cur=='&' && hkey_is_starts($Str,"&nbsp;",$i,$encoding))
					{
						$i=$i+5;
						continue;
					}
					else if($cur!=' ' && (($cur!='\r' && $cur!='\t' && $cur!='\n')  || isset($tag_ranges['pre'])))
					{
						if(count($CurPassage))
							$Passages[]=$CurPassage;
						$CurPassage=Array();
					}
				}
			}
		}
		if($OnePassage)
		{
			$Res=Array();
			foreach($Passages as $Passage)
			{
				foreach($Passage as $Word)
					$Res[]=$Word;
			}
			return $Res;
		}
		return $Passages;
	}
	function CalculateGZams($Passages,$MaxLen,$encoding=false)
	{//Возвращает замены которые гипотетически возможны (куски текста)
		if(!$encoding)
			$encoding=$GLOBALS['htracer_encoding'];
		$Res=Array();
		for($i=1;$i<=$MaxLen;$i++)
		{
			 $Res0=CalculateGZams0($Passages,$i,$encoding);
			 foreach($Res0 as $cRes)
				$Res[]=$cRes;
		}
		return $Res;
	}
	function DoCLZams($In,$FZams,$class='alink', $encoding=false)
	{
		$wasb=false;
		$Str=$In;
		if(!$encoding)
			$encoding=$GLOBALS['htracer_encoding'];
		if(!is_Array($Str))
			$Str=hkey_str_split($Str, $encoding);
		foreach($FZams as $Zam)
		{
			$Start = intval($Zam['Start']);
			$End   = intval($Zam['End']);
			if(HTracer::isCurPage($Zam['URL']))
			{
				if(!$GLOBALS['htracer_context_links_b']||($GLOBALS['htracer_context_links_b']==='only_first' && $wasb))
					continue;
				$Str[$Start]= "<strong>".$Str[$Start];
				$Str[$End].= "</strong>";
				$wasb=true;
			}	
			else
			{
				$URL="http://".$_SERVER['SERVER_NAME'].$Zam['URL'];
				
				$title=mb_convert_encoding($Zam['title'], $encoding, 'UTF-8');
				if($class && trim($class))
					$Str[$Start]="<a href='$URL' class='$class' title='$title'>".$Str[$Start];
				else
					$Str[$Start]="<a href='$URL' title='$title'>".$Str[$Start];
				$Str[$End].= "</a>";
			}
		}
		return hkey_join($Str);
	}
	function hkey_is_site_stop_word($Str)
	{
		static $stop_words = false;
		if(!$stop_words)
		{
			$stop_words=$GLOBALS['htracer_site_stop_words'];
			if(!is_Array($stop_words))
			{
				$Arr=explode(',',$stop_words);
				$stop_words=Array();
				foreach($Arr as $Word)
					$stop_words[trim($Word)]=1;
			}
		}
		return isset($stop_words[$Str]);
	}
	function hkey_insert_cloud_by_selector_cb($Str)
	{
		error_reporting (E_ERROR | E_PARSE| E_WARNING);
		htracer_restore_globals();

		if(!isset($GLOBALS['htracer_cloud_selector']))
			return $Str;
		return hkey_insert_cloud_by_selector($Str,$GLOBALS['htracer_cloud_selector']);
	}
	function hkey_insert_cloud_by_selector($Str,$Selector)
	{
		if(!isset($Str{0})||$Str{0}==='{')
			return $Str;
			
		$Selector=trim($Selector);
		if(!$Selector)
			return $Str;
		$Selectors=explode(',',$Selector);
		if(count($Selectors)>1)
			foreach($Selectors as $Cur)
				$Str=hkey_insert_cloud_by_selector($Str,$Cur);
		else
		{
			$Place='_end';
			$Params='';
			
			if($Selector{0}==='_')
			{
				$Arr=explode(' ',$Selector,2);
				$Selector=trim($Arr[1]);
				if(!$Selector)
					return $Str;
				$Place=trim(strtolower($Arr[0]));	
			}
			$Arr=explode('?',$Selector,2);
			if(count($Arr>1))
			{
				$Selector=trim($Arr[0]);
				$Params=trim($Arr[1]);
			}
			$html = ht_str_get_html($Str);
			$res=$html->find($Selector);
			foreach($res as $cur)
			{
				$cloud=get_keys_cloud($Params);
				if($Place==='_end')
					$cur->innertext=$cur->innertext.$cloud;
				elseif($Place==='_after')
					$cur->outertext=$cur->outertext.$cloud;
				elseif($Place==='_before')
					$cur->outertext=$cloud.$cur->outertext;
				elseif($Place==='_start')
					$cur->innertext=$cloud.$cur->innertext;
				elseif($Place==='_replace')
					$cur->innertext=$cloud;
			}
			$Str=$html->save();
			$html->clear(); 
			unset($html);
		}
		return $Str;
	}
	function hkey_insert_context_links_cb($Str)//,$class='alink',$apages=300, $encoding=false)
	{
		error_reporting (E_ERROR | E_PARSE| E_WARNING);
		htracer_restore_globals();
		
		return hkey_insert_context_links(
										$Str
										,$GLOBALS['htracer_context_links_class']
										,$GLOBALS['htracer_context_links_acceptor_pages']
										);
	}
	function hkey_insert_context_links_in_selector_cb($Str)//,$class='alink',$apages=300, $encoding=false)
	{
		if(!isset($GLOBALS['htracer_context_links_selector']))
			return $Str;
		error_reporting (E_ERROR | E_PARSE| E_WARNING);
		htracer_restore_globals();
		return hkey_insert_context_links_in_selector(
										$Str
										,$GLOBALS['htracer_context_links_selector']
										,$GLOBALS['htracer_context_links_class']
										,$GLOBALS['htracer_context_links_acceptor_pages']);
	}
	function hkey_insert_context_links_in_selector($Str,$Selector='#ht_context_range',$class='alink',$apages=300, $encoding=false)
	{
		if(!$Selector || !$GLOBALS['htracer_use_php_dom'])	
			return $Str;
		
		if(!isset($Str{0})||$Str{0}==='{')
			return $Str;
		
		$html = ht_str_get_html($Str);
		$res=$html->find($Selector);
		foreach($res as $cur)
			$cur->innertext=hkey_insert_context_links($cur->innertext,$class,$apages, $encoding);
		$Str=$html->save();
		$html->clear(); 
		unset($html);
		return $Str;
	}
	function hkey_insert_context_links_in_ranges_cb($Str)//,$class='alink',$apages=300, $encoding=false)
	{
		error_reporting (E_ERROR | E_PARSE| E_WARNING);
		htracer_restore_globals();
		return hkey_insert_context_links_in_ranges(
										$Str
										,$GLOBALS['htracer_context_links_class']
										,$GLOBALS['htracer_context_links_acceptor_pages']
										);
	}
	function hkey_insert_context_links_in_ranges($Str,$class='alink',$apages=300, $encoding=false)
	{
		error_reporting (E_ERROR | E_PARSE| E_WARNING);
		htracer_restore_globals();
		$arr=explode('<!--htracer_context_links-->',$Str);
		if(count($arr)<2)
			return $Str;
		$Res='';
		for($i=0;$i<count($arr);$i++)
		{
			$cur =$arr[$i];
			if($i%2==0)
			{
				$Res.=$cur;	
				continue;
			}
			$cur = explode('<!--/htracer_context_links-->',$cur,2);
			$Res.=hkey_insert_context_links($cur[0],$class,$apages, $encoding);	
			if(isset($cur[1]))
				$Res.=str_replace('<!--/htracer_context_links-->','',$cur[1]);
		}
		return $Res;
	}
	function hkey_insert_context_links($Str,$class='alink',$apages=300, $encoding=false)
	{
		error_reporting (E_ERROR | E_PARSE| E_WARNING);
		htracer_restore_globals();
		global $HTracer;
		if(!$Str || !trim($Str))
			return $Str;
		if(!$encoding)
			$encoding=$GLOBALS['htracer_encoding'];
			
			
		$CashCS=$Str
				.$class
				.$GLOBALS['htracer_clcore_size'].'(rz)'
				.$GLOBALS['htracer_max_clinks'].'(rz)'
				.$GLOBALS['htracer_clinks_segment_lng'].'(rz)'
				.$encoding;	
		$SW=$GLOBALS['htracer_site_stop_words'];
		if(!is_Array($SW))
			$SW=explode(',',$SW);
		foreach ($SW as $K=>$W)
			$CashCS.=trim($W).$K.',';
		$Res=htracer_read_cash_file('hkey_insert_context_links',$CashCS);
		if($Res)
			return $Res;	
		//Разбивает текст на пассажи (Предложения)
		$Passages = hkey_split_passages($Str,$encoding); 	
		if(count($Passages)==0) 
		{
			if($GLOBALS['htracer_test'])
				return "ht_context_links_error_1:: No Passage<br />".$Str;
			return $Str;
		}
		//echo '<pre>';
		//print_r($Passages);

		//Пассажи разбиваем на слова, считаем их CS
		$GZams	  = CalculateGZams($Passages,5,$encoding); 	
		//echo '<hr /><pre>';
		//print_r($GZams);
		
		//HPROTECTION
		if(count($GZams)==0) 	
		{
			if($GLOBALS['htracer_test'])
				return "ht_context_links_error_2:: No Words<br />".$Str;
			return $Str;
		}

		//Формируем ядро всех кс
		$ZCore    = HTracer::FormCLinksCore($apages); 		
		//echo '<hr /><pre>ZCore=';
		//print_r($ZCore);

		if(count($ZCore)==0)
		{
			if($GLOBALS['htracer_test'])
				return "ht_context_links_error_3:: No Queries<br />".$Str;
			return $Str;
		}

		//Выбираем те кс, которые возможно вставить на данную страницу 		
		$AZams    = SelectAZams($GZams,$ZCore);				
		if(count($AZams)==0) 	
		{
			if($GLOBALS['htracer_test'])
				return "ht_context_links OK, but not have places<br />".$Str;
			htracer_write_cash_file($Str,'hkey_insert_context_links',$CashCS);
			return $Str;
		}
		//echo '<hr /><pre>AZams=';
		//print_r($AZams);
		
		//Конкуренция
		$FZams    = SelectFZams($AZams);					
		if(count($FZams)==0)	
		{
			if($GLOBALS['htracer_test'])
				return "ht_context_links_error_5:: No Queries after Concurency<br />".$Str;
			return $Str;
		}

		//echo '<hr /><pre>FZams=';
		//print_r($FZams);
		
		//Производим замены
		$Res	  = DoCLZams($Str,$FZams,$class,$encoding);	
		if(!$Res)				
		{
			if($GLOBALS['htracer_test'])
				return "ht_context_links_error_6:: Error in DoCLZams<br />".$Str;
			return $Str;
		}
		if($GLOBALS['htracer_test'])
			return "ht_context_links_inserted<br />".$Res;
		htracer_write_cash_file($Res,'hkey_insert_context_links',$CashCS);
		return $Res;
	}
	function SelectAZams($GZams,$ZCore)
	{//Возвращает замены которые возможны
		$AZams=Array();
		foreach($GZams as $GZam)
		{
			$AZam=$GZam;	
			$CS=(string) ($GZam['CS']);
			if(isset($ZCore[$CS]))
			{
				$Eva=$ZCore[$CS]['Eva'];
				if($AZam['Len']==$ZCore[$CS]['fWords'])//число слов с учетом стоп слов
					$Eva*=1.5;
				if($AZam['sCS']==$ZCore[$CS]['sCS'])//сумма с учетом порядка 
					$Eva*=1.5;
				if($AZam['fCS']==$ZCore[$CS]['fCS'])//сумма с учетом стоп слов сайта
					$Eva*=1.3;		
				$AZam['Eva']  =round($Eva);
				$AZam['title']=$ZCore[$CS]['Q'];
				$AZam['URL']  =$ZCore[$CS]['URL'];
				$AZams[]=$AZam;
			}
		}
		return $AZams;
	}
	function SelectFZams($AZams)
	{//Возвращает замены после конкуренции
		$Ranges=Array();
		$Ex=Array();

		$SegmentRange=100;
		if(isset($GLOBALS['htracer_clinks_segment_lng'])
		&& $GLOBALS['htracer_clinks_segment_lng']
		&& is_numeric($GLOBALS['htracer_clinks_segment_lng'])
		&& intval($GLOBALS['htracer_clinks_segment_lng'])>100)
			$SegmentRange=intval($GLOBALS['htracer_clinks_segment_lng']);
		
		foreach($AZams as $AZam)
		{//Делим текст на сегменты по 200 символов
			$range=floor($AZam['Start']/$SegmentRange);
			if($range==floor($AZam['End']/$SegmentRange))//если текст только в одном сегменте 
			{
				if(!isset($Ranges[$range]))	
					$Ranges[$range]=Array();
				$Ranges[$range][]=$AZam;
			}
			else
				$Ex[]=$AZam;
		}
		foreach($Ex as $AZam)
		{	//если в обоих диапозонах нет замен, то добавляем замену
			$range  = floor($AZam['Start']/$SegmentRange);
			$range2 = floor($AZam['End']/$SegmentRange);
			if(!isset($Ranges[$range]) && !isset($Ranges[$range2]))	
			{
				$Ranges[$range]=Array();
				$Ranges[$range][]=$AZam;			
			}
		}
		$Final=Array();
		$WasURLs=Array();
		$CurURLs=Array();
		if(!$GLOBALS['htracer_context_links_b']||$GLOBALS['htracer_context_links_b']!=='only_first')
		{
			$tURLS=&$CurURLs;
			if(!$GLOBALS['htracer_context_links_b'])
				$tURLS=&$WasURLs;

			$tURLS=Array($_SERVER["REQUEST_URI"]=>1,$_SERVER["REQUEST_URI"].'/'=>1);
			if(isset($_SERVER["REDIRECT_URL"]) && $_SERVER["REDIRECT_URL"])
			{
				$tURLS[$_SERVER["REDIRECT_URL"]]=1;
				$tURLS[$_SERVER["REDIRECT_URL"].'/']=1;
			}			
		}

		foreach($Ranges as $i=>$Range)
		{
			if(!$Range || !count($Range))
				continue;
			$maxEva =-1000;
			$maxZam =false;
			foreach($Range as $Zam)
			{
				if(($Zam['End']-$Zam['Start'])<4)
					continue;
				if(isset($WasURLs[$Zam['URL']]) && !isset($CurURLs[$Zam['URL']]))
					continue;
				if($maxEva<$Zam['Eva'])
				{
					$maxEva=$Zam['Eva'];
					$maxZam=$Zam;
				}
			}
			if($maxZam === false)
				continue;
			$maxZam0=$maxZam;
			$maxEva =-1000;	
			if(count($Range)!==1)
			{
				foreach($Range as $Zam)//Бумага кроет камень
				{
					if(isset($WasURLs[$Zam['URL']])||($Zam['End']-$Zam['Start'])<4)
						continue;
					if($Zam['Start']==$maxZam0['Start'] && $Zam['End']==$maxZam0['End'])
						continue;
					if($Zam['Start']<=$maxZam0['Start'] && $Zam['End']>=$maxZam0['End'] && $maxEva<$Zam['Eva'])
					{
						$maxEva=$Zam['Eva'];
						$maxZam=$Zam;
					}
				}
			}
			$Final[]= $maxZam;
			$WasURLs[$maxZam['URL']]=1;
		}
		//$Final2=Array();
		//echo '<pre>';
		//print_r($Final);
		
		//Упорядочиваем ссылки по убыванию оценки
		$MaxLinks=10;
		if(isset($GLOBALS['htracer_max_clinks'])
		&& $GLOBALS['htracer_max_clinks']
		&& is_numeric($GLOBALS['htracer_max_clinks'])
		&& intval($GLOBALS['htracer_max_clinks'])>0)
			$MaxLinks=intval($GLOBALS['htracer_max_clinks']);
		if($MaxLinks<count($Final))
		{
			uasort($Final,'ht_cl_eva_cmp');
			$i=0;
			foreach($Final as $K=>$Cur)
			{//Удаляем ссылки, если их больше чем нужно
				if(HTracer::isCurPage($Cur['URL']))
					continue;
				$i++;
				if($i>$MaxLinks)
					unset($Final[$K]);
			}
		}
		return $Final;
	}
	function ht_cl_eva_cmp($a, $b) 
	{
		if ($a['Eva'] == $b['Eva']) 
			return 0;
		else
			return ($a['Eva'] > $b['Eva']) ? -1 : 1;
	}

	function CalculateGZams0($Passages,$Len,$encoding=false)
	{//высчитывает гипотетически возможные замены $Len - длина в словах
		$Res0=Array();
		if(!$encoding)
			$encoding=$GLOBALS['htracer_encoding'];
		foreach($Passages as $Passage)
		{
			$count=count($Passage);
			foreach($Passage as $i=>$Word)
			{
				if($i+$Len>$count)
					break;
				if($Word->isStop||($Len==1 && strlen($Word->Str)<5))
					continue;
				$cur=Array();
				for($j=$i;$j<$i+$Len;$j++)
					$cur[]=$Passage[$j];
				if(count($cur)==$Len && !$cur[0]->isStop && !$cur[$Len-1]->isStop)
					$Res0[]=$cur;	
			}
		}
		$Res=Array();
		foreach($Res0 as $cur0)
		{
			$Cur=Array();
			$CS=0;
			$Str='';
			$sCS=0;
			$fCS=0;
			$wn=1;
			$isAllSiteStop=true;
			foreach($cur0 as $Word)
			{
				if(!$Word->isSiteStop)
				{
					$isAllSiteStop=false;
					break;
				}
			}
			foreach($cur0 as $Word)
			{
				if(!$Word->isStop)
				{
					if(!$Word->isSiteStop||$isAllSiteStop)
						$CS+= $Word->CS;
					$fCS+=$Word->CS;
					$sCS+=$Word->CS  * $wn * 773;
				}
				$Str.=$Word->Str.' ';
				$wn++;
			}
			$Cur['Len']   = $Len;
			$Cur['CS']    = $CS;
			$Cur['sCS']   = $sCS;
			$Cur['fCS']   = $fCS;
			$Cur['Str']   = $Str;
			$Cur['Start'] = $cur0[0]->Start;
			$Cur['End']   = $cur0[$Len-1]->End;
			$Res[]=$Cur;
		}
		return $Res;
	}
?>