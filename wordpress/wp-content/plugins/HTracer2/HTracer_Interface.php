<?php
function hkey_rand($key=false)
{
	return HTracer::myRand($key);
}
function insert_keywords_cb($html)
{	
	htracer_restore_globals();
	return insert_keywords($html,false,false);
}
function insert_keywords($html,$where=false,$params=false)
{
	htracer_restore_globals();
	return HTracer::Insert($html,$where,$params);
}
function get_keys_cloud($params="")
{
	htracer_restore_globals();
	return HTracer::Cloud($params);
}
function get_keys_cloud_subdir($params="",$MinLinks=5)
{
	$ReqUrl=$_SERVER["REQUEST_URI"];// Получаем кусок текущего URL после домена 
	if(getenv("REQUEST_URI"))// Для джумлы, чтобы пермалинки работали
		$ReqUrl=getenv("REQUEST_URI");
	$RURL=' '.$ReqUrl.' ';
	
	//На всякий случай удаляем http:// вдруг мы получили полный адрес
	$RURL=str_replace('http://',' ',$RURL);
	$RURL=explode('/',$RURL);
	if(count($RURL)<3||!trim($RURL[1]))//Если директории в адресе нет 
		 return get_keys_cloud($params);
	$RURL='/'.trim($RURL[1]).'/';
	
	//Проверяем на всякий случай директория ли это
	if(strpos($RURL,'&')      || strpos($RURL,'?') 
	|| strpos($RURL,'.htm/')  || strpos($RURL,'.php/')
	|| strpos($RURL,'.html/') || strpos($RURL,'.asp/'))
		return get_keys_cloud($params);
	
	//Если ссылок а директории меньше чем необходимо
	if($MinLinks && count(HTracer::SelectMaxPages($MinLinks+1,$RURL))<$MinLinks)
		return get_keys_cloud($params);
		
	if(is_string($params) || $params===false || $params===NULL)
	{
		$RURL="urlstart=$RURL";
		if($params)
			$params.='&'.$RURL;
		else
			$params=$RURL;
	}
	elseif(is_array($params))
		$params['urlstart']=$RURL;
	return get_keys_cloud($params);
}	
function htracer_get_queries_like($Like=false,$Count=7,$Highlight=true,$NotThis=true)
{
	return HTracer::get_queries_like($Like,$Count,$Highlight,$NotThis);
}
function htracer_print_queries_like($Like=false,$Count=7,$Highlight=true,$NotThis=true)
{
	echo htracer_get_queries_like($Like,$Count,$Highlight,$NotThis);
}	
function the_keys_cloud_subdir($params="",$MinLinks=5)
{
	echo get_keys_cloud_subdir($params,$MinLinks);
}
function get_the_keys_cloud($params="")
{
	return get_keys_cloud($params);
}
function the_keys_cloud($params="")
{
	echo get_keys_cloud($params);
}
function get_meta_keywords($Razd=', ',$Encoding=false)
{
	htracer_restore_globals();
	if(!$Encoding)
		$Encoding=$GLOBALS['htracer_encoding'];
	if(!$Encoding||strtolower($Encoding)==='auto'||strtolower($Encoding)==='global')
		$Encoding='UTF-8';
	$res= HTracer::get_meta_keys($Razd);
	if(!$res && $GLOBALS['htracer_test'])
		$res= 'HTracer_Test Тест';
	if($res!='HTracer_Test Тест' && $GLOBALS['htracer_test'])
		$res.=' (HTracer_Test Тест)';
	if($Encoding!=='UTF-8'&& strtolower($Encoding)!=='utf-8'||strtolower($Encoding)!=='utf8')
		$res=mb_convert_encoding($res, $Encoding, 'UTF-8');
	return trim($res);
}
function the_meta_keywords($Razd=', ',$Encoding=false)
{
	echo get_meta_keywords($Razd,$Encoding);
}
function get_meta_keys_tag($Razd=', ',$Encoding=false)
{
	htracer_restore_globals();
	$keys=trim(get_meta_keywords($Razd,$Encoding));
	if($keys)
		return ' <meta name="keywords" content="'.$keys.'"> ';
}
function the_meta_keys_tag($Razd=', ',$Encoding=false)
{
	echo get_meta_keys_tag($Razd,$Encoding);
}
function get_rand_key($URL=false,$Sanitarize=true,$UpCaseFirst=true,$Encoding=false)
{
	//echo "get_rand_key($URL)<br />";
	htracer_restore_globals();
	if(!$Encoding)
	{
		$Encoding=$GLOBALS['htracer_encoding'];
		if($Encoding==='auto'||$Encoding==='global')
		{
			$Encoding='UTF-8';
		}
		else
		{
			$LE=strtolower($Encoding);
			if($LE==='auto'||$LE==='global')
				$Encoding='UTF-8';
		}
	}
	if(!$URL)
		$URL=$_SERVER["REQUEST_URI"];
		
	$res=HTracer::GetRandKey($URL,$Sanitarize);
	if(!$res && $GLOBALS['htracer_test'])
		$res='HTracer_Test Тест';
	if($res!='HTracer_Test Тест' && $GLOBALS['htracer_test'])
		$res.=' (HTracer_Test Тест)';
	if($UpCaseFirst)
		$res=mb_ucfirst($res);
	if($Encoding!=='UTF-8'&& strtolower($Encoding)!=='utf-8'||strtolower($Encoding)!=='utf8')
		$res=mb_convert_encoding($res, $Encoding, 'UTF-8');
	return $res;
}
function the_rand_key($URL=false,$Sanitarize=true,$UpCaseFirst=true,$Encoding=false)
{
	echo get_rand_key($URL,$Sanitarize,$UpCaseFirst,$Encoding);
}
function sanitarize_keyword($Str)
{	
	return HTracer::Sanitarize($Str);
}
function page_keys_count($URL=false)
{//возвращает сохраняемое в БД число переходов с поисковиков на эту страницу
	htracer_restore_globals();
	if($URL===false)
		$URL=$_SERVER["REQUEST_URI"];
	else
		$URL=str_replace(HTracer::SiteUrl(),'',$URL);
	$CS=MD5($URL);
	return HTracer::SelectCountOfKeys($CS);
}
?>