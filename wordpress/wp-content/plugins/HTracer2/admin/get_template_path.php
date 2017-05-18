<?php 
	$GLOBALS['ht_admin_page']='ajax_get_template_path';
	include_once(dirname(__FILE__).'/functions.php');
	htracer_admin_header('Определение путей к шаблонам сайта');
	echo '<h1>'.ht_trans('Определение путей к шаблонам сайта').'</h1>';
	error_reporting(E_ERROR | E_PARSE);
	$mysql_auto_config=auto_detect_mysql_config();
	$Engine=$mysql_auto_config['engine'];
	$EnginePath=false;

	$EnginePathes=Array(
		'ModX'=>'/assets/templates',
		'Bitrix'=>'/bitrix/templates',
		'Drupal'=>'/sites/all/themes',
		'DLE'=>'/templates',
		'Joomla'=>'/templates',
		'WordPress'=>'/wp-content/themes',
	);
	$EngineHelp=Array(
		'WordPress'=>'
			Облако, скорее всего, нужно вставлять в нужном месте <i>sidebar.php</i> или в <i>header.php</i> или в <i>footer.php</i>.<br />
			Ограничения диапозона контекстных ссылок перед и после <i>the_content()</i> в файле <i>single.php</i> и/или <i>page.php</i>  		
		',
	);
	
