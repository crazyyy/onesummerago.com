<?php 
	$GLOBALS['ht_admin_page']='options';
	$GLOBALS['ht_is_this_admin_options']=true;
	include_once('functions.php');
	htracer_admin_header('Настройки HTracer');
	$ht_options_array=$GLOBALS['ht_options_array'];
	
	//echo '<pre>'; print_r(auto_detect_mysql_config()); echo '</pre>';
	
	$is_first_run=false;
	if(!$GLOBALS['htracer_is_demo'])
		$ConfigPath=str_replace('new_admin','admin',dirname(__FILE__).'/auto_config.php');
	else
	{
		//echo 'htracer_mysql_prefix='.$GLOBALS['htracer_mysql_prefix'];	
		$ConfigPath=str_replace('new_admin','admin',dirname(__FILE__).'/auto_config'.$GLOBALS['htracer_mysql_prefix'].'.php');
	}	
	error_reporting(E_ALL);
?>
<script>
	var is_first_run=false;
	var allow_autodetect=false;
	var encoding_auto_str='<?php echo ht_trans('Кодировка вашего сайта определена по главной странице и равна')?>';
	var mysql_auto_str='<?php echo ht_trans('Движок вашего сайта определен как `%engine%`, из его конфигурации были загружены доступы к MySQL')?>. <br /><br />';
	var ShowOptions_str='<?php echo ht_trans('Показать настройки')?>';
	var HideOptions_str='<?php echo ht_trans('Скрыть настройки')?>';
	var SpeedTestNoCash_str='<?php echo ht_trans('При эксперименте не использовалось кеширование')?>';
	
	var speed_test_prc_str='<?php echo ht_trans('HTracer занял %ftime% из %time% сек.')?>';
	var checking_str='<?php echo ht_trans('Проверка...')?>';
	var check_str_tr='<?php echo ht_trans('Проверить')?>';
	var save_str_tr='<?php echo ht_trans('Сохранить')?>';
	var saving_str='<?php echo ht_trans('Сохранение...')?>';
	var saved_str='<?php echo ht_trans('Сохранено')?>';
	
	var is_ru_lang= false;
	<?php if(ht_trans_is_ru()):?>
		is_ru_lang= true;
	<?php endif;?>	
</script>
<?php if(!ht_trans_is_ru()):?>
	<style>#test_query_res{display:none}</style>
<?php endif;//(!ht_trans_is_ru()):?>
	
<?php 

	if(!file_exists($ConfigPath))
	{
		$is_first_run=true;
		if(!isset($_POST['waspost']) ||	!$_POST['waspost'])
			echo "<script>allow_autodetect=true;</script>";
?>
		<div id="first_message" class='header_message'>
			<script>is_first_run=true;</script>
			<?php echo ht_trans('По всей видимости, вы впервые запустили HTracer на этом сайте')?>.<br /><br />
			<?php echo ht_trans('Если вы еще не прочли')?> <a href="../readme.html">Readme.html</a> &mdash; <?php echo ht_trans('обязательно сделайте это')?>!
		</div>
<?php 
		file_put_contents($ConfigPath,'');
		@chmod($ConfigPath, 0777);
		if(!file_exists($ConfigPath))
			echo "<br /><b style='color:red'>File 'admin/auto_config.php' not created. Check permission to HTracer/admin/ folder (must be 777).</b><br />";
		else
			@unlink($ConfigPath);		
	}
	foreach($ht_options_array as $Name => $Default)
		if(!isset($GLOBALS[$Name]))
			$GLOBALS[$Name]=$Default;
			
	$MySQL_Logins=Array('htracer_mysql','htracer_mysql_login', 'htracer_mysql_pass'  ,
						'htracer_mysql_dbname','htracer_mysql_host' , 'htracer_mysql_prefix');
	$MySQL_Logins_Temp=Array();
	foreach ($MySQL_Logins as $Key)
	{
		if(isset($GLOBALS[$Key]))
			$MySQL_Logins_Temp[$Key]=$GLOBALS[$Key];						
	}
	if(isset($_POST['waspost']) && $_POST['waspost'])
	{
		if($_POST['form']=='opt_clear'
		|| $_POST['form']=='opt_save'
		|| $_POST['form']=='opt_import')
		{
			if($_POST['form']=='opt_clear')
			{
				HTracer_Load_Default_Options(true);
				foreach ($MySQL_Logins as $Key)
				{
					if(isset($MySQL_Logins_Temp[$Key]))
						$GLOBALS[$Key]=$MySQL_Logins_Temp[$Key];	
				}
			}
			elseif($_POST['form']=='opt_import')
			{
				$Data=trim($_POST['data'],"\n\r");
				$Data=explode("\n",$Data);
				
				foreach($Data as $Str)
				{
					$Str=trim($Str,"\n\r");
					$Str=explode("=",$Str,2);
					$Key=trim($Str[0]);
					if((!isset($_POST['mysql']) || !$_POST['mysql']) 
					&& strpos($Key,'htracer_mysql')===0)
						continue;
					$GLOBALS[$Key]=$Str[1];
				}
				foreach($ht_options_array as $Name => $Default)
				{
					if(strpos($Name,'_pass'))
						continue;
					if($Default===true||$Default===false)
					{	
						if($GLOBALS[$Name]==='0'||$GLOBALS[$Name]===0)
							$GLOBALS[$Name]=false;
						elseif($GLOBALS[$Name]==='1'||$GLOBALS[$Name]===1)
							$GLOBALS[$Name]=true;
					}
				}
				
				$Words=$GLOBALS['htracer_site_stop_words'];
				$Words=mb_strtolower($Words,'utf-8');
				$Words=explode(',',$Words);
				foreach($Words as $i=>$Word)
					$Words[$i]=trim($Word);
				$Words=join(',',$Words);
				
				$GLOBALS['htracer_site_stop_words']=$Words;
				
				if(!$GLOBALS['htracer_encoding'])
					$GLOBALS['htracer_encoding']='utf-8';
					
				$HTT_Where=Array();
				if(isset($GLOBALS['htracer_insert_meta_keys']) && $GLOBALS['htracer_insert_meta_keys'])
					$HTT_Where[]='meta_keys';
				if(isset($GLOBALS['htracer_insert_img_alt'])   && $GLOBALS['htracer_insert_img_alt'])
					$HTT_Where[]='img_alt';
				if(isset($GLOBALS['htracer_insert_a_title'])   && $GLOBALS['htracer_insert_a_title'])
					$HTT_Where[]='a_title';
				$GLOBALS['insert_keywords_where']=join('+',$HTT_Where);
			}
			else
			{	
				// Пароли мы экранируем, поскольку возможен случай RegisterGlobals
				foreach($_POST as $Key => $Value)
					if(strpos($Key,'_htwdinput'))
						$_POST[str_replace('_htwdinput','',$Key)]=$Value;

				if(isset($_POST['htracer_admin_pass']) 
				&& $_POST['htracer_admin_pass']!=='******')
					$_POST['htracer_admin_pass']=ht_pwd_crc($_POST['htracer_admin_pass']);
				if(isset($_POST['htracer_trace_runaway']) && $_POST['htracer_trace_runaway'] && $_POST['htracer_trace_runaway']!=1
				&&(!isset ($GLOBALS['htracer_trace_runaway']) || !$GLOBALS['htracer_trace_runaway'] || $GLOBALS['htracer_trace_runaway']==1)
				&&(!isset ($GLOBALS['htracer_trace_runaway_start_time']) || !$GLOBALS['htracer_trace_runaway_start_time']))
					$GLOBALS['htracer_trace_runaway_start_time']=time();
				
				foreach($ht_options_array as $Name => $Default)
				{
					if(strpos($Name,'_pass') && $_POST[$Name]=='******')
						continue;
					if(isset($_POST[$Name]))
						$GLOBALS[$Name]=$_POST[$Name];
					if($Default===true||$Default===false)
					{	
						if(!isset($_POST[$Name])||$GLOBALS[$Name]==='0'||$GLOBALS[$Name]===0)
							$GLOBALS[$Name]=false;
						elseif($GLOBALS[$Name]==='1'||$GLOBALS[$Name]===1)
							$GLOBALS[$Name]=true;
					}
				}
				$Words=$GLOBALS['htracer_site_stop_words'];
				$Words=mb_strtolower($Words,'utf-8');
				$Words=explode(',',$Words);
				foreach($Words as $i=>$Word)
					$Words[$i]=trim($Word);
				$Words=join(',',$Words);
				$GLOBALS['htracer_site_stop_words']=$Words;
				$GLOBALS['htracer_encoding']=$_POST['htracer_encoding'];
				if(!$GLOBALS['htracer_encoding'])
					$GLOBALS['htracer_encoding']='utf-8';

				$HTT_Where=Array();
				if(isset($_POST['htracer_insert_meta_keys']) && $_POST['htracer_insert_meta_keys'])
					$HTT_Where[]='meta_keys';
				if(isset($_POST['htracer_insert_img_alt'])   && $_POST['htracer_insert_img_alt'])
					$HTT_Where[]='img_alt';
				if(isset($_POST['htracer_insert_a_title'])   && $_POST['htracer_insert_a_title'])
					$HTT_Where[]='a_title';
				$GLOBALS['insert_keywords_where']=join('+',$HTT_Where);
			}
			$Data='
			// This file was created automatically by options.php
			// DON`T CHANGE!!!
		
				if(!isset($GLOBALS["htracer_encoding"]) 
				||!$GLOBALS["htracer_encoding"] 
				||strtolower($GLOBALS["htracer_encoding"])==="auto"
				||strtolower($GLOBALS["htracer_encoding"])==="global")
					$GLOBALS["htracer_encoding"]="'.$GLOBALS['htracer_encoding'].'";
			';
			foreach($ht_options_array as $Name => $Default)
				$Data.=HT_GetOptionPHP($Name, $Default);
			$Data="<?php \n $Data \n ?>";
			file_put_contents($ConfigPath,$Data);
			@chmod($ConfigPath, 0777);
			if(!file_exists($ConfigPath))
				echo "<br /><b style='color:red'>File 'admin/auto_config.php' not created. Check permission to HTracer/admin/ folder (must be 777).</b><br />";
			else
				echo "<h2>".ht_trans('Настройки были сохранены')."</h2>"; 	
			include_once($ConfigPath);
			if($GLOBALS['htracer_mysql'])
			{
				//Проверяем конект к MySQL
				hkey_connect_to_mysql();
				if(!$GLOBALS["hkey_connect_to_mysql_was_error"])
					HTracer::CreateTables();
			}
		}
		elseif($_POST['form']=='synhr')
		{
			hkey_connect_to_mysql();
			echo "<h2>".ht_trans('Были синхронизированы фильтры. Ключевиков изменено').": ".HTracer::SynhrFilters()."</h2>";
		}
		elseif($_POST['form']=='cash_clear')
		{
			set_time_limit(600);
			HTracer::DelAllFiles('cash');
			echo "<h2>".ht_trans('Файлы кеша были удалены')."</h2>";
		}
	}
	if(!isset($_POST['form']) || $_POST['form']!='synhr')
		hkey_connect_to_mysql();

	$HTT_Where=' +'.$GLOBALS['insert_keywords_where'].'+ ';
	$GLOBALS['htracer_insert_meta_keys']=(bool) strpos($HTT_Where,'+meta_keys+');
	$GLOBALS['htracer_insert_img_alt']=(bool) strpos($HTT_Where,'+img_alt+');
	$GLOBALS['htracer_insert_a_title']=(bool) strpos($HTT_Where,'+a_title+');
	
