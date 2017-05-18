<?php 
	if($_SERVER['SERVER_NAME']=='demo.htracer.ru'||$_SERVER['SERVER_NAME']=='demo.htracer.com')
	{
		if(!isset($_COOKIE['prefix']))
		{
			if($_SERVER['SERVER_NAME']=='demo.htracer.ru')
				header('http://demo.htracer.ru/HTracer/admin/create_demo_account.php');
			else
				header('http://demo.htracer.com/HTracer/admin/create_demo_account.php');
			exit();
		}
		$GLOBALS['htracer_is_demo']=true;
		$GLOBALS['htracer_mysql_prefix']=$_COOKIE['prefix'];
	}
	function ht_trans($Str)
	{
		if(ht_trans_is_ru())
			return $Str;
		$dn=dirname(__FILE__);
		$fn=$dn.'/translate.php';
		
		if(!isset($GLOBALS['ht_translate_lib']))
			include($fn);
		if(!isset($GLOBALS['ht_translate_lib'][$Str]))
		{
			$GLOBALS['ht_translate_lib'][$Str]='NULL';
			$text='';
			
			foreach($GLOBALS['ht_translate_lib'] as $Ru=>$En)
			{
				$Ru=str_replace('"','\"',$Ru);
				$En=str_replace('"','\"',$En);
				$text.="\t\"$Ru\" =>\n\t\"$En\",\n\n";
			}
			file_put_contents($fn,'<?php $'."GLOBALS['ht_translate_lib']=Array(\n\n$text\n\n); ?>");
		}
		return $GLOBALS['ht_translate_lib'][$Str];
	}
	
	
	
	function ht_trans_is_ru()
	{
		return true;
	}

	function htracer_admin_is_wp()
	{
		$dirname=dirname(__FILE__);
		return strpos($dirname,'wp-content\plugins')||strpos($dirname,'wp-content/plugins');
	}
	$keysyn_location='../keysyn/';
	if(htracer_admin_is_wp())
	{
		if(function_exists('get_bloginfo'))
			$keysyn_location=get_bloginfo('url').'/wp-content/plugins/HTracer/keysyn/';
		else
			$keysyn_location=$_SERVER['SERVER_NAME'].'/wp-content/plugins/HTracer/keysyn/';
	}
	function ht_pwd_crc($str)
	{
		$str=strtolower(trim($str));
		if(function_exists('sha1'))
			$str=sha1($str);
		if(function_exists('crc32'))
			$str.=crc32($str);
		return md5($str);
	}
	if(!class_exists('db')){class db{};}
	
	function ht_proccess_key_table_update()
	{
		set_time_limit(300);
		$Keys=Array();
		$WasKeys=Array();
		$NewKeys=Array();
		unset($_POST['keys_table_length']);
		foreach($_POST as $Key => $Value)
		{
			$Value=trim($Value);
			$Key=explode('_',$Key);
			$iswas=false;
			$isnew=false;
			if(count($Key)!=2)
			{
				if(count($Key)==3 && $Key[0]=='was')
					$iswas=true;
				elseif(count($Key)==3 && $Key[0]=='new')
					$isnew=true;
				else
					continue;
			}
			$Param=$Key[0+($iswas||$isnew)];
			$ID=$Key[1+($iswas||$isnew)];
			$cKeys=&$Keys;
			if($iswas)
				$cKeys=&$WasKeys;
			elseif($isnew)
				$cKeys=&$NewKeys;
				
			if(!isset($cKeys[$ID]))
			{
				$cKeys[$ID]=Array
				(
					'Status'=>false,
					'ShowInCLinks'=>false
				);
			}
			if($Param=='Status'||$Param=='ShowInCLinks')
				$Value=!!$Value;
			elseif($Param=='Eva')
				$Value=$Value+0;
			$cKeys[$ID][$Param]=$Value;
		}
		$ChangedData=Array();
		$URLs=Array();
			
		foreach($Keys as $ID => $Data)
		{
			foreach($Data as $Param => $Value)
			{
				if($WasKeys[$ID][$Param]==$Value)
					continue;
				if(!isset($_GET['url']))
				{				
					$URL=$WasKeys[$ID]['URL'];
					$URLs[$URL]=$URL;
				}
				if(!isset($ChangedData[$ID]))
					$ChangedData[$ID]=Array();
				$ChangedData[$ID][$Param]=$Value;
				if($Param=='Status'||$Param=='Out')
					$ChangedData[$ID]['Version']='10000';
			}
		}
		HTracer::UpdateTableData('keys',$ChangedData);
		if(isset($_GET['url']))
		{
			foreach ($NewKeys as $k=>$Key)
				$NewKeys[$k]['URL']=$_GET['url'];
			$URLs[$_GET['url']]=$_GET['url'];
		}		
		$KeysCount=HTracer::AddUserKeys($NewKeys);
			
		foreach ($NewKeys as $Key)
			$URLs[$Key['URL']]=$Key['URL'];
		foreach ($URLs as $URL)
			HTracer::RefreshPage($URL,false,true);

		if(isset($_POST['ajax']) && $_POST['ajax'])
		{
			$_GET['iDisplayStart']=0;
			$_GET['iDisplayLength']=$KeysCount;
			$_GET['idsort']=1;
			include('ajax/keys.php');
			exit();
		}
	}
	function ht_proccess_ulinks_table_update()
	{
		set_time_limit(300);
		$Keys=Array();
		$WasKeys=Array();
		$NewKeys=Array();
		unset($_POST['ulinks_table_length']);
		
		foreach($_POST as $Key => $Value)
		{
			$Value=trim($Value);
			$Key=explode('_',$Key);
			$iswas=false;
			$isnew=false;
			if(count($Key)!=2)
			{
				if(count($Key)==3 && $Key[0]=='was')
					$iswas=true;
				elseif(count($Key)==3 && $Key[0]=='new')
					$isnew=true;
				else
					continue;
			}
			$Param=$Key[0+($iswas||$isnew)];
			if($Value!=='' && ($Param==='aURL'||$Param==='Don'))
			{
				$Value=HTracer::FixURL($Value, !$GLOBAL['htracer_ulink_other_domains']);
				if($Param==='Don' && $Value==='y')
					$Value='Y';
				elseif($Param==='Don' && $Value==='g')
					$Value='G';
			}
			$ID=$Key[1+($iswas||$isnew)];
			$cKeys=&$Keys;
			if($iswas)
				$cKeys=&$WasKeys;
			elseif($isnew)
				$cKeys=&$NewKeys;	
			$cKeys[$ID][$Param]=$Value;
		}
		$ChangedData=Array();
		$URLs=Array();
		$Deleted=Array();
		foreach($Keys as $ID => $Data)
		{
			if($Data['Key']==='__deleted__')
				$Deleted[]=$ID;
			else
			{
				foreach($Data as $Param => $Value)
				{
					if($WasKeys[$ID][$Param]==$Value)
						continue;
					if(!isset($ChangedData[$ID]))
						$ChangedData[$ID]=Array();
					$ChangedData[$ID][$Param]=$Value;
				}
			}
		}
		

		
		HTracer::UpdateTableData('htracer_ulinks',$ChangedData);
		$KeysCount=HTracer::AddUserKeys($NewKeys);
		
		$Values=Array();
		//print_r($NewKeys);
		foreach($NewKeys as $Key)
		{
			if($Key['aURL']===''||$Key['Key']===''||$Key['Key']==='__deleted__')
				continue;
			$aURL=mysql_real_escape_string($Key['aURL']);
			$Don=mysql_real_escape_string($Key['Don']);
			$aKey=mysql_real_escape_string($Key['Key']);
			$DON_CS=md5($Key['Don']);
			$Values[]="('{$aURL}','{$aKey}','{$Don}','{$DON_CS}')";
		}
		$KeysCount=count($Values);
		$Values=JOIN(',',$Values);
		$TableName=HTracer::GetTablePrefix().'htracer_ulinks';
		if($KeysCount)
		{
			htracer_mysql_query("
				INSERT INTO `$TableName` (`aURL`, `Key`, `Don`, `DON_CS`)
				VALUES $Values
			");
		}
		if(count($Deleted))
			htracer_mysql_query("DELETE FROM `$TableName` WHERE `ID` In (".join(',',$Deleted).")");
		//Теперь возвращаем новые строки в конец
		if(isset($_POST['ajax']) && $_POST['ajax'])
		{
			$_GET['iDisplayStart']=0;
			$_GET['iDisplayLength']=$KeysCount;
			$_GET['idsort']=1;
			include('ajax/ulinks.php');
			exit();
		}
	}
	
	function eval_config_file($FileName,$return=false)
	{
		if(!file_exists($FileName) 
		|| !function_exists('file_get_contents'))
			return false;

		$content=@file_get_contents($FileName);
		if(!$content)
			return false;	
	
		$content=str_replace(
			array("\rinclude","\ninclude","\rrequire","\nrequire","\tinclude","\trequire",
				 "\rini_set(","\nini_set(","\tini_set(","\r@ini_set(","\n@ini_set(","\t@ini_set(",
				 "\rexit","\nexit","\texit","\rdie(","\ndie(","\tdie(")
		,'//',$content);
		$content=str_replace(array('exit()','die()','exit;'),';',$content);
		$content=str_replace(array('<? ',"<?\n","<?\t","<?\r"),"<?php\n",$content);
		
		$content=trim($content);
		$len=strlen($content);
		if($content[$len-2]=='?' && $content[$len-1]=='>')
		{
			$content[$len-2]=' ';
			$content[$len-1]=' ';
		}
		if($return)
			$content.=" \n return $return;";
		ob_start();
		$res=@eval('?>'.$content);
		$buf = ob_get_clean();
		if($buf)
			return false;
		
		if($res===false||($return && $res===NULL))
			return false;
		if($return)
			return $res;
		else
			return true;
	}
	function auto_detect_mysql_config()
	{//Пытаемся найти файлы файлы конфигурации популярных движков
		$Res=Array();
		$Res['engine']=false;
		$Res['prefix']='';
		$Res['set_names']=false;
	
		$ModX='../../core/config/config.inc.php';		
		$Bitrix='../../bitrix/php_interface/dbconn.php';		
		$NetCat='../../vars.inc.php';		
		$WP='../../wp-config.php';		
		$Joomla='../../configuration.php';
		$DLE='../../engine/data/dbconfig.php';	
		$Drupal='../../sites/default/settings.php';		
		try{
			if($arr=eval_config_file($ModX, 'Array($database_server,$database_user,$database_password,$dbase)'))
			{
				if(is_array($arr) && isset($arr[0]) && isset($arr[1]) && isset($arr[2]) && isset($arr[3])
				&& $arr[0]!==NULL && $arr[1]!==NULL && $arr[2]!==NULL && $arr[3]!==NULL
				&& $arr[0]!==false && $arr[1]!==false && $arr[2]!==false && $arr[3]!==false)
				{
					$Res['engine']='ModX';
					$Res['host']=$arr[0];
					$Res['user']=$arr[1];
					$Res['pass']=$arr[2];
					$Res['db']=$arr[3];
				}
			}
			elseif($arr=eval_config_file($Bitrix, 'Array($DBHost,$DBLogin,$DBPassword,$DBName)'))
			{
				if(is_array($arr) && isset($arr[0]) && isset($arr[1]) && isset($arr[2]) && isset($arr[3])
				&& $arr[0]!==NULL && $arr[1]!==NULL && $arr[2]!==NULL && $arr[3]!==NULL
				&& $arr[0]!==false && $arr[1]!==false && $arr[2]!==false && $arr[3]!==false)
				{
					$Res['engine']='Bitrix';
					$Res['host']=$arr[0];
					$Res['user']=$arr[1];
					$Res['pass']=$arr[2];
					$Res['db']=$arr[3];
				}
			}
			elseif($arr=eval_config_file($NetCat, 'Array($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD,$MYSQL_DB_NAME)'))
			{
				if(is_array($arr) && isset($arr[0]) && isset($arr[1]) && isset($arr[2]) && isset($arr[3])
				&& $arr[0]!==NULL && $arr[1]!==NULL && $arr[2]!==NULL && $arr[3]!==NULL
				&& $arr[0]!==false && $arr[1]!==false && $arr[2]!==false && $arr[3]!==false)
				{
					$Res['engine']='NetCat';
					$Res['host']=$arr[0];
					$Res['user']=$arr[1];
					$Res['pass']=$arr[2];
					$Res['db']=$arr[3];
				}
			}
			elseif($arr=eval_config_file($Drupal,'$databases'))
			{
				if(is_array($arr) && isset($arr['default']) && isset($arr['default']['default']) && is_array($arr['default']['default']))
				{
					$Data=$arr['default']['default'];
					if(isset($Data['database']) && isset($Data['username'])
					&& isset($Data['password']) && isset($Data['host']))
					{
						$Res['engine']='Drupal';
						$Res['db']=$Data['database'];
						$Res['user']=$Data['username'];
						$Res['pass']=$Data['password'];
						$Res['host']=$Data['host'];
						if(isset($Data['prefix']))
							$Res['prefix']=$Data['prefix'];
					}
				}
			}
			elseif(eval_config_file($DLE) && defined('DBHOST') && defined('DBNAME') && defined('DBUSER') && defined('DBPASS'))
			{
				$Res['engine']='DLE';
				$Res['db']=DBNAME;
				$Res['user']=DBUSER;
				$Res['pass']=DBPASS;
				$Res['host']=DBHOST;
				if(defined('PREFIX') && PREFIX)
					$Res['prefix']=PREFIX.'_';
			}
			elseif(eval_config_file($Joomla) && class_exists('JConfig'))
			{
				$Res['engine']='Joomla';
				$Config=new JConfig();
				$Res['db']=$Config->db;
				$Res['user']=$Config->user;
				$Res['pass']=$Config->password;
				$Res['host']=$Config->host;
				$Res['prefix']=$Config->dbprefix;
			}
			elseif(eval_config_file($WP) && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASSWORD') && defined('DB_HOST'))
			{
				$Res['engine']='WordPress';
				$Res['db']=DB_NAME;
				$Res['user']=DB_USER;
				$Res['pass']=DB_PASSWORD;
				$Res['host']=DB_HOST;
			}
		} catch (Exception $e) {}
		return $Res;
	}
	function htracer_admin_check_pwd($pwd=false)
	{
		if(!$GLOBALS['htracer_admin_pass']||$GLOBALS['htracer_admin_pass']===ht_pwd_crc('')
		//||$GLOBALS['htracer_is_demo']
		)
			return true;
		//echo $GLOBALS['htracer_admin_pass'].'='.ht_pwd_crc('');
		
		if($pwd==false && $_COOKIE["cookie_htracer_admin_pass"])
			return htracer_admin_check_pwd($_COOKIE["cookie_htracer_admin_pass"]);
		if(strtolower(trim($pwd))==strtolower(trim($GLOBALS['htracer_admin_pass'])))	
			return true;
		if($pwd==ht_pwd_crc($GLOBALS['htracer_admin_pass']))
			return true;
		if($GLOBALS['htracer_admin_pass']==ht_pwd_crc($pwd))
			return true;
		if(ht_pwd_crc($pwd)==ht_pwd_crc($GLOBALS['htracer_admin_pass']))
			return true;
		if($_COOKIE["cookie_htracer_admin_pass"] && $_COOKIE["cookie_htracer_admin_pass"]!=$pwd)
			return htracer_admin_check_pwd($_COOKIE["cookie_htracer_admin_pass"]);
		return false;
	}
	function htracer_out_menu($is_footer=false)
	{
		?>
			<?php if($GLOBALS['ht_admin_page']=='pages'):?>
					<span><?php echo ht_trans('Страницы');?> </span>
			<?php else:?>
					<a href="pages.php" title="<?php echo ht_trans('Работа с ключевыми словами и настройками страницы');?>"><?php echo ht_trans('Страницы');?></a>
			<?php endif;?>
			
			<?php if($GLOBALS['ht_admin_page']=='keys'):?>
					<span><?php echo ht_trans('Ключи');?></span>
			<?php else:?>
					<a href="keys.php" title="<?php echo ht_trans('Редактирование ключевиков');?>"><?php echo ht_trans('Ключи');?></a>
			<?php endif;?>
						
			<?php if($GLOBALS['ht_admin_page']=='options'):?>
					<span><?php echo ht_trans('Настройки');?></span>
			<?php else:?>
					<a href="options.php"><?php echo ht_trans('Настройки');?></a>
			<?php endif;?>
		<?php 					
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
	function htracer_admin_header($Title)
	{
		if($Title)
			$Title=ht_trans($Title);
		if(isset($GLOBALS['was_htracer_admin_header']))
			return;
		$GLOBALS['was_htracer_admin_header']=true;
		if($Title===false)
			error_reporting(E_PARSE|E_ERROR);
		global $user_level, $user_ID; 
		if(!isset($GLOBALS['htracer_admin_page'])||$GLOBALS['htracer_admin_page']===false)
			$GLOBALS['htracer_admin_page']=true;
		$Aut_Error=false;
		if(htracer_admin_is_wp())
		{
			if(!$user_ID && $_SERVER['REQUEST_URI']!='/wp-admin/options-general.php?page=HTracer')
			{
				if(isset($_GET['wp_akey'])||isset($_COOKIE['wp_akey']))
				{
					if(isset($_GET['wp_akey']))
						$akey=$_GET['wp_akey'];
					else
						$akey=$_COOKIE['wp_akey'];
					if(file_exists('../../../../wp-config.php'))
						$table_prefix=eval_config_file('../../../../wp-config.php',' $table_prefix ');
					else
						$table_prefix=eval_config_file('../../../../../wp-config.php',' $table_prefix ');
					
					$GLOBALS['htracer_mysql']=true;
					
					if(defined('DB_USER'))
						$GLOBALS['htracer_mysql_login']=DB_USER;
					if(defined('DB_PASSWORD'))
						$GLOBALS['htracer_mysql_pass']=DB_PASSWORD;
					if(defined('DB_NAME'))
						$GLOBALS['htracer_mysql_dbname']=DB_NAME;
					if(defined('DB_HOST'))
						$GLOBALS['htracer_mysql_host']=DB_HOST;
						
					$GLOBALS['htracer_mysql_prefix']=$table_prefix;
								
					if($akey!==ht_calc_wp_key())
						$Aut_Error=('E1: Access denied. Use <a href="/wp-admin/options-general.php?page=HTracer">this link</a>');
					elseif(isset($_GET['wp_akey']))
						setcookie("wp_akey", $akey, time()+ 7*24*3600);
				}
				else
					$Aut_Error=('E2: Access denied. Use <a href="/wp-admin/options-general.php?page=HTracer">this link</a>');
			}
			elseif($user_level && $user_level<6)
				$Aut_Error=('E3: Access denied. You access level is less then 6. Please login as admin');
			if($Aut_Error)
				die($Aut_Error);
			if(function_exists('get_bloginfo'))
			{
				echo "<style>	#tabs div ol{list-style:decimal outside none;}</style>";	
				HTracer_In();
				$Aut_Error=true;
			}
		}
		if(!$Aut_Error)
		{
			//if($Title!==false)
				header("Content-type: text/html;charset=UTF-8");
//			echo dirname(dirname(__FILE__)).'/HTracer.php';
			$dn=dirname(dirname(__FILE__));
			
			if(!file_exists($dn.'/HTracer.php') && file_exists($dn.'/htracer.php'))
				die ("<b style='color:red'>File 'HTracer.php' not exist, but 'htracer.php' is exist.<br />You FTP client upload files in a wrong case.<br />Reupload files in other FTP client.</b>");
				
			include_once($dn.'/HTracer.php');
			if(isset($GLOBALS['htracer_admin_pass']) && $GLOBALS['htracer_admin_pass'] 
			&& $GLOBALS['htracer_admin_pass']!='******')
			{
				HTracer_In();
				$UserPass=false;
				if(isset($_POST['htracer_admin_login_pass']))
				{
					$UserPass=$_POST['htracer_admin_login_pass'];
					setcookie('cookie_htracer_admin_pass',ht_pwd_crc($_POST['htracer_admin_login_pass']),time()+365 *24 * 3600);
				}
				if(!htracer_admin_check_pwd($UserPass))
				{
				?>
					<html>
						<head>
							<meta name="robots" content="noindex" />
							<style>
								body {padding:50px}
							</style>
						</head>
						<body>
							<form method='post'>
								<b><?php echo ht_trans('Введите пароль к админке HTracer');?></b><br />
								<input name='htracer_admin_login_pass' /><br />
								<input type='submit' value='OK' /><br /><br /><br />
								<small>
									<?php echo ht_trans('Если вы забыли пароль, то откройте файл admin/auto_config.php');?><br /> 
									<?php echo ht_trans('Найдите строку вроде GLOBALS["htracer_admin_pass"]="e3d3d25dcfe5f7043869f311714ce216"; и замените ее на GLOBALS["htracer_admin_pass"]="";');?> 
									<br /><br />
									<?php echo ht_trans('Если вы видите это сообщение при любом переходе на страницу админки -- включите coockies в вашем браузере');?>. 
								</small>	
								<br />
							</form>
						</body>
					</html>
				<?php
					exit();
				}
				if(isset($_POST['htracer_admin_pass']) && $_POST['htracer_admin_pass'] && $_POST['htracer_admin_pass']!='******')
					setcookie('cookie_htracer_admin_pass',ht_pwd_crc($_POST['htracer_admin_pass']),time()+365 *24 * 3600);

			}
			//Автоопределение сет неймес MySQL
			if($GLOBALS['htracer_mysql'] && $GLOBALS['htracer_mysql_set_names']=='auto'
			&& !$GLOBALS['ht_is_this_admin_options'])
			{
				$pages=HTracer::SelectMaxPages(10);
				$pages=HTracer::SelectMaxQueries($pages,false,false,false);
				$All=0;
				$Collapsed=0;
				$CP1251=0;
				
				foreach($pages as $key => $cpage)
				{
					foreach($cpage['Q'] as $q => $num)
					{
						$All++;
						if((strpos($q,'??')!==false||strpos($q,'? ?')!==false)
						 && strpos($q,'а')===false && strpos($q,'А')===false
						 && strpos($q,'о')===false && strpos($q,'О')===false
						 && strpos($q,'е')===false && strpos($q,'Е')===false
						 && strpos($q,'и')===false && strpos($q,'И')===false)
							$Collapsed++;
						if(strpos(mb_detect_encoding($q,Array('utf8','cp1251')),'1251')!==false)
							$CP1251++;
						if($All>100)
							break;
					}
				}
				if($CP1251>$All/2)
					mysql_query("SET NAMES 'utf8'") or die ('_SET NAMES :'.mysql_error());
				elseif($Collapsed>$All/2)
					mysql_query("SET NAMES 'cp1251'") or die ('_SET NAMES :'.mysql_error());
			}
			if($Title===false)
				return;
			?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">			
<html dir="ltr" lang="ru-RU">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta charset="UTF-8" />
		<title><?php echo $Title; ?></title>
			<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css" type="text/css" media="all" />
			<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" />
			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
			<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js" type="text/javascript"></script>
			<script src="http://jquery-ui.googlecode.com/svn/tags/latest/external/jquery.bgiframe-2.1.2.js" type="text/javascript"></script>
			<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/i18n/jquery-ui-i18n.min.js" type="text/javascript"></script>
			<script src="http://malsup.github.com/jquery.form.js" type="text/javascript"></script>
			<script src="http://datatables.net/release-datatables/media/js/jquery.dataTables.js" type="text/javascript"></script>
			<link rel='stylesheet' href='style.css' type='text/css' />
			<meta name="robots" content="noindex" />
			<script type="text/javascript">
				var single_page_url=false;
				var AJAX_PASS	= '<?php echo $GLOBALS['htracer_ajax_pass'];?>';
				<?php if($_SERVER['SERVER_NAME']=='htest.ru'||$_SERVER['SERVER_NAME']=='projects'):?>
					var Domain 			 = 'visit.odessa.ua';
				<?php else:?>
					var Domain 			 = '<?php echo $_SERVER['SERVER_NAME'];?>';
				<?php endif;?>
				function SetTableValuesAS(evt,cb)
				{
					if (!evt) evt=window.evt; 
					var target = evt.target;
					if(!target) target=evt.srcElement;
					if(!cb) cb=false;
					var to = $(target).attr('to')
					var val=false;
					if(cb)
						val=!!$("#"+to+"_to_all").attr("checked");
					else
						val=$("#"+to+"_to_all").attr("value");
					if(cb)
						$("."+to+"_td input[type='checkbox']").attr("checked",val);
					else
						$("."+to+"_td input[type!='hidden']").val(val);	
					
					$("."+to+"_td input").trigger('change');
				}
			</script>
			<?php if(!ht_trans_is_ru()):?>
				<style>
					table thead th.eva_th,
					table thead th.peva_th
					{
						font-size:70%;
						padding-left: 3px;
						width: 57px;
						padding-right: 0;
					}
				</style>
			<?php endif;//(!ht_trans_is_ru()):?>
	</head>
	<body class='<?php echo $GLOBALS['ht_admin_page']; ?>_admin_page'>

<!--[if lt IE 9]>
<style>
	#logolinks
	{
		line-height:normal;
		padding-left: 20px;
	}
	#logolinks a, #logolinks span
	{
		padding-left: 30px;
	}
</style>
<![endif]-->
		<div id="outerframe">
			<div id="innerframe">			 	   
				<div id="logodiv" style="sborder-bottom: 2px solid rgb(206,239,214);">
					<table border='0' cellpadding='0' cellspacing='0' style='border:0;margin:0;padding:0'><tr>
					<td style='sborder: 1px green solid'>
						<a 
							<?php if(ht_trans_is_ru()):?>		
								href='http://htracer.ru/' 
							<?php else:?>	
								href='http://htracer.com/' 
							<?php endif;?>
							title='<?php echo ht_trans('Перейти на сайт программы');?>' style="display: block;">
							<img src="images/slogo.png" alt="HTracer" style="display: block;" />
						</a>
					</td><td valign='bottom' style='sborder: 1px green solid'>
					<div id="logolinks">
							<?php htracer_out_menu();?>
					</div>
					</td></tr></table>
				</div><hr style="margin:0;padding:0;margin-bottom:20px;margin-top:0px; padding:0; z-index:-1; position:relative; top:-1px" />
				
<script type="text/javascript">
<?php if($GLOBALS['ht_admin_page']=='options'):?>
	var isOptionsPage=true;
<?php else:?>
	var isOptionsPage=false;
<?php endif;?>
</script>
<script src="script.js" type="text/javascript"></script>

<div id='outer-dialog' style='display:none'>
	<div id='dialog'>
		dialog
	</div>
</div>

			
			<?php
		}
	}
	function ShowSetTableValuesForm($To,$Cb,$Def=0)
	{
		$Cb=$Cb+0;
	?>
		<hr style="width:auto; left: 0px; margin: 25px 0 25px;" /> 
		<input id="<?php echo $To;?>_to_all" <?php 
			if($Cb) 
			{
				echo 'type="checkbox"'; 
				if($Def) 
					echo 'checked="checked"';
			}
			else 
			{
				echo 'size="3"'; 
				if($Def) 
					echo 'value="'.$Def.'"';
			}
		?> />
		<small><?php echo ht_trans('задать это значение для отображаемых ключей');?></small>
		<br />
		<input type="button" style="margin-top:5px" value="<?php echo ht_trans('Задать');?>" onclick="SetTableValuesAS(event,<?php echo $Cb;?>)" to="<?php echo $To;?>" />
		
<?php
	}
	function htracer_admin_footer()
	{
		if(HTracer::IsNeedToConvertTables())
			HTracer::ConvertTables();
		if(!htracer_admin_is_wp() ||(isset($_GET['wp_akey'])||isset($_COOKIE['wp_akey'])))
		{
		?>
				</div>	
			</div>	
			<div id='footer'>
				<!--[if lt IE 9]>
					<br /><br />
					<?php echo ht_trans('Вы используете старую версию Internet Explorer. Рекомендуется обновится до 9ой, либо использовать другой браузер.');?>
					<br /><br />
				<![endif]-->
				<?php if(strpos($_SERVER['REQUEST_URI'],'wp-content/plugins/HTracer')): ?>	
					<a href='../../../../' style="margin-left:0px;text-decoration:none;"><small>&lt;&lt; <?php echo ht_trans('Вернуться на сайт');?></small></a>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<a href='../../../../wp-admin/' style="margin-left:0px;text-decoration:none;"><small><?php echo ht_trans('Вернуться в админку WP');?></small></a>
				<?php else: ?>	
					<a href='../../' style="margin-left:0px;text-decoration:none;"><small>&lt;&lt; <?php echo ht_trans('Вернуться на сайт');?></small></a>
				<?php endif; ?>	
				<div> </div>
				<center id='footer-links'>
					<?php htracer_out_menu(true);?>
					<a href='../readme.html'>Readme</a>
				</center>
			</div>	
		</body>
	</html>
	<?php		
		}
		HTracer_Out();	
	}
	
	function HT_GetOptionPHP($Name,$Default)
	{	
		$Value=$Default;
		if(isset($GLOBALS[$Name]))
			$Value=$GLOBALS[$Name];
		//echo "$Name == {$GLOBALS[$Name]} // $Value <br />";
			
		if($Value===false)
			$Value='false';
		elseif($Value===true)
			$Value='true';
		elseif(!is_numeric($Value) || (is_string($Value) && isset($Value{1}) && $Value{0}=='0' && $Value{0}!==' ' && $Value{1}!='.'))
		{
			$Value=stripslashes($Value);
			$Value=addslashes($Value);
			$Value=str_replace('\$','$',$Value);
			$Value=str_replace('$','\$',$Value);
			$Value='"'.$Value.'"';
		}
		return "
		if(!isset(\$GLOBALS['$Name']))
			\$GLOBALS['$Name']=$Value;";
	}
	function HT_OutCheckBox($Name)
	{
		if($GLOBALS[$Name])	
			echo "<input type='checkbox' name='$Name' id='$Name' value='1' checked='checked' />";
		else
			echo "<input type='checkbox' name='$Name' id='$Name' value='1' />";
	}
	function HT_OutSelect($Name,$Values)
	{
		$is_assoc=false;
		$i=0;
		foreach($Values as $Key => $Val)
		{
			if($Key!==$i)
				$is_assoc=true;
			$i++;
		}
		$pr='<br />';

		//echo "<br />is_assoc=$is_assoc<br />";

		echo "<select name='$Name' id='$Name'>";
			foreach($Values as $Key => $Val)
			{
				$pr.="$Key => $Val// ";
				if(!$is_assoc)
				{
					$Key=$Val;
					if(mb_strtolower($Val,'utf-8')==='нет'||$Val==='нет'||$Val==='Нет')
						$Key='0';
					elseif(mb_strtolower($Val,'utf-8')==='да'||$Val==='да'||$Val==='Да')
						$Key='1';
					elseif(strtolower($Val)==='no')
						$Key='0';
					elseif(strtolower($Val)==='yes')
						$Key='1';
				}
				else
				{
					if($Key==='ht_true')
						$Key='1';
					if($Key==='ht_false')
						$Key='0';
				}
				
				if($GLOBALS[$Name]===$Key||
				(($GLOBALS[$Name]===false||$GLOBALS[$Name]===0||$GLOBALS[$Name]==='0')&&
				($Key===false||$Key===0||$Key==='0'))||
				(($GLOBALS[$Name]===true||$GLOBALS[$Name]===1||$GLOBALS[$Name]==='1')&&
				($Key===true||$Key===1||$Key==='1'))||
				strtolower($GLOBALS[$Name])===strtolower($Key))
				{	
					$pr.="$Name:: $Key == {$GLOBALS[$Name]} SELECTED<br />";
					echo "<option value='$Key' selected='selected'>$Val</option>";
				}
				else	
				{
					$pr.="$Name:: $Key == {$GLOBALS[$Name]} NOT_SELECTED<br />";
					echo "<option value='$Key'>$Val</option>";
				}
			}
		echo "</select>";
		//echo $pr;
	}
	function HT_OutPwdInput($Name)
	{
		// Имена экранируем, от RegisterGlobals
		if((!$GLOBALS[$Name] && $GLOBALS[$Name]!=='0' && $GLOBALS[$Name]!==0)
		|| $GLOBALS[$Name]===ht_pwd_crc(''))
			echo "<input id='$Name' type='text' name='{$Name}_htwdinput' value='' />";
		else
			echo "<input id='$Name' type='text' name='{$Name}_htwdinput' value='******' />";
	}
	function HT_OutTextInput($Name)
	{
		echo "<input name='$Name' type='text' id='$Name' value='{$GLOBALS[$Name]}' />";
	}
	function HT_OutTextArea($Name)
	{
		echo "<textarea name='$Name' type='text' id='$Name' spellcheck='false'>{$GLOBALS[$Name]}</textarea>";
	}
	
	$FirstWordOS_0=Array(
		'одесса,киев,львов,луганск,донецк,харьков,днепропетровск,херсон'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('украин','Украин'),//слова которых не должно быть
				'Addon' => Array('украина,'),
				'Place' => 'Pre'
			),
		'симферополь,севастополь,форос,феодосия,ялка,алупка,алушта'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('украин','росс','Украин'),//слова которых не должно быть
				'Addon' => Array('украина,','крым,','Украина, Крым,'),
				'Place' => 'Pre'
			),
		'рестораны,гостиницы,отели,клубы'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('лучш','дешев','дешёв','дорог'),//слова которых не должно быть
				'Addon' => Array('лучшие','самые лучшие'),
				'Place' => 'Pre'
			),
		'квартиры'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('1','2','3','4','5','6','7','одно','двух','трех','трёх','четыр'),//слова которых не должно быть
				'Addon' => Array('1-комнатные','однокомнатные','2-комнатные','двухомнатные','3-комнатные','трехомнатные'),
				'Place' => 'Pre'
			),
		'квартиры'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('цен','стоим'),//слова которых не должно быть
				'Addon' => Array('цены'),
				'Place' => 'Pre'
			),
		'москва,владивосток,суздаль,спб,тюмень,иркутск,новгород,екатеренбург,тверь,калуга'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('рос','РФ','Рос','рф'),//слова которых не должно быть
				'Addon' => Array('Россия,','РФ,'),
				'Place' => 'Pre'
			),
		'достопримечательности,достопремечательности'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array(' главн',' основн',' сам','наиболее',' известн'),//слова которых не должно быть
				'Addon' => Array('главные','основные'),
				'Place' => 'Pre'
			),
		'одесса,москва,донецк,луганск,харьков,днепропетровск,стамбул,каир,александрия,владивосток,адлер,алупка,алушта,ростов,владивосток,париж,сочи,ялта,севастополь,симферополь,спб,воронеж,казань,киев,львов,теронополь,луганск'=>
			Array(
				'Find'	=> Array('достопримечательности','клубы','достопримечательности','музеи','гостиницы','отели','музеи','карта'),//слова которые должны быть
				'Ex'	=> Array('город'),//слова которых не должно быть
				'Addon' => Array('города'),
				'Place' => 'Post'
			),
		'карта'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('онлайн','скачать','купит','бума','офлайн','он лайн','online'),//слова которых не должно быть
				'Addon' => Array('онлайн'),
				'Place' => 'Pre'
			),
		'гостиницы,отели'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('дешев','дёшев','недорог','цен','стоим'),//слова которых не должно быть
				'Addon' => Array('дешевые','недорогие'),
				'Place' => 'Pre'
			),
		'ноутбуки,телевизоры,нетбуки,компьютеры,пластиковые,стиральные,швейные,посудомоечные,холодильники,телефоны'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('ремонт','настройк','прод','покуп','дешев','дёшев','недорог','цен','стоим','купит','скачать','драйв','купл'),//слова которых не должно быть
				'Addon' => Array('дешевые','недорогие','купить','купить недорого'),
				'Place' => 'Pre'
			),
		'ноутбук,телевизор,нетбук,компьютер,холодильник,телефон'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('ремонт','бесплатн','настройк','прод','покуп','дешев','дёшев','недорог','цен','стоим','купит','скачать','драйв','купл'),//слова которых не должно быть
				'Addon' => Array('купить','купить недорого'),
				'Place' => 'Pre'
			),
		'купить,снять'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('недорог','дешев','дешёв','цен','стоим'),//слова которых не должно быть
				'Addon' => Array('недорого'),
				'Place' => 'Pre'
			),
		'купить'=>
			Array(
				'Find'	=> Array('ноутбук','телевизор','нетбук','компьютер','холодильник','телефон','машин',' авто'),//слова которые должны быть
				'Ex'	=> Array('недорог','дешев','дешёв','цен','стоим','опт','розн','расср','росср','раср','роср','кред'),//слова которых не должно быть
				'Addon' => Array('оптом','в розницу','по низким ценам','по оптовым ценам','в рассрочку','в кредит','недорого','дешево'),
				'Place' => 'Post'
			),
		'скачать'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('бесплатн','недорог','дешев','дешёв','цен','стоим'),//слова которых не должно быть
				'Addon' => Array('бесплатно'),
				'Place' => 'Pre'
			),
		'сайт'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('веб','web','интернет','Web','вэб'),//слова которых не должно быть
				'Addon' => Array('веб','web','интернет'),
				'Place' => 'Pre'
			),	
		'поезд'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('движ','приб','отбыт','росп','расп'),
				'Addon' => Array('расписание','расписание движения'),
				'Place' => 'Post'
			),
		'кинотеатр'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('движ','приб','отбыт','росп','расп','сеан','фильм','филм','афиш'),
				'Addon' => Array('расписание','расписание сеансов','расписание фильмов','афиша'),
				'Place' => 'Post'
			),	
		'театр'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('движ','приб','отбыт','росп','расп','сеан','фильм','филм','кино','афиш','спект'),
				'Addon' => Array('расписание','расписание сеансов','расписание фильмов','афиша'),
				'Place' => 'Post'
			),	
		'аренда,снять'=>
			Array(
				'Find'	=> Array('квартир','комнат',' дом'),//слова которые должны быть
				'Ex'	=> Array('месяц','сутк','сроч','длит','долго','меся'),//слова которых не должно быть
				'Addon' => Array('посуточно','длительно','долгосрочно','без посредников'),
				'Place' => 'Post'
			),
		'квартиру,квартира,квартир,квартиры,комнат,комнаты,комнат,комнату,дом,дома,домов'=>
			Array(
				'Find'	=> Array('аренда','снять'),//слова которые должны быть
				'Ex'	=> Array('месяц','сутк','сроч','длит','долго','меся'),//слова которых не должно быть
				'Addon' => Array('посуточно','длительно','долгосрочно','без посредников'),
				'Place' => 'Post'
			),
		'отели'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('гостин'),
				'Addon' => Array('гостиницы и'),
				'Place' => 'Pre'
			),	
		'гостиницы'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('отел'),
				'Addon' => Array('отели и'),
				'Place' => 'Pre'
			),	
		'санатории'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('пансион'),
				'Addon' => Array('пансионаты и'),
				'Place' => 'Pre'
			),	
		'пансионаты'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('санато'),
				'Addon' => Array('санатории и'),
				'Place' => 'Pre'
			),		
		'кафе'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('рестор'),
				'Addon' => Array('рестораны и'),
				'Place' => 'Pre'
			),	
		'рестораны'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('каф'),
				'Addon' => Array('кафе и'),
				'Place' => 'Pre'
			),		
			
	);
	$FirstWordOS=Array();
	foreach ($FirstWordOS_0 as $Keys => $Data)
	{
		$Keys=explode(',',$Keys);
		foreach($Keys as $Key)
		{	
			$Key=trim($Key);
			if($Key)
				$FirstWordOS[$Key][]=$Data;
		}
	}
	$LastWordOS_0=Array(
		'аренда,снять'=>
			Array(
				'Find'	=> Array('квартир','комнат',' дом'),//слова которые должны быть
				'Ex'	=> Array('месяц','сутк','сроч','длит','долго','меся'),//слова которых не должно быть
				'Addon' => Array('посуточно','длительно','долгосрочно','без посредников'),
				'Place' => 'Post'
			),
		'квартиру,квартира,квартир,квартиры,комнат,комнаты,комнат,комнату,дом,дома,домов'=>
			Array(
				'Find'	=> Array('аренда','снять'),//слова которые должны быть
				'Ex'	=> Array('месяц','сутк','сроч','длит','долго','меся'),//слова которых не должно быть
				'Addon' => Array('посуточно','длительно','долгосрочно','без посредников'),
				'Place' => 'Post'
			),
		'одесса,киев,львов,луганск,донецк,харьков,днепропетровск,херсон'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('украин','росс'),//слова которых не должно быть
				'Addon' => Array(', Украина'),
				'Place' => 'Post'
			),
		'москва,владивосток,суздаль,спб,тюмень,иркутск,новгород,екатеренбург,тверь,калуга'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('украин','росс','рф'),//слова которых не должно быть
				'Addon' => Array(', Россия', ', РФ'),
				'Place' => 'Post'
			),	
		'ноутбук,телевизор,нетбук,компьютер,холодильник,телефон,ноутбуки,телевизоры,нетбуки,компьютеры,холодильники,телефоны'=>	
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('опт','кред','расрочк','розниц'),//слова которых не должно быть
				'Addon' => Array('розница', 'опт'),
				'Place' => 'Post'
			),	
		'симферополь,севастополь,форос,феодосия,ялка,алупка,алушта'=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('украин','росс','Украин'),//слова которых не должно быть
				'Addon' => Array(', Украина',', Крым',', Крым, Украина'),
				'Place' => 'Post'
			),
		'расписание,росписание'	=>
			Array(
				'Find'	=> Array('поезд','автобус','жд','вокзал','ЖД'),//слова которые должны быть
				'Ex'	=> Array('движ','приб','отбыт'),//слова которых не должно быть
				'Addon' => Array('движения'),
				'Place' => 'Post'
			)
		,
		'расписание,росписание,'	=>
			Array(
				'Find'	=> Array('кинотеатр','синема'),//слова которые должны быть
				'Ex'	=> Array('показ','филм','фильм','сеанс'),//слова которых не должно быть
				'Addon' => Array('сеансов','фильмов'),
				'Place' => 'Post'
			),
		'расписание,росписание,,'	=>
			Array(
				'Find'	=> Array(' театр'),//слова которые должны быть
				'Ex'	=> Array('показ','филм','фильм','сеанс','спект'),//слова которых не должно быть
				'Addon' => Array('спектаклей'),
				'Place' => 'Post'
			),
		'расписание,росписание,,,'	=>
			Array(
				'Find'	=> Array(' вокзал',' жд ',' ЖД '),//слова которые должны быть
				'Ex'	=> Array('поезд','движ','приб','отпр'),//слова которых не должно быть
				'Addon' => Array('поездов','движения поездов'),
				'Place' => 'Post'
			),			
		'расписание,росписание,,,,'	=>
			Array(
				'Find'	=> Array('автовокзал'),//слова которые должны быть
				'Ex'	=> Array('поезд','движ','приб','отпр','автобус','маршр'),//слова которых не должно быть
				'Addon' => Array('автобусов','движения автобусов'),
				'Place' => 'Post'
			),			
			
		'поезд'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('движ','приб','отбыт','росп','расп'),
				'Addon' => Array('расписание','расписание движения'),
				'Place' => 'Post'
			),
		'купить'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('дорог','дешев','дёшев','цен','стоим'),
				'Addon' => Array('недорого'),
				'Place' => 'Post'
			),
		'отели'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('гостин'),
				'Addon' => Array('и гостиницы'),
				'Place' => 'Post'
			),	
		'гостиницы'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('отел'),
				'Addon' => Array('и отели'),
				'Place' => 'Post'
			),	
		'санатории'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('пансион'),
				'Addon' => Array('пансионаты и'),
				'Place' => 'Post'
			),	
		'пансионаты'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('санато'),
				'Addon' => Array('санатории и'),
				'Place' => 'Post'
			),		
		'кафе'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('рестор'),
				'Addon' => Array('рестораны и'),
				'Place' => 'Post'
			),	
		'рестораны'	=>
			Array(
				'Find'	=> Array(),//слова которые должны быть
				'Ex'	=> Array('каф'),
				'Addon' => Array('кафе и'),
				'Place' => 'Post'
			),		
			
		//купить, скачать, см. онлайн 	
	);
	$LastWordOS=Array();
	foreach ($LastWordOS_0 as $Keys => $Data)
	{
		$Keys=explode(',',$Keys);
		foreach($Keys as $Key)
		{	
			$Key=trim($Key);
			if($Key)
				$LastWordOS[$Key][]=$Data;
		}
	}
	function HT_AddOStoSapeProjectLink($Anchor,$Pre='',$Post='',$i=0)
	{//Возвращает массив возможных анкоров с околоссыочным
		global $FirstWordOS,$LastWordOS;
		$Res=Array();
		if(!$i)
			$Res[]=Array('Anchor' => $Anchor,'Pre' => $Pre, 'Post' => $Post);
			
		//return $Res; 
		$Full=" $Pre $Anchor $Post ";
		$arr=explode(' ',trim($Full));
		$First=$arr[0];
		$Last=$arr[count($arr)-1];
		
		
	//	for($i=0;$i<2;$i++)
	//	{
			$Arr=&$FirstWordOS;
			$Word=$First;
			if($i)
			{
				$Arr=&$LastWordOS;
				$Word=$Last;
			}
			//echo "$i";
			if(isset($Arr[$Word]))
			{
				$cur=$Arr[$Word];
				foreach($cur as $cur2)
				{
					if((count($cur2['Find'])==0 || str_replace($cur2['Find'],'',$Full)!=$Full)
					&& (count($cur2['Ex'])==0   || str_replace($cur2['Ex']  ,'',$Full)==$Full))
					{
						$New=Array('Anchor' => $Anchor,'Pre' => $Pre, 'Post' => $Post);
						foreach($cur2['Addon'] as $Addon)
						{
							$New2=$New;
							if($cur2['Place']=='Pre')
								$New2['Pre']=trim("$Addon {$New['Pre']}");
							else
								$New2['Post']=trim("{$New['Post']} $Addon");
							$Res[]=$New2;
						}
					}
				}
			}
	//	}
		foreach($Res as $j=>$cur)
		{	
			$Res[$j]['Anchor']=trim($Res[$j]['Anchor']);
			$Res[$j]['Pre']=trim($Res[$j]['Pre']);
			$Res[$j]['Post']=trim($Res[$j]['Post']);
			if($Res[$j]['Post'] && $Res[$j]['Post']{0}!=',')
				$Res[$j]['Post']=' '.$Res[$j]['Post'];
		}
		if(!$i)
		{
			$Res0=$Res;
			foreach($Res0 as $i=>$Cur)
			{
				$Res2=HT_AddOStoSapeProjectLink($Cur['Anchor'],$Cur['Pre'],$Cur['Post'],1);
				foreach($Res2 as $Cur2)
					$Res[]=$Cur2;
			}
		}
		return $Res; 
	}
	function htracer_admin_export_create_file()
	{
		if(!htracer_admin_is_wp())
		{
			htracer_admin_header(false);
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
			header ("Cache-Control: no-cache, must-revalidate");
			header ("Pragma: no-cache");
			header ("Content-type: application/csv");
			header ("Content-Disposition: attachment; filename=HTracerExport.csv" );
			header ("Content-Description: PHP/HTracer Generated Data" );/**/
		}
		else	
		{
			echo '<textarea onfocus="this.select()"
					spellcheck="false" wrap="off"
					style="
						white-space:nowrap; 
						margin-left: 7px; 
						width:920px; 
						height:460px; 
						overflow:scroll;
					">';
		}
		htracer_admin_export_out_data();
		if(htracer_admin_is_wp())
		{
			echo '</textarea>';
			return;
		}
		exit;
	}
	function htracer_admin_export_out_data()
	{
		echo htracer_admin_export_get_data();
	}
	function htracer_admin_export_get_data($force_utf8=false)
	{
		$res='';
		if(!isset($_REQUEST['maxkeys']))
			$_REQUEST['maxkeys']= 10;
		$pages=HTracer::SelectMaxPages(intval($_REQUEST['maxpages']));
		$pages=HTracer::SelectMaxQueries($pages,false,false,false,false);
		if($_REQUEST['export_dest']=='sape')
		{
			foreach($pages as $key => $cpage)
			{
				$pq=$cpage['Q'];
				$Summ=$cpage['N']*1;
				arsort($pq);
				$name='';
				$i=0;
				$maxnum=0;
				$PageOut=Array();
				foreach($pq as $q => $num)
				{
					if(($maxnum>10 && $num<3)||($maxnum>100 && $num<7)||($num * 200 < $maxnum))
						continue;
					$q=trim(str_replace(chr(209).chr(63),'ш',$q));
					if(!$q||strlen($q)<2)
						continue;
					if($maxnum<$num)	
						$maxnum=$num;
					$i++; 
					if($i==1||$i==0)
					{
						if(isset($_REQUEST['summ_trafic']) && $_REQUEST['summ_trafic'])
							$name='&lt;name>'.sanitarize_keyword($q)."($Summ)&lt;/name>";
						else
							$name='&lt;name>'.sanitarize_keyword($q)."&lt;/name>";
					}
					//$name='';
					if($i>intval($_REQUEST['maxkeys']))
						break;	
					$PageOut[$q]=Array('num'=>$num,'links'=>Array());
					$url='"http://'.$_SERVER['SERVER_NAME'].$key.'"';
					//$a="&lt;a href=$url>$q&lt;/a>";
					$CurAddons=HT_AddOStoSapeProjectLink($q);
					//$res.="{$name}{$a}\n";
					$k=1;
					foreach($pq as $q2 => $num2)
					{		
						$q2=trim(str_replace(chr(209).chr(63),'ш',$q2));
						$a2=str_replace($q,'|'.$q.'|',$q2);
						if($q2==$q||$a2==$q2||strpos(' '.$q2.' ',' '.$q.' ')===false)//||$num/$k < $Summ/count($pq))
							continue;
						$parts=explode('\|',$a2);
						if(count($parts)!=3)
							continue;
						$CurAddons2=HT_AddOStoSapeProjectLink($parts[1],$parts[0],$parts[2]);
						foreach($CurAddons2 as $CurAddon)
							$CurAddons[]=$CurAddon;
						$k++;
					}
					$Was=Array();
					foreach($CurAddons as $CurAddon)
					{
						$tmp=$name.trim("{$CurAddon['Pre']} &lt;a href=$url>{$CurAddon['Anchor']}&lt;/a>{$CurAddon['Post']}")."\n";
						$tmp=str_replace(' ,',',',$tmp);
						$tmp=str_replace('  ',' ',$tmp);
						$tmp=str_replace('  ',' ',$tmp);
						$tmp=str_replace('  ',' ',$tmp);
						if(!isset($Was[$tmp]))
						{
							$PageOut[$q]['links'][]=$tmp;
							$Was[$tmp]=1;
						}
					}
					//print_r($PageOut[$q]);
				}
				$MinCP=10000000;//Минимальные коефициент плюларизма
				foreach($PageOut as $q=>$Data)
				{
					$CP=count($Data['links'])/$Data['num'];
					if($MinCP>$CP)
						$MinCP=$CP;
				}
				foreach($PageOut as $q=>$Data)
				{
					$t=0;
					$maxt=1 + $MinCP * $Data['num'] * 3;//Ссылок может быть не более чем втрое больше чем минимальный коефициент плюларизма
					foreach($Data['links'] as $Link)
					{
						$res.=$Link;
						$t++;
						if($t>$maxt)
							break;
					}
				}
				$res.="\n";
			}
			return $res;
		}
		$Core=Array();
		$Summ=0;
		$Arr=Array();
		foreach($pages as $URL => $cpage)
		{
			$pq=$cpage['Q'];
			arsort($pq);
			$maxnum=0;
			foreach($pq as $q => $num)
			{
				if(($maxnum>10 && $num<3)||($maxnum>100 && $num<7)||($num * 200 < $maxnum))
					continue;
				$q=trim(str_replace(chr(209).chr(63),'ш',$q));
				$q=trim(str_replace(Array("\t","  ",';'),' ',$q));
				$q=str_replace("  ",' ',$q);
				$q=str_replace("  ",' ',$q);
				$q=str_replace("  ",' ',$q);

				if(!$q||strlen($q)<2)
					continue;
				if($maxnum<$num)	
					$maxnum=$num;
				if(!isset($Core[$URL]))
					$Core[$URL]=Array();
				$Value=pow(intval($num),0.75);
				if(!$Value)
					$Value=	intval($num);
				$Core[$URL][$q]=$Value;
				$Summ+=$Value;
				$Arr[$URL.'|*_htracer_*|'.$q]=$Value;
			}
		}
		if($Summ==0)
			$Summ=1;
		// Теперь нужно удалить все ключи с бюджетом ниже 25	
		asort($Arr);
		foreach($Arr as $Key => $Count)
		{
			
			$Budget=round(($_REQUEST['budget']/$Summ) * $Count);
			if($Budget<1 ||($Budget<25 && $_REQUEST['export_dest']!='webeffector' && $_REQUEST['export_dest']!='rookee'))
			{
				$Key=explode('|*_htracer_*|',$Key);
				unset($Core[$Key[0]][$Key[1]]);
				$Summ-=$Count;
			}
		}
		if($Summ==0)
			$Summ=1;
		$ResBudget=0;	
		foreach($Core as $URL => $Queries)
		{
			$FullURL='http://'.$_SERVER['SERVER_NAME'].$URL;
			foreach($Queries as $Query => $Count)
			{
				$Budget=round(($_REQUEST['budget']/$Summ) * $Count);
				if($Budget<1 ||($Budget<25 && $_REQUEST['export_dest']!='webeffector' && $_REQUEST['export_dest']!='rookee'))
					continue;
				$ResBudget+=$Budget;
				if($_REQUEST['export_dest']=='rookee')
				{
					$CurAddons=HT_AddOStoSapeProjectLink($Query);
					foreach($Queries as $Query2 => $Count2)
					{		
						if($Query2==$Query)
							continue;
						$a2=str_replace($Query,'|'.$Query.'|',$Query2);
						if($a2==$Query2||strpos(' '.$Query2.' ',' '.$Query.' ')===false)
							continue;
						$parts=explode('\|',$a2);
						if(count($parts)!=3)
							continue;
						$CurAddons2=HT_AddOStoSapeProjectLink($parts[1],$parts[0],$parts[2]);
						foreach($CurAddons2 as $CurAddon)
							$CurAddons[]=$CurAddon;
					}
					$AddQueries='';
					foreach($CurAddons as $CurAddon)
					{
						if(!$CurAddon['Pre']||!$CurAddon['Post'])
							continue;
						if($AddQueries)
							$AddQueries.=';';
						$CurAddQuery="{$CurAddon['Pre']} #a#{$CurAddon['Anchor']}#/a# {$CurAddon['Post']}";
						$CurAddQuery=str_replace('"','""',$CurAddQuery);
						$CurAddQuery=str_replace('#a# ','#a#',$CurAddQuery);
						$CurAddQuery=str_replace(' #/a#','#/a#',$CurAddQuery);
						$CurAddQuery=str_replace('  ',' ',$CurAddQuery);
						$CurAddQuery=str_replace('  ',' ',$CurAddQuery);
						$CurAddQuery=str_replace('  ',' ',$CurAddQuery);
						$CurAddQuery=str_replace('  ',' ',$CurAddQuery);
						$AddQueries.='"'.$CurAddQuery.'"';
					}
					if($force_utf8)
					{
						if($AddQueries)
							$AddQueries=';;;'.$AddQueries;
						$CPQuery=$Query;
					}
					else
					{
						if($AddQueries)
							$AddQueries=';;;'.mb_convert_encoding($AddQueries,'cp1251','utf-8');
						$CPQuery=mb_convert_encoding($Query,'cp1251','utf-8');
					}
					$res.= "\"$CPQuery\";\"$FullURL\";{$_REQUEST['needtop']};$Budget{$AddQueries}\n";
				}
				elseif($_REQUEST['export_dest']=='webeffector')
					$res.= "$Query	$URL	{$_REQUEST['needtop']}	$Budget\n";
				else
					$res.= "$Query	{$_REQUEST['needtop']}	$URL	$Budget\n";
			}
		}
		return $res;
	}
