<?php 
	if(isset($_POST['url']) && !isset($_GET['url']))
		$_GET['url']=$_POST['url'];
	$GLOBALS['ht_admin_page']='page';
	if(strpos(strtolower($_GET['url']),'http://')===0)
	{
		$p=explode('/',$_GET['url'],4);
		$_GET['url']='/'.$p[3];
	}
	
	$GLOBALS['ht_edited_page']=$_GET['url'];
	
	include_once('functions.php');
	if(isset($_POST['ajax']) && $_POST['ajax'])
		htracer_admin_header(false);
	else
		htracer_admin_header('Редактирование страницы');
	
	
	if(isset($_POST['waspost']) && $_POST['waspost'] && isset($_POST['form']))
	{	
		if($_POST['form']=='keys')
		{
			ht_proccess_key_table_update();
			echo "<h1>".ht_trans('Данные были обновлены')."</h1>"; 
		}
		elseif($_POST['form']=='del_page_keys')
		{
			HTracer::DeletePage($_POST['url'],true);
			echo "<h1>".ht_trans('Данные были обновлены')."</h1>"; 
		}
		elseif($_POST['form']=='del_page_full')
		{
			HTracer::DeletePage($_POST['url'],false);
			echo "<h1>".ht_trans('Данные были обновлены')."</h1>"; 
		}
	}
	elseif(isset($_GET['change_page_url']) && $_GET['change_page_url'])
	{
		HTracer::ChangePageURL($_GET['old_url'],$_GET['url']);
		//echo $_GET['old_url'].'<br />';
		//echo $_GET['url'];
		
		$CancelURL='page.php?url='.urlencode($_GET['old_url']).'&old_url='.urlencode($_GET['url']).'&change_page_url=1';
		
		echo "<h1>".ht_trans('URL был изменен');
			echo "<a href='$CancelURL' style='font-size: 50%; font-family:arial; color: gray; display:block'>".ht_trans('Отменить')."</a>";	
		echo "</h1>"; 
	}
	if(isset($_POST['ajax']) && $_POST['ajax'])
		exit();
	if(!$GLOBALS['htracer_mysql'])
	{		
?>
		<div class='header_message'>
			<?php echo ht_trans('К сожалению, большинство функций новой админки не доступны при храненении информации в файлах, а не в MySQL')?>. <br /><br />
			<?php echo ht_trans('Для изменения ключевиков используйте при храненении информации в файлах старую админку')?>.
		</div>	
<?php
		htracer_admin_footer();	
		exit();
	}
	
	$fURL='http://'.$_SERVER['SERVER_NAME'].$_GET['url'];
