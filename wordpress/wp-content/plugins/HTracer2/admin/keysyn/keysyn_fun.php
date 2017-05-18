<?php
	header ("Content-type: text/html;charset=UTF-8");
	setlocale(LC_ALL, "ru_RU.UTF-8");

	include_once("Snoopy.class.php");
	if(file_exists('HStem.php'))
		include_once('HStem.php');
	else
	{
		$back=substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), "\\"));
		include_once($back.'\HStem.php');
	}
	
	srand();
	set_time_limit(100);
	error_reporting (E_ERROR | E_PARSE| E_WARNING);

	$StopWords=Array('свои'=>1,'себя'=>1,'а'=>1,'без'=>1,'более'=>1,'бы'=>1,'был'=>1,'была'=>1,'были'=>1,'было'=>1,'быть'=>1,'в'=>1,'вам'=>1,'вас'=>1,'весь'=>1,'во'=>1,'вот'=>1,'все'=>1,'всего'=>1,'всех'=>1,'вы'=>1,'где'=>1,'да'=>1,'даже'=>1,'для'=>1,'до'=>1,'его'=>1,'ее'=>1,'если'=>1,'есть'=>1,'еще'=>1,'же'=>1,'за'=>1,'здесь'=>1,'и'=>1,'из'=>1,'или'=>1,'им'=>1,'их'=>1,'к'=>1,'как'=>1,'ко'=>1,'когда'=>1,'кто'=>1,'ли'=>1,'либо'=>1,'мне'=>1,'может'=>1,'мы'=>1,'на'=>1,'надо'=>1,'наш'=>1,'не'=>1,'него'=>1,'нее'=>1,'нет'=>1,'ни'=>1,'них'=>1,'но'=>1,'ну'=>1,'о'=>1,'об'=>1,'однако'=>1,'он'=>1,'она'=>1,'они'=>1,'оно'=>1,'от'=>1,'очень'=>1,'по'=>1,'под'=>1,'при'=>1,'с'=>1,'со'=>1,'так'=>1,'также'=>1,'такой'=>1,'там'=>1,'те'=>1,'тем'=>1,'то'=>1,'того'=>1,'тоже'=>1,'той'=>1,'только'=>1,'том'=>1,'ты'=>1,'у'=>1,'уже'=>1,'хотя'=>1,'чего'=>1,'чей'=>1,'чем'=>1,'что'=>1,'чтобы'=>1,'чье'=>1,'чья'=>1,'эта'=>1,'эти'=>1,'это'=>1,'я'=>1);
	
	$snoopy = new Snoopy;
	$snoopy->agent 	 = "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13";
	$snoopy->rawheaders["Accept"] = "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
	$snoopy->rawheaders["Accept-Language"] = "ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3";
	$snoopy->rawheaders["Accept_charset"] = "windows-1251,utf-8;q=0.7,*;q=0.7";
	$snoopy->rawheaders["Accept_encoding"] = "identity";
	$snoopy->rawheaders["Connection"] = "Keep-Alive";
	//двусловники
	$SimpleReplaces=Array
	(
		//Array('from'=>'цена', 'to'=>'цены')
	);
	//корректировочные замены
	$CorrectReplaces=Array
	(
		' цена характеристики'=>' цены и характеристики'
	);
	$Replaces0=Array
	(
		//no_pril не доделан
		Array('from'=>'цена', 'to'=>'цены'  ,'reverse'=>true  ,'no_pril'=>true),
		Array('from'=>'недорого'    		, 'to'=>Array('дешево','дёшево')  ,'reverse'=>true),
		Array('from'=>'недорогие'   		, 'to'=>Array('дешевые'),'reverse'=>true),
		Array('from'=>'недорогой'   		, 'to'=>Array('дешевый'),'reverse'=>true),
		Array('from'=>'недорогая'  			, 'to'=>Array('дешевая'),'reverse'=>true),
		Array('from'=>'недорогое'   		, 'to'=>Array('дешевое'),'reverse'=>true),
		Array('from'=>'скачать'     		, 'to'=>Array('скачать бесплатно','бесплатно скачать'),'reverse'=>true),
		//Array('from'=>'скачать бесплатно'   , 'to'=>'бесплатно скачать','reverse'=>true),
		Array('from'=>'скачать'     		, 'search'=>'start','place'=>'end','max'=>2,'reverse'=>true),
		Array('from'=>'купить'      		, 'to'=>Array('купить недорого','недорого купить'),'reverse'=>true),
		Array('from'=>'купить'      		, 'search'=>'start','place'=>'end','max'=>2,'reverse'=>true),
		Array('from'=>Array('бесплатные','бесплатная','бесплатно','бесплатное','бесплатные')
					,'to'=>'бесплатно','place'=>'end', 'search'=>'start','place'=>'end'),
		Array('from'=>Array('дёшево','дешево','дешевые','дешевая','дешевое','дешевый','недорого','недорогие','недорогой','недорогое','недорогая')
					,'to'=>Array('дешево','недорого','дёшево')
					,'place'=>'end', 'search'=>'start','place'=>'end'),	
		Array('from'=>'недорого', 'to'=>Array('дешевые','недорогие'),'place'=>'start','mn'=>true),
		Array('from'=>'дешево', 'to'=>Array('дешевые','недорогие'),'place'=>'start','mn'=>true),
		Array('from'=>'дёшево', 'to'=>Array('дешевые','недорогие'),'place'=>'start','mn'=>true),
		Array('from'=>'бесплатно', 'to'=>'бесплатные','place'=>'start','mn'=>true),
		Array('from'=>'купить', 'search'=>'start', 'to'=>'купить недорогие','mn'=>true,'mark'=>'xxxx'),
		Array('from'=>'купить', 'search'=>'end', 'place'=>'start', 'to'=>'купить недорогие','mn'=>true)
	);
	$tReplaces=Array();
	$iReplaces=Array();//Индекс замен
	$SynReplaces=Array();//Проверка на синонимы
	СompileReplaceBase();
	function СompileReplaceBase()
	{	
		global $Replaces0,$tReplaces,$iReplaces,$SynReplaces;
		$tReplaces=Array();
		$iReplaces=Array();
		$SynReplaces=Array();//Проверка на синонимы
		foreach($Replaces0 as $Replace)
		{
			if(isset($Replace['from']) && !is_array($Replace['from']))
				$Replace['from']=Array($Replace['from']);
			if(isset($Replace['to']) && !is_array($Replace['to']))
				$Replace['to']=Array($Replace['to']);
			elseif(!isset($Replace['to']))
				$Replace['to']=$Replace['from'];
		
			if($Replace['reverse'])
			{
				unset($Replace['reverse']);
				$cReplace=$Replace;
				$cReplace['to']=$Replace['from'];
				$cReplace['from']=$Replace['to'];
				$cReplace['place']=$Replace['search'];
				$cReplace['search']=$Replace['place'];
				$tReplaces[]=$cReplace;
			}
			$tReplaces[]=$Replace;
		}
		$Replaces=$tReplaces;
		foreach($Replaces as $Replace)
		{
			foreach($Replace['from'] as $Word)
			{
				if(!isset($iReplaces[$Word]))
				$iReplaces[$Word]=Array();
				$tReplace=$Replace;
				unset($tReplace['from']);
				foreach($Replace['to'] as $tWord)
				{
					$tReplace['to']=$tWord;
					$iReplaces[$Word][]=$tReplace;
					$SynReplaces[$Word][$tWord]=1;
					$SynReplaces[$tWord][$Word]=1;
				}
			}
		}
		//Слова из каждого сета не должны встречаться в одном кее
		$AddSynReplaces=Array(
			Array('недорого','недорогие','недорогая','недорогой','недорогое',
				'дёшево','дешевый','дешевые','дешевое','дешевая','дешевое',
				'выгодно'),
			Array('купить','покупать','куплю','покупка','приберести'),
			Array('цена','цены','стоимость'),
			Array('скачать','смотреть')
		);
		foreach($AddSynReplaces as $Set)
		{
			foreach($Set as $W1)
			{
				if(!isset($SynReplaces[$W1]))
					$SynReplaces[$W1]=Array();
				foreach($Set as $W2)
					$SynReplaces[$W1][$W2]=1;
			}
		}
	}
	function DoVariatesInKey(&$Variantes,$Deep=0,$ta=Array())
	{
		global $iReplaces,$SynReplaces;
		$Out=Array();
		foreach($Variantes[$Deep] as $Replace)
		{
			if($Replace['place'])
				$Cur=Array($Replace['place']=>$Replace['to']);
			else
				$Cur=Array('words'=>Array($Replace['to']));
			if($Replace['mn'])
				$Cur['mn']=$Replace['to'];
			if($Replace['max'])
				$Cur['max']=$Replace['max'];
			if($Replace['min'])
				$Cur['min']=$Replace['min'];
			if($Replace['mark'])
				$Cur['mark']=$Replace['mark'];
			$Out[]=$Cur;
		}
		if(count($Variantes)>$Deep+1)
		{
			$Out0=Array();
			$Addons=DoVariatesInKey($Variantes,$Deep+1,$ta);
			foreach($Addons as $Addon)
			{
				foreach($Out as $Do)
				{
					if(($Do['start'] && $Addon['start'])||($Do['end'] && $Addon['end']))
						continue;
					$Temp=array_merge_recursive($Do,$Addon);
					$Out0[]=$Temp;
				}
			}
			$Out=$Out0;
			if($Deep==0)
			{
				$Out0=Array();
				foreach($Out as $Do)
				{	
					$base=JOIN(' ',$Do['words']);
					if($base && $Do['start'])
						$base=' '.$base;
					$base=$Do['start'].$base;
					if($base && $Do['end'])
						$base=$base.' ';
					$base=trim($base.$Do['end']);
					if($Do['max'] || $Do['min'])
					{
						$cw=count(split(' ',$base));
						if(($Do['max'] && $cw>$Do['max'])
						 ||($Do['min'] && $cw<$Do['min']))
							continue;
					}
					if($Do['mn'])	
					{
						$base2=' '.$base.' ';	
						$base2=str_replace(' '.$Do['mn'].' ','',$base2);
						$base2=str_replace(Array(' скачать ',' купить ',' куплю ',' бесплатно ',' недорого ',' дёшево ',' дешево '),'',$base2);
						$base2=str_replace('  ',' ',trim($base2));
						$base2=str_replace('  ',' ',$base2);
						$base2=str_replace('  ',' ',$base2);
						if(!is_key_mn($base2))
							continue;
					}
					$Out0[]=$base;
				}
				$Out=$Out0;
				$Out0=Array();
				foreach($Out as $base)
				{	
					$test_arr=Array();
					$Abase=split(' ',$base);
					foreach($Abase as $W)
					{
						if(isset($test_arr[$W])||isset($ta[$W]))
							continue 2;
						$test_arr[$W]=1;							
					}
					$Out0[]=$base;
				}
				if(count($Out0))
					$Out=$Out0;
				$Out0=Array();
				foreach($Out as $base)
				{	
					$Abase=split(' ',$base);
					foreach($Abase as $i => $W1)
					{
						if(isset($SynReplaces[$W1]))
						{
							foreach($Abase as $j => $W2)
							{
								if($i==$j || !isset($SynReplaces[$W2]))
									continue;
								if(isset($SynReplaces[$W1][$W2]))
									continue 3;
							}
						}
					}
					foreach($ta as $W1)
					{
						if(isset($SynReplaces[$W1]))
						{
							foreach($Abase as $j => $W2)
							{
								if($i==$j || !isset($SynReplaces[$W2]))
									continue;
								if(isset($SynReplaces[$W1][$W2]))
									continue 3;
							}
						}
					}
					$Out0[]=$base;
				}
				if(count($Out0))
					$Out=$Out0;					
			}
		}
		return $Out;
	}
	function GetZnWordsCount($Key)
	{	
		global $StopWords;
		$c=0;
		$Key=split(' ',trim($Key));
		foreach($Key as $Word)
			if(!isset($StopWords[$Word]))
				$c++;
		return $c;
	}
	function DoReplacesInKey($Key,$AllowMn=true,$ta=Array())
	{		
		global $iReplaces,$SynReplaces;
		global $SimpleReplaces,$CorrectReplaces;
		if($AllowMn)
		{
			$Mn=key_to_mn($Key);
			if($Mn)
			{
				$a1=DoReplacesInKey($Key,false,$ta);
				$a2=DoReplacesInKey($Mn,false,$ta);
				foreach($a2 as $w)
					$a1[]=$w;
				return $a1;
			}
		}
		$Key0=$Key;
		$Key=split(' ',trim($Key));
		$Variantes=Array();
		foreach($Key as $i=>$Word)
		{
			$Variantes[$i]=Array();
			$Variantes[$i][]=Array('to'=>$Word);
			if(!isset($iReplaces[$Word]))
				continue;
			foreach($iReplaces[$Word] as $Replace)
			{
				if(!$Replace['search']
				||($Replace['search']=='start' && $i==0)
				||($Replace['search']=='end' && $i==count($Key)-1))
					$Variantes[$i][]=$Replace;
			}
		}
		foreach($Variantes as $Variant)
		{
			$have=false;
			if(count($Variant)==0)
				return Array($Key0);
			if(count($Variant)>1)
			{
				$have=true;
				break;
			}
		}
		if($have)
			$New=DoVariatesInKey($Variantes,0,$ta);
		else
			$New=Array();
		$New[]=$Key0;

		static $CorrectReplacesFrom=false;
		static $CorrectReplacesTo=false;
		if($CorrectReplacesFrom)
		{
			$CorrectReplacesFrom=array_keys($CorrectReplaces);
			$CorrectReplacesTo=array_values($CorrectReplaces);
		}
		//корретироваочные замены
		foreach($New as $i=>$key)
			$New[$i]=str_replace($CorrectReplacesFrom, $CorrectReplacesTo, $key);
		
		//простые замены
		$New2=Array();
		foreach($New as $i=>$key)
		{
			foreach($SimpleReplaces as $Replace)
				if(strpos($key,$Replace['from']))
					$New2[]=str_replace($Replace['from'],$Replace['to'], $key);
			$New2[]=$key;
		}
		
		//чистим дубли
		$New=Array();
		$Was=Array();
		foreach($New2 as $i=>$key)
		{
			if(!isset($Was[$key]))
			{
				$Was[$key]=1;
				$New[]=$key;
			}
		}
		return $New;
	}
	//город только из одного слова
	$WordStatCities=Array
	(
		array('Москва', 'Москвы', 'в Москве', 'Россия','московские','мск'),
		array('Владивосток', 'Владивостока', 'во Владивостоке', 'Россия'),
		array('Самара', 'Самары', 'в Самаре', 'Россия','самарские'),
		array('Саратов', 'Саратова', 'в Саратове', 'Россия','саратовские'),
		array('Тюмень', 'Тюмени', 'в Тюмене', 'Россия','тюменские'),
		array('Новгород', 'Новгорода', 'в Новгороде', 'Россия','новгородские'),
		array('Белгород', 'Белгорода', 'в Белгороде', 'Россия','белгородские'),
		array('Мурманск', 'Мурманска', 'в Мурманске', 'Россия','мурманские'),
		array('Екатеринбург', 'Екатеринбурга', 'в Екатеринбурге', 'Россия'),
		array('Калинград', 'Калинграда', 'в Калинграде', 'Россия'),
		array('Сочи', 'Сочи', 'в Сочи', 'Россия'),
		array('Одесса', 'Одессы', 'в Одессе', 'Украина','одесские','одеса'),
		array('Киев', 'Киева', 'в Киеве', 'Украина','киевские'),
		array('Харьков', 'Харькова', 'в Харькове', 'Украина','харьковские'),
		array('Львов', 'Львова', 'во Львове', 'Украина','львовские'),
		array('Днепропетровск', 'Днепропетровска', 'в Днепропетровске', 'Украина','днепропетровские'),
		array('Кировоград', 'Кировограда', 'в Кировограде', 'Украина','кировоградские'),
		array('Житомир', 'Житомира', 'в Житомире', 'Украина'),
		array('Жмеринка', 'Жмеринки', 'в Жмеринке', 'Украина'),
		array('Донецк', 'Донецка', 'в Донецке', 'Украина'),
		array('Запорожье', 'Запорожья', 'в Запорожье', 'Украина')
	);
	//эта вся радость нужна чтобы тормозов не было
	$WordStat_StartCities = Array();
	$WordStat_EndCities   = Array();
	$WordStat_AllCities   = Array(); 
	foreach($WordStatCities as $i => $lCity)
	{
		$upCity=$lCity;
		$lCity[0]=mb_strtolower($lCity[0],'utf-8');
		$lCity[1]=mb_strtolower($lCity[1],'utf-8');
		$lCity[2]=mb_strtolower($lCity[2],'utf-8');
		$CityList=Array($upCity,$lCity);
		foreach($CityList as $City)
		{
			$WordStat_AllCities[]=' '.$City[0].' ';
			$WordStat_AllCities[]=' '.$City[1].' ';
			$WordStat_AllCities[]=' '.$City[2].' ';
			
			$WordStat_StartCities[$City[0]]=$i;
			$WordStat_StartCities[$City[0].',']=$i;
			$WordStat_EndCities[$City[0]]=$i;
			$WordStat_EndCities[$City[1]]=$i;
			$City[2]=split(' ',$City[2]);
			$City[2]=$City[2][count($City[2])-1];
			$WordStat_EndCities[$City[2]]=$i;
		}
	}
	function key_to_mn2($Key)
	{	
		if(key_to_mn($Key))	
			return trim(key_to_mn($Key));
		return $Key;
	}
	function key_to_mn($Key)
	{
		//Удадяем стоп слова справа и слева
		static $Stop=Array('купить'=>'','дешево'=>'','дёшево'=>'','недорого'=>'','скачать'=>'','цена'=>'','цены'=>'','бесплатно'=>'');
		$First=Array();
		$Last=Array();
		$Key=split(' ',trim($Key));
		$Arr=$Key;
		for($i=0;$i<count($Key)-1;$i++)
		{
			if(!isset($Stop[$Key[$i]]))
				break;
			$First[]=$Key[$i];	
			unset($Arr[$i]);
		}
		$Key=JOIN(' ',$Arr);
		$Key=split(' ',trim($Key));
		$Arr=$Key;
		for($i=count($Key)-1;$i>0;$i--)
		{
			if(!isset($Stop[$Key[$i]]))
				break;
			$First[]=$Key[$i];
			unset($Last[$i]);
		}
		$Key=JOIN(' ',$Arr);
		$Key=split(' ',trim($Key));
		
		if(count($Key)>2||count($Key)==0
		||(count($Key)==1 && isset($Stop[$Key[0]])))
			return false;
		//Обрабатываем существительное
		static $Sysh=Array
		(
			'т '=>'ты',
			'ца '=>'цы',
			'ль '=>'ли',
			'р '=>'ры',
			'вие '=>'вия',
			'ние '=>'ния',
			'н '=>'ны',
			'нка '=>'нки',
			'нк '=>'нки',
			'ук '=>'уки',
			'нг '=>'нги',
			'рс '=>'рсы',
			'льм ' =>'льмы'
		);
		static $SyshFrom=false; if(!$SyshFrom) $SyshFrom=array_keys($Sysh);
		static $SyshTo  =false; if(!$SyshTo)   $SyshTo  =array_values($Sysh);
		$Res='';
		if(count($Key)==1)
		{
			$Key0=trim(str_replace($SyshFrom,$SyshTo,$Key[0].' '));
			if($Key0==$Key[0]||$Key0{0}!=$Key[0]{0}||$Key0{1}!=$Key[0]{1}||$Key0{2}!=$Key[0]{2})
				return false;
			$Res= $Key0; 
		}
		//Обрабаьтываем прилагательное
		static $Pril=Array
		(
			'ная '=>'ные',
			'ный '=>'ные',
			'ное '=>'ные',
			'кий '=>'кие',
			'кая '=>'кие',
			'кое '=>'кие',
			'вый '=>'вые',
			'вая '=>'вые',
			'вой '=>'вые',
			'гой '=>'гие',
			'гая '=>'гие',
			'гие '=>'гие',
		);
		static $PrilFrom=false;if(!$PrilFrom) $PrilFrom=array_keys($Pril);
		static $PrilTo  =false;if(!$PrilTo)   $PrilTo  =array_values($Pril);
		if(count($Key)==2)
		{
			$Key0=trim(str_replace($PrilFrom,$PrilTo,$Key[0].' '));
			if($Key0==$Key[0]||$Key0{0}!=$Key[0]{0}||$Key0{1}!=$Key[0]{1}||$Key0{2}!=$Key[0]{2})
				return false;
			$Key1=trim(str_replace($SyshFrom,$SyshTo,$Key[1].' '));
			if($Key1==$Key[1]||$Key1{0}!=$Key[1]{0}||$Key1{1}!=$Key[1]{1}||$Key1{2}!=$Key[1]{2})
				return false;
		}
		$Res=$Key0.' '.$Key1;
		if($First && count($First))
			$Res=$Res.' '. JOIN(' ',$First);
		if($Last && count($Last))
			$Res= JOIN(' ',$Last).' '.$Res;
		$Res=str_replace('  ',' ',$Res);
		$Res=str_replace('  ',' ',$Res);
		$Res=str_replace('  ',' ',$Res);
		$Res=str_replace('  ',' ',$Res);
		return trim($Res); 
	}
	function is_key_mn($Key)
	{//множественное ли число вернет нет если не уверен
		
		$Key=str_replace(Array(' купить ',' бесплатно ',' дешево ', ' скачать '),' ',' '.$Key.' ');
		$Key=trim($Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$City=GetCityFromKeyWord($Key);
		if($City)
			$Key=$City['clear'];
		$Key=split(' ',$Key);
		if(count($Key)>2||!count($Key))
			return false;

		//Проверяем последнее слово	Должно быть существительным
		static $Sysh=Array('ты','цы','ли','ры','вия','ния','ны','ки');
		$Last=$Key[count($Key)-1];
		$cLast=strlen($Last);
		$Was=false;
		foreach($Sysh as $Okon)	
		{
			$cOkon=strlen($Okon);
			if($cOkon>=$cLast)
				continue;
			for($i=0;$i<$cOkon;$i++)
				if($Last{$cLast-$i-1}!=$Okon{$cOkon-$i-1})
					continue 2;
			$Was=true;
			break;
		}
		if(!$Was)	
			return false;
		if($Was && count($Key)==1)
			return true;
		//Проверяем первое слово Должно быть прилагательным
		$F=$Key[0];
		$l=count($F);
		return ($F[$l-1]=='е' && ($F{$l-2}=='и'||$F{$l-2}=='ы'));
	}
	function AddKeyToArrays($Key,$isClear,$Mark, &$Clear,&$Dirty)
	{
		if($isClear)
		{
			$Clear[$Key]=$Mark;
			unset($Dirty[$Key]);
		}
		elseif(!isset($Clear[$Key]) && !isset($Dirty[$Key]))
			$Dirty[$Key]=$Mark;
	}
function randomcmp($a, $b)
{
  return mt_rand(-1, 1);
}

function swapshuffle(&$array)
{
  srand((double) microtime() * 10000000);
  uksort($array, "randomcmp");
}	
	function is_same_key($Key1,$Key2)
	{
		global $StopWords;
		$cKey1=ClearCityFromKeyWord($Key1);
		$cKey2=ClearCityFromKeyWord($Key2);
		$Key1=trim($Key1);
		$Key2=trim($Key2);
		$cKey1=trim($cKey1);
		$cKey2=trim($cKey2);
		$mKey1=key_to_mn2($cKey1);
		$mKey2=key_to_mn2($cKey2);
		if($Key1==$Key2||$cKey1==$cKey2||$cKey1==$mKey2||$mKey1==$cKey2)
			return true;
		//print_r(Array(array($Key1,$cKey1,$mKey1),array($Key1,$cKey1,$mKey1)));
		//return false;
		$A1=split(' ',$mKey1);
		$A2=split(' ',$mKey2);
		
		//Стоп слова
		//Слово формы популярных слов
		static $FormsSets = Array(
			Array('купить','куплю'),
			Array('дешево','дёшево','дешевой','дешевые','дешевое','дешевая'),
			Array('недорого','недорогой','недорогая','недорогие','недорогое'),
			Array('бесплатно','бесплатный','бесплатные','бесплатное','бесплатная'),
			Array('цена','цены','цене')
		);
		static $Forms=false;
		if(!$Forms)	
		{
			$Forms=Array();
			foreach ($FormsSets as $Set)
			{
				foreach ($Set as $W1)
				{	
					$Forms[$W1]=Array();
					foreach ($Set as $W2)
						$Forms[$W1][]=$W2;
				}
			}
		}
		$Assoc=Array();
		$c1=0;
		$c2=0;
		foreach($A1 as $i => $Word)
		{
			if(isset($StopWords[$Word]))
				continue;
			$Assoc[$Word]=1;
			$c1++;
		}		
		foreach($A2 as $i => $Word)
		{
			if(isset($StopWords[$Word]))
				continue;
			$c2++;
			if(!isset($Assoc[$Word]))
			{
				$was=false;
				if($Forms[$Word])
					foreach ($Forms[$Word] as $Form)
						if(isset($Assoc[$Form])){$was=true; break;}
				if(!$was){$c2=-1; break;}
			}
		}	
		return ($c2===$c1);
	}
	function GetVariantAndUnset(&$Variants,$Random=true)
	{
		global $GetVariantAndUnset_LastKey;
		if(!count($Variants))
			return NULL;
		$keys=array_keys($Variants);
		if($Random)
			$key=$keys[rand()%count($Variants)];
		else
			$key=$keys[0];
		$GetVariantAndUnset_LastKey=$key;	
		$Res= $Variants[$key];
		unset($Variants[$key]);
		return $Res;
	}

	$GetVariant_LastKey=NULL;
	function GetVariant(&$Variants,$Random=true)
	{
		global $GetVariant_LastKey;
		if(!count($Variants))
			return NULL;
		$keys=array_keys($Variants);
		if($Random)
			$key=$keys[rand()%count($Variants)];
		else
			$key=$keys[0];	
		$GetVariant_LastKey=$key;
		return $Variants[$key];
	}
	function AddOkoloSilochnoe(&$Base,$BaseKey,$Anchor,$Text,$Count)
	{//Фильтрует околоссылочное
		if($Anchor==$Text||strlen($Text) - strlen($Anchor)<2)
			return false;
		$Words=split(' ',$Text);
		$First = $Words[0];
		$Last  = $Words[count($Words)-1];
		if((is_numeric($First) && strpos($Anchor,$First.' ')===false && strlen($First)!=4)
		 ||(is_numeric($Last)  && strpos($Anchor,' '.$Last)===false  && strlen($First)!=4))
			return false;
		if(count($Base[$BaseKey]['variantes'][$Anchor])
		&&(($Base[$BaseKey]['count']>300 && $Count<10)
		 ||($Base[$BaseKey]['count']>500 && $Count<20)
		 ||($Base[$BaseKey]['count']>1000 && $Count<50)
		 ||($Base[$BaseKey]['count']>5000 && $Count<100)))
			return false;/**/
		if(count($Base[$BaseKey]['variantes'][$Anchor])/2>$Count)
			return false;
		$Okolo=trim(str_replace($Anchor,'',$Text));
		if($Okolo=='ru'||$Okolo=='ua'||$Okolo=='com'||$Okolo=='ру')
			return false;

		$Base[$BaseKey]['variantes'][$Anchor][]=$Text;
		return true;
	}
	function is_key_obj($Key)
	{
		static $isObject=Array(' дом',' квартир',' участк',' участок',' вил',' коттедж',' котедж',' комнат',' музе',' гостиниц', ' отел',' достоприм',' кафе',' ресторан',' бар');
		return $Key!=trim(str_replace($isObject,' ', ' '.$Key.' '));
	}
	$AddWords=Array(
			'купить'=>15,'скачать'=>7,'бесплатно'=>15,
			'отзывы'=>50,'цены'=>40,'цена'=>40,
			'недорого'=>50, 'недорогие'=>'недорого'
	);
	$AddWordsEx=Array
	(
		'купить' =>Array(' сним',' посуточн',' сутк',' срок',' суток',' длите',' долгосрочн',' сдаю',' аренд',' снять',' сдать'
						,'прода',' как',' где ',' отзыв',' обзор',' стат','купить ', ' покуп',' бесплатн',' скачать ',' куп',','
						,' распис',' расcпис',' афиш',' реперту'),
		'скачать'=>Array('прода',' как',' где ',' отзыв',' обзор',' стат','купить ', ' покуп',' бесплатн',' скачать ',' куп',','
							,' дешев',' дёшев','дорог',' цен',' склад',' кредит'),
		'бесплатно'=>Array('прода',' как',' где ',' отзыв',' обзор',' стат','купить ', ' покуп',' бесплатн',' скачать ',' куп',','
						,' дешев',' дёшев','дорог',' цен',' склад',' кредит'),
		'отзывы'=>Array('прода',' как',' где ',' отзыв',' обзор',' стат','купить ', ' покуп',' бесплатн',' скачать ',' куп',','
						,' дешев',' дёшев','дорог',' цен',' склад',' кредит'
						,' отзыв'),
		'цены'=>Array('прода',' как',' где ',' отзыв',' обзор',' стат','купить ', ' покуп',' бесплатн',' скачать ',' куп',','
						,' дешев',' дёшев','дорог',' цен',' склад',' кредит'
						,' отзыв', ' цен'
						,' распис',' расcпис',' афиш',' реперту'),
		'цена'=>Array('прода',' как',' где ',' отзыв',' обзор',' стат','купить ', ' покуп',' бесплатн',' скачать ',' куп',','
						,' дешев',' дёшев','дорог',' цен',' склад',' кредит'
						,' отзыв', ' цен'
						,' распис',' расcпис',' афиш',' реперту'),		
		'недорого'=>Array('прода',' как',' где ',' отзыв',' обзор',' стат','купить ', ' покуп',' бесплатн',' скачать ',' куп',','
						,' дешев',' дёшев','дорог',' цен',' склад',' кредит'
						,' отзыв', ' цен'
						,' распис',' расcпис',' афиш',' реперту')					
	);
	$SapeStartToEnd=Array(
			'купить'=>
				Array(
					'недорого'=>Array(' дешев',' дешёв','дорог','цен',' стоим'),
					'по низким ценам'=>Array('mn'=>'1111111',' дешев',' дешёв','дорог','цен',' стоим'),
					'по низкой цене'=>Array('mn'=>'0000000',' дешев',' дешёв','дорог','цен',' стоим'),
					'по оптовым ценам'=>Array('obj'=>'0000000','mn'=>'1111111',' дешев',' дешёв','дорог',' цен',' опт',' стоим'),
					'по оптовой цене'=>Array('obj'=>'0000000','mn'=>'0000000',' дешев',' дешёв','дорог',' цен',' опт',' стоим'),
					'оптом'=>Array('obj'=>'0000000','mn'=>'0000000',' дешев',' дешёв','дорог',' цен',' опт',' стоим'),
					'дешево'=>Array(' дешев',' дешёв','дорог','цен',' стоим'),
					'в рассрочку'=>Array('obj'=>'0000000',' дешев',' дешёв','дорог','цен', 'рассроч','расро', 'кредит'),
					'в кредит'=>Array('obj'=>'0000000',' дешев',' дешёв','дорог','цен', 'рассроч','расро', 'кредит'),
					'со склада'=>Array('obj'=>'0000000','дешев',' дешёв','дорог','цен'),
					'со склада'=>Array('obj'=>'0000000','дешев',' дешёв','дорог','цен'),					
				)
	);
	$AskWords=Array('как','где','какой','какая','какие','сколько');
	function  DelBredovoeOkolossilocnoe(&$Start,&$Anchor,&$End)
	{//Удаляет бред в около сссылочном. Иногда меняет околоссылочное
	//недорогие
		if(($Start=='самые'||$Start=='самый'||$Start=='самое'||$Start=='самая')
		&& strpos(' '.$Start,' лучш')===false)
		{
			$T=split(' ',$Anchor);
			$T=$T[0].'*';
			if($Start=='самые' && strpos($T,'ые*')===false && strpos($T,'ие*')===false)
				$Start='самые лучшие';
			elseif($Start=='самый' && strpos($T,'ый*')===false && strpos($T,'ий*')===false && strpos($T,'ой*')===false)
				$Start='самый лучший';
			elseif($Start=='самое' && strpos($T,'ое*')===false && strpos($T,'ее*')===false)
				$Start='самое лучшее';
			elseif($Start=='самая' && strpos($T,'ая*')===false && strpos($T,'ея*')===false)
				$Start='самое лучшая';
			else
				$Start='';
		}
		elseif(rand()%4==0 && ($End=='цены'||$End=='характеристики') && $Start.' '.$Anchor==trim(str_replace(Array(
				' стоим',' цен',' отзыв',' характер',' низк',' данные','высок','дешев','дёшев',' опт',' ттх', ' свойств',' параметр',' описан',' стат',' обзо'),'',' '.$Start.' '.$Anchor.' ')))
		{
			if(rand()%3==0) 			
				$End='характеристики и цены';
			else				
				$End='цены и характеристики';
		}
		elseif(rand()%3==0 && ($End=='отзывы'||$End=='описание') && $Start.' '.$Anchor==trim(str_replace(Array(
				' отзыв',' характер',' низк',' данные','высок','дешев','дёшев',' опт',' ттх', ' свойств',' параметр',' описан',' стат',' обзо'),'',' '.$Start.' '.$Anchor.' ')))
		{		
			if(rand()%3==0) 			
				$End='отзывы и описание';
			else				
				$End='описание и отзывы';
		}		
		elseif($End=='через интернет' && !$Start && $Anchor==trim(str_replace(Array(
				' купит',' купл',' снят', ' бесплатн',' аренд','брон','гостин','отел','номер'),'',' '.$Anchor.' ')))
				$Start='купить';
		elseif(($Start=='где'||$Start=='где недорого') && rand()%2 && $Anchor.' '.$End==trim(str_replace(Array(' можн',' мог',' лучш'),'',' '.$Anchor.' '.$End.' '))
		&& $Anchor.' '.$End!=trim(str_replace(Array(' купить '),'',' '.$Anchor.' '.$End.' ')))
		{
			if($Start=='где'|| rand()%6==0)
			{	
				if(rand()%3==0 && strpos($Anchor,'купить ')===0)
					$Start='где лучше';
				elseif(rand()%3==0 && strpos($Anchor,'купить ')===0)
					$Start='где лучше всего';
				else
					$Start='где можно';
			}
			else
			{			
				if(rand()%2 && strpos($Anchor,'купить ')===0)
				{
					if(rand()%2)
						$Start='где лучше'; 
					else
						$Start='где лучше всего'; 

					if((!$End||$End=='?')&&rand()%2)
						$End='недорого?';
					else
						$Start.=' недорого';
				}
				elseif(rand()%2)
					$Start='где можно недорого';
				else
					$Start='где недорого можно';
			}
		}
		elseif(($Start=='какой'||$Start=='какую'||$Start=='какое'||$Start=='какие') && rand()%2
		&& $Anchor.' '.$End!=trim(str_replace(Array(' купить '),'',' '.$Anchor.' '.$End.' ')))
		{
			$Start.=' лучше';
			if(rand()%2 && strpos($Anchor,'купить ')===0)
				$Start.= 'всего';
		} 
		elseif(($Start=='бу'||$Start=='б у'||$Start=='б/у') &&$Anchor.' '.$End==trim(str_replace(Array(
				' купит',' купл',' снят', ' бесплатн',' аренд'),'',' '.$Anchor.' '.$End.' ')))
		{
			if(rand()%2 && $Anchor.' '.$End==trim(str_replace(Array(
				'дорог','дешев','дёшев', 'цен',' стоим'),'',' '.$Anchor.' '.$End.' ')))
			{
				if(rand()%2 && !$End)
				{
					$Start='купить б/у';
					$End='недорого';
				}
				elseif(rand()%2==0)
					$Start='недорого купить б/у';
				else
					$Start='купить недорого б/у';
			}
			else
				$Start='купить б/у';
		}
		elseif($Anchor!=trim(str_replace(Array(
				' отел',' гостин',' санатор', ' рестор',' кафе'),'',' '.$Anchor.' ')))
		{
			if(stripos($Start,'купить')!==false)
				$Start='';
			if(stripos($End,'купить')!==false)
				$End='';
		}			
	}
	function GenerateClearKeys($Base,$Addon=0)
	{
		$ClearKeys=Array();
		foreach ($Base as $BaseKey =>$Data)
		{
			$Vars=array_keys($Data['variantes']);
			$Addon+=$Count;
			$Count=$Data['final_count'];
			if(!$Count)
				continue;
			if($Count>=count($Vars))
			{
				foreach($Vars as $Var)
				{
					$ClearKeys[]=$Var;
					$Count--;
					if(!$Count)
						break;
				}
			}
			else
			{
				if($Addon>0)
				{
					$delta=count($Vars)-$Count;
					if($delta>$Addon)
						$delta=$Addon;
					$Count+=$delta;
					$Addon-=$delta;
				}
				while($Count--)
					$ClearKeys[]=GetVariantAndUnset($Vars);
			}
		}
		return $ClearKeys; 
	}
	function SapeKeyFilter(&$Start,&$End,&$Anchor,$LogicRes,$City)
	{
	}
	function FinalizeSapeKeys($SapeKeys,$LogicRes,$City=false)
	{
		$ri=1;
		foreach($SapeKeys as $Anchor => $Text)
		{
			$ri++;
			srand(rand()*time() * microtime() * 13 * $ri);
			if(!$Text && !$Anchor)
				continue;
			if(is_int($Anchor))
				$Anchor=$Text;
			elseif(is_int($Text) || is_null($Text)|| is_bool($Text) || trim($Text)==='')
				$Text=$Anchor;
			$Anchor=trim($Anchor);
			$Text=trim($Text);
			$Arr=split('\#\*\#',str_replace($Anchor,'#*#',$Text));
			$Start=trim($Arr[0]);
			$End  =trim($Arr[1]);
		
			SapeKeyFilter($Start,$End,$Anchor,$LogicRes,$City);
		
			if($Start || $End)
				$cur=trim(trim($Start).' #a#'.trim($Anchor).'#/a# '.trim($End));
			else
				$cur=trim($Anchor);
			$cur=str_replace(' ?','?',$cur);
			$cur=str_replace(' ,',',',$cur);
			$cur=str_replace('  ',' ',$cur);
			$cur=str_replace('  ',' ',$cur);
			$cur=$cur.' '; 
			$cur=str_replace(' ru ','.ru ',$cur);
			$cur=str_replace(' com ','.com ',$cur);
			$cur=str_replace(' ua ','.ua ',$cur);
			$cur=str_replace(' com ua ','.com.ua ',$cur);
			if(rand()%2)
				$cur=str_replace('интернет магазин','интернет-магазин',$cur);
			if(rand()%2)
				$cur=str_replace(' б у ',' б/у ',' '.$cur.' ');

			$cur=trim($cur);
			$cur=str_replace('  ',' ',$cur);
			$cur=str_replace('  ',' ',$cur);
			$Sape0[]=$cur;
		}
		return $Sape0;
	}
	function FinalizeSapeKeys_old($SapeKeys,$LogicRes,$City=false)
	{
	//Пытется замутить нормальные ключи для сапы с околоссылочным
		global $WordStatCities, $WordStat_StartCities,$WordStat_EndCities,$AskWords, $SapeStartToEnd, $AddWordsEx, $AddWords;
		$ri=1;
		foreach($SapeKeys as $Anchor => $Text)
		{
			$ri++;
			srand(rand()*time() * microtime() * 13 * $ri);
			if(!$Text && !$Anchor)
				continue;
			if(is_int($Anchor))
				$Anchor=$Text;
			elseif(is_int($Text) || is_null($Text)|| is_bool($Text) || trim($Text)==='')
				$Text=$Anchor;
			$Anchor=trim($Anchor);
			$Text=trim($Text);
			$Arr=split('\#\*\#',str_replace($Anchor,'#*#',$Text));
			$Start=trim($Arr[0]);
			$End  =trim($Arr[1]);
			DelBredovoeOkolossilocnoe($Start,$Anchor,$End);
			$ClearAnhor=$Anchor;
			$tmc=GetCityFromKeyWord($Anchor);
			if($tmc)	
				$ClearAnhor=$tmc['clear'];
					
			if($Start=='расписание' 
			&& rand()%4==0
			&& strpos($Text,'движ')===false
			&& strpos($Anchor,'поезд')!==false)
				$Start='расписание движения';
			if(!$Start && !$End)
			{
				if(rand()%2==0 && trim($Anchor)!=trim(str_replace(array(
				'купить','снять','аренда'),'',' '.$Anchor.' '))
				&&trim($Anchor)==trim(str_replace(array(
				' кред','опт',' раср',' расср','недорог','дешев','дешёв','цен','стои','дорог',' лучш',' беспл'),'',' '.$Anchor.' ')))
				{
					if(rand()%2==0 && is_key_mn($Anchor))
					{
						if(rand()%2==0)
							$End='по низким ценам';
						elseif(rand()%2==0)
							$End='по оптовым ценам';
						elseif(rand()%2==0)
							$End='оптом';
						else
							$End='по оптовой цене';
					}
					elseif(rand()%4==0)
						$End='дешево';
					else
						$End='недорого';
				}
				elseif(rand()%4==0 && trim($Anchor)!=trim(str_replace(array('купить'),'',' '.$Anchor.' '))
				&&trim($Anchor)==trim(str_replace(array(
				' кред','опт',' раср',' расср','недорог','дешев','дешёв','цен','стои','дорог',' лучш',' беспл'),'',' '.$Anchor.' ')))
					$End='в кредит';
				elseif(rand()%2==0 && 
				trim($Anchor)!=trim(str_replace(array('цены','цена'),'',' '.$Anchor.' '))
				&&trim($Anchor)==trim(str_replace(array(
				' кред',' опт',' раср',' расср',' недорог',' дешев',' дешёв',' стои',' дорог',' лучш',' беспл'),'',' '.$Anchor.' '))
				&&is_key_mn(trim(str_replace(array('цены','цена'),'',$ClearAnhor))))
				{	
					if(rand()%4==0)
						$Start='дешевые';
					else
						$Start='недорогие';
				}
			}			
			if(!$Start)
			{
				if(rand()%2==0 && strpos($Anchor.' ','кредит ')===0
				&&trim($Anchor.' '.$End)==trim(str_replace(array('выгод','возьм','взят','дам','даю','купить'),'',' '.$Anchor.' '.$End.' ')))
				{
					$Start='взять';
					if(rand()%4==0)	
						$Start='выгодный';
					else if(rand()%6==0)	
						$Start='взять выгодный';
				}
				elseif(rand()%2==0 && strpos($Anchor.' ','кредиты ')===0
				&&trim($Anchor.' '.$End)==trim(str_replace(array('выгод','возьм','взят','дам','даю','купить'),'',' '.$Anchor.' '.$End.' ')))
					$Start='выгодные';
				elseif(rand()%2==0 && strpos($Anchor.' ','объявления ')===0
				&&trim($Anchor.' '.$End)==trim(str_replace(array('платн',' цен',' стоим',' недорог',' дешев',' дёшев',),'',' '.$Anchor.' '.$End.' ')))
				{	
					if(rand()%2==0)
						$Start='бесплатные';
					elseif(rand()%2==0)
						$Start='дать';
					else
						$Start='дать бесплатные';	
					if(rand()%10==0)
						$Start='частные';
					if(rand()%14==0)
						$Start='дать частные';	
				}
				elseif(rand()%2==0 && strpos($Anchor.' ','объявления ')===0
				&&trim($Anchor.' '.$End)==trim(str_replace(array(' доск',' цен',' стоим',' недорог',' дешев',' дёшев',),'',' '.$Anchor.' '.$End.' ')))
				{
					$Anchor=trim(str_replace('объявления ','объявлений ',$Anchor.' '));
					if(rand()%3==0 && strpos($Text.' ','платн')===0)
						$Start='доска бесплатных';
					else
						$Start='доска';
					if(rand()%4==0 && strpos($Text.' ','платн')===0)
						$Start='бесплатная доска';
					elseif(rand()%3==0)		
						$Start=trim(str_replace('доска ','доски ',$Start.' '));
				}
				elseif(rand()%3==0 && (strpos($Anchor.' ','банк ')===0 ||strpos($Anchor.' ','банки ')===0)
				&&trim($Anchor.' '.$End)==trim(str_replace(array('курсы','кред','валют','депоз','процен',' работ',' рефер',' заказ',' данн',' знан'),'',' '.$Anchor.' '.$End.' ')))
				{
					$Start='работа в';
					$Anchor=trim(str_replace('банк ','банке ',$Anchor.' '));
					$Anchor=trim(str_replace('банки ','банках ',$Anchor.' '));
				}
				elseif(rand()%3==0 && strpos($Anchor.' ','такси ')===0 
				&&trim($Anchor.' '.$End)==trim(str_replace(array(' маршрутн',' телефон',' заказ',' номер',' вызов'),'',' '.$Anchor.' '.$End.' ')))
				{
					$varr=Array('заказ', 'телефон', 'вызов', 'заказать', 'номер', 'номер телефона');
					$Start=$varr[rand()%count($varr)];
				}
				elseif(rand()%4==0 && strpos($Anchor.' ','такси ')===0 
				&&trim($Anchor.' '.$End)==trim(str_replace(array(' маршрутн',' дешев',' дёшев',' недорог',' бесплатн'),'',' '.$Anchor.' '.$End.' ')))
					$Start='недорогое';
				elseif(rand()%5==0 && strpos($Anchor,'такси ')===0  &&  strpos($Text,'маршрутн')===false &&  strpos($Text,'служб')===false)
					$Start='служба';
				elseif(rand()%3==0 && strpos($Anchor.' ','гостиницы ')===0 && strpos($Text,'отел')===false)
					$Start='отели и';
				elseif(rand()%4==0 && 
					(
						   strpos($Anchor.' ','гостиницы ')===0 
						|| strpos($Anchor.' ','отели ')===0
					)
				&& strpos($Text,'номер')===false)
				{
					if(strpos($Anchor.' ','гостиницы ')===0 )
						$Anchor=trim(str_replace('гостиницы ','гостиницах ',$Anchor.' '));
					else//if(strpos($Anchor.' ','отели ')===0 )
						$Anchor=trim(str_replace('отели ','отелях ',$Anchor.' '));
					$Start='номера в';
					if(rand()%4==0 
					&& mb_strlen($End.$Anchor,'utf-8')<70
					&& strpos($Text,'лучш')===false 
					&& strpos($Text,'недорог')===false
					&& strpos($Text,'дешев')===false
					&& strpos($Text,'дёшев')===false)
						$Start='номера в лучших';
				}
				elseif(rand()%4==0 && 
					(
						   strpos($Anchor.' ','гостиница ')===0 
						|| strpos($Anchor.' ','отель ')===0
						|| strpos($Anchor.' ','санатории ')===0
					)
				&& strpos($Text,'номер')===false)
				{
					if(strpos($Anchor.' ','гостиница ')===0 )
						$Anchor=trim(str_replace('гостиница ','гостинице ',$Anchor.' '));
					else//if(strpos($Anchor.' ','отель ')===0 )
						$Anchor=trim(str_replace('отель ','отеле ',$Anchor.' '));
					$Start='номера в';
					if(rand()%3==0 
					&& mb_strlen($End.$Anchor,'utf-8')<60
					&& strpos($Text,'брон')===false 
					&& strpos($Text,'заказ')===false
					&& strpos($Text,'снят')===false
					&& strpos($Text,'аренд')===false)
						$Start='забронировать номер в';
				}
				elseif(rand()%3==0 && strpos($Anchor.' ','отели ')===0 && strpos($Text,'гостиниц')===false)
					$Start='гостиницы и';
				elseif(rand()%3==0 
				&&(strpos($Anchor.' ','гостиницы ')===0 
				  ||strpos($Anchor.' ','отели ')===0
				  ||strpos($Anchor.' ','санатории ')===0)
				&& strpos($Text,'недорог')===false && strpos($Text,'лучш')===false && strpos($Text,'дешев')===false && strpos($Text,'дёшев')===false)
				{
					if(rand()%3==0)
						$Start='лучшие';
					$Start='недорогие';
				}
				elseif(rand()%5==0 
				&&(strpos($Anchor.' ','аптеки ')===0 
				  //||strpos($Anchor.' ','отели ')===0
				  //||strpos($Anchor.' ','санатории ')===0
				  )
				&& strpos($Text,'недорог')===false && strpos($Text,'лучш')===false && strpos($Text,'дешев')===false && strpos($Text,'дёшев')===false)
					$Start='недорогие';
				elseif(rand()%3==0 && strpos($Anchor.' ','аптеки ')===0 &&strpos($Text,'суто')===false)
					$Start='круглосуточные';
				elseif(rand()%4==0 && strpos($Anchor.' ','рестораны ')===0 && strpos($Text,'кафе')===false)
					$Start='кафе и';
				elseif(rand()%5==0 && strpos($Anchor.' ','рестораны ')===0 
				&& strpos($Text,'недорог')===false && strpos($Text,'лучш')===false && strpos($Text,'дешев')===false && strpos($Text,'дёшев')===false)
					$Start='лучшие';
				elseif(rand()%4==0 && strpos($Anchor.' ','рестораны ')===0 
				&& strpos($Text,'меню')===false && strpos($Text,'фото')===false)
				{
					$Anchor=trim(str_replace('рестораны ','ресторанов ',$Anchor.' '));
					if(rand()%3)
						$Start='меню';
					elseif(rand()%3)
						$Start='фотографии';
					else
						$Start='фото';
				}
				elseif(rand()%4==0 && strpos($Anchor.' ','рестораны ')===0 
				&& strpos($Text,'меню')===false && strpos($Text,'фото')===false && strpos($Text,'отзывы')===false)
					$Start='отзывы на';
				elseif(rand()%3==0 && strpos($Anchor.' ','погода ')===0 && strpos($Text,'прогноз')===false)
				{
					$Anchor=trim(str_replace('погода ','погоды ',$Anchor.' '));
					$Start='прогноз';
					if(rand()%4==0 && strpos($Text,' точн')===false)
						$Start='точный прогноз';
				}
				elseif(rand()%3==0 && strpos($Anchor.' ','поезд ')===0 
				&& strpos($Text,'распис')===false && strpos($Text,'движ')===false)
				{
					$Anchor=trim(str_replace('поезд ','поезда ',$Anchor.' '));
					$Start='расписание';
					if(rand()%3==0)
						$Start='расписание движения';
				}
				elseif(rand()%3==0 && strpos($Anchor.' ','билеты ')===0 
				&& strpos($Text,'купит')===false && strpos($Text,'цен')===false
				&& strpos($Text,'стоим')===false && strpos($Text,'купл')===false)
				{
					if(rand()%2)
						$Start='купить';
					else
						$Start='цены на';
				}
				elseif(rand()%5==0 && strpos($Anchor.' ','прогноз погоды ')===0 && strpos(' '.$Text,' точн')===false)
				{
					$Anchor=trim(str_replace('погода ','погоды ',$Anchor.' '));
					$Start='точный';
				}
				elseif(rand()%4==0
				&&	(  strpos($Anchor.' ','интернет магазин')===0 
					|| strpos($Anchor.' ','интернет-магазин')===0
					|| strpos($Anchor.' ','магазин')===0)
					&& trim($Anchor.' '.$End)==trim(str_replace(array(' недорог',' дешев',' дёшев',' цен',' опт'),'',' '.$Anchor.' '.$End.' ')))
							$Start='недорогой';
			}
			$Text=trim($Start).' '.trim($Anchor).' '.trim($End);
			if(!$End)
			{
				static $WeathePer=Array('на неделю','на месяц','на 15 дней','на 7 дней',
										'на 2 недели', 'на две недели', 'на 30 дней',
										'на 2 месяца', 'на два месяца');
				static $WeathePerEx=Array('1','2','3','4','5','6','7','8','9','0',
										' дво',' две',' один ',' одн',' три ',' трое',
										' четыр',' четве',' пять',' шесть',' семь',
										' пн ',' вт ',' ср ',' чт ',' пт ',' сб ',' вск ',
										' понед',' втор',' сред',' четвер',' пятни',
										' суббот',' субот',' воскр',
										' на ', ' дн',' месяц',' год',' недел',
										' лет',' зим',' осен',' весн',
										' январ',' феврал',' феврал',' март',' апрел',
										' мая ',' мае ',' май ',
										' июн',' июл',' август',' сентябр',' сентебр'
										,' ноябр',' ноебр',' октябр',' октебр',' дека'
										,' завтр',' послезавтр',' сегодн');
				static $ArendaFind=Array(' аренда дом',' снять дом',' сдам дом',' сдаю дом',' снимаю дом',
  										 ' аренда квартир',' снять квартир',' сдам квартир',' сдаю квартир',' снимаю квартир',
										 ' аренда комнат',' снять комнат',' сдам комнат',' сдаю комнат',' снимаю комнат');
				static $ArendaEx=Array(' длит',' суто',' сутк',' посуточн',' месяц',' помесяц',' срок',' дней ',' дня ',' день ');						 
				static $Arenda=Array('длительно','на длительный срок',' долгосрочно');	
				
				if(rand()%3===0)
				{
					if($ClearAnhor=='кредит'||$ClearAnhor=='кредиты'||$ClearAnhor=='взять кредит'||$ClearAnhor=='взять в кредит'
					||$ClearAnhor=='выгодный кредит')
					{
						if(rand()%7===0)
							$End='без поручителей';
						elseif(rand()%7===0)
							$End='в день обращения';
						elseif(rand()%3===0)
							$End='наличными';
						else
							$End='под залог';
					}
				}
				$WordComWithBuy =Array(
					'запчасти'=>1,'автозапчасти'=>1,'авто запчасти'=>1,'ноутбуки'=>1,'машина'=>1,'ноутбук'=>1,
					'машины'=>1,'автомобиль'=>1,'цветы'=>1,'подарки'=>1,'ноутбуки'=>1,
					'авто'=>1,'телефон'=>1,'квартиры'=>1,'квартира'=>1
					,'nokia'=>1,'диски'=>1,'часы'=>1,'лыжи'=>1,'одежда'=>1,
					'samsung'=>1,'участок'=>1,'iphone'=>1,'холодильник'=>1,
					'кондиционер'=>1
				);
				$ClearAnhor2=trim(str_replace(Array(' toshiba ',' acer aspire ',' asus ',' acer ',' hp ',' samsung ',' недорого ' ,  ' недорогие ',' в ' , ' дешево ', ' кредит ',' дешевые '),'',' '.$ClearAnhor.' '));
				$ClearAnhor2=trim($ClearAnhor2,' .,');
				$ClearAnhor2=str_replace('  ',' ',$ClearAnhor2);
				$ClearAnhor2=str_replace('  ',' ',$ClearAnhor2);
				if(rand()%3 && isset($WordComWithBuy[$ClearAnhor2]) && !$Start)
				{
					if(rand()%2) 
						$Start='купить';
					else
						$End='купить';
				}
				elseif(rand()%3===0
				&& (strpos(' '.$Anchor.' ',' объявления ')!==false||strpos(' '.$Anchor.' ',' объявление ')!==false)
				&&  strpos(' '.$Anchor.' ','платн')===false
				&&  strpos(' '.$Anchor.' ',' доск')===false
				&& (!$Start || $Start=='дать' || $Start=='разместить'))
					$End='бесплатно';
				elseif(rand()%2===0
				&&(strpos(' '.$Anchor.' ',' погоды ')!==false || strpos(' '.$Anchor.' ',' погода ')!==false)
				&& trim($Start.' '.$Anchor)==trim(str_replace($WeathePerEx,'',' '.$Start.' '.$Anchor.' ')))
					$End=$WeathePer[rand()%count($WeathePer)];// на 15 дней
				elseif(rand()%2===0
				&& trim($Anchor)!=trim(str_replace($ArendaFind,'',' '.$Anchor.' '))
				&& trim($Start.' '.$Anchor)==trim(str_replace($ArendaEx,'',' '.$Start.' '.$Anchor.' ')))
					$End=$Arenda[rand()%count($Arenda)];//на длительный срок
				elseif(rand()%4===0
				&& trim($Anchor)!=trim(str_replace('купить квартиру','',' '.$Anchor.' '))
				&& trim($Start.' '.$Anchor)==trim(str_replace(array(' втори',' от ',' без '),'',' '.$Start.' '.$Anchor.' ')))
					$End='от застройщика';
				elseif(rand()%4===0
				&& trim($Anchor)!=trim(str_replace(array(' кинотеатр',' театр'),'',' '.$Anchor.' '))
				&& trim($Start.' '.$Anchor)==trim(str_replace(array(' фото',' афиша',' афиш',' aфиш',' сеанс',' идет ',' идёт ',' распис',' анонс',' расспис',' репертуар',' домашн',' работ',' граф'),'',' '.$Start.' '.$Anchor.' ')))
					$End='aфиша';
				elseif(rand()%3===0
				&& trim($Anchor)!=trim(str_replace(array(' кафе ',' ресторан'),'',' '.$Anchor.' '))
				&& trim($Start.' '.$Anchor)==trim(str_replace(array(' меню',' отзыв',' фото',' афиша',' афиш',' aфиш',' сеанс',' идет ',' идёт ',' распис',' анонс',' расспис',' репертуар',' домашн',' работ',' граф'),'',' '.$Start.' '.$Anchor.' ')))
				{
					if(rand()%2===0)
						$End='отзывы';
					else
						$End='меню';
				}
				elseif(rand()%3==0 
				&&(strpos(' '.$Anchor,' гостиниц')!==false 
				 ||strpos(' '.$Anchor,' отели')!==false
				 ||strpos(' '.$Anchor,' санатори')!==false)
				&& strpos($Text,'цен')===false && strpos($Text,'стои')===false && strpos($Text,'отзыв')===false && strpos($Text,'фото')===false)
				{
					if(rand()%3==0)
						$End='цены';
					elseif(rand()%2==0)
						$End='отзывы';
					elseif(rand()%4==0)	
						$End='фотографии';
					else
						$End='фото';	
				}
				elseif(rand()%2===0
				&& trim($Anchor)!=trim(str_replace(array(' ночной клуб',' ночные клубы'),'',' '.$Anchor.' ')))
				{
					if(rand()%2===0
					&& trim($Start.' '.$Anchor)==trim(str_replace(array(' фото'),'',' '.$Start.' '.$Anchor.' ')))
						$End='фото';
					elseif(rand()%3===0
					&& trim($Start.' '.$Anchor)==trim(str_replace(array(' вечеринк',' анонс',' пати'),'',' '.$Start.' '.$Anchor.' ')))
						$End='вечеринки';
				}
				elseif(rand()%4===0
				&& trim($Anchor)!=trim(str_replace(array('такси'),'',' '.$Anchor.' '))
				&& trim($Start.' '.$Anchor)==trim(str_replace(array(' цен',' маршрутн',' телефон',' номер',' тел',' тел.'),'',' '.$Start.' '.$Anchor.' ')))
				{				
					if(rand()%6===0)
						$End='цены и номера телефонов';
					elseif(rand()%3===0)
						$End='цены';
					elseif(rand()%2===0)
						$End='телефоны';
					else
						$End='номера телефонов';
				}	
				elseif(rand()%4===0)
				{	
					static $CursiAfter=Array('фотографии'=>1,'маркейтинга'=>1,'барменов'=>1,'массажа'=>1,'косметолога'=>1,'шитья'=>1,'вождения'=>1,'английского'=>1,'языка'=>1,'парикмахеров'=>1,'визажа'=>1);
					static $CursiBefore=Array('компьютерные'=>1,'английские'=>1,'экономические'=>1);
	
					$tmpWords=split(' ',trim($Anchor));
					$lw=$tmpWords[count($tmpWords)-1];
					$lw2=$tmpWords[count($tmpWords)-2];
					if((trim($Start.' '.$Anchor)==trim(str_replace(array('нович','начин','начал','чайн'),'',' '.$Start.' '.$Anchor.' ')))
					   &&(($lw=='курсы'  && isset($CursiBefore[$lw2]))
					    ||($lw2=='курсы' && isset($CursiAfter[$lw]))))
					{
						if(rand()%3)
							$End='для новичков';
						else	
							$End='для начинающих';
					}
					elseif(strlen($lw) && is_numeric($lw) && $lw[0]=='2' && $lw[1]=='0'
					&&trim($Start.' '.$Anchor)==trim(str_replace(array(' год',' г ',' г.',' мисс '),'',' '.$Start.' '.$Anchor.' ')))
					{
						if(count($tmpWords)<2 || $tmpWords[count($tmpWords)-2]!='в')
							$End='год';
						else		
							$End='году';
					}
					elseif($lw=='рестораны'
					&&trim($Start.' '.$Anchor)==trim(str_replace(array(' кафе ',' бар'),'',' '.$Start.' '.$Anchor.' ')))
						$End='и кафе';
					
					


					//elseif($lw=='кафе'
					//&&trim($Start.' '.$Anchor)==trim(str_replace(array(' рестор',' бар'),'',' '.$Start.' '.$Anchor.' ')))
					//	$End='и рестораны';
				}
				elseif(rand()%5===0
				&& trim($Anchor)!=trim(str_replace(array(' рестораны '),'',' '.$Anchor.' '))
				&& trim($Start.' '.$Anchor)==trim(str_replace(array(' караоке',' музык',' живая',' живой'),'',' '.$Start.' '.$Anchor.' ')))
					$End='с живой музыкой';	
			}
			$Text=trim($Start).' '.trim($Anchor).' '.trim($End);
			if(!$Start||!$End)
			{
				foreach($LogicRes['AddWords'] as $AddWord)
				{
					if(rand()%3==1 && isset($LogicRes['AddWords'][$AddWord])
					&& $Text==trim(str_replace($AddWordsEx[$AddWord],'',' '.$Text.' ')))
					{
						if(!$End)
							$End=$AddWord;
						else if(rand()%2==1)
							$Start=$AddWord;
						break;
					}
				}
			}
			$Text=trim($Start).' '.trim($Anchor).' '.trim($End);
			if($Start && !$End)
			{	
				$Vars=Array('');
				foreach ($SapeStartToEnd as $SStart =>$Data)
				{
					if($SStart==$Start)
					{
						foreach ($Data as $SEnd =>$Ex)
						{
							if((isset($Ex['obj']) && is_key_obj($Anchor)!=(int) $Ex['obj'])
							 ||(isset($Ex['mn'])	 && is_key_mn($Anchor)!=(int) $Ex['mn']))
								continue;
							if(trim(str_replace($Ex,'',' '.$Anchor.' '))!=$Anchor)
								continue;
							$Vars[]=$SEnd;
						}
					}
				}
				$End=$Vars[rand()%count($Vars)];
				foreach($AskWords as $AskWord)
				{	
					if(rand()%2 && ($Start==$AskWord||strpos($Start,$AskWord.' ')===0))
					{
						$End.='?';
						break;
					}
				}
			}
			$Text=trim($Start).' '.trim($Anchor).' '.trim($End);
			if($City)
			{
				$CCity=$WordStatCities[$City['n']];
				$AWords=split(' ',$Anchor);
				$AFirst=$AWords[0];
				$ALast=$AWords[count($AWords)-1];
				if(mb_strtolower($AFirst,'utf-8')==mb_strtolower($CCity[0],'utf-8')
		 		 ||mb_strtolower($AFirst,'utf-8')==mb_strtolower($CCity[0].',','utf-8'))
				{
					if(!$Start && rand()%3==0 && isset($CCity[3]) && $CCity[3]
					&& mb_stripos($Anchor,$CCity[3],0,'utf-8')===false)
					{
						$Start='';
						if(strpos($Anchor,',')!==false|| rand()%3==0)
							$Start=','.$Start;
						$Start=' '.$Start;
						if($CCity[0]==$ALast || rand()%3==0)		
							$Start=$CCity[3].$Start;
						else
							$Start=$CCity[3].mb_strtolower($Start,'utf-8');	
					}
				}
				elseif(mb_strtolower($ALast,'utf-8')==mb_strtolower($CCity[0],'utf-8')
				|| mb_strtolower($ALast,'utf-8')==mb_strtolower($CCity[1],'utf-8'))
				{
					if($Start.' '.$Anchor!=trim(
						str_replace(Array(' купить ',' где ',' куплю ', ' аренда ',' найти '),'',' '.$Start.' '.$Anchor.' '))
					&& !strpos(',',$Anchor))
					{	
						if(rand()%2)	
							$AWords[count($AWords)-1]=$CCity[2];
						else
							$AWords[count($AWords)-1]=mb_strtolower($CCity[2],'utf-8');
						$Anchor=JOIN(' ',$AWords);		
					}
					elseif(!$End && rand()%3==0 && isset($CCity[3]) && $CCity[3] 
						&& mb_strtolower($ALast,'utf-8')==mb_strtolower($CCity[0],'utf-8')
						&& mb_stripos($Anchor,$CCity[3],0,'utf-8')===false)
					{	
						$End='';
						if(strpos($Anchor,',')!==false|| rand()%3==0)
							$End.=',';
						$End.=' ';
						if($CCity[0]==$ALast || rand()%3==0)		
							$End.=$CCity[3];
						else
							$End.=mb_strtolower($CCity[3],'utf-8');
					}
				}
			}
			DelBredovoeOkolossilocnoe($Start,$Anchor,$End);
			if($Start || $End)
				$cur=trim(trim($Start).' #a#'.trim($Anchor).'#/a# '.trim($End));
			else
				$cur=trim($Anchor);
			$cur=str_replace(' ?','?',$cur);
			$cur=str_replace(' ,',',',$cur);
			$cur=str_replace('  ',' ',$cur);
			$cur=str_replace('  ',' ',$cur);
			$cur=$cur.' '; 
			$cur=str_replace(' ru ','.ru ',$cur);
			$cur=str_replace(' com ','.com ',$cur);
			$cur=str_replace(' ua ','.ua ',$cur);
			$cur=str_replace(' com ua ','.com.ua ',$cur);
			if(rand()%2)
				$cur=str_replace('интернет магазин','интернет-магазин',$cur);
			if(rand()%2)
				$cur=str_replace(' б у ',' б/у ',' '.$cur.' ');

			$cur=trim($cur);
			$cur=str_replace('  ',' ',$cur);
			$cur=str_replace('  ',' ',$cur);
			$Sape0[]=$cur;
		}
		return $Sape0;
	}
	
	function ClearRegion_unsafe($Key)
	{//удаляет все похожее на регион
		$City=GetCityFromKeyWord($Key);
		if($City)
			return $City['clear'];
		$Words=split(' ',$Key);
		//if($Words)
		return $Key;
	}
	function change_chislo($Key)
	{// безопасная функция
		return key_to_mn2($Key);
	}
	function change_chislo_unsafe($Key)
	{// выведет что сможет
		return change_chislo($Key);
	}
	function StemWord($Word)
	{//обрезает окончания
	 //нe не реализовано
		return $Word;
	}
	function is_sub_key($Main0,$Sub0)
	{//возвращает истину, если Sub подзапрос Main 
		global $StopWords;
		if(strpos(' '.$Main.' ',' '.$Sub.' ')!==false)
			return true;
		$Main=split(' ',$Main);
		$Sub=split(' ',$Sub);
		$Index=Array();
		foreach($Main as $Word)
			$Index[StemWord($Word)]=1;
		$was=false;
		foreach($Sub as $Word)
			if(!isset($Index[StemWord($Word)]) && !isset($StopWords[StemWord($Word)]))
				$was=true;
		if(!$was)	
			return true;
		return is_same_keys($Main0,$Sub0);
	}
	function is_same_keys($Key1,$Key2)
	{
		global $StopWords;
		//возвращает истину, если ключи подозрительны на форму одно и того же ключа
		if(GetZnWordsCount($Key1)!=GetZnWordsCount($Key2))
			return false;
		$Key1=mb_strtolower($Key1, 'utf-8');
		$Key2=mb_strtolower($Key2, 'utf-8');
		
		$Main=split(' ',$Key1);
		$Sub=split(' ',$Key2);
		$Index=Array();
		foreach($Main as $Word)
			$Index[$Word]=1;
		$was=false;
		foreach($Sub as $Word)
			if(!isset($Index[StemWord($Word)]) && !isset($StopWords[StemWord($Word)]))
				$was=true;
		if(!$was)
			return true;
		
		return (
		$Key1==$Key2
		||change_chislo_unsafe($Key1)==change_chislo_unsafe($Key2)
		||change_chislo_unsafe($Key2)==$Key1
		||change_chislo_unsafe($Key1)==$Key2
		||strpos(' '.$Key1,' '.$Key2)!==false
		||strpos(' '.$Key2,' '.$Key1)!==false);
	}
	
	function HkTranlit($Str,$Variant=0)
	{
		static $Variants=Array
		(
			Array
			(
				"і"=>"i","є"=>"eh","а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
				"е"=>"e","ё"=>"jo","ж"=>"zh","з"=>"z","и"=>"i","й"=>"jj","к"=>"k","л"=>"l",
				"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u",
				"ф"=>"f","х"=>"kh","ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
				"ы"=>"y","ь"=>"","э"=>"eh","ю"=>"yu","я"=>"ya"
			),
			Array(
				"є"=>"ye","ѓ"=>"g","а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
				"е"=>"e","ё"=>"yo","ж"=>"zh","з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
				"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"x",
				"ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"","ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"
			)
		);
		return str_replace( array_keys($Variants[$Variant]),
							array_values($Variants[$Variant]),
							$Str);
	}
	//echo 'Translate='.DoTranslate("гостиница одесса", 'ru', 'uk');
	function DoTranslate($text, $from, $to)
	{ 
		static $TranslateCash=Array();
		include_once("Snoopy.class.php");	
		$snoopy = new Snoopy;
		$snoopy->agent 	 = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
		$snoopy->referer = "http://google.com.ua/";
		$snoopy->rawheaders["Accept"] = "text/html";
		$snoopy->rawheaders["Accept_charset"] = "utf-8";
		$snoopy->rawheaders["Accept_encoding"] = "identity";
		$snoopy->rawheaders["Connection"] = "Keep-Alive";
		$URL = 'http://translate.google.com/translate_a/t?client=t';//&text='.urlencode($text).'&sl='.$from.'&tl='.$to; 
		$POSTDATA = Array
		(
			"text"=>$text,
			"sl"=>$from,
			"tl"=>$to
		);
		$snoopy->httpmethod = "POST";
		$snoopy->submit($URL, $POSTDATA);

		$tmp = $snoopy->results;
		$res='';
		$tmp=split('\"',$tmp);
		$res=$tmp[1];
		$res=trim($res);
		return $res;
	} 
	function FindWordMistakes($Key)
	{
		global $WordStatCities;	
		//			0       1			2			3		4			5
		//array('Москва', 'Москвы', 'в Москве', 'Россия','московские','мск'),

		$Key=mb_strtolower($Key);
		static $Base0 = Array
		(
			'г'=>Array('город'), 'город'=>Array('г'),
			'агентство'=>Array('агентство'), 'агентство'=>Array('агентство'),
			'ноутбук'=>Array('нетбук'),'ноутбуки'=>Array('нетбуки'),
			'нетбук'=>Array('ноутбук'),'нетбуки'=>Array('ноутбуки'),
			'посуточно'=>Array('на сутки')
		);
		static $Okons=Array('ие','ая','ое','ий','ой');
		static $CityBase=Array();
		static $Base=false;
		if(!$Base)
		{
			$Base=$Base0;
			foreach($WordStatCities as $i => $City)
			{
				foreach($City as $j => $Word)
					$City[$j]=mb_strtolower($Word,'utf-8');
				$City[2]= split(' ',$City[2]);
				$City[2]=$City[2][1];
				for($j=0;$j<3;$j++)
					$CityBase[$City[$j]]=$i;
				if(isset($City[4]))
				{//прилагательное московский
					for($j=0;$j<3;$j++)
						$Base[$City[$j]][]=$City[4];
					$tp=$City[4].'*';
					foreach($Okons as $Okon)
					{
						$Base[str_replace('ие*',$Okon,$tp)][]=$City[0];	
						$CityBase[str_replace('ие*',$Okon,$tp)]=$i;
					}
				}
				if(isset($City[5]))
				{//написания мск
					if(!is_array($City[5]))
						$City[5]=Array($City[5]);
					foreach($City[5] as $Word)
					{
						$Base[$Word][]=$City[0];
						for($j=0;$j<3;$j++)
						{
							$Base[$City[$j]][]=$Word;
							$CityBase[$Word]=$i;
						}
					}
				}
			}
		}
		//print_r($Base);
		$LogicRes=GetLogicRes(GetWordStat($Key,1));
		$Mistakes=Array();
		$Mistakes[$Key]=$LogicRes['Count'];
		foreach($LogicRes['Mistakes'] as $Word =>$Count)	
			$Mistakes[$Word]=$Count;
		if(isset($Base[$Key]))
		{
			foreach ($Base[$Key] as $Word)
				if(!isset($Mistakes[$Word]))
					$Mistakes[$Word]=-1;
		}
		if(isset($CityBase[$Key]))
			$Mistakes['_CityID_']=$CityBase[$Key];			
		return $Mistakes;
	}
	
	function GetSeeAlsoKeys($Str)
	{
		global $StopWords;
		global $WordStatCities;	
		if(!is_array($Str))
			$Str=split(' ',$Str);
			
		$Str0=JOIN(' ',$Str);	
		$Words=Array();	
		$First=false;
		$CityID=false;
		$StrA=Array();
		foreach($Str as $Word)
		{
			$StrA[$Word]=$Word;
			if(!$First)	
				$First=$Word;
			if(!isset($StopWords[$Word]))
			{
				$Words[$Word]=FindWordMistakes($Word);
				if(isset($Words[$Word]['_CityID_']))
				{
					$CityID=$Words[$Word]['_CityID_'];
					unset($Words[$Word]['_CityID_']);
				}
			}
		}
	//	echo '<pre>';
	//		echo '$Words=';	
	//		print_r($Words);
	//	echo '</pre>';
		if(count($Words)==0)
			return Array(); 
		if(count($Words)==1)
		{	
			unset($Words[$First][$First]);
			$Words[$First]['_PT_']=1;
			return $Words[$First];
		}
		//Пересчитываем коэфициенты 
		foreach($Words as $Word => $Variants)
			foreach($Variants as $Variant=>$Count)
			{	
				if($Variants[$Word])
					$Words[$Word][$Variant]/=$Variants[$Word];
				else
					$Words[$Word][$Variant]=0;
			}
		$Res=Array();
		GetSeeAlsoKeys_R($Res,$Words);
		
		if(isset($StrA['украина'])||($CityID && $WordStatCities[$CityID][3]=='Украина'))
		{
			$Res0=$Res;
			$t=DoTranslate($Str0, 'ru', 'uk');
			if($t && !isset($Res[$t]))
				$Res[$t]=-1;
			foreach($Res0 as $Cur => $Val)
			{
				if($Val>0.10)
				{
					$t=DoTranslate($Cur, 'ru', 'uk');
					if($t && !isset($Res[$t]))
						$Res[$t]=-1;
				}
			}
		}//
	//	$Res2=Array();
	//	foreach($Res as $Cur => $Val)
			//if($Val<0 || $Val>0.005)//пол %
				//$Res2[$Cur]=$Val;
		return $Res;
	}
	function GetSeeAlsoKeys_R(&$Res,$Words
							 ,$cur='',$WasNewWord=false, $K=1.0, $deep=0)
	{//Рекурсиивная функция
		if($deep == count($Words))
		{	
			if($WasNewWord)
				$Res[$cur]=$K;
			return;
		}
		$i=0;
		foreach($Words as $Word => $Variants)
		{	
			$i++;
			if($i-1!=$deep)
				continue;
			foreach($Variants as $Variant=>$K2)
			{
				$Str=$Variant;
				if($cur)
					$Str=$cur.' '.$Variant;
				GetSeeAlsoKeys_R($Res,$Words
								,$Str,$WasNewWord,$K*$K2, $deep+1);
				$WasNewWord=true;		 
			}
		}
	}
		

	//
	//
	
	//'микрософт'	
	//$LogicRes['Mistakes']
//	echo '<pre>';
//		print_r(GetSeeAlsoKeys('гостиница одесса'));
		//print_r(FindWordMistakes('гостиница'));
//	exit();
	//foreach()
	
	function GetLogicRes($WS)
	{
	// только по не региональным запросам
	// запрос должен быть глобализирован
	// по региональным запросам нельзя и_синоним отпрасить
	///и многие константы должны быть другими
	
	
	
		$RuRaskl=Array('й','ц','у','к','е','н','г','ш','щ','з','ф','ы','в','а','п','р','о','л','д','я','ч','с','м','и','т','ь');
		$EnRaskl=Array('q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m');
		$Mistakes=Array();
	//	echo ''.levenshtein($Key,'Ожд',1,10,100).' <br />';
		
		foreach($WS['Near'] as $Key => $Count)
		{
			if(strpos($Key,' '))
				continue;
			$len=mb_strlen($WS['Key'],'utf-8');
			$lev=levenshtein($Key,$WS['Key'])/2;
			//echo "$Key==$lev ($len)<br />" ;	

			if($lev<=$len/4 && $lev<3)
				$Mistakes[$Key]=$Count;
			else
			{
				$KeyE=str_replace($RuRaskl,$EnRaskl,$Key);
				$KeyR=str_replace($EnRaskl,$RuRaskl,$Key);
				if($KeyE==$WS['Key']||$KeyR==$WS['Key'])
					$Mistakes[$Key]=$Count;
				elseif($len>8 &&(levenshtein($KeyE,$WS['Key'])<3||levenshtein($KeyR,$WS['Key'])<3))
					$Mistakes[$Key]=$Count;
				else
				{	
					$M=$WS['Key'];
					$C=$Key;
					$TM0=HkTranlit($WS['Key'],0);
					$TM1=HkTranlit($WS['Key'],1);
					$TС0=HkTranlit($Key,0);
					$TС1=HkTranlit($Key,1);
					if($TM0==$C||$TM1==$C||$TC0==$M||$TC1==$M)
						$Mistakes[$Key]=$Count;
					elseif((levenshtein($TM0,$C)<=$len/2 && levenshtein($TM0,$C)<5)
						|| (levenshtein($TM1,$C)<=$len/2 && levenshtein($TM1,$C)<5)
						|| (levenshtein($TС0,$M)<=$len/2 && levenshtein($TС0,$M)<5)
						|| (levenshtein($TС0,$M)<=$len/2 && levenshtein($TС0,$M)<5)
					)
						$Mistakes[$Key]=$Count;
				}
			}
		}
		$LogicRes=Array(
			//'AddWords'=>Array(),
			'WordsCount'=>count(split(' ',trim($ClearKey))),
			'FindWords0'=>Array(),
			'Count'=>$WS['Count']
			//,'FindWords'=>Array()
		);
	//Составляем базу соответсивий	
		//Какие уточнения мы ищем
		static $FindWords=Array(
			' города',' и ',' купить ',' ремонт ', ' скачать ',' бесплатно ',' билет',' сайт ',
			'скачать бесплатно'=>Array(' скачать бесплатн','бесплатно скачать'),
			'отзывы'=>' отзыв',
			'лучшие'=>' лучш',
			'цена'=>' цен',
			'аренда+'=>Array(' аренд',' снять ','брониров'),
			'недорого+'=>Array(' недорог',' дёшев',' дешев'),
			'недорого'=>Array(' недорог'),
			'дешево'=>Array(' дешев',' дёшев'),
			'бу'=>Array(' бу ',' б у '),
			'аренда'=>' аренд',
			' снять ',
			'бронировать'=>'брониров');
		$i=0;
		//Ищем уточнения в вордстатовских кеях
		$UnsetAfter=Array();//уточнения которые входят в главный кей мы должны удалить после евристик
		foreach($WS['Keys'] as $Key => $Count)
		{
			if($i==0)
				$LogicRes['Max']=$Count;
			if($Count*1000<$LogicRes['Max']||$Count<10)
				break;//до 1/10 процента
			foreach($FindWords as $Name => $FWords)
			{
				if(!is_array($FWords))
					$FWords=Array($FWords);
				if(is_int($Name))
					$Name=$FWords[0];
				$Name=trim($Name);
				if(isset($LogicRes['FindWords0'][$Name]))
					continue;
				foreach($FWords as $FWord)
				{
					if(stripos(' '.$Key.' ',$FWord)!==false)
					{
						$LogicRes['FindWords0'][$Name]=round(($Count/$LogicRes['Max']) * 100.0,6);
						if($i==0)
							$UnsetAfter[$Name]=1;
						break;
					}
				}
			}
			$i++;	
		}	
		$LogicRes['FindWords']=$LogicRes['FindWords0'];
		// теперь отчищаем от мусора по евристикам
		if($LogicRes['FindWords']['купить']<1.0 
		|| $LogicRes['FindWords']['аренда+']/3.0>$LogicRes['FindWords']['купить'])
			unset($LogicRes['FindWords']['купить']);
		if($LogicRes['FindWords']['аренда+']<0.3 
		|| $LogicRes['FindWords']['аренда+']<$LogicRes['FindWords']['купить']/2.0)
		{
			unset($LogicRes['FindWords']['аренда+']);
			unset($LogicRes['FindWords']['аренда']);
			unset($LogicRes['FindWords']['снять']);
			unset($LogicRes['FindWords']['бронировать']);
		}
		if($LogicRes['FindWords']['бесплатно']-$LogicRes['FindWords']['скачать бесплатно']<1.5)
			unset($LogicRes['FindWords']['бесплатно']);
		if($LogicRes['FindWords']['скачать']<2)
			unset($LogicRes['FindWords']['скачать']);
		if($LogicRes['FindWords']['цена']<0.4)
			unset($LogicRes['FindWords']['цена']);
		if($LogicRes['FindWords']['цена']<0.5)
			unset($LogicRes['FindWords']['сайт']);	
		// теперь отчищаем от вхождений в главный кей
		foreach($UnsetAfter as $cUnset =>$one)
			unset ($LogicRes['FindWords'][$cUnset]);
	//База соответсвий готова
	
	

		
		//Находим и_синоним.
		//Это такое слово которое во мн числе сочетаеться с запросом отели и гостиницы, алкометры и алкотестеры
		if(count(split(' ',$WS['Key']))==1 //число слов в запросе 1
		&&(key_to_mn($WS['Key'])||is_key_mn($WS['Key'])))//это слово во мн или приводиться во мн. 
		{//если глобализированный синоним содержит одно слово
			$iSyn='';// Сам синонима
			$iSynFirst=false; // Первое ли это похожее слово
			$iSynCount=0;     // Число показов синонима
			$i=0;
			foreach($WS['Near'] as $Key => $Count)
			{
				if(count(split(' ',$Key))==1 && !is_same_keys($WS['Key'],$Key))
				//проверяем ключи на сходство
				{
					$iSyn=$Key;
					$iSynCount=$Count;
					$iSynFirst=($i==0);
					break;
				}
				$i++;
			}
			if($iSyn//найден кандидат
			&& (key_to_mn($iSyn)||is_key_mn($iSyn)))//он во мн или приводиться во мн
			{
				$AllEva=0;// Оценка всего веса Eva от Evaluate 
				$SynEva=0;// Оценка веса синонима
				$i=0;
				$iSynCR=change_chislo_unsafe($iSyn);
				foreach($WS['Near'] as $Key => $Count)
				{
					$Eva=count($WS['Near']) * 2 -$i;//чем первее ключ тем он весомее 	
					$AllEva+=$Eva;
					if(is_sub_key($Key,$iSyn))
						$SynEva+=$Eva;
				}
				$i++;
				//echo "<br /> $iSyn :: AllEva=$AllEva SynEva=$SynEva <br />";
				if(($AllEva/4<$SynEva)//больше четверти похожих ключей имеют и_синоним
				 ||($iSynFirst && $iSynCount<$LogicRes['Max']/5))
				{
					$iSyn=key_to_mn2($iSyn);     //приводим во мн синоним
					$MKey=key_to_mn2($WS['Key']);//приводим во мн запрос
					$iSynCombiCount=0;          //число показов "и_син и запрос"
					foreach($WS['Keys'] as $Key => $Count)
					{
						if($Key==$iSyn.' '.$MKey  ||$Key==$MKey.' '.$iSyn//гостиницы отели
						|| $Key==$iSyn.' и '.$MKey||$Key==$MKey.' и '.$iSyn)//гостиницы и отели
						{
							$iSynCombiCount=$Count;
							break;
						}
					}
					if($iSynCombiCount/min($LogicRes['Max'],$iSynCount)>0.01)
						$LogicRes['iSyn']=$iSyn;// Добавили и_синоним
				}
				//echo "<br /> $iSyn :: iSynCombiCount=$iSynCombiCount iSynCount=$iSynCount <br />";
				//Показов комбинированного больше процента от всего показов или от показов и_синонима

			}
		}
		if($LogicRes['iSyn'])
			$Mistakes[$LogicRes['iSyn']]=$iSynCount;
		//echo '<pre>';
		//echo "iSyn={$LogicRes['iSyn']} <br />";
		//print_r($Mistakes);
		
		$LogicRes['Mistakes']=$Mistakes;
		
		//Поиск и_синонима закончен
		
		
		// Если это необходимо, то нужно искать искать словоформы.
		//  Там алгоритмы простые
		
		//echo '<pre>';
		//	print_r($LogicRes);
		//echo '</pre>';
		return $LogicRes;
	}

	//Вычисляет растояние между строками по Левейнштейну.
	//Оно же растояние редактирования. Число операций вставки, удаления и редактирования
	//, которое необходимо чтобы одну строку преобразовать в другую
	
	//Алгоритм изменен, чтобы к концу слова вес редактирования затухал
	//Чтобы окончания лучше выделялись
	function hkey_levenshtein($source, $dest) 
	{
		if ($source == $dest) return 0;
		$slen = strlen($source);
		$dlen = strlen($dest);
		if ($slen == 0) 
			return $dlen;
		elseif ($dlen == 0) 
			return $slen;
		
		$SumLen=$dlen+$slen;//Сумма длин

		$dist = range(0, $dlen);
		for ($i = 0; $i < $slen; $i++) 
		{
			$_dist = array($i + 1);
			$char = $source{$i};
			for ($j = 0; $j < $dlen; $j++) 
			{
				//3/2 до 1/2 в среднем 1
				$k = (3 - (2 * ($i+$j))/$SumLen)/2;
				$cost = $char == $dest{$j} ? 0 : 1;
				$_dist[$j + 1] = min(
					$dist[$j + 1] + 1,   // deletion
					$_dist[$j] + 1,      // insertion
					$dist[$j] + $cost    // substitution
				) * $k;
			}
			$dist = $_dist;
		}
		//echo "lng($source, $dest)=={$dist[$j]}<br>";
		return $dist[$j];
	}
	function HSplitWords($Key)
	{//Возвращает массив слов
		$Key=split(' ',$Key);
		$Res=Array();
		foreach($Key as $Word)
		{	
			$Word=trim($Word,'-,!\'"');
			$Res[]=$Word;
		}
		return $Res;
	}
	function FindKeyParents($Data1,$Keys)
	{
		global $StopWords;
		$Len=$Data1['WordsCount'];
		$Res=Array();
		for($i=1;$i<2;$i++)// Мы пропускаем 0,1 или 2 слова 
		{
			if($Len-$i<0)
				break;
			foreach($Keys as $Key2 => $Data2)
			{
				if($Data2['WordsCount']!=$Len-$i)	
					continue;
				$SameCount=0;	
				$SkipCount=0;
				foreach($Data1['Words'] as $Word)
				{
					$SameCount0=$SameCount;
					if(isset($Data2['Words'][$Word]))
						$SameCount++;
					else
					{
						foreach($Data2['Words'] as $Word2)
						{	
							if(hkey_levenshtein($Word,$Word2)<1.0)
							{
								//echo "$Word==$Word2  (".hkey_levenshtein($Word,$Word2).")<br />";
								$SameCount++;
								break;
							}
						}
					}
					if($SameCount0==$SameCount && $SkipCount<$i-1)
					{
						$SameCount++;
						$SkipCount++;
					}
				}
				if($SameCount==$Len-$i)
					$Res[]=$Key2;
			}	
			if(count($Res)!=0)
				break;
		}
		return $Res;
	}
	function AdvancesWordStat($Key)
	{
		$Key0=$Key;
		$WS=GetWordStat($Key);
		$City0=GetCityFromKeyWord($Key);
		$Words0=HSplitWords($Key);
		$Res=Array();
		$Res['Key']=$Key;
		$Res['WS']=$WS;
		$Res['City']=$City0;
		$Res['Words']=$Words0;
		
		
		$Keys=Array();
		$TempKeys=Array();
		$MaxWordsCount=0;
		$LastCount=-1;
		$MinCount=1000000000;
		
		foreach ($WS['Keys'] as $Key => $Count)
		{
			if($MinCount>$Count)
				$MinCount=$Count;
			if($Count==$LastCount && $Count>25)
				continue;//Пропускаем мусор 
			$Keys[$Key]=Array();
			$Words=HSplitWords($Key);
			
			if($LastCount==-1)	
				$FirstKey=$Key;
			$LastCount=$Count;
			
			//Теперь удаляем из текущего запроса все слова основного
			$Keys[$Key]['Forms']=Array();
			foreach($Words0 as $i=>$Word0)
			{
				$MinD=10000;
				$MinI=0;
				foreach($Words as $i=>$Word)
				{//Находим самое похожее слово
					if(HkStremIsSameWords($Word,$Word0))
						$D=0.1 * hkey_levenshtein($Word,$Word0);	
					else
						$D=hkey_levenshtein($Word,$Word0);
					if($MinD>$D)
					{
						$MinD=$D;
						$MinI=$i;
					}
				}
				$Keys[$Key]['Forms'][$Word0]=$Words[$MinI];
				unset($Words[$MinI]);
			}
			
			$Words2=Array();
			foreach($Words as $i=>$Word)
				$Words2[$Word]=$Word;//Чтобы быстрее искать
			$Keys[$Key]['DirtyCount']=$Count;
			$Keys[$Key]['ClearCount']=$Count;
			$Keys[$Key]['Words']=$Words2;
			$Keys[$Key]['WordsCount']=count($Words);
			$Keys[$Key]['Addon']=JOIN(' ',$Words);
			
			if($MaxWordsCount<count($Words))
				$MaxWordsCount=count($Words);
		}
		//foreach($Keys as $Data)
		//	echo JOIN(' ',$Data['Words']).'<br />';
//		echo '<pre>';
	//	print_r($Keys);
		//exit;
		//Составляем дерево кеев
		$TreeKeys=Array();
		
		foreach($Keys as $Key1 => $Data1)
		{
			$TreeKeys[$Key1]=Array();
			if($Key1==$FirstKey)
				continue;
			foreach($Keys as $Key2 => $Data2)
			{
				if($Key1==$Key2||$Data1['WordsCount']>=$Data2['WordsCount'])
					continue;// Одинаковая длина
				if($Data1['ClearCount']<$Data2['ClearCount'])	
					continue;
					
				foreach($Data1['Words'] as $Word1)
				{
					if(isset($Data2['Words'][$Word1]))
						continue;
					foreach($Data2['Words'] as $Word2)
						if($Word1==$Word2||HkStremIsSameWords($Word1,$Word2))
							continue 2;
					continue 2;// Какого-то из слов нет
				}
				$TreeKeys[$Key1][$Key2]=$Key2;//все слова есть
			}
		}
		//Добавляем все запросы к основному
		foreach ($Keys as $Key => $Data)
		{
			if($Key!=$FirstKey)
				$TreeKeys[$FirstKey][$Key]=$Key;
		}
		//$FirstKey as 
		echo '<pre>';
		//foreach($TreeKeys['оборудование для окон'] as $Key1 => $Data1)
		//	echo $Key1.' '.$Keys[$Key1]['ClearCount'].' '.$Keys[$Key1]['DirtyCount'].'<br />';
	//	print_r();
		echo '</pre>';
		echo '<br />';
		//Ищем невидимых общих детей
		$WasAllChild=Array();
		foreach($Keys as $Parent => $Data1)
		{
			if(count($TreeKeys[$Parent])!=2||$WasAllChild[$Parent])
				continue;
			$Summ=0;	
			foreach($TreeKeys[$Parent] as $Child)
			{
				if(count($TreeKeys[$Child]))
					continue 2;
				$Summ+=$Keys[$Child]['DirtyCount'];	
			}
			if($Keys[$Parent]['DirtyCount']>$Summ)
				continue;
			//echo ' X1X ';	
			//Паренту оставляем 10% чистых	
			$delta=round($Summ-0.9 * $Keys[$Parent]['DirtyCount']);
			if($delta>$MinCount)
			{//Если добавлеяемый кей должен быть в выдаче
				$delta=round($Summ - 0.99 * $Keys[$Parent]['DirtyCount']);
				if($delta>$MinCount)
				{// Глюк Яши, когда омонемия неправильно снимаеться 
					if($Summ>1.5*$Keys[$Parent]['DirtyCount'])
					{
						$cMin=-10000000;
						$cMinKey='';
						$cMax=10000000;
						foreach($TreeKeys[$Parent]  as $Child)
						{
							if($cMin<$Keys[$Child]['DirtyCount'])
							{	
								$cMinKey=$Child;
								$cMin=$Keys[$Child]['DirtyCount'];
							}
							if($cMax>$Keys[$Child]['DirtyCount'])
								$cMax=$Keys[$Child]['DirtyCount'];
						}
						if($cMin>$delta && $cMin*1.05>$cMax)
						{
							$Keys[$cMinKey]['ClearCount']-=$delta;
							/*
								echo "$Parent <pre>";
								print_r($Keys[$Parent]);
								foreach($TreeKeys[$Parent]  as $Child)
								{	
									echo "<br /><br /><b>$Child</b><br />";
									print_r($Keys[$Child]);
								}
								echo "</pre>";
							*/
						}
					}
					continue;
				}
			}
			
			
			//echo ' X2X ';		
			$TmpWords=Array();
			$TmpForms=Array();			
			foreach($TreeKeys[$Parent] as $Child)
			{//Чтобы отрицательных показов не было
				if($delta>$Keys[$Child]['DirtyCount'] ||$Keys[$Child]['WasAllChild'])
					continue 2;
				$TmpForms=$Keys[$Child]['Forms'];
				foreach($Keys[$Child]['Words'] as $Word1)
				{
					foreach($TmpWords as $Word2)
						if(HkStremIsSameWords($Word1,$Word2))
							continue 2;
					$TmpWords[$Word1]=$Word1;
				}				
			}
			//echo ' X3X ';
			// Теперь собираем ключ
			$TmpWordsAll=$Words0;
			foreach($TmpWords as $TmpWord)
				$TmpWordsAll[$TmpWord]=$TmpWord;
			$AllChildKey=JOIN(' ',$TmpWordsAll);
			$AllChild=Array('DirtyCount'=>$delta,'ClearCount'=>$delta,'Virtual'=>true);
			$AllChild['Words']=$TmpWords;
			$AllChild['WordsCount']=count($TmpWordsAll);
			$AllChild['Addon']=JOIN(' ',$TmpWords);
			$AllChild['Forms']=$TmpForms;
			
			//echo '<hr>'.$AllChildKey.'<br /><pre>';
			//print_r($AllChild);
			//echo '</pre><hr>';
			foreach($TreeKeys[$Parent] as $Child)
				$Keys[$Child]['WasAllChild']=true;
			$WasAllChild[$Parent]=true;
			if(!isset($Keys[$AllChildKey]))
			{
				$Keys[$AllChildKey]=$AllChild;
				$TreeKeys[$AllChildKey]=Array();
				
				//Добавляем деткам
				foreach($TreeKeys[$Parent] as $Child)
					$TreeKeys[$Child][$AllChildKey]=$AllChildKey;
				//Добавляем родительскому
				$TreeKeys[$Parent][$AllChildKey]=$AllChildKey;

				//Добавляем родительским родительского
				foreach($TreeKeys as $mParent => $Data)
					foreach($TreeKeys[$mParent] as $Child)
						if($Child==$Parent)
							$TreeKeys[$mParent][$AllChildKey]=$AllChildKey;
			}
		}
		//exit();
		for($Len=$MaxWordsCount;$Len>=0;$Len--)
		{// Перебираем кем по числу слов от самых длинных к самым коротки
			foreach($Keys as $Parent => $Data1)
			{
				if($Data1['WordsCount']!=$Len)	
					continue;
				$SummClear=0;	
				$Was=$Keys[$Parent]['ClearCount'];
				foreach($TreeKeys[$Parent] as $Child)//перебираем наследников
				{
					$Keys[$Parent]['ClearCount']-=$Keys[$Child]['ClearCount'];
					$SummClear+=$Keys[$Child]['ClearCount'];
				}
				if($Keys[$Parent]['ClearCount']<0)
				{
					/*
					if($FirstKey!=$Parent)
					{
						
						$safds=0;
						foreach($TreeKeys[$Parent] as $Child)
							$safds+=$Keys[$Child]['ClearCount'];
						echo '<hr /><Table>';
						echo '<tr><td>'.$Parent.'</td><td>'.$Keys[$Parent]['DirtyCount'].'</td><td></td></tr>';
						foreach($TreeKeys[$Parent] as $Child)
							echo '<tr><td>'.$Child.'</td><td>'.$Keys[$Child]['DirtyCount'].'</td><td>'.$Keys[$Child]['ClearCount'].'</td></tr>';
						echo '<tr><td>~~~~</td><td>'.$Keys[$Parent]['ClearCount'].'</td><td>'.$safds.'<td><tr>';
						echo '</Table>';
						echo '<pre>';
						foreach($TreeKeys[$Parent] as $Child)
						{
							echo '<br /><br /><b>'.$Child.'</b><br />';
							print_r($Keys[$Child]);
							
							foreach($TreeKeys[$Child] as $Child2)
							{
								echo '<br />'.$Child2.' ';
								print_r($Keys[$Child2]);
							}
						}
						echo '</pre>';
						echo '<hr />';
						
					}
				*/	
					$N=round($Was * 0.1);//Новое значение
					$D=-$Keys[$Parent]['ClearCount'] + $N;//разница текущего и нового
					$Keys[$Parent]['ClearCount'] = $N;
					
					foreach($TreeKeys[$Parent] as $Child)
					{//размазываем разницу по наследникам
						$C=$Keys[$Child]['ClearCount'];
						$C-=round($D * ($C/$SummClear));
						$Keys[$Child]['ClearCount']=$C;
					}
				}
				//unset($Keys[$Parent]['Words']);
			}
		}
		
		//Считаем частотность уточнений
		global $StopWords;
		$SingleСlarify=Array();//Одиночные слова
		$MultiСlarify=Array(); 
		$CountСlarify=Array();//Число уточняющих слов
		foreach($Keys as $Key1 => $Data1)
		{
			foreach($Data1['Words'] as $i => $Word1)
			{
				if(isset($StopWords[$Word1]))
					unset ($Data1['Words'][$i]);
			}
			$y=JOIN(' ',$Data1['Words']);
			if($y==='')
				$y='_нет_';
			if(!isset($MultiСlarify[$y]))
				$MultiСlarify[$y]=0;
			$MultiСlarify[$y]+=$Data1['ClearCount'];
			$znwcount=count($Data1['Words']);
			if($znwcount>2)
				$znwcount='3+';
			$znwcount.='';	
			$CountСlarify[$znwcount]+=$Data1['ClearCount'];
			if(count($Data1['Words'])<=1)
			{
				if(!isset($SingleСlarify[$y]))
					$SingleСlarify[$y]=0;
				if(count($Data1['Words'])==0)
					$SingleСlarify[$y]+=$Data1['ClearCount'];
				else if($SingleСlarify[$y]<$Data1['DirtyCount'])
					$SingleСlarify[$y]=$Data1['DirtyCount'];
			}
		}	
	
		arsort($SingleСlarify);
		arsort($MultiСlarify);
		//echo '<pre>';
		//print_r($SingleСlarify);
		
		$Forms=Array();
		foreach($Words0 as $i=>$Word)
		{
			//$Word=mb_strtolower($Word,'utf-8');
			$Forms[$Word]=Array();
			foreach($Keys as $Data)
			{
				$Form=$Data['Forms'][$Word];
				$Form=mb_strtolower($Form,'utf-8');
				if(!isset($Forms[$Word][$Form]))
					$Forms[$Word][$Form]=0;	
				$Forms[$Word][$Form]+=$Data['ClearCount'];
			}
		}
		$Summ=0;
		$FormsAll=Array();
		foreach($Keys as $Data)
		{
			$Summ+=$Data['ClearCount'];
			$Form=JOIN('_',$Data['Forms']);
			if(!isset($FormsAll[$Form]))
				$FormsAll[$Form]=0;	
			$FormsAll[$Form]+=$Data['ClearCount'];
		}
		if(count($Words0)>1)
			$Forms['Сочетания форм']=$FormsAll;
		//echo '<br />FormsAll='.$Summ.'<br />';
		foreach($Forms as $Word=>$WordForms)
		{
			foreach($WordForms as $Form=>$Count)
				if($Summ && round(($Count/$Summ)*100.0, 1)<=1)
					unset($Forms[$Word][$Form]);
			arsort($Forms[$Word]);		
		}
		$KeyToN=Array();
		$i=0;
		foreach($Keys as $Key=>$Data)
		{
			$KeyToN[$Key]=$i;
			$i++;
		}
		$TreeKeysN=Array();
		foreach($TreeKeys as $Parent => $Childrens)
		{
			$TreeKeysN[$KeyToN[$Parent]]=Array();
			foreach($Childrens as $Child)
				$TreeKeysN[$KeyToN[$Parent]][]=$KeyToN[$Child];
		}
		foreach($TreeKeysN as  $Parent => $Childrens)
			if(!count($Childrens))
				unset($TreeKeysN[$Parent]);
		$SeeAlso0=GetSeeAlsoKeys($Key0);
		if($SeeAlso0['_PT_'])
		{
			unset($SeeAlso0['_PT_']);
			$PT=true;
		}
		$SeeAlso=Array();
	
	
		//Нужно если в выдаче хинтов по вордстату есть хинты скрипта, чтобы уточнить их число показов
		$NearCheck1=Array();
		$NearCheck2=Array();
		foreach($WS['Near'] as $Key => $Count)
		{
			$Arr=split(' ',$Key);
			rsort($Arr);
			$NearCheck1[join(' ',$Arr)]=$Count;
			foreach($Arr as $i=>$Word)
				$Arr[$i]=HkStrem($Word);
			$NearCheck2[join(' ',$Arr)]=$Count;
		}
		//echo '<pre>';
		//print_r($NearCheck2);
		
		foreach($SeeAlso0 as $Var =>$Count)
		{
			if($Count<=0)
				$Count=-1;
			else if(!$PT)
			{
				$Count=round($Count*$WS['Count']);	
				$s=$Count.'';
				if(strlen($s)>2)
					$Count=round($Count,2-strlen($s));
				elseif($Count>50)
					$Count=round($Count,-1);
			}
			if(!$PT || $Count==-1)
			{
				if(isset($NearCheck1[$Var]))
					$Count=$NearCheck1[$Var];
				else
				{
					$Arr=split(' ',$Var);
					rsort($Arr);
					if(isset($NearCheck1[join(' ',$Arr)]))
						$Count=$NearCheck1[join(' ',$Arr)];
					else
					{
						foreach($Arr as $i=>$Word)
							$Arr[$i]=HkStrem($Word);
						if(isset($NearCheck2[join(' ',$Arr)]))
							$Count=$NearCheck2[join(' ',$Arr)];
					}
				}
			}
			if($Count>20||$Count<0)
				$SeeAlso[$Var]=$Count;
		}
		$OutData=Array();
		foreach($Keys as $Key=>$Data)
		{	
			$OutData[$Key]=Array(
				'ClearCount'=>$Data['ClearCount'],
				'DirtyCount'=>$Data['DirtyCount'],
				'Childrens'=>Array()
			);
			foreach($TreeKeys[$Key] as $Child)
				$OutData[$Key]['Childrens'][]=$Child;
		}
		return 
			Array(
				'PT'=>$PT,
				'SeeAlso'=>$SeeAlso,
				'Summ'=>$Summ,
				'Keys'=>$Keys,
				'Forms'=>$Forms,
				'Near'=>$WS['Near'],
				'Tree'=>$TreeKeys,
				'KeyToN'=>$KeyToN,
				'TreeN'=>$TreeKeysN,
				'SingleСlarify'=>$SingleСlarify,
				'MultiСlarify'=>$MultiСlarify,
				'CountСlarify'=>$CountСlarify,
				'Data'=>$OutData
			);
	}
	function the_google_pie($Data, $Title='',$width=450, $height=300,$Markers=false)
	{
		$Colors0=Array('76A4FB','80C65A','C2BDDD','EED483','CE7F73','F17979','AA0033','FFFF88','0000FF');
		$LColor='FFEACA';
		if(count($Data)> 8+$Markers*3)//F17979 красный
		{
			arsort($Data);
			$NewData=Array();
			$i=0;
			foreach($Data as $Key => $Val)
			{
				if($i<8+$Markers*3)
					$NewData[$Key]=$Val;
				else
				{
					if(!isset($NewData['Другое']))
						$NewData['Другое']=0;
					$NewData['Другое']+=$Val;
				}
				$i++;
			}
			$Data=$NewData;
		}
	?>
		<img width="<?php echo $width;?>" height="<?php echo $height;?>" src="http://chart.apis.google.com/chart?chxt=x&chs=<?php echo $width;?>x<?php echo $height;?>&cht=p<?php 
				if($Title)
					echo '&chtt='.urlencode($Title);
				echo '&chd=t:'.JOIN(',',$Data);
				if(!$Markers)
				{
					echo '&chdl=' .urlencode(JOIN('|',array_keys($Data)));
					echo '&chl=|';
					$Colors=Array();
					$len=count($Data);//-;
					if(isset($Data['Другое']))
						$len--;
					for($i=0;$i<$len;$i++)
						$Colors[]=$Colors0[$i];
					if(isset($Data['Другое']))
						$Colors[]=$LColor;
					//echo '&chco=FF9900|000000';
					//http://chart.apis.google.com/chart?chxt=x&chs=450x300&cht=p&chtt=%D0%92%D1%81%D0%B5+%D0%BF%D0%BE%D0%BA%D0%B0%D0%B7%D1%8B&chd=t:2765,1726,639,207,198,149,105,817&chdl=%D0%BD%D0%BE%D1%83%D1%82%D0%B1%D1%83%D0%BA+%D0%BE%D0%B4%D0%B5%D1%81%D1%81%D0%B0%7C%D0%BD%D0%BE%D1%83%D1%82%D0%B1%D1%83%D0%BA%D0%B8+%D0%B2+%D0%BE%D0%B4%D0%B5%D1%81%D1%81%D0%B5%7C%D0%BA%D1%83%D0%BF%D0%B8%D1%82%D1%8C+%D0%BD%D0%BE%D1%83%D1%82%D0%B1%D1%83%D0%BA+%D0%B2+%D0%BE%D0%B4%D0%B5%D1%81%D1%81%D0%B5%7C%D0%BD%D0%BE%D1%83%D1%82%D0%B1%D1%83%D0%BA%D0%B8+%D0%B2+%D0%BE%D0%B4%D0%B5%D1%81%D1%81%D0%B5+%D1%86%D0%B5%D0%BD%D1%8B%7C%D0%BD%D0%BE%D1%83%D1%82%D0%B1%D1%83%D0%BA%D0%B8+asus+%D0%BE%D0%B4%D0%B5%D1%81%D1%81%D0%B0%7C%D1%80%D0%B5%D0%BC%D0%BE%D0%BD%D1%82+%D0%BD%D0%BE%D1%83%D1%82%D0%B1%D1%83%D0%BA%D0%BE%D0%B2+%D0%BE%D0%B4%D0%B5%D1%81%D1%81%D0%B0%7C%D0%BD%D0%BE%D1%83%D1%82%D0%B1%D1%83%D0%BA%D0%B8+acer+%D0%BE%D0%B4%D0%B5%D1%81%D1%81%D0%B0%7C%D0%94%D1%80%D1%83%D0%B3%D0%BE%D0%B5&chl=|&chco=FF9900|6A4FB|80C65A|C2BDDD|F62E2E|C65FC8|9D6363|FFEACA&chds=0,100000000
					//echo '&chco=FF0000,0000FF'; 
					echo '&chco='.JOIN('|',$Colors); 
				}
				else	
				{
					echo '&chl=' .urlencode(JOIN('|',array_keys($Data)));
				}
		?>&chds=0,100000000" />
	<?php
	}
	function SynKeyFull($Key,$SapeKeys=50,$DelRegions=true)
	{
		global $GetVariantAndUnset_LastKey,$GetVariant_LastKey;
		$Key0=$Key;
		//Парсим водстат
		$oWS=$WS=GetWordStat($Key);
		$City=GetCityFromKeyWord($Key0);
		if($City)
			$DelRegions=false;
		$ClearKey=$Key;
		if($City)	
			$ClearKey=$City['clear'];
		//Анализ закончился начинаем собирать базу
		$LastCount=-1;
		$LastKey=false;
		$Base=Array();
		$i=0;
		$SecondWS=Array();//База по околоссылочному
		foreach ($WS['Keys'] as $Key => $Count)
		{	
			if($SapeKeys<$i || ($DelRegions && HaveKeyWordCity($Key,$Key0)))
			{
				$SecondWS[$Key]=$Count;
				continue;
			}
			$i++;
			if($LastKey)
			{
				if(($Count==$LastCount && $Count>100)
				||  is_same_key($LastKey,$Key))
				{
					$Base[$LastKey]['forms'][]=$Key;
					continue;
				}
			}
			$LastCount=$Count;
			$LastKey=$Key;
			$Count2=$Count-7; if($Count2<0) $Count2=1;			
			$Base[$Key]=Array(
				'count'=>$Count,
				'eva'=>sqrt($Count2) * (GetZnWordsCount($Key) + 2),
				'forms'=>Array($Key)
			);
		}
		foreach ($WS['Keys'] as $Key => $Count)
		//Кофициенты корректировки оценки
		$BuyWords=Array(
			'1.6'=>Array(' купить '),
			'1.4'=>Array(' цены ',' стоимость ',' цена '),
			'1.2'=>Array(' недорого ',' дешево ',' дёшево '),
			'0.8'=>Array(' бесплатно ',' бесплатные ')
		);
		//Корректируем оценки
		$SummEva=0;//Сумма оценок нужна для рассчета вероятности
		foreach ($Base as $Key =>$Data)
		{
			$K=0;
			foreach ($Data['forms'] as $Str=>$Data)
			{
				$Str=' '.$Str.' ';
				foreach ($BuyWords as $C =>$Words)
				{
					if($Str!=str_replace($Words,'',$Str))
					{
						$K+=(float) $C;
						continue 2;
					}
				}
				$K+=1;
			}
			$Base[$Key]['eva']*=$K/count($Data['forms']);
			$SummEva+=$Base[$Key]['eva'];
		}

		//Теперь пересчитываем число
		$Addon=$SapeKeys;
		foreach ($Base as $BaseKey =>$Data)
		{
			$Count=round(($Data['eva']/$SummEva) * $SapeKeys);
			$Base[$BaseKey]['final_count']=$Count;
			$Addon-=$Count;
			if(!$Count)
			{		
				$SecondWS[$BaseKey]=$Data['count'];
				unset ($Base[$BaseKey]); 
			}
		}
		$Addon=$Addon0;
		if($City)//Если запрос региональный
		{
			$oWS=$WS2=GetWordStat($City['clear']);
			$City2=$City;
			$SecondWSt=$SecondWS;
			$SecondWS=$WS2['Keys'];
			foreach ($SecondWSt as $BaseKey => $Count)
			{
				$cCity=GetCityFromKeyWord($BaseKey);
				if($cCity)
					$BaseKey=$cCity['clear'];
				if(!isset($SecondWS[$BaseKey]))
					$SecondWS[$BaseKey]=$Count;
			}
		}
		$LogicRes=GetLogicRes($oWS);	
		
		//Теперь мутим вариации
		foreach ($Base as $BaseKey =>$Data)
		{
			$Base[$BaseKey]['variantes']=Array();
			foreach($Data['forms'] as $Str)
				$Base[$BaseKey]['variantes'][$Str]=Array();
			foreach($Data['forms'] as $Str)
			{	
				$Variantes=SynKeyWordByCity($Str);
				foreach($Variantes as $Variant)
					if(!isset($Base[$BaseKey]['variantes'][$Variant]))
						$Base[$BaseKey]['variantes'][$Variant]=Array();
			}
		}
		$ClearKeys=GenerateClearKeys($Base,$Addon);
		$VarToKey=Array();
		foreach ($Base as $BaseKey =>$Data)
			foreach($Data['variantes'] as $Variant=>$Arr)
				if(!isset($VarToKey[$Variant]))	
					$VarToKey[$Variant]=$BaseKey;
		
		$Dirty=Array();
		foreach ($SecondWS as $BaseKey => $Count)
		{	
			if(($City && HaveKeyWordCity($BaseKey,$Key0)) || mb_strlen($BaseKey,'utf-8')>75)
				continue;
			$BaseKey=str_replace('интернет магазин','интернет-магазин',$BaseKey);
			$BaseKey=str_replace('интернет магазине','интернет-магазине',$BaseKey);
			$BaseKey=str_replace('веб магазин','веб-магазин',$BaseKey);
			$BaseKey=str_replace('веб магазине','веб-магазине',$BaseKey);
			
			if($City)
			{	
				$City2['clear']=$BaseKey;
				$R1=SynKeyWordByCity($City2);
			}
			else
				$R1=SynKeyWordByCity($BaseKey);
			foreach ($R1 as $Key)
				$Dirty[$Key]=$Count;
		}
		
		//Находим соответсвия
		foreach ($Dirty as $Key => $Count)
		{
			$Words=split(' ',$Key);
			$Str='';
			$i=0;
			foreach($Words as $Word)
			{		
				if($Str)
					$Str.=' ';
				$Str.=$Word;
				if($Str!=$Key && isset($VarToKey[$Str]))
					AddOkoloSilochnoe($Base,$VarToKey[$Str],$Str,$Key,$Count);//$Base[$VarToKey[$Str]]['variantes'][$Str][]=$Key;
			}
			$Str='';
			for($i=count($Words)-1;$i>0;$i--)
			{
				if($Str)
					$Str=' '.$Str;
				$Str=$Words[$i].$Str;
				if(isset($VarToKey[$Str]))
					AddOkoloSilochnoe($Base,$VarToKey[$Str],$Str,$Key,$Count);//$Base[$VarToKey[$Str]]['variantes'][$Str][]=$Key;
			}
		}
		//Приводим в финальный вид базу
		foreach ($Base as $BaseKey =>$Data)
		{
			$Base[$BaseKey]['base_count']=count($Data['forms']);
			$Base[$BaseKey]['all_vars_count']=0;
			$Base[$BaseKey]['variantes2']=Array();
			$was=false;
			foreach($Data['variantes'] as $Key =>$Data2)
			{
				if(count($Data2))
				{
					swapshuffle($Data2);
					$Base[$BaseKey]['variantes2'][$Key]=$Data2;
					$Base[$BaseKey]['all_vars_count']+=count($Data2);
					$was=true;
				}
				elseif(!$was)
					$Base[$BaseKey]['base_count']--;
			}
		}
		$Sape=Array();
		foreach ($Base as $BaseKey =>$Data)
		{
			$Vars=$Data['variantes2'];
			$Addon+=$Count;
			$Count=$Data['final_count'];
			if(!$Count)
				continue;
			if(count($Vars)==0)
			{//ни одной ссылки с околоссылочным	
				$Vars=$Data['variantes'];
				if(count($Vars)<$Count)
				{
					foreach($Vars as $VarKey => $Data)
					{
						$Sape[$VarKey]=$VarKey;
						$Count--;
					}
				}
				else
				{
					$Vars=array_keys($Vars);
					while($Count--)
					{
						$VarKey=GetVariantAndUnset($Vars);
						$Sape[$VarKey]=$VarKey;
					}
				}
			}		
			elseif($Count<=count($Vars))
			{//вариаций ключа больше, чем необходимо
				if($Addon>0 && $Count!=count($Vars))
				{//стараемся востановить равновесие	
					$delta=count($Vars)-$Count;
					if($delta>$Addon)
						$delta=$Addon;
					$Addon-=$delta;
					$Count+=$delta;
				}
				if($Count==count($Vars))
				{//Необходимо столько же сколько и вариаций ключа
					$Count=0;
					foreach($Vars as $VarKey => $Data)
						$Sape[$VarKey]=GetVariant($Data);
				}
				else
				{
					while($Count--)
					{	
						$Var=GetVariant(GetVariantAndUnset($Vars));
						$Sape[$GetVariantAndUnset_LastKey]=$Var;
					}
				}
			}
			else//$Count>count($Vars)!=0
			{	
				//генерируем ключи с околоссылочным 
				$Vars2=$Vars;
				foreach($Vars as $VarKey => $Data)
				{
					$Sape[$VarKey]=GetVariant($Data);
					unset($Vars2[$VarKey][$GetVariant_LastKey]);
					if(!count($Vars2[$VarKey]))	
						unset($Vars2[$VarKey]);
					$Count--;
				}
				//генерируем ключи без околоссылочного, но уникальные
				$Vars0=$Data['variantes'];
				//Удаляем уже отобранные
				if($Vars0 && count($Vars0)>count($Vars))
				{
					foreach($Vars as $VarKey => $Data)
						unset($Vars0[$VarKey]);
					$Vars0=array_keys($Vars0);
					while($Count--)
					{
						$VarKey=GetVariantAndUnset($Vars0);
						if(!$VarKey)
							break;
						$Sape[$VarKey]=$VarKey;
					}
				}
				$Vars=$Vars2;//убиваем уже выбранные вариации кеев
				if(!$Count||!$Vars || !count($Vars))
					continue;
				if($Count<=count($Vars))
				{
					while($Count--)
					{	
						$Var=GetVariant(GetVariantAndUnset($Vars));
						$Sape[$GetVariantAndUnset_LastKey]=$Var;
					}
				}
				else
				{
					$VarKeys=Array();//array_keys($Vars);
					$Repeats=Array();//Число повторов
					$MaxRepeats=floor(($Count+1.0)/count($Vars))+1;//максимум повторений
					foreach($Vars as $VarKey => $Data)
					{
						for($i=0;$i<$MaxRepeats,$i<count($Data);$i++)
							$VarKeys[]=$VarKey;
						$Repeats[$VarKey]=1;
					}
					while($Count--)
					{	
						$VarKey=GetVariantAndUnset($VarKeys);
						if(!$VarKey) break;
						$Key=GetVariantAndUnset($Vars[$VarKey]);
						if(!$Key) break;
						$VarKey.=str_repeat(' ',$Repeats[$VarKey]);//чтобы массив по ключам не склеился
						$Sape[$VarKey]=$Key;
					}
				}
			}
		}
		//раставляем сапа ссылки 
		$Sape=FinalizeSapeKeys($Sape,$LogicRes,$City);
		if(count($Sape)>$SapeKeys)
		{
			$SapeB=Array();
			$Sape0=Array();
			$Sape1=Array();
			$Sape2=Array();
			foreach($Sape as $Lnk)
			{
				if(mb_strlen($Lnk,'utf-8')>100)
					$SapeB[]=$Lnk;
				elseif(strpos($Lnk,'#')===false)	
					$Sape0[]=$Lnk;
				elseif($Lnk{0}=='#'||$Lnk{strlen($Lnk)-1}=='#')	
					$Sape1[]=$Lnk;
				else
					$Sape2[]=$Lnk;
			}
			$Count=$SapeKeys;
			$Sape=Array();
			$SapeT=Array($Sape2,$Sape1,$Sape0,$SapeB);
			foreach($SapeT  as $Data)
			{
				foreach($Data as $Lnk)
				{
					if(!$Count)
						break 2;
					$Sape[]=$Lnk;
					$Count--;				
				}
			}
		}
		return Array('Clear'=>$ClearKeys,'count(Sape)'=>count($Sape),'Sape'=>$Sape,'Base'=>$Base,'Dirty'=>$Dirty);
	}
	function SynKeyWordByCity($Key,$full=true,$ta=Array())
	{
		global $WordStatCities;
		if(is_array($Key) && $Key['clear'])
		{	
			$aCity=$Key;
			$Key=false;
			$Res= DoSynKeyWordByCity($aCity);
		}
		else
		{
			$aCity=GetCityFromKeyWord($Key);
			if($aCity===false)
				$Res= Array($Key);
			else
				$Res= DoSynKeyWordByCity($aCity,$Key);
		}
		if(!$full)
			return $Res;
		$Res2=Array();
		if($aCity===false)
		{
			$Replaces=DoReplacesInKey($Key,true,$ta);
			return $Replaces;
		}
		$Replaces=DoReplacesInKey($aCity['clear'],true,$ta);
		$tCity=$aCity;
		foreach($Replaces as $Replace)
		{
			$tCity['clear']=$Replace;
			$Res2=array_merge($Res2,DoSynKeyWordByCity($tCity));
		}
		foreach($Res2 as $Key=>$C)
		{
			$Res2[$Key]=trim($Res2[$Key],', ');
			$Res2[$Key]=str_replace(Array(',,','  '),Array(',',' '),$Res2[$Key]);
			$Res2[$Key]=str_replace(Array(',,','  '),Array(',',' '),$Res2[$Key]);
			$Res2[$Key]=str_replace(Array(',,','  '),Array(',',' '),$Res2[$Key]);
		}
		return $Res2;
	}
	function DoSynKeyWordByCity($aCity,$Key=false)
	{	
		global $WordStatCities;
		$Keys=Array();
		if($Key)
			$Keys[]=$Key;
		$City=$WordStatCities[$aCity['n']];
		$Key=$aCity['clear'];
		$Keys[]= $City[0].' '.$Key;
		$Keys[]= $City[0].', '.$Key;
		$Keys[]= $Key.' '.$City[0];
		$Keys[]= $Key.', '.$City[0];
		$Keys[]= $Key.' '.$City[2];
		if($aCity['form']==mb_strtolower($City[1],'utf-8')
		||is_key_mn($Key))
			$Keys[]= $Key.' '.$City[1];
		$Res=$Keys;
		foreach($Keys as $Key)
			if($Key!=mb_strtolower($Key,'utf-8'))
				$Res[]=mb_strtolower($Key,'utf-8');
		return $Res;
	}
//	echo 'HaveKeyWordCity::погода в самарской области=='.HaveKeyWordCity('погода в самарской области').'<br />';
//	exit();
	function HaveKeyWordCity($Key,$Key0='')
	{
		global $WordStat_AllCities;
		if (GetCityFromKeyWord($Key))
			return true;
		$Key=trim($Key);
		$Key0=trim($Key0);
		static $Arr=Array(
			'городе ','города ','городу ','город ',' г ',' г. ',
			'бурге ', 'бург ','бурга ','ск ','ска ','ске ',
			'град ','граде ','грода ',
			' области ',' самар',
			'градская ','градские ','градских ','градский ','градское ',
			'городcкая ','городcкие ','городcких ', 'городcкий ','городcкое ',
			'сити ','цк ','цка ','цке ',' спб',' ростов',
			'рске ','рск ','рска ','рские ','сибир',
			' ево', ' кипр',
			' кривой рог',' кривом рог',' кривого рог',
			' крае ',' край ',' края ',
			' область ',' области ',
			'украина','украине','украины',
			' рф ',' россия ',' россии ',' россию ',
			'киев ','киева ','киеве ',
			' киров ', ' тайланд ',' турция ', ' египет ', 
			'ева ','еве ','аев ',' уфа ',' уфе ',' уфы ',
			' пхукет',' шарма ', ' фобос',' бали '
			,' паттайя ',' паттайе ',' патайя ',' патайе ',
			' великие лук',' великих лук',
			' карпат', ' крым',' ялт',' сочи ', ' гоа ', ' барнаул',
			' эль ',' набережных челн', ' набережные челн',
			' санкт',' уренгой',' уренгое',' уренгоя',' тунис', ' саратов',' ставропол'
		);
		if($Key  != trim(str_replace($Arr,'',' '.$Key.' '))
		 &&$Key0 == trim(str_replace($Arr,'',' '.$Key0.' ')))
			return true;
		if($Key  != trim(str_replace($WordStat_AllCities,'',' '.$Key.' '))
		 &&$Key0 == trim(str_replace($WordStat_AllCities,'',' '.$Key0.' ')))
			return true;
		$a=split(' ',$Key);
		if(($a[count($a)-2]=='в'||$a[count($a)-2]=='во')
		 && $a[count($a)-1]!='расрочку'
		 && $a[count($a)-1]!='рассрочку'
		 && $a[count($a)-1]!='кредит')	
			return true;
		return false; 
	}
	function ClearCityFromKeyWord($Key)
	{
		$City=GetCityFromKeyWord($Key);
		if($City && $City['clear'])
			return $City['clear'];
		return $Key;
	}
	function GetCityFromKeyWord($Key)
	{
		global $WordStatCities,$WordStat_StartCities,$WordStat_EndCities;
		$nKey=split(' ',trim($Key,'utf-8'));
		$Key=split(' ',trim(mb_strtolower($Key,'utf-8')));
		if(count($Key)>=4)
		{
			$с1=$Key[count($Key)-3];
			$с2=$Key[count($Key)-2];
			$с3=$Key[count($Key)-1];
			if(($с1=='в'||$с1=='во')
			 &&($с2=='г'||$с2=='г.'||$с2=='городе')
			 && isset($WordStat_EndCities[$с3]))
			{
				unset($nKey[count($Key)-1]);
				unset($nKey[count($Key)-2]);
				unset($nKey[count($Key)-3]);
				$Key0=JOIN(' ',$nKey);
				return Array('n'=>$WordStat_EndCities[$с3],
							 'form'=>$с3,
							 'clear'=>JOIN(' ',$nKey));
			}
		}
		if(count($Key)>=3)
		{
			$с1=$Key[count($Key)-2];
			$с2=$Key[count($Key)-1];
			if(($с1=='г'||$с1=='г.'||$с1=='города'||$с1=='в'||$с1=='во')
			&&(isset($WordStat_EndCities[$с2])))
			{
				unset($nKey[count($Key)-1]);
				unset($nKey[count($Key)-2]);
				$Key0=JOIN(' ',$nKey);
				return Array('n'=>$WordStat_EndCities[$с2],
							 'form'=>$с2,
							 'clear'=>JOIN(' ',$nKey));
			}
		}
		if(count($Key)>=2)
		{
			$сf=$Key[0];
			$сl=$Key[count($Key)-1];
			if(isset($WordStat_EndCities[$сl]))
			{
				unset($nKey[count($Key)-1]);
				$Key0=JOIN(' ',$nKey);
				return Array('n'=>$WordStat_EndCities[$сl],
							 'form'=>$сl,
							 'clear'=>JOIN(' ',$nKey));
			}	
			if(isset($WordStat_StartCities[$сf]))
			{
				unset($nKey[0]);
				return Array('n'=>$WordStat_StartCities[$сf],
							 'form'=>$сf,
							 'clear'=>JOIN(' ',$nKey));
			}	
		}
		return false;
	}
	function SanitarizeWordStatKeyName($Key)
	{
		$Key=str_replace('&nbsp;',' ',$Key);
		$Key=str_replace('+',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		return trim($Key); 
	}
	if(isset($_GET['getcountof']))
	{
		echo GetWordStatCount($_GET['getcountof'],$_GET['kav'],$_GET['voskl']);
	}
	function GetWordStatCount($Key,$Kav=false,$Voskl=false)
	{
		global $snoopy;
		$Key=trim($Key);
		$Key=str_replace(Array('  ',',','.','?','&','-','+','!','"',"'"),' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=str_replace('  ',' ',$Key);
		$Key=trim($Key);
		$WS_StopWords=Array('для'=>1,'по'=>1,'и'=>1,'а'=>1,'на'=>1,'над'=>1,'под'=>1,'кроме'=>1,'но'=>1,'однако'=>1,'для'=>1,'что'=>1,'не'=>1,'где'=>1,'как'=>1,'сколько'=>1,'почем'=>1);

		$Key=split(' ',$Key);
		foreach($Key as $i=> $Word)
		{
			if(!$Word)
				continue;
			if(!$Voskl||isset($WS_StopWords[$Word]))
				$Key[$i]='+'.$Word;
			else
				$Key[$i]='!'.$Word;
		}
		$Key=join(' ',$Key);

		if($Kav)
			$Key='"'.$Key.'"';
		if(!count($snoopy->cookies))
			$snoopy->fetch("http://kiks.yandex.ru/su/");
		$URL = 'http://wordstat.yandex.ru/?cmd=words&page=1&text='.urlencode($Key).'&geo=&text_geo='; 	
		$snoopy->fetch($URL);
		$data = $snoopy->results;
		//return $data;
		$data0=explode('&nbsp;—&nbsp;',$data);
		$data0=explode('&nbsp;',$data0[1]);
		$data0=trim($data0[0]);
		return $data0;
	}
	function GetWordStat($Key,$Max=150)
	{
		global $snoopy;
		$CS= md5($Key);
		if($Max==1)	
			$CS.='limit_1';
		$CashFile="cash\\$CS";
		if(file_exists($CashFile))
			return unserialize(file_get_contents($CashFile));
		$Keys=Array();
		$Near=Array();
		$cur_listing=&$Keys;
		if(!count($snoopy->cookies))
			$snoopy->fetch("http://kiks.yandex.ru/su/");
		if(stripos($Key,'http://')!==0)
			$URL = 'http://wordstat.yandex.ru/?cmd=words&page=1&text='.urlencode($Key).'&geo=&text_geo='; 
		else
			$URL=$Key;
		$snoopy->fetch($URL);
		$data = $snoopy->results;//file_get_contents($url);
		if(stripos($Key,'http://')!==0)
		{
			$data0=split('\&nbsp\;\—\&nbsp\;',$data);
			$data0=split('&nbsp;',$data0[1]);
			$data0[0]=trim($data0[0]);
			if(is_numeric($data0[0]));
			{
				$Keys[SanitarizeWordStatKeyName($Key)]=(int)$data0[0];
				$Count=(int)$data0[0];
			}
		}
		$dom = new domDocument;
		@$dom->loadHTML($data);
		$dom->preserveWhiteSpace = false;
		foreach ($dom->getElementsByTagName('table') as $table)
		{
			if($table->getElementsByTagName('table')->length)
				continue;
			$waslst=false;	
			foreach ($table->getElementsByTagName('tr') as $tr)
			{
				if($tr->getAttribute('class')=='tlist')
				{
					$waslst=true;
					$cur_listing[SanitarizeWordStatKeyName($tr->getElementsByTagName('td')->item(0)->textContent)]
						=(int) (trim($tr->getElementsByTagName('td')->item(2)->textContent));
				}
			}
			if($waslst)
				$cur_listing=&$Near;
		}
		$next=false;
		foreach ($dom->getElementsByTagName('a') as $a)
		{
			if(stripos($a->textContent,'следующая')===0)
			{
				$next=$a->getAttribute('href');
				break;
			}
		}
		$Res=Array('Count'=>$Count,'Keys'=>$Keys,'Near'=>$Near);
		if($Max>1 && $next && !stripos($next,'&page='.$Max) && !stripos($next,'&page='.($Max+1)))
		{
			if(stripos($next,'http://')!==0)
				$next='http://wordstat.yandex.ru/'.$next;
			$Res2=GetWordStat($next);
			$Res['Keys']=array_merge($Res['Keys'],$Res2['Keys']);
			//$Res['Near']=array_merge($Res['Near'],$Res2['Near']);
		}
		$Res['Key']=$Key;
		if($URL!=$Key)
			@file_put_contents($CashFile,serialize($Res));
		return $Res;
	}
	function AWSLink($Key)
	{	
		if($_GET['project'])
		{
			$KeyP=PodsvStopWords($Key);
			$KeyD=DelStopWords($Key);
			return "<!--$Key--><a 
						onclick='return nextdialog();' 
						href='".AWSLinkURL($KeyD)."'>$KeyP</a>";
		}	
		return "<!--$Key--><a href='".AWSLinkURL($Key)."'>$Key</a>";
	}
	function AWSLinkURL($Key)
	{
		$base='aws.php';
		if(!$_GET['project'])
			return $base.'?key='.urlencode($Key);
		else
			return $base.'?project='.$_GET['project'].'&key='.urlencode($Key);
	}
?>