if (!function_exists('json_encode')) {  
    function json_encode($value) 
    {
        if (is_int($value)) {
            return (string)$value;   
        } elseif (is_string($value)) {
	        $value = str_replace(array('\\', '/', '"', "\r", "\n", "\b", "\f", "\t"), 
	                             array('\\\\', '\/', '\"', '\r', '\n', '\b', '\f', '\t'), $value);
	        $convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
	        $result = "";
	        for ($i = mb_strlen($value) - 1; $i >= 0; $i--) {
	            $mb_char = mb_substr($value, $i, 1);
	            if (mb_ereg("&#(\\d+);", mb_encode_numericentity($mb_char, $convmap, "UTF-8"), $match)) {
	                $result = sprintf("\\u%04x", $match[1]) . $result;
	            } else {
	                $result = $mb_char . $result;
	            }
	        }
	        return '"' . $result . '"';                
        } elseif (is_float($value)) {
            return str_replace(",", ".", $value);         
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            $with_keys = false;
            $n = count($value);
            for ($i = 0, reset($value); $i < $n; $i++, next($value)) 
			{
                if (key($value) !== $i) 
				{
			      $with_keys = true;
			      break;
                }
            }
        } elseif (is_object($value)) {
            $with_keys = true;
        } else {
            return '';
        }
        $result = array();
        if ($with_keys) {
            foreach ($value as $key => $v) {
                $result[] = json_encode((string)$key) . ':' . json_encode($v);    
            }
            return '{' . implode(',', $result) . '}';                
        } else {
            foreach ($value as $key => $v) {
                $result[] = json_encode($v);    
            }
            return '[' . implode(',', $result) . ']';
        }
    } 
}
?>