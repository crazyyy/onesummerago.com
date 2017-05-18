<?php 	
	//Вспомогательные функции
	
	$GLOBALS['HTracer_Files_Count']=64;//если MYSQL не работает, то на сколько файлов разбивать 
	$GLOBALS['ht_options_array'] = Array
	(
		'htracer_test'=>false,
		'htracer_encoding'=>'utf-8',
		'htracer_cash_days'=>0,
		'htracer_cash_use_gzip'=>false,
		'htracer_cash_save_full_pages'=>true,
		
		'htracer_mysql'=>false,
		'htracer_mysql_login'=>'root',
		'htracer_mysql_pass'=>'',
		'htracer_mysql_dbname'=>'seotest',
		'htracer_mysql_host'=>'localhost',
		'htracer_mysql_prefix'=>'',
		'htracer_mysql_disable_auto_detect'=>false,
		'htracer_mysql_set_names'=>'auto',
		'htracer_mysql_dont_create_tables'=>false,
		'htracer_mysql_ignore_mysql_ping'=>false,
		
		'htracer_use_php_dom'=>false,

		'insert_keywords_where'=>'img_alt+meta_keys+a_title',

		'hkey_insert_context_links'=>false,//'ranges'
		//'htracer_context_links_class'=>'alink',
		//'htracer_context_links_acceptor_pages'=>300,
		'htracer_site_stop_words'=>'',
		'htracer_context_links_b'=>false,
		'htracer_trace_grooping'=>'1',
		'htracer_trace'=>true,
		'htracer_mysql_optimize_tables'=>false,
		'htracer_mysql_close'=>false,

		'htracer_trace_double_not_first_page'=>false,
		'htracer_trace_double_comercial_query'=>false,
		'htracer_trace_sex_filter'=>false,
		'htracer_trace_free_filter'=>false,
		'htracer_trace_download_filter'=>false,
		'htracer_trace_service_filter'=>false,		
		
		'htracer_admin_pass'=>'',
	
		'htracer_meta_keys_rewrite'=>false,
		'htracer_img_alt_rewrite'=>false,
		'htracer_a_title_rewrite'=>false,  
		
//3.0
		'htracer_validate'=>true,
		'htracer_short_cash'=>true,
		'htracer_only_night_update'=>false,
		'htracer_symb_white_list'=>false,
		
		'htracer_show_all_options'=>false,
		
		'htracer_cloud_links'=>30,
		'htracer_cloud_randomize'=>1,
		'htracer_cloud_min_size'=>70,
		'htracer_cloud_max_size'=>180,
		'htracer_cloud_style'=>'',
		
		'htracer_clcore_size'=>1000,
		'htracer_max_clinks'=>10,
		'htracer_clinks_segment_lng'=>100,
		
//3.1.2
		'htracer_context_links_selector'=>'',
		'htracer_cloud_selector'=>'',
		'htracer_cloud_post'=>'',
		'htracer_cloud_pre'=>'',
//3.1.3
		'htracer_trace_runaway'=>1.05,
		'htracer_trace_runaway_start_time'=>0,
		'htracer_trace_view_depth'=>0,
		'htracer_trace_use_targets'=>false,

		'htracer_trace_p1_url'=>'/',
		'htracer_trace_p2_url'=>'/',
		'htracer_trace_p3_url'=>'/',
		'htracer_trace_p4_url'=>'/',
		'htracer_trace_p5_url'=>'/',

		'htracer_trace_p1_bonus'=>0,
		'htracer_trace_p2_bonus'=>0,
		'htracer_trace_p3_bonus'=>0,
		'htracer_trace_p4_bonus'=>0,
		'htracer_trace_p5_bonus'=>0,

		'htracer_pconnect'=>false,
// 3.3
		'htracer_url_exceptions'=>"/admin/\n/administrator/\n/wp-admin/\n",
		'htracer_usp'=>false,
		'htracer_title_order'=>false,
		'htracer_title_spacer'=>'',
		
//3.3.1	
		'htracer_numeric_filter'=>true,
		'htracer_not_ru_filter'=>false,

//3.3.2
		'htracer_premoderation'=>false,
		'htracer_mats_filter'=>true,
		'htracer_404_plugin'=>false,

		'htracer_404'=>true,
		'htracer_301'=>true,
//3.4
		'htracer_ulink_plugin'=>false,
		'htracer_ulink_count'=>1,
		'htracer_ulink_list'=>'comma',
		
		
		'htracer_ulink_fast'=>false,
		'htracer_ulink_other_domains'=>true,
//3.4.1
		'htracer_ulink_ignore_more'=>true,
		'htracer_user_minus_words'=>"word1,word2,word3",
	
	
		
		/*'htracer_permalinks'=>false,
		'htracer_breadcrumbs'=>false,

		'htracer_ignored_get_params'=>'',
		'htracer_ignored_get_params_rel'=>true,
		*/
		
	);
	
	//HPROTECTION
	if(!isset($GLOBALS['htracer_context_links_acceptor_pages'])|| !$GLOBALS['htracer_context_links_acceptor_pages'])
		$GLOBALS['htracer_context_links_acceptor_pages']=300;
	$GLOBALS['htracer_context_links_acceptor_pages']=intval($GLOBALS['htracer_context_links_acceptor_pages']);
	
	// Параметры формирование метакейвордс
	if(!isset($GLOBALS['htracer_metakeys_max_len']))
		$GLOBALS['htracer_metakeys_max_len']=200;//длина в символах
	if(!isset($GLOBALS['htracer_metakeys_max_words1']))
		$GLOBALS['htracer_metakeys_max_words1']=5;//число однословников 
	if(!isset($GLOBALS['htracer_metakeys_max_words2']))
		$GLOBALS['htracer_metakeys_max_words2']=4;//число двухсловников 
	if(!isset($GLOBALS['htracer_metakeys_max_words3']))
		$GLOBALS['htracer_metakeys_max_words3']=2;//число трехсловников 
	if(!isset($GLOBALS['htracer_metakeys_max_forms']))
		$GLOBALS['htracer_metakeys_max_forms']=2;	//число словоформ 
	if(!isset($GLOBALS['ht_is_this_admin_options']))
		$GLOBALS['ht_is_this_admin_options']=false;
		
	if(!isset($GLOBALS['ht_admin_page']))
		$GLOBALS['ht_admin_page']=false;

	HTracer_Load_Default_Options();
	
	if($GLOBALS['htracer_meta_keys_rewrite']
	||$GLOBALS['htracer_img_alt_rewrite']
	||$GLOBALS['htracer_a_title_rewrite'])
	{
		$Params=Array();
		if(isset($GLOBALS['insert_keywords_params']) && $GLOBALS['insert_keywords_params'])
		{
			if(is_string($GLOBALS['insert_keywords_params']))
				$Params=htracer_parse_params($GLOBALS['insert_keywords_params']);
			elseif(is_array($GLOBALS['insert_keywords_params']))
				$Params=$GLOBALS['insert_keywords_params'];
		}
		$Params['meta_keys_rewrite']=$GLOBALS['htracer_meta_keys_rewrite'];
		$Params['img_alt_rewrite']=$GLOBALS['htracer_img_alt_rewrite'];
		$Params['a_title_rewrite']=$GLOBALS['htracer_a_title_rewrite'];
		$GLOBALS['insert_keywords_params']=$Params;
	}
	//HPROTECTION

	//Нормализация параметров	
	$GLOBALS['htracer_cash_days']			= intval($GLOBALS['htracer_cash_days']);
	$GLOBALS['htracer_metakeys_max_len']	= intval($GLOBALS['htracer_metakeys_max_len']);
	$GLOBALS['htracer_metakeys_max_words1'] = intval($GLOBALS['htracer_metakeys_max_words1']);
	$GLOBALS['htracer_metakeys_max_words2'] = intval($GLOBALS['htracer_metakeys_max_words2']);
	$GLOBALS['htracer_metakeys_max_words3'] = intval($GLOBALS['htracer_metakeys_max_words3']);
	$GLOBALS['htracer_metakeys_max_forms']  = intval($GLOBALS['htracer_metakeys_max_forms']);

	if($GLOBALS["htracer_test"] && !isset($GLOBALS["htracer_is_options.php"]))
		$GLOBALS["htracer_cash_days"]=-1;
	
	function HTracer_Load_Default_Options($forced=false)
	{
		foreach($GLOBALS['ht_options_array'] as $Name => $Default)
			if($forced||!isset($GLOBALS[$Name]))
				$GLOBALS[$Name]=$Default;
	}
	if(!function_exists('ht_calc_wp_key'))
	{
		function ht_calc_wp_key()
		{
			$aCS=md5(AUTH_KEY.SECURE_AUTH_KEY.LOGGED_IN_KEY);
			if(function_exists('sha1'))
				$aCS=sha1($aCS);
			return $aCS; 	
		}
	}
	function HTracer_Load()
	{
		HTracer_Load_Default_Options();
		if($GLOBALS['htracer_mysql']==='forced' && !isset($GLOBALS['htracer_admin_page']))
			hkey_connect_to_mysql();
		if($GLOBALS['htracer_mysql'])
			HTracer::CreateTables();
		if($GLOBALS['htracer_trace'])
			HTracer::AddQuery();
	}

	
	$GLOBALS["hkey_connect_to_mysql_was"]=false;
	$GLOBALS["hkey_connect_to_mysql_was_error"]=false;
	function hkey_check_connection_to_mysql()
	{
		if(!isset($GLOBALS['htracer_mysql']))
			$GLOBALS['htracer_mysql']=false;
		if(!isset($GLOBALS['htracer_mysql_ignore_mysql_ping']))
			$GLOBALS['htracer_mysql_ignore_mysql_ping']=false;

		if(!$GLOBALS['htracer_mysql'])
			return 10;//MySQL выключен
		if(!function_exists('mysql_connect'))
			return 15;//Сервер не поддерживает MySQL
		elseif(@mysql_ping() && !$GLOBALS['htracer_mysql_ignore_mysql_ping'])
			return 20;//Включите игнорирование mysql_ping
		elseif(!$GLOBALS['htracer_mysql_login'] 
		 	 && $GLOBALS['htracer_mysql_login']!=='0'
			 && $GLOBALS['htracer_mysql_login']!==0)
			return 30;//Имя пользователя MySQL пустое
		else
		{
			$link=@mysql_connect($GLOBALS['htracer_mysql_host'],$GLOBALS['htracer_mysql_login'],$GLOBALS['htracer_mysql_pass']);
			if(!$link)
				return 40;//Хост, логин или пароль не верны 
			if(!$GLOBALS['htracer_mysql_dbname'] 
			&& $GLOBALS['htracer_mysql_dbname']!=='0'
			&& $GLOBALS['htracer_mysql_dbname']!==0)
				return 50;//Имя базы данных пустое
			if(!@mysql_select_db($GLOBALS['htracer_mysql_dbname'],$link))
				return 60;//Такой БД не существует, либо у пользователя нет прав доступа к ней
		}
		if(strpos(strtolower($GLOBALS['htracer_mysql_dbname']),'htracer')!==false
		|| mysql_num_rows(mysql_query("SHOW TABLES FROM `{$GLOBALS['htracer_mysql_dbname']}`",$link))<=2)
			return 1010;//Возможно, БД сайта не совпадает с БД HTracer  
		return 0;//доступы верны
	}
	//htracer_pconnect
	
	function hkey_connect_to_mysql($SuperForced=false)
	{		
		if(isset($GLOBALS['htracer_admin_page']) && $GLOBALS['htracer_admin_page']==='ajax_test_mysql')
			return;
			
		//HPROTECTION
		$GLOBALS["hkey_connect_to_mysql_was"]=true;
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);
		HTracer_In();
		
		if(!$SuperForced && 		
		(!$GLOBALS['htracer_mysql']
		||(isset($GLOBALS['htracer_mysql_link']) && $GLOBALS['htracer_mysql_link'])
		||(@mysql_ping() && !$GLOBALS['htracer_mysql_ignore_mysql_ping'])))
		{
			if((!isset($GLOBALS['htracer_mysql_link'])||!$GLOBALS['htracer_mysql_link']) 
			&& $GLOBALS['htracer_mysql']==='forced' 
			&& @mysql_ping() && !$GLOBALS['htracer_mysql_ignore_mysql_ping'])
			{
				echo   '<b>Forcing of MySQL connection not posible!</b><br />
						Code of HTracer run when connection to MySQL is already established<br />
						If HTracer will connect to MySQL, this can lead to errors of CMS.<br />
						Move HTracer code to another place OR disable forced MySQL connection in HTracer options<br />
						If you see this message in HTracer admin &mdash; switch on option "ignore mysql_ping"';
			}
			HTracer_Out();
			return;
		}
		if(isset($GLOBALS['htracer_pconnect']) && $GLOBALS['htracer_pconnect'] && function_exists('mysql_pconnect'))	
			$link=mysql_pconnect( $GLOBALS['htracer_mysql_host'],$GLOBALS['htracer_mysql_login'],$GLOBALS['htracer_mysql_pass']);
		else
			$link=mysql_connect( $GLOBALS['htracer_mysql_host'],$GLOBALS['htracer_mysql_login'],$GLOBALS['htracer_mysql_pass']);
		
		$GLOBALS['htracer_mysql_link']=$link;
		if($link)
		{	
			if(!$GLOBALS['htracer_mysql_dbname'] 
			&& $GLOBALS['htracer_mysql_dbname']!=='0'
			&& $GLOBALS['htracer_mysql_dbname']!==0)
			{
				if($GLOBALS['ht_is_this_admin_options'])
				{
					$GLOBALS["hkey_connect_to_mysql_was_error"]=true;
					echo '<b style="color:red">DB name is void</b>';
				}
				else
					DIE("DB name is void");	
			}
			if(!mysql_select_db($GLOBALS['htracer_mysql_dbname'],$link))
			{
				if(isset($GLOBALS['ht_is_this_admin_options']) && $GLOBALS['ht_is_this_admin_options'])
				{
					$GLOBALS["hkey_connect_to_mysql_was_error"]=true;
					echo '<b style="color:red">DB name is not correct '.mysql_error().'</b>';
				}
				else
					DIE("Connect to MySQL is not establiched");	
			}
			elseif($GLOBALS['htracer_mysql_set_names'] && $GLOBALS['htracer_mysql_set_names']!=='auto')
				mysql_query("SET NAMES '{$GLOBALS['htracer_mysql_set_names']}'") or die ('_SET NAMES :'.mysql_error());
			mysql_query("USE `{$GLOBALS['htracer_mysql_dbname']}`;",$link); 
				//or DIE("Выбор БД MySQL не удался. Возможно вы не правильно ввели имя бд".mysql_error());
		}
		else
		{
			if($GLOBALS['ht_is_this_admin_options'])
			{
				$GLOBALS["hkey_connect_to_mysql_was_error"]=true;
				echo '<b style="color:red">MySQL login, password or Host is invalid!</b> '.mysql_error();
			}
			else
				DIE("MySQL login, password or Host is invalid");	
		}
		HTracer_Out();
	}
	if(isset($GLOBALS['htracer_admin_page']) 
	&&(!isset($GLOBALS['ht_is_this_admin_options']) || !$GLOBALS['ht_is_this_admin_options'] || !isset($_POST['waspost']) || !$_POST['waspost']))
		hkey_connect_to_mysql();
	function htracer_insert_clouds($HTML)
	{
		HTracer::P404_process();
		if($GLOBALS['htracer_ulink_plugin'] && strpos($HTML,'<!--ulinks-->')!==false)
			$HTML=str_replace('<!--ulinks-->',htracer_ulinks(),$HTML);
		
		$Arr=explode('<!--the_keys_cloud',$HTML);
		$Res='';
		
		
		foreach($Arr as $i=>$El)
		{
			if($i===0)	
				$Res.=$El;
			else
			{
				$El=explode('-->',$El,2);
				$Params=$El[0];
				if(trim($Params)==='')
					$Res.=get_keys_cloud();
				elseif($Params{0}!=' ')
					$Res.=get_keys_cloud($Params);
				else	
					$Res.=get_keys_cloud(substr($Params,1));
				$Res.=$El[1];
			}
		}
		return $Res;
	}
	
