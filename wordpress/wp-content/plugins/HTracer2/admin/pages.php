<?php 
	$GLOBALS['ht_admin_page']='pages';
	include_once('functions.php');
	if(isset($_POST['ajax']) && $_POST['ajax'])
		htracer_admin_header(false);
	else
		htracer_admin_header('Страницы');

	
	
	if(!$GLOBALS['htracer_mysql'])
	{
		echo "
			<div class='header_message'>
				".ht_trans('Эта страница недоступна при хранении информации в файлах, а не в MySQL').". <br /><br />
			</div>";	
		htracer_admin_footer();	
		exit();
	}
	if(isset($_POST['waspost']) && $_POST['waspost'])
	{	
		if($_POST['form']=='pages')
		{
			$Pages=Array();
			$WasPages=Array();
			unset($_POST['keys_table_length']);

			foreach($_POST as $Key => $Value)
			{
				$Value=trim($Value);
				$Key=explode('_',$Key);
				$iswas=false;
				if(count($Key)!=2)
				{
					if(count($Key)==3 && $Key[0]=='was')
						$iswas=true;
					else
						continue;
				}
				$Param=$Key[0+$iswas];
				$ID=$Key[1+$iswas];
				$cPages=&$Pages;
				if($iswas)
					$cPages=&$WasPages;
				if(!isset($cPages[$ID]))
				{
					$cPages[$ID]=Array
					(
						 'ShowInCloud'=>0, 
						 'ShowATitle'=>0,
						 'isFirstKeysSetByUser'=>1 
					);
				}
				if($Param=='ShowInCloud'||$Param=='ShowATitle')
					$Value=$Value;
				$cPages[$ID][$Param]=$Value;
			}
			foreach($Pages as $ID=>$Page)
			{
				if($Page['FirstKey'] !=$WasPages[$ID]['FirstKey']
				 ||$Page['SecondKey']!=$WasPages[$ID]['SecondKey'])
					$Pages[$ID]['isFirstKeysSetByUser']=0;
				$max=250;
				$str=$Page['FirstKey'];
				if(mb_strlen($str, 'utf-8')>$max)
				{
					$pos=false;
					for($i=$max-1;$i>=0;$i--)
					{
						if($str{$i}==='|')
						{
							$pos=$i;
							break;
						}
					}
					if(!$pos)
						$pos=$max;
					$str=mb_substr($str , 0, $pos, 'utf-8');
				}			
				$Page['FirstKey']=$str;
			}
			$Changes=Array();
			foreach($Pages as $ID => $Data)
			{
				foreach($Data as $Param => $Value)
				{
					if($WasPages[$ID][$Param]==$Value)
						continue;
					if(!isset($Changes[$ID]))
						$Changes[$ID]=Array('wasURL'=>$WasPages[$ID]['URL']);
					$Changes[$ID][$Param]=$Value;
				}
			}
			HTracer::UpdateTableData('pages',$Changes);
		}
	}
	if(isset($_POST['ajax']) && $_POST['ajax'])
		exit();
?>	
<script>
	var save_str_tr='<?php echo ht_trans('Сохранить')?>';
	var saving_str='<?php echo ht_trans('Сохранение...')?>';
	var saved_str='<?php echo ht_trans('Сохранено')?>';
	var loading_str='<?php echo ht_trans('Загрузка...')?>';
	var exportg_str_tr='<?php echo ht_trans('Экспортировать')?>';
	var not_save_confirm='<?php echo ht_trans('Внесенные изменения не будут сохранены. Продолжить?')?>';
	var is_ru_lang= false;
	<?php if(ht_trans_is_ru()):?>
		is_ru_lang= true;
	<?php endif;//(!ht_trans_is_ru()):?>
</script>
	<script src="pages.js" type="text/javascript"></script>

	<h1><?php echo ht_trans('Редактирование страниц')?></h1>
	<form method='post' id='pages_form'>
		<table class='szebra data_table' cellspacing='0' id='pages_table'>
			<thead>	
				<tr class='header_line'>
					<th class='page_th'><div class='hint' hcontent='
						<?php echo ht_trans('Изменение URL страницы перенесет также и все ее ключевики')?>.<br /><br />
						<?php echo ht_trans('Если отчистить это поле и нажать сохранить, то страница и все ее ключи будут удалены')?> 
						'>URL
					</div></th>
					<th class='peva_th'><div class='hint' hcontent='
						<?php echo ht_trans('Суммарный вес всех ключевиков страницы')?>
						'><?php echo ht_trans('Вес')?></div></th>
					<th class='k1_th'><div class='hint' hcontent='
						<?php echo ht_trans('Этот ключевик -- анкор ссылки на эту страницу в облаке')?>.<br /><br />
						<?php echo ht_trans('Также 2/3 ссылок на эту страницу будут иметь такой титл')?>. 
						'><?php echo ht_trans('Первый ключевик')?></div></th>
					<th class='k2_th'><div class='hint' hcontent='
						<?php echo ht_trans('>Этот ключевик -- титл ссылки на эту страницу в облаке')?>.<br /><br />
						<?php echo ht_trans('Также 1/3 ссылок на эту страницу будут иметь такой титл')?>. 
						'><?php echo ht_trans('Второй ключевик')?></div></th>
					<th class='cloud_th'><div class='hint' hcontent='
						<?php echo ht_trans('Показывать ли данную страницу в семантическом облаке')?>
						<?php echo ShowSetTableValuesForm('cloud',true);?>
						'><?php echo ht_trans('О')?></div></th>
					<th class='titles_th'><div class='hint' hcontent='
						<?php echo ht_trans('Добавлять ли ссылкам на данную страницу титлы')?>.
						<?php echo ShowSetTableValuesForm('titles',true);?>
						'>T</div></th>
				</tr>
			</thead>	
			<tbody></tbody>	
		</table>
		<input type='hidden' name='waspost' value='1' />
		<input type='hidden' name='form' value='pages' />
		<input type='submit' id='submit_btn' value='<?php echo ht_trans('Сохранить')?>' />
	</form>	
	
	<hr />
<h2><?php echo ht_trans('Действия')?></h2>
	<div id="accordion">
		<?php include('actions/goto.php')?>
<!--		
		<h3><a href="#check_redirects">Проверить редиректы</a></h3>
		<div>
			Если структура сайта изменилась и настроены редиректы со старых URL на новые, 
			то HTracer сможет считать новые адреса страниц. <br /><br /> 
			<form action='page.php'>
				<input type='submit' value='Поехали' />
			</form>
		</div>
-->		
	</div>	
<?php htracer_admin_footer();?>	