?>
	<script type="text/javascript">
		var suggest_add_msg		 = '<?php echo ht_trans('Ключи были добавлены в таблицу')?>.<br /> <?php echo ht_trans('Они еще не сохранены в БД')?>.';
		var single_page_url		 = '<?php echo addslashes($_GET['url']);?>';	
		var is_ru_lang= false;
		
		<?php if(ht_trans_is_ru()):?>
			is_ru_lang= true;
		<?php endif;//(!ht_trans_is_ru()):?>
		
		var save_str_tr='<?php echo ht_trans('Сохранить')?>';
		var saving_str='<?php echo ht_trans('Сохранение...')?>';
		var saved_str='<?php echo ht_trans('Сохранено')?>';
		var loading_str='<?php echo ht_trans('Загрузка...')?>';
		var exportg_str_tr='<?php echo ht_trans('Экспортировать')?>';
		var not_save_confirm='<?php echo ht_trans('Внесенные изменения не будут сохранены. Продолжить?')?>';
	</script>
	<script src="keys.js" type="text/javascript"></script>
	<h1>
	<?php echo ht_trans('Редактирование страницы')?> 
		<a href='<?php echo $fURL;?>' style="font-size: 50%; font-family:arial; color: gray; display:block"><?php echo $_GET['url'];?></a>
	</h1>
	
		<?php if(isset($GLOBALS['htracer_usp']) && $GLOBALS['htracer_usp']):
			if(isset($_POST['form']) && $_POST['form']=='usp_options')
			{
				HTracer::add_page_meta($_GET['url'],'page_title_cb'      , isset($_POST['page_title_cb'])       && $_POST['page_title_cb']);
				HTracer::add_page_meta($_GET['url'],'meta_keywords_cb'   , isset($_POST['meta_keywords_cb'])    && $_POST['meta_keywords_cb']);
				HTracer::add_page_meta($_GET['url'],'meta_description_cb', isset($_POST['meta_description_cb']) && $_POST['meta_description_cb']);
				
				HTracer::add_page_meta($_GET['url'],'page_title'		, isset($_POST['page_title']) ? $_POST['page_title'] : '');
				HTracer::add_page_meta($_GET['url'],'meta_keywords'		, isset($_POST['meta_keywords']) ? $_POST['meta_keywords'] : '');
				HTracer::add_page_meta($_GET['url'],'meta_description'	, isset($_POST['meta_description']) ? $_POST['meta_description'] : '');
			}
			$TURL=$_SERVER["REQUEST_URI"];
			$_SERVER["REQUEST_URI"]=$_GET['url'];
			//unset($GLOBALS['htracer_curent_page_keys_in']);
			$HTitle=HT_FormTitle('none');
			$_SERVER["REQUEST_URI"]=$TURL;
		?>
		<form method='post' id='usp_options'>
		<div  class='page_options'>
			<table>
				<tr>
					<td valign='top'><input type='checkbox' name='page_title_cb' class='row_enabler' 
						<?php if(HTracer::get_page_meta($_GET['url'],'page_title_cb')) echo "checked='checked'";?> /></td>
					<th valign='top' class='hint' hcontent='<?php echo ht_trans('Содержимое тега title')?>. 
					<?php echo ht_trans('Большинство поисковиков учитывают только первые 60 символов')?>.<br /><br />
					<?php echo ht_trans('Сильно влияет на релевантность страницы и на кликабельность сниппета в выдаче')?>.<br /><br />
					'><?php echo ht_trans('Заголовок')?>:</th>
					<td valign='top'><input type='text' name='page_title' class='calc_symb' 
						value='<?php echo HTracer::get_page_meta($_GET['url'],'page_title');?>' 
					/></td>
					<td valign='top' class='symb_count'><span id='page_title_symb'></span>/60</td>
				</tr>
				<tr>
					<td valign='top'><input type='checkbox' name='meta_keywords_cb' class='row_enabler' 
					<?php if(HTracer::get_page_meta($_GET['url'],'meta_keywords_cb')) echo "checked='checked'";?> /></td>
					<th valign='top' class='hint' hcontent='<?php echo ht_trans('Содержимое мета-тега KeyWords. Ключевые слова страницы через запятую. Рекомендовано использовать не больше 100 символов')?>.<br /><br />
					<?php echo ht_trans('Не влияет на выдачу в большинстве поисковиков, однако Яндекс, учитывает этот тег')?>.<br /><br />
					'>MetaKeywords:</th>
					<td valign='top'><input type='text' name='meta_keywords' class='calc_symb' 
						value='<?php echo HTracer::get_page_meta($_GET['url'],'meta_keywords');?>'
					/></td>
					<td valign='top' class='symb_count'><span id='meta_keywords_symb'></span>/100</td>
				</tr>
				<tr>
					<td valign='top'><input type='checkbox' name='meta_description_cb' class='row_enabler'
						<?php if(HTracer::get_page_meta($_GET['url'],'meta_description_cb')) echo "checked='checked'";?> /></td>
					<th valign='top' class='hint' hcontent='
					<?php echo ht_trans('Содержимое мета-тега Decription. Описание страницы')?>.<br /><br />
					<?php echo ht_trans('Иногда используеться поисковиками для формирования сниппета')?>.<br /><br />
					<?php echo ht_trans('Практически не влияет на релевантность страницы')?>.<br /><br />
					'>MetaDescription:</th>
					<td valign='top'><textarea class='calc_symb' name='meta_description'
					><?php echo HTracer::get_page_meta($_GET['url'],'meta_description');?></textarea></td>
					<td valign='top' class='symb_count'><span id='meta_description_symb'></span>/160</td>
				</tr>
			</table>
			<small><?php echo ht_trans('Подсказка')?>: <i style='color:gray'><?php echo ht_trans('Если вы дважды кликните по полю ввода заголовка или ключевых слов, то появятся автоварианты')?>.</i></small>
		</div>	
		
			<script>
			//usp_options
				function usp_form_changed()
				{
					$("#usp_options input[type='submit']").attr('disabled',false);
					$("#usp_options input[type='submit']").attr('value','Cохранить');
				}
				$("#usp_options input[type='submit']").attr('disabled',true);
				$('#usp_options input,#usp_options textarea').keypress(function(){usp_form_changed()});
				$('#usp_options input,#usp_options select,#usp_options textarea').change(function(){usp_form_changed()});
				$('#usp_options').ajaxForm({ 
					beforeSubmit:  function(){
						$("#usp_options input[type='submit']").attr('disabled',true);
						$("#usp_options input[type='submit']").attr('value','Cохранение..');
					}, 
					success:       function(){
						$("#usp_options input[type='submit']").attr('value','Cохранено');
					}
				});
	   
				var HTitle='<?php echo addslashes($HTitle);?>';
				var TitleVariants = [HTitle];
				var MkeysVariants = ['<?php echo addslashes(HTracer::get_meta_keys(', ',$_GET['url']));?>'];
				var CPU ='<?php echo $fURL;?>';
				var TitleOrder = '<?php echo addslashes(trim($GLOBALS['htracer_title_order']));?>';
				var TitleSpace = '<?php echo addslashes(trim($GLOBALS['htracer_title_spacer']));?>';
				function AddTitleVariant(val)
				{
					TitleVariants.push(val);
					if(TitleOrder=='rtl'||TitleOrder=='ltr')
					{
						var TitleSpace0=TitleSpace;
						var vals=val.split(TitleSpace);
						if(vals.length<2)
						{
							if(TitleSpace=='<<')	
								TitleSpace0='&lt;&lt;';
							else if(TitleSpace=='<')
								TitleSpace0='&lt;';
							else if(TitleSpace=='&lt;&lt;')
								TitleSpace0='<<';
							else if(TitleSpace=='&lt;')
								TitleSpace0='<';
							else if(TitleSpace=='>>')	
								TitleSpace0='&qt;&qt;';
							else if(TitleSpace=='>')
								TitleSpace0='&qt;';
							else if(TitleSpace=='&qt;&qt;')
								TitleSpace0='>>';
							else if(TitleSpace=='&qt;')
								TitleSpace0='>';
							else if(TitleSpace=='&laquo;')
								TitleSpace0='«';
							else if(TitleSpace=='«')
								TitleSpace0='&laquo;';
							else if(TitleSpace=='&raquo;')
								TitleSpace0='»';
							else if(TitleSpace=='»')
								TitleSpace0='&raquo;';
							vals=val.split(TitleSpace0);	
						}
						if(vals.length>1)
						{	
							var HTitle0=HTitle;
							var l=0;
							if(TitleOrder=='ltr')
								l = vals.length-1;
								//alert(l);
								//alert(vals);
							if(vals[l].length)
							{
								if(vals[l][0]==' ')
									HTitle0=' '+HTitle0;
								if(vals[l][vals[l].length-1]==' ')
									HTitle0=HTitle0+' ';
							}
							vals[l]=HTitle0;
							TitleVariants.push(vals.join(TitleSpace0));
						}
					}
				}
				
				//CPU ='http://htest.ru/test.php';
				var is_in_dbl_click=false;
				var is_ac_showing=false;
				
				$(function() 
				{
				//	alert(HTitle);
				//Добавляем подсказки к заголовку
					var field=$('input[name="page_title"]');
					var title_field=field;
					if(field.val())
						TitleVariants.push(field.val());
					field.autocomplete({
						search: function(event, ui) {return true;},
						minLength:0,
						search:function()
						{
							return is_in_dbl_click;
						},
						open: function( request, response )
						{
							is_ac_showing=true;
						},
						close: function( request, response )
						{
							is_ac_showing=false;
						},
						source: function( request, response ) 
						{
							var Variants=[];
							for(var i=0;i<TitleVariants.length;i++)
							{
								if(!TitleVariants[i].trim() || title_field.val()==TitleVariants[i])
									continue;
								var was=false;
								for(var j=0;j<TitleVariants.length;j++)
									if(Variants[j]==TitleVariants[i])
										was=true;
								if(was)
									continue;
								Variants.push(TitleVariants[i].trim());
							}
							response(Variants);
						},
						select: function(event, ui){setTimeout(function(){title_field.trigger('change');},50);}
					});
					field.dblclick(function(){
						is_in_dbl_click=true; 
						if(is_ac_showing)
							title_field.autocomplete("close"); 
						else
							title_field.autocomplete("search"); 
						is_in_dbl_click=false;
					});
					
				//Добавляем подсказки к MetaKeyWords
					var field=$('input[name="meta_keywords"]');
					var mkeys_field=field;
					if(field.val())
						MkeysVariants.push(field.val());
					field.autocomplete({
						search: function(event, ui) {return true;},
						minLength:0,
						search:function()
						{
							return is_in_dbl_click;
						},
						open: function( request, response )
						{
							is_ac_showing=true;
						},
						close: function( request, response )
						{
							is_ac_showing=false;
						},
						source: function( request, response ) 
						{
							var Variants=[];
							for(var i=0;i<MkeysVariants.length;i++)
							{
								if(!MkeysVariants[i].trim()||mkeys_field.val()==MkeysVariants[i])
									continue;
								var was=false;
								for(var j=0;j<MkeysVariants.length;j++)
									if(Variants[j]==MkeysVariants[i])
										was=true;
								if(was)
									continue;
								Variants.push(MkeysVariants[i].trim());
							}
							response(Variants);
						},
						select: function(event, ui){setTimeout(function(){mkeys_field.trigger('change');},50);}
					});
					field.dblclick(function(){
						is_in_dbl_click=true; 
						if(is_ac_showing)
							mkeys_field.autocomplete("close"); 
						else
							mkeys_field.autocomplete("search"); 
						is_in_dbl_click=false;
					});
					$.ajax({
						url: CPU,
						dataType:'xml',
						success: function(data)
						{
							var field=$('input[name="page_title"]');
							var val  =$(data).find('title').text();
							if(!field.val())
								field.val(val);
							if(val)
								AddTitleVariant(val);
							field.trigger('change');

							field=$('input[name="meta_keywords"]');
							val  =$(data).find('meta[name="keywords"]').attr('content');
							if(!field.val())
								field.val(val);
							if(val)
								MkeysVariants.push(val);
							field.trigger('change');
							
							field=$('textarea[name="meta_description"]');
							if(!field.html())
								field.html($(data).find('meta[name="description"]').attr('content'));
							field.trigger('change');
						}
					});
					
					//Запрашиваем титл и метакейводс без HTracer
					$.ajax({
						url: CPU,
						dataType:'xml',
						data:{'disable_htracer':'<?php echo $GLOBALS['htracer_ajax_pass'];?>'},
						success: function(data)
						{
							var val  =$(data).find('title').text();
							if(val)
								AddTitleVariant(val);
								
							val  =$(data).find('meta[name="keywords"]').attr('content');
							MkeysVariants.push(val);
						}
					});
				});
			</script>			
		<br />
		<input type='hidden' name='waspost' value='1' />
		<input type='hidden' name='form' value='usp_options' />
		<input type='submit' class='submit_btn' value='<?php echo ht_trans('Сохранить')?>' />
	</form>
	<hr />
	<?php endif;?>

						
	<form method='post' id='active_keys'>
		<table class='szebra data_table' cellspacing='0' id='keys_table'>
			<thead>	
				<tr class='header_line'>
					<th class='sc_th'><div class='hint' 
						hcontent='
							<?php echo ht_trans('Принимает ли ключевик участие в построении семантического ядра сайта')?>.<br /><br />
							<?php echo ht_trans('Другими словами: учавствует ли он в генерации облак, альтов, титлов и прочего')?>.<br /><br />
							<?php echo ht_trans('Если вы убирете галочку и нажмете сохранить, то ключ будет игнорироваться')?>.
							<?php echo ShowSetTableValuesForm('sc',true);?><br /><br />
						'
						><?php echo ht_trans('СЯ')?></div></th>
					<th class='key_th'><div style='float:left; display:inline'><?php echo ht_trans('Ключ')?></div></th>
					<th class='out_th'>
						<div class='hint' hcontent='
							<?php echo ht_trans('Благодаря этому полю вы можете изменить отображение ключевика на сайте')?>.<br /><br />
							<?php echo ht_trans('Влияет на все кроме поисковых подсказок и meta keywords')?>.
							'><?php echo ht_trans('Написание')?>
						</div>
					</th>
					<th class='eva_th'>
						<div class='hint' hcontent='
							<?php echo ht_trans('При стандартных настройках вес ключевика равен числу переходов по нему');?>. <br /><br />
							<?php echo ht_trans('Влияет на частоту выпадений ключевика на странице и на сайте');?>.
							<?php echo ShowSetTableValuesForm('eva',false,1);?>
							<br /><br />
							'><?php echo ht_trans('Вес')?>
						</div></th>
					<th class='cl_th'><div class='hint' hcontent='
						<?php echo ht_trans('Разрешено ли использовать ли этот ключ для построения контекстных ссылок')?>.<br /><br />
						<?php echo ht_trans('Вы можете исключить ключ из формирования контекстных ссылок, не исключая его из титлов альтов и прочего')?>.
						<?php echo ShowSetTableValuesForm('cl',true);?><br /><br />
							
						'><?php echo ht_trans('КС')?></div></th>
				</tr>
				</thead>	
				<tbody></tbody>	
		</table>
		<input type='hidden' name='waspost' value='1' />
		<input type='hidden' name='form' value='keys' />
		<input type='submit' id='submit_btn' value='<?php echo ht_trans('Сохранить')?>' />
		<span style='float:right; margin-top:3px; font-size:120%; cursor:pointer; border-bottom: 1px dashed black'
			  onclick="addKey()"><?php echo ht_trans('Добавить ключевик')?></span>
	</form>
		<hr />
		<h2><?php echo ht_trans('Действия')?></h2>
		<div id="accordion">
		<?php if(ht_trans_is_ru()): ?>
			<h3><a href="#wordstat">Парсинг WordStat</a></h3>
			<div>
				<table style='width:100%'><tr><td id='ws_td1' valign='top'>
					<!--
					<b>Источник</b>:<br />					
					<select id='ws_parse_type'>
						<option value='ya'>WordStat</option>
						<option value='g'>Google</option>
					</select><br />
					-->
					<b>Запрос</b>:<br />
					<span id='ws_parse_input'>
						<input type='text' id='ws_parse_str' /><br />
						<span style='color:gray'>Например, <i>одесса гостиницы</i></span><br /><br />
					</span>	
					<br />
					<input type='button' id='ws_parse_btn'   value='Поехали' onclick='parse_ws();' />
					<br /><br />
				</td><td id='ws_td2' valign='top'>
					<div id='ws_void_div'>
						Благодаря этой возможности вы можете быстро и легко добавить новые запросы.<br /><br />
						Запросы берутся из <a href="http://wordstat.yandex.ru/">Yandex.Wordstat</a> &mdash; ститистики поисковых запросов в Яндексе.<br /><br />
						Добавлять запросы вручную необязательно. HTracer собирает свою базу автоматически, благодаря переходам на ваш сайт с поисковиков.<br /><br />
					</div>
					<div id='ws_wait_div' style='display:none;'><center><img src='images/loader.gif' /></center></div>			
					<div id='ws_table_div' style='display:none; max-height:210px; height:210px; overflow-y:scroll;'>
						Вес = ЧП * <input id='ws_k_input' style='width:40px' value='1.0' onkeypress='ws_k_change()' onchange='ws_k_change()' /><br />
						<table style='width:100%;' valign='top' id='ws_key_table'>
							<thead>
								<tr>
									<td style='width:20px'></td>
									<td style='width:100%'>Ключ</td>
									<td style='width:40px'><span class='hint' hcontent='
										Число показов по данным WordStat из этого параметра высчитываеться вес
									'>ЧП</span></td>
									<td style='width:50px'>Вес</td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div id='add_ws_keys_btn' style='display:none; margin-top: 7px;' >	
						<input type='button' value='Добавить' onclick='add_ws_keys()'  style='float:left'/>
						<div style='float:right'>	
							<small  style='border-bottom:1px dotted black; cursor:pointer' 
									onclick='$(".add_ws_key").attr("checked","checked"); recalc_ws_count();'
								>Выделить все</small>
							&nbsp;&nbsp;
							<small 	style='border-bottom:1px dotted black; cursor:pointer'
									onclick='$(".add_ws_key").attr("checked",false); recalc_ws_count();'
								>Отменить все</small>
							<small style='color:gray; width: 35px;' id='ws_keys_count'></small>
						</div>
					</div>	
				</td></tr></table>
				<br />
			</div>
			<h3><a href="#suggest"><?php echo ht_trans('Парсинг поисковых подсказок')?></a></h3>
			<div>
				<table style='width:100%'><tr><td id='suggest_td1' valign='top'>
					<span class='hint' hcontent='
						<b><?php echo ht_trans('Таблица')?></b>    -- <?php echo ht_trans('будут найдены все уточнения ключевиков из таблицы вверху')?>.<br /><br />
						<b><?php echo ht_trans('Поле ввода')?></b> -- <?php echo ht_trans('вы можете ввести произвольную строку и добавить все ее уточнения')?>. 						
						'><b><?php echo ht_trans('Источник')?></b></span>:<br />
					<select id='suggest_parse_type' onchange='suggest_parse_type_changed();'>
						<option value='table'><?php echo ht_trans('Таблица')?></option>
						<option value='input'><?php echo ht_trans('Поле ввода')?></option>
					</select><br />
					<span id='suggest_parse_input' style='display:none'>
						<?php echo ht_trans('Введите начало запроса')?>:<br />
						<input type='text' id='suggest_parse_str' /><br />
					</span>	
					<input type='checkbox' id='suggest_aparse_cb' onchange='suggest_parse_str_change(true);' />
					<span class='hint' id='suggest_aparse_hint'  hcontent='
						<?php echo ht_trans('Если вы выбирете эту опцию, то за счет попыток перемешивания слов в запросе возрастет число ключевиков')?>. <br /><br />
						<?php echo ht_trans('Однако, это может замедлить скорость работы приложения')?>.
					'><?php echo ht_trans('Перемешивать слова')?></span><br />
					<?php if(ht_trans_is_ru()): ?>
						<input type="checkbox" id="suggest_aparse_stem" />
						<span class='hint' id='suggest_aparse_stem_hint'  hcontent='
							Если вы выбирете эту опцию, то окончание последнего слова будет обрезаться.
							Например, "музеи одессы"=>"музеи одесс".<br /><br />
							Это увеличит число ключевиков засчет снижения скорости парсинга.
						'>Обрезать окончания</span><br />
					<?php else:?>
						<input type="hidden" id="suggest_aparse_stem" value='0' />
					<?php endif;?>	
					<br />
					<input type='button' id='suggest_parse_btn'   value='<?php echo ht_trans('Поехали')?>' onclick='parse_suggests_from_table();' />

					<br /><br />
				</td><td id='suggest_td2'  valign='top'>
					<div id='suggest_void_div'></div>
					<div id="progressbar"></div>&nbsp;
					<div id="progressbar_label" style='padding-bottom:20px; font-size: 20px'></div>
					<ul style='display:none'></ul>
					<div id='add_sugested_keys_btn' style='display:none; margin-top: 7px;' >	
						<input type='button' value='Добавить' onclick='add_sugested_keys()'  style='float:left'/>
						<div style='float:right'>	
							<small  style='border-bottom:1px dotted black; cursor:pointer' 
									onclick='$(".add_sugested_key").attr("checked","checked"); recalc_suggest_count();'
								><?php echo ht_trans('Выделить все')?></small>
							&nbsp;&nbsp;
							<small 	style='border-bottom:1px dotted black; cursor:pointer'
									onclick='$(".add_sugested_key").attr("checked",false); recalc_suggest_count();'
								><?php echo ht_trans('Отменить все')?></small>
							<small style='color:gray; width: 35px;' id='sugested_keys_count'></small>
						</div>
					</div>	
				</td></tr></table>
				<br />
			</div>
		<?php endif;?>	
			<?php include('actions/goto.php')?>
			<h3><a href="#del_page_keys"><?php echo ht_trans('Удалить ключевики')?></a></h3>
			<div>
				<form action='page.php' method='post'>
					<input type='hidden' name='waspost' value='1' />
					<input type='hidden' name='form' value='del_page_keys' />
					<input type='hidden' name='url' value='<?php echo $_GET['url'];?>' />
					<input type='submit' value='<?php echo ht_trans('Удалить')?>' />
				</form>
			</div>
			<h3><a href="#del_page_full"><?php echo ht_trans('Удалить страницу')?></a></h3>
			<div>
				<form action='page.php' method='post'>
					<input type='hidden' name='waspost' value='1' />
					<input type='hidden' name='form' value='del_page_full' />	
					<input type='hidden' name='url' value='<?php echo $_GET['url'];?>' />
					<input type='submit' value='<?php echo ht_trans('Удалить')?>' />
				</form>
			</div>		
			<h3><a href="#change_page_url"><?php echo ht_trans('Изменить URL')?></a></h3>
			<div>
				<form action='page.php'>
					<input id='change_url_input' type='text' name='url' style='margin-bottom: 1px; width: 400px;' value='<?php echo $_GET['url']?>' spellcheck='false' /> <br />
					<input type='hidden' name='old_url' value='<?php echo $_GET['url'];?>' />
					<input type='hidden' name='change_page_url' value='1' />
					<input type='submit' value='<?php echo ht_trans('Изменить')?>' />
				</form>
			</div>		
			
			<script type="text/javascript">
				$(function() {
					$("#change_url_input").autocomplete({source: 'ajax/pages_autocomplete.php'});
				});
			</script>
		</div>	
<?php htracer_admin_footer();?>	