//TODO: сократить вложенность буферов
//TODO: отлавливать функцию отлова ошибок
	function htracer_show_time_cb($html)
	{
		return $html.'<!--ht_show_time='.HTracer_Refresh_Full_Time().'-->';
	}
	function htracer_is_extension_page($URL=false)
	{
		if(!isset($GLOBALS['htracer_url_exceptions'])||!$GLOBALS['htracer_url_exceptions'])
			return false;
		if($URL===false)
			$URL=$_SERVER["REQUEST_URI"];
		$list=explode("\n",$GLOBALS['htracer_url_exceptions']);
		foreach($list as $cur)
		{
			$cur=trim($cur);
			if($cur===''||!$cur)
				continue;
			if($cur[0]!='/')
				$cur='/'.$cur;
			if($cur[strlen($cur)-1]=='#')
			{
				if($URL.'#'==$cur)
					return true;
			}
			elseif($cur[strlen($cur)-1]=='*')
			{
				$cur[strlen($cur)-1]=' ';
				$cur=trim($cur);
				if(strpos($URL,$cur)===0)
					return true;
			}
			else
			{
				if(strpos($URL,$cur)===0||$URL.'/'===$cur)
					return true;
			}
		}
		if(isset($_GET['disable_htracer']) && $_GET['disable_htracer']==$GLOBALS['htracer_ajax_pass'])
			return true;
		return false;
	}
	function htracer_start()
	{
		//HPROTECTION
		if(!htracer_is_extension_page())
		{
		//$GLOBALS['htracer_ajax_pass']
			HTracer_In();
			ob_start('htracer_delete_globals');
			if($GLOBALS['insert_keywords_where']||$GLOBALS['htracer_test'])
				ob_start('insert_keywords_cb');
			if($GLOBALS['hkey_insert_context_links']==='ranges')
				ob_start('hkey_insert_context_links_in_ranges_cb');
			elseif($GLOBALS['hkey_insert_context_links']==='selector')
				ob_start('hkey_insert_context_links_in_selector_cb');
			elseif($GLOBALS['hkey_insert_context_links'])
				ob_start('hkey_insert_context_links_cb');
			ob_start('hkey_insert_cloud_by_selector_cb');
			ob_start('htracer_insert_clouds');
			ob_start('htracer_mysql_close');
			if(isset($_GET['htracer_show_time']) && $_GET['htracer_show_time']==$GLOBALS['htracer_ajax_pass'])
				ob_start('htracer_show_time_cb');
			HTracer_Out();	
		}
	}
	function htracer_ob_start()
	{
		ob_start();
	}
	function htracer_do_all($content)
	{
		if(!htracer_is_extension_page())
		{
			if($GLOBALS['insert_keywords_where']||$GLOBALS['htracer_test'])
				$content=insert_keywords_cb($content);
			if($GLOBALS['hkey_insert_context_links']==='ranges')
				$content=hkey_insert_context_links_in_ranges_cb($content);
			elseif($GLOBALS['hkey_insert_context_links']==='selector')
				$content=hkey_insert_context_links_in_selector_cb($content);
			elseif($GLOBALS['hkey_insert_context_links'])
				$content=hkey_insert_context_links_cb($content);		
		
			$content=hkey_insert_cloud_by_selector_cb($content);
			$content=htracer_insert_clouds($content);
			
			if(isset($_GET['htracer_show_time']) && $_GET['htracer_show_time']==$GLOBALS['htracer_ajax_pass'])
				$content.='<!--ht_show_time='.HTracer_Refresh_Full_Time().'-->';
		}
		return $content;
	}
	function htracer_ob_end($notice_protection=false)
	{
		//error_reporting (E_ERROR | E_PARSE| E_WARNING);//Убираем Нотисы
		HTracer_In();
		htracer_restore_globals();//Исправление ебанизма джумлы
		$content=ob_get_contents(); // Получаем содержимое буфера вывода
		if(!$notice_protection)
			ob_clean(); // Очищаем буфер вывода
		else
		{
			$a=explode("**ht notices end**",$content);
			if(count($a)===2)
				$content=$a[1];
		}
		
		//HPROTECTION
		$content=htracer_do_all($content);
		if($notice_protection)
			ob_clean(); // Очищаем буфер вывода

		echo $content;
		htracer_mysql_close();
		HTracer_Out();
	} 
	function htracer_mysql_close($Str=false)
	{	
		if($GLOBALS['htracer_mysql_close'] && $GLOBALS['htracer_mysql_link'])
			mysql_close($GLOBALS['htracer_mysql_link']);
		return $Str;
	}
	function htracer_mysql_query($query,$Name='',$die=true)
	{		
		//Защита от иньекций через табл префикс. Либо через регистер глобалс либо через админку
		if(strpos($GLOBALS['htracer_mysql_prefix'],'`')!==false)
		{
			echo "<br />Table prefix contains an invalid character '`'. Query was not processed for security reasons.<br />";
			return false;
		}
		
		//Защита от создания шела через MYSQL
		if(function_exists('stripos'))
		{
			if(stripos($query,'OUTFILE')!==false||stripos($query,'dumpfile')!==false)  
				return false;
		}
			
		if(isset($GLOBALS['htracer_admin_page']) && $GLOBALS['htracer_admin_page']==='ajax_test_mysql')
			return false;
		if(isset($GLOBALS['ht_is_this_admin_options']) && isset($_POST['waspost']) && $_POST['waspost'] && !$GLOBALS["hkey_connect_to_mysql_was"])
			return false;
		if((!isset($GLOBALS['htracer_mysql_link'])||!$GLOBALS['htracer_mysql_link']) 
		&& $GLOBALS['htracer_mysql']!=='forced' 
		&& !(@mysql_ping()) && $GLOBALS['htracer_mysql'] && !isset($GLOBALS['ht_admin_page']))
		{
			echo "<br />
					<b style='color:red'>
						MySQL-connection not found. Switch on MySQL forced mode. 
					</b><br />";
			if(!$die)	
				return false;			
			exit();
		}
		if(isset($GLOBALS['htracer_mysql_link']) && $GLOBALS['htracer_mysql_link'])	
			$Res=mysql_query($query,$GLOBALS['htracer_mysql_link']);
		else
			$Res=mysql_query($query);// or die("<h1>$Name</h1> ".mysql_error().'<br>'.$query);
		if(!$Res)
		{
			if(!$die)	
				return false;
			elseif(isset($GLOBALS['ht_is_this_admin_options']) 
 			 &&			 $GLOBALS['ht_is_this_admin_options'])
				echo ("<h1>$Name</h1> ".mysql_error().'<br>'.$query.'<!-- -->');
			else
			{
				$mysql_error=mysql_error();
				if(!$GLOBALS['htracer_admin_page'] && $GLOBALS['htracer_mysql']!=='forced' && strpos($mysql_error, 'using password: NO'))
					die('Set MySQL=forced and Ignore MySQL ping = true');

				if($GLOBALS['htracer_mysql']!=='forced'||$GLOBALS['htracer_admin_page'])
					die("<h1>$Name</h1> ".$mysql_error.'<br />'.$query);
				else
					die("<h1>$Name</h1> ".$mysql_error.'<br />'.$query."<h2>You may need to turn on forcing in MySQL settings of HTracer</h2>");
			}
		}
		else
		{
			//HPROTECTION
		}
		return $Res; 
	}
	// Запоминаем глобальные переменные на случай если CMS ебанутая