?>
	<script src="options.js" type="text/javascript"></script>
	<h1><?php echo ht_trans('Настройки HTracer')?></h1>
	<form method="post" id='options_form'>
		<b><?php echo ht_trans('Показать все настройки')?>:</b> <?php HT_OutCheckBox('htracer_show_all_options');?><br /><br />
		<div class="tabs">
			<ul class="tabs_headers">
				<li><a href="#tabs-main"><?php echo ht_trans('Основные')?></a></li>
				<li><a href="#tabs-mysql"><?php echo ht_trans('MySQL')?></a></li>
				<li><a href="#tabs-speed"><?php echo ht_trans('Быстродействие')?></a></li>
				<li><a href="#tabs-insert"><?php echo ht_trans('Вставка')?></a></li>
				<li><a href="#tabs-filters"><?php echo ht_trans('Фильтры')?></a></li>
				<li><a href="#tabs-cloud"><?php echo ht_trans('Облако')?></a></li>
			</ul>
			<div id="tabs-main">
				<span id='encoding_autodetect' style='color:gray'></span>
				<table>
					<tr><th>
						<span class='hint'
							hcontent='<?php echo ht_trans('Когда эта опция включена вы сможете с легкостью проверить работает ли HTracer')?>. 
		 							   <?php echo ht_trans('После проверки, обязательно отключите эту опцию')?>.<br /><br />'>
							<?php echo ht_trans('Включить тестирование')?></span>:					
					</th>
					<td><?php HT_OutCheckBox('htracer_test'); ?></td></tr>
					<tr><th><span class='hint' id='encoding_hint'
							hcontent='
								<?php echo ht_trans('Для определение кодировки перейдите по')?> <a href="http://validator.w3.org/check?uri=<?php echo $_SERVER['SERVER_NAME'];?>"><?php echo ht_trans('этой ссылке')?></a> <?php echo ht_trans('и посмотрите на поле `Encoding`')?>.
							'>
							<?php echo ht_trans('Кодировка Вашего сайта')?></span>:</th>
							<?php if(ht_trans_is_ru()):?>
								<td><?php HT_OutSelect('htracer_encoding',Array('utf-8','windows-1251'));?></td>
							<?php else:?>
								<td><?php HT_OutTextInput('htracer_encoding');?></td>
							<?php endif;?>		
					</tr>
					<tr>
							<th><span class='hint' hcontent='<?php echo ht_trans('Пароль хешируется и записывается в cookies браузера')?>. <?php echo ht_trans('Поэтому, вводить его нужно крайне редко')?>. <br /><br />'>
								<?php echo ht_trans('Пароль админки HTracer')?></span>:
							</th>
							<td><?php HT_OutPwdInput('htracer_admin_pass');?></td>
					</tr>
				<!--
					<tr>
						<th class='hint' hcontent='
							Список GET-пареметров через запятую, которые не сильно влияют на содержимое страницы. 
							<br /><br />
							Эти параметры будут игнорироваться при сравнении URL страниц. 
							Например, если в этом списке есть параметр param, то все переходы на страницу /page1.html?param
							будут защитываться странице /page1.html. Облака на этих двух страницах будут одинаковыми.
							<br /><br />
							Также возможно задать игнорированние параметров, только есл у них определенное значение. 
							Например, "param=value" означает игнорировать param только когда он равен value. 
						'>Игнорируемые GET-параметры:</th>
						<td><?php //HT_OutTextInput('htracer_ignored_get_params');?></td>
					</tr>
				-->	
					<tr>
						<th valign='top' class='hint' hcontent='
							<?php echo ht_trans('Позволяет запретить HTracer изменять определенные страницы страницы')?>.<br /><br />
							<?php echo ht_trans('URLы вводятся построчно, без http:// и домена')?>.<br /><br />
							<?php echo ht_trans('Например')?>:
							<i>/admin/</i>  или	<i>/administrator/</i>	
							<br /><br />

							<?php echo ht_trans('Если URL <b>не</b> заканчивается на "#", то запрет действует на все производные адреса')?>.
							<?php echo ht_trans('Например, правило <i>/admin/</i> распространяеться <i>/admin/login</i> и на <i>/admin/logout</i>')?>.
							<br /><br />
							<?php echo ht_trans('Если URL заканчивается на "/", то правило распространяется и на URL без этого символа в конце')?>.
							<?php echo ht_trans('Например, правило <i>/admin/</i> распространяется и на <i>/admin</i> но не на <i>/admin1</i>')?>. 
							<br /><br />
							<?php echo ht_trans('Чтобы избежать этого нужно поставить "*" после "/", например, <i>/admin/*</i>')?>
							<br /><br />
						'><?php echo ht_trans('Исключенные URL')?>:</th>
						<td><?php HT_OutTextArea('htracer_url_exceptions');?></td>
					</tr>
				</table>
				<!--Плагины-->
					<hr class='normal' />
					<h3><?php echo ht_trans('Расширения')?></h3>		
					<table cellspacing="0" style='
						border:1px solid #CEEFD6;
					    padding-bottom: 15px;
						padding-top: 15px;
						border-radius: 7px;
					'>
				<!--USP-->
	
						<tr>
							<td valign='top' rowspan="2" style='padding-left:15px; padding-right: 3px;'>
								<?php HT_OutCheckBox('htracer_usp'); ?>
							</td>
							<td valign='top'>
								<nobr><b class='hint' 
								hcontent='
									<?php echo ht_trans('Требует MySQL')?>. <?php echo ht_trans('Позволяет настраивать заголовки и мета-теги отдельных страниц')?>.<br /><br />
									<?php echo ht_trans('Многие из CMS не позволяют задавать необходимые оптимизаторам настройки страниц')?>.<br /><br /> 
									<?php echo ht_trans('Да и те которые лишены этого недостатка, обладают разным интерфейсом, что усложняет жизнь оптимизатору, которому приходиться работать с клиентскими сайтами на разных CMS')?>.<br /><br />   
								'>USP</b> <small><i>(Universal SEO Patch)</i></small></nobr>
							</td>
							<td valign='top' rowspan="2" style='padding-left: 30px; padding-right: 10px;'><small>
								<?php echo ht_trans('Позволяет задавать meta keywords, description и заголовок для каждой страницы')?>. 
								<?php echo ht_trans('USP дает оптимизатору независимый от CMS механизм SEO-настройки страницы')?>.
							</small></td>
						</tr>
						
						<tr>
							<td valign="bottom">
								<span class='dhint' id='usp_options' dwidth='600'><?php echo ht_trans('Показать настройки')?><span class='tmp'></span></span>
							</td>
						</tr>	
						<tr>
							<td></td>
							<td colspan="2">
								<div id="usp_options_dialog" class='plugin_options' title=<?php echo ht_trans('Настройки модуля USP')?>" style='display:none'>
									<?php echo ht_trans('Введите как формируется тег Title на вашем сайте')?>.
									<?php echo ht_trans('Например, для заголовка')?>.

									<nobr><small><i><?php echo ht_trans('Оперный театр &lt;&lt; Театры &lt;&lt; Развлечения')?></i><br /></small></nobr><br />
									<?php echo ht_trans('порядок = "Справа на лево", а разделитель')?> &mdash; "&lt;&lt;"
									<br /><br />
									
									<table>
										<tr>
											<th><?php echo ht_trans('Разделитель')?>:</th>
											<td>
												<?php  HT_OutTextInput('htracer_title_spacer');?>
											</td>
										</tr>	
										<tr>
											<th><?php echo ht_trans('Порядок')?>:</th>
											<td>
												<?php HT_OutSelect('htracer_title_order',Array('rtl'=>ht_trans('Справа на лево'),'ltr'=>ht_trans('Слева на право')));?>
											</td>												
										</tr>
									</table>
								</div>
							</td>
						</tr>
					<!--/USP-->
					<!--404-->
						<tr>
							<td colspan="3"><hr class="normal" style='scolor:gray; border-width:1px;' /></td>
						</tr>	
						<tr>
							<td valign='top' rowspan="2" style='padding-left:15px; padding-right: 3px;'>
								<?php HT_OutCheckBox('htracer_404_plugin'); ?>
							</td>
							<td valign='top'>
								<nobr><b class='hint' hcontent='<?php echo ht_trans('Требует MySQL')?>.'>404</b> <small><i>(auto page remover)</i></small></nobr>
							</td>
							<td valign='top' rowspan="2" style='padding-left: 30px; padding-right: 10px;'><small>
								<?php echo ht_trans('Если страница генерирует 404 ошибку, то удаляет ее из базы')?>. 
								<?php echo ht_trans('При 301 ответе сервера (редирект) меняет ее URL')?>.
							</small></td>
						</tr>
						<tr>
							<td valign="bottom">
								<span class='dhint' id='p404_options' dwidth='600'><?php echo ht_trans('Показать настройки')?><span class='tmp'></span></span>
							</td>
						</tr>	
						<tr>
							<td></td>
							<td colspan="2">
								<div id="p404_options_dialog" class='plugin_options' title=<?php echo ht_trans('Настройки модуля USP')?>" style='display:none'>
									<?php HT_OutCheckBox('htracer_404'); ?>404<br />
									<?php HT_OutCheckBox('htracer_301'); ?>301<br />
									
								</div>
							</td>
						</tr>
						<!-- ULinks -->	
						<tr>
							<td colspan="3"><hr class="normal" style='scolor:gray; border-width:1px;' /></td>
						</tr>	
						<tr>
							<td valign='top' rowspan="2" style='padding-left:15px; padding-right: 3px;'>
								<?php HT_OutCheckBox('htracer_ulink_plugin'); ?>
							</td>
							<td valign='top'>
								<nobr><b class='hint' 
									hcontent='
										<?php echo ht_trans('Требует MySQL')?>.<br /><br />
										<?php echo ht_trans('С помощью ULinks вы можете увеличить ссылочное и ускорить индексацию страниц')?>.
										<?php echo ht_trans('Также вы можете разместить ссылки на другие сайты')?>.<br /><br />
										
										<?php echo ht_trans('Ускорение индексации страниц-доноров достигается с помощью "трамплинов"')?>.
										<?php echo ht_trans('Ссылка получает донора, когда донор посещает поисковый робот')?>.
										<?php echo ht_trans('Поскольку, в дальнейшем ссылка закрепляется за донором, то это не является клоакингом')?>.
										<?php echo ht_trans('Включить трамплины для всех ссылок можно в опциях плагина')?>.
										<?php echo ht_trans('Включить трамплин для конкретной ссылки можно поставив вместо адреса ее донора G или Y')?>.<br /><br />
										
										<?php echo ht_trans('Включив трамплин для всех ссылок, вы значительно ускорите индексацию ссылок')?>.<br /><br />
										
										<?php echo ht_trans('Трамплины не работают, если вы пропишите донор у ссылки вручную')?>.
										<?php echo ht_trans('Также трамплины не работают для ссылок донор у которых был определен')?>.<br /><br />
										<br /><br />
									'>ULinks</b> <small><i>(Unical Links)</i></small></nobr>
							</td>
							<td valign='top' rowspan="2" style='padding-left: 30px; padding-right: 10px;'><small>
								<?php echo ht_trans('Позволяет добавить на страницы блоки с уникальными ссылками')?>. 
								<?php echo ht_trans('Увеличивает ссылочное')?>. <?php echo ht_trans('Чтобы добавить ссылки, перейдите в Ключи::Действия')?>.
							</small></td>
						</tr>
						<tr>
							<td valign="bottom">
								<span class='dhint' id='ulinks_options' dwidth='600'><?php echo ht_trans('Показать настройки')?><span class='tmp'></span></span>
							</td>
						</tr>	
						<tr>
							<td></td>
							<td colspan="2">
								<div id="ulinks_options_dialog" class='plugin_options' title=<?php echo ht_trans('Настройки')?>" style='display:none'>
									<?php echo ht_trans('Ссылок на странице')?>: <?php HT_OutTextInput('htracer_ulink_count'); ?><br />
									<span class='hint'
										hcontent='
											<?php echo ht_trans('Если эта опция включена и вы задали донору больше ссылок, чем в опцииям, то часть ссылок будут снята')?>. 
											<?php echo ht_trans('Либо, если вы уменьшите число ссылок в опциях, то излишек будет снят')?>. 
											<br /><br />
											<?php echo ht_trans('Если эта опция выключена, то вы сможете задать некоторым донорам больше ссылок, чем указано в опциях')?>. 
											<br /><br />
									'> 
										<?php echo ht_trans('Снимать ссылки')?>
									</span>: <?php HT_OutCheckBox('htracer_ulink_ignore_more'); ?><br />
									<?php echo ht_trans('Стиль списка')?>: 
									<?php HT_OutSelect('htracer_ulink_list',
										Array(
											'space'=>ht_trans('Пробел'),
											'comma'=>ht_trans('Запятая'),
											'br'=>'br',
											'li'=>'li'
										)
									);?><br />
									<?php echo ht_trans('Быстрая индексация')?>: 
									
									<?php
										if(ht_trans_is_ru())
										{
											HT_OutSelect('htracer_ulink_fast',
												Array(
													0=>ht_trans('Выкл.'),
													'G'=>'Google',
													'Y'=>'Yandex'
												)
											);
										}
										else
											HT_OutCheckBox('htracer_ulink_fast');
									?><br />
									<?php echo ht_trans('Разрешить ссылки на другие домены')?>: <?php HT_OutCheckBox('htracer_ulink_other_domains');?>									
								</div>
							</td>
						</tr>
					<!---->	
					<!--
						<tr><td colspan="3"><hr class='normal' style='border-width:1px' /></td></tr>						
						<tr>
							<td valign='top' rowspan="2" style='padding-left:15px; padding-right: 3px;'>
								<?php //HT_OutCheckBox('htracer_permalinks'); ?>
							</td>
							<td valign='top'>
								<b class='hint' hcontent='
									Требует MySQL. Позволяет настраивать редиректы и кононические URL.<br /><br />
									Если ваша CMS не позволяет использовать ЧПУ, вы можете их задать в HTracer.<br /><br />
									Изменения подействуют и на ссылки и на редиректы.<br /><br />
									Также можно задать канонические URLы страниц, чтобы в индексе не было дублей страниц, например, так можно поступить с версией для печати
								'>Permalinks</b>
							</td>
							<td valign='top' rowspan="2" style='padding-left: 30px; padding-right: 10px;'><small>
								Позволяет задавать редиректы. Причем, в ссылках на сайте старые URL заменяются на новые.
								Также позволяет задавать и rel=canonical (канонический URL страницы) и параметры его автоматического формирования.
							</small></td>
						</tr>
						<tr>
							<td valign="bottom">
								<span class='dhint' id="Permalinks_options" dwidth='600'><small>Показать настройки</small><span class='tmp'></span></span>
								<div id="Permalinks_options_dialog" title="Настройки модуля Permalinks" style='display:none'>
									<table>
										<tr>
											<th class='hint' hcontent='
												Использовать игнорируемые GET параметры для канонизации.<br /><br />
												Например, если задан параметр param, то на страницу /page.html?param 
												будет добавлено, что ее канонический URL = /page.html<br /><br />
											'>
												Испол. игнор. GET параметры для канонизации: 
											</th>
											<td>
												<?php //HT_OutCheckBox('htracer_ignored_get_params_rel');?>
											</td>
										</tr>	
										<tr>
											<th class='hint' hcontent='
												Если эта опция включена, HTracer будет и для /page1.html задан канонический URL /page2.html, 
												то все переходы на page1.html будут засчитаны page2.html, 
												облако для этих страниц будет одинаковым. 
												При формировании альтов и метакейвордс для page1.html будут использованы ключи page2.html
											'>
												Учитывать канонический URL в HTracer:
											</th>
											<td>
												<?php //HT_OutCheckBox('htracer_ignored_get_params_rel');?>
											</td>
										</tr>
									</table>
								</div>	
							</td>
						</tr>
						<tr><td colspan="3"><hr class='normal' style='border-width:1px' /></td></tr>						
						<tr>
							<td valign='top' rowspan="2" style='padding-left:15px; padding-right: 3px;'>
								<?php //HT_OutCheckBox('htracer_breadcrumbs'); ?>
							</td>
							<td valign='top'>
								<b class='hint' hcontent='
									Позволяет выводить хлебные крошки (пути в котором находиться пользователь на сайте). 
									Например, <br />
									<small>
										<a href="#">Развлечения</a> -> <a href="#">Театры</a> -> <b>Оперный театр</b> 
									</small>
									<br /><br />
									Однако, это возможно только когда выполняются все эти условия: 
									<ol>
										<li>URL имеет вид /category/sub_category/page</li> 
										<li>В теге Title прослеживаеться иерархия документа. Например, <i><nobr>Оперный театр | Театры | Развлечения</nobr></i></li>
										<li>Было настроено расширение USP</li>
									</ol>	
									<br /><br />
								'>Хлебные крошки</b>
							</td>
							<td valign='top' rowspan="2" style='padding-left: 30px; padding-right: 10px;'><small>
								Позволяет выводить хлебные крошки. Например, <nobr><i><a href="#">Развлечения</a> -> <a href="#">Театры</a> -> Оперный театр</nobr></i>. 
								Данные берутся из тега Title и Адреса страницы. Работает только когда и в URL и в заголовке страницы (теге Title) прослеживается иерархия документа и настроено расширение USP.
							</small></td>
						</tr>
						<tr>
							<td valign="bottom">
								<span class='hint'><small>Показать настройки</small></span>
							</td>
						</tr>
					-->	
					</table>
			</div>
			<div id="tabs-mysql">
				<table><tr><td style="padding-right: 11px;" valign='top'>
					<span id='mysql_autodetect_msg' style='color:gray;'></span>
					<table id='mysql_options'>
						<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('При отключенном MySQL запись будет происходить в файлы')?>. 
							<?php echo ht_trans('Этот режим значительно медленее и обладает меньшим функционалом')?>.<br /><br />
							<?php echo ht_trans('При обычном режиме код подключения HTracer необходимо вставлять после подключения сайта к БД. И имена БД сайта и HTracer должны совпадать')?>.<br /><br />
							<?php echo ht_trans('При форсированном режиме приложение само подключается к MySQL. В этом режиме код подключения HTracer должен стоять до подключения к БД, в противном случае сработает защита, отключить которую можно с помощью опции "игнорировать mysql_ping"')?> <br /><br />
							<?php echo ht_trans('Если сайт выводит ошибки в стиле "MySQL acess denied", то включайте форсирование')?>.<br /><br />
							<?php echo ht_trans('При смене имени БД, префикса таблиц или хранилища (MySQL/Файлы) перенос ключей не осуществляется')?>.
						'>
							<?php echo ht_trans('Использовать MySQL')?></span>:</th>
							<td><?php HT_OutSelect('htracer_mysql',Array('0'=>ht_trans('нет'),'1'=>ht_trans('да'),'forced'=>ht_trans('Форсировать')));?></td></tr>
						<tr><th><?php echo ht_trans('Пользователь MySQL')?>:</th>
							<td><?php HT_OutTextInput('htracer_mysql_login');?></td></tr>
						<tr><th><?php echo ht_trans('Пароль к MySQL')?>:</th>
							<td><?php HT_OutPwdInput('htracer_mysql_pass');?></td></tr>
						<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Рекомендуеться, чтобы имена БД, которые используют сайт и HTracer совпадали. В противном случае возможны ошибки.')?>
							'><?php echo ht_trans('Имя базы данных');?></span>:</th>
							<td><?php HT_OutTextInput('htracer_mysql_dbname');?></td></tr>
						<tr><th><?php echo ht_trans('Хост MySQL');?>:</th>
							<td><?php HT_OutTextInput('htracer_mysql_host');?></td></tr>
						<?php if(!$GLOBALS['htracer_is_demo']):?>
							<tr><th><span class='hint' hcontent='
								<?php echo ht_trans('Используется, когда число БД ограничено на хостинге и в одну БД "запихивается" несколько сайтов');?>. 
							'>
								<?php echo ht_trans('Префикс таблиц')?></span>:</th>
								<td><?php HT_OutTextInput('htracer_mysql_prefix');?></td></tr>	
						<?php endif;?>	
						<tr><th>
						<span class='hint' hcontent='
							<?php echo ht_trans('Если вместо запросов в админке вы увидите крокозяблы, то используйте эту опцию')?>. <br /><br /> 
							<?php echo ht_trans('Если вы только что установили HTracer и используете форсирование MySQL, то установите "SetNames админки"=UTF-8')?>. <br /><br />
							<?php echo ht_trans('В остальных случаях изменять эту настройку SetNames не рекомендуется')?>.
						'>
							<?php echo ht_trans('SetNames админки')?></span>:</th>
								<?php if(ht_trans_is_ru()):?>
									<td><?php HT_OutSelect('htracer_mysql_set_names',Array('auto'=>ht_trans('автоопределение'),'0'=>ht_trans('нет'),'utf8'=>'UTF-8','cp1251'=>'cp1251'));?></td></tr>
								<?php else:?>
									<td><?php HT_OutTextInput('htracer_mysql_set_names');?></td>
									
							<?php endif;?>		

						<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('В HTracer встроена защита, которая не позволяет соединяться с MySQL после того, как CMS сайта создала свое соединение')?>.<br /><br />
							<?php echo ht_trans('В противном случае HTracer мог бы нарушить работоспособность сайта. Эта отпция отключает эту защиту')?>.<br /><br />
							<?php echo ht_trans('Используется при возникновении ошибки "Форсирование MySQL не возможно!"')?>
						'>
							<?php echo ht_trans('Игнор. mysql_ping')?>:</span></th>
							<td><?php HT_OutCheckBox('htracer_mysql_ignore_mysql_ping');?></td></tr>
					</table>
				</td><td style='padding-left: 15px; border-left: 1px solid rgb(206, 239, 214);'>
					<h3 style="margin-bottom: 7px;"><?php echo ht_trans('Проверка')?></h3>
					<?php echo ht_trans('Здесь вы можете проверить правильность введенных параметров доступа к MySQL')?>.<br /><br />
					<input id='test_mysql_btn' type='button' value='<?php echo ht_trans('Проверить')?>' onclick='CheckMySQL()' />		
					<span id='test_mysql_res' style='smargin-left:10px'></span>
				</td></tr></table>	
			</div>
			<div id="tabs-speed">
				<table><tr><td style="padding-right: 15px; width:500px">
				<table width="100%">
					<?php if(class_exists('DOMDocument')):?>
						<tr><th><?php echo ht_trans('Способ разбора HTML')?>:</th>
							<td><?php HT_OutSelect('htracer_use_php_dom',Array('ht_false'=>ht_trans('надежный'),'ht_true'=>ht_trans('быстрый'))); ?></td></tr>
						<tr><td colspan="2"><hr class='normal' id='ohr_1' /></td></tr>
					<?php endif;?>
					<?php if(!$GLOBALS['htracer_is_demo']):?>
						<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Чтобы отключить кеш совсем задайте 0')?>.<br /><br />
							<?php echo ht_trans('Рекомендованное значение 14. Уменьшение этого числа практически не влияет на размер кеша')?><br /><br />
						'><?php echo ht_trans('Актуальность кеша')?></span>:</th>
							<td><nobr><?php HT_OutTextInput('htracer_cash_days');?> <?php echo ht_trans('дней')?></nobr></td></tr>
						<?php if(function_exists('gzcompress')):?>
							<tr><th><span class='hint' hcontent='
								<?php echo ht_trans('Уменьшает место необходимое для кеша засчет небольшого увеличения затрат процессорного времени')?>.<br /><br />
								<?php echo ht_trans('На некоторых хостингах может вызывать ошибки. Для проверки: со включенным кешем и этой опцией дважды перегрузите любую страницу вашего сайта')?>.
							'><?php echo ht_trans('GZip cжатие кеша')?></span>:</th>
								<td><?php HT_OutCheckBox('htracer_cash_use_gzip');?></td></tr>	
						<?php endif;?>
						<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Если эта опция включена, то кеш будет занимать крайне мало места на диске (меньше 1 мб), но его эффективность снизиться')?>.<br /><br />
							<?php echo ht_trans('При отключении этой опции кеш на больших сайтах может занимать очень много места')?>.<br /><br />
							<?php echo ht_trans('Отключать только в случае крайней необходимости')?>.
						'><?php echo ht_trans('Кешировать только общие данные')?></span>:</th>
							<td><?php HT_OutCheckBox('htracer_short_cash');?></td></tr>
						<tr><th><span class='hint' hcontent='
								<?php echo ht_trans('Если эта опция включена, то в кеш будут добавляться только данные относящиеся ко всем сраницам сразу или к главной странице сайта')?><br /><br />
								<?php echo ht_trans('Увеличивает эффективность кеша засчет увеличения занимаемого места на диске')?>.<br /><br />
								<?php echo ht_trans('При наличии на страницах сайта постоянно меняющихся данных (рандомные данные, указание на сайте текущей даты или времени генерации страницы) не приведет к ускорению работы')?>.
						'><?php echo ht_trans('Кешировать страницы целиком')?></span>:</th>
							<td><?php HT_OutCheckBox('htracer_cash_save_full_pages');?></td></tr>
						<tr><td colspan="2"><hr class='normal' id='ohr_2' /></td></tr>
					<?php endif;?>	

					<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Если вы выберите эту опцию, то это немного увеличит скорость, но после каждого обновления вам нужно будет заходить на любую из страниц админки')?>.
					'><?php echo ht_trans('Не создавать таблицы')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_mysql_dont_create_tables'); ?></td></tr>
					<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Раз в 2000 переходов оптимизирует таблицы MySQL')?><br /><br />
					'><?php echo ht_trans('Оптимизировать таблицы')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_mysql_optimize_tables'); ?></td></tr>
					<tr><td colspan="2"><hr class='normal' id='ohr_3' /></td></tr>
					<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Если эта опция включена, то переходы будут записываться не сразу, а только после того, как будет запомнено необходимое число переходов')?>.
					'><?php echo ht_trans('Группировать переходы по')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_trace_grooping'); ?></td></tr>
					<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Если опция включена, то переходы будут записываться и таблицы оптимизироваться только с 2 до 6 часов ночи')?>.
					'><?php echo ht_trans('Только ночное обновление')?>:</th>
						<td><?php HT_OutCheckBox('htracer_only_night_update'); ?></td></tr>
					<tr><td colspan="2"><hr class='normal' id='ohr_4' /></td></tr>
					<tr><th><span class='hint' hcontent='<?php echo ht_trans('В абсолютном большинстве случаев не оказывает влияние на быстродействие. Может вызывать ошибки.')?>'>
						<?php echo ht_trans('Закрывать MySQL соединение')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_mysql_close'); ?></td></tr>	
					<?php if(function_exists('mysql_pconnect')): ?>
						<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Использовать постоянное соединение с MySQL')?><br /><br />
							<?php echo ht_trans('Немного снижает нагрузку на сервер, но может привести к ошибкам')?><br /><br />
							<?php echo ht_trans('Если вы не знаете что это такое -- не трогайте')?>.
						'><?php echo ht_trans('Использовать pconnect')?></span>:</th>
							<td><?php HT_OutCheckBox('htracer_pconnect'); ?></td></tr>	
					<?php endif; ?>
				</table>
				<td style="padding-left: 15px; border-left: 1px solid rgb(206, 239, 214);" id='speed_test'>
					<h3 id='speed_test_title'><?php echo ht_trans('Проверка скорости')?></h3>
					<p><?php echo ht_trans('Здесь вы можете оценить задержки создаваемые HTracer при загрузке страницы')?>.</p>
					<?php if(!$GLOBALS['htracer_is_demo']): ?>
						<b>URL</b>: <input id='speed_test_url' value='/' /><br />
						<input id='speed_test_btn' type='button' value='<?php echo ht_trans('Поехали')?>' onclick='CheckSpeed()' /><br />
					<?php else: ?>
						<?php echo ht_trans('Проверка скорости недоступна в демо-версии')?>
					<?php endif; ?>
					<div id='speed_test_res' style='height: 60px;'></div>
				</td></tr></table>
			</div>
			<div id="tabs-insert">
				<h3 style="margin-bottom:0;padding-bottom:0;"><?php echo ht_trans('Вставлять')?>:</h3>
				<table>
					<tr><th>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:black"><?php echo ht_trans('Альты картинок')?></span>:</th>
						<td>
							<?php HT_OutCheckBox('htracer_insert_img_alt');?>
						</td><td><nobr>
							&nbsp;&nbsp;&nbsp;&nbsp;<span class='hint' hcontent='
								<?php echo ht_trans('По умолчанию (когда эта опция выключена) HTracer прописывает ключевики только когда соответсвующего аттрибута нет либо он пуст')?>.<br /><br />
								<?php echo ht_trans('Если эта опция включена, то даже непустые аттрибуты будут переписываться')?>.  
							'><?php echo ht_trans('Переписывать')?></span>: 
							<?php HT_OutCheckBox('htracer_img_alt_rewrite');?>
						</nobr></td>
					</tr>
					<tr><th>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:black"><?php echo ht_trans('Титлы ссылок')?></span>:</th>
						<td>
							<?php HT_OutCheckBox('htracer_insert_a_title');?>
						</td><td><nobr>
							&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ht_trans('Переписывать')?>: 
							<?php HT_OutCheckBox('htracer_a_title_rewrite');?>
						</nobr></td>
					</tr>

					<tr><th>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:black"><?php echo ht_trans('Мета Кейвордс')?></span>:</th>
						<td>
							<?php HT_OutCheckBox('htracer_insert_meta_keys');?>
						</td><td><nobr>
							&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ht_trans('Переписывать')?></span>: 
							<?php HT_OutCheckBox('htracer_meta_keys_rewrite');?>
						</nobr></td>
					</tr>
				</table>
				<hr style="position:static; width: auto; margin-top:10px;margin-bottom:10px" />
				<h3 style="margin-bottom:0;padding-bottom:0;"><?php echo ht_trans('Контекстные ссылки')?>:</h3>
				<table>
					<tr><th>&nbsp;&nbsp;&nbsp;&nbsp;<span class='hint' hcontent='
							<?php echo ht_trans('Контекстные ссылки &mdash; это ссылки прямо из текста. Например, "<i>Оперный театр &mdash; одно из самых красивых зданий <a href="#">Одессы</a> и Европы</i>"')?>.<br /><br />
							<?php echo ht_trans('Если вы выбирите "в диапазоне", то контекстные ссылки будут вставлены только в текст между <i>&amp;lt;!--htracer_context_links--&gt;</i> и <i>&amp;lt;!--/htracer_context_links--&gt;</i> Этот вариант является рекомендуемым')?>. <br /><br /> 
							<?php echo ht_trans('Если вы выбирете "в селекторе", то вы сможете указать CSS-селектор, куда нужно вставлять ссылки. Например чтобы вставлять ссылки только в элемент с id=content напишите "<i>#content</i>"')?>.
					'><?php echo ht_trans('Вставлять контекстные ссылки')?></span>:</th>
						<td><?php 
						$CLT_Arr=Array('0'=>ht_trans('нет'),'1'=>ht_trans('везде'),'ranges'=>ht_trans('в диапазоне'));
						if(version_compare(PHP_VERSION, '5', '>'))
							$CLT_Arr['selector']=ht_trans('в селекторе');
						HT_OutSelect('hkey_insert_context_links',$CLT_Arr);				
						?></td></tr>
						<tr><th>&nbsp;&nbsp;&nbsp;&nbsp;<span class='hint' hcontent='
							<?php echo ht_trans('Например, чтобы вставлять ссылки только в элемент с id=content напишите')?> "<i>#content</i>".<br /><br />
							<?php echo ht_trans('Чтобы вставлять ссылки элементы с class=cl напишите')?> ".cl".<br /><br />
							<?php echo ht_trans('Поддерживается множественный выбор')?> "<i>#id1,#id2</i>" <br /><br />
							<?php echo ht_trans('Также можно указать пути')?> "<i>div.class1 span.class2</i>"
					'><?php echo ht_trans('CSS-селектор')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_context_links_selector'); ?></td></tr>

					<tr><th>&nbsp;&nbsp;&nbsp;&nbsp;<span class='hint' hcontent='
							<?php echo ht_trans('Эти слова частично игнорируются при расстановке контекстных ссылок, что позволяет увеличить число контекстных ссылок')?>.<br /><br />
							<?php echo ht_trans('Все словоформы, в нижнем регистре, через запятую')?>.<br /><br />
							<?php echo ht_trans('Например, если у вас есть сайт про Одессу, то это поле должно иметь вид "<i>одесса,одессы,одессу</i>"')?>.<br /><br />
					'><?php echo ht_trans('Стоп-слова сайта через запятую')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_site_stop_words');?></td></tr>
					<tr><th>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ht_trans('Выделять жирным ключевики')?>:</th>
						<td><?php HT_OutSelect('htracer_context_links_b',Array('0'=>ht_trans('нет'),'only_first'=>ht_trans('только первый'),'1'=>ht_trans('да')));?></td></tr>
					<tr><th>&nbsp;&nbsp;&nbsp;&nbsp;<span class='hint' hcontent='
							<?php echo ht_trans('Чем это число больше, тем больше контекстных ссылок на странице')?>.<br /><br />
							<?php echo ht_trans('Однако, увеличение этого числа снижает производительность')?>.
					'><?php echo ht_trans('Размер базы контекстных ссылок')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_clcore_size');?></td></tr>
					<tr><th>&nbsp;&nbsp;&nbsp;&nbsp;<span class='hint' hcontent='
							<?php echo ht_trans('Максимальное число контекстных ссылок на одной странице')?>. 
					'><?php echo ht_trans('Максимум контекстных ссылок')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_max_clinks');?></td></tr>
						
					<tr><th>&nbsp;&nbsp;&nbsp;&nbsp;<span class='hint' hcontent='
							<?php echo ht_trans('Не меньше 100. Определяет максимальную плотность контекстных ссылок')?>.<br /><br />
							<?php echo ht_trans('Чем выше длина сегмента, тем ниже плотность контекстных ссылок')?>.
					'><?php echo ht_trans('Длина сегмента')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_clinks_segment_lng');?></td></tr>
				</table>
				<hr style="position:static; width: auto; margin-top:10px;margin-bottom:10px" />
				<table>
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('HTracer способен автоматически исправлять большинство формальных ошибок валидности HTML кода')?>.<br /><br />
						<?php echo ht_trans('Исправляются только формальные ошибки, благодаря чему автовалидация не влияет на отображение страницы в браузерах')?>.<br /><br />
						<?php echo ht_trans('Это позволяет добиться лучших результатов в ранжировании сайта в поисковых системах, которые учитывают валидность HTML-кода')?>.<br /><br />
						<?php echo ht_trans('Также это улучшает ценность валидаторов, как инструментов поиска ошибок')?>.<br /><br />
						<?php echo ht_trans('Автовалидация полностью безопастна, однако, ее можно отключить для ускорения работы приложения')?>.<br /><br />
					'><?php echo ht_trans('Автовалидация HTML')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_validate');?></td></tr>
				</table>

			</div>
			<div id="tabs-filters">
			<table cellspacing="0" cellpadding="0"><tr>
			<td valign='top' style="padding-right: 11px;">
				<table>
					<?php if(!isset($_GET['wp_akey'])&&!isset($_COOKIE['wp_akey'])):?>
						<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Если эта опция выключена, то обновление семантического ядра не будет происходить при переходе пользователя с поисковых систем')?>.<br /><br />
							<?php echo ht_trans('Отключение этой опции может немного увеличить скорость приложения')?>.
						'><?php echo ht_trans('Запоминать переходы')?></span>:</th>
							<td><?php HT_OutCheckBox('htracer_trace');?></td></tr>
					<?php endif; ?>
					<tr><th valign='top'><span class='hint' hcontent='
						<?php echo ht_trans('Если эта опция включена, то все новые ключи будут иметь отключенную галочку СЯ')?>.<br /><br />
					'><?php echo ht_trans('Премодерация')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_premoderation');?><br /><br /></td></tr>
						
						
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Если эта опция включена, то система будет игнорировать переходы со словами "аренда", "ремонт" и др')?>.<br /><br />
						<?php echo ht_trans('Актуально для веб-магазинов, чтобы не продвигаться по запросам в стиле "ремонт ноутбуков"')?>
					'><?php echo ht_trans('Отфильтровывать услуги')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_trace_service_filter');?></td></tr>
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Если эта опция включена, то система будет игнорировать переходы со словами "бесплатно", "кряк", "кейген" и др')?>.<br /><br />
					'><?php echo ht_trans('Отфильтровывать "бесплатные" слова')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_trace_free_filter');?></td></tr>
						
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Если эта опция включена, то система будет игнорировать переходы с цензурными словами, которые относятся к сексу. Например, "оральный", "вагина" и др')?>.<br /><br />
						<?php echo ht_trans('Матные слова фильтруются в любом случае (независимо включен этот фильтр или нет)')?>.
					'><?php echo ht_trans('Секс-фильтр')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_trace_sex_filter');?></td></tr>
						
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Если эта опция включена, то система будет игнорировать переходы с нецензурными словами')?>.<br /><br />
					'><?php echo ht_trans('Фильтр матов')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_mats_filter');?></td></tr>
						
						
					<tr><th valign='top'><span class='hint' hcontent='
						<?php echo ht_trans('Фильтр на слова: скачать, драйвера, программы, фильмы, картинки и прочие, говорящих что пользователь хочет скачать или купить нематериальные объекты')?>.<br /><br />
						<?php echo ht_trans('Предназначен для интернет-магазинов')?>.
					'><?php echo ht_trans('Софт-фильтр')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_trace_download_filter');?><br /><br /></td></tr>
					
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Отфильтровывает запросы содержащие символы не входящие в английский, русский и латинский алфавиты, в числа и знаки препинания')?>.<br /><br />
						<?php echo ht_trans('Предназначен для борьбы с неправильной кодировкой')?>.
					'><?php echo ht_trans('Whitelist символов')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_symb_white_list');?></td></tr>
					
					
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Если эта опция включена, то запросы состоящие только из цифр будут отфильтрованы')?>.<br /><br />
						<?php echo ht_trans('Например, `123` или `1.23` или `1,23`, но не `Doom 3`')?>.
					'><?php echo ht_trans('Цифровой фильтр')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_numeric_filter');?></td></tr>
					<?php if(ht_trans_is_ru()):?>
						<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Если эта опция включена, то запросы НЕ содержащие ни одного кирилического символа будут отфильтровываться')?>.<br /><br />
							<?php echo ht_trans('Отфильтровывает запросы написанные транслитом либо в неправильной раскладке клавиатуры')?>.<br /><br />
							<?php echo ht_trans('НЕ включайте это опцию если вам приходят люди по запросам в стиле `Sumsung D500` или `php str_replace`')?>.
						'><?php echo ht_trans('Не русский фильтр')?></span>:</th>
							<td><?php HT_OutCheckBox('htracer_not_ru_filter');?></td></tr>					
					<?php endif;?>					
				</table>
				<div><div id='user_minus_words' style='padding:1px; white-space:nowrap'>
					<span class='hint' hcontent='
						<?php echo ht_trans('Если запрос содержит одно из этих слов, то он будет отфильтрован')?>.<br /><br />
						<?php echo ht_trans('Например, название вашего сайта')?>. <?php echo ht_trans('Запросы содержащие домен и маты фильтруются автоматически')?><br /><br />
						<?php echo ht_trans('Синтаксис: `абв` - слово целиком, `абв*` - слово начинается с абв,  `*абв` - заканчивается на абв, `*абв*` - подстрока')?>.<br /><br />
					'>	
					<?php echo ht_trans('Минус слова')?></span>:
					<?php HT_OutTextInput('htracer_user_minus_words');?>
				</div></div>
				<hr style="position:static; width: auto; margin-top:20px;margin-bottom:15px" />
				<table>
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Если эта опция включена, то если пользователь перешел со второй и более страницы выдачи, то его переход будет засчитан за два')?>.<br /><br />
						<?php echo ht_trans('Это позволяет системе уделять большее внимание запросам по которым сайт еще не вышел на приемлемую позицию')?>.
					'><?php echo ht_trans('Удвоить вес переходов с &ge; 2<small>ой</small> страницы')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_trace_double_not_first_page');?></td></tr>
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Если эта опция включена, то если пользователь перешел по запросу содержащему слова "купить", "цена", "аренда" и пр., то его переход засчитывается за 2')?>.<br /><br />
						<?php echo ht_trans('Актуально для сайтов занимающихся продажей товаров или услуг. Это позволит уделять продающим запросам большее внимание, чем информационным')?>.
					'><?php echo ht_trans('Удвоить вес коммерческих запросов')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_trace_double_comercial_query');?></td></tr>
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Рекомендовано поставить 1.05')?><br /><br />
						<?php echo ht_trans('Благодаря этому параметру вы можете сделать так, чтобы новые переходы имели больший вес, чем старые')?>.
						<?php echo ht_trans('Это позволит динамичнее реагировать на изменения алгоритмов поисковых систем')?>.<br /><br />
						<?php echo ht_trans('Например, если вы поставите коэффициент 1.05 то запрос, совершенный в октябре будет иметь на 5% больше веса, чем сентябрьский, и на 10% чем августовский')?>.
					'><?php echo ht_trans('Коэффициент месячного приращения')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_trace_runaway');?></td></tr>
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Это число добавляется к весу запроса после каждого просмотра страницы пользователем, пришедшим по этому запросу. Просмотр посадочной страницы не учитывается')?>.<br /><br />
						<?php echo ht_trans('Например, если этот параметр равен 1, то вес запроса будет равен не числу переходов по нему, а числу страниц просмотренных, пользователями перешедшими по этому запросу')?>.<br /><br />
						<?php echo ht_trans('Рекомендовано поставить 1 для сайтов, получающих доход только с показов рекламы. Для всех остальных -- 0.5')?> <br /><br />
						<?php echo ht_trans('Эта опция недоступна при включенной группировке переходов или при отключенном MySQL')?><br /><br />
					'><?php echo ht_trans('Бонус за просмотр одной страницы')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_trace_view_depth');?></td></tr>
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Позволяет ввести до 5 целевых страниц и бонусы за просмотр каждой из них')?>.<br /><br />
						<?php echo ht_trans('Например, если покупка завершается страницей вроде "Спасибо_за_покупку.html", то вы можете увеличить вес продающих запросов')?>.<br /><br /><br />
						<?php echo ht_trans('Эта опция недоступна при включенной группировке переходов или при отключенном MySQL')?><br /><br /><br />
						
						<?php echo ht_trans('Также если эта опция включена, то есть возможность добавить вес ключевику с которого пришел текущий пользователь, через PHP')?>. 
						<?php echo ht_trans('Например, <nobr><i>&amp;lt;?php HTracer::AddBonus(100);?></i></nobr> добавит 100 веса соответствующему запросу')?><br /><br />
					'><?php echo ht_trans('Бонусы за просмотр целевых страниц')?></span>:</th>
						<td><?php HT_OutCheckBox('htracer_trace_use_targets');?></td></tr>
				</table>
				<div id='targrets'>
					<hr style="position:static; width: auto; margin-top:10px;margin-bottom:10px" />
					<h3><?php echo ht_trans('Целевые страницы')?></h3>
					<table>
						<tr>
							<th><span class='hint' hcontent='
								<?php echo ht_trans('URL ключевой страницы без http://site.ru. Например, /page.html')?>.<br /><br />
								<?php echo ht_trans('Можно использовать * в конце ключа, это значит урлы начинающиеся с этого')?>. 
								<?php echo ht_trans('Например, если указано /dir/*, то будут подходить все урлы начинающиеся с /dir/ (/dir/, /dir/page1.html, /dir/page2.html, /dir/dir2/page.html, ..)')?>  
							'>URL</span>
							</th>
							<th><span class='hint' hcontent='
								<?php echo ht_trans('Сколько веса добавлять запросу с которого пришел пользователь, при переходе на целевую страницу страницу')?>'
								><?php echo ht_trans('Бонус')?></span>
							</th>
						</tr>
						<tr><td><?php HT_OutTextInput('htracer_trace_p1_url');?></td><td><?php HT_OutTextInput('htracer_trace_p1_bonus');?></td></tr>
						<tr><td><?php HT_OutTextInput('htracer_trace_p2_url');?></td><td><?php HT_OutTextInput('htracer_trace_p2_bonus');?></td></tr>
						<tr><td><?php HT_OutTextInput('htracer_trace_p3_url');?></td><td><?php HT_OutTextInput('htracer_trace_p3_bonus');?></td></tr>
						<tr><td><?php HT_OutTextInput('htracer_trace_p4_url');?></td><td><?php HT_OutTextInput('htracer_trace_p4_bonus');?></td></tr>
						<tr><td><?php HT_OutTextInput('htracer_trace_p5_url');?></td><td><?php HT_OutTextInput('htracer_trace_p5_bonus');?></td></tr>
					</table>
				</div>
			</td><td style="padding-left: 45px; border-left: 1px solid rgb(206, 239, 214);" valign='top'>
				<div style='margin:0 auto'>
					<h3 style="margin-bottom: 7px;"><?php echo ht_trans('Проверка запроса')?></h3>
					<?php echo ht_trans('Введите любой запрос')?><br />
					<input id='test_query' style="margin-bottom: 4px; margin-top: 4px; vertical-align: text-bottom;" 
					/><img id='test_query_img' style='height: 22px;width: 22px; margin: 4px; display:none' /><br />
					<!--<input id='test_query_btn' type="button" value='Проверить' onclick='CheckQueryFilters()' />-->
					<div id='test_query_res' style='height: 40px;'></div>
				</div>
			</td></tr></table>			
				
			</div>
		
		<div id="tabs-cloud">
			<table cellspacing="0" cellpadding="0" width='100%'><tr>
			<td valign='top' style="padding-right: 11px;">
				<nobr><?php echo ht_trans('Здесь вы можете задать настройки')?><br /><?php echo ht_trans('облака ссылок по умолчанию')?>.</nobr><br /><br />
				<table>
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Благодаря этому параметру вы сможете превратить облако в список ссылок')?>.
					'><?php echo ht_trans('Стиль')?></span>:</th>
						<td><?php HT_OutSelect('htracer_cloud_style', Array(''=>ht_trans('облако'),'ul_list'=>'UL','ol_list'=>'OL','br_list'=>'BR','space_list'=>'Space','comma_list'=>'Comma'));?></td></tr>

					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Число ссылок в облаке. От 1 до бесконечности')?>.
					'><?php echo ht_trans('Число ссылок')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_cloud_links');?></td></tr>

					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Случайность облака. Чем этот параметр выше, тем более разные облака будут на разных страницах')?><br /><br /> 
						<?php echo ht_trans('От 1 до 10. Может быть дробным (Например, 1.5). Значения больше 3 могут снизить производительность')?>.
					'><?php echo ht_trans('Случайность')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_cloud_randomize');?></td></tr>

					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Размер шрифта минимального  элемента облака. Выражается в процентах от обычного размера текста на сайте. От 1 до бесконечности. Рекомендуемые значения от 50 до 90')?>.
					'><?php echo ht_trans('Мин. размер')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_cloud_min_size');?></td></tr>
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('Размер шрифта максимального элемента облака. Выражается в процентах от обычного размера текста на сайте.От 1 до бесконечности. Рекомендуемые значения от 110 до 300')?>.
					'><?php echo ht_trans('Макс. размер')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_cloud_max_size');?></td></tr>
					<?php if(version_compare(PHP_VERSION, '5', '>')):?>	
						<tr><th><span class='hint' hcontent='
							<?php echo ht_trans('Позволяет без изменения файлов шаблона движка вставить облако на сайт')?><br /><br />
							<?php echo ht_trans('Например, чтобы вставить облако в тег с id="sidebar" нужно написать')?> "<i>#sidebar</i>"<br /><br />
							<?php echo ht_trans('Поддерживаются пути ("<i>#id .class tag</i>") и множественный выбор')?> -- "<i>#id1, #id2</i>".<br /><br />
							<?php echo ht_trans('По умолчанию, облако вставляется в конец найденных тегов')?>, 
							<?php echo ht_trans('чтобы добавить его в начало тега -- допишите перед селектором "<i>_start</i>", например')?> "<i>_start</i> #sidebar".<br /><br />
							"<i>_after </i>"  - <?php echo ht_trans('означает вставку после тега, "<i>_before </i>" -- перед тегом, "<i>_replace </i>" -- заменяет содержимое тега')?>.<br /><br />
							<?php echo ht_trans('Если у вас несколько облак, то задать параметры одному из них можно поставив `?` после селектора и ввести настройки #id?count=10')?>.
						'><?php echo ht_trans('CSS-селектор')?></span>:</th>
							<td><?php HT_OutTextInput('htracer_cloud_selector');?></td></tr>
					<?php endif;?>	
					
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('HTML-код, который будет вставлен перед облаком')?>.
					'><?php echo ht_trans('HTML перед')?></span>:</th>
					<td><?php HT_OutTextInput('htracer_cloud_pre');?></td></tr>
					
					<tr><th><span class='hint' hcontent='
						<?php echo ht_trans('HTML-код, который будет вставлен после облака')?>.
					'><?php echo ht_trans('HTML после')?></span>:</th>
						<td><?php HT_OutTextInput('htracer_cloud_post');?></td></tr>
				</table>
				<br />
				<span class='hint' hcontent='
					<br /><br />
					<?php echo ht_trans('Чтобы убрать ссылку из облака -- перейдите в раздел `Страницы` и уберите у необходимой вам строки в таблице галочку `О`')?>.
					<br /><br />
					<?php echo ht_trans('Чтобы изменить текст ссылки(анкор) -- отредактируйте колонку первый ключевик')?>
					<br /><br />
					<?php echo ht_trans('Вы можете добавить несколько вариантов, разделяя их `|`')?>.
					<?php echo ht_trans('Например `Вариант1|Вариант2|Вариант3`')?>.
					<?php echo ht_trans('Однако, длина поля не должна быть больше 250 символов')?>.	
					<br /><br /><br />
				' style='color:gray'><?php echo ht_trans('Как изменять ссылки в облаке?')?></span>
			</td><td style="padding-left: 15px; border-left: 1px solid rgb(206, 239, 214); width:540px">
				<h3 style="margin-bottom: 7px;">
					<?php echo ht_trans('Предпросмотр')?>
					<small onclick="$('#cloud_css_dialog').dialog({ width: 210 });" style="float: right; cursor:pointer; border-bottom: 1px dashed gray;">CSS</small>
				</h3>
				<style id='cloud_css_style' >
				</style>
				<div style="display:none" id='cloud_css_dialog'>
					<table>
						<tr><td>NoWrap:	 </td><td><input id='cloud_css_nowrap' onchange='update_cloud_css()' type='checkbox' /></td></tr>
						<tr><td>Center:	 </td><td><input id='cloud_css_center' onchange='update_cloud_css()' type='checkbox' /></td></tr>
						<tr><td>Color:	 </td><td><input id='cloud_css_color'  onchange='update_cloud_css()' value='black' size='6' /></td></tr>
						<tr><td>Width:	 </td><td><input id='cloud_css_width'  onchange='update_cloud_css()' value='550'   size='3' />px</td></tr>
					</table>
					<br />
					<span id='cloud_css_code' class='hint'><?php echo ht_trans('Показать CSS код')?></span>
					<br /><br />
				</div>
				
				<div id='cloud' style="padding-bottom:15px; margin: 0 auto;">
				</div>
				<input id='test_cloud_btn' type="button" value='<?php echo ht_trans('Обновить')?>' onclick='RefreshCloud()' />
				<span style="float: right; cursor: help; border-bottom: 1px dashed black;" onclick='show_cloud_sourche();'><?php echo ht_trans('Показать код')?></span>
				<div style="display:none" id='cloud_code_dialog'>
					<div id="cloud_code">
						<br /><br />
						<b><span class="cloud_code_params"></span></b><br /><br />
						<table>
							<tr><th><b>HTML</b>:</th><td>&lt;!--the_keys_cloud?<span class="cloud_code_params"></span>--&gt;</td></tr>
							<tr><th><b>PHP</b>:</th><td>the_keys_cloud("<span class="cloud_code_params"></span>");<td></tr>
						</table>
						<br /><br />	
					</div>
				</div>
			</td></tr></table>			
				
			</div>
		</div>
		<input type="hidden" name="waspost" value='1' />
		<input type='hidden' name='form' value='opt_save' />
		
		<input type="submit" value='<?php echo ht_trans('Сохранить')?>' style='margin-top:3px;' id='submit_btn' />
	</form>
	<hr />
	<b><?php echo ht_trans('Подсказка')?></b>: <?php echo ht_trans('Если текст')?> <span class='hint' hcontent='<?php echo ht_trans('Пример подчеркнутого текста')?>'><?php echo ht_trans('подчеркнут')?></span>, <?php echo ht_trans('то вы можете кликнуть на него, чтобы вызвать справку')?>.
	<?php if(version_compare(PHP_VERSION, '5', '<')):?>
		<br /><br />
		<b><?php echo ht_trans('Внимание')?></b>: <?php echo ht_trans('Вы используете версию PHP младше 5.0. Часть возможностей на ней недоступны, а также скорость работы будет ниже')?>.
	<?php endif;?>
	<hr />
	
		<h2><?php echo ht_trans('Действия')?></h2>
		<div id="accordion">
			<?php include('actions/synhr.php'); ?>
			<h3><a href="#cash_clear"><?php echo ht_trans('Сбросить кеш')?></a></h3>
			<div>
				<form method='post'>
					<?php echo ht_trans('Вы можете сбросить кеш приложения')?>.<br /><br />
					<input type='hidden' name='waspost' value='1' />
					<input type='hidden' name='form' value='cash_clear' />
					<input id='synhr_filters_btn' type='submit' value='<?php echo ht_trans('Поехали')?>' />
				</form>
			</div>			
			<h3><a href="#opt_clear"><?php echo ht_trans('Сбросить настройки')?></a></h3>
			<div>
				<form method='post'>
					<?php echo ht_trans('Возвращает настройки программы в начальное положение, кроме доступов к MySQL')?>.<br /><br />
					<input type='hidden' name='waspost' value='1' />
					<input type='hidden' name='form' value='opt_clear' />
					<input type='submit' value='<?php echo ht_trans('Сбросить настройки')?>' />
				</form>
			</div>
			<h3><a href="#opt_import"><?php echo ht_trans('Импорт настроек')?></a></h3>
			<div>
				<?php echo ht_trans('Вы можете импортировать настройки из HTracer установленного на другом сайте')?>.<br /><br />
				<form method='post'>
					<textarea 
						name="data" 
						wrap="off" 			
						onfocus="this.select()"
						spellcheck="false"  
						rows="10" 
						style="width:720px; white-space:nowrap; font-size:90%"
					></textarea><br />
					<?php echo ht_trans('Сохранить текущие доступы к MySQL')?> <input type='checkbox' name='mysql' value='1' /><br />
					<input type='hidden' name='waspost' value='1' />
					<input type='hidden' name='form' value='opt_import' />
					<input type='submit' value='<?php echo ht_trans('Импортировать')?>' />
				</form>
			</div>
			<h3><a href="#opt_export"><?php echo ht_trans('Экспорт настроек')?></a></h3>
			<div>
				<?php echo ht_trans('При обновлении с одной версии на другую все настройки пользователя сохраняются')?>.
				<?php echo ht_trans('Эта возможность нужна для быстрого переноса конфигурации HTracer с одного сайта в другой')?>.<br /><br />				
				<?php echo ht_trans('Скопируйте эти данные и вставьте в поле импорта в опциях админки HTracer на другом сайте')?>.<br /><br />
				<textarea 
					id='export_text' 
					name="data" 
					onfocus="this.select()"
					spellcheck="false"  
					wrap="off" 
					rows="10" 
					style="width:720px; white-space:nowrap; font-size:90%"
				></textarea><br />
				<b><?php echo ht_trans('Внимание')?>:</b> <?php echo ht_trans('В целях безопасности пароли к MySQL и к админке HTracer не экспортируются')?>.
			</div>
		</div>		
<?php htracer_admin_footer();?>	