//	$Engine='WordPress';
	if($Engine)
	{
		$EnginePath=$EnginePathes[$Engine];
		echo  ht_trans('HTracer определил вашу CMS как')." `$Engine`.<br /> ";
		if($Engine=='NetCat')
		{
			if(ht_trans_is_ru())
				echo ht_trans("Шаблоны меняються через админку."). 
					 ht_trans("На главной странице системы администрирования Netcat.").	 
					 ht_trans("нажмите ссылку «Макеты дизайна» в разделе «Инструменты разработчика» и выберите соответсвующие места в дизайне");	 
			else
				echo 'Templates are chenges from Netcat admin';
		}
		else
			echo ht_trans("Скорее всего, шаблоны (темы оформления сайта) находятся в директории")." <b>`$EnginePath`</b>.";
		if(isset($EngineHelp[$Engine]) && ht_trans_is_ru())
			echo '<hr />'.$EngineHelp[$Engine];
	}
	
	if($Engine=='NetCat')
		exit();
	include_once('../keysyn/Snoopy.class.php');
	if(!class_exists('DOMDocument'))
		exit();
	//$_SERVER['HTTP_HOST']='visit.odessa.ua';	
	$URL='http://'.$_SERVER['HTTP_HOST'].'/';

	$snoopy = new Snoopy;
	$snoopy->agent 	 = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
	$snoopy->rawheaders["Accept"] = "text/html";
	$snoopy->rawheaders["Connection"] = "Keep-Alive";
	
	error_reporting(0);
	$snoopy->fetch($URL);
	error_reporting(E_ERROR | E_PARSE);

	$html = $snoopy->results;
	if(!$html)
		exit();
	libxml_use_internal_errors(true);
	$domDoc = new DOMDocument;
	@$domDoc->loadHTML($html);
	$errors=libxml_get_errors();	
	if (!empty($errors)) 
		libxml_clear_errors();
	libxml_use_internal_errors(false);
	$bases=$domDoc->getElementsByTagName('base');
	$Base=$URL;
	foreach ($bases as $cbase)
	{
		if($cbase->hasAttribute('href'))
		{
			$Base=$cbase->getAttribute('href');
			break;
		}
	} 

	$WAS=Array();
	$Hrefs=Array();

	$links=$domDoc->getElementsByTagName('link');
	foreach ($links as $link)
	{
		if($link->hasAttribute('href'))
		{	
			$href=$link->getAttribute('href');
			if(isset($WAS[$href]))
				continue;
			$WAS[$href]=1;
			if(strpos(strtolower($href),'.css'))
				$Hrefs[]=Array($href,100);	
		}
	}

	$imgs=$domDoc->getElementsByTagName('img');
	foreach ($imgs as $img)
	{
		if($img->hasAttribute('src'))
		{
			$href=$img->getAttribute('src');
			if(isset($WAS[$href]))
				continue;
			if(strpos(strtolower($href),'gallery')||strpos(strtolower($href),'.php?'))
				continue;
			$WAS[$href]=1;
			$Hrefs[]=Array($href,50);	
		}
	}
	
	$scripts=$domDoc->getElementsByTagName('script');
	foreach ($scripts as $script)
	{
		if($script->hasAttribute('src'))
		{
			$href=$script->getAttribute('src');
			if(isset($WAS[$href]))
				continue;
			$WAS[$href]=1;
			$Hrefs[]=Array($href,30);	
		}
	}
	$host=strtolower($_SERVER['HTTP_HOST']);
	if($host=='htest.ru')
		$host='visit.odessa.ua';

	$Pathes=Array();
	foreach ($Hrefs as $Cur)
	{
	//	echo $Href.'<br />';
		$Href=$Cur[0];
		$Eva=$Cur[1];
		if(!$Href ||$Href[0]=='.')
			continue;
		if(strpos($Href,'http:')!==0)
		{
			$lsb=$Base[strlen($Base)-1];
			if(($Href[0]=='/'||$Href[0]=="\\") 
			&& ($lsb=='/'||$lsb=="\\"))
				$Href=substr($Href[0],1);
			$Href=$Base.$Href;
		}
		if(strtolower($Href)==strtolower($URL)||strtolower($Href).'/'==strtolower($URL))
			continue;
		$Href=dirname($Href);
		if(strtolower($Href)==strtolower($URL)||strtolower($Href).'/'==strtolower($URL))
			continue;
		if(strpos(strtolower($Href),strtolower($host))===false)
			continue;
		if(strpos(strtolower($Href),'https:'))
			continue;
		if(strpos(strtolower($Href),'plugins')||strpos(strtolower($Href),'components')||strpos(strtolower($Href),'banners'))
			$Eva=$Eva/2;
		if(strpos(strtolower($Href),'jquery')||strpos(strtolower($Href),'gallery'))
			$Eva=$Eva/1.5;
		if(strpos(strtolower($Href),'theme') || strpos(strtolower($Href),'template'))
			$Eva=$Eva*1.5;
		if(strpos(strtolower($Href),'themes') || strpos(strtolower($Href),'templates')||strpos(strtolower($Href),'default'))
			$Eva=$Eva*1.1;
		if(strpos(strtolower($Href),'/themes/') || strpos(strtolower($Href),'/templates/'))
			$Eva=$Eva*1.2;
			
		$Href=explode('/',$Href);
		if($Href[count($Href)-1]==='')
			unset($Href[count($Href)-1]);
		$lst=$Href[count($Href)-1];
		if($lst==='images'||$lst==='image'||$lst==='img'||$lst==='imgs'||$lst==='include'||$lst==='js'||$lst==='css')
			unset($Href[count($Href)-1]);
		$Href=join('/',$Href);
		$Href=str_replace('http://'.$host,'',$Href);
		$Href=str_replace('http://www.'.$host,'',$Href);
		if(!isset($Pathes[$Href]))
			$Pathes[$Href]=0;
		if(strpos(strtolower($Href),'http:'))
			continue;
		if(count(explode('/',$Href))<=2)
			$Eva=$Eva/1.3;

		$Pathes[$Href]+=$Eva;
	}
	arsort($Pathes);
	$i=0;
	foreach($Pathes as $Path => $Eva)
	{
		if($Eva<30 || $i>4 || !$Path)
			unset($Pathes[$Path]);
		else
			$i++;
	}
	if(count($Pathes))
	{
		echo '<hr /><h2>'.ht_trans('Наиболее вероятные места нахождения шаблонов').'</h2> 
				<ul>
		';
		foreach($Pathes as $Path => $Eva)
			echo "<li>$Path</li>";
		echo '</ul>';
	}
	else
		echo ht_trans('К сожалению, HTracer не смог определить путь к шаблону по адрессу подключения картинок и css-файлов');
	$ePathes=Array();
	if($EnginePath)
	{
		foreach($Pathes as $Path => $Eva)
			if(strpos($Path,$EnginePath)===0)
				$ePathes[]=$Path;			
	}
	if(count($ePathes)===1)
	{
		echo '	<hr />
				<h2>'.$ePathes[0].'<h2>
		';
	}
	elseif(count($ePathes))
	{
		echo '<hr /><ul>';
		foreach($ePathes as $Path)
		foreach($ePathes as $Path)
			echo "<li><b>$Path</b></li>";
		echo '</ul>';
	}
	htracer_admin_footer();
?>