//ПРОВЕРИТЬ работу со статическим классом	
	function htracer_save_globals()
	{
		//HPROTECTION
		$GLOBALS['htracer_globals_restored']=false;
		$GLOBALS['htracer_test_globals']=true;
		$_SERVER["ht_temp_globals"]=Array();
		foreach($GLOBALS as $Key => $Value)
		{
			if(!is_object($Value) && 
			(//is_string($Value)||is_int($Value)||is_float($Value)||is_bool($Value)||
			stripos($Key,'ht_')===0 || stripos($Key,'htracer')===0
			||stripos($Key,'hkey')===0|| stripos($Key,'htacer')===0
			||stripos($Key,'insert_keywords')===0
			||$Key=='insert_keywords_params'
			||$Key=='insert_keywords_where'
			||$Key=='HTracer'||$Key=='HTracer_TempObj'||$Key=='HStrem1'))
				$_SERVER["ht_temp_globals"][$Key]=$Value;
		}
	}
	htracer_save_globals();
	function htracer_restore_globals()
	{
		if(isset($_SERVER["ht_temp_globals"]) && !$GLOBALS['htracer_test_globals'])
		{
			foreach($_SERVER["ht_temp_globals"] as $Key => $Value)
				if(!isset($GLOBALS[$Key]))
					$GLOBALS[$Key]=$Value;
			$GLOBALS['htracer_test_globals']=true;
			$GLOBALS['htracer_globals_restored']=true;
		}
	}
	function htracer_delete_globals($Str=false)
	{	
		HTracer_In();	
		if(isset($GLOBALS['htracer_globals_restored']) && $GLOBALS['htracer_globals_restored'])
		{	
			$GLOBALS=Array();
			unset($_SERVER["ht_temp_globals"]);
			$GLOBALS['htracer_globals_restored']=false;
		}
		HTracer_Out();
		return $Str;
	}
	function htracer_is_search_robot()
	{
		if(!isset($_SERVER['HTTP_USER_AGENT']))
			return false;
		$UA=strtolower($_SERVER['HTTP_USER_AGENT']);
		if(strpos($UA,'yandexbot'))
			return 'Y';
		if(strpos($UA,'googlebot'))
			return 'G';
		return false;
	}
	function htracer_ulinks($count=false,$offset=0,$iComma=false)
	{
		if(!$GLOBALS['htracer_ulink_plugin'])
			return '';
		if($count===false)
			$count=$GLOBALS['htracer_ulink_count'];
		if(!$offset)
			$offset=0;
		$Links=Array();
		
		$Need=$count+$offset;
		
		$TableName=HTracer::GetTablePrefix().'htracer_ulinks';
		$CS=MD5(HTracer::GetCurentURL());
		$res=htracer_mysql_query("SELECT * FROM  `$TableName` WHERE `DON_CS`='$CS'");

		$Was=Array();//Урлы на которые уже есть ссылки
		$ToDel=Array();
		while($cur=mysql_fetch_assoc($res))
		{
			$cur['aURL']=HTracer::FixURL($cur['aURL'], !$GLOBAL['htracer_ulink_other_domains']);//Удаляем http://
			$cur['Key']=trim($cur['Key']);
			
			if(isset($GLOBALS['htracer_ulink_ignore_more']) && $Need<=0)
				$ToDel[]=$cur['ID'];
			else
			{
				$Links[]=Array($cur['Key'],$cur['aURL']);
				$Was[$cur['aURL']]=true;
				$Was[$cur['Key']]=true;
				$Need--;
			}
		}
		if(count($ToDel))//Снимаем ссылки
		{
			$ToDel=JOIN(',',$ToDel);
			$CS=MD5('');
			htracer_mysql_query("UPDATE `$TableName` SET `Don`='',`DON_CS`='$CS' WHERE `ID` IN ($ToDel)");
		}
		if($Need>0)//Если нам еще нужны ссылки
		{
			$New=Array();//ID ссылок которые связаны сейчас с донором
			$bot=htracer_is_search_robot();//Либо G либо Y либо false

			for($step=0;$step<2; $step++)//Нулевой шаг ищем трамплины для необходимого нам бота
			{//Потом ссылки у которых нет донора и бот не задан
				if($Need<=0)
					break;

				if(($step===0 && $bot)//Либо Гугл либо Яндекс
				 ||($step===1 && ($bot==$GLOBALS['htracer_ulink_fast']||!$GLOBALS['htracer_ulink_fast'])))//Нужный нам бот или у нас нет условия на ускорения индексирования
				{
					//Мы ищем по МД5
					if($step===0)
						$CS=MD5($bot);//Необходимый нам бот
					else
						$CS=MD5('');//Донор еще не задан
						
					$i=0;//Счетчик циклов
					while($Need>0)//Из-за дублирования URL мы иногда вынуждены запустить несколько циклов
					{
						$i++;
						$j=0;
						$res=htracer_mysql_query("SELECT * FROM `$TableName` WHERE `DON_CS`='$CS' ORDER BY RAND() LIMIT $Need");
						$lNeed=$Need;
						while($cur=mysql_fetch_assoc($res))
						{
							$j++;
							$cur['aURL']=HTracer::FixURL($cur['aURL'], $GLOBAL['htracer_ulink_other_domains']);//Удаляем http://
							$cur['Key']=trim($cur['Key']);
							if(isset($Was[$cur['aURL']]) || isset($Was[$cur['Key']]))
								continue;//Чтобы дубли не попали в выдачу
							$Links[]=Array($cur['Key'],$cur['aURL']);
							$Need--;	
							$Was[$cur['aURL']]=true;
							$Was[$cur['Key']]=true;
							$New[]=$cur['ID'];
						}
						if($j<$lNeed||$i>3)//Либо число циклов больше 3, либо в последнем цикле мы получили меньше ответов, чем запрашивали
							break;
					}
				}
			}
			if(count($New))//Нужно пометить все новые ключи
			{
				$New=JOIN(',',$New);
				$URL=HTracer::GetCurentURL();
				$CS=MD5(HTracer::GetCurentURL($URL));
				$URL=mysql_real_escape_string($URL);
				htracer_mysql_query("UPDATE `$TableName` SET `Don`='$URL',`DON_CS`='$CS' WHERE `ID` IN ($New)");
			}
		}
		//Удаляем первые $offset ключей
		for($i=0;$i<$offset;$i++)
		{
			if(isset($Links[$i]))
				unset($Links[$i]);
			else
				break;
		}
		
		if(!count($Links))
			return '';
		$Links2=Array();
		foreach($Links as $Link)
		{
			$href=$Link[1];
			
			if(strpos($href,'&')!==false && strpos($href,';')===false)
				$href=str_replace("&","&amp;",$href);//Есть амперсант, но нет ";" т.е. точно это не спец. символ. Чтобы валидатор не ругался
			$href='http://'.$_SERVER['SERVER_NAME'].$href;	
				
			if(strpos($href,"'")===false)//Нет одинарных кавычек
				$href="'".$href."'";
			elseif(strpos($href,'"')===false)//Нет двойных кавычек
				$href='"'.$href.'"';
			else//Есть и одинарные и двойные
			{
				$href=str_replace("'","&#039;",$href);
				$href="'".$href."'";
			}
	
			$text=$Link[0];
			$text=str_replace("<","&lt;",$text);

			$Links2[]="<a href={$href}>{$text}</a>";
		}
		//$CommaName=htracer_ulink_list
		$CommaNameToStr = Array('space'=>' ','comma'=>', ','br'=>'<br />','li'=>'</li><li>');
		$CommaStr=' ';
		$CommaName='';
		
		if($iComma!==false)
		{
			if(isset($CommaNameToStr[$iComma]))
			{
				$CommaName=$iComma;
				$CommaStr=$CommaNameToStr[$iComma];
			}
			else
				$CommaStr=$iComma;
		}
		else
		{
			$CommaName=$GLOBALS['htracer_ulink_list'];
			if(isset($CommaNameToStr[$CommaName]))
				$CommaStr=$CommaNameToStr[$CommaName];
		}
		
		$Out=JOIN($CommaStr,$Links2);
		
		if($CommaName==='li')
			$Out="<ul><li>$Out</li></ul>";
		$Out=mb_convert_encoding($Out, 'utf-8', $GLOBALS['htracer_encoding']);
		return $Out;
	}
?>